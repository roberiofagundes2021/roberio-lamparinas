<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputSetorId'])){
	
	$iSetor = $_POST['inputSetorId'];
	$bStatus = $_POST['inputSetorStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Setor SET SetorStatus = :bStatus
				WHERE SetorId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iSetor); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do setor alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do setor!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("setor.php");

?>
