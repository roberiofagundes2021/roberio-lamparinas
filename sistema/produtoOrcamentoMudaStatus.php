<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputPrOrcId'])){
	
	$inputPrOrcId = $_POST['inputPrOrcId'];
	$inputPrOrcStatus = $_POST['inputPrOrcStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE ProdutoOrcamento SET PrOrcSituacao = :bStatus
				WHERE PrOrcId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $inputPrOrcStatus);
		$result->bindParam(':id', $inputPrOrcId);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do produto alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do produto!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("produtoOrcamento.php");

?>
