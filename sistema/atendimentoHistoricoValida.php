<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['historicoId'])){

	
	$sql = "SELECT AtendNumRegistro, AtendDataRegistro, AtendId
			FROM Atendimento
			JOIN Cliente ON ClienId = AtendCliente
			WHERE  AtendId = '". $_POST['historicoId']."'"; 
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
