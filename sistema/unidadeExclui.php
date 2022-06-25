<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputUnidadeId'])){        	
	try{
		$conn->beginTransaction();

		$iUnidade = $_POST['inputUnidadeId'];

		$sql = "DELETE FROM PadraoPerfilXPermissao WHERE PaPrXPeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM PerfilXPermissao WHERE PrXPeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM Perfil WHERE PerfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM LocalEstoque WHERE LcEstUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM FormaPagamento WHERE FrPagUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM GrupoConta WHERE GrConUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM AtendimentoClassificacao WHERE AtClaId = $iUnidade";
		$result = $conn->prepare($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);
		
		$sql = "DELETE FROM Unidade WHERE UnidaId = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC); 
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Não é possível excluir essa unidade, pois existem registros ligados a ela!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		//echo 'Error: ' . $e->getMessage();
	}
}

irpara("unidade.php");

?>
