<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$sql = "SELECT ProduId, ProduNome, ProduValorCusto, ProduCustoFinal, UnMedSigla, ProduDetalhamento, MarcaNome,
		dbo.fnSaldoEstoque(ProduUnidade, ProduId, 'P', '".$_POST['origem']."') as Estoque,
		dbo.fnValidadeProduto(ProduUnidade, ProduId) as Validade
		FROM Produto
		JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
		LEFT JOIN Marca on MarcaId = ProduMarca
		WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and ProduId = " . $_POST['idProduto'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);

//Verifica se já existe esse registro (se existir, retorna true )
if ($count) {

	if ($row['Estoque'] >= 1) {
		
		$valorCusto = formataMoeda($row['ProduCustoFinal']);
		$valorTotal = formataMoeda($_POST['quantidade'] * $row['ProduCustoFinal']);

		$total = $_POST['quantidade'] * $row['ProduCustoFinal'];

		$marca = $row['MarcaNome'] != "" ?  " MARCA: ". $row['MarcaNome'] : "";
		$validade = $row['Validade'] != "" ? " - Validade: ".mostraData($row['Validade']) : "";
		$lote = $_POST['Lote'] != "" ? " - Lote: ".$_POST['Lote'] : "";
		
		$output = 	'<tr id="row' . $_POST['numItens'] . '" class="trGrid">
						 <td>' . $_POST['numItens'] . '</td>
						 <td data-popup="tooltip" title="' . $row['ProduDetalhamento'] . $marca . $validade."".$lote.'">' . $row['ProduNome'] . '</td>
						 <td style="text-align: center">' . $row['UnMedSigla'] . '</td>
						 <td style="text-align: center">' . $_POST['quantidade'] . '</td>
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

		$output = 	'SEMESTOQUE';
		echo $output;
	}
} else {
	echo 0;
}
