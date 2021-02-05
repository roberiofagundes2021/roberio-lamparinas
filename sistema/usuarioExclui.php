<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputUsuarioId'])){
	
	$iUsuario = $_POST['inputUsuarioId'];
        	
	try{

		$conn->beginTransaction();

		$sql = "DELETE FROM EmpresaXUsuarioXPerfil
				WHERE EXUXPUsuario = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iUsuario);
		$result->execute();

		$sql = "DELETE FROM Usuario
				WHERE UsuarId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iUsuario);
		$result->execute();

		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Usuário excluído!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] ="Erro ao excluir usuário!!! O registro a ser excluído está sendo usado em outro local.";
		$_SESSION['msg']['tipo'] = "error";	
		
		//echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("usuario.php");

?>
