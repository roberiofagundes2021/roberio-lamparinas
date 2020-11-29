<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';
$sNome = '';
$iNome = $_POST['inputNome'];
	 
if ($iNome == '#'){
	$sql = "SELECT ClienTipo, ClienRazaoSocial, ClienNome, ClienCpf, ClienCnpj, ClienContato, ClienTelefone, ClienEmail
    FROM Cliente
    JOIN Situacao on SituaId = ClienStatus
    WHERE ClienUnidade = ".$_SESSION['UnidadeId']." and SituaChave = 'ATIVO'
    Group By  ClienTipo, ClienRazaoSocial, ClienNome, ClienCpf, ClienCnpj, ClienContato, ClienTelefone, ClienEmail";
} else {
	
	$sql = "SELECT *
            FROM Cliente
            JOIN Situacao on SituaId = ClienStatus
            WHERE ClienNome = ".$iNome." and ClienUnidade = ".$_SESSION['UnidadeId']." and SituaChave = 'ATIVO'";
}

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

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
		<div style='width:300px; float:left; display: inline;'>
			<img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			<span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
		</div>
		<div style='width:300px; float:right; display: inline; text-align:right;'>
			<div>{DATE j/m/Y}</div>
			<div style='margin-top:8px;font-weight:bold;'>RELAÇÃO DE Clientes</div>
		</div> 
	 </div>
	";		
	
	$html = '';
	
	//Se for todas os Nomes
	if ($iNome == '#'){

		$cont = 1;
		$nomeVelha = 0;
		foreach ($row as $item){

			$nomeNova = $item['ClienNome'];
			
			if ($nomeNova <> $nomeVelha){
				
				if($nomeVelha <> 0){
					$html .= "</table>";
				}
				
				$html .= '<div style="position:relative; margin-top: 40px; text-transform: uppercase; font-weight: bold; background-color: #ccc; padding: 5px;">Nome: '.$item['ClienNome'].'</div>';
						  
				$html .= '
				<br>
				<table style="width:100%; border-collapse: collapse;">
					<tr>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:5%">Num</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:38%">Razão Social/Nome</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:15%">CNPJ/CPF</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:12%">Contato</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:15%">Telefone</th>
						<th style="text-align: left; padding-top: 7px; padding-bottom: 7px; border-bottom: 1px solid #333; width:15%">E-mail</th>
					</tr>
				';							  
			}
			
			if ($item['ClienTipo'] == 'F'){
				$sCliente = $item['ClienNome'];
				$iDocumento = formatarCPF_Cnpj($item['ClienCpf']);
			} else {
				$sCliente = $item['ClienRazaoSocial'];
				$iDocumento = formatarCPF_Cnpj($item['ClienCnpj']);
			}
		
			$html .= "
					<tr>
						<td style='padding-top: 8px; font-size: 11px;'>".$cont."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$sCliente."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$iDocumento."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienContato']."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienTelefone']."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienEmail']."</td>
					</tr>
					";
			$cont++;	
			$nomeVelha = $nomeNova;
		}
				  
	} else {
		$html .= '<div style="font-weight: bold; position:relative; margin-top: 50px;text-transform: uppercase; background-color: #ccc; padding:5px;">Nome: '.$sCliente.'</div>';
				  
		$html .= '
		<br>
		<table style="width:100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:5%">Num</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:38%">Razão Social/Nome</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">CNPJ/CPF</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%">Contato</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">Telefone</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:15%">E-mail</th>
			</tr>
		';					  
	
		$cont = 1;
		foreach ($row as $item){	
		
			if ($item['ClienTipo'] == 'F'){
				$sCliente = $item['ClienNome'];
				$iDocumento = formatarCPF_Cnpj($item['ClienCpf']);
			} else {
				$sCliente = $item['ClienRazaoSocial'];
				$iDocumento = formatarCPF_Cnpj($item['ClienCnpj']);
			}
		
			$html .= "
					<tr>
						<td style='padding-top: 8px; font-size: 11px;'>".$cont."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$sCliente."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$iDocumento."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienContato']."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienTelefone']."</td>
						<td style='padding-top: 8px; font-size: 11px;'>".$item['ClienEmail']."</td>
					</tr>
					";
			$cont++;
		}
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
