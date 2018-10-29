<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputLicencaId'])){
	
	$iLicenca = $_POST['inputLicencaId'];
	$bStatus = $_POST['inputLicencaStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Licenca SET LicenStatus = :bStatus
				WHERE LicenId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iLicenca);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da licença alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da licença!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("licenca.php");

?>
