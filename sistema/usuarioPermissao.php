<?php 

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Permissões';

$unidade = $_SESSION['UnidadeId'];
$user = $_POST['inputUsuarioId'];
$perfilId = $_POST['inputUsuarioPerfil'];

$sqlUserPerfil = "SELECT PerfiId FROM perfil WHERE PerfiChave = '$perfilId'";
$resultUserPerfil = $conn->query($sqlUserPerfil);
$perfilId = $resultUserPerfil->fetch(PDO::FETCH_ASSOC);

$userPxP = "SELECT UsuarPermissaoPerfil
		FROM Usuario
		Where UsuarId = '$user'";
$resultUserUxP = $conn->query($userPxP);
$UxPxP = $resultUserUxP->fetch(PDO::FETCH_ASSOC);

// ao recarregar fica sumindo o valor atribuido à $perfilId;

$sqlModuloUxP = "SELECT ModulId, ModulOrdem, ModulNome, ModulStatus, SituaChave, SituaCor
FROM modulo join situacao on ModulStatus = SituaId order by ModulOrdem asc";
$resultModuloUxP = $conn->query($sqlModuloUxP);
$moduloUxP = $resultModuloUxP->fetchAll(PDO::FETCH_ASSOC);

$sqlMenuUxP = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo,
MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave,
UsXPeVisualizar, UsXPeAtualizar, UsXPeExcluir, UsXPeUnidade
FROM menu
join situacao on MenuStatus = SituaId
join UsuarioXPermissao on UsXPeUsuario = '$user' and UsXPeUnidade = '$unidade' and UsXPeMenu = MenuId
order by MenuOrdem asc";

$resultMenuUxP = $conn->query($sqlMenuUxP);
$menuUxP = $resultMenuUxP->fetchAll(PDO::FETCH_ASSOC);

if(!isset($menuUxP[0]['UsXPeVisualizar'])){
	$sqlMenuUxP = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo,
	MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave, 
	PrXPeId, PrXPePerfil, PrXPeMenu, PrXPeVisualizar, PrXPeAtualizar,  PrXPeExcluir, 
	PrXPeUnidade
	FROM menu
	join situacao on MenuStatus = SituaId
	join PerfilXPermissao on MenuId = PrXPeMenu and PrXPePerfil = '$perfilId[PerfiId]' and PrXPeUnidade = $unidade
	order by MenuOrdem asc";
	
	$resultMenuUxP = $conn->query($sqlMenuUxP);
	$menuUxP = $resultMenuUxP->fetchAll(PDO::FETCH_ASSOC);
}

$sqlSituacao = "SELECT SituaId, SituaNome, SituaChave, SituaStatus, SituaCor FROM situacao";
$resultSituacao = $conn->query($sqlSituacao);
$situacao = $resultSituacao->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Permissões</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
		
	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<script type="text/javascript">
		$(document).ready(function (){	
			$('#tblPermissao').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //permissao
					width: "80%",
					targets: [0]
				},
				{ 
					orderable: true,   //visualizar
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: false,   //atualizar
					width: "10%",
					targets: [2]
				},
				{ 
					orderable: false,   //excluir
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
		function needSave(){
			document.getElementById("btnSave").style.display="block"
		}
		function save(){
			document.getElementById('form_id').action = "usuarioPermissaoPersist.php";
			document.getElementById("form_id").submit();
		}
	</script>
	
</head>

<style>
.btn-group-fab {
	display: none;
	position: fixed;
	width: 100px;
	height: auto;
	right: 20px; bottom: 20px;
	z-index: 1;
}
.separate{
	height:10px;
	border-bottom:solid 1px #dddddd;
	margin-top:10px;
	margin-bottom:10px;
}
.full-width{
	width: 100%;
}
td{
	border-top:solid 1px #dddddd;
	border-bottom:solid 1px #dddddd;
}
</style>

<body class="navbar-top">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

		<button id="btnSave" type="submit" onClick="save()" class="btn-group-fab btn btn-lg btn-principal">salvar</button>
		<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
						<form id="form_id" method="POST">
							<div  class="row">
								<div class="card-header">
									<h5 class="card-title">Relação de Permissões de usuários (<?php echo $_POST['inputUsuarioNome']; ?>)</h5>
									<br>
									<div class="row mx-1" style="height:20px;">
										<label>
											<p class="mr-2 pt-2" style="font-size:15px;">Utilizar as permissoes do Perfil</p>
										</label>
										
										<div class="p-1">
											<?php echo '
											<label class="form-check-label d-flex align-items-center">
												<input onchange="needSave()" type="checkbox" name="permissao_usuario" data-on-text="Sim" data-off-text="Não" class="form-input-switch"'
												.($UxPxP['UsuarPermissaoPerfil']==0? ' />':' checked/>').
											'</label>';
											?>
										</div>
									</div>
								</div>
								<div style="position:absolute; right: 0px;" class="pt-4 pr-3">
									<div><a href="usuario.php" role="button"><< Relação de Usuários</a></div>
								</div>
							</div>
							
							<table id="tblPermissao" class="table pt-3 full-width">
								<thead>
									<tr class="bg-slate full-width">
										<th class="full-width">Permissão</th>
										<th>Visualizar</th>
										<th>Atualizar</th>
										<th>Excluir</th>
									</tr>
								</thead>
								<div class="separate"></div>
								<tbody class="full-width">
										<?php
										echo '<input name="usuarioId" value="'.$user.'" type="hidden"/>';
										echo '<input name="UnidadeId" value="'.$unidade.'" type="hidden"/>';
											foreach($moduloUxP as $mod){
												if($mod['SituaChave'] == strtoupper("ativo")){
													echo '<tr class="nav-item-header full-width">
																	<td class="nav-item-header full-width">
																		<h3 class="text-uppercase text-dark full-width">'.$mod['ModulNome'].'</h3>
																	</td>
																</tr>';
													foreach($menuUxP as $men){
														if ($men["MenuModulo"] == $mod["ModulId"] && $men['MenuSubMenu'] == 0 && $men['MenuPai'] == 0 && $men['SituaChave'] == strtoupper("ativo")){
															echo '<input name="'.$men['MenuId'].'-MenuId" value='.$men['MenuId'].' type="hidden">';
															echo '<tr>
																<td><h5>'.$men['MenuNome'].'</h5></td>
																<td class="text-center">
																	<div class="list-icons">';
																		if(isset($men['UsXPeVisualizar'])){
																			echo '<input name="'.$men['MenuId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"'.
																			($men['UsXPeVisualizar'] == 1?'checked/>':'/>');
																		}else{
																			echo '<input name="'.$men['MenuId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"'.
																			($men['PrXPeVisualizar'] == 1?'checked/>':'/>');
																		}
																	echo '</div>
																</td>
																<td class="text-center">
																	<div class="list-icons">';
																	if(isset($men['UsXPeAtualizar'])){
																		echo '<input name="'.$men['MenuId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"'.
																		($men['UsXPeAtualizar'] == 1?'checked/>':'/>');
																	}else{
																		echo '<input name="'.$men['MenuId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"'.
																		($men['PrXPeAtualizar'] == 1?'checked/>':'/>');
																	}
																	echo'</div>
																</td>
																<td class="text-center">
																	<div class="list-icons">';
																	if(isset($men['UsXPeExcluir'])){
																		echo '<input name="'.$men['MenuId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"'.
																		($men['UsXPeExcluir'] == 1?'checked/>':'/>');
																	}else{
																		echo '<input name="'.$men['MenuId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"'.
																		($men['PrXPeExcluir'] == 1?'checked/>':'/>');
																	}
																	echo '</div>
																</td>
															</tr>';
														}
														if($men['MenuSubMenu'] == 1  && $men["MenuModulo"] == $mod["ModulId"]){
															foreach($menuUxP as $men_f){
																if ($men_f["MenuPai"] == $men["MenuId"] && $men_f['SituaChave'] == strtoupper("ativo")){
																	echo '<input name="MenuId" value='.$men_f['MenuId'].' type="hidden">';
																	echo '<tr>
																		<td><h5>('.$men['MenuNome'].') - '.$men_f['MenuNome'].'</h5></td>
																		<td class="text-center">
																			<div class="list-icons">';
																				if(isset($men_f['UsXPeVisualizar'])){
																					echo '<input name="'.$men_f['MenuId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"'.
																					($men_f['UsXPeVisualizar'] == 1?'checked/>':'/>');
																				}else{
																					echo '<input name="'.$men_f['MenuId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"'.
																					($men_f['PrXPeVisualizar'] == 1?'checked/>':'/>');
																				}
																			echo '</div>
																		</td>
																		<td class="text-center">
																			<div class="list-icons">';
																			if(isset($men_f['UsXPeAtualizar'])){
																				echo '<input name="'.$men_f['MenuId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"'.
																				($men_f['UsXPeAtualizar'] == 1?'checked/>':'/>');
																			}else{
																				echo '<input name="'.$men_f['MenuId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"'.
																				($men_f['PrXPeAtualizar'] == 1?'checked/>':'/>');
																			}
																			echo'</div>
																		</td>
																		<td class="text-center">
																			<div class="list-icons">';
																			if(isset($men_f['UsXPeExcluir'])){
																				echo '<input name="'.$men_f['MenuId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"'.
																				($men_f['UsXPeExcluir'] == 1?'checked/>':'/>');
																			}else{
																				echo '<input name="'.$men_f['MenuId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"'.
																				($men_f['PrXPeExcluir'] == 1?'checked/>':'/>');
																			}
																			echo '</div>
																		</td>
																	</tr>';
																}
															}
														}
													}
												}
											}?>
									</form>
								</tbody>
							</table>
						</div>
						<!-- /basic responsive configuration -->

					</div>
				</div>				
				
				<!-- /info blocks -->

			</div>
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
