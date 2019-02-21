<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iInventario = $_POST['inputInventarioId'];
$sNumero = $_POST['inputInventarioNumero'];

$sql = ("SELECT InvenNumero, InvenCategoria, InXLELocal, LcEstNome
		 FROM Inventario
		 JOIN InventarioXLocalEstoque on InXLEInventario = InvenId
		 JOIN LocalEstoque on LcEstId = InXLELocal
		 Where InvenId = ".$iInventario."
		");
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
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Inventário: ".formatarNumero($sNumero)."</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	foreach ($row as $item){	
		
		$html .= '
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 50px;">Local: '.$item['LcEstNome'].'</div>
		<br>
		<table style="width:100%;">
			<tr>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Código</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:30%">Produto</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Unidade</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:30%">Categoria</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">1ª Contagem</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">2ª Contagem</th>
			</tr>
		';	
		
		$iCategoria = $item['InvenCategoria'];
		$iLocal = $item['InXLELocal'];
		
		$sql = ("SELECT Distinct ProduCodigo, ProduNome, UnMedSigla, CategNome, ProduCustoFinal, dbo.fnSaldoEstoque(".$_SESSION['EmpreId'].", ProduId, MovimDestinoLocal) as Saldo, LcEstNome
				 FROM Produto
				 JOIN Categoria on CategId = ProduCategoria
				 JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				 JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
				 JOIN Movimentacao on MovimId = MvXPrMovimentacao
				 JOIN LocalEstoque on LcEstId = MovimDestinoLocal
				 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduStatus = 1 and
					   ProduCategoria = ".$iCategoria." and
					   MovimDestinoLocal = (".$iLocal.") and MovimSituacao = 2
				 ");
		$result = $conn->query("$sql");
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);		
		
		foreach ($rowProdutos as $itemProduto){
			
			$html .= "
				<tr>
					<td style='padding-top: 8px;'>".formatarNumero($itemProduto['ProduCodigo'])."</td>
					<td style='padding-top: 8px;'>".$itemProduto['ProduNome']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['UnMedSigla']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['CategNome']."</td>
					<td style='padding-top: 8px;'>__________________</td>
					<td style='padding-top: 8px;'>__________________</td>
				</tr>
			";
		}
		
		$html .= "</table>";
	}
	
	$html .= '			
		<br><br>
		<div style="width: 100%; margin-top: 200px;">
			<div style="position: relative; width: 250px; border-top: 1px solid #333; padding-top:10px; float: left; text-align: center;">Responsável</div>
			<div style="position: relative; width: 250px; border-top: 1px solid #333; padding-top:10px; float: left; text-align: center; margin-left: 100px;">Membro 1</div>
			<div style="position: relative; width: 250px; border-top: 1px solid #333; padding-top:10px; float: left; text-align: center; margin-left: 100px;">Membro 2</div>
		</div>
	';	
	
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