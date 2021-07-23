<?php

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sqlNumero = "SELECT Max(CAST(OrComNumero AS int))
							FROM OrdemCompra where OrComUnidade = ".$_SESSION['UnidadeId'];
$resultNumero = $conn->query($sqlNumero);
$numero = $resultNumero->fetch(PDO::FETCH_ASSOC);

echo json_encode(intval($numero[""])+1);
?>
