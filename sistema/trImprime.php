<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iTR = $_POST['inputTRId'];
$sNumero = $_POST['inputTRNumero'];

try {

	$sql = "SELECT TrRefNumero, TrRefConteudo, CategNome
			FROM TermoReferencia
			JOIN Categoria on CategId = TrRefCategoria
			WHERE TrRefEmpresa = " . $_SESSION['EmpreId'] . " and TrRefId = " . $iTR;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT *
			FROM TRXSubcategoria
			JOIN SubCategoria on SbCatId = TRXSCSubcategoria
			WHERE TRXSCEmpresa = " . $_SESSION['EmpreId'] . " and TRXSCTermoReferencia = " . $iTR;
	$result = $conn->query($sql);
	$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

	// Selects para identificar a a tabela de origem dos produtos da TR.
	$sql = "SELECT COUNT(TRXPrProduto) as CONT
			FROM TermoReferenciaXProduto
			JOIN ProdutoOrcamento on PrOrcId = TRXPrProduto
			WHERE TRXPrEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'ProdutoOrcamento'";
	$result = $conn->query($sql);
	$rowProdutoUtilizado1 = $result->fetchAll(PDO::FETCH_ASSOC);

	$sql = "SELECT COUNT(TRXPrProduto) as CONT
			FROM TermoReferenciaXProduto
			JOIN Produto on ProduId = TRXPrProduto
			WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'Produto'";
	$result = $conn->query($sql);
	$rowProdutoUtilizado2 = $result->fetchAll(PDO::FETCH_ASSOC);

	// Selects para identificar a a tabela de origem dos serviços da TR.
	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN ServicoOrcamento on SrOrcId = TRXSrServico
			WHERE TRXSrEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'ServicoOrcamento'";
	$result = $conn->query($sql);
	$rowServicoUtilizado1 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado1 = count($rowServicoUtilizado1);


	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN Servico on ServiId = TRXSrServico
			WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'Servico'";
	$result = $conn->query($sql);
	$rowServicoUtilizado2 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado2 = count($rowServicoUtilizado2);

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
			<div style='width:300px; float:left; display: inline;'>
				<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
				<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
				<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
			</div>
			<div style='width:220px; float:right; display: inline; text-align:right;'>
				<div>".date('d/m/Y')."</div>
				<div style='margin-top:8px;'>Termo de Referência: " . formatarNumero($row['TrRefNumero']) . "</div>
			</div> 
			</div>

			<div style='text-align:center; margin-top: 20px;'><h1>TERMO DE REFERÊNCIA</h1></div>
	";

	$html .= '
	<div>' . $row['TrRefConteudo'] . '</div>
	<br>';

	if ($rowProdutoUtilizado1['CONT'] > 0 || $rowProdutoUtilizado2['CONT'] > 0){

		$html .= "<div style='text-align:center; margin-top: 20px;'><h2>PRODUTOS</h2></div>";

		$html .= '
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> 
		</div>
		<br>
		';

		//Se foi utilizado ProdutoOrcamento
		if($countProdutoUtilizado1){
			$sql = "SELECT PrOrcId as Id, PrOrcNome as Nome, PrOrcCategoria as Categoria, PrOrcSubCategoria as SubCategoria
					PrOrcDetalhamento as Detalhamento, UnMedSigla, TRXPrQuantidade, TRXPrValorUnitario
					FROM ProdutoOrcamento
					JOIN TermoReferenciaXProduto on TRXPrProduto = PrOrcId
					JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
					WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR;
		} else {
			$sql = "SELECT ProduId as Id, ProduNome as Nome, ProduCategoria as Categoria, ProduSubCategoria as SubCategoria, 
					ProduDetalhamento as Detalhamento, UnMedSigla, TRXPrQuantidade, TRXPrValorUnitario
					FROM Produto
					JOIN TermoReferenciaXProduto on TRXPrProduto = ProduId
					JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
					WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR;
		}
		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

		$cont = 1;

		foreach ($rowSubCategoria as $sbcat) {
			
			$html .= '
				<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
					SubCategoria: <span style="font-weight:normal;">' . $sbcat['SbCatNome'] . '</span>
				</div>
				<br> ';
				
			$totalGeralProdutos = 0;
			
			if (isset($rowProdutos)){
				
				$html .= '	
				<table style="width:100%; border-collapse: collapse;">
					<tr>
						<th style="text-align: center; width:8%">Item</th>
						<th style="text-align: left; width:46%">Produto</th>
						<th style="text-align: center; width:10%">Unidade</th>        	
						<th style="text-align: center; width:12%">Quant.</th>
						<th style="text-align: center; width:12%">V. Unit.</th>
						<th style="text-align: center; width:12%">V. Total</th>
					</tr>
				';			

				foreach ($rowProdutos as $itemProduto) {

					if ($sbcat['TRXSCSubcategoria'] == $itemProduto['SubCategoria']) {

						if ($itemProduto['TRXPrValorUnitario'] != '' and $itemProduto['TRXPrValorUnitario'] != null) {
							$valorUnitario = $itemProduto['TRXPrValorUnitario'];
							$valorTotal = $itemProduto['TRXPrQuantidade'] * $itemProduto['TRXPrValorUnitario'];
						} else {
							$valorUnitario = "";
							$valorTotal = "";
						}

						$html .= "
							<tr>
								<td style='text-align: center;'>" . $cont . "</td>
								<td style='text-align: left;'>" . $itemProduto['Nome'] . ": " . $itemProduto['Detalhamento'] . "</td>
								<td style='text-align: center;'>" . $itemProduto['UnMedSigla'] . "</td>							
								<td style='text-align: center;'>" . $itemProduto['TRXPrQuantidade'] . "</td>
								<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
								<td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
							</tr>
						";

						$cont++;
						$totalGeralProdutos += $valorTotal;
					}
				}
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
	}

	$rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";

	//$mpdf->SetHTMLHeader($topo, '0');
	$mpdf->SetHTMLFooter($rodape);
	$mpdf->WriteHTML($html);	

	// Other code
	$mpdf->Output();
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

	// Process the exception, log, print etc.
	echo $e->getMessage();
}
