<?php

use Mpdf\Utils\Arrays;

include_once("sessao.php");
include('global_assets/php/conexao.php');


if(!empty($_POST['inputProdutoId'])){
    if(session_status() !== 'PHP_SESSION_ACTIVE'){

        //$produtos = [];

        //if(isset($_SESSION['carrinho'])) {
          //  if(!in_array($_SESSION['carrinho'], $produtos)){
           //     array_push($produtos, $_SESSION['carrinho']);
           // } else {
           //     print('ja existe');
           // }
       // }

        //$_SESSION['carrinho'] = [$_POST['inputProdutoId']];

        $produtos = $_SESSION['carrinho'];

        array_push($produtos, $_POST['inputProdutoId']);
        $_SESSION['carrinho'] = $produtos;
        //array_push($_SESSION['carrinho'], $_POST['inputProdutoId'] );
    }
    var_dump($_SESSION['carrinho']);
}
