<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Movimentação do Patrimônio';

include('global_assets/php/conexao.php');

$sql = ("SELECT MovimId, MovimData, MovimTipo, MovimNotaFiscal, ForneNome, SituaNome, SituaChave, LcEstNome, SetorNome
		 FROM Movimentacao
		 LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		 LEFT JOIN LocalEstoque on LcEstId = MovimOrigem or LcEstId = MovimDestinoLocal
		 LEFT JOIN Setor on SetorId = MovimDestinoSetor
		 JOIN Situacao on SituaId = MovimSituacao
	     WHERE MovimEmpresa = " . $_SESSION['EmpreId'] . "
		 ORDER BY MovimData DESC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Movimentação</title>

    <?php include_once("head.php"); ?>

    <!-- Theme JS files -->
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <script src="global_assets/js/demo_pages/form_layouts.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            /* Início: Tabela Personalizada */
            $('#tblMovimentacao').DataTable({
                "order": [
                    [0, "desc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        visible: false,
                        targets: [0]
                    },
                    {
                        orderable: false,
                        width: 50,
                        targets: [7]
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

            (function selectSubcateg() {
                const cmbCategoria = $('#cmbCategoria')

                cmbCategoria.on('change', () => {
                    Filtrando()
                    const valCategoria = $('#cmbCategoria').val()

                    $.getJSON('filtraSubCategoria.php?idCategoria=' + valCategoria, function(dados) {

                        var option = '<option value="">Selecione a SubCategoria</option>';

                        if (dados.length) {

                            $.each(dados, function(i, obj) {
                                option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
                            });

                            $('#cmbSubCategoria').html(option).show();
                        } else {
                            Reset();
                        }
                    });
                })
            })()

            function Filtrando() {
                $('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
            }

            function Reset() {
                $('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
            }



            let resultadosConsulta = '';
            let inputsValues = {};

            (function Filtrar() {
                let cont = false;

                $('#submitFiltro').on('click', (e) => {
                    e.preventDefault()

                    const msg = $('<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>')


                    let dataDe = $('#inputDataDe').val()
                    let dataAte = $('#inputDataAte').val()
                    let localEstoque = $('#cmbLocalEstoque').val()
                    let setor = $('#cmbSetor').val()
                    let categoria = $('#cmbCategoria').val()
                    let subCategoria = $('#cmbSubCategoria').val()
                    let inputProduto = $('#inputProduto').val()
                    let url = "relatorioMovimentacaoPatrimonioFiltra.php";

                    inputsValues = {
                        inputDataDe: dataDe,
                        inputDataAte: dataAte,
                        inputLocalEstoque: localEstoque,
                        inputSetor: setor,
                        inputCategoria: categoria,
                        inputSubCategoria: subCategoria,
                        inputProduto: inputProduto
                    };

                    $.post(
                        url,
                        inputsValues,
                        (data) => {

                            if (data) {
                                $('tbody').html(data)
                                $('#imprimir').removeAttr('disabled')
                                resultadosConsulta = data
                            } else {
                                $('tbody').html(msg)
                                $('#imprimir').attr('disabled', '')
                            }
                        }
                    );
                })
            })()

            function imprime() {
                url = 'relatorioMovimentacaoPatrimonioImprime.php';

                $('#imprimir').on('click', (e) => {
                    e.preventDefault()
                    if (resultadosConsulta) {

                        $('#inputResultado').val(resultadosConsulta)
                        $('#inputDataDe_imp').val(inputsValues.inputDataDe)
                        $('#inputDataAte_imp').val(inputsValues.inputDataAte)
                        $('#inputLocalEstoque_imp').val(inputsValues.inputlocalEstoque)
                        $('#inputSetor_imp').val(inputsValues.inputSetor)
                        $('#inputCategoria_imp').val(inputsValues.inputCategoria)
                        $('#inputSubCategoria_imp').val(inputsValues.inputSubCategoria)
                        $('#inputProduto_imp').val(inputsValues.inputProduto)
                        
                        $('#formImprime').attr('action', url)
                        
                        $('#formImprime').submit()
                    }
                })
            }
            imprime()
        });
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
                                <h3 class="card-title">Movimentação do Patrimônio</h3>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                        <a href="perfil.php" class="list-icons-item" data-action="reload"></a>
                                        <!--<a class="list-icons-item" data-action="remove"></a>-->
                                    </div>
                                </div>
                            </div>

                            <form id="formImprime" method="POST" target="_blank">
                                <input id="inputResultado" type="hidden" name="resultados"></input>
                                <input id="inputDataDe_imp" type="hidden" name="inputDataDe_imp"></input>
                                <input id="inputDataAte_imp" type="hidden" name="inputDataAte_imp"></input>
                                <input id="inputLocalEstoque_imp" type="hidden" name="inputLocalEstoque_imp"></input>
                                <input id="inputSetor_imp" type="hidden" name="inputSetor_imp"></input>
                                <input id="inputCategoria_imp" type="hidden" name="inputCategoria_imp"></input>
                                <input id="inputSubCategoria_imp" type="hidden" name="inputSubCategoria_imp"></input>
                                <input id="inputProduto_imp" type="hidden" name="inputProduto_imp"></input>
                            </form>

                            <form name="formFiltro" id="formFiltro" method="POST" class="form-validate-jquery p-3">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="inputDataDe">Período de</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                </span>
                                                <input type="date" id="inputDataDe" name="inputDataDe" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="inputDataAte">Até</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                </span>
                                                <input type="date" id="inputDataAte" name="inputDataAte" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbLocalEstoque">Local Estoque</label>
                                            <select id="cmbLocalEstoque" name="cmbLocalEstoque" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = ("SELECT LcEstId, LcEstNome
																          FROM LocalEstoque															     
																          WHERE LcEstStatus = 1 and LcEstEmpresa = " . $_SESSION['EmpreId'] . "
																          ORDER BY LcEstNome ASC");
                                                $result = $conn->query($sql);
                                                $rowLcEst = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowLcEst as $item) {
                                                    print('<option value="' . $item['LcEstId'] . '">' . $item['LcEstNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbSetor">Setor</label>
                                            <select id="cmbSetor" name="cmbSetor" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = ("SELECT SetorId, SetorNome
																             FROM Setor													     
																             WHERE SetorStatus = 1 and SetorEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY SetorNome ASC");
                                                $result = $conn->query("$sql");
                                                $rowSetor = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowSetor as $item) {
                                                    print('<option value="' . $item['SetorId'] . '">' . $item['SetorNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbCategoria">Categoria</label>
                                            <select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = ("SELECT CategId, CategNome
																             FROM Categoria														     
																             WHERE CategStatus = 1 and CategEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY CategNome ASC");
                                                $result = $conn->query("$sql");
                                                $rowCateg = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowCateg as $item) {
                                                    print('<option value="' . $item['CategId'] . '">' . $item['CategNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbSubCategoria">SubCategoria</label>
                                            <select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="inputPoduto">Produto</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                </span>
                                                <input type="text" id="inputProduto" name="inputProduto" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div>
                                        <button id="submitFiltro" class="btn btn-success"><i class="icon-search">Consultar</i></button>
                                        <button id="imprimir" class="btn btn-secondary btn-icon" disabled>
                                            <i class="icon-printer2"> Imprimir</i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <table class="table" id="tblMovimentacao">
                                <thead>
                                    <tr class="bg-slate">
                                        <th>Descrição do Produto</th>
                                        <th>Item</th>
                                        <th>Descrição do Produto</th>
                                        <th>N° Patrimônio</th>
                                        <th>N° Nota Fiscal</th>
                                        <th>Valor/Aquisição</th>
                                        <th>Valor/Depreciação</th>
                                        <th>Validade</th>
                                        <th>Local/Origem</th>
                                        <th>Setor/Destino</th>
                                    </tr>
                                </thead>
                                <tbody>

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