<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT UnMedId
			FROM UnidadeMedida
			WHERE UnMedEmpresa = ".$_SESSION['EmpreId']." and UnMedNome = '". $_POST['nomeNovo']."' and UnMedNome <> '". $_POST['nomeVelho']."'";
} else{
	$sql = "SELECT UnMedId
			FROM UnidadeMedida
			WHERE UnMedEmpresa = ".$_SESSION['EmpreId']." and UnMedNome = '". $_POST['nomeNovo']."'";
}
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
} else{

	if ($_POST['estadoAtual'] == 'EDITA'){
		echo "EDITA";
	} else{
		echo 0;
	}
}

?>
