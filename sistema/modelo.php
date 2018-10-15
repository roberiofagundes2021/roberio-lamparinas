<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Modelo';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Modelo</title>

	<?php include_once("head.php"); ?>

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
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-body text-center">
							<i class="icon-book icon-2x text-success-400 border-success-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
								<h5 class="card-title">Base de Conhecimento</h5>
								<p class="mb-3">Ouch found swore much dear conductively hid submissively hatchet vexed far inanimately alongside candidly much and jeez</p>
								<a href="#" class="btn bg-success-400">Listar artigos</a>
							</div>
						</div>
					</div>
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
