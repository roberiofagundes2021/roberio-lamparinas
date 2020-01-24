<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Plano de Contas';

include('global_assets/php/conexao.php');

$sql = "SELECT PlConId, PlConNome, PlConStatus, CeCusNome
		 FROM PlanoContas
		 JOIN CentroCusto on CeCusId = PlConCentroCusto
	     WHERE PlConEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY PlConNome ASC";
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
	<title>Lamparinas | Plano de Contas</title>

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
			
			/* Início: Tabela Personalizada */
			$('#tblPlanoContas').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Plano de Contas
					width: "35%",
					targets: [0]
				},	
				{
					orderable: true,   //Centro de Custo
					width: "35%",
					targets: [1]
				},
				{ 
					orderable: true,   //Situação
					width: "15%",
					targets: [2]
				},
				{ 
					orderable: false,   //Ações
					width: "15%",
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
		function atualizaPlanoContas(PlConId, PlConNome, PlConStatus, Tipo){
		
			document.getElementById('inputPlanoContasId').value = PlConId;
			document.getElementById('inputPlanoContasNome').value = PlConNome;
			document.getElementById('inputPlanoContasStatus').value = PlConStatus;
					
			if (Tipo == 'edita'){	
				document.formPlanoContas.action = "planoContasEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formPlanoContas, "Tem certeza que deseja excluir esse Plano de Contas?", "planoContasExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formPlanoContas.action = "planoContasMudaSituacao.php";
			}
			
			document.formPlanoContas.submit();
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
								<h3 class="card-title">Relação de Planos de Contas</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="subcategoria.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência às Planos de Contas da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="planoContasNovo.php" class="btn btn-success" role="button">Novo Plano de Contas</a></div>
							</div>
							
							<table id="tblPlanoContas" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Plano de Contas</th>
										<th>Centro de Custo</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['PlConStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['PlConStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['PlConNome'].'</td>
											<td>'.$item['CeCusNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaPlanoContas('.$item['PlConId'].', \''.$item['PlConNome'].'\','.$item['PlConStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaPlanoContas('.$item['PlConId'].', \''.$item['PlConNome'].'\','.$item['PlConStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaPlanoContas('.$item['PlConId'].', \''.$item['PlConNome'].'\','.$item['PlConStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formPlanoContas" method="post">
					<input type="hidden" id="inputPlanoContasId" name="inputPlanoContasId" >
					<input type="hidden" id="inputPlanoContasNome" name="inputPlanoContasNome" >
					<input type="hidden" id="inputPlanoContasStatus" name="inputPlanoContasStatus" >
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