<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Fornecedor';

include('global_assets/php/conexao.php');

if(isset($_POST['inputFornecedorId'])){
	
	$iFornecedor = $_POST['inputFornecedorId'];
	
	try{
		
		$sql = "SELECT *
				FROM Fornecedor
				WHERE ForneId = $iFornecedor ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
}

if(isset($_POST['inputTipo'])){	
		
	try{
		
		$sql = "UPDATE Fornecedor SET ForneTipo = :sTipo, ForneNome = :sNome, ForneRazaoSocial = :sRazaoSocial, ForneCnpj = :sCnpj, 
									  ForneInscricaoMunicipal = :sInscricaoMunicipal, ForneInscricaoEstadual = :sInscricaoEstadual, 
									  ForneCategoria = :iCategoria, ForneSubCategoria = :iSubCategoria, ForneCpf = :sCpf, 
									  ForneRg = :sRg, ForneOrgaoEmissor = :sOrgaoEmissor, ForneUf = :sUf, ForneSexo = :sSexo, 
									  ForneAniversario = :dAniversario, ForneCep = :sCep, ForneEndereco = :sEndereco, 
									  ForneNumero = :sNumero, ForneComplemento = :sComplemento, ForneBairro = :sBairro, 
									  ForneCidade = :sCidade, ForneEstado = :sEstado, ForneContato = :sContato, ForneTelefone = :sTelefone, 
									  ForneCelular = :sCelular, ForneEmail = :sEmail, ForneSite = :sSite, ForneObservacao = :sObservacao,
									  ForneBanco = :iBanco, ForneAgencia = :sAgencia, ForneConta = :sConta, 
									  ForneInformacaoAdicional = :sInformacaoAdicional, ForneIpi = :iIpi, ForneFrete = :iFrete, 
									  ForneIcms = :iIcms, ForneOutros = :iOutros, ForneUsuarioAtualizador = :iUsuarioAtualizador
				WHERE ForneId = :iFornecedor";
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
						':sInformacaoAdicional' => $_POST['inputInfoAdicional'],
						':iIpi' => $_POST['inputIpi'],
						':iFrete' => $_POST['inputFrete'],
						':iIcms' => $_POST['inputIcms'],
						':iOutros' => $_POST['inputOutros'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iFornecedor'	=> $_POST['inputFornecedorId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fornecedor alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar fornecedor!!!";
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
								alert("Entrou aqui");
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
					
					<form name="formFornecedor" method="post" class="form-validate" action="fornecedorEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Fornecedor "<?php echo $row['ForneNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputFornecedorId" name="inputFornecedorId" value="<?php echo $row['ForneId']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" data-fouc onclick="selecionaPessoa('PF')"  <?php if ($row['ForneTipo'] == 'F') echo "checked"; ?> >
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" data-fouc onclick="selecionaPessoa('PJ')" <?php if ($row['ForneTipo'] == 'J') echo "checked"; ?>>
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
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome Completo" value="<?php echo $row['ForneNome']; ?>" required>
									</div>
								</div>	
								
								<div class="col-lg-3" id="CPF">
									<div class="form-group">
										<label for="inputCpf">CPF</label>
										<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" value="<?php echo formatarCPF_Cnpj($row['ForneCpf']); ?>">
									</div>	
								</div>
								
								<div class="col-lg-3" id="CNPJ" style="display:none;">
									<div class="form-group">				
										<label for="inputCnpj">CNPJ</label>
										<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99" value="<?php echo formatarCPF_Cnpj($row['ForneCnpj']); ?>">
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
													<input type="text" id="inputRg" name="inputRg" class="form-control" placeholder="RG" value="<?php echo $row['ForneRg']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputEmissor">Emissor</label>
													<input type="text" id="inputEmissor" name="inputEmissor" class="form-control" placeholder="Órgão Emissor" value="<?php echo $row['ForneOrgaoEmissor']; ?>">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="cmbUf">UF</label>
													<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
														<option value="#">Selecione um estado</option>
														<option value="AC" <?php if ($row['ForneUf'] == 'AC') echo "selected"; ?> >Acre</option>
														<option value="AL" <?php if ($row['ForneUf'] == 'AL') echo "selected"; ?> >Alagoas</option>
														<option value="AP" <?php if ($row['ForneUf'] == 'AP') echo "selected"; ?> >Amapá</option>
														<option value="AM" <?php if ($row['ForneUf'] == 'AM') echo "selected"; ?> >Amazonas</option>
														<option value="BA" <?php if ($row['ForneUf'] == 'BA') echo "selected"; ?> >Bahia</option>
														<option value="CE" <?php if ($row['ForneUf'] == 'CE') echo "selected"; ?> >Ceará</option>
														<option value="DF" <?php if ($row['ForneUf'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
														<option value="ES" <?php if ($row['ForneUf'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
														<option value="GO" <?php if ($row['ForneUf'] == 'GO') echo "selected"; ?> >Goiás</option>
														<option value="MA" <?php if ($row['ForneUf'] == 'MA') echo "selected"; ?> >Maranhão</option>
														<option value="MT" <?php if ($row['ForneUf'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
														<option value="MS" <?php if ($row['ForneUf'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
														<option value="MG" <?php if ($row['ForneUf'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
														<option value="PA" <?php if ($row['ForneUf'] == 'PA') echo "selected"; ?> >Pará</option>
														<option value="PB" <?php if ($row['ForneUf'] == 'PB') echo "selected"; ?> >Paraíba</option>
														<option value="PR" <?php if ($row['ForneUf'] == 'PR') echo "selected"; ?> >Paraná</option>
														<option value="PE" <?php if ($row['ForneUf'] == 'PE') echo "selected"; ?> >Pernambuco</option>
														<option value="PI" <?php if ($row['ForneUf'] == 'PI') echo "selected"; ?> >Piauí</option>
														<option value="RJ" <?php if ($row['ForneUf'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
														<option value="RN" <?php if ($row['ForneUf'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
														<option value="RS" <?php if ($row['ForneUf'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
														<option value="RO" <?php if ($row['ForneUf'] == 'RO') echo "selected"; ?> >Rondônia</option>
														<option value="RR" <?php if ($row['ForneUf'] == 'RR') echo "selected"; ?> >Roraima</option>
														<option value="SC" <?php if ($row['ForneUf'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
														<option value="SP" <?php if ($row['ForneUf'] == 'SP') echo "selected"; ?> >São Paulo</option>
														<option value="SE" <?php if ($row['ForneUf'] == 'SE') echo "selected"; ?> >Sergipe</option>
														<option value="TO" <?php if ($row['ForneUf'] == 'TO') echo "selected"; ?> >Tocantins</option>
														<option value="ES" <?php if ($row['ForneUf'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
													</select>
												</div>
											</div>
											
											<div class="col-lg-2">
												<div class="form-group">
													<label for="cmbSexo">Sexo</label>
													<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
														<option value="#">Selecione o sexo</option>
														<option value="F" <?php if ($row['ForneSexo'] == 'F') echo "selected"; ?> >Feminino</option>
														<option value="M" <?php if ($row['ForneSexo'] == 'M') echo "selected"; ?> >Masculino</option>
													</select>
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputAniversario">Aniversário</label>
													<input type="date" id="inputAniversario" name="inputAniversario" class="form-control" placeholder="Aniversário" value="<?php echo $row['ForneAniversario']; ?>">
												</div>
											</div>										
										</div>	
									</div> <!-- Fim dadosPF -->
									
									<div id="dadosPJ" style="display:none">
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputRazaoSocial">Razão Social</label>
													<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social" value="<?php echo $row['ForneRazaoSocial']; ?>">
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
													<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal" value="<?php echo $row['ForneInscricaoMunicipal']; ?>">
												</div>
											</div>

											<div class="col-lg-4">
												<div class="form-group">
													<label for="inputInscricaoEstadual">Inscrição Estadual</label>
													<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Inscrição Estadual" value="<?php echo $row['ForneInscricaoEstadual']; ?>">
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
												$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowCategoria as $item){			
													$seleciona = $item['CategId'] == $row['ForneCategoria'] ? "selected" : "";
													print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
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
												$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowSubCategoria as $item){
													$seleciona = $item['SbCatId'] == $row['ForneSubCategoria'] ? "selected" : "";
													print('<option value="'.$item['SbCatId'].'" '. $seleciona .'>'.$item['SbCatNome'].'</option>');
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
												<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" value="<?php echo $row['ForneCep']; ?>">
											</div>
										</div>
										
										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputEndereco">Endereço</label>
												<input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço" value="<?php echo $row['ForneEndereco']; ?>">
											</div>
										</div>

										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputNumero">Nº</label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" value="<?php echo $row['ForneNumero']; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputComplemento">Complemento</label>
												<input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento" value="<?php echo $row['ForneComplemento']; ?>">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputBairro">Bairro</label>
												<input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro" value="<?php echo $row['ForneBairro']; ?>">
											</div>
										</div>

										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputCidade">Cidade</label>
												<input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade" value="<?php echo $row['ForneCidade']; ?>">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbEstado">Estado</label>
												<select id="cmbEstado" name="cmbEstado" class="form-control form-control-select2">
													<option value="#">Selecione um estado</option>
													<option value="AC" <?php if ($row['ForneEstado'] == 'AC') echo "selected"; ?> >Acre</option>
													<option value="AL" <?php if ($row['ForneEstado'] == 'AL') echo "selected"; ?> >Alagoas</option>
													<option value="AP" <?php if ($row['ForneEstado'] == 'AP') echo "selected"; ?> >Amapá</option>
													<option value="AM" <?php if ($row['ForneEstado'] == 'AM') echo "selected"; ?> >Amazonas</option>
													<option value="BA" <?php if ($row['ForneEstado'] == 'BA') echo "selected"; ?> >Bahia</option>
													<option value="CE" <?php if ($row['ForneEstado'] == 'CE') echo "selected"; ?> >Ceará</option>
													<option value="DF" <?php if ($row['ForneEstado'] == 'DF') echo "selected"; ?> >Distrito Federal</option>
													<option value="ES" <?php if ($row['ForneEstado'] == 'ES') echo "selected"; ?> >Espírito Santo</option>
													<option value="GO" <?php if ($row['ForneEstado'] == 'GO') echo "selected"; ?> >Goiás</option>
													<option value="MA" <?php if ($row['ForneEstado'] == 'MA') echo "selected"; ?> >Maranhão</option>
													<option value="MT" <?php if ($row['ForneEstado'] == 'MT') echo "selected"; ?> >Mato Grosso</option>
													<option value="MS" <?php if ($row['ForneEstado'] == 'MS') echo "selected"; ?> >Mato Grosso do Sul</option>
													<option value="MG" <?php if ($row['ForneEstado'] == 'MG') echo "selected"; ?> >Minas Gerais</option>
													<option value="PA" <?php if ($row['ForneEstado'] == 'PA') echo "selected"; ?> >Pará</option>
													<option value="PB" <?php if ($row['ForneEstado'] == 'PB') echo "selected"; ?> >Paraíba</option>
													<option value="PR" <?php if ($row['ForneEstado'] == 'PR') echo "selected"; ?> >Paraná</option>
													<option value="PE" <?php if ($row['ForneEstado'] == 'PE') echo "selected"; ?> >Pernambuco</option>
													<option value="PI" <?php if ($row['ForneEstado'] == 'PI') echo "selected"; ?> >Piauí</option>
													<option value="RJ" <?php if ($row['ForneEstado'] == 'RJ') echo "selected"; ?> >Rio de Janeiro</option>
													<option value="RN" <?php if ($row['ForneEstado'] == 'RN') echo "selected"; ?> >Rio Grande do Norte</option>
													<option value="RS" <?php if ($row['ForneEstado'] == 'RS') echo "selected"; ?> >Rio Grande do Sul</option>
													<option value="RO" <?php if ($row['ForneEstado'] == 'RO') echo "selected"; ?> >Rondônia</option>
													<option value="RR" <?php if ($row['ForneEstado'] == 'RR') echo "selected"; ?> >Roraima</option>
													<option value="SC" <?php if ($row['ForneEstado'] == 'SC') echo "selected"; ?> >Santa Catarina</option>
													<option value="SP" <?php if ($row['ForneEstado'] == 'SP') echo "selected"; ?> >São Paulo</option>
													<option value="SE" <?php if ($row['ForneEstado'] == 'SE') echo "selected"; ?> >Sergipe</option>
													<option value="TO" <?php if ($row['ForneEstado'] == 'TO') echo "selected"; ?> >Tocantins</option>
													<option value="ES" <?php if ($row['ForneEstado'] == 'ES') echo "selected"; ?> >Estrangeiro</option>
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
												<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato" value="<?php echo $row['ForneContato']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefone">Telefone</label>
												<input type="tel" id="inputTelefone" name="inputTelefone" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999" value="<?php echo $row['ForneTelefone']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="tel" id="inputCelular" name="inputCelular" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999" value="<?php echo $row['ForneCelular']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputEmail">E-mail</label>
												<input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="E-mail" value="<?php echo $row['ForneEmail']; ?>">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSite">Site</label>
												<input type="url" id="inputSite" name="inputSite" class="form-control" placeholder="URL" value="<?php echo $row['ForneSite']; ?>">
											</div>
										</div>										
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtObservacao">Observação</label>
												<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"><?php echo $row['ForneObservacao']; ?></textarea>
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
													$rowBanco = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowBanco as $item){
														$seleciona = $item['BancoId'] == $row['ForneBanco'] ? "selected" : "";
														print('<option value="'.$item['BancoId'].'" '. $seleciona .'>'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
													}
												
												?>
											</select>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAgencia">Agência</label>
												<input type="text" id="inputAgencia" name="inputAgencia" class="form-control" placeholder="Agência + dígito" value="<?php echo $row['ForneAgencia']; ?>">												
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputConta">Conta</label>
												<input type="text" id="inputConta" name="inputConta" class="form-control" placeholder="Conta + dígito" value="<?php echo $row['ForneConta']; ?>">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputInfoAdicional">Informação Adicional</label>
												<input type="text" id="inputInfoAdicional" name="inputInfoAdicional" class="form-control" value="<?php echo $row['ForneInformacaoAdicional']; ?>">
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
												<input type="number" id="inputIpi" name="inputIpi" class="form-control" placeholder="IPI (%)" value="<?php echo $row['ForneIpi']; ?>">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFrete">Frete (%)</label>
												<input type="number" id="inputFrete" name="inputFrete" class="form-control" placeholder="Frete (%)" value="<?php echo $row['ForneFrete']; ?>">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputIcms">ICMS (%)</label>
												<input type="text" id="inputIcms" name="inputIcms" class="form-control" placeholder="ICMS (%)" value="<?php echo $row['ForneIcms']; ?>">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputOutros">Outros (%)</label>
												<input type="text" id="inputOutros" name="inputOutros" class="form-control" placeholder="Outros (%)" value="<?php echo $row['ForneOutros']; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
										<a href="fornecedor.php" class="btn btn-basic" role="button">Cancelar</a>
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
