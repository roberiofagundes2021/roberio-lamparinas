<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT VeicuId
			FROM Veiculo
			WHERE VeicuEmpresa = ".$_SESSION['EmpresaId']." and VeicuPlaca = '". $_POST['nomeNovo']."' and VeicuPlaca <> '". $_POST['nomeVelho']."' and VeicuUnidade = ".$_POST['unidade'];
} else {
	$sql = "SELECT VeicuId
			FROM Veiculo
			WHERE VeicuEmpresa = ".$_SESSION['EmpresaId']." and VeicuPlaca = '". $_POST['nomeNovo']."' and VeicuUnidade = ". $_POST['unidade'];
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
