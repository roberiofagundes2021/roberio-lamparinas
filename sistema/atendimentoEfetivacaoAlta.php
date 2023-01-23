<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Efetivação de Alta';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if (isset($_SESSION['iAtendimentoId']) && $iAtendimentoId == null) {
	$iAtendimentoId = $_SESSION['iAtendimentoId'];
}
$_SESSION['iAtendimentoId'] = null;

if(!$iAtendimentoId){
	irpara("atendimentoHospitalarListagem.php");	
}


//anamnese
/*$sql = "SELECT TOP(1) EnAnaId
FROM EnfermagemAnamnese
WHERE EnAnaAtendimento = $iAtendimentoId
ORDER BY EnAnaId DESC";
$result = $conn->query($sql);
$rowAnamnese= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoAnamneseId = $rowAnamnese?$rowAnamnese['EnAnaId']:null;*/

//exame físico
$sql = "SELECT TOP(1) EnEfAId
FROM EnfermagemEfetivacaoAlta
WHERE EnEfAAtendimento = $iAtendimentoId
ORDER BY EnEfAId DESC";
$result = $conn->query($sql);
$rowExameFisico= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoEfetivacaoAlta = $rowExameFisico?$rowExameFisico['EnEfAId']:null;
//$iAtendimentoEfetivacaoAlta = null; iAtendimentoEfetivacaoAlta
// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';


//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, A.ProfiUsuario, A.ProfiId as ProfissionalId, A.ProfiNome as ProfissionalNome, PrConNome, B.ProfiCbo as ProfissaoCbo, ProfiNumConselho
		FROM Usuario
		JOIN Profissional A ON A.ProfiUsuario = UsuarId
		LEFT JOIN Profissao B ON B.ProfiId = A.ProfiProfissao
		LEFT JOIN ProfissionalConselho ON PrConId = ProfiConselho
		WHERE UsuarId =  ". $_SESSION['UsuarId'] . " ";
$result = $conn->query($sql);
$rowUser = $result->fetch(PDO::FETCH_ASSOC);
$userId = $rowUser['ProfissionalId'];


//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtModNome, AtendClassificacaoRisco, ClienId, ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,
               ClienNomeMae, ClienCartaoSus, ClienCelular, ClienStatus, ClienUsuarioAtualizador, ClienUnidade, ClResNome, AtTriPeso,
			   AtTriAltura, AtTriImc, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca, AtTriTempAXI, AtClRCor,
               TpIntNome, TpIntId, EsLeiNome, EsLeiId, AlaNome, AlaId, QuartNome, QuartId, LeitoNome, LeitoId
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoTriagem ON AtTriAtendimento = AtendId
		LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
        LEFT JOIN AtendimentoXLeito ON AtXLeAtendimento = AtendId
        LEFT JOIN EspecialidadeLeito ON AtXLeEspecialidadeLeito = EsLeiId
        LEFT JOIN TipoInternacao ON EsLeiTipoInternacao = TpIntId
        LEFT JOIN Leito ON AtXLeLeito = LeitoId
        LEFT JOIN Quarto ON LeitoQuarto = QuartId
        LEFT JOIN Ala ON QuartAla = AlaId
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE  AtendId = $iAtendimentoId 
		ORDER BY AtendNumRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoCliente = $row['AtendCliente'] ;
$iAtendimentoId = $row['AtendId'];

//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Feminino';
} else{
    $sexo = 'Masculino';
}


//se estive editando
/*if(isset($iAtendimentoAnamneseId ) && $iAtendimentoAnamneseId ){

	//Essa consulta é para preencher o campo do Atendimento Ambulatorial ao editar
	$sql = "SELECT *
			FROM EnfermagemAnamnese
			WHERE EnAnaId = " . $iAtendimentoAnamneseId ;
	$result = $conn->query($sql);
	$rowAnamnese = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

} */

//se estive editando exame fisico
if(isset($iAtendimentoEfetivacaoAlta ) && $iAtendimentoEfetivacaoAlta ){

	//Essa consulta é para preencher o campo do Atendimento Ambulatorial ao editar
	$sql = "SELECT *
			FROM EnfermagemEfetivacaoAlta
			WHERE EnEfAId = " . $iAtendimentoEfetivacaoAlta ;
	$result = $conn->query($sql);
	$rowExameFisico = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

} 

if (isset($_POST['inputInicio'])) {

    try {
 
        /*if ($iAtendimentoAnamneseId) {

            $sql = "UPDATE EnfermagemAnamnese SET 
                EnAnaAtendimento = :sAtendimento,
                EnAnaDataInicio = :sDataInicio,
                EnAnaHoraInicio = :sHoraInicio,
                EnAnaDataFim = :sDataFim,
                EnAnaHoraFim = :sHoraFim,
                EnAnaProfissional = :sProfissional,
                EnAnaPas = :sPas,
                EnAnaPad = :sPad,
                EnAnaFreqCardiaca = :sFreqCardiaca,
                EnAnaFreqRespiratoria = :sFreqRespiratoria,
                EnAnaTemperatura = :sTemperatura,
                EnAnaSPO = :sSPO,
                EnAnaHGT = :sHGT,
                EnAnaPeso = :sPeso,
                EnAnaAlergia = :sAlergia,
                EnAnaAlergiaDescricao = :sAlergiaDescricao,
                EnAnaDiabetes = :sDiabetes,
                EnAnaDiabetesDescricao = :sDiabetesDescricao,
                EnAnaHipertensao = :sHipertensao,
                EnAnaHipertensaoDescricao = :sHipertensaoDescricao,
                EnAnaNeoplasia = :sNeoplasia,
                EnAnaNeoplasiaDescricao = :sNeoplasiaDescricao,
                EnAnaUsoMedicamento = :sUsoMedicamento,
                EnAnaUsoMedicamentoDescricao = :sUsoMedicamentoDescricao,
                EnAnaCid10 = :sCid10,
                EnAnaProcedimento = :sProcedimento,
                EnAnaQueixaPrincipal = :sQueixaPrincipal,
                EnAnaHistoriaMolestiaAtual = :sHistoriaMolestiaAtual,
                EnAnaHistoriaPatologicaPregressa = :sHistoriaPatologicaPregressa,
                EnAnaHistoriaFamiliar = :sHistoriaFamiliar,
                EnAnaHipoteseSocioEconomica = :sHipoteseSocioEconomica,
                EnAnaDigitacaoLivre = :sDigitacaoLivre,
                EnAnaUnidade = :sUnidade
                WHERE EnAnaId = :iAtendimentoAnamnese";
                
            $result = $conn->prepare($sql);
                    
            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId,
                ':sDataInicio' => date('m/d/Y'),
                ':sDataFim' => date('m/d/Y'),
                ':sHoraInicio' => date('H:i'),
                ':sHoraFim' => date('H:i'),
                ':sProfissional' => $userId,

                ':sPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
                ':sPad' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
                ':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
                ':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
                ':sTemperatura' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
                ':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
                ':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
                ':sPeso' => $_POST['inputPeso'] == "" ? null : $_POST['inputPeso'],

                ':sAlergia' => $_POST['inputAlergia'] == "" ? null : $_POST['inputAlergia'],
                ':sAlergiaDescricao' => $_POST['inputAlergiaDescricao'] == "" ? null : $_POST['inputAlergiaDescricao'],
                ':sDiabetes' => $_POST['inputDiabetes'] == "" ? null : $_POST['inputDiabetes'],
                ':sDiabetesDescricao' => $_POST['inputDiabetesDescricao'] == "" ? null : $_POST['inputDiabetesDescricao'],
                ':sHipertensao' => $_POST['inputHipertensao'] == "" ? null : $_POST['inputHipertensao'],
                ':sHipertensaoDescricao' => $_POST['inputHipertensaoDescricao'] == "" ? null : $_POST['inputHipertensaoDescricao'],
                ':sNeoplasia' => $_POST['inputNeoplasia'] == "" ? null : $_POST['inputNeoplasia'],
                ':sNeoplasiaDescricao' => $_POST['inputNeoplasiaDescricao'] == "" ? null : $_POST['inputNeoplasiaDescricao'],
                ':sUsoMedicamento' => $_POST['inputUsoMedicamento'] == "" ? null : $_POST['inputUsoMedicamento'],
                ':sUsoMedicamentoDescricao' => $_POST['inputUsoMedicamentoDescricao'] == "" ? null : $_POST['inputUsoMedicamentoDescricao'],

                ':sCid10' => $_POST['cmbCId10'] == "" ? null : $_POST['cmbCId10'],
                ':sProcedimento' => $_POST['cmbProcedimento'] == "" ? null : $_POST['cmbProcedimento'],

                ':sQueixaPrincipal' => $_POST['txtareaConteudo1'] == "" ? null : $_POST['txtareaConteudo1'],
                ':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'] == "" ? null : $_POST['txtareaConteudo2'],
                ':sHistoriaPatologicaPregressa' => $_POST['txtareaConteudo3'] == "" ? null : $_POST['txtareaConteudo3'],
                ':sHistoriaFamiliar' => $_POST['txtareaConteudo4'] == "" ? null : $_POST['txtareaConteudo4'],
                ':sHipoteseSocioEconomica' => $_POST['txtareaConteudo5'] == "" ? null : $_POST['txtareaConteudo5'],
                ':sDigitacaoLivre' => $_POST['txtareaConteudo6'] == "" ? null : $_POST['txtareaConteudo6'],

                ':sUnidade' => $_SESSION['UnidadeId'],

                ':iAtendimentoAnamnese' => $iAtendimentoAnamneseId 
                ));
    
            $_SESSION['msg']['mensagem'] = "Anamnese alterada!!!";
    
        } else {

            $sql = "INSERT INTO EnfermagemAnamnese 
                (EnAnaAtendimento, 
                EnAnaDataInicio, 
                EnAnaHoraInicio, 
                EnAnaDataFim, 
                EnAnaHoraFim, 
                EnAnaProfissional, 
                EnAnaPas, 
                EnAnaPad, 
                EnAnaFreqCardiaca, 
                EnAnaFreqRespiratoria, 
                EnAnaTemperatura, 
                EnAnaSPO, 
                EnAnaHGT, 
                EnAnaPeso, 
                EnAnaAlergia, 
                EnAnaAlergiaDescricao, 
                EnAnaDiabetes, 
                EnAnaDiabetesDescricao, 
                EnAnaHipertensao, 
                EnAnaHipertensaoDescricao, 
                EnAnaNeoplasia, 
                EnAnaNeoplasiaDescricao, 
                EnAnaUsoMedicamento, 
                EnAnaUsoMedicamentoDescricao, 
                EnAnaCid10, 
                EnAnaProcedimento, 
                EnAnaQueixaPrincipal, 
                EnAnaHistoriaMolestiaAtual, 
                EnAnaHistoriaPatologicaPregressa, 
                EnAnaHistoriaFamiliar, 
                EnAnaHipoteseSocioEconomica, 
                EnAnaDigitacaoLivre, 
                EnAnaUnidade)
			VALUES (
                :sAtendimento,
                :sDataInicio,
                :sHoraInicio,
                :sDataFim,
                :sHoraFim,
                :sProfissional,
                :sPas,
                :sPad,
                :sFreqCardiaca,
                :sFreqRespiratoria,
                :sTemperatura,
                :sSPO,
                :sHGT,
                :sPeso,
                :sAlergia,
                :sAlergiaDescricao,
                :sDiabetes,
                :sDiabetesDescricao,
                :sHipertensao,
                :sHipertensaoDescricao,
                :sNeoplasia,
                :sNeoplasiaDescricao,
                :sUsoMedicamento,
                :sUsoMedicamentoDescricao,
                :sCid10,
                :sProcedimento,
                :sQueixaPrincipal,
                :sHistoriaMolestiaAtual,
                :sHistoriaPatologicaPregressa,
                :sHistoriaFamiliar,
                :sHipoteseSocioEconomica,
                :sDigitacaoLivre,
                :sUnidade
            )";
			$result = $conn->prepare($sql);

            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId,
                ':sDataInicio' => date('m/d/Y'),
                ':sDataFim' => date('m/d/Y'),
                ':sHoraInicio' => date('H:i'),
                ':sHoraFim' => date('H:i'),

                'sPrevisaoAlta' => $_POST['inputPrevisaoAlta'] == "" ? null : $_POST['inputPrevisaoAlta'], 
                'sTipoInternacao' => $_POST['inputTipoInternacao'] == "" ? null : $_POST['inputTipoInternacao'], 
                'sEspecialidadeLeito' => $_POST['inputEspLeito'] == "" ? null : $_POST['inputEspLeito'],
                'sAla' => $_POST['inputAla'] == "" ? null : $_POST['inputAla'], 
                'sQuarto' => $_POST['inputQuarto'] == "" ? null : $_POST['inputQuarto'], 
                'sLeito' => $_POST['inputLeito'] == "" ? null : $_POST['inputLeito'], 
                ':sProfissional' => $userId,

                ':sPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
                ':sPad' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
                ':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
                ':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
                ':sTemperatura' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
                ':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
                ':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
                ':sPeso' => $_POST['inputPeso'] == "" ? null : $_POST['inputPeso'],

                ':sAlergia' => $_POST['inputAlergia'] == "" ? null : $_POST['inputAlergia'],
                ':sAlergiaDescricao' => $_POST['inputAlergiaDescricao'] == "" ? null : $_POST['inputAlergiaDescricao'],
                ':sDiabetes' => $_POST['inputDiabetes'] == "" ? null : $_POST['inputDiabetes'],
                ':sDiabetesDescricao' => $_POST['inputDiabetesDescricao'] == "" ? null : $_POST['inputDiabetesDescricao'],
                ':sHipertensao' => $_POST['inputHipertensao'] == "" ? null : $_POST['inputHipertensao'],
                ':sHipertensaoDescricao' => $_POST['inputHipertensaoDescricao'] == "" ? null : $_POST['inputHipertensaoDescricao'],
                ':sNeoplasia' => $_POST['inputNeoplasia'] == "" ? null : $_POST['inputNeoplasia'],
                ':sNeoplasiaDescricao' => $_POST['inputNeoplasiaDescricao'] == "" ? null : $_POST['inputNeoplasiaDescricao'],
                ':sUsoMedicamento' => $_POST['inputUsoMedicamento'] == "" ? null : $_POST['inputUsoMedicamento'],
                ':sUsoMedicamentoDescricao' => $_POST['inputUsoMedicamentoDescricao'] == "" ? null : $_POST['inputUsoMedicamentoDescricao'],

                ':sCid10' => $_POST['cmbCId10'] == "" ? null : $_POST['cmbCId10'],
                ':sProcedimento' => $_POST['cmbProcedimento'] == "" ? null : $_POST['cmbProcedimento'],

                ':sQueixaPrincipal' => $_POST['txtareaConteudo1'] == "" ? null : $_POST['txtareaConteudo1'],
                ':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'] == "" ? null : $_POST['txtareaConteudo2'],
                ':sHistoriaPatologicaPregressa' => $_POST['txtareaConteudo3'] == "" ? null : $_POST['txtareaConteudo3'],
                ':sHistoriaFamiliar' => $_POST['txtareaConteudo4'] == "" ? null : $_POST['txtareaConteudo4'],
                ':sHipoteseSocioEconomica' => $_POST['txtareaConteudo5'] == "" ? null : $_POST['txtareaConteudo5'],
                ':sDigitacaoLivre' => $_POST['txtareaConteudo6'] == "" ? null : $_POST['txtareaConteudo6'],

                ':sUnidade' => $_SESSION['UnidadeId'],
            ));
            
        }*/

        if ($iAtendimentoEfetivacaoAlta) {

            $sql = "UPDATE EnfermagemEfetivacaoAlta SET 
                EnEfAAtendimento = :sAtendimento ,
                EnEfADataInicio = :sDataInicio ,
                EnEfAHoraInicio = :sHoraInicio ,
                EnEfADataFim = :sDataFim ,
                EnEfAHoraFim = :sHoraFim ,

                EnEfAPrevisaoAlta = :sPrevisaoAlta,
                EnEfATipoInternacao = :sTipoInternacao,
                EnEfAEspecialidadeLeito = :sEspecialidadeLeito,
                EnEfAAla = :sAla,
                EnEfAQuarto = :sQuarto,
                EnEfALeito = :sLeito,

                EnEfAProfissional = :sProfissional ,
                EnEfAPas = :sTPas ,
                EnEfAPad = :sPad ,
                EnEfAFreqCardiaca = :sFreqCardiaca ,
                EnEfAFreqRespiratoria = :sFreqRespiratoria ,
                EnEfATemperatura = :sTemperatura ,
                EnEfASPO = :sSPO ,
                EnEfAHGT = :sHGT ,
                EnEfAPeso = :sPeso ,
                
                EnEfADataHoraAlta	
                EnEfATipoAlta	
                EnEfACondicaoPaciente	
                EnEfATipoTransporte	
                EnEfADataHoraObito	
                EnEfARegistroObito	
                EnEfAJustificativaAlta	
                EnEfAProfissionalResponsavel	
                EnEfAOrientacaoAlta	
                EnEfAEncaminhamento	
                EnEfAProcedimentoMedicacao	
                EnEfACid10	
                EnEfAProcedimentoRealizado	
                EnEfAObservacao
                
                WHERE EnEfAId = :iAtendimentoEfetivacaoAlta";

            $result = $conn->prepare($sql);
                   
            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId ,
                ':sDataInicio' => date('m/d/Y') ,
                ':sHoraInicio' => date('H:i') ,
                ':sDataFim' => date('m/d/Y') ,
                ':sHoraFim' => date('H:i') ,

                ':sPrevisaoAlta' => $_POST['inputPrevisaoAlta'] == "" ? null : $_POST['inputPrevisaoAlta'], 
                ':sTipoInternacao' => $_POST['inputTipoInternacao'] == "" ? null : $_POST['inputTipoInternacao'], 
                ':sEspecialidadeLeito' => $_POST['inputEspLeito'] == "" ? null : $_POST['inputEspLeito'],
                ':sAla' => $_POST['inputAla'] == "" ? null : $_POST['inputAla'], 
                ':sQuarto' => $_POST['inputQuarto'] == "" ? null : $_POST['inputQuarto'], 
                ':sLeito' => $_POST['inputLeito'] == "" ? null : $_POST['inputLeito'], 

                ':sProfissional' => $userId ,
                ':sTPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
                ':sPad' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
                ':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
                ':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
                ':sTemperatura' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
                ':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
                ':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
                ':sPeso' => $_POST['inputPeso'] == "" ? null : $_POST['inputPeso'],
                
                ':iAtendimentoEfetivacaoAlta' => $iAtendimentoEfetivacaoAlta 
                ));
    
            $_SESSION['msg']['mensagem'] = "Efetivaçaõ de Alta alterada!!!";
            
        } else {

            $sql = "INSERT INTO EnfermagemEfetivacaoAlta 
                (EnEfAAtendimento ,
                EnEfADataInicio ,
                EnEfAHoraInicio ,
                EnEfADataFim ,
                EnEfAHoraFim ,
                EnEfAPrevisaoAlta,
                EnEfATipoInternacao,
                EnEfAEspecialidadeLeito,
                EnEfAAla,
                EnEfAQuarto,
                EnEfALeito,
                EnEfAProfissional ,
                EnEfAPas ,
                EnEfAPad ,
                EnEfAFreqCardiaca ,
                EnEfAFreqRespiratoria ,
                EnEfATemperatura ,
                EnEfASPO ,
                EnEfAHGT ,
                EnEfAPeso ,
                
			VALUES (
                :sAtendimento,
                :sDataInicio,
                :sHoraInicio,
                :sDataFim,
                :sHoraFim,
                :sPrevisaoAlta,
                :sTipoInternacao,
                :sEspecialidadeLeito,
                :sAla,
                :sQuarto,
                :sLeito,
                :sProfissional,
                :sTPas,
                :sPad,
                :sFreqCardiaca,
                :sFreqRespiratoria,
                :sTemperatura,
                :sSPO,
                :sHGT,
                :sPeso,
                
            )";
			$result = $conn->prepare($sql);

            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId ,
                ':sDataInicio' => date('m/d/Y') ,
                ':sHoraInicio' => date('H:i') ,
                ':sDataFim' => date('m/d/Y') ,
                ':sHoraFim' => date('H:i') ,
                ':sPrevisaoAlta' => $_POST['inputPrevisaoAlta'] == "" ? null : $_POST['inputPrevisaoAlta'], 
                ':sTipoInternacao' => $_POST['inputTipoInternacao'] == "" ? null : $_POST['inputTipoInternacao'], 
                ':sEspecialidadeLeito' => $_POST['inputEspLeito'] == "" ? null : $_POST['inputEspLeito'],
                ':sAla' => $_POST['inputAla'] == "" ? null : $_POST['inputAla'], 
                ':sQuarto' => $_POST['inputQuarto'] == "" ? null : $_POST['inputQuarto'], 
                ':sLeito' => $_POST['inputLeito'] == "" ? null : $_POST['inputLeito'], 
                ':sProfissional' => $userId ,
                ':sTPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
                ':sPad' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
                ':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
                ':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
                ':sTemperatura' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
                ':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
                ':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
                ':sPeso' => $_POST['inputPeso'] == "" ? null : $_POST['inputPeso'],
                
            ));
            
        }
        
    } catch (PDOException $e) {

        $_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Efetivação da Alta!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
        
    }

    $_SESSION['iAtendimentoId'] = $iAtendimentoId;
    irpara("atendimentoEfetivacaoAlta.php");

    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Efetivação de Alta</title>

	<?php include_once("head.php"); ?>

    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<!-- /theme JS files -->	

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script type="text/javascript">

		$(document).ready(function() {	
            
            calculaScore()
	     
			$('.salvarAdmissao').on('click', function(e){
				e.preventDefault();

                /*let msg = ''
                let cid10 = $('#cmbCId10').val()
                let procedimento = $('#cmbProcedimento').val()

                switch(msg){
                    case cid10: msg = 'Informe o CID10!';$('#cmbCId10').focus();break
                    case procedimento: msg = 'Informe o Procedimento!';$('#cmbProcedimento').focus();break
                }
                if(msg){
                    $(".box-anamnese").css('display', 'block');
				    $(".box-exameFisico").css('display', 'none');
                    alerta('Campo Obrigatório!', msg, 'error')
                    return
                }*/
		
				$( "#formAtendimentoEfetivacaoAlta" ).submit();
			})

			$(".caracteressummernote1").text(' - ' + (500 - $("#summernote1").val().length) + ' restantes');
			$(".caracteressummernote2").text(' - ' + (500 - $("#summernote2").val().length) + ' restantes');
			$(".caracteressummernote3").text(' - ' + (500 - $("#summernote3").val().length) + ' restantes');
			$(".caracteressummernote4").text(' - ' + (500 - $("#summernote4").val().length) + ' restantes');
			$(".caracteressummernote5").text(' - ' + (500 - $("#summernote5").val().length) + ' restantes');
			$(".caracteressummernote6").text(' - ' + (1000 - $("#summernote6").val().length) + ' restantes');
			$(".caracteresinputAlteracaoOlfato").text(' - ' + (80 - $("#inputAlteracaoOlfato").val().length) + ' restantes');
			$(".caracteresinputAlteracaoAcuidadeVisual").text(' - ' + (80 - $("#inputAlteracaoAcuidadeVisual").val().length) + ' restantes');
			$(".caracteresinputAlteracaoAudicao").text(' - ' + (80 - $("#inputAlteracaoAudicao").val().length) + ' restantes');
			$(".caracteresinputAlteracaoTato").text(' - ' + (80 - $("#inputAlteracaoTato").val().length) + ' restantes');
			$(".caracteresinputAlteracaoPaladar").text(' - ' + (80 - $("#inputAlteracaoPaladar").val().length) + ' restantes');
			$(".caracteresinputAlteracaoDorAguda").text(' - ' + (80 - $("#inputAlteracaoDorAguda").val().length) + ' restantes');
			$(".caracteresinputEdema").text(' - ' + (80 - $("#inputEdema").val().length) + ' restantes');
			$(".caracteresinputCavidadeOral").text(' - ' + (80 - $("#inputCavidadeOral").val().length) + ' restantes');
			$(".caracteresinputPescoco").text(' - ' + (80 - $("#inputPescoco").val().length) + ' restantes');
			$(".caracteresinputTorax").text(' - ' + (80 - $("#inputTorax").val().length) + ' restantes');
			$(".caracteresinputAuscutaPulmonar").text(' - ' + (80 - $("#inputAuscutaPulmonar").val().length) + ' restantes');
			$(".caracteresinputBatimentoCardiaco").text(' - ' + (80 - $("#inputBatimentoCardiaco").val().length) + ' restantes');
			$(".caracteresinputAcessos").text(' - ' + (80 - $("#inputAcessos").val().length) + ' restantes');
			$(".caracteresinputFrequenciaIntestinais").text(' - ' + (80 - $("#inputFrequenciaIntestinais").val().length) + ' restantes');
			$(".caracteresinputFrequenciaEmese").text(' - ' + (80 - $("#inputFrequenciaEmese").val().length) + ' restantes');
			$(".caracteresinputOutrosIntestinais").text(' - ' + (80 - $("#inputOutrosIntestinais").val().length) + ' restantes');
			$(".caracteresinputOutrosUrinarias").text(' - ' + (80 - $("#inputOutrosUrinarias").val().length) + ' restantes');
			$(".caracteresinputQualAlergia").text(' - ' + (80 - $("#inputQualAlergia").val().length) + ' restantes');
			$(".caracteresinputQualDoenca").text(' - ' + (80 - $("#inputQualDoenca").val().length) + ' restantes');
			$(".caracteresinputIncQual").text(' - ' + (80 - $("#inputIncQual").val().length) + ' restantes');

		}); //document.ready

	</script>

    <style>

        .fieldset-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
        }

        .fieldset-border .legend-border {
            font-size: 1.2em !important;
            text-align: left !important;
            width: auto;
            padding: 0 10px;
            border-bottom: none;
        }

	</style>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php
			include_once("menu-left.php");
			include_once("menuLeftSecundarioVenda.php");
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					
					<div class="col-lg-12">
						<form id='dadosPost'>
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
						</form>
						<!-- Basic responsive configuration -->
						<form name="formAtendimentoEfetivacaoAlta" id="formAtendimentoEfetivacaoAlta" method="post">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">

                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-6" style="text-align: left;">

                                        <div class="card-header header-elements-inline">
                                            <h3 class="card-title"><b>ALTA DO PACIENTE</b></h3>
                                        </div>
            
                                    </div>

                                    <div class="col-md-6" style="text-align: right;">

                                        <div class="form-group" style="margin:20px;" >
                                            <button class="btn btn-lg btn-success mr-1 salvarAdmissao" >Salvar</button>
                                            <button type="button" class="btn btn-lg btn-secondary mr-1">Imprimir</button>
                                            <a href='atendimentoHospitalarListagem.php' class='btn btn-basic' role='button'>Voltar</a>

                                        </div>
                                    </div>

                                </div>
                            </div>

								
							</div>

							<div> 
                                <?php include ('atendimentoDadosPacienteHospitalar.php'); ?>
                                <?php include ('atendimentoDadosSinaisVitais.php'); ?>
                            </div>

                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <div class="col-lg-11">	
                                        <button type="button" id="prescricao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg  mr-2 " style="margin-left: -10px;" >Anamnese</button>
                                        <button type="button" id="evolucao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg active" >Exame Físico</button>
                                    </div>
                                </div>                                
                                
                            </div>

                            <!--<div class="box-anamnese" style="display: block;">

                                <div class="card">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title font-weight-bold ">DIAGNÓSTICO PRINCIPAL</h3>  
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 mb-2 row">
                                            
                                            <div class="col-lg-6">
                                                <label>CID <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Procedimento <span class="text-danger">*</span></label>
                                            </div>
                                            
                                            										
                                            <div class="col-lg-6">
                                                <select id="cmbCId10" name="cmbCId10" class="select-search" >
                                                    <option value="">Selecione</option>
                                                    <?php 
                                                        $sql = "SELECT Cid10Id,Cid10Capitulo, Cid10Codigo, Cid10Descricao
                                                                FROM Cid10
                                                                JOIN Situacao on SituaId = Cid10Status
                                                                WHERE SituaChave = 'ATIVO'
                                                                ORDER BY Cid10Codigo ASC";
                                                        $result = $conn->query($sql);
                                                        $row = $result->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($row as $item){
                                                            $seleciona = $item['Cid10Id'] == $rowAnamnese['EnAnaCid10'] ? "selected" : "";
                                                            print('<option value="'.$item['Cid10Id'].'" '. $seleciona .'>'.$item['Cid10Codigo'] . ' - ' . $item['Cid10Descricao'] . ' ' .'</option>');
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select id="cmbProcedimento" name="cmbProcedimento" class="select-search" >
                                                    <option value="">Selecione</option>
                                                    <?php  
                                                        $sql = "SELECT SrVenId,SrVenCodigo, SrVenNome
                                                                FROM ServicoVenda
                                                                WHERE SrVenUnidade = ". $_SESSION['UnidadeId'] ."
                                                                ORDER BY SrVenNome ASC";
                                                        $result = $conn->query($sql);
                                                        $row = $result->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($row as $item) {
                                                            $seleciona = $item['SrVenId'] == $rowAnamnese['EnAnaProcedimento'] ? "selected" : "";
                                                            print('<option value="' . $item['SrVenId'] . '" ' . $seleciona . '>' . $item['SrVenCodigo'] . ' - ' . $item['SrVenNome'] . '</option>');
                                                        }
                                                    ?>
                                                </select>											
                                            </div>

                                        </div>
                                        
                                    </div>



                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title font-weight-bold ">Anamnese</h3>  
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-9">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group"> 
                                                            <a href="#collapse1-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 1. Queixa Principal (QP)</h5></a>   
                                                            <div class="collapse" id="collapse1-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="4" cols="4" maxLength="500" onInput="contarCaracteres(this);"  id="summernote1" name="txtareaConteudo1" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça na queixa principal)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaQueixaPrincipal']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 500 caracteres<span class="caracteressummernote1"></span></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <a href="#collapse2-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 1.1. História da Moléstia Atual (HMA)</h5></a>   
                                                            <div class="collapse" id="collapse2-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="4" cols="4" maxLength="500" onInput="contarCaracteres(this);" id="summernote2" name="txtareaConteudo2" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça nna história da moléstia atual)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaHistoriaMolestiaAtual']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 500 caracteres<span class="caracteressummernote2"></span></small>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <a href="#collapse3-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 1.2. História Patológica Pregressa</h5></a>   
                                                            <div class="collapse" id="collapse3-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="4" cols="4" maxLength="500" onInput="contarCaracteres(this);" id="summernote3" name="txtareaConteudo3" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça na história patológica pregressa)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaHistoriaPatologicaPregressa']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 500 caracteres<span class="caracteressummernote3"></span></small>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <a href="#collapse4-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 1.3. História Familiar</h5></a>   
                                                            <div class="collapse" id="collapse4-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="4" cols="4" maxLength="500" onInput="contarCaracteres(this);" id="summernote4" name="txtareaConteudo4" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça na história familiar)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaHistoriaFamiliar']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 500 caracteres<span class="caracteressummernote4"></span></small>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <a href="#collapse5-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 1.4. História Sócioeconômica</h5></a>   
                                                            <div class="collapse" id="collapse5-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="4" cols="4" maxLength="500" onInput="contarCaracteres(this);" id="summernote5" name="txtareaConteudo5" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça na história sócioeconômica)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaHipoteseSocioEconomica']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 500 caracteres<span class="caracteressummernote5"></span></small>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <a href="#collapse6-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"><h5> 2. Anamnese (Digitação Livre)</h5></a>   
                                                            <div class="collapse" id="collapse6-link" style="">
                                                                <div class="mt-3">
                                                                    <textarea rows="5" cols="5" maxLength="1000" onInput="contarCaracteres(this);" id="summernote6" name="txtareaConteudo6" class="form-control" placeholder="Corpo da anamnese (informe aqui o texto que você queira que apareça na anamnese)" ><?php if (isset($iAtendimentoAnamneseId )) echo $rowAnamnese['EnAnaDigitacaoLivre']; ?></textarea>
                                                                    <small class="text-muted form-text">Max. 1000 caracteres<span class="caracteressummernote6"></span></small>
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                            </div>                                            
                                        </div>                                         
                                    </div>
                                </div>
                                			
                            </div>-->

                            <div class="box-exameFisico" style="display: block;">

                                <div class="card">
                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title font-weight-bold">Exame Físico Pediátrico</h3>  
                                    </div>
                                </div>


                                <!--avaliacao neurologica -->
                                <div class="card card-collapsed">
                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Avaliação Neurológica</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                      
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Ocular</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Verbal</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Motora</label>
                                            </div>
                                            <div class="col-lg-1">
                                                <label>Score</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbOcular" name="cmbOcular" class="form-control-select2" onChange="calculaScore()" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                        $arrayOcular = [ 'ES' => 'ESPONTÂNEA', 'OV' => 'ORDEM VERBAL', 'ED' => 'ESTÍMULO DOLOROSO', 'NR' => 'NÃO RESPONDE' ]; 
                                                        foreach ($arrayOcular as $key => $item) {
                                                            if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPOcular'] ==  $key) ) {																
                                                                print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                            } else {
                                                                print('<option value="' . $key . '">' . $item . '</option>');
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbVerbal" name="cmbVerbal" class="form-control-select2" onChange="calculaScore()" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                        $arrayVerbal = [ 'OR' => 'ORIENTADO', 'CO' => 'CONFUSO', 'PI' => 'PALAVRAS INAPROPRIADAS', 'PC' => 'PALAVRAS INCOMPREENSÍVAS', 'NR' => 'NÃO RESPONDE' ]; 
                                                        foreach ($arrayVerbal as $key => $item) {
                                                            if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPVerbal'] ==  $key) ) {																
                                                                print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                            } else {
                                                                print('<option value="' . $key . '">' . $item . '</option>');
                                                            }
                                                        }
                                                    ?>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbMotora" name="cmbMotora" class="form-control-select2" onChange="calculaScore()" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                        $arrayMotora = [ 'OA' => 'OBEDECE AO COMANDO', 'RO' => 'RETIRA O ESTÍMULO', 'LE' => 'LOCALIZA ESTÍMULO', 'RF' => 'RESPOSTA EM FLEXÃO', 'RE' => 'RESPOSTA EM EXTENSÃO', 'NR' => 'NÃO RESPONDE' ]; 
                                                        foreach ($arrayMotora as $key => $item) {
                                                            if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPMotora'] ==  $key) ) {																
                                                                print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                            } else {
                                                                print('<option value="' . $key . '">' . $item . '</option>');
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-lg-1 " style="margin-top: -5px;">
                                                <input type="text" id="inputScore" name="inputScore" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPScore']; ?>" style="background-color : #FFFFCC; border: 1px groove #ddd !important; text-align:center" readonly>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-4">
                                                <label>Pupilas</label>
                                            </div>
                                            <div class="col-lg-8"></div>
                                            <div class="col-lg-4">
                                                <select id="cmbPupilas" name="cmbPupilas[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IS' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaIsocorica'] == 1 ? 'selected' : ''; ?> >ISOCÓRICAS</option>
                                                    <option value='AN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaAnisocorica'] == 1 ? 'selected' : ''; ?> >ANISOCÓRICAS</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaMidriase'] == 1 ? 'selected' : ''; ?> >MIDRÍASE</option>
                                                    <option value='MO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaMiose'] == 1 ? 'selected' : ''; ?> >MIOSE</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaFotorreagente'] == 1 ? 'selected' : ''; ?> >FOTORREAGENTE</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPupilaParalitica'] == 1 ? 'selected' : ''; ?> >PARALÍTICA</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-8"></div>
                                        </div>

                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-4">
                                                <label>Nível de Consciência</label>
                                            </div>
                                            <div class="col-lg-8"></div>
                                            <div class="col-lg-4">
                                                <select id="cmbNConsciencia" name="cmbNConsciencia[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='LU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaLucido'] == 1 ? 'selected' : ''; ?> >LÚCIDO</option>
                                                    <option value='OR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaOrientado'] == 1 ? 'selected' : ''; ?> >ORIENTADO</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaDesorientado'] == 1 ? 'selected' : ''; ?> >DESORIENTADO</option>
                                                    <option value='SO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaSonolento'] == 1 ? 'selected' : ''; ?> >SONOLENTO</option>
                                                    <option value='AG' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaAgitado'] == 1 ? 'selected' : ''; ?> >AGITADO</option>
                                                    <option value='AT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaAtivo'] == 1 ? 'selected' : ''; ?> >ATIVO</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaHipoativo'] == 1 ? 'selected' : ''; ?> >HIPOATIVO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNivelConscienciaInconsciente'] == 1 ? 'selected' : ''; ?> >INCONSCIENTE</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-8"></div>
                                        </div>                                  

                                    </div>

                                </div>

                                <!--regulacao termica -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Regulação Térmica</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="col-lg-12 mb-3 row">                                           
                                            <div class="col-lg-6">                                               
                                                <select id="cmbRegulacaoTermica" name="cmbRegulacaoTermica[]" class="form-control multiselect-filtering" multiple="multiple">
                                                    <option  value='NO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoTermicaNormoTermico'] == 1 ? 'selected' : ''; ?> >NORMOTÉRMICO</option>
                                                    <option  value='HI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoTermicaHipoTermico'] == 1 ? 'selected' : ''; ?> >HIPOTÉRMICO</option>
                                                    <option  value='FE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoTermicaFebre'] == 1 ? 'selected' : ''; ?> >FEBRE</option>
                                                    <option  value='PI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoTermicaPirexia'] == 1 ? 'selected' : ''; ?> >PIREXIA</option>
                                                    <option  value='SU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoTermicaSudorese'] == 1 ? 'selected' : ''; ?> >SUDORESE</option>                                                   
                                                </select>
                                            </div>
                                        </div>    
                                    </div>

                                </div>

                                <!--Comunicação  -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Comunicação</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="col-lg-12 mb-3 row">                                           
                                            <div class="col-lg-6">                                               
                                                <select id="cmbComunicacao" name="cmbComunicacao[]" class="form-control multiselect-filtering" multiple="multiple">
                                                    <option  value='CC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPComunicacaoClaraCoerente'] == 1 ? 'selected' : ''; ?> >CLARA E COERENTE</option>
                                                    <option  value='PI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPComunicacaoPropriaIdade'] == 1 ? 'selected' : ''; ?> >PRÓPRIA PARA IDADE</option>
                                                    <option  value='NV' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPComunicacaoNaoVerbaliza'] == 1 ? 'selected' : ''; ?> >NÃO VERBALIZA</option>                                                
                                                </select>
                                            </div>
                                        </div>    
                                    </div>

                                </div>

                                <!--sentidos -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Sentidos</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body" >

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Olfato</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Acuidade Visual</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Audição</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbOlfato" name="cmbOlfato" class="form-control-select2" onChange="textoOlfato()" >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPOlfato'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPOlfato'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbAcuidadeVisual" name="cmbAcuidadeVisual" class="form-control-select2" onChange="textoAcuidadeVisual()"  >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcuidadeVisual'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcuidadeVisual'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbAudicao" name="cmbAudicao" class="form-control-select2" onChange="textoAudicao()"  >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAudicao'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAudicao'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-4 "  >
                                                <label class="alteracaoCmbOlfato" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPOlfato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <label class="alteracaoCmbAcuidadeVisual" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAcuidadeVisual'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <label class=" alteracaoCmbAudicao" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAudicao'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbOlfato" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPOlfato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoOlfato" name="inputAlteracaoOlfato" maxLength="80" onInput="contarCaracteres(this);" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPOlfatoAlteracao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoOlfato"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbAcuidadeVisual" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAcuidadeVisual'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoAcuidadeVisual" name="inputAlteracaoAcuidadeVisual" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcuidadeVisualAlteracao']; ?>">											
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoAcuidadeVisual"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbAudicao" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAudicao'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoAudicao" name="inputAlteracaoAudicao" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAudicaoAlteracao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoAudicao"></span></small>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Tato</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Paladar</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Dor Aguda</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbTato" name="cmbTato" class="form-control-select2" onChange="textoTato()">
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPTato'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPTato'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPaladar" name="cmbPaladar" class="form-control-select2" onChange="textoPaladar()" >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPaladar'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPaladar'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbDorAguda" name="cmbDorAguda" class="form-control-select2" onChange="textoDorAguda()" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                        $arrayGrauDependencia = [ 'SD' => 'SEM ALTERAÇÕES', 'DL' => 'DOR LEVE', 'DM' => 'DOR MODERADA', 'DI' => 'DOR INTENSA' ]; 
                                                        foreach ($arrayGrauDependencia as $key => $item) {
                                                            if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPDorAguda'] ==  $key) ) {																
                                                                print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                            } else {
                                                                print('<option value="' . $key . '">' . $item . '</option>');
                                                            }
                                                        }
                                                    ?>

                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-4 row">

                                            <div class="col-lg-4 ">
                                                <label class="alteracaoCmbTato" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPTato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="alteracaoCmbPaladar" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta ) ? ($rowExameFisico['EnAdPPaladar'] == 'AL' ? 'block' : 'none') : 'none';  ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="alteracaoCmbDorAguda" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta ) ? ($rowExameFisico['EnAdPDorAguda'] == 'SD' ? 'none' : 'block') : 'none'; ?>;">Local da dor?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbTato" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPTato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoTato" name="inputAlteracaoTato" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPTatoAlteracao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoTato"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbPaladar" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta ) ? ($rowExameFisico['EnAdPPaladar'] == 'AL' ? 'block' : 'none') : 'none';  ?>;">
                                                    <input type="text" id="inputAlteracaoPaladar" name="inputAlteracaoPaladar" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPaladarAlteracao']; ?>">											
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoPaladar"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbDorAguda" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta ) ? ($rowExameFisico['EnAdPDorAguda'] != 'SD' ? 'block' : 'none') : 'none'; ?>;">                                                    
                                                    <input type="text" id="inputAlteracaoDorAguda" name="inputAlteracaoDorAguda" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDorAgudaLocal']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoDorAguda"></span></small>
                                                </div>
                                            </div>
                                            
                                        </div>

                                    </div>

                                </div>

                                <!--CONDICOES DA PELE -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Condições da Pele</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body row">

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Aspecto</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Turgor/Elasticidade</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Cor</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbAspecto" name="cmbAspecto[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoIntegra'] == 1 ? 'selected' : ''; ?> >INTEGRA</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoCicatriz'] == 1 ? 'selected' : ''; ?> >CICATRIZ</option>
                                                    <option value='IC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoIncisao'] == 1 ? 'selected' : ''; ?> >INCISÃO</option>
                                                    <option value='ES' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoEscoriacao'] == 1 ? 'selected' : ''; ?> >ESCORIAÇÕES</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoDescamacao'] == 1 ? 'selected' : ''; ?> >DESCAMAÇÃO</option>
                                                    <option value='ER' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoErupcao'] == 1 ? 'selected' : ''; ?> >ERUPÇÃO</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoUmida'] == 1 ? 'selected' : ''; ?> >ÚMIDA</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoAspera'] == 1 ? 'selected' : ''; ?> >ÁSPERA</option>
                                                    <option value='EP' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoEspessa'] == 1 ? 'selected' : ''; ?> >ESPESSA</option>
                                                    <option value='FI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoFina'] == 1 ? 'selected' : ''; ?> >FINA</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoFeridaOperatoria'] == 1 ? 'selected' : ''; ?> >FERIDA OPERATÓRIA</option>
                                                    <option value='UD' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleAspectoUlceraDecubito'] == 1 ? 'selected' : ''; ?> >ÚLCERA DE DECÚBITO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbTurgorE" name="cmbTurgorE[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleTurgorSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleTurgorDiminuida'] == 1 ? 'selected' : ''; ?> >DIMINUÍDA</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleTurgorAumentada'] == 1 ? 'selected' : ''; ?> >AUMENTADA</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleTurgorHidratada'] == 1 ? 'selected' : ''; ?> >HIDRATADA</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleTurgorDesidratada'] == 1 ? 'selected' : ''; ?> >DESIDATRADA</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbCor" name="cmbCor[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleCorPalidez'] == 1 ? 'selected' : ''; ?> >PALIDEZ</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleCorCianose'] == 1 ? 'selected' : ''; ?> >CIANOSE</option>
                                                    <option value='IC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleCorIctericia'] == 1 ? 'selected' : ''; ?> >ICTERÍCIA</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleCorSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                </select>
                                            </div>

                                        </div>        
                                        
                                        <fieldset class=" col-lg-12 row fieldset-border " >
                                            <legend class="legend-border">Características</legend>
                                            
                                            <div class="col-lg-3">
                                                <label>Edema</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Hematoma</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Higiene</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Outros</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-3">
                                                <input type="text" id="inputEdema" name="inputEdema" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleEdema']; ?>">
                                                <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputEdema"></span></small>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbHematoma" name="cmbHematoma" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='SI'>SIM</option>
                                                    <option value='NA'>NÃO</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbHigiene" name="cmbHigiene" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='SA'>SATISFATÓRIA</option>
                                                    <option value='PR'>PRECÁRIA</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbCPOutros" name="cmbCPOutros[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='DR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleOutroDreno'] == 1 ? 'selected' : ''; ?> >DRENO</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPeleOutroSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÃO</option>
                                                </select>
                                            </div>
                                            
                                        </fieldset> 

                                    </div>

                                </div>

                                <!--CABECA, PESCOCO, TORAX -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Cabeça, Pescoço e Tórax</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Couro Cabeludo</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Mucosas Oculares</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Auriculares e Nasal</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Cavidade Oral</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-3">
                                                <select id="cmbCouroCabeludo" name="cmbCouroCabeludo[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoIntegro'] == 1 ? 'selected' : ''; ?> >ÍNTEGRO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÃO</option>
                                                    <option value='CE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoCeborreia'] == 1 ? 'selected' : ''; ?> >CEBORRÉIA</option>
                                                    <option value='PE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoPediculose'] == 1 ? 'selected' : ''; ?> >PEDICULOSE</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoCicatriz'] == 1 ? 'selected' : ''; ?> >CICATRIZ</option>
                                                    <option value='LI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCoroCabeludoLimpo'] == 1 ? 'selected' : ''; ?> >LIMPO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbMucOculares" name="cmbMucOculares[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='NO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMucosaOcularNormocromica'] == 1 ? 'selected' : ''; ?> >NORMOCRÔMICAS</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMucosaOcularHipocromica'] == 1 ? 'selected' : ''; ?> >HIPOCRÔMICAS</option>
                                                    <option value='HE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMucosaOcularHipercromica'] == 1 ? 'selected' : ''; ?> >HIPERCRÔMICAS</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbAurNasal" name="cmbAurNasal[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuricularNasalSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='OT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuricularNasalOtorragia'] == 1 ? 'selected' : ''; ?> >OTORRAGIA</option>
                                                    <option value='RI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuricularNasalRinorragia'] == 1 ? 'selected' : ''; ?> >RINORRAGIA</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuricularNasalSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÕES</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbCavOral" name="cmbCavOral[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoCavidadeOral()">
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCavidadeOralSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCavidadeOralComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÃO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCavidadeOralOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-9">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosCavidadeOral" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPCavidadeOralOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-9">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosCavidadeOral" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPCavidadeOralOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputCavidadeOral" name="inputCavidadeOral" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCavidadeOralOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputCavidadeOral"></span></small>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Pescoço</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Tórax</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Respiração</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Auscuta Pulmonar</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-3">
                                                <select id="cmbPescoco" name="cmbPescoco[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoPescoco()">
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPescocoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='LI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPescocoLinfonodoInfartado'] == 1 ? 'selected' : ''; ?> >LINFONODOS INFARTADOS</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPescocoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbTorax" name="cmbTorax[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoTorax()">
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxSimetrico'] == 1 ? 'selected' : ''; ?> >SIMÉTRICO</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxAssimetrico'] == 1 ? 'selected' : ''; ?> >ASSIMÉTRICO</option>
                                                    <option value='DR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxDrreno'] == 1 ? 'selected' : ''; ?> >DRENO</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxUsaMarcapasso'] == 1 ? 'selected' : ''; ?> >USA MARCAPASSO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbRespiracao" name="cmbRespiracao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='EU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoEupneico'] == 1 ? 'selected' : ''; ?>  >EUPNEICO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoDispneico'] == 1 ? 'selected' : ''; ?> >DISPNEICO</option>
                                                    <option value='BR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoBradipneico'] == 1 ? 'selected' : ''; ?> >BRADIPNEICO</option>
                                                    <option value='TA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoTaquipneico'] == 1 ? 'selected' : ''; ?> >TAQUIPNEICO</option>
                                                    <option value='AP' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoApneia'] == 1 ? 'selected' : ''; ?> >APNÉIA</option>
                                                    <option value='TI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoTiragemIntercostal'] == 1 ? 'selected' : ''; ?> >TIRAGEM INTERCOSTAL</option>
                                                    <option value='RF' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoRetracaoFurcula'] == 1 ? 'selected' : ''; ?> >RETRAÇÃO FÚRCULA</option>
                                                    <option value='AN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRespiracaoAletasNasais'] == 1 ? 'selected' : ''; ?> >ALETAS NASAIS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbAusPulmonar" name="cmbAusPulmonar[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoAusPulmonar()" >
                                                    <option value='MV' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarNvfds'] == 1 ? 'selected' : ''; ?> >MVFDS/RA</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarSibilo'] == 1 ? 'selected' : ''; ?> >SIBILOS</option>
                                                    <option value='DR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarCrepto'] == 1 ? 'selected' : ''; ?> >CRÉPTOS</option>
                                                    <option value='RO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarRonco'] == 1 ? 'selected' : ''; ?> >RONCOS</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="col-lg-12 mb-2 row">

                                            <div class="col-lg-3  ">
                                                <label class="outrosPescoco" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPPescocoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosTorax" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPToraxOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosAuscutaPulmonar" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAuscutaPulmonarOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-3 ">
                                                <div class="outrosPescoco" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPPescocoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputPescoco" name="inputPescoco" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPescocoOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputPescoco"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosTorax" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPToraxOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputTorax" name="inputTorax" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPToraxOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputTorax"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosAuscutaPulmonar" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAuscutaPulmonarOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAuscutaPulmonar" name="inputAuscutaPulmonar" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAuscutaPulmonarOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAuscutaPulmonar"></span></small>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!--REGULACAO VASCULAR -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Regulação Vascular</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="card-body" >

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Batimento Cardíaco</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Pulso</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Pressão Arterial</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbBatCardiaco" name="cmbBatCardiaco[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoBatimentoCardiaco()" >
                                                    <option value='BC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoBcnf'] == 1 ? 'selected' : ''; ?> >BCNF+</option>
                                                    <option value='NO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoNormocardico'] == 1 ? 'selected' : ''; ?> >NORMOCÁRDICO</option>
                                                    <option value='TA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoTaquicardico'] == 1 ? 'selected' : ''; ?> >TAQUICÁRDICO</option>
                                                    <option value='BR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoBradicardico'] == 1 ? 'selected' : ''; ?> >BRADICÁRDICO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPulso" name="cmbPulso[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='RE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPulsoRegular'] == 1 ? 'selected' : ''; ?> >REGULAR</option>
                                                    <option value='IR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPulsoIrregular'] == 1 ? 'selected' : ''; ?> >IRREGULAR</option>
                                                    <option value='FI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPulsoFiliforme'] == 1 ? 'selected' : ''; ?> >FILIFORME</option>
                                                    <option value='NP' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPulsoNaoPalpavel'] == 1 ? 'selected' : ''; ?> >NÃO PALPÁVEL</option>
                                                    <option value='CH' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPulsoCheio'] == 1 ? 'selected' : ''; ?> >CHEIO</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPreArterial" name="cmbPreArterial[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='NO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPressaoArterialNormotenso'] == 1 ? 'selected' : ''; ?> >NORMOTENSO</option>
                                                    <option value='HE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPressaoArterialHipertenso'] == 1 ? 'selected' : ''; ?> >HIPERTENSO</option>
                                                    <option value='HO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPressaoArterialHipotenso'] == 1 ? 'selected' : ''; ?> >HIPOTENSO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPPressaoArterialInaldivel'] == 1 ? 'selected' : ''; ?> >INALDÍVEL</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-4 ">
                                                <label class="outrosBatimentoCardiaco" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPBatimentoCardiacoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros</label>
                                            </div>
                                            <div class="col-lg-8">
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 ">
                                                <div class="outrosBatimentoCardiaco" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPBatimentoCardiacoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputBatimentoCardiaco" name="inputBatimentoCardiaco" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPBatimentoCardiacoOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputBatimentoCardiaco"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">											
                                            </div>
                                            
                                            
                                        </div>

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Rede Venosa Periférica</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Perfusão Periférica</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Acessos</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbRVPeriferica" name="cmbRVPeriferica" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='PR'>PRESERVADA</option>
                                                    <option value='CO'>COMPROMETIDA</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPPeriferica" name="cmbPPeriferica" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='NO'>NORMAL</option>
                                                    <option value='LE'>LENTIFICADA</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbAcessos" name="cmbAcessos[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoAcessos()">
                                                    <option value='CE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcessoCentral'] == 1 ? 'selected' : ''; ?> >CENTRAL</option>
                                                    <option value='AV' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcessoAvp'] == 1 ? 'selected' : ''; ?> >AVP</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcessoDisseccao'] == 1 ? 'selected' : ''; ?> >DISSECÇÃO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcessoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">
                                            
                                            <div class="col-lg-8">
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="outrosAcessos" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAcessoOutroDescricao'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros</label>
                                            </div>
                                            
                                            <div class="col-lg-8">											
                                            </div>									
                                            <div class="col-lg-4 ">
                                                <div class="outrosAcessos" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPAcessoOutroDescricao'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAcessos" name="inputAcessos" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAcessoOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAcessos"></span></small>
                                                </div>
                                            </div>
                                            
                                            
                                            
                                        </div>

                                    </div>

                                </div>

                                <!--ABDOMEN E GENITÁLIA -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                        
                                        <h3 class="card-title">Abdômen e Genitália</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-8 row">

                                            <div class="col-lg-6">
                                                <label>Abdômen</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Genitália</label>
                                            </div>                                                
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-6">
                                                <select id="cmbAbdomen" name="cmbAbdomen[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenPlano'] == 1 ? 'selected' : ''; ?> >PLANO</option>
                                                    <option value='GL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenGloboso'] == 1 ? 'selected' : ''; ?> >GLOBOSO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenDistendido'] == 1 ? 'selected' : ''; ?> >DISTENDIDO</option>
                                                    <option value='FL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenPlacido'] == 1 ? 'selected' : ''; ?> >FLÁCIDO</option>
                                                    <option value='EN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenEndurecido'] == 1 ? 'selected' : ''; ?> >ENDURECIDO</option>
                                                    <option value='TI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenTimpanico'] == 1 ? 'selected' : ''; ?> >TIMPÂNICO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenIndolor'] == 1 ? 'selected' : ''; ?> >INDOLOR</option>
                                                    <option value='DO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenDoloroso'] == 1 ? 'selected' : ''; ?> >DOLOROSO</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenAscitico'] == 1 ? 'selected' : ''; ?> >ASCÍTICO</option>
                                                    <option value='GR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAbdomenGravidico'] == 1 ? 'selected' : ''; ?> >GRAVÍDICO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select id="cmbGenitalia" name="cmbGenitalia[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPGenitaliaIntegra'] == 1 ? 'selected' : ''; ?> >ÍNTEGRA</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPGenitaliaComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÓES</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPGenitaliaSangramento'] == 1 ? 'selected' : ''; ?> >SANGRAMENTO</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPGenitaliaSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÃO</option>
                                                </select>											
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!--MEMBROS SUPERIORES E INFERIORES -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Membros Superiores e Membros Inferiores</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 row">

                                            <div class="col-lg-4">
                                                <label>Membros Superiores (MMSS)</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Membros Inferiores (MMII)</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Mobilidade</label>
                                            </div>                                                  
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbMSuperiores" name="cmbMSuperiores[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÕES</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorParesia'] == 1 ? 'selected' : ''; ?> >PARESIA</option>
                                                    <option value='PL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorPlegia'] == 1 ? 'selected' : ''; ?> >PLEGIA</option>
                                                    <option value='PT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorParestesia'] == 1 ? 'selected' : ''; ?> >PARESTESIA</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroSuperiorMovIncoordenado'] == 1 ? 'selected' : ''; ?> >MOVIMENTOS INCOORDENADOS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbMInferiores" name="cmbMInferiores[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÕES</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorParesia'] == 1 ? 'selected' : ''; ?> >PARESIA</option>
                                                    <option value='PL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorPlegia'] == 1 ? 'selected' : ''; ?> >PLEGIA</option>
                                                    <option value='PT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorParestesia'] == 1 ? 'selected' : ''; ?> >PARESTESIA</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMembroInferiorMovIncoordenado'] == 1 ? 'selected' : ''; ?> >MOVIMENTOS INCOORDENADOS</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbMobilidade" name="cmbMobilidade[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMobilidadePreservada'] == 1 ? 'selected' : ''; ?> >PRESERVADA</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMobilidadeDeambula'] == 1 ? 'selected' : ''; ?> >DEAMBULA</option>
                                                    <option value='ND' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMobilidadeNaoDeambula'] == 1 ? 'selected' : ''; ?> >NÃO DEAMBULA</option>
                                                    <option value='PJ' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMobilidadePrejudicada'] == 1 ? 'selected' : ''; ?> >PREJUDICADA</option>
                                                    <option value='AT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPMobilidadeAtaxia'] == 1 ? 'selected' : ''; ?> >ATAXIA</option>
                                                </select>											
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!--ELIMINACOES -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Eliminações</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card-body row">

                                        <div class="col-lg-12 row">

                                            <div class="col-lg-6 row">

                                                <div class="col-lg-12 row mb-2">

                                                    <div class="col-lg-6">
                                                        <label>Intestinais</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label>Êmese</label>
                                                    </div>                                                
                                                    
                                                    <!-- campos -->										
                                                    <div class="col-lg-6">
                                                        <select id="cmbIntestinais" name="cmbIntestinais[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoIntestinais()" >
                                                            <option value='NO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalNormal'] == 1 ? 'selected' : ''; ?> >NORMAL</option>
                                                            <option value='CO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalConstipacao'] == 1 ? 'selected' : ''; ?> >CONSTIPAÇÃO</option>
                                                            <option value='FR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalFrequencia'] == 1 ? 'selected' : ''; ?> >FREQUÊNCIA</option>
                                                            <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalDiarreia'] == 1 ? 'selected' : ''; ?> >DIARRÉIA</option>
                                                            <option value='ME' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalMelena'] == 1 ? 'selected' : ''; ?> >MELENA</option>
                                                            <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <select id="cmbEmese" name="cmbEmese[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                            <option value='NA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPEmeseNao'] == 1 ? 'selected' : ''; ?> >NÃO</option>
                                                            <option value='SI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPEmeseSim'] == 1 ? 'selected' : ''; ?> >SIM</option>
                                                            <option value='HE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPEmeseHematemese'] == 1 ? 'selected' : ''; ?> >HEMATÊMESE</option>
                                                            <option value='FR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPEmeseFrequencia'] == 1 ? 'selected' : ''; ?> >FREQUÊNCIA</option>
                                                        </select>											
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 row mb-2">

                                                   
                                                    <div class="col-lg-6">
                                                        <label class=""     >Frequência <span class="">(Vezes/semana)</span></label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="" >Frequência <span class="">(Vezes/semana)</span></label>
                                                    </div>                                                
                                                    
                                                    <!-- campos -->	
                                                    <div class="col-lg-6">
                                                        <input type="text" id="inputFrequenciaIntestinais" name="inputFrequenciaIntestinais" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalFrequenciaDescricao']; ?>">
                                                        <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputFrequenciaIntestinais"></span></small>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <input type="text" id="inputFrequenciaEmese" name="inputFrequenciaEmese" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPEmeseFrequenciaDescricao']; ?>">											
                                                        <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputFrequenciaEmese"></span></small>
                                                    </div>

                                                </div>

                                                <div class="col-lg-12 row mb-2">

                                                    <div class="col-lg-6">
                                                        <label class="outrosIntestinais" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPIntestinalOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros </label>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6">
                                                        <div class="outrosIntestinais" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPIntestinalOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">                                                            
                                                            <input type="text" id="inputOutrosIntestinais" name="inputOutrosIntestinais" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPIntestinalOutroDescricao']; ?>">
                                                            <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputOutrosIntestinais"></span></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6"></div>

                                                </div>
                                                
                                            </div>

                                            <fieldset class=" col-lg-6 row fieldset-border " >
                                                <legend class="legend-border">Urina</legend>

                                                <div class="col-lg-12 row mb-2">
                                                
                                                    <div class="col-lg-6">
                                                        <label>Urinárias</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label>Aspecto da Urina</label>
                                                    </div>                                                
                                                    
                                                    <!-- campos -->										
                                                    <div class="col-lg-6">
                                                        <select id="cmbUrinarias" name="cmbUrinarias[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoUrinarias()">
                                                            <option value='ES' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaEspontanea'] == 1 ? 'selected' : ''; ?> >ESPONTÂNEA</option>
                                                            <option value='PO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaPoliuria'] == 1 ? 'selected' : ''; ?> >POLIÚRIA</option>
                                                            <option value='RE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaRetencao'] == 1 ? 'selected' : ''; ?> >RETENÇÃO</option>
                                                            <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaIncontinencia'] == 1 ? 'selected' : ''; ?> >INCONTINÊNCIA</option>
                                                            <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaDisuria'] == 1 ? 'selected' : ''; ?> >DISÚRIA</option>
                                                            <option value='OL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaOliguria'] == 1 ? 'selected' : ''; ?> >OLIGÚRIA</option>
                                                            <option value='SD' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaSvd'] == 1 ? 'selected' : ''; ?> >SVD</option>
                                                            <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaSva'] == 1 ? 'selected' : ''; ?> >SVA</option>
                                                            <option value='OU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <select id="cmbAspUrina" name="cmbAspUrina[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                            <option value='CL' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAspectoUrinaClara'] == 1 ? 'selected' : ''; ?> >CLARA</option>
                                                            <option value='AM' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAspectoUrinaAmbar'] == 1 ? 'selected' : ''; ?> >ÂMBAR</option>
                                                            <option value='HE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPAspectoUrinaHematuria'] == 1 ? 'selected' : ''; ?> >HEMATÚRIA</option>
                                                        </select>											
                                                    </div>

                                                </div>

                                                <div class="col-lg-12 row">

                                                    <div class="col-lg-6 outrosUrinarias">
                                                        <label>Outros</label>
                                                    </div>
                                                    <div class="col-lg-6">
                                                    </div>                                                
                                                    
                                                    <!-- campos -->										
                                                    <div class="col-lg-6 outrosUrinarias">
													    <input type="text" id="inputOutrosUrinarias" name="inputOutrosUrinarias" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPUrinariaOutroDescricao']; ?>">
                                                        <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputOutrosUrinarias"></span></small>
                                                    </div>
                                                    <div class="col-lg-6">											
                                                    </div>

                                                </div>
                                                
                                            </fieldset> 
                                        
                                        </div>

                                    </div>



                                </div>

                                <!--NUTRICAO -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Nutrição</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body row">
                                        
                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Nutrição</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Deglutição</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Sucção</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Apetite</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-3">
                                                <select id="cmbNutricao" name="cmbNutricao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='LA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoLactario'] == 1 ? 'selected' : ''; ?> >LACTÁRIO</option>
                                                    <option value='OR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoOral'] == 1 ? 'selected' : ''; ?> >ORAL</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoParental'] == 1 ? 'selected' : ''; ?> >PARENTERAL</option>
                                                    <option value='SG' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoSng'] == 1 ? 'selected' : ''; ?> >SNG</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoSne'] == 1 ? 'selected' : ''; ?> >SNE</option>
                                                    <option value='GT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPNutricaoGgt'] == 1 ? 'selected' : ''; ?> >GGT</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbDegluticao" name="cmbDegluticao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDegluticaoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CD' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDegluticaoComDificuldade'] == 1 ? 'selected' : ''; ?> >COM DIFICULDADE</option>
                                                    <option value='NC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDegluticaoNaoConsDeglutir'] == 1 ? 'selected' : ''; ?> >NÃO CONSEGUE DEGLUTIR</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbSuccao" name="cmbSuccao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSuccaoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CD' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSuccaoComDificuldade'] == 1 ? 'selected' : ''; ?> >COM DIFICULDADE</option>
                                                    <option value='NC' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSuccaoNaoConsegueSugar'] == 1 ? 'selected' : ''; ?> >NÃO CONSEGUE SUGAR</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbApetite" name="cmbApetite[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPApetitePreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPApetiteAumentado'] == 1 ? 'selected' : ''; ?> >AUMENTADO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPApetiteDiminuido'] == 1 ? 'selected' : ''; ?> >DIMINUÍDO</option>
                                                    <option value='PJ' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPApetitePrejudicado'] == 1 ? 'selected' : ''; ?> >PREJUDICADO</option>
                                                </select>
                                            </div>
                                        
                                        </div>

                                    </div>


                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Dentição</h3>
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 mb-2 row">									
                                            <div class="col-lg-3">
                                                <select id="cmbDenticao" name="cmbDenticao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='TO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoTotal'] == 1 ? 'selected' : ''; ?> >TOTAL</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoParcial'] == 1 ? 'selected' : ''; ?> >PARCIAL</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoAusente'] == 1 ? 'selected' : ''; ?> >AUSENTE</option>
                                                    <option value='SU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoSuperior'] == 1 ? 'selected' : ''; ?> >SUPERIOR</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoInferior'] == 1 ? 'selected' : ''; ?> >INFERIOR</option>
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDenticaoProtese'] == 1 ? 'selected' : ''; ?> >PRÓTESE</option>
                                                </select>
                                            </div>
                                        
                                        </div>

                                    </div>

                                </div>

                                <!--SONO E REPOUSO -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Sono e Repouso</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 mb-2 row">

                                            <div class="col-lg-3 mr-3">
                                                <select id="cmbSonoRepouso" name="cmbSonoRepouso[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSonoRepousoPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='DA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSonoRepousoDifAdormecer'] == 1 ? 'selected' : ''; ?> >DIFICULDADE PARA ADORMECER</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSonoRepousoInsonia'] == 1 ? 'selected' : ''; ?> >INSÔNIA</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSonoRepousoUsoMedicacao'] == 1 ? 'selected' : ''; ?> >USO DE MEDICAÇÕES</option>
                                                    <option value='CA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPSonoRepousoCansacoAcordar'] == 1 ? 'selected' : ''; ?> >CANSAÇO AO ACORDAR</option>
                                                </select>
                                            </div>

                                            <fieldset class=" col-lg-8 row fieldset-border " >
                                                <legend class="legend-border">Cuidado Corporal</legend>
                                                
                                                <div class="col-lg-6">
                                                    <label>Higiene Corporal</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Higiene Bucal</label>
                                                </div>                                                
                                                
                                                <!-- campos -->										
                                                <div class="col-lg-6">
                                                    <select id="cmbHigieneCorporal" name="cmbHigieneCorporal" class="form-control-select2" >
                                                        <option value="">Selecione</option>
                                                        <option value='SA'>SATISFATÓRIO</option>
                                                        <option value='IN'>INSATISFATÓRIO</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <select id="cmbHigieneBucal" name="cmbHigieneBucal" class="form-control-select2" >
                                                        <option value="">Selecione</option>
                                                        <option value='SA'>SATISFATÓRIO</option>
                                                        <option value='IN'>INSATISFATÓRIO</option>
                                                    </select>											
                                                </div>
                                                
                                            </fieldset> 



                                        </div>

                                    

                                    </div>

                                </div>

                                <!--REGULACAO IMUNOLOGICA -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Regulação Imunológica</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        
                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-4">
                                                <label>Alergias</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Doenças do Sistema Imunológico</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Calendario Vacinal</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <select id="cmbAlergias" name="cmbAlergias" class="form-control-select2"  onChange="textoAlergias()">
                                                    <option value="">Selecione</option>
                                                    <option value='NA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoAlergia'] == 'NA' ? 'selected' : ''; ?> >NÃO</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoAlergia'] == 'SI' ? 'selected' : ''; ?> >SIM. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbDSImunologico" name="cmbDSImunologico" class="form-control-select2" onChange="teXtoDSImunologico()">
                                                    <option value="">Selecione</option>
                                                    <option value='NA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDoencaSistImunologico'] == 'NA' ? 'selected' : ''; ?> >NÃO</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDoencaSistImunologico'] == 'SI' ? 'selected' : ''; ?> >SIM. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbCalVacinal" name="cmbCalVacinal[]" class="form-control multiselect-filtering" multiple="multiple"  onChange="textoCalVacinal()">
                                                    <option value='CO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCalendarioVacinalCompleto'] == 1 ? 'selected' : ''; ?> >COMPLETO</option>
                                                    <option value='NT' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCalendarioVacinalNaoTrouxe'] == 1 ? 'selected' : ''; ?> >NÃO TROUXE</option>
                                                    <option value='NE' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCalendarioVacinalNaoTem'] == 1 ? 'selected' : ''; ?> >NÃO TEM</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCalendarioVacinalIncompleto'] == 1 ? 'selected' : ''; ?> >INCOMPLETO. QUAL?</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-2 row">

                                            <div class="col-lg-4 ">
                                                <label class="qualAlergia" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPRegulacaoAlergia'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alergia?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="qualDoenca" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPDoencaSistImunologico'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">Qual doença?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="qualCalVacinal" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPCalendarioVacinalIncompleto'] == 'IN' ? 'block' : 'none' ) :  'none' ; ?>;">Incompleto qual?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 ">
                                                <div class="qualAlergia" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPRegulacaoAlergia'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputQualAlergia" name="inputQualAlergia" maxLength="80"class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRegulacaoAlergiaQual']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputQualAlergia"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <div class="qualDoenca" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPDoencaSistImunologico'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputQualDoenca" name="inputQualDoenca" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPDoencaSistImunologicoQual']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputQualDoenca"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <div class="qualCalVacinal" style="display: <?php echo isset($iAtendimentoEfetivacaoAlta) ? ($rowExameFisico['EnAdPCalendarioVacinalIncompleto'] == 'IN' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputIncQual" name="inputIncQual" maxLength="80" class="form-control"  placeholder="" value="<?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPCalendarioVacinalQual']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputIncQual"></span></small>
                                                </div>
                                            </div>
                                            
                                        </div>


                                    </div>

                                </div>

                                <!--AMBIENTE ABRIGO -->
                                <div class="card card-collapsed">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Ambiente/Abrigo</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body row">

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Zona de Moradia</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Coleta de Lixo Regular</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Água Tratada</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Rede de Esgoto</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-3">
                                                <select id="cmbZonaMoradia" name="cmbZonaMoradia[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='UR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPZonaMoradiaUrbana'] == 1 ? 'selected' : ''; ?> >URBANA</option>
                                                    <option value='RU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPZonaMoradiaRural'] == 1 ? 'selected' : ''; ?> >RURAL</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPZonaMoradiaInstitucionalizada'] == 1 ? 'selected' : ''; ?> >INSTITUCIONALIZADA</option>
                                                    <option value='MR' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPZonaMoradiaMoradorRua'] == 1 ? 'selected' : ''; ?> >MORADOR(A) DE RUA</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbCLixoRegular" name="cmbCLixoRegular" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='SI'>SIM</option>
                                                    <option value='NA'>NÃO</option>
                                                    <option value='NS'>NÃO SE APLICA</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbAguaTratada" name="cmbAguaTratada" class="form-control-select2" >
                                                    <option value="">Selecione</option>
                                                    <option value='SI'>SIM</option>
                                                    <option value='NA'>NÃO</option>
                                                    <option value='NS'>NÃO SE APLICA</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbRedeEsgoto" name="cmbRedeEsgoto[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PU' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRedeEsgotoPublica'] == 1 ? 'selected' : ''; ?> >PÚBLICA</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRedeEsgotoFossa'] == 1 ? 'selected' : ''; ?> >FOSSA</option>
                                                    <option value='CA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRedeEsgotoCeuAberto'] == 1 ? 'selected' : ''; ?> >CÉU ABERTO</option>
                                                    <option value='NA' <?php if (isset($iAtendimentoEfetivacaoAlta )) echo $rowExameFisico['EnAdPRedeEsgotoNaoSeAplica'] == 1 ? 'selected' : ''; ?> >NÃO SE APLICA</option>
                                                </select>
                                            </div>
                                        
                                        </div>
                                        
                                    </div>

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Grau de Dependência</h3>
                                    </div>

                                    
                                    <div class="card-body" >
                                        <div class="col-lg-12 text-muted form-text" style="text-align: right;">
                                            0 - INDEPENDENTE    1 - APARELHO   2 - AJUDA DE PESSOAS   3 - DEPENDENTE
                                        </div>
                                        <div class="col-lg-12 row p-3" style="border: 1px groove #ddd !important;">

                                            <?php 
                                                $arrayGrauDependencia = [ 
                                                    '0' => '0 - INDEPENDENTE',
                                                    '1' => '1 - APARELHO',
                                                    '2' => '2 - AJUDA DE PESSOAS',
                                                    '3' => '3 - DEPENDENTE'
                                                ]; 
                                            ?>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">COMER/BEBER</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbComerBeber" name="cmbComerBeber" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPComerBeber'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">VESTIR-SE</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbVestirSe" name="cmbVestirSe" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPVestir'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">SUBIR ESCADAS</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbSubirEscadas" name="cmbSubirEscadas" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>                                                        
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPSubirEscada'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">BANHO</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbBanho" name="cmbBanho" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>                                                        
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPBanho'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">DEAMBULAR</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbDeambular" name="cmbDeambular" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>                                                        
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPDeambular'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-4">ANDAR</label>
                                                    <div class="col-lg-8">
                                                        <select id="cmbAndar" name="cmbAndar" class="form-control form-control-select2">
                                                            <option value="">Selecione</option>                                                        
                                                            <?php
                                                                foreach ($arrayGrauDependencia as $key => $item) {
                                                                    if ( (isset($iAtendimentoEfetivacaoAlta )) && ($rowExameFisico['EnAdPAndar'] ==  $key) ) {																
                                                                        print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $key . '">' . $item . '</option>');
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                
                                    </div>

                                </div>

                            </div>

                            <div class="card">
                                <div class=" card-body row">
                                    <div class="col-lg-12">
                                        <div class="form-group" style="margin-bottom:0px;">
                                            <button class="btn btn-lg btn-success mr-1 salvarAdmissao" >Salvar</button>
                                            <button type="button" class="btn btn-lg btn-secondary mr-1">Imprimir</button>
                                            <a href='atendimentoHospitalarListagem.php' class='btn btn-basic' role='button'>Voltar</a>
                                        </div>
                                    </div>
                                </div>  
                            </div>

							
						</form>	

							<!-- /basic responsive configuration -->
					</div>
					
				</div>				
				
				<!-- /info blocks -->

			</div>
			<!-- /content area -->
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>