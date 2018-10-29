<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputEmpresaId'])){
	
	$iEmpresa = $_POST['inputEmpresaId'];
	$bStatus = $_POST['inputEmpresaStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Empresa SET EmpreStatus = :bStatus
				WHERE EmpreId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iEmpresa); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da empresa alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da empresa!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("empresa.php");

?>
