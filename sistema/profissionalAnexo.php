<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Profissional Anexo';

include('global_assets/php/conexao.php');

if (isset($_POST['inputProfissionalId'])){
	$_SESSION['idProfissional'] = $_POST['inputProfissionalId'];
	$_SESSION['nomeProfissional'] = $_POST['inputProfissionalNome']; 
}

$sql = "SELECT PrAneId, PrAneData, PrAneNome, PrAneArquivo
        FROM ProfissionalAnexo
        WHERE PrAneUnidade = ". $_SESSION['UnidadeId'] ." and PrAneProfissional = ". $_SESSION['idProfissional'] ."
        ORDER BY PrAneNome ASC";
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
	<title>Lamparinas | Profissional Anexo</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblProfissionalAnexo').DataTable( {
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Data
					width: "14%",
					targets: [0]
				},
				{ 
					orderable: true,   //Nome
					width: "38%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Arquivo
					width: "38%",
					targets: [2]
				},
				{ 
					orderable: false,  //Ações
					width: "10%",
					targets: [3]
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
		function atualizaProfissionalAnexo(PrAneId, PrAneData, PrAneNome, PrAneArquivo, Tipo){

				document.getElementById('inputProfissionalAnexoId').value = PrAneId;
				document.getElementById('inputProfissionalAnexoNome').value = PrAneNome;
				document.getElementById('inputProfissionalAnexoData').value = PrAneData;
				document.getElementById('inputProfissionalAnexoArquivo').value = PrAneArquivo;	

				if (Tipo == 'edita'){	
					document.formProfissionalAnexo.action = "profissionalAnexoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formProfissionalAnexo, "Tem certeza que deseja excluir esse Anexo", "profissionalAnexoExclui.php");
			}
			
			document.formProfissionalAnexo.submit();
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
								<h3 class="card-title">Relação de Anexos</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="profissionalAnexo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência aos Anexos do profissional <span style="color: #FF0000; font-weight: bold;"> <?php echo $_SESSION['nomeProfissional']; ?> </span> da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b>
								
								<div class="text-right"><a href="profissional.php" role="button"><< Profissional</a>&nbsp;&nbsp;&nbsp;
								<a href="profissionalAnexoNovo.php" class="btn btn-principal" role="button">Novo Anexo</a></div>
							</div>
							
							<table class="table" id="tblProfissionalAnexo">
								<thead>
									<tr class="bg-slate">
                                        <th>Data</th>
										<th>Descrição</th>
										<th>Arquivo</th>										
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php

									foreach ($row as $item){
										
										print('
										<tr>
										    <td>'.mostraData($item['PrAneData']).'</td>
                                            <td>'.$item['PrAneNome'].'</td>
											<td><a href="global_assets/anexos/profissional/'.$item['PrAneArquivo'].'" target="_blank">'.$item['PrAneArquivo'].'</a></td>
											');
																										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
															<a href="#" onclick="atualizaProfissionalAnexo('.$item['PrAneId'].', \''.$item['PrAneData'].'\',\''.$item['PrAneNome'].'\', \''.$item['PrAneArquivo'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
															<a href="#" onclick="atualizaProfissionalAnexo('.$item['PrAneId'].', \''.$item['PrAneData'].'\',\''.$item['PrAneNome'].'\', \''.$item['PrAneArquivo'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formProfissionalAnexo" method="post">
					<input type="hidden" id="inputProfissionalAnexoId" name="inputProfissionalAnexoId">
					<input type="hidden" id="inputProfissionalAnexoData" name="inputProfissionalAnexoData">
					<input type="hidden" id="inputProfissionalAnexoNome" name="inputProfissionalAnexoNome">
					<input type="hidden" id="inputProfissionalAnexoArquivo" name="inputProfissionalAnexoArquivo">
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
