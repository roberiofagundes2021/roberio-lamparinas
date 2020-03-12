<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	
	$iTR = $_POST['inputTRId'];
        	
	try{
		$conn->beginTransaction();	

		/* Verificar se dá para excluir com Foreign Key on Cascade. Se der não precisaria desse tanto de DELETE. Senão teria que pesquisar todos os 
		   TRXOrcamentoXProduto e TRXOrcamentoXServico para listar todos que tem o Orçamento a ser excluido. Exclui eles primeiro antes de fazer os deletes abaixo */

		$sql = "DELETE FROM TRXOrcamento
				WHERE TrXOrTermoReferencia = :iTR and TrXOrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $_SESSION['EmpreId']); 
		$result->execute();
		
		$sql = "DELETE FROM TermoReferenciaXProduto
				WHERE TRXPrTermoReferencia = :iTR and TRXPrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $_SESSION['EmpreId']); 
		$result->execute();
		
		$sql = "DELETE FROM TermoReferenciaXServico
				WHERE TRXSrTermoReferencia = :iTR and TRXSrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $_SESSION['EmpreId']); 
		$result->execute();
		
		$sql = "DELETE FROM TermoReferencia
				WHERE TrRefId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iTR); 
		$result->execute();
		
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Termo de Referência excluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
			
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir termo de referência!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		$conn->rollback();
		
		//echo 'Error: ' . $e->getMessage();
	}
}

irpara("tr.php");

?>
