<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Leito';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT LeitoId, LeitoNome, LeitoQuarto, LeitoEspecialidade, LeitoStatus, LeitoUsuarioAtualizador, LeitoUnidade, qa.QuartNome, esl.EsLeiNome, s.SituaNome, s.SituaCor, s.SituaChave
		FROM Leito l
		JOIN Situacao s on s.SituaId = l.LeitoStatus
        LEFT JOIN Quarto qa on qa.QuartId = l.LeitoQuarto
        LEFT JOIN EspecialidadeLeito esl on esl.EsLeiId = l.LeitoEspecialidade
	    WHERE LeitoUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY LeitoId ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Se estiver editando
    if(isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'EDITA'){
    //Essa consulta é para preencher os campos com o Leito a se editar
    $sql = "SELECT LeitoId, LeitoNome, LeitoQuarto, LeitoEspecialidade
			FROM Leito
			WHERE LeitoId = " . $_POST['inputLeitoId'] . ";";
    $result = $conn->query($sql);
    $rowLeito = $result->fetch(PDO::FETCH_ASSOC);
    $_SESSION['msg'] = array();
}

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA') {
    try {
        //Edição
        if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA') {
            $sql = "UPDATE Leito SET LeitoNome = :sLeitoNome, LeitoQuarto = :iLeitoQuarto,  LeitoEspecialidade = :iLeitoEspecialidade, LeitoUsuarioAtualizador = :iLeitoUsuarioAtualizador
					WHERE LeitoId = :iLeitoId";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sLeitoNome' => $_POST['inputLeitoNome'],
                ':iLeitoQuarto' => $_POST['cmbQuarto'],
                ':iLeitoEspecialidade' => $_POST['cmbEspecialidadeLeito'],
                ':iLeitoUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iLeitoId' => $_POST['inputLeitoId']
            ));

            $_SESSION['msg']['mensagem'] = "Leito alterado!!!";
        } else { //inclusão
            $sql = "INSERT INTO Leito (LeitoNome, LeitoQuarto, LeitoEspecialidade, LeitoStatus, LeitoUsuarioAtualizador, LeitoUnidade)
					VALUES (:sLeitoNome, :iLeitoQuarto, :iLeitoEspecialidade, :bLeitoStatus, :iLeitoUsuarioAtualizador, :iLeitoUnidade)";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sLeitoNome' => $_POST['inputLeitoNome'],
                ':iLeitoQuarto' => $_POST['cmbQuarto'],
                ':iLeitoEspecialidade' => $_POST['cmbEspecialidadeLeito'],
                ':bLeitoStatus' => 1,
                ':iLeitoUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iLeitoUnidade' => $_SESSION['UnidadeId'],
            ));

            $_SESSION['msg']['mensagem'] = "Leito incluído!!!";
        }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['tipo'] = "success";

    } catch(PDOException $e) {
    //} catch (PDOException $e) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro reportado com o Leito!!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("atendimentoLeito.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Leito</title>

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
            $('#tblTipoInternacao').DataTable({
                "order": [
                    [0, "asc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: true, //Leito
                        width: "30%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Quarto
                        width: "30%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Especialidade do Leito
                        width: "30%",
                        targets: [2]
                    },
                    {
                        orderable: true, //Situação
                        width: "5%",
                        targets: [3]
                    },
                    {
                        orderable: false, //Ações
                        width: "5%",
                        targets: [4]
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
                var inputNome = $('#inputLeitoNome').val().trim();
                var quarto = $('#cmbQuarto').val();
                var especialidadeLeito = $('#cmbEspecialidadeLeito').val();
                var inputEstadoAtual = $('#inputEstadoAtual').val();

                //Se o usuário preencheu com espaços em branco ou não preencheu nada
                if (inputNome == '') {
                    alerta('Atenção', 'Nome do Leito é obrigatório!', 'error');
                    $('#inputLeitoId').focus();
                    dadosValidos = false;
                    return;
                }
                if (quarto == '') {
                    alerta('Atenção', 'Selecione um Quarto!', 'error');
                    $('#cmbQuarto').focus();
                    dadosValidos = false;
                    return;
                } 
                if (especialidadeLeito == '') {
                    alerta('Atenção', 'Selecione uma Especialidade do Leito!', 'error');
                    $('#cmbQuarto').focus();
                    dadosValidos = false;
                    return;
                } 
                if(dadosValidos) {
                    //Esse ajax está sendo usado para verificar no banco se o registro já existe
                    $.ajax({
                        type: "POST",
                        url: "atendimentoLeitoValida.php",
                        data: ('nome=' + inputNome + '&quarto=' + quarto + '&EspecialidadeLeito=' + especialidadeLeito + '&estadoAtual=' + inputEstadoAtual),
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

                            $("#formLeito").submit();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alerta('Atenção', 'Erro ao salvar o Leito!', 'error');
                            //console.log("Status: " + textStatus);
                            //console.log("Error: " + errorThrown);
                        }
                    })
                }
            })
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaLeito(Permission, LeitoId, LeitoNome, LeitoStatus, Tipo) {

            if (Permission == 1) {
                document.getElementById('inputLeitoId').value = LeitoId;
                document.getElementById('inputLeitoStatus').value = LeitoStatus;

                if (Tipo == 'edita') {
                    document.getElementById('inputEstadoAtual').value = "EDITA";
                    document.formLeito.action = "atendimentoLeito.php";
                } else if (Tipo == 'exclui') {
                    confirmaExclusao(document.formLeito, "Tem certeza que deseja excluir esse Leito?", "atendimentoLeitoExclui.php");
                } else if (Tipo == 'mudaStatus') {
                    document.formLeito.action = "atendimentoLeitoMudaSituacao.php";
                }

                document.formLeito.submit();
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
                                <h3 class="card-title">Relação de Leitos</h3>
                            </div>

                            <div class="card-body">
                                <form name="formLeito" id="formLeito" method="post" class="form-validate-jquery">

                                    <input type="hidden" id="inputLeitoId" name="inputLeitoId" value="<?php if (isset($_POST['inputLeitoId'])) echo $_POST['inputLeitoId']; ?>">
                                    <input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>" >
                                    <input type="hidden" id="inputLeitoStatus" name="inputLeitoStatus" >

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="inputLeitoNome">Leito<span class="text-danger"> *</span></label>
                                                <input type="text" id="inputLeitoNome" name="inputLeitoNome" class="form-control" placeholder="Leito" value="<?php if (isset($_POST['inputLeitoId'])) echo $rowLeito['LeitoNome']; ?>" required autofocus>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="cmbQuarto">Quarto<span class="text-danger">*</span></label>
                                            <select id="cmbQuarto" name="cmbQuarto" class="form-control select-search" required>
                                                <option value="">Selecione</option>
                                                <?php
                                                $sql = "SELECT QuartId, QuartNome
															FROM Quarto
															JOIN Situacao ON SituaId = QuartStatus
															WHERE QuartUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
														    ORDER BY QuartNome ASC";
                                                $result = $conn->query($sql);
                                                $rowLeito = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($rowLeito as $item) {
                                                    $seleciona = $item['QuartId'] == $rowLeito['LeitoQuarto'] ? "selected" : "";
                                                    print('<option value="' . $item['QuartId'] . '" ' . $seleciona . '>' . $item['QuartNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="cmbEspecialidadeLeito">Especialidade do Leito<span class="text-danger">*</span></label>
                                            <select id="cmbEspecialidadeLeito" name="cmbEspecialidadeLeito" class="form-control select-search" required>
                                                <option value="">Selecione</option>
                                                <?php
                                                $sql = "SELECT EsLeiId, EsLeiNome
															FROM EspecialidadeLeito
															JOIN Situacao ON SituaId = EsLeiStatus
															WHERE EsLeiUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
														    ORDER BY EsLeiNome ASC";
                                                $result = $conn->query($sql);
                                                $rowLeito = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($rowLeito as $item) {
                                                    $seleciona = $item['EsLeiId'] == $rowLeito['LeitoQuarto'] ? "selected" : "";
                                                    print('<option value="' . $item['EsLeiId'] . '" ' . $seleciona . '>' . $item['EsLeiNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group" style="padding-top:25px;">
                                                <?php

                                                //editando
                                                if (isset($_POST['EsLeiId'])) {
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
                                                    print('<a href="atendimentoLeito.php" class="btn btn-basic" role="button">Cancelar</a>');
                                                } else { //inserindo
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
                                                }

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <table id="tblTipoInternacao" class="table">
                                <thead>
                                    <tr class="bg-slate">
                                        <th data-filter>Leito</th>
                                        <th data-filter>Quarto</th>
                                        <th data-filter>Especialidade do Leito</th>
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
											<td>' . $item['LeitoNome'] . '</td>
											<td>' . $item['QuartNome'] . '</td>
                                            <td>' . $item['EsLeiNome'] . '</td>
											');

                                        print('<td><a href="#" onclick="atualizaLeito(1,' . $item['LeitoId'] . ', \'' . $item['LeitoNome'] . '\',' . $situacaoChave . ', \'mudaStatus\');"><span class="badge ' . $situacaoClasse . '">' . $situacao . '</span></a></td>');

                                        print('<td class="text-center">');



                                        print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaLeito(1,' . $item['LeitoId'] . ', \'' . $item['LeitoNome'] . '\', ' . $item['LeitoStatus'] . ', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaLeito(1,' . $item['LeitoId'] . ', \'' . $item['LeitoNome'] . '\', ' . $item['LeitoStatus'] . ', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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