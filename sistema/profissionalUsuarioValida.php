<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = ("SELECT ProfiId
			 FROM Profissional
			 WHERE ProfiUnidade = ".$_SESSION['UnidadeId']." and  ProfiUsuario = '". $_POST['nomeNovo']."' and ProfiUsuario <> '". $_POST['nomeVelho']."'");
} else{
	$sql = ("SELECT ProfiId
			 FROM Profissional
			 WHERE ProfiUnidade = ".$_SESSION['UnidadeId']." and ProfiUsuario = '". $_POST['nomeNovo']."'");
}
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
}  else{
	echo 0;
}

?>
