<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Profissional';

include('global_assets/php/conexao.php');

//Se veio do Profissional.php
if(isset($_POST['inputProfissionalId'])){
	 
	$iProfissional = $_POST['inputProfissionalId'];
	$iUnidade = $_SESSION['UnidadeId'];
		
	$sql = "SELECT *
			FROM Profissional
			WHERE ProfiId = $iProfissional";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT PrXEsId,PrXEsProfissional,PrXEsEspecialidade,PrXEsUnidade
		FROM ProfissionalXEspecialidade
		WHERE PrXEsProfissional = $iProfissional";
	$result = $conn->query($sql);
	$rowEspecialidades = $result->fetchAll(PDO::FETCH_ASSOC);

	$arrayEspecialidades = [];

	foreach($rowEspecialidades as $item){
		array_push($arrayEspecialidades, $item['PrXEsEspecialidade']);
	}
						
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("profissional.php");
}

if(isset($_POST['inputTipo'])){	
		
	try{
		$sTipo= $_POST['inputTipo'];
		$sNome= $_POST['inputTipo'] == 'J' ? $_POST['inputNomePJ'] : $_POST['inputNomePF'];
		$sRazaoSocial= $_POST['inputTipo'] == 'J' ? $_POST['inputRazaoSocial'] : null;
		$sCnpj= $_POST['inputTipo'] == 'J' ? limpaCPF_CNPJ($_POST['inputCnpj']) : null;
		$sInscricaoMunicipal= $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoMunicipal'] : null;
		$sInscricaoEstadual= $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoEstadual'] : null;
		$sCpf= $_POST['inputTipo'] == 'F' ? limpaCPF_CNPJ($_POST['inputCpf']) : null;
		$sRg= $_POST['inputTipo'] == 'F' ? $_POST['inputRg'] : null;
		$sOrgaoEmissor= $_POST['inputTipo'] == 'F' ? $_POST['inputEmissor'] : null;
		$sUf= $_POST['inputTipo'] == 'J' || $_POST['cmbUf'] == '#' ? null : $_POST['cmbUf'];
		$sSexo= $_POST['inputTipo'] == 'J' || $_POST['cmbSexo'] == '#' ? null : $_POST['cmbSexo'];
		$dDtNascimento= $_POST['inputTipo'] == 'F' ? ($_POST['inputDtNascimento'] == '' ? null : $_POST['inputDtNascimento']) : null;
		$sProfissao= $_POST['inputTipo'] == 'F'? ($_POST['cmbProfissao'] == '#' || $_POST['cmbProfissao'] == '' ? null : $_POST['cmbProfissao']) : NULL;
		$sConselho= $_POST['inputTipo'] == 'F' ? ($_POST['cmbConselho'] == '#' ? null : $_POST['cmbConselho']) : null;
		$sNumConselho= $_POST['inputTipo'] == 'F' ? $_POST['inputNumConselho'] : null;
		$sCnes= $_POST['inputTipo']  == 'J' ? $_POST['inputCnesPJ'] : $_POST['inputCnesPF'];
		$sCep= trim($_POST['inputCep']) == "" ? null : $_POST['inputCep'];
		$sEndereco= $_POST['inputEndereco'];
		$sNumero= $_POST['inputNumero'];
		$sComplemento= $_POST['inputComplemento'];
		$sBairro= $_POST['inputBairro'];
		$sCidade= $_POST['inputCidade'];
		$sEstado= $_POST['cmbEstado'];
		$sContato= $_POST['inputNomeContato'];
		$sTelefone= $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'];
		$sCelular= $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'];
		$sEmail= $_POST['inputEmail'];
		$sSite= $_POST['inputSite'];
		$sObservacao= $_POST['txtareaObservacao'];
		$sBanco= $_POST['cmbBanco'];
		$sAgencia= $_POST['inputAgencia'];
		$sConta= $_POST['inputConta'];
		$sInformacaoAdicional= $_POST['inputInformacaoAdicional'];
		$iUsuario= $_POST['cmbUsuario'];
		$iUsuarioAtualizador= $_SESSION['UsuarId'];
		$iProfissional= $_POST['inputProfissionalId'];
		
		$sql = "UPDATE Profissional SET ProfiTipo = '$sTipo', ProfiNome = '$sNome', ProfiRazaoSocial = '$sRazaoSocial', ProfiCnpj = '$sCnpj', 
                      ProfiInscricaoMunicipal = '$sInscricaoMunicipal', ProfiInscricaoEstadual = '$sInscricaoEstadual', 
                      ProfiCpf = '$sCpf', ProfiRg = '$sRg', ProfiOrgaoEmissor = '$sOrgaoEmissor', ProfiUf = '$sUf', ProfiSexo = '$sSexo', ProfiDtNascimento = '$dDtNascimento', 
                      ProfiProfissao = '$sProfissao', ProfiConselho = '$sConselho', ProfiNumConselho = '$sNumConselho', ProfiCNES =  '$sCnes',
                      ProfiCep = '$sCep', ProfiEndereco = '$sEndereco', ProfiNumero = '$sNumero', ProfiComplemento = '$sComplemento', ProfiBairro = '$sBairro', 
                      ProfiCidade = '$sCidade', ProfiEstado = '$sEstado', ProfiContato = '$sContato', ProfiTelefone = '$sTelefone', 
                      ProfiCelular = '$sCelular', ProfiEmail = '$sEmail', ProfiSite = '$sSite', ProfiObservacao = '$sObservacao', ProfiBanco = '$sBanco', 
                      ProfiAgencia = '$sAgencia', ProfiConta = '$sConta',ProfiInformacaoAdicional = '$sInformacaoAdicional', ProfiUsuario = '$iUsuario', ProfiUsuarioAtualizador = '$iUsuarioAtualizador'
				WHERE ProfiId = '$iProfissional'";
		$conn->query($sql);

		if($_POST['inputTipo'] == 'F'){
			$sql = "DELETE FROM ProfissionalXEspecialidade WHERE PrXEsProfissional = $iProfissional and PrXEsUnidade = $iUnidade";
			$conn->query($sql);

			$sql = "INSERT INTO ProfissionalXEspecialidade(PrXEsProfissional,PrXEsEspecialidade,PrXEsUnidade)
			VALUES ";

			if(isset($_POST['cmbEspecialidade'])){
				foreach($_POST['cmbEspecialidade'] as $item){
						$sql .= "('$iProfissional', '$item', '$iUnidade'),";
					}
					$sql = substr($sql, 0, -1);
					$conn->query($sql);
				}
			}
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Profissional alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar profissional!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	irpara("profissional.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Profissional</title>

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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>	

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

		window.onload = function(){
			//Ao carregar a página é verificado se é PF ou PJ para aparecer os campos relacionados e esconder o que não estiver
			var tipo = $('input[name="inputTipo"]:checked').val();

			selecionaPessoa(tipo);
		}

        $(document).ready(function() {			

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#inputEndereco").val("");
                $("#inputBairro").val("");
                $("#inputCidade").val("");
                $("#cmbEstado").val("");   
				$("#inputNumero").val("");
				$("#inputComplemento").val("");             
            }
            
            //Quando o campo cep perde o foco.
            $("#inputCep").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#inputEndereco").val("...");
                        $("#inputBairro").val("...");
                        $("#inputCidade").val("...");
                        $("#cmbEstado").val("...");                        

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {

                                //Atualiza os campos com os valores da consulta.
                                $("#inputEndereco").val(dados.logradouro);
                                $("#inputBairro").val(dados.bairro);
                                $("#inputCidade").val(dados.localidade);
                                $("#cmbEstado").val(dados.uf);
								$("#cmbEstado").find('option:selected').text();
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alerta("Erro","CEP não encontrado.", "erro");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
						$("#inputCep").val("");
                        limpa_formulário_cep();
                        alerta("Erro","Formato de CEP inválido.","erro");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            }); //cep

			//Valida Registro Duplicado
			$("#enviar").on('click', function(e){
				e.preventDefault();

				// subistitui qualquer espaço em branco no campo "CEP" antes de enviar para o banco
				var cep = $("#inputCep").val()
				cep = cep.replace(' ','')
				$("#inputCep").val(cep)
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNomeNovo  = "";
				var inputNomeNovoPF  = $('#inputNomePF').val();
				var inputNomeNovoPJ  = $('#inputNomePJ').val();
				var inputNomeVelho = $('#inputProfissionalNome').val();				
				var inputCpf  = $('#inputCpf').val().replace(/[^\d]+/g,'');
				var inputCnpj = $('#inputCnpj').val().replace(/[^\d]+/g,'');
				var cmbNomeNovo = $('#cmbUsuario').val();
      			var cmbNomeVelho = $('#cmbUsuarioVelho').val();


				if (inputTipo == 'F'){ 
					inputNomeNovo = inputNomeNovoPF; 

					if (inputCpf.trim() == ''){
						$('#inputCpf').val('');
					} else {
						if (!validaCPF(inputCpf)){
							$('#inputCpf').val('');
							alerta('Atenção','CPF inválido!','error');
							$('#inputCpf').focus();
							return false;
						}
					}

				} else{ 
                    inputNomeNovo = inputNomeNovoPJ;

					if (inputCnpj.trim() == ''){
						$('#inputCnpj').val('');
					} else {
						if (!validarCNPJ(inputCnpj)){
							$('#inputCnpj').val('');
							alerta('Atenção','CNPJ inválido!','error');
							$('#inputCnpj').focus();
							return false;
						}
					}			

				}
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();

				//Esse ajax está sendo usado para verificar o usuário já é vinculado a um profissional 
				$.ajax({
					type: "POST",
					url: "profissionalUsuarioValida.php",
					data: ('nomeNovo=' + cmbNomeNovo + '&nomeVelho=' + cmbNomeVelho),
					success: function(resposta) {

						if (resposta == 1) {
							alerta('Atenção', 'Esse usuário já está vinculado a outro profissional!', 'error');
							return false;
						} else{

							//Esse ajax está sendo usado para verificar no banco se o registro já existe
							$.ajax({
								type: "POST",
								url: "profissionalValida.php",
								data: {tipo: inputTipo, nomeNovo: inputNomeNovo, nomeVelho: inputNomeVelho, cpf: inputCpf, cnpj: inputCnpj},
								success: function(resposta){
									
									if(resposta == 1){
										alerta('Atenção','Esse funcionário já possui cadastro no sistema!','error');
										return false;
									}
									
									$( "#formProfissional" ).submit();
								}
							}); //ajax
						}
					}
				})
				
			}); // enviar
            
            
        }); //document.ready
                
        
        function selecionaPessoa(tipo) {

			if (tipo == 'F'){
				
				document.getElementById('dadosPF').style.display = "block";
				document.getElementById('dadosPJ').style.display = "none";

				document.getElementById('inputNomePF').setAttribute('required', 'required');				
				document.getElementById('inputCpf').setAttribute('required', 'required');
				document.getElementById('inputNomePJ').removeAttribute('required', 'required');
				document.getElementById('inputCnpj').removeAttribute('required', 'required');	
				
			} else {
								
				document.getElementById('dadosPF').style.display = "none";
				document.getElementById('dadosPJ').style.display = "block";

				document.getElementById('inputNomePF').removeAttribute('required', 'required');				
				document.getElementById('inputCpf').removeAttribute('required', 'required');
				document.getElementById('inputNomePJ').setAttribute('required', 'required');
				document.getElementById('inputCnpj').setAttribute('required', 'required');
				
			}
		}
		
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

		function validarCNPJ(cnpj) {
 
			cnpj = cnpj.replace(/[^\d]+/g,'');

			if(cnpj == '') return false;
			
			if (cnpj.length != 14)
				return false;

			// Elimina CNPJs invalidos conhecidos
			if (cnpj == "00000000000000" || 
				cnpj == "11111111111111" || 
				cnpj == "22222222222222" || 
				cnpj == "33333333333333" || 
				cnpj == "44444444444444" || 
				cnpj == "55555555555555" || 
				cnpj == "66666666666666" || 
				cnpj == "77777777777777" || 
				cnpj == "88888888888888" || 
				cnpj == "99999999999999")
				return false;
				
			// Valida DVs
			tamanho = cnpj.length - 2
			numeros = cnpj.substring(0,tamanho);
			digitos = cnpj.substring(tamanho);
			soma = 0;
			pos = tamanho - 7;
			for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
					pos = 9;
			}
			resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
			if (resultado != digitos.charAt(0))
				return false;
				
			tamanho = tamanho + 1;
			numeros = cnpj.substring(0,tamanho);
			soma = 0;
			pos = tamanho - 7;
			for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
					pos = 9;
			}
			resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
			if (resultado != digitos.charAt(1))
				return false;
					
			return true;
			
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
					<form name="formProfissional" id="formProfissional" method="POST" class="form-validate-jquery" action="profissionalEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Profissional "<?php echo $row['ProfiNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputProfissionalId" name="inputProfissionalId" value="<?php echo $row['ProfiId']; ?>" >
						<input type="hidden" id="inputProfissionalNome" name="inputProfissionalNome" value="<?php echo $row['ProfiNome']; ?>" >
						<input type="hidden" id="cmbUsuarioVelho" name="cmbUsuarioVelho" value="<?php echo $row['ProfiUsuario']; ?>">
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="F" class="form-input-styled" data-fouc onclick="selecionaPessoa('F')"  <?php if ($row['ProfiTipo'] == 'F') echo "checked"; ?> >
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="J" class="form-input-styled" data-fouc onclick="selecionaPessoa('J')" <?php if ($row['ProfiTipo'] == 'J') echo "checked"; ?>>
												Pessoa Jurídica
											</label>
										</div>										
									</div>									
								</div>
								<div class="col-lg-4">									
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbUsuario">Usuário <span class="text-danger">*</span></label>
										<select id="cmbUsuario" name="cmbUsuario" class="form-control select-search" required>
											<option value="">Selecione o usuário referente ao profissional</option>
											<?php
												$sql = "SELECT UsuarId, UsuarNome
														FROM Usuario
														JOIN EmpresaXUsuarioXPerfil ON EXUXPUsuario = UsuarId
														JOIN UsuarioXUnidade on UsXUnEmpresaUsuarioPerfil = EXUXPId
														JOIN Situacao on SituaId = EXUXPStatus
														WHERE UsXUnUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
														ORDER BY UsuarNome ASC";
												$result = $conn->query($sql);
												$rowUsuario = $result->fetchAll(PDO::FETCH_ASSOC);

												foreach ($rowUsuario as $item){
													$seleciona = $item['UsuarId'] == $row['ProfiUsuario'] ? "selected" : "";
													print('<option value="'.$item['UsuarId'].'" '. $seleciona .'>'. $item['UsuarNome']. '</option>');
												}
											?>
										</select>
									</div>
								</div>
							</div>
							
							<h5 class="mb-0 font-weight-semibold">Dados Pessoais</h5>
							<br>				
							<div class="row">				
								<div class="col-lg-12">
									<div id="dadosPF">
										<div class="row">
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputCodigo">Código</label>
													<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Prontuário Eletônico" value="<?php echo $row['ProfiCodigo']; ?>" readOnly>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="form-group">
												<label for="inputNomePF">Nome<span class="text-danger"> *</span></label>
													<input type="text" id="inputNomePF" name="inputNomePF" class="form-control" placeholder="Nome Completo" value="<?php echo $row['ProfiNome']; ?>" required autofocus>
												</div>
											</div>	
											
											<div class="col-lg-4" id="CPF">
												<div class="form-group">
													<label for="inputCpf">CPF<span class="text-danger"> *</span></label>
													<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" value="<?php echo formatarCPF_Cnpj($row['ProfiCpf']); ?>" <?php if ($row['ProfiTipo'] == 'F') echo "required"; ?>>
												</div>	
											</div>
										</div>	

										<div class="row">
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputRg">RG</label>
													<input type="text" id="inputRg" name="inputRg" class="form-control" placeholder="RG" value="<?php echo $row['ProfiRg']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputEmissor">Emissor</label>
													<input type="text" id="inputEmissor" name="inputEmissor" class="form-control" placeholder="Órgão Emissor" value="<?php echo $row['ProfiOrgaoEmissor']; ?>">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="cmbUf">UF</label>
													<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
														<option value="#">Selecione um estado</option>
														<option value="AC" <?php if ($row['ProfiUf'] == 'AC') echo "selected"; ?> >Acre</option>
														<option value="AL" <?php if ($row['ProfiUf'] == 'AL') echo "selected"; ?> >Alagoas</option>
														<option value="AP" <?php if ($row['ProfiUf'] == 'AP') echo "selected"; ?> >Amapá</option>
														<option value="AM" <?php if ($row['ProfiUf'] == 'AM') echo "selected"; ?> >Amazonas</option>
														<option value="BA" <?php if ($row['ProfiUf'] == 'BA') echo "selected"; ?> >Bahia</option>
														<option value="CE" <?php if ($row['ProfiUf'] == 'CE') echo "selected"; ?> >Ceará</option>
														<option value="DF" <?php if ($row['ProfiUf'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
														<option value="ES" <?php if ($row['ProfiUf'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
														<option value="GO" <?php if ($row['ProfiUf'] == 'GO') echo "selected"; ?> >Goiás</option>
														<option value="MA" <?php if ($row['ProfiUf'] == 'MA') echo "selected"; ?> >Maranhão</option>
														<option value="MT" <?php if ($row['ProfiUf'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
														<option value="MS" <?php if ($row['ProfiUf'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
														<option value="MG" <?php if ($row['ProfiUf'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
														<option value="PA" <?php if ($row['ProfiUf'] == 'PA') echo "selected"; ?> >Pará</option>
														<option value="PB" <?php if ($row['ProfiUf'] == 'PB') echo "selected"; ?> >Paraíba</option>
														<option value="PR" <?php if ($row['ProfiUf'] == 'PR') echo "selected"; ?> >Paraná</option>
														<option value="PE" <?php if ($row['ProfiUf'] == 'PE') echo "selected"; ?> >Pernambuco</option>
														<option value="PI" <?php if ($row['ProfiUf'] == 'PI') echo "selected"; ?> >Piauí</option>
														<option value="RJ" <?php if ($row['ProfiUf'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
														<option value="RN" <?php if ($row['ProfiUf'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
														<option value="RS" <?php if ($row['ProfiUf'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
														<option value="RO" <?php if ($row['ProfiUf'] == 'RO') echo "selected"; ?> >Rondônia</option>
														<option value="RR" <?php if ($row['ProfiUf'] == 'RR') echo "selected"; ?> >Roraima</option>
														<option value="SC" <?php if ($row['ProfiUf'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
														<option value="SP" <?php if ($row['ProfiUf'] == 'SP') echo "selected"; ?> >São Paulo</option>
														<option value="SE" <?php if ($row['ProfiUf'] == 'SE') echo "selected"; ?> >Sergipe</option>
														<option value="TO" <?php if ($row['ProfiUf'] == 'TO') echo "selected"; ?> >Tocantins</option>
														<option value="ES" <?php if ($row['ProfiUf'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
													</select>
												</div>
											</div>
											
											<div class="col-lg-2">
												<div class="form-group">
													<label for="cmbSexo">Sexo</label>
													<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
														<option value="#">Selecione o sexo</option>
														<option value="F" <?php if ($row['ProfiSexo'] == 'F') echo "selected"; ?> >Feminino</option>
														<option value="M" <?php if ($row['ProfiSexo'] == 'M') echo "selected"; ?> >Masculino</option>
													</select>
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputDtNascimento">Data Nascimento</label>
													<input type="date" id="inputDtNascimento" name="inputDtNascimento" class="form-control" placeholder="Data Nascimento" value="<?php echo $row['ProfiDtNascimento']; ?>">
												</div>
											</div>										
										</div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-12">									
                                                <h5 class="mb-0 font-weight-semibold">Dados Profissionais</h5>
                                                <br>
                                                <div class="row">								
                                                    <div class="col-lg-3">
                                                        <label for="cmbProfissao">Profissão</label>
                                                        <select id="cmbProfissao" name="cmbProfissao" class="form-control select-search">
                                                            <option value="#">Selecione uma profissão</option>
                                                            <?php 
                                                                $sql = "SELECT ProfiId, ProfiNome
                                                                        FROM Profissao
                                                                        JOIN Situacao on SituaId = ProfiStatus
                                                                        WHERE ProfiUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                                        ORDER BY ProfiNome ASC";
                                                                $result = $conn->query($sql);
                                                                $rowProfissao = $result->fetchAll(PDO::FETCH_ASSOC);
                                                                
                                                                foreach ($rowProfissao as $item){
                                                                    $seleciona = $item['ProfiId'] == $row['ProfiProfissao'] ? "selected" : "";
                                                                    print("<option value='$item[ProfiId]' $seleciona>$item[ProfiNome]</option>");
                                                                }
                                                            
                                                            ?>
                                                        </select>
                                                    </div>

													<div class="col-lg-2">
                                                        <label for="cmbConselho">Conselho</label>
                                                        <select id="cmbConselho" name="cmbConselho" class="form-control select-search">
                                                            <option value="#">Selecione </option>
															 <?php 
                                                                $sql = "SELECT PrConId, PrConNome
                                                                        FROM ProfissionalConselho
                                                                        JOIN Situacao on SituaId = PrConStatus
                                                                        WHERE SituaChave = 'ATIVO'
                                                                        ORDER BY PrConNome ASC";
                                                                $result = $conn->query($sql);
                                                                $rowConselho = $result->fetchAll(PDO::FETCH_ASSOC);
                                                                
                                                                foreach ($rowConselho as $item){
                                                                    $seleciona = $item['PrConId'] == $row['ProfiConselho'] ? "selected" : "";
                                                                    print('<option value="'.$item['PrConId'].'" '. $seleciona .'>'. $item['PrConNome']. '</option>');
                                                                }
                                                            
                                                            ?>
                                                           
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label for="inputNumConselho">Nº do Conselho/UF</label>
                                                            <input type="text" id="inputNumConselho" name="inputNumConselho" class="form-control" placeholder="CRM/Outros" value="<?php echo $row['ProfiNumConselho']; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label for="inputCnesPF">CNES</label>
                                                            <input type="text" id="inputCnesPF" name="inputCnesPF" class="form-control" placeholder="CNES" value="<?php echo $row['ProfiCNES']; ?>">
                                                        </div>
                                                    </div>
                                                
                                                    <div class="col-lg-3">
                                                        <label for="cmbEspecialidade">Especialidades</label>
                                                        <select id="cmbEspecialidade" name="cmbEspecialidade[]" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
                                                            <?php
																$sql = "SELECT EspecId, EspecNome
																		FROM Especialidade
																		JOIN Situacao on SituaId = EspecStatus
																		WHERE EspecUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		ORDER BY EspecNome ASC";
																$result = $conn->query($sql);
																$rowEspecialidade = $result->fetchAll(PDO::FETCH_ASSOC);

																foreach ($rowEspecialidade as $item) {
																	if(in_array($item['EspecId'], $arrayEspecialidades)){
																		print("<option selected value='$item[EspecId]'>$item[EspecNome]</option>");
																	}else{
																		print("<option value='$item[EspecId]'>$item[EspecNome]</option>");
																	}
																}
                                                            ?>
														</select>
                                                    </div>									
                                                </div>										
                                            </div>
                                        </div>	
										
									</div> <!-- Fim dadosPF -->
									
									<div id="dadosPJ">
										<div class="row">
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputCodigo">Código</label>
													<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código Interno" value="<?php echo $row['ProfiCodigo']; ?>" readOnly>
												</div>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
												    <label for="inputNomePJ">Nome<span class="text-danger"> *</span></label>
													<input type="text" id="inputNomePJ" name="inputNomePJ" class="form-control" placeholder="Nome Completo" value="<?php echo $row['ProfiNome']; ?>" required autofocus>
												</div>
											</div>	
											
											<div class="col-lg-3" id="CNPJ">
												<div class="form-group">				
													<label for="inputCnpj">CNPJ<span class="text-danger"> *</span></label>
													<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99" value="<?php echo formatarCPF_Cnpj($row['ProfiCnpj']); ?>" <?php if ($row['ProfiTipo'] == 'J') echo "required"; ?>>
												</div>	
											</div>							
										</div>

										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputRazaoSocial">Razão Social</label>
													<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social" value="<?php echo $row['ProfiRazaoSocial']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
													<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal" value="<?php echo $row['ProfiInscricaoMunicipal']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputInscricaoEstadual">Inscrição Estadual</label>
													<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Inscrição Estadual" value="<?php echo $row['ProfiInscricaoEstadual']; ?>">
												</div>
											</div>
                                            <div class="col-lg-2">
												<div class="form-group">
													<label for="inputCnesPJ">CNES</label>
													<input type="text" id="inputCnesPJ" name="inputCnesPJ" class="form-control" placeholder="CNES" value="<?php echo $row['ProfiCNES']; ?>">
												</div>
											</div>	
										</div>	
									</div> <!-- Fim dadosPJ -->
								</div>
							</div>
							
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Endereço</h5>
									<br>
									<div class="row">
										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputCep">CEP</label>
												<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" value="<?php echo (isset($row['ProfiCep'])?$row['ProfiCep']:''); ?>" maxLength="8">
											</div>
										</div>
										
										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputEndereco">Endereço</label>
												<input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço" value="<?php echo isset($row['ProfiEndereco'])?$row['ProfiEndereco']:''; ?>">
											</div>
										</div>

										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputNumero">Nº</label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" value="<?php echo isset($row['ProfiNumero'])?$row['ProfiNumero']:''; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputComplemento">Complemento</label>
												<input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento" value="<?php echo isset($row['ProfiComplemento'])?$row['ProfiComplemento']:''; ?>">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputBairro">Bairro</label>
												<input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro" value="<?php echo isset($row['ProfiBairro'])?$row['ProfiBairro']:''; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputCidade">Cidade</label>
												<input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade" value="<?php echo isset($row['ProfiCidade'])?$row['ProfiCidade']:''; ?>">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbEstado">Estado</label>
												<select id="cmbEstado" name="cmbEstado" class="form-control">
													<option value="#">Selecione um estado</option>
													<option value="AC" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'AC') echo "selected"; ?> >Acre</option>
													<option value="AL" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'AL') echo "selected"; ?> >Alagoas</option>
													<option value="AP" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'AP') echo "selected"; ?> >Amapá</option>
													<option value="AM" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'AM') echo "selected"; ?> >Amazonas</option>
													<option value="BA" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'BA') echo "selected"; ?> >Bahia</option>
													<option value="CE" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'CE') echo "selected"; ?> >Ceará</option>
													<option value="DF" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
													<option value="ES" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
													<option value="GO" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'GO') echo "selected"; ?> >Goiás</option>
													<option value="MA" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'MA') echo "selected"; ?> >Maranhão</option>
													<option value="MT" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
													<option value="MS" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
													<option value="MG" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
													<option value="PA" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'PA') echo "selected"; ?> >Pará</option>
													<option value="PB" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'PB') echo "selected"; ?> >Paraíba</option>
													<option value="PR" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'PR') echo "selected"; ?> >Paraná</option>
													<option value="PE" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'PE') echo "selected"; ?> >Pernambuco</option>
													<option value="PI" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'PI') echo "selected"; ?> >Piauí</option>
													<option value="RJ" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
													<option value="RN" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
													<option value="RS" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
													<option value="RO" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'RO') echo "selected"; ?> >Rondônia</option>
													<option value="RR" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'RR') echo "selected"; ?> >Roraima</option>
													<option value="SC" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
													<option value="SP" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'SP') echo "selected"; ?> >São Paulo</option>
													<option value="SE" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'SE') echo "selected"; ?> >Sergipe</option>
													<option value="TO" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'TO') echo "selected"; ?> >Tocantins</option>
													<option value="ES" <?php if (isset($row['ProfiEstado']) && $row['ProfiEstado'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Contato</h5>
									<br>
									<div class="row">								
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNomeContato">Nome</label>
												<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato" value="<?php echo isset($row['ProfiContato'])?$row['ProfiContato']:''; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefone">Telefone</label>
												<input type="tel" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999" value="<?php echo isset($row['ProfiTelefone'])?$row['ProfiTelefone']:''; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="tel" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999" value="<?php echo isset($row['ProfiCelular'])?$row['ProfiCelular']:''; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputEmail">E-mail</label>
												<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" value="<?php echo isset($row['ProfiEmail'])?$row['ProfiEmail']:''; ?>">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSite">Site</label>
												<input type="url" id="inputSite" name="inputSite" class="form-control" placeholder="URL" value="<?php echo isset($row['ProfiSite'])?$row['ProfiSite']:''; ?>">
											</div>
										</div>										
									</div>
									<br>
							
                                    <div class="row">
                                        <div class="col-lg-12">									
                                            <h5 class="mb-0 font-weight-semibold">Dados Bancários</h5>
                                            <br>
                                            <div class="row">								
                                                <div class="col-lg-4">
                                                    <label for="cmbBanco">Banco</label>
                                                    <select id="cmbBanco" name="cmbBanco" class="form-control select-search">
                                                        <option value="">Selecione</option>
                                                        <?php 
                                                            $sql = "SELECT CnBanId, CnBanNome
                                                                    FROM ContaBanco
                                                                    JOIN Situacao on SituaId =  CnBanStatus
                                                                    WHERE SituaChave = 'ATIVO'
                                                                    ORDER BY CnBanNome ASC";
                                                            $result = $conn->query($sql);
                                                            $rowBanco = $result->fetchAll(PDO::FETCH_ASSOC);
                                                        
                                                            foreach ($rowBanco as $item){
                                                                $seleciona = $item['CnBanId'] == $row['ProfiBanco'] ? "selected" : "";
                                                                print('<option value="'.$item['CnBanId'].'" '. $seleciona .'>'.$item['CnBanNome'].'</option>');
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="inputAgencia">Agência</label>
                                                        <input type="text" id="inputAgencia" name="inputAgencia" class="form-control" placeholder="Agencia" value="<?php echo isset($row['ProfiAgencia'])?$row['ProfiAgencia']:''; ?>">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label for="inputConta">Conta</label>
                                                        <input type="text" id="inputConta" name="inputConta" class="form-control" placeholder="Conta" value="<?php echo isset($row['ProfiConta'])?$row['ProfiConta']:''; ?>">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="inputInformacaoAdicional">Informação Adicional</label>
                                                        <input type="text" id="inputInformacaoAdicional" name="inputInformacaoAdicional" class="form-control" placeholder="Informação Adicional" value="<?php echo isset($row['ProfiInformacaoAdicional'])?$row['ProfiInformacaoAdicional']:''; ?>">
                                                    </div>
                                                </div>										
                                            </div>									
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="txtObservacao">Observação</label>
                                                <textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"><?php echo isset($row['ProfiObservacao'])?$row['ProfiObservacao']:''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>										
								</div>
							</div>
							
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<?php
											if ($_POST['inputPermission']) {
												echo '<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>';
											}
										?>	
										<a href="profissional.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
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
