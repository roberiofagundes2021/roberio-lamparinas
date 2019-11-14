<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Forma de Pagamento';

include('global_assets/php/conexao.php');

$sql = "SELECT FoPagId, FoPagNome, FoPagStatus
		FROM FormaPagamento
	    WHERE FoPagEmpresa = ". $_SESSION['EmpreId'] ."
		ORDER BY FoPagNome ASC";
$result = $conn->query($sql);
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
	<title>Lamparinas | Forma de Pagamento</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaCentroCusto(FoPagId, FoPagNome, FoPagStatus, Tipo){
		
			document.getElementById('inputFormaPagamentoId').value = FoPagId;
			document.getElementById('inputFormaPagamentoNome').value = FoPagNome;
			document.getElementById('inputFormaPagamentoStatus').value = FoPagStatus;
					
			if (Tipo == 'edita'){	
				document.formCentroCusto.action = "formaPagamentoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formCentroCusto, "Tem certeza que deseja excluir essa Forma de Pagamento?", "formaPagamentoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formCentroCusto.action = "formaPagamentoSituacao.php";
			} 
			
			document.formFormaPagamento.submit();
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
								<h3 class="card-title">Relação de Forma de Pagamento</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="formaPagamento.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência as Formas de Pagamento <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="formaPagamentoNovo.php" class="btn btn-success" role="button">Nova Forma de Pagamento</a></div>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Forma de Pagamento</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['FoPagStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['FoPagStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['FoPagNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCentroCusto('.$item['FoPagId'].', \''.$item['FoPagNome'].'\','.$item['FoPagStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCentroCusto('.$item['FoPagId'].', \''.$item['FoPagNome'].'\','.$item['FoPagStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCentroCusto('.$item['FoPagId'].', \''.$item['FoPagNome'].'\','.$item['FoPagStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formFormaPagamento" method="post" action="centroCustoEdita.php">
					<input type="hidden" id="inputFormaPagamentoId" name="inputFormaPagamentoId" >
					<input type="hidden" id="inputFormaPagamentoNome" name="inputFormaPagamentoNome" >
					<input type="hidden" id="inputFormaPagamentoStatus" name="inputFormaPagamentoStatus" >
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
