<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputProtocoloManchesterId'])){
	
	$iProtocoloManchester = $_POST['inputProtocoloManchesterId'];
        	
	try{
		
		$sql = "DELETE FROM AtendimentoProtocoloManchester
				WHERE AtPrMId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iProtocoloManchester);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Protocolo Manchester excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Protocolo Manchester!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("protocoloManchester.php");

?>
