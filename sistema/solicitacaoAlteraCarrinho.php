<?php

use Mpdf\Utils\Arrays;

include_once("sessao.php");
include('global_assets/php/conexao.php');


if (isset($_SESSION['Carrinho']) && !empty($_POST['inputIdProduto'])) {
    $quantProdu = $_POST['inputQuantidadeProduto'];
    $idProdu = $_POST['inputIdProduto'];

    foreach ($_SESSION['Carrinho'] as $key => $value) {
        if ($value['id'] == $idProdu) {
            // Acessando subindices da matriz, para mudar o valor da chave 'quantidade' no registro validado pelo if.
            //if ($quantProdu != 0) {
                $_SESSION['Carrinho'][$key]['quantidade'] = $quantProdu;
                print('' . $_SESSION['Carrinho'][$key]['quantidade'] . '');
           // } else {
                // Ver isso aqui
                //unset($_SESSION['Carrinho'][$key]);
           // }
        }
    }
} else {
    print('erro');
}
