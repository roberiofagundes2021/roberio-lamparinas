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


if ($count) {
    $numItens = 0;

    $output .= ' <table class="table" id="tabelaProdutoServico">';

    $output .= '<thead>
                    <tr class="bg-slate">
                        <th>Item</th>
                        <th>Produto/Serviço</th>
                        <th>Unidade Medida</th>
                        <th>Quant. Rec.</th>
                        <th>Saldo</th>
                        <th>Valor Unitário</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
        ';
    $output .= '<tbody>';

    foreach ($row as $item) {

        if ($item['tipo'] == 'P') {
            $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, ".$item['id'].", 'P') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, ".$item['id'].", 'P') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $_POST['ordemCompra'] . "'
                   ";
            $result = $conn->query($sql);
            $saldo = $result->fetch(PDO::FETCH_ASSOC);
        } else {

            $sql = "SELECT  dbo.fnQuantidadeEntrada(OrComEmpresa, OrComId, ".$item['id'].", 'S') as Quantidade, dbo.fnSaldoEntrada(OrComEmpresa, OrComId, ".$item['id'].", 'S') as Saldo
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
						 <td>' . $numItens . '</td>
						 <td data-popup="tooltip" data-placement="bottom" title="'.$item['detalhamento'].'">' . $item['nome'] . '</td>
						 <td>' . $item['UnMedSigla'] . '</td>
                         <td>' . $saldo['Quantidade'] . '</td>
                         <td>' . $saldo['Saldo'] . '</td>
						 <td>' . $valorCusto . '</td>
                         <td>' . $valorTotal . '</td>
                         <td><i idInput="campo' . $numItens . '" idRow="row' . $numItens . '" class="icon-file-check btn-acoes" style="cursor: pointer"></i></td>
                         <input type="hidden" tipo="' . $item['tipo'] . '" id="campo' . $numItens . '" quantInicial="'.$saldo['Quantidade'].'" saldoInicial="'.$saldo['Saldo'].'"  name="campo' . $numItens . '" value="'.$item['tipo'].'#' . $item['id'] . '#' . $item['valorCusto'] . '#0#0#0#0">
					<tr>
                    ';
        // $output .= "<input type='hidden' tipo='".$item['tipo']."' id='campo".$numItens."' name='campo".$numItens."' value='".$item['id']."#".$item['valorCusto']."'>";
    }

    $output .= '</tbody>';

    $totalGeral = formataMoeda($totalGeral);

    $output .= '
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align:right; font-size: 16px; font-weight:bold;">Total:</th>
                    <th colspan="2">
                        <div id="total" style="text-align:left; font-size: 15px; font-weight:bold;">' . $totalGeral . '</div>
                    </th>
                </tr>
            </tfoot>
    ';

    $output .= '</table>';

    echo $output;
} else {
    echo 0;
}
