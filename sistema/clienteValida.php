<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if ($_POST['tipo'] == 'F'){

	if(isset($_POST['nomeVelho'])){
		$sql = "SELECT ClienId
				FROM Cliente
				WHERE ClienUnidade = ".$_SESSION['UnidadeId']." and ClienNome = '". $_POST['nomeNovo']."' and ClienNome <> '". $_POST['nomeVelho']."' and ClienCpf = '". limpaCPF_CNPJ($_POST['cpf'])."'";
	} else{
		$sql = "SELECT ClienId
				FROM Cliente
				WHERE ClienUnidade = ".$_SESSION['UnidadeId']." and ClienNome = '". $_POST['nome']."' and ClienCpf = '". limpaCPF_CNPJ($_POST['cpf'])."'";
	}
} else{

	if(isset($_POST['nomeVelho'])){
		$sql = "SELECT ClienId
				FROM Cliente
				WHERE ClienUnidade = ".$_SESSION['UnidadeId']." and ClienNome = '". $_POST['nomeNovo']."' and  ClienNome <> '". $_POST['nomeVelho']."' and ClienCnpj = '". limpaCPF_CNPJ($_POST['cnpj'])."'";
	} else{
		$sql = "SELECT ClienId
				FROM Cliente
				WHERE ClienUnidade = ".$_SESSION['UnidadeId']." and ClienNome = '". $_POST['nome']."' and ClienCnpj = '". limpaCPF_CNPJ($_POST['cnpj'])."'";
	}
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
