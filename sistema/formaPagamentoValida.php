<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['nomeVelho'])){
	$sql = "SELECT FrPagId
			 FROM FormaPagamento
			 WHERE FrPagUnidade = ".$_SESSION['UnidadeId']." and FrPagNome = '". $_POST['nomeNovo']."' and FrPagNome <> '". $_POST['nomeVelho']."'";
} else{
	$sql = "SELECT FrPagId
			 FROM FormaPagamento
			 WHERE FrPagUnidade = ".$_SESSION['UnidadeId']." and FrPagNome = '". $_POST['nome']."'";
}
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
} else{
	echo 0;
}

?>
