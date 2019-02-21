<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputInventarioId'])){
	
	$iInventario = $_POST['inputInventarioId'];
	$bStatus = $_POST['inputInventarioStatus'] ? 1 : 2;  // aqui passa de PENDENTE para FINALIZADO
        	
	try{
		
		$sql = "UPDATE Inventario SET InvenSituacao = :bStatus
				WHERE InvenId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iInventario); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do inventário alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do inventario!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("inventario.php");

?>
