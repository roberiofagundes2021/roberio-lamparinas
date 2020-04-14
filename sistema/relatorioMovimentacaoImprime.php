<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$dDataInicio = $_POST['inputDataInicio'];
$dDataFim = $_POST['inputDataFim'];
$iTipo = isset($_POST['inputTipo']) ? $_POST['inputTipo'] : 0;
$iFornecedor = isset($_POST['cmFornecedor']) ? $_POST['cmbFornecedor'] : 0;
$iCategoria = isset($_POST['cmCategoria']) ? $_POST['cmbCategoria'] : 0;
$iSubCategoria = isset($_POST['cmSubCategoria']) ? $_POST['cmbSubCategoria'] : 0;
$sCodigo = isset($_POST['inputCodigo']) ? $_POST['inputCodigo'] : 0;
$iProduto = isset($_POST['cmProduto']) ? $_POST['cmbProduto'] : 0;


$sql = "SELECT MovimData, MovimTipo, ForneNome, LcEstNome as Origem, MovimDestinoLocal, MovimDestinoManual, 
	    ProduNome, MvXPrQuantidade, MvXPrLote, MvXPrValidade, MovimNotaFiscal, ClassNome, MvXPrValorUnitario
		FROM Movimentacao
		JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
		JOIN Produto on ProduId = MvXPrProduto
		LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
		LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		LEFT JOIN LocalEstoque on LcEstId = MovimOrigemLocal
		Where MovimEmpresa = ".$_SESSION['EmpreId']." and MovimData between '".$dDataInicio."' and '".$dDataFim."' ";

if ($iCategoria != '#' and $iCategoria != 0){
	$sql .= " and ProduCategoria = $iCategoria ";
}

if ($iSubCategoria != '#' and $iSubCategoria != 0){
	$sql .= " and ProduSubCategoria = $iSubCategoria ";
}

if ($iProduto != '#' and $iProduto != 0){
	$sql .= " and ProduId = $iProduto ";
}

if ($iFornecedor != '#' and $iFornecedor != 0){
	$sql .= " and MovimFornecedor = $iFornecedor ";
}
		
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        //'format' => [190, 236], 
        'format' => 'A4-L',
        'default_font_size' => 10,
		'default_font' => 'dejavusans',
        'orientation' => 'L'
	]);
	
	
	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:200px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Relatório de Movimentação</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	$html .= '
	<br><br>
	<table style="width:100%; border-collapse: collapse; font-size:10px;">
		<tr>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:6%">Data</th>
	';
	
	// Se for todos os tipos
/*	if ($iTipo == '#'){
		$html .= '<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Tipo</th>';
	}*/
	
	$html .= '
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Fornecedor</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Estoque Destino</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:18%">Produto</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">Quant.</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">Lote</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:6%">Validade</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">NF</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Classificação</th>
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">Valor</th>
		</tr>
	';		
	
	foreach ($row as $item){
		
		$html .= "
				<tr>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".mostraData($item['MovimData'])."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['ForneNome']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['MovimDestinoLocal']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['ProduNome']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['MvXPrQuantidade']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['MvXPrLote']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".mostraData($item['MvXPrValidade'])."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['MovimNotaFiscal']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".$item['ClassNome']."</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".mostraValor($item['MvXPrValorUnitario'])."</td>
				</tr>
			";
	}
	
	$html .= "</table>";	
	
    $rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";
    
    $mpdf->SetHTMLHeader($topo,'O',true);
    $mpdf->WriteHTML($html);
    $mpdf->SetHTMLFooter($rodape);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
