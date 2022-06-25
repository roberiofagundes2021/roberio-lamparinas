<?php 

include_once("sessao.php");

$sql = "SELECT MenuId,MenuNome,MenuUrl,MenuIco,ModulNome,MenuModulo,MenuPai,MenuLevel,MenuOrdem,MenuSubMenu,MenuSetorPublico,
MenuSetorPrivado,MenuPosicao,MenuUsuarioAtualizador,MenuStatus
FROM Menu
JOIN Situacao ON SituaId = MenuStatus
JOIN Modulo ON ModulId = MenuModulo
WHERE SituaChave = 'ATIVO'";
$result = $conn->query($sql);
$rowMenu = $result->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Menus</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>		
	<!-- /theme JS files -->	

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	

	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >
        $(document).ready(function() {
        	$('#tblMenu').DataTable({
				"order": [
					[0, "asc"]
				],
				autoWidth: false,
				responsive: true,
				columnDefs: [{
						orderable: true, //Nome
						width: "10%",
						targets: [0]
					},
					{
						orderable: false, //Url
						width: "20%",
						targets: [1]
					},
					{
						orderable: false, //Icone
						width: "10%",
						targets: [2]
					},
					{
						orderable: false, //Modulo
						width: "20%",
						targets: [3]
					},
					{
						orderable: false, //Nível
						width: "5%",
						targets: [4]
					},
					{
						orderable: false, //Ordem
						width: "5%",
						targets: [5]
					},
					{
						orderable: false, //SubMenu
						width: "5%",
						targets: [6]
					},
					{
						orderable: false, //Publico
						width: "10%",
						targets: [7]
					},
					{
						orderable: false, //Posição
						width: "10%",
						targets: [8]
					},
					{
						orderable: false, //Ações
						width: "5%",
						targets: [9]
					}
				],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: {
						'first': 'Primeira',
						'last': 'Última',
						'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
						'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
					}
				}
			});

			$('#body').addClass('sidebar-xs')
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
        }); // document.ready

		function atualizaMenu(tipo, id){
			if(tipo == 'ATUALIZAR'){
				$('#tipo').val('ATUALIZAR')
				$('#id').val(id)
				$('#formMenu').submit()
			} else {
				$('#tipo').val('EXCLUIR')
				$('#id').val(id)
				$('#formMenu').submit()
			}
		}
    </script>	
	
</head>

<body id="body" class="navbar-top">

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
				<div class="card">
					<div class="card-header header-elements-inline">
						<h3 class="card-title">Relação de Menus</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-9">
									A relação abaixo faz referência à todos os menus</b>
							</div>
							<div class="col-lg-3">
								<div class="text-right"><a href="menuCriar.php" class="btn btn-principal" role="button">Novo Menu</a></div>
							</div>
						</div>
					</div>
					
					<div class="card-body">
						<table class="table" id="tblMenu">
							<thead>
								<tr class="bg-slate">
									<th>Nome</th>
									<th>Url</th>
									<th>Icone</th>
									<th>Modulo</th>
									<th>Nível</th>
									<th>Ordem</th>
									<th>SubMenu</th>
									<th>Setor Publico</th>
									<th>Posição</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach ($rowMenu as $item) {
										$publico = $item['MenuSetorPublico']?'SIM': 'NÃO';
										$subMenu = $item['MenuSubMenu']?'SIM': 'NÃO';
										$atualiza = "$item[MenuId]";
										print("
											<tr>
												<td>$item[MenuNome]</td>
												<td>$item[MenuUrl]</td>
												<td>$item[MenuIco]</td>
												<td>$item[ModulNome]</td>
												<td>$item[MenuLevel]</td>
												<td>$item[MenuOrdem]</td>
												<td>$subMenu</td>
												<td>$publico</td>
												<td>$item[MenuPosicao]</td>
												<td>
													<div class='list-icons'>
														<div class='list-icons list-icons-extended'>
															<a href='#' onclick='atualizaMenu(\"ATUALIZAR\",\"$atualiza\")' class='list-icons-item' data-popup='tooltip' data-placement='bottom' title='Editar Produto'><i class='icon-pencil7'></i></a>
															<a href='#' onclick='atualizaMenu(\"EXCLUIR\",\"$atualiza\")' class='list-icons-item'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-bin'></i></a>
														</div>
													</div>
												</td>
											</tr>
										");
									}
								?>
							</tbody>
						</table>
					</div>
					<form name="formMenu" id="formMenu" method="post" action="menuCriar.php">
						<input id="tipo" type="hidden" name="tipo" value="ATUALIZAR">
						<input id="id" type="hidden" name="id" value="">
					</form>
				</div>
			</div>
			<?php include_once("footer.php"); ?>
		</div>
	</div>
</body>
</html>
