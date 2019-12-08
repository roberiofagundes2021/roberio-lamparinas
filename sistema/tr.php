<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Termo de Referência';

include('global_assets/php/conexao.php');

$sql = ("SELECT TrRefId, TrRefNumero, TrRefData, TrRefCategoria, TrRefSubCategoria, CategNome, SbCatNome, TrRefStatus
		 FROM TermoReferencia
		 JOIN Categoria on CategId = TrRefCategoria
		 LEFT JOIN SubCategoria on SbCatId = TrRefSubCategoria
	     WHERE TrRefEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY TrRefData DESC");
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
	<title>Lamparinas | Termo de Referência</title>

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
			$('#tblTR').DataTable( {
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Nº TR
					width: "15%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Categoria
					width: "30%",
					targets: [2]
				},
				{ 
					orderable: true,   //SubCategoria
					width: "30%",
					targets: [3]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [4]
				},
				{ 
					orderable: false,  //Ações
					width: "10%",
					targets: [5]
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
		function atualizaTR(TRId, TRNumero, TRCategoria, CategNome, TRStatus, Tipo){
		
			document.getElementById('inputTRId').value = TRId;
			document.getElementById('inputTRNumero').value = TRNumero;
			document.getElementById('inputTRCategoria').value = TRCategoria;
			document.getElementById('inputTRNomeCategoria').value = CategNome;
			document.getElementById('inputTRStatus').value = TRStatus;
			

			if (Tipo == 'imprimir'){
				document.formTR.action = "trImprime.php";
				document.formTR.setAttribute("target", "_blank");
			} else {
				if (Tipo == 'edita'){
					document.formTR.action = "trEdita.php";
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formTR, "Tem certeza que deseja excluir essa TR?", "trExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formTR.action = "trMudaSituacao.php";
				} else if (Tipo == 'produto'){
					document.formTR.action = "trProduto.php";
				} else if (Tipo == 'orcamento'){
					document.formTR.action = "trOrcamento.php";
				}
			}
			
			document.formTR.submit();
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
								<h5 class="card-title">Relação de Termos de Referência</h5>
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
								<div class="text-right"><a href="trNovo.php" class="btn btn-success" role="button">Nova TR</a></div>
							</div>
							
							<table class="table" id="tblTR">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Nº do TR</th>
										<th>Categoria</th>
										<th>SubCategoria</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){

										 $sql = "SELECT SbCatId, SbCatNome
				                                        FROM SubCategoria
				                                        JOIN TRXSubcategoria on TRXSCSubcategoria = SbCatId
				                                        WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and TRXSCTermoReferencia = ".$item['TrRefId']."
				                                            ORDER BY SbCatNome ASC";
		                                        $result = $conn->query($sql);
		                                        $rowSC = $result->fetchAll(PDO::FETCH_ASSOC);
													        
										
										$situacao = $item['TrRefStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['TrRefStatus'] ? 'badge-success' : 'badge-secondary';
										
										//$telefone = isset($item['ForneTelefone']) ? $item['ForneTelefone'] : $item['ForneCelular'];

										print('
										    <tr>
											    <td>'.mostraData($item['TrRefData']).'</td>
											    <td>'.$item['TrRefNumero'].'</td>
											    <td>'.$item['CategNome'].'</td>
										');
                                       
                                        if(!$rowSC){
											$seleciona = $item['SbCatNome'];
                                      
											print('
												<td>
											        <div class="d-flex flex-row">
                                                        <div class="p-1">
                                                            <div>'.$seleciona.'</div>
                                                        </div>
											        </div>
											    </td>
											');
										} else {
											print('<td>
                                                      <div class="d-flex flex-row">
												');
                                            foreach ($rowSC as $key => $a) {
                                            	if(count($rowSC) == $key + 1){
                                                    print('
                                                        <div class="py-1 pr-1 pl-0 ">
                                                            <div>'.$a['SbCatNome'].'</div>
                                                        </div>
											        ');
                                            	} else {
                                            		print('
                                                        <div class="py-1 pl-1 pr-0 ">
                                                            <div style="margin-right: 3px;">'.$a['SbCatNome'].'  |</div>
                                                        </div>
											        ');
                                            	}
											}
											print('
                                                   </div>
												</td>
											');
										}
										
										
										
										print('<td><a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar TR"></i></a>
														<a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir TR"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'produto\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																<a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'orcamento\');" class="dropdown-item"><i class="icon-coin-dollar" title="Orçamentos"></i> Orçamentos</a>
																<a href="#" onclick="atualizaTR('.$item['TrRefId'].', \''.$item['TrRefNumero'].'\', \''.$item['TrRefCategoria'].'\', \''.$item['CategNome'].'\','.$item['TrRefStatus'].', \'imprimir\');" class="dropdown-item" title="Imprimir TR"><i class="icon-printer2"></i> Imprimir TR</a>
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
				
				<form name="formTR" method="post">
					<input type="hidden" id="inputTRId" name="inputTRId" >
					<input type="hidden" id="inputTRNumero" name="inputTRNumero" >
					<input type="hidden" id="inputTRCategoria" name="inputTRCategoria" >
					<input type="hidden" id="inputTRNomeCategoria" name="inputTRNomeCategoria" >
					<input type="hidden" id="inputTRStatus" name="inputTRStatus" >
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
