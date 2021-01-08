<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Movimentação';

include('global_assets/php/conexao.php');

$sql = "SELECT MovimId, MovimData, MovimTipo, MovimNotaFiscal, ForneNome, SituaNome, SituaChave, SituaCor, LcEstNome, SetorNome, BandeMotivo
		FROM Movimentacao
		LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		LEFT JOIN LocalEstoque on LcEstId = MovimDestinoLocal
		LEFT JOIN Setor on SetorId = MovimDestinoSetor
		JOIN Situacao on SituaId = MovimSituacao
		LEFT JOIN Bandeja on BandeTabelaId = MovimId and BandeTabela = 'Movimentacao' and BandeUnidade = " . $_SESSION['UnidadeId'] . "
	    WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY MovimData, MovimId DESC";
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
	<title>Lamparinas | Movimentação</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>	

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

	<script type="text/javascript">
	
		$(document).ready(function() {

			$.fn.dataTable.moment('DD/MM/YYYY'); //Para corrigir a ordenação por data

			/* Início: Tabela Personalizada */
			$('#tblMovimentacao').DataTable({
				"order": [
					[0, "desc"]
				],
				autoWidth: false,
				responsive: true,
				columnDefs: [{
						orderable: true,  //Data
						width: "10%",
						targets: [0]
					},
					{
						orderable: true, //Tipo
						width: "10%",
						targets: [1]
					},
					{
						orderable: true, //Nota Fiscal
						width: "10%",
						targets: [2]
					},
					{
						orderable: true, //Fornecedor
						width: "30%",
						targets: [3]
					},
					{
						orderable: true, //Destino
						width: "20%",
						targets: [4]
					},
					{
						orderable: true, //Situação
						width: "10%",
						targets: [5]
					},
					{
						orderable: false, //Ações
						width: "10%",
						targets: [6]
					}
				],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: {
						'first': 'Primeira',
						'last': 'Última',
						'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
						'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
					}
				}
			});

			// Select2 for length menu styling
			var _componentSelect2 = function() {
				if (!$().select2) {
					console.warn('Warning - select2.min.js is not loaded.');
					return;
				}

				// Initialize
				$('.dataTables_length select').select2({
					minimumResultsForSearch: Infinity,
					dropdownAutoWidth: true,
					width: 'auto'
				});
			};

			_componentSelect2();

			/* Fim: Tabela Personalizada */
		});

		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaMovimentacao(MovimId, MovimNotaFiscal, MovimTipo, Tipo, Motivo) {

			document.getElementById('inputMovimentacaoId').value = MovimId;
			document.getElementById('inputMovimentacaoNotaFiscal').value = MovimNotaFiscal;

			if (Tipo == 'motivo'){
	            bootbox.alert({
                    title: '<strong>Motivo da Não Liberação</strong>',
                    message: Motivo
                });
                return false;
			} else if (Tipo == 'edita') {
				document.formMovimentacao.action = "movimentacaoEdita.php";
			} else if (Tipo == 'exclui') {
				confirmaExclusao(document.formMovimentacao, "Tem certeza que deseja excluir essa movimentação?", "movimentacaoExclui.php");
			} else if (Tipo == 'imprimir') {

				if (MovimTipo == 'E'){
					document.formMovimentacao.action = "movimentacaoImprimeEntrada.php";
				} else {
					document.formMovimentacao.action = "movimentacaoImprimeRetirada.php";
				}
				
				document.formMovimentacao.setAttribute("target", "_blank");
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
								<div class="row">
									<div class="col-lg-9">							
										<p class="font-size-lg">A relação abaixo faz referência às movimentações do estoque da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
									</div>

									<div class="col-lg-3">
										<div class="text-right">
											<a href="movimentacaoNovoEntrada.php" class="btn btn-principal" role="button">Nova Movimentação</a>
											<a href="index.php" class="btn bg-slate-700" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Listar Requisições">Requisições</a>
										</div>
									</div>
								</div>
							</div>

							<table class="table" id="tblMovimentacao">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Tipo</th>
										<th>NF</th>
										<th>Fornecedor</th>
										<th>Destino</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($row as $item) {

										$tipo = $item['MovimTipo'] == 'E' ? 'Entrada' : ($item['MovimTipo'] == 'S' ? 'Saída' : 'Transferência');
										if ($item['MovimTipo'] == 'S' || $item['MovimTipo'] == 'E') {

											$local = $item['MovimTipo'] == 'S' ? $item['LcEstNome'] : $item['LcEstNome'];
											
										} else if ($item['MovimTipo'] == 'T') {

											$local = isset($item['LcEstNome']) ? $item['LcEstNome'] : $item['SetorNome'];
										}
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];									

										print('
										<tr>
											<td>' . mostraData($item['MovimData']) . '</td>
											<td>' . $tipo . '</td>
											<td>' . $item['MovimNotaFiscal'] . '</td>
											<td>' . $item['ForneNome'] . '</td>
											<td>' . $local . '</td>
											');

										print('<td><span class="'.$situacaoClasse.'">'.$situacao.'</span></td>');

										print('<td class="text-center">
													<div class="list-icons">
														<div class="list-icons list-icons-extended">
															<!--<a href="#" onclick="atualizaMovimentacao(' . $item['MovimId'] . ', \'' . $item['MovimNotaFiscal'] . '\', \''.$item['MovimTipo'].'\', \'edita\', \'\');" class="list-icons-item"><i class="icon-pencil7"></i></a>-->
															<a href="#" onclick="atualizaMovimentacao(' . $item['MovimId'] . ', \'' . $item['MovimNotaFiscal'] . '\', \''.$item['MovimTipo'].'\', \'exclui\', \'\');" class="list-icons-item"><i class="icon-bin"></i></a>
															<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaMovimentacao(' . $item['MovimId'] . ', \'' . $item['MovimNotaFiscal'] . '\', \''.$item['MovimTipo'].'\', \'imprimir\', \'\');" class="dropdown-item"><i class="icon-printer2"></i> Imprimir</a>');
															
																if (isset($item['BandeMotivo'])){
																	print('
																	<div class="dropdown-divider"></div>
																	<a href="#" onclick="atualizaMovimentacao(' . $item['MovimId'] . ', \'' . $item['MovimNotaFiscal'] . '\', \''.$item['MovimTipo'].'\', \'motivo\', \''.$item['BandeMotivo'].'\')" class="dropdown-item" title="Motivo da Não liberação"><i class="icon-question4"></i> Motivo</a>');
																}															
										print('				</div>
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

				<form name="formMovimentacao" method="post" target="_blank">
					<input type="hidden" id="inputMovimentacaoId" name="inputMovimentacaoId">
					<input type="hidden" id="inputMovimentacaoNotaFiscal" name="inputMovimentacaoNotaFiscal">
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