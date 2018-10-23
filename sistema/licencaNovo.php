<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Licenca';

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
		
		$_SESSION['msg'] = "Empresa incluída com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao incluir empresa!!!";
		
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
	<title>Lamparinas | Licença</title>

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
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Licença</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataInicio">Data Início</label>
										<input type="text" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" required>
									</div>
								</div>
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputDataFim">Data Fim</label>
												<input type="text" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" required>
											</div>
										</div>
										
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputLimiteUsuarios">Limite de Usuários</label>
												<input type="text" id="inputLimiteUsuarios" name="inputLimiteUsuarios" class="form-control" placeholder="Limite de Usuários" required>
											</div>
										</div>
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
