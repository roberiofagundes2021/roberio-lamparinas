<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = ("SELECT ProduId, ProduNome, ProduValorCusto, ProduCustoFinal, UnMedSigla
		 FROM Produto
		 LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		 WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduId = ". $_POST['idProduto']);
$result = $conn->query("$sql");
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

	//Verifica se já existe esse registro (se existir, retorna true )
	if($count){
		
		if ($_POST['tipo'] == 'E'){
			$valorCusto = formataMoeda($row['ProduValorCusto']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduValorCusto']);
		} else {
			$valorCusto = formataMoeda($row['ProduCustoFinal']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduCustoFinal']);
		}

		$output = 	'<td>'.$_POST['numItens'].'</td>
					 <td>'.$row['ProduNome'].'</td>
					 <td>'.$row['UnMedSigla'].'</td>
					 <td>'.$_POST['quantidade'].'</td>
					 <td>'.$valorCusto.'</td>
					 <td>'.$valorTotal.'</td>
					 <td>Excluir</td>';
		echo $output;
	} else{
		echo 0;
	}

?>
