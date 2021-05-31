<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Permissões';

include('global_assets/php/conexao.php');
$MenuId = $_POST['MenuId'];
$PerfId = $_POST['PerfId'];

unset($_POST['MenuId']);
unset($_POST['PerfId']);
try{
		foreach($_POST as $key => $value){
			$id = explode("-", $key);

			$sqlView = "UPDATE PerfilXPermissao set PrXPeVisualizar =".(array_key_exists($id[0]."-view", $_POST)? 1:0)."
			where PrXPeId = '$id[0]' and PrXPePerfil = '$PerfId'";
			$conn->query($sqlView);

			$sqlEdit = "UPDATE PerfilXPermissao set PrXPeAtualizar = ".(array_key_exists($id[0]."-edit", $_POST)? 1:0)."
			where PrXPeId = '$id[0]' and PrXPePerfil = '$PerfId'";
			$conn->query($sqlEdit);

			$sqlDelet = "UPDATE PerfilXPermissao set PrXPeExcluir = ".(array_key_exists($id[0]."-delet", $_POST)? 1:0)."
			where PrXPeId = '$id[0]' and PrXPePerfil = '$PerfId'";
			$conn->query($sqlDelet);
		}
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Permissão atualizada!!!";
	$_SESSION['msg']['tipo'] = "success";

	irpara("perfil.php");
} catch(PDOException $e) {
	irpara("perfil.php");
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao atualizar Premissão!!!";
	$_SESSION['msg']['tipo'] = "error";				
	
	// echo 'Error: ' . $e->getMessage();
}
?>
