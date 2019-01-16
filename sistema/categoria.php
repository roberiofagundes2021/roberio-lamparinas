<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Categoria';

include('global_assets/php/conexao.php');

$sql = ("SELECT CategId, CategNome, CategStatus
		 FROM Categoria
	     WHERE CategEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY CategNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//var_dump($count);die;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Categoria</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaCategoria(CategId, CategNome, CategStatus, Tipo){
		
			document.getElementById('inputCategoriaId').value = CategId;
			document.getElementById('inputCategoriaNome').value = CategNome;
			document.getElementById('inputCategoriaStatus').value = CategStatus;
					
			if (Tipo == 'edita'){	
				document.formCategoria.action = "categoriaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formCategoria, "Tem certeza que deseja excluir essa categoria?", "categoriaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formCategoria.action = "categoriaMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formCategoria.action = "categoriaImprime.php";
				document.formCategoria.setAttribute("target", "_blank");
			}
			
			document.formCategoria.submit();
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
								<h3 class="card-title">Relação de Categorias</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="categoria.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às categorias da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="categoriaNovo.php" class="btn btn-success" role="button">Nova Categoria</a></div>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Categoria</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['CategStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['CategStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\','.$item['CategStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\','.$item['CategStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\','.$item['CategStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formCategoria" method="post" action="cEdita.php">
					<input type="hidden" id="inputCategoriaId" name="inputCategoriaId" >
					<input type="hidden" id="inputCategoriaNome" name="inputCategoriaNome" >
					<input type="hidden" id="inputCategoriaStatus" name="inputCategoriaStatus" >
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