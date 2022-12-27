<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputTpIntId'])){
	
	$iTipoInternacao = $_POST['inputTpIntId'];
        	
	try{
		
		$sql = "DELETE FROM TipoInternacao
				WHERE TpIntId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iTipoInternacao); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Tipo de internação excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir tipo de internação!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		//echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("atendimentoTipoInternacao.php");

?>
