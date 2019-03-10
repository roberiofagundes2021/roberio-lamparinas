<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Produto';

include('global_assets/php/conexao.php');

$sql = ("SELECT ProduId, ProduCodigo, ProduNome, CategNome, SbCatNome, ProduValorVenda, ProduStatus
		 FROM Produto
		 LEFT JOIN Categoria on CategId = ProduCategoria
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
	<title>Lamparinas | Produto</title>

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
			$('#tblProduto').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Codigo
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Produto
					width: "25%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Categoria
					width: "20%",
					targets: [2]
				},
				{ 
					orderable: true,   //SubCategoria
					width: "20%",
					targets: [3]
				},
				{ 
					orderable: true,   //Preço Venda
					width: "15%",
					targets: [4]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [5]
				},
				{ 
					orderable: false,  //Ações
					width: "5%",
					targets: [6]
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
		function atualizaProduto(ProduId, ProduNome, ProduStatus, Tipo){
		
			document.getElementById('inputProdutoId').value = ProduId;
			document.getElementById('inputProdutoNome').value = ProduNome;
			document.getElementById('inputProdutoStatus').value = ProduStatus;
					
			if (Tipo == 'edita'){	
				document.formProduto.action = "produtoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formProduto, "Tem certeza que deseja excluir esse produto?", "produtoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formProduto.action = "produtoMudaSituacao.php";
			}		
			
			document.formProduto.submit();
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
								<h3 class="card-title">Relação de Produtos</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos produtos da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right">
									<a href="produtoNovo.php" class="btn btn-success" role="button">Novo Produto</a>
									<a href="produtoImportar.php" class="btn bg-slate-700 btn-icon" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Importar Produtos"><i class="icon-drawer-in"></i></a>
									<a href="produtoExportar.php" class="btn bg-slate-700 btn-icon" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Exportar Produtos"><i class="icon-drawer-out"></i></a>									
									<!--<a href="produtoImprimir.php" class="btn bg-slate-700" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Imprimir Relação" target="_blank">Imprimir</a></div>-->
							</div>
							
							<table class="table" id="tblProduto">
								<thead>
									<tr class="bg-slate">
										<th>Código</th>
										<th>Produto</th>
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
											<td>'.$item['ProduCodigo'].'</td>
											<td>'.$item['ProduNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											<td>'.$item['SbCatNome'].'</td>
											<td>'.formataMoeda($item['ProduValorVenda']).'</td>
											');
										
										print('<td><a href="#" onclick="atualizaProduto('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaProduto('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaProduto('.$item['ProduId'].', \''.$item['ProduNome'].'\','.$item['ProduStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formProduto" method="post">
					<input type="hidden" id="inputProdutoId" name="inputProdutoId" >
					<input type="hidden" id="inputProdutoNome" name="inputProdutoNome" >
					<input type="hidden" id="inputProdutoStatus" name="inputProdutoStatus" >
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
