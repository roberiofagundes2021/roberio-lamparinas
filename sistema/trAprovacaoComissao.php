<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Aprovação - Centro Administrativo';

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	
	$iTrId = $_POST['inputTRId'];

	$sql = "
		SELECT TRXEqPresidente, TRXEqUsuario, TrRefNumero, TrRefTipo, TrRefData, TrRefStatus
  		FROM TRXEquipe
		JOIN TermoReferencia on TrRefId = TRXEqTermoReferencia
 	 	WHERE TRXEqUnidade = ".$_SESSION['UnidadeId']." AND TRXEqTermoReferencia = ".$_POST['inputTRId']."
   		AND TRXEqPresidente > 0
	";
	$result = $conn->query($sql);
	$rowTRPresidente = $result->fetch(PDO::FETCH_ASSOC);

	if (intval($rowTRPresidente) <= 0) {
		$_SESSION['msg']['titulo'] 		= "Atenção";
		$_SESSION['msg']['mensagem'] 	= "Só é permitido enviar após criar a comissão!!!";
		$_SESSION['msg']['tipo'] 		= "error";

	} else {

		try{
			$conn->beginTransaction();

			$sql = "SELECT SituaId
					FROM Situacao
					WHERE SituaChave = 'AGUARDANDOLIBERACAO' ";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			/* Verifica se a Bandeja já tem um registro com BandeTabela: TermoReferencia e BandeTabelaId: $iTrId, evitando duplicação */
			$sql = "SELECT COUNT(BandeId) as Count
					FROM Bandeja
					WHERE BandeTabela = 'TermoReferencia' AND BandeTabelaId =  " . $iTrId;
			$result = $conn->query($sql);
			$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
			$count = $rowBandeja['Count'];

			$sql = "SELECT BandeId, SituaChave
					FROM Bandeja
					JOIN Situacao on SituaId = BandeStatus
					WHERE BandeTabela = 'TermoReferencia' AND BandeTabelaId =  ".$iTrId;
			$result = $conn->query($sql);
			$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);

			$tipo = $rowTRPresidente['TrRefTipo'] == 'S' 
			? 'Serviços' : $rowTRPresidente['TrRefTipo'] == 'P' 
			? 'Produtos' : $rowTRPresidente['TrRefTipo'] == 'PS' 
			&& 'Produtos e Serviços';

			/* Insere na Bandeja para Aprovação do presidente da Comissão */
			$sIdentificacao = 'Termo de Referência (Nº Termo: '.$rowTRPresidente['TrRefNumero'].' | Data: '.mostradata($rowTRPresidente['TrRefData']).' | Tipo: '.$tipo.')';

			if ($count == 0){
			
				$sql = "
					INSERT INTO 
						Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandeSolicitante, 
						BandeSolicitanteSetor, BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, 
						BandeUnidade, BandePerfil, BandeUsuario)
					VALUES (
						:sIdentificacao, :dData, :sDescricao, :sURL, :iSolicitante, :iSolicitanteSetor, :sTabela, 
						:iTabelaId, :iStatus, :iUsuarioAtualizador, :iUnidade, :sPerfil, :iPresidente)
				";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
					':sIdentificacao' 			=> $sIdentificacao,
					':dData' 					=> date("Y-m-d"),
					':sDescricao' 				=> 'Dar como concluído o Termo de Referência (Só clique em Liberar quando o TR estiver totalmente concluído)',
					':sURL' 					=> '',
					':iSolicitante' 			=> $_SESSION['UsuarId'],
					':iSolicitanteSetor' 		=> null,
					':sTabela' 					=> 'TermoReferencia',
					':iTabelaId' 				=> $iTrId,
					':iStatus' 					=> $rowSituacao['SituaId'],
					':iUsuarioAtualizador' 		=> $_SESSION['UsuarId'],
					':iUnidade' 				=> $_SESSION['UnidadeId'],
					':sPerfil' 					=> null,
					':iPresidente' 				=> $rowTRPresidente['TRXEqUsuario']
				));
				/* Fim Insere Bandeja */

			} else{
				$sql = "
					UPDATE Bandeja SET BandeIdentificacao = :sIdentificacao, BandeData = :dData, BandeDescricao = :sDescricao,
									   BandeURL = :sURL, BandeSolicitante = :iSolicitante, BandeStatus = :iStatus,
									   BandeUsuarioAtualizador = :iUsuarioAtualizador, 
									   BandePerfil = :sPerfil, BandeUsuario = :iPresidente
					WHERE BandeUnidade = :iUnidade AND BandeId = :iIdBandeja";
				$result = $conn->prepare($sql);						
				$result->execute(array(
					':sIdentificacao' 		=> $sIdentificacao,
					':dData' 				=> date("Y-m-d"),
					':sDescricao' 			=> 'Dar como concluído o Termo de Referência (Só clique em Liberar quando o TR estiver totalmente concluído)',
					':sURL' 				=> '',
					':iSolicitante' 		=> $_SESSION['UsuarId'],
					':iStatus' 				=> $rowSituacao['SituaId'],
					':iUsuarioAtualizador' 	=> $_SESSION['UsuarId'],
					':sPerfil' 				=> null,
					':iPresidente' 			=> $rowTRPresidente['TRXEqUsuario'],
					':iUnidade' 			=> $_SESSION['UnidadeId'],
					':iIdBandeja' 			=> $rowBandeja['BandeId']														
				));
			}

			$conn->commit();
					
			$_SESSION['msg']['titulo'] 		= "Sucesso";
			$_SESSION['msg']['mensagem'] 	= "Termo de Referência enviado para comissão!!!";
			$_SESSION['msg']['tipo'] 		= "success";     

		} catch(PDOException $e){

			$conn->rollback();
			
			$_SESSION['msg']['titulo'] 		= "Erro";
			$_SESSION['msg']['mensagem'] 	= "Erro ao enviar Termo de Referência para comissão!!!";
			$_SESSION['msg']['tipo'] 		= "error";	

			echo 'Error Message: ' . $e->getMessage().'| Line:  '.$e->getLine();die;
		}
	}
}

irpara("tr.php");

?>
