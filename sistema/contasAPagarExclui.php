<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputContasAPagarId'])){
	
    $id = $_POST['inputContasAPagarId'];

    try{
		
		$sql = "DELETE FROM ContasAPagar
				WHERE CnAPaId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $id); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Conta excluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
			
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Conta!!!";
		$_SESSION['msg']['tipo'] = "error";			
	}
}

irpara("contasAPagar.php");

?>
