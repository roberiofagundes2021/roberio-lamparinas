<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	
	$iTR = $_POST['inputTRId'];
        	
	try{
		$conn->beginTransaction();	
		
		$sql = "DELETE FROM TermoReferenciaXProduto
				WHERE TRXPrTermoReferencia = :iTR and TRXPrEmpresa = :iEmpresa";
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
