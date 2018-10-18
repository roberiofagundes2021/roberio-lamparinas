<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputUsuarioId'])){
	
	$iUsuario = $_POST['inputUsuarioId'];
	$iEmpresa = $_SESSION['EmpreId'];
	$bStatus = $_POST['inputUsuarioStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE EmpresaXUsuarioXPerfil SET EXUXPStatus = :bStatus
				WHERE EXUXPUsuario = :idUsuario and EXUXPEmpresa = :idEmpresa";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus);
		$result->bindParam(':idUsuario', $iUsuario);
		$result->bindParam(':idEmpresa', $iEmpresa);
		$result->execute();
		
		$_SESSION['msg'] = "Situação do usuário alterada com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao alterar situação do usuário!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("usuario.php");

?>
