<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Modelo';

include('global_assets/php/conexao.php');

$sql = "SELECT ModelId, ModelNome, ModelStatus, SituaNome, SituaCor, SituaChave
		FROM Modelo
		JOIN Situacao on SituaId = ModelStatus
	    WHERE ModelUnidade = ". $_SESSION['UnidadeId'] ."
		ORDER BY ModelNome ASC";
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
	<title>Lamparinas | Modelo</title>

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
			$('#tblModelo').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Modelo
					width: "80%",
					targets: [0]
				},
				{ 
					orderable: false,   //Situação
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
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
		function atualizaModelo(ModelId, ModelNome, ModelStatus, Tipo){
		
			document.getElementById('inputModeloId').value = ModelId;
			document.getElementById('inputModeloNome').value = ModelNome;
			document.getElementById('inputModeloStatus').value = ModelStatus;
					
			if (Tipo == 'edita'){	
				document.formModelo.action = "modeloEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formModelo, "Tem certeza que deseja excluir esse modelo?", "modeloExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formModelo.action = "modeloMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formModelo.action = "modeloImprime.php";
				document.formModelo.setAttribute("target", "_blank");
			}
			
			document.formModelo.submit();
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
								<h3 class="card-title">Relação de Modelos</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="modelo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos modelos da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
								<div class="text-right"><a href="modeloNovo.php" class="btn btn-success" role="button">Novo Modelo</a></div>
							</div>
							
							<table id="tblModelo" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Modelo</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['ModelStatus'] == 1 ? 'Ativo' : 'Inativo';
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										
										print('
										<tr>
											<td>'.$item['ModelNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaModelo('.$item['ModelId'].', \''.htmlentities(addslashes($item['ModelNome']),ENT_QUOTES).'\',\''.$item['SituaChave'].'\', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaModelo('.$item['ModelId'].', \''.$item['ModelNome'].'\','.$item['ModelStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaModelo('.$item['ModelId'].', \''.$item['ModelNome'].'\','.$item['ModelStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>							
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
				
				<form name="formModelo" method="post">
					<input type="hidden" id="inputModeloId" name="inputModeloId" >
					<input type="hidden" id="inputModeloNome" name="inputModeloNome" >
					<input type="hidden" id="inputModeloStatus" name="inputModeloStatus" >
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
