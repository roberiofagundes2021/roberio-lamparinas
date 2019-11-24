<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Orçamento';

include('global_assets/php/conexao.php');

$sql = ("SELECT OrcamId, OrcamNumero, OrcamTipo, OrcamData, OrcamCategoria, ForneNome, CategNome, OrcamStatus
		 FROM Orcamento
		 LEFT JOIN Fornecedor on ForneId = OrcamFornecedor
		 JOIN Categoria on CategId = OrcamCategoria
	     WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY OrcamData DESC");
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
		function atualizaOrcamento(OrcamId, OrcamNumero, OrcamCategoria, CategNome, OrcamStatus, Tipo){
		
			document.getElementById('inputOrcamentoId').value = OrcamId;
			document.getElementById('inputOrcamentoNumero').value = OrcamNumero;
			document.getElementById('inputOrcamentoCategoria').value = OrcamCategoria;
			document.getElementById('inputOrcamentoNomeCategoria').value = CategNome;
			document.getElementById('inputOrcamentoStatus').value = OrcamStatus;
			

			if (Tipo == 'imprimir'){
				document.formOrcamento.action = "orcamentoImprime.php";
				document.formOrcamento.setAttribute("target", "_blank");
			} else {
				if (Tipo == 'edita'){	
					document.formOrcamento.action = "orcamentoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formOrcamento, "Tem certeza que deseja excluir esse orcamento?", "orcamentoExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formOrcamento.action = "orcamentoMudaSituacao.php";
				} else if (Tipo == 'produto'){
					document.formOrcamento.action = "orcamentoProduto.php";
				} else if(Tipo == 'servico'){
                    document.formOrcamento.action = "orcamentoServico.php";
				} else if (Tipo == 'duplica'){
					document.formOrcamento.action = "orcamentoDuplica.php";
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
								A relação abaixo faz referência aos orçamentos da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
								<div class="text-right"><a href="orcamentoNovo.php" class="btn btn-success" role="button">Novo Orçamento</a></div>
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
										
										$situacao = $item['OrcamStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['OrcamStatus'] ? 'badge-success' : 'badge-secondary';
										
										//$telefone = isset($item['ForneTelefone']) ? $item['ForneTelefone'] : $item['ForneCelular'];

										 $sql = "SELECT SbCatId, SbCatNome
				                                        FROM SubCategoria
				                                        JOIN OrcamentoXSubcategoria on OrXSCSubcategoria = SbCatId
				                                        WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and OrXSCOrcamento = ".$item['OrcamId']."
				                                            ORDER BY SbCatNome ASC";
		                                        $result = $conn->query($sql);
		                                        $rowSC = $result->fetchAll(PDO::FETCH_ASSOC);

										print('
										    <tr>
											    <td>'.mostraData($item['OrcamData']).'</td>
											    <td>'.$item['OrcamNumero'].'</td>
											    <td>'.$item['ForneNome'].'</td>
											    <td>'.$item['CategNome'].'</td>
										');
                                       
                                        /*if(!$rowSC){
											$seleciona = $item['SbCatNome'];
                                      
											print('
											    <td>'.$seleciona.'</td>
											');
										} else {*/
											print('<td>');
                                            foreach ($rowSC as $a) {
											    print('
											        '.$a['SbCatNome'].' |
											    ');
											}
											print('</td>');
										/*}*/
										
										print('<td><a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										if($item['OrcamTipo'] == 'P'){
											print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Orçamento"></i></a>
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Orçamento"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'produto\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'imprimir\')" class="dropdown-item" title="Imprimir Lista"><i class="icon-printer2"></i> Imprimir Orçamento</a>
																<div class="dropdown-divider"></div>
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'duplica\')" class="dropdown-item" title="Duplicar Orçamento"><i class="icon-popout"></i> Duplicar Orçamento</a>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>');
										} else {
											print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Orçamento"></i></a>
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Orçamento"></i></a>
														<div class="dropdown">	
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'servico\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Serviços</a>
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'imprimir\')" class="dropdown-item" title="Imprimir Lista"><i class="icon-printer2"></i> Imprimir Orçamento</a>
																<div class="dropdown-divider"></div>
																<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\', \''.$item['CategNome'].'\','.$item['OrcamStatus'].', \'duplica\')" class="dropdown-item" title="Duplicar Orçamento"><i class="icon-popout"></i> Duplicar Orçamento</a>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>');
										}
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
