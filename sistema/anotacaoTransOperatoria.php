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

    $sql = "SELECT P.ProfiId,P.ProfiNome,PFS.ProfiCbo,PFS.ProfiNome as profissao
    FROM Profissional P
    JOIN Profissao PFS ON PFS.ProfiId = P.ProfiProfissao
    WHERE P.ProfiUnidade = $_SESSION[UnidadeId]";
    $result = $conn->query($sql);
    $rowProfissionais = $result->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT KtCmeId,KtCmeNome
    FROM KitCme
    WHERE KtCmeUnidade = $_SESSION[UnidadeId]";
    $result = $conn->query($sql);
    $rowKitCME = $result->fetchAll(PDO::FETCH_ASSOC);
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
            // a função "cantaCaracteres" está no arquivo "custom.js"
            // "function cantaCaracteres(htmlTextId, numMaxCaracteres, htmlIdMostraRestantes)"

            $('#textMedicacao').on('input', function(e){
                cantaCaracteres('textMedicacao',150,'caracteresInputMedicacao')
            })
            $('#textObservacao').on('input', function(e){
                cantaCaracteres('textObservacao',800,'caracteresInputObservacao')
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
                                                <label>Entrada Hora</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Sala</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Saída Hora</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Circulante</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-3">
                                                <input id="entradaHora" class="form-control" type="text" name="entradaHora">
                                            </div>
                                            <div class="col-lg-3">
                                                <input id="sala" class="form-control" type="text" name="sala">
                                            </div>
                                            <div class="col-lg-3">
                                                <input id="saidaHora" class="form-control" type="text" name="saidaHora">
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissional" name="profissional" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 2 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-2">
                                                <label>Início da anestesia</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Término da anestesia</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Tipo de anestesia</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Anestesista</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Instrumentador</label>
                                            </div>
                                            
                                            <!-- campos -->

                                            <div class="col-lg-2">
                                                <input id="inicioAnestesia" class="form-control" type="time" name="inicioAnestesia">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="terminoAnestesia" class="form-control" type="time" name="terminoAnestesia">
                                            </div>
                                            <div class="col-lg-2">
                                                <select id="tipoAnestesia" name="tipoAnestesia" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='LO'>LOCAL</option>
                                                    <option value='PL'>PLEXULAR</option>
                                                    <option value='GV'>GERAL (VM)</option>
                                                    <option value='GS'>GERAL (SEDAÇÃO)</option>
                                                    <option value='BE'>BLOQUEIOS ESPINHAIS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissionalAnestesista" name="profissionalAnestesista" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissionalInstrumentador" name="profissionalInstrumentador" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 3 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-2">
                                                <label>Início da cirurgia</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Término da cirurgia</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Profissional Cirurgião</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Cirurgião Assistente</label>
                                            </div>
                                            
                                            <!-- campos -->

                                            <div class="col-lg-2">
                                                <input id="inicioAnestesia" class="form-control" type="time" name="inicioAnestesia">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="terminoAnestesia" class="form-control" type="time" name="terminoAnestesia">
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="profissionalCirurgiao" name="profissionalCirurgiao" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select id="profissionalAssistente" name="profissionalAssistente" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 4 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Descrição da posição operatória</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Serviços adicionais</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Encaminhamento pós cirurgia </label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Em uso pós cirurgia</label>
                                            </div>
                                            
                                            <!-- campos -->

                                            <div class="col-lg-3">
                                                <select id="descricaoPosicao" name="descricaoPosicao" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='VE'>VENTRAL</option>
                                                    <option value='DO'>DORSAL</option>
                                                    <option value='LA'>LATERAL</option>
                                                    <option value='GI'>GINECOLÓGICA</option>
                                                    <option value='SG'>SEMI-GINECOLÓGICA</option>
                                                    <option value='TR'>TRENDELENBURG</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="servicosAdicionais" name="servicosAdicionais[]" class="form-control multiselect-filtering" multiple="multiple">
                                                    <option value='BS'>BANCO DE SANGUE</option>
                                                    <option value='RA'>RADIOLOGIA</option>
                                                    <option value='LA'>LABORATÓRIO</option>
                                                    <option value='AN'>ANATOMIA PATOLÓGICA</option>
                                                    <option value='UC'>USO DE CONTRASTE</option>
                                                    <option value='OU'>OUTROS</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="encaminhamento" name="encaminhamento" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='SO'>SALA PÓS-OPERATÓRIA</option>
                                                    <option value='LE'>LEITO</option>
                                                    <option value='CTI'>CTI</option>
                                                    <option value='UTI'>UTI</option>
                                                    <option value='SU'>SEMI UTI</option>
                                                    <option value='OB'>ÓBITO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="usoPosCirurgia" name="usoPosCirurgia[]" class="form-control multiselect-filtering" multiple="multiple">
                                                    <option value='CE'>Cateter Epidural</option>
                                                    <option value='DT'>Drenos Tubulares</option>
                                                    <option value='ET'>Entubação Traqueal</option>
                                                    <option value='IN'>Intracath</option>
                                                    <option value='KE'>Kehr</option>
                                                    <option value='PC'>Peças Cirurgicas</option>
                                                    <option value='PE'>Penrose</option>
                                                    <option value='PR'>Prontuário</option>
                                                    <option value='PP'>Punção Periférica</option>
                                                    <option value='RA'>Radiografias</option>
                                                    <option value='SS'>Sistema de Sucção</option>
                                                    <option value='SG'>Sonda Gastrica</option>
                                                    <option value='SV'>Sonda Vesical</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 5 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Profissional Enfermeiro</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Técnico</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Enfermeiro CCO</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Profissional Técnico CCO</label>
                                            </div>
                                            
                                            <!-- campos -->

                                            <div class="col-lg-3">
                                                <select id="profissionalEnfermeiro" name="profissionalEnfermeiro" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissionalTecnico" name="profissionalTecnico" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissionalEnfermeiroCCO" name="profissionalEnfermeiroCCO" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="profissionalTecnicoCCO" name="profissionalTecnicoCCO" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 6 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-3">
                                                <label>Profissional Técnico RPA</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Kit CME</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <!--  -->
                                            </div>
                                            
                                            <!-- campos -->

                                            <div class="col-lg-3">
                                                <select id="profissionalTecnicoRPA" name="profissionalTecnicoRPA" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowProfissionais as $item){
                                                            echo "<option value='$item[ProfiId]'>$item[ProfiNome] - $item[profissao] - $item[ProfiCbo]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="kitCME" name="kitCME" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <?php
                                                        foreach($rowKitCME as $item){
                                                            echo "<option value='$item[KtCmeId]'>$item[KtCmeNome]</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <!--  -->
                                            </div>
                                        </div>

                                        <!-- linha 7 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-12">
                                                <label>Medicação Administrada (digitação livre)</label>
                                            </div>

                                            <!-- campos -->
                                            <div class="col-lg-12">
                                                <textarea id="textMedicacao" name="textMedicacao" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                <small class="text-muted form-text">
                                                    Máx. 150 caracteres<br>
                                                    <span id="caracteresInputMedicacao"></span>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- linha 8 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-12">
                                                <label>Observações</label>
                                            </div>

                                            <!-- campos -->
                                            <div class="col-lg-12">
                                                <textarea id="textObservacao" name="textObservacao" class="form-control" rows="4" cols="4" maxLength="800" placeholder="" ></textarea>
                                                <small class="text-muted form-text">
                                                    Máx. 800 caracteres<br>
                                                    <span id="caracteresInputObservacao"></span>
                                                </small>
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