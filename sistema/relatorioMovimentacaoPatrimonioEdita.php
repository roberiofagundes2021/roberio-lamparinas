<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

try {
    if (isset($_POST['inputId'])) {
        $sql = " UPDATE Patrimonio SET PatriNumSerie = :sNumeroSerie, PatriEstadoConservacao = :iEstadoConservacao WHERE PatriId = " . $_POST['inputId'] . "";
        $result = $conn->prepare($sql);
        $result->execute(array(
            ':sNumeroSerie' => isset($_POST['inputNumeroSerie']) ? $_POST['inputNumeroSerie'] : null,
            ':iEstadoConservacao' => isset($_POST['cmbEstadoConservacao']) ? $_POST['cmbEstadoConservacao'] : null,
        ));
    }

    print('sucesso');

} catch (PDOException $e) {
    $_SESSION['msg']['titulo'] = "Erro";
    $_SESSION['msg']['mensagem'] = "Erro ao alterar serviço!!!";
    $_SESSION['msg']['tipo'] = "error";

    //$result->debugDumpParams();

    echo 'Error: ' . $e->getMessage();
    exit;
}
