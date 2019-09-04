<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Fluxo Operacional';

include('global_assets/php/conexao.php');

if(isset($_POST['inputData'])){

	try{
		
		$conn->beginTransaction();
		
		$sql = "INSERT INTO FluxoOperacional (FlOpeFornecedor, FlOpeCategoria, FlOpeOrcamento, FlOpeDataInicio, FlOpeDataFim, FlOpeNumContrato, FlOpeNumProcesso, 
											  FlOpeValor, FlOpeStatus, FlOpeUsuarioAtualizador, FlOpeEmpresa)
				VALUES (:iFornecedor, :iCategoria, :iOrcamento, :dDataInicio, :dDataFim, :iNumContrato, :iNumProcesso, 
						:fValor, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':iFornecedor' => $_POST['cmbFornecedor'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iOrcamento' => $_POST['cmbOrcamento'] == '#' ? null : $_POST['cmbOrcamento'],
						':dDataInicio' => $_POST['inputDataInicio'] == '' ? null : $_POST['inputDataInicio'],
						':dDataFim' => $_POST['inputDataFim'] == '' ? null : $_POST['inputDataFim'],
						':iNumContrato' => $_POST['inputNumContrato'],
						':iNumProcesso' => $_POST['inputNumProcesso'],
						':fValor' => gravaValor($_POST['inputValor']),
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));

		$insertId = $conn->lastInsertId();	
		
		$sql = "SELECT *
				FROM OrcamentoXProduto
				Where OrXPrOrcamento = ".$_POST['cmbOrcamento'];
		$result = $conn->query("$sql");
		$rowOrcamentoProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($rowOrcamentoProdutos as $item){
		
			$sql = "INSERT INTO FluxoOperacionalXProduto (FOXPrFluxoOperacional, FOXPrProduto, FOXPrQuantidade, FOXPrValorUnitario, FOXPrUsuarioAtualizador, FOXPrEmpresa)
					VALUES (:iFluxoOperacional, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':iFluxoOperacional' => $insertId,
							':iProduto' => $item['OrXPrProduto'],
							':iQuantidade' => $item['OrXPrQuantidade'],
							':fValorUnitario' => $item['OrXPrValorUnitario'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpreId']
							));		
		}
						
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional incluído!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Fluxo Operacional!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
	
	irpara("fluxo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fluxo Operacional</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	
	
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	

			//Ao mudar o Fornecedor, filtra a categoria e o Orçamento via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e){
				
				Filtrando();
				
				var cmbFornecedor = $('#cmbFornecedor').val();

				$.getJSON('filtraCategoria.php?idFornecedor='+cmbFornecedor, function (dados){
					
					//var option = '<option value="#">Selecione a Categoria</option>';
					var option = '';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.CategId+'">'+obj.CategNome+'</option>';
						});						
						
						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}					
				});
				
				$.getJSON('filtraOrcamento.php?idFornecedor='+cmbFornecedor, function (dados){
					
					if (dados.length > 1){
						var option = '<option value="#" "selected">Selecione o Orçamento</option>';
					} else {
						var option = '';
					}
					
					if (dados.length){
						
						$.each(dados, function(i, obj){							
							option += '<option value="'+obj.OrcamId+'">Nº: ' + obj.OrcamNumero + ' - Data: ' + obj.OrcamData +'</option>';
						});						
						
						$('#cmbOrcamento').html(option).show();
					} else {
						ResetOrcamento();
					}					
				});				
				
			});	
			
			
			//Mostra o "Filtrando..." na combo Categoria e Orcamento ao mesmo tempo
			function Filtrando(){
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
				FiltraOrcamento();
			}		
			
			//Mostra o "Filtrando..." na combo Orcamento
			function FiltraOrcamento(){
				$('#cmbOrcamento').empty().append('<option>Filtrando...</option>');
			}		
			
			function ResetCategoria(){
				$('#cmbCategoria').empty().append('<option value="#">Sem Categoria</option>');
			}
			
			function ResetOrcamento(){
				$('#cmbOrcamento').empty().append('<option value="#">Sem orçamento</option>');
			}				
			

					
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputDataInicio = $('#inputDataInicio').val();
				var inputDataFim = $('#inputDataFim').val();
				
				if (inputDataFim < inputDataInicio){
					alerta('Atenção','A Data Fim deve ser maior que a Data Início!','error');
					$('#inputDataFim').focus();
					return false;				
				}
				
				$( "#formFluxoOperacional" ).submit();				
				
			});	
		
		});	
		
	</script>

</head>

<body class="navbar-top">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">
				
				<!-- Info blocks -->
				<div class="card">
					
					<form name="formFluxoOperacional" id="formFluxoOperacional" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Fluxo Operacional</h5>
						</div>
						
						<div class="card-body">								
														
							<h5 class="mb-0 font-weight-semibold">Dados do Orçamento</h5>
							<br>
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbFornecedor">Fornecedor</label>
										<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
														 FROM Fornecedor														     
														 WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
														 ORDER BY ForneNome ASC");
												$result = $conn->query("$sql");
												$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowFornecedor as $item){															
													print('<option value="'.$item['ForneId'].'">'.$item['ForneNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbCategoria">Categoria</label>
										<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
											<option value="#">Selecione</option>
										</select>
									</div>
								</div>
								
								<div class="col-lg-4">
									<label for="cmbOrcamento">Orçamento</label>
									<select id="cmbOrcamento" name="cmbOrcamento" class="form-control form-control-select2">
										<option value="#">Selecione</option>
									</select>
								</div>	
							</div>
							
							<h5 class="mb-0 font-weight-semibold">Dados do Contrato</h5>
							<br>
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputData">Data Início</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" required>
										</div>
									</div>
								</div>
								
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputData">Data Fim</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" required>
										</div>
									</div>
								</div>								
								
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNumContrato">Número do Contrato</label>
										<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº do Contrato">
									</div>
								</div>
										
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNumProcesso">Número do Processo</label>
										<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo">
									</div>
								</div>
								
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputValor">Valor Total</label>
										<input type="text" id="inputValor" name="inputValor" class="form-control" placeholder="Valor Total">
									</div>
								</div>								
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="fluxo.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
									</div>
								</div>
							</div>
						</form>								

					</div>
					<!-- /card-body -->
					
				</div>
				<!-- /info blocks -->

			</div>
			<!-- /content area -->			
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
