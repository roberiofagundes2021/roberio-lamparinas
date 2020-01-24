<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Unidade de Medida';

include('global_assets/php/conexao.php');

$sql = ("SELECT UnMedId, UnMedNome, UnMedSigla, UnMedStatus, SituaNome, SituaCor, SituaChave
		 FROM UnidadeMedida
		 JOIN Situacao on SituaId = UnMedStatus
	     WHERE UnMedEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY UnMedNome ASC");
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
	<title>Lamparinas | UnidadeMedida</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblUnidadeMedida').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //UnidadeMedida
					width: "50%",
					targets: [0]
				},
				{ 
					orderable: true,   //Sigla
					width: "20%",
					targets: [1]
				},
				{ 
					orderable: true,   //Situação
					width: "15%",
					targets: [1]
				},
				{ 
					orderable: true,   //Ações
					width: "15%",
					targets: [2]
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
		function atualizaUnidadeMedida(UnMedId, UnMedNome, UnMedStatus, Tipo){
		
			document.getElementById('inputUnidadeMedidaId').value = UnMedId;
			document.getElementById('inputUnidadeMedidaNome').value = UnMedNome;
			document.getElementById('inputUnidadeMedidaStatus').value = UnMedStatus;
					
			if (Tipo == 'edita'){	
				document.formUnidadeMedida.action = "unidademedidaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formUnidadeMedida, "Tem certeza que deseja excluir essa unidade de medida?", "unidademedidaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formUnidadeMedida.action = "unidademedidaMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formUnidadeMedida.action = "unidademedidaImprime.php";
				document.formUnidadeMedida.setAttribute("target", "_blank");
			}
			
			document.formUnidadeMedida.submit();
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
								<h3 class="card-title">Relação de Unidades de Medida</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="unidademedida.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às unidades de medida da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="unidademedidaNovo.php" class="btn btn-success" role="button">Nova Unidade de Medida</a></div>
							</div>
							
							<table id="tblUnidadeMedida" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Unidade de Medida</th>
										<th>Sigla</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										
										print('
										<tr>
											<td>'.$item['UnMedNome'].'</td>
											<td>'.$item['UnMedSigla'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaUnidadeMedida('.$item['UnMedId'].', \''.$item['UnMedNome'].'\',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaUnidadeMedida('.$item['UnMedId'].', \''.$item['UnMedNome'].'\','.$item['UnMedStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaUnidadeMedida('.$item['UnMedId'].', \''.$item['UnMedNome'].'\','.$item['UnMedStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formUnidadeMedida" method="post">
					<input type="hidden" id="inputUnidadeMedidaId" name="inputUnidadeMedidaId" >
					<input type="hidden" id="inputUnidadeMedidaNome" name="inputUnidadeMedidaNome" >
					<input type="hidden" id="inputUnidadeMedidaStatus" name="inputUnidadeMedidaStatus" >
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
