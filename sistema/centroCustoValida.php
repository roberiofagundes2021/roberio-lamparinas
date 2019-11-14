<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT CeCusId
			 FROM CentroCusto
			 WHERE CeCusEmpresa = ".$_SESSION['EmpreId']." and CeCusNome = '". $_POST['nomeNovo']."' and CeCusNome <> '". $_POST['nomeVelho']."'";
} else{
	$sql = "SELECT CeCusId
			 FROM CentroCusto
			 WHERE CeCusEmpresa = ".$_SESSION['EmpreId']." and CeCusNome = '". $_POST['nome']."'";
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
