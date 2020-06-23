<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Licitação';

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
        const selectEstCo = $('#selectSetadoConservacao').html()

        /*function modalAcoes() {

            $('.btn-acoes').each((i, elem) => {
                $(elem).on('click', function() {
                    $('#page-modal').fadeIn(200);

                    let linha = $(elem).parent().parent()

                    let id = linha.attr('idPatrimonio')
                    let editado = linha.attr('editado')

                    let tds = linha.children();
                    let produto = $(tds[1]).html();
                    let patrimonio = $(tds[2]).html();
                    let notaFisc = $(tds[3]).html();
                    let aquisicao = $(tds[4]).html();
                    let depreciacao = $(tds[5]).html();
                    let origem = $(tds[7]).html();
                    let destino = $(tds[8]).html();
                    let marca = $(tds[9]).html();
                    let fabricante = $(tds[10]).html();
                    let numeroSerie = $(tds[12]).children().first().val()
                    let estadoConservacao = $(tds[13]).children().first().val()
                    //console.log(numeroSerie)

                    const fonte1 = 'style="font-size: 1.1rem"'
                    const fonte2 = 'style="font-size: 0.9rem"'
                    const textCenter = 'style="text-align: center"'
                    const styleLabel1 = 'style="min-width: 250px; font-size: 0.9rem"'
                    const styleLabel2 = 'style="min-width: 150px; font-size: 0.9rem"'
                    const styleLabel3 = 'style="min-width: 100px; font-size: 0.9rem"'
                    const marginP = 'style="font-size: 0.9rem; margin-top: 4px"'

                    var NumSerie = numeroSerie ? numeroSerie : ''

                    $('#numeroSerie').val(NumSerie)
                    
                    $('#cmbEstadoConservacao').val(estadoConservacao)

                    if (estadoConservacao) {
                        url = 'filtraEstadoConservacao.php'
                            inputsValues = {
                                inputEstadoConservacao: estadoConservacao
                            }
                            
                            $.post(
                                url,
                                inputsValues,
                                (data) => {
                                    console.log(data)
                                    if (data) {
                                        $('#cmbEstadoConservacao').html(data)
                                        
                                        console.log(data)
                                    } else {}
                                }
                            );
                    }

                    formModal = `
                                    <div class='row'>
                                         <div class='col-lg-2'>
                                             <div class="form-group">
                                                 <label for="produto">Patrimônio</label>
                                                 <div class="input-group">
                                                    <input id='produto' class='form-control' value='${patrimonio}' readOnly />
                                                 </div>
                                            </div>
                                         </div>                                    
                                         <div class='col-lg-10'>
                                             <div class="form-group">
                                                 <label for="produto">Produto</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${produto}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                    </div>
                                    <div class='row'>
                                         <div class='col-lg-6'>
                                              <div class="form-group">
                                                  <label for="produto">Origem</label>
                                                  <div class="input-group">
                                                    <input id='produto' class='form-control' value='${origem}' readOnly />
                                                  </div>
                                             </div>
                                          </div>
                                          <div class='col-lg-6'>
                                              <div class="form-group">
                                                  <label for="produto">Destino</label>
                                                  <div class="input-group">
                                                    <input id='produto' class='form-control' value='${destino}' readOnly />
                                                  </div>
                                             </div>
                                         </div>
                                     </div>
                                     
                                    <div class='row'>
                                         <div class='col-lg-3'>
                                             <div class="form-group">
                                                 <label for="produto">Nota Fiscal</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${notaFisc}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                         <div class='col-lg-3'>
                                             <div class="form-group">
                                                 <label for="produto">Data da Compra</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                         <div class='col-lg-3'>
                                             <div class="form-group">
                                                 <label for="produto">(R$) Aquisição</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${aquisicao}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                         <div class='col-lg-3'>
                                             <div class="form-group">
                                                 <label for="produto">(R$) Depreciação</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${depreciacao}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                     </div>
                                     <div class='row'>
                                         <div class='col-lg-6'>
                                             <div class="form-group">
                                                 <label for="produto">Marca</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${marca}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                         <div class='col-lg-6'>
                                             <div class="form-group">
                                                 <label for="produto">Fabricante</label>
                                                 <div class="input-group">
                                                     <input id='produto' class='form-control' value='${fabricante}' readOnly />
                                                 </div>
                                            </div>
                                         </div>
                                     </div>
                                     <input type="text" id="inputProdutoEdita" name="inputProdutoEdita" value="${id}" style="display: none">
                    `;
                    $('.dados-produto').html(formModal)
                })
            })

            $('#modal-close').on('click', function() {
                $('#page-modal').fadeOut(200);
                $('body').css('overflow', 'scroll');
            })
        }*/

        $(document).ready(function() {

            /* Início: Tabela Personalizada */
            $('#tblLicitacao').DataTable({
                "order": [
                    [0, "desc"]
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        orderable: true,
                        width: 5,
                        targets: [0]
                    },
                    {
                        orderable: true,
                        width: 10,
                        targets: [1]
                    },
                    {
                        orderable: true,
                        width: 10,
                        targets: [2]
                    },
                    {
                        orderable: true,
                        width: 10,
                        targets: [3]
                    },
                    {
                        orderable: true,
                        width: 10,
                        targets: [4]
                    },
                    {
                        orderable: true,
                        width: 5,
                        targets: [5]
                    },
                    {
                        orderable: true,
                        width: 5,
                        targets: [6]
                    },
                    {
                        orderable: true,
                        width: 5,
                        targets: [7]
                    },
                    {
                        orderable: true,
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
                    },
                    {
                        orderable: false,
                        width: 5,
                        targets: [11]
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
                    let unidade = $('#cmbUnidade').val()
                    let empresaContratada = $('#cmbEmpresaContratada').val()
                    let categoria = $('#cmbCategoria').val()
                    let classificacao = $('#cmbClassificacao').val()
                    let modalidade = $('#cmbModalidade').val()
                    let prioridade = $('#cmbPrioridade').val()
                    let status = $('#cmbStatus').val()
                    let url = "relatorioLicitacaoFiltra.php";

                    inputsValues = {
                        inputDataDe: dataDe,
                        inputDataAte: dataAte,
                        cmbUnidade: unidade,
                        cmbEmpresaContratada: empresaContratada,
                        cmbCategoria: categoria,
                        cmbClassificacao: classificacao,
                        cmbModalidade: modalidade,
                        cmbPrioridade: prioridade,
                        cmbStatus: status
                    };

                    console.log( inputsValues )

                    $.post(
                        url,
                        inputsValues,
                        (data) => {

                            if (data) {
                                console.log(data)
                                $('tbody').html(data)
                                $('#imprimir').removeAttr('disabled')
                                resultadosConsulta = data
                                //modalAcoes()
                            } else {
                                $('tbody').html(msg)
                                $('#imprimir').attr('disabled', '')
                            }
                        }
                    );
                })
            })()

            $('#salvar').on('click', function(e) {
                let numeroSerie = $('#numeroSerie').val()
                let estadoConservacao = $('#cmbEstadoConservacao').val()
                let id = $('#inputProdutoEdita').val()
                let url = 'relatorioMovimentacaoPatrimonioEdita.php'
                let data = {
                    inputNumeroSerie: numeroSerie,
                    cmbEstadoConservacao: estadoConservacao,
                    inputId: id
                }
                console.log(data)

                $.post(
                    url,
                    data,
                    function(data) {
                        if (data) {
                            console.log(data)
                            alerta('Atenção', 'Registro editado', 'success');

                            //let inputNumeroSerie = $(`<td style="display: none" id="inputNumeroSerie">${numeroSerie}</td>`)
                            //let inputEstadoConservacao = $(`<td style="display: none" id="inputEstadoConservacao">${estadoConservacao}</td>`)

                            $('[idpatrimonio]').each((i, elem) => {
                                let tds = $(elem).children()
                                console.log()
                                if ($(elem).attr('idpatrimonio') == id) {
                                    $(tds[12]).children().first().val(numeroSerie)
                                    $(tds[13]).children().first().val(estadoConservacao)
                                    console.log($(tds[12]).children().first())
                                    // $(elem).append(inputNumeroSerie).append(inputEstadoConservacao)
                                }
                            })
                        }
                    }
                )

                $('#page-modal').fadeOut(200);
                $('body').css('overflow', 'scroll');
            })

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
                                <input id="cmbLocal_imp" type="hidden" name="cmbLocal_imp"></input>
                                <input id="cmbEmpresaContratada_imp" type="hidden" name="cmbEmpresaContratada_imp"></input>
                                <input id="cmbCategoria_imp" type="hidden" name="cmbCategoria_imp"></input>
                                <input id="cmbClassificacao_imp" type="hidden" name="cmbClassificacao_imp"></input>
                                <input id="cmbModalidade_imp" type="hidden" name="cmbModalidade_imp"></input>
                                <input id="cmbPrioridade_imp" type="hidden" name="cmbPrioridade_imp"></input>
                                <input id="cmbStatus_imp" type="hidden" name="cmbStatus_imp"></input>
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
                                            <label for="cmbStatus">Status</label>
                                            <select id="cmbStatus" name="cmbStatus" class="form-control form-control-select2">
                                               <option value="">Selecionar</option>
                                                <?php
                                                    $sql = "SELECT SituaId, SituaNome
                                                            FROM Situacao
                                                            WHERE SituaStatus = 1
                                                            ORDER BY SituaNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);
    
                                                    foreach ($rowSituacao as $item) {
                                                        print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbEmpresaContratada">Empresa Contratada</label>
                                            <select id="cmbEmpresaContratada" name="cmbEmpresaContratada" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
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
                                            <label for="cmbClassificacao">Classificação</label>
                                            <select id="cmbClassificacao" name="cmbClassificacao" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <option value="PS">Todos</option>
                                                <option value="P">Produto</option>
                                                <option value="S">Serviço</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbModalidade">Modalidade</label>
                                            <select id="cmbModalidade" name="cmbModalidade" class="form-control form-control-select2">
                                               <option value="">Selecionar</option>
                                                <?php
                                                    $sql = "SELECT MdLicId, MdLicNome
                                                            FROM ModalidadeLicitacao
                                                            JOIN Situacao on SituaId = MdLicStatus
                                                            WHERE SituaChave = 'ATIVO'
                                                            ORDER BY MdLicNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowMdLic = $result->fetchAll(PDO::FETCH_ASSOC);
    
                                                    foreach ($rowMdLic as $item) {
                                                        print('<option value="' . $item['MdLicId'] . '">' . $item['MdLicNome'] . '</option>');
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbPrioridade">Prioridade</label>
                                            <select id="cmbPrioridade" name="cmbPrioridade" class="form-control form-control-select2">
                                               <option value="">Selecionar</option>
                                                <?php
                                                    $sql = "SELECT PriorId, PriorNome
                                                            FROM Prioridade
                                                            JOIN Situacao on SituaId = PriorStatus
                                                            WHERE SituaChave = 'ATIVO'
                                                            ORDER BY PriorNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowPrioridade = $result->fetchAll(PDO::FETCH_ASSOC);
    
                                                    foreach ($rowPrioridade as $item) {
                                                        print('<option value="' . $item['PriorId'] . '">' . $item['PriorNome'] . '</option>');
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="cmbUnidade">Local</label>
                                            <select id="cmbUnidade" name="cmbUnidade" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = "SELECT UnidaId, UnidaNome
                                                        FROM Unidade
                                                        JOIN Situacao on SituaId = UnidaStatus
                                                        WHERE SituaChave = 'ATIVO'
                                                        ORDER BY UnidaNome ASC";
                                                $result = $conn->query($sql);
                                                $rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowUnidade as $item) {
                                                    print('<option value="' . $item['UnidaId'] . '">' . $item['UnidaNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
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

                            <table class="table" id="tblLicitacao">
                                <thead>
                                    <tr class="bg-slate">
                                        <th>Item</th>
                                        <th>Categoria</th>
                                        <th>Classificação</th>
                                        <th>Empresa Contratada</th>
                                        <th>Local</th>
                                        <th>Status</th>
                                        <th>Modalidade</th>
                                        <th>Inicio</th>
                                        <th>Término</th>
                                        <th>Prioridade</th>
                                        <th>Observação</th>
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
                            <form id="editarProduto" method="POST">
                                <div class="dados-produto p-3">

                                </div>
                                <div class="d-flex flex-row p-2">
                                    <div class='col-lg-6'>
                                        <div class="form-group">
                                            <label for="numeroSerie">Nº Série <span class="text-danger">(Editável)</span></label>
                                            <div class="input-group">
                                                <input type="text" id="numeroSerie" name="numeroSerie" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="numeroSerie">Estado de Conservação <span class="text-danger">(Editável)</span></label>
                                        <div class="form-group">
                                            <select id="cmbEstadoConservacao" name="cmbEstadoConservacao" class="form-control form-control-select2">
                                                <option value="">Selecionar</option>
                                                <?php
                                                $sql = "SELECT EstCoId, EstCoNome
                                                        FROM EstadoConservacao
                                                        JOIN Situacao on SituaId = EstCoStatus
                                                        WHERE SituaChave = 'ATIVO'
                                                        ORDER BY EstCoNome ASC";
                                                $result = $conn->query($sql);
                                                $rowEstCo = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowEstCo as $item) {
                                                    print('<option value="' . $item['EstCoId'] . '">' . $item['EstCoNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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