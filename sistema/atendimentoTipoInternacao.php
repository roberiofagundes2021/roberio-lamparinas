<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Tipos de Internação';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT TpIntId,TpIntNome,TpIntTipoAcomodacao,TpIntStatus,TpIntUsuarioAtualizador,TpIntUnidade, ta.TpAcoNome, s.SituaNome, s.SituaCor, s.SituaChave
		FROM TipoInternacao ti
		JOIN Situacao s on s.SituaId = ti.TpIntStatus
        LEFT JOIN TipoAcomodacao ta on ta.TpAcoId = ti.TpIntTipoAcomodacao
	    WHERE TpIntUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY TpIntId ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Se estiver editando
    if(isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'EDITA'){
    //Essa consulta é para preencher os campos com o tipo de internação a se editar
    $sql = "SELECT TpIntId, TpIntNome, TpIntTipoAcomodacao
			FROM TipoInternacao
			WHERE TpIntId = " . $_POST['inputTpIntId'] . ";";
    $result = $conn->query($sql);
    $rowTipoInternacao = $result->fetch(PDO::FETCH_ASSOC);
    $_SESSION['msg'] = array();
}

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA') {
    try {
        //Edição
        if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA') {
            $sql = "UPDATE TipoInternacao SET TpIntNome = :sTpIntNome, TpIntTipoAcomodacao = :iTpIntTipoAcomodacao, TpIntUsuarioAtualizador = :iTpIntUsuarioAtualizador
					WHERE TpIntId = :iTpIntId";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sTpIntNome' => $_POST['inputTpIntNome'],
                ':iTpIntTipoAcomodacao' => $_POST['cmbTpIntTipoAcomodacao'],
                ':iTpIntUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iTpIntId' => $_POST['inputTpIntId']
            ));

            $_SESSION['msg']['mensagem'] = "Tipo de internação alterada!!!";
        } else { //inclusão
            $sql = "INSERT INTO TipoInternacao (TpIntNome, TpIntTipoAcomodacao, TpIntStatus, TpIntUsuarioAtualizador, TpIntUnidade)
					VALUES (:sTpIntNome, :iTpIntTipoAcomodacao, :bTpIntStatus, :iTpIntUsuarioAtualizador, :iTpIntUnidade)";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sTpIntNome' => $_POST['inputTpIntNome'],
                ':iTpIntTipoAcomodacao' => $_POST['cmbTpIntTipoAcomodacao'],
                ':bTpIntStatus' => 1,
                ':iTpIntUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iTpIntUnidade' => $_SESSION['UnidadeId'],
            ));

            $_SESSION['msg']['mensagem'] = "Tipo de internação incluída!!!";
        }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['tipo'] = "success";

    } catch(PDOException $e) {
    //} catch (PDOException $e) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro reportado com o tipo de internação!!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("atendimentoTipoInternacao.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Tipo de internação</title>

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
                        orderable: true, //Tipo de internação
                        width: "50%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Tipo de acomodação
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
                var inputNome = $('#inputTpIntNome').val().trim();
                var tipoAcomodacao= $('#cmbTpIntTipoAcomodacao').val();
                var inputEstadoAtual = $('#inputEstadoAtual').val();

                //Se o usuário preencheu com espaços em branco ou não preencheu nada
                if (inputNome == '') {
                    alerta('Atenção', 'Nome do tipo da internação é obrigatório!', 'error');
                    $('#inputTpIntId').focus();
                    dadosValidos = false;
                    return;
                }
                if (tipoAcomodacao == '') {
                    alerta('Atenção', 'Selecione um tipo de acomodação!', 'error');
                    $('#cmbTpIntTipoAcomodacao').focus();
                    dadosValidos = false;
                    return;
                } 
                if(dadosValidos) {
                    //Esse ajax está sendo usado para verificar no banco se o registro já existe
                    $.ajax({
                        type: "POST",
                        url: "atendimentoTipoInternacaoValida.php",
                        data: ('nome=' + inputNome + '&tipoAcomodacao=' + tipoAcomodacao + '&estadoAtual=' + inputEstadoAtual),
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

                            $("#formTipoInternacao").submit();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alerta('Atenção', 'Erro ao salvar o tipo de internação!', 'error');
                            //console.log("Status: " + textStatus);
                            //console.log("Error: " + errorThrown);
                        }
                    })
                }
            })
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaTipoInternacao(Permission, TpIntId, AtSubNome, AtSubStatus, Tipo) {

            if (Permission == 1) {
                document.getElementById('inputTpIntId').value = TpIntId;
                document.getElementById('inputTpIntStatus').value = AtSubStatus;

                if (Tipo == 'edita') {
                    document.getElementById('inputEstadoAtual').value = "EDITA";
                    document.formTipoInternacao.action = "atendimentoTipoInternacao.php";
                } else if (Tipo == 'exclui') {
                    confirmaExclusao(document.formTipoInternacao, "Tem certeza que deseja excluir esse Tipo De Internação?", "atendimentoTipoInternacaoExclui.php");
                } else if (Tipo == 'mudaStatus') {
                    document.formTipoInternacao.action = "atendimentoTipoInternacaoMudaSituacao.php";
                }

                document.formTipoInternacao.submit();
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
                                <h3 class="card-title">Relação de Tipos de Internação</h3>
                            </div>

                            <div class="card-body">
                                <form name="formTipoInternacao" id="formTipoInternacao" method="post" class="form-validate-jquery">

                                    <input type="hidden" id="inputTpIntId" name="inputTpIntId" value="<?php if (isset($_POST['inputTpIntId'])) echo $_POST['inputTpIntId']; ?>">
                                    <input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>" >
                                    <input type="hidden" id="inputTpIntStatus" name="inputTpIntStatus" >

                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label for="inputTpIntNome">Nome do tipo de internação <span class="text-danger"> *</span></label>
                                                <input type="text" id="inputTpIntNome" name="inputTpIntNome" class="form-control" placeholder="Tipo de internação" value="<?php if (isset($_POST['inputTpIntId'])) echo $rowTipoInternacao['TpIntNome']; ?>" required autofocus>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="cmbTpIntTipoAcomodacao">Tipo da Acomodação<span class="text-danger"> *</span></label>
                                            <select id="cmbTpIntTipoAcomodacao" name="cmbTpIntTipoAcomodacao" class="form-control select-search" required>
                                                <option value="">Selecione</option>
                                                <?php
                                                $sql = "SELECT TpAcoId, TpAcoNome
															FROM TipoAcomodacao
															JOIN Situacao ON SituaId = TpAcoStatus
															WHERE TpAcoUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
														    ORDER BY TpAcoNome ASC";
                                                $result = $conn->query($sql);
                                                $rowGrupo = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($rowGrupo as $item) {
                                                    $seleciona = $item['TpAcoId'] == $rowTipoInternacao['TpIntTipoAcomodacao'] ? "selected" : "";
                                                    print('<option value="' . $item['TpAcoId'] . '" ' . $seleciona . '>' . $item['TpAcoNome'] . '</option>');
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
                                                    print('<a href="atendimentoTipoInternacao.php" class="btn btn-basic" role="button">Cancelar</a>');
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
                                        <th data-filter>Tipo de Internação</th>
                                        <th data-filter>Tipo de acomodação</th>
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
											<td>' . $item['TpIntNome'] . '</td>
											<td>' . $item['TpAcoNome'] . '</td>
											');

                                        print('<td><a href="#" onclick="atualizaTipoInternacao(1,' . $item['TpIntId'] . ', \'' . $item['TpIntNome'] . '\',' . $situacaoChave . ', \'mudaStatus\');"><span class="badge ' . $situacaoClasse . '">' . $situacao . '</span></a></td>');

                                        print('<td class="text-center">');



                                        print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaTipoInternacao(1,' . $item['TpIntId'] . ', \'' . $item['TpIntNome'] . '\', ' . $item['TpIntStatus'] . ', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaTipoInternacao(1,' . $item['TpIntId'] . ', \'' . $item['TpIntNome'] . '\', ' . $item['TpIntStatus'] . ', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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