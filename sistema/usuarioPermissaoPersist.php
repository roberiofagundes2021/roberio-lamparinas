<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Permissões';

include('global_assets/php/conexao.php');
$usuario = $_POST['usuarioId'];
$unidade = $_POST['UnidadeId'];

unset($_POST['usuarioId']);
unset($_POST['UnidadeId']);
unset($_POST['MenuId']);
$idMenu = [];
try{

	$sqlMenuUxP = "SELECT MenuId FROM menu";

	$resultMenuUxP = $conn->query($sqlMenuUxP);
	$menuUxPId = $resultMenuUxP->fetchAll(PDO::FETCH_ASSOC);

	// medir velocidade do código
	// $inicio1 = microtime(true);

	if(!array_key_exists('permissao_usuario', $_POST)){
		$sqlMenuUxP = "SELECT UsXPeId FROM UsuarioXPermissao WHERE UsXPeUsuario = '$usuario'";
		$UsXPe = $conn->query($sqlMenuUxP);
		$UsXPeList = $UsXPe->fetchAll(PDO::FETCH_ASSOC);

		$sqlUpdateUser = "UPDATE Usuario set UsuarPermissaoPerfil=0 where UsuarId = '$usuario'";
		$conn->query($sqlUpdateUser);

		if (count($UsXPeList)==0){
			$sqlInsert = "INSERT INTO UsuarioXPermissao
				(UsXPeUsuario, UsXPeMenu, UsXPeUnidade, UsXPeVisualizar, UsXPeAtualizar, UsXPeExcluir)
				VALUES ";
			foreach($menuUxPId as $key){
				$id = $key['MenuId'];
				$sqlInsert = $sqlInsert."('$usuario', '$id', '$unidade', 0, 0, 0),";
			}
			$sqlInsert = substr($sqlInsert, 0, -1);
			$conn->query($sqlInsert);
		}

		foreach($menuUxPId as $key){
			$id = $key['MenuId'];
			$sqlUpdate = "UPDATE UsuarioXPermissao set UsXPeVisualizar=".
			(array_key_exists($id."-view", $_POST)? 1:0).", UsXPeAtualizar=".
			(array_key_exists($id."-edit", $_POST)? 1:0).", UsXPeExcluir=".
			(array_key_exists($id."-delet", $_POST)? 1:0)."
			WHERE UsXPeUsuario='$usuario' and UsXPeMenu='$id' and UsXPeUnidade = '$unidade'";
			$conn->query($sqlUpdate);
		}
	}else{
		$sqlUpdateUser = "UPDATE Usuario set UsuarPermissaoPerfil=1 where UsuarId = '$usuario'";
		$sqlDelete = "DELETE FROM UsuarioXPermissao where UsXPeUsuario = '$usuario'";
		$conn->query($sqlUpdateUser);
		$conn->query($sqlDelete);
	}
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = "Permissão atualizada!!!";
	$_SESSION['msg']['tipo'] = "success";

	// medir velocidade do código
	// $total1 = microtime(true) - $inicio1;
	// echo '<span style="background-color:yellow; padding: 10px; font-size:24px;">Tempo de execução do script: ' . round($total1, 2).' segundos</span>';

	irpara("usuario.php");
} catch(PDOException $e) {
	// var_dump($e);
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao atualizar Premissão!!!";
	$_SESSION['msg']['tipo'] = "error";
	irpara("usuario.php");
	
	// echo 'Error: ' . $e->getMessage();
}
?>
