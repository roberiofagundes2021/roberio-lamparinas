<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputPlanoContasId'])){
	
	$iPlanoContas = $_POST['inputPlanoContasId'];
	$bStatus = $_POST['inputPlanoContasStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE PlanoContas SET PlConStatus = :bStatus
				WHERE PlConId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iPlanoContas); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do Plano de Contas alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do Plano de Contas!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("planoContas.php");

?>
