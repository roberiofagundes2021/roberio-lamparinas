<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputUnidadeId'])){
	
	$iUnidade = $_POST['inputUnidadeId'];
        	
	try{
		$sql = "DELETE FROM PadraoPerfilXPermissao WHERE PaPrXPeUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM PerfilXPermissao WHERE PrXPeUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM Perfil WHERE PerfiUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM LocalEstoque WHERE LcEstUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM FormaPagamento WHERE FrPagUnidade = $iUnidade";
		$conn->query($sql);
		
		$sql = "DELETE FROM Unidade
				WHERE UnidaId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':id', $iUnidade); 
		$result->execute();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade excluída!!!";
		$_SESSION['msg']['tipo'] = "success";		
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Não é possível excluir essa unidade, pois existem registros ligados a ela!!!";
		$_SESSION['msg']['tipo'] = "error";			
		
		//echo 'Error: ' . $e->getMessage();
	}
}

irpara("unidade.php");

?>
