<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['acomodacaoVelho'])){
	$sql = "SELECT VnLeiId
			 FROM VincularLeito
			 WHERE VnLeiUnidade = ".$_SESSION['UnidadeId']." and VnLeiTipoAcomodacao = '". $_POST['acomodacaoNovo']."' and VnLeiTipoAcomodacao <> '". $_POST['acomodacaoVelho']."'";
} else{
	$sql = "SELECT VnLeiId
			 FROM VincularLeito
			 WHERE VnLeiUnidade = ".$_SESSION['UnidadeId']." and VnLeiTipoAcomodacao = '". $_POST['acomodacao']."'";
}
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo 1;
} else{

	if ($_POST['estadoAtual'] == 'EDITA'){
		echo "EDITA";
	} else{
		echo 0;
	}
}

?>
