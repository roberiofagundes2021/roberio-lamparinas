<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Movimentação';

include('global_assets/php/conexao.php');

$sql = ("SELECT MovimId, MovimData, MovimTipo, MovimNotaFiscal, ForneNome, SituaNome, SituaChave, LcEstNome, SetorNome
		 FROM Movimentacao
		 LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		 LEFT JOIN LocalEstoque on LcEstId = MovimOrigem or LcEstId = MovimDestinoLocal
		 LEFT JOIN Setor on SetorId = MovimDestinoSetor
		 JOIN Situacao on SituaId = MovimSituacao
	     WHERE MovimEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY MovimData DESC");
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
	
	<!-- /theme JS files -->	
	
	 <script type="text/javascript">
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaMovimentacao(MovimId, MovimNotaFiscal, Tipo){
		
			document.getElementById('inputMovimentacaoId').value = MovimId;
			document.getElementById('inputMovimentacaoNotaFiscal').value = MovimNotaFiscal;
					
			if (Tipo == 'edita'){	
				document.formMovimentacao.action = "movimentacaoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formMovimentacao, "Tem certeza que deseja excluir esse movimentacao?", "movimentacaoExclui.php");
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
										<th>Data</th>
										<th>Tipo</th>
										<th>Nota Fiscal</th>
										<th>Fornecedor</th>
										<th>Estoque Destino</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$tipo = $item['MovimTipo'] == 'E' ? 'Entrada' : ($item['MovimTipo'] == 'S' ? 'Saída' : 'Transferência');
										$local = $item['MovimTipo'] == 'S' ? $item['SetorNome'] : $item['LcEstNome'];
										$situacao = $item['SituaNome'];
										$situacaoClasse = $item['SituaChave'] == 'PENDENTE' ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.mostraData($item['MovimData']).'</td>
											<td>'.$tipo.'</td>
											<td>'.$item['MovimNotaFiscal'].'</td>
											<td>'.$item['ForneNome'].'</td>
											<td>'.$local.'</td>
											');
										
										print('<td><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaMovimentacao('.$item['MovimId'].', \''.$item['MovimNotaFiscal'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaMovimentacao('.$item['MovimId'].', \''.$item['MovimNotaFiscal'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
					<input type="hidden" id="inputMovimentacaoNotaFiscal" name="inputMovimentacaoNotaFiscal" >
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
