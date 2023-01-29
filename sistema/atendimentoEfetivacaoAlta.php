<?php
include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Efetivação de Alta';
include('global_assets/php/conexao.php');


$iAtendimentoId = isset($_POST['iAtendimentoId']) ? $_POST['iAtendimentoId'] : null;

if (isset($_SESSION['iAtendimentoId']) && $iAtendimentoId == null) {
    $iAtendimentoId = $_SESSION['iAtendimentoId'];
}
$_SESSION['iAtendimentoId'] = null;

if (!$iAtendimentoId) {
    irpara("atendimentoHospitalarListagem.php");
}

//exame físico
$sql = "SELECT TOP(1) EnEfAId
FROM EnfermagemEfetivacaoAlta
WHERE EnEfAAtendimento = $iAtendimentoId
ORDER BY EnEfAId DESC";
$result = $conn->query($sql);
$rowExameFisico = $result->fetch(PDO::FETCH_ASSOC);

$ClaChave = isset($_POST['ClaChave']) ? $_POST['ClaChave'] : '';
$ClaNome = isset($_POST['ClaNome']) ? $_POST['ClaNome'] : '';

//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, A.ProfiUsuario, A.ProfiId as ProfissionalId, A.ProfiNome as ProfissionalNome, PrConNome, B.ProfiCbo as ProfissaoCbo, ProfiNumConselho
		FROM Usuario
		JOIN Profissional A ON A.ProfiUsuario = UsuarId
		LEFT JOIN Profissao B ON B.ProfiId = A.ProfiProfissao
		LEFT JOIN ProfissionalConselho ON PrConId = ProfiConselho
		WHERE UsuarId =  " . $_SESSION['UsuarId'] . " ";
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

$iAtendimentoCliente = $row['AtendCliente'];
$iAtendimentoId = $row['AtendId'];

//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Feminino';
} else{
    $sexo = 'Masculino';
}

if (isset($_POST['inputInicio'])) {

    try {

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
                
                EnEfADataHoraAlta = :dDataAlta,
                EnEfATipoAlta = :sTipoAlta,
                EnEfACondicaoPaciente = :sCondicaoPaciente,
                EnEfATipoTransporte	= :sTipoTransporte
                EnEfATransferencia = :sTransferencia,
                EnEfALocalTransferencia = :iLocalTransferencia,
                EnEfAAcompanhante = :Acompanhante,
                EnEfAAcompanhanteNome = :sAcompanhanteNome,
                EnEfAProfissionalResponsavel = :iProfissionalResponsavel,
                EnEfAJustificativaAlta	= :sJustificativaAlta,
                EnEfAProcedimentoMedicacao = :sProcedimentoMedicacao,
                EnEfACid10 = :iCid10,
                EnEfAProcedimentoRealizado = :iProcedimentoRealizado,
                EnEfADataHoraObito	= :sDataHoraObito,
                EnEfARegistroObito	= :sRegistroObito
                
                WHERE EnEfAId = :iAtendimentoEfetivacaoAlta";

            $result = $conn->prepare($sql);

            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId,
                ':sDataInicio' => date('m/d/Y'),
                ':sHoraInicio' => date('H:i'),
                ':sDataFim' => date('m/d/Y'),
                ':sHoraFim' => date('H:i'),

                ':sPrevisaoAlta' => $_POST['inputPrevisaoAlta'] == "" ? null : $_POST['inputPrevisaoAlta'],
                ':sTipoInternacao' => $_POST['inputTipoInternacao'] == "" ? null : $_POST['inputTipoInternacao'],
                ':sEspecialidadeLeito' => $_POST['inputEspLeito'] == "" ? null : $_POST['inputEspLeito'],
                ':sAla' => $_POST['inputAla'] == "" ? null : $_POST['inputAla'],
                ':sQuarto' => $_POST['inputQuarto'] == "" ? null : $_POST['inputQuarto'],
                ':sLeito' => $_POST['inputLeito'] == "" ? null : $_POST['inputLeito'],

                ':sProfissional' => $userId,
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
                (EnEfAAtendimento,
                EnEfADataInicio,
                EnEfAHoraInicio,
                EnEfADataFim,
                EnEfAHoraFim,
                EnEfAPrevisaoAlta,
                EnEfATipoInternacao,
                EnEfAEspecialidadeLeito,
                EnEfAAla,
                EnEfAQuarto,
                EnEfALeito,
                EnEfAProfissional,
                EnEfAPas,
                EnEfAPad,
                EnEfAFreqCardiaca,
                EnEfAFreqRespiratoria,
                EnEfATemperatura,
                EnEfASPO,
                EnEfAHGT,
                EnEfAPeso,
                EnEfADataHoraAlta,
                EnEfATipoAlta,
                EnEfACondicaoPaciente,
                EnEfATipoTransporte,
                EnEfATransferencia,
                EnEfALocalTransferencia,
                EnEfAAcompanhante,
                EnEfAAcompanhanteNome,
                EnEfAProfissionalResponsavel,
                EnEfAJustificativaAlta,
                EnEfAProcedimentoMedicacao,
                EnEfACid10,
                EnEfAProcedimentoRealizado,
                EnEfADataHoraObito,
                EnEfARegistroObito,
                
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
                :dDataAlta,
                :sTipoAlta,
                :sCondicaoPaciente,
                :sTipoTransporte
                :sTransferencia,
                :iLocalTransferencia,
                :Acompanhante,
                :sAcompanhanteNome,
                :iProfissionalResponsavel,
                :sJustificativaAlta,
                :sProcedimentoMedicacao,
                :iCid10,
                :iProcedimentoRealizado,
                :sDataHoraObito,
                :sRegistroObito
                
                
            )";
            $result = $conn->prepare($sql);

            $result->execute(array(
                ':sAtendimento' => $iAtendimentoId,
                ':sDataInicio' => date('m/d/Y'),
                ':sHoraInicio' => date('H:i'),
                ':sDataFim' => date('m/d/Y'),
                ':sHoraFim' => date('H:i'),
                ':sPrevisaoAlta' => $_POST['inputPrevisaoAlta'] == "" ? null : $_POST['inputPrevisaoAlta'],
                ':sTipoInternacao' => $_POST['inputTipoInternacao'] == "" ? null : $_POST['inputTipoInternacao'],
                ':sEspecialidadeLeito' => $_POST['inputEspLeito'] == "" ? null : $_POST['inputEspLeito'],
                ':sAla' => $_POST['inputAla'] == "" ? null : $_POST['inputAla'],
                ':sQuarto' => $_POST['inputQuarto'] == "" ? null : $_POST['inputQuarto'],
                ':sLeito' => $_POST['inputLeito'] == "" ? null : $_POST['inputLeito'],
                ':sProfissional' => $userId,
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

            $('.salvarEfetivacaoAlta').on('click', function(e) {
                e.preventDefault();

                $("#formAtendimentoEfetivacaoAlta").submit();
            })
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

                                            <div class="form-group" style="margin:20px;">
                                                <?php 
                                                    if (isset($SituaChave) && $SituaChave != "ATENDIDO") {
                                                        echo "<button class='btn btn-lg btn-success mr-1 salvarEfetivacaoAlta'>Salvar</button>";
                                                    }
                                                ?>
                                                <button type="button" class="btn btn-lg btn-secondary mr-1">Imprimir</button>
                                                <a href='atendimentoHospitalarListagem.php' class='btn btn-basic' role='button'>Voltar</a>

                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </div>

                            <div>
                                <?php include('atendimentoDadosPacienteHospitalar.php'); ?>
                            </div>

                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <div class="col-lg-11">
                                        <button type="button" id="prescricao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg  mr-2 " style="margin-left: -10px;">Anamnese</button>
                                        <button type="button" id="evolucao-btn" class="btn-grid btn btn-lg btn-outline-secondary btn-lg active">Exame Físico</button>
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

                                                    foreach ($row as $item) {
                                                        $seleciona = $item['Cid10Id'] == $rowAnamnese['EnAnaCid10'] ? "selected" : "";
                                                        print('<option value="' . $item['Cid10Id'] . '" ' . $seleciona . '>' . $item['Cid10Codigo'] . ' - ' . $item['Cid10Descricao'] . ' ' . '</option>');
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
                                                                WHERE SrVenUnidade = " . $_SESSION['UnidadeId'] . "
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
                
                                </div>
                                		
                            </div>-->

                            <div class="box-altaObito" style="display: block;">
                                <div class="col-lg-12">

                                </div>
                            </div>

                            <div class="card">
                                <div class=" card-body row">
                                    <div class="col-lg-12">
                                        <div class="form-group" style="margin-bottom:0px;">
                                            <?php 
                                                if (isset($SituaChave) && $SituaChave != "ATENDIDO") {
                                                    echo "<button class='btn btn-lg btn-success mr-1 salvarEfetivacaoAlta'>Salvar</button>";
                                                }
                                            ?>
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