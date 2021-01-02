<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');


if (
	$_POST['tipoDeFiltro'] == 'produto'
	&& $_POST['idSubCategoria'] !== ''
	&& $_POST['idSubCategoria'] !== null
) {
	$sql = "SELECT DISTINCT CONVERT(varchar(10), produid) 
	+ '#' 
	+ CONVERT(varchar(10),ProduValorCusto) as ProduValue, 
	       ProduNome, 
				 ProduValorCusto, 
				 ProduCustoFinal,
				 MvXPrValidade
		from Produto
		JOIN MovimentacaoXProduto
			on MvXPrProduto = ProduId
		JOIN Movimentacao 
			on MovimId = MvXPrMovimentacao
		JOIN Categoria 
			on CategId = ProduCategoria
		JOIN SubCategoria 
			on SbCatId = ProduSubCategoria
		JOIN Situacao 
			on SituaId = MovimSituacao
		WHERE SituaChave = 'LIBERADO'
		and MovimTipo = 'E'
		and ProduCategoria = " . $_POST['idCategoria'] . "
		and ProduSubCategoria = " . $_POST['idSubCategoria'] . "
		ORDER BY ProduNome ASC";
} else {
	$sql = "SELECT DISTINCT CONVERT(varchar(10), produid) 
	+ '#' 
	+ CONVERT(varchar(10),ProduValorCusto) as ProduValue, 
	       ProduNome, 
				 ProduValorCusto, 
				 ProduCustoFinal,
				 MvXPrValidade
		from Produto
		JOIN MovimentacaoXProduto
			on MvXPrProduto = ProduId
		JOIN Movimentacao 
			on MovimId = MvXPrMovimentacao
		JOIN Categoria 
			on CategId = ProduCategoria
		JOIN Situacao 
			on SituaId = MovimSituacao
		WHERE SituaChave = 'LIBERADO'
		and MovimTipo = 'E'
		and ProduCategoria = " . $_POST['idCategoria'] . "
		ORDER BY ProduNome ASC";
}


$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$cont = count($row);


if ($cont) {
	foreach ($row as $value) {
		if ($value['MvXPrValidade'] == null ) {
			print('<option value="' . $value['ProduValue'] . '#null' .'">' . $value['ProduNome'] . '</option>');
		} else {
			print('<option value="' . $value['ProduValue'] . '#' . $value['MvXPrValidade'] .'">' . $value['ProduNome'] . '</option>');
		}
	}
} else {
	echo 'sem dados';
}
