<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Usuário';

include('global_assets/php/conexao.php');

$sql = ("SELECT PerfiId, PerfiNome
		 FROM Perfil
		 Where PerfiStatus = 1
		 ORDER BY PerfiNome ASC");
$result = $conn->query("$sql");
$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);
//echo $count = count($rowPerfil);


if(isset($_POST['inputCpf'])){

	try{
		
		$sql = "INSERT INTO Usuario (UsuarCpf, UsuarNome, UsuarLogin, UsuarSenha)
				VALUES (:sCpf, :sNome, :sLogin, :sSenha)";
		$result = $conn->prepare($sql);
		$result->execute(array(
						':sCpf' => $_POST['inputCpf'],
						':sNome' => $_POST['inputNome'],
						':sLogin' => $_POST['inputLogin'],
						':sSenha' => $_POST['inputSenha']
						));
		$LAST_ID = $conn->lastInsertId();
		
						
		$sql = "INSERT INTO EmpresaXUsuarioXPerfil (EXUXPEmpresa, EXUXPUsuario, EXUXPPerfil, EXUXPStatus, EXUXPUsuarioAtualizador)
				VALUES (:iEmpresa, :iUsuario, :iPerfil, :bStatus, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
		$result->execute(array(
						':iEmpresa' => $_SESSION['EmpreId'],
						':iUsuario' => $LAST_ID,
						':iPerfil' => $_POST['inputPerfil'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));		
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário incluído!!!";
		$_SESSION['msg']['tipo'] = "success";				
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir usuário!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	

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
					
					<form name="formUsuario" method="post" class="form-validate" action="usuarioNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Usuário</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCpf">CPF</label>
												<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" maxlength="11" pattern="[0-9]+$" required>
											</div>
										</div>								
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-8">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputCpf">E-mail</label>
												<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" required>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputLogin">Login</label>
												<input type="text" id="inputLogin" name="inputLogin" class="form-control" placeholder="Login" required>
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputSenha">Senha</label>
												<input type="password" id="inputSenha" name="inputSenha" class="form-control" placeholder="Senha">
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputPerfil">Perfil</label>
												<select name="inputPerfil" class="form-control" required>
													<option value="0">Informe um perfil</option>
													<?php
														foreach ($rowPerfil as $item){
															print('<option value="'.$item['PerfiId'].'">'.$item['PerfiNome'].'</option>');
														}	
													?>
												</select>
											</div>
										</div>
										
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
										<a href="usuario.php" class="btn btn-basic" role="button">Cancelar</a>
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
