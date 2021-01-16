<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){
    
    include('global_assets/php/conexao.php');

    if ($_POST['tipoFiltro'] == 'FiltroNormal') {

        $cont = 0;
        $argsCr = [];
        $argsCp = [];

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

        // if (!empty($_POST['cmbCentroDeCustos'])) {
        //     $argsCr[]  = "CnARePlanoContas = " . $_POST['cmbPlanoContas'] . " ";
        //     $argsCp[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
        //     $_SESSION['MovFinancPlanoContas'] = $_POST['cmbPlanoContas'];
        // }

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
            $argsCr[]  = "CnAReStatus = 14";
            $argsCp[]  = "CnAPaStatus = 12";
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

            $sql = "SELECT CNAREID AS ID, CNAREDTEMISSAO AS DATA, CNAREDESCRICAO AS HISTORICO, CnARENUMDOCUMENTO AS NUMDOC, CNAREVALORRECEBIDO as TOTAL, TIPO = 'E' FROM ContasAReceber
                    WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                    UNION 
                    SELECT CNAPAID AS ID, CNAPADTEMISSAO AS DATA, CNAPADESCRICAO AS HISTORICO, CnAPANUMDOCUMENTO AS NUMDOC, CNAPAVALORPAGO as TOTAL, TIPO = 'S' FROM ContasAPagar
                    WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                    ORDER BY DATA ASC";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
    } else if (isset($_SESSION['MovFinancPeriodoDe']) ||  isset($_SESSION['MovFinancAte']) || isset($_SESSION['MovFinancContaBanco']) || isset($_SESSION['MovFinancCentroDeCustos']) || isset($_SESSION['MovFinancPlanoContas']) || isset($_SESSION['MovFinancFormaPagamento']) || isset($_SESSION['MovFinancStatus'])) {

        $cont = 0;
        $argsCr = [];
        $argsCp = [];

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

        // if (!empty($_SESSION['MovFinancCentroDeCustos'])) {
        //     $argsCr[]  = "CnAReContaBanco = " . $_SESSION['MovFinancContaBanco'] . " ";
        //     $argsCp[]  = "CnAPaContaBanco = " . $_SESSION['MovFinancContaBanco'] . " ";
        // }

        if (!empty($_SESSION['MovFinancPlanoContas'])) {
            $argsCr[]  = "CnARePlanoContas = " . $_SESSION['MovFinancPlanoContas'] . " ";
            $argsCp[]  = "CnAPaPlanoContas = " . $_SESSION['MovFinancPlanoContas'] . " ";
        }

        if (!empty($_SESSION['MovFinancFormaPagamento'])) {
            $argsCr[]  = "CnAReFormaPagamento = " . $_SESSION['MovFinancFormaPagamento'] . " ";
            $argsCp[]  = "CnAPaFormaPagamento = " . $_SESSION['MovFinancFormaPagamento'] . " ";
        }

        if (!empty($_POST['MovFinancStatus'])) {
            $argsCr[]  = "CnAReStatus = 14";
            $argsCp[]  = "CnAPaStatus = 12";
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

            $sql = "SELECT CNAREID AS ID, CNAREDTEMISSAO AS DATA, CNAREDESCRICAO AS HISTORICO, CnARENUMDOCUMENTO AS NUMDOC, CNAREVALORRECEBIDO as TOTAL, TIPO = 'E' FROM ContasAReceber
                    WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                    UNION 
                    SELECT CNAPAID AS ID, CNAPADTEMISSAO AS DATA, CNAPADESCRICAO AS HISTORICO, CnAPANUMDOCUMENTO AS NUMDOC, CNAPAVALORPAGO as TOTAL, TIPO = 'S' FROM ContasAPagar
                    WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                    ORDER BY DATA ASC";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
    } else {

        $dataInicio = date("Y-m-d");
        $dataFim = date("Y-m-d");

        $sql = "SELECT CNAREID AS ID, CNAREDTEMISSAO AS DATA, CNAREDESCRICAO AS HISTORICO, CnARENUMDOCUMENTO AS NUMDOC, CNAREVALORRECEBIDO as TOTAL, TIPO = 'E' FROM ContasAReceber
                WHERE CNARESTATUS = 14
                UNION 
                SELECT CNAPAID AS ID, CNAPADTEMISSAO AS DATA, CNAPADESCRICAO AS HISTORICO, CnAPANUMDOCUMENTO AS NUMDOC, CNAPAVALORPAGO as TOTAL, TIPO = 'S' FROM ContasAPagar
                WHERE CNAPASTATUS = 12
                ORDER BY DATA ASC";
                
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
            if ($item['TIPO'] === 'E'){
                $saldo += $item['TOTAL'];
            }
            else {
                $saldo -= $item['TOTAL'];
            }
        
            $data = mostraData($item['DATA']);

            $print = "
                <tr>
                    <td class='even'><p class='m-0'>" . $data . "</p><input type='hidden' value='" . $item['DATA'] . "'></td>";

                    if ($item['TIPO'] === 'E'){
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraRecebimento.php?lancamentoId=" . $item['ID'] . "'>" . $item['HISTORICO'] . "</a></td>";
                    }
                    else {
                        $print .= "<td class='even'><a href='movimentacaoFinanceiraPagamento.php?lancamentoId=" . $item['ID'] . "'>" . $item['HISTORICO'] . "</a></td>";
                    }

                $print .= "<td class='even' style='text-align: left;width: 15%;'>" . $item['NUMDOC'] . "</td>";

                    if ($item['TIPO'] === 'E'){
                        $print .= "<td class='even' style='color:green'>" . mostraValor($item['TOTAL']) . "</td>
                                   <td class='even'></td>";
                    }
                    else {
                        $print .= "<td class='even'></td>
                                   <td class='even' style='color:red'>-" . mostraValor($item['TOTAL']) . "</td>";
                    }

                    if ($saldo < 0) {
                        $print .= "<td class='even' style='color: red';>" . mostraValor($saldo) . "</td>";
                    }
                    else {
                        $print .= "<td class='even' style='color: green';>" . mostraValor($saldo) . "</td>";
                    }
                    
                $print .= "
                    <td class='even d-flex flex-row justify-content-around align-content-center' style='text-align: center'>
                        <div class='list-icons'>
                            <div class='list-icons list-icons-extended'>
                                <a href='#' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Editar Conta'><i class='icon-pencil7'></i></a>
                                <a href='#' idContaExcluir='" . $item['ID'] . "' class='list-icons-item excluirConta'  data-popup='tooltip' data-placement='bottom' title='Excluir Conta'><i class='icon-bin'></i></a>
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