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

    include('global_assets/php/conexao.php');

    $rowData = [];
    $rowSetor = [];
    $rowCategoria = [];
    $rowSubCategoria = [];
    $rowProduto = [];

    $args = []; 
    $argsData = [];

    $cont = 0;

  /*if (isset($_POST['inputDataDe']) || isset($_POST['inputDataAte'])) {
        $_POST['inputDataDe'] ? $inputDataDe = $_POST['inputDataDe'] : $inputDataDe = '1900-01-01';
        $_POST['inputDataAte'] ? $inputDataAte = $_POST['inputDataAte'] : $inputDataDe = '2100-01-01';

        $argsData[]  = "`dataDe` = '{$inputDataDe}'";
        $argsData[] = "`dataAte` = '{$inputDataAte}'";
    }*/

    if(isset($_POST['inputSetor'])){
        $args[]  = "MovimDestinoSetor = {$_POST['inputSetor']}";
    }
    if(isset($_POST['inputCategoria'])){
        $args[]  = "ProduCategoria = {$_POST['inputCategoria']}";
    }
    if(isset($_POST['inputSubCategoria'])){
        $args[]  = "ProduSubCategoria = {$_POST['inputSubCategoria']}";
    }

    if(isset($_POST['inputProduto'])){
        $args[]  = "ProduNome = '{$_POST['inputProduto']}'";
    }






    if (count($args) >= 1 || count($argsData) >= 1) {
        try {

            $string = implode( ' AND ',$args );

            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade, ProduNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    WHERE ".$string."
                    ";
            /*MovimData BETWEEN '" . $inputDataDe . "' and '" . $inputDataAte . "' and*/
            $result = $conn->query("$sql");
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            //if (count($rowData) >= 1) {
                //$cont += 1;
            //}
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /*if($inputEstoqueLocal){
        try{
           
            $sql = "SELECT MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    WHERE MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."'
                    ";
            $result = $conn->query("$sql");
            $row = $result->fetchAll(PDO::FETCH_ASSOC);	
    
        } catch(PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['inputSetor'])) {
        try {
            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade, ProduNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    WHERE MovimDestinoSetor = " . $_POST['inputSetor'] . "
                    ";
            $result = $conn->query("$sql");
            $rowSetor = $result->fetchAll(PDO::FETCH_ASSOC);

            if (count($rowSetor) >= 1) {
                $cont += 1;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['inputCategoria'])) {
        try {
            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade, ProduNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto 
                    WHERE ProduCategoria = " . $_POST['inputCategoria'] . " 
                    ";
            $result = $conn->query("$sql");
            $rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

            if (count($rowCategoria) >= 1) {
                $cont += 1;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['inputSubCategoria'])) {
        try {
            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade, ProduNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto 
                    WHERE ProduSubCategoria = " . $_POST['inputSubCategoria'] . " 
                    ";
            $result = $conn->query("$sql");
            $rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

            if (count($rowSubCategoria) >= 1) {
                $cont += 1;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['inputProduto'])) {
        $produto = $_POST['inputProduto'];
        try {
            $sql = "SELECT MvXPrId,MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade, ProduNome
                    FROM Movimentacao 
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId 
                    JOIN Produto on ProduId = MvXPrProduto 
                    WHERE ProduNome LIKE '%$produto%'
                    ";
            $result = $conn->query("$sql");
            $rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

            if($rowProduto < 1){
               unset($rowProduto);
            }

            if (count($rowProduto) >= 1) {
                $cont += 1;
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }*/

    //if ($cont >= 1) {
       // unset($result);
       // $result = array_merge($rowData + $rowSetor + $rowCategoria + $rowSubCategoria);
       // $resultFinal = array_unique($result, SORT_REGULAR);

        foreach ($rowData as $item) {
            print("
                <tr>
                   <td>" . $item['MvXPrId'] . "</td>
                   <td>" . $item['MovimId'] . "</td>
                   <td>" . $item['ProduNome'] . "</td>
                   <td>" . $item['MovimNotaFiscal'] . "</td>
                   <td>" . $item['MovimOrigem'] . "</td>
                   <td>" . $item['MovimDestinoLocal'] . "</td>
                   <td>" . $item['MvXPrValidade'] . "</td>
                   <td>" . count($result) . "</td>
                </tr>
             ");
        }
        
  // } else {
      //  print('Nada emcontrado...');
   // }
}

queryPesquisa();

/*function renderResults($result){
    
}

function pesquisa(){
    $result = queryPesquisa();
    if($result >= 1){
        renderResults($result);
    } else {
        print('Nada emcontrado...');
    }
}*/
