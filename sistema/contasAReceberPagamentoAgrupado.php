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
	    	            WHERE SituaChave = 'PAGA'";
            $result = $conn->query($sql1);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
    
            $sql = "UPDATE ContasAPagar SET CnAPaPlanoContas = :iPlanoContas, CnAPaContaBanco = :iContaBanco, CnAPaFormaPagamento = :iFormaPagamento, CnAPaNumDocumento = :sNumDocumento,
                                            CnAPaDescricao = :sDescricao, CnAPaDtPagamento = :dateDtPagamento, CnAPaValorPago = :fValorPago, CnAPaStatus = :iStatus, CnAPaUsuarioAtualizador = :iUsuarioAtualizador, CnAPaUnidade = :iUnidade
		    		WHERE CnAPaId = ".$dados[$i]['id']."";
		    $result = $conn->prepare($sql);
		    		
		    $result->execute(array(
                                ':iPlanoContas' =>$dados[$i]['planoContas'],
                                ':iContaBanco' => $_POST['contaBanco'],
                                ':iFormaPagamento' => $_POST['formaPagamento'],
                                ':sNumDocumento' => $_POST['numeroDocumento'],
                                ':sDescricao' => $dados[$i]['descricao'],
                                ':dateDtPagamento' => $_POST['dataPagamento'],
                                ':fValorPago' => isset($dados[$i]['valor']) ? floatval($dados[$i]['valor']) : null,
                                ':iStatus' => $situacao['SituaId'],
                                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                                ':iUnidade' => $_SESSION['UnidadeId']
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