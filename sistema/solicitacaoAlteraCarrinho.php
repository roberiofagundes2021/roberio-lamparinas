<?php

use Mpdf\Utils\Arrays;

include_once("sessao.php");
include('global_assets/php/conexao.php');


if (isset($_SESSION['Carrinho']) && !empty($_POST['inputIdProduto'])) {
    $quantProdu = $_POST['inputQuantidadeProduto'];
    $idProdu = $_POST['inputIdProduto'];
    $array = $_SESSION['Carrinho'];

    foreach ($array as $key => $value) {
        // essa parte verifica o array e retira o item que o usuário escolheu
        // OBS.: Antes setava a quantidade como "0", agora exclui do array
        if ($value['id'] == $idProdu) {
            print('' . $array[$key]['quantidade'] . '');
            unset($array[$key]);
            $_SESSION['Carrinho'] = $array;
        }
    }
} else {
    print('erro');
}
