<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Licenca';

include('global_assets/php/conexao.php');

if(isset($_POST['inputDataInicio'])){

	try{
		
		$sql = "INSERT INTO Licenca (LicenEmpresa, LicenDtInicio, LicenDtFim, LicenLimiteUsuarios, LicenStatus, LicenUsuarioAtualizador)
				VALUES (:iEmpresa, :dDtInicio, :dDtFim, :iLimiteUsuarios, :bStatus, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':iEmpresa' => $_SESSION['EmpreId'],
						':dDtInicio' => $_POST['inputDataInicio'],
						':dDtFim' => $_POST['inputDataFim'],
						':iLimiteUsuarios' => $_POST['inputLimiteUsuarios'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Licença incluída!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Licença!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("licenca.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Licença</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	
	
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>
		
		<?php include_once("menuLeftSecundario.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">
				
				<!-- Info blocks -->
				<div class="card">
					
					<form name="formLicenca" method="post" class="form-validate" action="licencaNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Licença</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataInicio">Data Início</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="text" id="inputDataInicio" name="inputDataInicio" class="form-control daterange-single" placeholder="Data Início" required>
										</div>
									</div>
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataFim">Data Fim</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>																					
											<input type="text" id="inputDataFim" name="inputDataFim" class="form-control daterange-single" placeholder="Data Fim">
										</div>
									</div>
								</div>
										
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputLimiteUsuarios">Limite de Usuários</label>
										<input type="text" id="inputLimiteUsuarios" name="inputLimiteUsuarios" class="form-control" placeholder="Limite de Usuários">
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
										<a href="licenca.php" class="btn btn-basic" role="button">Cancelar</a>
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
