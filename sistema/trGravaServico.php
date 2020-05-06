<?php
	
	if (isset($_POST['inputTRId'])){
		$insertId = $_POST['inputTRId'];
	}

	if ($possuiSubCategoria){

		foreach ($_POST['cmbSubCategoria'] as $value) {
		
			if ($rowParametro['ParamServicoOrcamento']) {

				$sql = "SELECT SrOrcId
						FROM ServicoOrcamento
						JOIN Situacao on SituaId = SrOrcStatus
						WHERE SrOrcSubcategoria = " . $value . " and SituaChave = 'ATIVO'";
			} else{
				
				$sql = "SELECT ServiId
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
						':iServico' => $rowParametro['ParamServicoOrcamento'] ? $servico['SrOrcId'] : $servico['ServiId'],
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

			$sql = "SELECT SrOrcId
					FROM ServicoOrcamento
					JOIN Situacao on SituaId = SrOrcStatus
					WHERE SrOrcCategoria = " . $value . " and SituaChave = 'ATIVO'";
		} else{
			
			$sql = "SELECT ServiId
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
					':iServico' => $rowParametro['ParamServicoOrcamento'] ? $servico['SrOrcId'] : $servico['ServiId'],
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