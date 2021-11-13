<?php

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sqlNumero = "SELECT Max(CAST(OrComNumero AS int))
							FROM OrdemCompra where OrComUnidade = ".$_SESSION['UnidadeId'];
$resultNumero = $conn->query($sqlNumero);
$numero = $resultNumero->fetch(PDO::FETCH_ASSOC);

// refatora o numero com 6 casas ex: 26 => 000026
$newNumero = "";
$number = intval($numero[""])+1;
$cont = strlen($number)<6?6-strlen($number):0;

for ($x=0; $x<$cont;$x++){
	$newNumero = $newNumero."0";
}
$newNumero = $newNumero.$number;

echo json_encode($newNumero);
?>
