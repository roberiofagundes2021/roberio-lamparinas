<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if ($_GET['idPatriProduto'] == '-1'){
	$sql = "SELECT  ProduId, ProduNome, ProduMarca, ProduFabricante
			FROM Produto
			JOIN Situacao on SituaId = ProduStatus 
			WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave = 'ATIVO'
			ORDER BY ProduNome ASC";

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
