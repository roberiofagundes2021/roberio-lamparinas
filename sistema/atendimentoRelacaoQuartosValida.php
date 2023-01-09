<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');


$sql = "SELECT QuartId
        FROM Quarto
        WHERE QuartUnidade = ".$_SESSION['UnidadeId']." 
		and QuartNome = '". $_POST['nome']."' 
		and QuartAla = '". $_POST['ala']."' 
		and QuartTipoInternacao = '". $_POST['tipoInternacao']."'";

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
