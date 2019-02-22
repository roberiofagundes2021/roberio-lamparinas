<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = ("SELECT ProduId, ProduNome, UnidaSigla
		 FROM Produto
		 JOIN Unidade on UnidaId = ProduUnidade
		 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduCategoria = '". $_GET['idCategoria']."' and ProduStatus = 1");

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
