<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$tipo = $_POST['inputTipo'];
$aberturaCaixaId = $_POST['inputAberturaCaixaId'];
$dataHora = date('Y-m-d H:i:s');
$valorRetirado = $_POST['inputValorRetirado'];
$justificativaRetirada = $_POST['inputJustificativaRetirada'];
$formaPagamento = $_POST['inputFormaPagamento'];
$planoContas = isset($_POST['inputPlanoContas']) ? $_POST['inputPlanoContas'] : null;
$centroCustos = isset($_POST['inputCentroCustos']) ? $_POST['inputCentroCustos'] : null;
$fornecedor = isset($_POST['inputFornecedor']) ? $_POST['inputFornecedor'] : null;

//Consulta do saldo do atual que está na abertura do caixa
$sql = "SELECT CxAbeTotalRecebido, CxAbeTotalPago
        FROM CaixaAbertura
        WHERE CxAbeId = $aberturaCaixaId";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$saldoAtual = $row['CxAbeTotalRecebido'] - $row['CxAbeTotalPago'];

if($saldoAtual > $valorRetirado) {
        try{
                $conn->beginTransaction();

                $sql = "SELECT SituaId
                        FROM Situacao
                        WHERE SituaChave = 'PAGO'";
                $result = $conn->query($sql);
                $situacao = $result->fetch(PDO::FETCH_ASSOC);

                if($tipo == 'SANGRIA') {
                        $sql = "INSERT INTO CaixaPagamento (CxPagCaixaAbertura, CxPagDataHora, CxPagValor, CxPagJustificativaRetirada, CxPagFormaPagamento, CxPagStatus, CxPagUnidade)
                                VALUES (:iCaixaAbertura, :sDataHora, :fValor, :sJustificativaRetirada, :iFormaPagamento, :bStatus, :iUnidade)";
                        $result = $conn->prepare($sql);
                                
                        $result->execute(array(
                                ':iCaixaAbertura' => $aberturaCaixaId,
                                ':sDataHora' => $dataHora,
                                ':fValor' => $valorRetirado,
                                ':sJustificativaRetirada' => $justificativaRetirada,
                                ':iFormaPagamento' => $formaPagamento,
                                ':bStatus' => $situacao['SituaId'],
                                ':iUnidade' => $_SESSION['UnidadeId'],
                        ));
                }else {
                        $sql = "INSERT INTO CaixaPagamento (CxPagCaixaAbertura, CxPagDataHora, CxPagValor, CxPagJustificativaRetirada, CxPagFormaPagamento, 
                                                           CxPagPlanoConta, CxPagCentroCusto, CxPagFornecedor, CxPagStatus, CxPagUnidade)
                                VALUES (:iCaixaAbertura, :sDataHora, :fValor, :sJustificativaRetirada, :iFormaPagamento, :iPlanoContas, 
                                        :iCentroCustos, :iFornecedor, :bStatus, :iUnidade)";
                        $result = $conn->prepare($sql);
                                
                        $result->execute(array(
                                ':iCaixaAbertura' => $aberturaCaixaId,
                                ':sDataHora' => $dataHora,
                                ':fValor' => $valorRetirado,
                                ':sJustificativaRetirada' => $justificativaRetirada,
                                ':iFormaPagamento' => $formaPagamento,
                                ':iPlanoContas' => $planoContas,
                                ':iCentroCustos' => $centroCustos,
                                ':iFornecedor' => $fornecedor,
                                ':bStatus' => $situacao['SituaId'],
                                ':iUnidade' => $_SESSION['UnidadeId'],
                        ));	
                }
                

                $valorRetirado = $row['CxAbeTotalPago'] + $valorRetirado;
                
                $sql = "UPDATE CaixaAbertura SET CxAbeTotalPago = :fValorRecebido
                        WHERE CxAbeId = :iCaixaAberturaId";
                $result = $conn->prepare($sql);
                
                $result->execute(array(
                        ':fValorRecebido' => $valorRetirado,
                        ':iCaixaAberturaId' => $aberturaCaixaId 
                ));
                
                $resposta = 'Foi';

                $conn->commit();
                            
        } catch(PDOException $e) {

                $conn->rollback();	
        
                echo 'Error: ' . $e->getMessage();
        }
}else {
        $resposta = 'Impossivel retirar';
}

print(json_encode($resposta));
?>