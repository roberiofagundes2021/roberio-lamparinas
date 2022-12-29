<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ala';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT AlaId, AlaNome, AlaStatus, s.SituaNome, s.SituaCor, s.SituaChave
		FROM Ala al
		JOIN Situacao s on s.SituaId = al.AlaStatus
	    WHERE AlaUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY AlaId ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

//Se estiver editando
if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'EDITA') {
    //Essa consulta é para preencher os campos a se editar
    $sql = "SELECT AlaId, AlaNome
			FROM Ala
			WHERE AlaId = " . $_POST['inputAlaId'] . ";";
    $result = $conn->query($sql);
    $rowAla = $result->fetch(PDO::FETCH_ASSOC);
    $_SESSION['msg'] = array();
}

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA') {
    try {
        //Edição
        if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA') {
            $sql = "UPDATE Ala SET AlaNome = :sAlaNome, AlaUsuarioAtualizador = :iAlaUsuarioAtualizador
					WHERE AlaId = :iAlaId";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sAlaNome' => $_POST['inputAlaNome'],
                ':iAlaUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iAlaId' => $_POST['inputAlaId']
            ));

            $_SESSION['msg']['mensagem'] = "Ala alterada!!!";
        } else { //inclusão
            $sql = "INSERT INTO Ala (AlaNome, AlaStatus, AlaUsuarioAtualizador, AlaUnidade)
					VALUES (:sAlaNome, :bAlaStatus, :iAlaUsuarioAtualizador, :iAlaUnidade)";
            $result = $conn->prepare($sql);
            $result->execute(array(
                ':sAlaNome' => $_POST['inputAlaNome'],
                ':bAlaStatus' => 1,
                ':iAlaUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iAlaUnidade' => $_SESSION['UnidadeId'],
            ));

            $_SESSION['msg']['mensagem'] = "Ala incluída!!!";
        }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['tipo'] = "success";
    } catch (PDOException $e) {
        //} catch (PDOException $e) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro reportado com a Ala!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("atendimentoAla.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Ala</title>

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
            $('#tblAla').DataTable({
                "order": [
                    [0, "asc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: true, //Ala
                        width: "80%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Situação
                        width: "10%",
                        targets: [1]
                    },
                    {
                        orderable: false, //Ações
                        width: "10%",
                        targets: [2]
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
                var inputNome = $('#inputAlaNome').val().trim();
                var inputEstadoAtual = $('#inputEstadoAtual').val();

                //Se o usuário preencheu com espaços em branco ou não preencheu nada
                if (inputNome == '') {
                    alerta('Atenção', 'Ala é obrigatória!', 'error');
                    $('#inputAlaId').focus();
                    dadosValidos = false;
                    return;
                }
                
                if (dadosValidos) {
                    //Esse ajax está sendo usado para verificar no banco se o registro já existe
                    $.ajax({
                        type: "POST",
                        url: "atendimentoAlaValida.php",
                        data: ('nome=' + inputNome + '&estadoAtual=' + inputEstadoAtual),
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

                            $("#formAla").submit();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alerta('Atenção', 'Erro ao salvar a Ala!', 'error');
                        }
                    })
                }
            })
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaAla(Permission, AlaId, AlaStatus, Tipo) {

            if (Permission == 1) {
                document.getElementById('inputAlaId').value = AlaId;
                document.getElementById('inputAlaStatus').value = AlaStatus;

                if (Tipo == 'edita') {
                    document.getElementById('inputEstadoAtual').value = "EDITA";
                    document.formAla.action = "atendimentoAla.php";
                } else if (Tipo == 'exclui') {
                    confirmaExclusao(document.formAla, "Tem certeza que deseja excluir essa Ala?", "atendimentoAlaExclui.php");
                } else if (Tipo == 'mudaStatus') {
                    document.formAla.action = "atendimentoAlaMudaSituacao.php";
                }

                document.formAla.submit();
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
                                <h3 class="card-title">Relação de Alas</h3>
                            </div>

                            <div class="card-body">
                                <form name="formAla" id="formAla" method="post" class="form-validate-jquery">

                                    <input type="hidden" id="inputAlaId" name="inputAlaId" value="<?php if (isset($_POST['inputAlaId'])) echo $_POST['inputAlaId']; ?>">
                                    <input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>">
                                    <input type="hidden" id="inputAlaStatus" name="inputAlaStatus">

                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label for="inputAlaNome">Ala<span class="text-danger">*</span></label>
                                                <input type="text" id="inputAlaNome" name="inputAlaNome" class="form-control" placeholder="Ala" value="<?php if (isset($_POST['inputAlaId'])) echo $rowAla['AlaNome']; ?>" required autofocus>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group" style="padding-top:25px;">
                                                <?php

                                                //editando
                                                if (isset($_POST['AlaId'])) {
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
                                                    print('<a href="atendimentoAla.php" class="btn btn-basic" role="button">Cancelar</a>');
                                                } else { //inserindo
                                                    print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
                                                }

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <table id="tblAla" class="table">
                                <thead>
                                    <tr class="bg-slate">
                                        <th data-filter>Ala</th>
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
											<td>' . $item['AlaNome'] . '</td>
											');

                                        print('<td><a href="#" onclick="atualizaAla(
                                            1,
                                            ' . $item['AlaId'] . ',
                                            ' . $situacaoChave . ',
                                            \'mudaStatus\'
                                        );"><span class="badge ' . $situacaoClasse . '">' . $situacao . '</span></a></td>');

                                        print('<td class="text-center">');

                                        print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaAla(
                                                    1,
                                                    ' . $item['AlaId'] . ',
                                                    ' . $item['AlaStatus'] . ',
                                                    \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaAla(
                                                    1,
                                                    ' . $item['AlaId'] . ',
                                                    ' . $item['AlaStatus'] . ',
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