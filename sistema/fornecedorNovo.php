<?php
include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Novo Fornecedor';
include('global_assets/php/conexao.php');
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>

	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<!-- /theme JS files -->

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		//Ao clicar no botão Adicionar Foto aciona o click do file que está hidden
		function adicionaFoto() {
			$('#imagem').trigger("click");
		};

		$(document).ready(function() {

			$('#imagem').on('change', function() {
				$('#visualizar').html('<img src="global_assets/images/lamparinas/ajax-loader.gif" alt="Enviando..."/>');

				var form = $('#formFoto')[0];
				var formData = new FormData(form);
				formData.append('file', $('#imagem')[0].files[0]);
				formData.append('tela', 'fornecedor');

				$.ajax({
					type: "POST",
					enctype: 'multipart/form-data',
					url: "upload.php",
					processData: false, // impedir que o jQuery tranforma a "data" em querystring
					contentType: false, // desabilitar o cabeçalho "Content-Type"
					cache: false, // desabilitar o "cache"
					data: formData, //{imagem: inputImagem},
					success: function(resposta) {
						$('#visualizar').html(resposta);
						$('#addFoto').text("Alterar Foto...");
						//Aqui sou obrigado a instanciar novamente a utilização do fancybox
						$(".fancybox").fancybox({
							// options
						});
						return false;
					}
				}); //ajax

				//$('#formFoto').submit();

				// Efetua o Upload sem dar refresh na pagina
				$('#formFoto').ajaxForm({
					target: '#visualizar' // o callback será no elemento com o id #visualizar
				}).submit();
			});

			selecionaPessoa('PF');
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
					if (validacep.test(cep)) {

						//Preenche os campos com "..." enquanto consulta webservice.
						$("#inputEndereco").val("...");
						$("#inputBairro").val("...");
						$("#inputCidade").val("...");
						$("#cmbEstado").val("...");

						//Consulta o webservice viacep.com.br/
						$.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

							if (!("erro" in dados)) {

								//Atualiza os campos com os valores da consulta.
								$("#inputEndereco").val(dados.logradouro);
								$("#inputBairro").val(dados.bairro);
								$("#inputCidade").val(dados.localidade);
								$("#cmbEstado").val(dados.uf);
							} //end if.
							else {
								//CEP pesquisado não foi encontrado.
								limpa_formulário_cep();
								alerta("Erro", "CEP não encontrado.", "erro");
							}
						});
					} //end if.
					else {
						//cep é inválido.
						limpa_formulário_cep();
						alerta("Erro", "Formato de CEP inválido.", "erro");
					}
				} //end if.
				else {
					//cep sem valor, limpa formulário.
					limpa_formulário_cep();
				}
			});

			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e) {

				Filtrando();

				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function(dados) {

					var option = '<option>Selecione </option>';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						Reset();
					}
				});
			});

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				// subistitui qualquer espaço em branco no campo "CEP" antes de enviar para o banco
				var cep = $("#inputCep").val()
				cep = cep.replace(' ', '')
				$("#inputCep").val(cep)

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNome = $('#inputNome').val();
				var inputCpf = $('#inputCpf').val().replace(/[^\d]+/g, '');
				var inputCnpj = $('#inputCnpj').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();

				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();

				if (cmbSubCategoria[0] == 'Filtrando') {
					alerta('Atenção', 'Por algum problema na sua conexão o campo SubCategoria parece não conseguindo ser filtrado! Favor cancelar a edição e tentar novamente.', 'error');
					return false;
				}

				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "fornecedorValida.php",
					data: {
						tipo: inputTipo,
						nome: inputNome,
						cpf: inputCpf,
						cnpj: inputCnpj
					},
					success: function(resposta) {

						if (resposta == 1) {
							alerta('Atenção', 'Esse registro já existe!', 'error');
							return false;
						}

						$("#formFornecedor").submit();
					}
				}); //ajax

			}); // enviar

			function Filtrando() {
				$('#cmbSubCategoria').empty().append('<option value="Filtrando">Filtrando...</option>');
			}

			function Reset() {
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}

		}); // document.ready

		function selecionaPessoa(tipo) {
			var camposPFVisiveis = [
				'inputCpf',
				'inputRg',
				'inputNit',
				'inputEmissor',
				'cmbUf',
				'inputNaturalidade',
				'inputNaturalidadeUf',
				'inputNacionalidade',
				'inputAno',
				'inputCarteiraTrabalho',
				'inputNumSerie',
				'cmbSexo',
				'inputAniversario'
			]
			var camposPJVisiveis = [
				'inputCnpj',
				'inputNire',
				'inputInscricaoMunicipal',
				'inputInscricaoEstadual',
				'inputRazaoSocial'
			]
			if (tipo == 'PF') {
				camposPFVisiveis.forEach(element => $("#" + element).parent().parent().css("display", "block"));
				camposPJVisiveis.forEach(element => $("#" + element).parent().parent().css("display", "none"));
				document.getElementById('inputNome').placeholder = "Nome Completo";
				document.getElementById('foto').style.display = 'flex';

			} else {
				camposPJVisiveis.forEach(element => $("#" + element).parent().parent().css("display", "block"));
				camposPFVisiveis.forEach(element => $("#" + element).parent().parent().css("display", "none"));
				document.getElementById('inputNome').placeholder = "Nome Fantasia";
				document.getElementById('foto').style.display = 'none';
			}

			marcaCamposObrigatorios(tipo);
		}

		function marcaCamposObrigatorios(tipo) {
			var camposPFObrigatorios = [
				'inputCpf',

			]
			var camposPJObrigatorios = [
				'inputCnpj',

			]
			if (tipo == 'PF') {
				camposPFObrigatorios.forEach(element => $("#" + element).attr('required', true));
				camposPJObrigatorios.forEach(element => $("#" + element).attr('required', false));

			} else {
				camposPJObrigatorios.forEach(element => $("#" + element).attr('required', true));
				camposPFObrigatorios.forEach(element => $("#" + element).attr('required', false));
			}
		}


		function validaEFormataCnpj() {
			let cnpj = $('#inputCnpj').val();
			let resultado = validarCNPJ(cnpj);
			if (!resultado) {
				let labelErro = $('#inputCnpj-error')
				labelErro.removeClass('validation-valid-label');
				labelErro[0].innerHTML = "CNPJ Inválido";
				$('#inputCnpj').val("");
			}

		}

		function validaEFormataCpf() {
			let cpf = $('#inputCpf').val().replace(/[^\d]+/g, '');
			let resultado = validaCPF(cpf);
			if (!resultado) {
				let labelErro = $('#inputCpf-error')
				labelErro.removeClass('validation-valid-label');
				labelErro[0].innerHTML = "CPF Inválido";
				$('#inputCpf').val("");
			}
		}

		function validaDataNascimento(dataASerValidada) {
			//Adicionado um espaço para forçar o fuso horário de brasília		
			let dataObj = new Date(dataASerValidada + " ");
			let hoje = new Date();
			if ((hoje - dataObj) < 0) {
				return false;
			} else {
				return true;
			}
		}

		function formataCampoDataNascimento() {
			let dataPreenchida = $('#inputAniversario').val();
			if (!validaDataNascimento(dataPreenchida)) {
				let labelErro = $('#inputAniversario-error');
				labelErro.removeClass('validation-valid-label');
				labelErro[0].innerHTML = "Data não pode ser futura";
				$('#inputAniversario').val("");
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
				
					<form name="formFornecedor" action="fornecedorNovoFinalizaTransacao.php" id="formFornecedor" method="post" class="form-validate-jquery">
					<div class="card">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Fornecedor</h5>
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
								<div class="col-lg-8">
									
								</div>	
							</div>

							<div class="row">
								<div class="col-lg-6" style='margin-top: 60px'>
									 <h5 class="mb-0 font-weight-semibold">Dados Pessoais</h5> 
								</div>
								<div class="col-lg-3" id="CPF">
									<div class="form-group">
										<label for="inputCpf">CPF<span class="text-danger">*</span></label>
										<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" onblur="validaEFormataCpf()">
									</div>
								</div>

								<div class="col-lg-3" id="CNPJ">
									<div class="form-group">
										<label for="inputCnpj">CNPJ<span class="text-danger">*</span></label>
										<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99" onblur="validaEFormataCnpj()">
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNire">NIRE</label>
										<input type="text" id="inputNire" name="inputNire" class="form-control" placeholder="NIRE">
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputNit">NIT</label>
										<input type="text" id="inputNit" name="inputNit" class="form-control" placeholder="NIT">
									</div>
								</div>
							</div>							

							<!-- Pessoa Física -->
							<div class="row">
								<div class="col-lg-10">
									<div class="row">
										<div class="col-lg-8">
											<div class="form-group">
												<label for="inputRazaoSocial">Razão Social</label>
												<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome<span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome Fantasia" required autofocus>
											</div>
										</div>
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

										<div class="col-lg-2">
											<div class="form-group">
												<label for="cmbUf">UF</label>
												<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
													<option value="#">Selecione</option>
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

									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCarteiraTrabalho">Carteira de Trabalho</label>
												<input type="text" id="inputCarteiraTrabalho" name="inputCarteiraTrabalho" class="form-control" placeholder="Carteira de Trabalho">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Numero de Série</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" placeholder="Numero de Série">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAniversario">Data Nascimento</label>
												<input type="date" id="inputAniversario" name="inputAniversario" class="form-control" placeholder="Aniversário" onblur="formataCampoDataNascimento()">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="cmbSexo">Sexo</label>
												<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<option value="F">Feminino</option>
													<option value="M">Masculino</option>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNaturalidade">Naturalidade</label>
												<input type="text" id="inputNaturalidade" name="inputNaturalidade" class="form-control" placeholder="Naturalidade">
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNaturalidadeUf">UF da Naturalidade</label>
												<select id="inputNaturalidadeUf" name="inputNaturalidadeUf" class="form-control form-control-select2">
													<option value="#">Selecione</option>
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
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNacionalidade">Nacionalidade</label>
												<input type="text" id="inputNacionalidade" name="inputNacionalidade" class="form-control" placeholder="Nacionalidade">
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAno">Ano &nbsp<i style="color:#375b82;" class="icon-question4" data-popup="tooltip" data-original-title="Entrada no Brasil (se estrangeiro)" data-placement="right"></i></label>
												<input type="text" id="inputAno" name="inputAno" class="form-control" placeholder="Ano">
											</div>
										</div>
									</div>

									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
												<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputInscricaoEstadual">Inscrição Estadual</label>
												<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Ins. Estadual">
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria<span class="text-danger"> *</span></label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione </option>
													<?php
													$sql = "SELECT CategId, CategNome
															FROM Categoria
															JOIN Situacao on SituaId = CategStatus
															WHERE CategEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
															ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCategoria as $item) {
														print('<option value="' . $item['CategId'] . '">' . $item['CategNome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group" style="border-bottom:1px solid #ddd;">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control select" multiple="multiple" data-fouc>
													<option value="#">Selecione </option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-2">
									<div id="foto">
										<div id="visualizar">
											<img class="ml-3" src="global_assets/images/lamparinas/sem_foto.gif" width="195px" alt="Fornecedores" style="border:2px solid #ccc;">
										</div>
										<br>
										<button id="addFoto" type="button" onclick="adicionaFoto()" class="ml-3 btn btn-lg btn-principal" style="width:90%">Adicionar Foto...</button>
									</div>
								</div>									
							</div>




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
												<select id="cmbEstado" name="cmbEstado" class="form-control">
													<option value="#">Selecione </option>
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
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNomeContato">Nome</label>
												<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefoneResidencial">Telefone Residencial</label>
												<input type="tel" id="inputTelefoneResidencial" name="inputTelefoneResidencial" class="form-control" placeholder="Telefone Residencial" data-mask="(99) 9999-9999">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefoneComercial">Telefone Comercial</label>
												<input type="tel" id="inputTelefoneComercial" name="inputTelefoneComercial" class="form-control" placeholder="Telefone Comercial" data-mask="(99) 9999-9999">
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

										<div class="col-lg-2">
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
												<option value="#">Selecione </option>
												<?php
												$sql = "SELECT BancoId, BancoCodigo, BancoNome
														FROM Banco
														JOIN Situacao on SituaId = BancoStatus
														WHERE SituaChave = 'ATIVO'
														ORDER BY BancoCodigo ASC";
												$result = $conn->query($sql);
												$rowBanco = $result->fetchAll(PDO::FETCH_ASSOC);

												foreach ($rowBanco as $item) {
													print('<option value="' . $item['BancoId'] . '">' . $item['BancoCodigo'] . " - " . $item['BancoNome'] . '</option>');
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

										<div class="col-lg-3">
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
												<input type="text" id="inputIpi" name="inputIpi" class="form-control" placeholder="IPI (%)" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFrete">Frete (%)</label>
												<input type="text" id="inputFrete" name="inputFrete" class="form-control" placeholder="Frete (%)" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputIcms">ICMS (%)</label>
												<input type="text" id="inputIcms" name="inputIcms" class="form-control" placeholder="ICMS (%)" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputOutros">Outros (%)</label>
												<input type="text" id="inputOutros" name="inputOutros" class="form-control" placeholder="Outros (%)" onKeyUp="moeda(this)" maxLength="6">
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
										<a href="fornecedor.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>

						</div><!-- /card-body -->	
					</div>
		
					</form>

					<form id="formFoto" method="post" enctype="multipart/form-data" action="upload.php">
						<input type="file" id="imagem" name="imagem" style="display:none;" />
					</form>

				
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