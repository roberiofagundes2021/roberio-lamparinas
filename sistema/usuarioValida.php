<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_SESSION['EmpresaId'])){
	$EmpresaId = $_SESSION['EmpresaId'];
} else {	
	$EmpresaId = $_SESSION['EmpreId'];
}

if(isset($_POST['nomeVelho'])){
	$sql = ("SELECT UsuarId
			 FROM Usuario
			 WHERE UsuarEmpresa = ".$_SESSION['EmpreId']." and UsuarNome = '". $_POST['nomeNovo']."' and UsuarNome <> '". $_POST['nomeVelho']."'");
} else{
	$sql = ("SELECT UsuarId, UsuarNome, UsuarLogin, UsuarSenha, UsuarEmail, UsuarTelefone, UsuarCelular, 
					EXUXPEmpresa, EXUXPPerfil, EXUXPUnidade, EXUXPSetor
			 FROM Usuario
			 JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
			 WHERE UsuarCpf = '". $_GET['cpf']."'");
			 
	$result = $conn->query("$sql");
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	$count = count($row);
	
	if ($count){
		
		$empresas = array();
		
		foreach ($row as $item){
			$empresas[] = $item['EXUXPEmpresa'];
		}
		
		if (in_array($EmpresaId, $empresas)){
			echo 1; //"existeNessaEmpresa";
		} else {			
			echo json_encode($row);
		}
	} else{
		echo 0;
	}			 
}

?>
