<?php 

include_once("sessao.php"); 

$_SESSION['EmpresaId'] = $_POST['id'];
$_SESSION['EmpresaNome'] = $_POST['nome'];

echo 1;

?>
