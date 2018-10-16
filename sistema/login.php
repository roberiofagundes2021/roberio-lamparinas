<?php

if(!isset($_SESSION)){
    session_start();
}

date_default_timezone_set("Brazil/East");

require_once("global_assets/php/funcoesgerais.php");
include('global_assets/php/conexao.php');

$erro = array();
$empresas = array();
$piEmpresa = 0;

// Se a pessoa preencheu o login
if(isset($_POST['usuario'])){
	$psUsuario = $_POST['usuario'];
	$psSenha = md5($_POST['senha']);
	if(isset($_POST['empresa'])){
		$piEmpresa = $_POST['empresa'];
		$_SESSION['EmpreId'] = $piEmpresa;
	}
	
	$_SESSION['UsuarLogin'] = $_POST['usuario'];

	$usuario_escape = addslashes($psUsuario);
	$senha_escape = addslashes($psSenha);

	$sql = ("SELECT Top 1 UsuarId, UsuarLogin, UsuarNome, UsuarSenha, EXUXPStatus, PerfiChave
			 FROM Usuario
			 JOIN EmpresaXUsuarioXPerfil EUP on EXUXPUsuario = UsuarId	
			 JOIN Perfil on PerfiId = EXUXPPerfil			 
			 WHERE UsuarLogin = '$usuario_escape'");
	$result = $conn->query("$sql");
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	$count = count($row);
	//$count = $result->rowCount();
	//$row = $result->fetch();
	//var_dump($row);die;
	
	$_SESSION['UsuarLogado'] = 0;
	
	$sPerfilChave = $row[0]['PerfiChave'];
	
	if ($count == 0){	
		$erro[] = "O usuário não está cadastrado.";
	} else if ($row[0]['EXUXPStatus'] == 0){
		$erro[] = "Esse usuário está desativado.";
	} else if (strcmp($row[0]['UsuarSenha'], ($psSenha)) != 0){  //"strcmp": cpmpara 2 strings (se for 0 significa que são iguais)
		$erro[] = "<strong>Senha</strong> incorreta.";
	} else {	
		
		if ($sPerfilChave == 'SUPER'){
			$sql = ("SELECT UsuarId, UsuarLogin, UsuarNome, EmpreId, EmpreNomeFantasia, PerfiChave
					 FROM Usuario
					 JOIN EmpresaXUsuarioXPerfil EUP on EXUXPUsuario = UsuarId
					 JOIN Perfil on PerfiId = EXUXPPerfil
					 JOIN Empresa on EmpreId = EXUXPEmpresa
					 JOIN Licenca on LicenEmpresa = EmpreId					 
					 ");			
		} else {
			$sql = ("SELECT UsuarId, UsuarLogin, UsuarNome, EmpreId, EmpreNomeFantasia, PerfiChave
					 FROM Usuario
					 JOIN EmpresaXUsuarioXPerfil EUP on EXUXPUsuario = UsuarId
					 JOIN Perfil on PerfiId = EXUXPPerfil
					 JOIN Empresa on EmpreId = EXUXPEmpresa
					 JOIN Licenca on LicenEmpresa = EmpreId
					 WHERE UsuarLogin = '$usuario_escape' and EXUXPStatus = 1 and 
						   EmpreId in (Select LicenEmpresa from Licenca where LicenDtFim is null or LicenDtFim > GETDATE() and LicenStatus = 1)
					 ");
		}
		$result = $conn->query("$sql");
		$row = $result->fetchAll(PDO::FETCH_ASSOC);  //Pega o número de registros associados a essa consulta
		$count = count($row);
		
		if ($count == 0 and $sPerfilChave != 'SUPER'){
			$erro[] = "A licença da sua empresa expirou. Procure o Gestor do Contrato do sistema \"Lamparinas\" na sua empresa.";			
		} else if ($count > 1 and $piEmpresa == 0) {
			$erro[] = "Você está vinculado em mais de uma empresa. Informe qual deseja acessar.";
			
			$_SESSION['EmpreId'] = 99999999;  // Se preferirem deixar pre-selecionado já uma empresa basta trocar o 9999999 por $row[0]['EmpreId']
			
			$result = $conn->query("$sql");
			while ($linhas = $result->fetch()){
				$_SESSION['Empresa'][$linhas['EmpreId']] = $linhas['EmpreNomeFantasia'];
			}
		} else if ($piEmpresa) {

			$sql = ("SELECT UsuarId, UsuarLogin, UsuarNome, EmpreId, EmpreNomeFantasia, PerfiChave
					 FROM Usuario
					 JOIN EmpresaXUsuarioXPerfil EUP on EXUXPUsuario = UsuarId
					 JOIN Perfil on PerfiId = EXUXPPerfil
					 JOIN Empresa on EmpreId = EXUXPEmpresa
					 JOIN Licenca on LicenEmpresa = EmpreId
					 WHERE UsuarLogin = '$usuario_escape' and EXUXPStatus = 1 and EmpreId = $piEmpresa and 
						   EmpreId in (Select LicenEmpresa from Licenca where LicenDtFim is null or LicenDtFim > GETDATE() and LicenStatus = 1)
					 ");			 		 
			$result = $conn->query("$sql");
			$row = $result->fetchAll(PDO::FETCH_ASSOC);
			
			$_SESSION['UsuarId'] = $row[0]['UsuarId'];
			$_SESSION['UsuarLogin'] = $row[0]['UsuarLogin'];
			$_SESSION['UsuarNome'] = $row[0]['UsuarNome'];
			$_SESSION['EmpreId'] = $row[0]['EmpreId'];
			$_SESSION['EmpreNomeFantasia'] = $row[0]['EmpreNomeFantasia'];
			$_SESSION['PerfiChave'] = $row[0]['PerfiChave'];
			$_SESSION['UsuarLogado'] = 1;
			
			irpara("index.php");
			
		} else {			
			
			$_SESSION['UsuarId'] = $row[0]['UsuarId'];
			$_SESSION['UsuarLogin'] = $row[0]['UsuarLogin'];
			$_SESSION['UsuarNome'] = $row[0]['UsuarNome'];
			$_SESSION['EmpreId'] = $row[0]['EmpreId'];
			$_SESSION['EmpreNomeFantasia'] = $row[0]['EmpreNomeFantasia'];
			$_SESSION['PerfiChave'] = $row[0]['PerfiChave'];
			$_SESSION['UsuarLogado'] = 1;
			
			irpara("index.php");
		}

	} 
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Login</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="layout_1/LTR/default/full/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="layout_1/LTR/default/full/assets/css/bootstrap_limitless.min.css" rel="stylesheet" type="text/css">
	<link href="layout_1/LTR/default/full/assets/css/layout.min.css" rel="stylesheet" type="text/css">
	<link href="layout_1/LTR/default/full/assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="layout_1/LTR/default/full/assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/main/jquery.min.js"></script>
	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	
	<!-- Theme JS files -->
	<!--<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
		<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script> -->
	
	<!-- Theme JS files -->
	<script src="layout_1/LTR/default/full/assets/js/app.js"></script>
	<!--<script src="global_assets/js/demo_pages/login_validation.js"></script> -->
	<!-- /theme JS files -->
	
	<!--<script src="global_assets/js/lamparinas/traducao.js"></script> -->
	<!--<script type="text/javascript">
		function foco(){
			//alert('Entrou');
			//document.getElementById("senha").focus();
			//$("#senha").focus();
			//document.forms[0].senha.focus();
			$(this).next().focus();
		}
	</script> -->

</head>

<body>

	<!-- Page content -->
	<div class="page-content login-cover">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">

				<!-- Login form -->
				<form name="formLogin" method="post" class="login-form form-validate" action="login.php">
					<div class="card mb-0">
						<div class="card-body">
							<div class="text-center mb-3">
								<!--<i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>-->
								<img src="global_assets/images/lamparinas/logo-lamparinas_200x200.jpg" />
								<h5 class="mb-0">Acesse sua conta</h5>
								<span class="d-block text-muted">Informe as credenciais abaixo</span>
							</div>

							<?php 
								if(isset($erro)) {
									if(count($erro) > 0){ 
							?>
										<div class="alert alert-danger">
											<?php foreach($erro as $msg) echo "$msg <br>"; ?>
										</div>
							<?php 
									}
								}
								
								if (isset($_SESSION['EmpreId'])){	
									
									print('
									
									<div class="form-group">
										<select name="empresa" class="form-control select" data-fouc>
											<option value="0">Selecione uma empresa</option>');
											
											foreach($_SESSION['Empresa'] as $indice => $valor){
												if ($_SESSION['EmpreId'] == $indice){
													echo '<option value="'.$indice.'" selected>'.$valor.'</option>';
												} else {
													echo '<option value="'.$indice.'">'.$valor.'</option>';
												}
											}
											
											print('
											</optgroup>
										</select>
									</div>
									
									');
								}								
                            ?>							
							
							<div class="form-group form-group-feedback form-group-feedback-left">
								<input value="<?php if(isset($_SESSION['UsuarLogin'])) echo $_SESSION['UsuarLogin']; ?>" name="usuario" type="text" class="form-control" placeholder="Usuário..." required <?php if(!isset($_SESSION['UsuarLogin'])) echo "autofocus"; ?>>
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input name="senha" id="senha" type="password" class="form-control" placeholder="Senha..." onKeyPress="if (event.keyCode == 13){document.forms[0].submit();}" required  <?php if(isset($_SESSION['UsuarLogin'])) echo "autofocus"; ?>>
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">Entrar <i class="icon-circle-right2 ml-2"></i></button>
							</div>

							<div class="form-group text-center">
								<a href="esqueceu-sua-senha.php">Esqueceu sua senha?</a>
							</div>							
						</div>
					</div>
					<div class="form-group text-center text-muted content-divider" style="margin-top: 15px;">
						<span class="px-2"><a href="http://www.lamparinas.com.br">Ir para o site</a></spam>
					</div>
				</form>
				<!-- /login form -->

			</div>
			<!-- /content area -->			

			<?php //include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
