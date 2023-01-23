<?php 

    include_once("sessao.php"); 

    $_SESSION['PaginaAtual'] = 'Admissão Cirúrgica Pré-Operatório';

    include('global_assets/php/conexao.php');

    $iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

    if (isset($_SESSION['iAtendimentoId']) && $iAtendimentoId == null) {
        $iAtendimentoId = $_SESSION['iAtendimentoId'];
    }
    $_SESSION['iAtendimentoId'] = null;

    if(!$iAtendimentoId){
        irpara("atendimentoHospitalarListagem.php");	
    }

    //exame físico
    $sql = "SELECT TOP(1) EnAdPId
    FROM EnfermagemAdmissaoPediatrica
    WHERE EnAdPAtendimento = $iAtendimentoId
    ORDER BY EnAdPId DESC";
    $result = $conn->query($sql);
    $rowExameFisico= $result->fetch(PDO::FETCH_ASSOC);

    $iAtendimentoAdmissaoPediatrica = $rowExameFisico?$rowExameFisico['EnAdPId']:null;

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

if(isset($_POST['alergias'])){
    // $sql = "SELECT ";
    // $result = $conn->query($sql);
    // $row = $result->fetch(PDO::FETCH_ASSOC);
    // $sql = "INSERT INTO EnfermagemTransOperatoria(
    //     EnTrOAtendimento,EnTrODataInicio,EnTrOHoraInicio,EnTrODataFim,EnTrOHoraFim,EnTrOPrevisaoAlta,
    //     EnTrOTipoInternacao,EnTrOEspecialidadeLeito,EnTrOAla,EnTrOQuarto,EnTrOLeito,EnTrOProfissional,EnTrOPas,
    //     EnTrOPad,EnTrOFreqCardiaca,EnTrOFreqRespiratoria,EnTrOTemperatura,EnTrOSPO,EnTrOHGT,EnTrOPeso,
    //     EnTrOHoraEntrada,EnTrOHoraSaida,EnTrOSala,EnTrOProfiCirculante,EnTrOInicioAnestesia,EnTrOTerminoAnestesia,
    //     EnTrOTipoAnestesia,EnTrOProfiAnestesista,EnTrOProfiInstrumentador,EnTrOInicioCirurgia,EnTrOTerminoCirurgia,
    //     EnTrOProfiCirurgiao,EnTrOProfiCirurgiaoAssistente,EnTrODescPosicaoOperatoria,EnTrOServAdicionalBancoSangue,
    //     EnTrOServAdicionalRadiologia,EnTrOServAdicionalLaboratorio,EnTrOServAdicionalAnatPatologica,
    //     EnTrOServAdicionalUsoContraste,EnTrOServAdicionalOutros,EnTrOEncaminhamentoPosCirurgia,
    //     EnTrOEmUsoCateterEpidural,EnTrOEmUsoDrenoTubular,EnTrOEmUsoEntubacaoTraqueal,EnTrOEmUsoIntracath,
    //     EnTrOEmUsoKehr,EnTrOEmUsoPecaCirurgica,EnTrOEmUsoPenrose,EnTrOEmUsoProntuario,EnTrOEmUsoPuncaoPeriferica,
    //     EnTrOEmUsoRadiografia,EnTrOEmUsoSistemaSuccao,EnTrOEmUsoSondaGastrica,EnTrOEmUsoSondaVesical,
    //     EnTrOProfiEnfermeiro,EnTrOProfiTecnico,EnTrOProfiEnfermeiroCCO,EnTrOProfiTecnicoCCO,EnTrOProfiTecnicoRPA,
    //     EnTrOKitCME,EnTrOMedicacaoAdministrada,EnTrOObservacao)
    //     VALUES(:EnTrOAtendimento,:EnTrODataInicio,:EnTrOHoraInicio,:EnTrODataFim,:EnTrOHoraFim,
    //     :EnTrOPrevisaoAlta,:EnTrOTipoInternacao,:EnTrOEspecialidadeLeito,:EnTrOAla,:EnTrOQuarto,
    //     :EnTrOLeito,:EnTrOProfissional,:EnTrOPas,:EnTrOPad,:EnTrOFreqCardiaca,:EnTrOFreqRespiratoria,
    //     :EnTrOTemperatura,:EnTrOSPO,:EnTrOHGT,:EnTrOPeso,:EnTrOHoraEntrada,:EnTrOHoraSaida,:EnTrOSala,
    //     :EnTrOProfiCirculante,:EnTrOInicioAnestesia,:EnTrOTerminoAnestesia,:EnTrOTipoAnestesia,
    //     :EnTrOProfiAnestesista,:EnTrOProfiInstrumentador,:EnTrOInicioCirurgia,:EnTrOTerminoCirurgia,
    //     :EnTrOProfiCirurgiao,:EnTrOProfiCirurgiaoAssistente,:EnTrODescPosicaoOperatoria,
    //     :EnTrOServAdicionalBancoSangue,:EnTrOServAdicionalRadiologia,:EnTrOServAdicionalLaboratorio,
    //     :EnTrOServAdicionalAnatPatologica,:EnTrOServAdicionalUsoContraste,:EnTrOServAdicionalOutros,
    //     :EnTrOEncaminhamentoPosCirurgia,:EnTrOEmUsoCateterEpidural,:EnTrOEmUsoDrenoTubular,
    //     :EnTrOEmUsoEntubacaoTraqueal,:EnTrOEmUsoIntracath,:EnTrOEmUsoKehr,:EnTrOEmUsoPecaCirurgica,
    //     :EnTrOEmUsoPenrose,:EnTrOEmUsoProntuario,:EnTrOEmUsoPuncaoPeriferica,:EnTrOEmUsoRadiografia,
    //     :EnTrOEmUsoSistemaSuccao,:EnTrOEmUsoSondaGastrica,:EnTrOEmUsoSondaVesical,:EnTrOProfiEnfermeiro,
    //     :EnTrOProfiTecnico,:EnTrOProfiEnfermeiroCCO,:EnTrOProfiTecnicoCCO,:EnTrOProfiTecnicoRPA,:EnTrOKitCME,
    //     :EnTrOMedicacaoAdministrada,:EnTrOObservacao)";

    // $result = $conn->prepare($sql);

    // $result->execute(array(
    //     ":EnTrOAtendimento" => $iAtendimentoId,
    //     ":EnTrODataInicio" => date('Y-m-d'),
    //     ":EnTrOHoraInicio" => date('H:i'),
    //     ":EnTrODataFim" => date('Y-m-d'),
    //     ":EnTrOHoraFim" => date('H:i'),
    //     ":EnTrOPrevisaoAlta" => '',
    //     ":EnTrOTipoInternacao" => '',
    //     ":EnTrOEspecialidadeLeito" => $_POST[''],
    //     ":EnTrOAla" => $_POST[''],
    //     ":EnTrOQuarto" => $_POST[''],
    //     ":EnTrOLeito" => $_POST[''],
    //     ":EnTrOProfissional" => $_POST[''],
    //     ":EnTrOPas" => $_POST[''],
    //     ":EnTrOPad" => $_POST[''],
    //     ":EnTrOFreqCardiaca" => $_POST[''],
    //     ":EnTrOFreqRespiratoria" => $_POST[''],
    //     ":EnTrOTemperatura" => $_POST[''],
    //     ":EnTrOSPO" => $_POST[''],
    //     ":EnTrOHGT" => $_POST[''],
    //     ":EnTrOPeso" => $_POST[''],
    //     ":EnTrOHoraEntrada" => $_POST[''],
    //     ":EnTrOHoraSaida" => $_POST[''],
    //     ":EnTrOSala" => $_POST[''],
    //     ":EnTrOProfiCirculante" => $_POST[''],
    //     ":EnTrOInicioAnestesia" => $_POST[''],
    //     ":EnTrOTerminoAnestesia" => $_POST[''],
    //     ":EnTrOTipoAnestesia" => $_POST[''],
    //     ":EnTrOProfiAnestesista" => $_POST[''],
    //     ":EnTrOProfiInstrumentador" => $_POST[''],
    //     ":EnTrOInicioCirurgia" => $_POST[''],
    //     ":EnTrOTerminoCirurgia" => $_POST[''],
    //     ":EnTrOProfiCirurgiao" => $_POST[''],
    //     ":EnTrOProfiCirurgiaoAssistente" => $_POST[''],
    //     ":EnTrODescPosicaoOperatoria" => $_POST[''],
    //     ":EnTrOServAdicionalBancoSangue" => $_POST[''],
    //     ":EnTrOServAdicionalRadiologia" => $_POST[''],
    //     ":EnTrOServAdicionalLaboratorio" => $_POST[''],
    //     ":EnTrOServAdicionalAnatPatologica" => $_POST[''],
    //     ":EnTrOServAdicionalUsoContraste" => $_POST[''],
    //     ":EnTrOServAdicionalOutros" => $_POST[''],
    //     ":EnTrOEncaminhamentoPosCirurgia" => $_POST[''],
    //     ":EnTrOEmUsoCateterEpidural" => $_POST[''],
    //     ":EnTrOEmUsoDrenoTubular" => $_POST[''],
    //     ":EnTrOEmUsoEntubacaoTraqueal" => $_POST[''],
    //     ":EnTrOEmUsoIntracath" => $_POST[''],
    //     ":EnTrOEmUsoKehr" => $_POST[''],
    //     ":EnTrOEmUsoPecaCirurgica" => $_POST[''],
    //     ":EnTrOEmUsoPenrose" => $_POST[''],
    //     ":EnTrOEmUsoProntuario" => $_POST[''],
    //     ":EnTrOEmUsoPuncaoPeriferica" => $_POST[''],
    //     ":EnTrOEmUsoRadiografia" => $_POST[''],
    //     ":EnTrOEmUsoSistemaSuccao" => $_POST[''],
    //     ":EnTrOEmUsoSondaGastrica" => $_POST[''],
    //     ":EnTrOEmUsoSondaVesical" => $_POST[''],
    //     ":EnTrOProfiEnfermeiro" => $_POST[''],
    //     ":EnTrOProfiTecnico" => $_POST[''],
    //     ":EnTrOProfiEnfermeiroCCO" => $_POST[''],
    //     ":EnTrOProfiTecnicoCCO" => $_POST[''],
    //     ":EnTrOProfiTecnicoRPA" => $_POST[''],
    //     ":EnTrOKitCME" => $_POST[''],
    //     ":EnTrOMedicacaoAdministrada" => $_POST[''],
    //     ":EnTrOObservacao" => $_POST[''],
    // ));
}
// alergias
// textAlergias
// medicamentos
// textMedicamentos
// cirurgiaAnterior
// textCirurgiaAnterior
// jejum
// textJejum

// proteses
// textProteses
// acessorios
// textAcessorios
// doencas
// textDoencas
// tricotomia
// textTricotomia

// local
// lado
// esvaziamento
// textEsvaziamento

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Admissão Cirúrgica Pré-Operatório</title>

	<?php include_once("head.php"); ?>

    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>

    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<!-- /theme JS files -->	

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script type="text/javascript">

		$(document).ready(function() {
            $('#tblAcessoVenoso').DataTable({
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: false,   //item
					width: "1%",
					targets: [0]
				},
				{ 
					orderable: true,   //Data
					width: "25%",
					targets: [1]
				},
                { 
					orderable: true,   //Local
					width: "25%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Tipo
					width: "24%",
					targets: [3]
				},
				{ 
					orderable: false,  //Responsável
					width: "20%",
					targets: [4]
				},
                { 
					orderable: false,  //ações
					width: "5%",
					targets: [4]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
			});
            $('#tblConcentimento').DataTable({
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: false,   //item
					width: "1%",
					targets: [0]
				},
				{ 
					orderable: true,   //Data
					width: "35%",
					targets: [1]
				},
                { 
					orderable: true,   //descrição
					width: "60%",
					targets: [2]
				},
                { 
					orderable: false,  //ações
					width: "4%",
					targets: [3]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
			});
            $('#tblExame').DataTable({
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: false,   //item
					width: "1%",
					targets: [0]
				},
				{ 
					orderable: true,   //Data
					width: "35%",
					targets: [1]
				},
                { 
					orderable: true,   //descrição
					width: "60%",
					targets: [2]
				},
                { 
					orderable: false,  //ações
					width: "4%",
					targets: [3]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
			});
            // a função "cantaCaracteres" está no arquivo "custom.js"
            // "function cantaCaracteres(htmlTextId, numMaxCaracteres, htmlIdMostraRestantes)"

            $(".alergias").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textAlergiasViwer').removeClass('d-none')
                    }else{
                        $('#textAlergias').val('');
                        $('#textAlergiasViwer').addClass('d-none')
                        cantaCaracteres('textAlergias', 150, 'caracteresInputAlergias')
                    }
                })
            })
            $(".medicamentos").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textMedicamentosViwer').removeClass('d-none');
                    }else{
                        $('#textMedicamentos').val('');
                        $('#textMedicamentosViwer').addClass('d-none');
                        cantaCaracteres('textMedicamentos', 150, 'caracteresInputMedicamentos')
                    }
                })
            })
            $(".cirurgiaAnterior").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textCirurgiaAnteriorViwer').removeClass('d-none');
                    }else{
                        $('#textCirurgiaAnterior').val('');
                        $('#textCirurgiaAnteriorViwer').addClass('d-none');
                        cantaCaracteres('textCirurgiaAnterior', 150, 'caracteresInputCirurgiaAnterior')
                    }
                })
            })
            $(".jejum").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textJejumViwer').removeClass('d-none');
                    }else{
                        $('#textJejum').val('');
                        $('#textJejumViwer').addClass('d-none');
                        cantaCaracteres('textJejum', 150, 'caracteresInputJejum')
                    }
                })
            })
            $(".proteses").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textProtesesViwer').removeClass('d-none')
                    }else{
                        $('#textProteses').val('');
                        $('#textProtesesViwer').addClass('d-none')
                        cantaCaracteres('textProteses', 150, 'caracteresInputProteses')
                    }
                })
            })
            $(".acessorios").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textAcessoriosViwer').removeClass('d-none');
                    }else{
                        $('#textAcessorios').val('');
                        $('#textAcessoriosViwer').addClass('d-none');
                        cantaCaracteres('textAcessorios', 150, 'caracteresInputAcessorios')
                    }
                })
            })
            $('#doencas').on('change',function(e){
                if($(this).val()){
                    $('#textDoencasViwer').removeClass('d-none');
                }else{
                    $('#textDoencas').val('');
                    $('#textDoencasViwer').addClass('d-none');
                    cantaCaracteres('textDoencas', 150, 'caracteresInputDoencas')
                }
            })
            $(".tricotomia").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textTricotomiaViwer').removeClass('d-none');
                    }else{
                        $('#textTricotomia').val('');
                        $('#textTricotomiaViwer').addClass('d-none');
                        cantaCaracteres('textTricotomia', 150, 'caracteresInputTricotomia')
                    }
                })
            })
            $(".esvaziamento").each(function(){
                $(this).on('click',function(e){
                    if($(this).val() == 'SIM'){
                        $('#textEsvaziamentoViwer').removeClass('d-none');
                    }else{
                        $('#textEsvaziamento').val('');
                        $('#textEsvaziamentoViwer').addClass('d-none');
                        cantaCaracteres('textEsvaziamento', 150, 'caracteresInputEsvaziamento')
                    }
                })
            })

            $("#textMedicamentos").on('input', function(e){
                cantaCaracteres('textMedicamentos', 150, 'caracteresInputMedicamentos')
            })
            $("#textAlergias").on('input', function(e){
                cantaCaracteres('textAlergias', 150, 'caracteresInputAlergias')
            })
            $("#textCirurgiaAnterior").on('input', function(e){
                cantaCaracteres('textCirurgiaAnterior', 150, 'caracteresInputCirurgiaAnterior')
            })
            $("#textJejum").on('input', function(e){
                cantaCaracteres('textJejum', 150, 'caracteresInputJejum')
            })
            $("#textProteses").on('input', function(e){
                cantaCaracteres('textProteses', 150, 'caracteresInputProteses')
            })
            $("#textAcessorios").on('input', function(e){
                cantaCaracteres('textAcessorios', 150, 'caracteresInputAcessorios')
            })
            $("#textDoencas").on('input', function(e){
                cantaCaracteres('textDoencas', 150, 'caracteresInputDoencas')
            })
            $("#textTricotomia").on('input', function(e){
                cantaCaracteres('textTricotomia', 150, 'caracteresInputTricotomia')
            })
            $("#textEsvaziamento").on('input', function(e){
                cantaCaracteres('textEsvaziamento', 150, 'caracteresInputEsvaziamento')
            })

            $('#salvarAcessoModal').on('click',function(e){
                e.preventDefault()

            })
            $('#salvarConcentimentoModal').on('click',function(e){
                e.preventDefault()

            })
             $('#salvarExameModal').on('click',function(e){
                e.preventDefault()

             })

            $('#venosoBTN').on('click',function(e){
                e.preventDefault()
                $('#page-modal-acesso').fadeIn(200)
            })

            $('#termoBTN').on('click',function(e){
                e.preventDefault()
                $('#page-modal-concentimento').fadeIn(200)
            })

            $('#examesBTN').on('click',function(e){
                e.preventDefault()
                $('#page-modal-exames').fadeIn(200)
            })

            $('#addAcesso').on('click',function(e){
                e.preventDefault()
                $.ajax({
					type: 'POST',
					url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ACESSOVENOSO',
                        'data': $('#dataAcessoVenoso').val(),
                        'hora': $('#horaAcessoVenoso').val(),
                        'lado': $('#ladoAcessoVenoso').val(),
                        'calibre': $('#calibreAcessoVenoso').val(),
                        'responsavel': $('#responsavelAcessoVenoso').val(),

					},
					success: function(response) {
                        $('#dataAcessoVenoso').val('')
                        $('#horaAcessoVenoso').val('')
                        $('#ladoAcessoVenoso').val('')
                        $('#calibreAcessoVenoso').val('')
                        $('#responsavelAcessoVenoso').val('')

                        cheackList()
					}
				});
            })
            $('#addConcentimento').on('click',function(e){
                e.preventDefault()
                $.ajax({
					type: 'POST',
					url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'CONCENTIMENTO',
                        'data': $('#dataConcentimento').val(),
                        'hora': $('#horaConcentimento').val(),
                        'descricao': $('#descricaoConcentimento').val()
					},
					success: function(response) {
                        $('#dataConcentimento').val('')
                        $('#horaConcentimento').val('')
                        $('#descricaoConcentimento').val('')

                        cheackList()
					}
				});
            })
            $('#addExame').on('click',function(e){
                e.preventDefault()
                $.ajax({
					type: 'POST',
					url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'EXAMES',
                        'data': $('#dataExame').val(),
                        'hora': $('#horaExame').val(),
                        'descricao': $('#descricaoExame').val()
					},
					success: function(response) {
                        $('#dataExame').val('')
                        $('#horaExame').val('')
                        $('#descricaoExame').val('')

                        cheackList()
					}
				});
            })

            $('#salvarAdmissao').on('click', function(e){
                e.preventDefault()
                $.ajax({
                    type: 'POST',
                    url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
                    dataType: 'json',
                    data:{
                        'tipoRequest': 'SALVARADMISSAO',
                    },
                    success: function(response) {

                    }
                });
            })

            $('#modal-acesso-close-x').on('click', function(e){
                e.preventDefault()
                $('#tblAcessoVenosoViwer').addClass('d-none')
                $('#page-modal-acesso').fadeOut(200)
            })
            $('#modal-concentimento-close-x').on('click', function(e){
                e.preventDefault()
                $('#tblConcentimentoViwer').addClass('d-none')
                $('#page-modal-concentimento').fadeOut(200)
            })
            $('#modal-exames-close-x').on('click', function(e){
                e.preventDefault()
                $('#tblExameViwer').addClass('d-none')
                $('#page-modal-exames').fadeOut(200)
            })
            
		}); //document.ready

        function exclui(element){
            $.ajax({
                type: 'POST',
                url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
                dataType: 'json',
                data:{
                    'tipoRequest': 'EXCLUIR',
                    'id': $(element).data('id'),
                    'tipo': $(element).data('tipo')
                },
                success: function(response) {
                    cheackList()
                }
            });
        }
        function cheackList(){
            $.ajax({
                type: 'POST',
                url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
                dataType: 'json',
                data:{
                    'tipoRequest': 'CHECKLIST'
                },
                success: function(response) {
                    // Acesso venoso listagem
                        $('#tblAcessoVenoso').DataTable().clear().draw()
                        let tableAcesso = $('#tblAcessoVenoso').DataTable()
                        let rowNodeAcesso
                        let rowsAcesso = [];
                        if(response.acesso.length){
                            $('#tblAcessoVenosoViwer').removeClass('d-none')
                            response.acesso.forEach((item, index) => {
                                rowsAcesso.push([
                                    index+1,
                                    item.dataHora,
                                    item.lado == 'DI'?'Direito':'Esquerdo',
                                    item.calibre,
                                    item.responsavel,
                                    item.acoes
                                ])
                            })
                            rowsAcesso.forEach((item, index) => {
                                rowNodeAcesso = tableAcesso.row.add(item).draw().node()
                                // $(rowNodeAcesso).attr('class', 'text-left')
                                // $(rowNodeAcesso).find('td:eq(3)').attr('title', `Prontuário: ${item.identify.prontuario}`)
                            })
                        }else{
                            $('#tblAcessoVenosoViwer').addClass('d-none')
                        }
                    //
                    // Concentimento listagem
                        $('#tblConcentimento').DataTable().clear().draw()
                        let tableConcentimento = $('#tblConcentimento').DataTable()
                        let rowNodeConcentimento
                        let rowsConcentimento = [];

                        if(response.concentimento.length){
                            $('#tblConcentimentoViwer').removeClass('d-none')
                            response.concentimento.forEach((item, index) => {
                                rowsConcentimento.push([
                                    index+1,
                                    item.dataHora,
                                    item.descricao,
                                    item.acoes
                                ])
                            })
                            rowsConcentimento.forEach((item, index) => {
                                rowNodeConcentimento = tableConcentimento.row.add(item).draw().node()
                                // $(rowNodeConcentimento).attr('class', 'text-left')
                                // $(rowNodeConcentimento).find('td:eq(3)').attr('title', `Prontuário: ${item.identify.prontuario}`)
                            })
                        }else{
                            $('#tblConcentimentoViwer').addClass('d-none')
                        }
                    //
                    // Exames listagem
                        $('#tblExame').DataTable().clear().draw()
                        let tableExame = $('#tblExame').DataTable()
                        let rowNodeExame
                        let rowsExame = [];

                        if(response.exames.length){
                            $('#tblExameViwer').removeClass('d-none')
                            response.exames.forEach((item, index) => {
                                rowsExame.push([
                                    index+1,
                                    item.dataHora,
                                    item.descricao,
                                    item.acoes
                                ])
                            })
                            rowsExame.forEach((item, index) => {
                                rowNodeExame = tableExame.row.add(item).draw().node()
                                // $(rowNodeConcentimento).attr('class', 'text-left')
                                // $(rowNodeConcentimento).find('td:eq(3)').attr('title', `Prontuário: ${item.identify.prontuario}`)
                            })
                        }else{
                            $('#tblExameViwer').addClass('d-none')
                        }
                    //
                }
            });
        }

        // $.ajax({
        //     type: 'POST',
        //     url: 'filtraAdmissaoCirurgicaPreOperatorio.php',
        //     dataType: 'json',
        //     data:{
        //         'tipoRequest': 'ACESSOVENOSO',
        //     },
        //     success: function(response) {}
        // });

	</script>

    <style>
        textarea{
            height:80px;
        }
        .options{
            height:40px;
        }
	</style>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php
			include_once("menu-left.php");
			// include_once("menuLeftSecundarioVenda.php");
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
						<form name="formAtendimentoAdmissaoPediatrica" id="formAtendimentoAdmissaoPediatrica" method="post">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">

                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-6" style="text-align: left;">

                                        <div class="card-header header-elements-inline">
                                            <h3 class="card-title"><b>Admissão Cirúrgica Pré-Operatório</b></h3>
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

                            <div class="box-exameFisico" style="display: block;">
                                <div class="card">
                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Histórico Pré-Operatório</h3>

                                        <div class="header-elements">
                                            <div class="list-icons">
                                                <a class="list-icons-item" data-action="collapse"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                      
                                        <!-- linha 1 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Alergias</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Uso de medicamentos</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>História de Cirurgia Anterior</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Jejum Mínimo (8 horas)</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="alergias" class="alergias" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="alergias" class="alergias" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textAlergiasViwer" class="d-none">
                                                    <textarea id="textAlergias" name="textAlergias" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputAlergias"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="medicamentos" class="medicamentos" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="medicamentos" class="medicamentos" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textMedicamentosViwer" class="d-none">
                                                    <textarea id="textMedicamentos" name="textMedicamentos" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputMedicamentos"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="cirurgiaAnterior" class="cirurgiaAnterior" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="cirurgiaAnterior" class="cirurgiaAnterior" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textCirurgiaAnteriorViwer" class="d-none">
                                                    <textarea id="textCirurgiaAnterior" name="textCirurgiaAnterior" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputCirurgiaAnterior"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="jejum" class="jejum" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="jejum" class="jejum" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textJejumViwer" class="d-none">
                                                    <textarea id="textJejum" name="textJejum" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputJejum"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 2 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Uso de Próteses</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Remoção de Jóias e Acessórios</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Doenças Prévias</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Área Operatória Tricotomia</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="proteses" class="proteses" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="proteses" class="proteses" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textProtesesViwer" class="d-none">
                                                    <textarea id="textProteses" name="textProteses" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputProteses"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="acessorios" class="acessorios" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="acessorios" class="acessorios" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textAcessoriosViwer" class="d-none">
                                                    <textarea id="textAcessorios" name="textAcessorios" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputAcessorios"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <select id="doencas" name="doencas" class="select-search">
                                                        <option value=''>selecione</option>
                                                        <option value='1'>teste</option>
                                                        <option value='2'>teste2</option>
                                                    </select>
                                                </div>
                                                <div id="textDoencasViwer" class="d-none">
                                                    <textarea id="textDoencas" name="textDoencas" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputDoencas"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="tricotomia" class="tricotomia" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="tricotomia" class="tricotomia" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textTricotomiaViwer" class="d-none">
                                                    <textarea id="textTricotomia" name="textTricotomia" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputTricotomia"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 3 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Local da Cirurgia</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Lado</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Acesso Venoso</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Esvaziamento Vesical</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-3">
                                                <select id="local" name="local" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='1'>teste</option>
                                                    <option value='2'>teste2</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-3">
                                                <select id="lado" name="lado" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='1'>teste</option>
                                                    <option value='2'>teste2</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-3">
                                                <button id="venosoBTN" class="btn btn-lg btn-principal p-0 m-0" style="width:40px; height:35px;">
                                                    <i class='icon-search4' title='Pesquisar'></i>
                                                </button>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="col-lag-12 row options">
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="esvaziamento" class="esvaziamento" placeholder="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input type="radio" name="esvaziamento" class="esvaziamento" placeholder="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                                <div id="textEsvaziamentoViwer" class="d-none position-absolute" style="width:100%;">
                                                    <textarea id="textEsvaziamento" name="textEsvaziamento" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 150 caracteres<br>
                                                        <span id="caracteresInputEsvaziamento"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 4 -->
                                        <div class="col-6 mb-3 row m-0 p-0 py-2">
                                            <!-- titulos -->
                                            <div class="col-lg-6">
                                                <label>Termo de Consentimento para Cirurgia</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Exames Complementares de Imagens</label>
                                            </div>

                                            <!-- campos -->
                                            <div class="col-lg-6 row mr-2">
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" id="termo" readonly placeholder='Anexar aquirvo...'>
                                                </div>
                                                <div class="col-lg-2 p-0 m-0">
                                                    <button id='termoBTN' class="btn btn-lg btn-principal p-0 m-0" style="width:40px; height:35px;">
                                                        <i class='icon-attachment' title='Pesquisar'></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 row">
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" id="exames" readonly placeholder='Anexar aquirvo...'>
                                                </div>
                                                <div class="col-lg-2 p-0 m-0">
                                                    <button id='examesBTN' class="btn btn-lg btn-principal p-0 m-0" style="width:40px; height:35px;">
                                                        <i class='icon-attachment' title='Pesquisar'></i>
                                                    </button>
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

							<!--Modal-->
                            <div id="page-modal-acesso" class="custon-modal">
                                <div class="custon-modal-container" style="max-width: 1000px">
                                    <div class="card custon-modal-content">
                                        <div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
                                            <p class="h5">Acesso Venoso</p>
                                            <i id="modal-acesso-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                                        </div>
                                        <div class="px-0" style="overflow-y: scroll;">
                                            <div class="d-flex flex-row">
                                                <div class="col-lg-12">
                                                    <form id="novoAcessoVenoso" name="novoAcessoVenoso" method="POST" class="form-validate-jquery">
                                                        <!-- linha 1 -->
                                                        <div class="col-lg-12 m-0 p-0 mb-3 row">
                                                            <!-- titulos -->
                                                            <div class="col-lg-2">
                                                                <label>Data</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label>Hora</label>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <label>Local de punção</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label>Tipo/Calibre</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label>Responsável Técnico</label>
                                                            </div>
                                                            <div class="col-lg-1">
                                                            </div>
                                                            
                                                            <!-- campos -->
                                                            <div class="col-lg-2">
                                                                <input id="dataAcessoVenoso" class="form-control" type="date" name="dataAcessoVenoso">
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input id="horaAcessoVenoso" class="form-control" type="time" name="horaAcessoVenoso">
                                                            </div>

                                                            <div class="col-lg-3">
                                                                <select id="ladoAcessoVenoso" name="ladoAcessoVenoso" class="select-search">
                                                                    <option value=''>selecione</option>
                                                                    <option value='ES'>Esquerdo</option>
                                                                    <option value='DI'>Direito</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <input id="calibreAcessoVenoso" class="form-control" type="text" name="calibreAcessoVenoso">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <input id="responsavelAcessoVenoso" class="form-control" type="text" name="responsavelAcessoVenoso">
                                                            </div>
                                                            <div class="col-lg-1">
                                                                <button id="addAcesso" class="btn btn-lg btn-principal p-0 m-0" style="width:50px; height:35px;">
                                                                    <i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <div id="tblAcessoVenosoViwer" class="d-none">
                                                        <table class="table" id="tblAcessoVenoso">
                                                            <thead>
                                                                <tr class="bg-slate">
                                                                    <th>Item</th>
                                                                    <th>Data/Hora</th>
                                                                    <th>Local</th>
                                                                    <th>Tipo</th>										
                                                                    <th>Responsável</th>
                                                                    <th class="text-center">Ações</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-left m-2">
                                                <button id="salvarAcessoModal" class="btn btn-success" role="button">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="page-modal-concentimento" class="custon-modal">
                                <div class="custon-modal-container" style="max-width: 1000px">
                                    <div class="card custon-modal-content">
                                        <div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
                                            <p class="h5">Termo de Consentimento para Cirurgia</p>
                                            <i id="modal-concentimento-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                                        </div>
                                        <div class="px-0" style="overflow-y: scroll;">
                                            <div class="d-flex flex-row">
                                                <div class="col-lg-12">
                                                    <form id="novoConcentimento" name="novoConcentimento" method="POST" class="form-validate-jquery">
                                                        <!-- linha 1 -->
                                                        <div class="col-lg-12 m-0 p-0 mb-3 row">
                                                            <!-- titulos -->
                                                            <div class="col-lg-2">
                                                                <label>Data</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label>Hora</label>
                                                            </div>
                                                            <div class="col-lg-7">
                                                                <label>Desclição</label>
                                                            </div>
                                                            <div class="col-lg-1">
                                                                <!-- btn -->
                                                            </div>
                                                            
                                                            <!-- campos -->
                                                            <div class="col-lg-2">
                                                                <input id="dataConcentimento" class="form-control" type="date" name="dataConcentimento">
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input id="horaConcentimento" class="form-control" type="time" name="horaConcentimento">
                                                            </div>

                                                            <div class="col-lg-7">
                                                                <input id="descricaoConcentimento" class="form-control" type="text" name="descricaoConcentimento">
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <button id="addConcentimento" class="btn btn-lg btn-principal p-0 m-0" style="width:50px; height:35px;">
                                                                    <i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <div id="tblConcentimentoViwer" class="d-none">
                                                        <table class="table" id="tblConcentimento">
                                                            <thead>
                                                                <tr class="bg-slate">
                                                                    <th>Item</th>
                                                                    <th>Data/Hora</th>
                                                                    <th>Descrição</th>
                                                                    <th class="text-center">Ações</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-left m-2">
                                                <button id="salvarConcentimentoModal" class="btn btn-success" role="button">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="page-modal-exames" class="custon-modal">
                                <div class="custon-modal-container" style="max-width: 1000px">
                                    <div class="card custon-modal-content">
                                        <div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
                                            <p class="h5">Exames Complementares de Imagens</p>
                                            <i id="modal-exames-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                                        </div>
                                        <div class="px-0" style="overflow-y: scroll;">
                                            <div class="d-flex flex-row">
                                                <div class="col-lg-12">
                                                    <form id="novoExamesComplementares" name="novoExamesComplementares" method="POST" class="form-validate-jquery">
                                                        <!-- linha 1 -->
                                                        <div class="col-lg-12 m-0 p-0 mb-3 row">
                                                            <!-- titulos -->
                                                            <div class="col-lg-2">
                                                                <label>Data</label>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <label>Hora</label>
                                                            </div>
                                                            <div class="col-lg-7">
                                                                <label>Desclição</label>
                                                            </div>
                                                            <div class="col-lg-1">
                                                                <!-- btn -->
                                                            </div>
                                                            
                                                            <!-- campos -->
                                                            <div class="col-lg-2">
                                                                <input id="dataExame" class="form-control" type="date" name="dataExame">
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <input id="horaExame" class="form-control" type="time" name="horaExame">
                                                            </div>

                                                            <div class="col-lg-7">
                                                                <input id="descricaoExame" class="form-control" type="text" name="descricaoExame">
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <button id="addExame" class="btn btn-lg btn-principal p-0 m-0" style="width:50px; height:35px;">
                                                                    <i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <div id="tblExameViwer" class="d-none">
                                                        <table class="table" id="tblExame">
                                                            <thead>
                                                                <tr class="bg-slate">
                                                                    <th>Item</th>
                                                                    <th>Data/Hora</th>
                                                                    <th>Descrição</th>
                                                                    <th class="text-center">Ações</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-left m-2">
                                                <button id="salvarExameModal" class="btn btn-success" role="button">Confirmar</button>
                                            </div>
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