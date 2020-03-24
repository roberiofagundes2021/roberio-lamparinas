<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iOrcamento = $_POST['inputOrcamentoId'];
$sNumero = $_POST['inputOrcamentoNumero'];

$sql = "SELECT *
		FROM Orcamento
		LEFT JOIN Fornecedor on ForneId = OrcamFornecedor
		JOIN Categoria on CategId = OrcamCategoria
		LEFT JOIN OrcamentoXSubCategoria on OrXSCOrcamento = OrcamId
		LEFT JOIN SubCategoria on SbCatId = OrXSCSubcategoria 
		WHERE OrcamEmpresa = " . $_SESSION['EmpreId'] . " and OrcamId = " . $iOrcamento;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

try {
	$mpdf = new Mpdf([
		'mode' => 'utf-8',
		//'format' => [190, 236], 
		'format' => 'A4-P', //A4-L
		'default_font_size' => 9,
		'default_font' => 'dejavusans',
		'orientation' => 'P' //P->Portrait (retrato)    L->Landscape (paisagem)
	]);

	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Orçamento: " . formatarNumero($sNumero) . "</div>
		</div> 
	 </div>
	";

	$html = '

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

	';

	if ($row['OrcamTipo'] == 'S') {
		$tipo = "Serviço";
	} else {
		$tipo = "Produto";
	}

	if ($tipo == "Produto") {
		$html .= '
		            <br>
		            <div style="font-weight: bold; position:relative; margin-top: 50px; background-color:#ccc; padding: 5px;">
			            Fornecedor: <span style="font-weight:normal;">' . $row['ForneNome'] . '</span> <span style="color:#aaa;"></span><br>Telefone: <span style="font-weight:normal;">' . $row['ForneCelular'] . '</span> <span style="color:#aaa;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> E-mail: <span style="font-weight:normal;">' . $item['ForneEmail'] . '</span>
		            </div>
		            <div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			            Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> &nbsp;&nbsp;<span style="color:#ccc;">|</span> &nbsp;&nbsp; SubCategoria: <span style="font-weight:normal;">' . $row['SbCatNome'] . '</span> 
		            </div>
		            <br>
		            <div>' . $row['OrcamConteudo'] . '</div>
		            <br>
		            <table style="width:100%; border-collapse: collapse;">
			            <tr>
			            	<th style="text-align: center; width:8%">Item</th>
				            <th style="text-align: left; width:46%">' . $tipo . '</th>
				            <th style="text-align: center; width:12%">Quant.</th>
				            <th style="text-align: center; width:11%">Unidade</th>
				            <th style="text-align: center; width:11%">V. Unit.</th>
				            <th style="text-align: center; width:12%">V. Total</th>
			            </tr>
		            ';

		$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, OrXPrQuantidade, OrXPrValorUnitario
				FROM Produto
				JOIN OrcamentoXProduto on OrXPrProduto = ProduId
				LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OrXPrOrcamento = " . $iOrcamento;

		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

		$cont = 1;
		$cont2 = 0;
		$totalGeral = 0;

		foreach ($rowProdutos as $itemProduto) {

			if (($itemProduto['OrXPrValorUnitario'] != '' and $itemProduto['OrXPrValorUnitario'] != null) && ($itemProduto['OrXPrQuantidade'] != '' and $itemProduto['OrXPrQuantidade'] != null)) {
				$valorUnitario = mostraValor($itemProduto['OrXPrValorUnitario']);
				$valorTotal = mostraValor($itemProduto['OrXPrQuantidade'] * $itemProduto['OrXPrValorUnitario']);
				$totalGeral = ($itemProduto['OrXPrQuantidade'] * $itemProduto['OrXPrValorUnitario']) + $totalGeral;

				$cont2++;
			} else {
				$valorUnitario = "";
				$valorTotal = "";
			}

			$html .= "
				<tr>
					<td style='text-align: center;'>" . $cont . "</td>
					<td style='text-align: left;'>" . $itemProduto['ProduNome'] . ": " . $itemProduto['ProduDetalhamento'] . "</td>
					<td style='text-align: center;'>" . $itemProduto['OrXPrQuantidade'] . "</td>
					<td style='text-align: center;'>" . $itemProduto['UnMedSigla'] . "</td>
					<td style='text-align: right;'>" . $valorUnitario . "</td>
					<td style='text-align: right; '>" . $valorTotal . "</td>
				</tr>
			";

			$cont++;
		}

		if ($cont2 == count($rowProdutos)) {
			$html .= "  
			    <tr>
	            	<td colspan='5' height='50' valign='middle'>
		                <strong>Total Geral</strong>
	                </td>
				    <td style='border-top: 1px solid #bbb; text-align: right;'>
				        " . mostraValor($totalGeral) . "
				    </td>
				</tr>";
		} else {
			$html .= "  
			    <tr>
	            	<td colspan='5' height='50' valign='middle'>
		                <strong>Total Geral</strong>
	                </td>
				    <td style='border-top: 1px solid #bbb; text-align: right'>
					    
				    </td>
				</tr>";
		}


		$html .= "</table>";
	} else {
		$html .= '
		            <br>
		            <div style="font-weight: bold; position:relative; margin-top: 50px; background-color:#ccc; padding: 5px;">
		            	Fornecedor: <span style="font-weight:normal;">' . $row['ForneNome'] . '</span> <span style="color:#aaa;"></span><br>Telefone: <span style="font-weight:normal;">' . $row['ForneCelular'] . '</span> <span style="color:#aaa;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> E-mail: <span style="font-weight:normal;">' . $row['ForneEmail'] . '</span>
		            </div>
		            <div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
		            	Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> &nbsp;&nbsp;<span style="color:#ccc;">|</span> &nbsp;&nbsp; SubCategoria: <span style="font-weight:normal;">' . $row['SbCatNome'] . '</span> 
		            </div>
		            <br>
		            <div>' . $row['OrcamConteudo'] . '</div>
		            <br>
		            <table style="width:100%; border-collapse: collapse;">
		            	<tr>
		            		<th style="text-align: center; width:8%">Item</th>
		            		<th style="text-align: left; width:50%">' . $tipo . '</th>
		            		<th style="text-align: center; width:12%">Quant.</th>
		            		<th style="text-align: center; width:15%">V. Unit.</th>
		            		<th style="text-align: center; width:15%">V. Total</th>
		            	</tr>
		            ';

		$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, OrXSvQuantidade, OrXSvValorUnitario
				FROM Servico
				JOIN OrcamentoXServico on OrXSvServico = ServiId
				WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and OrXSvOrcamento = " . $iOrcamento;

		$result = $conn->query($sql);
		$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);

		$cont = 1;
		$cont2 = 0;
		$totalGeral = 0;

		foreach ($rowServicos as $itemServico) {

			if (($itemServico['OrXSvValorUnitario'] != '' and $itemServico['OrXSvValorUnitario'] != null and $itemServico['OrXSvValorUnitario'] != 0) && ($itemServico['OrXSvQuantidade'] != '' and $itemServico['OrXSvQuantidade'] != null and $itemServico['OrXSvQuantidade'] != 0)) {
				$valorUnitario = mostraValor($itemServico['OrXSvValorUnitario']);
				$valorTotal = mostraValor($itemServico['OrXSvQuantidade'] * $itemServico['OrXSvValorUnitario']);
				$totalGeral = ($itemServico['OrXSvQuantidade'] * $itemServico['OrXSvValorUnitario']) + $totalGeral;

				$cont2++;
			} else {
				$valorUnitario = "";
				$valorTotal = "";
			}

			$html .= "
				<tr>
					<td style='text-align: center'>" . $cont . "</td>
					<td style='text-align: left'>" . $itemServico['ServiNome'] . ": " . $itemServico['ServiDetalhamento'] . "</td>
					<td style='text-align: center'>" . $itemServico['OrXSvQuantidade'] . "</td>
					<td style='text-align: right'>" . $valorUnitario . "</td>
					<td style='text-align: right'>" . $valorTotal . "</td>
				</tr>
			";

			$cont++;
		}

		if ($cont2 == count($rowServicos)) {
			$html .= "  
			    <tr>
	            	<td colspan='5' height='50' valign='middle'>
		                <strong>Total Geral</strong>
	                </td>
				    <td text-align: left'>
				        " . mostraValor($totalGeral) . "
				    </td>
				</tr>";
		} else {
			$html .= "  
			    <tr>
	            	<td colspan='4' height='50' valign='middle'>
		                <strong>Total Geral</strong>
	                </td>
				    <td style='border-top: 1px solid #333; text-align: left'>
					    
				    </td>
				</tr>";
		}

		$html .= "</table>";
	}


	$sql = "SELECT UsuarId, UsuarNome, UsuarEmail, UsuarTelefone
			FROM Usuario
			Where UsuarId = " . $_SESSION['UsuarId'] . "
			ORDER BY UsuarNome ASC";
	$result = $conn->query($sql);
	$rowUsuario = $result->fetch(PDO::FETCH_ASSOC);

	$html .= '			
		<br><br>
		<div style="width: 100%; margin-top: 200px;">
			<div style="position: relative; float: left; text-align: center;">
				Solicitante: ' . $rowUsuario['UsuarNome'] . '<br>
				<div style="margin-top:3px;">
					Telefone: ' . $rowUsuario['UsuarTelefone'] . ' <br>
					E-mail: ' . $rowUsuario['UsuarEmail'] . '
				</div>
			</div>
		</div>
	';

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
