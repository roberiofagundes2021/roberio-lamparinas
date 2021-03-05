<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputFluxoOperacionalId'])){
	
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	$sStatus = $_POST['inputFluxoOperacionalStatus'] == 'ATIVO' ? 'INATIVO' : 'ATIVO';
        	
	try{
          
		$sql = "SELECT SituaId
				FROM  Situacao
				WHERE SituaChave = '". $sStatus."'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iStatus = $row['SituaId'];
		
		$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :iStatus
				WHERE FlOpeId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':iStatus', $iStatus); 
		$result->bindParam(':id', $iFluxoOperacional); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do Fluxo Operacional alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do Fluxo Operacional!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("fluxo.php");

?>
