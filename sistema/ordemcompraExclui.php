<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputOrdemCompraId'])){
	
	$iOrdemCompra = $_POST['inputOrdemCompraId'];
        	
	try{
		$conn->beginTransaction();	
		
		$sql = "DELETE FROM OrdemCompraXProduto
				WHERE OCXPrOrdemCompra = :iOrdemCompra and OCXPrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iOrdemCompra', $iOrdemCompra);
		$result->bindParam(':iEmpresa', $_SESSION['EmpreId']); 
		$result->execute();
		
		$sql = "DELETE FROM OrdemCompraXServico
				WHERE OCXSrOrdemCompra = :iOrdemCompra and OCXSrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
		$result->bindParam(':iOrdemCompra', $iOrdemCompra);
		$result->bindParam(':iEmpresa', $_SESSION['EmpreId']); 
		$result->execute();
		
		$sql = "DELETE FROM OrdemCompra
				WHERE OrComId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iOrdemCompra); 
		$result->execute();
		
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Ordem de Compra excluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
			
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir ordem de compra!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		$conn->rollback();
		
		//echo 'Error: ' . $e->getMessage();
	}
}

irpara("ordemcompra.php");

?>
