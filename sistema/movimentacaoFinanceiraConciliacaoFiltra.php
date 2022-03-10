<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){
    
    include('global_assets/php/conexao.php');

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
                           SituaNome as SITUACAO,
                           SituaCor as COR,
                           SituaChave as CHAVE,
                           CNAPACONCILIADO AS CONCILIADO
                    FROM ContasAPagar 
                    JOIN Situacao on SituaId = CnAPaStatus";
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
                           SituaNome as SITUACAO,
                           SituaCor as COR,
                           SituaChave as CHAVE,
                           CNARECONCILIADO AS CONCILIADO
                    FROM ContasAReceber 
                    JOIN Situacao on SituaId = CnAReStatus";
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
                           SituaNome as SITUACAO,
                           SituaCor as COR,
                           SituaChave as CHAVE,
                           CNARECONCILIADO AS CONCILIADO
                    FROM ContasAReceber
                    JOIN Situacao on SituaId = CnAReStatus";
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
                           SituaNome as SITUACAO,
                           SituaCor as COR,
                           SituaChave as CHAVE,
                           CNAPACONCILIADO AS CONCILIADO
                    FROM ContasAPagar
                    JOIN Situacao on SituaId = CnAPaStatus";
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


    if ($cont == 1) {
        $cont = 0;
        //print('<input type="hidden" id="elementosGrid" value="' . count($rowData) . '">');
        $saldo = 0;

        $arrayData = [];
        
        foreach ($rowData as $item) {
            $cont++;
            if ($item['TIPO'] === 'R'){
                $saldo += $item['TOTAL'];
            }
            else {
                $saldo -= $item['TOTAL'];
            }
        
            $data = mostraData($item['DATA']);

            /*
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

                    //SITUAÇÃO
                    $situacao = $item['SITUACAO']; //$item['SituaNome'];
                    $situacaoClasse = 'badge badge-flat border-'.$item['COR'].' text-'.$item['COR'];
                    $chave = $item['CHAVE'];

                    if($chave == 'ARECEBER' || $chave == 'RECEBIDO') {
                        $print .= '<td class="even" style="text-align: center;padding-right:40px;">
                                    <a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'ContaAReceber\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>
                               </td>';    
                    }else if($chave == 'APAGAR' || $chave == 'PAGO') {
                        $print .= '<td class="even" style="text-align: center;padding-right:40px;">
                                        <a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'ContaAPagar\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>
                                   </td>';
                    }else {
                        $print .= '<td class="even" style="text-align: center;padding-right:40px;">
                                        <a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'Teste\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>
                                   </td>';
                    }
                    
                    
                    //CHECKBOX - CONCILIADO
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
            */

            //HISTÓRICO
            if (intval($item['CODTRANSFREC']) > 0){
                $historico = "<a href='#' onclick='atualizaMovimentacaoFinanceira(1,".$item['CODTRANSFREC'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a>";
                           
            } else if (intval($item['CODTRANSFPAG']) > 0) {
                $historico = "<a href='#' onclick='atualizaMovimentacaoFinanceira(1,".$item['CODTRANSFPAG'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a>";

            } else if ($item['TIPO'] === 'R'){
                $historico = "<a href='#' onclick='atualizaMovimentacaoFinanceira(1,".$item['ID'].", \"R\", \"edita\");'>" . $item['HISTORICO'] . "</a>";

            } else if ($item['TIPO'] === 'P') {
                $historico = "<a href='#' onclick='atualizaMovimentacaoFinanceira(1,".$item['ID'].", \"P\", \"edita\");'>" . $item['HISTORICO'] . "</a>";
            }

            //NUMERO DO DOCUMENTO
            $numeroDocucmento = $item['NUMDOC'];

            $entrada = null;
            $saida = null;
                    
            if ($item['TIPO'] === 'R'){
                //ENTRADA
                $entrada = mostraValor($item['TOTAL']);
            } else {
                //SAIDA
                $saida = mostraValor($item['TOTAL']);
            }

            //APLICANDO ESTILO NA COLUNA SALDO
            if ($saldo < 0) {
                $colunaSaldo = mostraValor($saldo);
            }
            else {
                $colunaSaldo = mostraValor($saldo);
            }

            //Saldo conciliado
            if ($item['CONCILIADO'] >= 1) {
                $saldoConciliaco = mostraValor($saldo);

            } else if ($item['CONCILIADO'] !== 0) {
                $saldoConciliaco = "0,00";
            }

            //SITUAÇÃO
            $situacao = $item['SITUACAO']; //$item['SituaNome'];
            $situacaoClasse = 'badge badge-flat border-'.$item['COR'].' text-'.$item['COR'];
            $chave = $item['CHAVE'];

            if($chave == 'ARECEBER' || $chave == 'RECEBIDO') {
                $colunaSituacao = '<a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'ContaAReceber\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>';    
            }else if($chave == 'APAGAR' || $chave == 'PAGO') {
                $colunaSituacao = '<a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'ContaAPagar\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>';
            }else {
                $colunaSituacao = '<a href="#" onclick="atualizaConciliacao('.$item['ID'].', \'Teste\');"><span class="badge  '.$situacaoClasse.'">'.$situacao.'</span></a>';
            }

            //CHECKBOX - CONCILIADO
            $prod = $item['ID'].'#'.$item['TIPO'];
            //BOTAO CONCILIADO
            if ($item['CONCILIADO'] >= 1) {
                $checkbox = "
                    <input type='checkbox' id='".$prod."' onchange='atualizarConciliado()' value='1' checked/>
                ";
            } else {
                $checkbox = "
                    <input type='checkbox' id='".$prod."' onchange='atualizarConciliado()'  value='0' />
                ";
            }

            $array = [
                'data'=>[
                    isset($data) ? $data : null, 
                    isset($historico) ? $historico : null, 
                    isset($numeroDocucmento) ? $numeroDocucmento : null, 
                    isset($entrada) ? $entrada : null, 
                    isset($saida) ? $saida : null, 
                    isset($colunaSaldo) ? $colunaSaldo : null,
                    isset($saldoConciliaco) ? $saldoConciliaco : null,
                    isset($colunaSituacao) ? $colunaSituacao : null, 
                    isset($checkbox) ? $checkbox : null
                ],
                'identify'=>[
                    
                ]
            ];

            array_push($arrayData,$array);
        }

        print(json_encode($arrayData));
    }
}

queryPesquisa();