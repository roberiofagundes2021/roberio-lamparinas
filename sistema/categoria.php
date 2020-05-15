<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Categoria';

include('global_assets/php/conexao.php');

//O ALTERAR é usado na importação de Produtos (eles não devem aparecer aqui)
$sql = "SELECT CategId, CategNome, CategStatus, SituaNome, SituaChave, SituaCor
		FROM Categoria
		JOIN Situacao on SituaId = CategStatus
	    WHERE CategUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave != 'ALTERAR'
		ORDER BY CategNome ASC";
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
	<title>Lamparinas | Categoria</title>

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
			$('#tblCategoria').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Categoria
					width: "80%",
					targets: [0]
				},
				{ 
					orderable: false,   //Situação
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
		})
			
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
								<p class="font-size-lg">A relação abaixo faz referência às categorias da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
								<div class="text-right"><a href="categoriaNovo.php" class="btn btn-success" role="button">Nova Categoria</a></div>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table id="tblCategoria" class="table">
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
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										
										print('
										<tr>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\',\''.$item['SituaChave'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCategoria('.$item['CategId'].', \''.$item['CategNome'].'\',\''.$item['SituaChave'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formCategoria" method="post" action="categoriaEdita.php">
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
