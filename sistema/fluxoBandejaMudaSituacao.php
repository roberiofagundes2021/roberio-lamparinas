<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputFluxoId'])){
	
	$sql = "SELECT SituaId
			FROM Situacao	
			WHERE SituaChave = '".$_POST['inputFluxoStatus']."'";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
        	
	try{

		$conn->beginTransaction();
		
		$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :bStatus, FlOpeUsuarioAtualizador = :iUsuario
				WHERE FlOpeId = :iFluxo";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iFluxo', $_POST['inputFluxoId']);
		$result->execute();
		
		$sql = "UPDATE Bandeja SET BandeStatus = :bStatus, BandeMotivo = :sMotivo, BandeUsuarioAtualizador = :iUsuario
				WHERE BandeId = :iBandeja";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':sMotivo', $_POST['inputMotivo']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iBandeja', $_POST['inputBandejaId']);
		$result->execute();

		$sql = "SELECT ParamValorAtualizadoFluxo
				FROM Parametro
				WHERE ParamEmpresa = " . $_SESSION['EmpreId'];
		$result = $conn->query($sql);
		$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

		//Se o parâmetro diz que o Valor do Produto/Serviço será atualizado a partir da Ordem de Compra, tais valores devem ser atualizados		
		if ($rowParametro['ParamValorAtualizadoFluxo']){
			
			$sql = "SELECT FOXPrProduto, FOXPrValorUnitario 
					FROM FluxoOperacionalXProduto
					WHERE FOXPrFluxoOperacional = '".$_POST['inputFluxoId']."'";
			$result = $conn->query($sql);
			$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);  

			foreach ($rowProduto as $item){

				$sql = "UPDATE Produto SET ProduValorCusto = :fValor, ProduUsuarioAtualizador = :iUsuario
						WHERE ProduId = :iProduto";
				$result = $conn->prepare($sql);
				$result->bindParam(':fValor', $item['FOXPrValorUnitario']);
				$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
				$result->bindParam(':iProduto', $item['FOXPrProduto']);
				$result->execute();
			}

			$sql = "SELECT FOXSrServico, FOXSrValorUnitario 
					FROM FluxoOperacionalXServico
					WHERE FOXSrFluxoOperacional = '".$_POST['inputFluxoId']."'";
			$result = $conn->query($sql);
			$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);  

			foreach ($rowServico as $item){

				$sql = "UPDATE Servico SET ServiValorCusto = :fValor, ServiUsuarioAtualizador = :iUsuario
						WHERE ServiId = :iServico";
				$result = $conn->prepare($sql);
				$result->bindParam(':fValor', $item['FOXSrValorUnitario']);
				$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
				$result->bindParam(':iServico', $item['FOXSrServico']);
				$result->execute();
			}			
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação do fluxo operacional alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação do fluxo operacional!!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("index.php");

?>
