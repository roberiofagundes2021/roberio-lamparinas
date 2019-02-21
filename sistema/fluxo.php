<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Operacional';

include('global_assets/php/conexao.php');

$sql = ("SELECT FlOpeId, ForneNome, FlOpeOrcamento, FlOpeCategoria, FlOpeData, 
				FlOpeNumContrato, FlOpeNumProcesso, FlOpeStatus, CategNome
		 FROM FluxoOperacional
		 JOIN Categoria on CategId = FlOpeCategoria
		 JOIN Fornecedor on ForneId = FlOpeFornecedor
	     WHERE FlOpeEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY FlOpeData DESC, FlOpeCategoria ASC");
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
	<title>Lamparinas | Fluxo Operacional</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files --> 
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblFluxo').DataTable( {
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: false,
					width: 50,
					targets: [ 6 ]
				}], 
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
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
		function atualizaFluxoOperacional(FlOpeId, FlOpeNumContrato, FlOpeStatus, Tipo){
		
			document.getElementById('inputFluxoOperacionalId').value = FlOpeId;
			document.getElementById('inputFluxoOperacionalNumContrato').value = FlOpeNumContrato;
			document.getElementById('inputFluxoOperacionalStatus').value = FlOpeStatus;
					
			if (Tipo == 'edita'){	
				document.formFluxoOperacional.action = "fluxoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formFluxoOperacional, "Tem certeza que deseja excluir esse fluxo?", "fluxoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formFluxoOperacional.action = "fluxoMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formFluxoOperacional.action = "fluxoImprime.php";
				document.formFluxoOperacional.setAttribute("target", "_blank");
			}
			
			document.formFluxoOperacional.submit();
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
								<h3 class="card-title">Relação dos Fluxos Operacionais</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos fluxos operacionais da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="fluxoNovo.php" class="btn btn-success" role="button">Novo Fluxo Operacional</a></div>
							</div>
							
							<table class="table" id="tblFluxo">
								<thead>
									<tr class="bg-slate">
										<th width="10%">Data</th>
										<th width="15%">Nº Contrato</th>
										<th width="15%">Nº Processo</th>										
										<th width="20%">Fornecedor</th>
										<th width="20%">Categoria</th>
										<th width="10%">Situação</th>
										<th width="10%" class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['FlOpeStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['FlOpeStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.mostraData($item['FlOpeData']).'</td>
											<td>'.$item['FlOpeNumContrato'].'</td>
											<td>'.$item['FlOpeNumProcesso'].'</td>
											<td>'.$item['ForneNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaFluxoOperacional('.$item['FlOpeId'].', \''.$item['FlOpeNumContrato'].'\','.$item['FlOpeStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaFluxoOperacional('.$item['FlOpeId'].', \''.$item['FlOpeNumContrato'].'\','.$item['FlOpeStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaFluxoOperacional('.$item['FlOpeId'].', \''.$item['FlOpeNumContrato'].'\','.$item['FlOpeStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
														<a href="#" onclick="atualizaFluxoOperacional('.$item['FlOpeId'].', \''.$item['FlOpeNumContrato'].'\','.$item['FlOpeStatus'].', \'imprime\');" class="list-icons-item"><i class="icon-statistics" data-popup="tooltip" data-placement="bottom" title="Fluxo Realizado"></i></a>
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
				
				<form name="formFluxoOperacional" method="post">
					<input type="hidden" id="inputFluxoOperacionalId" name="inputFluxoOperacionalId" >
					<input type="hidden" id="inputFluxoOperacionalNumContato" name="inputFluxoOperacionalNumContrato" >
					<input type="hidden" id="inputFluxoOperacionalStatus" name="inputFluxoOperacionalStatus" >
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