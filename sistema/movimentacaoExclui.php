<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputMovimentacaoId'])){
	
	$iMovimentacao = $_POST['inputMovimentacaoId'];
	
	try{

		$conn->beginTransaction();

		/*----- DELETA MOVIMENTAÇÃO POR PRODUTO -----*/
		$sql = "DELETE FROM MovimentacaoXProduto
				WHERE MvXPrMovimentacao = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iMovimentacao); 
		$result->execute();

		/*----- DELETA MOVIMENTAÇÃO POR SERVICO -----*/
		$sql = "DELETE FROM MovimentacaoXServico
				WHERE MvXSrMovimentacao = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iMovimentacao); 
		$result->execute();
		
		/*----- DELETA MOVIMENTAÇÃO -----*/
		$sql = "DELETE FROM Movimentacao
				WHERE MovimId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iMovimentacao); 
		$result->execute();

		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("movimentacao.php");

?>