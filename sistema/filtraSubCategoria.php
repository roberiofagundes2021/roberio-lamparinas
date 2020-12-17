<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_GET['idOrcamento'])){
	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN OrcamentoXSubCategoria on OrXSCSubcategoria = SbCatId
			JOIN Situacao on SituaId = SbCatStatus
			WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and OrXSCOrcamento = '". $_GET['idOrcamento']."' and SituaChave = 'ATIVO' ";
} else if (isset($_GET['idFornecedor']) && $_GET['idFornecedor'] != -1){
	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN FornecedorXSubCategoria on FrXSCSubCategoria = SbCatId
			JOIN Situacao on SituaId = SbCatStatus
			WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and FrXSCFornecedor = '". $_GET['idFornecedor']."' and SituaChave = 'ATIVO' ";
} else if (isset($_GET['produtoServico'])){

	if ($_GET['produtoServico'] == 'S'){
		$sql = "SELECT DISTINCT SbCatId, SbCatNome
		FROM SubCategoria
		JOIN Servico on ServiSubCategoria = SbCatId
		JOIN Situacao on SituaId = SbCatStatus
		WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and SbCatCategoria = '". $_GET['idCategoria']."' and SituaChave = 'ATIVO' ";
	} else {
		$sql = "SELECT DISTINCT SbCatId, SbCatNome
		FROM SubCategoria
		JOIN Produto on ProduSubCategoria = SbCatId
		JOIN Situacao on SituaId = SbCatStatus
		WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and SbCatCategoria = '". $_GET['idCategoria']."' and SituaChave = 'ATIVO' ";
	}

} else {
	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN Situacao on SituaId = SbCatStatus
			WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and SbCatCategoria = '". $_GET['idCategoria']."' and SituaChave = 'ATIVO' ";
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

?>
