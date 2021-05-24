<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputComissaoAnexoID'])){
	
	$iComissaoAnexoID = $_POST['inputComissaoAnexoID'];
	$sArquivo = $_POST['inputComissaoAnexoArquivo'];
	$sPasta = 'global_assets/anexos/comissao/';

	try{
		
		$conn->beginTransaction();

		$sql = "
				DELETE 
				FROM TRXComissao
				WHERE TRXCoId = :id
			";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iComissaoAnexoID);
		$result->execute();

		if (file_exists($sPasta.$sArquivo) and $sArquivo <> ""){
			unlink($sPasta.$sArquivo);
		}

		$conn->commit();
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Anexo excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$conn->rollback();
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Anexo!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("trComissao.php");

?>
