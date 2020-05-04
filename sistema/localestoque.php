<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Local do Estoque';

include('global_assets/php/conexao.php');

$sql = ("SELECT LcEstId, LcEstNome, LcEstStatus, UnidaNome, SituaNome, SituaCor, SituaChave
		 FROM LocalEstoque
		 JOIN Unidade on UnidaId = LcEstUnidade
		 JOIN Situacao on SituaId = LcEstStatus
	     WHERE LcEstEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY LcEstNome ASC");
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
	<title>Lamparinas | Local do Estoque</title>

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
			$('#tblLocalEstoque').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Local de Estoque
					width: "40%",
					targets: [0]
				},
				{ 
					orderable: true,   //Unidade
					width: "40%",
					targets: [1]
				},
				{ 
					orderable: true,   //Situação
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: true,   //Ações
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
		function atualizaLocalEstoque(LcEstId, LcEstNome, LcEstStatus, Tipo){
		
			document.getElementById('inputLocalEstoqueId').value = LcEstId;
			document.getElementById('inputLocalEstoqueNome').value = LcEstNome;
			document.getElementById('inputLocalEstoqueStatus').value = LcEstStatus;
					
			if (Tipo == 'edita'){	
				document.formLocalEstoque.action = "localestoqueEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formLocalEstoque, "Tem certeza que deseja excluir esse local?", "localestoqueExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formLocalEstoque.action = "localestoqueMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formLocalEstoque.action = "localestoqueImprime.php";
				document.formLocalEstoque.setAttribute("target", "_blank");
			}
			
			document.formLocalEstoque.submit();
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
								<h3 class="card-title">Relação de Locais do Estoque</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="localestoque.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos locais de estoque da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
								<div class="text-right"><a href="localestoqueNovo.php" class="btn btn-success" role="button">Novo Local do Estoque</a></div>
							</div>
							
							<table id="tblLocalEstoque" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Local do Estoque</th>
										<th>Unidade</th>
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
											<td>'.$item['LcEstNome'].'</td>
											<td>'.$item['UnidaNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaLocalEstoque('.$item['LcEstId'].', \''.$item['LcEstNome'].'\',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaLocalEstoque('.$item['LcEstId'].', \''.$item['LcEstNome'].'\','.$item['LcEstStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaLocalEstoque('.$item['LcEstId'].', \''.$item['LcEstNome'].'\','.$item['LcEstStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formLocalEstoque" method="post">
					<input type="hidden" id="inputLocalEstoqueId" name="inputLocalEstoqueId" >
					<input type="hidden" id="inputLocalEstoqueNome" name="inputLocalEstoqueNome" >
					<input type="hidden" id="inputLocalEstoqueStatus" name="inputLocalEstoqueStatus" >
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
