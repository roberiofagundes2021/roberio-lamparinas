<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

//$inputDataDe = $_POST['inputDataDe'];
//$inputDataAte = $_POST['inputDataAte'];
//$inputSetor = $_POST['inputSetor'];

//$_POST['inputDataDe'] ? $inputDataDe = $_POST['inputDataDe'] : $inputDataDe = '1900-01-01';
//$_POST['inputDataAte'] ? $inputDataAte = $_POST['inputDataAte'] : $inputDataDe = '2100-01-01';
//$inputLocalEstoque = $_POST['inputEstoqueLocal'];
//$inputCategoria = $_POST['inputCategoria'];
//$inputSubCategoria = $_POST['inputSubCategoria'];
//$inputProduto = $_POST['inputProduto'];





function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];

    if (!empty($_POST['inputDataDe']) || !empty($_POST['inputDataAte'])) {
        empty($_POST['inputDataDe']) ? $inputDataDe = '1900-01-01' : $inputDataDe = $_POST['inputDataDe'];
        empty($_POST['inputDataAte']) ? $inputDataAte = '2100-01-01' : $inputDataAte = $_POST['inputDataAte'];

        //$args[]  = "MovimData = ".$inputDataDe." ";MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."'
        //$args[] = "`dataAte` = ".$inputDataAte." ";

        $args[]  = "MovimData BETWEEN '" . $inputDataDe . "' and '" . $inputDataAte . "' ";
    }

    if (!empty($_POST['cmbTipo'])) {
        $args[]  = "MovimTipo = '" . $_POST['cmbTipo'] . "' ";
    }

    if (!empty($_POST['cmbFornecedor'])) {
        $args[]  = "MovimFornecedor = " . $_POST['cmbFornecedor'] . " ";
    }

    if (!empty($_POST['cmbCategoria']) && $_POST['cmbCategoria'] != 'Sem Categoria' && $_POST['cmbCategoria'] != "Filtrando...") {
        $args[]  = "ProduCategoria = " . $_POST['cmbCategoria'] . " ";
    }

    if (!empty($_POST['cmbSubCategoria']) && $_POST['cmbSubCategoria'] != "Sem Subcategoria" && $_POST['cmbSubCategoria'] != "Filtrando...") {
        $args[]  = "ProduSubCategoria = " . $_POST['cmbSubCategoria'] . " ";
    }

    if (!empty($_POST['cmbCodigo'])) {
        $args[]  = "ProduCodigo = " . $_POST['cmbCodigo'] . " ";
    }

    if (!empty($_POST['cmbProduto']) && $_POST['cmbProduto'] != "Sem produto" && $_POST['cmbProduto'] != "Filtrando...") {
        $args[]  = "ProduId = " . $_POST['cmbProduto'] . " ";
    }

    /*if(!empty($_POST['inputProduto'])){
        $args[]  = "ProduNome LIKE '%".$_POST['inputProduto']."%' ";
    }*/

    if ($_POST['cmbTipo'] == 'E') {

        if (count($args) >= 1) {
            try {

                $string = implode(" and ", $args);

                if ($string != '') {
                    $string .= ' and ';
                }

                $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimTipo, MovimNotaFiscal, MovimOrigemLocal, MovimDestinoSetor, MovimFornecedor, LcEstNome, MvXPrValidade, MvXPrQuantidade, MvXPrValorUnitario, MvXPrValidade, ProduNome, SetorNome, CategNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    JOIN Categoria on CategId = ProduCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ProduEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        if ($cont == 1) {
            $cont = 0;
            foreach ($rowData as $item) {
                $cont++;
                print("
                
                <tr>
                   <td class='even'>" . mostraData($item['MovimData']) . "</td>
                   <td class='even' style='text-align: center'>" . $item['MovimTipo'] . "</td>
                   <td class='odd'>" . $item['ProduNome'] . "</td>
                   <td class='even'>" . $item['CategNome'] . "</td>
                   <td class='odd'>" . $item['ForneNome'] . "</td>
                   <td class='odd'>" . $item['MvXPrQuantidade'] . "</td>
                   <td class='odd'></td>
                   <td class='even'>" . $item['LcEstNome'] . "</td>
                </tr>
             ");
            }
        }
    } else if ($_POST['cmbTipo'] == 'S') {
        if (count($args) >= 1) {
            try {

                $string = implode(" and ", $args);

                if ($string != '') {
                    $string .= ' and ';
                }

                $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimTipo, MovimNotaFiscal, MovimOrigemLocal, MovimDestinoSetor, MovimFornecedor, LcEstNome, MvXPrValidade, MvXPrQuantidade, MvXPrValorUnitario, MvXPrValidade, ProduNome, SetorNome, CategNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    JOIN Categoria on CategId = ProduCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ProduEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        if ($cont == 1) {
            $cont = 0;
            foreach ($rowData as $item) {
                $cont++;
                print("
                
                <tr>
                   <td class='even'>" . mostraData($item['MovimData']) . "</td>
                   <td class='even' style='text-align: center'>" . $item['MovimTipo'] . "</td>
                   <td class='odd'>" . $item['ProduNome'] . "</td>
                   <td class='even'>" . $item['CategNome'] . "</td>
                   <td class='odd'>" . $item['ForneNome'] . "</td>
                   <td class='odd' style='text-align: center'>" . $item['MvXPrQuantidade'] . "</td>
                   <td class='odd'>" . $item['LcEstNome'] . "</td>
                   <td class='even'>" . $item['SetorNome'] . "</td>
                </tr>
             ");
            }
        }
    } else {
        if (count($args) >= 1) {
            try {

                $string = implode(" and ", $args);

                if ($string != '') {
                    $string .= ' and ';
                }

                $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimTipo, MovimNotaFiscal, MovimOrigemLocal, MovimDestinoSetor, MovimFornecedor, LcEstNome, MvXPrValidade, MvXPrQuantidade, MvXPrValorUnitario, MvXPrValidade, ProduNome, SetorNome, CategNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    JOIN Categoria on CategId = ProduCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ProduEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        if ($cont == 1) {
            $cont = 0;
            foreach ($rowData as $item) {
                $cont++;
                print("
                
                <tr>
                   <td class='even'>" . mostraData($item['MovimData']) . "</td>
                   <td class='even' style='text-align: center'>" . $item['MovimTipo'] . "</td>
                   <td class='odd'>" . $item['ProduNome'] . "</td>
                   <td class='even'>" . $item['CategNome'] . "</td>
                   <td class='odd'>" . $item['ForneNome'] . "</td>
                   <td class='odd' style='text-align: center'>" . $item['MvXPrQuantidade'] . "</td>
                   <td class='odd'>" . $item['LcEstNome'] . "</td>
                   <td class='even'>" . $item['SetorNome'] . "</td>
                </tr>
             ");
            }
        }
    }
}

queryPesquisa();
