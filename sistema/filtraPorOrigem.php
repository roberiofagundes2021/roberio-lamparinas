<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

if ($_POST['tipoDeFiltro'] == 'Categoria') {
	$sql = "SELECT MvXPrProduto as id
			FROM MovimentacaoXProduto
			JOIN Movimentacao on MovimId = MvXPrMovimentacao
			WHERE MvXPrUnidade = " . $_SESSION['UnidadeId'] . " and MovimDestinoLocal = " . $_POST['origem'] . " and MovimTipo = 'E'
			UNION
			SELECT MvXSrServico as id
			FROM MovimentacaoXServico
			JOIN Movimentacao on MovimId = MvXSrMovimentacao
			WHERE MvXSrUnidade = " . $_SESSION['UnidadeId'] . " and MovimDestinoLocal = " . $_POST['origem'] . " and MovimTipo = 'E'
			";
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	$cont = count($row);
	//print($sql);
	$produtosIds = [];
	$categorias = [];
	if ($cont) {
		foreach ($row as $value) {

			$sql = "SELECT ProduCategoria
		       		FROM Produto
		        	WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and ProduId = " . $value['id'] . "
					";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$produtosIds[] = $row['ProduCategoria'];
		}
		$categoriasUnic = array_unique($produtosIds);
		//var_dump($categorias);

		$sql = "SELECT CategId, CategNome
		        FROM Categoria
						JOIN Situacao on SituaStatus = CategStatus
		        WHERE CategUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
		";
		$result = $conn->query($sql);
		$categorias = $result->fetchAll(PDO::FETCH_ASSOC);

		foreach ($categorias as $value) {
			if (in_array($value['CategId'], $categoriasUnic)) {
				print('<option value="' . $value['CategId'] . '">' . $value['CategNome'] . '</option>');
			}
		}
	} else {
		echo 'sem dados';
	}
} else if ($_POST['tipoDeFiltro'] == 'Patrimonio') {
	$sql = "SELECT PatriId, PatriNumero
	        FROM Patrimonio
	        LEFT JOIN MovimentacaoXProduto on MvXPrPatrimonio = PatriId
	        LEFT JOIN Movimentacao on MovimId = MvXPrMovimentacao
	        WHERE MvXPrUnidade = " . $_SESSION['UnidadeId'] . " and MovimDestinoSetor = " . $_POST['origem'] . " and MovimTipo = 'S'
	";
	$result = $conn->query($sql);
	$rowPatrimonios = $result->fetchAll(PDO::FETCH_ASSOC);
	$cont = count($rowPatrimonios);

	if ($cont >= 1) {
		foreach ($rowPatrimonios as $value) {
			print('<option value="' . $value['PatriId'] . '">' . $value['PatriNumero'] . '</option>');
		}
	}
}
