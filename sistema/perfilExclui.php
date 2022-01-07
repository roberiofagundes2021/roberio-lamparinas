<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputPerfilId'])){
	
	$iPerfil = $_POST['inputPerfilId'];
        	
	try{
		
		$sql = "DELETE FROM Perfil
				WHERE PerfiId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iPerfil); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Perfil excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Perfil!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("perfil.php");

?>
