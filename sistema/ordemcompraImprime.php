<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

if (isset($_POST['inputOrdemCompraId'])){
	$iOrdemCompra = $_POST['inputOrdemCompraId'];
} else{
	print('<script>
				window.close();
		   </script> ');
}

$sql = "SELECT OrComTipo, OrComNumero, OrComDtEmissao, OrComLote, OrComNumAta, OrComNumProcesso, 
		OrComConteudoInicio, OrComConteudoFim, ForneCnpj, ForneNome, ForneCelular, ForneEmail, CategNome, FlOpeNumContrato
		FROM OrdemCompra
		JOIN Fornecedor on ForneId = OrComFornecedor
		JOIN FluxoOperacional on FlOpeId = OrComFluxoOperacional
		JOIN Categoria on CategId = OrComCategoria
		WHERE OrComUnidade = ". $_SESSION['UnidadeId'] ." and OrComId = ".$iOrdemCompra;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row['OrComTipo'] == 'O'){
	$sTipo = "Ordem de Compra";
} else{
	$sTipo = "Carta Contrato";
}

try {
	$mpdf = new mPDF([
	             'mode' => 'utf-8',    // mode - default ''
	             'format' => 'A4-P',    // format - A4, for example, default ''
	             'default_font_size' => 9,     // font size - default 0
	             'default_font' => '',    // default font family
	             'margin-left' => 15,    // margin_left
	             'margin-right' => 15,    // margin right
	             'margin-top' => 158,     // margin top    -- aumentei aqui para que não ficasse em cima do header
	             'margin-bottom' => 60,    // margin bottom
	             'margin-header' => 6,     // margin header
	             'margin-bottom' => 0,     // margin footer
	             'orientation' => 'P']);  // L - landscape, P - portrait	
	
	$html = "

	<style>
		th{
		    text-align: center; 
		    border: #bbb solid 1px; 
		    background-color: #f8f8f8; 
		    padding: 8px;
		}

		td{
			padding: 8px;				
			border: #bbb solid 1px;
		}
	</style>

	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:480px; float:left; display: inline;'>
			<img src='global_assets/images/empresas/".$_SESSION['EmpreFoto']."' style='width:110px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
		</div>
		<div style='width:200px; float:right; display: inline; text-align:right;'>
			<div>".date('d/m/Y')."</div>
			<div style='margin-top:8px;'>".$sTipo.": ".formatarNumero($row['OrComNumero'])."</div>
		</div> 
	</div>

	<div style='text-align:center; margin-top: 20px;'><h1>".strtoupper($sTipo)."</h1></div>
	";
	
	$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, OCXPrQuantidade, OCXPrValorUnitario, MarcaNome
			FROM Produto
			JOIN OrdemCompraXProduto on OCXPrProduto = ProduId
			JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			LEFT JOIN Marca on MarcaId = ProduMarca
			WHERE ProduUnidade = ".$_SESSION['UnidadeId']." and OCXPrOrdemCompra = ".$iOrdemCompra;

	$result = $conn->query($sql);
	$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
	$totalProdutos = count($rowProdutos);

	$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, OCXSrQuantidade, OCXSrValorUnitario
			FROM Servico
			JOIN OrdemCompraXServico on OCXSrServico = ServiId
			WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and OCXSrOrdemCompra = ".$iOrdemCompra;

	$result = $conn->query($sql);
	$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
	$totalServicos = count($rowServicos);
	
	$html .= '
	<table style="width:100%; border-collapse: collapse;"> 
		<tr>
			<td colspan="1" style="width:25%; font-size:12px;">Nº Contrato:<br>'. $row['FlOpeNumContrato'].'</td>	
			<td colspan="1" style="width:25%; font-size:12px;">Data Emissão:<br>'. mostraData($row['OrComDtEmissao']).'</td>';

	if ($row['OrComTipo'] == 'O'){		
		$html .= '<td colspan="1" style="width:25%; font-size:12px;">Lote:<br>'. $row['OrComLote'].'</td>';
	} else {
		$html .= '<td colspan="1" style="width:25%; font-size:12px;">Nº Ata Registro:<br>'. $row['OrComNumAta'].'</td>';
	}

	$html .= '<td colspan="1" style="width:25%; font-size:12px;">Nº Processo:<br>'. $row['OrComNumProcesso'].'</td>
		</tr>
	</table>
	<table style="width:100%; border-collapse: collapse;">
		<tr style="background-color:#F1F1F1;">
			<td colspan="3" style="width:100%; font-size:12px;">Fornecedor:<br>'. $row['ForneNome'].'</td>
		</tr>
		<tr>
			<td colspan="1" style="width:30%; font-size:12px;">CNPJ:<br>'. $row['ForneCnpj'].'</td>	
			<td colspan="1" style="width:30%; font-size:12px;">Telefone:<br>'. $row['ForneCelular'].'</td>
			<td colspan="1" style="width:40%; font-size:12px;">E-mail:<br>'. $row['ForneEmail'].'</td>
		</tr>
	</table>
	<table style="width:100%; border-collapse: collapse;">
		<tr>
			<td style="width:100%; font-size:12px;">Categoria:<br>'.$row['CategNome'].'</td>
		</tr>
	</table>';
	
	$html .= '
	<br>
	<div>'.$row['OrComConteudoInicio'].'</div>
	<br>';

	$totalGeralProdutos = 0;
		
	if($totalProdutos > 0){

		$html .= "<div style='text-align:center; margin-top: 20px;'><h2>PRODUTOS</h2></div>";

		$html .= '
		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: center; width:8%">Item</th>
				<th style="text-align: left; width:43%">Produto</th>
				<th style="text-align: center; width:10%">Unidade</th>				
				<th style="text-align: center; width:12%">Quant.</th>
				<th style="text-align: center; width:12%">V. Unit.</th>
				<th style="text-align: center; width:15%">V. Total</th>
			</tr>
		';
		
		$cont = 1;
		
		foreach ($rowProdutos as $itemProduto){
			
			if ($itemProduto['OCXPrValorUnitario'] != '' and $itemProduto['OCXPrValorUnitario'] != null){
				$valorUnitario = $itemProduto['OCXPrValorUnitario'];
				$valorTotal = $itemProduto['OCXPrQuantidade'] * $itemProduto['OCXPrValorUnitario'];
			} else {
				$valorUnitario = 0;
				$valorTotal = 0;
			}

			if($totalProdutos == ($cont)){
				$html .= "
				<tr>
					<td style='text-align: center;'>".$cont."</td>
					<td style='text-align: left;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."<br>Marca: ".$itemProduto['MarcaNome']."</td>
					<td style='text-align: center;'>".$itemProduto['UnMedSigla']."</td>					
					<td style='text-align: center;'>".$itemProduto['OCXPrQuantidade']."</td>
					<td style='text-align: right;'>".mostraValor($valorUnitario)."</td>
					<td style='text-align: right;'>".mostraValor($valorTotal)."</td>
				</tr>
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>".$cont."</td>
					<td style='text-align: left;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."<br>Marca: ".$itemProduto['MarcaNome']."</td>
					<td style='text-align: center;'>".$itemProduto['UnMedSigla']."</td>					
					<td style='text-align: center;'>".$itemProduto['OCXPrQuantidade']."</td>
					<td style='text-align: right;'>".mostraValor($valorUnitario)."</td>
					<td style='text-align: right'>".mostraValor($valorTotal)."</td>
				</tr>
			";
			}
			
			$cont++;
			$totalGeralProdutos += $valorTotal;
		}

		$html .= "<br>";
		
		$html .= "  <tr>
						<td colspan='5' height='50' valign='middle'>
							<strong>Total Produtos</strong>
						</td>
						<td style='text-align: right' colspan='2'>
							".mostraValor($totalGeralProdutos)."
						</td>
					</tr>";
		$html .= "</table>";
	}

	$totalGeralServicos = 0;
	
	if($totalServicos > 0){

		$html .= "<div style='text-align:center; margin-top: 20px;'><h2>SERVIÇOS</h2></div>";

		$html .= '
		<table style="width:100%; border-collapse: collapse; margin-top: 20px;">
			<tr>
				<th style="text-align: center; width:8%">Item</th>
				<th style="text-align: left; width:53%">Serviço</th>
				<th style="text-align: center; width:12%">Quant.</th>
				<th style="text-align: center; width:12%">V. Unit.</th>
				<th style="text-align: center; width:15%">V. Total</th>
			</tr>
		';
		
		$cont = 1;
		
		foreach ($rowServicos as $itemServico){
			
			if ($itemServico['OCXSrValorUnitario'] != '' and $itemServico['OCXSrValorUnitario'] != null){
				$valorUnitario = $itemServico['OCXSrValorUnitario'];
				$valorTotal = $itemServico['OCXSrQuantidade'] * $itemServico['OCXSrValorUnitario'];
			} else {
				$valorUnitario = 0;
				$valorTotal = 0;
			}

			if($totalServicos == ($cont)){
				$html .= "
				<tr>
					<td style='text-align: center;'>".$cont."</td>
					<td style='text-align: left;'>".$itemServico['ServiNome'].": ".$itemServico['ServiDetalhamento']."</td>	
					<td style='text-align: center;'>".$itemServico['OCXSrQuantidade']."</td>
					<td style='text-align: right;'>".mostraValor($valorUnitario)."</td>
					<td style='text-align: right;'>".mostraValor($valorTotal)."</td>
				</tr>
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>".$cont."</td>
					<td style='text-align: left;'>".$itemServico['ServiNome'].": ".$itemServico['ServiDetalhamento']."</td>
					<td style='text-align: center;'>".$itemServico['OCXSrQuantidade']."</td>
					<td style='text-align: right;'>".mostraValor($valorUnitario)."</td>
					<td style='text-align: right'>".mostraValor($valorTotal)."</td>
				</tr>
			";
			}
			
			$cont++;
			$totalGeralServicos += $valorTotal;
		}

		$html .= "<br>";
		
		$html .= "  <tr>
						<td colspan='4' height='50' valign='middle'>
							<strong>Total Serviços</strong>
						</td>
						<td style='text-align: right' colspan='2'>
							".mostraValor($totalGeralServicos)."
						</td>
					</tr>";
		$html .= "</table>";
	}

	$totalGeral = $totalGeralProdutos + $totalGeralServicos;

	$html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
				<tr>
					<td colspan='5' height='50' valign='middle' style='width:85%'>
						<strong>TOTAL GERAL</strong>
					</td>
					<td style='text-align: right; width:15%'>
						".mostraValor($totalGeral)."
					</td>
				</tr>
				</table>
	";
	
	$html .= '
	<br><br>
	<div>' . $row['OrComConteudoFim'] . '</div>
	<br>';
	
    $rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";
    
    //$mpdf->SetHTMLHeader($topo,'O',true); //o SetHTMLHeader deve vir antes do WriteHTML para que o cabeçalho apareça em todas as páginas
    $mpdf->SetHTMLFooter($rodape); 	//o SetHTMLFooter deve vir antes do WriteHTML para que o rodapé apareça em todas as páginas
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    $html = $e->getMessage();
	
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();	
}
