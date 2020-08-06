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

$dataInicio = date("Y-m-01"); //30 dias atrás
$dataFim = date("Y-m-t");

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
                        orderable: true, //Vencimento
                        width: "5%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Descrição
                        width: "20%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Fornecedor
                        width: "20%",
                        targets: [2]
                    },
                    {
                        orderable: true, //Plano de Contas
                        width: "15%",
                        targets: [3]
                    },
                    {
                        orderable: true, //Número Doc.
                        width: "10%",
                        targets: [4]
                    },
                    {
                        orderable: true, //Valor Total
                        width: "10%",
                        targets: [5]
                    },
                    {
                        orderable: true, //Saldo
                        width: "10%",
                        targets: [6]
                    },
                    {
                        orderable: true, //Status
                        width: "5%",
                        targets: [7]
                    },
                    {
                        orderable: true, //Ações
                        width: "5%",
                        targets: [8]
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

            //     // Select2 for length menu styling
            //     var _componentSelect2 = function () {
            //         if (!$().select2) {
            //             console.warn('Warning - select2.min.js is not loaded.');
            //             return;
            //         }

            //         // Initialize
            //         $('.dataTables_length select').select2({
            //             minimumResultsForSearch: Infinity,
            //             dropdownAutoWidth: true,
            //             width: 'auto'
            //         });
            //     };

            //     _componentSelect2();
            //     /* Fim: Tabela Personalizada */

            //     //Ao mudar o fornecedor, filtra a categoria, subcategoria e produto via ajax (retorno via JSON)
            //     $('#cmbFornecedor').on('change', function (e) {

            //         var cmbTipo = $('#cmbTipo').val();
            //         var inputFornecedor = $('#inputFornecedor').val();
            //         var cmbFornecedor = $('#cmbFornecedor').val();

            //         $('#inputFornecedor').val(cmbFornecedor);

            //         FiltraCategoria();
            //         Filtrando();
            //         FiltraServico();

            //         $.getJSON('filtraCategoria.php?idFornecedor=' + cmbFornecedor, function (dados) {

            //             var option = '<option value="">Selecione a Categoria</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.CategId + '">' + obj
            //                         .CategNome + '</option>';
            //                 });

            //                 $('#cmbCategoria').html(option).show();
            //             } else {
            //                 ResetCategoria();
            //             }
            //         });

            //         $.getJSON('filtraSubCategoria.php?idFornecedor=' + cmbFornecedor, function (dados) {

            //             var option = '<option value="">Selecione a SubCategoria</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.SbCatId + '">' + obj
            //                         .SbCatNome + '</option>';
            //                 });

            //                 $('#cmbSubCategoria').html(option).show();
            //             } else {
            //                 ResetSubCategoria();
            //             }
            //         });

            //         $.getJSON('filtraProduto.php?idFornecedor=' + cmbFornecedor, function (dados) {

            //             var option = '<option value="" "selected">Selecione o Produto</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.ProduId + '">' + obj
            //                         .ProduNome + '</option>';
            //                 });

            //                 $('#cmbProduto').html(option).show();
            //             } else {
            //                 ResetProduto();
            //             }
            //         });

            //         $.getJSON('filtraServico.php?idFornecedor=' + cmbFornecedor, function (dados) {

            //             var option = '<option value="" "selected">Selecione o Serviço</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.ServiId + '">' + obj
            //                         .ServiNome + '</option>';
            //                 });

            //                 $('#cmbServico').html(option).show();
            //             } else {
            //                 ResetServico();
            //             }
            //         });

            //     });

            //     //Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
            //     $('#cmbCategoria').on('change', function (e) {

            //         Filtrando();

            //         var cmbCategoria = $('#cmbCategoria').val();

            //         $.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function (dados) {

            //             var option = '<option value="">Selecione a SubCategoria</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.SbCatId + '">' + obj
            //                         .SbCatNome + '</option>';
            //                 });

            //                 $('#cmbSubCategoria').html(option).show();
            //             } else {
            //                 ResetSubCategoria();
            //             }
            //         });

            //         $.getJSON('filtraProduto.php?idCategoria=' + cmbCategoria, function (dados) {

            //             var option = '<option value="" "selected">Selecione o Produto</option>';

            //             if (dados.length) {

            //                 $.each(dados, function (i, obj) {
            //                     option += '<option value="' + obj.ProduId + '">' + obj
            //                         .ProduNome + '</option>';
            //                 });

            //                 $('#cmbProduto').html(option).show();
            //             } else {
            //                 ResetProduto();
            //             }
            //         });

            //     });

            //     //Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
            //     $('#cmbSubCategoria').on('change', function (e) {

            //         FiltraProduto();

            //         var cmbTipo = $('#cmbTipo').val();
            //         var cmbFornecedor = $('#cmbFornecedor').val();
            //         var cmbCategoria = $('#cmbCategoria').val();
            //         var cmbSubCategoria = $('#cmbSubCategoria').val();

            //         if (cmbTipo == 'S' || cmbTipo == 'T') {
            //             cmbFornecedor = '#';
            //         }

            //         if (cmbFornecedor != '#' && cmbFornecedor != '') {
            //             $.getJSON('filtraProduto.php?idFornecedor=' + cmbFornecedor + '&idCategoria=' +
            //                 cmbCategoria + '&idSubCategoria=' + cmbSubCategoria,
            //                 function (dados) {

            //                     var option =
            //                         '<option value="#" "selected">Selecione o Produto</option>';

            //                     if (dados.length) {

            //                         $.each(dados, function (i, obj) {
            //                             option += '<option value="' + obj.ProduId + '">' + obj
            //                                 .ProduNome + '</option>';
            //                         });

            //                         $('#cmbProduto').html(option).show();
            //                     } else {
            //                         ResetProduto();
            //                     }
            //                 });
            //         } else if (cmbCategoria != '#' && cmbCategoria != '') {
            //             $.getJSON('filtraProduto.php?idCategoria=' + cmbCategoria + '&idSubCategoria=' +
            //                 cmbSubCategoria,
            //                 function (dados) {

            //                     var option =
            //                         '<option value="#" "selected">Selecione o Produto</option>';

            //                     if (dados.length) {

            //                         $.each(dados, function (i, obj) {
            //                             option += '<option value="' + obj.ProduId + '">' + obj
            //                                 .ProduNome + '</option>';
            //                         });

            //                         $('#cmbProduto').html(option).show();
            //                     } else {
            //                         ResetProduto();
            //                     }
            //                 });
            //         } else {
            //             $.getJSON('filtraProduto.php?idSubCategoria=' + cmbSubCategoria, function (dados) {

            //                 var option =
            //                     '<option value="#" "selected">Selecione o Produto</option>';

            //                 if (dados.length) {

            //                     $.each(dados, function (i, obj) {
            //                         option += '<option value="' + obj.ProduId + '">' + obj
            //                             .ProduNome + '</option>';
            //                     });

            //                     $('#cmbProduto').html(option).show();
            //                 } else {
            //                     ResetProduto();
            //                 }
            //             });
            //         }


            //     });

            //     //Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
            //     function Filtrando() {
            //         $('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');

            //     }

            //     //Mostra o "Filtrando..." na combo Produto
            //     function FiltraCategoria() {
            //         $('#cmbCategoria').empty().append('<option>Filtrando...</option>');
            //     }

            //     //Mostra o "Filtrando..." na combo Produto
            //     function FiltraProduto() {
            //         $('#cmbProduto').empty().append('<option>Filtrando...</option>');
            //     }

            //     function FiltraServico() {
            //         $('#cmbServico').empty().append('<option>Filtrando...</option>');
            //     }

            //     function ResetCategoria() {
            //         $('#cmbCategoria').empty().append('<option>Sem Categoria</option>');
            //     }

            //     function ResetSubCategoria() {
            //         $('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
            //     }

            //     function ResetProduto() {
            //         $('#cmbProduto').empty().append('<option>Sem produto</option>');
            //     }

            //     function ResetServico() {
            //         $('#cmbServico').empty().append('<option>Sem serviço</option>');
            //     }

            //     let resultadosConsulta = '';
            //     let inputsValues = {};

            //     function Filtrar() {
            //         let cont = false;

            //         $('#submitFiltro').on('click', (e) => {
            //             e.preventDefault()

            //             const msg = $(
            //                 '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>'
            //             )

            //             if ($('#cmbProduto').val() == 'Sem produto' || $('#cmbProduto').val() ==
            //                 'Filtrando...') $('#cmbProduto').val("")

            //             let dataDe = $('#inputDataDe').val()
            //             let dataAte = $('#inputDataAte').val()
            //             let tipo = $('#cmbTipo').val()
            //             let fornecedor = $('#cmbFornecedor').val()
            //             let categoria = $('#cmbCategoria').val()
            //             let subCategoria = $('#cmbSubCategoria').val()
            //             let inputProduto = $('#cmbProduto').val()
            //             let inputServico = $('#cmbServico').val()
            //             let codigo = $('#cmbCodigo').val()
            //             let tipoDeFiltro = $('input[name="inputTipo"]:checked').val();
            //             let url = "";
            //             tipoDeFiltro == 'P' ? url = "relatorioMovimentacaoFiltraProduto.php" : url =
            //                 "relatorioMovimentacaoFiltraServico.php";

            //             inputsValues = {
            //                 inputDataDe: dataDe,
            //                 inputDataAte: dataAte,
            //                 cmbTipo: tipo,
            //                 cmbFornecedor: fornecedor,
            //                 cmbCategoria: categoria,
            //                 cmbSubCategoria: subCategoria,
            //                 cmbProduto: inputProduto,
            //                 cmbServico: inputServico,
            //                 cmbCodigo: codigo,
            //             };

            //             $.post(
            //                 url,
            //                 inputsValues,
            //                 (data) => {

            //                     if (data) {
            //                         $('tbody').html(data)
            //                         $('#imprimir').removeAttr('disabled')
            //                         resultadosConsulta = data
            //                     } else {
            //                         $('tbody').html(msg)
            //                         $('#imprimir').attr('disabled', '')
            //                     }
            //                 }
            //             );
            //         })
            //     }
            //     Filtrar()


            //     function imprime() {
            //         url = 'relatorioMovimentacaoImprime.php';

            //         $('#imprimir').on('click', (e) => {
            //             e.preventDefault()
            //             console.log('teste')
            //             if (resultadosConsulta) {
            //                 let tipo = $('input[name="inputTipo"]:checked').val()

            //                 $('#TipoProdutoServico').val(tipo)
            //                 $('#inputResultado').val(resultadosConsulta)
            //                 $('#inputDataDe_imp').val(inputsValues.inputDataDe)
            //                 $('#inputDataAte_imp').val(inputsValues.inputDataAte)
            //                 $('#cmbTipo_imp').val(inputsValues.cmbTipo)
            //                 $('#cmbFornecedor_imp').val(inputsValues.cmbFornecedor)
            //                 $('#cmbCategoria_imp').val(inputsValues.cmbCategoria)
            //                 $('#cmbSubCategoria_imp').val(inputsValues.cmbSubCategoria)
            //                 $('#cmbProduto_imp').val(inputsValues.cmbProduto)
            //                 $('#cmbServico_imp').val(inputsValues.cmbServico)
            //                 $('#cmbCodigo_imp').val(inputsValues.cmbCodigo)

            //                 $('#formImprime').attr('action', url)

            //                 $('#formImprime').submit()
            //             }
            //         })

            //     }
            //     imprime()

        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        // function atualizaFornecedor(ForneId, ForneNome, ForneStatus, Tipo) {

        //     if (Tipo == 'imprime') {

        //         document.getElementById('inputFornecedorCategoria').value = document.getElementById('cmbCategoria')
        //             .value;

        //         document.formFornecedor.action = "fornecedorImprime.php";
        //         document.formFornecedor.setAttribute("target", "_blank");
        //     } else {
        //         document.getElementById('inputFornecedorId').value = ForneId;
        //         document.getElementById('inputFornecedorNome').value = ForneNome;
        //         document.getElementById('inputFornecedorStatus').value = ForneStatus;

        //         if (Tipo == 'edita') {
        //             document.formFornecedor.action = "fornecedorEdita.php";
        //         } else if (Tipo == 'exclui') {
        //             confirmaExclusao(document.formFornecedor, "Tem certeza que deseja excluir esse fornecedor?",
        //                 "fornecedorExclui.php");
        //         } else if (Tipo == 'mudaStatus') {
        //             document.formFornecedor.action = "fornecedorMudaSituacao.php";
        //         }
        //     }

        //     document.formFornecedor.submit();
        // }

        // function selecionaTipo(tipo) {
        //     if (tipo == 'P') {
        //         document.getElementById('Produto').style.display = "block";
        //         document.getElementById('Servico').style.display = "none";
        //     } else {
        //         document.getElementById('Produto').style.display = "none";
        //         document.getElementById('Servico').style.display = "block";
        //     }
        // }
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
                                                <label for="inputDataInicio">Período de</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="icon-calendar22"></i></span>
                                                    </span>
                                                    <input type="date" id="inputDataDe" name="inputDataInicio"
                                                        class="form-control" value="<?php echo $dataInicio; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputDataFim">Até</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="icon-calendar22"></i></span>
                                                    </span>
                                                    <input type="date" id="inputDataAte" name="inputDataFim"
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
													$sql = "SELECT CategId, CategNome
																FROM Categoria
																JOIN Situacao on SituaId = CategStatus
																WHERE CategUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCategoria as $item) {
														print('<option value="' . $item['CategId'] . '">' . $item['CategNome'] . '</option>');
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
                                                                    print('<option value="' . $item['SituatId'] . '">' . $item['SituaNome'] . '</option>');
                                                                }
													        }
													    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right col-2 pt-3">
                                                <div>
                                                    <button id="pesquisar" class="btn btn-success">Pesquisar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right pt-3">
                                            <div>
                                                <button id="novoLacamento" class="btn btn-success"><a
                                                        href="novoLancamento.php"
                                                        style="text-decoration:none; color: #FFF">Novo
                                                        Lançamento</a></button>
                                                <button id="efetuarPagamento" class="btn btn-success">Efetuar
                                                    Pagamento</button>
                                                <button class="btn bg-secondary"><i class="icon-printer2"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <table class="table" id="tblMovimentacao">
                                    <thead>
                                        <tr class="bg-slate">
                                            <th>Vencimento</th>
                                            <th style='text-align: center'>Descrição</th>
                                            <th>Fornecedor</th>
                                            <th>Plano de Contas</th>
                                            <th>Número Doc.</th>
                                            <th>Valor Total</th>
                                            <th>Saldo</th>
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