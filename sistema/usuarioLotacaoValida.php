<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho'])){
	$sql = "SELECT UsXUnEmpresaUsuarioPerfil, UsXUnUnidade, UsXUnSetor, UsXUnLocalEstoque, UsXUnUsuarioAtualizador
			FROM UsuarioXUnidade
			WHERE UsXUnEmpresaUsuarioPerfil = ".$_SESSION['EmpresaId']." and UsXUnUnidade = '". $_POST['nomeNovo']."' and UsXUnUnidade <> '". $_POST['nomeVelho']."'";
} else {
	$sql = "SELECT UsXUnEmpresaUsuarioPerfil, UsXUnUnidade, UsXUnSetor, UsXUnLocalEstoque, UsXUnUsuarioAtualizador
			FROM UsuarioXUnidade
			WHERE UsXUnEmpresaUsuarioPerfil = ".$_SESSION['EmpresaId']." and UsXUnUnidade = '". $_POST['nome']."'";
} 

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
