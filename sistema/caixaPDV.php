<?php

include_once("sessao.php");

//Fazer alteração posteriormente
$_SESSION['PaginaAtual'] = 'Caixa PDV';

include('global_assets/php/conexao.php');

if(isset($_POST['inputAbrirCaixa'])) {
    $dataHorAtual = date('Y-m-d H:i:s');
    $saldoInicial = str_replace(',', '.', $_POST['inputSaldoInicial']);

    $sql = "INSERT INTO CaixaAbertura (CxAbeCaixa, CxAbeDataHoraAbertura, CxAbeOperador, 
                        CxAbeSaldoInicial, CxAbeStatus, CxAbeUnidade) 
            VALUES ( :iCaixa, :sDataHoraAbertura, :iOperador, :bSaldoInicial, :iStatus, :iUnidade)";
    $result = $conn->prepare($sql);

    $result->execute(array(
                    ':iCaixa' => $_POST['cmbCaixa'],
                    ':sDataHoraAbertura' => $dataHorAtual,
                    ':iOperador' => $_SESSION['UsuarId'],
                    ':bSaldoInicial' => $saldoInicial,
                    ':iStatus' => 1,
                    ':iUnidade' => $_SESSION['UnidadeId']
                    )); //Depois se informar a respeito do status

    $nomeCaixa = $_POST['inputCaixaNome'];
}

if(isset($_POST['inputAberturaCaixaId'])) {
    //alerta($_POST['inputAberturaCaixaId']);

    $nomeCaixa = $_POST['inputAberturaCaixaNome'];

    $aberturaCaixaId = $_POST['inputAberturaCaixaId'];
}

if(isset($_POST['inputAtendimentoId'])) {
    $nomeCaixa = $_POST['inputCaixaNome'];

    $aberturaCaixaId = $_POST['inputCaixaId'];
    $dataHora = date("Y-m-d H:i:s");
    $atendimentoId = $_POST['inputAtendimentoId'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['inputValorTotal']));
    $desconto = str_replace(',', '.', str_replace('.', '', $_POST['inputDesconto']));
    $valorFinal = str_replace(',', '.', str_replace('.', '', $_POST['inputValorFinal']));
    $formaPagamentoId = $_POST['inputFormaPagamento'];
    $parcelas = $_POST['inputParcelas'];

    $sql_saldoInicial    = "SELECT CxRecId 
                            FROM CaixaRecebimento
                            WHERE CxRecAtendimento = " . $atendimentoId . "";
    $resultSaldoInicial  = $conn->query($sql_saldoInicial);

    if($rowSaldoInicial = $resultSaldoInicial->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Esse atendimento já foi registrado em outro caixa!!!";
        $_SESSION['msg']['tipo'] = "error";
    }else {
        if($parcelas == 1) {
            $sql = "SELECT SituaId
                    FROM Situacao
                    WHERE SituaChave = 'RECEBIDO'";
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $iStatus = $row['SituaId'];		

            try{
                $sql = "INSERT INTO CaixaRecebimento (CxRecCaixaAbertura, CxRecDataHora, CxRecAtendimento, CxRecValor, CxRecDesconto, CxRecValorTotal, 
                                                    CxRecFormaPagamento, CxRecStatus, CxRecUnidade)
                        VALUES (:iAberturaCaixa, :sDataHora, :iAtendimento, :fValor, :fDesconto, :fValorTotal, :iFormaPagamento, :bStatus, :iUnidade)";
                $result = $conn->prepare($sql);
                        
                $result->execute(array(
                                ':iAberturaCaixa' => $aberturaCaixaId,
                                ':sDataHora' => $dataHora,
                                ':iAtendimento' => $atendimentoId,
                                ':fValor' => $valor,
                                ':fDesconto' => $desconto,
                                ':fValorTotal' => $valorFinal,
                                ':iFormaPagamento' => $formaPagamentoId,
                                ':bStatus' => $iStatus,
                                ':iUnidade' => $_SESSION['UnidadeId'],
                                ));
                        
                $_SESSION['msg']['titulo'] = "Sucesso";
                $_SESSION['msg']['mensagem'] = "Atendimento incluído ao caixa!!!";
                $_SESSION['msg']['tipo'] = "success";
                            
            } catch(PDOException $e) {
                
                $_SESSION['msg']['titulo'] = "Erro";
                $_SESSION['msg']['mensagem'] = "Erro aos cadastrar atendimento no caixa!!!";
                $_SESSION['msg']['tipo'] = "error";	
                
                echo 'Error: ' . $e->getMessage();
            }
        }else {
            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Pagamento parcelado ainda não está disponível!!!";
            $_SESSION['msg']['tipo'] = "error";	
        }
    }
}
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
            $('#tblAtendimento').DataTable( {
                "order": [[ 0, "asc" ]],
                autoWidth: false,
                responsive: true,
                columnDefs: [
                {
                    orderable: true,   //Marca
                    width: "40%",
                    targets: [0]
                },
                { 
                    orderable: true,   //Situação
                    width: "40%",
                    targets: [1]
                },
                { 
                    orderable: true,   //Ações
                    width: "20%",
                    targets: [2]
                }],
                dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
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

            //Apenas para deixar em azul o botão de PDV do resumo financeiro, já que é a página atual
            $('#btnPdv').removeClass()
            $('#btnPdv').addClass('btn bg-slate legitRipple');
            
            /* Fim: Tabela Personalizada */

            $('#cmbAtendimento').on("change", function() {
                let urlConsultaAberturaCaixa = "consultaCaixaServicos.php";
                let idAtendimento = ($(this).val() != '') ? $(this).val() : 0;

                let inputsValuesConsulta = {
                    inputAtendimentoId: idAtendimento
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
                        table = $('#tblAtendimento').DataTable()
                        table = $('#tblAtendimento').DataTable().clear().draw()
                        //--|

                        table = $('#tblAtendimento').DataTable()

                        let rowNode

                        let valorTotal = null;
                        let descontoTotal = null;

                        resposta.forEach(item => {
                            valor = item.data[2].replace(".", "").replace(",", ".");
                            valorTotal += parseFloat(valor);

                            desconto = item.data[3].replace(".", "").replace(",", ".");
                            descontoTotal += parseFloat(desconto);
                            
                            rowNode = table.row.add(item.data).draw().node();

                            $(rowNode).find('td').eq(2).attr('style', 'text-align: right;');
                        })

                        valorFinal = valorTotal - descontoTotal;
                        valorFinal = (valorFinal != null && valorFinal > 0) ?  float2moeda(valorFinal) : null;
                        valorTotal = (valorTotal != null) ?  float2moeda(valorTotal) : null;
                        descontoTotal = (descontoTotal != null) ?  float2moeda(descontoTotal) : null;
                        
                        $("#valorTotal").val(valorTotal);
                        $("#desconto").val(descontoTotal);
                        $("#valorFinal").val(valorFinal);
                    }
                })
            })

            //Mostra o valor de cada parcela no pop up - Finalização do Recebimento
            function calculaValorParcela(quantidade) {
                let valorFinal = $("#valorFinal").val().replace(".", "").replace(",", ".");
                valorFinal = parseFloat(valorFinal) / quantidade;
                
                $("#valorPorParcela").val(float2moeda(valorFinal));
            }

            $('#cmbFormaPagamento').on("change", function() {
                let arrayFormaPagamentoId = $(this).val().split("-");;
                
                if(arrayFormaPagamentoId[1] == 'CHEQUE') {
                    $("#detalhamentoCheque").trigger("click");
                }
            })
            
            $('#cmbParcela').on("change", function() {
                let quantidadeParcelas =  $("#cmbParcela").val() != '' ? parseInt($("#cmbParcela").val()) : 1;

                calculaValorParcela(quantidadeParcelas);
            })

            $("#btnImprimir").on('click', () => {
                alert('imprimir')
            })
            
            $("#btnFinalizar").on('click', () => {
                let idAtendimento = $("#cmbAtendimento").val();

                if(idAtendimento != '') {
                    let parcelas = $("#cmbParcela").val() != '' ? parseInt($("#cmbParcela").val()) : 1;

                    calculaValorParcela(parcelas);

                    $("#recebimento").trigger("click");
                }else {
                    $("#cmbAtendimento").focus();
                    
                    var menssagem = 'Informe um atendimento por favor!'
                    alerta('Atenção', menssagem, 'error')
					return
                }
            })

            $("#btnFinalizarRecebimento").on('click', () => {
                
                let idAtendimento = $("#cmbAtendimento").val();
                let valorTotal = $("#valorTotal").val();
                let desconto = $("#desconto").val();
                let valorFinal = $("#valorFinal").val();
                let formaPagamento = $("#cmbFormaPagamento").val().split('-');
                let parcelamento = $("#cmbParcela").val() != '' ? $("#cmbParcela").val() : 1;

                $("#inputAtendimentoId").val(idAtendimento);
                $("#inputValorTotal").val(valorTotal);
                $("#inputDesconto").val(desconto);
                $("#inputValorFinal").val(valorFinal);
                $("#inputFormaPagamento").val(formaPagamento[0]);
                $("#inputParcelas").val(parcelamento);

                if(formaPagamento != '') {
                    document.formAtendimento.submit();
                }else {
                    $("#cmbFormaPagamento").focus();
                        
                    var menssagem = 'Informe uma forma de pagamento por favor!'
                    alerta('Atenção', menssagem, 'error')
                    return
                }

            })
        });
    </script>

</head>

<body class="navbar-top sidebar-right-visible sidebar-xs">

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
                            <div class="card-header header-elements">
                                <div class="row">
                                    <div class="col-4">
                                        <h4 class="m-auto">PDV: 005 - Mudar dps</h4>
                                    </div>

                                    <div class="col-4">
                                        <h4 class="m-auto">Caixa: <?php echo $nomeCaixa; ?></h4>
                                    </div>

                                    <div class="col-4 text-right">
                                        <h4 class="m-auto">Data: <?php echo date('d/m/Y'); ?></h4>
                                    </div>
                                </div>

                                <hr>

                                <h3 class="card-title">PDV - <?php echo  $_SESSION['UnidadeNome']; ?></h3>
                            </div>

                            <div class="card-body">
                                <?php
                                if (isset($lancamento)) {
                                    echo '<input type="hidden" name="inputEditar" value="sim">';
                                    echo '<input type="hidden" name="inputContaId" value="' . $lancamento['CnAPaId'] . '">';
                                }

                                ?>
                            
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="cmbAtendimento">Atendimento <span class="text-danger">*</span></label>
                                            <select id="cmbAtendimento" name="cmbAtendimento" class="form-control form-control-select2" required>
                                                <option value="">Todos</option>
                                                <?php
                                                $sql = "SELECT AtendId, ClienNome
                                                        FROM Atendimento
                                                        JOIN Cliente on ClienId = AtendCliente
                                                        JOIN Situacao on SituaId = AtendSituacao
                                                        LEFT JOIN CaixaRecebimento on CxRecAtendimento = AtendId
                                                        WHERE AtendUnidade = ".$_SESSION['UnidadeId']." and AtendId not in (SELECT CxRecAtendimento FROM CaixaRecebimento)
                                                        ORDER BY ClienNome";
                                                $result = $conn->query($sql);
                                                $rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowFornecedor as $item) {
                                                    print('<option value="' . $item['AtendId'] . '">' . $item['ClienNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="valorTotal">Valor Total</label>
                                                <input type="text" id="valorTotal" class="form-control text-right" name="valorTotal" value="" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-right">
                                            <label for="desconto">Desconto</label>
                                            <input type="text" id="desconto" class="form-control text-right" name="desconto" value="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="valorFinal">Valor Final</label>
                                                <input type="text" id="valorFinal" class="form-control text-right" name="valorFinal" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <table id="tblAtendimento" class="table">
                                            <thead>
                                                <tr class="bg-slate">
                                                    <th>Procedimento</th>
                                                    <th>Médico</th>
                                                    <th class="text-center">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="text-right">
                                            <div>
                                                <a href="caixaMovimentacao.php" class="btn legitRipple">Fechar</a>
                                                <button id="btnFinalizar" class="btn btn-principal legitRipple">Finalizar</button>
                                            </div>
                                        </div>
                                    </div>	
                                </div>
                            </div>
                        </div>
                        <!-- /basic responsive configuration -->

                    </div>
                </div>

                
                <form name="formAtendimento" method="post" action="caixaPDV.php">   
                    <input type="hidden" id="inputCaixaNome" name="inputCaixaNome" value="<?php echo $nomeCaixa; ?>">
                    <input type="hidden" id="inputCaixaId" name="inputCaixaId" value="<?php echo $aberturaCaixaId; ?>">
                    <input type="hidden" id="inputAtendimentoId" name="inputAtendimentoId">
                    <input type="hidden" id="inputValorTotal" name="inputValorTotal">
                    <input type="hidden" id="inputDesconto" name="inputDesconto">
                    <input type="hidden" id="inputValorFinal" name="inputValorFinal">
                    <input type="hidden" id="inputFormaPagamento" name="inputFormaPagamento">
                    <input type="hidden" id="inputParcelas" name="inputParcelas">
                </form>
            </div>
            <!-- /content area -->

            <!--Link para finalizar recebimento-->
            <a id="recebimento" data-toggle="modal" data-target="#modal_small_Recebimento"></a>
            <a id="detalhamentoCheque" data-toggle="modal" data-target="#modal_small_detalhamento_cheque"></a>

            <!-- Small modal -->
            <!--Procurar uma correção com relação ao filtro do select-->
            <div id="modal_small_Recebimento" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Finalização do recebimento</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group mt-3">
                                <label for="cmbFormaPagamento">Forma de Pagamento <span class="text-danger">*</span></label>
                                <select id="cmbFormaPagamento" name="cmbFormaPagamento" class="form-control form-control-select2 select2-hidden-accessible" required="" tabindex="-1" aria-hidden="true">
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

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group mt-2">
                                        <label for="cmbParcela">Parcelas</label>
                                        <select id="cmbParcela" name="cmbParcela" class="form-control form-control-select2 select2-hidden-accessible" required="" tabindex="-1" aria-hidden="true">
                                            <option value="">Selecionar</option>
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

                                <div class="col-lg-8">
                                    <div class="form-group mt-2">
                                        <label for="valorPorParcela">Valor por parcela</label>
                                        <input type="text" id="valorPorParcela" name="valorPorParcela" class="form-control removeValidacao" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="btnImprimir" type="button" class="btn btn-basic legitRipple">Finalizar e Imprimir</button>
                            <button id="btnFinalizarRecebimento" type="button" class="btn bg-slate legitRipple">Finalizar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modal_small_detalhamento_cheque" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Abertura de Caixa</h5>
                        </div>

                        <form id="formAbrirCaixa" method="POST" action="caixaPdv.php">
                            <input type="hidden" id="inputCaixaNome" name="inputCaixaNome" value="">

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        Data: <?php echo date('d/m/Y'); ?>
                                    </div>
    
                                    <div class="col-lg-6">
                                        Operador: <?php echo nomeSobrenome($_SESSION['UsuarNome'], 1); ?>
                                    </div>
                                </div>
    
                                <div class="form-group mt-3">
                                    <!--Input para controle, para que caso acesse o PDV pela abertura de caixa ele fará o cadastro da nova abertura de caixa-->
                                    <input type="hidden" id="inputAbrirCaixa" name="inputAbrirCaixa" value="" class="form-control removeValidacao">

                                    <label for="cmbCaixa">Caixa <span class="text-danger">*</span></label>
                                    <select id="cmbCaixa" name="cmbCaixa" class="form-control form-control-select2 select2-hidden-accessible" required="" tabindex="-1" aria-hidden="true">
                                        <option value="">Selecionar</option>
                                        <?php
                                        $sql = "SELECT CaixaId, CaixaNome, SituaNome
                                                FROM Caixa
                                                JOIN Situacao on SituaId = CaixaStatus
                                                WHERE CaixaUnidade = " . $_SESSION['UnidadeId'] . "
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
                                    <label for="inputSaldoInicial">Saldo Inicial</label>
                                    <input type="text" id="inputSaldoInicial" name="inputSaldoInicial" value="" class="form-control removeValidacao" readonly>
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