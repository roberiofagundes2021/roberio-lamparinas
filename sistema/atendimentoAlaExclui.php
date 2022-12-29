<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputAlaId'])){
	
	$iAla = $_POST['inputAlaId'];
        	
	try{
		
		$sql = "DELETE FROM Ala
				WHERE AlaId = :id";	
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iAla); 
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
