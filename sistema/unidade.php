<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Unidade';

include('global_assets/php/conexao.php');

$sql = ("SELECT UnidaId, UnidaNome, UnidaStatus
		 FROM Unidade
	     WHERE UnidaEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY UnidaNome ASC");
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
	<title>Lamparinas | Unidade</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaUnidade(UnidaId, UnidaNome, UnidaStatus, Tipo){
		
			document.getElementById('inputUnidadeId').value = UnidaId;
			document.getElementById('inputUnidadeNome').value = UnidaNome;
			document.getElementById('inputUnidadeStatus').value = UnidaStatus;
					
			if (Tipo == 'edita'){	
				document.formUnidade.action = "unidadeEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formUnidade, "Tem certeza que deseja excluir essa unidade?", "unidadeExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formUnidade.action = "unidadeMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formUnidade.action = "unidadeImprime.php";
				document.formUnidade.setAttribute("target", "_blank");
			}
			
			document.formUnidade.submit();
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
								<h3 class="card-title">Relação de Unidades</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="unidade.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às unidades da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="unidadeNovo.php" class="btn btn-success" role="button">Nova Unidade</a></div>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Unidade</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['UnidaStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['UnidaStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['UnidaNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaUnidade('.$item['UnidaId'].', \''.$item['UnidaNome'].'\','.$item['UnidaStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaUnidade('.$item['UnidaId'].', \''.$item['UnidaNome'].'\','.$item['UnidaStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaUnidade('.$item['UnidaId'].', \''.$item['UnidaNome'].'\','.$item['UnidaStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formUnidade" method="post" action="cEdita.php">
					<input type="hidden" id="inputUnidadeId" name="inputUnidadeId" >
					<input type="hidden" id="inputUnidadeNome" name="inputUnidadeNome" >
					<input type="hidden" id="inputUnidadeStatus" name="inputUnidadeStatus" >
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
