<?php
	
	if ($possuiSubCategoria){
		$insertId = $_POST['inputTRId'];
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

				$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
						VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
				$result = $conn->prepare($sql);

				if ($servico) {

					$result->execute(array(
						':iTR' => $insertId,
						':iServico' => $rowParametro['ParamServicoOrcamento'] ? $servico['SrOrcId'] : $servico['ServiId'],
						':iQuantidade' => null,
						':fValorUnitario' => null,
						':sTabela' => $parametroServico,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId'],
					));
				}
			}
		}
		
	} else {
		$insertId = $_POST['inputTRId'];

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

			$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
					VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);

			if ($servico) {

				$result->execute(array(
					':iTR' => $insertId,
					':iServico' => $rowParametro['ParamServicoOrcamento'] ? $servico['SrOrcId'] : $servico['ServiId'],
					':iQuantidade' => null,
					':fValorUnitario' => null,
					':sTabela' => $parametroServico,
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId'],
				));
			}
		}	
	}
?>