<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Cliente';

include('global_assets/php/conexao.php');

//Se veio do Cliente.php
if(isset($_POST['inputClienteId'])){
	
	$iCliente = $_POST['inputClienteId'];
		
	$sql = "SELECT *
			FROM Cliente
			WHERE ClienId = $iCliente ";
	$result = $conn->query("$sql");
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	
						
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("cliente.php");
}

if(isset($_POST['inputTipo'])){	
		
	try{
		
		$sql = "UPDATE Cliente SET ClienTipo = :sTipo, ClienNome = :sNome, ClienRazaoSocial = :sRazaoSocial, ClienCnpj = :sCnpj, 
									  ClienInscricaoMunicipal = :sInscricaoMunicipal, ClienInscricaoEstadual = :sInscricaoEstadual, 
									  ClienCpf = :sCpf, ClienRg = :sRg, ClienOrgaoEmissor = :sOrgaoEmissor, ClienUf = :sUf,
									  ClienSexo = :sSexo, ClienDtNascimento = :dDtNascimento, ClienNomePai = :sNomePai, ClienNomeMae = :sNomeMae,
									  ClienCartaoSus = :sCartaoSus, ClienCep = :sCep, ClienEndereco = :sEndereco, 
									  ClienNumero = :sNumero, ClienComplemento = :sComplemento, ClienBairro = :sBairro, 
									  ClienCidade = :sCidade, ClienEstado = :sEstado, ClienContato = :sContato, ClienTelefone = :sTelefone, 
									  ClienCelular = :sCelular, ClienEmail = :sEmail, ClienSite = :sSite, ClienObservacao = :sObservacao,
									  ClienUsuarioAtualizador = :iUsuarioAtualizador
				WHERE ClienId = :iCliente";
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
						':sCep' => trim($_POST['inputCep']) == "" ? null : $_POST['inputCep'],
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
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iCliente'	=> $_POST['inputClienteId']
						));
			
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Cliente alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar cliente!!!";
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
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNomeNovo  = "";
				var inputNomeNovoPF  = $('#inputNomePF').val();
				var inputNomeNovoPJ  = $('#inputNomePJ').val();
				var inputNomeVelho = $('#inputClienteNome').val();				
				var inputCpf  = $('#inputCpf').val().replace(/[^\d]+/g,'');
				var inputCnpj = $('#inputCnpj').val();

				if (inputTipo == 'F'){ 
					inputNomeNovo = inputNomeNovoPF; 
				} else{ 
                    inputNomeNovo = inputNomeNovoPJ;
				}
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
					
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "clienteValida.php",
					data: {tipo: inputTipo, nomeNovo: inputNomeNovo, nomeVelho: inputNomeVelho, cpf: inputCpf, cnpj: inputCnpj},
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formCliente" ).submit();
					}
				}); //ajax
				
			}); // enviar
            
            
        }); //document.ready
                
        
        function selecionaPessoa(tipo) {

			if (tipo == 'F'){
				document.getElementById('CPF').style.display = "block";
				document.getElementById('CNPJ').style.display = "none";
				document.getElementById('dadosPF').style.display = "block";
				document.getElementById('dadosPJ').style.display = "none";
				document.getElementById('inputNome').placeholder = "Nome Completo";

				document.getElementById('inputNomePF').setAttribute('required', 'required');				
				document.getElementById('inputCpf').setAttribute('required', 'required');
				document.getElementById('inputNomePJ').removeAttribute('required', 'required');
				document.getElementById('inputCnpj').removeAttribute('required', 'required');	
			} else {
				document.getElementById('CPF').style.display = "none";
				document.getElementById('CNPJ').style.display = "block";				
				document.getElementById('dadosPF').style.display = "none";
				document.getElementById('dadosPJ').style.display = "block";
				document.getElementById('inputNome').placeholder = "Nome Fantasia";

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
					
					<form name="formCliente" id="formCliente" method="post" class="form-validate-jquery" action="clienteEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Cliente "<?php echo $row['ClienNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputClienteId" name="inputClienteId" value="<?php echo $row['ClienId']; ?>" >
						<input type="hidden" id="inputClienteNome" name="inputClienteNome" value="<?php echo $row['ClienNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="F" class="form-input-styled" data-fouc onclick="selecionaPessoa('F')"  <?php if ($row['ClienTipo'] == 'F') echo "checked"; ?> >
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="J" class="form-input-styled" data-fouc onclick="selecionaPessoa('J')" <?php if ($row['ClienTipo'] == 'J') echo "checked"; ?>>
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
													<input type="text" id="inputNomePF" name="inputNomePF" class="form-control" placeholder="Nome Completo" value="<?php echo $row['ClienNome']; ?>" required autofocus>
												</div>
											</div>	
											
											<div class="col-lg-3" id="CPF">
												<div class="form-group">
													<label for="inputCpf">CPF<span class="text-danger"> *</span></label>
													<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" value="<?php echo formatarCPF_Cnpj($row['ClienCpf']); ?>">
												</div>	
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputCartaoSus">Cartao SUS</label>
													<input type="text" id="inputCartaoSus" name="inputCartaoSus" class="form-control" placeholder="Cartão SUS" value="<?php echo $row['ClienCartaoSus']; ?>">
												</div>
											</div>	
										</div>	

										<div class="row">
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputRg">RG</label>
													<input type="text" id="inputRg" name="inputRg" class="form-control" placeholder="RG" value="<?php echo $row['ClienRg']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputEmissor">Emissor</label>
													<input type="text" id="inputEmissor" name="inputEmissor" class="form-control" placeholder="Órgão Emissor" value="<?php echo $row['ClienOrgaoEmissor']; ?>">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="cmbUf">UF</label>
													<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
														<option value="#">Selecione um estado</option>
														<option value="AC" <?php if ($row['ClienUf'] == 'AC') echo "selected"; ?> >Acre</option>
														<option value="AL" <?php if ($row['ClienUf'] == 'AL') echo "selected"; ?> >Alagoas</option>
														<option value="AP" <?php if ($row['ClienUf'] == 'AP') echo "selected"; ?> >Amapá</option>
														<option value="AM" <?php if ($row['ClienUf'] == 'AM') echo "selected"; ?> >Amazonas</option>
														<option value="BA" <?php if ($row['ClienUf'] == 'BA') echo "selected"; ?> >Bahia</option>
														<option value="CE" <?php if ($row['ClienUf'] == 'CE') echo "selected"; ?> >Ceará</option>
														<option value="DF" <?php if ($row['ClienUf'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
														<option value="ES" <?php if ($row['ClienUf'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
														<option value="GO" <?php if ($row['ClienUf'] == 'GO') echo "selected"; ?> >Goiás</option>
														<option value="MA" <?php if ($row['ClienUf'] == 'MA') echo "selected"; ?> >Maranhão</option>
														<option value="MT" <?php if ($row['ClienUf'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
														<option value="MS" <?php if ($row['ClienUf'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
														<option value="MG" <?php if ($row['ClienUf'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
														<option value="PA" <?php if ($row['ClienUf'] == 'PA') echo "selected"; ?> >Pará</option>
														<option value="PB" <?php if ($row['ClienUf'] == 'PB') echo "selected"; ?> >Paraíba</option>
														<option value="PR" <?php if ($row['ClienUf'] == 'PR') echo "selected"; ?> >Paraná</option>
														<option value="PE" <?php if ($row['ClienUf'] == 'PE') echo "selected"; ?> >Pernambuco</option>
														<option value="PI" <?php if ($row['ClienUf'] == 'PI') echo "selected"; ?> >Piauí</option>
														<option value="RJ" <?php if ($row['ClienUf'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
														<option value="RN" <?php if ($row['ClienUf'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
														<option value="RS" <?php if ($row['ClienUf'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
														<option value="RO" <?php if ($row['ClienUf'] == 'RO') echo "selected"; ?> >Rondônia</option>
														<option value="RR" <?php if ($row['ClienUf'] == 'RR') echo "selected"; ?> >Roraima</option>
														<option value="SC" <?php if ($row['ClienUf'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
														<option value="SP" <?php if ($row['ClienUf'] == 'SP') echo "selected"; ?> >São Paulo</option>
														<option value="SE" <?php if ($row['ClienUf'] == 'SE') echo "selected"; ?> >Sergipe</option>
														<option value="TO" <?php if ($row['ClienUf'] == 'TO') echo "selected"; ?> >Tocantins</option>
														<option value="ES" <?php if ($row['ClienUf'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
													</select>
												</div>
											</div>
											
											<div class="col-lg-2">
												<div class="form-group">
													<label for="cmbSexo">Sexo</label>
													<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
														<option value="#">Selecione o sexo</option>
														<option value="F" <?php if ($row['ClienSexo'] == 'F') echo "selected"; ?> >Feminino</option>
														<option value="M" <?php if ($row['ClienSexo'] == 'M') echo "selected"; ?> >Masculino</option>
													</select>
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputDtNascimento">Data Nascimento</label>
													<input type="date" id="inputDtNascimento" name="inputDtNascimento" class="form-control" placeholder="Data Nascimento" value="<?php echo $row['ClienDtNascimento']; ?>">
												</div>
											</div>										
										</div>	
										
										<div class="row" >
										    <div class="col-lg-6">
											   <div class="form-group">
												  <label for="inputNomePai">Nome do Pai</label>
												  <input type="text" id="inputNomePai" name="inputNomePai" class="form-control" placeholder="Nome do Pai" value="<?php echo $row['ClienNomePai']; ?>">
											   </div>
										    </div>

										    <div class="col-lg-6">
											    <div class="form-group">
												   <label for="inputNomeMae">Nome da Mãe</label>
												   <input type="text" id="inputNomeMae" name="inputNomeMae" class="form-control" placeholder="Nome da Mãe" value="<?php echo $row['ClienNomeMae']; ?>">
											    </div>
										    </div>
									    </div>	
									</div> <!-- Fim dadosPF -->
									
										<div id="dadosPJ">
												<div class="row">
											<div class="col-lg-9">
												<div class="form-group">
												    <label for="inputNomePJ">Nome<span class="text-danger"> *</span></label>
													<input type="text" id="inputNomePJ" name="inputNomePJ" class="form-control" placeholder="Nome Completo" value="<?php echo $row['ClienNome']; ?>" required autofocus>
												</div>
											</div>	
											
											<div class="col-lg-3"  id="CNPJ">
												<div class="form-group">				
													<label for="inputCnpj">CNPJ<span class="text-danger"> *</span></label>
													<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99" value="<?php echo formatarCPF_Cnpj($row['ClienCnpj']); ?>">
												</div>	
											</div>							
										</div>

										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label for="inputRazaoSocial">Razão Social</label>
													<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social" value="<?php echo $row['ClienRazaoSocial']; ?>">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
													<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal" value="<?php echo $row['ClienInscricaoMunicipal']; ?>">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputInscricaoEstadual">Inscrição Estadual</label>
													<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Inscrição Estadual" value="<?php echo $row['ClienInscricaoEstadual']; ?>">
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
												<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" value="<?php echo $row['ClienCep']; ?>" maxLength="8">
											</div>
										</div>
										
										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputEndereco">Endereço</label>
												<input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço" value="<?php echo $row['ClienEndereco']; ?>">
											</div>
										</div>

										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputNumero">Nº</label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" value="<?php echo $row['ClienNumero']; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputComplemento">Complemento</label>
												<input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento" value="<?php echo $row['ClienComplemento']; ?>">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputBairro">Bairro</label>
												<input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro" value="<?php echo $row['ClienBairro']; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputCidade">Cidade</label>
												<input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade" value="<?php echo $row['ClienCidade']; ?>">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbEstado">Estado</label>
												<select id="cmbEstado" name="cmbEstado" class="form-control">
													<option value="#">Selecione um estado</option>
													<option value="AC" <?php if ($row['ClienEstado'] == 'AC') echo "selected"; ?> >Acre</option>
													<option value="AL" <?php if ($row['ClienEstado'] == 'AL') echo "selected"; ?> >Alagoas</option>
													<option value="AP" <?php if ($row['ClienEstado'] == 'AP') echo "selected"; ?> >Amapá</option>
													<option value="AM" <?php if ($row['ClienEstado'] == 'AM') echo "selected"; ?> >Amazonas</option>
													<option value="BA" <?php if ($row['ClienEstado'] == 'BA') echo "selected"; ?> >Bahia</option>
													<option value="CE" <?php if ($row['ClienEstado'] == 'CE') echo "selected"; ?> >Ceará</option>
													<option value="DF" <?php if ($row['ClienEstado'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
													<option value="ES" <?php if ($row['ClienEstado'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
													<option value="GO" <?php if ($row['ClienEstado'] == 'GO') echo "selected"; ?> >Goiás</option>
													<option value="MA" <?php if ($row['ClienEstado'] == 'MA') echo "selected"; ?> >Maranhão</option>
													<option value="MT" <?php if ($row['ClienEstado'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
													<option value="MS" <?php if ($row['ClienEstado'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
													<option value="MG" <?php if ($row['ClienEstado'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
													<option value="PA" <?php if ($row['ClienEstado'] == 'PA') echo "selected"; ?> >Pará</option>
													<option value="PB" <?php if ($row['ClienEstado'] == 'PB') echo "selected"; ?> >Paraíba</option>
													<option value="PR" <?php if ($row['ClienEstado'] == 'PR') echo "selected"; ?> >Paraná</option>
													<option value="PE" <?php if ($row['ClienEstado'] == 'PE') echo "selected"; ?> >Pernambuco</option>
													<option value="PI" <?php if ($row['ClienEstado'] == 'PI') echo "selected"; ?> >Piauí</option>
													<option value="RJ" <?php if ($row['ClienEstado'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
													<option value="RN" <?php if ($row['ClienEstado'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
													<option value="RS" <?php if ($row['ClienEstado'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
													<option value="RO" <?php if ($row['ClienEstado'] == 'RO') echo "selected"; ?> >Rondônia</option>
													<option value="RR" <?php if ($row['ClienEstado'] == 'RR') echo "selected"; ?> >Roraima</option>
													<option value="SC" <?php if ($row['ClienEstado'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
													<option value="SP" <?php if ($row['ClienEstado'] == 'SP') echo "selected"; ?> >São Paulo</option>
													<option value="SE" <?php if ($row['ClienEstado'] == 'SE') echo "selected"; ?> >Sergipe</option>
													<option value="TO" <?php if ($row['ClienEstado'] == 'TO') echo "selected"; ?> >Tocantins</option>
													<option value="ES" <?php if ($row['ClienEstado'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
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
												<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato" value="<?php echo $row['ClienContato']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefone">Telefone</label>
												<input type="tel" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999" value="<?php echo $row['ClienTelefone']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="tel" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999" value="<?php echo $row['ClienCelular']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputEmail">E-mail</label>
												<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" value="<?php echo $row['ClienEmail']; ?>">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSite">Site</label>
												<input type="url" id="inputSite" name="inputSite" class="form-control" placeholder="URL" value="<?php echo $row['ClienSite']; ?>">
											</div>
										</div>										
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtObservacao">Observação</label>
												<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"><?php echo $row['ClienObservacao']; ?></textarea>
											</div>
										</div>
									</div>										
								</div>
							</div>
							
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
										<a href="cliente.php" class="btn btn-basic" role="button">Cancelar</a>
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
