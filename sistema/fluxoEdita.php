<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Fluxo Operacional';

include('global_assets/php/conexao.php');

//Se veio do fluxo.php
if(isset($_POST['inputFluxoOperacionalId'])){
	
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	
	try{
		
		$sql = "SELECT *
				FROM FluxoOperacional
				WHERE FlOpeId = $iFluxoOperacional ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
						
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();die;
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("fluxo.php");
}

if(isset($_POST['inputDataInicio'])){

	try{
		
		$sql = "UPDATE FluxoOperacional SET FlOpeFornecedor = :iFornecedor, FlOpeCategoria = :iCategoria, FlOpeOrcamento = :iOrcamento, 
										    FlOpeDataInicio = :dDataInicio, FlOpeDataFim = :dDataFim, FlOpeNumContrato = :iNumContrato, 
										    FlOpeNumProcesso = :iNumProcesso, FlOpeValor = :fValor, FlOpeUsuarioAtualizador = :iUsuarioAtualizador
				WHERE FlOpeId = ".$_POST['inputFluxoOperacionalId']."
				";
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
						':iUsuarioAtualizador' => $_SESSION['UsuarId']						
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional alterado!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Fluxo Operacional!!!";
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
    <script type="text/javascript">

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
				$('#cmbCategoria').empty().append('<option>Sem Categoria</option>');
			}
			
			function ResetOrcamento(){
				$('#cmbOrcamento').empty().append('<option>Sem orçamento</option>');
			}				
			

					
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputDataInicio = $('#inputDataInicio').val();				
				
				if (inputDataInicio == ''){
					alerta('Atenção','Informe a data de início do contrato!','error');
					$('#inputDataInicio').focus();
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
					
					<form name="formFluxoOperacional" id="formFluxoOperacional" method="post" class="form-validate" action="fluxoEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Fluxo Operacional</h5>
						</div>
						
						<input type="hidden" id="inputFluxoOperacionalId" name="inputFluxoOperacionalId" value="<?php echo $row['FlOpeId']; ?>" >
						
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
													$seleciona = $item['ForneId'] == $row['FlOpeFornecedor'] ? "selected" : "";
													print('<option value="'.$item['ForneId'].'" '. $seleciona .'>'.$item['ForneNome'].'</option>');
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
											<?php 
												$sql = ("SELECT CategId, CategNome
														 FROM Categoria															     
														 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY CategNome ASC");
												$result = $conn->query("$sql");
												$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowCategoria as $item){			
													$seleciona = $item['CategId'] == $row['FlOpeCategoria'] ? "selected" : "";
													print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
												}
											
											?>											
										</select>
									</div>
								</div>
								
								<div class="col-lg-4">
									<label for="cmbOrcamento">Orçamento</label>
									<select id="cmbOrcamento" name="cmbOrcamento" class="form-control form-control-select2">
										<option value="#">Selecione</option>
										<?php 
										 
											$sql = ("SELECT OrcamId, OrcamNumero, OrcamData
													 FROM Orcamento
													 WHERE OrcamEmpresa = ".$_SESSION['EmpreId']." and OrcamFornecedor = '". $row['FlOpeFornecedor']."' and OrcamStatus = 1
													 Order By OrcamId DESC");													 
											$result = $conn->query("$sql");
											$rowOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($rowOrcamento as $item){			
												$seleciona = $item['OrcamId'] == $row['FlOpeOrcamento'] ? "selected" : "";
												print('<option value="'.$item['OrcamId'].'" '. $seleciona .'>Nº: '.$item['OrcamNumero'].' - Data: ' .mostraData($item['OrcamData']).'</option>');
											}
										
										?>										
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
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $row['FlOpeDataInicio']; ?>">
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
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $row['FlOpeDataFim']; ?>">
										</div>
									</div>
								</div>								
								
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNumContrato">Número do Contrato</label>
										<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº do Contrato" value="<?php echo $row['FlOpeNumContrato']; ?>">
									</div>
								</div>
										
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNumProcesso">Número do Processo</label>
										<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo" value="<?php echo $row['FlOpeNumProcesso']; ?>">
									</div>
								</div>
								
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputValor">Valor Total</label>
										<input type="text" id="inputValor" name="inputValor" class="form-control" placeholder="Valor Total" value="<?php echo mostraValor($row['FlOpeValor']); ?>" onKeyUp="moeda(this)" maxLength="12">
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
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
