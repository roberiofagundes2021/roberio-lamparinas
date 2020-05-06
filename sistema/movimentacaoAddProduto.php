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

		$output = 	'<tr id="row' . $_POST['numItens'] . '" class="trGrid">
						 <td>' . $_POST['numItens'] . '</td>
						 <td data-popup="tooltip" title="' . $row['ProduDetalhamento'] . '">' . $row['ProduNome'] . '</td>
						 <td style="text-align: center">' . $row['UnMedSigla'] . '</td>
						 <td style="text-align: center">' . $_POST['quantidade'] . '</td>
						 <td style="text-align: center"></td>
						 <td style="text-align: right">' . $valorCusto . '</td>
						 <td style="text-align: right">' . $valorTotal . '</td>
						 
					 ';

		$output .= '
					<td style="text-align:center">
					    <div class="d-flex flex-row ">
					        <select id="' . $_POST['numItens'] . '" name="cmbClassificacao" class="form-control form-control-select2 selectClassific2">
					            <option value="#">Selecione</option>
				';

		$sql = "SELECT ClassId, ClassNome
				FROM Classificacao
				JOIN Situacao on SituaId = ClassStatus
				WHERE SituaChave = 'ATIVO'
				ORDER BY ClassNome ASC";
		$result = $conn->query($sql);
		$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);

		foreach ($rowClassificacao as $item) {
			if($_POST['classific'] == $item['ClassId']){
				$output .= '<option value="' . $item['ClassId'] . '" selected>' . $item['ClassNome'] . '</option>';
			} else {
				$output .= '<option value="' . $item['ClassId'] . '">' . $item['ClassNome'] . '</option>';
			}
			
		}

		$output .= "
							    </select>
							</div>
						</td>
						<td><span name='remove' id='" . $_POST['numItens'] . "#" . $total . "' class='btn btn_remove'>X</span></td>
					</tr>
			";
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

		$output = 	'SEMESTOQUE';
		echo $output;
	}
} else {
	echo 0;
}
