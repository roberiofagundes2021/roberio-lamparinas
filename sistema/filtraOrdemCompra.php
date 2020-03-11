<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

// Formata Data para aparecer DD/MM/YYYY
$sql = " SELECT OrComNumero
		 FROM OrdemCompra
         JOIN Situacao on SituaId = OrComSituacao
		 WHERE OrComEmpresa = ".$_SESSION['EmpreId']." and OrComFornecedor = '". $_GET['idFornecedor']."' and SituaChave = 'LIBERADO'
        ";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if($count){
    foreach ($row as $value) {
        print('<option value="'. $value['OrComNumero'] .'">'. $value['OrComNumero'] .'</option>');  
    }
}
?>
