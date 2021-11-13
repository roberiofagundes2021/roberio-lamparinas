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

		// Selecionando o id da Bandeja 
		$sql = "SELECT BandeId 
		FROM Bandeja
		WHERE BandeTabelaId =  ". $iMovimentacao ." and BandeTabela = 'Movimentacao' ";
		$result = $conn->query($sql);
		$Bandeja= $result->fetch(PDO::FETCH_ASSOC);

		/*----- DELETA BANDEJA -----*/
		$sql = "DELETE FROM Bandeja
				WHERE BandeId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $Bandeja['BandeId']); 
		$result->execute();

		
		/*----- DELETA BANDEJA X PERFIL -----*/
		$sql = "DELETE FROM BandejaXPerfil
				WHERE BnXPeBandeja = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $Bandeja['BandeId']); 
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