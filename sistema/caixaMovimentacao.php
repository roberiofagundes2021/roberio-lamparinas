<?php

include_once("sessao.php");

//Fazer alteração posteriormente
$_SESSION['PaginaAtual'] = 'Movimentação do Caixa';

include('global_assets/php/conexao.php');

$sql = "SELECT ForneId, ForneNome, ForneCpf, ForneCnpj, ForneTelefone, ForneCelular, ForneStatus, CategNome
		FROM Fornecedor
		JOIN Categoria on CategId = ForneCategoria
	    WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY ForneNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

$data = date("Y-m-d");

$visibilidadeResumoFinanceiro = isset($_SESSION['ResumoFinanceiro']) && $_SESSION['ResumoFinanceiro'] ? 'sidebar-right-visible' : ''; 
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Movimentação do Caixa</title>

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
                    [0, "desc"]
                ],
                responsive: true,
                columnDefs: [{
                        orderable: true, //Nº registro
                        width: "15%",
                        targets: [0]
                    },
                    {
                        orderable: true, //Data Hora
                        width: "16%",
                        targets: [1]
                    },
                    {
                        orderable: true, //Histórico
                        width: "18%",
                        targets: [2]
                    },
                    {
                        orderable: true, //Tipo
                        width: "10%",
                        targets: [3]
                    },
                    {
                        orderable: true, //Forma de Rec/Pag
                        width: "18%",
                        targets: [4]
                    },
                    {
                        orderable: true, //Valor Total
                        width: "14%",
                        targets: [5]
                    },
                    {
                        orderable: true, //Status
                        width: "5%",
                        targets: [6]
                    },
                    {
                        orderable: false, //Ações
                        width: "4%",
                        targets: [7]
                    }
                ],
                dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
                }
            });

            function filtrar() {
                const msg = $(
                    '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty"><img src="global_assets/images/lamparinas/loader.gif" style="width: 120px"></td></tr>'
                )

                $('tbody').html(msg)

                let periodoDe = $('#inputPeriodoDe').val()
                let ate = $('#inputAte').val()
                let clientes = $('#cmbClientes').val()
                let formaPagamento = $("#cmbFormaPagamento").val()
                let status = $("#cmbStatus").val()
                let urlConsultaAberturaCaixa = "caixaMovimentacaoFiltra.php";

                let inputsValuesConsulta = {
                    inputPeriodoDe: periodoDe,
                    inputAte: ate,
                    cmbClientes: clientes,
                    inputFormaPagamento: formaPagamento,
                    cmbStatus: status
                }; 

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    data: inputsValuesConsulta,
                    success: function(resposta) {
                        //|--Aqui é criado o DataTable caso seja a primeira vez q é executado e o clear é para evitar duplicação na tabela depois da primeira pesquisa
                        let table 
                        table = $('#tblMovimentacao').DataTable()
                        table = $('#tblMovimentacao').DataTable().clear().draw()
                        //--|

                        table = $('#tblMovimentacao').DataTable()

                        let rowNode

                        let valorTotal = null;
                        let descontoTotal = null;

                        resposta.forEach(item => {
                            rowNode = table.row.add(item.data).draw().node()

                            $(rowNode).find('td').eq(5).attr('style', 'text-align: right;')
                        })

                        valorFinal = valorTotal - descontoTotal;
                        valorFinal = (valorFinal != null && valorFinal > 0) ?  float2moeda(valorFinal) : null;
                        valorTotal = (valorTotal != null) ?  float2moeda(valorTotal) : null;
                        descontoTotal = (descontoTotal != null) ?  float2moeda(descontoTotal) : null;
                        
                        $("#inputValorTotal").val(valorTotal);
                        $("#inputDesconto").val(descontoTotal);
                        $("#inputValorFinal").val(valorFinal);
                    }
                })
            }

            filtrar();

            $("#submitFiltro").on('click', () => {
                filtrar();
            })

            $("#cmbCaixa").on("change", function() {
                let nomeCaixa = $(this).find('option').filter(':selected').text();
                
                $("#inputCaixaNome").val(nomeCaixa);

                let urlConsultaAberturaCaixa = "consultaCaixaSaldo.php";
                let idCaixa = ($(this).val() != "") ? $(this).val() : 0;

                let inputsValuesConsulta = {
                    inputCaixaId: idCaixa
                }; 

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    data: inputsValuesConsulta,
                    success: function(resposta) {
                        //Caixa aberto pela primeira vez
                        if(resposta == 'abrirCaixa') {
                            $("#inputSaldoInicial").val(float2moeda(0));
                        }else {
                            //Verifica se o último caixa do operador já foi fechado, se sim irá aparecer uma tela para abrir novamente com o saldo anterior
                            if(resposta.CxAbeDataHoraFechamento != null) {
                                $("#inputSaldoInicial").val(float2moeda(resposta.CxAbeSaldoFinal));
                            }
                        }
                    }
                })
            })

            $("#btnPdv").on('click', () => {
                let urlConsultaAberturaCaixa = "consultaAberturaCaixa.php";

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    success: function(resposta) {
                        if(resposta == 'abrirCaixa') {
                            $("#aberturaCaixa").trigger("click");
                        }else {
                            //Verifica se o último caixa do operador já foi fechado, se sim irá aparecer uma tela para abrir novamente
                            if(resposta.CxAbeDataHoraFechamento != null) {
                                $("#aberturaCaixa").trigger("click");
                            }else {
                                let arrayDataAbertura = resposta.CxAbeDataHoraAbertura.split(" ")
                                let dataAbertura = arrayDataAbertura[0]

                                let arrayDataAtual = new Date();

                                let mes = arrayDataAtual.getMonth()+1;
                                let dia = arrayDataAtual.getDate();

                                let dataAtual = arrayDataAtual.getFullYear() + '-' +
                                    (mes <10 ? '0' : '') + mes + '-' +
                                    (dia <10 ? '0' : '') + dia;

                                //alert('Data abertura: ' + dataAbertura + ' Data atual: ' + dataAtual)

                                //Verifica se o caixa foi aberto hoje, se não foi o operador terá que fechar
                                if(dataAbertura == dataAtual) {
                                    $("#inputAberturaCaixaId").val(resposta.CxAbeId);
                                    $("#inputAberturaCaixaNome").val(resposta.CaixaNome);

                                    document.formCaixaAbertura.action = "caixaPDV.php";
                                    document.formCaixaAbertura.submit();
                                }else {
                                    $("#inputAberturaCaixaId").val(resposta.CxAbeId);                    
                                    $("#inputCaixaId").val(resposta.CxAbeCaixa);
                                    $("#inputAberturaCaixaNome").val(resposta.CaixaNome);

                                    document.formCaixaAbertura.action = "caixaFechamento.php";
                                    document.formCaixaAbertura.submit();
                                }
                            }
                        }
                    }
                })
            }) 

            function consultaSaldoCaixaAtual() {
                let urlConsultaAberturaCaixa = "consultaCaixaSaldoAtual.php";
                
                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    success: function(resposta) {
                        if(resposta != 'consultaVazia') {
                            let valorRecebido = resposta[0].SaldoRecebido;
                            let valorPago = resposta[1].SaldoPago;
        
                            let saldo = valorRecebido - valorPago;
                            
                            $("#inputRecebido").val(float2moeda(valorRecebido));
                            $("#inputPago").val(float2moeda(valorPago * -1));
        
        
                            $("#inputSaldo").val(float2moeda(saldo));
                        }else {
                            $("#inputRecebido").val('');
                            $("#inputSaldo").val('');
                        }
                    }
                })
            }

            consultaSaldoCaixaAtual();
            
            $("#btnFinalizarRetirada").on('click', () => {
                if($("#valorRetirada").val() == '') {
                    $("#valorRetirada").focus();
                        
                    var menssagem = 'Informe um valor retirado!'
                    alerta('Atenção', menssagem, 'error')
                    return
                }
                
                if($("#pagamentoRetirada").val() == '') {
                    $("#pagamentoRetirada").focus();
                        
                    var menssagem = 'Informe uma forma de pagamento!'
                    alerta('Atenção', menssagem, 'error')
                    return
                }

                if($("#justificativa").val() == '') {
                    $("#justificativa").focus();
                        
                    var menssagem = 'Informe uma justificativa!'
                    alerta('Atenção', menssagem, 'error')
                    return
                }

                //Para fechar o pop up dps que é feito uma retirada
                $("#btnCancelar").trigger("click");

                let idCaixaAbertura = $("#inputAberturaCaixaId").val();
                let valorRetirado = $("#valorRetirada").val().replace(".", "").replace(",", ".");
                let arrayFormaPagamento = $("#pagamentoRetirada").val().split('-');
                let formaPagamento = arrayFormaPagamento[0];
                let justificativa = $("#justificativa").val(); 

                let inputsValuesConsulta = {
                    inputAberturaCaixaId: idCaixaAbertura,
                    inputValorRetirado: valorRetirado,
                    inputFormaPagamento: formaPagamento,
                    inputJustificativa: justificativa
                }; 

                let urlConsultaAberturaCaixa = "caixaPagamentoNovo.php";
                
                //Cadastro do caixa pagamento
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    data: inputsValuesConsulta,
                    success: function(resposta) {
                        if(resposta != 'Impossivel retirar') {
                            $("#valorRetirada").val('');
                            $("#pagamentoRetirada").val('').change()
                            $("#justificativa").val('');
    
                            filtrar();
                            consultaSaldoCaixaAtual();
    
                            $("#inputValorRetirada").val(valorRetirado);
                            $("#cmbPagamentoRetirada").val(formaPagamento);
                            $("#inputJustificativa").val(justificativa);
    
                            $('#formRetiradaCaixa').attr('action', 'caixaImprimiReciboRetirada.php');
                            $('#formRetiradaCaixa').attr('target', '_blank');
                            $('#formRetiradaCaixa').submit();
                        }else {
                            var menssagem = 'Não é possível retirar um valor superior ao saldo do atual!'
                            alerta('Atenção', menssagem, 'error')
                            return
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        var menssagem = 'Ocorreu um erro ao fazer a retirada!'
                        alerta('Atenção', menssagem, 'error')
                        return
                    }  
                })
            })

            $("#btnFechamento").on('click', () => {
                let urlConsultaAberturaCaixa = "consultaAberturaCaixa.php";

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    success: function(resposta) {
                        $("#inputAberturaCaixaId").val(resposta.CxAbeId);                    
                        $("#inputCaixaId").val(resposta.CxAbeCaixa);
                        $("#inputAberturaCaixaNome").val(resposta.CaixaNome);

                        document.formCaixaAbertura.action = "caixaFechamento.php";
                        document.formCaixaAbertura.submit();
                    }
                })
            }) 

            //Função para determinar a visibilidade de Retirada e Fechamento do caixa no "Resumo do Caixa"
            function consultaSituacaoCaixa() {
                let urlConsultaAberturaCaixa = "consultaAberturaCaixa.php";

                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    success: function(resposta) {
                        //Essa condicional acontece quando n há registros no banco
                        if(resposta == 'abrirCaixa') {
                            $("#caixaEmOperacao").hide();
                        }else {
                            //Se há uma data de fechamento do caixa, significa que n há um caixa aberto
                            if(resposta.CxAbeDataHoraFechamento != null) {
                                $("#caixaEmOperacao").hide();
                            }
                            
                            $("#inputAberturaCaixaId").val(resposta.CxAbeId);
                            $("#inputSaldoInicial").val(float2moeda(resposta.CxAbeSaldoFinal));
                        }
                    }
                })
            }

            consultaSituacaoCaixa();
        });
    </script>

</head>

<body class="navbar-top <?php echo $visibilidadeResumoFinanceiro; ?> sidebar-xs">

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
                                <h3 class="card-title">Movimentação do Caixa</h3>
                            </div>

                            <div class="card-body">
                                <!--<p class="font-size-lg">Utilize os filtros abaixo para gerar o relatório.</p>
                                <br>-->

                                <!--Link para abertura de caixa-->
                                <a id="aberturaCaixa" data-toggle="modal" data-target="#modal_small_abertura_caixa"></a>

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

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="inputPeriodoDe">Período de</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="icon-calendar22"></i></span>
                                                </span>
                                                <input type="date" id="inputPeriodoDe" name="inputPeriodoDe" min="1800-01-01" max="2100-12-12"
                                                    class="form-control" value="<?php if(isset($_SESSION['MovCaixaPeriodoDe'])) echo $_SESSION['MovCaixaPeriodoDe'];  else echo $data; ?>">
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
                                                <input type="date" id="inputAte" name="inputAte" min="1800-01-01" max="2100-12-12"
                                                    class="form-control" value="<?php if(isset($_SESSION['MovCaixaAte'])) echo $_SESSION['MovCaixaAte'];  else echo $data; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="cmbClientes">Clientes</label>
                                            <select id="cmbClientes" name="cmbClientes" class="form-control form-control-select2">
                                                <option value="">Todos</option>
                                                <?php
                                                    try {
                                                        $sql = "SELECT *
                                                                FROM  Cliente
                                                                JOIN Situacao ON SituaId = ClienStatus
                                                                WHERE ClienUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                                ORDER BY ClienNome ASC";
                                                        $result = $conn->query($sql);
                                                        $rowCliente = $result->fetchAll(PDO::FETCH_ASSOC);

                                                        try {

                                                            foreach ($rowCliente as $item) {
                                                                if (isset($_SESSION['MovCaixaFormaPagamento'])) {
                                                                    if ($item['ClienId'] == $_SESSION['MovCaixaCliente']) {
                                                                        print('<option value="' . $item['ClienId'] . '" selected>' . $item['ClienNome'] . '</option>');
                                                                    } else {
                                                                        print('<option value="' . $item['ClienId'] . '">' . $item['ClienNome'] . '</option>');
                                                                    }
                                                                } else {
                                                                    print('<option value="' . $item['ClienId'] . '">' . $item['ClienNome'] . '</option>');
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                                                        }
                                                    } catch (Exception $e) {
                                                        echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="cmbFormaPagamento">Forma de Rec/Pag</label>
                                            <select id="cmbFormaPagamento" name="cmbFormaPagamento"
                                                class="form-control form-control-select2">
                                                <option value="">Todos</option>
                                                <?php
                                                $sql = "SELECT FrPagId, FrPagNome, FrPagChave
                                                        FROM FormaPagamento
                                                        JOIN Situacao on SituaId = FrPagStatus
                                                        WHERE FrPagUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                        ORDER BY FrPagNome ASC";     
                                                $result = $conn->query($sql);
                                                $rowFormaPagamento = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowFormaPagamento as $item) {
                                                    if (isset($_SESSION['MovCaixaFormaPagamento'])) {
                                                        if ($item['FrPagId'] == $_SESSION['MovCaixaFormaPagamento']) {
                                                            print('<option value="' . $item['FrPagId'] . '" selected>' . $item['FrPagNome'] . '</option>');
                                                        } else {
                                                            print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
                                                        }
                                                    } else {
                                                        print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
                                                    }
                                                }

                                                //Para consulta
                                                /*
                                                foreach ($rowPlanoContas as $item) {
                                                    if(isset($_SESSION['MovCaixaPlanoContas'])){
                                                        if($item['PlConId'] == $_SESSION['MovCaixaPlanoContas']){
                                                            print('<option value="' . $item['PlConId'] . '" selected>' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');
                                                        } else {
                                                            print('<option value="' . $item['PlConId'] . '">' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');
                                                        }
                                                    } else {
                                                        print('<option value="' . $item['PlConId'] . '">' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');                                                            
                                                    }
                                                }
                                                */
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="cmbStatus">Status</label>
                                            <select id="cmbStatus" name="cmbStatus"
                                                class="form-control form-control-select2">
                                                <option value="">Todos</option>
                                                <?php
                                                    $sql = "SELECT SituaId, SituaNome, SituaChave
                                                            FROM Situacao
                                                            WHERE SituaStatus = 1 and (SituaChave = 'PAGO' or SituaChave = 'RECEBIDO' or SituaChave = 'ESTORNADO')
                                                            ORDER BY SituaNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($rowSituacao as $item) {
                                                        if (isset($_SESSION['MovCaixaStatus'])) {
                                                            if ($item['SituaId'] == $_SESSION['MovCaixaStatus']) {
                                                                print('<option value="' . $item['SituaId'].'" selected>' . $item['SituaNome'] . '</option>');
                                                            } else {
                                                                print('<option value="' . $item['SituaId'].'">' . $item['SituaNome'] . '</option>');
                                                            }
                                                        } else {
                                                            print('<option value="' . $item['SituaId'].'">' . $item['SituaNome'] . '</option>');
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 m-auto text-center">
                                        <button id="submitFiltro" class="btn btn-principal">Pesquisar</button>
                                    </div>
                                </div>

                                <table class="table" id="tblMovimentacao">
                                    <thead>
                                        <tr class="bg-slate">
                                            <th id='dataGrid'>Nº registro</th>
                                            <th>Data/Hora</th>
                                            <th>Histórico</th>
                                            <th>Tipo</th>
                                            <th>Forma de Rec/Pag</th>
                                            <th>Valor Total</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                           
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>

                            </div>

                        </div>
                        <!-- /basic responsive configuration -->

                    </div>
                </div>
                
                <form name="formCaixaAbertura" method="post">
					<input type="hidden" id="inputAberturaCaixaId" name="inputAberturaCaixaId" value="">
                    <input type="hidden" id="inputCaixaId" name="inputCaixaId" value="">
                    <input type="hidden" id="inputAberturaCaixaNome" name="inputAberturaCaixaNome" value="">
				</form>

                
                <form id="formRetiradaCaixa" name="formRetiradaCaixa" method="POST">
                    <input type="hidden" id="inputValorRetirada" name="inputValorRetirada" value="">
                    <input type="hidden" id="cmbPagamentoRetirada" name="cmbPagamentoRetirada" value="">
                    <input type="hidden" id="inputJustificativa" name="inputJustificativa" value="">
                </form>

            </div>
            <!-- /content area -->

            <!-- Small modal -->
            <!--Procurar uma correção com relação ao filtro do select-->
            <div id="modal_small_abertura_caixa" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="custon-modal-title">
                            <i class=""></i>
                            <h2 class="modal-title p-2">Abertura de Caixa</h2>
                            <i class=""></i>
                        </div>

                        <form id="formAbrirCaixa" method="POST" action="caixaPdv.php">
                            <input type="hidden" id="inputCaixaNome" name="inputCaixaNome" value="">

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5>Data: <?php echo date('d/m/Y'); ?></h5>
                                    </div>
    
                                    <div class="col-lg-6">
                                        <h5>Operador: <?php echo nomeSobrenome($_SESSION['UsuarNome'], 1); ?></h5>
                                    </div>
                                </div>
    
                                <div class="form-group mt-3">
                                    <!--Input para controle, para que caso acesse o PDV pela abertura de caixa ele fará o cadastro da nova abertura de caixa-->
                                    <input type="hidden" id="inputAbrirCaixa" name="inputAbrirCaixa" value="" class="form-control">

                                    <label for="cmbCaixa" class="font-size-lg">Caixa <span class="text-danger">*</span></label>
                                    <select id="cmbCaixa" name="cmbCaixa" class="form-control form-control-select2 select2-hidden-accessible" required="" tabindex="-1" aria-hidden="true">
                                        <option value="">Selecionar</option>
                                        <?php
                                        
                                        $sql = "SELECT CaixaId, CaixaNome, SituaNome
                                                FROM Caixa
                                                JOIN Situacao on SituaId = CaixaStatus
                                                WHERE CaixaUnidade = " . $_SESSION['UnidadeId'] . " 
                                                      and CaixaId not in (SELECT CxAbeCaixa 
                                                                          FROM CaixaAbertura
                                                                          WHERE CxAbeUnidade = " . $_SESSION['UnidadeId'] . " and CxAbeDataHoraFechamento is NULL)
                                                ORDER BY CaixaNome ASC";
                                        $result = $conn->query($sql);
                                        $rowCaixa = $result->fetchAll(PDO::FETCH_ASSOC);
    
                                        foreach ($rowCaixa as $item) {
                                            print('<option value="' . $item['CaixaId'] . '">'. $item['CaixaNome'] . '</option>');
                                        }
                                        ?>
                                    </select>
                                </div>
    
                                <div class="form-group mt-2">
                                    <!--Se informar depois sobre o saldo, como irá funcionar-->
                                    <label for="inputSaldoInicial" class="font-size-lg">Saldo Inicial</label>
                                    <input type="text" id="inputSaldoInicial" name="inputSaldoInicial" value="" class="form-control" readonly>
                                </div>
                            </div>
    
                            <div class="modal-footer">
                                <button type="button" class="btn btn-basic legitRipple" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn bg-slate legitRipple">Abrir Caixa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="modal_small_Retirada_Caixa" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="custon-modal-title">
                            <i class=""></i>
                            <h2 class="modal-title p-2">Retirada do Caixa</h2>
                            <i class=""></i>
                        </div>

                        <div class="modal-body">
                            <div class="row mt-2">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="valorRetirada" class="font-size-lg">Valor <span class="text-danger">*</span></label>
                                        <input type="text" id="valorRetirada" name="valorRetirada" onkeyup="moeda(this)" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <div class="form-group">
                                        <label for="pagamentoRetirada" class="font-size-lg">Forma de Pagamento <span class="text-danger">*</span></label>
                                        <select id="pagamentoRetirada" name="pagamentoRetirada" class="form-control form-control-select2 select2-hidden-accessible" required tabindex="-1" aria-hidden="true">
                                            <option value="">Selecionar</option>
                                            <?php
                                            $sql = "SELECT FrPagId, FrPagNome, FrPagChave
                                                    FROM FormaPagamento
                                                    JOIN Situacao on SituaId = FrPagStatus
                                                    WHERE FrPagUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                    ORDER BY FrPagNome ASC";
                               
                                            $result = $conn->query($sql);
                                            $rowFormaPagamento = $result->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($rowFormaPagamento as $item) {
                                                print('<option value="' . $item['FrPagId'] . '-' . $item['FrPagChave'] . '">' . $item['FrPagNome'] . '</option>');
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="justificativa">Justificativa<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <textarea id="justificativa" class="form-control" name="justificativa" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button id="btnCancelar" type="button" class="btn btn-basic legitRipple" data-dismiss="modal">Cancelar</button>
                            <button id="btnFinalizarRetirada" type="button" class="btn bg-slate legitRipple">Finalizar e Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /small modal -->

            <?php include_once("footer.php"); ?>

        </div>
        <!-- /main content -->

        <?php include_once("sidebar-right-resumo-caixa.php"); ?>

    </div>
    <!-- /page content -->

    <?php include_once("alerta.php"); ?>

</body>

</html>