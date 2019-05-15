<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['idProdutos']) and $_POST['idProdutos'] != ''){
	$produtos = $_POST['idProdutos'];
	$numProdutos = count($produtos);
	
	$lista = "";
	
	for ($i=0; $i < $numProdutos; $i++){
		$lista .= $produtos[$i] . ",";
	}
	
	//retira a última vírgula
	$lista = substr($lista, 0, -1);
} else{
	$lista = 0;
}

//echo $produto; 

if (isset($_POST['idSubCategoria']) && $_POST['idSubCategoria'] != '#'){

	$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla
			FROM Produto
			JOIN Categoria on CategId = ProduCategoria
			JOIN Fornecedor on ForneCategoria = CategId
			LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduSubCategoria = '". $_POST['idSubCategoria']."' and ProduId in (".$lista.")
			";
} else {
	$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla
			FROM Produto
			JOIN Categoria on CategId = ProduCategoria
			JOIN Fornecedor on ForneCategoria = CategId
			LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduCategoria = '". $_POST['idCategoria']."' and ProduId in (".$lista.")
			";
}

//echo $sql;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);
//echo json_encode($sql);

$output = '';

$cont = 1;

foreach ($row as $item){
	$output .= ' <div class="row" style="margin-top: 8px;">
					<div class="col-lg-6">
						<div class="row">
							<div class="col-lg-1">
								<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
							</div>
							<div class="col-lg-11">
								<input type="text" id="inputProduto'.$cont.'" name="inputProduto'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ProduDetalhamento'].'" value="'.$item['ProduNome'].'" readOnly>
							</div>
						</div>
					</div>								
					<div class="col-lg-1">
						<input type="text" id="inputUnidade'.$cont.'" name="inputUnidade'.$cont.'" class="form-control-border-off" value="'.$item['UnMedSigla'].'" readOnly>
					</div>
					<div class="col-lg-1">
						<input type="text" id="inputQuantidade'.$cont.'" name="inputQuantidade'.$cont.'" class="form-control-border" onChange="calculaValorTotal('.$cont.')">
					</div>	
					<div class="col-lg-2">
						<input type="text" id="inputValorUnitario'.$cont.'" name="inputValorUnitario'.$cont.'" class="form-control-border" onChange="calculaValorTotal('.$cont.')" onKeyUp="moeda(this)" maxLength="12">
					</div>	
					<div class="col-lg-2">
						<input type="text" id="inputValorTotal'.$cont.'" name="inputValorTotal'.$cont.'" class="form-control-border-off" value="" readOnly>
					</div>
				</div>';
	$cont++;
}

echo $output;

?>
