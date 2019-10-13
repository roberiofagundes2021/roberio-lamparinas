<?php 

include_once("sessao.php"); 
include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Produto Orçamento';

include('global_assets/php/conexao.php');

$sql = ("SELECT PrOrcNome, PrOrcDetalhamento, PrOrcCategoria, PrOrcSubcategoria, PrOrcUnidadeMedida, PrOrcSituacao, PrOrcId
		 FROM ProdutoOrcamento
	     WHERE PrOrcEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY PrOrcNome ASC");
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
	<title>Lamparinas | Modelo</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaModelo(ModelId, ModelNome, ModelStatus, Tipo){
		
			document.getElementById('inputModeloId').value = ModelId;
			document.getElementById('inputModeloNome').value = ModelNome;
			document.getElementById('inputModeloStatus').value = ModelStatus;
					
			if (Tipo == 'edita'){	
				document.formModelo.action = "modeloEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formModelo, "Tem certeza que deseja excluir esse modelo?", "modeloExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formModelo.action = "modeloMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formModelo.action = "modeloImprime.php";
				document.formModelo.setAttribute("target", "_blank");
			}
			
			document.formModelo.submit();
		}		
			
	</script>

	<script>
			
		function atualizaModelo(PrOrcId, PrOrcNome, PrOrcStatus, Tipo){
		
			document.getElementById('inputPrOrcId').value = PrOrcId;
			document.getElementById('inputPrOrcNome').value = PrOrcNome;
			document.getElementById('inputPrOrcStatus').value = PrOrcStatus;
					
			if (Tipo == 'edita'){	
				document.formPrOrc.action = "produtoOrcamentoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formPrOrc, "Tem certeza que deseja excluir esse Produto?", "produtoOrcamentoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formPrOrc.action = "produtoOrcamentoMudaStatus.php";
			}

			document.formPrOrc.submit();
		}

  

        /* Início: Tabela Personalizada */
			$('#tblProduto').DataTable( {
				"order": [[ 1, "asc" ]],
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
								<h3 class="card-title">Relação de Produtos para Orçamento</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="modelo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos produtos para orçamento da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="produtoOrcamentoNovo.php" class="btn btn-success" role="button">Novo Produto</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Produto</th>
										<th>Categoria</th>
										<th>Subcategoria</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){

										$sql = ("SELECT CategNome
											FROM Categoria															     
											WHERE CategId = ".$item['PrOrcCategoria']." and CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
												ORDER BY CategNome ASC");
											$result = $conn->query("$sql");
											$categ = $result->fetch(PDO::FETCH_ASSOC);

										$sql = ("SELECT SbCatNome
											FROM SubCategoria															     
											WHERE SbCatId = ".$item['PrOrcSubcategoria']." and SbCatStatus = 1 and SBCatEmpresa = ". $_SESSION['EmpreId'] ."
												ORDER BY SbCatNome ASC");
											$result = $conn->query("$sql");
											$subCateg = $result->fetch(PDO::FETCH_ASSOC);
													
										
										$situacao = $item['PrOrcSituacao'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['PrOrcSituacao'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['PrOrcNome'].'</td>
											<td>'.$categ['CategNome'].'</td>
											<td>'.$subCateg['SbCatNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaModelo('.$item['PrOrcId'].', \''.$item['PrOrcNome'].'\','.$item['PrOrcSituacao'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaModelo('.$item['PrOrcId'].', \''.$item['PrOrcNome'].'\','.$item['PrOrcSituacao'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaModelo('.$item['PrOrcId'].', \''.$item['PrOrcNome'].'\','.$item['PrOrcSituacao'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>							
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
				
				<form name="formPrOrc" method="post">
					<input type="hidden" id="inputPrOrcId" name="inputPrOrcId" >
					<input type="hidden" id="inputPrOrcNome" name="inputPrOrcNome" >
					<input type="hidden" id="inputPrOrcStatus" name="inputPrOrcStatus" >
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
