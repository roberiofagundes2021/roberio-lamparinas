<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputLocalEstoqueId'])){
	
	$iLocalEstoque = $_POST['inputLocalEstoqueId'];
	$bStatus = $_POST['inputLocalEstoqueStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE LocalEstoque SET LcEstStatus = :bStatus
				WHERE LcEstId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iLocalEstoque); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do local do estoque alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do local do estoque!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("localestoque.php");

?>
