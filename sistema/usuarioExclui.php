<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputUsuarioId'])){
	
	$iUsuario = $_POST['inputUsuarioId'];
        	
	try{
		
		$sql = "DELETE FROM Usuario
				WHERE UsuarId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iUsuario);
		$result->execute();
		
		$_SESSION['msg'] = "Usuário excluída com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao excluir usuário!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("usuario.php");

?>
