<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

// Formata Data para aparecer DD/MM/YYYY
$sql = " SELECT OrComId, OrComNumero
		 FROM OrdemCompra
         JOIN Situacao on SituaId = OrComSituacao
		 WHERE OrComEmpresa = ". $_SESSION['EmpreId'] . " and OrComFornecedor = " . $_GET['idFornecedor'] . " and SituaChave = 'LIBERADO' ";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if ($count) {

    foreach ($row as $value) {

        // Verifica se tem alguma Movimentação do tipo ENTRADA para essa OrdemCompra
        $sql = " SELECT COUNT(MovimId) as CONT
                FROM Movimentacao
                JOIN Situacao on SituaId = MovimSituacao
                WHERE MovimEmpresa = ". $_SESSION['EmpreId'] . " and MovimOrdemCompra = " . $value['OrComId'] . " 
                and MovimTipo = 'E' and SituaChave = 'LIBERADO' ";
        $result = $conn->query($sql);
        $rowMovimentacao = $result->fetch(PDO::FETCH_ASSOC);

        if ($rowMovimentacao['CONT']){
            
            $sql = "
            SELECT OCXPrQuantidade as quantidade, ProduId as id, ProduNome as nome, ProduDetalhamento as detalhamento, ProduValorCusto as valorCusto, ProduCustoFinal as custoFinal, tipo = 'P'
            FROM OrdemCompraXProduto
            JOIN Produto on ProduId = OCXPrProduto
            WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and OCXPrOrdemCompra = " . $value['OrComId'] . "
            UNION
            SELECT OCXSrQuantidade as quantidade, ServiId as id, ServiNome as nome, ServiDetalhamento as detalhamento, ServiValorCusto as valorCusto, ServiCustoFinal as custoFinal, tipo = 'S'
            FROM OrdemCompraXServico
            JOIN Servico on ServiId = OCXSrServico
            WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and OCXSrOrdemCompra = " . $value['OrComId'] . "
            ";
            $result = $conn->query($sql);
            $rowProdutoServico = $result->fetchAll(PDO::FETCH_ASSOC);

            $saldosPositivos = '';
            $totalProdutos = count($rowProdutoServico);

            foreach ($rowProdutoServico as $item) {

                if ($item['tipo'] == 'P') {
                    $sql = "SELECT dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'P') as Saldo
                            FROM OrdemCompra
                            Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = " . $value['OrComId'] . "
                    ";
                    $result = $conn->query($sql);
                    $saldo = $result->fetch(PDO::FETCH_ASSOC);

                    if ($saldo['Saldo'] > 0) {
                        $saldosPositivos++;
                    }
                } else {

                    $sql = "SELECT dbo.fnSaldoEntrada(OrComEmpresa, OrComId, " . $item['id'] . ", 'S') as Saldo
                            FROM OrdemCompra
                            Where OrComEmpresa = " . $_SESSION['EmpreId'] . " and OrComId = " . $value['OrComId'] . "
                    ";
                    $result = $conn->query($sql);
                    $saldo = $result->fetch(PDO::FETCH_ASSOC);

                    if ($saldo['Saldo'] > 0) {
                        $saldosPositivos++;
                    }
                }
            }

            if ($saldosPositivos >= 1) {
                print('<option idOrdemCompra="' . $value['OrComId'] . '" value="' . $value['OrComId'] . '">' . $value['OrComNumero'] . '</option>');
            }
        } else {
            print('<option idOrdemCompra="' . $value['OrComId'] . '" value="' . $value['OrComId'] . '">' . $value['OrComNumero'] . '</option>');
        }
        var_dump($row);
    }
}
