<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputEmpresaId'])){
	
	$iEmpresa = $_POST['inputEmpresaId'];
        	
	try{

		$sql = "DELETE FROM Parametro
				WHERE ParamEmpresa = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iEmpresa); 
		$result->execute();		

		$sql = "DELETE FROM Empresa
				WHERE EmpreId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iEmpresa); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Empresa excluída!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Não é possível excluir essa empresa, pois existem registros ligados a ela.";
		$_SESSION['msg']['tipo'] = "error";	
		
		//echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("empresa.php");

?>
