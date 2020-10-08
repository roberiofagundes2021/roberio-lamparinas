<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Relação de Contas à Pagar';

include('global_assets/php/conexao.php');

$sql = "SELECT ForneId, ForneNome, ForneCpf, ForneCnpj, ForneTelefone, ForneCelular, ForneStatus, CategNome
		FROM Fornecedor
		JOIN Categoria on CategId = ForneCategoria
	    WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY ForneNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

$d = date("d");
$m = date("m");
$Y = date("Y");

// $dataInicio = date("Y-m-01"); //30 dias atrás
$dataInicio = date("Y-m-d");
$dataFim = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Relatório de Movimentação</title>

    <?php include_once("head.php"); ?>

    <!-- Theme JS files -->
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/form_layouts.js"></script>
    <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <!-- /theme JS files -->

    <!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $.fn.dataTable.moment('DD/MM/YYYY'); //Para corrigir a ordenação por data			

            /* Início: Tabela Personalizada */
            $('#tblMovimentacao').DataTable({
                "order": [
                    [0, "desc"],
                    [1, "desc"],
                    [2, "asc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: false, //selecionar
                        width: "5%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Vencimento
                        width: "10%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Descrição
                        width: "30%",
                        targets: [2]
                    },
                    {
                        orderable: true, //Fornecedor
                        width: "25%",
                        targets: [3]
                    },
                    {
                        orderable: true, //Número Doc.
                        width: "15%",
                        targets: [4]
                    },
                    {
                        orderable: true, //Valor Total
                        width: "5%",
                        targets: [5]
                    },
                    {
                        orderable: true, //Status
                        width: "5%",
                        targets: [6]
                    },
                    {
                        orderable: true, //Ações
                        width: "5%",
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

            $('#tblParcelamento').DataTable({
                "order": [
                    [0, "desc"],
                    [1, "desc"],
                    [2, "asc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: true, //Item
                        width: "10%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Descrição
                        width: "40%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Vencimento
                        width: "25%",
                        targets: [2]
                    },
                    {
                        orderable: true, //Valor
                        width: "25%",
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

            function modalParcelas() {

                $('.btnParcelar').each((i, elem) => {
                    $(elem).on('click', function () {
                        $('#page-modal').fadeIn(200);


                        let linha = $(elem).parent().parent().parent().parent().parent()
                        .parent()

                        // let id = linha.attr('idPatrimonio')
                        // let editado = linha.attr('editado')

                        let tds = linha.children();
                        let valor = $(tds[5]).html();
                        let dataVencimentolistChild = $(tds[1]).children()
                        let dataVencimento = $(dataVencimentolistChild[1]).val()
                        let descricao = $(tds[2]).html()
                        let idContainer = $(tds[0]).children()
                        let id = $(idContainer[1]).val()

                        //Conteúdo novo

                        $('#inputValor').val(valor)
                        $('#inputDataVencimento').val(dataVencimento)
                        $('#inputDescricao').val(descricao)
                        $('#inputId').val(id)

                        const fonte1 = 'style="font-size: 1.1rem"'


                        // if (estadoConservacao) {
                        //     let url = 'filtraEstadoConservacao.php'
                        //     let inputsValues = {
                        //         inputEstadoConservacao: estadoConservacao
                        //     }

                        //     $.post(
                        //         url,
                        //         inputsValues,
                        //         (data) => {
                        //             if (data) {
                        //                 $('#cmbEstadoConservacao').html(data)

                        //             } else {}
                        //         }
                        //     );
                        // }


                    })
                })

                $('#modal-close').on('click', function () {
                    $('#page-modal').fadeOut(200);
                    $('body').css('overflow', 'scroll');
                })
            }

            function cadastraParcelas(){
                let parcelasNum = $("#cmbParcelas").val()
                let id = $("#inputId").val()
            
                let dataParcelas = new Array

                for(let i = 1; i <= parcelasNum; i++){
                    // console.log(i)
                    let arrayParcela = new Array

                    dataParcelas.push({
                        descricao: $(`#inputParcelaDescricao${i}`).val(),
                        vencimento: $(`#inputParcelaDataVencimento${i}`).val(),
                        valor: $(`#inputParcelaValorAPagar${i}`).val()
                    })

                    // dataParcelas.push(arrayParcela)

                    data = {
                        parcelas: JSON.stringify(dataParcelas),
                        numParcelas: parcelasNum,
                        idConta: id
                    }
                }
                console.log(dataParcelas)

                let url = 'contasAPagarParcelamento.php'

                $.post(
                    url,
                    data,
                    (data) => {
                        console.log(data)
                })
                
            }
            $("#salvar").on('click', ( ) => {
                cadastraParcelas()
            })
            /////////////////////////////////////////////////////////////////
            function geararParcelas(parcelas, valorTotal, dataVencimento, periodicidade, descricao) {
                $("#parcelasContainer").html("")

                let valorParcela = float2moeda(valorTotal / parcelas)
                let numeroParcelas = `<input type="hidden" value="${parcelas}" name="inputNumeroParcelas">`
                // let dataVencimento = dataVencimento
                $("#parcelasContainer").append(numeroParcelas)
                let cont = 0
                let iAnterior = 0
                for (let i = 1; i <= parcelas; i++) {

                    let novaDataVencimento = ''

                    let somadorPeriodicidade = periodicidade == 1 ? 0 : periodicidade == 2 ? 2 :
                        periodicidade == 3 ? 3 : 6
                    if (i > 1) {
                        let dataArray = dataVencimento.split("-")
                        let mes = parseInt(dataArray[1])
                        let novoMes = 0
                        let ano = parseInt(dataArray[0])

                        novoMes = mes + i > 9 ? (mes + (i - 1)).toString() : `0${(mes + (i - 1)).toString()}`

                        if (novoMes > 12) {
                            cont++
                            ano = ano + 1
                            novoMes = cont > 9 ? cont : `0${cont}`
                        }

                        dataArray[1] = novoMes
                        dataArray[0] = ano
                        novaDataVencimento = `${dataArray[0]}-${dataArray[1]}-${dataArray[2]}`
                    } else {
                        novaDataVencimento = dataVencimento
                        
                    }

                    let elem = `<div class="d-flex flex-row justify-content-center">
                                    <p class="col-1 p-2 pl-4">${i}</p>
                                    <div class="form-group col-5 p-2">
                                        <input type="text" class="form-control" id="inputParcelaDescricao${i}" name="inputParcelaDescricao${i}" value="${descricao} ${i}/${parcelas}">
                                    </div>
                                    <div class="form-group col-3 p-2">
                                        <input type="date" class="form-control" id="inputParcelaDataVencimento${i}" name="inputParcelaDataVencimento${i}" value="${novaDataVencimento}">
                                    </div>
                                    <div class="form-group col-3 p-2">
                                        <input type="text" class="form-control" id="inputParcelaValorAPagar${i}" name="inputParcelaValorAPagar${i}" value="${valorParcela}">
                                    </div> 
                                </div>`

                    $("#parcelasContainer").append(elem)
                }
            }

            function parcelamento() {
                $('#gerarParcelas').on('click', (e) => {
                    e.preventDefault()
                    let parcelas = $("#cmbParcelas").val()
                    let valorTotal = $("#inputValor").val()
                    let dataVencimento = $("#inputDataVencimento").val()
                    let periodicidade = $("#cmbPeriodicidade").val()
                    let descricao = $("#inputDescricao").val()
                    geararParcelas(parcelas, valorTotal, dataVencimento, periodicidade, descricao)
                })
            }
            parcelamento()
            /////////////////////////////////////////////////////////////////

            function pagamentoAgrupado() {

                let numLinhas = $("#elementosGrid").val()

                for (let i = 1; i <= numLinhas; i++) {

                    let id = $(`#check${i}`).parent().children().last().val();

                    $(`#check${i}`).on('click', () => {

                        if (!$(`#check${i}`).hasClass('clicado')) {
                            $(`#check${i}`).addClass('clicado')
                            let input = `<input type="hidden" name="conta${i}" value="${id}">`
                            $("#pagamentoAgrupadoForm").append(input)

                            let quantInputs = $("#pagamentoAgrupadoForm").children()

                            if (quantInputs.length >= 1) {
                                $("#efetuarPagamento").removeAttr('disabled')
                            } else {
                                $("#efetuarPagamento").attr('disabled')
                            }

                        } else {
                            $(`[name=conta${i}]`).remove()

                            $(`#check${i}`).removeClass('clicado')

                            let quantInputs = $("#pagamentoAgrupadoForm").children()

                            if (quantInputs.length == 0) {
                                $("#efetuarPagamento").prop('disabled', true)
                            }
                        }
                    })
                }
            }

            function pagamentoAgrupadoEnvia() {
                let inputs = $("#pagamentoAgrupadoForm").children()

                let ids = []

                inputs.each((i, elem) => {
                    ids[i] = $(elem).val()
                })

                data = {
                    values: ids
                }

                url = 'contasAPagarPagamentoAgrupado.php'

                $.post(
                    url,
                    data,
                    (data) => {
                        let linhas = $('tbody').children()
                        let ids = data.split('/')
                        for (let h = 0; h <= ids.length; h++) {
                            for (let i = 1; i <= linhas.length - 1; i++) {

                                let p = $(linhas[i]).children()[0]
                                let irm = $(p).children()[1]

                                if ($(irm).val() == ids[h]) {
                                    
                                    let linha = $(`#check${i}`)
                                    let status = $(linhas[i]).children()[6]
                                    $(status).html('Paga')

                                }
                            }
                        }
                    })
            }

            $("#efetuarPagamento").on("click", (e) => {
                e.preventDefault()
                pagamentoAgrupadoEnvia()
            })


            function editarLancamento() {
                $('.editarLancamento').each((i, elem) => {
                    $(elem).on('click', () => {
                        let linha = $(elem).parent().parent().parent().parent()
                        let tds = linha.children();

                        let filhosPrimeiroTd = $(tds[0]).children();
                        let idLancamento = $(filhosPrimeiroTd[1]).val()

                        window.location.href =
                            `contasAPagarNovoLancamento.php?lancamentoId=${idLancamento}`
                    })
                })
            }



            function Filtrar(carregamentoPagina) {
                let cont = false;

                    const msg = $(
                        '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>'
                    )

                    if ($('#cmbProduto').val() == 'Sem produto' || $('#cmbProduto').val() ==
                        'Filtrando...') $('#cmbProduto').val("")

                    let periodoDe = $('#inputPeriodoDe').val()
                    let ate = $('#inputAte').val()
                    let numeroDocumento = $('#inputNumeroDocumento').val()
                    let fornecedor = $('#cmbFornecedor').val()
                    let planoContas = $('#cmbPlanoContas').val()
                    let status = $('#cmbStatus').val()
                    let url = "contasAPagarFiltra.php";
                    let tipoFiltro = carregamentoPagina ? 'CarregamentoPagima' : 'FiltroNormal'

                    inputsValues = {
                        inputPeriodoDe: periodoDe,
                        inputAte: ate,
                        inputNumeroDocumento: numeroDocumento,
                        cmbFornecedor: fornecedor,
                        cmbPlanoContas: planoContas,
                        cmbStatus: status,
                        tipoFiltro: tipoFiltro
                    };

                    $.post(
                        url,
                        inputsValues,
                        (data) => {
                            // console.log(data)
                            if (data) {
                                $('tbody').html(data)
                                $('#imprimir').removeAttr('disabled')
                                resultadosConsulta = data
                                modalParcelas()
                                editarLancamento()
                                pagamentoAgrupado()

                            } else {
                                $('tbody').html(msg)
                                $('#imprimir').attr('disabled', '')
                            }
                        }
                    );
            }

            $('#submitFiltro').on('click', (e) => {
                e.preventDefault()
                Filtrar(false)
            })

            Filtrar(true)
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
                                <h3 class="card-title">Relação de Contas à Pagar</h3>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                        <a href="relatorioMovimentacao.php" class="list-icons-item"
                                            data-action="reload"></a>
                                        <!--<a class="list-icons-item" data-action="remove"></a>-->
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="font-size-lg">Utilize os filtros abaixo para gerar o relatório.</p>
                                <br>

                                <form id="formImprime" method="POST" target="_blank">
                                    <input id="TipoProdutoServico" type="hidden" name="TipoProdutoServico"></input>
                                    <input id="inputResultado" type="hidden" name="resultados"></input>
                                    <input id="inputDataDe_imp" type="hidden" name="inputDataDe_imp"></input>
                                    <input id="inputDataAte_imp" type="hidden" name="inputDataAte_imp"></input>
                                    <input id="cmbTipo_imp" type="hidden" name="cmbTipo_imp"></input>
                                    <input id="cmbFornecedor_imp" type="hidden" name="cmbFornecedor_imp"></input>
                                    <input id="cmbCategoria_imp" type="hidden" name="cmbCategoria_imp"></input>
                                    <input id="cmbSubCategoria_imp" type="hidden" name="cmbSubCategoria_imp"></input>
                                    <input id="cmbProduto_imp" type="hidden" name="cmbProduto_imp"></input>
                                    <input id="cmbServico_imp" type="hidden" name="cmbServico_imp"></input>
                                    <input id="cmbCodigo_imp" type="hidden" name="cmbCodigo_imp"></input>
                                </form>

                                <form name="formMovimentacao" method="post" class="p-3">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputPeriodoDe">Período de</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="icon-calendar22"></i></span>
                                                    </span>
                                                    <input type="date" id="inputPeriodoDe" name="inputPeriodoDe"
                                                        class="form-control" value="<?php echo $dataInicio; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputAte">Até</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="icon-calendar22"></i></span>
                                                    </span>
                                                    <input type="date" id="inputAte" name="inputAte"
                                                        class="form-control" value="<?php echo $dataFim; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputNumeroDocumento">Número Doc.</label>
                                                <input type="text" name="inputNumeroDocumento" id="inputNumeroDocumento"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="cmbFornecedor">Fornecedor</label>
                                                <select id="cmbFornecedor" name="cmbFornecedor"
                                                    class="form-control form-control-select2">
                                                    <option value="">Todos</option>
                                                    <?php
													$sql = "SELECT ForneId, ForneNome
																FROM Fornecedor
																JOIN Situacao on SituaId = ForneStatus
																WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																ORDER BY ForneNome ASC";
													$result = $conn->query($sql);
													$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowFornecedor as $item) {
														print('<option value="' . $item['ForneId'] . '">' . $item['ForneNome'] . '</option>');
													}

													?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="cmbPlanoContas">Plano de Contas</label>
                                                <select id="cmbPlanoContas" name="cmbPlanoContas"
                                                    class="form-control form-control-select2">
                                                    <option value="">Todos</option>
                                                    <?php
													$sql = "SELECT PlConId, PlConNome
																FROM PlanoContas
																JOIN Situacao on SituaId = PlConStatus
																WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																ORDER BY PlConNome ASC";
													$result = $conn->query($sql);
													$rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowPlanoContas as $item) {
														print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
													}

													?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-between">
                                        <div class="row col-4">
                                            <div class="col-lg-10">
                                                <div class="form-group">
                                                    <label for="cmbSubCategoria">Status</label>
                                                    <select id="cmbSubCategoria" name="cmbSubCategoria"
                                                        class="form-control form-control-select2">
                                                        <option value="">Selecione</option>
                                                        <?php
													        $sql = "SELECT SituaId, SituaNome, SituaChave
													        				FROM Situacao
													        				WHERE SituaStatus = 1
													        				ORDER BY SituaNome ASC";
													        $result = $conn->query($sql);
													        $rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);
        
													        foreach ($rowSituacao as $item) {
													        	if($item['SituaChave'] == 'APAGAR' || $item['SituaChave'] == 'PAGA'){
                                                                    print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
                                                                }
													        }
													    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right col-2 pt-3">
                                                <div>
                                                    <button id="submitFiltro"
                                                        class="btn btn-principal">Pesquisar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right pt-3">
                                            <div>
                                                <button id="novoLacamento" class="btn btn-success"><a
                                                        href="contasAPagarNovoLancamento.php"
                                                        style="text-decoration:none; color: #FFF">Novo
                                                        Lançamento</a></button>
                                                <button id="efetuarPagamento" class="btn btn-principal" disabled>Efetuar
                                                    Pagamento</button>
                                                <button class="btn bg-secondary"><i class="icon-printer2"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <table class="table" id="tblMovimentacao">
                                    <thead>
                                        <tr class="bg-slate">
                                            <th></th>
                                            <th>Vencimento</th>
                                            <th style='text-align: center'>Descrição</th>
                                            <th>Fornecedor</th>
                                            <th>Número Doc.</th>
                                            <th>Valor Total</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>

                        </div>
                        <!-- /basic responsive configuration -->

                    </div>
                </div>

                <form id="pagamentoAgrupadoForm" action=""></form>

                <!-- /info blocks -->

                <!--------------------------------------------------------------------------------------------------->
                <!--Modal ditar-->
                <div id="page-modal" class="custon-modal">
                    <div class="custon-modal-container">
                        <div class="card custon-modal-content">
                            <div class="custon-modal-title">
                                <i class=""></i>
                                <p class="h3">Parcelamento</p>
                                <i class=""></i>
                            </div>
                            <form id="editarProduto" method="POST">
                                <div class="d-flex flex-row p-2">
                                    <div class='col-lg-3'>
                                        <div class="form-group">
                                            <label for="inputValor">Valor Total</label>
                                            <div class="input-group">
                                                <input type="text" id="inputValor" name="inputValor"
                                                    class="form-control" readOnly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="numeroSerie">Periodicidade</label>
                                        <div class="form-group">
                                            <select id="cmbPeriodicidade" name="cmbPeriodicidade"
                                                class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <option value="">Mensal</option>
                                                <option value="">Quinsenal</option>
                                                <option value="">Semanal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="numeroSerie">Parcelas</label>
                                        <div class="form-group">
                                            <select id="cmbParcelas" name="cmbPeriodicidade"
                                                class="form-control form-control-select2">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-lg btn-primary mt-2" id="gerarParcelas">Gerar
                                            Parcelas</button>
                                    </div>
                                </div>
                                <div class="d-flex flex-row">
                                    <div class="col-12 d-flex flex-row justify-content-center">
                                        <p class="col-2 p-2" style="background-color:#f2f2f2">Item</p>
                                        <p class="col-4 p-2" style="background-color:#f2f2f2">Descrição</p>
                                        <p class="col-3 p-2" style="background-color:#f2f2f2">Vencimento</p>
                                        <p class="col-3 p-2" style="background-color:#f2f2f2">Valor</p>
                                        </table>
                                    </div>
                                </div>
                                <div id="parcelasContainer" class="d-flex flex-column px-5"
                                    style="overflow-Y: scroll; max-height: 300px">

                                </div>
                                <input type="hidden" id='inputDataVencimento'>
                                <input type="hidden" id='inputDescricao'>
                                <input type="hidden" id='inputId'>
                            </form>

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
                <!--------------------------------------------------------------------------------------------------->

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