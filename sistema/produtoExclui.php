<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputProdutoId'])){
	
	$iProduto = $_POST['inputProdutoId'];
        	
	try{
		
		$sql = "DELETE FROM Produto
				WHERE ProduId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iProduto);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Produto excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Produto!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("produto.php");

?>
