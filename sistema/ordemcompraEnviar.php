<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

try{
	if(isset($_POST['inputIdOrdemCompra'])){
	
		$iOrdemCompra = $_POST['inputIdOrdemCompra'];

		/* Atualiza o Status da Ordem de Compra para "Aguardando Liberação" */
		$sql = "SELECT SituaId
				FROM Situacao
				Where SituaChave = 'AGUARDANDOLIBERACAO' ";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "UPDATE OrdemCompra SET OrComSituacao = :iStatus
	            WHERE OrComId = :iOrdemCompra";
	    $result = $conn->prepare($sql);

	    $result->execute(array(
			':iStatus' => $rowSituacao['SituaId'],
			':iOrdemCompra' => $iOrdemCompra					
			));
	    /* Fim Atualiza */

		$sql = "SELECT PerfiId
				FROM Perfil
				Where PerfiChave IN ('ADMINISTRADOR','CENTROADMINISTRATIVO') ";
		$result = $conn->query($sql);
		$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT OrComNumero, OrComNumProcesso
				FROM OrdemCompra
				Where OrComId = ".$iOrdemCompra;
		$result = $conn->query($sql);
		$rowOrdemCompra = $result->fetch(PDO::FETCH_ASSOC);

		/* Verifica se a Bandeja já tem um registro com BandeTabela: OrdemCompra e BandeTabelaId: IdOrdemCompraAtual, evitando duplicação */
		$sql = "SELECT COUNT(BandeId) as Count
				FROM Bandeja
				Where BandeTabela = 'OrdemCompra' and BandeTabelaId =  ".$iOrdemCompra;
		$result = $conn->query($sql);
		$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
		$count = $rowBandeja['Count'];

		$sql = "SELECT BandeId, SituaChave
				FROM Bandeja
				JOIN Situacao on SituaId = BandeStatus
				Where BandeTabela = 'OrdemCompra' and BandeTabelaId =  ".$iOrdemCompra;
		$result = $conn->query($sql);
		$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);

		if ($count == 0){

			/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
			$sIdentificacao = 'Ordem de Compra (Nº Ordem Compra: '.$rowOrdemCompra['OrComNumero'].' | Nº Processo: '.$rowOrdemCompra['OrComNumProcesso'].')';
		
			$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandePerfilDestino, BandeSolicitante, 
								BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, BandeEmpresa)
					VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iPerfilDestino, :iSolicitante, :sTabela, :iTabelaId, 
							:iStatus, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sIdentificacao' => $sIdentificacao,
							':dData' => date("Y-m-d"),
							':sDescricao' => 'Liberar Ordem de Compra',
							':sURL' => '',
							':iPerfilDestino' => $rowPerfil['PerfiId'],  //Tem que tirar esse campo do banco, já que agora tem uma tabela BandejaXPerfil
							':iSolicitante' => $_SESSION['UsuarId'],
							':sTabela' => 'OrdemCompra',
							':iTabelaId' => $iOrdemCompra,
							':iStatus' => $rowSituacao['SituaId'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpreId']						
							));

			$insertId = $conn->lastInsertId();

			foreach ($rowPerfil as $item){
			
				$sql = "INSERT INTO BandejaXPerfil (BnXPeBandeja, BnXPePerfil, BnXPeEmpresa)
						VALUES (:iBandeja, :iPerfil, :iEmpresa)";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
								':iBandeja' => $insertId,
								':iPerfil' => $item['PerfiId'],
								':iEmpresa' => $_SESSION['EmpreId']						
								));					
			}
			/* Fim Insere Bandeja */

		} else{

			$sql = "UPDATE Bandeja SET BandeData = :dData, BandeSolicitante = :iSolicitante, BandeStatus = :iStatus, 
					BandeUsuarioAtualizador = :iUsuarioAtualizador
					WHERE BandeEmpresa = :iEmpresa and BandeId = :iIdBandeja";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':dData' => date("Y-m-d"),
							':iSolicitante' => $_SESSION['UsuarId'],
							':iStatus' => $rowSituacao['SituaId'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpreId'],
							':iIdBandeja' => $rowBandeja['BandeId']														
							));
		}
	}

} catch(PDOException $e){

    echo 'Error1: ' . $e->getMessage();exit;
}

irpara("ordemcompra.php");

?>
