<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if(isset($_POST['inputOrdemCompraId'])){
	
	try{

		$conn->beginTransaction();

		$sql = "SELECT SituaId
				FROM Situacao	
				WHERE SituaChave = '".$_POST['inputOrdemCompraStatus']."'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);        	

		if ($_POST['inputOrdemCompraStatus'] == 'NAOLIBERADO'){
			$motivo = $_POST['inputMotivo'];
		} else{
			$motivo = NULL;
		}
		
		$sql = "UPDATE OrdemCompra SET OrComSituacao = :bStatus, OrComUsuarioAtualizador = :iUsuario
				WHERE OrComId = :iOrdemCompra";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iOrdemCompra', $_POST['inputOrdemCompraId']);
		$result->execute();
		
		$sql = "UPDATE Bandeja SET BandeStatus = :bStatus, BandeMotivo = :sMotivo, BandeUsuarioAtualizador = :iUsuario
				WHERE BandeId = :iBandeja";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $row['SituaId']);
		$result->bindParam(':sMotivo', $motivo);
		$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
		$result->bindParam(':iBandeja', $_POST['inputBandejaId']);
		$result->execute();

		$sql = "SELECT ParamValorAtualizadoOrdemCompra
				FROM Parametro
				WHERE ParamEmpresa = " . $_SESSION['EmpreId'];
		$result = $conn->query($sql);
		$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

		//Se o parâmetro diz que o Valor do Produto/Serviço será atualizado a partir da Ordem de Compra, tais valores devem ser atualizados		
		if ($rowParametro['ParamValorAtualizadoOrdemCompra']){
			
			$sql = "SELECT OCXPrProduto, OCXPrValorUnitario 
					FROM OrdemCompraXProduto
					WHERE OCXPrOrdemCompra = '".$_POST['inputOrdemCompraId']."'";
			$result = $conn->query($sql);
			$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);  

			foreach ($rowProduto as $item){

				$sql = "UPDATE Produto SET ProduValorCusto = :fValor, ProduUsuarioAtualizador = :iUsuario
						WHERE ProduId = :iProduto";
				$result = $conn->prepare($sql);
				$result->bindParam(':fValor', $item['OCXPrValorUnitario']);
				$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
				$result->bindParam(':iProduto', $item['OCXPrProduto']);
				$result->execute();
			}

			$sql = "SELECT OCXSrServico, OCXSrValorUnitario 
					FROM OrdemCompraXServico
					WHERE OCXSrOrdemCompra = '".$_POST['inputOrdemCompraId']."'";
			$result = $conn->query($sql);
			$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);  

			foreach ($rowServico as $item){

				$sql = "UPDATE Servico SET ServiValorCusto = :fValor, ServiUsuarioAtualizador = :iUsuario
						WHERE ServiId = :iServico";
				$result = $conn->prepare($sql);
				$result->bindParam(':fValor', $item['OCXSrValorUnitario']);
				$result->bindParam(':iUsuario', $_SESSION['UsuarId']);
				$result->bindParam(':iServico', $item['OCXSrServico']);
				$result->execute();
			}			
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Situação da ordem de ompra alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar situação da ordem compra !!!";
		$_SESSION['msg']['tipo'] = "error";
		
		echo 'Error: ' . $e->getMessage();
	}
}

irpara("index.php");

?>
