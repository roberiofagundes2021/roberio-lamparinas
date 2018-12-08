<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputSubCategoriaId'])){
	
	$iSubCategoria = $_POST['inputSubCategoriaId'];
	$bStatus = $_POST['inputSubCategoriaStatus'] ? 0 : 1;
        	
	try{
		
		$sql = "UPDATE SubCategoria SET SbCatStatus = :bStatus
				WHERE SbCatId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':bStatus', $bStatus); 
		$result->bindParam(':id', $iSubCategoria); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da sub categoria alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da sub categoria!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("subcategoria.php");

?>
