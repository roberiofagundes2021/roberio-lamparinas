<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Admissão de Enfermagem';

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
$sql = "SELECT TOP(1) EnAnaId
FROM EnfermagemAnamnese
WHERE EnAnaAtendimento = $iAtendimentoId
ORDER BY EnAnaId DESC";
$result = $conn->query($sql);
$rowAnamnese= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoAnamneseId = $rowAnamnese?$rowAnamnese['EnAnaId']:null;

//exame físico
$sql = "SELECT TOP(1) EnExFId
FROM EnfermagemExameFisico
WHERE EnExFAtendimento = $iAtendimentoId
ORDER BY EnExFId DESC";
$result = $conn->query($sql);
$rowExameFisico= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoExameFisicoId = $rowExameFisico?$rowExameFisico['EnExFId']:null;
//$iAtendimentoExameFisicoId = null;
// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';


//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, A.ProfiUsuario, A.ProfiId as ProfissionalId, A.ProfiNome as ProfissionalNome, PrConNome, B.ProfiCbo as ProfissaoCbo
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
			   AtTriAltura, AtTriImc, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca, AtTriTempAXI, AtClRCor
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoTriagem ON AtTriAtendimento = AtendId
		LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
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
if(isset($iAtendimentoAnamneseId ) && $iAtendimentoAnamneseId ){

	//Essa consulta é para preencher o campo do Atendimento Ambulatorial ao editar
	$sql = "SELECT *
			FROM EnfermagemAnamnese
			WHERE EnAnaId = " . $iAtendimentoAnamneseId ;
	$result = $conn->query($sql);
	$rowAnamnese = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

} 

//se estive editando exame fisico
if(isset($iAtendimentoExameFisicoId ) && $iAtendimentoExameFisicoId ){

	//Essa consulta é para preencher o campo do Atendimento Ambulatorial ao editar
	$sql = "SELECT *
			FROM EnfermagemExameFisico
			WHERE EnExFId = " . $iAtendimentoExameFisicoId ;
	$result = $conn->query($sql);
	$rowExameFisico = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

} 

if (isset($_POST['inputInicio'])) {

    try {
 
        if ($iAtendimentoAnamneseId) {

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
            
        }

        if ($iAtendimentoExameFisicoId) {

            $sql = "UPDATE EnfermagemExameFisico SET 
                EnExFAtendimento = :sAtendimento ,
                EnExFDataInicio = :sDataInicio ,
                EnExFHoraInicio = :sHoraInicio ,
                EnExFDataFim = :sDataFim ,
                EnExFHoraFim = :sHoraFim ,
                EnExFProfissional = :sProfissional ,
                EnExFTPas = :sTPas ,
                EnExFPad = :sPad ,
                EnExFFreqCardiaca = :sFreqCardiaca ,
                EnExFFreqRespiratoria = :sFreqRespiratoria ,
                EnExFTemperatura = :sTemperatura ,
                EnExFSPO = :sSPO ,
                EnExFHGT = :sHGT ,
                EnExPeso = :sPeso ,
                EnExFAlergia = :sAlergia ,
                EnExFAlergiaDescricao = :sAlergiaDescricao ,
                EnExFDiabetes = :sDiabetes ,
                EnExFDiabetesDescricao = :sDiabetesDescricao ,
                EnExFHipertensao = :sHipertensao ,
                EnExFHipertensaoDescricao = :sHipertensaoDescricao ,
                EnExFNeoplasia = :sNeoplasia ,
                EnExFNeoplasiaDescricao = :sNeoplasiaDescricao ,
                EnExFUsoMedicamento = :sUsoMedicamento ,
                EnExFUsoMedicamentoDescricao = :sUsoMedicamentoDescricao ,
                EnExFOcular = :sOcular ,
                EnExFVerbal = :sVerbal ,
                EnExFMotora = :sMotora ,
                EnExFScore = :sScore ,
                EnExFPupilaIsocorica = :sPupilaIsocorica ,
                EnExFPupilaAnisocorica = :sPupilaAnisocorica ,
                EnExFPupilaMidriase = :sPupilaMidriase ,
                EnExFPupilaMiose = :sPupilaMiose ,
                EnExFPupilaFotorreagente = :sPupilaFotorreagente ,
                EnExFPupilaParalitica = :sPupilaParalitica ,
                EnExFNivelConscienciaLucido = :sNivelConscienciaLucido ,
                EnExFNivelConscienciaOrientado = :sNivelConscienciaOrientado ,
                EnExFNivelConscienciaDesorientado = :sNivelConscienciaDesorientado ,
                EnExFNivelConscienciaSonolento = :sNivelConscienciaSonolento ,
                EnExFNivelConscienciaAgitado = :sNivelConscienciaAgitado ,
                EnExFNivelConscienciaAtivo = :sNivelConscienciaAtivo ,
                EnExFNivelConscienciaHipoativo = :sNivelConscienciaHipoativo ,
                EnExFNivelConscienciaInconsciente = :sNivelConscienciaInconsciente ,
                EnExFRegulacaoTermicaNormoTermico = :sRegulacaoTermicaNormoTermico ,
                EnExFRegulacaoTermicaHipoTermico = :sRegulacaoTermicaHipoTermico ,
                EnExFRegulacaoTermicaFebre = :sRegulacaoTermicaFebre ,
                EnExFRegulacaoTermicaPirexia = :sRegulacaoTermicaPirexia ,
                EnExFRegulacaoTermicaSudorese = :sRegulacaoTermicaSudorese ,
                EnExFOlfato = :sOlfato ,
                EnExFOlfatoAlteracao = :sOlfatoAlteracao ,
                EnExFAcuidadeVisual = :sAcuidadeVisual ,
                EnExFAcuidadeVisualAlteracao = :sAcuidadeVisualAlteracao ,
                EnExFAudicao = :sAudicao ,
                EnExFAudicaoAlteracao = :sAudicaoAlteracao ,
                EnExFTato = :sTato ,
                EnExFTatoAlteracao = :sTatoAlteracao ,
                EnExFPaladar = :sPaladar ,
                EnExFPaladarAlteracao = :sPaladarAlteracao ,
                EnExFDorAguda = :sDorAguda ,
                EnExFDorAgudaLocal = :sDorAgudaLocal ,
                EnExFPeleAspectoIntegra = :sPeleAspectoIntegra ,
                EnExFPeleAspectoCicatriz = :sPeleAspectoCicatriz ,
                EnExFPeleAspectoIncisao = :sPeleAspectoIncisao ,
                EnExFPeleAspectoEscoriacao = :sPeleAspectoEscoriacao ,
                EnExFPeleAspectoDescamacao = :sPeleAspectoDescamacao ,
                EnExFPeleAspectoErupcao = :sPeleAspectoErupcao ,
                EnExFPeleAspectoUmida = :sPeleAspectoUmida ,
                EnExFPeleAspectoAspera = :sPeleAspectoAspera ,
                EnExFPeleAspectoEspessa = :sPeleAspectoEspessa ,
                EnExFPeleAspectoFina = :sPeleAspectoFina ,
                EnExFPeleAspectoFeridaOperatoria = :sPeleAspectoFeridaOperatoria ,
                EnExFPeleAspectoUlceraDecubito = :sPeleAspectoUlceraDecubito ,
                EnExFPeleTurgorSemAlteracao = :sPeleTurgorSemAlteracao ,
                EnExFPeleTurgorDiminuida = :sPeleTurgorDiminuida ,
                EnExFPeleTurgorAumentada = :sPeleTurgorAumentada ,
                EnExFPeleTurgorHidratada = :sPeleTurgorHidratada ,
                EnExFPeleTurgorDesidratada = :sPeleTurgorDesidratada ,
                EnExFPeleCorPalidez = :sPeleCorPalidez ,
                EnExFPeleCorCianose = :sPeleCorCianose ,
                EnExFPeleCorIctericia = :sPeleCorIctericia ,
                EnExFPeleCorSemAlteracao = :sPeleCorSemAlteracao ,
                EnExFPeleEdema = :sPeleEdema ,
                EnExFPeleHematoma = :sPeleHematoma ,
                EnExFPeleHigiene = :sPeleHigiene ,
                EnExFPeleOutroDreno = :sPeleOutroDreno ,
                EnExFPeleOutroSecrecao = :sPeleOutroSecrecao ,
                EnExFCoroCabeludoIntegro = :sCoroCabeludoIntegro ,
                EnExFCoroCabeludoComLesao = :sCoroCabeludoComLesao ,
                EnExFCoroCabeludoCeborreia = :sCoroCabeludoCeborreia ,
                EnExFCoroCabeludoPediculose = :sCoroCabeludoPediculose ,
                EnExFCoroCabeludoCicatriz = :sCoroCabeludoCicatriz ,
                EnExFCoroCabeludoLimpo = :sCoroCabeludoLimpo ,
                EnExFMucosaOcularNormocromica = :sMucosaOcularNormocromica ,
                EnExFMucosaOcularHipocromica = :sMucosaOcularHipocromica ,
                EnExFMucosaOcularHipercromica = :sMucosaOcularHipercromica ,
                EnExFAuricularNasalSemAlteracao = :sAuricularNasalSemAlteracao ,
                EnExFAuricularNasalOtorragia = :sAuricularNasalOtorragia ,
                EnExFAuricularNasalRinorragia = :sAuricularNasalRinorragia ,
                EnExFAuricularNasalSecrecao = :sAuricularNasalSecrecao ,
                EnExFCavidadeOralSemAlteracao = :sCavidadeOralSemAlteracao ,
                EnExFCavidadeOralComLesao = :sCavidadeOralComLesao ,
                EnExFCavidadeOralOutro = :sCavidadeOralOutro ,
                EnExFCavidadeOralOutroDescricao = :sCavidadeOralOutroDescricao ,
                EnExFPescocoSemAlteracao = :sPescocoSemAlteracao ,
                EnExFPescocoLinfonodoInfartado = :sPescocoLinfonodoInfartado ,
                EnExFPescocoOutro = :sPescocoOutro ,
                EnExFPescocoOutroDescricao = :sPescocoOutroDescricao ,
                EnExFToraxSemAlteracao = :sToraxSemAlteracao ,
                EnExFToraxSimetrico = :sToraxSimetrico ,
                EnExFToraxAssimetrico = :sToraxAssimetrico ,
                EnExFToraxDrreno = :sToraxDrreno ,
                EnExFToraxUsaMarcapasso = :sToraxUsaMarcapasso ,
                EnExFToraxOutro = :sToraxOutro ,
                EnExFToraxOutroDescricao = :sToraxOutroDescricao ,
                EnExFRespiracaoEupneico = :sRespiracaoEupneico ,
                EnExFRespiracaoDispneico = :sRespiracaoDispneico ,
                EnExFRespiracaoBradipneico = :sRespiracaoBradipneico ,
                EnExFRespiracaoTaquipneico = :sRespiracaoTaquipneico ,
                EnExFRespiracaoApneia = :sRespiracaoApneia ,
                EnExFRespiracaoTiragemIntercostal = :sRespiracaoTiragemIntercostal ,
                EnExFRespiracaoRetracaoFurcula = :sRespiracaoRetracaoFurcula ,
                EnExFRespiracaoAletasNasais = :sRespiracaoAletasNasais ,
                EnExFAuscutaPulmonarNvfds = :sAuscutaPulmonarNvfds ,
                EnExFAuscutaPulmonarSibilo = :sAuscutaPulmonarSibilo ,
                EnExFAuscutaPulmonarCrepto = :sAuscutaPulmonarCrepto ,
                EnExFAuscutaPulmonarRonco = :sAuscutaPulmonarRonco ,
                EnExFAuscutaPulmonarOutro = :sAuscutaPulmonarOutro ,
                EnExFAuscutaPulmonarOutroDescricao = :sAuscutaPulmonarOutroDescricao ,
                EnExFBatimentoCardiacoBcnf = :sBatimentoCardiacoBcnf ,
                EnExFBatimentoCardiacoNormocardico = :sBatimentoCardiacoNormocardico ,
                EnExFBatimentoCardiacoTaquicardico = :sBatimentoCardiacoTaquicardico ,
                EnExFBatimentoCardiacoBradicardico = :sBatimentoCardiacoBradicardico ,
                EnExFBatimentoCardiacoOutro = :sBatimentoCardiacoOutro ,
                EnExFBatimentoCardiacoOutroDescricao = :sBatimentoCardiacoOutroDescricao ,
                EnExFPulsoRegular = :sPulsoRegular ,
                EnExFPulsoIrregular = :sPulsoIrregular ,
                EnExFPulsoFiliforme = :sPulsoFiliforme ,
                EnExFPulsoNaoPalpavel = :sPulsoNaoPalpavel ,
                EnExFPulsoCheio = :sPulsoCheio ,
                EnExFPressaoArterialNormotenso = :sPressaoArterialNormotenso ,
                EnExFPressaoArterialHipertenso = :sPressaoArterialHipertenso ,
                EnExFPressaoArterialHipotenso = :sPressaoArterialHipotenso ,
                EnExFPressaoArterialInaldivel = :sPressaoArterialInaldivel ,
                EnExFRedeVenosaPeriferica = :sRedeVenosaPeriferica ,
                EnExFPerfusaoPeriferica = :sPerfusaoPeriferica ,
                EnExFAcessoCentral = :sAcessoCentral ,
                EnExFAcessoAvp = :sAcessoAvp ,
                EnExFAcessoDisseccao = :sAcessoDisseccao ,
                EnExFAcessoOutro = :sAcessoOutro ,
                EnExFAcessoOutroDescricao = :sAcessoOutroDescricao ,
                EnExFAbdomenPlano = :sAbdomenPlano ,
                EnExFAbdomenGloboso = :sAbdomenGloboso ,
                EnExFAbdomenDistendido = :sAbdomenDistendido ,
                EnExFAbdomenPlacido = :sAbdomenPlacido ,
                EnExFAbdomenEndurecido = :sAbdomenEndurecido ,
                EnExFAbdomenTimpanico = :sAbdomenTimpanico ,
                EnExFAbdomenIndolor = :sAbdomenIndolor ,
                EnExFAbdomenDoloroso = :sAbdomenDoloroso ,
                EnExFAbdomenAscitico = :sAbdomenAscitico ,
                EnExFAbdomenGravidico = :sAbdomenGravidico ,
                EnExFGenitaliaIntegra = :sGenitaliaIntegra ,
                EnExFGenitaliaComLesao = :sGenitaliaComLesao ,
                EnExFGenitaliaSangramento = :sGenitaliaSangramento ,
                EnExFGenitaliaSecrecao = :sGenitaliaSecrecao ,
                EnExFMembroSuperiorPreservado = :sMembroSuperiorPreservado ,
                EnExFMembroSuperiorComLesao = :sMembroSuperiorComLesao ,
                EnExFMembroSuperiorParesia = :sMembroSuperiorParesia ,
                EnExFMembroSuperiorPlegia = :sMembroSuperiorPlegia ,
                EnExFMembroSuperiorParestesia = :sMembroSuperiorParestesia ,
                EnExFMembroSuperiorMovIncoordenado = :sMembroSuperiorMovIncoordenado ,
                EnExFMembroInferiorPreservado = :sMembroInferiorPreservado ,
                EnExFMembroInferiorComLesao = :sMembroInferiorComLesao ,
                EnExFMembroInferiorParesia = :sMembroInferiorParesia ,
                EnExFMembroInferiorPlegia = :sMembroInferiorPlegia ,
                EnExFMembroInferiorParestesia = :sMembroInferiorParestesia ,
                EnExFMembroInferiorMovIncoordenado = :sMembroInferiorMovIncoordenado ,
                EnExFIntestinalNormal = :sIntestinalNormal ,
                EnExFIntestinalConstipacao = :sIntestinalConstipacao ,
                EnExFIntestinalFrequencia = :sIntestinalFrequencia ,
                EnExFIntestinalDiarreia = :sIntestinalDiarreia ,
                EnExFIntestinalMelena = :sIntestinalMelena ,
                EnExFIntestinalOutro = :sIntestinalOutro ,
                EnExFIntestinalFrequenciaDescricao = :sIntestinalFrequenciaDescricao ,
                EnExFIntestinalOutroDescricao = :sIntestinalOutroDescricao ,
                EnExFEmeseNao = :sEmeseNao ,
                EnExFEmeseSim = :sEmeseSim ,
                EnExFEmeseHematemese = :sEmeseHematemese ,
                EnExFEmeseFrequencia = :sEmeseFrequencia ,
                EnExFEmeseFrequenciaDescricao = :sEmeseFrequenciaDescricao ,
                EnExFUrinariaEspontanea = :sUrinariaEspontanea ,
                EnExFUrinariaPoliuria = :sUrinariaPoliuria ,
                EnExFUrinariaRetencao = :sUrinariaRetencao ,
                EnExFUrinariaIncontinencia = :sUrinariaIncontinencia ,
                EnExFUrinariaDisuria = :sUrinariaDisuria ,
                EnExFUrinariaOliguria = :sUrinariaOliguria ,
                EnExFUrinariaSvd = :sUrinariaSvd ,
                EnExFUrinariaSva = :sUrinariaSva ,
                EnExFUrinariaOutro = :sUrinariaOutro ,
                EnExFUrinariaOutroDescricao = :sUrinariaOutroDescricao ,
                EnExFAspectoUrinaClara = :sAspectoUrinaClara ,
                EnExFAspectoUrinaAmbar = :sAspectoUrinaAmbar ,
                EnExFAspectoUrinaHematuria = :sAspectoUrinaHematuria ,
                EnExFNutricaoLactario = :sNutricaoLactario ,
                EnExFNutricaoOral = :sNutricaoOral ,
                EnExFNutricaoParental = :sNutricaoParental ,
                EnExFNutricaoSng = :sNutricaoSng ,
                EnExFNutricaoSne = :sNutricaoSne ,
                EnExFNutricaoGgt = :sNutricaoGgt ,
                EnExFDegluticaoSemAlteracao = :sDegluticaoSemAlteracao ,
                EnExFDegluticaoComDificuldade = :sDegluticaoComDificuldade ,
                EnExFDegluticaoNaoConsDeglutir = :sDegluticaoNaoConsDeglutir ,
                EnExFSuccaoSemAlteracao = :sSuccaoSemAlteracao ,
                EnExFSuccaoComDificuldade = :sSuccaoComDificuldade ,
                EnExFSuccaoNaoConsegueSugar = :sSuccaoNaoConsegueSugar ,
                EnExFApetitePreservado = :sApetitePreservado ,
                EnExFApetiteAumentado = :sApetiteAumentado ,
                EnExFApetiteDiminuido = :sApetiteDiminuido ,
                EnExFApetitePrejudicado = :sApetitePrejudicado ,
                EnExFDenticaoTotal = :sDenticaoTotal ,
                EnExFDenticaoParcial = :sDenticaoParcial ,
                EnExFDenticaoAusente = :sDenticaoAusente ,
                EnExFDenticaoSuperior = :sDenticaoSuperior ,
                EnExFDenticaoInferior = :sDenticaoInferior ,
                EnExFDenticaoProtese = :sDenticaoProtese ,
                EnExFSonoRepousoPreservado = :sSonoRepousoPreservado ,
                EnExFSonoRepousoDifAdormecer = :sSonoRepousoDifAdormecer ,
                EnExFSonoRepousoInsonia = :sSonoRepousoInsonia ,
                EnExFSonoRepousoUsoMedicacao = :sSonoRepousoUsoMedicacao ,
                EnExFSonoRepousoCansacoAcordar = :sSonoRepousoCansacoAcordar ,
                EnExFHigieneCorporal = :sHigieneCorporal ,
                EnExFHigieneBucal = :sHigieneBucal ,
                EnExFRegulacaoAlergia = :sRegulacaoAlergia ,
                EnExFRegulacaoAlergiaQual = :sRegulacaoAlergiaQual ,
                EnExFDoencaSistImunologico = :sDoencaSistImunologico ,
                EnExFDoencaSistImunologicoQual = :sDoencaSistImunologicoQual ,
                EnExFCalendarioVacinalCompleto = :sCalendarioVacinalCompleto ,
                EnExFCalendarioVacinalNaoTrouxe = :sCalendarioVacinalNaoTrouxe ,
                EnExFCalendarioVacinalNaoTem = :sCalendarioVacinalNaoTem ,
                EnExFCalendarioVacinalIncompleto = :sCalendarioVacinalIncompleto ,
                EnExFCalendarioVacinalQual = :sCalendarioVacinalQual ,
                EnExFZonaMoradiaUrbana = :sZonaMoradiaUrbana ,
                EnExFZonaMoradiaRural = :sZonaMoradiaRural ,
                EnExFZonaMoradiaInstitucionalizada = :sZonaMoradiaInstitucionalizada ,
                EnExFZonaMoradiaMoradorRua = :sZonaMoradiaMoradorRua ,
                EnExFColetaLixoRegular = :sColetaLixoRegular ,
                EnExFAguaTratada = :sAguaTratada ,
                EnExFRedeEsgotoPublica = :sRedeEsgotoPublica ,
                EnExFRedeEsgotoFossa = :sRedeEsgotoFossa ,
                EnExFRedeEsgotoCeuAberto = :sRedeEsgotoCeuAberto ,
                EnExFRedeEsgotoNaoSeAplica = :sRedeEsgotoNaoSeAplica ,
                EnExFComerBeber = :sComerBeber ,
                EnExFVestir = :sVestir ,
                EnExFSubirEscada = :sSubirEscada ,
                EnExFBanho = :sBanho ,
                EnExFDeambular = :sDeambular ,
                EnExFAndar = :sAndar ,
                EnExFUnidade = :sUnidade
                WHERE EnExFId = :iAtendimentoExameFisico";

            $result = $conn->prepare($sql);
                   
            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId ,
                ':sDataInicio' => date('m/d/Y') ,
                ':sHoraInicio' => date('H:i') ,
                ':sDataFim' => date('m/d/Y') ,
                ':sHoraFim' => date('H:i') ,
                ':sProfissional' => $userId ,
                ':sTPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
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
                ':sOcular' => $_POST['cmbOcular'] == "" ? null : $_POST['cmbOcular'] ,
                ':sVerbal' => $_POST['cmbVerbal'] == "" ? null : $_POST['cmbVerbal'] ,
                ':sMotora' => $_POST['cmbMotora'] == "" ? null : $_POST['cmbMotora'] ,
                ':sScore' => $_POST['inputScore'] == "" ? null : $_POST['inputScore'] ,
                ':sPupilaIsocorica' => isset($_POST['cmbPupilas']) ? (in_array("IS", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaAnisocorica' => isset($_POST['cmbPupilas']) ? (in_array("AN", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaMidriase' => isset($_POST['cmbPupilas']) ? (in_array("MI", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaMiose' => isset($_POST['cmbPupilas']) ? (in_array("MO", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaFotorreagente' => isset($_POST['cmbPupilas']) ? (in_array("FO", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaParalitica' => isset($_POST['cmbPupilas']) ? (in_array("PA", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaLucido' => isset($_POST['cmbNConsciencia']) ? (in_array("LU", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaOrientado' => isset($_POST['cmbNConsciencia']) ? (in_array("OR", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaDesorientado' => isset($_POST['cmbNConsciencia']) ? (in_array("DE", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaSonolento' => isset($_POST['cmbNConsciencia']) ? (in_array("SO", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaAgitado' => isset($_POST['cmbNConsciencia']) ? (in_array("AG", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaAtivo' => isset($_POST['cmbNConsciencia']) ? (in_array("AT", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaHipoativo' => isset($_POST['cmbNConsciencia']) ? (in_array("HI", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaInconsciente' => isset($_POST['cmbNConsciencia']) ? (in_array("IN", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaNormoTermico' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("NO", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaHipoTermico' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("HI", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaFebre' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("FE", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaPirexia' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("PI", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaSudorese' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("SU", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sOlfato' => $_POST['cmbOlfato'] == "" ? null : $_POST['cmbOlfato'] ,
                ':sOlfatoAlteracao' => $_POST['inputAlteracaoOlfato'] == "" ? null : $_POST['inputAlteracaoOlfato'] ,
                ':sAcuidadeVisual' => $_POST['cmbAcuidadeVisual'] == "" ? null : $_POST['cmbAcuidadeVisual'] ,
                ':sAcuidadeVisualAlteracao' => $_POST['inputAlteracaoAcuidadeVisual'] == "" ? null : $_POST['inputAlteracaoAcuidadeVisual'] ,
                ':sAudicao' => $_POST['cmbAudicao'] == "" ? null : $_POST['cmbAudicao'] ,
                ':sAudicaoAlteracao' => $_POST['inputAlteracaoAudicao'] == "" ? null : $_POST['inputAlteracaoAudicao'] ,
                ':sTato' => $_POST['cmbTato'] == "" ? null : $_POST['cmbTato'] ,
                ':sTatoAlteracao' => $_POST['inputAlteracaoTato'] == "" ? null : $_POST['inputAlteracaoTato'] ,
                ':sPaladar' => $_POST['cmbPaladar'] == "" ? null : $_POST['cmbPaladar'] ,
                ':sPaladarAlteracao' => $_POST['inputAlteracaoPaladar'] == "" ? null : $_POST['inputAlteracaoPaladar'] ,
                ':sDorAguda' => $_POST['cmbDorAguda'] == "" ? null : $_POST['cmbDorAguda'] ,
                ':sDorAgudaLocal' => $_POST['inputAlteracaoDorAguda'] == "" ? null : $_POST['inputAlteracaoDorAguda'] ,
                ':sPeleAspectoIntegra' => isset($_POST['cmbAspecto']) ? (in_array("IN", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoCicatriz' => isset($_POST['cmbAspecto']) ? (in_array("CI", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoIncisao' => isset($_POST['cmbAspecto']) ? (in_array("IC", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoEscoriacao' => isset($_POST['cmbAspecto']) ? (in_array("ES", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoDescamacao' => isset($_POST['cmbAspecto']) ? (in_array("DE", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoErupcao' => isset($_POST['cmbAspecto']) ? (in_array("ER", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoUmida' => isset($_POST['cmbAspecto']) ? (in_array("UM", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoAspera' => isset($_POST['cmbAspecto']) ? (in_array("AS", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoEspessa' => isset($_POST['cmbAspecto']) ? (in_array("EP", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoFina' => isset($_POST['cmbAspecto']) ? (in_array("FI", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoFeridaOperatoria' => isset($_POST['cmbAspecto']) ? (in_array("FO", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoUlceraDecubito' => isset($_POST['cmbAspecto']) ? (in_array("UD", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleTurgorSemAlteracao' => isset($_POST['cmbTurgorE']) ? (in_array("SA", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorDiminuida' => isset($_POST['cmbTurgorE']) ? (in_array("DI", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorAumentada' => isset($_POST['cmbTurgorE']) ? (in_array("AU", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorHidratada' => isset($_POST['cmbTurgorE']) ? (in_array("HI", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorDesidratada' => isset($_POST['cmbTurgorE']) ? (in_array("DE", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleCorPalidez' => isset($_POST['cmbCor']) ? (in_array("PA", $_POST['cmbCor']) ? '1' : '0') : '0' ,
                ':sPeleCorCianose' =>  isset($_POST['cmbCor']) ? (in_array("CI", $_POST['cmbCor']) ? '1' : '0') : '0',
                ':sPeleCorIctericia' => isset($_POST['cmbCor']) ? (in_array("IC", $_POST['cmbCor']) ? '1' : '0') : '0' ,
                ':sPeleCorSemAlteracao' => isset($_POST['cmbCor']) ? (in_array("SA", $_POST['cmbCor']) ? '1' : '0') : '0' ,                
                ':sPeleEdema' => $_POST['inputEdema'] == "" ? null : $_POST['inputEdema'] ,
                ':sPeleHematoma' => $_POST['cmbHematoma'] == "" ? null : $_POST['cmbHematoma'] ,
                ':sPeleHigiene' => $_POST['cmbHigiene'] == "" ? null : $_POST['cmbHigiene'] ,                
                ':sPeleOutroDreno' => isset($_POST['cmbCPOutros']) ? (in_array("DR", $_POST['cmbCPOutros']) ? '1' : '0') : '0' ,
                ':sPeleOutroSecrecao' => isset($_POST['cmbCPOutros']) ? (in_array("SE", $_POST['cmbCPOutros']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoIntegro' => isset($_POST['cmbCouroCabeludo']) ? (in_array("IN", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoComLesao' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CL", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoCeborreia' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CE", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoPediculose' => isset($_POST['cmbCouroCabeludo']) ? (in_array("PE", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoCicatriz' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CI", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoLimpo' => isset($_POST['cmbCouroCabeludo']) ? (in_array("LI", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,                
                ':sMucosaOcularNormocromica' => isset($_POST['cmbMucOculares']) ? (in_array("NO", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,
                ':sMucosaOcularHipocromica' => isset($_POST['cmbMucOculares']) ? (in_array("HI", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,
                ':sMucosaOcularHipercromica' => isset($_POST['cmbMucOculares']) ? (in_array("HE", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,                
                ':sAuricularNasalSemAlteracao' => isset($_POST['cmbAurNasal']) ? (in_array("SA", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalOtorragia' => isset($_POST['cmbAurNasal']) ? (in_array("OT", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalRinorragia' => isset($_POST['cmbAurNasal']) ? (in_array("RI", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalSecrecao' => isset($_POST['cmbAurNasal']) ? (in_array("SE", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,                
                ':sCavidadeOralSemAlteracao' => isset($_POST['cmbCavOral']) ? (in_array("SA", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,
                ':sCavidadeOralComLesao' => isset($_POST['cmbCavOral']) ? (in_array("CL", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,
                ':sCavidadeOralOutro' => isset($_POST['cmbCavOral']) ? (in_array("OU", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,                
                ':sCavidadeOralOutroDescricao' => $_POST['inputCavidadeOral'] == "" ? null : $_POST['inputCavidadeOral'] ,
                ':sPescocoSemAlteracao' => isset($_POST['cmbPescoco']) ? (in_array("SA", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,
                ':sPescocoLinfonodoInfartado' => isset($_POST['cmbPescoco']) ? (in_array("LI", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,
                ':sPescocoOutro' => isset($_POST['cmbPescoco']) ? (in_array("OU", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,                
                ':sPescocoOutroDescricao' => $_POST['inputPescoco'] == "" ? null : $_POST['inputPescoco'] ,
                ':sToraxSemAlteracao' => isset($_POST['cmbTorax']) ? (in_array("SA", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxSimetrico' => isset($_POST['cmbTorax']) ? (in_array("SI", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxAssimetrico' => isset($_POST['cmbTorax']) ? (in_array("AS", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxDrreno' => isset($_POST['cmbTorax']) ? (in_array("DR", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxUsaMarcapasso' => isset($_POST['cmbTorax']) ? (in_array("UM", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxOutro' => isset($_POST['cmbTorax']) ? (in_array("OU", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxOutroDescricao' => $_POST['inputTorax'] == "" ? null : $_POST['inputTorax'] ,
                ':sRespiracaoEupneico' => isset($_POST['cmbRespiracao']) ? (in_array("EU", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoDispneico' => isset($_POST['cmbRespiracao']) ? (in_array("DI", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoBradipneico' => isset($_POST['cmbRespiracao']) ? (in_array("BR", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoTaquipneico' => isset($_POST['cmbRespiracao']) ? (in_array("TA", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoApneia' => isset($_POST['cmbRespiracao']) ? (in_array("AP", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoTiragemIntercostal' => isset($_POST['cmbRespiracao']) ? (in_array("TI", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoRetracaoFurcula' => isset($_POST['cmbRespiracao']) ? (in_array("RF", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoAletasNasais' => isset($_POST['cmbRespiracao']) ? (in_array("AN", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarNvfds' => isset($_POST['cmbAusPulmonar']) ? (in_array("MV", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarSibilo' => isset($_POST['cmbAusPulmonar']) ? (in_array("SI", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarCrepto' => isset($_POST['cmbAusPulmonar']) ? (in_array("DR", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarRonco' => isset($_POST['cmbAusPulmonar']) ? (in_array("RO", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarOutro' => isset($_POST['cmbAusPulmonar']) ? (in_array("OU", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarOutroDescricao' => $_POST['inputAuscutaPulmonar'] == "" ? null : $_POST['inputAuscutaPulmonar'] ,
                ':sBatimentoCardiacoBcnf' => isset($_POST['cmbBatCardiaco']) ? (in_array("BC", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoNormocardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("NO", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoTaquicardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("TA", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoBradicardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("BR", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoOutro' => isset($_POST['cmbBatCardiaco']) ? (in_array("OU", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoOutroDescricao' => $_POST['inputBatimentoCardiaco'] == "" ? null : $_POST['inputBatimentoCardiaco'] ,
                ':sPulsoRegular' => isset($_POST['cmbPulso']) ? (in_array("RE", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoIrregular' => isset($_POST['cmbPulso']) ? (in_array("IR", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoFiliforme' => isset($_POST['cmbPulso']) ? (in_array("FI", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoNaoPalpavel' => isset($_POST['cmbPulso']) ? (in_array("NP", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoCheio' => isset($_POST['cmbPulso']) ? (in_array("CH", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPressaoArterialNormotenso' => isset($_POST['cmbPreArterial']) ? (in_array("NO", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialHipertenso' => isset($_POST['cmbPreArterial']) ? (in_array("HE", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialHipotenso' => isset($_POST['cmbPreArterial']) ? (in_array("HO", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialInaldivel' => isset($_POST['cmbPreArterial']) ? (in_array("IN", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,                
                ':sRedeVenosaPeriferica' => $_POST['cmbRVPeriferica'] == "" ? null : $_POST['cmbRVPeriferica'] ,
                ':sPerfusaoPeriferica' => $_POST['cmbPPeriferica'] == "" ? null : $_POST['cmbPPeriferica'] ,                
                ':sAcessoCentral' => isset($_POST['cmbAcessos']) ? (in_array("CE", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoAvp' => isset($_POST['cmbAcessos']) ? (in_array("AV", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoDisseccao' => isset($_POST['cmbAcessos']) ? (in_array("DI", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoOutro' => isset($_POST['cmbAcessos']) ? (in_array("OU", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,                
                ':sAcessoOutroDescricao' => $_POST['inputAcessos'] == "" ? null : $_POST['inputAcessos'] ,                
                ':sAbdomenPlano' => isset($_POST['cmbAbdomen']) ? (in_array("PL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenGloboso' => isset($_POST['cmbAbdomen']) ? (in_array("GL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenDistendido' => isset($_POST['cmbAbdomen']) ? (in_array("DI", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenPlacido' => isset($_POST['cmbAbdomen']) ? (in_array("FL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenEndurecido' => isset($_POST['cmbAbdomen']) ? (in_array("EN", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenTimpanico' => isset($_POST['cmbAbdomen']) ? (in_array("TI", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenIndolor' => isset($_POST['cmbAbdomen']) ? (in_array("IN", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenDoloroso' => isset($_POST['cmbAbdomen']) ? (in_array("DO", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenAscitico' => isset($_POST['cmbAbdomen']) ? (in_array("AS", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenGravidico' => isset($_POST['cmbAbdomen']) ? (in_array("GR", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,                
                ':sGenitaliaIntegra' => isset($_POST['cmbGenitalia']) ? (in_array("IN", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaComLesao' => isset($_POST['cmbGenitalia']) ? (in_array("CL", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaSangramento' => isset($_POST['cmbGenitalia']) ? (in_array("SA", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaSecrecao' => isset($_POST['cmbGenitalia']) ? (in_array("SE", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,                
                ':sMembroSuperiorPreservado' => isset($_POST['cmbMSuperiores']) ? (in_array("PR", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorComLesao' => isset($_POST['cmbMSuperiores']) ? (in_array("CL", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorParesia' => isset($_POST['cmbMSuperiores']) ? (in_array("PA", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorPlegia' => isset($_POST['cmbMSuperiores']) ? (in_array("PL", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorParestesia' => isset($_POST['cmbMSuperiores']) ? (in_array("PT", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorMovIncoordenado' => isset($_POST['cmbMSuperiores']) ? (in_array("MI", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorPreservado' => isset($_POST['cmbMInferiores']) ? (in_array("PR", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorComLesao' => isset($_POST['cmbMInferiores']) ? (in_array("CL", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorParesia' => isset($_POST['cmbMInferiores']) ? (in_array("PA", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorPlegia' => isset($_POST['cmbMInferiores']) ? (in_array("PL", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorParestesia' => isset($_POST['cmbMInferiores']) ? (in_array("PT", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorMovIncoordenado' => isset($_POST['cmbMInferiores']) ? (in_array("MI", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,                
                ':sIntestinalNormal' => isset($_POST['cmbIntestinais']) ? (in_array("NO", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalConstipacao' => isset($_POST['cmbIntestinais']) ? (in_array("CO", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalFrequencia' => isset($_POST['cmbIntestinais']) ? (in_array("FR", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalDiarreia' => isset($_POST['cmbIntestinais']) ? (in_array("DI", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalMelena' => isset($_POST['cmbIntestinais']) ? (in_array("ME", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalOutro' => isset($_POST['cmbIntestinais']) ? (in_array("OU", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,                
                ':sIntestinalFrequenciaDescricao' => $_POST['inputFrequenciaIntestinais'] == "" ? null : $_POST['inputFrequenciaIntestinais'] ,
                ':sIntestinalOutroDescricao' => $_POST['inputOutrosIntestinais'] == "" ? null : $_POST['inputOutrosIntestinais'] ,               
                ':sEmeseNao' => isset($_POST['cmbEmese']) ? (in_array("NA", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseSim' => isset($_POST['cmbEmese']) ? (in_array("SI", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseHematemese' => isset($_POST['cmbEmese']) ? (in_array("HE", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseFrequencia' => isset($_POST['cmbEmese']) ? (in_array("FR", $_POST['cmbEmese']) ? '1' : '0') : '0' ,                
                ':sEmeseFrequenciaDescricao' => $_POST['inputFrequenciaEmese'] == "" ? null : $_POST['inputFrequenciaEmese'] ,                
                ':sUrinariaEspontanea' => isset($_POST['cmbUrinarias']) ? (in_array("ES", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaPoliuria' => isset($_POST['cmbUrinarias']) ? (in_array("PO", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaRetencao' => isset($_POST['cmbUrinarias']) ? (in_array("RE", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaIncontinencia' => isset($_POST['cmbUrinarias']) ? (in_array("IN", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaDisuria' => isset($_POST['cmbUrinarias']) ? (in_array("DI", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOliguria' => isset($_POST['cmbUrinarias']) ? (in_array("OL", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaSvd' => isset($_POST['cmbUrinarias']) ? (in_array("SD", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaSva' => isset($_POST['cmbUrinarias']) ? (in_array("SA", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOutro' => isset($_POST['cmbUrinarias']) ? (in_array("OU", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOutroDescricao' => $_POST['inputOutrosUrinarias'] == "" ? null : $_POST['inputOutrosUrinarias'] ,
                ':sAspectoUrinaClara' => isset($_POST['cmbAspUrina']) ? (in_array("CL", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sAspectoUrinaAmbar' => isset($_POST['cmbAspUrina']) ? (in_array("AM", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sAspectoUrinaHematuria' => isset($_POST['cmbAspUrina']) ? (in_array("HE", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sNutricaoLactario' => isset($_POST['cmbNutricao']) ? (in_array("LA", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoOral' => isset($_POST['cmbNutricao']) ? (in_array("OR", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoParental' => isset($_POST['cmbNutricao']) ? (in_array("PA", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoSng' => isset($_POST['cmbNutricao']) ? (in_array("SG", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoSne' => isset($_POST['cmbNutricao']) ? (in_array("SE", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoGgt' => isset($_POST['cmbNutricao']) ? (in_array("GT", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,                
                ':sDegluticaoSemAlteracao' => isset($_POST['cmbDegluticao']) ? (in_array("SA", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,
                ':sDegluticaoComDificuldade' => isset($_POST['cmbDegluticao']) ? (in_array("CD", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,
                ':sDegluticaoNaoConsDeglutir' => isset($_POST['cmbDegluticao']) ? (in_array("NC", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,               
                ':sSuccaoSemAlteracao' => isset($_POST['cmbSuccao']) ? (in_array("SA", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,
                ':sSuccaoComDificuldade' => isset($_POST['cmbSuccao']) ? (in_array("CD", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,
                ':sSuccaoNaoConsegueSugar' => isset($_POST['cmbSuccao']) ? (in_array("NC", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,                
                ':sApetitePreservado' => isset($_POST['cmbApetite']) ? (in_array("PR", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetiteAumentado' => isset($_POST['cmbApetite']) ? (in_array("AU", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetiteDiminuido' => isset($_POST['cmbApetite']) ? (in_array("DI", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetitePrejudicado' => isset($_POST['cmbApetite']) ? (in_array("PJ", $_POST['cmbApetite']) ? '1' : '0') : '0' ,                
                ':sDenticaoTotal' => isset($_POST['cmbDenticao']) ? (in_array("TO", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoParcial' => isset($_POST['cmbDenticao']) ? (in_array("PA", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoAusente' => isset($_POST['cmbDenticao']) ? (in_array("AU", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoSuperior' => isset($_POST['cmbDenticao']) ? (in_array("SU", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoInferior' => isset($_POST['cmbDenticao']) ? (in_array("IN", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoProtese' => isset($_POST['cmbDenticao']) ? (in_array("PR", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,                
                ':sSonoRepousoPreservado' => isset($_POST['cmbSonoRepouso']) ? (in_array("PR", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoDifAdormecer' => isset($_POST['cmbSonoRepouso']) ? (in_array("DA", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoInsonia' => isset($_POST['cmbSonoRepouso']) ? (in_array("IN", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoUsoMedicacao' => isset($_POST['cmbSonoRepouso']) ? (in_array("UM", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoCansacoAcordar' => isset($_POST['cmbSonoRepouso']) ? (in_array("CA", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sHigieneCorporal' => $_POST['cmbHigieneCorporal'] == "" ? null : $_POST['cmbHigieneCorporal'] ,
                ':sHigieneBucal' => $_POST['cmbHigieneBucal'] == "" ? null : $_POST['cmbHigieneBucal'] ,
                ':sRegulacaoAlergia' => $_POST['cmbAlergias'] == "" ? null : $_POST['cmbAlergias'] ,
                ':sRegulacaoAlergiaQual' => $_POST['inputQualAlergia'] == "" ? null : $_POST['inputQualAlergia'] ,
                ':sDoencaSistImunologico' => $_POST['cmbDSImunologico'] == "" ? null : $_POST['cmbDSImunologico'] ,
                ':sDoencaSistImunologicoQual' => $_POST['inputQualDoenca'] == "" ? null : $_POST['inputQualDoenca'] ,
                ':sCalendarioVacinalCompleto' => isset($_POST['cmbCalVacinal']) ? (in_array("CO", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalNaoTrouxe' => isset($_POST['cmbCalVacinal']) ? (in_array("NT", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalNaoTem' => isset($_POST['cmbCalVacinal']) ? (in_array("NE", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalIncompleto' => isset($_POST['cmbCalVacinal']) ? (in_array("IN", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalQual' => $_POST['inputIncQual'] == "" ? null : $_POST['inputIncQual'] ,
                ':sZonaMoradiaUrbana' => isset($_POST['cmbZonaMoradia']) ? (in_array("UR", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaRural' => isset($_POST['cmbZonaMoradia']) ? (in_array("RU", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaInstitucionalizada' => isset($_POST['cmbZonaMoradia']) ? (in_array("IN", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaMoradorRua' => isset($_POST['cmbZonaMoradia']) ? (in_array("MR", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sColetaLixoRegular' => $_POST['cmbCLixoRegular'] == "" ? null : $_POST['cmbCLixoRegular'] ,
                ':sAguaTratada' => $_POST['cmbAguaTratada'] == "" ? null : $_POST['cmbAguaTratada'] ,
                ':sRedeEsgotoPublica' => isset($_POST['cmbRedeEsgoto']) ? (in_array("PU", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoFossa' => isset($_POST['cmbRedeEsgoto']) ? (in_array("FO", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoCeuAberto' => isset($_POST['cmbRedeEsgoto']) ? (in_array("CA", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoNaoSeAplica' => isset($_POST['cmbRedeEsgoto']) ? (in_array("NA", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sComerBeber' => $_POST['cmbComerBeber'] == "" ? null : $_POST['cmbComerBeber'] ,
                ':sVestir' => $_POST['cmbVestirSe'] == "" ? null : $_POST['cmbVestirSe'] ,
                ':sSubirEscada' => $_POST['cmbSubirEscadas'] == "" ? null : $_POST['cmbSubirEscadas'] ,
                ':sBanho' => $_POST['cmbBanho'] == "" ? null : $_POST['cmbBanho'] ,
                ':sDeambular' => $_POST['cmbDeambular'] == "" ? null : $_POST['cmbDeambular'] ,
                ':sAndar' => $_POST['cmbAndar'] == "" ? null : $_POST['cmbAndar'] ,
                ':sUnidade' => $_SESSION['UnidadeId'],
                ':iAtendimentoExameFisico' => $iAtendimentoExameFisicoId 
                ));
    
            $_SESSION['msg']['mensagem'] = "Anamnese alterada!!!";
            
        } else {

            $sql = "INSERT INTO EnfermagemExameFisico 
                (EnExFAtendimento ,
                EnExFDataInicio ,
                EnExFHoraInicio ,
                EnExFDataFim ,
                EnExFHoraFim ,
                EnExFProfissional ,
                EnExFTPas ,
                EnExFPad ,
                EnExFFreqCardiaca ,
                EnExFFreqRespiratoria ,
                EnExFTemperatura ,
                EnExFSPO ,
                EnExFHGT ,
                EnExPeso ,
                EnExFAlergia ,
                EnExFAlergiaDescricao ,
                EnExFDiabetes ,
                EnExFDiabetesDescricao ,
                EnExFHipertensao ,
                EnExFHipertensaoDescricao ,
                EnExFNeoplasia ,
                EnExFNeoplasiaDescricao ,
                EnExFUsoMedicamento ,
                EnExFUsoMedicamentoDescricao ,
                EnExFOcular ,
                EnExFVerbal ,
                EnExFMotora ,
                EnExFScore ,
                EnExFPupilaIsocorica ,
                EnExFPupilaAnisocorica ,
                EnExFPupilaMidriase ,
                EnExFPupilaMiose ,
                EnExFPupilaFotorreagente ,
                EnExFPupilaParalitica ,
                EnExFNivelConscienciaLucido ,
                EnExFNivelConscienciaOrientado ,
                EnExFNivelConscienciaDesorientado ,
                EnExFNivelConscienciaSonolento ,
                EnExFNivelConscienciaAgitado ,
                EnExFNivelConscienciaAtivo ,
                EnExFNivelConscienciaHipoativo ,
                EnExFNivelConscienciaInconsciente ,
                EnExFRegulacaoTermicaNormoTermico ,
                EnExFRegulacaoTermicaHipoTermico ,
                EnExFRegulacaoTermicaFebre ,
                EnExFRegulacaoTermicaPirexia ,
                EnExFRegulacaoTermicaSudorese ,
                EnExFOlfato ,
                EnExFOlfatoAlteracao ,
                EnExFAcuidadeVisual ,
                EnExFAcuidadeVisualAlteracao ,
                EnExFAudicao ,
                EnExFAudicaoAlteracao ,
                EnExFTato ,
                EnExFTatoAlteracao ,
                EnExFPaladar ,
                EnExFPaladarAlteracao ,
                EnExFDorAguda ,
                EnExFDorAgudaLocal ,
                EnExFPeleAspectoIntegra ,
                EnExFPeleAspectoCicatriz ,
                EnExFPeleAspectoIncisao ,
                EnExFPeleAspectoEscoriacao ,
                EnExFPeleAspectoDescamacao ,
                EnExFPeleAspectoErupcao ,
                EnExFPeleAspectoUmida ,
                EnExFPeleAspectoAspera ,
                EnExFPeleAspectoEspessa ,
                EnExFPeleAspectoFina ,
                EnExFPeleAspectoFeridaOperatoria ,
                EnExFPeleAspectoUlceraDecubito ,
                EnExFPeleTurgorSemAlteracao ,
                EnExFPeleTurgorDiminuida ,
                EnExFPeleTurgorAumentada ,
                EnExFPeleTurgorHidratada ,
                EnExFPeleTurgorDesidratada ,
                EnExFPeleCorPalidez ,
                EnExFPeleCorCianose ,
                EnExFPeleCorIctericia ,
                EnExFPeleCorSemAlteracao ,
                EnExFPeleEdema ,
                EnExFPeleHematoma ,
                EnExFPeleHigiene ,
                EnExFPeleOutroDreno ,
                EnExFPeleOutroSecrecao ,
                EnExFCoroCabeludoIntegro ,
                EnExFCoroCabeludoComLesao ,
                EnExFCoroCabeludoCeborreia ,
                EnExFCoroCabeludoPediculose ,
                EnExFCoroCabeludoCicatriz ,
                EnExFCoroCabeludoLimpo ,
                EnExFMucosaOcularNormocromica ,
                EnExFMucosaOcularHipocromica ,
                EnExFMucosaOcularHipercromica ,
                EnExFAuricularNasalSemAlteracao ,
                EnExFAuricularNasalOtorragia ,
                EnExFAuricularNasalRinorragia ,
                EnExFAuricularNasalSecrecao ,
                EnExFCavidadeOralSemAlteracao ,
                EnExFCavidadeOralComLesao ,
                EnExFCavidadeOralOutro ,
                EnExFCavidadeOralOutroDescricao ,
                EnExFPescocoSemAlteracao ,
                EnExFPescocoLinfonodoInfartado ,
                EnExFPescocoOutro ,
                EnExFPescocoOutroDescricao ,
                EnExFToraxSemAlteracao ,
                EnExFToraxSimetrico ,
                EnExFToraxAssimetrico ,
                EnExFToraxDrreno ,
                EnExFToraxUsaMarcapasso ,
                EnExFToraxOutro ,
                EnExFToraxOutroDescricao ,
                EnExFRespiracaoEupneico ,
                EnExFRespiracaoDispneico ,
                EnExFRespiracaoBradipneico ,
                EnExFRespiracaoTaquipneico ,
                EnExFRespiracaoApneia ,
                EnExFRespiracaoTiragemIntercostal ,
                EnExFRespiracaoRetracaoFurcula ,
                EnExFRespiracaoAletasNasais ,
                EnExFAuscutaPulmonarNvfds ,
                EnExFAuscutaPulmonarSibilo ,
                EnExFAuscutaPulmonarCrepto ,
                EnExFAuscutaPulmonarRonco ,
                EnExFAuscutaPulmonarOutro ,
                EnExFAuscutaPulmonarOutroDescricao ,
                EnExFBatimentoCardiacoBcnf ,
                EnExFBatimentoCardiacoNormocardico ,
                EnExFBatimentoCardiacoTaquicardico ,
                EnExFBatimentoCardiacoBradicardico ,
                EnExFBatimentoCardiacoOutro ,
                EnExFBatimentoCardiacoOutroDescricao ,
                EnExFPulsoRegular ,
                EnExFPulsoIrregular ,
                EnExFPulsoFiliforme ,
                EnExFPulsoNaoPalpavel ,
                EnExFPulsoCheio ,
                EnExFPressaoArterialNormotenso ,
                EnExFPressaoArterialHipertenso ,
                EnExFPressaoArterialHipotenso ,
                EnExFPressaoArterialInaldivel ,
                EnExFRedeVenosaPeriferica ,
                EnExFPerfusaoPeriferica ,
                EnExFAcessoCentral ,
                EnExFAcessoAvp ,
                EnExFAcessoDisseccao ,
                EnExFAcessoOutro ,
                EnExFAcessoOutroDescricao ,
                EnExFAbdomenPlano ,
                EnExFAbdomenGloboso ,
                EnExFAbdomenDistendido ,
                EnExFAbdomenPlacido ,
                EnExFAbdomenEndurecido ,
                EnExFAbdomenTimpanico ,
                EnExFAbdomenIndolor ,
                EnExFAbdomenDoloroso ,
                EnExFAbdomenAscitico ,
                EnExFAbdomenGravidico ,
                EnExFGenitaliaIntegra ,
                EnExFGenitaliaComLesao ,
                EnExFGenitaliaSangramento ,
                EnExFGenitaliaSecrecao ,
                EnExFMembroSuperiorPreservado ,
                EnExFMembroSuperiorComLesao ,
                EnExFMembroSuperiorParesia ,
                EnExFMembroSuperiorPlegia ,
                EnExFMembroSuperiorParestesia ,
                EnExFMembroSuperiorMovIncoordenado ,
                EnExFMembroInferiorPreservado ,
                EnExFMembroInferiorComLesao ,
                EnExFMembroInferiorParesia ,
                EnExFMembroInferiorPlegia ,
                EnExFMembroInferiorParestesia ,
                EnExFMembroInferiorMovIncoordenado ,
                EnExFIntestinalNormal ,
                EnExFIntestinalConstipacao ,
                EnExFIntestinalFrequencia ,
                EnExFIntestinalDiarreia ,
                EnExFIntestinalMelena ,
                EnExFIntestinalOutro ,
                EnExFIntestinalFrequenciaDescricao ,
                EnExFIntestinalOutroDescricao ,
                EnExFEmeseNao ,
                EnExFEmeseSim ,
                EnExFEmeseHematemese ,
                EnExFEmeseFrequencia ,
                EnExFEmeseFrequenciaDescricao ,
                EnExFUrinariaEspontanea ,
                EnExFUrinariaPoliuria ,
                EnExFUrinariaRetencao ,
                EnExFUrinariaIncontinencia ,
                EnExFUrinariaDisuria ,
                EnExFUrinariaOliguria ,
                EnExFUrinariaSvd ,
                EnExFUrinariaSva ,
                EnExFUrinariaOutro ,
                EnExFUrinariaOutroDescricao ,
                EnExFAspectoUrinaClara ,
                EnExFAspectoUrinaAmbar ,
                EnExFAspectoUrinaHematuria ,
                EnExFNutricaoLactario ,
                EnExFNutricaoOral ,
                EnExFNutricaoParental ,
                EnExFNutricaoSng ,
                EnExFNutricaoSne ,
                EnExFNutricaoGgt ,
                EnExFDegluticaoSemAlteracao ,
                EnExFDegluticaoComDificuldade ,
                EnExFDegluticaoNaoConsDeglutir ,
                EnExFSuccaoSemAlteracao ,
                EnExFSuccaoComDificuldade ,
                EnExFSuccaoNaoConsegueSugar ,
                EnExFApetitePreservado ,
                EnExFApetiteAumentado ,
                EnExFApetiteDiminuido ,
                EnExFApetitePrejudicado ,
                EnExFDenticaoTotal ,
                EnExFDenticaoParcial ,
                EnExFDenticaoAusente ,
                EnExFDenticaoSuperior ,
                EnExFDenticaoInferior ,
                EnExFDenticaoProtese ,
                EnExFSonoRepousoPreservado ,
                EnExFSonoRepousoDifAdormecer ,
                EnExFSonoRepousoInsonia ,
                EnExFSonoRepousoUsoMedicacao ,
                EnExFSonoRepousoCansacoAcordar ,
                EnExFHigieneCorporal ,
                EnExFHigieneBucal ,
                EnExFRegulacaoAlergia ,
                EnExFRegulacaoAlergiaQual ,
                EnExFDoencaSistImunologico ,
                EnExFDoencaSistImunologicoQual ,
                EnExFCalendarioVacinalCompleto ,
                EnExFCalendarioVacinalNaoTrouxe ,
                EnExFCalendarioVacinalNaoTem ,
                EnExFCalendarioVacinalIncompleto ,
                EnExFCalendarioVacinalQual ,
                EnExFZonaMoradiaUrbana ,
                EnExFZonaMoradiaRural ,
                EnExFZonaMoradiaInstitucionalizada ,
                EnExFZonaMoradiaMoradorRua ,
                EnExFColetaLixoRegular ,
                EnExFAguaTratada ,
                EnExFRedeEsgotoPublica ,
                EnExFRedeEsgotoFossa ,
                EnExFRedeEsgotoCeuAberto ,
                EnExFRedeEsgotoNaoSeAplica ,
                EnExFComerBeber ,
                EnExFVestir ,
                EnExFSubirEscada ,
                EnExFBanho ,
                EnExFDeambular ,
                EnExFAndar ,
                EnExFUnidade)
			VALUES (
                :sAtendimento,
                :sDataInicio,
                :sHoraInicio,
                :sDataFim,
                :sHoraFim,
                :sProfissional,
                :sTPas,
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
                :sOcular,
                :sVerbal,
                :sMotora,
                :sScore,
                :sPupilaIsocorica,
                :sPupilaAnisocorica,
                :sPupilaMidriase,
                :sPupilaMiose,
                :sPupilaFotorreagente,
                :sPupilaParalitica,
                :sNivelConscienciaLucido,
                :sNivelConscienciaOrientado,
                :sNivelConscienciaDesorientado,
                :sNivelConscienciaSonolento,
                :sNivelConscienciaAgitado,
                :sNivelConscienciaAtivo,
                :sNivelConscienciaHipoativo,
                :sNivelConscienciaInconsciente,
                :sRegulacaoTermicaNormoTermico,
                :sRegulacaoTermicaHipoTermico,
                :sRegulacaoTermicaFebre,
                :sRegulacaoTermicaPirexia,
                :sRegulacaoTermicaSudorese,
                :sOlfato,
                :sOlfatoAlteracao,
                :sAcuidadeVisual,
                :sAcuidadeVisualAlteracao,
                :sAudicao,
                :sAudicaoAlteracao,
                :sTato,
                :sTatoAlteracao,
                :sPaladar,
                :sPaladarAlteracao,
                :sDorAguda,
                :sDorAgudaLocal,
                :sPeleAspectoIntegra,
                :sPeleAspectoCicatriz,
                :sPeleAspectoIncisao,
                :sPeleAspectoEscoriacao,
                :sPeleAspectoDescamacao,
                :sPeleAspectoErupcao,
                :sPeleAspectoUmida,
                :sPeleAspectoAspera,
                :sPeleAspectoEspessa,
                :sPeleAspectoFina,
                :sPeleAspectoFeridaOperatoria,
                :sPeleAspectoUlceraDecubito,
                :sPeleTurgorSemAlteracao,
                :sPeleTurgorDiminuida,
                :sPeleTurgorAumentada,
                :sPeleTurgorHidratada,
                :sPeleTurgorDesidratada,
                :sPeleCorPalidez,
                :sPeleCorCianose,
                :sPeleCorIctericia,
                :sPeleCorSemAlteracao,
                :sPeleEdema,
                :sPeleHematoma,
                :sPeleHigiene,
                :sPeleOutroDreno,
                :sPeleOutroSecrecao,
                :sCoroCabeludoIntegro,
                :sCoroCabeludoComLesao,
                :sCoroCabeludoCeborreia,
                :sCoroCabeludoPediculose,
                :sCoroCabeludoCicatriz,
                :sCoroCabeludoLimpo,
                :sMucosaOcularNormocromica,
                :sMucosaOcularHipocromica,
                :sMucosaOcularHipercromica,
                :sAuricularNasalSemAlteracao,
                :sAuricularNasalOtorragia,
                :sAuricularNasalRinorragia,
                :sAuricularNasalSecrecao,
                :sCavidadeOralSemAlteracao,
                :sCavidadeOralComLesao,
                :sCavidadeOralOutro,
                :sCavidadeOralOutroDescricao,
                :sPescocoSemAlteracao,
                :sPescocoLinfonodoInfartado,
                :sPescocoOutro,
                :sPescocoOutroDescricao,
                :sToraxSemAlteracao,
                :sToraxSimetrico,
                :sToraxAssimetrico,
                :sToraxDrreno,
                :sToraxUsaMarcapasso,
                :sToraxOutro,
                :sToraxOutroDescricao,
                :sRespiracaoEupneico,
                :sRespiracaoDispneico,
                :sRespiracaoBradipneico,
                :sRespiracaoTaquipneico,
                :sRespiracaoApneia,
                :sRespiracaoTiragemIntercostal,
                :sRespiracaoRetracaoFurcula,
                :sRespiracaoAletasNasais,
                :sAuscutaPulmonarNvfds,
                :sAuscutaPulmonarSibilo,
                :sAuscutaPulmonarCrepto,
                :sAuscutaPulmonarRonco,
                :sAuscutaPulmonarOutro,
                :sAuscutaPulmonarOutroDescricao,
                :sBatimentoCardiacoBcnf,
                :sBatimentoCardiacoNormocardico,
                :sBatimentoCardiacoTaquicardico,
                :sBatimentoCardiacoBradicardico,
                :sBatimentoCardiacoOutro,
                :sBatimentoCardiacoOutroDescricao,
                :sPulsoRegular,
                :sPulsoIrregular,
                :sPulsoFiliforme,
                :sPulsoNaoPalpavel,
                :sPulsoCheio,
                :sPressaoArterialNormotenso,
                :sPressaoArterialHipertenso,
                :sPressaoArterialHipotenso,
                :sPressaoArterialInaldivel,
                :sRedeVenosaPeriferica,
                :sPerfusaoPeriferica,
                :sAcessoCentral,
                :sAcessoAvp,
                :sAcessoDisseccao,
                :sAcessoOutro,
                :sAcessoOutroDescricao,
                :sAbdomenPlano,
                :sAbdomenGloboso,
                :sAbdomenDistendido,
                :sAbdomenPlacido,
                :sAbdomenEndurecido,
                :sAbdomenTimpanico,
                :sAbdomenIndolor,
                :sAbdomenDoloroso,
                :sAbdomenAscitico,
                :sAbdomenGravidico,
                :sGenitaliaIntegra,
                :sGenitaliaComLesao,
                :sGenitaliaSangramento,
                :sGenitaliaSecrecao,
                :sMembroSuperiorPreservado,
                :sMembroSuperiorComLesao,
                :sMembroSuperiorParesia,
                :sMembroSuperiorPlegia,
                :sMembroSuperiorParestesia,
                :sMembroSuperiorMovIncoordenado,
                :sMembroInferiorPreservado,
                :sMembroInferiorComLesao,
                :sMembroInferiorParesia,
                :sMembroInferiorPlegia,
                :sMembroInferiorParestesia,
                :sMembroInferiorMovIncoordenado,
                :sIntestinalNormal,
                :sIntestinalConstipacao,
                :sIntestinalFrequencia,
                :sIntestinalDiarreia,
                :sIntestinalMelena,
                :sIntestinalOutro,
                :sIntestinalFrequenciaDescricao,
                :sIntestinalOutroDescricao,
                :sEmeseNao,
                :sEmeseSim,
                :sEmeseHematemese,
                :sEmeseFrequencia,
                :sEmeseFrequenciaDescricao,
                :sUrinariaEspontanea,
                :sUrinariaPoliuria,
                :sUrinariaRetencao,
                :sUrinariaIncontinencia,
                :sUrinariaDisuria,
                :sUrinariaOliguria,
                :sUrinariaSvd,
                :sUrinariaSva,
                :sUrinariaOutro,
                :sUrinariaOutroDescricao,
                :sAspectoUrinaClara,
                :sAspectoUrinaAmbar,
                :sAspectoUrinaHematuria,
                :sNutricaoLactario,
                :sNutricaoOral,
                :sNutricaoParental,
                :sNutricaoSng,
                :sNutricaoSne,
                :sNutricaoGgt,
                :sDegluticaoSemAlteracao,
                :sDegluticaoComDificuldade,
                :sDegluticaoNaoConsDeglutir,
                :sSuccaoSemAlteracao,
                :sSuccaoComDificuldade,
                :sSuccaoNaoConsegueSugar,
                :sApetitePreservado,
                :sApetiteAumentado,
                :sApetiteDiminuido,
                :sApetitePrejudicado,
                :sDenticaoTotal,
                :sDenticaoParcial,
                :sDenticaoAusente,
                :sDenticaoSuperior,
                :sDenticaoInferior,
                :sDenticaoProtese,
                :sSonoRepousoPreservado,
                :sSonoRepousoDifAdormecer,
                :sSonoRepousoInsonia,
                :sSonoRepousoUsoMedicacao,
                :sSonoRepousoCansacoAcordar,
                :sHigieneCorporal,
                :sHigieneBucal,
                :sRegulacaoAlergia,
                :sRegulacaoAlergiaQual,
                :sDoencaSistImunologico,
                :sDoencaSistImunologicoQual,
                :sCalendarioVacinalCompleto,
                :sCalendarioVacinalNaoTrouxe,
                :sCalendarioVacinalNaoTem,
                :sCalendarioVacinalIncompleto,
                :sCalendarioVacinalQual,
                :sZonaMoradiaUrbana,
                :sZonaMoradiaRural,
                :sZonaMoradiaInstitucionalizada,
                :sZonaMoradiaMoradorRua,
                :sColetaLixoRegular,
                :sAguaTratada,
                :sRedeEsgotoPublica,
                :sRedeEsgotoFossa,
                :sRedeEsgotoCeuAberto,
                :sRedeEsgotoNaoSeAplica,
                :sComerBeber,
                :sVestir,
                :sSubirEscada,
                :sBanho,
                :sDeambular,
                :sAndar,
                :sUnidade
            )";
			$result = $conn->prepare($sql);

            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId ,
                ':sDataInicio' => date('m/d/Y') ,
                ':sHoraInicio' => date('H:i') ,
                ':sDataFim' => date('m/d/Y') ,
                ':sHoraFim' => date('H:i') ,
                ':sProfissional' => $userId ,
                ':sTPas' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
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
                ':sOcular' => $_POST['cmbOcular'] == "" ? null : $_POST['cmbOcular'] ,
                ':sVerbal' => $_POST['cmbVerbal'] == "" ? null : $_POST['cmbVerbal'] ,
                ':sMotora' => $_POST['cmbMotora'] == "" ? null : $_POST['cmbMotora'] ,
                ':sScore' => $_POST['inputScore'] == "" ? null : $_POST['inputScore'] ,
                ':sPupilaIsocorica' => isset($_POST['cmbPupilas']) ? (in_array("IS", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaAnisocorica' => isset($_POST['cmbPupilas']) ? (in_array("AN", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaMidriase' => isset($_POST['cmbPupilas']) ? (in_array("MI", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaMiose' => isset($_POST['cmbPupilas']) ? (in_array("MO", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaFotorreagente' => isset($_POST['cmbPupilas']) ? (in_array("FO", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sPupilaParalitica' => isset($_POST['cmbPupilas']) ? (in_array("PA", $_POST['cmbPupilas']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaLucido' => isset($_POST['cmbNConsciencia']) ? (in_array("LU", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaOrientado' => isset($_POST['cmbNConsciencia']) ? (in_array("OR", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaDesorientado' => isset($_POST['cmbNConsciencia']) ? (in_array("DE", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaSonolento' => isset($_POST['cmbNConsciencia']) ? (in_array("SO", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaAgitado' => isset($_POST['cmbNConsciencia']) ? (in_array("AG", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaAtivo' => isset($_POST['cmbNConsciencia']) ? (in_array("AT", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaHipoativo' => isset($_POST['cmbNConsciencia']) ? (in_array("HI", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sNivelConscienciaInconsciente' => isset($_POST['cmbNConsciencia']) ? (in_array("IN", $_POST['cmbNConsciencia']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaNormoTermico' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("NO", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaHipoTermico' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("HI", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaFebre' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("FE", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaPirexia' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("PI", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sRegulacaoTermicaSudorese' => isset($_POST['cmbRegulacaoTermica']) ? (in_array("SU", $_POST['cmbRegulacaoTermica']) ? '1' : '0') : '0' ,
                ':sOlfato' => $_POST['cmbOlfato'] == "" ? null : $_POST['cmbOlfato'] ,
                ':sOlfatoAlteracao' => $_POST['inputAlteracaoOlfato'] == "" ? null : $_POST['inputAlteracaoOlfato'] ,
                ':sAcuidadeVisual' => $_POST['cmbAcuidadeVisual'] == "" ? null : $_POST['cmbAcuidadeVisual'] ,
                ':sAcuidadeVisualAlteracao' => $_POST['inputAlteracaoAcuidadeVisual'] == "" ? null : $_POST['inputAlteracaoAcuidadeVisual'] ,
                ':sAudicao' => $_POST['cmbAudicao'] == "" ? null : $_POST['cmbAudicao'] ,
                ':sAudicaoAlteracao' => $_POST['inputAlteracaoAudicao'] == "" ? null : $_POST['inputAlteracaoAudicao'] ,
                ':sTato' => $_POST['cmbTato'] == "" ? null : $_POST['cmbTato'] ,
                ':sTatoAlteracao' => $_POST['inputAlteracaoTato'] == "" ? null : $_POST['inputAlteracaoTato'] ,
                ':sPaladar' => $_POST['cmbPaladar'] == "" ? null : $_POST['cmbPaladar'] ,
                ':sPaladarAlteracao' => $_POST['inputAlteracaoPaladar'] == "" ? null : $_POST['inputAlteracaoPaladar'] ,
                ':sDorAguda' => $_POST['cmbDorAguda'] == "" ? null : $_POST['cmbDorAguda'] ,
                ':sDorAgudaLocal' => $_POST['inputAlteracaoDorAguda'] == "" ? null : $_POST['inputAlteracaoDorAguda'] ,
                ':sPeleAspectoIntegra' => isset($_POST['cmbAspecto']) ? (in_array("IN", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoCicatriz' => isset($_POST['cmbAspecto']) ? (in_array("CI", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoIncisao' => isset($_POST['cmbAspecto']) ? (in_array("IC", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoEscoriacao' => isset($_POST['cmbAspecto']) ? (in_array("ES", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoDescamacao' => isset($_POST['cmbAspecto']) ? (in_array("DE", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoErupcao' => isset($_POST['cmbAspecto']) ? (in_array("ER", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoUmida' => isset($_POST['cmbAspecto']) ? (in_array("UM", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoAspera' => isset($_POST['cmbAspecto']) ? (in_array("AS", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoEspessa' => isset($_POST['cmbAspecto']) ? (in_array("EP", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoFina' => isset($_POST['cmbAspecto']) ? (in_array("FI", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoFeridaOperatoria' => isset($_POST['cmbAspecto']) ? (in_array("FO", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleAspectoUlceraDecubito' => isset($_POST['cmbAspecto']) ? (in_array("UD", $_POST['cmbAspecto']) ? '1' : '0') : '0' ,
                ':sPeleTurgorSemAlteracao' => isset($_POST['cmbTurgorE']) ? (in_array("SA", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorDiminuida' => isset($_POST['cmbTurgorE']) ? (in_array("DI", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorAumentada' => isset($_POST['cmbTurgorE']) ? (in_array("AU", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorHidratada' => isset($_POST['cmbTurgorE']) ? (in_array("HI", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleTurgorDesidratada' => isset($_POST['cmbTurgorE']) ? (in_array("DE", $_POST['cmbTurgorE']) ? '1' : '0') : '0' ,
                ':sPeleCorPalidez' => isset($_POST['cmbCor']) ? (in_array("PA", $_POST['cmbCor']) ? '1' : '0') : '0' ,
                ':sPeleCorCianose' =>  isset($_POST['cmbCor']) ? (in_array("CI", $_POST['cmbCor']) ? '1' : '0') : '0',
                ':sPeleCorIctericia' => isset($_POST['cmbCor']) ? (in_array("IC", $_POST['cmbCor']) ? '1' : '0') : '0' ,
                ':sPeleCorSemAlteracao' => isset($_POST['cmbCor']) ? (in_array("SA", $_POST['cmbCor']) ? '1' : '0') : '0' ,                
                ':sPeleEdema' => $_POST['inputEdema'] == "" ? null : $_POST['inputEdema'] ,
                ':sPeleHematoma' => $_POST['cmbHematoma'] == "" ? null : $_POST['cmbHematoma'] ,
                ':sPeleHigiene' => $_POST['cmbHigiene'] == "" ? null : $_POST['cmbHigiene'] ,                
                ':sPeleOutroDreno' => isset($_POST['cmbCPOutros']) ? (in_array("DR", $_POST['cmbCPOutros']) ? '1' : '0') : '0' ,
                ':sPeleOutroSecrecao' => isset($_POST['cmbCPOutros']) ? (in_array("SE", $_POST['cmbCPOutros']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoIntegro' => isset($_POST['cmbCouroCabeludo']) ? (in_array("IN", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoComLesao' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CL", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoCeborreia' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CE", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoPediculose' => isset($_POST['cmbCouroCabeludo']) ? (in_array("PE", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoCicatriz' => isset($_POST['cmbCouroCabeludo']) ? (in_array("CI", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,
                ':sCoroCabeludoLimpo' => isset($_POST['cmbCouroCabeludo']) ? (in_array("LI", $_POST['cmbCouroCabeludo']) ? '1' : '0') : '0' ,                
                ':sMucosaOcularNormocromica' => isset($_POST['cmbMucOculares']) ? (in_array("NO", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,
                ':sMucosaOcularHipocromica' => isset($_POST['cmbMucOculares']) ? (in_array("HI", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,
                ':sMucosaOcularHipercromica' => isset($_POST['cmbMucOculares']) ? (in_array("HE", $_POST['cmbMucOculares']) ? '1' : '0') : '0' ,                
                ':sAuricularNasalSemAlteracao' => isset($_POST['cmbAurNasal']) ? (in_array("SA", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalOtorragia' => isset($_POST['cmbAurNasal']) ? (in_array("OT", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalRinorragia' => isset($_POST['cmbAurNasal']) ? (in_array("RI", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,
                ':sAuricularNasalSecrecao' => isset($_POST['cmbAurNasal']) ? (in_array("SE", $_POST['cmbAurNasal']) ? '1' : '0') : '0' ,                
                ':sCavidadeOralSemAlteracao' => isset($_POST['cmbCavOral']) ? (in_array("SA", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,
                ':sCavidadeOralComLesao' => isset($_POST['cmbCavOral']) ? (in_array("CL", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,
                ':sCavidadeOralOutro' => isset($_POST['cmbCavOral']) ? (in_array("OU", $_POST['cmbCavOral']) ? '1' : '0') : '0' ,                
                ':sCavidadeOralOutroDescricao' => $_POST['inputCavidadeOral'] == "" ? null : $_POST['inputCavidadeOral'] ,
                ':sPescocoSemAlteracao' => isset($_POST['cmbPescoco']) ? (in_array("SA", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,
                ':sPescocoLinfonodoInfartado' => isset($_POST['cmbPescoco']) ? (in_array("LI", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,
                ':sPescocoOutro' => isset($_POST['cmbPescoco']) ? (in_array("OU", $_POST['cmbPescoco']) ? '1' : '0') : '0' ,                
                ':sPescocoOutroDescricao' => $_POST['inputPescoco'] == "" ? null : $_POST['inputPescoco'] ,
                ':sToraxSemAlteracao' => isset($_POST['cmbTorax']) ? (in_array("SA", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxSimetrico' => isset($_POST['cmbTorax']) ? (in_array("SI", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxAssimetrico' => isset($_POST['cmbTorax']) ? (in_array("AS", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxDrreno' => isset($_POST['cmbTorax']) ? (in_array("DR", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxUsaMarcapasso' => isset($_POST['cmbTorax']) ? (in_array("UM", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxOutro' => isset($_POST['cmbTorax']) ? (in_array("OU", $_POST['cmbTorax']) ? '1' : '0') : '0' ,
                ':sToraxOutroDescricao' => $_POST['inputTorax'] == "" ? null : $_POST['inputTorax'] ,
                ':sRespiracaoEupneico' => isset($_POST['cmbRespiracao']) ? (in_array("EU", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoDispneico' => isset($_POST['cmbRespiracao']) ? (in_array("DI", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoBradipneico' => isset($_POST['cmbRespiracao']) ? (in_array("BR", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoTaquipneico' => isset($_POST['cmbRespiracao']) ? (in_array("TA", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoApneia' => isset($_POST['cmbRespiracao']) ? (in_array("AP", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoTiragemIntercostal' => isset($_POST['cmbRespiracao']) ? (in_array("TI", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoRetracaoFurcula' => isset($_POST['cmbRespiracao']) ? (in_array("RF", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sRespiracaoAletasNasais' => isset($_POST['cmbRespiracao']) ? (in_array("AN", $_POST['cmbRespiracao']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarNvfds' => isset($_POST['cmbAusPulmonar']) ? (in_array("MV", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarSibilo' => isset($_POST['cmbAusPulmonar']) ? (in_array("SI", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarCrepto' => isset($_POST['cmbAusPulmonar']) ? (in_array("DR", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarRonco' => isset($_POST['cmbAusPulmonar']) ? (in_array("RO", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarOutro' => isset($_POST['cmbAusPulmonar']) ? (in_array("OU", $_POST['cmbAusPulmonar']) ? '1' : '0') : '0' ,
                ':sAuscutaPulmonarOutroDescricao' => $_POST['inputAuscutaPulmonar'] == "" ? null : $_POST['inputAuscutaPulmonar'] ,
                ':sBatimentoCardiacoBcnf' => isset($_POST['cmbBatCardiaco']) ? (in_array("BC", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoNormocardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("NO", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoTaquicardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("TA", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoBradicardico' => isset($_POST['cmbBatCardiaco']) ? (in_array("BR", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoOutro' => isset($_POST['cmbBatCardiaco']) ? (in_array("OU", $_POST['cmbBatCardiaco']) ? '1' : '0') : '0' ,
                ':sBatimentoCardiacoOutroDescricao' => $_POST['inputBatimentoCardiaco'] == "" ? null : $_POST['inputBatimentoCardiaco'] ,
                ':sPulsoRegular' => isset($_POST['cmbPulso']) ? (in_array("RE", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoIrregular' => isset($_POST['cmbPulso']) ? (in_array("IR", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoFiliforme' => isset($_POST['cmbPulso']) ? (in_array("FI", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoNaoPalpavel' => isset($_POST['cmbPulso']) ? (in_array("NP", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPulsoCheio' => isset($_POST['cmbPulso']) ? (in_array("CH", $_POST['cmbPulso']) ? '1' : '0') : '0' ,
                ':sPressaoArterialNormotenso' => isset($_POST['cmbPreArterial']) ? (in_array("NO", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialHipertenso' => isset($_POST['cmbPreArterial']) ? (in_array("HE", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialHipotenso' => isset($_POST['cmbPreArterial']) ? (in_array("HO", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,
                ':sPressaoArterialInaldivel' => isset($_POST['cmbPreArterial']) ? (in_array("IN", $_POST['cmbPreArterial']) ? '1' : '0') : '0' ,                
                ':sRedeVenosaPeriferica' => $_POST['cmbRVPeriferica'] == "" ? null : $_POST['cmbRVPeriferica'] ,
                ':sPerfusaoPeriferica' => $_POST['cmbPPeriferica'] == "" ? null : $_POST['cmbPPeriferica'] ,                
                ':sAcessoCentral' => isset($_POST['cmbAcessos']) ? (in_array("CE", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoAvp' => isset($_POST['cmbAcessos']) ? (in_array("AV", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoDisseccao' => isset($_POST['cmbAcessos']) ? (in_array("DI", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,
                ':sAcessoOutro' => isset($_POST['cmbAcessos']) ? (in_array("OU", $_POST['cmbAcessos']) ? '1' : '0') : '0' ,                
                ':sAcessoOutroDescricao' => $_POST['inputAcessos'] == "" ? null : $_POST['inputAcessos'] ,                
                ':sAbdomenPlano' => isset($_POST['cmbAbdomen']) ? (in_array("PL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenGloboso' => isset($_POST['cmbAbdomen']) ? (in_array("GL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenDistendido' => isset($_POST['cmbAbdomen']) ? (in_array("DI", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenPlacido' => isset($_POST['cmbAbdomen']) ? (in_array("FL", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenEndurecido' => isset($_POST['cmbAbdomen']) ? (in_array("EN", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenTimpanico' => isset($_POST['cmbAbdomen']) ? (in_array("TI", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenIndolor' => isset($_POST['cmbAbdomen']) ? (in_array("IN", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenDoloroso' => isset($_POST['cmbAbdomen']) ? (in_array("DO", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenAscitico' => isset($_POST['cmbAbdomen']) ? (in_array("AS", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,
                ':sAbdomenGravidico' => isset($_POST['cmbAbdomen']) ? (in_array("GR", $_POST['cmbAbdomen']) ? '1' : '0') : '0' ,                
                ':sGenitaliaIntegra' => isset($_POST['cmbGenitalia']) ? (in_array("IN", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaComLesao' => isset($_POST['cmbGenitalia']) ? (in_array("CL", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaSangramento' => isset($_POST['cmbGenitalia']) ? (in_array("SA", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,
                ':sGenitaliaSecrecao' => isset($_POST['cmbGenitalia']) ? (in_array("SE", $_POST['cmbGenitalia']) ? '1' : '0') : '0' ,                
                ':sMembroSuperiorPreservado' => isset($_POST['cmbMSuperiores']) ? (in_array("PR", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorComLesao' => isset($_POST['cmbMSuperiores']) ? (in_array("CL", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorParesia' => isset($_POST['cmbMSuperiores']) ? (in_array("PA", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorPlegia' => isset($_POST['cmbMSuperiores']) ? (in_array("PL", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorParestesia' => isset($_POST['cmbMSuperiores']) ? (in_array("PT", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroSuperiorMovIncoordenado' => isset($_POST['cmbMSuperiores']) ? (in_array("MI", $_POST['cmbMSuperiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorPreservado' => isset($_POST['cmbMInferiores']) ? (in_array("PR", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorComLesao' => isset($_POST['cmbMInferiores']) ? (in_array("CL", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorParesia' => isset($_POST['cmbMInferiores']) ? (in_array("PA", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorPlegia' => isset($_POST['cmbMInferiores']) ? (in_array("PL", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorParestesia' => isset($_POST['cmbMInferiores']) ? (in_array("PT", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,
                ':sMembroInferiorMovIncoordenado' => isset($_POST['cmbMInferiores']) ? (in_array("MI", $_POST['cmbMInferiores']) ? '1' : '0') : '0' ,                
                ':sIntestinalNormal' => isset($_POST['cmbIntestinais']) ? (in_array("NO", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalConstipacao' => isset($_POST['cmbIntestinais']) ? (in_array("CO", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalFrequencia' => isset($_POST['cmbIntestinais']) ? (in_array("FR", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalDiarreia' => isset($_POST['cmbIntestinais']) ? (in_array("DI", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalMelena' => isset($_POST['cmbIntestinais']) ? (in_array("ME", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,
                ':sIntestinalOutro' => isset($_POST['cmbIntestinais']) ? (in_array("OU", $_POST['cmbIntestinais']) ? '1' : '0') : '0' ,                
                ':sIntestinalFrequenciaDescricao' => $_POST['inputFrequenciaIntestinais'] == "" ? null : $_POST['inputFrequenciaIntestinais'] ,
                ':sIntestinalOutroDescricao' => $_POST['inputOutrosIntestinais'] == "" ? null : $_POST['inputOutrosIntestinais'] ,               
                ':sEmeseNao' => isset($_POST['cmbEmese']) ? (in_array("NA", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseSim' => isset($_POST['cmbEmese']) ? (in_array("SI", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseHematemese' => isset($_POST['cmbEmese']) ? (in_array("HE", $_POST['cmbEmese']) ? '1' : '0') : '0' ,
                ':sEmeseFrequencia' => isset($_POST['cmbEmese']) ? (in_array("FR", $_POST['cmbEmese']) ? '1' : '0') : '0' ,                
                ':sEmeseFrequenciaDescricao' => $_POST['inputFrequenciaEmese'] == "" ? null : $_POST['inputFrequenciaEmese'] ,                
                ':sUrinariaEspontanea' => isset($_POST['cmbUrinarias']) ? (in_array("ES", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaPoliuria' => isset($_POST['cmbUrinarias']) ? (in_array("PO", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaRetencao' => isset($_POST['cmbUrinarias']) ? (in_array("RE", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaIncontinencia' => isset($_POST['cmbUrinarias']) ? (in_array("IN", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaDisuria' => isset($_POST['cmbUrinarias']) ? (in_array("DI", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOliguria' => isset($_POST['cmbUrinarias']) ? (in_array("OL", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaSvd' => isset($_POST['cmbUrinarias']) ? (in_array("SD", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaSva' => isset($_POST['cmbUrinarias']) ? (in_array("SA", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOutro' => isset($_POST['cmbUrinarias']) ? (in_array("OU", $_POST['cmbUrinarias']) ? '1' : '0') : '0' ,
                ':sUrinariaOutroDescricao' => $_POST['inputOutrosUrinarias'] == "" ? null : $_POST['inputOutrosUrinarias'] ,
                ':sAspectoUrinaClara' => isset($_POST['cmbAspUrina']) ? (in_array("CL", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sAspectoUrinaAmbar' => isset($_POST['cmbAspUrina']) ? (in_array("AM", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sAspectoUrinaHematuria' => isset($_POST['cmbAspUrina']) ? (in_array("HE", $_POST['cmbAspUrina']) ? '1' : '0') : '0' ,
                ':sNutricaoLactario' => isset($_POST['cmbNutricao']) ? (in_array("LA", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoOral' => isset($_POST['cmbNutricao']) ? (in_array("OR", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoParental' => isset($_POST['cmbNutricao']) ? (in_array("PA", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoSng' => isset($_POST['cmbNutricao']) ? (in_array("SG", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoSne' => isset($_POST['cmbNutricao']) ? (in_array("SE", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,
                ':sNutricaoGgt' => isset($_POST['cmbNutricao']) ? (in_array("GT", $_POST['cmbNutricao']) ? '1' : '0') : '0' ,                
                ':sDegluticaoSemAlteracao' => isset($_POST['cmbDegluticao']) ? (in_array("SA", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,
                ':sDegluticaoComDificuldade' => isset($_POST['cmbDegluticao']) ? (in_array("CD", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,
                ':sDegluticaoNaoConsDeglutir' => isset($_POST['cmbDegluticao']) ? (in_array("NC", $_POST['cmbDegluticao']) ? '1' : '0') : '0' ,               
                ':sSuccaoSemAlteracao' => isset($_POST['cmbSuccao']) ? (in_array("SA", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,
                ':sSuccaoComDificuldade' => isset($_POST['cmbSuccao']) ? (in_array("CD", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,
                ':sSuccaoNaoConsegueSugar' => isset($_POST['cmbSuccao']) ? (in_array("NC", $_POST['cmbSuccao']) ? '1' : '0') : '0' ,                
                ':sApetitePreservado' => isset($_POST['cmbApetite']) ? (in_array("PR", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetiteAumentado' => isset($_POST['cmbApetite']) ? (in_array("AU", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetiteDiminuido' => isset($_POST['cmbApetite']) ? (in_array("DI", $_POST['cmbApetite']) ? '1' : '0') : '0' ,
                ':sApetitePrejudicado' => isset($_POST['cmbApetite']) ? (in_array("PJ", $_POST['cmbApetite']) ? '1' : '0') : '0' ,                
                ':sDenticaoTotal' => isset($_POST['cmbDenticao']) ? (in_array("TO", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoParcial' => isset($_POST['cmbDenticao']) ? (in_array("PA", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoAusente' => isset($_POST['cmbDenticao']) ? (in_array("AU", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoSuperior' => isset($_POST['cmbDenticao']) ? (in_array("SU", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoInferior' => isset($_POST['cmbDenticao']) ? (in_array("IN", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,
                ':sDenticaoProtese' => isset($_POST['cmbDenticao']) ? (in_array("PR", $_POST['cmbDenticao']) ? '1' : '0') : '0' ,                
                ':sSonoRepousoPreservado' => isset($_POST['cmbSonoRepouso']) ? (in_array("PR", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoDifAdormecer' => isset($_POST['cmbSonoRepouso']) ? (in_array("DA", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoInsonia' => isset($_POST['cmbSonoRepouso']) ? (in_array("IN", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoUsoMedicacao' => isset($_POST['cmbSonoRepouso']) ? (in_array("UM", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sSonoRepousoCansacoAcordar' => isset($_POST['cmbSonoRepouso']) ? (in_array("CA", $_POST['cmbSonoRepouso']) ? '1' : '0') : '0' ,
                ':sHigieneCorporal' => $_POST['cmbHigieneCorporal'] == "" ? null : $_POST['cmbHigieneCorporal'] ,
                ':sHigieneBucal' => $_POST['cmbHigieneBucal'] == "" ? null : $_POST['cmbHigieneBucal'] ,
                ':sRegulacaoAlergia' => $_POST['cmbAlergias'] == "" ? null : $_POST['cmbAlergias'] ,
                ':sRegulacaoAlergiaQual' => $_POST['inputQualAlergia'] == "" ? null : $_POST['inputQualAlergia'] ,
                ':sDoencaSistImunologico' => $_POST['cmbDSImunologico'] == "" ? null : $_POST['cmbDSImunologico'] ,
                ':sDoencaSistImunologicoQual' => $_POST['inputQualDoenca'] == "" ? null : $_POST['inputQualDoenca'] ,
                ':sCalendarioVacinalCompleto' => isset($_POST['cmbCalVacinal']) ? (in_array("CO", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalNaoTrouxe' => isset($_POST['cmbCalVacinal']) ? (in_array("NT", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalNaoTem' => isset($_POST['cmbCalVacinal']) ? (in_array("NE", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalIncompleto' => isset($_POST['cmbCalVacinal']) ? (in_array("IN", $_POST['cmbCalVacinal']) ? '1' : '0') : '0' ,
                ':sCalendarioVacinalQual' => $_POST['inputIncQual'] == "" ? null : $_POST['inputIncQual'] ,
                ':sZonaMoradiaUrbana' => isset($_POST['cmbZonaMoradia']) ? (in_array("UR", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaRural' => isset($_POST['cmbZonaMoradia']) ? (in_array("RU", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaInstitucionalizada' => isset($_POST['cmbZonaMoradia']) ? (in_array("IN", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sZonaMoradiaMoradorRua' => isset($_POST['cmbZonaMoradia']) ? (in_array("MR", $_POST['cmbZonaMoradia']) ? '1' : '0') : '0' ,
                ':sColetaLixoRegular' => $_POST['cmbCLixoRegular'] == "" ? null : $_POST['cmbCLixoRegular'] ,
                ':sAguaTratada' => $_POST['cmbAguaTratada'] == "" ? null : $_POST['cmbAguaTratada'] ,
                ':sRedeEsgotoPublica' => isset($_POST['cmbRedeEsgoto']) ? (in_array("PU", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoFossa' => isset($_POST['cmbRedeEsgoto']) ? (in_array("FO", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoCeuAberto' => isset($_POST['cmbRedeEsgoto']) ? (in_array("CA", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sRedeEsgotoNaoSeAplica' => isset($_POST['cmbRedeEsgoto']) ? (in_array("NA", $_POST['cmbRedeEsgoto']) ? '1' : '0') : '0' ,
                ':sComerBeber' => $_POST['cmbComerBeber'] == "" ? null : $_POST['cmbComerBeber'] ,
                ':sVestir' => $_POST['cmbVestirSe'] == "" ? null : $_POST['cmbVestirSe'] ,
                ':sSubirEscada' => $_POST['cmbSubirEscadas'] == "" ? null : $_POST['cmbSubirEscadas'] ,
                ':sBanho' => $_POST['cmbBanho'] == "" ? null : $_POST['cmbBanho'] ,
                ':sDeambular' => $_POST['cmbDeambular'] == "" ? null : $_POST['cmbDeambular'] ,
                ':sAndar' => $_POST['cmbAndar'] == "" ? null : $_POST['cmbAndar'] ,
                ':sUnidade' => $_SESSION['UnidadeId'],
            ));
            
        }
        
    } catch (PDOException $e) {

        $_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Anamnese!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
        
    }

    $_SESSION['iAtendimentoId'] = $iAtendimentoId;
    irpara("atendimentoAdmissaoEnfermagem.php");

    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Admissão Anamnese</title>

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
	     
			$('.enviarAnamnese').on('click', function(e){
				e.preventDefault();

                let msg = ''
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
                }
		
				$( "#formAtendimentoAnamnese" ).submit();
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

        function textoOlfato() {
            let cmbOlfato = $('#cmbOlfato').val()
            if (cmbOlfato == 'AL') {
                $(".alteracaoCmbOlfato").css('display', 'block');
            } else {
                $(".alteracaoCmbOlfato").css('display', 'none');
            }          
        }
        function textoAcuidadeVisual() {
            let cmbAcuidadeVisual = $('#cmbAcuidadeVisual').val()
            if (cmbAcuidadeVisual == 'AL') {
                $(".alteracaoCmbAcuidadeVisual").css('display', 'block');
            } else {
                $(".alteracaoCmbAcuidadeVisual").css('display', 'none');
            }          
        }
        function textoAudicao() {
            let cmbAudicao = $('#cmbAudicao').val()
            if (cmbAudicao == 'AL') {
                $(".alteracaoCmbAudicao").css('display', 'block');
            } else {
                $(".alteracaoCmbAudicao").css('display', 'none');
            }          
        }
        function textoTato() {
            let cmbTato = $('#cmbTato').val()
            if (cmbTato == 'AL') {
                $(".alteracaoCmbTato").css('display', 'block');
            } else {
                $(".alteracaoCmbTato").css('display', 'none');
            }          
        }
        function textoPaladar() {
            let cmbPaladar = $('#cmbPaladar').val()
            if (cmbPaladar == 'AL') {
                $(".alteracaoCmbPaladar").css('display', 'block');
            } else {
                $(".alteracaoCmbPaladar").css('display', 'none');
            }          
        }
        function textoDorAguda() {
            let cmbDorAguda = $('#cmbDorAguda').val()
            if (cmbDorAguda == 'SD') {
                $(".alteracaoCmbDorAguda").css('display', 'none');
            } else {
                $(".alteracaoCmbDorAguda").css('display', 'block');
            }          
        }
        function textoCavidadeOral() {

            let cmbCavOral = $('#cmbCavOral').val()
            if (cmbCavOral.includes('OU')) {
                $(".outrosPescoco").css('display', 'block');
            } else {
                $(".outrosPescoco").css('display', 'none');
            }          
        }
        function textoPescoco() {

            let cmbPescoco = $('#cmbPescoco').val()
            if (cmbPescoco.includes('OU')) {
                $(".outrosPescoco").css('display', 'block');
            } else {
                $(".outrosPescoco").css('display', 'none');
            }          
        }
        function textoTorax() {

            let cmbTorax = $('#cmbTorax').val()
            if (cmbTorax.includes('OU')) {
                $(".outrosTorax").css('display', 'block');
            } else {
                $(".outrosTorax").css('display', 'none');
            }          
        }
        function textoAusPulmonar() {

            let cmbAusPulmonar = $('#cmbAusPulmonar').val()
            if (cmbAusPulmonar.includes('OU')) {
                $(".outrosAuscutaPulmonar").css('display', 'block');
            } else {
                $(".outrosAuscutaPulmonar").css('display', 'none');
            }          
        }
        function textoBatimentoCardiaco() {

            let cmbBatCardiaco = $('#cmbBatCardiaco').val()
            if (cmbBatCardiaco.includes('OU')) {
                $(".outrosBatimentoCardiaco").css('display', 'block');
            } else {
                $(".outrosBatimentoCardiaco").css('display', 'none');
            }          
        }

        function textoAcessos() {

            let cmbAcessos = $('#cmbAcessos').val()
            if (cmbAcessos.includes('OU')) {
                $(".outrosAcessos").css('display', 'block');
            } else {
                $(".outrosAcessos").css('display', 'none');
            }          
        }

        function textoIntestinais() {

            let cmbIntestinais = $('#cmbIntestinais').val()
            if (cmbIntestinais.includes('OU')) {
                $(".outrosIntestinais").css('display', 'block');
            } else {
                $(".outrosIntestinais").css('display', 'none');
            }          
        }

        function textoUrinarias() {

            let cmbUrinarias = $('#cmbUrinarias').val()
            if (cmbUrinarias.includes('OU')) {
                $(".outrosUrinarias").css('display', 'block');
            } else {
                $(".outrosUrinarias").css('display', 'none');
            }          
        }

        function textoAlergias() {

            let cmbAlergias = $('#cmbAlergias').val()
            if (cmbAlergias == 'SI') {
                $(".qualAlergia").css('display', 'block');
            } else {
                $(".qualAlergia").css('display', 'none');
            }          
        }
        function teXtoDSImunologico() {

            let cmbDSImunologico = $('#cmbDSImunologico').val()
            if (cmbDSImunologico == 'SI') {
                $(".qualDoenca").css('display', 'block');
            } else {
                $(".qualDoenca").css('display', 'none');
            }          
        }

        function textoCalVacinal() {

            let cmbCalVacinal = $('#cmbCalVacinal').val()
            if (cmbCalVacinal.includes('IN')) {
                $(".qualCalVacinal").css('display', 'block');
            } else {
                $(".qualCalVacinal").css('display', 'none');
            }          
        }

        function calculaScore(){

            let cmbOcular = $('#cmbOcular').val()
            let cmbVerbal = $('#cmbVerbal').val()
            let cmbMotora = $('#cmbMotora').val()

            let valorOcular = 0, valorVerbal = 0, valorMotora = 0;

            if (cmbOcular == 'ES') { valorOcular = 4}
            if (cmbOcular == 'OV') { valorOcular = 3}
            if (cmbOcular == 'ED') { valorOcular = 2}
            if (cmbOcular == 'NR') { valorOcular = 1}
            
            if (cmbVerbal == 'OR') { valorVerbal = 5}
            if (cmbVerbal == 'CO') { valorVerbal = 4}
            if (cmbVerbal == 'PI') { valorVerbal = 3}
            if (cmbVerbal == 'PC') { valorVerbal = 2}
            if (cmbVerbal == 'NR') { valorVerbal = 1}

            if (cmbMotora == 'OA') { valorMotora = 6}
            if (cmbMotora == 'RO') { valorMotora = 5}
            if (cmbMotora == 'LE') { valorMotora = 4}
            if (cmbMotora == 'RF') { valorMotora = 3}
            if (cmbMotora == 'RE') { valorMotora = 2}
            if (cmbMotora == 'NR') { valorMotora = 1}

            let total = valorOcular + valorVerbal + valorMotora;

            $('#inputScore').val(total)

        }
        
        $(function() {
			$('.btn-grid').click(function(){
				$('.btn-grid').removeClass('active');
				$(this).addClass('active');     
			});
		});

        function mudarGrid(grid){
			if (grid == 'anamnese') {				
				$(".box-anamnese").css('display', 'block');
				$(".box-exameFisico").css('display', 'none');
			} else if (grid == 'exameFisico') {
				$(".box-exameFisico").css('display', 'block');
				$(".box-anamnese").css('display', 'none');
			}
		}

		function contarCaracteres(params) {

			var limite = params.maxLength;
			var informativo = " restantes.";
			var caracteresDigitados = params.value.length;
			var caracteresRestantes = limite - caracteresDigitados;

			if (caracteresRestantes <= 0) {
				var texto = $(`textarea[id=${params.id}]`).val();
				$(`textarea[id=${params.id}]`).val(texto.substr(0, limite));
				$(".caracteres" + params.id).text("0 " + informativo);
			} else {
				$(".caracteres" + params.id).text(" - " + caracteresRestantes + " " + informativo);
			}
		}

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
						<form name="formAtendimentoAnamnese" id="formAtendimentoAnamnese" method="post">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">

                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-6" style="text-align: left;">

                                        <div class="card-header header-elements-inline">
                                            <h3 class="card-title"><b>ADMISSÃO DE ENFERMAGEM</b></h3>
                                        </div>
            
                                    </div>

                                    <div class="col-md-6" style="text-align: right;">

                                        <div class="form-group" style="margin:20px;" >
                                            <button class="btn btn-lg btn-success mr-1 enviarAnamnese" >Salvar</button>
                                            <button type="button" class="btn btn-lg btn-secondary mr-1">Imprimir</button>
                                            <a href='atendimentoHospitalarListagem.php' class='btn btn-basic' role='button'>Voltar</a>

                                        </div>
                                    </div>

                                </div>
                            </div>

								
							</div>

							<div> 
                                <?php include ('atendimentoDadosPaciente.php'); ?>
                                <?php include ('atendimentoDadosSinaisVitais.php'); ?>
                            </div>

                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <div class="col-lg-11">	
                                        <button type="button" id="prescricao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg active mr-2 " onclick="mudarGrid('anamnese')" style="margin-left: -10px;" >Anamnese</button>
                                        <button type="button" id="evolucao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg " onclick="mudarGrid('exameFisico')" >Exame Físico</button>
                                    </div>
                                </div>                                
                                
                            </div>

                            <div class="box-anamnese" style="display: block;">

                                <div class="card">

                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title font-weight-bold ">DIAGNÓSTICO PRINCIPAL</h3>  
                                    </div>

                                    <div class="card-body">

                                        <div class="col-lg-12 mb-2 row">
                                            <!-- titulos -->
                                            <div class="col-lg-6">
                                                <label>CID <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Procedimento <span class="text-danger">*</span></label>
                                            </div>
                                            
                                            <!-- campos -->										
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
                                			
                            </div>

                            <div class="box-exameFisico" style="display: none;">

                                <div class="card">
                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title font-weight-bold">Exame Físico</h3>  
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
                                                            if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFOcular'] ==  $key) ) {																
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
                                                            if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFVerbal'] ==  $key) ) {																
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
                                                            if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFMotora'] ==  $key) ) {																
                                                                print('<option value="' . $key . '" selected>' . $item . '</option>');
                                                            } else {
                                                                print('<option value="' . $key . '">' . $item . '</option>');
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-lg-1 " style="margin-top: -5px;">
                                                <input type="text" id="inputScore" name="inputScore" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFScore']; ?>" style="background-color : #FFFFCC; border: 1px groove #ddd !important; text-align:center" readonly>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-4">
                                                <label>Pupilas</label>
                                            </div>
                                            <div class="col-lg-8"></div>
                                            <div class="col-lg-4">
                                                <select id="cmbPupilas" name="cmbPupilas[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IS' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaIsocorica'] == 1 ? 'selected' : ''; ?> >ISOCÓRICAS</option>
                                                    <option value='AN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaAnisocorica'] == 1 ? 'selected' : ''; ?> >ANISOCÓRICAS</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaMidriase'] == 1 ? 'selected' : ''; ?> >MIDRÍASE</option>
                                                    <option value='MO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaMiose'] == 1 ? 'selected' : ''; ?> >MIOSE</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaFotorreagente'] == 1 ? 'selected' : ''; ?> >FOTORREAGENTE</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPupilaParalitica'] == 1 ? 'selected' : ''; ?> >PARALÍTICA</option>
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
                                                    <option value='LU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaLucido'] == 1 ? 'selected' : ''; ?> >LÚCIDO</option>
                                                    <option value='OR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaOrientado'] == 1 ? 'selected' : ''; ?> >ORIENTADO</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaDesorientado'] == 1 ? 'selected' : ''; ?> >DESORIENTADO</option>
                                                    <option value='SO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaSonolento'] == 1 ? 'selected' : ''; ?> >SONOLENTO</option>
                                                    <option value='AG' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaAgitado'] == 1 ? 'selected' : ''; ?> >AGITADO</option>
                                                    <option value='AT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaAtivo'] == 1 ? 'selected' : ''; ?> >ATIVO</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaHipoativo'] == 1 ? 'selected' : ''; ?> >HIPOATIVO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNivelConscienciaInconsciente'] == 1 ? 'selected' : ''; ?> >INCONSCIENTE</option>
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
                                                    <option  value='NO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoTermicaNormoTermico'] == 1 ? 'selected' : ''; ?> >NORMOTÉRMICO</option>
                                                    <option  value='HI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoTermicaHipoTermico'] == 1 ? 'selected' : ''; ?> >HIPOTÉRMICO</option>
                                                    <option  value='FE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoTermicaFebre'] == 1 ? 'selected' : ''; ?> >FEBRE</option>
                                                    <option  value='PI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoTermicaPirexia'] == 1 ? 'selected' : ''; ?> >PIREXIA</option>
                                                    <option  value='SU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoTermicaSudorese'] == 1 ? 'selected' : ''; ?> >SUDORESE</option>                                                   
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
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbAcuidadeVisual" name="cmbAcuidadeVisual" class="form-control-select2" onChange="textoAcuidadeVisual()"  >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcuidadeVisual'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcuidadeVisual'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbAudicao" name="cmbAudicao" class="form-control-select2" onChange="textoAudicao()"  >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAudicao'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAudicao'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-4 "  >
                                                <label class="alteracaoCmbOlfato" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFOlfato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <label class="alteracaoCmbAcuidadeVisual" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAcuidadeVisual'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <label class=" alteracaoCmbAudicao" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAudicao'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbOlfato" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFOlfato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoOlfato" name="inputAlteracaoOlfato" maxLength="80" onInput="contarCaracteres(this);" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfatoAlteracao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoOlfato"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbAcuidadeVisual" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAcuidadeVisual'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoAcuidadeVisual" name="inputAlteracaoAcuidadeVisual" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcuidadeVisualAlteracao']; ?>">											
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoAcuidadeVisual"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 "  >
                                                <div class="alteracaoCmbAudicao" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAudicao'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoAudicao" name="inputAlteracaoAudicao" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAudicaoAlteracao']; ?>">
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
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFTato'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFTato'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPaladar" name="cmbPaladar" class="form-control-select2" onChange="textoPaladar()" >
                                                    <option value="">Selecione</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPaladar'] == 'SA' ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='AL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPaladar'] == 'AL' ? 'selected' : ''; ?> >ALTERADO. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbDorAguda" name="cmbDorAguda" class="form-control-select2" onChange="textoDorAguda()" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                        $arrayGrauDependencia = [ 'SD' => 'SEM ALTERAÇÕES', 'DL' => 'DOR LEVE', 'DM' => 'DOR MODERADA', 'DI' => 'DOR INTENSA' ]; 
                                                        foreach ($arrayGrauDependencia as $key => $item) {
                                                            if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFDorAguda'] ==  $key) ) {																
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
                                                <label class="alteracaoCmbTato" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFTato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="alteracaoCmbPaladar" style="display: <?php echo isset($iAtendimentoExameFisicoId ) ? ($rowExameFisico['EnExFPaladar'] == 'AL' ? 'block' : 'none') : 'none';  ?>;">Qual alteração?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="alteracaoCmbDorAguda" style="display: <?php echo isset($iAtendimentoExameFisicoId ) ? ($rowExameFisico['EnExFDorAguda'] == 'SD' ? 'none' : 'block') : 'none'; ?>;">Local da dor?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbTato" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFTato'] == 'AL' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAlteracaoTato" name="inputAlteracaoTato" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFTatoAlteracao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoTato"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbPaladar" style="display: <?php echo isset($iAtendimentoExameFisicoId ) ? ($rowExameFisico['EnExFPaladar'] == 'AL' ? 'block' : 'none') : 'none';  ?>;">
                                                    <input type="text" id="inputAlteracaoPaladar" name="inputAlteracaoPaladar" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPaladarAlteracao']; ?>">											
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputAlteracaoPaladar"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="alteracaoCmbDorAguda" style="display: <?php echo isset($iAtendimentoExameFisicoId ) ? ($rowExameFisico['EnExFDorAguda'] != 'SD' ? 'block' : 'none') : 'none'; ?>;">                                                    
                                                    <input type="text" id="inputAlteracaoDorAguda" name="inputAlteracaoDorAguda" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDorAgudaLocal']; ?>">
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
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoIntegra'] == 1 ? 'selected' : ''; ?> >INTEGRA</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoCicatriz'] == 1 ? 'selected' : ''; ?> >CICATRIZ</option>
                                                    <option value='IC' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoIncisao'] == 1 ? 'selected' : ''; ?> >INCISÃO</option>
                                                    <option value='ES' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoEscoriacao'] == 1 ? 'selected' : ''; ?> >ESCORIAÇÕES</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoDescamacao'] == 1 ? 'selected' : ''; ?> >DESCAMAÇÃO</option>
                                                    <option value='ER' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoErupcao'] == 1 ? 'selected' : ''; ?> >ERUPÇÃO</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoUmida'] == 1 ? 'selected' : ''; ?> >ÚMIDA</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoAspera'] == 1 ? 'selected' : ''; ?> >ÁSPERA</option>
                                                    <option value='EP' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoEspessa'] == 1 ? 'selected' : ''; ?> >ESPESSA</option>
                                                    <option value='FI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoFina'] == 1 ? 'selected' : ''; ?> >FINA</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoFeridaOperatoria'] == 1 ? 'selected' : ''; ?> >FERIDA OPERATÓRIA</option>
                                                    <option value='UD' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleAspectoUlceraDecubito'] == 1 ? 'selected' : ''; ?> >ÚLCERA DE DECÚBITO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbTurgorE" name="cmbTurgorE[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleTurgorSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleTurgorDiminuida'] == 1 ? 'selected' : ''; ?> >DIMINUÍDA</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleTurgorAumentada'] == 1 ? 'selected' : ''; ?> >AUMENTADA</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleTurgorHidratada'] == 1 ? 'selected' : ''; ?> >HIDRATADA</option>
                                                    <option value='DE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleTurgorDesidratada'] == 1 ? 'selected' : ''; ?> >DESIDATRADA</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbCor" name="cmbCor[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleCorPalidez'] == 1 ? 'selected' : ''; ?> >PALIDEZ</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleCorCianose'] == 1 ? 'selected' : ''; ?> >CIANOSE</option>
                                                    <option value='IC' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleCorIctericia'] == 1 ? 'selected' : ''; ?> >ICTERÍCIA</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleCorSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
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
                                                <input type="text" id="inputEdema" name="inputEdema" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleEdema']; ?>">
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
                                                    <option value='DR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleOutroDreno'] == 1 ? 'selected' : ''; ?> >DRENO</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPeleOutroSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÃO</option>
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
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoIntegro'] == 1 ? 'selected' : ''; ?> >ÍNTEGRO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÃO</option>
                                                    <option value='CE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoCeborreia'] == 1 ? 'selected' : ''; ?> >CEBORRÉIA</option>
                                                    <option value='PE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoPediculose'] == 1 ? 'selected' : ''; ?> >PEDICULOSE</option>
                                                    <option value='CI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoCicatriz'] == 1 ? 'selected' : ''; ?> >CICATRIZ</option>
                                                    <option value='LI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCoroCabeludoLimpo'] == 1 ? 'selected' : ''; ?> >LIMPO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbMucOculares" name="cmbMucOculares[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='NO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMucosaOcularNormocromica'] == 1 ? 'selected' : ''; ?> >NORMOCRÔMICAS</option>
                                                    <option value='HI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMucosaOcularHipocromica'] == 1 ? 'selected' : ''; ?> >HIPOCRÔMICAS</option>
                                                    <option value='HE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMucosaOcularHipercromica'] == 1 ? 'selected' : ''; ?> >HIPERCRÔMICAS</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbAurNasal" name="cmbAurNasal[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuricularNasalSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='OT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuricularNasalOtorragia'] == 1 ? 'selected' : ''; ?> >OTORRAGIA</option>
                                                    <option value='RI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuricularNasalRinorragia'] == 1 ? 'selected' : ''; ?> >RINORRAGIA</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuricularNasalSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÕES</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbCavOral" name="cmbCavOral[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoCavidadeOral()">
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCavidadeOralSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCavidadeOralComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÃO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCavidadeOralOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-9">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosCavidadeOral" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFCavidadeOralOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-9">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosCavidadeOral" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFCavidadeOralOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputCavidadeOral" name="inputCavidadeOral" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCavidadeOralOutroDescricao']; ?>">
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
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPescocoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='LI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPescocoLinfonodoInfartado'] == 1 ? 'selected' : ''; ?> >LINFONODOS INFARTADOS</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPescocoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbTorax" name="cmbTorax[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoTorax()">
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÕES</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxSimetrico'] == 1 ? 'selected' : ''; ?> >SIMÉTRICO</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxAssimetrico'] == 1 ? 'selected' : ''; ?> >ASSIMÉTRICO</option>
                                                    <option value='DR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxDrreno'] == 1 ? 'selected' : ''; ?> >DRENO</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxUsaMarcapasso'] == 1 ? 'selected' : ''; ?> >USA MARCAPASSO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbRespiracao" name="cmbRespiracao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='EU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoEupneico'] == 1 ? 'selected' : ''; ?>  >EUPNEICO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoDispneico'] == 1 ? 'selected' : ''; ?> >DISPNEICO</option>
                                                    <option value='BR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoBradipneico'] == 1 ? 'selected' : ''; ?> >BRADIPNEICO</option>
                                                    <option value='TA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoTaquipneico'] == 1 ? 'selected' : ''; ?> >TAQUIPNEICO</option>
                                                    <option value='AP' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoApneia'] == 1 ? 'selected' : ''; ?> >APNÉIA</option>
                                                    <option value='TI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoTiragemIntercostal'] == 1 ? 'selected' : ''; ?> >TIRAGEM INTERCOSTAL</option>
                                                    <option value='RF' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoRetracaoFurcula'] == 1 ? 'selected' : ''; ?> >RETRAÇÃO FÚRCULA</option>
                                                    <option value='AN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRespiracaoAletasNasais'] == 1 ? 'selected' : ''; ?> >ALETAS NASAIS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbAusPulmonar" name="cmbAusPulmonar[]" class="form-control multiselect-filtering" multiple="multiple" onChange="textoAusPulmonar()" >
                                                    <option value='MV' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarNvfds'] == 1 ? 'selected' : ''; ?> >MVFDS/RA</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarSibilo'] == 1 ? 'selected' : ''; ?> >SIBILOS</option>
                                                    <option value='DR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarCrepto'] == 1 ? 'selected' : ''; ?> >CRÉPTOS</option>
                                                    <option value='RO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarRonco'] == 1 ? 'selected' : ''; ?> >RONCOS</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="col-lg-12 mb-2 row">

                                            <div class="col-lg-3  ">
                                                <label class="outrosPescoco" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFPescocoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosTorax" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFToraxOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <label class="outrosAuscutaPulmonar" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAuscutaPulmonarOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">Outros</label>
                                            </div>

                                            <div class="col-lg-3 ">
                                                <div class="outrosPescoco" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFPescocoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputPescoco" name="inputPescoco" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPescocoOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputPescoco"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosTorax" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFToraxOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputTorax" name="inputTorax" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFToraxOutroDescricao']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputTorax"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-3 ">
                                                <div class="outrosAuscutaPulmonar" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAuscutaPulmonarOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAuscutaPulmonar" name="inputAuscutaPulmonar" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAuscutaPulmonarOutroDescricao']; ?>">
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
                                                    <option value='BC' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoBcnf'] == 1 ? 'selected' : ''; ?> >BCNF+</option>
                                                    <option value='NO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoNormocardico'] == 1 ? 'selected' : ''; ?> >NORMOCÁRDICO</option>
                                                    <option value='TA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoTaquicardico'] == 1 ? 'selected' : ''; ?> >TAQUICÁRDICO</option>
                                                    <option value='BR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoBradicardico'] == 1 ? 'selected' : ''; ?> >BRADICÁRDICO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPulso" name="cmbPulso[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='RE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPulsoRegular'] == 1 ? 'selected' : ''; ?> >REGULAR</option>
                                                    <option value='IR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPulsoIrregular'] == 1 ? 'selected' : ''; ?> >IRREGULAR</option>
                                                    <option value='FI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPulsoFiliforme'] == 1 ? 'selected' : ''; ?> >FILIFORME</option>
                                                    <option value='NP' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPulsoNaoPalpavel'] == 1 ? 'selected' : ''; ?> >NÃO PALPÁVEL</option>
                                                    <option value='CH' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPulsoCheio'] == 1 ? 'selected' : ''; ?> >CHEIO</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbPreArterial" name="cmbPreArterial[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='NO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPressaoArterialNormotenso'] == 1 ? 'selected' : ''; ?> >NORMOTENSO</option>
                                                    <option value='HE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPressaoArterialHipertenso'] == 1 ? 'selected' : ''; ?> >HIPERTENSO</option>
                                                    <option value='HO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPressaoArterialHipotenso'] == 1 ? 'selected' : ''; ?> >HIPOTENSO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFPressaoArterialInaldivel'] == 1 ? 'selected' : ''; ?> >INALDÍVEL</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">

                                            <div class="col-lg-4 ">
                                                <label class="outrosBatimentoCardiaco" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFBatimentoCardiacoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros</label>
                                            </div>
                                            <div class="col-lg-8">
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 ">
                                                <div class="outrosBatimentoCardiaco" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFBatimentoCardiacoOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputBatimentoCardiaco" name="inputBatimentoCardiaco" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFBatimentoCardiacoOutroDescricao']; ?>">
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
                                                    <option value='CE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcessoCentral'] == 1 ? 'selected' : ''; ?> >CENTRAL</option>
                                                    <option value='AV' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcessoAvp'] == 1 ? 'selected' : ''; ?> >AVP</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcessoDisseccao'] == 1 ? 'selected' : ''; ?> >DISSECÇÃO</option>
                                                    <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcessoOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-5 row">
                                            
                                            <div class="col-lg-8">
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="outrosAcessos" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAcessoOutroDescricao'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros</label>
                                            </div>
                                            
                                            <div class="col-lg-8">											
                                            </div>									
                                            <div class="col-lg-4 ">
                                                <div class="outrosAcessos" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFAcessoOutroDescricao'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputAcessos" name="inputAcessos" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAcessoOutroDescricao']; ?>">
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
                                                    <option value='PL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenPlano'] == 1 ? 'selected' : ''; ?> >PLANO</option>
                                                    <option value='GL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenGloboso'] == 1 ? 'selected' : ''; ?> >GLOBOSO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenDistendido'] == 1 ? 'selected' : ''; ?> >DISTENDIDO</option>
                                                    <option value='FL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenPlacido'] == 1 ? 'selected' : ''; ?> >FLÁCIDO</option>
                                                    <option value='EN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenEndurecido'] == 1 ? 'selected' : ''; ?> >ENDURECIDO</option>
                                                    <option value='TI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenTimpanico'] == 1 ? 'selected' : ''; ?> >TIMPÂNICO</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenIndolor'] == 1 ? 'selected' : ''; ?> >INDOLOR</option>
                                                    <option value='DO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenDoloroso'] == 1 ? 'selected' : ''; ?> >DOLOROSO</option>
                                                    <option value='AS' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenAscitico'] == 1 ? 'selected' : ''; ?> >ASCÍTICO</option>
                                                    <option value='GR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAbdomenGravidico'] == 1 ? 'selected' : ''; ?> >GRAVÍDICO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select id="cmbGenitalia" name="cmbGenitalia[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFGenitaliaIntegra'] == 1 ? 'selected' : ''; ?> >ÍNTEGRA</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFGenitaliaComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÓES</option>
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFGenitaliaSangramento'] == 1 ? 'selected' : ''; ?> >SANGRAMENTO</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFGenitaliaSecrecao'] == 1 ? 'selected' : ''; ?> >SECREÇÃO</option>
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

                                        <div class="col-lg-8 row">

                                            <div class="col-lg-6">
                                                <label>Membros Superiores (MMSS)</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Membros Inferiores (MMII)</label>
                                            </div>                                                
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-6">
                                                <select id="cmbMSuperiores" name="cmbMSuperiores[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÕES</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorParesia'] == 1 ? 'selected' : ''; ?> >PARESIA</option>
                                                    <option value='PL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorPlegia'] == 1 ? 'selected' : ''; ?> >PLEGIA</option>
                                                    <option value='PT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorParestesia'] == 1 ? 'selected' : ''; ?> >PARESTESIA</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroSuperiorMovIncoordenado'] == 1 ? 'selected' : ''; ?> >MOVIMENTOS INCOORDENADOS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select id="cmbMInferiores" name="cmbMInferiores[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorComLesao'] == 1 ? 'selected' : ''; ?> >COM LESÕES</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorParesia'] == 1 ? 'selected' : ''; ?> >PARESIA</option>
                                                    <option value='PL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorPlegia'] == 1 ? 'selected' : ''; ?> >PLEGIA</option>
                                                    <option value='PT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorParestesia'] == 1 ? 'selected' : ''; ?> >PARESTESIA</option>
                                                    <option value='MI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFMembroInferiorMovIncoordenado'] == 1 ? 'selected' : ''; ?> >MOVIMENTOS INCOORDENADOS</option>
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
                                                            <option value='NO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalNormal'] == 1 ? 'selected' : ''; ?> >NORMAL</option>
                                                            <option value='CO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalConstipacao'] == 1 ? 'selected' : ''; ?> >CONSTIPAÇÃO</option>
                                                            <option value='FR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalFrequencia'] == 1 ? 'selected' : ''; ?> >FREQUÊNCIA</option>
                                                            <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalDiarreia'] == 1 ? 'selected' : ''; ?> >DIARRÉIA</option>
                                                            <option value='ME' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalMelena'] == 1 ? 'selected' : ''; ?> >MELENA</option>
                                                            <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <select id="cmbEmese" name="cmbEmese[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                            <option value='NA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFEmeseNao'] == 1 ? 'selected' : ''; ?> >NÃO</option>
                                                            <option value='SI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFEmeseSim'] == 1 ? 'selected' : ''; ?> >SIM</option>
                                                            <option value='HE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFEmeseHematemese'] == 1 ? 'selected' : ''; ?> >HEMATÊMESE</option>
                                                            <option value='FR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFEmeseFrequencia'] == 1 ? 'selected' : ''; ?> >FREQUÊNCIA</option>
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
                                                        <input type="text" id="inputFrequenciaIntestinais" name="inputFrequenciaIntestinais" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalFrequenciaDescricao']; ?>">
                                                        <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputFrequenciaIntestinais"></span></small>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <input type="text" id="inputFrequenciaEmese" name="inputFrequenciaEmese" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFEmeseFrequenciaDescricao']; ?>">											
                                                        <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputFrequenciaEmese"></span></small>
                                                    </div>

                                                </div>

                                                <div class="col-lg-12 row mb-2">

                                                    <div class="col-lg-6">
                                                        <label class="outrosIntestinais" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFIntestinalOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;" >Outros </label>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6">
                                                        <div class="outrosIntestinais" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFIntestinalOutro'] == 1 ? 'block' : 'none' ) :  'none' ; ?>;">                                                            
                                                            <input type="text" id="inputOutrosIntestinais" name="inputOutrosIntestinais" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFIntestinalOutroDescricao']; ?>">
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
                                                            <option value='ES' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaEspontanea'] == 1 ? 'selected' : ''; ?> >ESPONTÂNEA</option>
                                                            <option value='PO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaPoliuria'] == 1 ? 'selected' : ''; ?> >POLIÚRIA</option>
                                                            <option value='RE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaRetencao'] == 1 ? 'selected' : ''; ?> >RETENÇÃO</option>
                                                            <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaIncontinencia'] == 1 ? 'selected' : ''; ?> >INCONTINÊNCIA</option>
                                                            <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaDisuria'] == 1 ? 'selected' : ''; ?> >DISÚRIA</option>
                                                            <option value='OL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaOliguria'] == 1 ? 'selected' : ''; ?> >OLIGÚRIA</option>
                                                            <option value='SD' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaSvd'] == 1 ? 'selected' : ''; ?> >SVD</option>
                                                            <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaSva'] == 1 ? 'selected' : ''; ?> >SVA</option>
                                                            <option value='OU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaOutro'] == 1 ? 'selected' : ''; ?> >OUTROS</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <select id="cmbAspUrina" name="cmbAspUrina[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                            <option value='CL' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAspectoUrinaClara'] == 1 ? 'selected' : ''; ?> >CLARA</option>
                                                            <option value='AM' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAspectoUrinaAmbar'] == 1 ? 'selected' : ''; ?> >ÂMBAR</option>
                                                            <option value='HE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFAspectoUrinaHematuria'] == 1 ? 'selected' : ''; ?> >HEMATÚRIA</option>
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
													    <input type="text" id="inputOutrosUrinarias" name="inputOutrosUrinarias" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFUrinariaOutroDescricao']; ?>">
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
                                                    <option value='LA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoLactario'] == 1 ? 'selected' : ''; ?> >LACTÁRIO</option>
                                                    <option value='OR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoOral'] == 1 ? 'selected' : ''; ?> >ORAL</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoParental'] == 1 ? 'selected' : ''; ?> >PARENTERAL</option>
                                                    <option value='SG' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoSng'] == 1 ? 'selected' : ''; ?> >SNG</option>
                                                    <option value='SE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoSne'] == 1 ? 'selected' : ''; ?> >SNE</option>
                                                    <option value='GT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFNutricaoGgt'] == 1 ? 'selected' : ''; ?> >GGT</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbDegluticao" name="cmbDegluticao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDegluticaoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CD' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDegluticaoComDificuldade'] == 1 ? 'selected' : ''; ?> >COM DIFICULDADE</option>
                                                    <option value='NC' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDegluticaoNaoConsDeglutir'] == 1 ? 'selected' : ''; ?> >NÃO CONSEGUE DEGLUTIR</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbSuccao" name="cmbSuccao[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='SA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSuccaoSemAlteracao'] == 1 ? 'selected' : ''; ?> >SEM ALTERAÇÃO</option>
                                                    <option value='CD' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSuccaoComDificuldade'] == 1 ? 'selected' : ''; ?> >COM DIFICULDADE</option>
                                                    <option value='NC' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSuccaoNaoConsegueSugar'] == 1 ? 'selected' : ''; ?> >NÃO CONSEGUE SUGAR</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="cmbApetite" name="cmbApetite[]" class="form-control multiselect-filtering" multiple="multiple" >
                                                    <option value='PR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFApetitePreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFApetiteAumentado'] == 1 ? 'selected' : ''; ?> >AUMENTADO</option>
                                                    <option value='DI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFApetiteDiminuido'] == 1 ? 'selected' : ''; ?> >DIMINUÍDO</option>
                                                    <option value='PJ' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFApetitePrejudicado'] == 1 ? 'selected' : ''; ?> >PREJUDICADO</option>
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
                                                    <option value='TO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoTotal'] == 1 ? 'selected' : ''; ?> >TOTAL</option>
                                                    <option value='PA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoParcial'] == 1 ? 'selected' : ''; ?> >PARCIAL</option>
                                                    <option value='AU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoAusente'] == 1 ? 'selected' : ''; ?> >AUSENTE</option>
                                                    <option value='SU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoSuperior'] == 1 ? 'selected' : ''; ?> >SUPERIOR</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoInferior'] == 1 ? 'selected' : ''; ?> >INFERIOR</option>
                                                    <option value='PR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDenticaoProtese'] == 1 ? 'selected' : ''; ?> >PRÓTESE</option>
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
                                                    <option value='PR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSonoRepousoPreservado'] == 1 ? 'selected' : ''; ?> >PRESERVADO</option>
                                                    <option value='DA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSonoRepousoDifAdormecer'] == 1 ? 'selected' : ''; ?> >DIFICULDADE PARA ADORMECER</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSonoRepousoInsonia'] == 1 ? 'selected' : ''; ?> >INSÔNIA</option>
                                                    <option value='UM' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSonoRepousoUsoMedicacao'] == 1 ? 'selected' : ''; ?> >USO DE MEDICAÇÕES</option>
                                                    <option value='CA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFSonoRepousoCansacoAcordar'] == 1 ? 'selected' : ''; ?> >CANSAÇO AO ACORDAR</option>
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
                                                    <option value='NA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'NA' ? 'selected' : ''; ?> >NÃO</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'SI' ? 'selected' : ''; ?> >SIM. QUAL?</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbDSImunologico" name="cmbDSImunologico" class="form-control-select2" onChange="teXtoDSImunologico()">
                                                    <option value="">Selecione</option>
                                                    <option value='NA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'NA' ? 'selected' : ''; ?> >NÃO</option>
                                                    <option value='SI' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFOlfato'] == 'SI' ? 'selected' : ''; ?> >SIM. QUAL?</option>
                                                </select>											
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="cmbCalVacinal" name="cmbCalVacinal[]" class="form-control multiselect-filtering" multiple="multiple"  onChange="textoCalVacinal()">
                                                    <option value='CO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCalendarioVacinalCompleto'] == 1 ? 'selected' : ''; ?> >COMPLETO</option>
                                                    <option value='NT' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCalendarioVacinalNaoTrouxe'] == 1 ? 'selected' : ''; ?> >NÃO TROUXE</option>
                                                    <option value='NE' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCalendarioVacinalNaoTem'] == 1 ? 'selected' : ''; ?> >NÃO TEM</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCalendarioVacinalIncompleto'] == 1 ? 'selected' : ''; ?> >INCOMPLETO. QUAL?</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="col-lg-12 mb-2 row">

                                            <div class="col-lg-4 ">
                                                <label class="qualAlergia" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFRegulacaoAlergia'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">Qual alergia?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="qualDoenca" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFDoencaSistImunologico'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">Qual doença?</label>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <label class="qualCalVacinal" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFCalendarioVacinalIncompleto'] == 'IN' ? 'block' : 'none' ) :  'none' ; ?>;">Incompleto qual?</label>
                                            </div>
                                            
                                            <!-- campos -->										
                                            <div class="col-lg-4 ">
                                                <div class="qualAlergia" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFRegulacaoAlergia'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputQualAlergia" name="inputQualAlergia" maxLength="80"class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRegulacaoAlergiaQual']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputQualAlergia"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <div class="qualDoenca" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFDoencaSistImunologico'] == 'SI' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputQualDoenca" name="inputQualDoenca" maxLength="80" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFDoencaSistImunologicoQual']; ?>">
                                                    <small class="text-muted form-text">Max. de 80 caracteres<span class="caracteresinputQualDoenca"></span></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 ">
                                                <div class="qualCalVacinal" style="display: <?php echo isset($iAtendimentoExameFisicoId) ? ($rowExameFisico['EnExFCalendarioVacinalIncompleto'] == 'IN' ? 'block' : 'none' ) :  'none' ; ?>;">
                                                    <input type="text" id="inputIncQual" name="inputIncQual" maxLength="80" class="form-control"  placeholder="" value="<?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFCalendarioVacinalQual']; ?>">
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
                                                    <option value='UR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFZonaMoradiaUrbana'] == 1 ? 'selected' : ''; ?> >URBANA</option>
                                                    <option value='RU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFZonaMoradiaRural'] == 1 ? 'selected' : ''; ?> >RURAL</option>
                                                    <option value='IN' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFZonaMoradiaInstitucionalizada'] == 1 ? 'selected' : ''; ?> >INSTITUCIONALIZADA</option>
                                                    <option value='MR' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFZonaMoradiaMoradorRua'] == 1 ? 'selected' : ''; ?> >MORADOR(A) DE RUA</option>
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
                                                    <option value='PU' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRedeEsgotoPublica'] == 1 ? 'selected' : ''; ?> >PÚBLICA</option>
                                                    <option value='FO' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRedeEsgotoFossa'] == 1 ? 'selected' : ''; ?> >FOSSA</option>
                                                    <option value='CA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRedeEsgotoCeuAberto'] == 1 ? 'selected' : ''; ?> >CÉU ABERTO</option>
                                                    <option value='NA' <?php if (isset($iAtendimentoExameFisicoId )) echo $rowExameFisico['EnExFRedeEsgotoNaoSeAplica'] == 1 ? 'selected' : ''; ?> >NÃO SE APLICA</option>
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFComerBeber'] ==  $key) ) {																
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFVestir'] ==  $key) ) {																
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFSubirEscada'] ==  $key) ) {																
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFBanho'] ==  $key) ) {																
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFDeambular'] ==  $key) ) {																
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
                                                                    if ( (isset($iAtendimentoExameFisicoId )) && ($rowExameFisico['EnExFAndar'] ==  $key) ) {																
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
                                            <button class="btn btn-lg btn-success mr-1 enviarAnamnese" >Salvar</button>
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