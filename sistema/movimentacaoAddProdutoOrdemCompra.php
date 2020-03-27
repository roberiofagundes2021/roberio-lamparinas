<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$sql = "SELECT OCXPrQuantidade, ProduId, ProduNome, ProduValorCusto, ProduCustoFinal, UnMedSigla
		FROM OrdemCompraXProduto
        JOIN Produto on ProduId = OCXPrProduto
 		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OCXPrOrdemCompra = '".$_POST['ordemCompra']."'";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//print($sql);
//var_dump($row);
//Verifica se já existe esse registro (se existir, retorna true )
$output = '';

if ($count) {
    $numItens = 0;
    foreach ($row as $produto) {
        $numItens++;

        $valorCusto = formataMoeda($produto['ProduValorCusto']);
        $valorTotal = formataMoeda($produto['OCXPrQuantidade'] * $produto['ProduValorCusto']);

        $total = $produto['OCXPrQuantidade'] * $produto['ProduValorCusto'];

        $output .=     '<tr id="row' . $numItens . '">
						 <td>' . $numItens . '</td>
						 <td>' . $produto['ProduNome'] . '</td>
						 <td>' . $produto['UnMedSigla'] . '</td>
						 <td>' . $produto['OCXPrQuantidade'] . '</td>
						 <td>' . $produto['ProduValorCusto'] . '</td>
						 <td>' . $valorTotal . '</td>
					 <tr>
					 ';
    }
    echo $output;
} else {
    echo 0;
}
