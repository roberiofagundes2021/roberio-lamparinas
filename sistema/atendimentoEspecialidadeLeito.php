<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Quartos';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT EsLeiId, EsLeiNome, EsLeiTipoInternacao, EsLeiStatus, ti.TpIntNome, s.SituaNome, s.SituaCor, s.SituaChave
		FROM EspecialidadeLeito esl
		JOIN Situacao s on s.SituaId = esl.EsLeiStatus
        LEFT JOIN TipoInternacao ti on ti.TpIntId = esl.EsLeiTipoInternacao
	    WHERE EsLeiUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY EsLeiId ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

//Se estiver editando
if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'EDITA') {
    //Essa consulta é para preencher os campos a se editar
    $sql = "SELECT EsLeiId, EsLeiNome, EsLeiTipoInternacao
			FROM EspecialidadeLeito
			WHERE EsLeiId = " . $_POST['inputEspecialidadeLeitoId'] . ";";
    $result = $conn->query($sql);
    $rowEspecialidadeLeito = $result->fetch(PDO::FETCH_ASSOC);
    $_SESSION['msg'] = array();
}

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA') {
    try {
        //Edição
        if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA') {
            $sql = "UPDATE EspecialidadeLeito SET EsLeiNome = :sEsLeiNome, EsLeiTipoInternacao = :iEsLeiTipoInternacao, EsLeiUsuarioAtualizador = :iEsLeiUsuarioAtualizador
					WHERE EsLeiId = :iEsLeiId";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sEsLeiNome' => $_POST['inputEspecialidadeLeitoNome'],
                ':iEsLeiTipoInternacao' => $_POST['cmbEspecialidadeTipoInternacao'],
                ':iEsLeiUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iEsLeiId' => $_POST['inputEspecialidadeLeitoId']
            ));

            $_SESSION['msg']['mensagem'] = "Especialidade do Leito alterada!!!";
        } else { //inclusão
            $sql = "INSERT INTO EspecialidadeLeito (EsLeiNome, EsLeiTipoInternacao, EsLeiStatus, EsLeiUsuarioAtualizador, EsLeiUnidade)
					VALUES (:sEsLeiNome, :iEsLeiTipoInternacao, :bEsLeiStatus, :iEsLeiUsuarioAtualizador, :iEsLeiUnidade)";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sEsLeiNome' => $_POST['inputEspecialidadeLeitoNome'],
                ':iEsLeiTipoInternacao' => $_POST['cmbEspecialidadeTipoInternacao'],
                ':bEsLeiStatus' => 1,
                ':iEsLeiUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iEsLeiUnidade' => $_SESSION['UnidadeId'],
            ));

            $_SESSION['msg']['mensagem'] = "Especialidade do Leito incluída!!!";
        }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['tipo'] = "success";
    } catch (PDOException $e) {
        //} catch (PDOException $e) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro reportado com a Especialidade do Leito!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("atendimentoEspecialidadeLeito.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Especialidade do Leito</title>

    <?php include_once("head.php"); ?>

    <!-- Theme JS files -->
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/form_select2.js"></script>


    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

    <!-- Validação -->
    <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
    <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
    <script src="global_assets/js/demo_pages/form_validation.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#tblEspecialidadeLeito').DataTable({
                "order": [
                    [0, "asc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: true, //Quarto
                        width: "50%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Tipo de internação
                        width: "40%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Situação
                        width: "5%",
                        targets: [2]
                    },
                    {
                        orderable: false, //Ações
                        width: "5%",
                        targets: [3]
                    }
                ],
                dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: {
                        'first': 'Primeira',
                        'last': 'Última',
                        'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                        'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                    }
                }
            });

            // Select2 for length menu styling
            var _componentSelect2 = function() {
                if (!$().select2) {
                    console.warn('Warning - select2.min.js is not loaded.');
                    return;
                }

                // Initialize
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity,
                    dropdownAutoWidth: true,
                    width: 'auto'
                });
            };

            _componentSelect2();

            /* Fim: Tabela Personalizada */

            //Valida Registro Duplicado
            $('#enviar').on('click', function(e) {
                e.preventDefault();
                dadosValidos = true;
                var inputNome = $('#inputEspecialidadeLeitoNome').val().trim();
                var tipoInternacao = $('#cmbEspecialidadeTipoInternacao').val();
                var inputEstadoAtual = $('#inputEstadoAtual').val();

                //Se o usuário preencheu com espaços em branco ou não preencheu nada
                if (inputNome == '') {
                    alerta('Atenção', 'Especialidade do Leito é obrigatório!', 'error');
                    $('#inputEspecialidadeLeitoId').focus();
                    dadosValidos = false;
                    return;
                }
                if (tipoInternacao == '') {
                    alerta('Atenção', 'Selecione um tipo de internação!', 'error');
                    $('#cmbEspecialidadeTipoInternacao').focus();
                    dadosValidos = false;
                    return;
                }
                if (dadosValidos) {
                    //Esse ajax está sendo usado para verificar no banco se o registro já existe
                    $.ajax({
                        type: "POST",
                        url: "atendimentoEspecialidadeLeitoValida.php",
                        data: ('nome=' + inputNome + '&tipoInternacao=' + tipoInternacao + '&estadoAtual=' + inputEstadoAtual),
                        success: function(resposta) {

                            if (resposta == 1) {
                                alerta('Atenção', 'Esse registro já existe!', 'error');
                                return false;
                            }
                            if (resposta == 'EDITA') {
                                document.getElementById('inputEstadoAtual').value = 'GRAVA_EDITA';
                            } else {
                                document.getElementById('inputEstadoAtual').value = 'GRAVA_NOVO';
                            }

                            $("#formEspecialidadeLeito").submit();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alerta('Atenção', 'Erro ao salvar o Quarto!', 'error');
                            //console.log("Status: " + textStatus);
                            //console.log("Error: " + errorThrown);
                        }
                    })
                }
            })
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaEspecialidadeLeito(Permission, EsLeiId, EsLeiStatus, Tipo) {

            if (Permission == 1) {
                document.getElementById('inputEspecialidadeLeitoId').value = EsLeiId;
                document.getElementById('inputEspecialidadeLeitoStatus').value = EsLeiStatus;

                if (Tipo == 'edita') {
                    document.getElementById('inputEstadoAtual').value = "EDITA";
                    document.formEspecialidadeLeito.action = "atendimentoEspecialidadeLeito.php";
                } else if (Tipo == 'exclui') {
                    confirmaExclusao(document.formEspecialidadeLeito, "Tem certeza que deseja excluir essa Especialidade do Leito?", "atendimentoEspecialidadeLeitoExclui.php");
                } else if (Tipo == 'mudaStatus') {
                    document.formEspecialidadeLeito.action = "atendimentoEspecialidadeLeitoMudaSituacao.php";
                }

                document.formEspecialidadeLeito.submit();
            } else {
                alerta('Permissão Negada!', '');
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
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Basic responsive configuration -->
                        <div class="card">
                            <div class="card-header header-elements-inline">
                                <h3 class="card-title">Relação de Especialidades do Leito</h3>
                            </div>

                            <div class="card-body">
                                <form name="formEspecialidadeLeito" id="formEspecialidadeLeito" method="post" class="form-validate-jquery">

                                    <input type="hidden" id="inputEspecialidadeLeitoId" name="inputEspecialidadeLeitoId" value="<?php if (isset($_POST['inputEspecialidadeLeitoId'])) echo $_POST['inputEspecialidadeLeitoId']; ?>">
                                    <input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>">
                                    <input type="hidden" id="inputEspecialidadeLeitoStatus" name="inputEspecialidadeLeitoStatus">

                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label for="inputEspecialidadeLeitoNome">Especialidade do Leito<span class="text-danger"> *</span></label>
                                                <input type="text" id="inputEspecialidadeLeitoNome" name="inputEspecialidadeLeitoNome" class="form-control" placeholder="Especialidade do Leito" value="<?php if (isset($_POST['inputEspecialidadeLeitoId'])) echo $rowEspecialidadeLeito['EsLeiNome']; ?>" required autofocus>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="cmbEspecialidadeTipoInternacao">Tipo da internação<span class="text-danger"> *</span></label>
                                            <select id="cmbEspecialidadeTipoInternacao" name="cmbEspecialidadeTipoInternacao" class="form-control select-search" required>
                                                <option value="">Selecione</option>
                                                <?php
                                                $sql = "SELECT TpIntId, TpIntNome
                                                FROM TipoInternacao
                                                JOIN Situacao ON SituaId = TpIntStatus
                                                WHERE TpIntUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
                                                ORDER BY TpIntNome ASC";
                                                $result = $conn->query($sql);
                                                $rowTipoInternacao = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($rowTipoInternacao as $item) {
                                                    $seleciona = $item['TpIntId'] == $rowEspecialidadeLeito['EsLeiTipoInternacao'] ? "selected" : "";
                                                    print('<option value="' . $item['TpIntId'] . '" ' . $seleciona . '>' . $item['TpIntNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group" style="padding-top:25px;">
                                                <?php

                                                //editando
                                                if (isset($_POST['TpAcoId'])) {
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
                                                    print('<a href="atendimentoEspecialidadeLeito.php" class="btn btn-basic" role="button">Cancelar</a>');
                                                } else { //inserindo
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
                                                }

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <table id="tblEspecialidadeLeito" class="table">
                                <thead>
                                    <tr class="bg-slate">
                                        <th data-filter>Especialidade do Leito</th>
                                        <th data-filter>Tipo de internação</th>
                                        <th>Situação</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($row as $item) {

                                        $situacao = $item['SituaNome'];
                                        $situacaoClasse = 'badge badge-flat border-' . $item['SituaCor'] . ' text-' . $item['SituaCor'];
                                        $situacaoChave = '\'' . $item['SituaChave'] . '\'';

                                        print('
										<tr>
											<td>' . $item['EsLeiNome'] . '</td>
											<td>' . $item['TpIntNome'] . '</td>
											');

                                        print('<td><a href="#" onclick="atualizaEspecialidadeLeito(
                                            1,
                                            ' . $item['EsLeiId'] . ',
                                            ' . $situacaoChave . ',
                                            \'mudaStatus\'
                                        );"><span class="badge ' . $situacaoClasse . '">' . $situacao . '</span></a></td>');

                                        print('<td class="text-center">');

                                        print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaEspecialidadeLeito(
                                                    1,
                                                    ' . $item['EsLeiId'] . ',
                                                    ' . $item['EsLeiStatus'] . ',
                                                    \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaEspecialidadeLeito(
                                                    1,
                                                    ' . $item['EsLeiId'] . ',
                                                    ' . $item['EsLeiStatus'] . ',
                                                    \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
											</div>
										</div>								
										');


                                        print('
											</td>
										</tr>');
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
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