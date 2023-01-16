<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fornecedor Anexo';

include('global_assets/php/conexao.php');

if (isset($_POST['inputFornecedorId'])){
	$_SESSION['idFornecedor'] = $_POST['inputFornecedorId'];
	$_SESSION['nomeFornecedor'] = $_POST['inputFornecedorNome']; 
}

$sql = "SELECT FnAneId, FnAneData, FnAneNome, FnAneArquivo
        FROM FornecedorAnexo
        WHERE FnAneEmpresa = ". $_SESSION['EmpreId'] ." and FnAneFornecedor = ". $_SESSION['idFornecedor'] ."
        ORDER BY FnAneNome ASC";
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
	<title>Lamparinas | Fornecedor Anexo</title>

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
			$('#tblFornecedorAnexo').DataTable( {
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
		function atualizaFornecedorAnexo(FnAneId, FnAneData, FnAneNome, FnAneArquivo, Tipo){

				document.getElementById('inputFornecedorAnexoId').value = FnAneId;
				document.getElementById('inputFornecedorAnexoNome').value = FnAneNome;
				document.getElementById('inputFornecedorAnexoData').value = FnAneData;
				document.getElementById('inputFornecedorAnexoArquivo').value = FnAneArquivo;	

				if (Tipo == 'edita'){	
					document.formFornecedorAnexo.action = "fornecedorAnexoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formFornecedorAnexo, "Tem certeza que deseja excluir esse Anexo", "fornecedorAnexoExclui.php");
			}
			
			document.formFornecedorAnexo.submit();
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
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9">
										A relação abaixo faz referência aos Anexos do fornecedor <span style="color: #FF0000; font-weight: bold;"> <?php echo $_SESSION['nomeFornecedor']; ?> </span> da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
									</div>
									<div class="col-lg-3" style="margin-top: -10px;">
										<div class="text-right"><a href="fornecedor.php" role="button"><< Fornecedor</a>&nbsp;&nbsp;&nbsp;
										<a href="fornecedorAnexoNovo.php" class="btn btn-principal" role="button">Novo Anexo</a></div>
									</div>
								</div>
							</div>
							
							<table class="table" id="tblFornecedorAnexo">
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
										    <td>'.mostraData($item['FnAneData']).'</td>
                                            <td>'.$item['FnAneNome'].'</td>
											<td><a href="global_assets/anexos/fornecedor/'.$item['FnAneArquivo'].'" target="_blank">'.$item['FnAneArquivo'].'</a></td>
											');
																										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
															<a href="#" onclick="atualizaFornecedorAnexo('.$item['FnAneId'].', \''.$item['FnAneData'].'\',\''.$item['FnAneNome'].'\', \''.$item['FnAneArquivo'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
															<a href="#" onclick="atualizaFornecedorAnexo('.$item['FnAneId'].', \''.$item['FnAneData'].'\',\''.$item['FnAneNome'].'\', \''.$item['FnAneArquivo'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formFornecedorAnexo" method="post">
					<input type="hidden" id="inputFornecedorAnexoId" name="inputFornecedorAnexoId">
					<input type="hidden" id="inputFornecedorAnexoData" name="inputFornecedorAnexoData">
					<input type="hidden" id="inputFornecedorAnexoNome" name="inputFornecedorAnexoNome">
					<input type="hidden" id="inputFornecedorAnexoArquivo" name="inputFornecedorAnexoArquivo">
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
