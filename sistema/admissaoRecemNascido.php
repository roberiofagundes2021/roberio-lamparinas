<?php 

    include_once("sessao.php"); 

    $_SESSION['PaginaAtual'] = 'Admissão de Recém Nascido';

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

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Admissão de Recém Nascido</title>

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

            $("#textMedicamentos").on('input', function(e){
                cantaCaracteres('textMedicamentos', 150, 'caracteresInputMedicamentos')
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

            $('#modal-acesso-close-x').on('click', function(e){
                e.preventDefault()
                $('#tblAcessoVenosoViwer').addClass('d-none')
                $('#page-modal-acesso').fadeOut(200)
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
        function mudarGrid(grid){
            // if (grid == 'preparto') {
            //     document.getElementById("box-pacientes-espera").style.display = 'block';
            //     document.getElementById("box-pacientes-atendidos").style.display = 'none';
            //     document.getElementById("box-pacientes-observacao").style.display = 'none';
            //     document.getElementById("box-pacientes-atendimento").style.display = 'none';

            // } else if (grid == 'admissao') {
            //     document.getElementById("box-pacientes-atendidos").style.display = 'block';
            //     document.getElementById("box-pacientes-espera").style.display = 'none';
            //     document.getElementById("box-pacientes-observacao").style.display = 'none';
            //     document.getElementById("box-pacientes-atendimento").style.display = 'none';

            // }
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
            height:40px;
        }
        .options{
            height:40px;
        }
        .text-float-border{
            position: absolute;
            top: 5px;
            left: 60px;
            background-color: #ffffff;
            padding-left: 10px;
            padding-right: 10px;
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
                                            <h3 class="card-title"><b>Admissão de RN</b></h3>
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
                                <?php //include ('atendimentoDadosSinaisVitais.php'); ?>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">	
                                            <button type="button" id="pacientes-espera-btn" class="btn-grid btn btn-outline-secondary btn-lg" onclick="mudarGrid('preparto')" >Admissão Pré Parto</button>
                                            <button type="button" id="pacientes-atendimento-btn" class="btn-grid btn btn-outline-secondary btn-lg active" onclick="mudarGrid('admissao')" >Admissão RN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <h3 class="card-title">Admissão RN</h3>
                                </div>
                            </div>

                            <div class="box-exameFisico" style="display: block;">
                                <div class="card">
                                    <div class="card-header header-elements-inline">
                                        <h3 class="card-title">Evolução Recém Nascido</h3>

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
                                            <div class="col-lg-5">
                                                <label>RN / Mãe</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Data de Nascimento</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Hora de Nascimento</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Sexo</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-5">
                                                <input id="RN" class="form-control" type="text" name="RN">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="dataNascimento" class="form-control" type="date" name="dataNascimento">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="horaNascimento" class="form-control" type="time" name="horaNascimento">
                                            </div>
                                            <div class="col-lg-3">
                                                <select id="sexo" name="sexo" class="select-search">
                                                    <option value=''>selecione</option>
                                                    <option value='M'>Masculino</option>
                                                    <option value='F'>Feminino</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 2 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-2">
                                                <label>Choro Presente</label>
                                            </div>
                                            <div class="col-lg-1">
                                                <label>Apgar 1 min</label>
                                            </div>
                                            <div class="col-lg-1">
                                                <label>Apgar 5 min</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Amamentação na 1ª hora de vida</label>
                                            </div>
                                            <div class="col-lg-5">
                                                <label>Motivo do não aleitamento</label>
                                            </div>
                                            
                                            <!-- campos -->                                            
                                            <div class="col-lg-2">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="choro" class="choro" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="choro" class="choro" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <input id="Apgar1" class="form-control" type="number" name="Apgar1">
                                            </div>
                                            <div class="col-lg-1">
                                                <input id="Apgar5" class="form-control" type="number" name="Apgar5">
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="amamentacao" class="" value="SIM">
                                                        <label>SIM</label>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="amamentacao" class="" value="NÃO">
                                                        <label>NÃO</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <textarea id="motivoAleitamento" name="motivoAleitamento" class="form-control" rows="4" cols="4" maxLength="150" placeholder="" ></textarea>
                                                <small class="text-muted form-text">
                                                    Máx. 30 caracteres<br>
                                                    <span id="caracteresInputMotivoAleitamento"></span>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <h4 class="card-title"><b>SSVV / Monitoramento</b></h4>
                                        </div>

                                        <!-- linha 3 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-2">
                                                <label>FC (bpm)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>FR (irpm)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Temperatura (C°)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>SPO   (%)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>HGT (mg/dl)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Peso (Kg)</label>
                                            </div>
                                            
                                            <!-- campos -->
                                            <div class="col-lg-2">
                                                <input id="FC" name="FC"  class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="FR" name="FR" class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="Temperatura" name="Temperatura"  class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="SPO" name="SPO" class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="HGT" name="HGT"  class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="Peso" name="Peso" class="form-control" type="text">
                                            </div>
                                        </div>

                                        <!-- linha 4 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-2">
                                                <label>Idade Gestacional</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>TS/Fator RH</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>Estatura (cm)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>PC (cm)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>PT (cm)</label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label>PA (cm)</label>
                                            </div>
                                            
                                            <!-- campos -->
                                            <div class="col-lg-2">
                                                <input id="idadeGestacional" name="idadeGestacional"  class="form-control" type="text">
                                            </div>
                                            <div class="col-lg-2">
                                                <select id="fatorRH" name="fatorRH" class="select">
                                                    <option value=''>selecione</option>
                                                    <option value='A+'>A+</option>
                                                    <option value='B+'>B+</option>
                                                    <option value='AB+'>AB+</option>
                                                    <option value='O+'>O+</option>
                                                    <option value='A-'>A-</option>
                                                    <option value='B-'>B-</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="Estatura" name="Estatura"  class="form-control" type="number" value="0">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="PC" name="PC" class="form-control" type="number" value="0">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="PT" name="PT"  class="form-control" type="number" value="0">
                                            </div>
                                            <div class="col-lg-2">
                                                <input id="PA" name="PA" class="form-control" type="number" value="0">
                                            </div>
                                        </div>

                                        <!-- linha 5 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-6">
                                                <div class="col-lg-6r">
                                                    <h4 class="card-title"><b>Atividade</b></h4>
                                                </div>

                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-2">
                                                        <input name="hipoativo" type="checkbox"/>
                                                        <label>Hipoativo</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="sonolento" type="checkbox"/>
                                                        <label>Sonolento</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="ativo" type="checkbox"/>
                                                        <label>Ativo</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="choroso" type="checkbox"/>
                                                        <label>Choroso</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="gemente" type="checkbox"/>
                                                        <label>Gemente</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textAtividade" name="textAtividade" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputAtividade"></span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="col-lg-6r">
                                                    <h4 class="card-title"><b>Coloração</b></h4>
                                                </div>

                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-2">
                                                        <input name="corado" type="checkbox"/>
                                                        <label>Corado</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="hipocorado" type="checkbox"/>
                                                        <label>Hipocorado</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="cianotico" type="checkbox"/>
                                                        <label>Cianotico</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="icterico" type="checkbox"/>
                                                        <label>Ictérico</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="pletorico" type="checkbox"/>
                                                        <label>Pletórico</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textColoracao" name="textColoracao" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputColoracao"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 6 -->
                                        <div class="col-lg-6 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-6">
                                                <label>Hidratação</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Fontanela</label>
                                            </div>
                                            
                                            <!-- campos -->
                                            <div class="col-lg-6">
                                                <select id="hidratacao" name="hidratacao" class="select">
                                                    <option value=''>selecione</option>
                                                    <option value='S'>SIM</option>
                                                    <option value='N'>NÃO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select id="fatorRH" name="fatorRH" class="select">
                                                    <option value=''>selecione</option>
                                                    <option value='NO'>NOMOTENSA</option>
                                                    <option value='AB'>ABAULADA</option>
                                                    <option value='DE'>DEPRIMIDA</option>
                                                    <option value='CA'>CAVALGADURA</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- linha 7 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-6">
                                                <div class="col-lg-12">
                                                    <h4 class="card-title"><b>Pele</b></h4>
                                                </div>
                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-3">
                                                        <input name="pele" type="radio"/>
                                                        <label>Íntegra</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="pele" type="radio"/>
                                                        <label>Descamativa</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="pele" type="radio"/>
                                                        <label>Eritema Tóxico</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="pele" type="radio"/>
                                                        <label>Enrugada</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textPele" name="textPele" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputPele"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="col-lg-12">
                                                    <h4 class="card-title"><b>Reflexos Normais</b></h4>
                                                </div>
                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-3">
                                                        <input name="succao" type="checkbox"/>
                                                        <label>Sucção</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="moro" type="checkbox"/>
                                                        <label>Moro</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="preensaoPalmar" type="checkbox"/>
                                                        <label>Preensão Palmar</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="pressaoPlantar" type="checkbox"/>
                                                        <label>Pressão Plantar</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textReflexos" name="textReflexos" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputReflexos"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 8 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-12">
                                                <h4 class="card-title"><b>Cabeça</b></h4>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-3">
                                                        <input name="mascaraEquimotica" type="checkbox"/>
                                                        <label>Máscara Equimótica</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="Cefalohematoma" type="checkbox"/>
                                                        <label>Cefalohematoma</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="bossa" type="checkbox"/>
                                                        <label>BOSSA</label>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <input name="gig" type="checkbox"/>
                                                        <label>GIG</label>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <input name="pig" type="checkbox"/>
                                                        <label>PIG</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="escoriacoes" type="checkbox"/>
                                                        <label>Escoriações</label>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <textarea id="textCabeca" name="textCabeca" class="form-control" rows="4" cols="4" maxLength="50" placeholder="" ></textarea>
                                                        <small class="text-muted form-text">
                                                            Máx. 50 caracteres<br>
                                                            <span id="caracteresInputCabeca"></span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 row">
                                                <!-- titulos -->
                                                <div class="col-lg-12">
                                                    <h6 class="card-title"><b>Abdome</b></h6>
                                                </div>

                                                <div class="col-lg-12">
                                                    <select id="abdome" name="abdome" class="select">
                                                        <option value=''>selecione</option>
                                                        <option value='IN'>ÍNTEGRO</option>
                                                        <option value='FL'>FLÁCIDO</option>
                                                        <option value='GL'>GLOBOSO</option>
                                                        <option value='DI'>DISTENTIDO</option>
                                                        <option value='TI'>TIMPÂNICO</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-lg-12 mt-2">
                                                    <h6 class="card-title"><b>Sucção Satisfatória</b></h6>
                                                </div>
                                                
                                                <div class="col-lg-12">
                                                    <select id="succao" name="succao" class="select">
                                                        <option value=''>selecione</option>
                                                        <option value='S'>SIM</option>
                                                        <option value='N'>NÃO</option>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 9 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-6">
                                                <div class="col-lg-12">
                                                    <h4 class="card-title"><b>Padrão Respiratório</b></h4>
                                                </div>
                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-3">
                                                        <input name="padraoRespiratorio" type="radio"/>
                                                        <label>Eupnéico</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="padraoRespiratorio" type="radio"/>
                                                        <label>Taquipnéia</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="padraoRespiratorio" type="radio"/>
                                                        <label>Bradipnéia</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="padraoRespiratorio" type="radio"/>
                                                        <label>Obestrução Nasal</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textPadraoRespiratorio" name="textPadraoRespiratorio" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputPadraoRespiratorio"></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="col-lg-12">
                                                    <h4 class="card-title"><b>Genturinário</b></h4>
                                                </div>
                                                <div class="col-lg-12 row">
                                                    <div class="col-lg-2">
                                                        <input name="integro" type="checkbox"/>
                                                        <label>Íntegro</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="diurese" type="checkbox"/>
                                                        <label>Diurese</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input name="anusPervio" type="checkbox"/>
                                                        <label>Ânus Pérvio</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="Meconio" type="checkbox"/>
                                                        <label>Mecônio</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input name="outros" type="checkbox"/>
                                                        <label>Outros</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textGenturinario" name="textGenturinario" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputGenturinario"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- linha 12 -->
                                        <div class="col-lg-12 mb-3 row">
                                            <div class="col-lg-12">
                                                <h4 class="card-title"><b>Coto Umbilical</b></h4>
                                            </div>
                                            <div class="col-lg-12 row">
                                                <div class="col-lg-2">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Limpo e Seco</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Gelatinoso</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Mumificado</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Úmido</label>
                                                </div>
                                                <div class="col-lg-1">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Sujo</label>
                                                </div>
                                                <div class="col-lg-1">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Fétido</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input name="padraoRespiratorio" type="checkbox"/>
                                                    <label>Hiperemia</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea id="textCotoUmbilical" name="textCotoUmbilical" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                <small class="text-muted form-text">
                                                    Máx. 30 caracteres<br>
                                                    <span id="caracteresInputCotoUmbilical"></span>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- linha 6 -->
                                        <div class="col-lg-6 mb-3 row">
                                            <!-- titulos -->
                                            <div class="col-lg-6">
                                                <label>Cateter</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Sonda</label>
                                            </div>
                                            
                                            <!-- campos -->
                                            <div class="col-lg-6">
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input type="radio" name="cateter" class="cateter" value="SIM">
                                                            <label>SIM</label>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input type="radio" name="cateter" class="cateter" value="NÃO">
                                                            <label>NÃO</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textCateter" name="textCateter" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputCateter"></span>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <input type="radio" name="sonda" class="sonda" value="SIM">
                                                            <label>SIM</label>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <input type="radio" name="sonda" class="sonda" value="NÃO">
                                                            <label>NÃO</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea id="textSonda" name="textSonda" class="form-control" rows="4" cols="4" maxLength="30" placeholder="" ></textarea>
                                                    <small class="text-muted form-text">
                                                        Máx. 30 caracteres<br>
                                                        <span id="caracteresInputSonda"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card p-3">
                                <div style="border: 1px solid black" class="p-3 mb-4">
                                    <h5 class="card-title text-float-border"><b>Diagnóstico de Enfermagem</b></h5>

                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="dorAguda" type="checkbox"/>
                                            <label>DOR AGUDA</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoGlicemia" type="checkbox"/>
                                            <label>RISCO DE GLICEMIA</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="deficitAutoCuidado" type="checkbox"/>
                                            <label>DÉFICIT NO AUTO-CUIDADO</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoIctericia" type="checkbox"/>
                                            <label>RISCO DE ICTERICIA</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="eliminacaoUrinaria" type="checkbox"/>
                                            <label>ELIMINAÇÃO URINÁRIA</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoInfeccao" type="checkbox"/>
                                            <label>RISCO DE INFECÇÃO</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="nutricaoDesequilibrada" type="checkbox"/>
                                            <label>NUTRIÇÃO DESEQUILIBRADA QUE AS NECESSIDADES CORPORAIS</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoIntegridade" type="checkbox"/>
                                            <label>RISCO DE INTEGRIDADE</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="padraoRespiratorio" type="checkbox"/>
                                            <label>PADRÃO RESPIRATÓRIO</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoSufocacao" type="checkbox"/>
                                            <label>RISCO DE SUFOCAÇÃO</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="padraoSono" type="checkbox"/>
                                            <label>PADRÃO DE SONO</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="termorregulacao" type="checkbox"/>
                                            <label>TERMORREGULAÇÃO</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 row">
                                        <div class="col-lg-6">
                                            <input name="riscoConstipacao" type="checkbox"/>
                                            <label>RISCO DE CONSTIPAÇÃO</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input name="riscoOutros" type="checkbox"/>
                                            <label>OUTROS</label>
                                            <input name="riscoOutrosText" type="text" class="form-control" placeholder="Descrição..."/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-3 row">
                                    <!-- titulos -->
                                    <div class="col-lg-12">
                                        <label>Avaliação de Enfermagem</label>
                                    </div>

                                    <!-- campos -->
                                    <div class="col-lg-12">
                                        <textarea id="textAvaliacao" name="textAvaliacao" class="form-control" rows="4" cols="4" maxLength="100" placeholder="" ></textarea>
                                        <small class="text-muted form-text">
                                            Máx. 100 caracteres<br>
                                            <span id="caracteresInputAvaliacao"></span>
                                        </small>
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