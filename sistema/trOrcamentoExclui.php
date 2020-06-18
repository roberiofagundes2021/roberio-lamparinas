<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputOrcamentoId'])){
	
	$iOrcamento = $_POST['inputOrcamentoId'];
        	
	try{
		$conn->beginTransaction();	
		
		$sql = "DELETE FROM TRXOrcamentoXProduto
				WHERE TXOXPOrcamento = :iOrcamento and TXOXPUnidade = :iUnidade";
		$result = $conn->prepare($sql);
		$result->bindParam(':iOrcamento', $iOrcamento);
		$result->bindParam(':iUnidade', $_SESSION['UnidadeId']); 
		$result->execute();
		
		
		$sql = "DELETE FROM TRXOrcamento
				WHERE TrXOrId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iOrcamento); 
		$result->execute();

		$sql = "DELETE FROM TRXOrcamentoXSubcategoria
				WHERE TXOXSCOrcamento = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iOrcamento); 
		$result->execute();
		
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Orcamento excluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
			
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir orcamento!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		$conn->rollback();
		
		//echo 'Error: ' . $e->getMessage();
	}
}

irpara("trOrcamento.php");

?>
