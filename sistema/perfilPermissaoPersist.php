<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Permissões';

include('global_assets/php/conexao.php');
$MenuId = $_POST['MenuId'];

unset($_POST['MenuId']);
unset($_POST['PerfId']);
try{
		// $inicio1 = microtime(true);
		foreach($_POST as $key => $value){
			$id = explode("-", $key);

			$sqlUpdate = "UPDATE PerfilXPermissao set PrXPeVisualizar =".
			(array_key_exists($id[0]."-view", $_POST)? 1:0).", PrXPeAtualizar = ".
			(array_key_exists($id[0]."-edit", $_POST)? 1:0).", PrXPeExcluir = ".
			(array_key_exists($id[0]."-delet", $_POST)? 1:0)." where PrXPeId = '$id[0]'";
			$conn->query($sqlUpdate);
		}
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Permissão atualizada!!!";
	$_SESSION['msg']['tipo'] = "success";
	// $total1 = microtime(true) - $inicio1;
	// echo '<span style="background-color:yellow; padding: 10px; font-size:24px;">Tempo de execução do script: ' . round($total1, 2).' segundos</span>';

	irpara("perfil.php");
} catch(PDOException $e) {
	// var_dump($e);
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao atualizar Premissão!!!";
	$_SESSION['msg']['tipo'] = "error";
	irpara("perfil.php");
	
	// echo 'Error: ' . $e->getMessage();
}
?>
