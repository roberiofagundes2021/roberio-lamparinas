<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Aprovação - Centro Administrativo';

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	$iTrId = $_POST['inputTRId'];

	function validTr() {
		include('global_assets/php/conexao.php');
		$iTrId = $_POST['inputTRId'];

		$sql = "
			SELECT TrRefTipo
				FROM TermoReferencia
			 WHERE TrRefId = ".$iTrId."
		";
		$result = $conn->query($sql);
		$rowTipoTr = $result->fetch(PDO::FETCH_ASSOC);

		if (isset($rowTipoTr['TrRefTipo']) && $rowTipoTr['TrRefTipo'] === 'S') {
			$countValidationServices = 0;

			$sql = "
				SELECT COUNT(TRXSrTermoReferencia) as countServico
					FROM TermoReferenciaXServico
				 WHERE TRXSrTermoReferencia = ".$iTrId." 
					 AND TRXSrQuantidade <= 0
			";
			$result = $conn->query($sql);
			$rowServico = $result->fetch(PDO::FETCH_ASSOC);

			$countValidationServices = isset($rowServico['countProduto']) ? intval($rowServico['countProduto']) : 0;
			
			if($countValidationServices > 0){
				return 'S';
			} else {
				return true;
			}
		} else if (isset($rowTipoTr['TrRefTipo']) && $rowTipoTr['TrRefTipo'] === 'P') {
			$countValidationProducts = 0;

			$sql = "
				SELECT COUNT(TRXPrTermoReferencia) as countProduto
					FROM TermoReferenciaXProduto
				 WHERE TRXPrTermoReferencia = ".$iTrId." 
					 AND TRXPrQuantidade <= 0
			";
			$result = $conn->query($sql);
			$rowProduto = $result->fetch(PDO::FETCH_ASSOC);

			$countValidationProducts = isset($rowProduto['countProduto']) ? intval($rowProduto['countProduto']) : 0;

			if($countValidationProducts > 0){
				return 'P';
			} else {
				return true;
			}
		} else if (isset($rowTipoTr['TrRefTipo']) && $rowTipoTr['TrRefTipo'] === 'PS') {
			$countValidationServices = 0;
			$countValidationProducts = 0;
			$rowProduto = '';

			$sql = "
				SELECT COUNT(TRXPrTermoReferencia) as countProduto
					FROM TermoReferenciaXProduto
				 WHERE TRXPrTermoReferencia = ".$iTrId." 
					 AND TRXPrQuantidade <= 0
			";
			$result = $conn->query($sql);
			$rowProduto = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "
				SELECT COUNT(TRXSrTermoReferencia) as countServico
					FROM TermoReferenciaXServico
				 WHERE TRXSrTermoReferencia = ".$iTrId." 
					 AND TRXSrQuantidade <= 0
			";
			$result = $conn->query($sql);
			$rowServico = $result->fetch(PDO::FETCH_ASSOC);

			$countValidationServices = isset($rowServico['countProduto']) ? intval($rowServico['countProduto']) : 0;
			$countValidationProducts = isset($rowProduto['countProduto']) ? intval($rowProduto['countProduto']) : 0;

			if(($countValidationProducts > 0) || ($countValidationServices > 0)){
				return 'PS';
			} else {
				return true;
			}
		}
	}

	$validacaoTipo = validTr();

	if ($validacaoTipo === true) {
		try{
			$conn->beginTransaction();

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
				 WHERE PerfiChave IN ('ADMINISTRADOR', 'CENTROADMINISTRATIVO');
			";
			$result = $conn->query($sql);
			$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);

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
				$tipo = $rowTermoReferencia['TrRefTipo'] == 'S' 
					? 'Serviços' : $rowTermoReferencia['TrRefTipo'] == 'P' 
					? 'Produtos' : $rowTermoReferencia['TrRefTipo'] == 'PS' 
				 && 'Produtos e Serviços';

				/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
				$sIdentificacao = '
					Termo de Referência (Nº Termo: '.$rowTermoReferencia['TrRefNumero'].' | Data: '.mostradata($rowTermoReferencia['TrRefData']).' | Tipo: '.$tipo.')
				';
			
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
					':sPerfil' 							=> 'CENTROADMINISTRATIVO'
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
								 BandePerfil = 'CENTROADMINISTRATIVO'
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

		} catch(PDOException $e){

			$conn->rollback();
			
			$_SESSION['msg']['titulo'] 		= "Erro";
			$_SESSION['msg']['mensagem'] 	= "Erro ao enviar Termo de Referência para aprovação!!!";
			$_SESSION['msg']['tipo'] 			= "error";	

			echo 'Error Message: ' . $e->getMessage().'| Line:  '.$e->getLine();
		}
	
	} else if($validacaoTipo === 'P') {
		$_SESSION['msg']['titulo'] 		= "Erro";
		$_SESSION['msg']['mensagem'] 	= "Existem produtos sem quantidade. Preencha todas as quantidades do termo de referência antes de enviar para aprovação!!!";
		$_SESSION['msg']['tipo'] 			= "error";	

	} else if($validacaoTipo === 'S') {
		$_SESSION['msg']['titulo'] 		= "Erro";
		$_SESSION['msg']['mensagem'] 	= "Existem serviços sem quantidade. Preencha todas as quantidades do termo de referência antes de enviar para aprovação!!!";
		$_SESSION['msg']['tipo'] 			= "error";	

	} else if ($validacaoTipo === 'PS') {
		$_SESSION['msg']['titulo'] 		= "Erro";
		$_SESSION['msg']['mensagem'] 	= "Existem produtos e serviços sem quantidade. Preencha todas as quantidades do termo de referência antes de enviar para aprovação!!!";
		$_SESSION['msg']['tipo'] 			= "error";	
	}
}

irpara("tr.php");

?>
