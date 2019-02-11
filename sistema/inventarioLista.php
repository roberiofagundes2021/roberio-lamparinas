<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iInventario = $_POST['inputInventarioId'];

$sql = ("SELECT ProduCodigo, ProduNome, UnMedSigla, CategNome, ProduCustoFinal 
		 FROM Produto
		 JOIN Categoria on CategId = ProduCategoria
		 JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduStatus = 1 and
		 ProduCategoria in (select InvenCategoria from Inventario where InvenId = ".$iInventario.")
		 ");
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
	<div style='width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<span style='font-size:12px;margin-left:5px;'>Unidade: Hospital Padre Manoel</span>
		</div>
		<div style='width:150px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;'>Inventário: ".$row['InvenNumero']."</div>
		</div> 
	 </div>	 
	";

	foreach ($row as $item){
		$html = '
			<br>
			<div style="font-weight: bold;">Local: '.$item[''].'</div>
			<br>
			<div style="width: 30%; text-align: center; margin-top: 100px; border-top: 1px solid #ccc; float:left;">Responsável</div>
			<div style="width: 30%; text-align: center; margin-top: 100px; border-top: 1px solid #ccc; float:left;">Membro 1</div>
			<div style="width: 30%; text-align: center; margin-top: 100px; border-top: 1px solid #ccc; float:left;">Membro 2</div>
		';
	}
	
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
