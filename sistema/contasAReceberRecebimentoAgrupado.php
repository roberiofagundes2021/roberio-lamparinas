<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

// var_dump($_POST);

if (isset($_POST['valores'])) {
    $dados = $_POST['valores'];
    $idsCont = count($_POST['valores']);

    $retorno = '';
    $strin = '';
    try {
        for ($i = 0; $i < $idsCont; $i++) {
            try {
                // var_dump($dados[$i]);
                $sql1 = "SELECT SituaId
	    	               FROM Situacao
                          WHERE SituaChave = 'RECEBIDA'";

                $result = $conn->query($sql1);
                $situacao = $result->fetch(PDO::FETCH_ASSOC);

                $sql = "UPDATE ContasAReceber 
                           SET CnARePlanoContas         = :iPlanoContas, 
                               CnAReContaBanco          = :iContaBanco, 
                               CnAReFormaPagamento      = :iFormaPagamento, 
                               CnAReNumDocumento        = :sNumDocumento,
                               CnAReDescricao           = :sDescricao, 
                               CnAReDtRecebimento       = :dateDtRecebimento, 
                               CnAReValorRecebido       = :fValorRecebido, 
                               CnAReStatus              = :iStatus, 
                               CnAReUsuarioAtualizador  = :iUsuarioAtualizador, 
                               CnAReUnidade             = :iUnidade                               
                         WHERE CnAReId = " . $dados[$i]['id'] . "";

                $result = $conn->prepare($sql);
                $result->execute(array(
                    ':iPlanoContas'         => $dados[$i]['planoContas'],
                    ':iContaBanco'          => $_POST['contaBanco'],
                    ':iFormaPagamento'      => $_POST['formaPagamento'],
                    ':sNumDocumento'        => $_POST['numeroDocumento'],
                    ':sDescricao'           => $dados[$i]['descricao'],
                    ':dateDtRecebimento'    => $_POST['dataRecebimento'],
                    ':fValorRecebido'       => isset($dados[$i]['valor']) ? floatval($dados[$i]['valor']) : null,
                    ':iStatus'              => $situacao['SituaId'],
                    ':iUsuarioAtualizador'  => $_SESSION['UsuarId'],
                    ':iUnidade'             => $_SESSION['UnidadeId']
                ));
            } catch (Exception $e) {
                echo 'Error: ',  $e->getMessage(), "\n";
            }

            if ($i == 0) {
                $retorno .= $strin . '' . $dados[$i]['id'];
            } else {
                $retorno .= $strin . '/' . $dados[$i]['id'];
            }
        }

        echo $retorno;
    } catch (PDOException $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
    }
}
