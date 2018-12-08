<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Fornecedor';

include('global_assets/php/conexao.php');

if(isset($_POST['inputTipo'])){
		
	try{
		
		$sql = "INSERT INTO Fornecedor (ForneTipo, ForneNome, ForneRazaoSocial, ForneCnpj, ForneInscricaoMunicipal, ForneInscricaoEstadual, 
										ForneCategoria, ForneSubCategoria, ForneCpf, ForneRg, ForneOrgaoEmissor, ForneUf, ForneSexo, 
										ForneAniversario, ForneCep, ForneEndereco, ForneNumero, ForneComplemento, ForneBairro, ForneCidade, 
										ForneEstado, ForneContato, ForneTelefone, ForneCelular, ForneEmail, ForneSite, ForneObservacao,
									    ForneBanco, ForneAgencia, ForneConta, ForneInformacaoAdicional, ForneIpi, ForneFrete, ForneIcms, 
									    ForneOutros, ForneStatus, ForneUsuarioAtualizador, ForneEmpresa)
				VALUES (:sTipo, :sNome, :sRazaoSocial, :sCnpj, :sInscricaoMunicipal, :sInscricaoEstadual, :iCategoria, :iSubCategoria, 
						:sCpf, :sRg, :sOrgaoEmissor, :sUf, :sSexo, :dAniversario, :sCep, :sEndereco, :sNumero, :sComplemento, :sBairro, 
						:sCidade, :sEstado, :sContato, :sTelefone, :sCelular, :sEmail, :sSite, :sObservacao, :iBanco, :sAgencia, 
						:sConta, :sInformacaoAdicional, :iIpi, :iFrete, :iIcms, :iOutros, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'] == "on" ? "F" : "J",
						':sNome' => $_POST['inputNome'],
						':sRazaoSocial' => $_POST['inputRazaoSocial'],
						':sCnpj' => limpaCPF_CNPJ($_POST['inputCnpj']),
						':sInscricaoMunicipal' => $_POST['inputInscricaoMunicipal'],
						':sInscricaoEstadual' => $_POST['inputInscricaoEstadual'],
						':iCategoria' => $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'],
						':sCpf' => limpaCPF_CNPJ($_POST['inputCpf']),
						':sRg' => $_POST['inputRg'],
						':sOrgaoEmissor' => $_POST['inputEmissor'],
						':sUf' => $_POST['cmbUf'],
						':sSexo' => $_POST['cmbSexo'],
						':dAniversario' => $_POST['inputAniversario'],
						':sCep' => $_POST['inputCep'],
						':sEndereco' => $_POST['inputEndereco'],
						':sNumero' => $_POST['inputNumero'],
						':sComplemento' => $_POST['inputComplemento'],
						':sBairro' => $_POST['inputBairro'],
						':sCidade' => $_POST['inputCidade'],
						':sEstado' => $_POST['cmbEstado'],
						':sContato' => $_POST['inputNomeContato'],
						':sTelefone' => $_POST['inputTelefone'],
						':sCelular' => $_POST['inputCelular'],
						':sEmail' => $_POST['inputEmail'],
						':sSite' => $_POST['inputSite'],
						':sObservacao' => $_POST['txtareaObservacao'],
						':iBanco' => $_POST['cmbBanco'],
						':sAgencia' => $_POST['inputAgencia'],
						':sConta' => $_POST['inputConta'],
						':sInformacaoAdicional' => $_POST['inputInformacaoAdicional'],
						':iIpi' => $_POST['inputIpi'],
						':iFrete' => $_POST['inputFrete'],
						':iIcms' => $_POST['inputIcms'],
						':iOutros' => $_POST['inputOutros'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fornecedor incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir fornecedor!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	
	irpara("fornecedor.php");
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>	
	
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_pnotify.js"></script>
	
	<script src="global_assets/js/lamparinas/custom.js"></script>	
	<!-- /theme JS files -->	

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

				$("#cmbEstado").removeClass("form-control-select2");
				
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
            
        });
        
        function selecionaPessoa(tipo) {
			if (tipo == 'PF'){
				document.getElementById('CPF').style.display = "block";
				document.getElementById('CNPJ').style.display = "none";
				document.getElementById('dadosPF').style.display = "block";
				document.getElementById('dadosPJ').style.display = "none";
			} else {
				document.getElementById('CPF').style.display = "none";
				document.getElementById('CNPJ').style.display = "block";				
				document.getElementById('dadosPF').style.display = "none";
				document.getElementById('dadosPJ').style.display = "block";
			}
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
					
					<form name="formFornecedor" method="post" class="form-validate" action="fornecedorNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Fornecedor</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" checked data-fouc onclick="selecionaPessoa('PF')">
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" data-fouc onclick="selecionaPessoa('PJ')">
												Pessoa Jurídica
											</label>
										</div>										
									</div>									
								</div>
							</div>
							
							<h5 class="mb-0 font-weight-semibold">Dados Pessoais</h5>
							<br>
							<div class="row">
								<div class="col-lg-9">
									<div class="form-group">
										<label for="inputNome">Nome</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome Completo" required>
									</div>
								</div>	
								
								<div class="col-lg-3" id="CPF">
									<div class="form-group">
										<label for="inputCpf">CPF</label>
										<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99">
									</div>	
								</div>
								
								<div class="col-lg-3" id="CNPJ" style="display:none;">
									<div class="form-group">				
										<label for="inputCnpj">CNPJ</label>
										<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99">
									</div>	
								</div>							
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div id="dadosPF">
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
													<label for="inputAniversario">Aniversário</label>
													<input type="date" id="inputAniversario" name="inputAniversario" class="form-control" placeholder="Aniversário">
												</div>
											</div>										
										</div>	
									</div> <!-- Fim dadosPF -->
									
									<div id="dadosPJ" style="display:none">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputRazaoSocial">Razão Social</label>
													<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social">
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
													<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal">
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputInscricaoEstadual">Inscrição Estadual</label>
													<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Inscrição Estadual">
												</div>
											</div>	
										</div>	
									</div> <!-- Fim dadosPJ -->
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbCategoria">Categoria</label>
										<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
											<option value="#">Selecione uma categoria</option>
											<?php 
												$sql = ("SELECT CategId, CategNome
														 FROM Categoria															     
														 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY CategNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){															
													print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbSubCategoria">SubCategoria</label>
										<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
											<option value="#">Selecione uma subcategoria</option>
											<?php 
												$sql = ("SELECT SbCatId, SbCatNome
														 FROM SubCategoria															     
														 WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY SbCatNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													print('<option value="'.$item['SbCatId'].'">'.$item['SbCatNome'].'</option>');
												}
											
											?>
										</select>
									</div>
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
												<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP">
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
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados Bancários</h5>
									<br>
									<div class="row">
										<div class="col-lg-5">
											<label for="cmbBanco">Banco</label>
											<select id="cmbBanco" name="cmbBanco" class="form-control form-control-select2">
												<option value="#">Selecione um banco</option>
												<?php 
													$sql = ("SELECT BancoId, BancoCodigo, BancoNome
															 FROM Banco
															 WHERE BancoStatus = 1
															 ORDER BY BancoCodigo ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($row as $item){
														print('<option value="'.$item['BancoId'].'">'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
													}
												
												?>
											</select>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAgencia">Agência</label>
												<input type="text" id="inputAgencia" name="inputAgencia" class="form-control" placeholder="Agência + dígito">												
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputConta">Conta</label>
												<input type="text" id="inputConta" name="inputConta" class="form-control" placeholder="Conta + dígito">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputInfoAdicional">Informação Adicional</label>
												<input type="text" id="inputInfoAdicional" name="inputInfoAdicional" class="form-control">
											</div>
										</div>
									</div>
								</div>
							</div>
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Tributos</h5>
									<br>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbBanco">IPI (%)</label>
												<input type="text" id="inputIpi" name="inputIpi" class="form-control" data-mask="99%" placeholder="IPI (%)">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFrete">Frete (%)</label>
												<input type="text" id="inputFrete" name="inputFrete" class="form-control" data-mask="99%" placeholder="Frete (%)">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputIcms">ICMS (%)</label>
												<input type="text" id="inputIcms" name="inputIcms" class="form-control" data-mask="99%" placeholder="ICMS (%)">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputOutros">Outros (%)</label>
												<input type="text" id="inputOutros" name="inputOutros" class="form-control" data-mask="99%" placeholder="Outros (%)">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
										<a href="fornecedor.php" class="btn btn-basic" role="button">Cancelar</a>
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
