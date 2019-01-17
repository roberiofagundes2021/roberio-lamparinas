<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

//Isso aqui é pra corrigir um bug. Já que o correto era vir #
if(isset($_GET['idSubCategoria']) and $_GET['idSubCategoria'] == null) $_GET['idSubCategoria'] = "#";

if (isset($_GET['idSubCategoria']) and $_GET['idSubCategoria'] != "#"){
	$sql = ("SELECT ProduId, ProduNome, ProduValorCusto
			 FROM Produto
			 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduSubCategoria = '". $_GET['idSubCategoria']."'");
} else {
	$sql = ("SELECT ProduId, ProduNome, ProduValorCusto
			 FROM Produto
			 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduCategoria = '". $_GET['idCategoria']."'");
}

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
