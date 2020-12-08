<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputClienteAnexoId'])){
	
	$iClienteAnexo = $_POST['inputClienteAnexoId'];
        	
	try{
		
		$sql = "DELETE FROM ClienteAnexo
				WHERE ClAneId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iClienteAnexo);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Anexo excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Anexo!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("clienteAnexo.php");

?>
