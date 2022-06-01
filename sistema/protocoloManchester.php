<?php 

include_once("sessao.php"); 
include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Protocolo Manchester';

$sql = "SELECT AtPrMId, AtPrMNome, AtPrMTempo, AtPrMCor, AtPrMDeterminantes, AtPrMStatus, SituaNome, SituaChave, SituaCor
		FROM AtendimentoProtocoloManchester
		JOIN Situacao ON SituaId = AtPrMStatus
	    WHERE AtPrMUnidade = ". $_SESSION['UnidadeId'] ."
		ORDER BY AtPrMNome ASC";
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
	<title>Lamparinas | Protocolo Manchester</title>

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
	
		$(document).ready(function() {		
			
			/* Início: Tabela Personalizada */
			$('#tblProtocoloManchester').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Protocolo
					width: "70%",
					targets: [0]
				},				
				{ 
					orderable: true,   //Situação
					width: "15%",
					targets: [1]
				},
				{ 
					orderable: false,  //Ações
					width: "15%",
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
			function atualizaProtocoloManchester(Permission,ProtocoloManchesterId, ProtocoloManchesterNome, ProtocoloManchesterStatus, Tipo){

				document.getElementById('inputPermission').value = Permission;
				document.getElementById('inputProtocoloManchesterId').value = ProtocoloManchesterId;
				document.getElementById('inputProtocoloManchesterNome').value = ProtocoloManchesterNome;
				document.getElementById('inputProtocoloManchesterStatus').value = ProtocoloManchesterStatus;


				//Esse ajax está sendo usado para verificar no banco se o registro já existe

					if (Tipo == 'edita'){	
						document.formProtocoloManchester.action = "protocoloManchesterEdita.php";
					} else if (Tipo == 'mudaStatus'){
						document.formProtocoloManchester.action = "protocoloManchesterMudaSituacao.php";
				 	} else if (Tipo == 'exclui'){
						if(Permission){
							confirmaExclusao(document.formProtocoloManchester, "Tem certeza que deseja excluir esse protoco loManchester?", "protocoloManchesterExclui.php");
						}	else{
							alerta('Permissão Negada!','');
							return false;
						}
					}  
				
				
				document.formProtocoloManchester.submit();
				
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
								<h3 class="card-title">Relação de Protocolos Manchester</h3>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9">
									<p class="font-size-lg">A relação abaixo faz referência aos Protocolos Manchester da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
									</div>
                                    <div class="col-lg-3">
										<div class="text-right">
											<a href="protocoloManchesterNovo.php" class="btn btn-principal" role="button">Novo Protocolo Manchester</a>
										</div>
									</div>
								</div>
							</div>
							
							<table class="table" id="tblProtocoloManchester">
								<thead>
									<tr class="bg-slate">
										<th>Nome</th>
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
											<td>'.$item['AtPrMNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaProtocoloManchester(1,'.$item['AtPrMId'].', \''.$item['AtPrMNome'].'\',\''.$item['SituaChave'].'\', \'mudaStatus\');" data-popup="tooltip" data-placement="bottom" title="Mudar Situação"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaProtocoloManchester( 1 ,'.$item['AtPrMId'].', \''.$item['AtPrMNome'].'\',\''.$item['SituaChave'].'\', \'edita\');" class="list-icons-item" data-popup="tooltip" data-placement="bottom" title="Editar Protocolo Manchester"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaProtocoloManchester( 1 ,'.$item['AtPrMId'].', \''.$item['AtPrMNome'].'\',\''.$item['SituaChave'].'\', \'exclui\');" class="list-icons-item" data-popup="tooltip" data-placement="bottom" title="Excluir Protocolo Manchester"><i class="icon-bin"></i></a>
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
				
				<form name="formProtocoloManchester" method="post">
					<input type="hidden" id="inputPermission" name="inputPermission" >
					<input type="hidden" id="inputProtocoloManchesterId" name="inputProtocoloManchesterId" >
					<input type="hidden" id="inputProtocoloManchesterNome" name="inputProtocoloManchesterNome" >
					<input type="hidden" id="inputProtocoloManchesterStatus" name="inputProtocoloManchesterStatus" >
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
