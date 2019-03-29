<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputFluxoOperacionalId'])){
	
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	$bStatus = $_POST['inputFluxoOperacionalStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :bStatus
				WHERE FlOpeId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iFluxoOperacional); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do Fluxo Operacional alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do Fluxo Operacional!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("fluxo.php");

?>
