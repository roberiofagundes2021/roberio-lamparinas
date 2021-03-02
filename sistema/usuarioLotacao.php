<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Lotacao';

include('global_assets/php/conexao.php');

if (isset($_POST['inputEmpresaId'])){
	$_SESSION['EmpresaId'] = $_POST['inputEmpresaId'];
	$_SESSION['EmpresaNome'] = $_POST['inputEmpresaNome'];
}

if (!isset($_SESSION['EmpresaId'])) {
	irpara("empresa.php");
}

$sql = "SELECT UsXUnEmpresaUsuarioPerfil, UsXUnUnidade, UsXUnSetor, UsXUnLocalEstoque, UnidaNome, SetorNome
		FROM UsuarioXUnidade
		JOIN Unidade ON UnidaId = UsXUnUnidade
		JOIN Setor ON SetorId = UsXUnSetor
	    WHERE UsXUnEmpresaUsuarioPerfil = ". $_SESSION['EmpresaId'] ."
		ORDER BY UsXUnUnidade";
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
	<title>Lamparinas | Lotação</title>

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
	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblLotacao').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{ 
					orderable: true,   //Unidade
					width: "35%",
					targets: [0]
				},
				{ 
					orderable: true,   //Setor
					width: "30%",
					targets: [1]
				},
				{ 
					orderable: true,   //Local Estoque
					width: "30%",
					targets: [2]
				},								
				{ 
					orderable: false,  //Ações
					width: "5%",
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
		function atualizaLotacao( UsXUnUnidade, UsXUnSetor, UsXUnLocalEstoque, Tipo){
		
		
			document.getElementById('inputUnidade').value = UsXUnUnidade;
			document.getElementById('inputSetor').value = UsXUnSetor;
			document.getElementById('inputLocalEstoque').value = UsXUnLocalEstoque;
			
					
			if (Tipo == 'exclui'){
				confirmaExclusao(document.formLotacao, "Tem certeza que deseja excluir esse Lotação?", "usuarioLotacaoExclui.php");
			} 		
			document.formLotacao.submit();
		}		
			
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>
		
		<?php include_once("menuLeftSecundario.php"); ?>		

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
								<h3 class="card-title">Relação de Lotação</h3>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9" class="card-body">	
									A relação abaixo faz referência a Lotação do <span style="color: #FF0000; font-weight: bold;">Usúario <?php echo $_SESSION['UsuarNome']; ?></span> da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b>	
									</div>	
									
									<div class="col-lg-3">	
										<div class="text-right"><a href="usuarioLotacaoNovo.php" class="btn btn-principal" role="button">Nova Lotação</a></div>
									</div>
								</div>
							</div>
							
							<table class="table" id="tblLotacao">
								<thead>
									<tr class="bg-slate">
										<th >Unidade</th>
										<th >Setor</th>
										<th >Local de Estoque</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										
										
										print('
										<tr>
											<td>'.$item['UnidaNome'].'</td>
											<td>'.$item['SetorNome'].'</td>
											<td>'.$item['UsXUnLocalEstoque'].'</td>
											');
										
										
										print('<td class="text-center">                             
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
													<a href="#" onclick="atualizaLotacao('.$item['UsXUnUnidade'].', \''.$item['UsXUnSetor'].'\', \''.$item['UsXUnLocalEstoque'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>							
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
				
				<form name="formLotacao" method="post">
					<input type="hidden" id="inputUnidade" name="inputUnidade" >
					<input type="hidden" id="inputSetor" name="inputSetor" >
					<input type="hidden" id="inputLocalEstoque" name="inputLocalEstoque" >
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
