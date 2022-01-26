<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

//Verifica se já existe saldo 

    $sql = " SELECT FOXPrFluxoOperacional as FluxoId, ProduId as ProdutoId, tipo = 'P'
            FROM FluxoOperacionalXProduto
            JOIN FluxoOperacional on FlOpeId = FOXPrFluxoOperacional
            JOIN Produto on ProduId = FOXPrProduto
            WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $_POST['IdFlOpe'] . "
            UNION
            SELECT FOXSrFluxoOperacional as FluxoId, ServiId as ServicoId, tipo = 'S'
            FROM FluxoOperacionalXServico
            JOIN FluxoOperacional on FlOpeId = FOXSrFluxoOperacional
            JOIN Servico on ServiId = FOXSrServico
            WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $_POST['IdFlOpe'] . "
    ";
    $result = $conn->query($sql);
    $rowProdutoServico = $result->fetchAll(PDO::FETCH_ASSOC);
  
    foreach ($rowProdutoServico as $item) {

        if ($item['tipo'] == 'P') {

            $sql = "SELECT dbo.fnItensRestantesContrato( " . $item['FluxoId'] . " , " .$_SESSION['UnidadeId']. " , FOXPrProduto , 'P' ) as ItensRestantesContrato 
                    FROM FluxoOperacionalXProduto
                    WHERE FOXPrFluxoOperacional = " . $_POST['IdFlOpe'] . "
            ";
            $result = $conn->query($sql);
            $saldo = $result->fetch(PDO::FETCH_ASSOC);

            if ($saldo['ItensRestantesContrato'] > 0) {
                $saldosPositivos++;
            }
        } else {

            $sql = "SELECT dbo.fnItensRestantesContrato( " . $item['FluxoId'] . " , ".$_SESSION['UnidadeId']. " ,  FOXSrServico , 'S' ) as ItensRestantesContrato
                    FROM FluxoOperacionalXServico
                    WHERE FOXSrFluxoOperacional = " . $_POST['IdFlOpe'] . "
            ";
            $result = $conn->query($sql);
            $saldo = $result->fetch(PDO::FETCH_ASSOC);

            if ($saldo['ItensRestantesContrato'] > 0) {
                $saldosPositivos++;
            }
        }
    }

 //Verifica se já existe saldo (se existir, retorna true )

    if ($saldosPositivos >= 1) {
        echo 1;
    } else{
        echo 0;
    }

        
    
