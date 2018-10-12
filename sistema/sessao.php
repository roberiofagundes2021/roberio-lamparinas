<?php

session_start(); 

if(!array_key_exists('UsuarId', $_SESSION) or !$_SESSION['UsuarLogado']){
  header('Expires: 0');
  header('Pragma: no-cache');  
  header("Location: login.php");
  return false;	
}

require_once("global_assets/php/funcoesgerais.php");

?>
