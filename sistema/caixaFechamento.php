<?php

include_once("sessao.php");

//Fazer alteração posteriormente
$_SESSION['PaginaAtual'] = 'Caixa Fechamento';

include('global_assets/php/conexao.php');

if(isset($_POST['inputDestinoContaFinanceiraId'])) { 
    //gravaData($_POST['inputData']);
    $idCaixaAbertura = $_POST['aberturaCaixaId'];
    $dataAtual = date('Y-m-d');
    $dataHoraAtual = date('Y-m-d H:i:s');
    $destinoContaFinanceiraId = $_POST['inputDestinoContaFinanceiraId'];
    $totalRecebido = $_POST['inputTotalRecebido'];
    $totalPago = $_POST['inputTotalPago'];
    $valorTransferido = $_POST['inputValorTransferir'] != '' ? gravaValor($_POST['inputValorTransferir']) : 0;
    //$saldoFinal = gravaValor($_POST['inputSaldoCaixa']);
    $saldoFinal = 0; //No momento está deixando saldo final do caixa zerado porque está transferindo todo o valor
    $justificativa = $_POST['inputJustificativa'] != '' ? $_POST['inputJustificativa'] : null;
    $nomeCaixa = $_POST['caixaNome'];
    
    try{
        $conn->beginTransaction();
        
        $sql = "SELECT SituaId
				FROM Situacao
			    WHERE SituaChave = 'FECHADO'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iStatus = $row['SituaId'];	
        
        $sql = "UPDATE CaixaAbertura SET CxAbeDataHoraFechamento = :sDataHoraFechamento, CxAbeContaTransferencia = :iDestinoTransfererencia, 
                                         CxAbeValorTransferido = :fValorTransferido, CxAbeSaldoFinal = :fSaldoFinal, CxAbeJustificativa = :sJustificativa, 
                                         CxAbeStatus = :iStatus, CxAbeUnidade = :iUnidade
                WHERE CxAbeId = " . $idCaixaAbertura . "";
        $result = $conn->prepare($sql);			
        
        $result->execute(array(
            ':sDataHoraFechamento' => $dataHoraAtual,
            ':iDestinoTransfererencia' => $destinoContaFinanceiraId,
            ':fValorTransferido' => $valorTransferido,
            ':fSaldoFinal' => $saldoFinal,
            ':sJustificativa' => $justificativa,
            ':iStatus' => $iStatus,
            ':iUnidade' => $_SESSION['UnidadeId']
        ));
        
        $sql_movimentacaoRecebimento = "SELECT AtendNumRegistro, AtendCliente, ClienNome, CxRecFormaPagamento, CxRecValor, CxRecValorTotal, CxRecDataHora, CxRecDesconto
                                        FROM CaixaRecebimento
                                        JOIN CaixaAbertura on CxAbeId = CxRecCaixaAbertura
                                        JOIN FormaPagamento on FrPagId = CxRecFormaPagamento
                                        JOIN Atendimento on AtendId = CxRecAtendimento
                                        JOIN Cliente on ClienId = AtendCliente
                                        WHERE CxAbeOperador = $_SESSION[UsuarId] and CxRecUnidade = $_SESSION[UnidadeId] and CxRecCaixaAbertura = $idCaixaAbertura";
        $resultMovimentacaoRecebimento  = $conn->query($sql_movimentacaoRecebimento);

        if($rowMovimentacaoRecebimento = $resultMovimentacaoRecebimento->fetchAll(PDO::FETCH_ASSOC)) {
            $descricaoAgrupamentoRecebimento = 'Recebimento do '.$nomeCaixa.' - '.date('d/m/Y H:i');

            //Corrigir valor do agrupamento do caixa
            $sql = "INSERT INTO ContasAgrupadas ( CnAgrDtPagamento, CnAgrValorTotal, CnAgrFormaPagamento, CnAgrContaBanco, CnAgrDescricaoAgrupamento)
                    VALUES ( :dateDtPagamento, :fValorTotal, :iFormaPagamento, :iContaBanco, :sDescricaoAgrupamento)";
            $result = $conn->prepare($sql);
            
            $result->execute(array(
                ':dateDtPagamento' => $dataAtual,
                ':fValorTotal' => $totalRecebido,
                ':iFormaPagamento' => null,
                ':iContaBanco' => null,
                ':sDescricaoAgrupamento' => $descricaoAgrupamentoRecebimento
            ));

            $agrupamentoRecebimento = $conn->lastInsertId();
            
            $sql = "SELECT SituaId
                    FROM Situacao
                    WHERE SituaChave = 'RECEBIDO'";
            $result = $conn->query($sql);
            $rowAReceber = $result->fetch(PDO::FETCH_ASSOC);
            $iStatusRecebido = $rowAReceber['SituaId'];		

            foreach($rowMovimentacaoRecebimento as $item) {
                $arrayDataHora = explode(' ', $item['CxRecDataHora']);
                $dataRecebimento = $arrayDataHora[0];

                $descricaoContaRecebimento = $item['AtendNumRegistro'].' - '.$item['ClienNome'];
                $cliente = $item['AtendCliente'];
                $formaPagamento = $item['CxRecFormaPagamento'];
                $valorAReceber = $item['CxRecValor'];
                $valorRecebido = $item['CxRecValorTotal'];
                $desconto = $item['CxRecDesconto'];

                //Falta o plano de contas e centro de custos
                
                $sql = "INSERT INTO ContasAReceber ( CnAReDtEmissao, CnARePlanoContas, CnAReCliente, CnAReDescricao, CnAReNumDocumento,  CnAReContaBanco, 
                                                    CnAReFormaPagamento, CnAReVenda, CnAReDtVencimento, CnAReValorAReceber, CnAReDtRecebimento, CnAReValorRecebido, 
                                                    CnAReTipoJuros, CnAReJuros, CnAReTipoDesconto, CnAReDesconto, CnAReObservacao, CnAReNumCheque, CnAReValorCheque,                  
                                                    CnAReDtEmissaoCheque, CnAReDtVencimentoCheque, CnAReBancoCheque, CnAReAgenciaCheque, CnAReContaCheque,                  
                                                    CnAReNomeCheque, CnAReCpfCheque, CnAReAgrupamento, CnAReStatus, CnAReUsuarioAtualizador, CnAReUnidade)
                           VALUES ( :dDtEmissao, :iPlanoContas, :iCliente, :sDescricao, :sNumDocumento, :iContaBanco, 
                                    :iFormaPagamento, :iVenda, :dDtVencimento, :fValorAReceber, :dDtRecebimento, :fValorRecebido,
                                    :sTipoJuros, :fJuros, :sTipoDesconto, :fDesconto, :sObservacao, :sNumCheque, :fValorCheque, 
                                    :dDtEmissaoCheque, :dDtVencimentoCheque, :iBancoCheque, :iAgenciaCheque, :iContaCheque,
                                    :iNomeCheque, :iCpfCheque, :iAgrupamento, :iStatus, :iUsuarioAtualizador, :iUnidade)";

                $result = $conn->prepare($sql);

                $result->execute(array(

                    ':dDtEmissao'           => $dataRecebimento,
                    ':iPlanoContas'         => null,
                    ':iCliente'             => $cliente,
                    ':sDescricao'           => $descricaoContaRecebimento,
                    ':sNumDocumento'        => null,
                    ':iContaBanco'          => $destinoContaFinanceiraId,
                    ':iFormaPagamento'      => $formaPagamento,
                    ':iVenda'               => null,
                    ':dDtVencimento'        => $dataRecebimento,
                    ':fValorAReceber'       => $valorAReceber,
                    ':dDtRecebimento'       => $dataRecebimento,
                    ':fValorRecebido'       => $valorRecebido,
                    ':sTipoJuros'           => null,
                    ':fJuros'               => null,
                    ':sTipoDesconto'        => null,
                    ':fDesconto'            => $desconto,
                    ':sObservacao'          => null,
                    ':sNumCheque'           => isset($_POST['inputNumCheque']) ? $_POST['inputNumCheque'] : null,
                    ':fValorCheque'         => isset($_POST['inputValorCheque']) ? floatval(gravaValor($_POST['inputValorCheque'])) : null,
                    ':dDtEmissaoCheque'     => isset($_POST['inputDtEmissaoCheque']) ? $_POST['inputDtEmissaoCheque'] : null,
                    ':dDtVencimentoCheque'  => isset($_POST['inputDtVencimentoCheque']) ? $_POST['inputDtVencimentoCheque'] : null,
                    ':iBancoCheque'         => isset($_POST['cmbBancoCheque']) ? intval($_POST['cmbBancoCheque']) : null,
                    ':iAgenciaCheque'       => isset($_POST['inputAgenciaCheque']) ? $_POST['inputAgenciaCheque'] : null,
                    ':iContaCheque'         => isset($_POST['inputContaCheque']) ? $_POST['inputContaCheque'] : null,
                    ':iNomeCheque'          => isset($_POST['inputNomeCheque']) ? $_POST['inputNomeCheque'] : null,
                    ':iCpfCheque'           => isset($_POST['inputCpfCheque']) ? $_POST['inputCpfCheque'] : null,   
                    ':iAgrupamento'         => $agrupamentoRecebimento, 
                    ':iStatus'              => $iStatusRecebido,
                    ':iUsuarioAtualizador'  => intval($_SESSION['UsuarId']),
                    ':iUnidade'             => intval($_SESSION['UnidadeId'])
                ));
                
                //$idContaAReceber = $conn->lastInsertId();
            }   
        }

        $sql_movimentacaoPagamento = "SELECT CxPagFormaPagamento, CxPagValor, CxPagDataHora, CxPagPlanoConta, CxPagJustificativaRetirada, CxPagFornecedor, CxPagCentroCusto
                                        FROM CaixaPagamento
                                        JOIN CaixaAbertura on CxAbeId = CxPagCaixaAbertura
                                        JOIN FormaPagamento on FrPagId = CxPagFormaPagamento
                                        WHERE CxAbeOperador = $_SESSION[UsuarId] and CxPagUnidade = $_SESSION[UnidadeId] and CxPagCaixaAbertura = $idCaixaAbertura";
        $resultMovimentacaoPagamento  = $conn->query($sql_movimentacaoPagamento);
        
        if($rowMovimentacaoPagamento = $resultMovimentacaoPagamento->fetchAll(PDO::FETCH_ASSOC)) {
            $descricaoPagamento = 'Pagamento do '.$nomeCaixa.' - '.date('d/m/Y H:i');
            
            $sql = "INSERT INTO ContasAgrupadas ( CnAgrDtPagamento, CnAgrValorTotal, CnAgrFormaPagamento, CnAgrContaBanco, CnAgrDescricaoAgrupamento)
                    VALUES ( :dateDtPagamento, :fValorTotal, :iFormaPagamento, :iContaBanco, :sDescricaoAgrupamento)";
            $result = $conn->prepare($sql);
                    
            $result->execute(array(
                ':dateDtPagamento' => $dataAtual,
                ':fValorTotal' => $totalPago,
                ':iFormaPagamento' => null,
                ':iContaBanco' => null,
                ':sDescricaoAgrupamento' => $descricaoPagamento
            ));

            $agrupamentoPagamento = $conn->lastInsertId();

            $sql = "SELECT SituaId
            FROM Situacao
            WHERE SituaChave = 'PAGO'";
            $result = $conn->query($sql);
            $rowAPagar = $result->fetch(PDO::FETCH_ASSOC);
            $iStatusPago = $rowAPagar['SituaId'];

            foreach($rowMovimentacaoPagamento as $item) {
                $arrayDataHora = explode(' ', $item['CxPagDataHora']);
                $dataPagamento = $arrayDataHora[0];

                $planoConta = $item['CxPagPlanoConta'];
                $centroCusto = $item['CxPagCentroCusto'];
                $planoConta = $item['CxPagPlanoConta'];
                $fornecedor = $item['CxPagFornecedor'] != '' ? $item['CxPagFornecedor'] : 0; 
                $formaPagamento =  $item['CxPagFormaPagamento'];
                $descricaoContaPagamento = $item['CxPagJustificativaRetirada'];
                $valorPago = $item['CxPagValor'];

                $sql = "INSERT INTO ContasAPagar ( CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento,
                                                CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                                CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaTipoJuros, CnAPaJuros, 
                                                CnAPaTipoDesconto, CnAPaDesconto, CnAPaAgrupamento, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                        VALUES ( :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                                :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :sTipoJuros, :fJuros, 
                                :sTipoDesconto, :fDesconto, :iAgrupamento, :iStatus, :iUsuarioAtualizador, :iUnidade)";
                $result = $conn->prepare($sql);

                $result->execute(array(
                    ':iPlanoContas'         => $planoConta,
                    ':iFornecedor'          => $fornecedor,
                    ':iContaBanco'          => $destinoContaFinanceiraId,
                    ':iFormaPagamento'      => $formaPagamento,
                    ':sNotaFiscal'          => null,
                    ':dateDtEmissao'        => $dataPagamento,
                    ':iOrdemCompra'         => null,
                    ':sDescricao'           => $descricaoContaPagamento,
                    ':dateDtVencimento'     => $dataPagamento,
                    ':fValorAPagar'         => $valorPago,
                    ':dateDtPagamento'      => $dataPagamento,
                    ':fValorPago'           => $valorPago,
                    ':sObservacao'          => null,
                    ':sTipoJuros'           => null,
                    ':fJuros'               => null,
                    ':sTipoDesconto'        => null,
                    ':fDesconto'            => null,   
                    ':iAgrupamento'         => $agrupamentoPagamento, 
                    ':iStatus'              => $iStatusPago,
                    ':iUsuarioAtualizador'  => $_SESSION['UsuarId'],
                    ':iUnidade'             => $_SESSION['UnidadeId']
                ));

                if($centroCusto != '') {
                    $idContaAPagar = $conn->lastInsertId();

                    $sql = "INSERT INTO ContasAPagarXCentroCusto ( CAPXCContasAPagar, CAPXCCentroCusto, CAPXCValor, CAPXCUsuarioAtualizador, CAPXCUnidade)
                            VALUES ( :iContasAPagar, :iCentroCusto, :iValor, :iUsuarioAtualizador, :iUnidade)";
                    $result = $conn->prepare($sql);

                    $result->execute(array(
                        ':iContasAPagar' => $idContaAPagar,
                        ':iCentroCusto' => $centroCusto,
                        ':iValor' => $valorPago,
                        ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                        ':iUnidade' => $_SESSION['UnidadeId']
                    ));
                }
            }
        }
        
        $conn->commit();

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Fechamento do Caixa Concluído!!!";
        $_SESSION['msg']['tipo'] = "success";

        
    } catch(PDOException $e) {
        
        $conn->rollback();

        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro ao fechar o caixa!!!";
        $_SESSION['msg']['tipo'] = "error";	
        
        echo 'Error: ' . $e->getMessage();
    }
    
    irpara('caixaMovimentacao.php');
}

$situaCaixa = '';

if(isset($_POST['inputAberturaCaixaId']) || isset($_POST['aberturaCaixaId'])) {
    if(isset($_POST['inputSituacaoCaixa']) && $_POST['inputSituacaoCaixa'] != '') {
        $situaCaixa = $_POST['inputSituacaoCaixa'];
    }

    $caixaAberturaId = isset($_POST['inputAberturaCaixaId']) ? $_POST['inputAberturaCaixaId'] : $_POST['aberturaCaixaId'];
    $idCaixa = isset($_POST['inputCaixaId']) ? $_POST['inputCaixaId'] : $_POST['caixaId'];
    $nomeCaixa = $_POST['inputAberturaCaixaNome'];

    $sql_saldoCaixa    = "SELECT CxAbeSaldoInicial
                          FROM CaixaAbertura 
                          WHERE CxAbeUnidade = " . $_SESSION['UnidadeId'] . " and CxAbeCaixa = ".$idCaixa."
                          ORDER BY CxAbeId DESC";
    $resultSaldoCaixa  = $conn->query($sql_saldoCaixa);

    $saldoInicialCaixa = 0;
    if($rowSaldoCaixa = $resultSaldoCaixa->fetch(PDO::FETCH_ASSOC)) {
        $saldoInicialCaixa = $rowSaldoCaixa['CxAbeSaldoInicial'];
    }
    
    $sql_totalMovimentacao    = "SELECT SUM(CxRecValorTotal) as TotalRecebido
                                FROM CaixaRecebimento 
                                WHERE CxRecUnidade = " . $_SESSION['UnidadeId'] . " and CxRecCaixaAbertura = ".$caixaAberturaId."";
    $resultTotalMovimentacao  = $conn->query($sql_totalMovimentacao);
    $rowTotalRecebido = $resultTotalMovimentacao->fetch(PDO::FETCH_ASSOC);

    $sql_totalMovimentacao    = "SELECT SUM(CxPagValor) as TotalPago
                                FROM CaixaPagamento
                                WHERE CxPagUnidade = " . $_SESSION['UnidadeId'] . " and CxPagCaixaAbertura = ".$caixaAberturaId."";
    $resultTotalMovimentacao  = $conn->query($sql_totalMovimentacao);
    $rowTotalPago = $resultTotalMovimentacao->fetch(PDO::FETCH_ASSOC);

    $valorRecebido = $rowTotalRecebido['TotalRecebido'];
    $valorPago = $rowTotalPago['TotalPago'];

    $valorCalculado = $valorRecebido - $valorPago;

    $valorATransferir = $valorCalculado + $saldoInicialCaixa;
    $saldoFinalCaixa = $valorCalculado + $saldoInicialCaixa; 

    $sql_movimentacao    = "SELECT AtendNumRegistro, ClienNome as HISTORICO, CxRecDataHora as DATAHORA, CxRecAtendimento, FrPagId, FrPagNome, 
                                CxRecValor, CxRecValorTotal as TOTAL, SituaNome, SituaChave, 'Recebimento' as Tipo
                        FROM CaixaRecebimento
                        JOIN CaixaAbertura on CxAbeId = CxRecCaixaAbertura
                        JOIN FormaPagamento on FrPagId = CxRecFormaPagamento
                        JOIN Atendimento on AtendId = CxRecAtendimento
                        JOIN Cliente on ClienId = AtendCliente
                        JOIN Situacao on SituaId = CxRecStatus
                        WHERE CxAbeOperador = $_SESSION[UsuarId] and CxRecCaixaAbertura = $_POST[inputAberturaCaixaId] and CxRecUnidade = $_SESSION[UnidadeId]
                        UNION 
                        SELECT '' as NUM_REGISTRO, CxPagJustificativaRetirada as HISTORICO, CxPagDataHora as DATAHORA, 0 as ATENDIMENTO, FrPagId, FrPagNome,
                                0 as Valor, CxPagValor as TOTAL, SituaNome, SituaChave, 'Pagamento' as Tipo
                        FROM CaixaPagamento
                        JOIN CaixaAbertura on CxAbeId = CxPagCaixaAbertura
                        JOIN FormaPagamento on FrPagId = CxPagFormaPagamento
                        JOIN Situacao on SituaId = CxPagStatus
                        WHERE CxAbeOperador = $_SESSION[UsuarId] and CxPagCaixaAbertura = $_POST[inputAberturaCaixaId] and CxPagUnidade = $_SESSION[UnidadeId]
                        ORDER BY FrPagNome,  HISTORICO ASC";
    $resultMovimentacao  = $conn->query($sql_movimentacao);
    $rowMovimentacao = $resultMovimentacao->fetchAll(PDO::FETCH_ASSOC);
}else {
    irpara("caixaMovimentacao.php");
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
            $('#tblFechamentoCaixa').DataTable( {
                "order": [
                    [ 5, "asc" ], //Coluna de controle, para deixar a lista na ordem correta, já que o dataTable organiza a tabela automaticamente
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [
                {
                    orderable: false,   //Forma Recebimento
                    width: "38%",
                    targets: [0]
                },
                { 
                    orderable: false,   //Valor Recebido
                    width: "15%",
                    targets: [1]
                },
                { 
                    orderable: false,   //Valor Retirado
                    width: "15%",
                    targets: [2]
                },
                { 
                    orderable: false,   //Valor na gaveta
                    width: "15%",
                    targets: [3]
                },
                { 
                    orderable: false,   //Falta ou Sobra
                    width: "15%",
                    targets: [4]
                },
                { 
                    visible: false, //Deixa a coluna controle invisível
                    orderable: false,   //Controle / contador
                    width: "2%",
                    targets: [5]
                }],
                dom: ''
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

            function inputsValorGaveta() {
                var contadorLinha = $("#tblFechamentoCaixa tr").length;
                
                for (let i = 1; i <= contadorLinha; i++) {

                    $(`#valorGaveta${i}`).on("change", function() {
                        let valorRecebido = parseFloat($(`#valorRecebido${i}`).text().replaceAll(".", "").replace(",", "."));
                        let valorRetirado = parseFloat($(`#valorRetirado${i}`).text().replaceAll(".", "").replace(",", "."));
                        let valor = valorRecebido + valorRetirado; //Está sendo somado pq o valor retirado ele já é negativo

                        let valorGaveta = $(this).val().replaceAll(".", "").replace(",", ".");
                        let faltaSobra = valor - valorGaveta;

                        $(`#valorFaltaSobra${i}`).html(float2moeda(faltaSobra * -1)); //É para o valor do falta ou sobra vir correto

                        consultaTotalDadosFechamento(contadorLinha);
                    })
                }
            }

            inputsValorGaveta();

            function consultaTotalDadosFechamento(quantidadeLinha) {
                let valorGaveta = 0;
                let faltaSobra = 0;
                let totalGaveta = 0;
                let totalFaltaSobra = 0;

                for (let i = 1; i <= quantidadeLinha; i++) {
                    valorGaveta = $(`#valorGaveta${i}`).val() != undefined && $(`#valorGaveta${i}`).val() != '' ? parseFloat($(`#valorGaveta${i}`).val()) : 0;

                    if($(`#valorFaltaSobra${i}`).text() != undefined && $(`#valorFaltaSobra${i}`).text() != '') {
                        if($(`#valorFaltaSobra${i}`).text().indexOf('-') > -1) { //retorna a posição da primeira ocorrência do valor especificado, caso contrário retorna -1
                            faltaSobra = $(`#valorFaltaSobra${i}`).text() != undefined && $(`#valorFaltaSobra${i}`).text() != '' ? parseFloat($(`#valorFaltaSobra${i}`).text().replaceAll(".", "").replace(",", ".").replace("-", "")) * -1 : 0;
                        }else {
                            faltaSobra = $(`#valorFaltaSobra${i}`).text() != undefined && $(`#valorFaltaSobra${i}`).text() != '' ? parseFloat($(`#valorFaltaSobra${i}`).text().replaceAll(".", "").replace(",", ".")): 0;
                        }
                        
                        totalFaltaSobra += faltaSobra; 
                    }

                    totalGaveta += valorGaveta;  
                } 

                $("#totalGaveta").text(float2moeda(totalGaveta));
                $("#totalFaltaSobra").text(float2moeda(totalFaltaSobra)); 
            }

            function consultaSaldoCaixaAtual() {
                let urlConsultaAberturaCaixa = "consultaCaixaSaldoAtual.php";
                
                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    success: function(resposta) {
                        if(resposta != 'consultaVazia') {
                            let saldoInicial = parseFloat(resposta[0].CxAbeSaldoInicial);

                            let valorRecebido = parseFloat(resposta[1].SaldoRecebido);
                            let valorPago = parseFloat(resposta[2].SaldoPago);
        
                            let saldo = saldoInicial + valorRecebido - valorPago;
                            
                            $("#inputResumoCaixaSaldoInicial").val(float2moeda(saldoInicial));
                            $("#inputResumoCaixaRecebido").val(float2moeda(valorRecebido));
                            $("#inputResumoCaixaPago").val(float2moeda(valorPago * -1));
        
                            $("#inputResumoCaixaSaldo").val(float2moeda(saldo));
                        }else {
                            $("#inputResumoCaixaSaldoInicial").val('');
                            $("#inputResumoCaixaRecebido").val('');
                            $("#inputResumoCaixaPago").val('');
                            $("#inputResumoCaixaSaldo").val('');
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

            function pendenciaValorGavetaFaltaSobra() {
                var contadorLinha = $("#tblFechamentoCaixa tr").length;
                
                //Não permitir fechar o caixa caso todos os inputs não tenham sido preenchidos
                for (let i = 1; i <= contadorLinha; i++) {    
                    let gaveta = $(`#valorGaveta${i}`).val();
                    if(gaveta == '') {
                        $(`#valorGaveta${i}`).focus();
                        var menssagem = 'Informe o valor desta gaveta por favor!';
                        alerta('Atenção', menssagem, 'error');
                        return true;
                    }
                } 

                //Deverá ser dada uma justificativa caso tenha alguma falta ou sobra nas gavetas
                for (let i = 1; i <= contadorLinha; i++) {
                    let faltaSobra = $(`#valorFaltaSobra${i}`).text() != undefined && $(`#valorFaltaSobra${i}`).text() != '' ? parseFloat($(`#valorFaltaSobra${i}`).text().replaceAll(".", "").replace(",", ".").replace("-", "")): 0;
                    if(faltaSobra != 0) {
                        $('#abrirJustificativa').trigger("click");
                        return true;
                    }
                } 

                return false;
            }

            $("#btnJustificar").on('click', () => {
                let justificativa = $('#justificativa').val();
                
                $('#inputJustificativa').val(justificativa);
                document.formFechamentoCaixa.submit();
            })

            $("#btnFecharCaixa").on('click', () => {
                let idDestino = $("#cmbDestinoContaFinanceira").val();
                let totalRecebido = '<?php echo $valorRecebido; ?>';
                let totalPago = '<?php echo $valorPago; ?>';
                let valorCalculado = $("#valorCalculado").val();
                let valorTransferir = $("#valorTransferir").val();
                let saldoFinalCaixa = $("#saldoFinalCaixa").val();

                if(idDestino == '') {
                    $("#cmbDestinoContaFinanceira").focus();
                    
                    var menssagem = 'Informe uma conta destino por favor!';
                    alerta('Atenção', menssagem, 'error');
					return;
                }  

                $("#inputDestinoContaFinanceiraId").val(idDestino);
                $("#inputTotalRecebido").val(totalRecebido);
                $("#inputTotalPago").val(totalPago);
                $("#inputValorCalculado").val(valorCalculado);
                $("#inputValorTransferir").val(valorTransferir);
                $("#inputSaldoCaixa").val(saldoFinalCaixa);

                if(pendenciaValorGavetaFaltaSobra()) {
                    return;
                }
                
                document.formFechamentoCaixa.submit();
            })

            //Desabilita o botão do PDV caso o caixa tenha uma data diferente da de hoje e que ainda não foi fechada
            function situacaoCaixa() {
                let situacaoCaixa = "<?php echo $situaCaixa; ?>";

                if(situacaoCaixa == 'DEVE_FECHAR') {
                    $('#btnPdv').prop('disabled', true);
                }
            }

            situacaoCaixa();
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
                            <div class="card-header bg-white" >
                                <div class="row d-flex ">
                                    <div class="col-lg-8">
                                        <h3 class="card-title">Fechamento de Caixa</h3>
                                    </div>

                                    <div class="col-lg-2" style="margin-top: 5px;">
                                        <h5>Data: <?php echo date('d/m/Y'); ?>
                                    </div>

                                    <div class="col-lg-2" style="margin-top: 5px;">
                                        <h5>Operador: <?php echo nomeSobrenome($_SESSION['UsuarNome'], 1); ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="text-center">
                                    <h3>Dados do Fechamento</h3>
                                </div>

                                <!--Link para abertura de caixa-->
                                <a id="abrirJustificativa" data-toggle="modal" data-target="#modal_small_justifica"></a>
                                
                                <table id="tblFechamentoCaixa" class="table table-bordered">
                                    <thead>
                                        <tr style="background-color: #CCCCCC;">
                                            <th>Forma de Recebimento/Pagamento</th>
                                            <th>Valor Recebido</th>
                                            <th>Valor Retirado</th>
                                            <th>Valor na gaveta</th>
                                            <th>Falta ou Sobra</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $contador = 1;
                                        $formaPagamentoNome = '';
                                        $formaPagamentoNomeControle = '';

                                        $totalValorRecebimento = 0;
                                        $totalValorPagamento = 0;

                                        foreach($rowMovimentacao as $item) {
                                            $formaPagamentoNome = $item['FrPagNome'];

                                            if($formaPagamentoNome != $formaPagamentoNomeControle) {
                                                $sql = "SELECT dbo.fnValorTotalRecebimentoCaixa(" . $_POST['inputAberturaCaixaId'] . ", " . $item['FrPagId'] . ") as ValorRecebimento";
                                                $result = $conn->query($sql);
                                                $rowValorRecebimento = $result->fetch(PDO::FETCH_ASSOC);

                                                $sql = "SELECT dbo.fnValorTotalPagamentoCaixa(" . $_POST['inputAberturaCaixaId'] . ", " . $item['FrPagId'] . ") as ValorPagamento";
                                                $result = $conn->query($sql);
                                                $rowValorPagamento = $result->fetch(PDO::FETCH_ASSOC);

                                                $valorRecebimento = $rowValorRecebimento['ValorRecebimento']; 
                                                $totalValorRecebimento += $valorRecebimento;
                                                $valorPagamento = $rowValorPagamento['ValorPagamento']; 
                                                $totalValorPagamento += $valorPagamento;

                                                print('
                                                <tr style="background-color: #e5e6e5;"> 
                                                    <td>'.$item['FrPagNome'].'</td>
                                                    <td id="valorRecebido'.$contador.'" style="text-align: right;">'.mostraValor($valorRecebimento).'</td>
                                                    <td id="valorRetirado'.$contador.'" style="text-align: right; color: red;">'.mostraValor($valorPagamento * -1).'</td>
                                                    <td><input id="valorGaveta'.$contador.'" tabindex="'.$contador.'" type="text" onkeyup="moeda(this)" class="text-right"></td>
                                                    <td id="valorFaltaSobra'.$contador.'" style="text-align: right;"></td>
                                                    <td style="display: none;">'.$contador.'</td>
                                                </tr>');
                                            }

                                            if($item['Tipo'] == 'Recebimento') {
                                                $saldo = $item['TOTAL'];

                                                print('
                                                <tr style="background-color: #f0f0f0;">
                                                    <td class="pl-5">'.$item['HISTORICO'].'</td>
                                                    <td id="teste'.$contador.'" style="text-align: right;">'.mostraValor($saldo).'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>'.$contador.'</td>
                                                </tr>');
                                            }else {
                                                $saldo = $item['TOTAL'] * -1;

                                                print('
                                                <tr style="background-color: #f0f0f0;">
                                                    <td class="pl-5">'.$item['HISTORICO'].'</td>
                                                    <td></td>
                                                    <td style="text-align: right; color: red;">'.mostraValor($saldo).'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="display: none;">'.$contador.'</td>
                                                </tr>');
                                            }

                                            $formaPagamentoNomeControle = $formaPagamentoNome;

                                            $contador++;
                                        }
                                        ?>

                                            <tr style="background-color: #f0f0f0;">
                                                <td>VALOR TOTAL</td>
                                                <td style="text-align: right;"><?php echo mostraValor($totalValorRecebimento); ?></td>
                                                <td style="text-align: right; color: red;"><?php echo mostraValor($totalValorPagamento * -1); ?></td>
                                                <td id="totalGaveta" style="text-align: right;"></td>
                                                <td id="totalFaltaSobra" style="text-align: right;"></td>
                                                <td style="display: none;"><?php echo $contador + 1; ?></td>
                                            </tr>
                                    </tbody>
                                </table>

                                <div class="text-center mt-4">
                                    <h3>Dados de Transferência</h3>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
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
                                        <div class="form-group text-right">
                                            <label for="saldoInicial">Saldo Inicial</label>
                                            <input type="text" id="saldoInicial" class="form-control text-right" name="saldoInicial"  value="<?php echo mostraValor($saldoInicialCaixa); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-right">
                                            <label for="valorCalculado">Valor Calculado</label>
                                            <input type="text" id="valorCalculado" class="form-control text-right" name="valorCalculado" value="<?php echo mostraValor($valorCalculado); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-right">
                                            <label for="valorTransferir">Valor a Transferir</label>
                                            <input type="text" id="valorTransferir" class="form-control text-right" name="valorTransferir" value="<?php echo mostraValor($valorATransferir); ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group text-right">
                                        <label for="saldoFinalCaixa">Saldo Caixa</label>
                                        <input type="text" id="saldoFinalCaixa" class="form-control text-right" name="saldoFinalCaixa" value="<?php echo mostraValor($saldoFinalCaixa); ?>" readonly>
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
                    <input type="hidden" id="inputTotalRecebido" name="inputTotalRecebido">
                    <input type="hidden" id="inputTotalPago" name="inputTotalPago">
                    <input type="hidden" id="inputValorCalculado" name="inputValorCalculado">
                    <input type="hidden" id="inputValorTransferir" name="inputValorTransferir">
                    <input type="hidden" id="inputSaldoCaixa" name="inputSaldoCaixa">
                    <input type="hidden" id="inputJustificativa" name="inputJustificativa">
                    <input type="hidden" id="aberturaCaixaId" name="aberturaCaixaId" value="<?php echo $caixaAberturaId;?>">
                    <input type="hidden" id="caixaId" name="caixaId" value="<?php echo $idCaixa;?>">
                    <input type="hidden" id="caixaNome" name="caixaNome" value="<?php echo $nomeCaixa;?>">
                </form>

                <form name="formCaixaAberturaId" method="post">
					<input type="hidden" id="inputAberturaCaixaId" name="inputAberturaCaixaId" value="">
                    <input type="hidden" id="inputAberturaCaixaNome" name="inputAberturaCaixaNome" value="">
				</form>
            </div>
            <!-- /content area -->

            <!-- small modal -->
            <div id="modal_small_justifica" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-xs">
                    <div class="modal-content">
                        <div class="custon-modal-title">
                            <i class=""></i>
                            <p class="h3">Falta ou Sobra</p>
                            <i class=""></i>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="justificativa" class="font-size-lg">Justificativa<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <textarea id="justificativa" class="form-control font-size-lg" name="justificativa" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-basic" data-dismiss="modal">Cancelar</button>
                            <button id="btnJustificar" type="button" class="btn bg-slate">Justificar</button>
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