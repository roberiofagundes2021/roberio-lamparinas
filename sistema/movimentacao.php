<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Movimentação';

include('global_assets/php/conexao.php');

$sql = ("SELECT MovimData, MovimTipo, MovimNumNotaFiscal, ForneNome, ProduValorVenda, ProduStatus
		 FROM Movimentacao
		 LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		 LEFT JOIN SubCategoria on SbCatId = ProduSubCategoria
	     WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY ProduNome ASC");
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
	<title>Lamparinas | Movimentação</title>

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
		function atualizaMovimentacao(ProduId, ProduNome, ProduStatus, Tipo){
		
			document.getElementById('inputMovimentacaoId').value = ProduId;
			document.getElementById('inputMovimentacaoNome').value = ProduNome;
			document.getElementById('inputMovimentacaoStatus').value = ProduStatus;
					
			if (Tipo == 'edita'){	
				document.formMovimentacao.action = "movimentacaoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formMovimentacao, "Tem certeza que deseja excluir esse movimentacao?", "movimentacaoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formMovimentacao.action = "movimentacaoMudaSituacao.php";
			}		
			
			document.formMovimentacao.submit();
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
								<h3 class="card-title">Relação das Movimentacões do Estoque</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às movimentações do estoque da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right">
									<a href="movimentacaoNovo.php" class="btn btn-success" role="button">Nova Movimentação</a>
									<a href="movimentacaoImprimir.php" class="btn bg-slate-700" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Imprimir Relação" target="_blank">Requisiçẽos</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Movimentacao</th>
										<th>Categoria</th>
										<th>SubCategoria</th>
										<th>Preço Venda</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['ProduStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['ProduStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['ProduNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											<td>'.$item['SbCatNome'].'</td>
											<td>'.formataMoeda($item['ProduValorVenda']).'</td>
											');
										
										print('<td><a href="#" onclick="atualizaMovimentacao('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaMovimentacao('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaMovimentacao('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formMovimentacao" method="post">
					<input type="hidden" id="inputMovimentacaoId" name="inputMovimentacaoId" >
					<input type="hidden" id="inputMovimentacaoNome" name="inputMovimentacaoNome" >
					<input type="hidden" id="inputMovimentacaoStatus" name="inputMovimentacaoStatus" >
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
