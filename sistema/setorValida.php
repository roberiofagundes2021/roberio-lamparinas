<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nomeVelho']) and $_POST['nomeVelho'] <> $_POST['nomeNovo']){
	$sql = ("SELECT SetorId
			 FROM Setor
			 WHERE SetorEmpresa = ".$_SESSION['EmpresaId']." and SetorNome = '". $_POST['nomeNovo']."' and SetorNome <> '". $_POST['nomeVelho']."' and SetorUnidade = ".$_POST['unidade']);
} else if ($_POST['nomeNovo']) {
	$sql = ("SELECT SetorId
			 FROM Setor
			 WHERE SetorEmpresa = ".$_SESSION['EmpresaId']." and SetorNome = '". $_POST['nomeNovo']."' and SetorUnidade = '". $_POST['unidade']."'");
} else {
	$sql = ("SELECT SetorId
			 FROM Setor
			 WHERE SetorEmpresa = ".$_SESSION['EmpresaId']." and SetorNome = '". $_POST['nome']."' and SetorUnidade = '". $_POST['unidade']."'");
}
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
} else{
	echo 0;
}

?>
