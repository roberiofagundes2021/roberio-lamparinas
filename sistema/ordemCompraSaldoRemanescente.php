<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

//Verifica se já existe saldo 

    $sql = " SELECT SUM(dbo.fnItensRestantesContrato( " . $_POST['IdFlOpe'] . " , " .$_SESSION['UnidadeId']. " , FOXPrProduto , 'P' )) as ItensRestantesContrato 
            FROM FluxoOperacionalXProduto
            JOIN FluxoOperacional on FlOpeId = FOXPrFluxoOperacional
            JOIN Produto on ProduId = FOXPrProduto
            WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $_POST['IdFlOpe'] . "        
        ";
    $result = $conn->query($sql);
    $rowProduto = $result->fetch(PDO::FETCH_ASSOC);

    $sql = " SELECT SUM(dbo.fnItensRestantesContrato( " . $_POST['IdFlOpe'] . " , ".$_SESSION['UnidadeId']. " ,  FOXSrServico , 'S' )) as ItensRestantesContrato
            FROM FluxoOperacionalXServico
            JOIN FluxoOperacional on FlOpeId = FOXSrFluxoOperacional
            JOIN Servico on ServiId = FOXSrServico
            WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $_POST['IdFlOpe'] . "
        ";
    $result = $conn->query($sql);
    $rowServico = $result->fetch(PDO::FETCH_ASSOC);

    $rowSoma = $rowProduto['ItensRestantesContrato'] + $rowServico['ItensRestantesContrato'];
  
   

 //Verifica se já existe saldo (se existir, retorna true )

    if ($rowSoma >= 1) {
        echo 1;
    } else{
        echo 0;
    }

        
    
