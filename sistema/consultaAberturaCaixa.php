<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

//Para pegar a última consulta
$sql_saldoInicial    = "SELECT CxAbeId, CaixaNome, CxAbeCaixa, CxAbeDataHoraAbertura, CxAbeDataHoraFechamento 
                        FROM CaixaAbertura
                        JOIN Caixa on CaixaId = CxAbeCaixa
                        WHERE CxAbeOperador = ".$_SESSION['UsuarId']." ORDER BY CxAbeId DESC";
$resultSaldoInicial  = $conn->query($sql_saldoInicial);


if($rowSaldoInicial = $resultSaldoInicial->fetch(PDO::FETCH_ASSOC)) {
    $resposta = $rowSaldoInicial;
}else {
    $resposta = 'abrirCaixa';
}

print(json_encode($resposta));
?>