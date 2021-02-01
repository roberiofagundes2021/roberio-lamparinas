<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputTRId'])){
	
	$iTR = $_POST['inputTRId'];
	$iUsuario = $_POST['inputUsuarioId'];
	$iUnidade = $_SESSION['UnidadeId'];
        	
	try{
		$conn->beginTransaction();		  
		
		$sql = "DELETE FROM TRXEquipe
				WHERE TRXEqTermoReferencia = :iTr and TRXEqUsuario = :iUsuario";
		$result = $conn->prepare($sql);
		$result->bindParam(':iTr', $iTR);
		$result->bindParam(':iUsuario', $iUsuario);
		$result->execute();
		
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Membro excluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
			
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir membro!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		$conn->rollback();
		
		echo 'Error: ' . $e->getMessage();die;
	}
}

irpara("trComissao.php");

?>
