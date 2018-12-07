<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputUnidadeMedidaId'])){
	
	$iUnidadeMedida = $_POST['inputUnidadeMedidaId'];
	$bStatus = $_POST['inputUnidadeMedidaStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE UnidadeMedida SET UnMedStatus = :bStatus
				WHERE UnMedId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iUnidadeMedida); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da unidade de medida alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da unidade de medida!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("unidademedida.php");

?>
