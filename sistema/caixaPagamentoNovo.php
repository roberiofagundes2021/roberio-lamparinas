<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$aberturaCaixaId = $_POST['inputAberturaCaixaId'];
$dataHora = date('Y-m-d H:i:s');
$valorRetirado = $_POST['inputValorRetirado'];
$justificativa = $_POST['inputJustificativa'];
$formaPagamento = $_POST['inputFormaPagamento'];

//Consulta o saldo de recebimento atual que está na abertura do caixa
$sql = "SELECT CxAbeTotalRecebido, CxAbeTotalPago
        FROM CaixaAbertura
        WHERE CxAbeId = $aberturaCaixaId";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$valorRecebido = $row['CxAbeTotalRecebido'];

if($valorRecebido > $valorRetirado) {
        $valorRetirado = $row['CxAbeTotalPago'] + $valorRetirado;

        try{
                $conn->beginTransaction();

                $sql = "SELECT SituaId
                        FROM Situacao
                        WHERE SituaChave = 'PAGO'";
                $result = $conn->query($sql);
                $situacao = $result->fetch(PDO::FETCH_ASSOC);
                
                $sql = "INSERT INTO CaixaPagamento (CxPagCaixaAbertura, CxPagDataHora, CxPagValor, CxPagJustificativa, CxPagFormaPagamento, CxPagStatus, CxPagUnidade)
                        VALUES (:iCaixaAbertura, :sDataHora, :fValor, :sJustificativa, :iFormaPagamento, :bStatus, :iUnidade)";
                $result = $conn->prepare($sql);
                        
                $result->execute(array(
                        ':iCaixaAbertura' => $aberturaCaixaId,
                        ':sDataHora' => $dataHora,
                        ':fValor' => $valorRetirado,
                        ':sJustificativa' => $justificativa,
                        ':iFormaPagamento' => $formaPagamento,
                        ':bStatus' => $situacao['SituaId'],
                        ':iUnidade' => $_SESSION['UnidadeId'],
                ));	
                
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