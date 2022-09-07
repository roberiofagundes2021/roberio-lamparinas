<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['iProfissional'])){

	$sql = "SELECT ProfiId, ProfiNome
			FROM Profissional
			JOIN Situacao on SituaId = ProfiStatus
			WHERE ProfiId = $_POST['iProfissional']
		";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$count = count($row);
	
	if($count){

		print('
			<p style="margin-right:10px; margin-left: 10px"><b> Profissional:</b> '.$row['ProfiNome'].'</p>
		');
			
	} else{
		echo 0;
	}
} else {
	echo 0;
}

?>
