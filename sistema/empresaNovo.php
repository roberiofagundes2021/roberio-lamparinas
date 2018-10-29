<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Empresa';

include('global_assets/php/conexao.php');

if(isset($_POST['inputCnpj'])){

	try{
		
		$sql = "INSERT INTO Empresa (EmpreCnpj, EmpreRazaoSocial, EmpreNomeFantasia, EmpreEndereco, EmpreStatus, EmpreUsuarioAtualizador)
				VALUES (:sCnpj, :sRazaoSocial, :sNomeFantasia, :sEndereco, :bStatus, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCnpj' => $_POST['inputCnpj'],
						':sRazaoSocial' => $_POST['inputRazaoSocial'],
						':sNomeFantasia' => $_POST['inputNomeFantasia'],
						':sEndereco' => $_POST['inputEndereco'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Empresa incluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir empresa!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("empresa.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Empresa</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	

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
					
					<form name="formEmpresa" method="post" class="form-validate" action="empresaNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Empresa</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputCnpj">CNPJ</label>
										<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" maxlength="14" pattern="[0-9]+$" required>
									</div>
								</div>
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputRazaoSocial">Razão Social</label>
												<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social" required>
											</div>
										</div>
										
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNomeFantasia">Nome Fantasia</label>
												<input type="text" id="inputNomeFantasia" name="inputNomeFantasia" class="form-control" placeholder="Nome Fantasia" required>
											</div>
										</div>
									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputEndereço">Endereço</label>
										<input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço">
									</div>
								</div>
							</div>								

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
										<a href="empresa.php" class="btn btn-basic" role="button">Cancelar</a>
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
