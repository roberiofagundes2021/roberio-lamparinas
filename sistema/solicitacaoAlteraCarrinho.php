<?php

use Mpdf\Utils\Arrays;

include_once("sessao.php");
include('global_assets/php/conexao.php');


if (isset($_SESSION['Carrinho']) && !empty($_POST['inputIdProduto'])) {
    $quantProdu = $_POST['inputQuantidadeProduto'];
    $idProdu = $_POST['inputIdProduto'];
    $produtos = $_SESSION['Carrinho'];
    $newProdutos = [];

    foreach ($produtos as $key => $value) {
        // essa parte verifica o array e retira o item que o usuário escolheu
        // OBS.: Antes setava a quantidade como "0", agora exclui do array
        if($quantProdu != 0){
            if ($value['id'] == $idProdu) {
                $produtos[$key]['quantidade'] = $quantProdu;
                print('' . $produtos[$key]['quantidade'] . '');
            }
        } else {
            if ($value['id'] != $idProdu) {
                array_push($newProdutos, $value);
            }
        }
    }
    $_SESSION['Carrinho'] = $newProdutos;

    // foreach ($_SESSION['Carrinho'] as $key => $value) {
    //     if ($value['id'] == $idProdu) {
    //         // Acessando subindices da matriz, para mudar o valor da chave 'quantidade' no registro validado pelo if.
    //         //if ($quantProdu != 0) {
    //             $_SESSION['Carrinho'][$key]['quantidade'] = $quantProdu;
    //             print('' . $_SESSION['Carrinho'][$key]['quantidade'] . '');
    //        // } else {
    //             // Ver isso aqui
    //             //unset($_SESSION['Carrinho'][$key]);
    //        // }
    //     }
    // }
} else {
    print('erro');
}
