<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Solicitação';

include('global_assets/php/conexao.php');

$sql = "SELECT SolicId, SolicNumero, SolicData, SolicObservacao, SolicSetor, SolicSolicitante, SolicSituacao, UsuarNome, SetorNome, SituaNome, SituaChave
		FROM Solicitacao
		JOIN Usuario on UsuarId = SolicSolicitante
		JOIN Setor on SetorId = SolicSetor
		JOIN Situacao on SituaId = SolicSituacao
	    WHERE SolicEmpresa = ". $_SESSION['EmpreId'] ."
		ORDER BY SolicData DESC";
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
	<title>Lamparinas | Solicitação</title>

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
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	<!-- /theme JS files -->	
	
	<script type="text/javascript" >
			
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblSolicitacao').DataTable( {
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Numero
					width: "10%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Lote
					width: "10%",
					targets: [2]
				},
				{ 
					orderable: true,   //Tipo
					width: "15%",
					targets: [3]
				},
				{ 
					orderable: true,   //Fornecedor
					width: "20%",
					targets: [4]
				},
				{ 
					orderable: true,   //Processo
					width: "10%",
					targets: [5]
				},
				{ 
					orderable: true,   //Categoria
					width: "15%",
					targets: [6]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [7]
				},
				{ 
					orderable: false,  //Ações
					width: "5%",
					targets: [8]
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
		function atualizaSolicitacao(SolicId, SolicNumero, SolicSituacao, SolicSituacaoChave, Tipo){
		
			document.getElementById('inputSolicitacaoId').value = SolicId;
			document.getElementById('inputSolicitacaoNumero').value = SolicNumero;
			document.getElementById('inputSolicitacaoStatus').value = SolicSituacao;
			
			if (Tipo == 'imprimir'){
				if (SolicSituacaoChave == 'PENDENTE'){
					alerta('Atenção','Enquanto o status estiver PENDENTE de liberação a impressão não poderá ser realizada!','error');
					return false;
				} else if (SolicSituacaoChave == 'NAOLIBERADO'){			
					alerta('Atenção','A ordem de compra/contrato não foi liberada, portanto, a impressão não poderá ser realizada!','error');
					return false;
				} else {
					document.formSolicitacao.action = "solicitacaoImprime.php";
					document.formSolicitacao.setAttribute("target", "_blank");
				}
			} else {
				if (Tipo == 'edita'){	
					document.formSolicitacao.action = "solicitacaoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formSolicitacao, "Tem certeza que deseja excluir essa ordem de compra?", "solicitacaoExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formSolicitacao.action = "solicitacaoMudaSituacao.php";
				} else if (Tipo == 'produto'){
					document.formSolicitacao.action = "solicitacaoProduto.php";
				} else if (Tipo == 'duplica'){
					document.formSolicitacao.action = "solicitacaoDuplica.php";
				}
				document.formSolicitacao.setAttribute("target", "_self");
			}
			
			document.formSolicitacao.submit();
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
								<h5 class="card-title">Relação de Solicitações de Material</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência às solicitações da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
								<div class="text-right"><a href="solicitacaoNovo.php" class="btn btn-success" role="button">Nova Solicitação</a></div>
							</div>
							
							<table class="table" id="tblSolicitacao">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Número</th>
										<th>Setor</th>
										<th>Solicitante</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SituaNome'];
										
										if ($item['SituaChave'] == 'PENDENTE'){
											$situacaoClasse = 'badge badge-flat border-primary text-danger-600';
										} else if ($item['SituaChave'] == 'NAOLIBERADO'){
											$situacaoClasse = 'badge badge-flat border-danger text-danger-600';
										} else{
											$situacaoClasse = 'badge badge-flat border-success text-success-600';
										} 
									
										print('
										<tr>
											<td>'.mostraData($item['SolicData']).'</td>
											<td>'.$item['SolicNumero'].'</td>
											<td>'.$item['SetorNome'].'</td>
											<td>'.$item['UsuarNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaSolicitacao('.$item['SolicId'].', \''.$item['SolicNumero'].'\','.$item['SolicSituacao'].',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="'.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaSolicitacao('.$item['SolicId'].', \''.$item['SolicNumero'].'\','.$item['SolicSituacao'].',\''.$item['SituaChave'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Ordem de Compra"></i></a>
														<a href="#" onclick="atualizaSolicitacao('.$item['SolicId'].', \''.$item['SolicNumero'].'\','.$item['SolicSituacao'].',\''.$item['SituaChave'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Ordem de Compra"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaSolicitacao('.$item['SolicId'].', \''.$item['SolicNumero'].'\','.$item['SolicSituacao'].',\''.$item['SituaChave'].'\',  \'produto\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																<a href="#" onclick="atualizaSolicitacao('.$item['SolicId'].', \''.$item['SolicNumero'].'\','.$item['SolicSituacao'].',\''.$item['SituaChave'].'\',  \'imprimir\')" class="dropdown-item" title="Imprimir"><i class="icon-printer2"></i> Imprimir</a>
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
				
				<form name="formSolicitacao" method="post">
					<input type="hidden" id="inputSolicitacaoId" name="inputSolicitacaoId" >
					<input type="hidden" id="inputSolicitacaoNumero" name="inputSolicitacaoNumero" >
					<input type="hidden" id="inputSolicitacaoStatus" name="inputSolicitacaoStatus" >
					<input type="hidden" id="inputSolicitacaoTipo" name="inputSolicitacaoTipo" >
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
