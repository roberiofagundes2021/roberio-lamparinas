<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "SELECT MovimTipo
        FROM Movimentacao
        WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MovimId = ". $_POST['iMovimentacao'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

echo $row['MovimTipo'];

?>
