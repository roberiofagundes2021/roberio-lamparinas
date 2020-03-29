<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iOrdemCompra = $_POST['inputOrdemCompraId'];
$sNumero = $_POST['inputOrdemCompraNumero'];

if ($_POST['inputOrdemCompraTipo'] == 'O'){
	$sTipo = "Ordem de Compra";
} else{
	$sTipo = "Carta Contrato";
}

$sql = "SELECT *
		FROM OrdemCompra
		JOIN Fornecedor on ForneId = OrComFornecedor
		JOIN Categoria on CategId = OrComCategoria
		WHERE OrComEmpresa = ". $_SESSION['EmpreId'] ." and OrComId = ".$iOrdemCompra;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

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
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:250px; float:right; display: inline; text-align:right;'>
			<div>".date('d/m/Y')."</div>
			<div style='margin-top:8px;'>".$sTipo.": ".formatarNumero($sNumero)."</div>
		</div> 
	</div>

	<div style='text-align:center; margin-top: 20px;'><h1>ORDEM DE COMPRA</h1></div>	 
	";
	
	foreach ($row as $item){	
		
		$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, OCXPrQuantidade, OCXPrValorUnitario
				FROM Produto
				JOIN OrdemCompraXProduto on OCXPrProduto = ProduId
				JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and OCXPrOrdemCompra = ".$iOrdemCompra;

		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
		$totalProdutos = count($rowProdutos);

		$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, OCXSrQuantidade, OCXSrValorUnitario
				FROM Servico
				JOIN OrdemCompraXServico on OCXSrServico = ServiId
				WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and OCXSrOrdemCompra = ".$iOrdemCompra;

		$result = $conn->query($sql);
		$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
		$totalServicos = count($rowServicos);

		$html .= '
		<div style="font-weight: bold; position:relative; margin-top: 10px; background-color:#ccc; padding: 5px;">
			Fornecedor: <span style="font-weight:normal;">'.$item['ForneNome'].'</span> <span style="color:#aaa;"></span><br>Telefone: <span style="font-weight:normal;">'.$item['ForneCelular'].'</span> <span style="color:#aaa;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> E-mail: <span style="font-weight:normal;">'.$item['ForneEmail'].'</span>
		</div>
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			Categoria: <span style="font-weight:normal;">'.$item['CategNome'].'</span>
		</div>
		<br>
		<div>'.$item['OrComConteudo'].'</div>
		<br>';
		
		if($totalProdutos > 0){
	
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
			$totalGeralProdutos = 0;
			
			foreach ($rowProdutos as $itemProduto){
				
				if ($itemProduto['OCXPrValorUnitario'] != '' and $itemProduto['OCXPrValorUnitario'] != null){
					$valorUnitario = $itemProduto['OCXPrValorUnitario'];
					$valorTotal = $itemProduto['OCXPrQuantidade'] * $itemProduto['OCXPrValorUnitario'];
				} else {
					$valorUnitario = "";
					$valorTotal = "";
				}

				if($totalProdutos == ($cont)){
					$html .= "
					<tr>
						<td style='text-align: center;'>".$cont."</td>
						<td style='text-align: left;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."</td>
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
						<td style='text-align: left;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."</td>
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

		if($totalServicos > 0){
	
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
			$totalGeralServicos = 0;
			
			foreach ($rowServicos as $itemServico){
				
				if ($itemServico['OCXSrValorUnitario'] != '' and $itemServico['OCXSrValorUnitario'] != null){
					$valorUnitario = $itemServico['OCXSrValorUnitario'];
					$valorTotal = $itemServico['OCXSrQuantidade'] * $itemServico['OCXSrValorUnitario'];
				} else {
					$valorUnitario = "";
					$valorTotal = "";
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
	}
	
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
    echo $e->getMessage();
}
