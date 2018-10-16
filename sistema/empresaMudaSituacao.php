<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

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
		
		$_SESSION['msg'] = "Situação da Empresa alterada com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao alterar situação da empresa!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("empresa.php");

?>
