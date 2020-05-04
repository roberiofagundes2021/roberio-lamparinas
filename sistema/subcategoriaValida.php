<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['nome'])){
	$sql = "SELECT SbCatId
		    FROM SubCategoria
		    WHERE SbCatUnidade = ".$_SESSION['UnidadeId']." and SbCatNome = '".$_POST['nome']."' and SbCatCategoria = '".$_POST['categoria']."'";
}
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

$count = count($row);


//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	if(isset($_POST['subcategoriaId'])){
        foreach ($row as $SubCategoria) {
	        if($SubCategoria['SbCatId'] == $_POST['subcategoriaId']){
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

