<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if ($_GET['idCategoria'] == '-1'){
	$sql = ("SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
			 FROM Fornecedor
			 WHERE ForneEmpresa = ".$_SESSION['EmpreId']." and ForneStatus = 1
			 ORDER BY ForneNome ASC");	
} else{
	$sql = ("SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
			 FROM Fornecedor
			 JOIN Categoria on CategId = ForneCategoria
			 WHERE ForneEmpresa = ".$_SESSION['EmpreId']." and CategId = '". $_GET['idCategoria']."' and ForneStatus = 1
			 ORDER BY ForneNome ASC");
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
