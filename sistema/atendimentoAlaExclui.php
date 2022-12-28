<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputAlaId'])){
	
	$iCaraterInternacao = $_POST['inputAlaId'];
        	
	try{
		
		$sql = "DELETE FROM CaraterInternacao
				WHERE CrIntId = :id";	
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iCaraterInternacao); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Relação de Ala excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Ala!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("atendimentoAla.php");

?>
