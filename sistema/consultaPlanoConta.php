<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$planoConta1 = $_POST['inputPlanoConta1'];
$data1 = $_POST['inputData1'];
$data2 = $_POST['inputData2'];
$data3 = $_POST['inputData3'];
$data4 = $_POST['inputData4'];

$sql = "SELECT PlConId, PlConCodigo, PlConNome, PlConNatureza,
               dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data1."', '".$data1."', PlConNatureza)  as Previsto,
               dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data1."', '".$data1."', PlConNatureza)  as Realizado,
               dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data2."', '".$data2."', PlConNatureza)  as Previsto2,
               dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data2."', '".$data2."', PlConNatureza)  as Realizado2,
               dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data3."', '".$data3."', PlConNatureza)  as Previsto3,
               dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data3."', '".$data3."', PlConNatureza)  as Realizado3,
               dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data4."', '".$data4."', PlConNatureza)  as Previsto4,
               dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$data4."', '".$data4."', PlConNatureza)  as Realizado4
        FROM PlanoConta
        JOIN Situacao on SituaId = PlConStatus
        WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and PlConPlanoContaPai = $planoConta1
        ORDER BY PlConCodigo ASC";
$result = $conn->query($sql);
$rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

$array[0] = $rowPlanoContas;
$array[1] = $data2; 
$array[2] = $data3;
$array[3] = $data4;

print(json_encode($array));
?>