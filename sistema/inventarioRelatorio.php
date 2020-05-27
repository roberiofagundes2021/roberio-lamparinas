<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iInventario = $_POST['inputInventarioId'];
$sNumero = $_POST['inputInventarioNumero'];

$sql = "SELECT InvenNumero, InvenCategoria, InvenObservacao, CategNome, InXLELocal, LcEstNome
		 FROM Inventario
		 JOIN InventarioXLocalEstoque on InXLEInventario = InvenId
		 JOIN LocalEstoque on LcEstId = InXLELocal
		 JOIN Categoria on CategId = InvenCategoria
		 Where InvenId = ".$iInventario."
		";
$result = $conn->query($sql);
$rowLocalEstoque = $result->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT InvenNumero, InvenCategoria, InvenObservacao, CategNome, InXSeSetor, SetorNome
		 FROM Inventario
		 JOIN InventarioXSetor on InXSeInventario = InvenId
		 JOIN Setor on SetorId = InXSeSetor
		 JOIN Categoria on CategId = InvenCategoria
		 Where InvenId = ".$iInventario."
		";
$result = $conn->query($sql);
$rowSetor= $result->fetchAll(PDO::FETCH_ASSOC);

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
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
		</div>
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Inventário: ".formatarNumero($sNumero)."</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	foreach ($rowLocalEstoque as $item){	
		
		$html .= '
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 50px;text-transform: uppercase;">Local: '.$item['LcEstNome'].'</div>
		<div style="font-weight: bold; position:relative; margin-top: 20px;">'.$item['CategNome'].'</div>
		<br>
		<table style="width:100%;">
			<tr>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Patrimônio</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:40%">Produto</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Unidade</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">Quantidade</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Unitário</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Total</th>
			</tr>
		';	
		
		$iCategoria = $item['InvenCategoria'];
		$iLocal = $item['InXLELocal'];
		
		$sql = ("SELECT ProduCodigo, ProduNome, UnMedSigla, CategNome, ProduCustoFinal, PatriNumero, 
						dbo.fnSaldoEstoque(".$_SESSION['UnidadeId'].", ProduId, MovimDestinoLocal) as Saldo, 
						dbo.fnCalculaValorTotalInventario(dbo.fnSaldoEstoque(".$_SESSION['UnidadeId'].", ProduId, MovimDestinoLocal), ProduCustoFinal) as ValorTotal
				 FROM Produto
				 JOIN Categoria on CategId = ProduCategoria
				 JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				 JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
				 JOIN Patrimonio on PatriId = MvXPrPatrimonio
				 JOIN Movimentacao on MovimId = MvXPrMovimentacao
				 JOIN LocalEstoque on LcEstId = MovimDestinoLocal
				 JOIN Situacao on SituaId = MovimSituacao
				 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduStatus = 1 and
					   ProduCategoria = ".$iCategoria." and
					   MovimDestinoLocal = (".$iLocal.") and SituaChave = 'FINALIZADO'
				 ");
		$result = $conn->query("$sql");
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);		
		
		$totalGeral = 0;
		
		foreach ($rowProdutos as $itemProduto){
			
			$html .= "
				<tr>
					<td style='padding-top: 8px;'>".formatarNumero($itemProduto['PatriNumero'])."</td>
					<td style='padding-top: 8px;'>".$itemProduto['ProduNome']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['UnMedSigla']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['Saldo']."</td>
					<td style='padding-top: 8px;'>".mostraValor($itemProduto['ProduCustoFinal'])."</td>
					<td style='padding-top: 8px;'>".formataMoeda($itemProduto['ValorTotal'])."</td>
				</tr>
			";
			
			$totalGeral += $itemProduto['ValorTotal'];
		}
		
		$html .= "
				<tr>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".formataMoeda($totalGeral)."</td>
				</tr>
			";		
		
		$html .= "</table>";
		
	}
/////////////////////////////////////////////////////
	foreach ($rowSetor as $item){	
		
		$html .= '
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 50px;text-transform: uppercase;">Setor: '.$item['SetorNome'].'</div>
		<div style="font-weight: bold; position:relative; margin-top: 20px;">'.$item['CategNome'].'</div>
		<br>
		<table style="width:100%;">
			<tr>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Patrimônio</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:40%">Produto</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Unidade</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">Quantidade</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Unitário</th>
				<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Total</th>
			</tr>
		';	
		
		$iCategoria = $item['InvenCategoria'];
		$iSetor = $item['InXSeSetor'];
		
		$sql = ("SELECT ProduCodigo, ProduNome, UnMedSigla, CategNome, ProduCustoFinal, PatriNumero, 
						dbo.fnSaldoEstoque(".$_SESSION['UnidadeId'].", ProduId, MovimDestinoLocal) as Saldo, 
						dbo.fnCalculaValorTotalInventario(dbo.fnSaldoEstoque(".$_SESSION['UnidadeId'].", ProduId, MovimDestinoLocal), ProduCustoFinal) as ValorTotal
				 FROM Produto
				 JOIN Categoria on CategId = ProduCategoria
				 JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				 JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
				 JOIN Patrimonio on PatriId = MvXPrPatrimonio
				 JOIN Movimentacao on MovimId = MvXPrMovimentacao
				 JOIN Setor on SetorId = MovimDestinoSetor
				 JOIN Situacao on SituaId = MovimSituacao
				 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduStatus = 1 and
					   ProduCategoria = ".$iCategoria." and
					   MovimDestinoLocal = (".$iSetor.") and SituaChave = 'FINALIZADO'
				 ");
		$result = $conn->query("$sql");
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);		
		
		$totalGeral = 0;
		
		foreach ($rowProdutos as $itemProduto){
			
			$html .= "
				<tr>
					<td style='padding-top: 8px;'>".formatarNumero($itemProduto['PatriNumero'])."</td>
					<td style='padding-top: 8px;'>".$itemProduto['ProduNome']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['UnMedSigla']."</td>
					<td style='padding-top: 8px;'>".$itemProduto['Saldo']."</td>
					<td style='padding-top: 8px;'>".mostraValor($itemProduto['ProduCustoFinal'])."</td>
					<td style='padding-top: 8px;'>".formataMoeda($itemProduto['ValorTotal'])."</td>
				</tr>
			";
			
			$totalGeral += $itemProduto['ValorTotal'];
		}
		
		$html .= "
				<tr>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>".formataMoeda($totalGeral)."</td>
				</tr>
			";		
		
		$html .= "</table>";
		
	}
	
	$html .= '
		<br><br>
		<div style="width: 100%; margin-top: 200px; text-align: center; position: absolute; left: 35%">
			<div style="position: relative; width: 250px; border-top: 1px solid #333; padding-top:10px; text-align: center;">Responsável</div>
		</div>';
	
	if ($item['InvenObservacao'] != ''){
		$html .= '
			<br>
			<div style="100%">Observação: '.$item['InvenObservacao'].'</div>
		';	
	}		
	
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
