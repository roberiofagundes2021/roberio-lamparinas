<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){
    
    include('global_assets/php/conexao.php');

    if ($_POST['tipoFiltro'] == 'FiltroNormal') {

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
                $_SESSION['MovFinancPeriodoDe'] = $_POST['inputPeriodoDe'];
            }

            if (!empty($_POST['inputAte'])) {
                $_SESSION['MovFinancAte'] = $_POST['inputAte'];
            }
        }

        if (!empty($_POST['cmbContaBanco'])) {
            $argsCr[]  = "CnAReContaBanco = " . $_POST['cmbContaBanco'] . " ";
            $argsCp[]  = "CnAPaContaBanco = " . $_POST['cmbContaBanco'] . " ";
            $_SESSION['MovFinancContaBanco'] = $_POST['cmbContaBanco'];
        }

        if (!empty($_POST['cmbCentroDeCustos'])) {
            $argsCenCustCp = " join PlanoContas
                                on PlConId = CnAPaPlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";

            $argsCenCustCr = " join PlanoContas
                                on PlConId = CnARePlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";
                            
            $argsCr[]  = "CnCusId = " . $_POST['cmbCentroDeCustos'] . " ";
            $argsCp[]  = "CnCusId = " . $_POST['cmbCentroDeCustos'] . " ";
            $_SESSION['MovFinancCentroDeCustos'] = $_POST['cmbCentroDeCustos'];
        }

        if (!empty($_POST['cmbPlanoContas'])) {
            $argsCr[]  = "CnARePlanoContas = " . $_POST['cmbPlanoContas'] . " ";
            $argsCp[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
            $_SESSION['MovFinancPlanoContas'] = $_POST['cmbPlanoContas'];
        }

        if (!empty($_POST['cmbFormaDeRecebimento'])) {
            $argsCr[]  = "CnAReFormaPagamento = " . $_POST['cmbFormaDeRecebimento'] . " ";
            $argsCp[]  = "CnAPaFormaPagamento = " . $_POST['cmbFormaDeRecebimento'] . " ";
            $_SESSION['MovFinancFormaPagamento'] = $_POST['cmbFormaDeRecebimento'];
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
            $_SESSION['MovFinancStatus'] = $_POST['cmbStatus'];
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
                               CNAPACONTABANCO AS CONTABANCO
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
                               CNARECONTABANCO AS CONTABANCO
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
                               CNARECONTABANCO AS CONTABANCO
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
                               CNAPACONTABANCO AS CONTABANCO
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

    } else if (isset($_SESSION['MovFinancPeriodoDe']) ||  isset($_SESSION['MovFinancAte']) || isset($_SESSION['MovFinancContaBanco']) || isset($_SESSION['MovFinancCentroDeCustos']) || isset($_SESSION['MovFinancPlanoContas']) || isset($_SESSION['MovFinancFormaPagamento']) || isset($_SESSION['MovFinancStatus'])) {

        $cont = 0;
        $argsCr = [];
        $argsCp = [];
        $argsCenCustCr = '';
        $argsCenCustCp = '';
        $status = explode('|', $_POST['cmbStatus']);

        if (!empty($_SESSION['MovFinancPeriodoDe']) || !empty($_SESSION['MovFinancAte'])) {
            empty($_SESSION['MovFinancPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_SESSION['MovFinancPeriodoDe'];
            empty($_SESSION['MovFinancAte']) ? $inputAte = '2100-01-01' : $inputAte = $_SESSION['MovFinancAte'];

            $argsCr[]  = "CnAReDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
            $argsCp[]  = "CnAPaDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
        }

        if (!empty($_SESSION['MovFinancContaBanco'])) {
            $argsCr[]  = "CnAReContaBanco = " . $_SESSION['MovFinancContaBanco'] . " ";
            $argsCp[]  = "CnAPaContaBanco = " . $_SESSION['MovFinancContaBanco'] . " ";
        }

        if (!empty($_SESSION['MovFinancCentroDeCustos'])) {
            $argsCenCustCp = " join PlanoContas
                                on PlConId = CnAPaPlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";

            $argsCenCustCr = " join PlanoContas
                                on PlConId = CnARePlanoContas
                                join CentroCusto
                                on CnCusId = PlConCentroCusto ";
                            
            $argsCr[]  = "CnCusId = " . $_SESSION['MovFinancContaBanco'] . " ";
            $argsCp[]  = "CnCusId = " . $_SESSION['MovFinancContaBanco'] . " ";
            $_SESSION['MovFinancCentroDeCustos'] = $_POST['cmbCentroDeCustos'];
        }

        if (!empty($_SESSION['MovFinancPlanoContas'])) {
            $argsCr[]  = "CnARePlanoContas = " . $_SESSION['MovFinancPlanoContas'] . " ";
            $argsCp[]  = "CnAPaPlanoContas = " . $_SESSION['MovFinancPlanoContas'] . " ";
        }

        if (!empty($_SESSION['MovFinancFormaPagamento'])) {
            $argsCr[]  = "CnAReFormaPagamento = " . $_SESSION['MovFinancFormaPagamento'] . " ";
            $argsCp[]  = "CnAPaFormaPagamento = " . $_SESSION['MovFinancFormaPagamento'] . " ";
        }

        if (!empty($_POST['MovFinancStatus'])) {
            $statusSession = explode('|', $_SESSION['MovFinancStatus']);

            if ($statusSession[0] === "12") {
                $argsCp[]  = "CnAPaStatus = 12";

            } else if ($statusSession[0] === "14") {
                $argsCp[]  = "CnAReStatus = 14";

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
                               CNAPACONTABANCO AS CONTABANCO
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
                               CNARECONTABANCO AS CONTABANCO
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
                               CNARECONTABANCO AS CONTABANCO
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
                               CNAPACONTABANCO AS CONTABANCO
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
                       CNARECONTABANCO AS CONTABANCO
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
                       CNAPACONTABANCO AS CONTABANCO
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


                    //CONTA CAIXA
                    if (isset($item['CONTABANCO']) && $item['CONTABANCO'] != 0) {
                        $sql = "SELECT  CnBanNome
                                  FROM  ContaBanco
                                  JOIN  Situacao 
                                    ON  SituaId = CnBanStatus
                                 WHERE  CnBanUnidade = " . $_SESSION['UnidadeId'] . " 
                                   AND  SituaChave = 'ATIVO'
                                   AND  CnBanId = ". $item['CONTABANCO'] ."";
                        $result = $conn->query($sql);
                        $ContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);

                        $print .= "<td class='even' style='text-align: left;'>" . $ContaBanco[0]['CnBanNome'] . "</td>";
                    } else {
                        $print .= "<td class='even' style='text-align: left;'></td>";
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
                    
                    //MENU EDITAR E EXCLUIR
                    $print .= "
                        <td class='even d-flex flex-row justify-content-around align-content-center' style='text-align: center'>
                            <div class='list-icons'>
                                <div class='list-icons list-icons-extended'> ";

                                    //BOTAO EDITAR
                                    if (intval($item['CODTRANSFREC']) > 0){
                                        $print .= "<a href='movimentacaoFinanceiraTransferencia.php?lancamentoId=" . $item['CODTRANSFREC'] . "' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Editar Conta'><i class='icon-pencil7'></i></a>";
                
                                    } else if (intval($item['CODTRANSFPAG']) > 0) {
                                        $print .= "<a href='movimentacaoFinanceiraTransferencia.php?lancamentoId=" . $item['CODTRANSFPAG'] . "' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Editar Conta'><i class='icon-pencil7'></i></a>";
                
                                    } else if ($item['TIPO'] === 'R'){
                                        $print .= "<a href='movimentacaoFinanceiraRecebimento.php?lancamentoId=" . $item['ID'] . "' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Editar Conta'><i class='icon-pencil7'></i></a>";
                                        
                                    } else if ($item['TIPO'] === 'P') {
                                        $print .= "<a href='movimentacaoFinanceiraPagamento.php?lancamentoId=" . $item['ID'] . "' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Editar Conta'><i class='icon-pencil7'></i></a>";
                                    }
                                    
                                    //BOTAO EXCLUIR
                                    if (intval($item['CODTRANSFREC']) > 0){
                                        $print .= "
                                            <a href='#' idContaExcluir='" . $item['CODTRANSFREC'] . "' tipo='T' class='list-icons-item excluirConta'  data-popup='tooltip' data-placement='bottom' title='Excluir Conta'><i class='icon-bin'></i></a>";
                
                                    } else if (intval($item['CODTRANSFPAG']) > 0) {
                                        $print .= "
                                            <a href='#' idContaExcluir='" . $item['CODTRANSFPAG'] . "' tipo='T' class='list-icons-item excluirConta'  data-popup='tooltip' data-placement='bottom' title='Excluir Conta'><i class='icon-bin'></i></a>";

                                    } else {
                                        $print .= "
                                            <a href='#' idContaExcluir='" . $item['ID'] . "' tipo='" . $item['TIPO'] . "' class='list-icons-item excluirConta'  data-popup='tooltip' data-placement='bottom' title='Excluir Conta'><i class='icon-bin'></i></a>";
                                    }
                                    $print .= "
                                </div>
                            </div>
                        </td>
                    </tr>
                ";
            print($print);
        }
    }
}

queryPesquisa();