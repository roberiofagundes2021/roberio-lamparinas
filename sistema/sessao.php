<?php

session_start(); 

$arquivosEmpresa = array('usuario.php', 'usuarioNovo.php', 'usuarioEdita.php', 'usuarioExclui.php', 'usuarioMudaSituacao.php', 'usuarioValida.php', 
						 'licenca.php', 'licencaNovo.php', 'licencaEdita.php', 'licencaExclui.php', 'licencaMudaSituacao.php', 
				  		 'unidade.php', 'unidadeNovo.php', 'unidadeEdita.php', 'unidadeExclui.php', 'unidadeMudaSituacao.php', 'unidadeValida.php',
				  		 'setor.php'  , 'setorNovo.php'  , 'setorEdita.php'  , 'setorExclui.php'  , 'setorMudaSituacao.php', 'filtraSetor.php',
				  		 'menu.php'   , 'menuNovo.php'   , 'menuEdita.php'   , 'menuExclui.php'   , 'menuMudaSituacao.php', 'menuLeftSecundario.php',
				  		 'parametro.php');
				  
//Se existe a sessão $_SESSION['EmpresaId'] e a página que está sendo acessada não é nenhuma das sitadas acima, limpa essa sessão.	  
if(array_key_exists('EmpresaId', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $arquivosEmpresa)){
	unset($_SESSION['EmpresaId']);
	unset($_SESSION['EmpresaNome']);
}

$arquivosAditivo = array('fluxoAditivo.php', 'fluxoAditivoNovo.php', 'fluxoAditivoEdita.php', 'fluxoAditivoExclui.php');
				  
//Se existe a sessão $_SESSION['FluxoId'] e a página que está sendo acessada não é nenhuma das sitadas acima, limpa essa sessão.	  
if(array_key_exists('FluxoId', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $arquivosAditivo)){
	unset($_SESSION['FluxoId']);
}

/*
$tr = array('trOrcamento.php', 'trOrcamentoNovo.php', 'trOrcamentoEdita.php', 'trOrcamentoExclui.php', 'trOrcamentoImprime.php',
			'trOrcamentoProduto.php', 'trOrcamentoDuplica.php');

//Se existe a sessão $_SESSION['TRId'] e a página que está sendo acessada não é nenhuma das sitadas acima, limpa essa sessão.	  
if(array_key_exists('TRId', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $tr)){
	unset($_SESSION['TRId']);
	unset($_SESSION['TRNumero']);
}

echo "<br>";
echo "Existe o array? ".array_key_exists('TRId', $_SESSION);
echo "<br>";
echo basename($_SERVER['PHP_SELF']);
echo "<br>";
echo in_array(basename($_SERVER['PHP_SELF']), $tr);
*/

if(!array_key_exists('UsuarId', $_SESSION) or !$_SESSION['UsuarLogado']){
  header('Expires: 0');
  header('Pragma: no-cache');  
  header("Location: login.php");
  return false;	
}

require_once("global_assets/php/funcoesgerais.php");

?>
