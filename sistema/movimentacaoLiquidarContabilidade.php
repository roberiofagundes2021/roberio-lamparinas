<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Contas a Pagar ';

include('global_assets/php/conexao.php');

try{
	if(isset($_POST['inputMovimentacaoId'])){
	
		$conn->beginTransaction();
		
		$iMovimentacao = $_POST['inputMovimentacaoId'];
        

		/* Atualiza o Status da Movimentação para "Liberado Contabilidade" */
		$sql = "SELECT SituaId
				FROM Situacao
				WHERE SituaChave = 'LIBERADOCONTABILIDADE' ";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "UPDATE Movimentacao SET MovimSituacao = :iStatus
				WHERE MovimId = :iMovimentacao";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iStatus' => $rowSituacao['SituaId'],
			':iMovimentacao' => $iMovimentacao					
		));

        /* Capturando dados para Update */
		$iStatus = intval($rowSituacao['SituaId']);
        
        /* Atualiza status bandeja */
	 	$sql = " UPDATE Bandeja SET BandeStatus = :iStatus
                 WHERE BandeUnidade = :iUnidade AND BandeId in (Select BandeId FROM Bandeja 
                 WHERE BandeTabelaId = :iMovimentacao and BandePerfil = 'CONTABILIDADE')";
        $result = $conn->prepare($sql);
        $result->bindParam(':iStatus', $iStatus);
        $result->bindParam(':iUnidade', $_SESSION['UnidadeId']);
        $result->bindParam(':iMovimentacao', $iMovimentacao);
        $result->execute();

        /* Status do Contas Apagar*/
        $sql = "SELECT SituaId
				FROM Situacao
				WHERE SituaChave = 'APAGAR' ";
		$result = $conn->query($sql);
		$rowSituaChave = $result->fetch(PDO::FETCH_ASSOC);
		/* Fim Atualiza */

		$sql = "SELECT  MovimId, MovimNumRecibo, MovimTipo, MovimData, MovimFinalidade, MovimOrigemLocal, MovimOrigemSetor, MovimDestinoLocal,
			            MovimDestinoSetor, MovimDestinoManual, MovimObservacao, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie,
					    MovimValorTotal, MovimChaveAcesso, MovimFornecedor, MovimMotivo, MovimSituacao, MovimUnidade, MovimUsuarioAtualizador
				FROM Movimentacao
				WHERE MovimId = ".$iMovimentacao;
		$result = $conn->query($sql);
		$rowMovimentacao = $result->fetch(PDO::FETCH_ASSOC);


		/* Insere na Tabela Contas a Pagar */
		
        $sql = "INSERT INTO ContasAPagar ( CnAPaMovimentacao, CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento,
                                            CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                            CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaTipoJuros, CnAPaJuros, 
                                            CnAPaTipoDesconto, CnAPaDesconto, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                VALUES ( :iMovimentacao, :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento,:sNumDocumento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                        :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :sTipoJuros, :fJuros, 
                        :sTipoDesconto, :fDesconto, :iStatus, :iUsuarioAtualizador, :iUnidade)";
        $result = $conn->prepare($sql);
                
        $result->execute(array(
            ':iMovimentacao' => $iMovimentacao,
            ':iPlanoContas' => null,
            ':iFornecedor' => $rowMovimentacao['MovimFornecedor'],
            ':iContaBanco' => null,
            ':iFormaPagamento' => null,
            ':sNumDocumento' => $rowMovimentacao['MovimNumRecibo'],
            ':sNotaFiscal' => $rowMovimentacao['MovimNotaFiscal'],
            ':dateDtEmissao' => date('Y-m-d') , //Se for Data da Liquidação ficará assim: $rowMovimentacao['MovimDataEmissao']
            ':iOrdemCompra' => $rowMovimentacao['MovimOrdemCompra'],
            ':sDescricao' => 'Pagamento da NF '.$rowMovimentacao['MovimNotaFiscal'], // Ver com Valma
            ':dateDtVencimento' => date('Y-m-d', strtotime('+60 days')), //Se for Data Liquidação ficará assim: date('Y-m-d', strtotime('+60 days', $rowMovimentacao['MovimDataEmissao']))
            ':fValorAPagar' => $rowMovimentacao['MovimValorTotal'],
            ':dateDtPagamento' => null,
            ':fValorPago' => null,
            ':sObservacao' => null,
            ':sTipoJuros' => null,
            ':fJuros' =>  null,
            ':sTipoDesconto' =>  null,
            ':fDesconto' => null,
            ':iStatus' => $rowSituaChave['SituaId'],
            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
            ':iUnidade' => $_SESSION['UnidadeId']
        ));     
        
        /* Insere na Tabela Movimentacao Liquidacao */
		
        $sql = "INSERT INTO MovimentacaoLiquidacao ( MvLiqMovimentacao, MvLiqData, MvLiqUsuario,  MvLiqUnidade )
                VALUES ( :iMovimentacao, :dateData, :iUsuario, :iUnidade)";
        $result = $conn->prepare($sql);
       
        $result->execute(array(
            ':iMovimentacao' => $iMovimentacao,
            ':dateData' => date('Y-m-d'), 
            ':iUsuario' => $_SESSION['UsuarId'],
            ':iUnidade' => $_SESSION['UnidadeId']
        ));     
        
        /* Fim Insere ContasAPagar */		

		$conn->commit();
        
		$_SESSION['msg']['titulo'] 		= "Sucesso";
		$_SESSION['msg']['mensagem'] 	= "Movimentação Liquidada!!!";
		$_SESSION['msg']['tipo'] 		= "success";      		
	}

} catch(PDOException $e){

    $conn->rollback();
		
    $_SESSION['msg']['titulo'] 		= "Erro";
    $_SESSION['msg']['mensagem'] 	= "Erro ao Liquidar a Movimentação!!!";
    $_SESSION['msg']['tipo'] 		= "error";	

    echo 'Error1: ' . $e->getMessage();
}

irpara("movimentacao.php");

?>
