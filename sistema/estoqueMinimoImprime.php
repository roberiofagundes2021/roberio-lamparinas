<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';


$sql = "SELECT DISTINCT ProduCodigo, ProduNome, ProduEstoqueMinimo, dbo.fnSaldoEstoque(MovimEmpresa, ProduId, MovimDestinoLocal) as saldo
	    FROM Produto
	    JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
	    JOIN Movimentacao on MovimId = MvXPrMovimentacao
	    WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and dbo.fnSaldoEstoque(MovimEmpresa, ProduId, MovimDestinoLocal) < ProduEstoqueMinimo
	    ORDER BY ProduCodigo
	    ";
$result = $conn->query($sql);
$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        //'format' => [190, 236], 
        'format' => 'A4-L',
        'default_font_size' => 10,
		'default_font' => 'dejavusans',
        //'orientation' => 'P', //P =>Portrait, L=> Landscape
		'margin_top' => 30 // se quiser dar margin no header, aí seria 'margin_header'
	]);
	
	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='float:left; width: 400px; display: inline-block;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:10px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
		</div>
		<div style='width:300px; float:right; display: inline-block; text-align:right; font-size: 10px; padding-bottom'>
			<div style='margin-bottom: 20px'>Data: {DATE j/m/Y}</div>
			<div style='margin-top:8px;font-weight:bold;'>Estoque Mínimo</div>
		</div> 
	 </div>
	";		
	
	$html = '';
    
    $html .= '<br>
                <div style="border-bottom: 1px solid #333; margin-bottom: -32px; margin-top: -50px">
                  <h3 style="text-align: center; padding-top: 40px; padding-bottom: -20px; margin-bottom:60px">Relação dos Produtos em estoque mínimo</h3>
	          </div>
	          <br>';

	$html .= '
				<br>
				<table style="width:100%; border-collapse: collapse;">
					<tr>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:5%">Código</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:38%">Descrição</th>
						<th style="text-align: center; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:12%">Estoque Mínimo</th>
						<th style="text-align: center; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:15%">Estoque atual</th>
					</tr>
				';	
	    foreach ($rowProduto as $value) {
		
			$html .= "
					<tr>
						<td style='padding-top: 8px; padding-bottom: 8px; font-size: 11px;'>".$value['ProduCodigo']."</td>
						<td style='padding-top: 8px; padding-bottom: 12px; font-size: 11px;'>".$value['ProduNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['ProduEstoqueMinimo']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['saldo']."</td>
					</tr>
					";
	    }
	    $html .= "</table>";
				  
			
    
    $rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";
    
$mpdf->SetHTMLHeader($topo);	
	
   // $mpdf->SetHTMLHeader($topo,'O',true);
    $mpdf->SetHTMLFooter($rodape);
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
