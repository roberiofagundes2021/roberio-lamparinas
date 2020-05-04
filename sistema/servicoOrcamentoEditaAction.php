<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "UPDATE ServicoOrcamento SET SrOrcNome = :sNome,  SrOrcDetalhamento = :sDetalhamento, SrOrcCategoria = :iCategoria, 
				SrOrcSubcategoria = :iSubCategoria, SrOrcUsuarioAtualizador = :iUsuarioAtualizador, SrOrcUnidade = :iUnidade
				WHERE SrOrcId = :sId ";
		$result = $conn->prepare($sql);

		$result->execute(array(
						':sId' => $_POST['inputId'],
						':sNome' => $_POST['inputNome'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Serviço alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar serviço!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error2: ' . $e->getMessage();die;
		
	}
	
	irpara("servicoOrcamento.php");
} 

?>