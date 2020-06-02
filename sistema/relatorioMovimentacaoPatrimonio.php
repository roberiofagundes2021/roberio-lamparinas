<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Movimentação do Patrimônio';

include('global_assets/php/conexao.php');

$sql = "SELECT MovimId, MovimData, MovimTipo, MovimNotaFiscal, ForneNome, SituaNome, SituaChave, LcEstNome, SetorNome
		FROM Movimentacao
		LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		LEFT JOIN LocalEstoque on LcEstId = MovimOrigemLocal or LcEstId = MovimDestinoLocal
		LEFT JOIN Setor on SetorId = MovimDestinoSetor
		JOIN Situacao on SituaId = MovimSituacao
	    WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY MovimData DESC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

$d = date("d");
$m = date("m");
$Y = date("Y");

$dataInicio = date("Y-m-d", mktime(0, 0, 0, $m, $d - 30, $Y)); //30 dias atrás
$dataFim = date("Y-m-d");

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
        function modalAcoes() {

            $('.btn-acoes').each((i, elem) => {
                $(elem).on('click', function() {
                    $('#page-modal').fadeIn(200);

                    let linha = $(elem).parent().parent()

                    let tds = linha.children();
                    let produto = $(tds[1]).html();
                    let patrimonio = $(tds[2]).html();
                    let notaFisc = $(tds[3]).html();
                    let aquisicao= $(tds[4]).html();
                    let depreciacao = $(tds[5]).html();
                    let origem = $(tds[7]).html();
                    let destino = $(tds[8]).html();
                    let marca = $(tds[9]).html();
                    let fabricante = $(tds[10]).html();

                    const fonte1 = 'style="font-size: 1.1rem"'
                    const fonte2 = 'style="font-size: 0.9rem"'
                    const textCenter = 'style="text-align: center"'
                    const styleLabel1 =  'style="min-width: 250px; font-size: 0.9rem"'
                    const styleLabel2 =  'style="min-width: 150px; font-size: 0.9rem"'
                    const styleLabel3 =  'style="min-width: 100px; font-size: 0.9rem"'

                    formModal = `<form>
                                                   <div class='row'>
                                                        <div class='col-lg-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${fonte1} class="pr-2">Produto:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte1}>${produto}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${fonte1} class="pr-2">Patrimônio:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte1}>${patrimonio}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                   </div>
                                                   <div class='row'>
                                                        <div class='col-lg-6 col-12'>
                                                             <div class="form-group d-flex flex-row">
                                                                 <label for="produto" ${fonte1} class="pr-2">Origem:</label>
                                                                 <div class="input-group">
                                                                     <p id='produto' ${fonte1}>${origem}</p>
                                                                 </div>
                                                            </div>
                                                         </div>
                                                         <div class='col-lg-6 col-12'>
                                                             <div class="form-group d-flex flex-row">
                                                                 <label for="produto" ${fonte1} class="pr-2">Destino:</label>
                                                                 <div class="input-group">
                                                                     <p id='produto' ${fonte2}>${destino}</p>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   <div class='row'>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${styleLabel3}>Nota Fiscal: </label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}>${notaFisc}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${styleLabel2} >Data da Compra:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}></p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${styleLabel3}>(R$) Aquisição:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}>${ aquisicao}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${styleLabel2}>(R$) Depreciação:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}>${depreciacao}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                    </div>
                                                   <div class='row'>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${fonte2} class="pr-1">Marca:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}>${marca}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-3 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="produto" ${fonte2} class="pr-1">Fabricante:</label>
                                                                <div class="input-group">
                                                                    <p id='produto' ${fonte2}>${fabricante}</p>
                                                                </div>
                                                           </div>
                                                        </div>
                                                        <div class='col-lg-6 col-sm-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="numeroSerie" ${styleLabel3} class="pr-1">N Série:</label>
                                                                <div class="input-group">
                                                                   <input type="text" id="numeroSerie" name="numeroSerie" class="form-control p-0">
                                                                </div>
                                                           </div>
                                                        </div>
                                                   </div>
                                                   <div class='row'>
                                                        <div class='col-lg-6 col-12'>
                                                            <div class="form-group d-flex flex-row">
                                                                <label for="estadoConserv" ${styleLabel1} class="pr-1">Estado de Conservação (Status):</label>
                                                                <div class="input-group">
                                                                   <input type="text" id="estadoConserv" name="estadoConserv" class="form-control p-0">
                                                                </div>
                                                           </div>
                                                        </div>
                                                   </div>
                                                   <div class='row'>
                                                        
                                                   </div>
                                           </form>
                    `;
                    $('.form-modal').html(formModal)
                   /* if ($(elem).attr('idRow') == linha.attr('id')) {
                        let tds = linha.children();
                        let tipoProdutoServico = $(tds[8]).attr('tipo');



                        let valores = [];

                        let inputItem = $('<td></td>');
                        let inputProdutoServico = $('<input type="text">');
                        let inputQuantidade = $('<input type="text">');
                        let inputSaldo = $('<input type="text">');
                        let inputValidade = $('<input type="text">');

                        let linhaTabela = '';

                        tds.each((i, elem) => {
                            valores[i] = $(elem).html();
                        })

                        inputItem.val(valores[0]);

                        if (tipoProdutoServico != 'P') {

                            cabecalho = `
                               
                                <tr class="bg-slate">
                                     <th width="5%">Item</th>
                                     <th width="75%">Serviço</th>
                                     <th width="10%">Quantidade</th>
                                     <th width="10%">Saldo</th>
                                     <th width="10%"></th>
                                 </tr>
                                    `;

                            linhaTabela = `<tr id='trModal'>
                                    <td>${valores[0]}</td>
                                    <td>${valores[1]}</td>
                                    <td><input id='quantidade' type="text" class="form-control" value="" style="text-align: center" autofocus></td>
                                    <td><input id='saldo' class="form-control" style="text-align: center"  value="${saldoinicialModal}" disabled></td>
                                </tr>
                              `;
                        } else {
                            cabecalho = `
                                 <tr class="bg-slate">
                                        <th width="5%">Item</th>
                                        <th width="45%">Produto</th>
                                        <th width="8%">Quantidade</th>
                                        <th width="10%">Saldo</th>
                                        <th width="10%">Lote</th>
                                        <th width="12%">Validade</th>
                                </tr>
                                    `;

                            linhaTabela = `<tr id='trModal'>
                                                <td>${valores[0]}</td>
                                                <td>${valores[1]}</td>
                                                <td><input id='quantidade' quantMax='${valores[4]}' type="text" class="form-control" value="" style="text-align: center" autofocus></td>
                                                <td><input id='saldo' type="text" class="form-control" value="${saldoinicialModal}" style="text-align: center"  disabled></td>
                                                <td><input id='lote' type="text" class="form-control" value="" style="text-align: center"></td>
                                                <td><input id='validade' type="date" class="form-control" value="" style="text-align: center"></td>
                                            </tr>
                                            `;
                        }

                        $('#thead-modal').html(cabecalho);

                        $('#tbody-modal').html(linhaTabela);

                        // Esta função não permite que o valor digitado pelo usuário seja maior que o valor de saldo.
                        function validaQuantInputModal(quantMax) {
                            $('#quantidade').on('keyup', function() {
                                if (parseInt($('#quantidade').val()) > parseInt(quantMax)) {
                                    $('#quantidade').val(quantMax)
                                }
                            })
                        }

                        validaQuantInputModal($('#saldo').val())

                        $('#quantidade').focus()
                    }*/
                })
            })

            $('#modal-close').on('click', function() {
                $('#page-modal').fadeOut(200);
                $('body').css('overflow', 'scroll');
            })
        }

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
                        orderable: true,
                        width: 15,
                        targets: [1]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [2]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [3]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [4]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [5]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [6]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [7]
                    },
                    {
                        orderable: false,
                        width: 15,
                        targets: [8]
                    },
                    {
                        orderable: false,
                        width: 15,
                        targets: [9]
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [10]
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
                                modalAcoes()
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
                                                <input type="date" id="inputDataDe" name="inputDataDe" class="form-control" value="<?php echo $dataInicio ?>">
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
                                                <input type="date" id="inputDataAte" name="inputDataAte" class="form-control" value="<?php echo $dataFim ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbLocalEstoque">Local Estoque</label>
                                            <select id="cmbLocalEstoque" name="cmbLocalEstoque" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = "SELECT LcEstId, LcEstNome
                                                        FROM LocalEstoque
                                                        JOIN Situacao on SituaId = LcEstStatus
                                                        WHERE LcEstUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                        ORDER BY LcEstNome ASC";
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
                                                $sql = "SELECT SetorId, SetorNome
                                                        FROM Setor
                                                        JOIN Situacao on SituaId = SetorStatus
                                                        WHERE SetorUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                        ORDER BY SetorNome ASC";
                                                $result = $conn->query($sql);
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
                                                $sql = "SELECT CategId, CategNome
                                                        FROM Categoria
                                                        JOIN Situacao on SituaId = CategStatus
                                                        WHERE CategUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                        ORDER BY CategNome ASC";
                                                $result = $conn->query($sql);
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
                                        <th>Patrimônio</th>
                                        <th>Nota Fiscal</th>
                                        <th>Aquisição (R$)</th>
                                        <th>Depreciação (R$)</th>
                                        <th>Validade</th>
                                        <th>Origem</th>
                                        <th>Destino</th>
                                        <th>Ações</th>
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

                <!--Modal ditar-->
                <div id="page-modal" class="custon-modal">
                    <div class="custon-modal-container">
                        <div class="card custon-modal-content">
                            <div class="custon-modal-title">
                                <i class=""></i>
                                <p class="h3">Dados Produto</p>
                                <i class=""></i>
                            </div>
                            <div class="form-modal p-3">
                            
                            </div>
                            <div class="card-footer mt-2 d-flex flex-column">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <button class="btn btn-lg btn-success" id="salvar">Salvar</button>
                                            <a id="modal-close" class="btn btn-basic" role="button">Cancelar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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