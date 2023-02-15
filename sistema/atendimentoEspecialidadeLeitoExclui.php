<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputEspecialidadeLeitoId'])){
	
	$iEspecialidadeLeito = $_POST['inputEspecialidadeLeitoId'];
        	
	try{
		
		$sql = "DELETE FROM EspecialidadeLeito
				WHERE EsLeiId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iEspecialidadeLeito); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Especialidade do leito excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir especialidade do leito!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		//echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("atendimentoEspecialidadeLeito.php");

?>
