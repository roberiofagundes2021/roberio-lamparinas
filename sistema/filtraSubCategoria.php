<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = ("SELECT SbCatId, SbCatNome
		 FROM SubCategoria
		 WHERE SbCatEmpresa = ".$_SESSION['EmpreId']." and SbCatCategoria = '". $_GET['idCategoria']."'");

$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo json_encode($row);
} else{
	echo 0;
}

?>
