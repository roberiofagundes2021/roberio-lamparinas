<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "SELECT TrRefTipo
		FROM TermoReferencia
		WHERE TRXPrTermoReferencia = ".$_POST['iTr']." and TRXPrEmpresa = ".$_SESSION['EmpreId'];
$result = $conn->query($sql);
$rowTipo = $result->fetch(PDO::FETCH_ASSOC);

if ($rowTipo['TrRefTipo'] == 'P'){

	$sql = "SELECT COUNT(TRXPrProduto) as Qtde
			FROM TermoReferenciaXProduto
			WHERE (TRXPrQuantidade is null or TRXPrQuantidade = 0) and TRXPrTermoReferencia = ".$_POST['iTr']." and TRXPrEmpresa = ".$_SESSION['EmpreId'];
			
} else if ($rowTipo['TrRefTipo'] == 'S'){
	
	$sql = "SELECT COUNT(TRXSrServico) as Qtde
			FROM TermoReferenciaXServico
			WHERE (TRXSrQuantidade is null or TRXSrQuantidade = 0) and TRXSrTermoReferencia = ".$_POST['iTr']." and TRXSrEmpresa = ".$_SESSION['EmpreId'];	
			
} else {
	$sql = "SELECT Sum(qtde) as Qtde
			FROM
			(
			(SELECT COUNT(TRXPrTermoReferencia) qtde
			 FROM TermoReferenciaXProduto
			 WHERE (TRXPrQuantidade is null or TRXPrQuantidade = 0) and TRXPrTermoReferencia = ".$_POST['iTr']." and TRXPrEmpresa = ".$_SESSION['EmpreId'].")
			UNION
			(SELECT COUNT(TRXSrTermoReferencia) qtde
			 FROM TermoReferenciaXServico
			 WHERE (TRXSrQuantidade is null or TRXSrQuantidade = 0) and TRXSrTermoReferencia = ".$_POST['iTr']." and TRXSrEmpresa = ".$_SESSION['EmpreId'].")
			) as Soma";
}

$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = $row['Qtde'];
	
if($count){		
	echo 1;
} else{
	echo 0;
}

?>
