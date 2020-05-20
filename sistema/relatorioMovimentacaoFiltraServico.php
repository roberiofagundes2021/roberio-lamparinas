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
//$inputServico = $_POST['inputServico'];





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

    if (!empty($_POST['inputTipo'])) {
        $args[]  = "MovimTipo = '" . $_POST['inputTipo'] . "' ";
    }

    if (!empty($_POST['inputFornecedor'])) {
        $args[]  = "MovimFornecedor = " . $_POST['inputFornecedor'] . " ";
    }

    if (!empty($_POST['inputCategoria'])) {
        $args[]  = "ServiCategoria = " . $_POST['inputCategoria'] . " ";
    }

    if (!empty($_POST['inputSubCategoria'])) {
        $args[]  = "ServiSubCategoria = " . $_POST['inputSubCategoria'] . " ";
    }

    if (!empty($_POST['inputCodigo'])) {
        $args[]  = "ServiCodigo = " . $_POST['inputCodigo'] . " ";
    }

    if (!empty($_POST['inputServico'])) {
        $args[]  = "ServiId = '" . $_POST['inputServico'] . "'";
    }

    if ($_POST['inputTipo'] == 'E') {


        if (count($args) >= 1) {
            try {

                $string = implode(" and ", $args);

                if ($string != '') {
                    $string .= ' and ';
                }

                $sql = "SELECT MvXSrId, MovimId ,MovimData, MovimNotaFiscal, MovimTipo, MovimOrigemLocal, LcEstNome, MovimDestinoSetor,  MvXSrValorUnitario, MvXSrQuantidade, CategNome, ServiNome, SetorNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXServico on MvXSrMovimentacao = MovimId
                    JOIN Servico on ServiId = MvXSrServico
                    JOIN Categoria on CategId = ServiCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ServiEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
                print($sql);
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
                    <td class='odd'>" . $item['ServiNome'] . "</td>
                    <td class='even'>" . $item['CategNome'] . "</td>
                    <td class='odd'>" . $item['ForneNome'] . "</td>
                    <td class='odd' style='text-align: center'>" . $item['MvXSrQuantidade'] . "</td>
                    <td class='odd'></td>
                    <td class='even'>" . $item['LcEstNome'] . "</td>
                </tr>
             ");
            }
        }
    } else if ($_POST['inputTipo'] == 'S') {
        if (count($args) >= 1) {
            try {

                $string = implode(" and ", $args);

                if ($string != '') {
                    $string .= ' and ';
                }

                $sql = "SELECT MvXSrId, MovimId ,MovimData, MovimNotaFiscal, MovimTipo, MovimOrigemLocal, LcEstNome, MovimDestinoSetor,  MvXSrValorUnitario, MvXSrQuantidade, CategNome, ServiNome, SetorNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXServico on MvXSrMovimentacao = MovimId
                    JOIN Servico on ServiId = MvXSrServico
                    JOIN Categoria on CategId = ServiCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ServiEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
                //print($sql);
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
                    <td class='odd'>" . $item['ServiNome'] . "</td>
                    <td class='even'>" . $item['CategNome'] . "</td>
                    <td class='odd'>" . $item['ForneNome'] . "</td>
                    <td class='odd' style='text-align: center'>" . $item['MvXSrQuantidade'] . "</td>
                    <td class='odd'>" . $item['LcEstNome'] . "</td>
                    <td class='even'>" . $item['LcEstNome'] . "</td>
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

                $sql = "SELECT MvXSrId, MovimId ,MovimData, MovimNotaFiscal, MovimTipo, MovimOrigemLocal, LcEstNome, MovimDestinoSetor,  MvXSrValorUnitario, MvXSrQuantidade, CategNome, ServiNome, SetorNome, ForneNome
                    FROM Movimentacao
                    JOIN MovimentacaoXServico on MvXSrMovimentacao = MovimId
                    JOIN Servico on ServiId = MvXSrServico
                    JOIN Categoria on CategId = ServiCategoria
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    JOIN Fornecedor on ForneId = MovimFornecedor
                    WHERE " . $string . " ServiEmpresa = " . $_SESSION['EmpreId'] . "
                    ";
                $result = $conn->query("$sql");
                $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

                count($rowData) >= 1 ? $cont = 1 : $cont = 0;
                print($sql);
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
                    <td class='odd'>" . $item['ServiNome'] . "</td>
                    <td class='even'>" . $item['CategNome'] . "</td>
                    <td class='odd'>" . $item['ForneNome'] . "</td>
                    <td class='odd' style='text-align: center'>" . $item['MvXSrQuantidade'] . "</td>
                    <td class='odd'>" . $item['LcEstNome'] . "</td>
                    <td class='even'>" . $item['SetorNome'] . "</td>
                </tr>
             ");
            }
        }
    }
}

queryPesquisa();
