<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Marca';

include('global_assets/php/conexao.php');

$sql = ("SELECT MarcaId, MarcaNome, MarcaStatus
		 FROM Marca
	     WHERE MarcaEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY MarcaNome ASC");
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
	<title>Lamparinas | Marca</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblMarca').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Marca
					width: "70%",
					targets: [0]
				},
				{ 
					orderable: true,   //Situação
					width: "15%",
					targets: [1]
				},
				{ 
					orderable: true,   //Ações
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
		function atualizaMarca(MarcaId, MarcaNome, MarcaStatus, Tipo){
		
			document.getElementById('inputMarcaId').value = MarcaId;
			document.getElementById('inputMarcaNome').value = MarcaNome;
			document.getElementById('inputMarcaStatus').value = MarcaStatus;
					
			if (Tipo == 'edita'){	
				document.formMarca.action = "marcaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formMarca, "Tem certeza que deseja excluir essa marca?", "marcaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formMarca.action = "marcaMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formMarca.action = "marcaImprime.php";
				document.formMarca.setAttribute("target", "_blank");
			}
			
			document.formMarca.submit();
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
								<h3 class="card-title">Relação de Marcas</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="marca.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às marcas da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="marcaNovo.php" class="btn btn-success" role="button">Nova Marca</a></div>
							</div>
							
							<table id="tblMarca" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Marca</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['MarcaStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['MarcaStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['MarcaNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaMarca('.$item['MarcaId'].', \''.$item['MarcaNome'].'\','.$item['MarcaStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaMarca('.$item['MarcaId'].', \''.$item['MarcaNome'].'\','.$item['MarcaStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaMarca('.$item['MarcaId'].', \''.$item['MarcaNome'].'\','.$item['MarcaStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formMarca" method="post">
					<input type="hidden" id="inputMarcaId" name="inputMarcaId" >
					<input type="hidden" id="inputMarcaNome" name="inputMarcaNome" >
					<input type="hidden" id="inputMarcaStatus" name="inputMarcaStatus" >
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
