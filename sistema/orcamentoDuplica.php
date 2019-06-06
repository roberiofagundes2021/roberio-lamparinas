<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Orçamento Duplica';

include('global_assets/php/conexao.php');

if (isset($_POST['inputOrcamentoId'])){

	$sql = ("SELECT OrcamId, OrcamNumero, OrcameTipo, OrcamCategoria, OrcamConteudo, OrcamFornecedor, OrcamStatus
			 FROM Orcamento
			 LEFT JOIN Fornecedor on ForneId = OrcamFornecedor
			 JOIN Categoria on CategId = OrcamCategoria
			 LEFT JOIN SubCategoria on SbCatId = OrcamSubCategoria
			 WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ." and OrcamId = ".$_POST['inputOrcamentoId']."
			 ORDER BY OrcamData DESC");
	$result = $conn->query("$sql");
	$rowOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);
	//$count = count($row);
	
	
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
	
	$aFornecedor = explode("#",$_POST['cmbFornecedor']);
	$iFornecedor = $aFornecedor[0];
	
	$result->execute(array(
					':sNumero' => $sNumero,
					':sTipo' => $rowOrcamento['OrcamTipo'],
					':dData' => gravaData(date('d/m/Y')),
					':iCategoria' => $rowOrcamento['OrcamCategoria'] == '' ? null : $rowOrcamento['OrcamCategoria'],
					':iSubCategoria' => $rowOrcamento['OrcamSubCategoria'] == '' ? null : $rowOrcamento['OrcamSubCategoria'],
					':sConteudo' => $_POST['txtareaConteudo'],
					':iFornecedor' => $iFornecedor,
					':iSolicitante' => $_SESSION['UsuarId'],
					':bStatus' => 1,
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iEmpresa' => $_SESSION['EmpreId']
					));	

} else {
	irpara("orcamento.php");
}	
	
?>