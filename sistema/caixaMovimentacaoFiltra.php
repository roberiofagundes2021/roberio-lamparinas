<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

//$atendimentoId = $_POST['inputAtendimentoId'];
$atendimentoId = 5;
$operadorId = $_SESSION['UsuarId'];

//Falta colocar o CaixaFechamento
$sql_movimentacao    = "SELECT AtendNumRegistro, ClienNome, CxRecDataHora, CxRecAtendimento, FrPagNome, 
                              CxRecValor, CxRecValorTotal, SituaNome, SituaChave, 'Recebimento' as Tipo
                       FROM CaixaRecebimento
                       JOIN CaixaAbertura on CxAbeId = CxRecCaixaAbertura
                       JOIN FormaPagamento on FrPagId = CxRecFormaPagamento
                       JOIN Atendimento on AtendId = CxRecAtendimento
                       JOIN Cliente on ClienId = AtendCliente
                       JOIN Situacao on SituaId = CxRecStatus
                       WHERE CxAbeOperador = " . $operadorId . " and CxRecUnidade = " . $_SESSION['UnidadeId'] . "";
$resultMovimentacao  = $conn->query($sql_movimentacao);
$rowMovimentacao = $resultMovimentacao->fetchAll(PDO::FETCH_ASSOC);

$arrayData = [];
foreach ($rowMovimentacao as $item) {
    $numeroRegistro = $item["AtendNumRegistro"];
    $dataHora = mostraDataHora($item["CxRecDataHora"]);
    $historico = $item["ClienNome"];
    $tipo = $item["Tipo"];
    $formaPagamento = $item["FrPagNome"];
    $valorFinal = mostraValor($item["CxRecValorTotal"]);
    $status = $item["SituaNome"];

    $iconeVizivel = $item["SituaChave"] == 'ESTORNADO' ? '<a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar"><i class="icon-info3"></i></a>' :
                                                         '<a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar"><i class="icon-undo2"></i></a>';

    $acoes = '
            <div class="list-icons">
                <div class="list-icons list-icons-extended">
                    <a href="#" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Detalhamento"><i class="icon-file-text2"></i></a>
                    '.$iconeVizivel.'
                    <a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaContasAPagar('.$item['AtendNumRegistro'].','.$item["ClienNome"].', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Imprimir"><i class="icon-printer2"></i></a>
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