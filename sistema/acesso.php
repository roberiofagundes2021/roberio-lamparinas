<?php

if(!isset($_SESSION)){
    session_start();
}

date_default_timezone_set("Brazil/East");

require_once("global_assets/php/funcoesgerais.php");
include('global_assets/php/conexao.php');

$erro = array();

if(isset($_POST)){
	$psUsuario = $_POST['usuario'];
	$psSenha = md5($_POST['senha']);
	
	$_SESSION['UsuarioLogin'] = $_POST['usuario'];

	$usuario_escape = addslashes($psUsuario);
	$senha_escape = addslashes($psSenha);

	$sql = ("SELECT UsuarId, UsuarLogin, UsuarNome FROM Usuario WHERE UsuarLogin = '$usuario_escape' and UsuarSenha = '$senha_escape'");
	$result = $conn->query("$sql");

	if ($row = $result->fetch()){
		$_SESSION['UsuarioId'] = $row[0];
		$_SESSION['UsuarioLogin'] = $row[1];
		$_SESSION['UsuarioNome'] = $row[2];
		$_SESSION['UsuarioLogado'] = 1;
	} else {
		$_SESSION['UsuarioLogado'] = 0;
		$erro[] = "<strong>Senha</strong> incorreta.";
	}
}
	
if(!array_key_exists('UsuarioId', $_SESSION) or !$_SESSION['UsuarioLogado']){
  header('Expires: 0');
  header('Pragma: no-cache');  
  header("Location: login.php");
  return false;
};

?>
