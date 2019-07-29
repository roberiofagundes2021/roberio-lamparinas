<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra';

include('global_assets/php/conexao.php');

$sql = "SELECT OrComId, OrComTipo, OrComNumero, OrComDtEmissao, OrComCategoria, ForneNome, CategNome, SbCatNome, OrComSituacao
		FROM OrdemCompra
		LEFT JOIN Fornecedor on ForneId = OrComFornecedor
		JOIN Categoria on CategId = OrComCategoria
		LEFT JOIN SubCategoria on SbCatId = OrComSubCategoria
	    WHERE OrComEmpresa = ". $_SESSION['EmpreId'] ."
		ORDER BY OrComDtEmissao DESC";
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
	<title>Lamparinas | Ordem de Compra</title>

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
	
	<script type="text/javascript" >
			
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblOrdemCompra').DataTable( {
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Nº Orçamento
					width: "15%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Fornecedor
					width: "25%",
					targets: [2]
				},
				{ 
					orderable: true,   //Categoria
					width: "20%",
					targets: [3]
				},
				{ 
					orderable: true,   //Categoria
					width: "20%",
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
		function atualizaOrdemCompra(OrComId, OrComNumero, OrComCategoria, CategNome, OrComSituacao, Tipo){
		
			document.getElementById('inputOrdemCompraId').value = OrComId;
			document.getElementById('inputOrdemCompraNumero').value = OrComNumero;
			document.getElementById('inputOrdemCompraCategoria').value = OrComCategoria;
			document.getElementById('inputOrdemCompraNomeCategoria').value = CategNome;
			document.getElementById('inputOrdemCompraStatus').value = OrComSituacao;
			

			if (Tipo == 'imprimir'){
				document.formOrdemCompra.action = "ordemcompraImprime.php";
				document.formOrdemCompra.setAttribute("target", "_blank");
			} else {
				if (Tipo == 'edita'){	
					document.formOrdemCompra.action = "ordemcompraEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formOrdemCompra, "Tem certeza que deseja excluir essa ordem de compra?", "ordemcompraExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formOrdemCompra.action = "ordemcompraMudaSituacao.php";
				} else if (Tipo == 'produto'){
					document.formOrdemCompra.action = "ordemcompraProduto.php";
				} else if (Tipo == 'duplica'){
					document.formOrdemCompra.action = "ordemcompraDuplica.php";
				}
			}
			
			document.formOrdemCompra.submit();
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
								<h5 class="card-title">Relação de Ordens de Compra</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência às ordens de compra da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
								<div class="text-right"><a href="ordemcompraNovo.php" class="btn btn-success" role="button">Nova Ordem de Compra</a></div>
							</div>
							
							<table class="table" id="tblOrdemCompra">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Nº Ordem Compra</th>
										<th>Fornecedor</th>
										<th>Categoria</th>
										<th>SubCategoria</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['OrComSituacao'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['OrComSituacao'] ? 'badge-success' : 'badge-secondary';
										
										//$telefone = isset($item['ForneTelefone']) ? $item['ForneTelefone'] : $item['ForneCelular'];
										
										print('
										<tr>
											<td>'.mostraData($item['OrComDtEmissao']).'</td>
											<td>'.$item['OrComNumero'].'</td>
											<td>'.$item['ForneNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											<td>'.$item['SbCatNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Ordem de Compra"></i></a>
														<a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Ordem de Compra"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'produto\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																<a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'imprimir\')" class="dropdown-item" title="Imprimir Lista"><i class="icon-printer2"></i> Imprimir Ordem de Compra</a>
																<div class="dropdown-divider"></div>
																<a href="#" onclick="atualizaOrdemCompra('.$item['OrComId'].', \''.$item['OrComNumero'].'\', \''.$item['OrComCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrComSituacao'].', \'duplica\')" class="dropdown-item" title="Duplicar Orçamento"><i class="icon-popout"></i> Duplicar Ordem de Compra</a>
															</div>
														</div>
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
				
				<form name="formOrdemCompra" method="post">
					<input type="hidden" id="inputOrdemCompraId" name="inputOrdemCompraId" >
					<input type="hidden" id="inputOrdemCompraNumero" name="inputOrdemCompraNumero" >
					<input type="hidden" id="inputOrdemCompraCategoria" name="inputOrdemCompraCategoria" >
					<input type="hidden" id="inputOrdemCompraNomeCategoria" name="inputOrdemCompraNomeCategoria" >
					<input type="hidden" id="inputOrdemCompraStatus" name="inputOrdemCompraStatus" >
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
