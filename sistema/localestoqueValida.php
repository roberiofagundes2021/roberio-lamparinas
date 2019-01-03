<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = ("SELECT LcEstId
			 FROM LocalEstoque
			 WHERE LcEstEmpresa = ".$_SESSION['EmpreId']." and LcEstNome = '". $_POST['nomeNovo']."' and LcEstNome <> '". $_POST['nomeVelho']."'");
} else{
	$sql = ("SELECT LcEstId
			 FROM LocalEstoque
			 WHERE LcEstEmpresa = ".$_SESSION['EmpreId']." and LcEstNome = '". $_POST['nome']."'");
}
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
} else{
	echo 0;
}

?>
