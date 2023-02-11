<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

	if ($_GET['idCategoria'] == '-1'){
		$sql = "SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
				FROM Fornecedor
				JOIN Situacao on SituaId = ForneStatus
				WHERE ForneEmpresa = ".$_SESSION['EmpreId']." and SituaChave = 'ATIVO'
				ORDER BY ForneNome ASC";
	} else{
		$sql = "SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
				FROM Fornecedor
				JOIN Categoria on CategId = ForneCategoria
				JOIN Situacao on SituaId = ForneStatus
				WHERE ForneEmpresa = ".$_SESSION['EmpreId']." and CategId = '". $_GET['idCategoria']."' and SituaChave = 'ATIVO'
				ORDER BY ForneNome ASC";
	}

	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	$count = count($row);


	/*if ($tipoRequest == 'INCLUIRDADOSOCIETARIO') {
		$iFornecedorId = $_SESSION['EmpresaNome'];
		$dadoSocietariosNome = $_POST['dadoSocietariosNome'] == "" ? null : $_POST['dadoSocietariosNome'];
		$dadoSocietariosCPF= $_POST['dadoSocietariosCPF'] == "" ? null : $_POST['dadoSocietariosCPF'];
		$dadoSocietariosRG = $_POST['dadoSocietariosRG'] == "" ? null : $_POST['dadoSocietariosRG'];
		$dadoSocietariosCelular = $_POST['dadoSocietariosCelular'] == "" ? null : $_POST['dadoSocietariosCelular'];
		$dadoSocietariosEmail = $_POST['dadoSocietariosEmail'] == "" ? null : $_POST['dadoSocietariosEmail'];
		$dadoSocietariosEmpresa = $_SESSION['EmpresaNome'];

		$tipo = $_POST['tipo'];



		if (isset($tipo) && $tipo == 'INSERT') {

			$sql = "INSERT INTO  Fornecedor(FrXSoFornecedor, FrXSoNome, FrXSoCpf, FrXSoRg, FrXSoCelular ,FrXSoEmail, FrXSoEmpresa )
			VALUES ('$iFornecedorId', '$dadoSocietariosNome', '$dadoSocietariosCPF',$dadoSocietariosRG, $dadoSocietariosCelular, $dadoSocietariosEmail, $dadoSocietariosEmpresa";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Incluir Dado Societários',
				'menssagem' => 'Dado Societários inserido com sucesso!!!'
			]);		
		} 	
	} */

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
	echo json_encode($row);
} else{
	echo 0;
}

?>
