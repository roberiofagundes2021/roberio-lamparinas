<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$sql = "SELECT ServiId, ServiNome, ServiValorCusto, ServiCustoFinal, ServiDetalhamento, dbo.fnSaldoEstoque(ServiUnidade, ServiId, 'S', NULL) as Estoque
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
	
			// $output = 	'<tr id="row'.$_POST['numItens'].'">
			// 				 <td>'.$_POST['numItens'].'</td>
			// 				 <td data-popup="tooltip" title="'.$row['ServiDetalhamento'].'">'.$row['ServiNome'].'</td>
			// 				 <td></td>
			// 				 <td style="text-align: center">'.$_POST['quantidade'].'</td>
			// 				 <td style="text-align: right">'.$valorCusto.'</td>
			// 				 <td style="text-align: right">'.$valorTotal.'</td>
			// 				 <td></td>
			// 				 <td><span name="remove" id="'.$_POST['numItens'].'#'.$total.'" class="btn btn_remove">X</span></td>
			// 			 <tr>
			// 			 ';
			// echo $output;

			$teste = [
				'data' => [
					$_POST['numItens'],
					$row['ServiDetalhamento'],
					'',
					$_POST['quantidade'],
					$valorCusto,
					$valorTotal,
					'',
					"<span name='remove' id='".$_POST['numItens']."#$total#S' class='btn btn_remove'>X</span>"
				],
				'identify' => [
					'row'.$_POST['numItens'],   //ID
					$row['ServiId'],            //ProdId
					'S',                        //Tipo
					'',                         //lote
					''                          //validade
				]
			];
			echo json_encode($teste);
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
	
			$teste = [
				'status' => 'SEMESTOQUE'
			];
			echo json_encode($teste);
		}
	} else{
		echo 0;
	}
