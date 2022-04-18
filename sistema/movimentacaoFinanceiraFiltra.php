<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){
    
    include('global_assets/php/conexao.php');

    $cont = 0;
    $argsCr = [];
    $argsCp = [];
    $argsCenCustCr = '';
    $argsCenCustCp = '';
    $status = explode('|', $_POST['cmbStatus']);

    //Aqui é para limpar a sessão caso o usuário filtre todos novamente
    $_SESSION['MovFinancPeriodoDe'] = '';
    $_SESSION['MovFinancAte'] = '';
    $_SESSION['MovFinancContaBanco'] = '';
    $_SESSION['MovFinancCentroDeCustos'] = '';
    $_SESSION['MovFinancPlanoContas'] = '';
    $_SESSION['MovFinancFormaPagamento'] = '';
    $_SESSION['MovFinancStatus'] = '';

    if (!empty($_POST['inputPeriodoDe']) || !empty($_POST['inputAte'])) {
        empty($_POST['inputPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_POST['inputPeriodoDe'];
        empty($_POST['inputAte']) ? $inputAte = '2100-01-01' : $inputAte = $_POST['inputAte'];

        $argsCr[]  = "CNAREDTRECEBIMENTO BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
        $argsCp[]  = "CNAPADTPAGAMENTO BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";

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
            $argsCr[]  = "CnAReStatus = 14";
        
        } else if ($status[0] === '16'){
            $argsCr[]  = "CnAReTransferencia > 0";
            $argsCp[]  = "CnAPaTransferencia > 0";

        }
        $_SESSION['MovFinancStatus'] = $_POST['cmbStatus'];
    }else {
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

        if ($status[0] === "12") {
            $sql = "SELECT CNAPAID AS ID, 
                           CNAPADTPAGAMENTO AS DATA, 
                           CNAPADESCRICAO AS HISTORICO,  
                           FORNENOME AS FORNECEDOR,
                           CnAPANUMDOCUMENTO AS NUMDOC, 
                           CNAPAVALORPAGO as TOTAL, 
                           TIPO = 'P' ,
                           CODTRANSFREC = 0,
                           CnAPaTransferencia as CODTRANSFPAG,
                           CNAPACONTABANCO AS CONTABANCO
                    FROM ContasAPagar  
                    JOIN FORNECEDOR on FORNEID = CNAPAFORNECEDOR";
                    if (isset($argsCenCustCp)) {
                        $sql .= " $argsCenCustCp ";
                    }
            $sql .= "WHERE " . $stringCp . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                     ORDER BY DATA DESC";
                    
        } else if ($status[0] === "14") {
            $sql = "SELECT CNAREID AS ID, 
                           CNAREDTRECEBIMENTO AS DATA, 
                           CNAREDESCRICAO AS HISTORICO,  
                           CLIENNOME AS CLIENTE, 
                           CnARENUMDOCUMENTO AS NUMDOC, 
                           CNAREVALORRECEBIDO as TOTAL, 
                           TIPO = 'R' , 
                           CnAReTransferencia as CODTRANSFREC, 
                           CODTRANSFPAG = 0,
                           CNARECONTABANCO AS CONTABANCO
                    FROM ContasAReceber  
                    JOIN CLIENTE on CLIENID = CNARECLIENTE";
                    if (isset($argsCenCustCr)) {
                        $sql .= " $argsCenCustCr ";
                    }
            $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                    ORDER BY DATA DESC";
                    
        } else {
            $sql = "SELECT CNAREID AS ID, 
                           CNAREDTRECEBIMENTO AS DATA, 
                           CNAREDESCRICAO AS HISTORICO,  
                           CLIENNOME AS CLIENTE,
                           CnARENUMDOCUMENTO AS NUMDOC, 
                           CNAREVALORRECEBIDO as TOTAL, 
                           TIPO = 'R' , 
                           CnAReTransferencia as CODTRANSFREC, 
                           CODTRANSFPAG = 0,
                           CNARECONTABANCO AS CONTABANCO
                    FROM ContasAReceber 
                    JOIN CLIENTE on CLIENID = CNARECLIENTE";
                    if (isset($argsCenCustCr)) {
                        $sql .= " $argsCenCustCr ";
                    }
            $sql .= "WHERE " . $stringCr . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                    UNION 
                    SELECT CNAPAID AS ID, 
                           CNAPADTPAGAMENTO AS DATA, 
                           CNAPADESCRICAO AS HISTORICO, 
                           FORNENOME AS FORNECEDOR,
                           CnAPANUMDOCUMENTO AS NUMDOC, 
                           CNAPAVALORPAGO as TOTAL, 
                           TIPO = 'P' ,
                           CODTRANSFREC = 0 ,
                           CnAPaTransferencia as CODTRANSFPAG,
                           CNAPACONTABANCO AS CONTABANCO
                    FROM ContasAPagar 
                    JOIN FORNECEDOR on FORNEID = CNAPAFORNECEDOR";
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
                        $print .= "<td class='even'><a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['CODTRANSFREC'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a></td>";
                                   
                    } else if (intval($item['CODTRANSFPAG']) > 0) {
                        $print .= "<td class='even'><a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['CODTRANSFPAG'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a></td>";

                    } else if ($item['TIPO'] === 'R'){
                        $print .= "<td class='even'><a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['ID'].", \"R\", \"edita\");'>" . $item['HISTORICO'] . "</a></td>";

                    } else if ($item['TIPO'] === 'P') {
                        $print .= "<td class='even'><a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['ID'].", \"P\", \"edita\");'>" . $item['HISTORICO'] . "</a></td>";
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
                    $print .= '
                    <td class="even d-flex flex-row justify-content-around align-content-center" style="text-align: center">
                        <div class="list-icons">
                            <div class="list-icons list-icons-extended"> ';

                                //BOTAO EDITAR
                                if (intval($item["CODTRANSFREC"]) > 0){
                                    //$print .= '<a href="movimentacaoFinanceiraTransferencia.php?lancamentoId=' . $item["CODTRANSFREC"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                    $print .= ' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["CODTRANSFREC"].', \'T\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                } else if (intval($item["CODTRANSFPAG"]) > 0) {
                                    //$print .= '<a href="movimentacaoFinanceiraTransferencia.php?lancamentoId=' . $item["CODTRANSFPAG"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                    $print .= ' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["CODTRANSFPAG"].', \'T\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                } else if ($item["TIPO"] === "R"){
                                    //$print .= '<a href="movimentacaoFinanceiraRecebimento.php?lancamentoId=' . $item["ID"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                    $print .= ' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'R\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                } else if ($item["TIPO"] === "P") {
                                    //$print .= '<a href="movimentacaoFinanceiraPagamento.php?lancamentoId=' . $item["ID"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                    $print .= ' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'P\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                                }
                                
                                //BOTAO EXCLUIR
                                if (intval($item["CODTRANSFREC"]) > 0){
                                    $print .= 
                                       // '<a href="#" idContaExcluir="' . $item["CODTRANSFREC"] . '" tipo="T" class="list-icons-item excluirConta"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>';
                                        '<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["CODTRANSFREC"].', \'T\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>';
            
                                } else if (intval($item["CODTRANSFPAG"]) > 0) {
                                    $print .= 
                                       // '<a href="#" idContaExcluir="' . $item["CODTRANSFPAG"] . '" tipo="T" class="list-icons-item excluirConta"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>';
                                        '<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["CODTRANSFPAG"].', \'T\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>';
                                    } else if  ($item["TIPO"] === "R"){
                                        $print .= 
                                          // '<a href="#" idContaExcluir="' . $item["ID"] . '" tipo="' . $item["TIPO"] . '" class="list-icons-item excluirConta"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>';
                                        '<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["ID"].', \'R\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>';
                                    } else if ($item["TIPO"] === "P") {
                                        $print .= 
                                        // '<a href="#" idContaExcluir="' . $item["ID"] . '" tipo="' . $item["TIPO"] . '" class="list-icons-item excluirConta"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>';
                                        '<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["ID"].', \'P\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>';
                                    }
                                $print .= '
                            </div>
                        </div>
                    </td>
                </tr>
            ';
            print($print);
            */

            //HISTÓRICO
            if (intval($item['CODTRANSFREC']) > 0){
                $historico = $_POST["permissionAtualiza"]?"<a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['CODTRANSFREC'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a>":"";
                           
            } else if (intval($item['CODTRANSFPAG']) > 0) {
                $historico = $_POST["permissionAtualiza"]?"<a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['CODTRANSFPAG'].", \"T\", \"edita\");'>" . $item['HISTORICO'] . "</a>":"";

            } else if ($item['TIPO'] === 'R'){
                $historico = $_POST["permissionAtualiza"]?"<a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['ID'].", \"R\", \"edita\");'>" . $item['HISTORICO'] . "</a>":"";

            } else if ($item['TIPO'] === 'P') {
                $historico = $_POST["permissionAtualiza"]?"<a href='#' onclick='atualizaMovimentacaoFinanceira(".$_POST["permissionAtualiza"].",".$item['ID'].", \"P\", \"edita\");'>" . $item['HISTORICO'] . "</a>":"";
            }

            $ClienteOuFornecedor = (isset($item['CLIENTE'])) ? $item['CLIENTE'] : $item['FORNECEDOR'];

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
                $ContaBanco = $result->fetch(PDO::FETCH_ASSOC);

                $contaCaixa = $ContaBanco['CnBanNome'];
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
            
            //MENU EDITAR E EXCLUIR
            $acoes = '
            <div class="list-icons">
                <div class="list-icons list-icons-extended"> ';

                    //BOTAO EDITAR
                    /*
                    if (intval($item["CODTRANSFREC"]) > 0){
                        //$acoes .= '<a href="movimentacaoFinanceiraTransferencia.php?lancamentoId=' . $item["CODTRANSFREC"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                        $acoes .= $_POST['permissionAtualiza']?' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["CODTRANSFREC"].', \'T\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>':"";
                    } else if (intval($item["CODTRANSFPAG"]) > 0) {
                        //$acoes .= '<a href="movimentacaoFinanceiraTransferencia.php?lancamentoId=' . $item["CODTRANSFPAG"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                        $acoes .= $_POST['permissionAtualiza']?' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["CODTRANSFPAG"].', \'T\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>':"";
                    } else if ($item["TIPO"] === "R"){
                        //$acoes .= '<a href="movimentacaoFinanceiraRecebimento.php?lancamentoId=' . $item["ID"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                        $acoes .= $_POST['permissionAtualiza']?' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'R\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>':"";
                    } else if ($item["TIPO"] === "P") {
                        //$acoes .= '<a href="movimentacaoFinanceiraPagamento.php?lancamentoId=' . $item["ID"] . '" class="list-icons-item editarLancamento"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>';
                        $acoes .= $_POST['permissionAtualiza']?' <a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'P\', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>':"";
                    }*/
                    
                    //BOTAO EXCLUIR
                    if (intval($item["CODTRANSFREC"]) > 0){
                        $acoes .= $_POST['permissionExclui']?'<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["CODTRANSFREC"].', \'T\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>':"";
                    } else if (intval($item["CODTRANSFPAG"]) > 0) {
                        $acoes .= $_POST['permissionExclui']?'<a href="#" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionExclui'].','.$item["CODTRANSFPAG"].', \'T\', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>':"";
                    } else if  ($item["TIPO"] === "R"){
                        $acoes .= $_POST['permissionExclui']?'<a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'R\', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar Conta"><i class="icon-undo2"></i></a>':"";
                    } else if ($item["TIPO"] === "P") {
                        $acoes .= $_POST['permissionExclui']?'<a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaMovimentacaoFinanceira('.$_POST['permissionAtualiza'].','.$item["ID"].', \'P\', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar Conta"><i class="icon-undo2"></i></a>':"";
                    }
                    $acoes .= '
                </div>
            </div>';

            $array = [
                'data'=>[
                    isset($data) ? $data : null, 
                    isset($historico) ? $historico : null, 
                    isset($ClienteOuFornecedor) ? $ClienteOuFornecedor : null,
                    isset($contaCaixa) ? $contaCaixa : null, 
                    isset($numeroDocucmento) ? $numeroDocucmento : null, 
                    isset($entrada) ? $entrada : null, 
                    isset($saida) ? $saida : null, 
                    isset($colunaSaldo) ? $colunaSaldo : null, 
                    isset($acoes) ? $acoes : null
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