<?php

include_once("sessao.php");

//Fazer alteração posteriormente
$_SESSION['PaginaAtual'] = 'Caixa Fechamento';

include('global_assets/php/conexao.php');

if(isset($_POST['inputDestinoContaFinanceiraId'])) {
    //gravaData($_POST['inputData']);
    $idCaixaAbertura = $_POST['aberturaCaixaId'];
    $dataHoraAtual = date('Y-m-d H:i:s');
    $totalRecebido = str_replace(',', '.', str_replace('.', '', $_POST['inputValorCalculado']));
    $totalPago = 0; //Corrigir depois
    $destinoContaFinanceiraId = str_replace(',', '.', str_replace('.', '', $_POST['inputDestinoContaFinanceiraId']));
    $valorTransferido = str_replace(',', '.', str_replace('.', '', $_POST['inputValorTransferir']));
    $saldoFinal = str_replace(',', '.', str_replace('.', '', $_POST['inputSaldoCaixa']));

    //Mandar para contas a pagar e contas a receber

    try{
        $conn->beginTransaction();

        $sql = "SELECT SituaId
				FROM Situacao
			    WHERE SituaChave = 'FECHADO'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iStatus = $row['SituaId'];	

        $sql = "UPDATE CaixaAbertura SET CxAbeDataHoraFechamento = :sDataHoraFechamento, CxAbeTotalRecebido = :fTotalRecebido, CxAbeContaTransferencia = :iDestinoTransfererencia,
                                         CxAbeValorTransferido = :fValorTransferido, CxAbeSaldoFinal = :fSaldoFinal, CxAbeStatus = :iStatus, CxAbeUnidade = :iUnidade
                WHERE CxAbeId = " . $idCaixaAbertura . "";
        $result = $conn->prepare($sql);			

        $result->execute(array(
            ':sDataHoraFechamento' => $dataHoraAtual,
            ':fTotalRecebido' => $totalRecebido,
            ':iDestinoTransfererencia' => $destinoContaFinanceiraId,
            ':fValorTransferido' => $valorTransferido,
            ':fSaldoFinal' => $saldoFinal,
            ':iStatus' => $iStatus,
            ':iUnidade' => $_SESSION['UnidadeId']
        ));
                
        $conn->commit();

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Fechamento do Caixa Concluído!!!";
        $_SESSION['msg']['tipo'] = "success";

        
    } catch(PDOException $e) {
        
        $conn->rollback();

        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro fechar o caixa!!!";
        $_SESSION['msg']['tipo'] = "error";	
        
        echo 'Error: ' . $e->getMessage();
    }

    irpara('caixaMovimentacao.php');
}

if(isset($_POST['inputAberturaCaixaId']) || isset($_POST['aberturaCaixaId'])) {
    $caixaAberturaId = isset($_POST['inputAberturaCaixaId']) ? $_POST['inputAberturaCaixaId'] : $_POST['aberturaCaixaId'];
    $idCaixa = isset($_POST['inputCaixaId']) ? $_POST['inputCaixaId'] : $_POST['caixaId'];
    //$nomeCaixa = $_POST['inputAberturaCaixaNome'];

    $sql_saldoCaixa    = "SELECT CxAbeSaldoFinal
                          FROM CaixaAbertura 
                          WHERE CxAbeUnidade = " . $_SESSION['UnidadeId'] . " and CxAbeCaixa = ".$idCaixa."
                          ORDER BY CxAbeId DESC";
    $resultSaldoCaixa  = $conn->query($sql_saldoCaixa);
    $rowSaldoCaixa = $resultSaldoCaixa->fetch(PDO::FETCH_ASSOC);

    $saldoCaixa = $rowSaldoCaixa['CxAbeSaldoFinal'];
    
    //Falta colocar o CaixaFechamento
    $sql_totalMovimentacao    = "SELECT SUM(CxRecValorTotal) as TotalFinal
                                FROM CaixaRecebimento 
                                WHERE CxRecUnidade = " . $_SESSION['UnidadeId'] . " and CxRecCaixaAbertura = ".$caixaAberturaId."";
    $resultTotalMovimentacao  = $conn->query($sql_totalMovimentacao);
    $rowTotalMovimentacao = $resultTotalMovimentacao->fetch(PDO::FETCH_ASSOC);

    $valorCalculado = $rowTotalMovimentacao['TotalFinal'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Fechamento do Caixa</title>

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
            /*
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
            */

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

            $("#btnPdv").on('click', () => {
                let urlConsultaAberturaCaixa = "consultaAberturaCaixa.php";
                let idOperador = "<?php echo $_SESSION['UsuarId']; ?>"

                let inputsValuesConsulta = {
                    inputUsuarioId: idOperador
                }; 

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    data: inputsValuesConsulta,
                    success: function(resposta) {
                        $("#inputAberturaCaixaId").val(resposta.CxAbeId);
                        $("#inputAberturaCaixaNome").val(resposta.CaixaNome);

                        document.formCaixaAberturaId.action = "caixaPDV.php";
                        document.formCaixaAberturaId.submit();
                    }
                })
            }) 

            $('#valorTransferir').on("change", function() {
                let valorCalculado = "<?php echo $valorCalculado; ?>";
                let valorTransferir = $(this).val() != '' ? parseFloat($(this).val().replace(".", "").replace(",", ".")) : 0;
                let saldoCaixa = "<?php echo $saldoCaixa != '' ? $saldoCaixa : 0; ?>";

                valorCalculado = parseFloat(valorCalculado);
                saldoCaixa = parseFloat(saldoCaixa);

                if(valorTransferir != 0) {
                    if(valorTransferir < valorCalculado) {
                        valorCalculado -= valorTransferir;
                        saldoCaixa += valorCalculado;
                        saldoCaixa = saldoCaixa;
                        
                        valorCalculado = valorCalculado;
                    }else {
                        $(this).val(float2moeda(valorCalculado));
                        saldoCaixa = saldoCaixa;
                        valorCalculado = 0,00;
                    }
                }

                $('#saldoCaixa').val(float2moeda(saldoCaixa));
            })

            $("#btnFecharCaixa").on('click', () => {
                let idDestino = $("#cmbDestinoContaFinanceira").val();
                let valorCalculado = $("#valorCalculado").val();
                let valorTransferir = $("#valorTransferir").val();
                let saldoCaixa = $("#saldoCaixa").val();

                if(idDestino == '') {
                    $("#cmbDestinoContaFinanceira").focus();
                    
                    var menssagem = 'Informe uma conta destino por favor!';
                    alerta('Atenção', menssagem, 'error');
					return;
                }  

                $("#inputDestinoContaFinanceiraId").val(idDestino);
                $("#inputValorCalculado").val(valorCalculado);
                $("#inputValorTransferir").val(valorTransferir);
                $("#inputSaldoCaixa").val(saldoCaixa);

                document.formFechamentoCaixa.submit();
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
                            <div class="card-header text-center">
                                <h3 class="card-title">Fechamento de Caixa</h3>
                                <br>
                                <h5>Data: <?php echo date('d/m/Y'); ?>

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                Operador: <?php echo nomeSobrenome($_SESSION['UsuarNome'], 1); ?></h5>
                            </div>

                            <div class="card-body">
                                <div class="text-center">
                                    <h3>Dados de Transferência</h3>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="cmbDestinoContaFinanceira">Destino (Conta Financeira) <span class="text-danger">*</span></label>
                                            <select id="cmbDestinoContaFinanceira" name="cmbDestinoContaFinanceira" class="form-control form-control-select2" required>
                                                <option value="">Todos</option>
                                                <?php
                                                $sql = "SELECT CnBanId, CnBanNome
                                                        FROM ContaBanco
                                                        JOIN Situacao on SituaId = CnBanStatus
                                                        WHERE CnBanUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                        ORDER BY CnBanNome ASC";
                                                $result = $conn->query($sql);
                                                $rowContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($rowContaBanco as $item) {
                                                    print('<option value="' . $item['CnBanId'] . '">' . $item['CnBanNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="valorCalculado">Valor Calculado</label>
                                                <input type="text" id="valorCalculado" class="form-control text-right" name="valorCalculado" value="<?php echo mostraValor($valorCalculado); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="valorTransferir">Valor a Transferir <span class="text-danger">*</span></label>
                                                <input type="text" id="valorTransferir" onkeyup="moeda(this)" class="form-control text-right" name="valorTransferir" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-right">
                                            <label for="saldoCaixa">Saldo Caixa</label>
                                            <input type="text" id="saldoCaixa" class="form-control text-right" name="saldoCaixa" value="<?php echo mostraValor($saldoCaixa); ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="text-right">
                                            <div>
                                                <a href="movimentacaoFinanceiraNovo.php" class="btn btn-outline bg-slate text-slate border-slate legitRipple" role="button" title="Nova Movimentação Financeira">Imprimir relatório</a>
                                                <button id="btnFecharCaixa" class="btn btn-principal legitRipple">Fechar Caixa</button>
                                            </div>
                                        </div>
                                    </div>	
                                </div>
                            </div>
                        </div>
                        <!-- /basic responsive configuration -->

                    </div>
                </div>

                
                <form name="formFechamentoCaixa" method="post">   
                    <input type="hidden" id="inputDestinoContaFinanceiraId" name="inputDestinoContaFinanceiraId">
                    <input type="hidden" id="inputValorCalculado" name="inputValorCalculado">
                    <input type="hidden" id="inputValorTransferir" name="inputValorTransferir">
                    <input type="hidden" id="inputSaldoCaixa" name="inputSaldoCaixa">
                    <input type="hidden" id="aberturaCaixaId" name="aberturaCaixaId" value="<?php echo $caixaAberturaId;?>">
                    <input type="hidden" id="caixaId" name="caixaId" value="<?php echo $idCaixa;?>">
                </form>

                <form name="formCaixaAberturaId" method="post">
					<input type="hidden" id="inputAberturaCaixaId" name="inputAberturaCaixaId" value="">
                    <input type="hidden" id="inputAberturaCaixaNome" name="inputAberturaCaixaNome" value="">
				</form>
            </div>
            <!-- /content area -->

            <?php include_once("footer.php"); ?>

        </div>
        <!-- /main content -->

        <?php include_once("sidebar-right-resumo-caixa.php"); ?>

    </div>
    <!-- /page content -->

    <?php include_once("alerta.php"); ?>

</body>

</html>