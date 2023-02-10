<?php
include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Editar Fornecedor';
include('global_assets/php/conexao.php');

//Se veio do fornecedor.php
if (isset($_POST['inputFornecedorId'])) {

	$iFornecedor = $_POST['inputFornecedorId'];

	$sql = "SELECT *
			FROM Fornecedor
			WHERE ForneId = $iFornecedor ";
	$result = $conn->query("$sql");
	$row = $result->fetch(PDO::FETCH_ASSOC);

	//SubCategorias para esse fornecedor
	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN FornecedorXSubCategoria on FrXSCSubCategoria = SbCatId
			WHERE SbCatEmpresa = " . $_SESSION['EmpreId'] . " and FrXSCFornecedor = $iFornecedor
			ORDER BY SbCatNome ASC";
	$result = $conn->query($sql);
	$rowBD = $result->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rowBD as $item) {
		$aSubCategorias[] = $item['SbCatId'];
	}

	//Primeiro verifica se no banco está nulo
	if ($row['ForneFoto'] != null) {

		//Depois verifica se o arquivo físico ainda existe no servidor
		if (file_exists("global_assets/images/fornecedores/" . $row['ForneFoto'])) {
			$sFoto = "global_assets/images/fornecedores/" . $row['ForneFoto'];
		} else {
			$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
		}
		$sButtonFoto = "Alterar Foto...";
	} else {
		$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
		$sButtonFoto = "Adicionar Foto...";
	}

	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

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
	<script type="text/javascript">
		function adicionaFoto() {
			$('#imagem').trigger("click");
		};

		$(document).ready(function() {

			 /* Início: Tabela Personalizada */
			 $('#tblDadoSocietarios').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
				searching: false,
				ordering: false, 
				paging: false,
			    columnDefs: [
				{ 
					orderable: true, 
					width: "5%", 
					targets: [0]
				},
				{ 
					orderable: true,   
					width: "25%", 
					targets: [1]
				},
				{ 
					orderable: true,
					width: "20%", 
					targets: [2]
				},				
				{ 
					orderable: true,  
					width: "20%", 
					targets: [3]
				},				
				{ 
					orderable: true,  
					width: "10%", 
					targets: [4]
				},
				{ 
					orderable: true,  
					width: "10%", 
					targets: [5]
				},				
				{ 
					orderable: true,  
					width: "10%", 
					targets: [6]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer">',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
                
			});

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
			if ($('#inputTipoPF:checked').val()) {
				selecionaPessoa('PF');
			} else {
				selecionaPessoa('PJ');
			}

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
								$("#cmbEstado").find('option:selected').text();
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

					var option = '';

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

				var inputTipo = "";
				if ($('input[name="inputTipoPF"]:checked').val()) {
					inputTipo = 'F';
				} else {
					inputTipo = 'J';
				}
				var inputNomeNovo = $('#inputNome').val();
				var inputNomeVelho = $('#inputFornecedorNome').val();
				var inputCpf = $('#inputCpf').val().replace(/[^\d]+/g, '');
				var inputCnpj = $('#inputCnpj').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();

				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();

				//Verifica se o campo só possui espaços em branco
				if (inputNomeNovo == '') {
					alerta('Atenção', 'Informe o nome do fornecedor!', 'error');
					$('#inputNome').focus();
					return false;
				}

				// Se Pessoa Física
				if (inputTipo == "F") {
					//Verifica se o campo só possui espaços em branco
					if (inputCpf == '') {
						alerta('Atenção', 'Informe o CPF!', 'error');
						$('#inputCPF').focus();
						return false;
					}

					if (!validaCPF(inputCpf)) {
						alerta('Atenção', 'CPF inválido!', 'error');
						$('#inputCpf').focus();
						return false;
					}
				} else {
					//Verifica se o campo só possui espaços em branco
					if (inputCnpj == '' || inputCnpj == '__.___.___/____-__') {
						alerta('Atenção', 'Informe o CNPJ!', 'error');
						$('#inputCNPJ').focus();
						return false;
					}
				}

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
						nomeNovo: inputNomeNovo,
						nomeVelho: inputNomeVelho,
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


		}); //document.ready

		function Filtrando() {
			$('#cmbSubCategoria').empty().append('<option value="Filtrando">Filtrando...</option>');
		}

		function Reset() {
			$('#cmbSubCategoria').empty().append('<option value="#">Sem Subcategoria</option>');
		}

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
				let labelErro = $('#inputCnpj-error');
				labelErro.removeClass('validation-valid-label');
				labelErro[0].innerHTML = "CNPJ Inválido";
				$('#inputCnpj').val("");
			}

		}

		function validaEFormataCpf() {
			let cpf = $('#inputCpf').val().replace(/[^\d]+/g, '');
			let resultado = validaCPF(cpf);
			if (!resultado) {
				let labelErro = $('#inputCpf-error');
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
				

					<form name="formFornecedor" action="fornecedorEditaFinalizaTransacao.php" id="formFornecedor" method="post" class="form-validate-jquery">
						<div class="card">
							<div class="card-header header-elements-inline">
								<h5 class="text-uppercase font-weight-bold">Editar Fornecedor "<?php echo $row['ForneNome']; ?>"</h5>
							</div>

							<input type="hidden" id="inputFornecedorId" name="inputFornecedorId" value="<?php echo $row['ForneId']; ?>">
							<input type="hidden" id="inputFornecedorNome" name="inputFornecedorNome" value="<?php echo $row['ForneNome']; ?>">

						
							<div class="card-body">
								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<div class="form-check form-check-inline">
												<label class="form-check-label">
													<input type="radio" id="inputTipoPF" name="inputTipo" value="F" class="form-input-styled" data-fouc onclick="selecionaPessoa('PF')" <?php if ($row['ForneTipo'] == 'F') echo "checked"; ?>>
													Pessoa Física
												</label>
											</div>
											<div class="form-check form-check-inline">
												<label class="form-check-label">
													<input type="radio" id="inputTipoPJ" name="inputTipo" value="J" class="form-input-styled" data-fouc onclick="selecionaPessoa('PJ')" <?php if ($row['ForneTipo'] == 'J') echo "checked"; ?>>
													Pessoa Jurídica
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6" style='margin-top: 60px'>
											<h5 class="mb-0 font-weight-semibold">Dados Pessoais</h5> 
									</div>

									<div class="col-lg-3" id="CPF">
										<div class="form-group">
											<label for="inputCpf">CPF<span class="text-danger">*</span></label>
											<input type="text" id="inputCpf" name="inputCpf" class="form-control" placeholder="CPF" data-mask="999.999.999-99" onblur="validaEFormataCpf()" value="<?php echo formatarCPF_Cnpj($row['ForneCpf']); ?>">
										</div>
									</div>

									<div class="col-lg-3" id="CNPJ">
										<div class="form-group">
											<label for="inputCnpj">CNPJ<span class="text-danger">*</span></label>
											<input type="text" id="inputCnpj" name="inputCnpj" class="form-control" placeholder="CNPJ" data-mask="99.999.999/9999-99" onblur="validaEFormataCnpj()" value="<?php echo formatarCPF_Cnpj($row['ForneCnpj']); ?>">
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputNire">NIRE</label>
											<input type="text" id="inputNire" name="inputNire" class="form-control" placeholder="NIRE" value="<?php echo $row['ForneNire']; ?>">
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputNit">NIT</label>
											<input type="text" id="inputNit" name="inputNit" class="form-control" placeholder="NIT" value="<?php echo $row['ForneNit']; ?>">
										</div>
									</div>
								</div>
								<br>

								<div id="foto" style="text-align:center;width:237px; height:310px; overflow:hidden; justify-content: flex-start; display:flex; flex-direction:column; position:absolute; z-index:1; margin-left:81%">
									<div id="visualizar">
										<img class="ml-3" src="<?php echo $sFoto; ?>" width="200px" alt="Fornecedores" style="border:2px solid #ccc;">
									</div>
									<br>
									<button id="addFoto" type="button" onclick="adicionaFoto()" class="ml-3 btn btn-lg btn-principal" style="width:90%"><?php echo $sButtonFoto; ?></button>
								</div>

								<div class="row">
									<div class="col-lg-8">
										<div class="form-group">
											<label for="inputRazaoSocial">Razão Social</label>
											<input type="text" id="inputRazaoSocial" name="inputRazaoSocial" class="form-control" placeholder="Razão Social" value="<?php echo $row['ForneRazaoSocial']; ?>">
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label for="inputNome">Nome<span class="text-danger">*</span></label>
											<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome Fantasia" value="<?php echo $row['ForneNome']; ?>" required>
										</div>
									</div>
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

									<div class="col-lg-2">
										<div class="form-group">
											<label for="cmbUf">UF</label>
											<select id="cmbUf" name="cmbUf" class="form-control form-control-select2">
												<option value="#">Selecione um estado</option>
												<option value="AC" <?php if ($row['ForneUf'] == 'AC') echo "selected"; ?>>Acre</option>
												<option value="AL" <?php if ($row['ForneUf'] == 'AL') echo "selected"; ?>>Alagoas</option>
												<option value="AP" <?php if ($row['ForneUf'] == 'AP') echo "selected"; ?>>Amapá</option>
												<option value="AM" <?php if ($row['ForneUf'] == 'AM') echo "selected"; ?>>Amazonas</option>
												<option value="BA" <?php if ($row['ForneUf'] == 'BA') echo "selected"; ?>>Bahia</option>
												<option value="CE" <?php if ($row['ForneUf'] == 'CE') echo "selected"; ?>>Ceará</option>
												<option value="DF" <?php if ($row['ForneUf'] == 'DF') echo "selected"; ?>>Distrito Federal</option>
												<option value="ES" <?php if ($row['ForneUf'] == 'ES') echo "selected"; ?>>Espírito Santo</option>
												<option value="GO" <?php if ($row['ForneUf'] == 'GO') echo "selected"; ?>>Goiás</option>
												<option value="MA" <?php if ($row['ForneUf'] == 'MA') echo "selected"; ?>>Maranhão</option>
												<option value="MT" <?php if ($row['ForneUf'] == 'MT') echo "selected"; ?>>Mato Grosso</option>
												<option value="MS" <?php if ($row['ForneUf'] == 'MS') echo "selected"; ?>>Mato Grosso do Sul</option>
												<option value="MG" <?php if ($row['ForneUf'] == 'MG') echo "selected"; ?>>Minas Gerais</option>
												<option value="PA" <?php if ($row['ForneUf'] == 'PA') echo "selected"; ?>>Pará</option>
												<option value="PB" <?php if ($row['ForneUf'] == 'PB') echo "selected"; ?>>Paraíba</option>
												<option value="PR" <?php if ($row['ForneUf'] == 'PR') echo "selected"; ?>>Paraná</option>
												<option value="PE" <?php if ($row['ForneUf'] == 'PE') echo "selected"; ?>>Pernambuco</option>
												<option value="PI" <?php if ($row['ForneUf'] == 'PI') echo "selected"; ?>>Piauí</option>
												<option value="RJ" <?php if ($row['ForneUf'] == 'RJ') echo "selected"; ?>>Rio de Janeiro</option>
												<option value="RN" <?php if ($row['ForneUf'] == 'RN') echo "selected"; ?>>Rio Grande do Norte</option>
												<option value="RS" <?php if ($row['ForneUf'] == 'RS') echo "selected"; ?>>Rio Grande do Sul</option>
												<option value="RO" <?php if ($row['ForneUf'] == 'RO') echo "selected"; ?>>Rondônia</option>
												<option value="RR" <?php if ($row['ForneUf'] == 'RR') echo "selected"; ?>>Roraima</option>
												<option value="SC" <?php if ($row['ForneUf'] == 'SC') echo "selected"; ?>>Santa Catarina</option>
												<option value="SP" <?php if ($row['ForneUf'] == 'SP') echo "selected"; ?>>São Paulo</option>
												<option value="SE" <?php if ($row['ForneUf'] == 'SE') echo "selected"; ?>>Sergipe</option>
												<option value="TO" <?php if ($row['ForneUf'] == 'TO') echo "selected"; ?>>Tocantins</option>
												<option value="ES" <?php if ($row['ForneUf'] == 'ES') echo "selected"; ?>>Estrangeiro</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputCarteiraTrabalho">Carteira de Trabalho</label>
											<input type="text" id="inputCarteiraTrabalho" name="inputCarteiraTrabalho" class="form-control" placeholder="Carteira de Trabalho" value="<?php echo $row['ForneCarteiraTrabalho']; ?>">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputNumSerie">Numero de Série</label>
											<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" placeholder="Numero de Série" value="<?php echo $row['ForneNumSerie']; ?>">
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputAniversario">Data Nascimento</label>
											<input type="date" id="inputAniversario" name="inputAniversario" class="form-control" placeholder="Aniversário" onblur="formataCampoDataNascimento()" value="<?php echo $row['ForneAniversario']; ?>">
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label for="cmbSexo">Sexo</label>
											<select id="cmbSexo" name="cmbSexo" class="form-control form-control-select2">
												<option value="#">Selecione o sexo</option>
												<option value="F" <?php if ($row['ForneSexo'] == 'F') echo "selected"; ?>>Feminino</option>
												<option value="M" <?php if ($row['ForneSexo'] == 'M') echo "selected"; ?>>Masculino</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputNaturalidade">Naturalidade</label>
											<input type="text" id="inputNaturalidade" name="inputNaturalidade" class="form-control" placeholder="Naturalidade" value="<?php echo $row['ForneNaturalidade']; ?>">
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputNaturalidadeUf">UF da Naturalidade</label>
											<select id="inputNaturalidadeUf" name="inputNaturalidadeUf" class="form-control form-control-select2">
												<option value="#">Selecione um estado</option>
												<option value="AC" <?php if ($row['ForneNaturalidadeUf'] == 'AC') echo "selected"; ?>>Acre</option>
												<option value="AL" <?php if ($row['ForneNaturalidadeUf'] == 'AL') echo "selected"; ?>>Alagoas</option>
												<option value="AP" <?php if ($row['ForneNaturalidadeUf'] == 'AP') echo "selected"; ?>>Amapá</option>
												<option value="AM" <?php if ($row['ForneNaturalidadeUf'] == 'AM') echo "selected"; ?>>Amazonas</option>
												<option value="BA" <?php if ($row['ForneNaturalidadeUf'] == 'BA') echo "selected"; ?>>Bahia</option>
												<option value="CE" <?php if ($row['ForneNaturalidadeUf'] == 'CE') echo "selected"; ?>>Ceará</option>
												<option value="DF" <?php if ($row['ForneNaturalidadeUf'] == 'DF') echo "selected"; ?>>Distrito Federal</option>
												<option value="ES" <?php if ($row['ForneNaturalidadeUf'] == 'ES') echo "selected"; ?>>Espírito Santo</option>
												<option value="GO" <?php if ($row['ForneNaturalidadeUf'] == 'GO') echo "selected"; ?>>Goiás</option>
												<option value="MA" <?php if ($row['ForneNaturalidadeUf'] == 'MA') echo "selected"; ?>>Maranhão</option>
												<option value="MT" <?php if ($row['ForneNaturalidadeUf'] == 'MT') echo "selected"; ?>>Mato Grosso</option>
												<option value="MS" <?php if ($row['ForneNaturalidadeUf'] == 'MS') echo "selected"; ?>>Mato Grosso do Sul</option>
												<option value="MG" <?php if ($row['ForneNaturalidadeUf'] == 'MG') echo "selected"; ?>>Minas Gerais</option>
												<option value="PA" <?php if ($row['ForneNaturalidadeUf'] == 'PA') echo "selected"; ?>>Pará</option>
												<option value="PB" <?php if ($row['ForneNaturalidadeUf'] == 'PB') echo "selected"; ?>>Paraíba</option>
												<option value="PR" <?php if ($row['ForneNaturalidadeUf'] == 'PR') echo "selected"; ?>>Paraná</option>
												<option value="PE" <?php if ($row['ForneNaturalidadeUf'] == 'PE') echo "selected"; ?>>Pernambuco</option>
												<option value="PI" <?php if ($row['ForneNaturalidadeUf'] == 'PI') echo "selected"; ?>>Piauí</option>
												<option value="RJ" <?php if ($row['ForneNaturalidadeUf'] == 'RJ') echo "selected"; ?>>Rio de Janeiro</option>
												<option value="RN" <?php if ($row['ForneNaturalidadeUf'] == 'RN') echo "selected"; ?>>Rio Grande do Norte</option>
												<option value="RS" <?php if ($row['ForneNaturalidadeUf'] == 'RS') echo "selected"; ?>>Rio Grande do Sul</option>
												<option value="RO" <?php if ($row['ForneNaturalidadeUf'] == 'RO') echo "selected"; ?>>Rondônia</option>
												<option value="RR" <?php if ($row['ForneNaturalidadeUf'] == 'RR') echo "selected"; ?>>Roraima</option>
												<option value="SC" <?php if ($row['ForneNaturalidadeUf'] == 'SC') echo "selected"; ?>>Santa Catarina</option>
												<option value="SP" <?php if ($row['ForneNaturalidadeUf'] == 'SP') echo "selected"; ?>>São Paulo</option>
												<option value="SE" <?php if ($row['ForneNaturalidadeUf'] == 'SE') echo "selected"; ?>>Sergipe</option>
												<option value="TO" <?php if ($row['ForneNaturalidadeUf'] == 'TO') echo "selected"; ?>>Tocantins</option>
												<option value="ES" <?php if ($row['ForneNaturalidadeUf'] == 'ES') echo "selected"; ?>>Estrangeiro</option>
											</select>
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label for="inputNacionalidade">Nacionalidade</label>
											<input type="text" id="inputNacionalidade" name="inputNacionalidade" class="form-control" placeholder="Nacionalidade" value="<?php echo $row['ForneNacionalidade']; ?>">
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputAno">Ano &nbsp<i style="color:#375b82;" class="icon-question4" data-popup="tooltip" data-original-title="Entrada no Brasil (se estrangeiro)" data-placement="right"></i></label>
											<input type="text" id="inputAno" name="inputAno" class="form-control" placeholder="Ano" value="<?php echo $row['ForneAno']; ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputInscricaoMunicipal">Inscrição Municipal</label>
											<input type="text" id="inputInscricaoMunicipal" name="inputInscricaoMunicipal" class="form-control" placeholder="Inscrição Municipal" value="<?php echo $row['ForneInscricaoMunicipal']; ?>">
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputInscricaoEstadual">Inscrição Estadual</label>
											<input type="text" id="inputInscricaoEstadual" name="inputInscricaoEstadual" class="form-control" placeholder="Ins. Estadual" value="<?php echo $row['ForneInscricaoEstadual']; ?>">
										</div>
									</div>

									<div class="col-lg-4">
										<div class="form-group">
											<label for="cmbCategoria">Categoria<span class="text-danger"> *</span></label>
											<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
												<option value="#">Selecione uma categoria</option>
												<?php
												$sql = "SELECT CategId, CategNome
														FROM Categoria
														JOIN Situacao on SituaId = CategStatus
														WHERE CategEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
														ORDER BY CategNome ASC";
												$result = $conn->query($sql);
												$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

												foreach ($rowCategoria as $item) {
													$seleciona = $item['CategId'] == $row['ForneCategoria'] ? "selected" : "";
													print('<option value="' . $item['CategId'] . '" ' . $seleciona . '>' . $item['CategNome'] . '</option>');
												}

												?>
											</select>
										</div>
									</div>

									<div class="col-lg-4">
										<div class="form-group" style="border-bottom:1px solid #ddd;">
											<label for="cmbSubCategoria">SubCategoria</label>
											<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control select" multiple="multiple" data-fouc>
												<!--<option value="#">Selecione uma subcategoria</option>-->
												<?php

												if (isset($row['ForneCategoria'])) {

													$sql = "SELECT SbCatId, SbCatNome
																FROM SubCategoria
																JOIN Situacao on SituaId = SbCatStatus
																WHERE SbCatEmpresa = " . $_SESSION['EmpreId'] . " and SbCatCategoria = " . $row['ForneCategoria'] . " and SituaChave = 'ATIVO'
																ORDER BY SbCatNome ASC";
													$result = $conn->query($sql);
													$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													$count = count($rowSubCategoria);

													if ($count) {
														foreach ($rowSubCategoria as $item) {
															$seleciona = in_array($item['SbCatId'], $aSubCategorias) ? "selected" : "";
															print('<option value="' . $item['SbCatId'] . '" ' . $seleciona . '>' . $item['SbCatNome'] . '</option>');
														}
													}
												}
												?>
											</select>
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
													<input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" value="<?php echo $row['ForneCep']; ?>" maxLength="8">
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
													<select id="cmbEstado" name="cmbEstado" class="form-control">
														<option value="#">Selecione um estado</option>
														<option value="AC" <?php if ($row['ForneEstado'] == 'AC') echo "selected"; ?>>Acre</option>
														<option value="AL" <?php if ($row['ForneEstado'] == 'AL') echo "selected"; ?>>Alagoas</option>
														<option value="AP" <?php if ($row['ForneEstado'] == 'AP') echo "selected"; ?>>Amapá</option>
														<option value="AM" <?php if ($row['ForneEstado'] == 'AM') echo "selected"; ?>>Amazonas</option>
														<option value="BA" <?php if ($row['ForneEstado'] == 'BA') echo "selected"; ?>>Bahia</option>
														<option value="CE" <?php if ($row['ForneEstado'] == 'CE') echo "selected"; ?>>Ceará</option>
														<option value="DF" <?php if ($row['ForneEstado'] == 'DF') echo "selected"; ?>>Distrito Federal</option>
														<option value="ES" <?php if ($row['ForneEstado'] == 'ES') echo "selected"; ?>>Espírito Santo</option>
														<option value="GO" <?php if ($row['ForneEstado'] == 'GO') echo "selected"; ?>>Goiás</option>
														<option value="MA" <?php if ($row['ForneEstado'] == 'MA') echo "selected"; ?>>Maranhão</option>
														<option value="MT" <?php if ($row['ForneEstado'] == 'MT') echo "selected"; ?>>Mato Grosso</option>
														<option value="MS" <?php if ($row['ForneEstado'] == 'MS') echo "selected"; ?>>Mato Grosso do Sul</option>
														<option value="MG" <?php if ($row['ForneEstado'] == 'MG') echo "selected"; ?>>Minas Gerais</option>
														<option value="PA" <?php if ($row['ForneEstado'] == 'PA') echo "selected"; ?>>Pará</option>
														<option value="PB" <?php if ($row['ForneEstado'] == 'PB') echo "selected"; ?>>Paraíba</option>
														<option value="PR" <?php if ($row['ForneEstado'] == 'PR') echo "selected"; ?>>Paraná</option>
														<option value="PE" <?php if ($row['ForneEstado'] == 'PE') echo "selected"; ?>>Pernambuco</option>
														<option value="PI" <?php if ($row['ForneEstado'] == 'PI') echo "selected"; ?>>Piauí</option>
														<option value="RJ" <?php if ($row['ForneEstado'] == 'RJ') echo "selected"; ?>>Rio de Janeiro</option>
														<option value="RN" <?php if ($row['ForneEstado'] == 'RN') echo "selected"; ?>>Rio Grande do Norte</option>
														<option value="RS" <?php if ($row['ForneEstado'] == 'RS') echo "selected"; ?>>Rio Grande do Sul</option>
														<option value="RO" <?php if ($row['ForneEstado'] == 'RO') echo "selected"; ?>>Rondônia</option>
														<option value="RR" <?php if ($row['ForneEstado'] == 'RR') echo "selected"; ?>>Roraima</option>
														<option value="SC" <?php if ($row['ForneEstado'] == 'SC') echo "selected"; ?>>Santa Catarina</option>
														<option value="SP" <?php if ($row['ForneEstado'] == 'SP') echo "selected"; ?>>São Paulo</option>
														<option value="SE" <?php if ($row['ForneEstado'] == 'SE') echo "selected"; ?>>Sergipe</option>
														<option value="TO" <?php if ($row['ForneEstado'] == 'TO') echo "selected"; ?>>Tocantins</option>
														<option value="ES" <?php if ($row['ForneEstado'] == 'ES') echo "selected"; ?>>Estrangeiro</option>
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
													<input type="text" id="inputNomeContato" name="inputNomeContato" class="form-control" placeholder="Contato" value="<?php echo $row['ForneContato']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputTelefoneResidencial">Telefone Residencial</label>
													<input type="tel" id="inputTelefoneResidencial" name="inputTelefoneResidencial" class="form-control" placeholder="Telefone Residencial" data-mask="(99) 9999-9999" value="<?php echo $row['ForneTelefone']; ?>">
												</div>
											</div>

											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputTelefoneComercial">Telefone Comercial</label>
													<input type="tel" id="inputTelefoneComercial" name="inputTelefoneComercial" class="form-control" placeholder="Telefone Comercial" data-mask="(99) 9999-9999" value="<?php echo $row['ForneTelefoneComercial']; ?>">
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

											<div class="col-lg-2">
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
							</div>
						</div>

						<div class="card">

						<div class="card-header header-elements-inline">
							<h5 class="card-title font-weight-bold">Dados Societários</h5>
						</div>

						<div class="card-body">

							<form id="formDadoSocietarios" name="formDadoSocietarios" method="post" class="form-validate-jquery">
								<input type="hidden" name="idDadoSocietarios" id="idDadoSocietarios">
					
								<div class="col-lg-12 mb-2 row" style='margin-left: -20px;'>
									<!-- titulos -->
									<div class="col-lg-4">
										<label>Nome <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-2">
										<label>CPF</label>
									</div>
									<div class="col-lg-2">
										<label>RG</label>
									</div>
									<div class="col-lg-2">
										<label>Celular</label>
									</div>
									<div class="col-lg-2">
										<label>E-mail</label>
									</div>
									
									<!-- campos -->										
									<div class="col-lg-4">
										<input type="text" class="form-control" name="dadoSocietariosNome" id="dadoSocietariosNome" value="">	
									
									</div>
									<div class="col-lg-2">
										<input type="text" class="form-control" name="dadoSocietariosCPF" id="dadoSocietariosCPF" value="">	
									
									</div>
									<div class="col-lg-2">
										<input type="text" class="form-control" name="dadoSocietariosRG" id="dadoSocietariosRG" value="">	
									
									</div>
									<div class="col-lg-2">
										<input type="text" class="form-control" name="dadoSocietariosCelular" id="dadoSocietariosCelular" value="">	
									
									</div>
									<div class="col-lg-2">
										<input type="text" class="form-control" name="dadoSocietariosEmail" id="dadoSocietariosEmail" value="">	
									
									</div>
									
								</div>
								
							</form>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group" style="padding-top:15px;">	
										<button class="btn btn-lg btn-success" id="incluirDadoSocietarios" style="display: block;"  >Adicionar</button>		
									</div>
								</div>
							</div> 
						</div>

						<div class="row">
							<div class="col-lg-12">
								<table class="table" id="tblDadoSocietarios">
									<thead>
										<tr class="bg-slate">
											<th class="text-left">Item</th>
											<th class="text-left">Nome</th>
											<th class="text-left">CPF</th>
											<th class="text-left">RG</th>
											<th class="text-left">Celular</th>
											<th class="text-left">Email</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody id="dataDadoSocietarios">
									</tbody>
								</table>
							</div>		
						</div>							

					</div>
						
						<div class="card">	
							<div class="card-body">
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
													$sql = "SELECT BancoId, BancoCodigo, BancoNome
																FROM Banco
																JOIN Situacao on SituaId = BancoStatus
																WHERE SituaChave = 'ATIVO'
																ORDER BY BancoCodigo ASC";
													$result = $conn->query($sql);
													$rowBanco = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowBanco as $item) {
														$seleciona = $item['BancoId'] == $row['ForneBanco'] ? "selected" : "";
														print('<option value="' . $item['BancoId'] . '" ' . $seleciona . '>' . $item['BancoCodigo'] . " - " . $item['BancoNome'] . '</option>');
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

											<div class="col-lg-3">
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
													<input type="text" id="inputIpi" name="inputIpi" class="form-control" placeholder="IPI (%)" value="<?php echo mostraValor($row['ForneIpi']); ?>" onKeyUp="moeda(this)" maxLength="6">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputFrete">Frete (%)</label>
													<input type="text" id="inputFrete" name="inputFrete" class="form-control" placeholder="Frete (%)" value="<?php echo mostraValor($row['ForneFrete']); ?>" onKeyUp="moeda(this)" maxLength="6">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputIcms">ICMS (%)</label>
													<input type="text" id="inputIcms" name="inputIcms" class="form-control" placeholder="ICMS (%)" value="<?php echo mostraValor($row['ForneIcms']); ?>" onKeyUp="moeda(this)" maxLength="6">
												</div>
											</div>

											<div class="col-lg-3">
												<div class="form-group">
													<label for="inputOutros">Outros (%)</label>
													<input type="text" id="inputOutros" name="inputOutros" class="form-control" placeholder="Outros (%)" value="<?php echo mostraValor($row['ForneOutros']); ?>" onKeyUp="moeda(this)" maxLength="6">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="card">
							<div class="card-body">
								<div class="row" style="margin-top: 40px;">
									<div class="col-lg-12">
										<div class="form-group">
											<?php
											if ($_POST['inputPermission']) {
												echo '<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>';
											}
											?>
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