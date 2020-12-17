<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_GET['idOrcamento'])){
	$sql = "SELECT CategId, CategNome
						FROM Categoria
						JOIN Orcamento on OrcamCategoria = CategId
						WHERE CategUnidade = ".$_SESSION['UnidadeId']." and CategStatus = 1 and OrcamId = ".$_GET['idOrcamento']."
						ORDER BY CategNome ASC";
} else if (isset($_GET['idFornecedor']) && $_GET['idFornecedor'] != -1){
	$sql = "SELECT CategId, CategNome
						FROM Categoria
						JOIN Fornecedor on ForneCategoria = CategId
						WHERE CategUnidade = ".$_SESSION['UnidadeId']." and ForneId = '". $_GET['idFornecedor']."' and CategStatus = 1
						ORDER BY CategNome ASC";
} else{
	$sql = "SELECT CategId, CategNome
					FROM Categoria
					WHERE CategUnidade = ".$_SESSION['UnidadeId']." and CategStatus = 1
					ORDER BY CategNome ASC";
}

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo json_encode($row);
} else{
	echo 0;
}
