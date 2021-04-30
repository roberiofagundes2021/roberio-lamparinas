<?php 
	include_once("sessao.php"); 
	include('global_assets/php/conexao.php');

	$_SESSION['PaginaAtual'] = 'Dotação Orçamentária';

	if (isset($_POST['inputTRId'])){
		$_SESSION['inputTRIdDotacao'] = $_POST['inputTRId'];
	}

	$sql = "
		SELECT DtOrcId, 
					 DtOrcData, 
					 DtOrcNome, 
					 DtOrcArquivo
			FROM DotacaoOrcamentaria
		 WHERE DtOrcUnidade = ". $_SESSION['UnidadeId'] ." 
			 AND DtOrcTermoReferencia = ". $_SESSION['inputTRIdDotacao'] ."
		 ORDER BY DtOrcNome ASC
	";
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Dotação Orçamentária</title>

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
			$('#tblClienteAnexo').DataTable( {
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
		function atualizaClienteAnexo(ClAneId, ClAneData, ClAneNome, ClAneArquivo, Tipo){

				document.getElementById('inputDotacaoID').value = ClAneId;
				document.getElementById('inputDotacaoNome').value = ClAneNome;
				document.getElementById('inputDotacaoData').value = ClAneData;
				document.getElementById('inputDotacaoArquivo').value = ClAneArquivo;	

				if (Tipo == 'edita'){	
					document.formDotacao.action = "trDotacaoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formDotacao, "Tem certeza que deseja excluir esse Anexo", "trDotacaoExclui.php");
			}
			
			document.formDotacao.submit();
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
								<h3 class="card-title">Relação de Dotações Orçamentárias</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="tr.php" class="list-icons-item" data-action="reload"></a>
									</div>
								</div>
							</div>

							<div class="card-body">
								<p>
									A relação abaixo faz referência as Dotações Orçamentárias do Termo de Referência <span style="color: #FF0000; font-weight: bold;"> <?php echo $_POST['inputTRNumero']; ?> </span>
								</p>

								<form name="formDotacao" id="formDotacao" method="post" enctype="multipart/form-data" class="form-validate-jquery">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" placeholder="Data" value="<?php echo date('d/m/Y'); ?>"  readOnly>
											</div>
										</div>
										<div class="col-lg-10">
											<div class="form-group">
												<label for="inputNome">Descrição<span class="text-danger"> *</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Descrição" required autofocus>
											</div>
										</div>
									</div>	
									<div class="row">
										<div class="col-lg-12">
											<label for="inputArquivo">Arquivo<span class="text-danger"> *</span></label>
											<input type="file" id="inputArquivo" name="inputArquivo" class="form-control" required>
										</div>
									</div>	
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">										
												Obs.: arquivos permitidos (.pdf, .doc, .docx, .odt, .jpg, .jpeg, .png) Tamanho máximo: 32MB
											</div>
										</div>									
									</div>												
									<div class="row" style="margin-top: 30px;">
										<div class="col-lg-12">								
											<div class="form-group">
												<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
												<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
											</div>
										</div>
									</div>
								</form>
							</div>

							
							<table class="table" id="tblClienteAnexo">
								<thead>
									<tr class="bg-slate">
										<th>Data</th>
										<th>Descrição</th>
										<th>Arquivo</th>										
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach ($row as $item){
										print('
										<tr>
												<td>'.mostraData($item['ClAneData']).'</td>
																						<td>'.$item['ClAneNome'].'</td>
											<td><a href="global_assets/anexos/cliente/'.$item['ClAneArquivo'].'" target="_blank">'.$item['ClAneArquivo'].'</a></td>
											');
																										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
															<a href="#" onclick="atualizaClienteAnexo('.$item['ClAneId'].', \''.$item['ClAneData'].'\',\''.$item['ClAneNome'].'\', \''.$item['ClAneArquivo'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
															<a href="#" onclick="atualizaClienteAnexo('.$item['ClAneId'].', \''.$item['ClAneData'].'\',\''.$item['ClAneNome'].'\', \''.$item['ClAneArquivo'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				<form name="formDotacao" method="post">
					<input type="hidden" id="inputDotacaoID" name="inputDotacaoID">
					<input type="hidden" id="inputDotacaoData" name="inputDotacaoData">
					<input type="hidden" id="inputDotacaoNome" name="inputDotacaoNome">
					<input type="hidden" id="inputDotacaoArquivo" name="inputDotacaoArquivo">
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
