<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputEmpresaId'])){
	
	$iEmpresa = $_POST['inputEmpresaId'];
        	
	try{
		
		$sql = "DELETE FROM Empresa
				WHERE EmpreId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iEmpresa); 
		$result->execute();
		
		$_SESSION['msg'] = "Empresa excluída com sucesso!!!";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg'] = "Erro ao excluir empresa!!!";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("empresa.php");

?>
