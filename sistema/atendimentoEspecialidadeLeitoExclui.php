<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputEspecialidadeLeitoId'])){
	
	$iQuartoId = $_POST['inputEspecialidadeLeitoId'];
        	
	try{
		
		$sql = "DELETE FROM EspecialidadeLeito
				WHERE EsLeiId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iEspecialidadeLeitoId); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Especialidade do Leito excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Especialidade do Leito!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		//echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("atendimentoEspecialidadeLeito.php");

?>
