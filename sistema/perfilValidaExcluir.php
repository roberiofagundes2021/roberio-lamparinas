<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');
	
$sql = "SELECT UsuarId
		FROM Usuario
		JOIN EmpresaXUsuarioXPerfil ON EXUXPUsuario = UsuarId
		JOIN UsuarioXUnidade ON UsXUnEmpresaUsuarioPerfil = EXUXPId
		JOIN Perfil ON PerfiId = EXUXPPerfil
		WHERE  UsXUnUnidade = ". $_SESSION['UnidadeId'] ." AND PerfiId = ".$_POST['IdPerfil']." 
        ";

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )

if($count){
	echo 1;
} else{
	echo 0;
}
	

?>
