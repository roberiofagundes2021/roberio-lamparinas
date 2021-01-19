<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

if (isset($_POST['idMov'])) {

	if ($_POST['tipoMov'] === 'R') {
		try {
			$sql = "DELETE FROM ContasAReceber
										WHERE CnAReId = :id";
			$result = $conn->prepare($sql);
			$result->bindParam(':id', $_POST['idMov']);
			$result->execute();

			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "Conta excluída!!!";
			$_SESSION['msg']['tipo'] = "success";
		} catch (PDOException $e) {

			$_SESSION['msg']['titulo'] = "Erro";
			$_SESSION['msg']['mensagem'] = "Erro ao excluir Conta!!!";
			$_SESSION['msg']['tipo'] = "error";
		}
	} else {
		try {
			$sql = "DELETE FROM ContasAPagar
										WHERE CnAPaId = :id";
			$result = $conn->prepare($sql);
			$result->bindParam(':id', $_POST['idMov']);
			$result->execute();

			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "Conta excluída!!!";
			$_SESSION['msg']['tipo'] = "success";
		} catch (PDOException $e) {

			$_SESSION['msg']['titulo'] = "Erro";
			$_SESSION['msg']['mensagem'] = "Erro ao excluir Conta!!!";
			$_SESSION['msg']['tipo'] = "error";
		}
	}
}

irpara("movimentacaoFinanceira.php");