<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = ("SELECT SbCatId
			 FROM SubCategoria
			 WHERE SbCatEmpresa = ".$_SESSION['EmpreId']." and SbCatNome = '". $_POST['nomeNovo']."' and SbCatNome <> '". $_POST['nomeVelho']."'");
} else{
	$sql = ("SELECT SbCatId
			 FROM SubCategoria
			 WHERE SbCatEmpresa = ".$_SESSION['EmpreId']." and SbCatNome = '". $_POST['nome']."'");
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
