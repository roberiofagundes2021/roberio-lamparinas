<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputServicoId'])){
	
	$iServico = $_POST['inputServicoId'];
	$bStatus = $_POST['inputServicoStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE Servico SET ServStatus = :bStatus
				WHERE ServId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus);
		$result->bindParam(':id', $iServico);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do serviço alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do serviço!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("servico.php");

?>
