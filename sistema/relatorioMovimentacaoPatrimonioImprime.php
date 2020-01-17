<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';

// Aplicado filtro aos resultados
/***************************************************************/
$args = []; 

    if (!empty($_POST['inputDataDe_imp']) || !empty($_POST['inputDataAte_imp'])) {
        empty($_POST['inputDataDe_imp']) ? $inputDataDe = '1900-01-01' : $inputDataDe = $_POST['inputDataDe_imp'];
        empty($_POST['inputDataAte_imp']) ? $inputDataAte = '2100-01-01' : $inputDataAte = $_POST['inputDataAte_imp'];

        //$args[]  = "MovimData = ".$inputDataDe." ";MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."'
        //$args[] = "`dataAte` = ".$inputDataAte." ";

        $args[]  = "MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."' ";
    }

    if(!empty($_POST['inputLocalEstoque_imp'])){
        $args[]  = "MovimDestinoLocal = ".$_POST['inputLocalEstoque_imp']." ";
    }

    if(!empty($_POST['inputSetor_imp'])){
        $args[]  = "MovimDestinoSetor = ".$_POST['inputSetor']." ";
    }

    if(!empty($_POST['inputCategoria_imp'])){
        $args[]  = "ProduCategoria = ".$_POST['inputCategoria_imp']." ";
    }

    if(!empty($_POST['inputSubCategoria_imp'])){
        $args[]  = "ProduSubCategoria = ".$_POST['inputSubCategoria_imp']." ";
    }

    if(!empty($_POST['inputProduto_imp'])){
        $args[]  = "ProduNome LIKE '%".$_POST['inputProduto_imp']."%' ";
    }


    if (count($args) >= 1) {
        try {

			$string = implode( " and ",$args );
			
			$string != '' ? $string .= ' and ' : $string;

            $sql = "SELECT MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, LcEstNome, MovimDestinoSetor, MvXPrValidade, MvXPrValorUnitario, ProduNome, SetorNome
                    FROM Movimentacao
                    JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
                    JOIN Produto on ProduId = MvXPrProduto
                    JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    WHERE ".$string." ProduEmpresa = ".$_SESSION['EmpreId']."
                    ";
            $result = $conn->query("$sql");
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
/***************************************************************/



if (isset($_POST['inputLocalEstoque_imp'])) {
	try {
		$sql = "SELECT LcEstNome
		        FROM LocalEstoque
		        WHERE LcEstId = " . $_POST['inputLocalEstoque_imp'] . " and LcEstEmpresa = " . $_SESSION['EmpreId'] . "
	            ";
		$result = $conn->query($sql);
		$LocalEstoque = $result->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

if (isset($_POST['inputSetor_imp'])) {
	try {
		$sql = "SELECT SetorNome
		        FROM Setor
		        WHERE SetorId = " . $_POST['inputSetor_imp'] . " and SetorEmpresa = " . $_SESSION['EmpreId'] . "
	            ";
		$result = $conn->query($sql);
		$Setor = $result->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

if (isset($_POST['inputCategoria_imp'])) {
	try {
		$sql = "SELECT CategNome
		        FROM Categoria
		        WHERE CategId = " . $_POST['inputCategoria_imp'] . " and CategEmpresa = " . $_SESSION['EmpreId'] . "
	            ";
		$result = $conn->query($sql);
		$Categoria = $result->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}


if (isset($_POST['inputSubCategoria_imp'])) {
	try {
		$sql = "SELECT SbCatNome
		        FROM SubCategoria
		        WHERE SbCatId = " . $_POST['inputSubCategoria_imp'] . " and SbCatEmpresa = " . $_SESSION['EmpreId'] . "
	            ";
		$result = $conn->query($sql);
		$SubCategoria = $result->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

if (isset($_POST['resultados'])) {
	try {
		$mpdf = new Mpdf([
			'mode' => 'utf-8',
			//'format' => [190, 236], 
			'format' => 'A4-P', //A4-L
			'default_font_size' => 9,
			'default_font' => 'dejavusans',
			'orientation' => 'P' //P->Portrait (retrato)    L->Landscape (paisagem)
		]);

		$topo = "
		        <div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		            <div style='float:left; width: 400px; display: inline-block; margin-bottom: 10px;'>
			            <img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			            <span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			            <div style='position: absolute; font-size:9px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
					</div>
					<div style='width:250px; float:right; display: inline; text-align:right;'>
			            <div style='font-size: 0.8rem'>Data {DATE j/m/Y}</div>
			            <div style='margin-top:8px;'></div>
		            </div>
		            <div style='margin-top: -44px;width:300px; float:right; display: inline-block; text-align:right; font-size: 0.8rem; margin-bottom: 10px;'>
			            <div style='margin-top:8px; font-weight:bold;'>Relatório Movimentação do Patrimônio</div>
		            </div> 
	            </div>
	    ";

		$html = '';

		$html .= '<br>';
		$html .= '<br>';

		if(!empty($_POST['inputDataDe_imp']) || !empty($_POST['inputDataAte_imp'])){
			if(!empty($_POST['inputDataDe_imp']) && !empty($_POST['inputDataAte_imp'])){
				$html .= '
            		<div style="font-weight: bold; font-size: 0.8rem; position:relative; margin-top: 10px; padding: 5px;">
			            Período: De '.mostraData($_POST['inputDataDe_imp']).' à '.mostraData($_POST['inputDataAte_imp']).' 
		            </div>
		        ';
			} else if(!empty($_POST['inputDataDe_imp'])){
				$html .= '
            		<div style="font-weight: bold; font-size: 0.8rem; position:relative; margin-top: 20px; padding: 5px;">
			            Período: De '.mostraData($_POST['inputDataDe_imp']).' à '.date('d/m/Y').' 
		            </div>
		        ';
			} else {
				$html .= '
            		<div style="font-weight: bold; font-size: 0.8rem; position:relative; margin-top: 20px; padding: 5px;">
			            Período: Até '.date('d/m/Y').' 
		            </div>
		        ';
			}
		}

		if(!empty($_POST['inputCategoria_imp'])){
			$html .= '
					<div style="font-weight: bold; font-size: 0.8rem; padding: 5px;">
			            Categoria: '.$Categoria['CategNome'].'
		            </div>
		        ';
		}
		if(!empty($_POST['inputSubCategoria_imp'])){
			$html .= '
            		<div style="font-weight: bold; font-size: 0.8rem; position:relative; padding: 5px;">
			            SubCategoria: '.$SubCategoria['SbCatNome'].' 
		            </div>
		        ';
		}


		$html .= '
		<hr>
		<div></div>
		<br>
		<table style="width:100%; border-collapse: collapse; margin-top: -24px">
			<tr>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:4%; font-size: 0.6rem">Item</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:17%; font-size: 0.6rem">Descrição</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%; font-size: 0.6rem">Patrimônio</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%; font-size: 0.6rem">Nota Fiscal</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:11%; font-size: 0.6rem">R$ Aquisição</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%; font-size: 0.6rem">R$ Depreciação</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:10%; font-size: 0.6rem">Validade</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%; font-size: 0.6rem">Local/Origem</th>
				<th style="text-align: left; border-bottom: 1px solid #333; padding-top: 7px; padding-bottom: 7px; width:12%; font-size: 0.6rem">Setor/Destino</th>
			</tr>
		';

		$html .= "<tbody>";
		
		$cont = 0;
		foreach($rowData as $produto){
			$cont += 1;
			$html .= "
			<tr>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $cont . "</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $produto['ProduNome'] . "</td>
				<td style='font-size: 0.6rem; padding-top: 7px'></td>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $produto['MovimNotaFiscal'] . "</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>".$produto['MvXPrValorUnitario']."</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $produto['MovimNotaFiscal'] . "</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>".mostraData($produto['MvXPrValidade'])."</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $produto['LcEstNome'] . "</td>
				<td style='font-size: 0.6rem; padding-top: 7px'>" . $produto['SetorNome'] . "</td>
			</tr>
		 ";
		}
		$html .= "</tbody>";

		$html .= "</table>";


		$rodape = "<hr/>
    <div style='width:100%'>
		<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
		<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
	</div>";

		$mpdf->SetHTMLHeader($topo, 'O', true);
		$mpdf->WriteHTML($html);
		$mpdf->SetHTMLFooter($rodape);

		// Other code
		$mpdf->Output();
	} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

		// Process the exception, log, print etc.
		echo $e->getMessage();
	}
}
