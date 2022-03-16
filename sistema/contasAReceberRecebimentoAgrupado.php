<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

// var_dump($_POST);

if(isset($_POST['valores'])){
    $dados = $_POST['valores'];
    $idsCont = count($_POST['valores']);

    $retorno = '';
    $strin = '';
    try{
        for($i = 0; $i < $idsCont; $i++){
            // var_dump($dados[$i]);
            $sql1 = "SELECT SituaId
	    	            FROM Situacao
	    	            WHERE SituaChave = 'RECEBIDO'";
            $result = $conn->query($sql1);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
    
            $sql = "UPDATE ContasAReceber SET CnARePlanoContas = :iPlanoContas, CnAReContaBanco = :iContaBanco, CnAReFormaPagamento = :iFormaPagamento, CnAReNumDocumento = :sNumDocumento,
                            CnAReDescricao = :sDescricao, CnAReDtRecebimento = :dateDtRecebimento, CnAReValorRecebido = :fValorRecebido, CnAReStatus = :iStatus, CnAReUsuarioAtualizador = :iUsuarioAtualizador, CnAReUnidade = :iUnidade
            WHERE CnAReId = ".$dados[$i]['id']."";

                $result = $conn->prepare($sql);
                $result->execute(array(

                ':iPlanoContas'         => isset($dados[$i]['planoContas']) ? intval($dados[$i]['planoContas']) : null,
                ':iContaBanco'          => isset($_POST['contaBanco']) ? intval($_POST['contaBanco']) : null,
                ':iFormaPagamento'      => isset($_POST['formaPagamento']) ? $_POST['formaPagamento'] : null,
                ':sNumDocumento'        => isset($_POST['numeroDocumento']) ? $_POST['numeroDocumento'] : null,
                ':sDescricao'           => $dados[$i]['descricao'],
                ':dateDtRecebimento'    => $_POST['dataPagamento'],
                ':fValorRecebido'       => isset($dados[$i]['valor']) ? floatval($dados[$i]['valor']) : null,
                ':iStatus'              => intval($situacao['SituaId']),
                ':iUsuarioAtualizador'  => intval($_SESSION['UsuarId']),
                ':iUnidade'             => intval($_SESSION['UnidadeId'])
                ));
            
            if($i == 0){
                $retorno .= $strin.''.$dados[$i]['id'];
            } else {
                $retorno .= $strin.'/'.$dados[$i]['id'];
            }
        }

        echo $retorno;

    } catch(PDOException $e) {	
        
        echo 'Erro.';

    }
}


?>