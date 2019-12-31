<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';
require_once 'global_assets/php/funcoesgerais.php';


if(isset($_POST['inputMovimentacaoId'])){
    $sql = "SELECT *, MvXPrProduto, MvXPrQuantidade, MvXPrLote, MvXPrValidade, ClassNome, ProduNome, ProduMarca, ProduModelo, ProduCodigo, ProduUnidadeMedida, ProduModelo, ProduNumSerie, CategNome, UnMedNome, ModelNome, MarcaNome
	    FROM Movimentacao
	    JOIN MovimentacaoXProduto on MvXPrMovimentacao = ".$_POST['inputMovimentacaoId']."
	    LEFT JOIN Produto on ProduId = MvXPrProduto
	    LEFT JOIN Categoria on CategId = ProduCategoria
	    LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	    LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
	    LEFT JOIN Modelo on ModelId = ProduModelo
	    LEFT JOIN Marca on MarcaId = ProduMarca
	    WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	    ";
$result = $conn->query($sql);
$rowMvPr = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT MovimData, MovimTipo, MovimFinalidade, MovimOrigem, MovimObservacao, FinalNome, MovimDestinoLocal, MovimDestinoSetor,     MovimDestinoManual, MovimMotivo, LcEstNome, ParamValorObsImpreRetirada
	    FROM Movimentacao
	    JOIN Finalidade on FinalId = MovimFinalidade
	    JOIN LocalEstoque on LcEstId = MovimOrigem
	    JOIN Parametro on ParamEmpresa = MovimEmpresa
	    WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	    ";
$result = $conn->query($sql);
$rowMv = $result->fetch(PDO::FETCH_ASSOC);
}

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        //'format' => [190, 236], 
        //'format' => 'A4-L',
        'default_font_size' => 10,
		'default_font' => 'dejavusans',
        //'orientation' => 'P', //P =>Portrait, L=> Landscape
		'margin_top' => 8 // se quiser dar margin no header, aí seria 'margin_header'
	]);
	
	// Caso seja uma movimentação de saída
	if($rowMv['MovimTipo'] == 'S'){

        $html = "";

        $html .= "<p style='font-size: 1.2rem; margin: 0px 0px 5px 0px'><strong>Recibo de retirada</strong></p>";

	    $html .= "<div style='height: 950px ;position: relative ;border: 1px solid rgb(149, 150, 148); box-sizing: border-box; padding: 20px'>";

		$topo = "
	            <div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		           <div style='float:left; width: 400px; display: inline-block; margin-bottom: 10px;'>
			           <img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			           <span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			           <div style='position: absolute; font-size:9px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
		               </div>
		            <div style='margin-top: -44px;width:300px; float:right; display: inline-block; text-align:right; font-size: 0.8rem; margin-bottom: 10px;'>
			            <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada - Requisição de Material</div>
		            </div> 
	            </div>
	    ";		

        $html .= $topo;

        if($rowMv['MovimMotivo'] != 'consignação' ||
            $rowMv['MovimMotivo'] != 'descarte' || 
            $rowMv['MovimMotivo'] != 'doação' || 
            $rowMv['MovimMotivo'] != 'devolução'){

       	    $sql = "SELECT MovimDestinoLocal, LcEstNome
	                FROM Movimentacao
	                JOIN LocalEstoque on LcEstId = MovimDestinoLocal
	                WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	        ";
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);

    	    $html .= '<br>
	            <div style="display: flex; flex-direction: column ;width: 100%; height 20px">
	               <div style="display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 18.1%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Data: '.mostraData($rowMv['MovimData']).'</p>
                        </div>
                        <div style="margin: 0px 2px 0px 2px ;float: left; width: 13.5%; border: 1px solid #333; background-color: #e9e9e9">
                            <p style="font-size: 0.8rem; text-align: center ;margin: 0px; padding: 8px">Nº 0001/19</p>
                        </div>
                        <div style="float: right; width: 66.8%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Finalidade: '.$rowMv['FinalNome'].'</p>
                        </div>
	               </div>
	               <div style="margin-top: 3px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 49.5%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Estoque de Origem: '.$rowMv['LcEstNome'].'</p>
                        </div>
                        <div style="margin: 0px 2px 0px 2px ;float: left; width: 49.5%; border: 1px solid #333">
                            <p style="font-size: 0.8rem ;margin: 0px; padding: 8px">Estoque de Destino(setor): '.$row['LcEstNome'].'</p>
                        </div>
	               </div>
	               <div style="margin-top: 7px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #333; background-color: #e9e9e9">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Identificação dos Bens</p>
                        </div>
	               </div>
	            </div>
	          <br>';
        } else {

            $sql = "SELECT MovimDestinoManual, LcEstNome
	                FROM Movimentacao
	                JOIN LocalEstoque on LcEstId = MovimDestinoManual
	                WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	        ";
            $result = $conn->query($sql);
            $row2 = $result->fetch(PDO::FETCH_ASSOC);

	          $html .= '<br>
	            <div style="display: flex; flex-direction: column ;width: 100%; height 20px">
	               <div style="display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 18.1%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Data: '.mostraData($rowMv['MovimData']).'</p>
                        </div>
                        <div style="margin: 0px 2px 0px 2px ;float: left; width: 13.5%; border: 1px solid #333; background-color: #e9e9e9">
                            <p style="font-size: 0.8rem; text-align: center ;margin: 0px; padding: 8px">Nº 0001/19</p>
                        </div>
                        <div style="float: right; width: 66.8%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Motivo: '.$rowMv['FinalNome'].'</p>
                        </div>
	               </div>
	               <div style="margin-top: 3px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 49.5%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Estoque de Origem: '.$rowMv['LcEstNome'].'</p>
                        </div>
                        <div style="margin: 0px 2px 0px 2px ;float: left; width: 49.5%; border: 1px solid #333">
                            <p style="font-size: 0.8rem ;margin: 0px; padding: 8px">Estoque de Destino(setor): '.$row['LcEstNome'].'</p>
                        </div>
	               </div>
	               <div style="margin-top: 7px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #333; background-color: #e9e9e9">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Identificação dos Bens</p>
                        </div>
	               </div>
	            </div>
	          <br>';
        }

	        $html .= '

				<br>
			
				';	

		    $cont = 0;

	        foreach ($rowMvPr as $value) {
                $cont += 1;
		        if($value['ClassNome'] == 'Bem Permanente'){
		    	  
					$html .= "
                        <div style='margin-top: -28px ;display: flex; flex-direction: row; text-align: justify'>
                            <div style='margin-right: 12px ;float: left ;width: 20%; border: 1px solid #333'>
                                <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: ".$value['ProduCodigo']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 80%; border: 1px solid #333'>
                                <p style'font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: ".$value['ProduNome']."</p>
                            </div>
	                    </div>
	                    <div style='margin-top: 2px ;margin-bottom 4px; display: flex; flex-direction: row; text-align: justify'>
                            <div style='margin-right: 12px ;float: left ;width: 30%; border: 1px solid #333'>
                                <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Categoria: ".$value['ProduCodigo']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 20%; border: 1px solid #333'>
                                <p style'font-size: 0.8rem ;margin: 0px; padding: 8px'>Classificação: ".$value['ClassNome']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 20%; border: 1px solid #333'>
                                <p style'font-size: 0.8rem ;margin: 0px; padding: 8px'>Patrimônio: 10188237</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #333'>
                                <p style'font-size: 0.8rem ;margin: 0px; padding: 8px'>Unidade: ".$value['UnMedNome']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #333'>
                                <p style'font-size: 0.8rem ;margin: 0px; padding: 8px'>Quantidade: ".$value['MvXPrQuantidade']."</p>
                            </div>
	                    </div>
					         ";
				} else {
					$html .= "
                        <div style='margin-top: -28px ;display: flex; flex-direction: row; text-align: justify'>
                            <div style='margin-right: 12px ;float: left ;width: 22%; border: 1px solid #333; background-color: #e9e9e9'>
                                <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: ".$value['ProduCodigo']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 77%; border: 1px solid #333; background-color: #e9e9e9'>
                                <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: ".$value['ProduNome']."</p>
                            </div>
	                    </div>
	                    <div style='margin-top: 2px; margin-bottom 4px; display: flex; flex-direction: row; text-align: justify'>
                            <div style='margin-right: 12px ;float: left ;width: 29%; border: 1px solid #333'>
                                <p style='font-size: 0.6rem; margin: 0px; padding: 8px'>Categoria: ".$value['CategNome']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 24%; border: 1px solid #333'>
                                <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Classificação: ".$value['ClassNome']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 16%; border: 1px solid #333'>
                                <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Patrimônio: </p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #333'>
                                <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Unidade: ".$value['UnMedNome']."</p>
                            </div>
                            <div style='margin: 0px 2px 0px 2px ;float: left; width: 13%; border: 1px solid #333; background-color: #e9e9e9'>
                                <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Quantidade: ".$value['MvXPrQuantidade']."</p>
                            </div>
	                    </div>
					         ";
				}
			
	    }

	    if($rowMv['ParamValorObsImpreRetirada'] == 1){
	    	$html .= ' 
	    	        <div style="margin-top: 7px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #333; background-color: #e9e9e9">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Observação</p>
                        </div>
	                </div>
	                <div style="margin-top: 2px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #333">
                            <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: justify">
                               '.$rowMv['MovimObservacao'].'
                            </p>
                        </div>
	                </div>
	            '; 
	    }
	    
		$html .= '<div style="margin-top: 8px ;display: flex; flex-direction: row; text-align: justify">
                        <div style="margin-right: 2px ;float: left; width: 49%; border: 1px solid #333">
                            <div style="float: left; margin-left: 29px; margin-top: 29px; margin-bottom: -20px; width: 260px; height: 100px;">
                                <div style="width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);">
                              
                                </div>
                                <div style="">
                                    <p style="font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;">Solicitante</p>
                                    <p style="color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;">Assinatura (funcionário)</p>
                                </div>
                            </div>
                        </div>
                        <div style="margin-left: 2px ;float: left; width: 50%; border: 1px solid #333">
                            <div style="float: left; margin-left: 29px; margin-top: 29px; margin-bottom: -20px; width: 260px; height: 100px;">
                                <div style="width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);">
                                    
                                </div>
                                <div style="">
                                    <p style="font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;">Solicitado</p>
                                    <p style="color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;">Assinatura (resp. pelo setor)</p>
                                </div>
                            </div>
                        </div>
	                </div>';

		 $rodape .= "<hr/>
                        <div style='width:100%'>
		                    <div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		                    <div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
                        </div>";
        
	    $html .= "</div>";
    
        $numPaginas = ($cont / 3);
        
        for($i = 1; $i <= $numPaginas; $i++) {
           //$mpdf->SetHTMLHeader($topo);
		   $mpdf->WriteHTML($html);
		   $mpdf->SetHTMLFooter($rodape);
           // $mpdf->SetHTMLHeader($topo,'O',true);	

            // Other code
           
        }

        $mpdf->Output();
        
    
    // Caso seja uma movimentação de Transferência
	} else if($rowMv['MovimTipo'] == 'T'){


        $html = '';

	    $html .= "<div style='border: 1px solid #333'>";

		$topo = "
	            <div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		           <div style='float:left; width: 400px; display: inline-block; margin-bottom: 10px;'>
			           <img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			           <span style='font-weight:bold;line-height:200px;'>".$_SESSION['EmpreNomeFantasia']."</span><br>
			           <div style='position: absolute; font-size:10px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
		               </div>
		            <div style='margin-top: -44px;width:300px; float:right; display: inline-block; text-align:right; font-size: 0.9rem; margin-bottom: 10px;'>
			            <div style='margin-top:8px; font-weight:bold;'>Recibo de Transferência - Requisição de Material</div>
		            </div> 
	            </div>
	    ";		
	
	    $html = '';
    
        if($rowMv['MovimMotivo'] != 'consignação' ||
            $rowMv['MovimMotivo'] != 'descarte' || 
            $rowMv['MovimMotivo'] != 'doação' || 
            $rowMv['MovimMotivo'] != 'devolução'){

       	    $sql = "SELECT MovimDestinoLocal, LcEstNome
	                FROM Movimentacao
	                JOIN LocalEstoque on LcEstId = MovimDestinoLocal
	                WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	        ";
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);

    	    $html .= '<br>
                <div style="border-bottom: 1px solid #333; margin-bottom: -32px; margin-top: -50px;">
                   <div style="width: 90%; display: inline-block;">
                        <p style="font-size: 0.9rem ;margin-bottom: 5px; margin-top: 20px; font-weight:bold;">
                            Data:
                            <span style="font-weight:normal"> 
                                '.mostraData($rowMv['MovimData']).'
                            </span>
                        </p>
                       <p style="font-size: 0.9rem ;margin-bottom: 5px; margin-top: 8px; font-weight:bold;">
                            Tipo de Transferência:
                            <span style="font-weight:normal">
                              '.$rowMv['FinalNome'].'
                            </span>
                        </p>
                       <div style="width: 100%;">
                            <p style="font-size: 0.9rem ;margin-top: 0px;width: 250px ;float: left ; font-weight:bold;">
                                Estoque de origem:
                                <span style="font-weight:normal">
                                    '.$rowMv['LcEstNome'].'
                                </span>
                            </p>
                           <p style=" font-size: 0.9rem; width: 350px;margin-top: 0px;float: right ; font-weight:bold;">
                                Estoque de destino (Setor):  
                                <span style="font-weight:normal">
                                    '.$row['LcEstNome'].'
                                </span>
                            </p>
                       </div>
                   </div>
                   <div style="position: relative; right: 30px; width:13%; margin-top: -73px; float: right; display: inline-block;">
                       <div style="width: 100%; height: 20px; background-color: #61da66; padding: 10px 7px 0px 11px; box-sizing: content-box;">
                            <span style="color: #fff; font-size: 0.9rem">Nº 0001/19</span>
                       </div>
                   </div>
	            </div>
	          <br>';
        } else {

            $sql = "SELECT MovimDestinoManual, LcEstNome
	                FROM Movimentacao
	                JOIN LocalEstoque on LcEstId = MovimOrigem
	                WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ".$_POST['inputMovimentacaoId']."
	        ";
            $result = $conn->query($sql);
            $row2 = $result->fetch(PDO::FETCH_ASSOC);
   
    	    $html .= '<br>
                <div style="border-bottom: 1px solid #333; margin-bottom: -32px; margin-top: -50px;">
                   <div style="width: 90%; display: inline-block;">
                        <p style="font-size: 0.9rem ;margin-bottom: 5px; margin-top: 20px; font-weight:bold;">
                            Data:
                            <span style="font-weight:normal"> 
                                '.mostraData($rowMv['MovimData']).'
                            </span>
                        </p>
                       <p style="font-size: 0.9rem ;margin-bottom: 5px; margin-top: 8px; font-weight:bold;">
                            Tipo de Transferência:
                            <span style="font-weight:normal">
                              '.$rowMv['FinalNome'].'
                            </span>
                        </p>
                       <div style="width: 100%;">
                            <p style="font-size: 0.9rem ;margin-top: 0px;width: 250px ;float: left ; font-weight:bold;">
                                Estoque de origem:
                                <span style="font-weight:normal">
                                    '.$rowMv['LcEstNome'].'
                                </span>
                            </p>
                           <p style=" font-size: 0.9rem; width: 350px;margin-top: 0px;float: right ; font-weight:bold;">
                                Estoque de destino (Setor):  
                                <span style="font-weight:normal">
                                    '.$row['LcEstNome'].'
                                </span>
                            </p>
                       </div>
                   </div>
                   <div style="position: relative; right: 30px; width:13%; margin-top: -73px; float: right; display: inline-block;">
                       <div style="width: 100%; height: 20px; background-color: #61da66; padding: 10px 7px 0px 11px; box-sizing: content-box;">
                            <span style="color: #fff; font-size: 0.9rem">Nº 0001/19</span>
                       </div>
                   </div>
	            </div>
	          <br>';
        }

	        $html .= '
				<br>
				<table style="width:100%; border-collapse: collapse; border-bottom: 1px solid #333; margin-bottom: 950px" >
					<tr>
						<th style="font-size: 0.7rem; text-align: left; padding: 10px 10px 10px 0; solid #333; width:10%; border-bottom: 1px solid #333;">Código</th>
						<th style="font-size: 0.7rem; text-align: left; padding: 10px; border-bottom: 1px solid #333; width:15%; padding-left: 5px;">Categoria</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:25%">Produto</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Lote</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Classificação</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Validade</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Marca</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Modelo</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Série</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Unidade</th>
						<th style="font-size: 0.7rem; text-align: center; padding: 10px; border-bottom: 1px solid #333; width:15%">Quantidade</th>
					</tr>
				';	
	        foreach ($rowMvPr as $value) {

		       $html .= "
					<tr>
						<td style='padding-top: 8px; padding-bottom: 8px; font-size: 11px;'>".$value['ProduCodigo']."</td>
						<td style='padding-top: 8px; padding-bottom: 12px; font-size: 11px;'>".$value['CategNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['ProduNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['MvXPrLote']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['ClassNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".mostraData($value['MvXPrValidade'])."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['MarcaNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['ModelNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['ProduNumSerie']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['UnMedNome']."</td>
						<td style='padding-top: 8px; text-align: center; padding-bottom: 12px; font-size: 11px;'>".$value['MvXPrQuantidade']."</td>
					</tr>
					";
			
	    }
	    $html .= "</table>";
		$html .= "<div style='position: relative; right: 100px; width: 100%; display: flex; flex-direction: row; bottom: 200px;'>
                      <div style='float: left; margin-left: 75px; width: 165px; height: 100px;'>
                          <div style='width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);'>
                              
                          </div>
                          <div style=''>
                              <p style='font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;'>Solicitante</p>
                              <p style='color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;'>Assinatura (funcionário)</p>
                          </div>
                      </div>
                      <div style='float: right; margin-right: 75px; width: 165px; height: 100px;'>
                          <div style='width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);'>
                              
                          </div>
                          <div style=''>
                              <p style='font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;'>Solicitado</p>
                              <p style='color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;'>Assinatura (resp. pelo setor)</p>
                          </div>
                      </div>
                      <div style='width: 100%; height: 100px;'>
                          <p style='padding: 0 30px 0 30px;font-size: 0.7rem;'>Observação: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                          </p>
                      </div>
		          </div>";

		    $rodape = "<hr/>
                            <div style='width:100%'>
		                       <div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		                       <div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	                        </div>";

	        $html .= "</div>";
    
        //$mpdf->SetHTMLHeader($topo);	
	
            // $mpdf->SetHTMLHeader($topo,'O',true);
        $mpdf->SetHTMLFooter($rodape);
        $mpdf->WriteHTML($html);
    
        // Other code
        $mpdf->Output();
	}
			
    
    
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
