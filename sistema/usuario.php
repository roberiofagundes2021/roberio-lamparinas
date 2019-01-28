<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Usuário';

include('global_assets/php/conexao.php');

if (isset($_SESSION['EmpresaId'])){	
	$EmpresaId =   $_SESSION['EmpresaId'];
	$EmpresaNome = $_SESSION['EmpresaNome'];
} else {	
	$EmpresaId = $_SESSION['EmpreId'];
	$EmpresaNome = $_SESSION['EmpreNomeFantasia'];
	$_SESSION['UC'] = 'Usuario';
}

$sql = ("SELECT UsuarId, UsuarCpf, UsuarNome, UsuarLogin, EXUXPStatus, PerfiNome, EmpreNomeFantasia
		 FROM Usuario
		 JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
		 JOIN Empresa on EXUXPEmpresa = EmpreId
		 JOIN Perfil on PerfiId = EXUXPPerfil
		 Where EmpreId = ".$EmpresaId."
		 ORDER BY UsuarNome ASC");
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
	<title>Lamparinas | Usuário</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>		
	<!-- /theme JS files -->
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblUsuario').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: true,
				responsive: true,
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
		
		function atualizaUsuario(UsuarioId, UsuarioStatus, Tipo){

			document.getElementById('inputUsuarioId').value = UsuarioId;
			document.getElementById('inputUsuarioStatus').value = UsuarioStatus;
		
			if (Tipo == 'edita'){
				document.formUsuario.action = "usuarioEdita.php";
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formUsuario, "Tem certeza que deseja excluir esse usuário?", "usuarioExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formUsuario.action = "usuarioMudaSituacao.php";
			}
			
			document.formUsuario.submit();
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
								<h3 class="card-title">Relação de Usuários</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">Os usuários cadastrados abaixo pertencem a empresa <b><?php echo $EmpresaNome; ?></b>.</p>
								<div class="text-right"><a href="usuarioNovo.php" class="btn btn-success" role="button">Novo usuário</a></div>
							</div>							

							<table class="table" id="tblUsuario">
								<thead>
									<tr class="bg-slate">
										<th>Nome</th>
										<th>Login</th>
										<th>CPF</th>
										<th>Perfil</th>
										<th>Situação</th>										
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['EXUXPStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['EXUXPStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['UsuarNome'].'</td>
											<td>'.$item['UsuarLogin'].'</td>
											<td>'.formatarCPF_Cnpj($item['UsuarCpf']).'</td>
											<td>'.$item['PerfiNome'].'</td>');
											
										if ($_SESSION['UsuarId'] != $item['UsuarId']) {
											print('<td><a href="#" onclick="atualizaUsuario('.$item['UsuarId'].', '.$item['EXUXPStatus'].', \'mudaStatus\')"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										} else {
											print('<td><a href="#" data-popup="tooltip" data-trigger="focus" title="Seu usuário não pode ser desativado por você."><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										}
										
										//<td><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></td>											
											
										print('	<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaUsuario('.$item['UsuarId'].', '.$item['EXUXPStatus'].', \'edita\')" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaUsuario('.$item['UsuarId'].', '.$item['EXUXPStatus'].', \'exclui\')" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formUsuario" method="post" action="usuarioEdita.php">
					<input type="hidden" id="inputUsuarioId" name="inputUsuarioId" >
					<input type="hidden" id="inputUsuarioStatus" name="inputUsuarioStatus" >
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
