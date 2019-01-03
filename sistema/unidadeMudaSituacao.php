<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputUnidadeId'])){
	
	$iUnidade = $_POST['inputUnidadeId'];
	$bStatus = $_POST['inputUnidadeStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Unidade SET UnidaStatus = :bStatus
				WHERE UnidaId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iUnidade); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da unidade alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da unidade!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("unidade.php");

?>
