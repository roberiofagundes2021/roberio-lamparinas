<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputTermoReferenciaId']) || isset($_POST['inputTRId'])){
	
	if (isset($_POST['inputTermoReferenciaId'])){
		$iTermoReferenciaId = $_POST['inputTermoReferenciaId'];
	} else {
		$iTermoReferenciaId = $_POST['inputTRId'];
	}	
	
	try{

		$conn->beginTransaction();

		$sql = "
			SELECT SituaId
			FROM Situacao	
			WHERE SituaChave = '".$_POST['inputTermoReferenciaStatus']."'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		if ($_POST['inputTermoReferenciaStatus'] === 'NAOLIBERADO'){
			$motivo = $_POST['inputMotivo'];

			$sql = "
				UPDATE TermoReferencia
					SET TrRefStatus = :bStatus, 
						TrRefUsuarioAtualizador = :iUsuario
				WHERE TrRefId = :iTermoReferenciaId";			
		} else{
			$motivo = NULL;

			$sql = "
				UPDATE TermoReferencia
					SET TrRefStatus = :bStatus, 
						TrRefUsuarioAtualizador = :iUsuario,
						TrRefLiberaParcial = ".true."
				WHERE TrRefId = :iTermoReferenciaId";			
		}
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iTermoReferenciaId', $iTermoReferenciaId);
		$result->execute();		
		
		$sql = "
			UPDATE Bandeja 
				 SET BandeStatus = :iStatus, 
					 BandeMotivo = :sMotivo, 
					 BandeUsuarioAtualizador = :iUsuario ";
		
		if ($_POST['inputTermoReferenciaStatus'] === 'FASEINTERNAFINALIZADA'){
			$sql .= ", BandeDescricao = 'CONCLUÍDO'";
		}	
		$sql .= "WHERE BandeId = :iBandeja";

		$result = $conn->prepare($sql);
		$result->bindParam(':iStatus', $row['SituaId']);
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

irpara("tr.php");

?>
