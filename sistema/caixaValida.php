<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nome'])){
	$sql = "SELECT CaixaId
		    FROM Caixa
		    WHERE CaixaUnidade = ".$_SESSION['UnidadeId']." and CaixaNome = '".$_POST['nome']."'";
}
		   
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	if(isset($_POST['caixaId'])){
        foreach ($row as $Caixa) {
	        if($Caixa['CaixaId'] == $_POST['caixaId']){
		       echo 0;
	        } else {
               echo 1;
	        }
	    }
	} else {
		echo 1;
	}
} else{
	echo 0;
}

?>
