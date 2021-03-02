<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Lotacao';

include('global_assets/php/conexao.php');

if (isset($_POST['inputUsuarioId'])){
	$_SESSION['UsuarioId'] = $_POST['inputUsuarioId'];
	$_SESSION['UsuarioNome'] = $_POST['inputUsuarioNome'];
	$_SESSION['UsuarioPerfil'] = $_POST['inputUsuarioPerfil'];
	$_SESSION['EmpresaUsuarioPerfil'] = $_POST['inputEmpresaUsuarioPerfil'];
}

if (!isset($_SESSION['UsuarioId'])){
	irpara('usuario.php');
}

$sql = "SELECT UsXUnEmpresaUsuarioPerfil, UsXUnUnidade, UsXUnSetor, UnidaNome, SetorNome, LcEstNome
		FROM UsuarioXUnidade
		JOIN Unidade ON UnidaId = UsXUnUnidade
		JOIN Setor ON SetorId = UsXUnSetor
		LEFT JOIN LocalEstoque on LcEstId = UsXUnLocalEstoque
		JOIN EmpresaXUsuarioXPerfil on EXUXPId = UsXUnEmpresaUsuarioPerfil
	    WHERE EXUXPUsuario = ". $_SESSION['UsuarioId'] ."
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
		function atualizaLotacao(UsXUnEmpresaUsuarioPerfil, UsXUnUnidade, Tipo){
		
			document.getElementById('inputEmpresaUsuarioPerfil').value = UsXUnEmpresaUsuarioPerfil;
			document.getElementById('inputUnidade').value = UsXUnUnidade;
			
			if (Tipo == 'exclui'){
				confirmaExclusao(document.formLotacao, "Tem certeza que deseja excluir essa Lotação?", "usuarioLotacaoExclui.php");
			} 		

			document.formLotacao.submit();
		}		
			
	</script>

</head>

	<?php
		
		if (isset($_SESSION['EmpresaId'])){	
			print('<body class="navbar-top sidebar-xs">');
		} else {
			print('<body class="navbar-top">');
		}

		include_once("topo.php");
	?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php 
			
			include_once("menu-left.php"); 
		
			if (isset($_SESSION['EmpresaId'])){
				include_once("menuLeftSecundario.php");
			}
		?>			

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
									A relação abaixo faz referência a lotação do usúario<span style="color: #FF0000; font-weight: bold;"> <?php echo $_SESSION['UsuarioNome']; ?></span> na empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>	
									</div>	
									
									<div class="col-lg-3">	
										<div class="text-right">
											<a href="usuario.php" style="margin-right: 10px;"><< Usuários</a>
											<a href="usuarioLotacaoNovo.php" class="btn btn-principal" role="button">Nova Lotação</a>
										</div>
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
											<td>'.$item['LcEstNome'].'</td>
											');
										
										
										print('<td class="text-center">                             
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
													<a href="#" onclick="atualizaLotacao('.$item['UsXUnEmpresaUsuarioPerfil'].', '.$item['UsXUnUnidade'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>							
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
					<input type="hidden" id="inputEmpresaUsuarioPerfil" name="inputEmpresaUsuarioPerfil" >
					<input type="hidden" id="inputUnidade" name="inputUnidade" >
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
