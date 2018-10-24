<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = "";

if(isset($_POST['inputPerfilId'])){
	
	$iPerfil = $_POST['inputPerfilId'];
	$bStatus = $_POST['inputPerfilStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Perfil SET PerfiStatus = :bStatus
				WHERE PerfiId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iPerfil); 
		$result->execute();
		
		$_SESSION['msg'] = "Situação da Perfil alterada com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao alterar situação da Perfil!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("perfil.php");

?>
