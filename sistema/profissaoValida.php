<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT ProfiId
			FROM Profissao
			WHERE ProfiUnidade = ".$_SESSION['UnidadeId']." and ProfiNome = '". mssql_escape($_POST['nomeNovo'])."' and ProfiNome <> '". mssql_escape($_POST['nomeVelho'])."'";
} else{
	$sql = "SELECT ProfiId
			FROM Profissao
			WHERE ProfiUnidade = ".$_SESSION['UnidadeId']." and ProfiNome = '". mssql_escape($_POST['nomeNovo'])."'";
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