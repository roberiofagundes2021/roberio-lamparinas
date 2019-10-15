<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');
if(isset($_POST['inputNome'])){
	
	try{		
		$sql = "SELECT COUNT(isnull(ProduCodigo,0)) as Codigo
				FROM Produto
				Where ProduEmpresa = ".$_SESSION['EmpreId']."";
		//echo $sql;die;
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);	
		
		$sCodigo = (int)$rowCodigo['Codigo'] + 1;
		$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);
	} catch(PDOException $e) {	
		echo 'Error1: ' . $e->getMessage();die;
	}
	
	try{
		
		$sql = "UPDATE ProdutoOrcamento SET PrOrcNome = :sNome,  PrOrcDetalhamento = :sDetalhamento, PrOrcCategoria = :iCategoria, PrOrcSubcategoria = :iSubCategoria, PrOrcUnidadeMedida = :iUnidadeMedida, PrOrcUsuarioAtualizador = :iUsuarioAtualizador, PrOrcEmpresa = :iEmpresa 
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
						':iEmpresa' => $_SESSION['EmpreId']
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