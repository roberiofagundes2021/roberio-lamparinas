<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = ("SELECT ProduId, ProduNome, ProduValorVenda, UnMedSigla
		 FROM Produto
		 LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduId = ". $_POST['idProduto']);
$result = $conn->query("$sql");
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

	//Verifica se já existe esse registro (se existir, retorna true )
	if($count){
		
		$valorTotal = $_POST['quantidade'] * $row['ProduValorVenda'];

		$output = 	'<td>'.$_POST['numItens'].'</td>
					 <td>'.$row['ProduNome'].'</td>
					 <td>'.$row['UnMedSigla'].'</td>
					 <td>'.$_POST['quantidade'].'</td>
					 <td>'.formataMoeda($row['ProduValorVenda']).'</td>
					 <td>'.formataMoeda($valorTotal).'</td>
					 <td>Excluir</td>';
		echo $output;
	} else{
		echo 0;
	}

?>
