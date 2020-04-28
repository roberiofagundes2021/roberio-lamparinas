<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$sql = "SELECT ProduId, ProduNome, ProduValorCusto, ProduCustoFinal, UnMedSigla, ProduDetalhamento, dbo.fnSaldoEstoque(ProduEmpresa, ProduId, NULL) as Estoque
		FROM Produto
		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and ProduId = " . $_POST['idProduto'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if ($count) {

	if ($row['Estoque'] >= 1) {
		if ($_POST['tipo'] == 'E') {
			$valorCusto = formataMoeda($row['ProduValorCusto']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduValorCusto']);

			$total = $_POST['quantidade'] * $row['ProduValorCusto'];
		} else {
			$valorCusto = formataMoeda($row['ProduCustoFinal']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduCustoFinal']);

			$total = $_POST['quantidade'] * $row['ProduCustoFinal'];
		}

		$output = 	'<tr id="row' . $_POST['numItens'] . '">
							 <td>' . $_POST['numItens'] . '</td>
							 <td title="' . $row['ProduDetalhamento'] . '">' . $row['ProduNome'] . '</td>
							 <td>' . $row['UnMedSigla'] . '</td>
							 <td>' . $_POST['quantidade'] . '</td>
							 <td>' . $valorCusto . '</td>
							 <td>' . $valorTotal . '</td>
							 <td><span name="remove" id="' . $_POST['numItens'] . '#' . $total . '" class="btn btn_remove">X</span></td>
						 <tr>
						 ';
		echo $output;
	} else {

		if ($_POST['tipo'] == 'E') {
			$valorCusto = formataMoeda($row['ProduValorCusto']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduValorCusto']);

			$total = $_POST['quantidade'] * $row['ProduValorCusto'];
		} else {
			$valorCusto = formataMoeda($row['ProduCustoFinal']);
			$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduCustoFinal']);

			$total = $_POST['quantidade'] * $row['ProduCustoFinal'];
		}

		$output = 	'<tr id="row' . $_POST['numItens'] . '">
								 <td>' . $_POST['numItens'] . '</td>
								 <td title="' . $row['ProduDetalhamento'] . '">' . $row['ProduNome'] . '</td>
								 <td>' . $row['UnMedSigla'] . '</td>
								 <td>Estoque Indisponível '.$row['Estoque'] .'</td>
								 <td>' . $valorCusto . '</td>
								 <td>' . $valorTotal . '</td>
								 <td><span name="remove" id="' . $_POST['numItens'] . '#' . $total . '" class="btn btn_remove">X</span></td>
							 <tr>
							 ';
		echo $output;
	}
} else {
	echo 0;
}
