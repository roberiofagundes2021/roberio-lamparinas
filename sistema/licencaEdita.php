<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Licenca';

include('global_assets/php/conexao.php');

if(isset($_POST['inputLicencaId'])){
	
	$iLicenca = $_POST['inputLicencaId'];
        	
	try{
		
		$sql = "SELECT LicenId, LicenDtInicio, LicenDtFim, LicenLimiteUsuarios
				FROM Licenca
				WHERE LicenId = $iLicenca ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {
	irpara("licenca.php");
}

if(isset($_POST['inputDataInicio'])){
	
	try{
		
		$sql = "UPDATE Licenca SET LicenDtInicio = :dDtInicio, LicenDtFim = :dDtFim, LicenLimiteUsuarios = :iLimiteUsuarios, LicenUsuarioAtualizador = :iUsuarioAtualizador
				WHERE LicenId = :iLicenca";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':dDtInicio' => $_POST['inputDataInicio'],
						':dDtFim' => $_POST['inputDataFim'],
						':iLimiteUsuarios' => $_POST['inputLimiteUsuarios'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iLicenca' => $_POST['inputLicencaId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Licença alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar licença!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();exit;
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	<!-- CV Documentacao: https://jqueryvalidation.org/ -->
	<!-- /theme JS files -->	
	
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
					
					<form name="formLicenca" id="formLicenca" method="post" class="form-validate-jquery" action="licencaEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Licença</h5>
						</div>
						
						<input type="hidden" id="inputLicencaId" name="inputLicencaId" value="<?php echo $row['LicenId']; ?>" >
						
						<div class="card-body">			
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $row['LicenDtInicio']; ?>" required>
										</div>
									</div>
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataFim">Data Fim <span class="text-danger">*</span></label></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>																					
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $row['LicenDtFim']; ?>" required>
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $row['FlOpeDataFim']; ?>" required>
										</div>
									</div>
								</div>
										
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputLimiteUsuarios">Limite de Usuários</label>
										<input type="text" id="inputLimiteUsuarios" name="inputLimiteUsuarios" class="form-control" placeholder="Limite de Usuários" value="<?php echo $row['LicenLimiteUsuarios']; ?>">
									</div>
								</div>
							</div>							
						
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
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
