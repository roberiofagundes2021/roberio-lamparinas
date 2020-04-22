<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "SELECT ServiId, ServiNome, ServiValorCusto, ServiCustoFinal, ServiDetalhamento
		FROM Servico
		WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and ServiId = ". $_POST['idServico'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

	//Verifica se já existe esse registro (se existir, retorna true )
	if($count){
		
		if ($_POST['tipo'] == 'E'){
			$valorCusto = formataMoeda($row['ServiValorCusto']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ServiValorCusto']);
			
			$total = $_POST['quantidade'] * $row['ServiValorCusto'];
		} else {
			$valorCusto = formataMoeda($row['ServiCustoFinal']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ServiCustoFinal']);
			
			$total = $_POST['quantidade'] * $row['ServiCustoFinal'];
		}

		$output = 	'<tr id="row'.$_POST['numItens'].'">
						 <td>'.$_POST['numItens'].'</td>
						 <td title="'.$row['ServiDetalhamento'].'">'.$row['ServiNome'].'</td>
						 <td></td>
						 <td>'.$_POST['quantidade'].'</td>
						 <td>'.$valorCusto.'</td>
						 <td>'.$valorTotal.'</td>
						 <td><span name="remove" id="'.$_POST['numItens'].'#'.$total.'" class="btn btn_remove">X</span></td>
					 <tr>
					 ';
		echo $output;
	} else{
		echo 0;
	}

?>
