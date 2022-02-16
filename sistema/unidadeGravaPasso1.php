<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "INSERT INTO Unidade (UnidaNome, UnidaCep, UnidaEndereco, UnidaNumero, UnidaComplemento, UnidaBairro, 
UnidaCidade, UnidaEstado, UnidaStatus, UnidaUsuarioAtualizador, UnidaEmpresa)
VALUES (:sNome, :sCep, :sEndereco, :sNumero, :sComplemento, :sBairro, 
:sCidade, :sEstado, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
$result = $conn->prepare($sql);

$result->execute(array(
	':sNome' => $_POST['nome'],
	':sCep' => $_POST['cep'],
	':sEndereco' => $_POST['endereco'],
	':sNumero' => $_POST['numero'],
	':sComplemento' => $_POST['complemento'],
	':sBairro' => $_POST['bairro'],
	':sCidade' => $_POST['cidade'],
	':sEstado' => $_POST['estado'],
	':bStatus' => 1,
	':iUsuarioAtualizador' => $_SESSION['UsuarId'],
	':iEmpresa' => $_SESSION['EmpresaId'],
));

//Retorna o ID inserido
echo $conn->lastInsertId();

?>
