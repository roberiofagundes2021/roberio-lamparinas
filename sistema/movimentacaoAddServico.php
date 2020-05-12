<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "SELECT ServiId, ServiNome, ServiValorCusto, ServiCustoFinal, ServiDetalhamento, dbo.fnSaldoEstoque(ServiUnidade, ServiId, NULL) as Estoque
		FROM Servico
		WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and ServiId = ". $_POST['idServico'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

	//Verifica se já existe esse registro (se existir, retorna true )
	if($count){
		if ($row['Estoque'] >= 1) {
			if ($_POST['tipo'] == 'E') {
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
							 <td data-popup="tooltip" title="'.$row['ServiDetalhamento'].'">'.$row['ServiNome'].'</td>
							 <td></td>
							 <td>'.$_POST['quantidade'].'</td>
							 <td>'.$valorCusto.'</td>
							 <td>'.$valorTotal.'</td>
							 <td></td>
							 <td><span name="remove" id="'.$_POST['numItens'].'#'.$total.'" class="btn btn_remove">X</span></td>
						 <tr>
						 ';
			echo $output;
		} else {
	
			if ($_POST['tipo'] == 'E') {
				$valorCusto = formataMoeda($row['ServiValorCusto']);
				$valorTotal = formataMoeda($_POST['quantidade'] * $row['ServiValorCusto']);
	
				$total = $_POST['quantidade'] * $row['ServiValorCusto'];
			} else {
				$valorCusto = formataMoeda($row['ServiCustoFinal']);
				$valorTotal = formataMoeda($_POST['quantidade'] * $row['ServiCustoFinal']);
	
				$total = $_POST['quantidade'] * $row['ServiCustoFinal'];
			}
	
			$output = 	'SEMESTOQUE';
			echo $output;
		}
	} else{
		echo 0;
	}
