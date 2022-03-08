<?php
include_once("sessao.php");

include('global_assets/php/conexao.php');

$justificativa = $_POST['inputContasAPagarJustificativa'];

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
}else {
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
}
?>