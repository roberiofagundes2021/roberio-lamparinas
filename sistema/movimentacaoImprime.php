<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';
require_once 'global_assets/php/funcoesgerais.php';

if (isset($_POST['inputMovimentacaoId'])) {

    $iMovimentacao = $_POST['inputMovimentacaoId'];

    $sql = "SELECT MovimTipo, MovimData, MovimObservacao, ParamValorObsImpreRetirada
            FROM Movimentacao
            JOIN Parametro on ParamEmpresa = MovimEmpresa
            WHERE MovimEmpresa = " . $_SESSION['EmpreId'] . " and MovimId = ". $iMovimentacao;
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT MvXPrProduto, MvXPrQuantidade, MvXPrLote, isnull(MvXPrValidade, '') as Validade, ClassNome, ClassChave, ProduNome, ProduMarca, 
            ProduModelo, ProduCodigo, ProduUnidadeMedida, ProduModelo, CategNome, UnMedSigla, ModelNome, MarcaNome
	        FROM Movimentacao
	        JOIN MovimentacaoXProduto on MvXPrMovimentacao = " . $_POST['inputMovimentacaoId'] . " 
	        LEFT JOIN Produto on ProduId = MvXPrProduto
	        LEFT JOIN Categoria on CategId = ProduCategoria
	        LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	        LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
	        LEFT JOIN Modelo on ModelId = ProduModelo
	        LEFT JOIN Marca on MarcaId = ProduMarca
	        WHERE MovimEmpresa = " . $_SESSION['EmpreId'] . " and MovimId = " . $iMovimentacao;
    $result = $conn->query($sql);
    $rowMvPr = $result->fetchAll(PDO::FETCH_ASSOC);

    if ($row['MovimTipo'] == 'S'){
        
        $sql = "SELECT LcEstNome as Origem, SetorNome as Destino
                FROM Movimentacao
                JOIN LocalEstoque on LcEstId = MovimOrigemLocal
                JOIN Setor on SetorId = MovimDestinoSetor
                WHERE MovimEmpresa = " . $_SESSION['EmpreId'] . " and MovimId = " . $iMovimentacao;

    } else if ($row['MovimTipo'] == 'T'){
        
        $sql = "SELECT CASE
                         WHEN MovimOrigemLocal IS NULL THEN MovimOrigemSetor
                         ELSE MovimOrigemLocal
                       END AS Origem, 
                       CASE
                         WHEN MovimDestinoLocal IS NULL THEN isnull(MovimDestinoSetor, MovimDestinoManual)
                         ELSE MovimDestinoLocal
                       END AS Destino, 
                       MotivChave
                FROM Movimentacao
                LEFT JOIN LocalEstoque LcO on LcO.LcEstId = MovimOrigemLocal
                LEFT JOIN LocalEstoque LcD on LcD.LcEstId = MovimDestinoLocal                
                LEFT JOIN Setor StO on StO.SetorId = MovimOrigemSetor
                LEFT JOIN Setor StD on StD.SetorId = MovimDestinoSetor
                JOIN Motivo on MotivId = MovimMotivo
                WHERE MovimEmpresa = " . $_SESSION['EmpreId'] . " and MovimId = " . $iMovimentacao;
    }
    $result = $conn->query($sql);
    $rowMv = $result->fetch(PDO::FETCH_ASSOC);

    $Origem = $rowMv['Origem'];
    $Destino = $rowMv['Destino'];
    $Motivo = $rowMv['MotivChave']; 
}

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

    // Evita erro ao recarregar pagina do relatório
    if (!isset($rowMv)) {
        return;
    }

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


            <div style='position: relative; width:100%; border-bottom: 1px solid #666;'>
               <div style='float:left; width: 400px; display: inline-block;'>
                   <img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />     
                   <span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
                   <div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
                   </div>
                <div style='width:250px; float:right; display: inline; text-align:right;'>
                    <div>".date('d/m/Y')."</div>
                    <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada</div>
                </div> 
            </div>

            <div style='text-align:center; margin-top: 20px;'><h1>RECIBO DE RETIRADA</h1></div>
            ";

    // Caso seja uma movimentação de saída
    if ($row['MovimTipo'] == 'S') {

        $numPaginas = count($rowMvPr) / 3;
        $cont = 0;
        $produtos = array_chunk($rowMvPr, 3);

        foreach ($produtos as $produtos3) {
            
            $cont += 1;

          //  $html .= "<div style='height: 800px ;position: relative; border: 1px solid #ccc; box-sizing: border-box; padding: 20px'>";


            //"Bens não patrimoniados" tem quantidade e não tem patrimônio, já os "Bens patrimoniados" não tem quantidade e tem patrimônio

			$html .= '<br>
                        <table style="width:100%;">
                            <tr>
                                <td style="width:25%">Data: ' . mostraData($row['MovimData']) . '</td>
                                <td style="width:25%; text-align: center; background-color: #d8d8d8;">Nº: 0001/19</td>
                                <td colspan="2" style="width:50%; border: none;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="width:50%">Origem: '. $Origem .'</td>
                                <td colspan="2" style="width:50%">Destino: '.$Destino.'</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="border: none;"></td>
                            </tr>

                            <tr>
                                <td colspan="4" style="background-color: #d8d8d8; text-align: center; font-weight: bold;">Identificação dos Bens Não Patrimoniados</td>
                            </tr>
                        </table>
				        <br>
                        ';

            foreach ($produtos3 as $value) {

                    $html .= '
                            <table style="width:100%;">
                                <tr>
                                    <td colspan="1">Código: '.$value['ProduCodigo'].'</td>
                                    <td colspan="3">Produto: '.$value['ProduNome'].'</td>
                                    <td colspan="2">Categoria: '.$value['CategNome'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Marca: '. $Origem .'</td>
                                    <td colspan="2">Modelo: '.$Destino.'</td>
                                    <td colspan="1">Unidade: '.$value['UnMedSigla'].'</td>                                    
                                </tr>
                                <tr>
                            ';

                    if ($value['ClassChave'] == 'PERMANENTE') {                            
                        $html .= '  <td colspan="2">Classificação: '.$value['ClassNome'].'</td>
                                    <td colspan="1">Patrimônio: 231.460-2</td>';
                    } else {
                        $html .= '  <td colspan="3">Classificação: '.$value['ClassNome'].'</td>';
                    }

                    $html .= '
                                    <td colspan="1">Lote: '.$value['CategNome'].'</td>
                                    <td colspan="1">Validade: '.mostraData($value['Validade']).'</td>
                                    <td colspan="1">Quantidade: '.$value['MvXPrQuantidade'].'</td>                                
                                </tr>
                            </table>
                            ';
            }

            if ($row['ParamValorObsImpreRetirada'] == 1 && $cont == ceil($numPaginas)) {
                $html .= ' 
                        <div style="margin-top: 7px ;">
                            <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                                <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Observação</p>
                            </div>
                        </div>
                        <div style="margin-top: 2px ;">
                            <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4">
                                <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: justify">
                                   ' . $row['MovimObservacao'] . '
                                </p>
                            </div>
                        </div>
                    ';
            }

            $html .= '<div style="margin-top: 8px ;">
                            <div style="margin-right: 2px ;float: left; width: 49%; border: 1px solid #c9d0d4">
                                <div style="float: left; margin-left: 29px; margin-top: 29px; margin-bottom: -20px; width: 260px; height: 100px;">
                                    <div style="width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);">
                                  
                                    </div>
                                    <div style="">
                                        <p style="font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;">Solicitante</p>
                                        <p style="color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;">Assinatura (funcionário)</p>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-left: 2px ;float: left; width: 50%; border: 1px solid #c9d0d4">
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

         //   $html .= "</div>";

			$rodape = "<hr/>
			<div style='width:100%'>
				<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
				<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
			</div>";			
			

            //$mpdf->SetHTMLHeader($topo);
            //$stylesheet = file_get_contents('global_assets/css/lamparinas/bootstrap-3.3.7/dist/css/bootstrap.min.css');
            //$stylesheet = file_get_contents('global_assets/css/lamparinas/impressao.css');         
            //$mpdf->WriteHTML($stylesheet, 1); // CSS Script goes here.
			$mpdf->SetHTMLFooter($rodape);
            $mpdf->WriteHTML($html);            
            // $mpdf->SetHTMLHeader($topo,'O',true);	

            // Other code
        }


        $mpdf->Output();

        //*************************************** Caso seja uma movimentação de Transferência ***********************************\\
    } else if ($row['MovimTipo'] == 'T') {

        $numPaginas = count($rowMvPr) / 3;
        $cont = 0;
        $produtos = array_chunk($rowMvPr, 3);

        foreach ($produtos as $produtos3) {
            $cont += 1;

            $html = "";

            $html .= "<div style='height: 950px; border: 1px solid rgb(149, 150, 148); box-sizing: border-box; padding: 20px'>";

            $html .= "
	            <div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		           <div style='float:left; width: 400px; display: inline-block; margin-bottom: 10px;'>
			           <img src='global_assets/images/lamparinas/logo-lamparinas_200x200.jpg' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			           <span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			           <div style='position: absolute; font-size:9px; margin-top: 8px; margin-left:4px;'>Unidade: Secretaria de Saúde / Hospital Padre Manoel</div>
		               </div>
		            <div style='margin-top: -44px;width:300px; float:right; display: inline-block; text-align:right; font-size: 0.8rem; margin-bottom: 10px;'>
			            <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada - Requisição de Material</div>
		            </div> 
	            </div>
			";

            $html .= '<br>
            <div style="display: flex; flex-direction: column ;width: 100%; height 20px">
               <div style="">
                    <div style="margin-right: 12px ;float: left ;width: 18.1%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Data: ' . mostraData($row['MovimData']) . '</p>
                    </div>
                    <div style="margin: 0px 2px 0px 2px ;float: left; width: 13.5%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                        <p style="font-size: 0.8rem; text-align: center ;margin: 0px; padding: 8px">Nº 0001/19</p>
                    </div>
               </div>
               <div style="margin-top: 3px ;">
                    <div style="margin-right: 12px ;float: left ;width: 49.5%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Origem: ' . $Origem . '</p>
                    </div>
                    <div style="margin: 0px 2px 0px 2px ;float: left; width: 49.5%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem ;margin: 0px; padding: 8px">Destino: ' . $Destino . '</p>
                    </div>
               </div>
               <div style="margin-top: 7px ;">
                    <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Identificação dos Bens</p>
                    </div>
               </div>
            </div>
            <br>';

            foreach ($produtos3 as $value) {


                if ($value['ClassNome'] == 'Bem Permanente') {

                    $html .= "
                            <div style='margin-top: -9px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 23.42%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: " . $value['ProduCodigo'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 50%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: " . $value['ProduNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 25%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Categoria: " . $value['CategNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-top: 2px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Marca: " . $value['MarcaNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Modelo: " . $value['ModelNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-bottom: 8px ;margin-top: 2px; margin-bottom 4px; max-height: 200px !important'>
                                <div style='margin-right: 12px ;float: left ;width: 20%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem; margin: 0px; padding: 8px'>Classificação: " . $value['ClassNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 16.4%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Patrimônio: 154224</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Lote: " . $value['MvXPrLote'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Validade: " . mostraData($value['MvXPrValidade']) . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Unidade: " . $value['UnMedNome'] . "</p>
                                </div>
                                <div style='heigth: 5vmin;margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='heigth: 100% ;font-size: 0.6rem ;margin: 0px; padding: 8px'>Quantidade1: " . $value['MvXPrQuantidade'] . "</p>
                                </div>
                            </div>
                                 ";
                } else {
                    $html .= "
                            <div style='margin-top: -9px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 23.42%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: " . $value['ProduCodigo'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 50%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: " . $value['ProduNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 25%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Categoria: " . $value['CategNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-top: 2px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Marca: " . $value['MarcaNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Modelo: " . $value['ModelNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-bottom: 8px ;margin-top: 2px; margin-bottom 4px; background-color: #e9e9e9!important'>
                                <div style='margin-right: 12px ;float: left ;width: 20%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem; margin: 0px; padding: 8px'>Classificação: " . $value['ClassNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 16.4%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Patrimônio: </p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Lote: " . $value['MvXPrLote'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Validade: " . mostraData($value['MvXPrValidade']) . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Unidade: " . $value['UnMedNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Quantidade: " . $value['MvXPrQuantidade'] . "</p>
                                </div>
                            </div>
                                 ";
                }
            }

            if ($row['ParamValorObsImpreRetirada'] == 1 && $cont == ceil($numPaginas)) {
                $html .= ' 
                        <div style="margin-top: 7px ;">
                            <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                                <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Observação</p>
                            </div>
                        </div>
                        <div style="margin-top: 2px ;">
                            <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4">
                                <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: justify">
                                   ' . $row['MovimObservacao'] . '
                                </p>
                            </div>
                        </div>
                    ';
            }

            $html .= '<div style="margin-top: 8px ;">
                            <div style="margin-right: 2px ;float: left; width: 49%; border: 1px solid #c9d0d4">
                                <div style="float: left; margin-left: 29px; margin-top: 29px; margin-bottom: -20px; width: 260px; height: 100px;">
                                    <div style="width= 100%; height: 25px; border-bottom: 1px solid rgb(187, 186, 186);">
                                  
                                    </div>
                                    <div style="">
                                        <p style="font-size: 0.9rem; margin: 7px 0 0 0; text-align: center;">Solicitante</p>
                                        <p style="color: rgb(156, 154, 154); font-size: 0.8rem; margin: 7px 0 0 0; text-align: center;">Assinatura (funcionário)</p>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-left: 2px ;float: left; width: 50%; border: 1px solid #c9d0d4">
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

            $html .= "</div>";


			$rodape = "<hr/>
			<div style='width:100%'>
				<div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
				<div style='width:105px; float:right; display: inline;'>Página {PAGENO} / {nbpg}</div> 
			</div>";			
			/*
            $html .= "
                    <div style='width:100%;'>
                        <hr/>
                        <div style='width:100%'>
                           <div style='width:300px; float:left; display: inline;'>Sistema Lamparinas</div>
                           <div style='width:105px; float:right; display: inline;'>Página " . $cont . " / " . ceil($numPaginas) . "</div> 
                        </div>
                    </div>
                     "; */

            //$mpdf->SetHTMLHeader($topo);
			$mpdf->SetHTMLFooter($rodape);
            $mpdf->WriteHTML($html);
            
            // $mpdf->SetHTMLHeader($topo,'O',true);	

            // Other code
        }


        $mpdf->Output();
    }
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

    // Process the exception, log, print etc.
    $html = $e->getMessage();
	
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();	
}
