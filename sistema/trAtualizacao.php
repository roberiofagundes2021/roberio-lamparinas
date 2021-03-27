<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Aprovação';

include('global_assets/php/conexao.php');

$sql = 
	"	UPDATE TermoReferencia
		SET trRefStatus = 9
		WHERE TrRefUnidade = " . $_SESSION['UnidadeId'] . "
		AND TrRefID = " . $_POST['inputTRId'] . "";
$result = $conn->prepare($sql);
$result->execute();	