<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$planoConta = $_POST['inputPlanoConta1'];
$data1 = $_POST['inputData1'];
$data2 = $_POST['inputData2'];
$data3 = $_POST['inputData3'];

$sql = "SELECT DISTINCT CnCusId, CnCusNome, 
        dbo.fnCentroCustoPrevisto(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data1."', '".$data1."', 'S')  as Previsto,
        dbo.fnCentroCustoRealizado(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data1."', '".$data1."', 'S')  as Realizado,
        dbo.fnCentroCustoPrevisto(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data2."', '".$data2."', 'S')  as Previsto2,
        dbo.fnCentroCustoRealizado(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data2."', '".$data2."', 'S')  as Realizado2,
        dbo.fnCentroCustoPrevisto(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data3."', '".$data3."', 'S')  as Previsto3,
        dbo.fnCentroCustoRealizado(".$_SESSION['UnidadeId'].", CnCusId, ".$planoConta.", '".$data3."', '".$data3."', 'S')  as Realizado3
        FROM CentroCusto
        JOIN ContasAPagarXCentroCusto ON CAPXCCentroCusto = CnCusId
        JOIN ContasAPagar ON CnAPaId = CAPXCContasAPagar
        WHERE CnAPaPlanoContas = $planoConta AND CAPXCUnidade = $_SESSION[UnidadeId]
        ORDER BY CnCusNome ASC";
$result = $conn->query($sql);
$rowCentroCusto = $result->fetchAll(PDO::FETCH_ASSOC);

$array[0] = $rowCentroCusto;
$array[1] = $data2; 
$array[2] = $data3;

print(json_encode($array));
?>