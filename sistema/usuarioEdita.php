<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Editar Usuário';

if (isset($_SESSION['EmpresaId'])) {
	$EmpresaId = $_SESSION['EmpresaId'];
} else {
	$EmpresaId = $_SESSION['EmpreId'];
}

if (isset($_POST['inputUsuarioId'])) {

	$iUsuario = $_POST['inputUsuarioId'];

	$sql = "SELECT UsuarId, UsuarCpf, UsuarNome, UsuarLogin, UsuarSenha, 
			UsuarEmail, UsuarTelefone, UsuarCelular, EXUXPPerfil
			FROM Usuario
			JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
			WHERE UsuarId = $iUsuario and EXUXPEmpresa = $EmpresaId ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT PerfiChave
			FROM Perfil
			JOIN Situacao on SituaId = PerfiStatus															     
			WHERE SituaChave = 'ATIVO' and PerfiId = " . $row['EXUXPPerfil'] . "
			ORDER BY PerfiNome ASC";
	$result = $conn->query($sql);
	$rowPerf = $result->fetch(PDO::FETCH_ASSOC);

	$_SESSION['msg'] = array();
}

if (isset($_POST['inputCpf'])) {

	try {

		$conn->beginTransaction();

		$sql = "SELECT UsuarSenha
				FROM Usuario
				JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
				WHERE UsuarId = $iUsuario and EXUXPEmpresa = $EmpresaId ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT PerfiId
				FROM Perfil
				WHERE PerfiChave = 'ALMOXARIFADO' ";
		$result = $conn->query($sql);
		$rowPerfilId = $result->fetch(PDO::FETCH_ASSOC);		

		$senha = '';
		$row['UsuarSenha'] == $_POST['inputSenha'] ? $senha = $_POST['inputSenha'] : $senha = md5($_POST['inputSenha']);

		$sql = "UPDATE Usuario SET UsuarNome = :sNome, usuarLogin = :sLogin, UsuarSenha = :sSenha, 
								   UsuarEmail = :sEmail, UsuarTelefone = :sTelefone, UsuarCelular = :sCelular
				WHERE UsuarId = :iUsuario";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sNome' => $_POST['inputNome'],
			':sLogin' => $_POST['inputLogin'],
			':sSenha' => $senha,
			':sEmail' => $_POST['inputEmail'],
			':sTelefone' => $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'],
			':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'],
			':iUsuario' => $_POST['inputUsuarioId']
		));

		$sql = "UPDATE EmpresaXUsuarioXPerfil SET EXUXPPerfil = :iPerfil, EXUXPUsuarioAtualizador = :iUsuarioAtualizador
				WHERE EXUXPUsuario = :iUsuario and EXUXPEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iPerfil' => $_POST['cmbPerfil'] == '' ? null : $_POST['cmbPerfil'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUsuario' => $_POST['inputUsuarioId'],
			':iEmpresa' => $EmpresaId
		));

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar usuário!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		die;
	}

	irpara("usuario.php");
}

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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		
		$(document).ready(function() {

			//Garantindo que ninguém mude a empresa na tela de edição
			$('#cmbEmpresa').prop("disabled", true);

			//Já realiza o filtro dos possíveis setores e seleciona o que está informado no banco		
			var cmbUnidade = $('#cmbUnidade').val();
			var cmbSetor = $('#cmbSetor').val();

			$.getJSON('filtraSetor.php?idUnidade=' + cmbUnidade, function(dados) {

				var option = '<option value="">Selecione o Setor</option>';

				if (dados.length) {

					$.each(dados, function(i, obj) {
						if (obj.SetorId == cmbSetor) {
							option += '<option value="' + obj.SetorId + '" selected="selected">' + obj.SetorNome + '</option>';
						} else {
							option += '<option value="' + obj.SetorId + '">' + obj.SetorNome + '</option>';
						}
					});

					$('#cmbSetor').html(option).show();
				}
			});

			$('#cmbPerfil').on('change', function(e) {
				let filhos = $('#cmbPerfil').children()
				let valorcmb = $('#cmbPerfil').val()
				filhos.each((i, elem) => {
					let perfil = $(elem).attr('chavePerfil')
					let valOption = $(elem).attr('value')

					if (valOption == valorcmb){

						if (perfil == 'ALMOXARIFADO') {
							
							$('#LocalEstoque').fadeIn('300');
							document.getElementById('cmbLocalEstoque').setAttribute('required', 'required');
						} else {
							document.getElementById('cmbLocalEstoque').removeAttribute('required', 'required');
							$('#LocalEstoque').fadeOut('300');							
						}
					}
				})
			})

			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbUnidade').on('change', function(e) {

				Filtrando();

				var cmbUnidade = $('#cmbUnidade').val();

				if (cmbUnidade == '') {
					ResetSetor();
					ResetLocalEstoque();
				} else {

					$.getJSON('filtraSetor.php?idUnidade=' + cmbUnidade, function(dados) {

						var option = '<option value="">Selecione o Setor</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.SetorId + '">' + obj.SetorNome + '</option>';
							});

							$('#cmbSetor').html(option).show();
						} else {
							ResetSetor();
						}
					});

					$.getJSON('filtraLocalEstoque.php?idUnidade=' + cmbUnidade, function(dados) {

						var option = '<option value="">Selecione o Local de Estoque</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.LcEstId + '">' + obj.LcEstNome + '</option>';
							});

							$('#cmbLocalEstoque').html(option).show();
						} else {
							ResetLocalEstoque();
						}
					});
				}
			});

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				var inputNome = $('#inputNome').val();
				var cmbPerfil = $('#cmbPerfil').val();
				var inputLogin = $('#inputLogin').val();
				var inputEmail = $('#inputEmail').val();
				var inputSenha = $('#inputSenha').val();
				var inputConfirmaSenha = $('#inputConfirmaSenha').val();
				var cmbUnidade = $('#cmbUnidade').val();				

				if (inputNome != '' && inputLogin != '' && inputEmail != '' && cmbPerfil != '' && cmbUnidade != ''){
					if (inputSenha != inputConfirmaSenha) {
						alerta('Atenção', 'A confirmação de senha não confere!', 'error');
						$('#inputConfirmaSenha').focus();
						$("#formUsuario").submit();
						return false;
					}
				}

				$('#cmbEmpresa').prop("disabled", false);

				$("#formUsuario").submit();
			})

			$('#cancelar').on('click', function(e) {

				$('#cmbEmpresa').prop("disabled", false);

				$(window.document.location).attr('href', "usuario.php");
			});

			function Filtrando() {
				$('#cmbSetor').empty().append('<option value="">Filtrando...</option>');
				$('#cmbLocalEstoque').empty().append('<option value="">Filtrando...</option>');
			}

			function ResetSetor() {
				$('#cmbSetor').empty().append('<option value="">Sem setor</option>');
			}

			function ResetLocalEstoque() {
				$('#cmbLocalEstoque').empty().append('<option value="">Sem Local de Estoque</option>');
			}			
		});
	</script>

</head>

<?php

if (isset($_SESSION['EmpresaId'])) {
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

	if (isset($_SESSION['EmpresaId'])) {
		include_once("menuLeftSecundario.php");
	}
	?>

	<!-- Main content -->
	<div class="content-wrapper">

		<?php include_once("cabecalho.php"); ?>

		<!-- Content area -->
		<div class="content">

			<!-- Info blocks -->
			<div class="card">

				<form name="formUsuario" id="formUsuario" method="post" class="form-validate-jquery">
					<div class="card-header header-elements-inline">
						<h5 class="text-uppercase font-weight-bold">Editar Usuário "<?php echo $row['UsuarNome']; ?>"</h5>
					</div>

					<input type="hidden" id="inputUsuarioId" name="inputUsuarioId" value="<?php echo $row['UsuarId']; ?>">

					<div class="card-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputCpf">CPF<span class="text-danger"> *</span></label>
											<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" value="<?php echo formatarCPF_Cnpj($row['UsuarCpf']); ?>" required readOnly>
										</div>
									</div>
									<div class="col-lg-7">
										<div class="form-group">
											<label for="inputNome">Nome<span class="text-danger"> *</span></label>
											<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['UsuarNome']; ?>" required>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label for="cmbPerfil">Perfil<span class="text-danger"> *</span></label>
											<select id="cmbPerfil" name="cmbPerfil" class="form-control form-control-select2" required>
												<option value="">Informe um perfil</option>
												<?php
												$sql = "SELECT PerfiId, PerfiNome, PerfiChave
														FROM Perfil
														JOIN Situacao on SituaId = PerfiStatus															     
														WHERE SituaChave = 'ATIVO'
														ORDER BY PerfiNome ASC";
												$result = $conn->query($sql);
												$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);

												foreach ($rowPerfil as $item) {
													if ($item['PerfiId'] == $row['EXUXPPerfil']) {
														print('<option value="' . $item['PerfiId'] . '" selected="selected" chavePerfil="' . $item['PerfiChave'] . '">' . $item['PerfiNome'] . '</option>');
													} else {
														print('<option value="' . $item['PerfiId'] . '" chavePerfil="' . $item['PerfiChave'] . '">' . $item['PerfiNome'] . '</option>');
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

						<h5 class="mb-0 font-weight-semibold">Login</h5>
						<br>
						<div class="row">
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputLogin">Login<span class="text-danger"> *</span></label>
											<input type="text" id="inputLogin" name="inputLogin" class="form-control" placeholder="Login" value="<?php echo $row['UsuarLogin']; ?>" required>
										</div>
									</div>

									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputSenha">Senha<span class="text-danger"> *</span></label>
											<input type="password" id="inputSenha" name="inputSenha" class="form-control" placeholder="Senha" value="<?php echo $row['UsuarSenha']; ?>" required>
										</div>
									</div>

									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputConfirmaSenha">Confirma Senha<span class="text-danger"> *</span></label>
											<input type="password" id="inputConfirmaSenha" name="inputConfirmaSenha" class="form-control" placeholder="Confirma Senha" value="<?php echo $row['UsuarSenha']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>

						<h5 class="mb-0 font-weight-semibold">Contato</h5>
						<br>
						<div class="row">
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputEmail">E-mail<span class="text-danger"> *</span></label>
											<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" value="<?php echo $row['UsuarEmail']; ?>" required>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputTelefone">Telefone</label>
											<input type="text" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999" value="<?php echo $row['UsuarTelefone']; ?>">
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputCelular">Celular</label>
											<input type="text" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999" value="<?php echo $row['UsuarCelular']; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row" style="margin-top: 20px;">
							<div class="col-lg-12">
								<div class="form-group">
									<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
									<a href="usuario.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
								</div>
							</div>
						</div>
				</form>

			</div>
			<!-- /card-body -->

		</div>
		<!-- /info blocks -->

	</div>
	<!-- /content area -->

	<?php include_once("footer.php"); ?>

</div>
<!-- /main content -->

</div>
<!-- /page content -->

</body>

</html>