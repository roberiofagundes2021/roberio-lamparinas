<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Sub Categoria';

include('global_assets/php/conexao.php');

$sql = "SELECT SbCatId, SbCatNome, SbCatStatus, CategNome, SituaNome, SituaCor, SituaChave
		FROM SubCategoria
		JOIN Categoria on CategId = SbCatCategoria
		JOIN Situacao on SituaId = SbCatStatus
	    WHERE SbCatUnidade = ". $_SESSION['UnidadeId'] ."
		ORDER BY SbCatNome ASC";
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
	<title>Lamparinas | SubCategoria</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblSubCategoria').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //SubCategoria
					width: "45%",
					targets: [0]
				},
				{ 
					orderable: true,   //Categoria
					width: "35%",
					targets: [1]
				},
				{ 
					orderable: true,   //Situação
					width: "10%",
					targets: [2]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
					targets: [3]
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
		function atualizaSubCategoria(SbCatId, SbCatNome, SbCatStatus, Tipo){
		
			document.getElementById('inputSubCategoriaId').value = SbCatId;
			document.getElementById('inputSubCategoriaNome').value = SbCatNome;
			document.getElementById('inputSubCategoriaStatus').value = SbCatStatus;
					
			if (Tipo == 'edita'){	
				document.formSubCategoria.action = "subcategoriaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formSubCategoria, "Tem certeza que deseja excluir essa subcategoria?", "subcategoriaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formSubCategoria.action = "subcategoriaMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formSubCategoria.action = "subcategoriaImprime.php";
				document.formSubCategoria.setAttribute("target", "_blank");
			}
			
			document.formSubCategoria.submit();
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
								<h3 class="card-title">Relação de Sub Categorias</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="subcategoria.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9">
										<p class="font-size-lg">A relação abaixo faz referência às sub categorias da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
									</div>
									<div class="col-lg-3">
										<div class="text-right"><a href="subcategoriaNovo.php" class="btn btn-principal" role="button">Nova Sub Categoria</a></div>
									</div>
								</div>
							</div>
							
							<table id="tblSubCategoria" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Sub Categoria</th>
										<th>Categoria</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										$situacaoChave ='\''.$item['SituaChave'].'\'';
										
										print('
										<tr>
											<td>'.$item['SbCatNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaSubCategoria('.$item['SbCatId'].', \''.$item['SbCatNome'].'\','.$situacaoChave.', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaSubCategoria('.$item['SbCatId'].', \''.$item['SbCatNome'].'\','.$item['SbCatStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaSubCategoria('.$item['SbCatId'].', \''.$item['SbCatNome'].'\','.$item['SbCatStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formSubCategoria" method="post">
					<input type="hidden" id="inputSubCategoriaId" name="inputSubCategoriaId" >
					<input type="hidden" id="inputSubCategoriaNome" name="inputSubCategoriaNome" >
					<input type="hidden" id="inputSubCategoriaStatus" name="inputSubCategoriaStatus" >
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
