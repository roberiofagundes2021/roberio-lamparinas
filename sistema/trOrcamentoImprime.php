<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

if (isset($_POST['inputOrcamentoId'])) {
    $iOrcamento = $_POST['inputOrcamentoId'];
} else {
    print('<script>
				window.close();
		   </script> ');
}

try {

    $sql = "SELECT *
			FROM TRXOrcamento
            JOIN TermoReferencia on TrRefId = TrXOrTermoReferencia
			LEFT JOIN Categoria on CategId = TrXOrCategoria
            LEFT JOIN SubCategoria on SbCatId = TrXOrSubCategoria
			WHERE TrXOrUnidade = " . $_SESSION['UnidadeId'] . " and TrXOrId = " . $iOrcamento;
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);


    $sql = "SELECT *
    FROM TRXOrcamentoXSubCategoria
    JOIN SubCategoria on SbCatId = TXOXSCSubcategoria
    WHERE TXOXSCUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSCOrcamento = " . $iOrcamento;
    $result = $conn->query($sql);
    $rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

    // Selects para identificar a a tabela de origem dos produtos da TR.
    $sql = "SELECT DISTINCT COUNT(TXOXPProduto) as CONT
    FROM TRXOrcamentoXProduto
    JOIN ProdutoOrcamento on PrOrcId = TXOXPProduto
    WHERE PrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento . "";
    $result = $conn->query($sql);
    $rowProdutoUtilizado1 = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT COUNT(TXOXPProduto) as CONT
    FROM TRXOrcamentoXProduto
    JOIN Produto on ProduId = TXOXPProduto
    WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento . "";
    $result = $conn->query($sql);
    $rowProdutoUtilizado2 = $result->fetch(PDO::FETCH_ASSOC);

    // var_dump($rowProdutoUtilizado1);
    // print('                      ');
    // var_dump($rowProdutoUtilizado2);
    // die;

    // Selects para identificar a a tabela de origem dos serviços da TR.
    $sql = "SELECT COUNT(TXOXSServico) as CONT
    FROM TRXOrcamentoXServico
    JOIN ServicoOrcamento on SrOrcId = TXOXSServico
    WHERE SrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento . " ";
    $result = $conn->query($sql);
    $rowServicoUtilizado1 = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT COUNT(TXOXSServico) as CONT
    FROM TRXOrcamentoXServico
    JOIN Servico on ServiId = TXOXSServico
    WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento . "";
    $result = $conn->query($sql);
    $rowServicoUtilizado2 = $result->fetch(PDO::FETCH_ASSOC);


    $totalProdutos = 0;
    $totalServicos = 0;
    $totalGeralProdutos = 0;
    $totalGeralServicos = 0;
    $totalGeral = 0;

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
        'orientation' => 'P'
    ]);  // L - landscape, P - portrait

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
				<span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
				<div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: " . $_SESSION['UnidadeNome'] . "</div>
			</div>
			<div style='width:220px; float:right; display: inline; text-align:right;'>
				<div>" . date('d/m/Y') . "</div>
				<div style='margin-top:8px;'>Orçamento: " . formatarNumero($row['TrXOrNumero']) . "</div>
			</div> 
			</div>

			<div style='text-align:center; margin-top: 20px;'><h1>ORÇAMENTO DO TERMO DE REFERÊNCIA</h1></div>
    ";
    
    $html .= '
	    <h3>TERMO DE REFERÊNCIA</h3>
	';
	
	$html .= '
    <table style="width:100%; border-collapse: collapse;">
        <tr style="background-color:#F1F1F1;">
            <td style="width:25%; font-size:12px;">Nº TR: '. $row['TrRefNumero'].'</td>
            <td style="width:15%; font-size:12px;">Data: '. mostraData($row['TrRefData']).'</td>
        </tr>
    </table>
	<br>';


    $html .= '
	<div>' . $row['TrRefConteudoInicio'] . '</div>
	<br>';

    if ($rowProdutoUtilizado1['CONT'] > 0 || $rowProdutoUtilizado2['CONT'] > 0) {

        $html .= "<div style='text-align:center;'><h2>PRODUTOS</h2></div>";

        $html .= '
		<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#ddd; padding: 8px; border: 1px solid #ccc;">
			Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> 
		</div>
		<br>
		';

        $cont = 1;

        foreach ($rowSubCategoria as $sbcat) {

            //Se foi utilizado ProdutoOrcamento
            if ($row['TrXOrTabelaProduto'] == 'ProdutoOrcamento') {
                $sql = "SELECT PrOrcId as Id, PrOrcNome as Nome, PrOrcCategoria as Categoria, PrOrcSubCategoria as SubCategoria,
						PrOrcDetalhamento as Detalhamento, UnMedSigla, TXOXPQuantidade, TXOXPValorUnitario
						FROM ProdutoOrcamento
						JOIN TRXOrcamentoXProduto on TXOXPProduto = PrOrcId
                        JOIN TRXOrcamento on TrXOrId = TXOXPOrcamento
                        JOIN TRXOrcamentoXSubcategoria on TXOXSCOrcamento = TXOXPOrcamento
						JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
                        WHERE PrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento;
            } else {
                $sql = "SELECT DISTINCT ProduId as Id, ProduNome as Nome, ProduCategoria as Categoria, ProduSubCategoria as SubCategoria, 
						ProduDetalhamento as Detalhamento, UnMedSigla, TXOXPQuantidade, TXOXPValorUnitario
						FROM Produto
						JOIN TRXOrcamentoXProduto on TXOXPProduto = ProduId
                        JOIN TRXOrcamento on TrXOrId = TXOXPOrcamento
                        JOIN TRXOrcamentoXSubcategoria on TXOXSCOrcamento = TXOXPOrcamento
						JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
						WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento;
            }
            $result = $conn->query($sql);
            $rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

            if (isset($rowProdutos)) {

                $html .= '
				<div style="font-weight: bold; position:relative; margin-top: 15px; background-color:#eee; padding: 8px; border: 1px solid #ccc;">
					SubCategoria: <span style="font-weight:normal;">' . $sbcat['SbCatNome'] . '</span>
				</div>
				<br> ';

                $html .= '	
				<table style="width:100%; border-collapse: collapse;">
					<tr>
						<th style="text-align: center; width:8%">Item</th>
						<th style="text-align: left; width:65%">Produto</th>
						<th style="text-align: center; width:12%">Unidade</th>        	
                        <th style="text-align: center; width:15%">Quantidade</th>
                        <th style="text-align: center; width:15%">V. Unitário</th>
                        <th style="text-align: center; width:15%">V. Total</th>
					</tr>
				';

                foreach ($rowProdutos as $itemProduto) {

                    if ($sbcat['TXOXSCSubcategoria'] == $itemProduto['SubCategoria']) {

                        if ($itemProduto['TXOXPValorUnitario'] != '' and $itemProduto['TXOXPValorUnitario'] != null){
                            $valorUnitario = $itemProduto['TXOXPValorUnitario'];
                            $valorTotal = $itemProduto['TXOXPQuantidade'] * $itemProduto['TXOXPValorUnitario'];
                        } else {
                            $valorUnitario = 0;
                            $valorTotal = 0;
                        }

                        $html .= "
                            
                            <tr>
					            <td style='text-align: center;'>" . $cont . "</td>
					            <td style='text-align: left;'>" . $itemProduto['Nome'] . "</td>
					            <td style='text-align: center;'>" . $itemProduto['UnMedSigla'] . "</td>					
					            <td style='text-align: center;'>" . $itemProduto['TXOXPQuantidade'] . "</td>
					            <td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					            <td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
				            </tr>
                		";

                        $cont++;
                        $totalProdutos += $itemProduto['TXOXPQuantidade'] * $itemProduto['TXOXPValorUnitario'];
                    }
                }

                $totalGeralProdutos += $totalProdutos;

                $html .= "<br>";

                $html .= "  <tr>
                				<td colspan='5' height='50' valign='middle'>
                					<strong>Total Produtos</strong>
                				</td>
                				<td style='text-align: right' colspan='1'>
                					" . mostraValor($totalProdutos) . "
                				</td>
                			</tr>";
                $html .= "</table>";
            }
        }
    }

    if ($rowServicoUtilizado1['CONT'] > 0 || $rowServicoUtilizado2['CONT'] > 0){

    	$html .= "<div style='text-align:center; margin-top: 20px;'><h2>SERVIÇOS</h2></div>";

    	$html .= '
    	<div style="font-weight: bold; position:relative; margin-top: 5px; background-color:#ddd;  padding: 8px;  border: 1px solid #ccc;">
    		Categoria: <span style="font-weight:normal;">' . $row['CategNome'] . '</span> 
    	</div>
    	<br>
    	';

    	$cont = 1;

    	foreach ($rowSubCategoria as $sbcat) {

    		//Se foi utilizado ServicoOrcamento
    		if($row['TrXOrTabelaServico'] == 'ServicoOrcamento'){
    			$sql = "SELECT SrOrcId as Id, SrOrcNome as Nome, SrOrcCategoria as Categoria, SrOrcSubCategoria as SubCategoria, TXOXSQuantidade, TXOXSValorUnitario
    					FROM ServicoOrcamento
    					JOIN TRXOrcamentoXServico on TXOXSServico = SrOrcId
    					WHERE SrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento . " and SrOrcSubCategoria = ".$sbcat['TXOXSCSubcategoria'];
    		} else {
    			$sql = "SELECT ServiId as Id, ServiNome as Nome, ServiCategoria as Categoria, ServiSubCategoria as SubCategoria, TXOXSQuantidade, TXOXSValorUnitario
    					FROM Servico
    					JOIN TRXOrcamentoXServico on TXOXSServico = ServiId
    					WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento . " and ServiSubCategoria = ".$sbcat['TXOXSCSubcategoria'];
    		}

    		$result = $conn->query($sql);
    		$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
            $count = count($rowServicos);		

    		if (isset($rowServicos) and $count){

    			$html .= '
    			<div style="font-weight: bold; position:relative; margin-top: 15px; background-color:#eee; padding: 8px; border: 1px solid #ccc;">
    				SubCategoria: <span style="font-weight:normal;">' . $sbcat['SbCatNome'] . '</span>
    			</div>
    			<br> ';				

    			$html .= '	
    			<table style="width:100%; border-collapse: collapse;">
    				<tr>
    					<th style="text-align: center; width:8%">Item</th>
    					<th style="text-align: left; width:77%">Serviço</th>
    					<th style="text-align: center; width:15%">Quantidade</th>
                        <th style="text-align: center; width:15%">V. Unitário</th>
                        <th style="text-align: center; width:15%">V. Total</th>
    				</tr>
    			';			

    			foreach ($rowServicos as $itemServico) {

    				if ($sbcat['TXOXSCSubcategoria'] == $itemServico['SubCategoria']) {

                        if ($itemServico['TXOXSValorUnitario'] != '' and $itemServico['TXOXSValorUnitario'] != null){
                            $valorUnitario = $itemServico['TXOXSValorUnitario'];
                            $valorTotal = $itemServico['TXOXSQuantidade'] * $itemServico['TXOXSValorUnitario'];
                        } else {
                            $valorUnitario = 0;
                            $valorTotal = 0;
                        }

    					$html .= "
    						<tr>
    							<td style='text-align: center;'>" . $cont . "</td>
    							<td style='text-align: left;'>" . $itemServico['Nome'] . "</td>
                                <td style='text-align: center;'>" . $itemServico['TXOXSQuantidade'] . "</td>
                                <td style='text-align: right;'>" . mostraValor($valorUnitario) . "</td>
					            <td style='text-align: right;'>" . mostraValor($valorTotal) . "</td>
    						</tr>
    					";

    					$cont++;
    					$totalServicos += $itemServico['TXOXSQuantidade'] * $itemServico['TXOXSValorUnitario'];
    				}
    			}

    			$totalGeralServicos += $totalServicos;

    			$html .= "<br>";

    			$html .= "  <tr>
    							<td colspan='4' height='50' valign='middle'>
    								<strong>Total Serviços</strong>
    							</td>
    							<td style='text-align: center' colspan='1'>
    								".mostraValor($totalServicos)."
    							</td>
    						</tr>";
    			$html .= "</table>";
    		} 
    	} 
    }

    $totalGeral = $totalGeralProdutos + $totalGeralServicos;
    //echo $totalGeral;die;

    if ($totalGeral) {
        $html .= "<table style='width:100%; border-collapse: collapse; margin-top: 20px;'>
					<tr>
						<td colspan='3' height='50' valign='middle' style='width:85%'>
							<strong>TOTAL GERAL DE ITENS</strong>
						</td>
						<td style='text-align: center; width:15%'>
							" . mostraValor($totalGeral) . "
						</td>
					</tr>
				</table>
		";
    }

    $html .= '
	<br><br>
	<div>' . $row['TrRefConteudoFim'] . '</div>
	<br>';

    $rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";

    //$mpdf->SetHTMLHeader($topo, '0');
    $mpdf->SetHTMLFooter($rodape);
    $mpdf->WriteHTML($html);

    // Other code
    $mpdf->Output();
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

    // Process the exception, log, print etc.
    echo $e->getMessage();
}
