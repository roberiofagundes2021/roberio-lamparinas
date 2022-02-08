<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){
    
    include('global_assets/php/conexao.php');



    if ($_POST['tipoFiltro'] == 'FiltroNormal') {
        

        if (isset($_POST['idConciliado'])) {
            try {
                if ($_POST['tpConciliado'] == "R") {
                        $sql = "UPDATE ContasAReceber 
                                    SET CnAReConciliado = ".$_POST['valorConciliado']."
                                WHERE CnAReId = ".$_POST['idConciliado']."";
                } else {
                        $sql = "UPDATE ContasAPagar
                                    SET CnAPaConciliado = ".$_POST['valorConciliado']."
                                WHERE CnAPaId = ".$_POST['idConciliado']."";
                }
                $result = $conn->prepare($sql);
                $result->execute();
            } catch (Exception $e) {
                echo ($e);
            }
        }

        $cont = 0;
        $argsCr = [];
        $argsCp = [];
        $argsCenCustCr = '';
        $argsCenCustCp = '';
        $status = explode('|', $_POST['cmbStatus']);

        if (!empty($_POST['inputPeriodoDe']) || !empty($_POST['inputAte'])) {
            empty($_POST['inputPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_POST['inputPeriodoDe'];
            empty($_POST['inputAte']) ? $inputAte = '2100-01-01' : $inputAte = $_POST['inputAte'];

            $argsCr[]  = "CNAREDTEMISSAO BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
            $argsCp[]  = "CNAPADTEMISSAO BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";

            if (!empty($_POST['inputPeriodoDe'])) {
                $_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe'] = $_POST['inputPeriodoDe'];
            }

            if (!empty($_POST['inputAte'])) {
                $_SESSION['MovimentacaoFinanceiraConciliacaoAte'] = $_POST['inputAte'];
            }
        }

        if (!empty($_POST['cmbContaBanco'])) {
            $argsCr[]  = "CnAReContaBanco = " . $_POST['cmbContaBanco'] . " ";
            $argsCp[]  = "CnAPaContaBanco = " . $_POST['cmbContaBanco'] . " ";
            $_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'] = $_POST['cmbContaBanco'];
        }

        if (!empty($_POST['cmbCentroDeCustos'])) {
            $argsCenCustCp = " join PlanoConta
                                on PlConId = CnAPaPlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";

            $argsCenCustCr = " join PlanoConta
                                on PlConId = CnARePlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";
                            
            $argsCr[]  = "CnCusId = " . $_POST['cmbCentroDeCustos'] . " ";
            $argsCp[]  = "CnCusId = " . $_POST['cmbCentroDeCustos'] . " ";
            $_SESSION['MovimentacaoFinanceiraConciliacaoCentroDeCustos'] = $_POST['cmbCentroDeCustos'];
        }

        if (!empty($_POST['cmbPlanoContas'])) {
            $argsCr[]  = "CnARePlanoContas = " . $_POST['cmbPlanoContas'] . " ";
            $argsCp[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
            $_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas'] = $_POST['cmbPlanoContas'];
        }

        if (!empty($_POST['cmbFormaDeRecebimento'])) {
            $argsCr[]  = "CnAReFormaPagamento = " . $_POST['cmbFormaDeRecebimento'] . " ";
            $argsCp[]  = "CnAPaFormaPagamento = " . $_POST['cmbFormaDeRecebimento'] . " ";
            $_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento'] = $_POST['cmbFormaDeRecebimento'];
        }

        if (!empty($_POST['cmbStatus'])) {
            if ($status[0] === "12") {
                $argsCp[]  = "CnAPaStatus = 12";

            } else if ($status[0] === "14") {
                $argsCp[]  = "CnAReStatus = 14";
            
            } else if ($status[0] === '16'){
                $argsCr[]  = "CnAReTransferencia > 0";
                $argsCp[]  = "CnAPaTransferencia > 0";

            } else {
                $argsCr[]  = "CnAReStatus = 14";
                $argsCp[]  = "CnAPaStatus = 12";
            }
            $_SESSION['MovimentacaoFinanceiraConciliacaoStatus'] = $_POST['cmbStatus'];
        }


        if ((count($argsCr) >= 1) || (count($stringCp) >= 1)) {

            $stringCr = implode(" and ", $argsCr);
            $stringCp = implode(" and ", $argsCp);

            if ($stringCr != '') {
                $stringCr .= ' and ';
            }

            if ($stringCp != '') {
                $stringCp .= ' and ';
            }

            if ($status[0] === "12") {
                $sql = "SELECT CNAPAID AS ID, 
                               CNAPADTEMISSAO AS DATA, 
                               CNAPADESCRICAO AS HISTORICO, 
                               CnAPANUMDOCUMENTO AS NUMDOC, 
                               CNAPAVALORPAGO as TOTAL, 
                               TIPO = 'P' ,
                               CODTRANSFREC = 0,
                               CnAPaTransferencia as CODTRANSFPAG,
                               CNAPACONCILIADO AS CONCILIADO
                        FROM ContasAPagar ";
                        if (isset($argsCenCustCp)) {
                            $sql .= " $argsCenCustCp ";
                        }
                $sql .= "WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                         ORDER BY DATA DESC";
                        
            } else if ($status[0] === "14") {
                $sql = "SELECT CNAREID AS ID, 
                               CNAREDTEMISSAO AS DATA, 
                               CNAREDESCRICAO AS HISTORICO, 
                               CnARENUMDOCUMENTO AS NUMDOC, 
                               CNAREVALORRECEBIDO as TOTAL, 
                               TIPO = 'R' , 
                               CnAReTransferencia as CODTRANSFREC, 
                               CODTRANSFPAG = 0,
                               CNARECONCILIADO AS CONCILIADO
                        FROM ContasAReceber ";
                        if (isset($argsCenCustCr)) {
                            $sql .= " $argsCenCustCr ";
                        }
                $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                        ORDER BY DATA DESC";
                        
            } else {
                $sql = "SELECT CNAREID AS ID, 
                               CNAREDTEMISSAO AS DATA, 
                               CNAREDESCRICAO AS HISTORICO, 
                               CnARENUMDOCUMENTO AS NUMDOC, 
                               CNAREVALORRECEBIDO as TOTAL, 
                               TIPO = 'R' , 
                               CnAReTransferencia as CODTRANSFREC, 
                               CODTRANSFPAG = 0,
                               CNARECONCILIADO AS CONCILIADO
                        FROM ContasAReceber ";
                        if (isset($argsCenCustCr)) {
                            $sql .= " $argsCenCustCr ";
                        }
                $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                        UNION 
                        SELECT CNAPAID AS ID, 
                               CNAPADTEMISSAO AS DATA, 
                               CNAPADESCRICAO AS HISTORICO, 
                               CnAPANUMDOCUMENTO AS NUMDOC, 
                               CNAPAVALORPAGO as TOTAL, 
                               TIPO = 'P' ,
                               CODTRANSFREC = 0 ,
                               CnAPaTransferencia as CODTRANSFPAG,
                               CNAPACONCILIADO AS CONCILIADO
                        FROM ContasAPagar ";
                        if (isset($argsCenCustCp)) {
                            $sql .= " $argsCenCustCp ";
                        }
                $sql .= "WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                        ORDER BY DATA DESC";
            }
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }

    } else if (isset($_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe']) ||  isset($_SESSION['MovimentacaoFinanceiraConciliacaoAte']) || isset($_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco']) || isset($_SESSION['MovimentacaoFinanceiraConciliacaoCentroDeCustos']) || isset($_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas']) || isset($_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento']) || isset($_SESSION['MovimentacaoFinanceiraConciliacaoStatus'])) {

        $cont = 0;
        $argsCr = [];
        $argsCp = [];
        $argsCenCustCr = '';
        $argsCenCustCp = '';
        $status = explode('|', $_POST['cmbStatus']);

        if (!empty($_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe']) || !empty($_SESSION['MovimentacaoFinanceiraConciliacaoAte'])) {
            empty($_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe'];
            empty($_SESSION['MovimentacaoFinanceiraConciliacaoAte']) ? $inputAte = '2100-01-01' : $inputAte = $_SESSION['MovimentacaoFinanceiraConciliacaoAte'];

            $argsCr[]  = "CnAReDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
            $argsCp[]  = "CnAPaDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
        }

        if (!empty($_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'])) {
            $argsCr[]  = "CnAReContaBanco = " . $_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'] . " ";
            $argsCp[]  = "CnAPaContaBanco = " . $_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'] . " ";
        }

        if (!empty($_SESSION['MovimentacaoFinanceiraConciliacaoCentroDeCustos'])) {
            $argsCenCustCp = " join PlanoConta
                                on PlConId = CnAPaPlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";

            $argsCenCustCr = " join PlanoConta
                                on PlConId = CnARePlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";
                            
            $argsCr[]  = "CnCusId = " . $_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'] . " ";
            $argsCp[]  = "CnCusId = " . $_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco'] . " ";
            $_SESSION['MovimentacaoFinanceiraConciliacaoCentroDeCustos'] = $_POST['cmbCentroDeCustos'];
        }

        if (!empty($_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas'])) {
            $argsCr[]  = "CnARePlanoContas = " . $_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas'] . " ";
            $argsCp[]  = "CnAPaPlanoContas = " . $_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas'] . " ";
        }

        if (!empty($_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento'])) {
            $argsCr[]  = "CnAReFormaPagamento = " . $_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento'] . " ";
            $argsCp[]  = "CnAPaFormaPagamento = " . $_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento'] . " ";
        }

        if (!empty($_POST['MovimentacaoFinanceiraConciliacaoStatus'])) {
            $statusSession = explode('|', $_SESSION['MovimentacaoFinanceiraConciliacaoStatus']);

            if ($statusSession[0] === "12") {
                $argsCp[]  = "CnAPaStatus = 12";

            } else if ($statusSession[0] === "14") {
                $argsCp[]  = "CnAReStatus = 14";

            } else if ($statusSession[0] === '16'){
                $argsCr[]  = "CnAReTransferencia > 0";
                $argsCp[]  = "CnAPaTransferencia > 0";

            } else {
                $argsCr[]  = "CnAReStatus = 14";
                $argsCp[]  = "CnAPaStatus = 12";
            }
        }

        if ((count($argsCr) >= 1) || (count($stringCp) >= 1)) {

            $stringCr = implode(" and ", $argsCr);
            $stringCp = implode(" and ", $argsCp);

            if ($stringCr != '') {
                $stringCr .= ' and ';
            }

            if ($stringCp != '') {
                $stringCp .= ' and ';
            }

            if ($status[0] === "12") {
                $sql = "SELECT CNAPAID AS ID, 
                               CNAPADTEMISSAO AS DATA, 
                               CNAPADESCRICAO AS HISTORICO, 
                               CnAPANUMDOCUMENTO AS NUMDOC, 
                               CNAPAVALORPAGO as TOTAL, 
                               TIPO = 'P',
                               CODTRANSFREC = 0 ,
                               CnAPaTransferencia as CODTRANSFPAG,
                               CNAPACONCILIADO AS CONCILIADO
                          FROM ContasAPagar ";
                        if (isset($argsCenCustCp)) {
                            $sql .= " $argsCenCustCp ";
                        }
                $sql .= "WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                         ORDER BY DATA DESC";
                        
            } else if ($status[0] === "14") {
                $sql = "SELECT CNAREID AS ID, 
                               CNAREDTEMISSAO AS DATA, 
                               CNAREDESCRICAO AS HISTORICO, 
                               CnARENUMDOCUMENTO AS NUMDOC, 
                               CNAREVALORRECEBIDO as TOTAL, 
                               TIPO = 'R', 
                               CnAReTransferencia as CODTRANSFREC, 
                               CODTRANSFPAG = 0,
                               CNARECONCILIADO AS CONCILIADO
                        FROM ContasAReceber ";
                        if (isset($argsCenCustCr)) {
                            $sql .= " $argsCenCustCr ";
                        }
                $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                        ORDER BY DATA DESC";
                        
            } else {
                $sql = "SELECT CNAREID AS ID, 
                               CNAREDTEMISSAO AS DATA, 
                               CNAREDESCRICAO AS HISTORICO, 
                               CnARENUMDOCUMENTO AS NUMDOC, 
                               CNAREVALORRECEBIDO as TOTAL, 
                               TIPO = 'R',
                               CnAReTransferencia as CODTRANSFREC, 
                               CODTRANSFPAG = 0,
                               CNARECONCILIADO AS CONCILIADO
                          FROM ContasAReceber ";
                        if (isset($argsCenCustCr)) {
                            $sql .= " $argsCenCustCr ";
                        }
                $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                        UNION 
                        SELECT CNAPAID AS ID, 
                               CNAPADTEMISSAO AS DATA, 
                               CNAPADESCRICAO AS HISTORICO, 
                               CnAPANUMDOCUMENTO AS NUMDOC, 
                               CNAPAVALORPAGO as TOTAL, 
                               TIPO = 'P' , 
                               CODTRANSFREC = 0 ,
                               CnAPaTransferencia as CODTRANSFPAG,
                               CNAPACONCILIADO AS CONCILIADO
                          FROM ContasAPagar ";
                        if (isset($argsCenCustCp)) {
                            $sql .= " $argsCenCustCp ";
                        }
                $sql .= "WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                        ORDER BY DATA DESC";
            }
    
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
        
    } else {

        $dataInicio = date("Y-m-d");
        $dataFim = date("Y-m-d");

        $sql = "SELECT CNAREID AS ID, 
                       CNAREDTEMISSAO AS DATA, 
                       CNAREDESCRICAO AS HISTORICO, 
                       CNARENUMDOCUMENTO AS NUMDOC, 
                       CNAREVALORRECEBIDO AS TOTAL, 
                       TIPO = 'R', 
                       CNAReTransferencia AS CODTRANSFREC, 
                       CODTRANSFPAG = 0,
                       CNARECONCILIADO AS CONCILIADO
                  FROM ContasAReceber
                 WHERE CNARESTATUS = 14
                   AND CnAReUnidade = " . $_SESSION['UnidadeId'] . " 
                   AND CnAReDtVencimento BETWEEN '" . $dataInicio . "' and '" . $dataFim . "' 
                 UNION 
                SELECT CNAPAID AS ID, 
                       CNAPADTEMISSAO AS DATA, 
                       CNAPADESCRICAO AS HISTORICO, 
                       CnAPANUMDOCUMENTO AS NUMDOC, 
                       CNAPAVALORPAGO as TOTAL, 
                       TIPO = 'P',
                       CODTRANSFREC = 0,
                       CnAPaTransferencia as CODTRANSFPAG,
                       CNAPACONCILIADO AS CONCILIADO
                  FROM ContasAPagar
                 WHERE CNAPASTATUS = 12
                   AND CnAPaUnidade = " . $_SESSION['UnidadeId'] . " 
                   AND CnAPaDtVencimento BETWEEN '" . $dataInicio . "' and '" . $dataFim . "' 
                ORDER BY DATA DESC";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);
        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }


    if ($cont == 1) {
        $cont = 0;
        print('<input type="hidden" id="elementosGrid" value="' . count($rowData) . '">');
        $saldo = 0;
        
        foreach ($rowData as $item) {
            $cont++;
            if ($item['TIPO'] === 'R'){
                $saldo += $item['TOTAL'];
            }
            else {
                $saldo -= $item['TOTAL'];
            }
        
            $data = mostraData($item['DATA']);

            $print = "
                <tr>
                    <td class='even'><p class='m-0'>" . $data . "</p><input type='hidden' value='" . $item['DATA'] . "'></td>";


                    //HISTÓRICO
                    if (intval($item['CODTRANSFREC']) > 0){
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraTransferencia.php?lancamentoId=" . $item['CODTRANSFREC'] . "'>" . $item['HISTORICO'] . "</a></td>";

                    } else if (intval($item['CODTRANSFPAG']) > 0) {
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraTransferencia.php?lancamentoId=" . $item['CODTRANSFPAG'] . "'>" . $item['HISTORICO'] . "</a></td>";

                    } else if ($item['TIPO'] === 'R'){
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraRecebimento.php?lancamentoId=" . $item['ID'] . "'>" . $item['HISTORICO'] . "</a></td>";

                    } else if ($item['TIPO'] === 'P') {
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraPagamento.php?lancamentoId=" . $item['ID'] . "'>" . $item['HISTORICO'] . "</a></td>";
                    }


                    //NUMERO DO DOCUMENTO
                    $print .= "<td class='even' style='text-align: left;width: 15%;'>" . $item['NUMDOC'] . "</td>";

                    
                    if ($item['TIPO'] === 'R'){
                        //ENTRADA
                        $print .= "<td class='even' style='color:green;text-align: right;padding-right:40px;'>" . mostraValor($item['TOTAL']) . "</td>
                                   <td class='even'></td>";
                    } else {
                        //SAIDA
                        $print .= "<td class='even'></td>
                                   <td class='even' style='color:red;text-align: right;padding-right:40px;'>" . mostraValor($item['TOTAL']) . "</td>";
                    }

                    
                    //APLICANDO ESTILO NA COLUNA SALDO
                    if ($saldo < 0) {
                        $print .= "<td class='even' style='color: red;text-align: right;padding-right:40px;'>" . mostraValor($saldo) . "</td>";
                    }
                    else {
                        $print .= "<td class='even' style='color: green;text-align: right;padding-right:40px;'>" . mostraValor($saldo) . "</td>";
                    }

                    if ($item['CONCILIADO'] >= 1) {
                        $saldo < 0 ? $print .= "<td class='even' style='color: red;text-align: right;padding-right:40px;'>" . mostraValor($saldo) . "</td>" : $print .= "<td class='even' style='color: green;text-align: right;padding-right:40px;'>" . mostraValor($saldo) . "</td>";

                    } else if ($item['CONCILIADO'] !== 0) {
                        $print .= "<td class='even' style='color: red;text-align: right;padding-right:40px;'>0,00</td>";
                    }
                    
                    //MENU EDITAR E EXCLUIR
                    $print .= "
                        <td class='even d-flex flex-row justify-content-around align-content-center' style='text-align: center'>";

                                    $prod = $item['ID'].'#'.$item['TIPO'];
                                    //BOTAO CONCILIADO
                                    if ($item['CONCILIADO'] >= 1) {
                                        $print .= "
                                            <input type='checkbox' id='".$prod."' onchange='atualizarConciliado()' value='1' checked/>
                                        ";
                                    } else {
                                        $print .= "
                                            <input type='checkbox' id='".$prod."' onchange='atualizarConciliado()'  value='0' />
                                        ";
                                    }

                                    $print .= "
                        </td>
                    </tr>
                ";
            print($print);
        }
    }
}

queryPesquisa();