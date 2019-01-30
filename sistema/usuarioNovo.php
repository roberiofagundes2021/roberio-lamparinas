<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Novo Usuário';

if (isset($_SESSION['EmpresaId'])){
	$EmpresaId = $_SESSION['EmpresaId'];
} else {	
	$EmpresaId = $_SESSION['EmpreId'];
}

if(isset($_POST['inputCpf'])){

	try{
		
		$conn->beginTransaction();
		
		//Se for um novo usuário que ainda não estava cadastrado em nenhuma empresa
		if ($_POST['inputId'] == 0){
		
			$sql = "INSERT INTO Usuario (UsuarCpf, UsuarNome, UsuarLogin, UsuarSenha, UsuarEmail, UsuarTelefone, UsuarCelular)
					VALUES (:sCpf, :sNome, :sLogin, :sSenha, :sEmail, :sTelefone, :sCelular)";
			$result = $conn->prepare($sql);			
			
			$result->execute(array(
							':sCpf' => limpaCPF_CNPJ($_POST['inputCpf']),
							':sNome' => $_POST['inputNome'],
							':sLogin' => $_POST['inputLogin'],
							':sSenha' => $_POST['inputSenha'],
							':sEmail' => $_POST['inputEmail'],
							':sTelefone' => $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'],
							':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular']						
							));
			$LAST_ID = $conn->lastInsertId();
			
							
			$sql = "INSERT INTO EmpresaXUsuarioXPerfil (EXUXPEmpresa, EXUXPUsuario, EXUXPPerfil, EXUXPUnidade, 
														EXUXPSetor, EXUXPStatus, EXUXPUsuarioAtualizador)
					VALUES (:iEmpresa, :iUsuario, :iPerfil, :iUnidade, :iSetor, :bStatus, :iUsuarioAtualizador)";
			$result = $conn->prepare($sql);
			$result->execute(array(
							':iEmpresa' => $EmpresaId,
							':iUsuario' => $LAST_ID,
							':iPerfil' => $_POST['cmbPerfil'],
							':iUnidade' => $_POST['cmbUnidade'] == '#' ? null : $_POST['cmbUnidade'],
							':iSetor' => $_POST['cmbSetor'] == '#' ? null : $_POST['cmbSetor'],
							':bStatus' => 1,
							':iUsuarioAtualizador' => $_SESSION['UsuarId']
							));
		} else {
			
			$sql = "UPDATE Usuario SET UsuarNome = :sNome, usuarLogin = :sLogin, 
						   UsuarSenha = :sSenha, UsuarEmail = :sEmail, UsuarTelefone = :sTelefone, UsuarCelular = :sCelular
					WHERE UsuarId = :iUsuario";
			$result = $conn->prepare($sql);
					
			$result->execute(array(							
							':sNome' => $_POST['inputNome'],
							':sLogin' => $_POST['inputLogin'],
							':sSenha' => $_POST['inputSenha'],
							':sEmail' => $_POST['inputEmail'],
							':sTelefone' => $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'],
							':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'],
							':iUsuario' =>  $_POST['inputId']
							));
			
			$sql = "INSERT INTO EmpresaXUsuarioXPerfil (EXUXPEmpresa, EXUXPUsuario, EXUXPPerfil, EXUXPUnidade, 
														EXUXPSetor, EXUXPStatus, EXUXPUsuarioAtualizador)
					VALUES (:iEmpresa, :iUsuario, :iPerfil, :iUnidade, :iSetor, :bStatus, :iUsuarioAtualizador)";
			$result = $conn->prepare($sql);
			$result->execute(array(
							':iEmpresa' => $EmpresaId,
							':iUsuario' => $_POST['inputId'],
							':iPerfil' => $_POST['cmbPerfil'] == '#' ? null : $_POST['cmbPerfil'],
							':iUnidade' => $_POST['cmbUnidade'] == '#' ? null : $_POST['cmbUnidade'],
							':iSetor' => $_POST['cmbSetor'] == '#' ? null : $_POST['cmbSetor'],
							':bStatus' => 1,
							':iUsuarioAtualizador' => $_SESSION['UsuarId']
							));
		}
		
		$conn->commit();
							
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário incluído!!!";
		$_SESSION['msg']['tipo'] = "success";				
		
	} catch(PDOException $e) {
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir usuário!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
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

		function validaCPF(strCPF) {
			var Soma;
			var Resto;
			Soma = 0;
		  if (strCPF == "00000000000") return false;
			 
		  for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
		  Resto = (Soma * 10) % 11;
		   
			if ((Resto == 10) || (Resto == 11))  Resto = 0;
			if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;
		   
		  Soma = 0;
			for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
			Resto = (Soma * 10) % 11;
		   
			if ((Resto == 10) || (Resto == 11))  Resto = 0;
			if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
			return true;
		}

        $(document).ready(function() {	
			
			//Garantindo que ninguém mude a empresa na tela de inclusão
			$('#cmbEmpresa').prop("disabled", true);
	
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#buscar').on('click', function(e){				
			
				var inputCpf = $('#inputCpf').val().replace(/[^\d]+/g,'');
				var inputId = $('#inputId').val();

				if (inputCpf.length < 11){
					alerta('Atenção','O CPF precisa ser informado corretamente!','error');
					$('#inputCpf').focus();
					return false;
				}
				
				if (!validaCPF(inputCpf)){
					alerta('Atenção','CPF inválido!','error');
					$('#inputCpf').focus();
					return false;					
				}	
								
				$.getJSON('usuarioValida.php?cpf='+inputCpf, function (dados){
					
					//Se o usuário está cadastrado e ele não está vinculado a essa empresa ainda
					if (typeof dados === 'object'){
												
						document.getElementById('demaisCampos').style.display = "block";
						
						$.each(dados, function(i, obj){
							$('#inputId').val(obj.UsuarId);
							$('#inputNome').val(obj.UsuarNome);
							$('#inputLogin').val(obj.UsuarLogin);
							$('#inputSenha').val(obj.UsuarSenha);
							$('#inputConfirmaSenha').val(obj.UsuarSenha);
							$('#inputEmail').val(obj.UsuarEmail);
							$('#inputTelefone').val(obj.UsuarTelefone);
							$('#inputCelular').val(obj.UsuarCelular);
						});						
						
						$('#enviar').prop("disabled", false);
		
					} else {
						//se o usuário está cadastrado e já está vinculado a essa empresa
						if (dados){
							document.getElementById('demaisCampos').style.display = "none";
							alerta('Atenção','O usuário com CPF ' + inputCpf + ' já está vinculado a essa empresa!','error');
							$('#enviar').prop("disabled", true);
							$('#inputCpf').val();
							$('#inputCpf').focus();
							return false;
						} else { // se o usuário não está cadastrado
							document.getElementById('demaisCampos').style.display = "block";
							$('#inputNome').val("");
							$('#cmbPerfil').val("#");
							$('#inputLogin').val("");
							$('#inputSenha').val("");
							$('#inputConfirmaSenha').val("");
							$('#inputEmail').val("");
							$('#inputTelefone').val("");
							$('#inputCelular').val("");
							$('#cmbUnidade').val("#");
							$('#cmbSetor').val("#");
							$('#inputNome').focus();
							$('#enviar').prop("disabled", false);
							$('#inputId').val(0);
						}
					}					
				});
			});

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

			$('#inputCpf').on('change', function(e){
				$('#buscar').trigger('click');
			});
			
			$('#inputCpf').keypress(function(e) {
				if (e.which == 13) {
					 $('#buscar').trigger('click'); 
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
					alerta('Atenção','Informe a senha!','error');
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

				if (cmbSetor == '#' || cmbSetor == 'Filtrando...'){
					alerta('Atenção','Informe o setor!','error');
					$('#cmSetor').focus();
					return false;
				}				
				
				$('#cmbEmpresa').prop("disabled", false);
				
				$( "#formUsuario" ).submit();
			})
			
			$('#cancelar').on('click', function(e){
				
				$('#cmbEmpresa').prop("disabled", false);
				
				$(window.document.location).attr('href', "usuario.php");
			});
			
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
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Usuário</h5>
						</div>
						
						<div class="card-body">	
							<div class="row">
								<div class="col-lg-2" style="max-width:150px;">
									<label for="inputCpf">CPF</label>
									<div class="form-group form-group-feedback form-group-feedback-right">										
										<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" required autofocus>
										<div class="form-control-feedback" id="buscar" style="cursor: pointer;">
											<i class="icon-search4"></i>
										</div>
										<input type="hidden" id="inputId" name="inputId" value="0">
									</div>
								</div>
							</div>
							
							<div id="demaisCampos" style="display:none;">
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
													<label for="cmbPerfil">Perfil</label>
													<select id="cmbPerfil" name="cmbPerfil" class="form-control form-control-select2" required>
														<option value="#">Informe um perfil</option>
														<?php
															$sql = ("SELECT PerfiId, PerfiNome
																	 FROM Perfil
																	 Where PerfiStatus = 1
																	 ORDER BY PerfiNome ASC");
															$result = $conn->query("$sql");
															$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);
														
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
								
								<h5 class="mb-0 font-weight-semibold">Login</h5>
								<br>
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
													<label for="inputConfirmaSenha">Confirma Senha</label>
													<input type="password" id="inputConfirmaSenha" name="inputConfirmaSenha" class="form-control" placeholder="Confirma Senha">
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
													<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" required>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputTelefone">Telefone</label>
													<input type="tel" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999">
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputCelular">Celular</label>
													<input type="tel" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999">
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
																print('<option value="'.$item['UnidaId'].'">'.$item['UnidaNome'].'</option>');
															}
														
														?>
													</select>
												</div>
											</div>
											
											<div class="col-lg-6">
												<div class="form-group">
													<label for="cmbSetor">Setor</label>
													<select name="cmbSetor" id="cmbSetor" class="form-control form-control-select2" required>
														<option value="#">Sem setor</option>
													</select>
												</div>
											</div>
											
										</div>
									</div>
								</div>
							</div>
							
							<div class="row" style="margin-top: 20px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" disabled id="enviar">Incluir</button>
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
