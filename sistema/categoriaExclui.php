<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputCategoriaId'])){
	
	$iPerfil = $_POST['inputCategoriaId'];
        	
	try{
		
		$sql = "DELETE FROM Categoria
				WHERE CategId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iPerfil); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Categoria excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Categoria!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("categoria.php");

?>
