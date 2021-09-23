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
		/* Fim Atualiza */

		$sql = "SELECT  MovimId, MovimNumRecibo, MovimTipo, MovimData, MovimFinalidade, MovimOrigemLocal, MovimOrigemSetor, MovimDestinoLocal,
			            MovimDestinoSetor, MovimDestinoManual, MovimObservacao, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie,
					    MovimValorTotal, MovimChaveAcesso, MovimFornecedor, MovimMotivo, MovimSituacao, MovimUnidade, MovimUsuarioAtualizador
				FROM Movimentacao
				WHERE MovimId = ".$iMovimentacao;
		$result = $conn->query($sql);
		$rowMovimentacao = $result->fetch(PDO::FETCH_ASSOC);


		/* Insere na Tabela Contas a Pagar */
		
        $sql = "INSERT INTO ContasAPagar ( CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento,
                                            CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                            CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaTipoJuros, CnAPaJuros, 
                                            CnAPaTipoDesconto, CnAPaDesconto, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                VALUES ( :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento,:sNumDocumento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                        :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :sTipoJuros, :fJuros, 
                        :sTipoDesconto, :fDesconto, :iStatus, :iUsuarioAtualizador, :iUnidade)";
        $result = $conn->prepare($sql);
                
        $result->execute(array(
            ':iPlanoContas' => null,
            ':iFornecedor' => $rowMovimentacao['MovimFornecedor'],
            ':iContaBanco' => null,
            ':iFormaPagamento' => null,
            ':sNumDocumento' => $rowMovimentacao['MovimNumRecibo'],
            ':sNotaFiscal' => $rowMovimentacao['MovimNotaFiscal'],
            ':dateDtEmissao' => $rowMovimentacao['MovimDataEmissao'], //Se for Data da Liquidação ficará assim: date('Y-m-d')
            ':iOrdemCompra' => $rowMovimentacao['MovimOrdemCompra'],
            ':sDescricao' => 'Pagamento da NF '.$rowMovimentacao['MovimNotaFiscal'], // Ver com Valma
            ':dateDtVencimento' => date('Y-m-d', strtotime('+30 days', $rowMovimentacao['MovimDataEmissao'])), //Se for Data Liquidação ficará assim: date('Y-m-d', strtotime('+30 days'))
            ':fValorAPagar' => $rowMovimentacao['MovimValorTotal'],
            ':dateDtPagamento' => null,
            ':fValorPago' => null,
            ':sObservacao' => null,
            ':sTipoJuros' => null,
            ':fJuros' =>  null,
            ':sTipoDesconto' =>  null,
            ':fDesconto' => null,
            ':iStatus' => $rowSituacao['SituaId'], // Trocar para A Pagar
            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
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
