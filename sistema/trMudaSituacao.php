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

		$sql = "SELECT SituaId
				FROM Situacao	
				WHERE SituaChave = '".$_POST['inputTermoReferenciaStatus']."'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		if ($_POST['inputTermoReferenciaStatus'] === 'NAOLIBERADOCENTRO'){
			$motivo = $_POST['inputMotivo'];
			$msg = "Termo de Referência não liberado!";

			$sql = "UPDATE TermoReferencia
					SET TrRefStatus = :bStatus, 
						TrRefUsuarioAtualizador = :iUsuario
					WHERE TrRefId = :iTermoReferenciaId";			
		} else{
			$motivo = NULL;
			$msg = "Termo de Referência liberado!";

			$sql = "UPDATE TermoReferencia
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

		if ($_POST['inputTermoReferenciaStatus'] === 'FASEINTERNAFINALIZADA'){
			
			//Essa consulta é feita para pegar o Id da Bandeja quando o Finalizar TR for feito a partir do tr.php para atualizar a bandeja após finalizar o TR
			$sql = "SELECT BandeId
					FROM Bandeja	
					WHERE BandeUnidade = ".$_SESSION['UnidadeId']." and BandeTabela = 'TermoReferencia' 
					and BandeTabelaId = ".$iTermoReferenciaId." and BandePerfil = 'COMISSAO' ";
			$result = $conn->query($sql);
			$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
		}

		if (isset($_POST['inputBandejaId'])){
			$iBandeja = $_POST['inputBandejaId'];
		} else{
			$iBandeja = $rowBandeja['BandeId'];
		}
		
		$sql = "UPDATE Bandeja 
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
		$result->bindParam(':iBandeja', $iBandeja);
		$result->execute();

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = $msg;
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro na liberação do Termo de Referência!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("tr.php");

?>
