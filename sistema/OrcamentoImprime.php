<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iOrcamento = $_POST['inputOrcamentoId'];
$sNumero = $_POST['inputOrcamentoNumero'];

$sql = "SELECT *
		FROM Orcamento
		JOIN Fornecedor on ForneId = OrcamFornecedor
		JOIN Categoria on CategId = OrcamCategoria
		LEFT JOIN SubCategoria on SbCatId = OrcamSubCategoria
		WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ." and OrcamId = ".$iOrcamento;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        //'format' => [190, 236], 
        'format' => 'A4-L',
        'default_font_size' => 9,
		'default_font' => 'dejavusans',
        'orientation' => 'L'
	]);
	
	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Orçamento: ".formatarNumero($sNumero)."</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	foreach ($row as $item){	
		
		if ($item['OrcamTipo'] == 'S'){
			$tipo = "Serviço";
		} else{
			$tipo = "Produto";
		}
		
		$html .= '
		<br>
		<div style="font-weight: bold; position:relative; margin-top: 50px; background-color:#ccc; padding: 5px;">
			Fornecedor: <span style="font-weight:normal;">'.$item['ForneNome'].'</span> <span style="color:#aaa;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> Telefone: <span style="font-weight:normal;">'.$item['ForneCelular'].'</span> <span style="color:#aaa;">&nbsp;&nbsp;|&nbsp;&nbsp;</span> E-mail: <span style="font-weight:normal;">'.$item['ForneEmail'].'</span>
		</div>
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#eee; padding: 5px;">
			Categoria: <span style="font-weight:normal;">'.$item['CategNome'].'</span> &nbsp;&nbsp;<span style="color:#ccc;">|</span> &nbsp;&nbsp; SubCategoria: <span style="font-weight:normal;">'.$item['SbCatNome'].'</span> 
		</div>
		<br>
		<div>'.$item['OrcamConteudo'].'</div>
		<br>
		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">Item</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:55%">'.$tipo.'</th>
				<th style="text-align: center; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Quantidade</th>
				<th style="text-align: center; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Unidade</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Valor Unit.</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%">Valor Total</th>
			</tr>
		';	
		
		$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, OrXPrQuantidade, OrXPrValorUnitario
				FROM Produto
				JOIN OrcamentoXProduto on OrXPrProduto = ProduId
				LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
				WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and OrXPrOrcamento = ".$iOrcamento;

		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);		
		
		$cont = 1;
		
		foreach ($rowProdutos as $itemProduto){
			
			$html .= "
				<tr>
					<td style='padding-top: 8px;'>".$cont."</td>
					<td style='padding-top: 8px;'>".$itemProduto['ProduNome'].": ".$itemProduto['ProduDetalhamento']."</td>
					<td style='padding-top: 8px; text-align: center;'>".$itemProduto['OrXPrQuantidade']."</td>
					<td style='padding-top: 8px; text-align: center;'>".$itemProduto['UnMedSigla']."</td>
					<td style='padding-top: 8px;'>______________</td>
					<td style='padding-top: 8px;'>______________</td>
				</tr>
			";
			
			$cont++;
		}
		
		$html .= "</table>";
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
    
    $mpdf->SetHTMLHeader($topo,'O',true);
    $mpdf->WriteHTML($html);
    $mpdf->SetHTMLFooter($rodape);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
