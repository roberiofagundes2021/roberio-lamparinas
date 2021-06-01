<?php 

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Permissões';

$unidade = $_SESSION['UnidadeId'];
$allPerfilId = $_POST['inputPerfilId'];

// ao recarregar fica sumindo o valor atribuido à $perfilId;

$sqlModuloPxP = "SELECT ModulId, ModulOrdem, ModulNome, ModulStatus, SituaChave, SituaCor
FROM modulo join situacao on ModulStatus = SituaId order by ModulOrdem asc";
$resultModuloPxP = $conn->query($sqlModuloPxP);
$moduloPxP = $resultModuloPxP->fetchAll(PDO::FETCH_ASSOC);

$sqlMenuPxP = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo,
MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave, 
PrXPeId, PrXPePerfil, PrXPeMenu, PrXPeVisualizar, PrXPeAtualizar,  PrXPeExcluir, 
PrXPeUnidade
FROM menu
join situacao on MenuStatus = SituaId
join PerfilXPermissao on MenuId = PrXPeMenu and PrXPePerfil = '$allPerfilId' and PrXPeUnidade = $unidade
order by MenuOrdem asc";

$resultMenuPxP = $conn->query($sqlMenuPxP);
$menuPxP = $resultMenuPxP->fetchAll(PDO::FETCH_ASSOC);

$sqlSituacao = "SELECT SituaId, SituaNome, SituaChave, SituaStatus, SituaCor FROM situacao";
$resultSituacao = $conn->query($sqlSituacao);
$situacao = $resultSituacao->fetchAll(PDO::FETCH_ASSOC);

// cadastrar todos os menus para todos os perfis com permições padrão

// $sqlMenu = "SELECT MenuId FROM menu";
// $resultMenu = $conn->query($sqlMenu);
// $menu = $resultMenu->fetchAll(PDO::FETCH_ASSOC);

// $sqlPerfil = "SELECT PerfiId FROM Perfil";
// $resultPerfil = $conn->query($sqlPerfil);
// $perfis = $resultPerfil->fetchAll(PDO::FETCH_ASSOC);

// if($perfil != null && $unidade != null){
// 	foreach($perfis as $perf){
// 		foreach($menu as $men){
// 			$sqlPerd = "INSERT INTO PerfilXPermissao (PrXPePerfil, PrXPeMenu, PrXPeVisualizar, PrXPeAtualizar,
// 			PrXPeExcluir, PrXPeUnidade) VALUES ('$perf[PerfiId]', '$men[MenuId]', 0, 0, 0, '$unidade')";
// 			$resultSetPerf = $conn->query($sqlPerd);
// 		}
// 	}
// }

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
			document.getElementById('form_id').action = "perfilPermissaoPersist.php";
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
							<div  class="row">
								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Permissões (<?php echo $_POST['inputPerfilNome']; ?>)</h5>	
								</div>
								<div style="position:absolute; right: 0px;" class="pt-4 pr-3">
									<div><a href="perfil.php" role="button"><< Termo de Referência</a></div>
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
									<form id="form_id" method="POST">
										<?php
											foreach($moduloPxP as $mod){
												if($mod['SituaChave'] == strtoupper("ativo")){
													echo '<tr class="nav-item-header full-width">
																	<td class="nav-item-header full-width">
																		<h3 class="text-uppercase text-dark full-width">'.$mod['ModulNome'].'</h3>
																	</td>
																</tr>';
													foreach($menuPxP as $men){
														if ($men["MenuModulo"] == $mod["ModulId"] && $men['MenuSubMenu'] == 0 && $men['MenuPai'] == 0 && $men['SituaChave'] == strtoupper("ativo")){
															echo '<input name="MenuId" value='.$men['MenuId'].' type="hidden">';
															echo '<input name="'.$men['PrXPeId'].'-PrXPeId" value='.$men['PrXPeId'].' type="hidden">';
															echo '<tr>
																<td><h5>'.$men['MenuNome'].'</h5></td>
																<td class="text-center">
																	<div class="list-icons">';
																	if($men['PrXPeVisualizar']==0){
																		echo '<input name="'.$men['PrXPeId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"/>';
																	}else{
																		echo '<input name="'.$men['PrXPeId'].'-view'.'" onclick="needSave()" value="view" type="checkbox" checked/>';
																	}
																	echo '</div>
																</td>
																<td class="text-center">
																	<div class="list-icons">';
																	if($men['PrXPeAtualizar']==0){
																		echo '<input name="'.$men['PrXPeId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"/>';
																	}else{
																		echo '<input name="'.$men['PrXPeId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox" checked/>';
																	}
																	echo '</div>
																</td>
																<td class="text-center">
																	<div class="list-icons">';
																	if($men['PrXPeExcluir']==0){
																		echo '<input name="'.$men['PrXPeId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"/>';
																	}else{
																		echo '<input name="'.$men['PrXPeId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox" checked/>';
																	}
																	echo '</div>
																</td>
															</tr>';
														}
														if($men['MenuSubMenu'] == 1  && $men["MenuModulo"] == $mod["ModulId"]){
															foreach($menuPxP as $men_f){
																if ($men_f["MenuPai"] == $men["MenuId"] && $men_f['SituaChave'] == strtoupper("ativo")){
																	echo '<input name="MenuId" value='.$men_f['MenuId'].' type="hidden">';
																	echo '<input name="'.$men_f['PrXPeId'].'-PrXPeId" value='.$men_f['PrXPeId'].' type="hidden">';
																	echo '<tr>
																		<td><h5>('.$men['MenuNome'].') - '.$men_f['MenuNome'].'</h5></td>
																		<td class="text-center">
																			<div class="list-icons">';
																			if($men_f['PrXPeVisualizar']==0){
																				echo '<input name="'.$men_f['PrXPeId'].'-view'.'" onclick="needSave()" value="view" type="checkbox"/>';
																			}else{
																				echo '<input name="'.$men_f['PrXPeId'].'-view'.'" onclick="needSave()" value="view" type="checkbox" checked/>';
																			}
																			echo '</div>
																		</td>
																		<td class="text-center">
																			<div class="list-icons">';
																			if($men_f['PrXPeAtualizar']==0){
																				echo '<input name="'.$men_f['PrXPeId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox"/>';
																			}else{
																				echo '<input name="'.$men_f['PrXPeId'].'-edit'.'" onclick="needSave()" value="edit" type="checkbox" checked/>';
																			}
																			echo '</div>
																		</td>
																		<td class="text-center">
																			<div class="list-icons">';
																			if($men_f['PrXPeExcluir']==0){
																				echo '<input name="'.$men_f['PrXPeId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox"/>';
																			}else{
																				echo '<input name="'.$men_f['PrXPeId'].'-delet'.'" onclick="needSave()" value="delet" type="checkbox" checked/>';
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
