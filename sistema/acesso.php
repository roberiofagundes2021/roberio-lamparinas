<?php

session_start();
date_default_timezone_set("Brazil/East");

require_once("global_assets/php/funcoesgerais.php");

try {
    $conn = new PDO("sqlsrv:server = tcp:lamparinas.database.windows.net,1433; 
									 Database = DBLamparinas", "valmaregia", "cValToChar16");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Erro de conexão com o banco.");
    die(print_r($e));
}

if($_POST){
	$psUsuario = $_POST['usuario'];
	$psSenha = md5($_POST['senha']);

	$usuario_escape = addslashes($psUsuario);
	$senha_escape = addslashes($psSenha);

	$sql = ("SELECT UsuarId, UsuarNome FROM Usuario WHERE UsuarLogin = '$usuario_escape' and UsuarSenha = '$senha_escape'");
	$result = $conn->query("$sql");

	if ($row = $result->fetch()){
		$_SESSION['UsuarioId'] = $row[0];
		$_SESSION['UsuarioNome'] = $row[1];
		$_SESSION['UsuarioLogado'] = 1;
	} else {
		$_SESSION['UsuarioLogado'] = 0;
	}
}
	
if(!array_key_exists('UsuarioId', $_SESSION) or !$_SESSION['UsuarioLogado']){
  header('Expires: 0');
  header('Pragma: no-cache');  
  header("Location: login.php");
  return false;
};

?>
