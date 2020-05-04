<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "UPDATE ProdutoOrcamento SET PrOrcNome = :sNome,  PrOrcDetalhamento = :sDetalhamento, PrOrcCategoria = :iCategoria, 
				PrOrcSubcategoria = :iSubCategoria, PrOrcUnidadeMedida = :iUnidadeMedida, PrOrcUsuarioAtualizador = :iUsuarioAtualizador, 
				PrOrcUnidade = :iUnidade 
				WHERE PrOrcId = :sId ";
		$result = $conn->prepare($sql);

		$result->execute(array(
						':sId' => $_POST['inputId'],
						':sNome' => $_POST['inputNome'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':iUnidadeMedida' => $_POST['cmbUnidadeMedida'] == '#' ? null : $_POST['cmbUnidadeMedida'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Produto alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar produto!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error2: ' . $e->getMessage();die;
		
	}
	
	irpara("produtoOrcamento.php");
} 

?>