<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iTR = $_POST['inputTRId'];
$sNumero = $_POST['inputTRNumero'];

$sql = "SELECT *
		FROM TermoReferencia
		JOIN Categoria on CategId = TrRefCategoria
		WHERE TrRefEmpresa = " . $_SESSION['EmpreId'] . " and TrRefId = " . $iTR;
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT *
		FROM TRXSubcategoria
		JOIN SubCategoria on SbCatId = TRXSCSubcategoria
		WHERE TRXSCEmpresa = " . $_SESSION['EmpreId'] . " and TRXSCTermoReferencia = " . $iTR;
$result = $conn->query($sql);
$rowTermoReferencia = $result->fetchAll(PDO::FETCH_ASSOC);


// Selects para identificar a a tabela de origem dos produtos da TR.
$sql = "SELECT TRXPrProduto
		FROM TermoReferenciaXProduto
		JOIN ProdutoOrcamento on PrOrcId = TRXPrProduto
		WHERE TRXPrEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'ProdutoOrcamento'";
$result = $conn->query($sql);
$rowProdutoUtilizado1 = $result->fetchAll(PDO::FETCH_ASSOC);
$countProdutoUtilizado1 = count($rowProdutoUtilizado1);


$sql = "SELECT TRXPrProduto
		FROM TermoReferenciaXProduto
		JOIN Produto on ProduId = TRXPrProduto
		WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'Produto'";
$result = $conn->query($sql);
$rowProdutoUtilizado2 = $result->fetchAll(PDO::FETCH_ASSOC);
$countProdutoUtilizado2 = count($rowProdutoUtilizado2);


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

	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:220px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Termo de Referência: " . formatarNumero($sNumero) . "</div>
		</div> 
	 </div>
	";

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
				<div style='margin-top:8px;'>Termo de Referência: " . formatarNumero($sNumero) . "</div>
			</div> 
		 </div>

		 <div style='text-align:center; margin-top: 20px;'><h1>TERMO DE REFERÊNCIA</h1></div>
	";

	foreach ($row as $item) {

		$html .= '
		<div>' . $item['TrRefConteudo'] . '</div>
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			Categoria: <span style="font-weight:normal;">' . $item['CategNome'] . '</span> 
		</div>
		<br>
		';

		$sql = "SELECT ProduId, ProduNome, ProduCategoria, ProduSubCategoria, ProduDetalhamento, UnMedSigla, TRXPrQuantidade, TRXPrValorUnitario
				FROM Produto
				JOIN TermoReferenciaXProduto on TRXPrProduto = ProduId
				LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and TRXPrTermoReferencia = " . $iTR;

		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

		$cont = 1;

		foreach ($rowTermoReferencia as $sbcat) {
			
			$html .= '
			    <div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			        SubCategoria: <span style="font-weight:normal;">' . $sbcat['SbCatNome'] . '</span>
		        </div>
		        <br>
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

			$totalGeralProdutos = 0;
				
			foreach ($rowProdutos as $itemProduto) {

				if ($sbcat['TRXSCSubcategoria'] == $itemProduto['ProduSubCategoria']) {

					if ($itemProduto['OrXPrValorUnitario'] != '' and $itemProduto['OrXPrValorUnitario'] != null) {
						$valorUnitario = $itemProduto['OrXPrValorUnitario'];
						$valorTotal = $itemProduto['OrXPrQuantidade'] * $itemProduto['OrXPrValorUnitario'];
					} else {
						$valorUnitario = "";
						$valorTotal = "";
					}

					$html .= "
						<tr>
							<td style='text-align: center;'>" . $cont . "</td>
							<td style='text-align: left;'>" . $itemProduto['ProduNome'] . ": " . $itemProduto['ProduDetalhamento'] . "</td>
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
	/*	
	$sql = "SELECT UsuarId, UsuarNome, UsuarEmail, UsuarTelefone
			FROM Usuario
			Where UsuarId = ".$_SESSION['UsuarId']."
			ORDER BY UsuarNome ASC";
	$result = $conn->query($sql);
	$rowUsuario = $result->fetch(PDO::FETCH_ASSOC);	
	
	$html .= '			
		<br><br>
		<div style="width: 100%; margin-top: 200px;">
			<div style="position: relative; float: left; text-align: center;">
				Solicitante: '.$rowUsuario['UsuarNome'].'<br>
				<div style="margin-top:3px;">
					Telefone: '.$rowUsuario['UsuarTelefone'].' <br>
					E-mail: '.$rowUsuario['UsuarEmail'].'
				</div>
			</div>
		</div>
	';	
*/
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
