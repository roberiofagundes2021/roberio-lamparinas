<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$sql = "SELECT OCXPrQuantidade as quantidade, ProduId as id, ProduNome as nome, ProduDetalhamento as detalhamento, ProduValorCusto as valorCusto, ProduCustoFinal as custoFinal, UnMedSigla, tipo = 'P'
		FROM OrdemCompraXProduto
        JOIN Produto on ProduId = OCXPrProduto
 		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
        WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OCXPrOrdemCompra = '" . $_POST['ordemCompra'] . "'
        UNION
        SELECT OCXSrQuantidade as quantidade, ServiId as id, ServiNome as nome, ServiDetalhamento as detalhamento, ServiValorCusto as valorCusto, ServiCustoFinal as custoFinal, UnMedSigla, tipo = 'S'
		FROM OrdemCompraXServico
        JOIN Servico on ServiId = OCXSrServico
 		LEFT JOIN UnidadeMedida on UnMedId = 0
        WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and OCXSrOrdemCompra = '" . $_POST['ordemCompra'] . "'
        ";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//print($sql);
//var_dump($row);
//Verifica se já existe esse registro (se existir, retorna true )
$output = '';
$totalGeral = 0;


$sql = "SELECT MovimId
		FROM Movimentacao
		JOIN Situacao on SituaId = MovimSituacao
	    WHERE MovimOrdemCompra = " . $_POST['numOrdemCompra'] . " and MovimTipo = 'E' and MovimEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'FINALIZADO'
		";
$result = $conn->query($sql);
$movimentAprovads = $result->fetchAll(PDO::FETCH_ASSOC);
$countMovimentAprovads = count($movimentAprovads);


$sql = " SELECT dbo.fnValorTotalOrdemCompra(" . $_SESSION['EmpreId'] . ",  " . $_POST['ordemCompra'] . ") as valorTotalOrdemCompra";
$result = $conn->query($sql);
$totalOrdemCompra = $result->fetch(PDO::FETCH_ASSOC);


if ($countMovimentAprovads) {
 
    if ($count) {
        $numItens = 0;

        $output .= ' <table class="table" id="tabelaProdutoServico">';

        $output .= '<thead>
                    <tr class="bg-slate">
                        <th  style="text-align: center">Item</th>
                        <th>Produto/Serviço</th>
                        <th  style="text-align: center">Unidade Medida</th>
                        <th  style="text-align: center">Quant. Recebida</th>
                        <th  style="text-align: center">Saldo</th>
                        <th  style="text-align: left; width: 10%">Valor Unitário</th>
                        <th  style="text-align: left; width: 13%">Valor Total</th>
                        <th  style="text-align: center">Ações</th>
                    </tr>
                </thead>
        ';
        $output .= '<tbody>';

        foreach (array_reverse($row) as $item) {

            if ($item['tipo'] == 'P') {
                $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $_POST['ordemCompra'] . "'
                   ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);
            } else {

                $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $_POST['ordemCompra'] . "'
                 ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);
            }

            $numItens++;

            $valorCusto = formataMoeda($item['valorCusto']);
            $valorTotal = formataMoeda($item['quantidade'] * $item['valorCusto']);

            $totalGeral += $item['quantidade'] * $item['valorCusto'];
            // var_dump($saldo);
            $output .=  '<tr class="trGrid" id="row' . $numItens . '">
						 <td style="text-align: center">' . $numItens . '</td>
						 <td data-popup="tooltip" data-placement="bottom" title="' . $item['detalhamento'] . '">' . $item['nome'] . '</td>
						 <td style="text-align: center">' . $item['UnMedSigla'] . '</td>
                         <td style="text-align: center">'.$saldo['Quantidade'].'</td>
                         <td style="text-align: center">' . $saldo['Saldo'] . '</td>
						 <td style="text-align: left">' . $valorCusto . '</td>
                         <td class="valorTotal" style="text-align: left">R$ 0, 00</td>
                         <td  style="text-align: center"><i idInput="campo' . $numItens . '" idRow="row' . $numItens . '" class="icon-file-check btn-acoes" style="cursor: pointer"></i></td>
                         <input type="hidden" tipo="' . $item['tipo'] . '" id="campo' . $numItens . '" idLinha="row' . $numItens . '" quantInicial="' . $saldo['Quantidade'] . '" saldoInicial="' . $saldo['Saldo'] . '"  name="campo' . $numItens . '" value="' . $item['tipo'] . '#' . $item['id'] . '#' . $item['valorCusto'] . '#0#0#0#0">
					<tr>
                    ';
            // $output .= "<input type='hidden' tipo='".$item['tipo']."' id='campo".$numItens."' name='campo".$numItens."' value='".$item['id']."#".$item['valorCusto']."'>";
        }

        $output .= '</tbody>';

        $totalGeral = formataMoeda($totalGeral);

        $output .= '
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:right; font-size: 16px; font-weight:bold;">
                         <div>Total (R$) Nota Fiscal:</div>
                         <div>Saldo (R$) Ordem de Compra/Carta Contrato:</div>
                    </th>
                    <th colspan="2">
                        <div>
                            <div id="total" valorTotalGeral="" style="text-align:left; font-size: 15px; font-weight:bold;">R$ 0, 00</div>
                        </div>
                        <div>
                            <div id="totalSaldo" style="text-align:left; font-size: 15px; font-weight:bold;" valor="' . $totalOrdemCompra['valorTotalOrdemCompra'] . '">' . formataMoeda($totalOrdemCompra['valorTotalOrdemCompra']) . '</div>
                         </div>
                    </th>
                </tr>
            </tfoot>
    ';

        $output .= '</table>';

        echo $output;
    } else {
        echo 0;
    }
     

} else {

    if ($count) {
        $numItens = 0;

        $output .= ' <table class="table" id="tabelaProdutoServico">';

        $output .= '<thead>
                    <tr class="bg-slate">
                        <th  style="text-align: center">Item</th>
                        <th>Produto/Serviço</th>
                        <th  style="text-align: center">Unidade Medida</th>
                        <th  style="text-align: center">Quant. Recebida</th>
                        <th  style="text-align: center">Saldo</th>
                        <th  style="text-align: left; width: 10%">Valor Unitário</th>
                        <th  style="text-align: left; width: 13%">Valor Total</th>
                        <th  style="text-align: center">Ações</th>
                    </tr>
                </thead>
        ';
        $output .= '<tbody>';

        foreach (array_reverse($row) as $item) {

            if ($item['tipo'] == 'P') {
                $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $_POST['ordemCompra'] . "'
                   ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);
            } else {

                $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $_POST['ordemCompra'] . "'
                 ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);
            }

            $numItens++;

            $valorCusto = formataMoeda($item['valorCusto']);
            $valorTotal = formataMoeda($item['quantidade'] * $item['valorCusto']);

            $totalGeral += $item['quantidade'] * $item['valorCusto'];
            // var_dump($saldo);
            $output .=  '<tr class="trGrid" id="row' . $numItens . '">
						 <td style="text-align: center">' . $numItens . '</td>
						 <td data-popup="tooltip" data-placement="bottom" title="' . $item['detalhamento'] . '">' . $item['nome'] . '</td>
						 <td style="text-align: center">' . $item['UnMedSigla'] . '</td>
                         <td style="text-align: center"></td>
                         <td style="text-align: center">' . $saldo['Saldo'] . '</td>
						 <td style="text-align: left">' . $valorCusto . '</td>
                         <td class="valorTotal" style="text-align: left">R$ 0, 00</td>
                         <td  style="text-align: center"><i idInput="campo' . $numItens . '" idRow="row' . $numItens . '" class="icon-file-check btn-acoes" style="cursor: pointer"></i></td>
                         <input type="hidden" tipo="' . $item['tipo'] . '" id="campo' . $numItens . '" idLinha="row' . $numItens . '" quantInicial="' . $saldo['Quantidade'] . '" saldoInicial="' . $saldo['Saldo'] . '"  name="campo' . $numItens . '" value="' . $item['tipo'] . '#' . $item['id'] . '#' . $item['valorCusto'] . '#0#0#0#0">
					<tr>
                    ';
            // $output .= "<input type='hidden' tipo='".$item['tipo']."' id='campo".$numItens."' name='campo".$numItens."' value='".$item['id']."#".$item['valorCusto']."'>";
        }

        $output .= '</tbody>';

        $totalGeral = formataMoeda($totalGeral);

        $output .= '
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:right; font-size: 16px; font-weight:bold;">
                         <div>Total (R$) Nota Fiscal:</div>
                         <div>Saldo (R$) Ordem de Compra/Carta Contrato:</div>
                    </th>
                    <th colspan="2">
                        <div>
                            <div id="total" valorTotalGeral="" style="text-align:left; font-size: 15px; font-weight:bold;">R$ 0, 00</div>
                        </div>
                        <div>
                            <div id="totalSaldo" style="text-align:left; font-size: 15px; font-weight:bold;" valor="' . $totalOrdemCompra['valorTotalOrdemCompra'] . '">' . formataMoeda($totalOrdemCompra['valorTotalOrdemCompra']) . '</div>
                         </div>
                    </th>
                </tr>
            </tfoot>
    ';

        $output .= '</table>';

        echo $output;
    } else {
        echo 0;
    }
}
