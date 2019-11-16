<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nome'])){
	$sql = "SELECT PlConId
		    FROM PlanoContas
		    WHERE PlConEmpresa = ".$_SESSION['EmpreId']." and PlConNome = '".$_POST['nome']."' and PlConCentroCusto = '".$_POST['centroCusto']."'";
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
