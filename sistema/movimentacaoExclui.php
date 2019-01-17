<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputMovimentacaoId'])){
	
	$iMovimentacao = $_POST['inputMovimentacaoId'];
        	
	try{
		
		$sql = "DELETE FROM Movimentacao
				WHERE MovimId = :id";
		$result = $conn->prepare("$sql");
		$result->bindParam(':id', $iMovimentacao); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("movimentacao.php");

?>
