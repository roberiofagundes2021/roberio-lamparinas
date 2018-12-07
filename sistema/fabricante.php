<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fabricante';

include('global_assets/php/conexao.php');

$sql = ("SELECT FabriId, FabriNome, FabriStatus
		 FROM Fabricante
	     WHERE FabriEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY FabriNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fabricante</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaFabricante(FabriId, FabriNome, FabriStatus, Tipo){
		
			document.getElementById('inputFabricanteId').value = FabriId;
			document.getElementById('inputFabricanteNome').value = FabriNome;
			document.getElementById('inputFabricanteStatus').value = FabriStatus;
					
			if (Tipo == 'edita'){	
				document.formFabricante.action = "fabricanteEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formFabricante, "Tem certeza que deseja excluir esse fabricante?", "fabricanteExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formFabricante.action = "fabricanteMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formFabricante.action = "fabricanteImprime.php";
				document.formFabricante.setAttribute("target", "_blank");
			}
			
			document.formFabricante.submit();
		}		
			
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
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Relação de Fabricantes</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="fabricante.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos fabricantes da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="fabricanteNovo.php" class="btn btn-success" role="button">Novo Fabricante</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Fabricante</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['FabriStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['FabriStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['FabriNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaFabricante('.$item['FabriId'].', \''.$item['FabriNome'].'\','.$item['FabriStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaFabricante('.$item['FabriId'].', \''.$item['FabriNome'].'\','.$item['FabriStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaFabricante('.$item['FabriId'].', \''.$item['FabriNome'].'\','.$item['FabriStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
													</div>
												</div>
											</td>
										</tr>');
									}
								?>

								</tbody>
							</table>
						</div>
						<!-- /basic responsive configuration -->

					</div>
				</div>				
				
				<!-- /info blocks -->
				
				<form name="formFabricante" method="post">
					<input type="hidden" id="inputFabricanteId" name="inputFabricanteId" >
					<input type="hidden" id="inputFabricanteNome" name="inputFabricanteNome" >
					<input type="hidden" id="inputFabricanteStatus" name="inputFabricanteStatus" >
				</form>

			</div>
			<!-- /content area -->
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
