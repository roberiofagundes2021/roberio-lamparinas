<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$sql = "SELECT OCXPrQuantidade as quantidade, ProduId as id, ProduNome as nome, ProduValorCusto as valorCusto, ProduCustoFinal as custoFinal, UnMedSigla
		FROM OrdemCompraXProduto
        JOIN Produto on ProduId = OCXPrProduto
 		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
        WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OCXPrOrdemCompra = '".$_POST['ordemCompra']."'
        UNION
        SELECT OCXSrQuantidade as quantidade, ServiId as id, ServiNome as nome, ServiValorCusto as valorCusto, ServiCustoFinal as custoFinal, UnMedSigla
		FROM OrdemCompraXServico
        JOIN Servico on ServiId = OCXSrServico
 		LEFT JOIN UnidadeMedida on UnMedId = 0
        WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and OCXSrOrdemCompra = '".$_POST['ordemCompra']."'
        ";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//print($sql);
var_dump($row);
//Verifica se já existe esse registro (se existir, retorna true )
$output = '';
$totalGeral = 0;


if ($count) {
    $numItens = 0;

    $output .= '<thead>
                    <tr class="bg-slate">
                        <th width="5%">Item</th>
                        <th width="45%">Produto/Serviço</th>
                        <th width="14%">Unidade Medida</th>
                        <th width="8%">Quantidade</th>
                        <th width="14%">Valor Unitário</th>
                        <th width="14%">Valor Total</th>
                    </tr>
                </thead>
        '; 

    foreach ($row as $produto) {
        $numItens++;

        $valorCusto = formataMoeda($produto['valorCusto']);
        $valorTotal = formataMoeda($produto['quantidade'] * $produto['valorCusto']);

        $totalGeral += $produto['quantidade'] * $produto['valorCusto'];

        $output .=     '<tr id="row' . $numItens . '">
						 <td>' . $numItens . '</td>
						 <td>' . $produto['nome'] . '</td>
						 <td>' . $produto['UnMedSigla'] . '</td>
						 <td>' . $produto['quantidade'] . '</td>
						 <td>' . $valorCusto . '</td>
						 <td>' . $valorTotal . '</td>
					 <tr>
					 ';
    }

    $totalGeral = formataMoeda($totalGeral);

    $output .= '
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align:right; font-size: 16px; font-weight:bold;">Total:</th>
                    <th colspan="2">
                        <div id="total" style="text-align:left; font-size: 15px; font-weight:bold;">'.$totalGeral.'</div>
                    </th>
                </tr>
            </tfoot>
    ';
    echo $output;
    
} else {
    echo 0;
}
