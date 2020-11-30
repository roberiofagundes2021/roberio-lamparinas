<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Cliente';

include('global_assets/php/conexao.php');

if(isset($_POST['inputTipo'])){
			
	try{
			
		$sql = "INSERT INTO Cliente (ClienTipo, ClienNome, ClienRazaoSocial, ClienCnpj, ClienInscricaoMunicipal, ClienInscricaoEstadual, 
									    ClienCpf, ClienRg, ClienOrgaoEmissor, ClienUf, ClienSexo, ClienDtNascimento, ClienNomePai, ClienNomeMae, ClienCartaoSus, 
										ClienCep, ClienEndereco, ClienNumero, ClienComplemento, ClienBairro, ClienCidade, 
										ClienEstado, ClienContato, ClienTelefone, ClienCelular, ClienEmail, ClienSite, ClienObservacao,
									    ClienStatus, ClienUsuarioAtualizador, ClienUnidade)
				VALUES (:sTipo, :sNome, :sRazaoSocial, :sCnpj, :sInscricaoMunicipal, :sInscricaoEstadual,  
						:sCpf, :sRg, :sOrgaoEmissor, :sUf, :sSexo, :dDtNascimento, :sNomePai, :sNomeMae, :sCartaoSus, :sCep, :sEndereco, :sNumero, :sComplemento, :sBairro, 
						:sCidade, :sEstado, :sContato, :sTelefone, :sCelular, :sEmail, :sSite, :sObservacao, 
						:bStatus, :iUsuarioAtualizador, :iUnidade)";
							   
		$result = $conn->prepare($sql);

		$conn->beginTransaction();
		
		$result->execute(array(
			':sTipo' => $_POST['inputTipo'],
			':sNome' => $_POST['inputTipo'] == 'J' ? $_POST['inputNomePJ'] : $_POST['inputNomePF'],
			':sRazaoSocial' => $_POST['inputTipo'] == 'J' ? $_POST['inputRazaoSocial'] : null,
			':sCnpj' => $_POST['inputTipo'] == 'J' ? limpaCPF_CNPJ($_POST['inputCnpj']) : null,
			':sInscricaoMunicipal' => $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoMunicipal'] : null,
			':sInscricaoEstadual' => $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoEstadual'] : null,
			':sCpf' => $_POST['inputTipo'] == 'F' ? limpaCPF_CNPJ($_POST['inputCpf']) : null,
			':sRg' => $_POST['inputTipo'] == 'F' ? $_POST['inputRg'] : null,
			':sOrgaoEmissor' => $_POST['inputTipo'] == 'F' ? $_POST['inputEmissor'] : null,
			':sUf' => $_POST['inputTipo'] == 'J' || $_POST['cmbUf'] == '#' ? null : $_POST['cmbUf'],
			':sSexo' => $_POST['inputTipo'] == 'J' || $_POST['cmbSexo'] == '#' ? null : $_POST['cmbSexo'],
			':dDtNascimento' => $_POST['inputTipo'] == 'F' ? ($_POST['inputDtNascimento'] == '' ? null : $_POST['inputDtNascimento']) : null,
			':sNomePai' => $_POST['inputTipo'] == 'F' ? $_POST['inputNomePai'] : null,
			':sNomeMae' => $_POST['inputTipo'] == 'F' ? $_POST['inputNomeMae'] : null,
			':sCartaoSus' => $_POST['inputTipo'] == 'F' ? $_POST['inputCartaoSus'] : null,
			':sCep' => $_POST['inputCep'],
			':sEndereco' => $_POST['inputEndereco'],
			':sNumero' => $_POST['inputNumero'],
			':sComplemento' => $_POST['inputComplemento'],
			':sBairro' => $_POST['inputBairro'],
			':sCidade' => $_POST['inputCidade'],
			':sEstado' => $_POST['cmbEstado'],
			':sContato' => $_POST['inputNomeContato'],
			':sTelefone' => $_POST['inputTelefone'] == '(__) ____-____' ? null : $_POST['inputTelefone'],
			':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'],
			':sEmail' => $_POST['inputEmail'],
			':sSite' => $_POST['inputSite'],
			':sObservacao' => $_POST['txtareaObservacao'],
			':bStatus' => 1,
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
			));
		
		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Cliente incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir cliente!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	
	irpara("cliente.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Cliente</title>

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
			
			//$("#cmbEstado").addClass("form-control-select2");
				            
            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#inputEndereco").val("");
                $("#inputBairro").val("");
                $("#inputCidade").val("");
                $("#cmbEstado").val("");                
            }
            
            //Quando o campo cep perde o foco.
            $("#inputCep").blur(function() {

				//$("#cmbEstado").removeClass("form-control-select2");
				
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
								//$("#cmbEstado").find('option[value="MA"]').attr('selected','selected');
								//$('#cmbEstado :selected').text();
								//$("#cmbEstado").find('option:selected').text();
								//document.getElementById("cmbEstado").options[5].selected = true;
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
                        limpa_formulário_cep();
                        alerta("Erro","Formato de CEP inválido.","erro");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }             
                
            });
            

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				e.preventDefault();
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNome = "";				
				var inputNomePF = $('#inputNomePF').val();
				var inputNomePJ = $('#inputNomePJ').val();
				var inputCpf  = $('#inputCpf').val().replace(/[^\d]+/g,'');
				var inputCnpj = $('#inputCnpj').val().replace(/[^\d]+/g,'');

				if (inputTipo == 'F'){ 
					inputNome = inputNomePF; 
				} else{ 
                    inputNome = inputNomePJ;
				}
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();

				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "clienteValida.php",
					data: {tipo: inputTipo, nome: inputNome, cpf: inputCpf, cnpj: inputCnpj},
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						//return false;
						
						$('#formCliente').submit();
					}
				}); //ajax
				
			}); // enviar
			
        }); // document.ready
		
		function selecionaPessoa(tipo) {
			if (tipo == 'PF'){
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
					
					<form name="formCliente" id="formCliente" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Cliente</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="F" class="form-input-styled" data-fouc onclick="selecionaPessoa('PF')" checked>
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="J" class="form-input-styled" data-fouc onclick="selecionaPessoa('PJ')">
												Pessoa Jurídica
											</label>
										</div>										
									</div>									
								</div>
							</div>
							
							<h5 class="mb-0 font-weight-semibold">Dados Pessoais</h5>
							<br>
								
							<div class="row">				
								<div class="col-lg-12">
									<div id="dadosPF">

										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputNomePF">Nome<span class="text-danger"> *</span></label>
													<input type="text" id="inputNomePF" name="inputNomePF" class="form-control" placeholder="Nome Completo" required autofocus>
												</div>
											</div>	
											
											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputCpf">CPF<span class="text-danger"> *</span></label>
													<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" required>
												</div>	
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputCartaoSus">Cartão SUS</label>
													<input type="text" id="inputCartaoSus" name="inputCartaoSus" class="form-control" placeholder="Cartão SUS">
												</div>
											</div>		
										</div>

										<div class="row">
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputRg">RG</label>
													<input type="text" id="inputRg" name="inputRg" class="form-control" placeholder="RG">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputEmissor">Emissor</label>
													<input type="text" id="inputEmissor" name="inputEmissor" class="form-control" placeholder="Órgão Emissor">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="cmbUf">UF</label>
													<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
														<option value="#">Selecione um estado</option>
														<option value="AC">Acre</option>
														<option value="AL">Alagoas</option>
														<option value="AP">Amapá</option>
														<option value="AM">Amazonas</option>
														<option value="BA">Bahia</option>
														<option value="CE">Ceará</option>
														<option value="DF">Distrito Federal</option>
														<option value="ES">Espírito Santo</option>
														<option value="GO">Goiás</option>
														<option value="MA">Maranhão</option>
														<option value="MT">Mato Grosso</option>
														<option value="MS">Mato Grosso do Sul</option>
														<option value="MG">Minas Gerais</option>
														<option value="PA">Pará</option>
														<option value="PB">Paraíba</option>
														<option value="PR">Paraná</option>
														<option value="PE">Pernambuco</option>
														<option value="PI">Piauí</option>
														<option value="RJ">Rio de Janeiro</option>
														<option value="RN">Rio Grande do Norte</option>
														<option value="RS">Rio Grande do Sul</option>
														<option value="RO">Rondônia</option>
														<option value="RR">Roraima</option>
														<option value="SC">Santa Catarina</option>
														<option value="SP">São Paulo</option>
														<option value="SE">Sergipe</option>
														<option value="TO">Tocantins</option>
														<option value="ES">Estrangeiro</option>
													</select>
												</div>
											</div>
											
											<div class="col-lg-2">
												<div class="form-group">
													<label for="cmbSexo">Sexo</label>
													<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
														<option value="#">Selecione o sexo</option>
														<option value="F">Feminino</option>
														<option value="M">Masculino</option>
													</select>
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputDtNascimento">Data Nascimento</label>
													<input type="date" id="inputDtNascimento" name="inputDtNascimento" class="form-control" placeholder="Data Nascimento">
												</div>
											</div>										
										</div>	
	
										<div class="row" >
											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputNomePai">Nome do Pai</label>
													<input type="text" id="inputNomePai" name="inputNomePai" class="form-control" placeholder="Nome do Pai">
												</div>
											</div>

											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputNomeMae">Nome da Mãe</label>
													<input type="text" id="inputNomeMae" name="inputNomeMae" class="form-control" placeholder="Nome da Mãe">
												</div>
											</div>
										</div>
									</div> <!-- Fim dadosPF -->
									
									<div id="dadosPJ" style="display:none">
									
										<div class="row">
											<div class="col-lg-9">
												<div class="form-group">
													<label for="inputNomePJ">Nome<span class="text-danger"> *</span></label>
													<input type="text" id="inputNomePJ" name="inputNomePJ" class="form-control" placeholder="Nome Fantasia">
												</div>
											</div>	
											
											<div class="col-lg-3">
												<div class="form-group">				
													<label for="inputCnpj">CNPJ<span class="text-danger"> *</span></label>
													<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99">
												</div>	
											</div>						
										</div>
									

										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputRazaoSocial">Razão Social</label>
													<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
													<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputInscricaoEstadual">Inscrição Estadual</label>
													<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Inscrição Estadual">
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
												<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" maxLength="8">
											</div>
										</div>
										
										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputEndereco">Endereço</label>
												<input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço">
											</div>
										</div>

										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputNumero">Nº</label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputComplemento">Complemento</label>
												<input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputBairro">Bairro</label>
												<input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputCidade">Cidade</label>
												<input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbEstado">Estado</label>
												<select id="cmbEstado" name="cmbEstado" class="form-control"> <!-- retirei isso da class: form-control-select2 para que funcionasse a seleção do texto do estado, além do valor -->
													<option value="#">Selecione um estado</option>
													<option value="AC">Acre</option>
													<option value="AL">Alagoas</option>
													<option value="AP">Amapá</option>
													<option value="AM">Amazonas</option>
													<option value="BA">Bahia</option>
													<option value="CE">Ceará</option>
													<option value="DF">Distrito Federal</option>
													<option value="ES">Espírito Santo</option>
													<option value="GO">Goiás</option>
													<option value="MA">Maranhão</option>
													<option value="MT">Mato Grosso</option>
													<option value="MS">Mato Grosso do Sul</option>
													<option value="MG">Minas Gerais</option>
													<option value="PA">Pará</option>
													<option value="PB">Paraíba</option>
													<option value="PR">Paraná</option>
													<option value="PE">Pernambuco</option>
													<option value="PI">Piauí</option>
													<option value="RJ">Rio de Janeiro</option>
													<option value="RN">Rio Grande do Norte</option>
													<option value="RS">Rio Grande do Sul</option>
													<option value="RO">Rondônia</option>
													<option value="RR">Roraima</option>
													<option value="SC">Santa Catarina</option>
													<option value="SP">São Paulo</option>
													<option value="SE">Sergipe</option>
													<option value="TO">Tocantins</option>
													<option value="ES">Estrangeiro</option>
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
												<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefone">Telefone</label>
												<input type="tel" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="tel" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputEmail">E-mail</label>
												<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSite">Site</label>
												<input type="url" id="inputSite" name="inputSite" class="form-control" placeholder="URL">
											</div>
										</div>										
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtObservacao">Observação</label>
												<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"></textarea>
											</div>
										</div>
									</div>										
								</div>
							</div>
							<br>

							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="cliente.php" class="btn btn-basic" role="button">Cancelar</a>
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
