<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');


if (isset($_GET['idCentroCusto']) && $_GET['idCentroCusto'] != ''){
	$sql = "SELECT PlConId, PlConCodigo, PlConNome
			FROM PlanoContas
			JOIN Situacao on SituaId = PlConStatus
			WHERE PlConUnidade = ".$_SESSION['UnidadeId']." and PlConCentroCusto = ". $_GET['idCentroCusto']." and SituaChave = 'ATIVO'";
} else {
	$sql = "SELECT PlConId, PlConCodigo, PlConNome
			FROM PlanoContas
			JOIN Situacao on SituaId = PlConStatus
			WHERE PlConUnidade = ".$_SESSION['UnidadeId']."  and SituaChave = 'ATIVO'";	
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
