<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Aprovação - Centro Administrativo';

include('global_assets/php/conexao.php');

try{
	if(isset($_POST['inputTRId'])){
	
		$conn->beginTransaction();
		
		$iTrId = $_POST['inputTRId'];

		/* Atualiza o Status da Ordem de Compra para "Aguardando Liberação" */
		$sql = "
			SELECT SituaId
				FROM Situacao
			 WHERE SituaChave = 'AGUARDANDOLIBERACAO' 
		";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "
			UPDATE TermoReferencia 
			   SET TrRefStatus = :iStatus
			 WHERE TrRefId = :iTrId
		";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iStatus' => $rowSituacao['SituaId'],
			':iTrId' => $iTrId					
		));
		/* Fim Atualiza */

		$sql = "
			SELECT PerfiId
			  FROM Perfil
			 WHERE PerfiChave = 'CONTABILIDADE'
		";
		$result = $conn->query($sql);
		$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($rowPerfil);

		$sql = "
			SELECT TrRefNumero, TrRefTipo, TrRefData
				FROM TermoReferencia
			 WHERE TrRefId = ".$iTrId;
		$result = $conn->query($sql);
		$rowTermoReferencia = $result->fetch(PDO::FETCH_ASSOC);

		/* Verifica se a Bandeja já tem um registro com BandeTabela: OrdemCompra e BandeTabelaId: IdOrdemCompraAtual, evitando duplicação */
		$sql = "
			SELECT COUNT(BandeId) as Count
				FROM Bandeja
			 WHERE BandeTabela = 'TermoReferencia' 
			   AND BandeTabelaId =  ".$iTrId;
		$result = $conn->query($sql);
		$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
		$count = $rowBandeja['Count'];

		$sql = "
			SELECT BandeId, SituaChave
				FROM Bandeja
				JOIN Situacao on SituaId = BandeStatus
			 WHERE BandeTabela = 'TermoReferencia' 
			   AND BandeTabelaId =  ".$iTrId;
		$result = $conn->query($sql);
		$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);

		if ($count == 0){

			/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
			$sIdentificacao = 'Termo de Referência (Nº Termo: '.$rowTermoReferencia['TrRefNumero'].' | Data: '.$rowTermoReferencia['TrRefData'].' | Tipo: '.$rowTermoReferencia['TrRefTipo'] == 'S' ? 'Serviços' : $rowTermoReferencia['TrRefTipo'] == 'P' ? 'Produtos' : $rowTermoReferencia['TrRefTipo'] == 'PS' && 'Produtos e Serviços'.')';
		
			$sql = "
				INSERT INTO 
					Bandeja (
						BandeIdentificacao, 
						BandeData, 
						BandeDescricao, 
						BandeURL, 
						BandeSolicitante, 
						BandeSolicitanteSetor, 
						BandeTabela, 
						BandeTabelaId, 
						BandeStatus, 
						BandeUsuarioAtualizador, 
						BandeUnidade,
						BandePerfil
					)
				VALUES (
					:sIdentificacao, 
					:dData, 
					:sDescricao, 
					:sURL, 
					:iSolicitante, 
					:iSolicitanteSetor, 
					:sTabela, 
					:iTabelaId, 
					:iStatus, 
					:iUsuarioAtualizador, 
					:iUnidade,
					:sPerfil
				)
			";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sIdentificacao' 			=> $sIdentificacao,
				':dData' 								=> date("Y-m-d"),
				':sDescricao' 					=> 'Liberar Termo de Referência',
				':sURL' 								=> '',
				':iSolicitante' 				=> $_SESSION['UsuarId'],
				':iSolicitanteSetor' 		=> null,
				':sTabela' 							=> 'TermoReferencia',
				':iTabelaId' 						=> $iTrId,
				':iStatus' 							=> $rowSituacao['SituaId'],
				':iUsuarioAtualizador' 	=> $_SESSION['UsuarId'],
				':iUnidade' 						=> $_SESSION['UnidadeId'],
				':sPerfil' 							=> 'CONTABILIDADE',
			));

			$insertId = $conn->lastInsertId();

			foreach ($rowPerfil as $item){
				$sql = "
					INSERT INTO 
						BandejaXPerfil (
							BnXPeBandeja, 
							BnXPePerfil, 
							BnXPeUnidade
						)
					VALUES (
						:iBandeja, 
						:iPerfil, 
						:iUnidade
					)
				";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
					':iBandeja' => $insertId,
					':iPerfil' 	=> $item['PerfiId'],
					':iUnidade' => $_SESSION['UnidadeId']
				));					
			}
			/* Fim Insere Bandeja */

		} else{

			$sql = "
				UPDATE Bandeja 
				  SET BandeData = :dData, 
						  BandeSolicitante = :iSolicitante, 
							BandeStatus = :iStatus, 
							BandeUsuarioAtualizador = :iUsuarioAtualizador,
							BandePerfil = 'CONTABILIDADE'
				WHERE BandeUnidade = :iUnidade 
					AND BandeId = :iIdBandeja
			";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':dData' 								=> date("Y-m-d"),
				':iSolicitante' 				=> $_SESSION['UsuarId'],
				':iStatus' 							=> $rowSituacao['SituaId'],
				':iUsuarioAtualizador' 	=> $_SESSION['UsuarId'],
				':iUnidade' 						=> $_SESSION['UnidadeId'],
				':iIdBandeja' 					=> $rowBandeja['BandeId']
			));

			/* Deleta os perfis da bandeja */ 
			$sql = "
				DELETE FROM BandejaXPerfil
							WHERE BnXPeBandeja = :iIdBandeja
			";
			$result = $conn->prepare($sql);
			$result->execute(array(
				':iIdBandeja' => $rowBandeja['BandeId']
			));

			/* Cria os perfis da bandeja */ 
			foreach ($rowPerfil as $item){
				$sql = "
					INSERT INTO 
						BandejaXPerfil (
							BnXPeBandeja,
							BnXPePerfil,
							BnXPeUnidade
						)
					VALUES (
						:iBandeja, 
						:iPerfil, 
						:iUnidade
					)
				";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
					':iBandeja' => $rowBandeja['BandeId'],
					':iPerfil' 	=> $item['PerfiId'],
					':iUnidade' => $_SESSION['UnidadeId'],
				));					
			}
		}

		$conn->commit();
        
		$_SESSION['msg']['titulo'] 		= "Sucesso";
		$_SESSION['msg']['mensagem'] 	= "Termo de Referência enviado para aprovação!!!";
		$_SESSION['msg']['tipo'] 			= "success";      		
	}

} catch(PDOException $e){

    $conn->rollback();
		
    $_SESSION['msg']['titulo'] 		= "Erro";
    $_SESSION['msg']['mensagem'] 	= "Erro ao enviar Termo de Referência para aprovação!!!";
    $_SESSION['msg']['tipo'] 			= "error";	

    echo 'Error1: ' . $e->getMessage();
}

irpara("tr.php");

?>
