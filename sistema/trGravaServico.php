<?php
	
	if (isset($_POST['inputTRId'])){
		$insertId = $_POST['inputTRId'];
	}

	if ($possuiSubCategoria){

		foreach ($_POST['cmbSubCategoria'] as $value) {
		
			if ($rowParametro['ParamServicoOrcamento']) {

				$sql = "SELECT SrOrcId as idServico
						FROM ServicoOrcamento
						JOIN Situacao on SituaId = SrOrcSituacao
						WHERE SrOrcSubcategoria = " . $value . " and SituaChave = 'ATIVO'";
			} else{
				
				$sql = "SELECT ServiId as idServico
						FROM Servico
						JOIN Situacao on SituaId = ServiStatus
						WHERE ServiSubCategoria = " . $value . " and SituaChave = 'ATIVO'";						
			}
			
			$result = $conn->query($sql);
			$servicos = $result->fetchAll(PDO::FETCH_ASSOC);

			foreach ($servicos as $servico) {

				$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrUnidade)
						VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iUnidade)";
				$result = $conn->prepare($sql);

				if ($servico) {

					$result->execute(array(
						':iTR' => $insertId,
						':iServico' => $servico['idServico'],
						':iQuantidade' => null,
						':fValorUnitario' => null,
						':sTabela' => $parametroServico,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId'],
					));
				}
			}
		}
		
	} else {

		$value = $_POST['cmbCategoria'];
	
		if ($rowParametro['ParamServicoOrcamento']) {

			$sql = "SELECT SrOrcId as idServico
					FROM ServicoOrcamento
					JOIN Situacao on SituaId = SrOrcSituacao
					WHERE SrOrcCategoria = " . $value . " and SituaChave = 'ATIVO'";
		} else{
			
			$sql = "SELECT ServiId as idServico
					FROM Servico
					JOIN Situacao on SituaId = ServiStatus
					WHERE ServiCategoria = " . $value . " and SituaChave = 'ATIVO'";						
		}
		
		$result = $conn->query($sql);
		$servicos = $result->fetchAll(PDO::FETCH_ASSOC);

		foreach ($servicos as $servico) {

			$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrUnidade)
					VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);

			if ($servico) {

				$result->execute(array(
					':iTR' => $insertId,
					':iServico' => $servico['idServico'],
					':iQuantidade' => null,
					':fValorUnitario' => null,
					':sTabela' => $parametroServico,
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iUnidade' => $_SESSION['UnidadeId'],
				));
			}
		}	
	}
?>