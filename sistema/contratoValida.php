<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['termoReferencia'])){
	$sql = "SELECT FlOpeId
		    FROM FluxoOperacional
            JOIN FluxoOperacionalXSubCategoria on FOXSCFluxo = FlOpeId
		    WHERE FlOpeUnidade = ".$_SESSION['UnidadeId']." and FlOpeTermoReferencia = '".$_POST['termoReferencia']."' and FOXSCSubCategoria = '".$_POST['subCategoria']."'";
}
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	if(isset($_POST['fluxoOperacionalId'])){
        foreach ($row as $FluxoOperacinal) {
	        if($FluxoOperacinal['FlOpeId'] == $_POST['fluxoOperacionalId']){
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
