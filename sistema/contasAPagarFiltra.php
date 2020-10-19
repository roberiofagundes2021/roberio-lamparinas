<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];

    if (!empty($_POST['inputPeriodoDe']) || !empty($_POST['inputAte'])) {
        empty($_POST['inputPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_POST['inputPeriodoDe'];
        empty($_POST['inputAte']) ? $inputAte = '2100-01-01' : $inputAte = $_POST['inputAte'];

        $args[]  = "CnAPaDtPagamento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
    }

    if (!empty($_POST['cmbFornecedor'])) {
        $args[]  = "CnAPaFornecedor = " . $_POST['cmbFornecedor'] . " ";
    }

    if (!empty($_POST['cmbPlanoContas'])) {
        $args[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
    }

    if (!empty($_POST['cmbStatus'])) {
        $args[]  = "CnApaStatus = " . $_POST['cmbStatus'] . " ";
    }

    if($_POST['tipoFiltro'] == 'FiltroNormal')
    {
        if (count($args) >= 1) {

            $string = implode(" and ", $args);
    
            if ($string != '') {
                $string .= ' and ';
            }
    
            $sql = "SELECT * 
                    FROM ContasAPagar
                    LEFT JOIN Fornecedor on ForneId = CnAPaFornecedor
                    JOIN Situacao on SituaId = CnApaStatus
                    WHERE " . $string . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
                ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
    } else {
        $sql = "SELECT * 
                FROM ContasAPagar
                LEFT JOIN Fornecedor on ForneId = CnAPaFornecedor
                JOIN Situacao on SituaId = CnApaStatus
                WHERE CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
        ";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }

    if ($cont == 1) {
        $cont = 0;
        print('<input type="hidden" id="elementosGrid" value="'.count($rowData).'">');
        foreach ($rowData as $item) {
            $cont++;     

            $status = $item['CnAPaStatus'] == 11 ? 'À Pagar' : 'Paga';

            print("
            
            <tr>
                <td class='even'>
                    <input type='checkbox' id='check".$cont."'>
                    <input type='hidden' value='".$item['CnAPaId']."'>
                </td>
                <td class='even'><p class='m-0'>" . mostraData($item['CnAPaDtVencimento']) . "</p><input type='hidden' value='".$item['CnAPaDtVencimento']."'></td>
                <td class='even'>" . $item['CnAPaDescricao'] . "</td>
                <td class='even'>" . $item['ForneNome'] . "</td>
                <td class='even' style='text-align: center'>" . $item['CnAPaNumDocumento'] . "</td>
                <td class='even' style='text-align: center'>" . $item['CnAPaValorAPagar'] . "</td>
                <td class='even' style='text-align: center'>" .$status. "</td>
                <td class='even d-flex flex-row justify-content-around align-content-center' style='text-align: center'>
                <div class='list-icons'>
                    <div class='list-icons list-icons-extended'>
                        <a href='#' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-pencil7'></i></a>
                        <a href='#' class='list-icons-item'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-bin'></i></a>
				        <div class='dropdown'>													
				        	<a href='#' class='list-icons-item' data-toggle='dropdown'>
				        		<i class='icon-menu9'></i>
				    
				        	<div class='dropdown-menu dropdown-menu-right'>
                                <a href='#' class='dropdown-item btnParcelar'  data-popup='tooltip' data-placement='bottom' title='Parcelar'><i class='icon-file-text2'></i> Parcelar</a>
                                <a href='#' class='dropdown-item'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-file-empty'></i></a>
				        	</div>
				        </div>
				    </div>
                   
                    </div>
                </td>
            </tr>
            ");
        }
    }
}

queryPesquisa();