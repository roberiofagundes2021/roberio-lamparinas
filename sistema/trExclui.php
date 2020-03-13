<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	
	$iTR = $_POST['inputTRId'];
	$iEmpresa = $_SESSION['EmpreId'];
        	
	try{
		$conn->beginTransaction();	

		/* Aqui não estou usando o Foreign Key on Cascade. Portanto, preciso excluir primeiro o TRXOrcamentoXProduto, TRXOrcamentoXServico e TRXOrcamentoXSubCategoria */

		$sql = "SELECT TrXOrId
				FROM TRXOrcamento
				WHERE TrXOrTermoReferencia = $iTR and TrXOrEmpresa = $iEmpresa";
		$result = $conn->query($sql);
		$rowOrcamentosTR = $result->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($rowOrcamentosTR as $item){
		   
		    $iOrcamento = $item['TrXOrId'];
		   
			$sql = "DELETE FROM TRXOrcamentoXSubCategoria
					WHERE TXOXSCOrcamento = :iOrcamento and TXOXSCEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);
			$result->bindParam(':iOrcamento', $iOrcamento);
			$result->bindParam(':iEmpresa', $iEmpresa); 
			$result->execute();
			
			$sql = "DELETE FROM TRXOrcamentoXProduto
					WHERE TXOXPOrcamento = :iOrcamento and TXOXPEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);
			$result->bindParam(':iOrcamento', $iOrcamento);
			$result->bindParam(':iEmpresa', $iEmpresa); 
			$result->execute();	

			$sql = "DELETE FROM TRXOrcamentoXServico
					WHERE TXOXSOrcamento = :iOrcamento and TXOXSEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);
			$result->bindParam(':iOrcamento', $iOrcamento);
			$result->bindParam(':iEmpresa', $iEmpresa); 
			$result->execute();				
		}
		   
		$sql = "DELETE FROM TRXOrcamento
				WHERE TrXOrTermoReferencia = :iTR and TrXOrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $iEmpresa); 
		$result->execute();
		
		$sql = "DELETE FROM TermoReferenciaXProduto
				WHERE TRXPrTermoReferencia = :iTR and TRXPrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $iEmpresa); 
		$result->execute();
		
		$sql = "DELETE FROM TermoReferenciaXServico
				WHERE TRXSrTermoReferencia = :iTR and TRXSrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTR', $iTR);
		$result->bindParam(':iEmpresa', $iEmpresa); 
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
		
		echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("tr.php");

?>
