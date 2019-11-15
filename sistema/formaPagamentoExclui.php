<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputFormaPagamentoId'])){
	
	$iFormaPagamento = $_POST['inputFormaPagamentoId'];
        	
	try{
		
		$sql = "DELETE FROM FormaPagamento
				WHERE FrPagId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iFormaPagamento); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "FormaPagamento excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir FormaPagamento!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("FormaPagamento.php");

?>
