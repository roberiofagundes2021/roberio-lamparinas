<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Fluxo Operacional';

include('global_assets/php/conexao.php');

if(isset($_POST['inputData'])){

	try{
		
		$sql = "INSERT INTO FluxoOperacional (FlOpeEmpresa, FlOpeDtInicio, FlOpeDtFim, FlOpeLimiteUsuarios, FlOpeStatus, FlOpeUsuarioAtualizador)
				VALUES (:iEmpresa, :dDtInicio, :dDtFim, :iLimiteUsuarios, :bStatus, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':iEmpresa' => $_SESSION['EmpresaId'],
						':dDtInicio' => $_POST['inputDataInicio'],
						':dDtFim' => $_POST['inputDataFim'],
						':iLimiteUsuarios' => $_POST['inputLimiteUsuarios'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional incluído!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Fluxo Operacional!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
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
				
				//Aqui falta verificar se a licença com data maior e ativa é menor que a data início (TEM QUE SER)
				
				$('#cmbEmpresa').prop("disabled", false);
				
				$( "#formFluxoOperacional" ).submit();				
				
			});		
			
			$('#cancelar').on('click', function(e){
				
				$('#cmbEmpresa').prop("disabled", false);			
				$(window.document.location).attr('href', "fluxo.php");
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
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputData">Data</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="text" id="inputData" name="inputData" class="form-control daterange-single" placeholder="Data" required>
										</div>
									</div>
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputNumContrato">Número do Contrato</label>
										<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº do Contrato">
									</div>
								</div>
										
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputNumProcesso">Número do Processo</label>
										<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-6">
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
													print('<option value="'.$item['ForneId'].'#'.$item['ForneContato'].'#'.$item['ForneEmail'].'#'.$item['ForneTelefone'].'#'.$item['ForneCelular'].'">'.$item['ForneNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbCategoria">Categoria</label>
										<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT CategId, CategNome
														 FROM Categoria															     
														 WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY CategNome ASC");
												$result = $conn->query("$sql");
												$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowCategoria as $item){															
													print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
												}
											
											?>
										</select>
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
