<?php

// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:lamparinas.database.windows.net,1433; 
									 Database = DBLamparinas", "valmaregia", "cValToChar16");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Erro de conexão com o banco.");
    die(print_r($e));
}

$psUsuario = $_POST['usuario'];
$psSenha = md5($_POST['senha']);

$usuario_escape = addslashes($psUsuario);
$senha_escape = addslashes($psSenha);

$sql = ("SELECT * FROM Usuario WHERE UsuarLogin = '" . $usuario_escape . "' and UsuarSenha = '".$senha_escape."'");

echo $sql;
$result = $conn->query("$sql");

While ($row = $result->fetch()){
	echo "$row[0] - $row[1] - $row[2] - $row[3]<br>";
}
$conn = NULL;

?>
