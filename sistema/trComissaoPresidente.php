<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Enviar para Aprovação - Centro Administrativo';

include('global_assets/php/conexao.php');

try{
	if(isset($_POST['inputReferenceTermId'])){
		$bIsPresident = $_POST['inputIsPresident'] == 1 ? false : true;
		$iTRXEqTermoRefencia = $_POST['inputReferenceTermId'];
		$iTRXEqUsuario = $_POST['inputUserId'];
		$iTRXEqUnidade = $_POST['inputUnitId'];

		$conn->beginTransaction();

		/* Atualiza status de todos para falso */
		$sql = "
			UPDATE TRXEquipe 
					SET TRXEqPresidente = 0
				WHERE TRXEqTermoReferencia = :iTRXEqTermoRefencia
					AND TRXEqUnidade = :iTRXEqUnidade
		";
		
		$result = $conn->prepare($sql);
		$result->execute(array(
			':iTRXEqTermoRefencia' => $iTRXEqTermoRefencia,
			':iTRXEqUnidade' => $iTRXEqUnidade,
		));

		/* Atualiza status do novo presidente */
		$sql = "
			UPDATE TRXEquipe 
					SET TRXEqPresidente = :bIsPresident
				WHERE TRXEqTermoReferencia = :iTRXEqTermoRefencia
					AND TRXEqUsuario = :iTRXEqUsuario
					AND TRXEqUnidade = :iTRXEqUnidade
		";
		
		$result = $conn->prepare($sql);
		$result->execute(array(
			':bIsPresident' => $bIsPresident,	
			':iTRXEqTermoRefencia' => $iTRXEqTermoRefencia,
			':iTRXEqUsuario' => $iTRXEqUsuario,
			':iTRXEqUnidade' => $iTRXEqUnidade,
		));

		$sql = "INSERT INTO AuditTR ( AdiTRTermoReferencia, AdiTRDataHora, AdiTRUsuario, AdiTRTela, AdiTRDetalhamento)
		VALUES (:iTRTermoReferencia, :iTRDataHora, :iTRUsuario, :iTRTela, :iTRDetalhamento)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
			':iTRTermoReferencia' => $iTRXEqTermoRefencia,
			':iTRDataHora' => date("Y-m-d H:i:s"),
			':iTRUsuario' => $_SESSION['UsuarId'],
			':iTRTela' =>'COMISSÃO DO PROCESSO LICITATÓRIO',
			':iTRDetalhamento' =>'INCLUSÃO DO PRESIDENTE'
		));

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Atualizado status para presidente!!!";
		$_SESSION['msg']['tipo'] = "success";   
	}
} catch(PDOException $e){

	$conn->rollback();
	
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao atualizar o status para presidente!!!";
	$_SESSION['msg']['tipo'] = "error";	

	echo 'Error1: ' . $e->getMessage();

}

irpara("trComissao.php");

?>

