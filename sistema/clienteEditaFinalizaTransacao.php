<?php 
include_once("sessao.php"); 
$_SESSION['PaginaAtual'] = 'Editar Cliente';
include('global_assets/php/conexao.php');

try{

    $conn->beginTransaction();
    
    $sql = "UPDATE Cliente SET 	  ClienCodigo = :sCodigo, ClienTipo = :sTipo, ClienNome = :sNome, ClienRazaoSocial = :sRazaoSocial, ClienCnpj = :sCnpj, 
                                    ClienInscricaoMunicipal = :sInscricaoMunicipal, ClienInscricaoEstadual = :sInscricaoEstadual, 
                                    ClienCpf = :sCpf, ClienRg = :sRg, ClienOrgaoEmissor = :sOrgaoEmissor, ClienUf = :sUf,
                                    ClienSexo = :sSexo, ClienDtNascimento = :dDtNascimento, ClienNomePai = :sNomePai, ClienNomeMae = :sNomeMae, 
                                    ClienProfissao = :sProfissao, ClienCartaoSus = :sCartaoSus, ClienCep = :sCep, ClienEndereco = :sEndereco, 
                                    ClienNumero = :sNumero, ClienComplemento = :sComplemento, ClienBairro = :sBairro, 
                                    ClienCidade = :sCidade, ClienEstado = :sEstado, ClienContato = :sContato, ClienTelefone = :sTelefone, 
                                    ClienCelular = :sCelular, ClienEmail = :sEmail, ClienSite = :sSite, ClienObservacao = :sObservacao,
                                    ClienUsuarioAtualizador = :iUsuarioAtualizador
            WHERE ClienId = :iCliente";
    $result = $conn->prepare($sql);						
    $_POST['inputTipo']="F";
    $result->execute(array(
                    ':sCodigo' => $_POST['inputCodigo'],
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
                    ':sProfissao' => $_POST['inputTipo'] == 'F' ? $_POST['cmbProfissao'] : null,
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
    
    $conn->rollback();
    
    $_SESSION['msg']['titulo'] = "Erro";
    $_SESSION['msg']['mensagem'] = "Erro ao alterar cliente!!!";
    $_SESSION['msg']['tipo'] = "error";	
    
    echo 'Error: ' . $e->getMessage();
    exit;
}
irpara("cliente.php");
?>