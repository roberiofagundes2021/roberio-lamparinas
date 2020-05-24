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
            JOIN Unidade on UnidaId = MovimUnidade
            JOIN Parametro on ParamEmpresa = UnidaEmpresa
            WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = ". $iMovimentacao;
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT MvXPrProduto, MvXPrQuantidade, MvXPrLote, isnull(MvXPrValidade, '') as Validade, ClassNome, ClassChave, ProduNome, ProduMarca, 
            ProduModelo, ProduCodigo, ProduUnidadeMedida, ProduModelo, CategNome, UnMedSigla, ModelNome, MarcaNome
	        FROM Movimentacao
	        JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
	        LEFT JOIN Produto on ProduId = MvXPrProduto
	        LEFT JOIN Categoria on CategId = ProduCategoria
	        LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	        LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
	        LEFT JOIN Modelo on ModelId = ProduModelo
            LEFT JOIN Marca on MarcaId = ProduMarca
	        WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao. " and ClassChave <> 'PERMANENTE' ";
    $result = $conn->query($sql);
    $rowMvPrNaoPatrimoniado = $result->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT MvXPrProduto, MvXPrQuantidade, MvXPrLote, isnull(MvXPrValidade, '') as Validade, ClassNome, ClassChave, ProduNome, ProduMarca, 
            ProduModelo, ProduCodigo, ProduUnidadeMedida, ProduModelo, CategNome, UnMedSigla, ModelNome, MarcaNome, PatriNumero
	        FROM Movimentacao
	        JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
	        LEFT JOIN Produto on ProduId = MvXPrProduto
	        LEFT JOIN Categoria on CategId = ProduCategoria
	        LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	        LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
	        LEFT JOIN Modelo on ModelId = ProduModelo
            LEFT JOIN Marca on MarcaId = ProduMarca
            JOIN Patrimonio on PatriId = MvXPrPatrimonio
	        WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao. " and ClassChave = 'PERMANENTE' ";
    $result = $conn->query($sql);
    $rowMvPrPatrimoniado = $result->fetchAll(PDO::FETCH_ASSOC);    

    if ($row['MovimTipo'] == 'S'){
        
        $sql = "SELECT LcEstNome as Origem, SetorNome as Destino
                FROM Movimentacao
                JOIN LocalEstoque on LcEstId = MovimOrigemLocal
                JOIN Setor on SetorId = MovimDestinoSetor
                WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao;

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
                WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao;
    }
    $result = $conn->query($sql);
    $rowMv = $result->fetch(PDO::FETCH_ASSOC);

    $Origem = $rowMv['Origem'];
    $Destino = $rowMv['Destino'];
    //$Motivo = $rowMv['MotivChave']; 
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
            <div style='position: absolute; font-size:12px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
            </div>
        <div style='width:250px; float:right; display: inline; text-align:right;'>
            <div>".date('d/m/Y')."</div>
            <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada</div>
        </div> 
    </div>

    <div style='text-align:center; margin-top: 20px;'><h1>RECIBO DE RETIRADA</h1></div>
    ";

    //Listando os Bens Não Patrimoniados
    include("movimentacaoImprimeRetiradaNaoPatrimoniado.php");

    //Listando os Bens Patrimoniados
    include("movimentacaoImprimeRetiradaPatrimoniado.php");

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

        $mpdf->Output();

} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch

    // Process the exception, log, print etc.
    $html = $e->getMessage();
	
    $mpdf->WriteHTML($html);
    
    // Other code
    $mpdf->Output();	
}
