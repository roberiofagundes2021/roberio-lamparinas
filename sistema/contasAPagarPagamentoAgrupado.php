<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');



if(isset($_POST['values'])){
    $ids = $_POST['values'];
    $idsCont = count($_POST['values']);

    $retorno = '';
    $strin = '';
    try{
        for($i = 0; $i < $idsCont; $i++){
    
            $sql1 = "SELECT SituaId
	    	            FROM Situacao
	    	            WHERE SituaChave = 'PAGA'";
            $result = $conn->query($sql1);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
    
            $sql = "UPDATE ContasAPagar SET CnAPaStatus = :iStatus
	    	        WHERE CnAPaId = ".$ids[$i]."";
            $result = $conn->prepare($sql);
            
            $result->execute(array(
                            ':iStatus' => $situacao['SituaId']
                        ));
            
            if($i == 0){
                $retorno .= $strin.''.$ids[$i];
            } else {
                $retorno .= $strin.'/'.$ids[$i];
            }
        }

        echo $retorno;

    } catch(PDOException $e) {	
        
        echo 'Erro.';

    }
}


?>