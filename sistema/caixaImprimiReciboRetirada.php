<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

use Mpdf\Mpdf;

require_once 'global_assets/php/vendor/autoload.php';
require_once 'global_assets/php/funcoesgerais.php';

$arrayPagamento = explode('-', $_POST['cmbFormaPagamentoRetirada']);
$pagamentoId = $arrayPagamento[0];

/*
if (isset($_POST['inputMovimentacaoId'])) {

    $iMovimentacao = $_POST['inputMovimentacaoId'];

    $sql = "SELECT MovimTipo, MovimData, MovimNumRecibo, MovimObservacao, ParamValorObsImpreRetirada, MotivNome
            FROM Movimentacao
            JOIN Unidade on UnidaId = MovimUnidade
            JOIN Parametro on ParamEmpresa = UnidaEmpresa
            LEFT JOIN Motivo on MotivId = MovimMotivo
            WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = ". $iMovimentacao;
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    // Após concluido a tela de Movimentação tem que avaliar se precisa desse Distinct,
    // porque não deve ser criado vários registros na tabela MovimentacaoXProduto pra esse caso
    // de produtos não patrimoniados.
    $sql = "SELECT Distinct MvXPrProduto as ProduServi, MvXPrQuantidade as Quantidade, MvXPrLote, isnull(cast(cast(MvXPrValidade as date)as varchar),'') as Validade, ClassNome, ClassChave, ProduNome as Nome, 
            ProduCodigo as Codigo, ProduUnidadeMedida, CategNome as Categoria, UnMedSigla, ModelNome as NomeModelo, MarcaNome as Marca, Tipo = 'P'
	        FROM Movimentacao
	        JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
	        JOIN Produto on ProduId = MvXPrProduto
	        JOIN Categoria on CategId = ProduCategoria
	        LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	        LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
            LEFT JOIN OrdemCompra on OrComId = MovimOrdemCompra
            LEFT JOIN FluxoOperacional on FlOpeId = OrComFluxoOperacional
            LEFT JOIN ProdutoXFabricante on PrXFaFluxoOperacional = FlOpeId
            LEFT JOIN Marca on MarcaId = PrXFaMarca
	        LEFT JOIN Modelo on ModelId = PrXFaModelo
	        WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao. " and ClassChave <> 'PERMANENTE' 
            UNION
            SELECT Distinct MvXSrServico as ProduServi, MvXSrQuantidade as Quantidade, '' , '' , '', '' , ServiNome as Nome, ServiCodigo as Codigo,
            '' , CategNome as Categoria, '', ModelNome as NomeModelo, MarcaNome as Marca, Tipo = 'S'
            FROM Movimentacao
            JOIN MovimentacaoXServico on MvXSrMovimentacao = MovimId
            JOIN Servico on ServiId = MvXSrServico
            JOIN Categoria on CategId = ServiCategoria
            LEFT JOIN OrdemCompra on OrComId = MovimOrdemCompra
            LEFT JOIN FluxoOperacional on FlOpeId = OrComFluxoOperacional
            LEFT JOIN ServicoXFabricante on SrXFaFluxoOperacional = FlOpeId
            LEFT JOIN Marca on MarcaId = SrXFaMarca
	        LEFT JOIN Modelo on ModelId = SrXFaModelo
            WHERE MovimUnidade = " . $_SESSION['UnidadeId'] . " and MovimId = " . $iMovimentacao. "
            ";
            $result = $conn->query($sql);
            $rowMvPrNaoPatrimoniado = $result->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT MvXPrProduto, MvXPrQuantidade, MvXPrLote, isnull(cast(cast(MvXPrValidade as date)as varchar),'') as Validade, ClassNome, ClassChave, ProduNome, ProduMarca, 
            ProduModelo, ProduCodigo, ProduUnidadeMedida, ProduModelo, CategNome, UnMedSigla, ModelNome, MarcaNome, PatriNumero
	        FROM Movimentacao
	        JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
	        JOIN Produto on ProduId = MvXPrProduto
	        JOIN Categoria on CategId = ProduCategoria
	        LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
	        JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
	        LEFT JOIN Modelo on ModelId = ProduModelo
            LEFT JOIN Marca on MarcaId = ProduMarca
            --JOIN MovimentacaoXProdutoXPatrimonio on MXPXPMovimentacaoXProduto = MvXPrId
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
                         WHEN MovimOrigemLocal IS NULL THEN StO.SetorNome
                         ELSE LcO.LcEstNome
                       END AS Origem, 
                       CASE
                         WHEN MovimDestinoLocal IS NULL THEN isnull(StD.SetorNome, MovimDestinoManual)
                         ELSE LcD.LcEstNome
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
*/

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
    //if (!isset($rowMv)) {
    //    return;
    //}

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
            <img src='global_assets/images/lamparinas/logo-lamparinas.png' style='width:190px; height:45px; float:left; margin-right: 10px; margin-top:-10px;' />  
        </div>
        
        <div style='width:250px; float:right; display: inline; text-align:right;'>
            <div>".date('d/m/Y')."</div>
            <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada</div>
        </div> 
    </div>

    <div style='text-align:center; margin-top: 20px;'><h1>$_POST[inputValorRetirada]</h1></div>
    <div style='text-align:center; margin-top: 20px;'><h1>$pagamentoId</h1></div>
    <div style='text-align:center; margin-top: 20px;'><h1>$_POST[inputJustificativa]</h1></div>
    ";

    //Listando os Bens Não Patrimoniados
    //include("movimentacaoImprimeRetiradaNaoPatrimoniado.php");

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
