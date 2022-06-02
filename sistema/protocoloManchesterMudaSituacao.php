<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputProtocoloManchesterId'])){
	
	$iProtocoloManchester = $_POST['inputProtocoloManchesterId'];
	$sStatus = $_POST['inputProtocoloManchesterStatus'] == 'ATIVO' ? 'INATIVO' : 'ATIVO';
        	
	try{

		$sql = "SELECT SituaId
				FROM Situacao
			    WHERE SituaChave = '". $sStatus."'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iStatus = $row['SituaId'];		
		
		$sql = "UPDATE AtendimentoProtocoloManchester SET AtPrMStatus = :iStatus
				WHERE AtPrMId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':iStatus', $iStatus);
		$result->bindParam(':id', $iProtocoloManchester);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do protocolo manchester alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do protocolo manchester!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("protocoloManchester.php");

?>
