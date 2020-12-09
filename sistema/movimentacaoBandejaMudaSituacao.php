<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if (isset($_POST['inputMovimentacaoId'])) {

	$iMovimentacao = $_POST['inputMovimentacaoId'];

	try {

		$conn->beginTransaction();

		$sql = "SELECT SituaId
							FROM Situacao	
						 WHERE SituaChave = '" . $_POST['inputMovimentacaoStatus'] . "'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		if ($_POST['inputMovimentacaoStatus'] == 'NAOLIBERADO') {
			$motivo = $_POST['inputMotivo'];
		} else {
			$motivo = NULL;
		}



		$sql = "UPDATE Movimentacao 
						 	 SET MovimSituacao = :bStatus, MovimUsuarioAtualizador = :iUsuario
						 WHERE MovimId = :iMovimentacao";
		$result = $conn->prepare($sql);

		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iMovimentacao', $iMovimentacao);
		$result->execute();



		$sql = "UPDATE Bandeja 
							 SET BandeStatus = :bStatus, BandeMotivo = :sMotivo, BandeUsuarioAtualizador = :iUsuario
						 WHERE BandeId = :iBandeja";
		$result = $conn->prepare($sql);

		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':sMotivo', $motivo);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iBandeja', $_POST['inputBandejaId']);
		$result->execute();

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da movimentação alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		exit;
	}
}

irpara("index.php");
