<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputCentroCustoId'])){
	
	$iCentroCusto = $_POST['inputCentroCustoId'];
	$bStatus = $_POST['inputCentroCustoStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE CentroCusto SET CeCusStatus = :bStatus
				WHERE CeCusId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iCentroCusto); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do Centro de Custo alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do Centro de Custo!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("centroCusto.php");

?>
