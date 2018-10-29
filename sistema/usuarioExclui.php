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
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário excluído!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Usuário!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("usuario.php");

?>
