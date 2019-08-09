<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputOrdemCompraId'])){
	
	$iProduto = $_POST['inputOrdemCompraId'];
	$bStatus = $_POST['inputOrdemCompraStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE OrdemCompra SET OrComSituacao = :bStatus
				WHERE OrComId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus);
		$result->bindParam(':id', $iProduto);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da ordem de compra alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da ordem de compra!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("index.php");

?>
