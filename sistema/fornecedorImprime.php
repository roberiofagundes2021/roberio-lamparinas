<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iCategoria = $_POST['inputFornecedorCategoria'];

if ($iCategoria == '#'){
	$sql = ("SELECT *
			 FROM Fornecedor
			 WHERE ForneEmpresa = ".$_SESSION['EmpreId']);
} else {
	$sql = ("SELECT *
			 FROM Fornecedor
			 WHERE ForneCategoria = ".$iCategoria." and ForneEmpresa = ".$_SESSION['EmpreId']);
}

$result = $conn->query("$sql");
$row = $result->fetch(PDO::FETCH_ASSOC);

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
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Hospital Padre Manoel</div>
		</div>
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>			
		</div> 
	 </div>
	";		
	
	if ($iCategoria == '#'){
		
	}
		
	
	$html .= '
	<br>
	<div style="font-weight: bold; position:relative; margin-top: 50px;text-transform: uppercase;">Categoria: '.$item['CategNome'].'</div>
	<div style="font-weight: bold; position:relative; margin-top: 20px;">'.$item['CategNome'].'</div>
	<br>
	<table style="width:100%;">
		<tr>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Código</th>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:42%">Produto</th>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:8%">Unidade</th>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">Quantidade</th>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Unitário</th>
			<th style="text-align: left; border-top: 1px solid #333; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Valor Total</th>
		</tr>
	';		
    
    $rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";
    
    $mpdf->SetHTMLHeader($topo,'O',true);
    $mpdf->SetHTMLFooter($rodape);
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
