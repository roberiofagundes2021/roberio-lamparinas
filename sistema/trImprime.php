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
		LEFT JOIN SubCategoria on SbCatId = TrRefSubCategoria
		WHERE TrRefEmpresa = ". $_SESSION['EmpreId'] ." and TrRefId = ".$iTR;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        //'format' => [190, 236], 
        'format' => 'A4-P', //A4-L
        'default_font_size' => 10,
		'default_font' => 'dejavusans',
        'orientation' => 'P', //P->Portrait (retrato)    L->Landscape (paisagem)
        'setAutoTopMargin' => 'pad' //deixa as margens automáticas no cabeçalho e rodapé
	]);
	
	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:220px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Termo de Referência: ".formatarNumero($sNumero)."</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	foreach ($row as $item){	

		$html .= '
		<div>'.$item['TrRefConteudo'].'</div>
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			Categoria: <span style="font-weight:normal;">'.$item['CategNome'].'</span> &nbsp;&nbsp;<span style="color:#ccc;">|</span> &nbsp;&nbsp; SubCategoria: <span style="font-weight:normal;">'.$item['SbCatNome'].'</span> 
		</div>
		<br>		
		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:7%">Item</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:51%">Produto</th>
				<th style="text-align: center; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">Quant.</th>
				<th style="text-align: center; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Unidade</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">V. Unit.</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">V. Total</th>
			</tr>
		';	
		
		$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, TRXPrQuantidade, TRXPrValorUnitario
				FROM Produto
				JOIN TermoReferenciaXProduto on TRXPrProduto = ProduId
				LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and TRXPrTermoReferencia = ".$iTR;

		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);		
		
		$cont = 1;
		
		foreach ($rowProdutos as $itemProduto){
			
			$html .= "
				<tr>
					<td style='padding-top: 8px;'>".$cont."</td>
					<td style='padding-top: 8px;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."</td>
					<td style='padding-top: 8px; text-align: center;'>".$itemProduto['TRXPrQuantidade']."</td>
					<td style='padding-top: 8px; text-align: center;'>".$itemProduto['UnMedSigla']."</td>
					<td style='padding-top: 8px;'>_________</td>
					<td style='padding-top: 8px;'>_________</td>
				</tr>
			";
			
			$cont++;
		}
		
		$html .= "</table>";
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
    
    $mpdf->SetHTMLHeader($topo,'0');
    $mpdf->WriteHTML($html);
    $mpdf->SetHTMLFooter($rodape);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
