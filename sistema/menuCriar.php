<?php 

include_once("sessao.php");

if(isset($_POST['id'])){
	$id = $_POST['id'];

	$sql = "SELECT MenuId,MenuNome,MenuUrl,MenuIco,ModulNome,MenuModulo,MenuModulo,MenuPai,MenuLevel,MenuOrdem,MenuSubMenu,MenuSetorPublico,
	MenuSetorPrivado,MenuPosicao,MenuUsuarioAtualizador,MenuStatus
	FROM Menu
	JOIN Situacao ON SituaId = MenuStatus
	JOIN Modulo ON ModulId = MenuModulo
	WHERE SituaChave = 'ATIVO' and MenuId = $id";
	$result = $conn->query($sql);
	$rowMenu = $result->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fornecedor</title>

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

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >
	
        $(document).ready(function() {
        	
			
        }); // document.ready

		function alterar(){
			$('#btnAlterar').attr("onclick", "")
			$('#btnAlterar').html("Alterando... <img src='global_assets/images/lamparinas/loader-transparente2.gif' style='width: 17px'>")
			let msg = ''
			let idMenu = $('#idMenu').val()
			let tipo = 'ATUALIZAR'
			let inputNome = $('#inputNome').val()
			let url = $('#url').val()
			let icone = $('#icone').val()
			let cmbModulo = $('#cmbModulo').val()
			let menuPai = $('#cmbMenuPai').val()
			let nivel = $('#nivel').val()
			let ordem = $('#ordem').val()
			let subMenu = $('#subMenu').is(":checked")?1:0
			let publico = $('#publico').is(":checked")?1:0
			let privado = $('#privado').is(":checked")?1:0
			let posicao = $('#cmbPosicao').val()

			switch(msg){
				case inputNome: msg = 'Informe nome do menu'; break;
				case url: msg = 'Informe a URL de destino'; break;
				case cmbModulo: msg = 'Informe o módulo que o menu irá pertencer'; break;
				case posicao: msg = 'Informe a posição do menu'; break;
				default: msg = ''; break;
			}

			if(msg){
				alerta('Informação obrigatória', msg, 'error')
				$('#btnAlterar').html("Alterar")
				$('#btnAlterar').attr("onclick", "alterar()")
				return
			}

			$.ajax({
				type: 'POST',
				url: 'menuFiltra.php',
				dataType: 'json',
				data:{
					'tipo': tipo,
					'idMenu': idMenu,
					'inputNome': inputNome,
					'url': url,
					'icone': icone,
					'cmbModulo': cmbModulo,
					'menuPai': menuPai,
					'nivel': nivel,
					'ordem': ordem,
					'subMenu': subMenu,
					'publico': publico,
					'privado': privado,
					'posicao': posicao
				},
				success: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#btnAlterar').html("Alterar")
					$('#btnAlterar').attr("onclick", "alterar()")
					window.location.href = 'menuLista.php'
				},
				error: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#btnAlterar').html("Alterar")
					$('#btnAlterar').attr("onclick", "alterar()")
				}
			});
		}
		function inserir(){
			$('#btnInserir').attr('onclick', '')
			$('#btnInserir').html("Inserindo... <img src='global_assets/images/lamparinas/loader-transparente2.gif' style='width: 17px'>")			
			let msg = ''
			let tipo = 'CRIAR'
			let inputNome = $('#inputNome').val()
			let url = $('#url').val()
			let icone = $('#icone').val()
			let cmbModulo = $('#cmbModulo').val()
			let menuPai = $('#cmbMenuPai').val()
			let nivel = $('#nivel').val()
			let ordem = $('#ordem').val()
			let subMenu = $('#subMenu').is(":checked")?1:0
			let publico = $('#publico').is(":checked")?1:0
			let privado = $('#privado').is(":checked")?1:0
			let posicao = $('#cmbPosicao').val()

			switch(msg){
				case inputNome: msg = 'Informe nome do menu'; break;
				case url: msg = 'Informe a URL de destino'; break;
				case cmbModulo: msg = 'Informe o módulo que o menu irá pertencer'; break;
				case posicao: msg = 'Informe a posição do menu'; break;
				default: msg = ''; break;
			}

			if(msg){
				alerta('Informação obrigatória', msg, 'error')
				$('#btnInserir').html('Inserir')
				$('#btnInserir').attr('onclick', 'inserir()')
				return
			}

			$.ajax({
				type: 'POST',
				url: 'menuFiltra.php',
				dataType: 'json',
				data:{
					'tipo': tipo,
					'inputNome': inputNome,
					'url': url,
					'icone': icone,
					'cmbModulo': cmbModulo,
					'menuPai': menuPai,
					'nivel': nivel,
					'ordem': ordem,
					'subMenu': subMenu,
					'publico': publico,
					'privado': privado,
					'posicao': posicao
				},
				success: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#btnInserir').html('Inserir')
					$('#btnInserir').attr('onclick', 'inserir()')
					window.location.href = 'menuLista.php'
				},
				error: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#btnInserir').html('Inserir')
					$('#btnInserir').attr('onclick', 'inserir()')
				}
			});
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
				<div class="card">
					<form name="formMenu" id="formMenu" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Menu</h5>
						</div>
						
						<div class="card-body">
							<div class="col-lg-12 row">
								<div class="col-lg-4">
									<label>Nome<span class="text-danger"> *</span></label>
								</div>
								<div class="col-lg-2">
									<label>URL<span class="text-danger"> *</span></label>
								</div>
								<div class="col-lg-2">
									<label>Ícone</label>
								</div>
								<div class="col-lg-4">
									<label>Módulo<span class="text-danger"> *</span></label>
								</div>

								<?php
									$sql = "SELECT ModulId,ModulNome
									FROM Modulo
									JOIN Situacao ON SituaId = ModulStatus
									WHERE SituaChave = 'ATIVO'";
									$result = $conn->query($sql);
									$rowModuloSelect = $result->fetchAll(PDO::FETCH_ASSOC);

									$sql = "SELECT MenuId, MenuNome, ModulNome
									FROM Menu
									JOIN Situacao ON SituaId = MenuStatus
									JOIN Modulo ON ModulId = MenuModulo
									WHERE SituaChave = 'ATIVO' and MenuSubMenu = 1";
									$result = $conn->query($sql);
									$rowMenuSelect = $result->fetchAll(PDO::FETCH_ASSOC);

									$MenuId = isset($_POST['id'])?$rowMenu['MenuId']:'';
									$MenuNome = isset($_POST['id'])?$rowMenu['MenuNome']:'';
									$MenuUrl = isset($_POST['id'])?$rowMenu['MenuUrl']:'';
									$MenuIco = isset($_POST['id'])?$rowMenu['MenuIco']:'';
									$ModulNome = isset($_POST['id'])?$rowMenu['ModulNome']:'';
									$MenuModulo = isset($_POST['id'])?$rowMenu['MenuModulo']:'';
									$MenuPai = isset($_POST['id'])?$rowMenu['MenuPai']:'';
									$MenuLevel = isset($_POST['id'])?$rowMenu['MenuLevel']:1;
									$MenuOrdem = isset($_POST['id'])?$rowMenu['MenuOrdem']:1;
									$MenuSubMenu = isset($_POST['id'])?($rowMenu['MenuSubMenu']?'checked':''):'';
									$MenuSetorPublico = isset($_POST['id'])?($rowMenu['MenuSetorPublico']?'checked':''):'checked';
									$MenuSetorPrivado = isset($_POST['id'])?($rowMenu['MenuSetorPrivado']?'checked':''):'checked';
									$MenuPosicao = isset($_POST['id'])?$rowMenu['MenuPosicao']:'';
									$MenuUsuarioAtualizador = isset($_POST['id'])?$rowMenu['MenuUsuarioAtualizador']:'';
									$MenuStatus = isset($_POST['id'])?$rowMenu['MenuStatus']:1;

									$optionsModulo = "<option value=''>selecione</option>";
									$optionsMenu = "<option value=''>selecione</option>";
									foreach($rowModuloSelect as $modulo){
										$nome = $modulo['ModulNome'];
										$id = $modulo['ModulId'];
										$options = $MenuModulo?($MenuModulo == $id?"selected":""):"";

										$optionsModulo .= "<option $options value='$id'>$nome</option>";
									}

									foreach($rowMenuSelect as $menu){
										$nome = "$menu[MenuNome] ($menu[ModulNome])";
										$id = $menu['MenuId'];
										$options = $MenuPai?($MenuPai == $id?"selected":""):"";

										$optionsMenu .= "<option $options value='$id'>$nome</option>";
									}
									$optionsPosicao = "<option value=''>Selecione</option>";
									if(isset($_POST['id'])){
										$optionsPosicao .= $rowMenu['MenuPosicao'] == 'CONFIGURADOR'?"<option selected value='CONFIGURADOR'>CONFIGURADOR</option>":"<option value='CONFIGURADOR'>CONFIGURADOR</option>";
										$optionsPosicao .= $rowMenu['MenuPosicao'] == 'PRINCIPAL'?"<option selected value='PRINCIPAL'>PRINCIPAL</option>":"<option value='PRINCIPAL'>PRINCIPAL</option>";
										$optionsPosicao .= $rowMenu['MenuPosicao'] == 'APOIO'?"<option selected value='APOIO'>APOIO</option>":"<option value='APOIO'>APOIO</option>";
									} else {
										$optionsPosicao = "
										<option value=''>Selecione</option>
										<option value='CONFIGURADOR'>CONFIGURADOR</option>
										<option value='PRINCIPAL'>PRINCIPAL</option>
										<option value='APOIO'>APOIO</option>";
									}

									if(isset($_POST['id'])){
										echo "<input type='hidden' id='idMenu' name='idMenu' value='$MenuId'>";
									}

									echo "
										<div class='col-lg-4'>
											<input type='text' id='inputNome' value='$MenuNome' name='inputNome' class='form-control' placeholder='Menu Nome' required autofocus>
										</div>
										<div class='col-lg-2'>
											<input type='text' id='url' value='$MenuUrl' name='url' class='form-control' placeholder='URL'>
										</div>	
										<div class='col-lg-2'>
											<input type='text' id='icone' value='$MenuIco' name='icone' class='form-control' placeholder='Ícone'>
										</div>	
										<div class='col-lg-4'>
											<select id='cmbModulo' name='cmbModulo' class='select-search' required>
												$optionsModulo
											</select>
										</div>
										<div class='form-group col-lg-12 row mt-4'>
											<div class='col-lg-3'>
												<label for='inputNome'>Menu Pai</label>
											</div>
											<div class='col-lg-1'>
												<label for='inputNome'>Nível</label>
											</div>
											<div class='col-lg-1'>
												<label for='inputNome'>Ordem</label>
											</div>
											<div class='col-lg-1 text-center'>
												<label for='inputNome'>SubMenu?</label>
											</div>
											<div class='col-lg-2 text-center'>
												<label for='inputNome'>Setor Público</label>
											</div>
											<div class='col-lg-2 text-center'>
												<label for='inputNome'>Setor Privado</label>
											</div>
											<div class='col-lg-2'>
												<label for='inputNome'>Posição <span class='text-danger'> *</span></label>
											</div>

											<div class='col-lg-3'>
												<select id='cmbMenuPai' name='cmbMenuPai' class='select-search'>
													$optionsMenu
												</select>
											</div>
											<div class='col-lg-1'>
												<input type='number' id='nivel' value='$MenuLevel' name='nivel' value='1' class='form-control'>
											</div>
											<div class='col-lg-1'>
												<input type='number' id='ordem' value='$MenuOrdem' name='ordem' value='1' class='form-control'>
											</div>
											<div class='col-lg-1'>
												<input type='checkbox' id='subMenu' $MenuSubMenu name='subMenu' class='form-control'>
											</div>
											<div class='col-lg-2'>
												<input type='checkbox' id='publico' $MenuSetorPublico name='publico' class='form-control'>
											</div>
											<div class='col-lg-2'>
												<input type='checkbox' id='privado' $MenuSetorPrivado name='privado' class='form-control'>
											</div>
											<div class='col-lg-2'>
												<select id='cmbPosicao' name='cmbPosicao' class='form-control-select2' required>
													$optionsPosicao
												</select>
											</div>
										</div>";
								?>
							</div>
						</div>
					</form>
					<div class="row ml-3" style="margin-top: 40px;">
						<div class="col-lg-12">								
							<div class="form-group">
								<?php
									echo isset($_POST['id'])?'<button id="btnAlterar" onclick="alterar()" class="btn btn-lg btn-principal" type="submit">Alterar</button>':'<button id="btnInserir" onclick="inserir()" class="btn btn-lg btn-principal" type="submit">Inserir</button>';
								?>	
								<a href="menuLista.php" class="btn btn-basic" role="button">Cancelar</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include_once("footer.php"); ?>
		</div>
	</div>
</body>
</html>
