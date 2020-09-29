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

    if (!empty($_POST['inputNumeroDocumento'])) {
        $args[]  = "CnAPaNumeroDocumento = '" . $_POST['cmbNumeroDocumento'] . "' ";
    }

    if (!empty($_POST['cmbFornecedor'])) {
        $args[]  = "CnAPaFornecedor = " . $_POST['cmbFornecedor'] . " ";
    }

    if (!empty($_POST['cmbPlanoContas'])) {
        $args[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
    }

    // if (!empty($_POST['cmbSubCategoria']) && $_POST['cmbSubCategoria'] != "Sem Subcategoria" && $_POST['cmbSubCategoria'] != "Filtrando...") {
    //     $args[]  = "ProduSubCategoria = " . $_POST['cmbSubCategoria'] . " ";
    // }

    // if (!empty($_POST['cmbCodigo'])) {
    //     $args[]  = "ProduCodigo = " . $_POST['cmbCodigo'] . " ";
    // }

    if (!empty($_POST['cmbStatus'])) {
        $args[]  = "CnApaStatus = " . $_POST['cmbStatus'] . " ";
    }

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
    // print($sql);

    if ($cont == 1) {
        $cont = 0;
        foreach ($rowData as $item) {
            $cont++;     

            print("
            
            <tr>
                <td class='even'>
                    <input type='checkbox'>
                    <input type='hidden' value='".$item['CnAPaId']."'>
                </td>
                <td class='even'>" . mostraData($item['CnAPaDtVencimento']) . "</td>
                <td class='even' style='text-align: center'>" . $item['ForneNome'] . "</td>
                <td class='even' style='text-align: center'>" . $item['CnAPaPlanoContas'] . "</td>
                <td class='even' style='text-align: center'>" . $item['CnAPaNumDocumento'] . "</td>
                <td class='even' style='text-align: center'>" . $item['CnAPaValorAPagar'] . "</td>
                <td class='even' style='text-align: center'></td>
                <td class='even' style='text-align: center'>" . $item['CnAPaStatus'] . "</td>
                <td class='even d-flex flex-row justify-content-around align-content-center' style='text-align: center'>
                    <a href='#' class='list-icons-item editarLancamento'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-pencil7'></i></a>
                    <a href='#' class='list-icons-item'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-bin'></i></a>
                    <div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right' style='max-width: 20px'>
															
						</div>
                    </div>
                    <a href='#' class='list-icons-item btnParcelar'  data-popup='tooltip' data-placement='bottom' title='Parcelar'><i class='icon-file-text2'></i></a>
                    <a href='#' class='list-icons-item'  data-popup='tooltip' data-placement='bottom' title='Excluir Produto'><i class='icon-file-empty'></i></a>
                </td>
            </tr>
            ");
        }
    }
}

queryPesquisa();
