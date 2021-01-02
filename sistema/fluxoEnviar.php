<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

try{
	if(isset($_POST['inputIdFluxoOperacional'])){

        $conn->beginTransaction();
	
		$iFluxoOperacional = $_POST['inputIdFluxoOperacional'];

		/* Atualiza o Status da Fluxo Operacional para "Aguardando Liberação" */
		$sql = "SELECT SituaId
				FROM Situacao
				Where SituaChave = 'AGUARDANDOLIBERACAO' ";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :iStatus
	            WHERE FlOpeId = :iFluxoOperacional";
	    $result = $conn->prepare($sql);

	    $result->execute(array(
			':iStatus' => $rowSituacao['SituaId'],
			':iFluxoOperacional' => $iFluxoOperacional					
			));
        /* Fim Atualiza */

		$sql = "SELECT PerfiId
				FROM Perfil
				Where PerfiChave IN ('ADMINISTRADOR','CONTROLADORIA') ";
		$result = $conn->query($sql);
		$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT FlOpeNumContrato, FlOpeNumProcesso
				FROM FluxoOperacional
				Where FlOpeId = ".$iFluxoOperacional;
		$result = $conn->query($sql);
        $rowFluxoOperacional = $result->fetch(PDO::FETCH_ASSOC);     

		/* Verifica se a Bandeja já tem um registro com BandeTabela: FluxoOperacional e BandeTabelaId: IdFluxoOperacionalAtual, evitando duplicação */
		$sql = "SELECT COUNT(BandeId) as Count
				FROM Bandeja
				Where BandeTabela = 'FluxoOperacional' and BandeTabelaId =  ".$iFluxoOperacional;
		$result = $conn->query($sql);
		$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
		$count = $rowBandeja['Count'];

		$sql = "SELECT BandeId, SituaChave
				FROM Bandeja
				JOIN Situacao on SituaId = BandeStatus
				Where BandeTabela = 'FluxoOperacional' and BandeTabelaId =  ".$iFluxoOperacional;
		$result = $conn->query($sql);
        $rowBandeja = $result->fetch(PDO::FETCH_ASSOC);   

		if ($count == 0){
                            
            /* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
            $sIdentificacao = 'Fluxo Operacional (Nº Contrato: '.$rowFluxo['FlOpeNumContrato'].' | Nº Processo: '.$rowFluxo['FlOpeNumProcesso'].')';
        
            $sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandeSolicitante, 
                    BandeSolicitanteSetor, BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, BandeUnidade)
                    VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iSolicitante, :iSolicitanteSetor, :sTabela, 
					:iTabelaId, :iStatus, :iUsuarioAtualizador, :iUnidade)";
            $result = $conn->prepare($sql);
                    
            $result->execute(array(
                            ':sIdentificacao' => $sIdentificacao,
                            ':dData' => date("Y-m-d"),
                            ':sDescricao' => 'Liberar Fluxo',
                            ':sURL' => '',
							':iSolicitante' => $_SESSION['UsuarId'],
							':iSolicitanteSetor' => null,
                            ':sTabela' => 'FluxoOperacional',
                            ':iTabelaId' => $iFluxoOperacional,
                            ':iStatus' => $rowSituacao['SituaId'],
                            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                            ':iUnidade' => $_SESSION['UnidadeId']
                            ));                            

			$insertId = $conn->lastInsertId();

			foreach ($rowPerfil as $item){
			
				$sql = "INSERT INTO BandejaXPerfil (BnXPeBandeja, BnXPePerfil, BnXPeUnidade)
						VALUES (:iBandeja, :iPerfil, :iUnidade)";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
								':iBandeja' => $insertId,
								':iPerfil' => $item['PerfiId'],
								':iUnidade' => $_SESSION['UnidadeId']
								));					
			}
			/* Fim Insere Bandeja */

		} else{

			$sql = "UPDATE Bandeja SET BandeData = :dData, BandeSolicitante = :iSolicitante, BandeStatus = :iStatus, 
					BandeUsuarioAtualizador = :iUsuarioAtualizador
					WHERE BandeUnidade = :iUnidade and BandeId = :iIdBandeja";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':dData' => date("Y-m-d"),
							':iSolicitante' => $_SESSION['UsuarId'],
							':iStatus' => $rowSituacao['SituaId'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iUnidade' => $_SESSION['UnidadeId'],
							':iIdBandeja' => $rowBandeja['BandeId']														
							));
        }

        $conn->commit();
        
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional enviado para aprovação!!!";
		$_SESSION['msg']['tipo'] = "success";        
	}

} catch(PDOException $e){

    $conn->rollback();
		
    $_SESSION['msg']['titulo'] = "Erro";
    $_SESSION['msg']['mensagem'] = "Erro ao enviar Fluxo Operacional para aprovação!!!";
    $_SESSION['msg']['tipo'] = "error";	

    echo 'Error1: ' . $e->getMessage();
}

irpara("fluxo.php");

?>
