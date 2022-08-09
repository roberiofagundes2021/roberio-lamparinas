<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['historicoId'])){

	$sql = "SELECT AtendNumRegistro, AtendDataRegistro, AtendId
			FROM Atendimento
			JOIN Cliente ON ClienId = AtendCliente
			WHERE AtendId = '". $_POST['historicoId']."' and AtendUnidade = ".$_SESSION['UnidadeId']; 
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$count = count($row);	
	
	//Verifica se já existe esse registro (se existir, retorna true )
	if($count){
		print('
			<p>Atend Id: '.$row['AtendId'].'</p>
			<p>Número Registro: '.$row['AtendNumRegistro'].'</p>
			<p>Data Registro: '.mostraData($row['AtendDataRegistro']).'</p>
			<p>Atend Id: '.$row['AtendId'].'</p>
			<p>Número Registro: '.$row['AtendNumRegistro'].'</p>
			<p>Data Registro: '.mostraData($row['AtendDataRegistro']).'</p>
			<p>Atend Id: '.$row['AtendId'].'</p>
			<p>Número Registro: '.$row['AtendNumRegistro'].'</p>
			<p>Data Registro: '.mostraData($row['AtendDataRegistro']).'</p>
			<p>Atend Id: '.$row['AtendId'].'</p>
			<p>Número Registro: '.$row['AtendNumRegistro'].'</p>
			<p>Data Registro: '.mostraData($row['AtendDataRegistro']).'</p>
		');
	} else{
		echo 0;
	}
} else {
	echo 0;
}

?>
