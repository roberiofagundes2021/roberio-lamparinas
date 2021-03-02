<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');


$sql = "SELECT PlConId, PlConNome
            FROM PlanoContas
            JOIN Situacao 
            ON SituaId = PlConStatus
        WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " 
            AND SituaChave = 'ATIVO'
        ORDER BY PlConNome ASC";

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
    echo json_encode($row);
} else{
    echo 0;
}
