<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

if (isset($_POST['inputFluxoId'])) {
	$iFluxoOperacional = $_POST['inputFluxoId'];
} else {
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
}

$sql = "SELECT FlOpeNumContrato, FlOpeNumProcesso, FlOpeValor, FlOpeDataInicio, FlOpeDataFim,FlOpeConteudoInicio, FlOpeConteudoFim, CategNome, SbCatNome,
		ForneNome, ForneCelular, ForneEmail
		FROM FluxoOperacional
		JOIN Fornecedor on ForneId = FlOpeFornecedor
		JOIN Categoria on CategId = FlOpeCategoria
		JOIN SubCategoria on SbCatId = FlOpeSubCategoria
		WHERE FlOpeUnidade = " . $_SESSION['UnidadeId'] . " and FlOpeId = " . $iFluxoOperacional;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario
		FROM Produto
		JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
		JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
$totalProdutos = count($rowProdutos);

$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrQuantidade, FOXSrValorUnitario
		FROM Servico
		JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
		WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
$totalServicos = count($rowServicos);

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
		'orientation' => 'P'
	]);  // L - landscape, P - portrait	

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
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: " . $_SESSION['UnidadeNome'] . "</div>
		</div>
		<div style='width:250px; float:right; display: inline; text-align:right;'>
			<div>" . date('d/m/Y') . "</div>
			<div style='margin-top:8px;'>Contrato: " . $row['FlOpeNumContrato'] . "</div>
		</div> 
	</div>

	<div style='text-align:center; margin-top: 20px;'><h1>Licitação - Contrato</h1></div>
    ";
    
    $html .= '
	<div>' . $row['FlOpeConteudoInicio'] . '</div>
	<br>';

	if ($totalProdutos > 0) {		

        $html .= "<div style='text-align:center; margin-top: 20px;'><h2>PRODUTOS</h2></div>";
        
        $html .= '
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#ddd; padding: 8px; border: 1px solid #ccc;">
			Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> 
		</div>
		<br>
		';

		$html .= '
				<div style="font-weight: bold; position:relative; margin-top: 15px; background-color:#eee; padding: 8px; border: 1px solid #ccc;">
					SubCategoria: <span style="font-weight:normal;">' . $row['SbCatNome'] . '</span>
				</div>
				<br> ';	

		$html .= '
		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: center; width:8%">Item</th>
				<th style="text-align: left; width:65%">Produto</th>
				<th style="text-align: center; width:12%">Unidade</th>				
				<th style="text-align: center; width:15%">Quant.</th>
			</tr>
		';

		$cont = 1;

		foreach ($rowProdutos as $rowProduto) {
			
			if ($totalProdutos == ($cont)) {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['ProduDetalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['FOXPrQuantidade'] . "</td>		
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['ProduDetalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['FOXPrQuantidade'] . "</td>
				</tr>
			";
			}

			$cont++;
			$totalGeralProdutos += $rowProduto['FOXPrQuantidade'];
		}

		$html .= "<br>";

		$html .= "  <tr>
	                	<td colspan='3' height='50' valign='middle'>
		                    <strong>Total Produtos</strong>
	                    </td>
					    <td style='text-align: center' colspan='1'>
					        " . $totalGeralProdutos . "
					    </td>
				    </tr>";
		$html .= "</table>";
	}

	$totalGeralServicos = 0;

	if ($totalServicos > 0) {

        $html .= "<div style='text-align:center; margin-top: 20px;'><h2>SERVIÇOS</h2></div>";
        
        $html .= '
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#ddd;  padding: 8px;  border: 1px solid #ccc;">
			Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> 
		</div>
		<br>
		';

		$html .= '
				<div style="font-weight: bold; position:relative; margin-top: 15px; background-color:#eee; padding: 8px; border: 1px solid #ccc;">
					SubCategoria: <span style="font-weight:normal;">' . $row['SbCatNome'] . '</span>
				</div>
				<br> ';	

		$html .= '
		<table style="width:100%; border-collapse: collapse; margin-top: 20px;">
			<tr>
				<th style="text-align: center; width:8%">Item</th>
				<th style="text-align: left; width:77%">Serviço</th>
				<th style="text-align: center; width:15%">Quant.</th>
			</tr>
		';

		$cont = 1;

		foreach ($rowServicos as $rowServico) {

			if ($totalServicos == ($cont)) {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['ServiDetalhamento'] . "</td>	
					<td style='text-align: center;'>" . $rowServico['FOXSrQuantidade'] . "</td>			
				</tr>
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['ServiDetalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowServico['FOXSrQuantidade'] . "</td>
					
				</tr>
			";
			}

			$cont++;
			$totalGeralServicos += $rowServico['FOXSrQuantidade'];
		}

		$html .= "<br>";

		$html .= "  <tr>
	                	<td colspan='2' height='50' valign='middle'>
		                    <strong>Total Serviços</strong>
	                    </td>
					    <td style='text-align: center' colspan='1'>
					        " . $totalGeralServicos . "
					    </td>
				    </tr>";
		$html .= "</table>";
	}

	$totalGeral = $totalGeralProdutos + $totalGeralServicos;

	$html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
	 			<tr>
                	<td colspan='5' height='50' valign='middle' style='width:85%'>
	                    <strong>TOTAL DE ITENS GERAIS</strong>
                    </td>
				    <td style='text-align: center; width:15%'>
				        " . $totalGeral . "
				    </td>
			    </tr>
			  </table>
	";

	
    
    $html .= '
	<br><br>
	<div>' . $row['FlOpeConteudoFim'] . '</div>
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
