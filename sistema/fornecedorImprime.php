<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iFornecedor = $_POST['inputFornecedorId'];

$sql = ("SELECT *
		 FROM Fornecedor
		 LEFT JOIN Empresa on EmpreId = ForneEmpresa
		 WHERE ForneId = $iFornecedor ");
$result = $conn->query("$sql");
$row = $result->fetch(PDO::FETCH_ASSOC);

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        'format' => [190, 236], 
        'orientation' => 'L'
	]);
	
	$topo = "
	<div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'><img src='teste.jpg' /> ".$row['EmpreNomeFantasia']." </div>
		<div style='width:105px; float:right; display: inline;'>{DATE j/m/Y}</div> 
	 </div>
	 <hr />			 
	";

	$html = '<br>
		
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
