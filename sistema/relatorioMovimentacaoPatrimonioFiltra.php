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





function queryPesquisa(){

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = []; 

    if (!empty($_POST['inputDataDe']) || !empty($_POST['inputDataAte'])) {
        empty($_POST['inputDataDe']) ? $inputDataDe = '1900-01-01' : $inputDataDe = $_POST['inputDataDe'];
        empty($_POST['inputDataAte']) ? $inputDataAte = '2100-01-01' : $inputDataAte = $_POST['inputDataAte'];

        //$args[]  = "MovimData = ".$inputDataDe." ";MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."'
        //$args[] = "`dataAte` = ".$inputDataAte." ";

        $args[]  = "MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."' ";
    }

    if(!empty($_POST['inputLocalEstoque'])){
        $args[]  = "MovimDestinoLocal = ".$_POST['inputLocalEstoque']." ";
    }

    if(!empty($_POST['inputSetor'])){
        $args[]  = "MovimDestinoSetor = ".$_POST['inputSetor']." ";
    }

    if(!empty($_POST['inputCategoria'])){
        $args[]  = "ProduCategoria = ".$_POST['inputCategoria']." ";
    }

    if(!empty($_POST['inputSubCategoria'])){
        $args[]  = "ProduSubCategoria = ".$_POST['inputSubCategoria']." ";
    }

    if(!empty($_POST['inputProduto'])){
        $args[]  = "ProduNome LIKE '%".$_POST['inputProduto']."%' ";
    }


    if (count($args) >= 1) {
        try {

            $string = implode( " and ",$args );

            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, LcEstNome, MovimDestinoSetor, MvXPrValidade, MvXPrValidade, ProduNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    WHERE ".$string." and ProduEmpresa = ".$_SESSION['EmpreId']."
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
                   <td>" . $cont . "</td>
                   <td>" . $item['ProduNome'] . "</td>
                   <td></td>
                   <td>" . $item['MovimNotaFiscal'] . "</td>
                   <td></td>
                   <td>" . $item['MovimNotaFiscal'] . "</td>
                   <td>" . mostraData($item['MvXPrValidade']) . "</td>
                   <td>" . $item['LcEstNome'] . "</td>
                   <td>" . $item['MovimDestinoSetor'] . "</td>
                   <td>" . count($result) . "</td>
                </tr>
             ");
        }
        
    }
}

queryPesquisa();
