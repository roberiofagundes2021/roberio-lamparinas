<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Inventário';

include('global_assets/php/conexao.php');

$sql = ("SELECT InvenId, InvenData, InvenNumero, SituaNome, SituaChave, LcEstNome
		 FROM Inventario
		 JOIN Situacao on SituaId = InvenSituacao
		 LEFT JOIN InventarioXLocalEstoque on InXLEInventario = InvenId
		 JOIN LocalEstoque on LcEstId = InXLELocal
		 WHERE InvenEmpresa = ".$_SESSION['EmpreId']."
		 ORDER BY InvenData DESC"); 
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Inventários</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>		
	<!-- /theme JS files -->	
	
	<script>
		
		function atualizaInventario(InvenId, Tipo){

			document.getElementById('inputInventarioId').value = InvenId;
				
			if (Tipo == 'edita'){	
				document.formInventario.action = "inventarioEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formInventario, "Tem certeza que deseja excluir esse inventário?", "inventarioExclui.php");
			} 
			
			document.formInventario.submit();
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
								<h5 class="card-title">Relação dos Inventários</h5>
								<div class="header-elements">
									<div class="list-icons">
										<!--<a class="list-icons-item" data-action="collapse"></a>-->
										<!--<a href="empresa.php" class="list-icons-item" data-action="reload"></a>-->
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência aos inventários da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>.
								<div class="text-right"><a href="inventarioNovo.php" class="btn btn-success" role="button">Novo Inventário</a></div>
							</div>							

							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th width="10%">Data</th>
										<th width="15%">Nº Inventário</th>
										<th width="35%">Locais do Estoque</th>
										<th width="20%">Responsável</th>
										<th width="10%">Situação</th>
										<th width="10%" class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = $item['SituaChave'] == 'FINALIZADO' ? 'badge-success' : 'bg-grey';
										
										print('
										<tr>
											<td>'.mostraData($item['InvenData']).'</td>
											<td>'.$item['InvenNumero'].'</td>
											<td>'.$item['LcEstNome'].'</td>
											<td>Presidente</td>'
										);
										
										print('<td><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></td>');
																				
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaInventario('.$item['InvenId'].', \'edita\')" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaInventario('.$item['InvenId'].', \'exclui\')" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formInventario" method="post" action="inventarioEdita.php">
					<input type="hidden" id="inputInventarioId" name="inputInventarioId" >
					<input type="hidden" id="inputInventarioStatus" name="inputInventarioStatus" >
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
