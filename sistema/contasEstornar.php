<?php
include_once("sessao.php");

include('global_assets/php/conexao.php');

$justificativa = $_POST['inputContaJustificativa'];

if(isset($_POST['inputContasAPagarId'])) {
    $contaApagarId = $_POST['inputContasAPagarId'];
    $situacao = 11; //Status a pagar 

    try {
        $sql = "UPDATE ContasAPagar SET CnAPaStatus = :iStatus, CnAPaJustificativaEstorno = :sJustificativa
                WHERE CnAPaId = $contaApagarId";
        $result = $conn->prepare($sql);

        $result->execute(array(
            ':iStatus' => $situacao,
            ':sJustificativa' => $justificativa
        ));


        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Conta estornada!!!";
        $_SESSION['msg']['tipo'] = "success";
    } catch (PDOException $e) {

        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro ao estornar conta!!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("contasAPagar.php");
}else if(isset($_POST['inputContasAReceberId'])){
    $contaReceberId = $_POST['inputContasAReceberId'];
    $situacao = 13; //Status a receber 

    try {
        $sql = "UPDATE ContasAReceber SET CnAReStatus = :iStatus, CnAReJustificativaEstorno = :sJustificativa
                WHERE CnAReId = $contaReceberId";
        $result = $conn->prepare($sql);

        $result->execute(array(
            ':iStatus' => $situacao,
            ':sJustificativa' => $justificativa
        ));


        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Conta estornada!!!";
        $_SESSION['msg']['tipo'] = "success";
    } catch (PDOException $e) {

        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro ao estornar conta!!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
    }

    irpara("contasAReceber.php");
}else {
    $contaId = $_POST['inputMovimentacaoFinanceiraId'];
    $tipoMovimentacao = $_POST['tipoMov'];
    
    if($tipoMovimentacao == 'P') {
        $situacao = 11; //Status a pagar 

        try {
            $sql = "UPDATE ContasAPagar SET CnAPaStatus = :iStatus, CnAPaJustificativaEstorno = :sJustificativa
                    WHERE CnAPaId = $contaId";
            $result = $conn->prepare($sql);
    
            $result->execute(array(
                ':iStatus' => $situacao,
                ':sJustificativa' => $justificativa
            ));
    
    
            $_SESSION['msg']['titulo'] = "Sucesso";
            $_SESSION['msg']['mensagem'] = "Conta estornada!!!";
            $_SESSION['msg']['tipo'] = "success";
        } catch (PDOException $e) {
    
            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Erro ao estornar conta!!!";
            $_SESSION['msg']['tipo'] = "error";
    
            echo 'Error: ' . $e->getMessage();
        }
    }else if($tipoMovimentacao == 'R') {
        $situacao = 13; //Status a receber

        try {
            $sql = "UPDATE ContasAReceber SET CnAReStatus = :iStatus, CnAReJustificativaEstorno = :sJustificativa
                    WHERE CnAReId = $contaId";
            $result = $conn->prepare($sql);
    
            $result->execute(array(
                ':iStatus' => $situacao,
                ':sJustificativa' => $justificativa
            ));
    
    
            $_SESSION['msg']['titulo'] = "Sucesso";
            $_SESSION['msg']['mensagem'] = "Conta estornada!!!";
            $_SESSION['msg']['tipo'] = "success";
        } catch (PDOException $e) {
    
            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Erro ao estornar conta!!!";
            $_SESSION['msg']['tipo'] = "error";
    
            echo 'Error: ' . $e->getMessage();
        }
    }

    irpara("movimentacaoFinanceira.php");
}
?>