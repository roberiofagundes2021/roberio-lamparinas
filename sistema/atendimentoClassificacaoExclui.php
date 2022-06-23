<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputAtendimentoClassificacaoId'])){
	
	$iAtendimentoClassificacao = $_POST['inputAtendimentoClassificacaoId'];
        	
	try{
		
		$sql = "DELETE FROM AtendimentoClassificacao
				WHERE AtClaId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iAtendimentoClassificacao);
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Classificação do atendimento excluído!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao excluir classificação do atendimento!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("atendimentoClassificacao.php");

?>
