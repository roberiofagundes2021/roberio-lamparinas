<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

// Formata Data para aparecer DD/MM/YYYY
$sql = " SELECT OrComId, OrComNumero
		 FROM OrdemCompra
         JOIN Situacao on SituaId = OrComSituacao
		 WHERE OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComFornecedor = '" . $_GET['idFornecedor'] . "' and SituaChave = 'LIBERADO'
        ";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if ($count) {
    foreach ($row as $value) {


        $sql = "SELECT OCXPrQuantidade as quantidade, ProduId as id, ProduNome as nome, ProduDetalhamento as detalhamento, ProduValorCusto as valorCusto, ProduCustoFinal as custoFinal, UnMedSigla, tipo = 'P'
		FROM OrdemCompraXProduto
        JOIN Produto on ProduId = OCXPrProduto
 		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
        WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OCXPrOrdemCompra = '" . $value['OrComId'] . "'
        UNION
        SELECT OCXSrQuantidade as quantidade, ServiId as id, ServiNome as nome, ServiDetalhamento as detalhamento, ServiValorCusto as valorCusto, ServiCustoFinal as custoFinal, UnMedSigla, tipo = 'S'
		FROM OrdemCompraXServico
        JOIN Servico on ServiId = OCXSrServico
 		LEFT JOIN UnidadeMedida on UnMedId = 0
        WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and OCXSrOrdemCompra = '" . $value['OrComId'] . "'
        ";
        $result = $conn->query($sql);
        $rowProdutoServico = $result->fetchAll(PDO::FETCH_ASSOC);

        $saldosPositivos = '';
        $totalProdutos = count($rowProdutoServico);

        foreach ($rowProdutoServico as $item) {

            if ($item['tipo'] == 'P') {
                $sql = "SELECT dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $value['OrComId'] . "'
                   ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);
        

                if ($saldo['Saldo'] > 0) {
                    $saldosPositivos++;
                }
            } else {

                $sql = "SELECT dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Saldo
                        FROM OrdemCompra
                        Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = '" . $value['OrComId'] . "'
                 ";
                $result = $conn->query($sql);
                $saldo = $result->fetch(PDO::FETCH_ASSOC);

                if ($saldo['Saldo'] > 0) {
                    $saldosPositivos++;
                }
            }
        }

        print($saldosPositivos);
        print($totalProdutos);

        if ($saldosPositivos == $totalProdutos) {
            print('<option idOrdemCompra="' . $value['OrComId'] . '" value="' . $value['OrComId'] . '">' . $value['OrComNumero'] . '</option>');
        }
    }
}
