<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$iUnidade = $_SESSION['UnidadeId'];

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT AtPrMId
			FROM AtendimentoProtocoloManchester
			WHERE AtPrMUnidade = ".$iUnidade." and AtPrMNome = '". $_POST['nomeNovo']."' and AtPrMNome <> '". $_POST['nomeVelho']."'";
} else{
	$sql = "SELECT AtPrMId
			FROM AtendimentoProtocoloManchester
			WHERE AtPrMUnidade = ".$iUnidade." and AtPrMNome = '". $_POST['nome']."'";
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
