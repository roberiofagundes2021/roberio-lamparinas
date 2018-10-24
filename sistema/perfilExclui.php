<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputPerfilId'])){
	
	$iPerfil = $_POST['inputPerfilId'];
        	
	try{
		
		$sql = "DELETE FROM Perfil
				WHERE PerfiId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iPerfil); 
		$result->execute();
		
		$_SESSION['msg'] = "Perfil excluído com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao excluir Perfil!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("perfil.php");

?>
