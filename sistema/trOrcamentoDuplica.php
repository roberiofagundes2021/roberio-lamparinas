<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Orçamento Duplica';

include('global_assets/php/conexao.php');

if (isset($_POST['inputOrcamentoId'])){

	$sql = "SELECT OrcamId, OrcamTipo, OrcamCategoria, OrcamSubCategoria, OrcamConteudo, OrcamFornecedor, OrcamStatus
			FROM Orcamento
			WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ." and OrcamId = ".$_POST['inputOrcamentoId']."";
	$result = $conn->query($sql);
	$rowOrcamento = $result->fetch(PDO::FETCH_ASSOC);
	//$count = count($rowOrcamento);
	
	$sql = ("SELECT COUNT(isnull(OrcamNumero,0)) as Numero
			 FROM Orcamento
			 Where OrcamEmpresa = ".$_SESSION['EmpreId']."");
	$result = $conn->query("$sql");
	$rowNumero = $result->fetch(PDO::FETCH_ASSOC);		
	
	$sNumero = (int)$rowNumero['Numero'] + 1;
	$sNumero = str_pad($sNumero,6,"0",STR_PAD_LEFT);
		
	$sql = "INSERT INTO Orcamento (OrcamNumero, OrcamTipo, OrcamData, OrcamCategoria, OrcamSubCategoria, OrcamConteudo, OrcamFornecedor,
								   OrcamSolicitante, OrcamStatus, OrcamUsuarioAtualizador, OrcamEmpresa)
			VALUES (:sNumero, :sTipo, :dData, :iCategoria, :iSubCategoria, :sConteudo, :iFornecedor, :iSolicitante, 
					:bStatus, :iUsuarioAtualizador, :iEmpresa)";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':sNumero' => $sNumero,
					':sTipo' => $rowOrcamento['OrcamTipo'],
					':dData' => gravaData(date('d/m/Y')),
					':iCategoria' => $rowOrcamento['OrcamCategoria'] == '' ? null : $rowOrcamento['OrcamCategoria'],
					':iSubCategoria' => $rowOrcamento['OrcamSubCategoria'] == '' ? null : $rowOrcamento['OrcamSubCategoria'],
					':sConteudo' => $rowOrcamento['OrcamConteudo'],
					':iFornecedor' => $rowOrcamento['OrcamFornecedor'],
					':iSolicitante' => $_SESSION['UsuarId'],
					':bStatus' => $rowOrcamento['OrcamStatus'],
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId']
					));						

	$insertId = $conn->lastInsertId();

	$sql = "SELECT *
			FROM OrcamentoXProduto
			WHERE OrXPrEmpresa = ". $_SESSION['EmpreId'] ." and OrXPrOrcamento = ".$_POST['inputOrcamentoId']."";
	$result = $conn->query($sql);
	$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rowProduto as $item){
		try {
		$sql = "INSERT INTO OrcamentoXProduto (OrXPrOrcamento, OrXPrProduto, OrXPrQuantidade, OrXPrValorUnitario, OrXPrUsuarioAtualizador, OrXPrEmpresa)
				VALUES (:iOrcamento, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iOrcamento' => $insertId,
						':iProduto' => $item['OrXPrProduto'],
						':iQuantidade' => $item['OrXPrQuantidade'],
						':fValorUnitario' => $item['OrXPrValorUnitario'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		} catch(PDOException $e) {
			echo 'Error2: ' . $e->getMessage();die;
		}
	}
	
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Orçamento duplicado!!!";
	$_SESSION['msg']['tipo'] = "success";	

} else {
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao duplicar orçamento!!!";
	$_SESSION['msg']['tipo'] = "error";		
}	

irpara("orcamento.php");
	
?>
