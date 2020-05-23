<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$sTipoProdutoServico = $_POST['TipoProdutoServico'];
$dDataInicio = $_POST['inputDataDe_imp'];
$dDataFim = $_POST['inputDataAte_imp'];
$iTipo = isset($_POST['cmbTipo_imp']) ? $_POST['cmbTipo_imp'] : 0;
$iFornecedor = isset($_POST['cmbFornecedor_imp']) ? $_POST['cmbFornecedor_imp'] : 0;
$iCategoria = isset($_POST['cmbCategoria_imp']) ? $_POST['cmbCategoria_imp'] : 0;
$iSubCategoria = isset($_POST['cmbSubCategoria_imp']) ? $_POST['cmbSubCategoria_imp'] : 0;
$sCodigo = isset($_POST['cmbCodigo_imp']) ? $_POST['cmbCodigo_imp'] : 0;
$iProduto = isset($_POST['cmbProduto_imp']) ? $_POST['cmbProduto_imp'] : 0;
$iServico = isset($_POST['cmbServico_imp']) ? $_POST['cmbServico_imp'] : 0;


if ($sTipoProdutoServico == 'P') {
	$sql = "SELECT MovimData, MovimTipo, MovimDestinoLocal, MovimDestinoManual, MovimNotaFiscal, MvXPrQuantidade, MvXPrLote,
	        MvXPrValidade, MvXPrValorUnitario, ProduNome, ForneNome, LcEstNome as Origem, ClassNome
		FROM Movimentacao
		JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
		JOIN Produto on ProduId = MvXPrProduto
		LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		LEFT JOIN LocalEstoque on LcEstId = MovimOrigemLocal
		LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
		Where MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimData between '" . $dDataInicio . "' and '" . $dDataFim . "' ";

	if ($iCategoria != '#' and $iCategoria != 0) {
		$sql .= " and ProduCategoria = $iCategoria ";
	}

	if ($iSubCategoria != '#' and $iSubCategoria != 0) {
		$sql .= " and ProduSubCategoria = $iSubCategoria ";
	}

	if ($iProduto != '#' and $iProduto != 0) {
		$sql .= " and ProduId = $iProduto ";
	}

	if ($iFornecedor != '#' and $iFornecedor != 0) {
		$sql .= " and MovimFornecedor = $iFornecedor ";
	}
} else {
	$sql = "SELECT MovimData, MovimTipo, MovimDestinoLocal, MovimDestinoManual, MovimNotaFiscal, 
	       MvXSrQuantidade, MvXSrLote, MvXSrValorUnitario, ServiNome, ForneNome, LcEstNome as Origem
		   FROM Movimentacao
		   JOIN MovimentacaoXServico on MvXSrMovimentacao = MovimId
		   JOIN Servico on ServiId = MvXSrServico
		   LEFT JOIN Fornecedor on ForneId = MovimFornecedor
		   LEFT JOIN LocalEstoque on LcEstId = MovimOrigemLocal
		   Where MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimData between '" . $dDataInicio . "' and '" . $dDataFim . "' ";

	if ($iCategoria != '#' and $iCategoria != 0) {
		$sql .= " and ServiCategoria = $iCategoria ";
	}

	if ($iSubCategoria != '#' and $iSubCategoria != 0) {
		$sql .= " and ServiSubCategoria = $iSubCategoria ";
	}

	if ($iProduto != '#' and $iProduto != 0) {
		$sql .= " and ServiId = $iServico ";
	}

	if ($iFornecedor != '#' and $iFornecedor != 0) {
		$sql .= " and MovimFornecedor = $iFornecedor ";
	}
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
			<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: " . $_SESSION['UnidadeNome'] . "</div>
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
			<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; padding-rigth: 10px; width:15%">Fornecedor</th>
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

	foreach ($row as $item) {
		if ($sTipoProdutoServico == 'P') {
			$html .= "
			<tr>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . mostraData($item['MovimData']) . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['ForneNome'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['Origem'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['ProduNome'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['MvXPrQuantidade'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['MvXPrLote'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . mostraData($item['MvXPrValidade']) . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['MovimNotaFiscal'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . $item['ClassNome'] . "</td>
				<td style='padding-top: 15px; border-top: 1px solid #333;'>" . mostraValor($item['MvXPrValorUnitario']) . "</td>
			</tr>
		";
		} else {
			$html .= "
				<tr>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . mostraData($item['MovimData']) . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['ForneNome'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['Origem'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['ServiNome'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['MvXSrQuantidade'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['MvXSrLote'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . mostraData($item['MvXPrValidade']) . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . $item['MovimNotaFiscal'] . "</td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'></td>
					<td style='padding-top: 8px; border-top: 1px solid #333;'>" . mostraValor($item['MvXSrValorUnitario']) . "</td>
				</tr>
			";
		}
	}

	$html .= "</table>";

	$rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";

	$mpdf->SetHTMLHeader($topo, 'O', true);
	$mpdf->WriteHTML($html);
	$mpdf->SetHTMLFooter($rodape);

	// Other code
	$mpdf->Output();
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

	// Process the exception, log, print etc.
	echo $e->getMessage();
}
