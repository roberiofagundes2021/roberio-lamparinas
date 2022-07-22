<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

//$atendimentoId = $_POST['inputAtendimentoId'];
$atendimentoId = 5;

$sql_atendimento    = "SELECT AtendNumRegistro, ClienNome, CxRecDataHora, CxRecAtendimento, FrPagNome, 
                              CxRecValor, CxRecValorTotal, SituaNome, 'Recebimento' as Tipo
                       FROM CaixaRecebimento
                       JOIN FormaPagamento on FrPagId = CxRecFormaPagamento
                       JOIN Atendimento on AtendId = CxRecAtendimento
                       JOIN Cliente on ClienId = AtendCliente
                       JOIN Situacao on SituaId = CxRecStatus
                       WHERE CxRecUnidade = " . $_SESSION['UnidadeId'] . "";
$resultAtendimento  = $conn->query($sql_atendimento);
$rowSaldoInicial = $resultAtendimento->fetchAll(PDO::FETCH_ASSOC);

$arrayData = [];
foreach ($rowSaldoInicial as $item) {
    $numeroRegistro = $item["AtendNumRegistro"];
    $dataHora = mostraDataHora($item["CxRecDataHora"]);
    $historico = $item["ClienNome"];
    $tipo = $item["Tipo"];
    $formaPagamento = $item["FrPagNome"];
    $valorFinal = mostraValor($item["CxRecValorTotal"]);
    $status = $item["SituaNome"];

    $acoes = '
            <div class="list-icons">
                <div class="list-icons list-icons-extended">
                    <a href="#" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>
                    <a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar Conta"><i class="icon-undo2"></i></a>
                    <div class="dropdown"">													
                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                            <i class="icon-menu9"></i>
                
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" class="dropdown-item btnParcelar"  data-popup="tooltip" data-placement="bottom" title="Parcelar"><i class="icon-file-text2"></i> Parcelar</a>
                        </div>
                    </div>
                </div>
            </div>';

    $array = [  
        'data'=>[
            isset($numeroRegistro) ? $numeroRegistro : null,
            isset($dataHora) ? $dataHora : null,
            isset($historico) ? $historico : null,
            isset($tipo) ? $tipo : null,
            isset($formaPagamento) ? $formaPagamento : null,
            isset($valorFinal) ? $valorFinal : null,
            isset($status) ? $status : null,
            isset($acoes) ? $acoes : null
        ],
        'identify'=>[
            
        ]
    ];

    array_push($arrayData,$array);
}

print(json_encode($arrayData));
?>