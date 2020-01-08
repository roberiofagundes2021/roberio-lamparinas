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

                Filtrando()
                const cmbCategoria = $('#cmbCategoria')

                cmbCategoria.on('change', () => {
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


            (function Filtrar() {
                $('#submitFiltro').on('click', (e) => {
                    e.preventDefault()

                    let dataDe = $('#inputDataDe').val()
                    let dataAte = $('#inputDataAte').val()
                    let localEstoque = $('#inputLocalEstoque').val()
                    let setor = $('#inputSetor').val()
                    let categoria = $('#inputCategoria').val()
                    let subCategoria = $('#inputSubCategoria').val()
                    let url = "relatorioMovimentacaoPatrimonioFiltra.php";

                    $.post(url,{inputDataDe: dataDe, inputDataAte: dataAte, inputLocalEstoque: localEstoque, inputSetor: setor, inputCategoria: categoria, inputSubCategoria: subCategoria}, (data) => {
                            $('tbody').html(data)
                        });
                })
            })()
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaMovimentacao(MovimId, MovimNotaFiscal, Tipo) {

            document.getElementById('inputMovimentacaoId').value = MovimId;
            document.getElementById('inputMovimentacaoNotaFiscal').value = MovimNotaFiscal;

            if (Tipo == 'edita') {
                document.formMovimentacao.action = "movimentacaoEdita.php";
            } else if (Tipo == 'exclui') {
                confirmaExclusao(document.formMovimentacao, "Tem certeza que deseja excluir esse movimentacao?", "movimentacaoExclui.php");
            } else if (Tipo == 'imprimir') {
                document.formMovimentacao.action = "movimentacaoImprime.php";
                document.formMovimentacao.setAttribute("target", "_blank");
            }

            document.formMovimentacao.submit();
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
                                <h3 class="card-title">Movimentação do Patrimônio</h3>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                        <a href="perfil.php" class="list-icons-item" data-action="reload"></a>
                                        <!--<a class="list-icons-item" data-action="remove"></a>-->
                                    </div>
                                </div>
                            </div>

                            <form name="formFiltro" id="formFiltro" method="POST" class="form-validate-jquery pl-3">
                                <div class="row col-lg-12" style="width: 100%">
                                    <div class="row col-11 pl-0">
                                        <div class="d-flex flex-column col-12">
                                            <div class="row m-0 " style="background-color: #eeeded">
                                                <div class="row col-lg-7">
                                                    <div class="col-lg-6 row">
                                                        <div class="row col-6 justify-content-center align-content-center">
                                                            <label for="inputDataDe">Período de</label>
                                                        </div>
                                                        <div class="col-6 form-group row">
                                                            <input type="date" id="inputDataDe" name="inputDataDe" class="form-control pb-0" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 row">
                                                        <div class="row col-6 justify-content-center align-content-center">
                                                            <label for="inputDataAte">Até</label>
                                                        </div>
                                                        <div class="col-6 form-group row">
                                                            <input type="date" id="inputDataAte" name="inputDataAte" class="form-control pb-0" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-lg-5">
                                                    <div class="col-lg-6 row">
                                                        <div class="form-group col-12">
                                                            <select id="cmbFabricante" name="cmbFabricante" class="form-control form-control-select2">
                                                                <option value="">Local Estoque</option>
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
                                                    <div class="col-lg-6 row">
                                                        <div class="form-group col-12">
                                                            <select id="cmbMarca" name="cmbMarca" class="form-control form-control-select2">
                                                                <option value="">Setor</option>
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
                                            </div>
                                            <div class="row m-0 mt-3 " style="background-color: #eeeded">
                                                <div class="row col-lg-5 ml-2">
                                                    <div class="col-lg-6 row">
                                                        <div class="form-group col-12">
                                                            <select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
                                                                <option value="">Categoria</option>
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
                                                    <div class="col-lg-6 row">
                                                        <div class="form-group col-12">
                                                            <select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
                                                                <option value="">Subcategoria</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-lg-7">
                                                    <div class="col-lg-8 row">
                                                        <div class="row col-4 justify-content-center align-content-center">
                                                            <label for="inputProduto">Produto</label>
                                                        </div>
                                                        <div class="col-6 form-group row">
                                                            <input type="search" id="inputNome" name="inputNome" class="form-control pb-0 imput-pesquisa-filtro" placeholder="Nome" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-lg-1 ml-1">
                                        <div class="d-flex flex-column">
                                            <div class="row" style="height: 57%">

                                            </div>
                                            <div class="d-flex flex-column justify-content-between align-content-center" style="height: 43%; padding: 4px">
                                                <button id="submitFiltro" class="btn btn-success btn-sm" style="padding: 0px 14px 0px 14px; text-transform: none;">Consultar</button>
                                                <button class="btn btn-success btn-sm" disabled style="padding: 0px 14px 0px 14px;  text-transform: none">Imprimir</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>


                            <table class="table" id="tblMovimentacao">
                                <thead>
                                    <tr class="bg-slate">
                                        <th>Id</th>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Nota Fiscal</th>
                                        <th>Fornecedor</th>
                                        <th>Estoque Destino</th>
                                        <th>Situação</th>
                                        <th class="text-center">Ações</th>
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

                <form name="formMovimentacao" method="post" target="_blank">
                    <input type="hidden" id="inputMovimentacaoId" name="inputMovimentacaoId">
                    <input type="hidden" id="inputMovimentacaoNotaFiscal" name="inputMovimentacaoNotaFiscal">
                </form>

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