<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

$iCliente = $_POST['inputClienteId'];

$sql = "SELECT *
		FROM Cliente
		JOIN Situacao on SituaId = ClienStatus
		WHERE ClienUnidade = " . $_SESSION['UnidadeId'] . " and ClienId = " . $iCliente;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

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

	$mpdf->SetDisplayMode('fullpage','two'); //'fullpage': Ajustar uma página inteira na tela, 'fullwidth': Ajustar a largura da página na tela, 'real': Exibir em tamanho real, 'default': Configuração padrão do usuário no Adobe Reader, 'none'

	/*$mpdf = new Mpdf([
		'mode' => 'utf-8',
		//'format' => [190, 236], 
		'format' => 'A4-P', //A4-L
		'default_font_size' => 9,
		'default_font' => 'dejavusans',
		'orientation' => 'P' //P->Portrait (retrato)    L->Landscape (paisagem)
	]);*/


	


	//$html .= "</div>";

	$topo = "
	<div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
		</div>
		<div style='width:300px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;font-weight:bold;'>CADASTRO DO CLIENTE</div>
		</div> 
	 </div>
	";	

				  
		$html = '
		<style>

			td{
				padding: 15px;				
				border: #bbb solid 1px;
			}
		</style>
		<br>
		<br>
		<div style="text-align:center;"><h1>'.$row['ClienNome'].'</h1></div>
		<br>

		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<td style="padding-top: 8px; font-size: 11px;">CPF: '.$row['ClienCpf'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">Cartão SUS: '.$row['ClienCartaoSus'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">RG: '.$row['ClienRg'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">Órgão Emissor: '.$row['ClienOrgaoEmissor'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">UF: '.$row['ClienUf'].'</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top: 8px; font-size: 11px;">CPF: '.$row['ClienCpf'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">Cartão SUS: '.$row['ClienCartaoSus'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">RG: '.$row['ClienRg'].'</td>
				<td style="padding-top: 8px; font-size: 11px;">Órgão Emissor: '.$row['ClienOrgaoEmissor'].'</td>
			
			</tr>
		</table>
					';
			
						 
    
	$rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";
	
	//ATENÇÃO: Tive que colocar o cabeçalho dentro do HTML, para o cabeçalho não sobrescrever o conteúdo HTML a partir da segunda página. Em compensação o cabeçalho só aparece na primeira página. Foi a única forma que encontrei. Tentei de tudo...

	$mpdf->SetHTMLHeader($topo);	
   // $mpdf->SetHTMLHeader($topo,'O',true);
    $mpdf->SetHTMLFooter($rodape);
    $mpdf->WriteHTML($html);
	// Other code
	$mpdf->Output();

} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

	// Process the exception, log, print etc.
	echo 'ERRO: '.$e->getMessage();
}
