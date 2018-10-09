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

	<!-- Theme JS files -->
<!--	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script> -->
	
	<!-- Theme JS files -->
	<script src="layout_1/LTR/default/full/assets/js/app.js"></script>
<!--	<script src="global_assets/js/demo_pages/login_validation.js"></script> -->
	<!-- /theme JS files -->
	
<!--	<script src="global_assets/js/lamparinas/traducao.js"></script> -->
	
</head>

<body>

	<!-- Page content -->
	<div class="page-content login-cover">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">

				<!-- Login form -->
				<form name="formLogin" method="post" class="login-form form-validate" action="index.php">
					<div class="card mb-0">
						<div class="card-body">
							<div class="text-center mb-3">
								<!--<i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>-->
								<img src="global_assets/images/lamparinas/logo-lamparinas.jpg" style="max-width: 200px;" />
								<h5 class="mb-0">Acesse sua conta</h5>
								<span class="d-block text-muted">Informe as credenciais abaixo</span>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input name="usuario" type="text" class="form-control" placeholder="Usuário..." required>
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input name="senha" type="password" class="form-control" placeholder="Senha..." onKeyPress="if (event.keyCode == 13){document.forms[0].submit();}" required>
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
