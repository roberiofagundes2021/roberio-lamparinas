<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'TR / Orçamento';

if (isset($_POST['inputTRId'])){
	
	//echo "Entrou";
	$_SESSION['TRId'] = $_POST['inputTRId'];
	$_SESSION['TRNumero'] = $_POST['inputTRNumero'];
}

$sql = ("SELECT TrRefNumero, TrXOrId, TrXOrNumero, TrXOrData, TrXOrCategoria, ForneNome, CategNome, SbCatNome, TrXOrStatus
		 FROM TRXOrcamento
		 JOIN TermoReferencia on TrRefId = TrXOrTermoReferencia
		 LEFT JOIN Fornecedor on ForneId = TrXOrFornecedor
		 JOIN Categoria on CategId = TrXOrCategoria
		 LEFT JOIN SubCategoria on SbCatId = TrXOrSubCategoria
	     WHERE TrXOrEmpresa = ". $_SESSION['EmpreId'] ." and TrXOrTermoReferencia = ".$_SESSION['TRId']."
		 ORDER BY TrXOrData DESC");
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
	<title>Lamparinas | Orçamento</title>

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
			$('#tblOrcamento').DataTable( {
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
		function atualizaOrcamento(TrXOrId, TrXOrNumero, TrXOrCategoria, CategNome, TrXOrStatus, Tipo){
		
			document.getElementById('inputOrcamentoId').value = TrXOrId;
			document.getElementById('inputOrcamentoNumero').value = TrXOrNumero;
			document.getElementById('inputOrcamentoCategoria').value = TrXOrCategoria;
			document.getElementById('inputOrcamentoNomeCategoria').value = CategNome;
			document.getElementById('inputOrcamentoStatus').value = TrXOrStatus;
			

			if (Tipo == 'imprimir'){
				document.formOrcamento.action = "trOrcamentoImprime.php";
				document.formOrcamento.setAttribute("target", "_blank");
			} else {
				if (Tipo == 'edita'){	
					document.formOrcamento.action = "trOrcamentoEdita.php";
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formOrcamento, "Tem certeza que deseja excluir esse orcamento?", "trOrcamentoExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formOrcamento.action = "trOrcamentoMudaSituacao.php";
				} else if (Tipo == 'produto'){
					document.formOrcamento.action = "trOrcamentoProduto.php";
				} else if (Tipo == 'duplica'){
					document.formOrcamento.action = "trOrcamentoDuplica.php";
				}
			}
			
			document.formOrcamento.submit();
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
								<h5 class="card-title">Relação de Orçamentos</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência aos orçamentos da <span style="color: #FF0000; font-weight: bold;">TR nº <?php echo $_SESSION['TRNumero']; ?></span> da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
								<div class="text-right"><a href="tr.php" role="button"><< Termo de Referência</a>&nbsp;&nbsp;&nbsp;
								<a href="trOrcamentoNovo.php" class="btn btn-success" role="button">Novo Orçamento</a></div>
							</div>
							
							<table class="table" id="tblOrcamento">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Nº Orçamento</th>
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
										
										$situacao = $item['TrXOrStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['TrXOrStatus'] ? 'badge-success' : 'badge-secondary';
										
										//$telefone = isset($item['ForneTelefone']) ? $item['ForneTelefone'] : $item['ForneCelular'];
										
										print('
										<tr>
											<td>'.mostraData($item['TrXOrData']).'</td>
											<td>'.$item['TrXOrNumero'].'</td>
											<td>'.$item['ForneNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											<td>'.$item['SbCatNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Orçamento"></i></a>
														<a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Orçamento"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'produto\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																<a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'imprimir\');" class="dropdown-item" title="Imprimir Lista"><i class="icon-printer2"></i> Imprimir Orçamento</a>
																<a href="#" onclick="atualizaOrcamento('.$item['TrXOrId'].', \''.$item['TrXOrNumero'].'\', \''.$item['TrXOrCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrXOrStatus'].', \'duplica\');" class="dropdown-item" title="Duplicar Orçamento"><i class="icon-popout"></i> Duplicar Orçamento</a>
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
				
				<form name="formOrcamento" method="post">				
					<input type="hidden" id="inputOrcamentoId" name="inputOrcamentoId" >
					<input type="hidden" id="inputOrcamentoNumero" name="inputOrcamentoNumero" >
					<input type="hidden" id="inputOrcamentoCategoria" name="inputOrcamentoCategoria" >
					<input type="hidden" id="inputOrcamentoNomeCategoria" name="inputOrcamentoNomeCategoria" >
					<input type="hidden" id="inputOrcamentoStatus" name="inputOrcamentoStatus" >
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