<?php

	if (isset($_POST['inputTRId'])){
		$insertId = $_POST['inputTRId'];
	}

	if ($possuiSubCategoria){

		foreach ($_POST['cmbSubCategoria'] as $value) {					
			
			if ($rowParametro['ParamProdutoOrcamento']) {
			
				$sql = "SELECT PrOrcId
						FROM ProdutoOrcamento
						JOIN Situacao on SituaId = PrOrcStatus
						WHERE PrOrcSubcategoria = " . $value . " and SituaChave = 'ATIVO'";
			} else{
			
				$sql = "SELECT ProduId
						FROM Produto
						JOIN Situacao on SituaId = ProduStatus
						WHERE ProduSubCategoria = " . $value . " and SituaChave = 'ATIVO'";
			}
			
			$result = $conn->query($sql);
			$produtos = $result->fetchAll(PDO::FETCH_ASSOC);
		
			foreach ($produtos as $produto) {

				$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
						VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
				$result = $conn->prepare($sql);

				if ($produto) {

					$result->execute(array(
						':iTR' => $insertId,
						':iProduto' => $rowParametro['ParamProdutoOrcamento'] ? $produto['PrOrcId'] : $produto['ProduId'],
						':iQuantidade' => null,
						':fValorUnitario' => null,
						':sTabela' => $parametroProduto,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId'],
					));
				}
			}
		}
		
	} else {
		
		$value = $_POST['cmbCategoria'];
			
		if ($rowParametro['ParamProdutoOrcamento']) {
		
			$sql = "SELECT PrOrcId
					FROM ProdutoOrcamento
					JOIN Situacao on SituaId = PrOrcStatus
					WHERE PrOrcCategoria = " . $value . " and SituaChave = 'ATIVO'";
		} else {
		
			$sql = "SELECT ProduId
					FROM Produto
					JOIN Situacao on SituaId = ProduStatus
					WHERE ProduCategoria = " . $value . " and SituaChave = 'ATIVO'";
		}
		
		$result = $conn->query($sql);
		$produtos = $result->fetchAll(PDO::FETCH_ASSOC);
	
		foreach ($produtos as $produto) {

			$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
					VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);

			if ($produto) {

				$result->execute(array(
					':iTR' => $insertId,
					':iProduto' => $rowParametro['ParamProdutoOrcamento'] ? $produto['PrOrcId'] : $produto['ProduId'],
					':iQuantidade' => null,
					':fValorUnitario' => null,
					':sTabela' => $parametroProduto,
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId'],
				));
			}
		}
	}
?>