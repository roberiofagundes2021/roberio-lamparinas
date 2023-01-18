<?php
include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Editar Fornecedor';
include('global_assets/php/conexao.php');



try {

	$input_foto = null;
	if ($_POST['inputTipo'] == 'F') {
		if (isset($_POST['inputFoto'])) {
			$input_foto = $_POST['inputFoto'];
		}
	}

	$sql = "UPDATE Fornecedor SET ForneTipo = :sTipo, ForneNome = :sNome, ForneRazaoSocial = :sRazaoSocial, ForneCnpj = :sCnpj, 
				ForneInscricaoMunicipal = :sInscricaoMunicipal, ForneInscricaoEstadual = :sInscricaoEstadual, 
				ForneCategoria = :iCategoria, ForneCpf = :sCpf, 
				ForneRg = :sRg, ForneOrgaoEmissor = :sOrgaoEmissor, ForneUf = :sUf, ForneSexo = :sSexo, 
				ForneAniversario = :dAniversario, ForneNaturalidade = :sNaturalidade, ForneNaturalidadeUf = :sNaturalidadeUf,
				ForneNacionalidade = :sNacionalidade, ForneAno = :sAno, ForneCarteiraTrabalho = :sCarteiraTrabalho, ForneNit = :sNit,
				ForneCategoriaCredor = :sCategoriaCredor, ForneFoto = :sFoto, ForneCep = :sCep, ForneEndereco = :sEndereco, 
				ForneNumero = :sNumero, ForneComplemento = :sComplemento, ForneBairro = :sBairro, 
				ForneCidade = :sCidade, ForneEstado = :sEstado, ForneContato = :sContato, ForneTelefone = :sTelefone, ForneTelefoneComercial = :sTelefoneComercial,
				ForneCelular = :sCelular, ForneEmail = :sEmail, ForneSite = :sSite, ForneObservacao = :sObservacao,
				ForneBanco = :iBanco, ForneAgencia = :sAgencia, ForneConta = :sConta, 
				ForneInformacaoAdicional = :sInformacaoAdicional, ForneIpi = :iIpi, ForneFrete = :iFrete, 
				ForneIcms = :iIcms, ForneOutros = :iOutros, ForneUsuarioAtualizador = :iUsuarioAtualizador
			WHERE ForneId = :iFornecedor";

	$result = $conn->prepare($sql);

	$conn->beginTransaction();

	$result->execute(array(
		':sTipo' => $_POST['inputTipo'],
		':sNome' => $_POST['inputNome'],
		':sRazaoSocial' => $_POST['inputTipo'] == 'J' ? $_POST['inputRazaoSocial'] : null,
		':sCnpj' => $_POST['inputTipo'] == 'J' ? limpaCPF_CNPJ($_POST['inputCnpj']) : null,
		':sInscricaoMunicipal' => $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoMunicipal'] : null,
		':sInscricaoEstadual' => $_POST['inputTipo'] == 'J' ? $_POST['inputInscricaoEstadual'] : null,
		':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
		//':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
		':sCpf' => $_POST['inputTipo'] == 'F' ? limpaCPF_CNPJ($_POST['inputCpf']) : null,
		':sRg' => $_POST['inputTipo'] == 'F' ? $_POST['inputRg'] : null,
		':sOrgaoEmissor' => $_POST['inputTipo'] == 'F' ? $_POST['inputEmissor'] : null,
		':sUf' => $_POST['inputTipo'] == 'J' || $_POST['cmbUf'] == '#' ? null : $_POST['cmbUf'],
		':sSexo' => $_POST['inputTipo'] == 'J' || $_POST['cmbSexo'] == '#' ? null : $_POST['cmbSexo'],
		':dAniversario' => $_POST['inputTipo'] == 'F' ? ($_POST['inputAniversario'] == '' ? null : $_POST['inputAniversario']) : null,
		':sNaturalidade' => $_POST['inputTipo'] == 'F' ? ($_POST['inputNaturalidade'] == '' ? null : $_POST['inputNaturalidade']) : null,
		':sNaturalidadeUf' => $_POST['inputTipo'] == 'F' ? ($_POST['inputNaturalidadeUf'] == '' ? null : $_POST['inputNaturalidadeUf']) : null,
		':sNacionalidade' => $_POST['inputTipo'] == 'F' ? ($_POST['inputNacionalidade'] == '' ? null : $_POST['inputNacionalidade']) : null,
		':sAno' => $_POST['inputTipo'] == 'F' ? ($_POST['inputAno'] == '' ? null : $_POST['inputAno']) : null,
		':sCarteiraTrabalho' => $_POST['inputTipo'] == 'F' ? ($_POST['inputCarteiraTrabalho'] == '' ? null : $_POST['inputCarteiraTrabalho']) : null,
		':sNit' => $_POST['inputNit'],
		'sCategoriaCredor' => $_POST['inputCategoriaCredor'],
		':sFoto' => $input_foto,
		':sCep' => trim($_POST['inputCep']) == "" ? null : $_POST['inputCep'],
		':sEndereco' => $_POST['inputEndereco'],
		':sNumero' => $_POST['inputNumero'],
		':sComplemento' => $_POST['inputComplemento'],
		':sBairro' => $_POST['inputBairro'],
		':sCidade' => $_POST['inputCidade'],
		':sEstado' => $_POST['cmbEstado'],
		':sContato' => $_POST['inputNomeContato'],
		':sTelefone' => $_POST['inputTelefoneResidencial'] == '(__) ____-____' ? null : $_POST['inputTelefoneResidencial'],
		':sTelefoneComercial' => $_POST['inputTelefoneComercial'] == '(__) ____-____' ? null : $_POST['inputTelefoneComercial'],
		':sCelular' => $_POST['inputCelular'] == '(__) _____-____' ? null : $_POST['inputCelular'],
		':sEmail' => $_POST['inputEmail'],
		':sSite' => $_POST['inputSite'],
		':sObservacao' => $_POST['txtareaObservacao'],
		':iBanco' => $_POST['cmbBanco'] == '#' ? null : $_POST['cmbBanco'],
		':sAgencia' => $_POST['inputAgencia'],
		':sConta' => $_POST['inputConta'],
		':sInformacaoAdicional' => $_POST['inputInfoAdicional'],
		':iIpi' => $_POST['inputIpi'] == null ? 0.00 : gravaValor($_POST['inputIpi']),
		':iFrete' => $_POST['inputFrete'] == null ? 0.00 : gravaValor($_POST['inputFrete']),
		':iIcms' => $_POST['inputIcms'] == null ? 0.00 : gravaValor($_POST['inputIcms']),
		':iOutros' => $_POST['inputOutros'] == null ? 0.00 : gravaValor($_POST['inputOutros']),
		':iUsuarioAtualizador' => $_SESSION['UsuarId'],
		':iFornecedor'	=> $_POST['inputFornecedorId']
	));

	$sql = "DELETE FROM FornecedorXSubCategoria
				WHERE FrXSCFornecedor = :iFornecedor and FrXSCUnidade = :iUnidade";
	$result = $conn->prepare($sql);

	$result->execute(array(
		':iFornecedor' => $_POST['inputFornecedorId'],
		':iUnidade' => $_SESSION['UnidadeId']
	));

	if (isset($_POST['cmbSubCategoria'])) {

		try {
			$sql = "INSERT INTO FornecedorXSubCategoria 
							(FrXSCFornecedor, FrXSCSubCategoria, FrXSCUnidade)
						VALUES 
							(:iFornecedor, :iSubCategoria, :iUnidade)";
			$result = $conn->prepare($sql);

			foreach ($_POST['cmbSubCategoria'] as $key => $value) {

				$result->execute(array(
					':iFornecedor' => $_POST['inputFornecedorId'],
					':iSubCategoria' => $value,
					':iUnidade' => $_SESSION['UnidadeId']
				));
			}
		} catch (PDOException $e) {
			$conn->rollback();
			echo 'Error: ' . $e->getMessage();
			exit;
		}
	}

	$conn->commit();

	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Fornecedor alterado!!!";
	$_SESSION['msg']['tipo'] = "success";
} catch (PDOException $e) {

	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao alterar fornecedor!!!";
	$_SESSION['msg']['tipo'] = "error";

	echo 'Error: ' . $e->getMessage();
	exit;
}

irpara("fornecedor.php");
