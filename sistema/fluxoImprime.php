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

$sql = "SELECT FlOpeNumContrato, FlOpeNumProcesso, FlOpeValor, FlOpeDataInicio, FlOpeDataFim, CategNome, 
		dbo.fnSubCategoriasFluxo(FlOpeUnidade, FlOpeId) as SubCategorias, ForneNome, ForneCelular, ForneEmail,
		FlOpeTermoReferencia, TrRefTabelaProduto, TrRefTabelaServico
		FROM FluxoOperacional
		JOIN Fornecedor on ForneId = FlOpeFornecedor
		JOIN Categoria on CategId = FlOpeCategoria
		LEFT JOIN TermoReferencia on TrRefId = FlOpeTermoReferencia
		WHERE FlOpeUnidade = " . $_SESSION['UnidadeId'] . " and FlOpeId = " . $iFluxoOperacional;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT AditiId, AditiNumero, AditiDtCelebracao, AditiDtInicio, AditiDtFim, AditiValor, FlOpeId, FlOpeNumContrato, FlOpeNumProcesso, FlOpeValor, FlOpeDataInicio, FlOpeDataFim, CategNome, SbCatNome,
		ForneNome, ForneCelular, ForneEmail
		FROM Aditivo
		JOIN FluxoOperacional on FlOpeId = AditiFluxoOperacional
		JOIN Fornecedor on ForneId = FlOpeFornecedor
		JOIN Categoria on CategId = FlOpeCategoria
		JOIN SubCategoria on SbCatId = FlOpeSubCategoria
		WHERE AditiUnidade = " . $_SESSION['UnidadeId'] . " and AditiFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowAditivos = $result->fetchAll(PDO::FETCH_ASSOC);

if ($row['FlOpeTermoReferencia'] && $row['TrRefTabelaProduto'] != null && $row['TrRefTabelaProduto'] == 'ProdutoOrcamento'){
	$sql = "SELECT ProduId, ProduNome, PrOrcDetalhamento as Detalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario
			FROM Produto
			JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
			JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			JOIN ProdutoOrcamento on PrOrcProduto = ProduId
			JOIN SubCategoria on SbCatId = ProduSubCategoria
			WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $iFluxoOperacional."
			ORDER BY SbCatNome, ProduNome ASC";
} else{
	$sql = "SELECT ProduId, ProduNome, ProduDetalhamento as Detalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario
			FROM Produto
			JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
			JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			JOIN SubCategoria on SbCatId = ProduSubCategoria
			WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $iFluxoOperacional."
			ORDER BY SbCatNome, ProduNome ASC";
}
$result = $conn->query($sql);
$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
$totalProdutos = count($rowProdutos);

if ($row['FlOpeTermoReferencia'] && $row['TrRefTabelaServico'] != null && $row['TrRefTabelaServico'] == 'ServicoOrcamento'){
	$sql = "SELECT ServiId, ServiNome, SrOrcDetalhamento as Detalhamento, FOXSrQuantidade, FOXSrValorUnitario
			FROM Servico
			JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
			JOIN ServicoOrcamento on SrOrcServico = ServiId
			JOIN SubCategoria on SbCatId = ServiSubCategoria
			WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional."
			ORDER BY SbCatNome, ServiNome ASC";
} else {
	$sql = "SELECT ServiId, ServiNome, ServiDetalhamento as Detalhamento, FOXSrQuantidade, FOXSrValorUnitario
			FROM Servico
			JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
			JOIN SubCategoria on SbCatId = ServiSubCategoria
			WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional."
			ORDER BY SbCatNome, ServiNome ASC";
}
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
		<div style='width:400px; float:left; display: inline;'>
			<img src='global_assets/images/empresas/".$_SESSION['EmpreFoto']."' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: " . $_SESSION['UnidadeNome'] . "</div>
		</div>
		<div style='width:250px; float:right; display: inline; text-align:right;'>
			<div>" . date('d/m/Y') . "</div>
			<div style='margin-top:8px;'>Fluxo Operacional: " . $row['FlOpeNumContrato'] . "</div>
		</div> 
	</div>

	<div style='text-align:center; margin-top: 20px;'><h1>FLUXO OPERACIONAL</h1></div>
	";

	$html .= '
	<h2 style="margin-top: 20px; text-align: center;">RESUMO</h2>
'	;	

	//Dados do Fluxo
	$html .= '
    <table style="width:100%; border-collapse: collapse;">
        <tr style="background-color:#F1F1F1;">
            <td style="width:20%; font-size:14px;">Início:<br>' . mostraData($row['FlOpeDataInicio']) . '</td>
            <td style="width:20%; font-size:14px;">Fim:<br>' . mostraData($row['FlOpeDataFim']) . '</td>
            <td style="width:20%; font-size:14px;">Nº Ata Registro:<br>' . $row['FlOpeNumContrato'] . '</td>
			<td style="width:20%; font-size:14px;">Nº Processo:<br>' . $row['FlOpeNumProcesso'] . '</td>
			<td style="width:20%; font-size:14px; border-left: none; text-align:right;">Valor:<br>' . mostraValor($row['FlOpeValor']) . '</td>
        </tr>
	</table>
	<table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="width:40%; font-size:14px;">Categoria:<br>' . $row['CategNome'] . '</td>
            <td style="width:60%; font-size:14px;">Sub Categoria:<br>' . $row['SubCategorias'] . '</td>
        </tr>
	</table>
	<table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="width:40%; font-size:14px;">Fornecedor:<br>' . $row['ForneNome'] . '</td>
            <td style="width:40%; font-size:14px;">E-mail:<br>' . $row['ForneEmail'] . '</td>
            <td style="width:20%; font-size:14px;">Telefone:<br>' . $row['ForneCelular'] . '</td>
        </tr>
    </table>
	<br>';

	$totalGeralFluxo = $row['FlOpeValor'];
	$totalGeralProdutos = 0;

	if ($rowProdutos){
		foreach ($rowProdutos as $rowProduto) {

			if ($rowProduto['FOXPrValorUnitario'] != '' and $rowProduto['FOXPrValorUnitario'] != null) {
				$valorUnitario = $rowProduto['FOXPrValorUnitario'];
				$valorTotal = $rowProduto['FOXPrQuantidade'] * $rowProduto['FOXPrValorUnitario'];
			} else {
				$valorUnitario = 0;
				$valorTotal = 0;
			}
	
			$totalGeralProdutos += $valorTotal;
		}	
	}

	$totalGeralServicos = 0;

	if ($rowServicos){
		foreach ($rowServicos as $rowServico) {

			if ($rowServico['FOXSrValorUnitario'] != '' and $rowServico['FOXSrValorUnitario'] != null) {
				$valorUnitario = $rowServico['FOXSrValorUnitario'];
				$valorTotal = $rowServico['FOXSrQuantidade'] * $rowServico['FOXSrValorUnitario'];
			} else {
				$valorUnitario = 0;
				$valorTotal = 0;
			}
	
			$totalGeralServicos += $valorTotal;
		}	
	}

	$totalGeralAditivos = 0;

	//Dados dos Aditivos
	if ($rowAditivos){

		foreach ($rowAditivos as $aditivo){
			$html .= '
			<table style="width:100%; border-collapse: collapse;">
				<tr>
					<td style="width:17%; font-size:10px;">Nº Aditivo: ' . $aditivo['AditiNumero'] . '</td>
					<td style="width:23%; font-size:10px;">Celebração: ' . mostraData($aditivo['AditiDtCelebracao']) . '</td>
					<td style="width:18%; font-size:10px;">Início: ' . mostraData($aditivo['AditiDtInicio']) . '</td>
					<td style="width:22%; font-size:10px;">Fim: ' . mostraData($aditivo['AditiDtFim']) . '</td>
					<td style="width:7%; font-size:10px; border-right: none;">Valor:</td>
					<td style="width:13%; font-size:10px; border-left: none; text-align:right;">'. mostraValor($aditivo['AditiValor']) . '</td>	
				</tr>
			</table>
			<br>';

			$totalGeralAditivos += $aditivo['AditiValor'];
		}
	}

	$totalGeral = $totalGeralFluxo + $totalGeralAditivos;

	$html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
	 			<tr>
                	<td colspan='5' height='50' valign='middle' style='width:80%'>
	                    <strong>TOTAL GERAL (Fluxo + Aditivos)</strong>
                    </td>
				    <td style='text-align: right; width:20%'>
				        " . mostraValor($totalGeral) . "
				    </td>
			    </tr>
			  </table>
	";

	$totalGeralProdutos = 0;

	$html .= '
	<h2 style="margin-top: 50px; text-align: center;">DETALHAMENTO DO FLUXO</h2>
	';

	if ($totalProdutos > 0) {		

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

		foreach ($rowProdutos as $rowProduto) {

			if ($rowProduto['FOXPrValorUnitario'] != '' and $rowProduto['FOXPrValorUnitario'] != null) {
				$valorUnitario = $rowProduto['FOXPrValorUnitario'];
				$valorTotal = $rowProduto['FOXPrQuantidade'] * $rowProduto['FOXPrValorUnitario'];
			} else {
				$valorUnitario = 0;
				$valorTotal = 0;
			}

			if ($totalProdutos == ($cont)) {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['FOXPrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
				</tr>
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['FOXPrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right'>" . mostraValor($valorTotal) . "</td>
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
					        " . mostraValor($totalGeralProdutos) . "
					    </td>
				    </tr>";
		$html .= "</table>";
	}

	$totalGeralServicos = 0;

	if ($totalServicos > 0) {

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

		foreach ($rowServicos as $rowServico) {

			if ($rowServico['FOXSrValorUnitario'] != '' and $rowServico['FOXSrValorUnitario'] != null) {
				$valorUnitario = $rowServico['FOXSrValorUnitario'];
				$valorTotal = $rowServico['FOXSrQuantidade'] * $rowServico['FOXSrValorUnitario'];
			} else {
				$valorUnitario = "";
				$valorTotal = "";
			}

			if ($totalServicos == ($cont)) {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['Detalhamento'] . "</td>	
					<td style='text-align: center;'>" . $rowServico['FOXSrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
				</tr>
			";
			} else {
				$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowServico['FOXSrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right'>" . mostraValor($valorTotal) . "</td>
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
					        " . mostraValor($totalGeralServicos) . "
					    </td>
				    </tr>";
		$html .= "</table>";
	}

	$totalGeral = $totalGeralProdutos + $totalGeralServicos;

	$html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
	 			<tr>
                	<td colspan='5' height='50' valign='middle' style='width:85%'>
	                    <strong>TOTAL DO FLUXO</strong>
                    </td>
				    <td style='text-align: right; width:15%'>
				        " . mostraValor($totalGeral) . "
				    </td>
			    </tr>
			  </table>
	";

	// Exibindo os produtos e serviços dos Aditivos do Fluxo Operacional
	foreach ($rowAditivos as $aditivo) {
		//////////////////////////////////
		$html .= '
	        <h2 style="margin-top: 50px; text-align: center;">DETALHAMENTO DO ADITIVO</h2>
	    ';

		$html .= '
                <table style="width:100%; border-collapse: collapse;">
                    <tr style="background-color:#F1F1F1;">
                        <td style="width:20%; font-size:10px;">Nº Aditivo: ' . $aditivo['AditiNumero'] . '</td>
                        <td style="width:30%; font-size:10px;">Data de Celebração: ' . mostraData($aditivo['AditiDtCelebracao']) . '</td>
                        <td style="width:18%; font-size:10px;">Início: ' . mostraData($aditivo['AditiDtInicio']) . '</td>
						<td style="width:17%; font-size:10px;">Fim: ' . mostraData($aditivo['AditiDtFim']) . '</td>
						<td style="width:7%; font-size:10px;border-right:none;">Valor:</td>
						<td style="width:8%; font-size:10px;border-left: none; text-align:right;">' . mostraValor($aditivo['AditiValor']) . '</td>						
                    </tr>
                </table>
	            <br>';

		if ($row['FlOpeTermoReferencia'] && $row['TrRefTabelaProduto'] != null && $row['TrRefTabelaProduto'] == 'ProdutoOrcamento'){
			$sql = "SELECT ProduId, ProduNome, PrOrcDetalhamento as Detalhamento, UnMedSigla, AdXPrQuantidade, AdXPrValorUnitario
					FROM Produto
					JOIN AditivoXProduto on AdXPrProduto = ProduId
					JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
					JOIN SubCategoria on SbCatId = ProduSubCategoria
					JOIN ProdutoOrcamento on PrOrcProduto = ProduId
					WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and AdXPrAditivo = " . $aditivo['AditiId']."
					ORDER BY SbCatNome ASC";
		} else {
			$sql = "SELECT ProduId, ProduNome, ProduDetalhamento as Detalhamento, UnMedSigla, AdXPrQuantidade, AdXPrValorUnitario
					FROM Produto
					JOIN AditivoXProduto on AdXPrProduto = ProduId
					JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
					JOIN SubCategoria on SbCatId = ProduSubCategoria
					WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and AdXPrAditivo = " . $aditivo['AditiId']."
					ORDER BY SbCatNome ASC";
		}
		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
		$totalProdutos = count($rowProdutos);

		if ($row['FlOpeTermoReferencia'] && $row['TrRefTabelaServico'] != null && $row['TrRefTabelaServico'] == 'ServicoOrcamento'){		
			$sql = "SELECT ServiId, ServiNome, SrOrcDetalhamento as Detalhamento, AdXSrQuantidade, AdXSrValorUnitario
					FROM Servico
					JOIN AditivoXServico on AdXSrServico = ServiId
					JOIN SubCategoria on SbCatId = ServiSubCategoria
					JOIN ServicoOrcamento on SrOrcServico = ServiId
					WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and AdXSrAditivo = " . $aditivo['AditiId']."
					ORDER BY SbCatNome ASC";
		} else {
			$sql = "SELECT ServiId, ServiNome, ServiDetalhamento as Detalhamento, AdXSrQuantidade, AdXSrValorUnitario
					FROM Servico
					JOIN AditivoXServico on AdXSrServico = ServiId
					JOIN SubCategoria on SbCatId = ServiSubCategoria
					WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and AdXSrAditivo = " . $aditivo['AditiId']."
					ORDER BY SbCatNome ASC";
		}
		$result = $conn->query($sql);
		$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
		$totalServicos = count($rowServicos);

		$totalGeralProdutos = 0;

		if ($totalProdutos > 0) {

			$html .= "<div style='margin-top: -10px; text-align:center;'><h3>Produtos do Aditivo N° ".$aditivo['AditiNumero']."</h3></div>";

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

			foreach ($rowProdutos as $rowProduto) {

				if ($rowProduto['AdXPrValorUnitario'] != '' and $rowProduto['AdXPrValorUnitario'] != null) {
					$valorUnitario = $rowProduto['AdXPrValorUnitario'];
					$valorTotal = $rowProduto['AdXPrQuantidade'] * $rowProduto['AdXPrValorUnitario'];
				} else {
					$valorUnitario = 0;
					$valorTotal = 0;
				}

				if ($totalProdutos == ($cont)) {
					$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['AdXPrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
				</tr>
			";
				} else if ($rowProduto['AdXPrQuantidade'] > 0) {
					$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowProduto['ProduNome'] . ": " . $rowProduto['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowProduto['UnMedSigla'] . "</td>					
					<td style='text-align: center;'>" . $rowProduto['AdXPrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right'>" . mostraValor($valorTotal) . "</td>
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
					        " . mostraValor($totalGeralProdutos) . "
					    </td>
				    </tr>";
			$html .= "</table>";
		}

		$totalGeralServicos = 0;

		if ($totalServicos > 0) {

			$html .= "<div style='margin-top: 10px; margin-bottom: -20px; text-align: center;'><h4>Serviços Aditivo N°".$aditivo['AditiNumero']."</h4></div>";

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

			foreach ($rowServicos as $rowServico) {

				if ($rowServico['AdXSrValorUnitario'] != '' and $rowServico['AdXSrValorUnitario'] != null) {
					$valorUnitario = $rowServico['AdXSrValorUnitario'];
					$valorTotal = $rowServico['AdXSrQuantidade'] * $rowServico['AdXSrValorUnitario'];
				} else {
					$valorUnitario = "";
					$valorTotal = "";
				}

				if ($totalServicos == ($cont)) {
					$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['Detalhamento'] . "</td>	
					<td style='text-align: center;'>" . $rowServico['AdXSrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
				</tr>
			";
				} else if ($rowProduto['AdXPrQuantidade'] > 0) {
					$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $rowServico['ServiNome'] . ": " . $rowServico['Detalhamento'] . "</td>
					<td style='text-align: center;'>" . $rowServico['AdXSrQuantidade'] . "</td>
					<td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					<td style='text-align: right'>" . mostraValor($valorTotal) . "</td>
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
					        " . mostraValor($totalGeralServicos) . "
					    </td>
				    </tr>";
			$html .= "</table>";
		}

		$totalGeral = $totalGeralProdutos + $totalGeralServicos;

		$html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
	 			<tr>
                	<td colspan='5' height='50' valign='middle' style='width:85%'>
	                    <strong>TOTAL DO ADITIVO</strong>
                    </td>
				    <td style='text-align: right; width:15%'>
				        " . mostraValor($totalGeral) . "
				    </td>
			    </tr>
			  </table>
	";
		/////////////////////////////////
	}

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
