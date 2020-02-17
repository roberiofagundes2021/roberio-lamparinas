<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function gerarNumeracao()
{
	include('global_assets/php/conexao.php');

	$sql = "SELECT MAX(SolicId)
	        FROM Solicitacao
			WHERE SolicEmpresa = " . $_SESSION['EmpreId'] . "
		   ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	if ($row[""]) {
		$sql = "SELECT SolicNumero
	            FROM Solicitacao
			    WHERE SolicId = " . $row[""] . " and SolicEmpresa = " . $_SESSION['EmpreId'] . "
		   ";
		$result = $conn->query($sql);
		$rowSolic = $result->fetch(PDO::FETCH_ASSOC);

		if (count($rowSolic) >= 1) {
			$temp = explode('/', $rowSolic['SolicNumero']);

			$int = intval($temp[0]);

			if ($int <= 9) {
				return '0' . ++$int . '/' . date('Y') . '';
			} else {
				return '' . ++$int . '/' . date('Y') . '';
			}
		} 
	} else {
		return '01/' . date('Y') . '';
	}
}

if (isset($_SESSION['Carrinho'])) {

	try {

		$conn->beginTransaction();

		// Selecionando dados do setor do usuário
		$sql = "SELECT EXUXPSetor
                FROM EmpresaXUsuarioXPerfil 
		        WHERE EXUXPUsuario = " . $_SESSION['UsuarId'] . "
		       ";
		$result = $conn->query($sql);
		$Setor = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT SituaId
		        FROM Situacao
		        WHERE SituaChave =  'AGUARDANDOLIBERACAO'
		       ";
		$result = $conn->query($sql);
		$Situacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT PerfiId
				FROM Perfil
				Where PerfiChave = 'ALMOXARIFADO' ";
		$result = $conn->query("$sql");
		$rowPerfil = $result->fetch(PDO::FETCH_ASSOC);

		$soliObservacao = '';
		if (isset($_POST['txtObservacao'])) {
			$soliObservacao = $_POST['txtObservacao'];
		}

		$sql = "INSERT INTO Solicitacao (SolicNumero, SolicData, SolicObservacao, SolicSetor, SolicSolicitante, SolicSituacao, SolicUsuarioAtualizador, SolicEmpresa)
				VALUES (:iNumero, :iData, :iObservacao, :iSetor, :iSolicitante, :iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		// var_dump($Perfil['PerfiId']);
		// var_dump($_SESSION['UsuarId']);
		// var_dump($situaId);
		//  var_dump($_SESSION['EmpreId']);
		$result->execute(array(
			':iNumero' => gerarNumeracao(),
			':iData' => date('Y-m-d'),
			':iObservacao' => $soliObservacao,
			':iSetor' => $Setor['EXUXPSetor'],
			':iSolicitante' => $_SESSION['UsuarId'],
			':iSituacao' => $Situacao['SituaId'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));

		$SolicitacaoId = $conn->lastInsertId();

		try {
			$sql = "INSERT INTO SolicitacaoXProduto
						(SlXPrSolicitacao, SlXPrProduto, SlXPrQuantidade, SlXPrUsuarioAtualizador, SlXPrEmpresa)
					VALUES 
						(:iSolicitacao, :iProduto, :iQuantidade, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);

			foreach ($_SESSION['Carrinho'] as $key => $value) {

				$result->execute(array(
					':iSolicitacao' => $SolicitacaoId,
					':iProduto' => $value['id'],
					':iQuantidade' => $value['quantidade'],
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId']
				));
			}
		} catch (PDOException $e) {
			$conn->rollback();
			echo 'Error: ' . $e->getMessage();
			exit;
		}

		$sIdentificacao = 'Solicitação de materiais (' . $Setor['EXUXPSetor'] . ')';

		$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandePerfilDestino, BandeSolicitante, BandeTabela, BandeTabelaId,
												 BandeStatus, BandeUsuarioAtualizador, BandeEmpresa)
							VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iPerfilDestino, :iSolicitante, :sTabela, :iTabelaId, :iStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sIdentificacao' => $sIdentificacao,
			':dData' => date("Y-m-d"),
			':sDescricao' => 'Liberar Solicitação',
			':sURL' => '',
			':iPerfilDestino' => $rowPerfil['PerfiId'],  //Tem que tirar esse campo do banco, já que agora tem uma tabela BandejaXPerfil
			':iSolicitante' => $_SESSION['UsuarId'],
			':sTabela' => 'Solicitacao',
			':iTabelaId' => $SolicitacaoId,
			':iStatus' => $Situacao['SituaId'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));

		$BandejaId = $conn->lastInsertId();


		$sql = "INSERT INTO BandejaXPerfil (BnXPeBandeja, BnXPePerfil, BnXPeEmpresa)
							VALUES (:iBandeja, :iPerfil, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iBandeja' => $BandejaId,
			':iPerfil' => $rowPerfil['PerfiId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));



		$conn->commit();

		unset($_SESSION['Carrinho']);

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Solicitação realizada!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao concluir solicitação!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		die;
	}

	irpara("solicitacao.php");
}
