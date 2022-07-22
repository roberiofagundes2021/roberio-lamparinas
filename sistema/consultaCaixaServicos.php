<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$atendimentoId = $_POST['inputAtendimentoId'];

$sql_atendimento    = "SELECT SrVenNome, ProfiNome, SrVenValorVenda, AtXSeDesconto
                        FROM AtendimentoXServico
                        JOIN ServicoVenda ON SrVenId = AtXSeServico
                        JOIN Profissional ON ProfiId = AtXSeProfissional
                        WHERE AtXSeAtendimento = ".$atendimentoId." AND AtXSeUnidade = ".$_SESSION['UnidadeId']."";
$resultAtendimento  = $conn->query($sql_atendimento);
$rowSaldoInicial = $resultAtendimento->fetchAll(PDO::FETCH_ASSOC);

$arrayData = [];
foreach ($rowSaldoInicial as $item) {
    $procedimento = $item["SrVenNome"];
    $medico = $item["ProfiNome"];
    $valorTotal = mostraValor($item["SrVenValorVenda"]);
    $desconto = mostraValor($item["AtXSeDesconto"]);

    $array = [
        'data'=>[
            isset($procedimento) ? $procedimento : null,
            isset($medico) ? $medico : null,
            isset($valorTotal) ? $valorTotal : null,
            isset($desconto) ? $desconto : null
        ],
        'identify'=>[
            
        ]
    ];

    array_push($arrayData,$array);
}

print(json_encode($arrayData));
?>