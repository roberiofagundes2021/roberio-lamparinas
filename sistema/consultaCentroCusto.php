<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$data = $_POST['inputPlanoConta1'];



print(json_encode($data));
?>