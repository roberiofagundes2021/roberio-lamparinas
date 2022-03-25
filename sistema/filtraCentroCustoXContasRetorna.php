<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$centros = $_POST['centroCustos']?$_POST['centroCustos']:null;
$conta = $_POST['conta'];

$array = '(';

for($x=0; $x < COUNT($centros); $x++){
    $array .= "".$centros[$x];
    if ($x < (COUNT($centros)-1)) {
        $array .= ',';
    }
}

$array .= ')';

$sqlCentroCusto = "SELECT CAPXCValor, CnCusId, CnCusCodigo, CnCusNome, CnCusDetalhamento, CnCusStatus, SituaChave
                FROM  ContasAPagarXCentroCusto 
                JOIN CentroCusto on CnCusId = CAPXCCentroCusto
                JOIN Situacao on SituaId = CnCusStatus
                WHERE CAPXCUnidade = ".$_SESSION['UnidadeId']." and SituaChave = 'ATIVO' and CAPXCContasAPagar = $conta and CAPXCCentroCusto in ".$array;

$resultCentroCusto = $conn->query($sqlCentroCusto);
$CentroCustos = $resultCentroCusto->fetchAll(PDO::FETCH_ASSOC);

$count = COUNT($CentroCustos);
//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo (json_encode($CentroCustos));
} else{
	echo (0);
}

?>
