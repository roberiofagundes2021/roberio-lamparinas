<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Editar Usuário';

if (isset($_SESSION['EmpresaId'])){
	$EmpresaId = $_SESSION['EmpresaId'];
} else {	
	$EmpresaId = $_SESSION['EmpreId'];
}

if(isset($_POST['inputUsuarioId'])){
	
	$iUsuario = $_POST['inputUsuarioId'];
        	
	try{
		
		$sql = "SELECT UsuarId, UsuarCpf, UsuarNome, UsuarLogin, UsuarSenha, UsuarEmail, UsuarTelefone, UsuarCelular, EXUXPPerfil, EXUXPUnidade, EXUXPSetor
				FROM Usuario
				JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
				WHERE UsuarId = $iUsuario and EXUXPEmpresa = $EmpresaId ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();	
}

if(isset($_POST['inputCpf'])){
	
	try{
		
		$sql = "UPDATE Usuario SET UsuarCpf = :sCpf, UsuarNome = :sNome, usuarLogin = :sLogin, 
					   UsuarSenha = :sSenha, UsuarEmail = :sEmail, UsuarTelefone = :sTelefone, UsuarCelular = :sCelular
				WHERE UsuarId = :iUsuario";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCpf' => $_POST['inputCpf'],
						':sNome' => $_POST['inputNome'],
						':sLogin' => $_POST['inputLogin'],
						':sSenha' => $_POST['inputSenha'],
						':sEmail' => $_POST['inputEmail'],
						':sTelefone' => $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'],
						':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'],
						':iUsuario' => $_POST['inputUsuarioId']
						));
						
		$sql = "UPDATE EmpresaXUsuarioXPerfil SET EXUXPPerfil = :iPerfil, EXUXPUnidade = :iUnidade, 
												  EXUXPSetor = :iSetor, EXUXPUsuarioAtualizador = :iUsuarioAtualizador
				WHERE EXUXPUsuario = :iUsuario and EXUXPEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
	
		$result->execute(array(
						':iPerfil' => $_POST['inputPerfil'],
						':iUnidade' => $_POST['cmbUnidade'] == '#' ? null : $_POST['cmbUnidade'],
						':iSetor' => $_POST['cmbSetor'] == '#' ? null : $_POST['cmbSetor'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUsuario' => $_POST['inputUsuarioId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));						
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário alterado!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar usuário!!!";
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>		
	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
	
				Filtrando();
				
				var cmbUnidade = $('#cmbUnidade').val();
				alert(cmbUnidade);
				
				if (cmbUnidade == '#'){
					Reset();
				} else {

					$.getJSON('filtraSetor.php?idUnidade=' + cmbUnidade, function (dados){
						
						var option = '<option value="#">Selecione o Setor</option>';
alert(dados.length);
						if (dados.length){						
							
							$.each(dados, function(i, obj){
								option += '<option value="'+obj.SetorId+'">' + obj.SetorNome + '</option>';
							});						
							
							$('#cmbSetor').html(option).show();
						} else {
							Reset();
						}					
					});
				}	
	
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbUnidade').on('change', function(e){

				Filtrando();
				
				var cmbUnidade = $('#cmbUnidade').val();
				
				if (cmbUnidade == '#'){
					Reset();
				} else {

					$.getJSON('filtraSetor.php?idUnidade=' + cmbUnidade, function (dados){
						
						var option = '<option value="#">Selecione o Setor</option>';

						if (dados.length){						
							
							$.each(dados, function(i, obj){
								option += '<option value="'+obj.SetorId+'">' + obj.SetorNome + '</option>';
							});						
							
							$('#cmbSetor').html(option).show();
						} else {
							Reset();
						}					
					});
				}
			});	

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputCpf = $('#inputCpf').val().replace(/[^\d]+/g,'');
				var inputNome = $('#inputNome').val();
				var cmbPerfil = $('#cmbPerfil').val();
				var inputLogin = $('#inputLogin').val();
				var inputSenha = $('#inputSenha').val();
				var inputConfirmaSenha = $('#inputConfirmaSenha').val();
				var cmbUnidade = $('#cmbUnidade').val();
				var cmbSetor = $('#cmbSetor').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				inputLogin = inputLogin.trim();				

				if (inputCpf.length < 11){
					alerta('Atenção','O CPF precisa ser informado corretamente!','error');
					return false;
				}
				
				//Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
					alerta('Atenção','Informe o nome do usuário!','error');
					$('#inputNome').focus();
					return false;
				}

				if (cmbPerfil == '#'){
					alerta('Atenção','Informe o perfil!','error');
					$('#cmPerfil').focus();
					return false;
				}
				
				if (inputLogin == ''){
					alerta('Atenção','Informe o login!','error');
					$('#inputLogin').focus();
					return false;
				}
				
				if (inputSenha == ''){
					alerta('Atenção','Informe senha!','error');
					$('#inputSenha').focus();
					return false;
				}
				
				if (inputSenha != inputConfirmaSenha){
					alerta('Atenção','A confirmação de senha não confere!','error');
					$('#inputConfirmaSenha').focus();
					return false;
				}	

				if (cmbUnidade == '#'){
					alerta('Atenção','Informe a unidade!','error');
					$('#cmUnidade').focus();
					return false;
				}

				if (cmbSetor == '#'){
					alerta('Atenção','Informe o setor!','error');
					$('#cmSetor').focus();
					return false;
				}				
				
				$( "#formUsuario" ).submit();
			})
			
			function Filtrando(){
				$('#cmbSetor').empty().append('<option>Filtrando...</option>');
			}
			
			function Reset(){
				$('#cmbSetor').empty().append('<option value="#">Sem setor</option>');
			}			
		});
		
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
				<div class="card">
					
					<form name="formUsuario" id="formUsuario" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Usuário "<?php echo $row['UsuarNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputUsuarioId" name="inputUsuarioId" value="<?php echo $row['UsuarId']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCpf">CPF</label>
												<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" value="<?php echo formatarCPF_Cnpj($row['UsuarCpf']); ?>" required readOnly>
											</div>
										</div>
										<div class="col-lg-7">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['UsuarNome']; ?>" required>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputPerfil">Perfil</label>
												<select name="inputPerfil" class="form-control form-control-select2" required>
													<option value="0">Informe um perfil</option>
													<?php
														$sql = ("SELECT PerfiId, PerfiNome
																 FROM Perfil
																 Where PerfiStatus = 1
																 ORDER BY PerfiNome ASC");
														$result = $conn->query("$sql");
														$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);													
													
														foreach ($rowPerfil as $item){
															if($item['PerfiId'] == $row['EXUXPPerfil']){
																print('<option value="'.$item['PerfiId'].'" selected="selected">'.$item['PerfiNome'].'</option>');
															} else {
																print('<option value="'.$item['PerfiId'].'">'.$item['PerfiNome'].'</option>');
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
												<label for="inputLogin">Login</label>
												<input type="text" id="inputLogin" name="inputLogin" class="form-control" placeholder="Login" value="<?php echo $row['UsuarLogin']; ?>" required>
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputSenha">Senha</label>
												<input type="password" id="inputSenha" name="inputSenha" class="form-control" placeholder="Senha" value="<?php echo $row['UsuarSenha']; ?>">
											</div>
										</div>	
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputConfirmaSenha">Confirma Senha</label>
												<input type="password" id="inputConfirmaSenha" name="inputConfirmaSenha" class="form-control" placeholder="Confirma Senha" value="<?php echo $row['UsuarSenha']; ?>">
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
												<label for="inputEmail">E-mail</label>
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
							
							<h5 class="mb-0 font-weight-semibold">Lotação</h5>
							<br>
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbUnidade">Unidade</label>
												<select name="cmbUnidade" id="cmbUnidade" class="form-control form-control-select2" required>
													<option value="#">Informe uma unidade</option>
													<?php 
														$sql = ("SELECT UnidaId, UnidaNome
																 FROM Unidade															     
																 WHERE UnidaEmpresa = ". $EmpresaId ." and UnidaStatus = 1
																 ORDER BY UnidaNome ASC");
														$result = $conn->query("$sql");
														$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowUnidade as $item){
															if($item['UnidaId'] == $row['EXUXPUnidade']){
																print('<option value="'.$item['UnidaId'].'" selected="selected">'.$item['UnidaNome'].'</option>');
															} else {
																print('<option value="'.$item['UnidaId'].'">'.$item['UnidaNome'].'</option>');
															}
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSetor">Setor</label>
												<select name="cmbSetor" id="cmbSetor" class="form-control form-control-select2" required>
													<option value="#">Informe um setor</option>
													<?php 
														$sql = ("SELECT SetorId, SetorNome
																 FROM Setor															     
																 WHERE SetorEmpresa = ". $EmpresaId ." and SetorStatus = 1
																 ORDER BY SetorNome ASC");
														$result = $conn->query("$sql");
														$rowSetor = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowSetor as $item){	
															if($item['SetorId'] == $row['EXUXPSetor']){
																print('<option value="'.$item['SetorId'].'" selected="selected">'.$item['SetorNome'].'</option>');
															} else {
																print('<option value="'.$item['SetorId'].'">'.$item['SetorNome'].'</option>');
															}
														}
													
													?>
												</select>
											</div>
										</div>
										
									</div>
								</div>
							</div>							
								
							<div class="row" style="margin-top: 20px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
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
