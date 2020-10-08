<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

if(isset($_POST['numParcelas'])){

    $sql = "SELECT * 
            FROM ContasAPagar
            WHERE CnAPaId = ".$_POST['idConta']." and CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
    ";
    $result = $conn->query($sql);
    $conta = $result->fetch(PDO::FETCH_ASSOC);

    $numParcelas = intVal($_POST['numParcelas']);
    $parcelas = json_decode($_POST['parcelas'], true);

    var_dump($parcelas);
            
    for($i = 0; $i <= $numParcelas - 1; $i++){
        $sql = "SELECT SituaId
        FROM Situacao
        WHERE SituaChave = 'APAGAR'
        ";
        $result = $conn->query($sql);
        $situacao = $result->fetch(PDO::FETCH_ASSOC);
    
        $sql = "INSERT INTO ContasAPagar ( CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento,
                                      CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                      CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                VALUES ( :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento,:sNumDocumento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                        :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :iStatus, :iUsuarioAtualizador, :iUnidade)";
        $result = $conn->prepare($sql);

        $result->execute(array(
            
                ':iPlanoContas' => $conta['CnAPaPlanoContas'],
                ':iFornecedor' => $conta['CnAPaFornecedor'],
                ':iContaBanco' => $conta['CnAPaContaBanco'],
                ':iFormaPagamento' => $conta['CnAPaFormaPagamento'],
                ':sNumDocumento' => $conta['CnAPaNumDocumento'],
                ':sNotaFiscal' => $conta['CnAPaNotaFiscal'],
                ':dateDtEmissao' => $conta['CnAPaDtEmissao'],
                ':iOrdemCompra' => $conta['CnAPaOrdemCompra'],
                ':sDescricao' => $parcelas[$i]['descricao'],
                ':dateDtVencimento' => $conta['CnAPaDtVencimento'],
                ':fValorAPagar' => floatval(str_replace(',', '.', $parcelas[$i]['valor'])),
                ':dateDtPagamento' => $parcelas[$i]['vencimento'],
                ':fValorPago' => null,
                ':sObservacao' => $conta['CnAPaObservacao'],
                ':iStatus' => $conta['CnAPaStatus'],
                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iUnidade' => $_SESSION['UnidadeId']
        ));
    }
}

?>