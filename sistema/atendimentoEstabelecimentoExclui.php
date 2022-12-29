<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputEstabId'])){
	
	$iEstabelecimento = $_POST['inputEstabId'];
        	
	try{
		
		$sql = "DELETE FROM Estabelecimento
				WHERE EstabId = :id";	
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iEstabelecimento);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Relação de Estabelecimento excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir Estabelecimento!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("atendimentoEstabelecimento.php");

?>
