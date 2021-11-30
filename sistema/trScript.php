<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Termo de Referência';

include('global_assets/php/conexao.php');

try {

	$conn->beginTransaction();

	/* Detalhamento para todos os TRs já cadastrados */
	$sql = "SELECT EmpreId, EmpreNomeFantasia
			FROM Empresa
			Where EmpreId in (8, 10, 11)";
	$result = $conn->query($sql);
	$rowEmpresa = $result->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rowEmpresa as $itemEmpresa){

		$sql = "SELECT ParamProdutoOrcamento, ParamServicoOrcamento
				FROM Parametro
				WHERE ParamEmpresa = " . $itemEmpresa['EmpreId'];
		$result = $conn->query($sql);
		$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

		isset($rowParametro['ParamProdutoOrcamento']) && $rowParametro['ParamProdutoOrcamento'] == 1 ? $parametroProduto = 'ProdutoOrcamento' : $parametroProduto = 'Produto';
		isset($rowParametro['ParamServicoOrcamento']) && $rowParametro['ParamServicoOrcamento'] == 1 ? $parametroServico = 'ServicoOrcamento' : $parametroServico = 'Servico';

		$sql = "SELECT UnidaId, UnidaNome
				FROM Unidade
				WHERE UnidaEmpresa = ".$itemEmpresa['EmpreId'];
		$result = $conn->query($sql);
		$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);

		foreach ($rowUnidade as $itemUnidade){

			$sql = "SELECT TrRefId
					FROM TermoReferencia
					WHERE TrRefUnidade = " . $itemUnidade['UnidaId'];
			$result = $conn->query($sql);
			$rowTR = $result->fetchAll(PDO::FETCH_ASSOC);		

			foreach ($rowTR as $itemTR){
			
				$iTR = $itemTR['TrRefId'];

				$sql = "SELECT TRXPrProduto
						FROM TermoReferenciaXProduto
						WHERE TRXPrTermoReferencia = " . $iTR;
				$result = $conn->query($sql);
				$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);
				$countProduto = count($rowProduto);

				if ($countProduto){
					
					if ($parametroProduto == 'ProdutoOrcamento'){
					
						foreach ($rowProduto as $itemProduto){
							
							$sql = "SELECT PrOrcDetalhamento
									FROM ProdutoOrcamento
									WHERE PrOrcId = " . $itemProduto['TRXPrProduto'];
							$result = $conn->query($sql);
							$rowProdutoDet = $result->fetch(PDO::FETCH_ASSOC);
	
							$sql = "UPDATE TermoReferenciaXProduto SET TRXPrDetalhamento = :sDetalhamento
									WHERE TRXPrProduto = :iProduto";
							$result = $conn->prepare($sql);
					
							$result->execute(array(
								':sDetalhamento' => $rowProdutoDet['PrOrcDetalhamento'],
								':iProduto' => $itemProduto['TRXPrProduto']
							));	
						}					
					} else {
						foreach ($rowProduto as $itemProduto){
							
							$sql = "SELECT ProduDetalhamento
									FROM Produto
									WHERE ProduId = " . $itemProduto['TRXPrProduto'];
							$result = $conn->query($sql);
							$rowProdutoDet = $result->fetch(PDO::FETCH_ASSOC);

							$sql = "UPDATE TermoReferenciaXProduto SET TRXPrDetalhamento = :sDetalhamento
									WHERE TRXPrProduto = :iProduto";
							$result = $conn->prepare($sql);
					
							$result->execute(array(
								':sDetalhamento' => $rowProdutoDet['ProduDetalhamento'],
								':iProduto' => $itemProduto['TRXPrProduto']
							));
						}
					}	
				}

				$sql = "SELECT TRXSrServico
						FROM TermoReferenciaXServico
						WHERE TRXSrTermoReferencia = " . $iTR;
				$result = $conn->query($sql);
				$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);
				$countServico = count($rowServico);

				if ($countServico){				

					if ($parametroServico == 'ServicoOrcamento'){
						
						foreach ($rowServico as $itemServico){
							
							$sql = "SELECT SrOrcDetalhamento
									FROM ServicoOrcamento
									WHERE SrOrcId = " . $itemServico['TRXSrServico'];
							$result = $conn->query($sql);
							$rowServicoDet = $result->fetch(PDO::FETCH_ASSOC);
							
							$sql = "UPDATE TermoReferenciaXServico SET TRXSrDetalhamento = :sDetalhamento
									WHERE TRXSrServico = :iServico";
							$result = $conn->prepare($sql);
					
							$result->execute(array(
								':sDetalhamento' => $rowServicoDet['SrOrcDetalhamento'],
								':iServico' => $itemServico['TRXSrServico']
							));
						}
					
					} else {
						foreach ($rowServico as $itemServico){
							
							$sql = "SELECT ServiDetalhamento
									FROM Servico
									WHERE ServiId = " . $itemServico['TRXSrServico'];
							$result = $conn->query($sql);
							$rowServicoDet = $result->fetch(PDO::FETCH_ASSOC);

							$sql = "UPDATE TermoReferenciaXServico SET TRXSrDetalhamento = :sDetalhamento
									WHERE TRXSrServico = :iServico";
							$result = $conn->prepare($sql);
						
							$result->execute(array(
								':sDetalhamento' => $rowServicoDet['ServiDetalhamento'],
								':iServico' => $itemServico['TRXSrServico']
							));
						}
					}
				}

			}

			echo "Unidade: ". $itemUnidade['UnidaNome']. " concluída com sucesso!";
			echo "<br>";
		}

		echo "<b>Empresa: ". $itemEmpresa['EmpreNomeFantasia']. " concluído com sucesso!</b>";
		echo "<br><hr /><br>";
	}

	$conn->commit();

} catch (PDOException $e) {

	$conn->rollback();
	echo 'Error1: ' . $e->getMessage();
}

?>
