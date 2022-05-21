<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$planoConta1 = $_POST['inputPlanoConta1'];

$sql = "SELECT PlConId, PlConCodigo, PlConNome
        FROM PlanoConta
        JOIN Situacao on SituaId = PlConStatus
        WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and PlConPlanoContaPai = $planoConta1
        ORDER BY PlConCodigo ASC";
$result = $conn->query($sql);
$rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

print(json_encode($rowPlanoContas));
?>