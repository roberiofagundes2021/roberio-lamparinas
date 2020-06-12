<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

if (isset($_POST['inputEstadoConservacao'])) {
    $sql = "SELECT EstCoId, EstCoNome
			FROM EstadoConservacao
            JOIN Situacao on SituaId = EstCoStatus
			WHERE SituaChave = 'ATIVO'
            ORDER BY EstCoNome ASC";

    $result = $conn->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);
    $count = count($row);

    //Verifica se já existe esse registro (se existir, retorna true)
    if ($count) {
        foreach ($row as $item) {
            if ($item['EstCoId'] == $_POST['inputEstadoConservacao']) {
                print("<option value='" . $item['EstCoId'] . "' selected>" . $item['EstCoNome']."</option>");
            } else {
                print("<option value='" . $item['EstCoId'] . "'>" . $item['EstCoNome'] . "</option>");
            }
        }
    } else {
        echo 0;
    }
}
