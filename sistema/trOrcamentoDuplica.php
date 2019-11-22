<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'TR Orçamento Duplica';

include('global_assets/php/conexao.php');

if (isset($_POST['inputOrcamentoId'])){

	$sql = "SELECT TrXOrId, TrXOrTermoReferencia, TrXOrTipo, TrXOrCategoria, TrXOrConteudo, TrXOrStatus
			FROM TRXOrcamento
			WHERE TrXOrEmpresa = ". $_SESSION['EmpreId'] ." and TrXOrId = ".$_POST['inputOrcamentoId']."";
	$result = $conn->query($sql);
	$rowOrcamento = $result->fetch(PDO::FETCH_ASSOC);
	//$count = count($rowOrcamento);
	
	$sql = "SELECT COUNT(isnull(TrXOrNumero,0)) as Numero
			 FROM TRXOrcamento
			 Where TrXOrEmpresa = ".$_SESSION['EmpreId']."";
	$result = $conn->query($sql);
	$rowNumero = $result->fetch(PDO::FETCH_ASSOC);		
	
	$sNumero = (int)$rowNumero['Numero'] + 1;
	$sNumero = str_pad($sNumero,6,"0",STR_PAD_LEFT);
		
	$sql = "INSERT INTO TRXOrcamento (TrXOrTermoReferencia, TrXOrNumero, TrXOrTipo, TrXOrData, TrXOrCategoria, TrXOrConteudo, TrXOrFornecedor,
								   TrXOrSolicitante, TrXOrStatus, TrXOrUsuarioAtualizador, TrXOrEmpresa)
			VALUES (:sTR, :sNumero, :sTipo, :dData, :iCategoria, :sConteudo, :iFornecedor, :iSolicitante, 
					:bStatus, :iUsuarioAtualizador, :iEmpresa)";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
		            ':sTR' => $rowOrcamento['TrXOrTermoReferencia'],
					':sNumero' => $sNumero,
					':sTipo' => $rowOrcamento['TrXOrTipo'],
					':dData' => gravaData(date('d/m/Y')),
					':iCategoria' => $rowOrcamento['TrXOrCategoria'] == '' ? null : $rowOrcamento['TrXOrCategoria'],
					':sConteudo' => $rowOrcamento['TrXOrConteudo'],
					':iFornecedor' => null,
					':iSolicitante' => $_SESSION['UsuarId'],
					':bStatus' => $rowOrcamento['TrXOrStatus'],
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId']
					));						

	$insertId = $conn->lastInsertId();

	$sql = "SELECT *
			FROM TRXOrcamentoXProduto
			WHERE TXOXPEmpresa = ". $_SESSION['EmpreId'] ." and TXOXPOrcamento = ".$_POST['inputOrcamentoId']."";
	$result = $conn->query($sql);
	$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rowProduto as $item){ 
		try {
		$sql = "INSERT INTO TRXOrcamentoXProduto (TXOXPOrcamento, TXOXPProduto, TXOXPQuantidade, TXOXPValorUnitario
, TXOXPUsuarioAtualizador, TXOXPEmpresa)
				VALUES (:iOrcamento, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iOrcamento' => $insertId,
						':iProduto' => $item['TXOXPProduto'],
						':iQuantidade' => $item['TXOXPQuantidade'],
						':fValorUnitario' => null,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		} catch(PDOException $e) {
			echo 'Error2: ' . $e->getMessage();die;
		}
	}


    // Select Subcategoria
	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN TRXOrcamentoXSubcategoria on TXOXSCSubcategoria = SbCatId
			WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and TXOXSCOrcamento = ".$rowOrcamento['TrXOrId']."";
	$result = $conn->query($sql);
	$rowSBC = $result->fetchAll(PDO::FETCH_ASSOC);

	$sql = "INSERT INTO TRXOrcamentoXSubcategoria 
							(TXOXSCOrcamento, TXOXSCSubcategoria, TXOXSCEmpresa)
						VALUES 
							(:iOrcamento, :iSubCategoria, :iEmpresa)";
				$result = $conn->prepare($sql);

				foreach ($rowSBC as $subcategoria){

					$result->execute(array(
									':iOrcamento' => $insertId,
									':iSubCategoria' => $subcategoria['SbCatId'],
									':iEmpresa' => $_SESSION['EmpreId']
									));
				}

	
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Orçamento duplicado!!!";
	$_SESSION['msg']['tipo'] = "success";	

} else {
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao duplicar orçamento!!!";
	$_SESSION['msg']['tipo'] = "error";		
}	

irpara("trOrcamento.php");
	
?>
