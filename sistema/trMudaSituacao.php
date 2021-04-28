<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputTermoReferenciaId'])){
	
	$iTermoReferenciaId = $_POST['inputTermoReferenciaId'];
	
	try{

		$conn->beginTransaction();

		$sql = "
			SELECT SituaId
				FROM Situacao	
			 WHERE SituaChave = '".$_POST['inputTermoReferenciaStatus']."'
		";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);        	

		if ($_POST['inputTermoReferenciaStatus'] === 'NAOLIBERADO'){
			$motivo = $_POST['inputMotivo'];
		} else{
			$motivo = NULL;
		}
		
		$sql = "
			UPDATE TermoReferencia
				 SET TrRefStatus = :bStatus, 
				     TrRefUsuarioAtualizador = :iUsuario,
						 TrRefLiberaParcial = ".true."
			 WHERE TrRefId = :iTermoReferenciaId";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iTermoReferenciaId', $iTermoReferenciaId);
		$result->execute();
		

		$sql = "
			UPDATE Bandeja 
				 SET BandeStatus = :bStatus, 
						 BandeMotivo = :sMotivo, 
						 BandeUsuarioAtualizador = :iUsuario
			 WHERE BandeId = :iBandeja
		";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':sMotivo', $motivo);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iBandeja', $_POST['inputBandejaId']);
		$result->execute();

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do Termo de Referência alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do Termo de Referência!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("index.php");

?>
