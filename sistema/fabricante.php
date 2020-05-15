<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fabricante';

include('global_assets/php/conexao.php');

$sql = ("SELECT FabriId, FabriNome, FabriStatus, SituaNome, SituaCor, SituaChave
		 FROM Fabricante
		 JOIN Situacao on SituaId = FabriStatus
	     WHERE FabriUnidade = ". $_SESSION['UnidadeId'] ."
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
	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblFabricante').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Fabricante
					width: "80%",
					targets: [0]
				},
				{ 
					orderable: true,   //Situação
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
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
								<p class="font-size-lg">A relação abaixo faz referência aos fabricantes da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
								<div class="text-right"><a href="fabricanteNovo.php" class="btn btn-success" role="button">Novo Fabricante</a></div>
							</div>
							
							<table id="tblFabricante" class="table">
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
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										
										print('
										<tr>
											<td>'.$item['FabriNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaFabricante('.$item['FabriId'].', \''.$item['FabriNome'].'\',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
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
