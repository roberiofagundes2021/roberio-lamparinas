<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['produtos']) and $_POST['produtos'] != ''){
	$produtos = $_POST['produtos'];
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

if (isset($_POST['idSubCategoria']) && $_POST['idSubCategoria'] != '#' and $_POST['idSubCategoria'] != ''){

	$sql = "SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcUnidadeMedida
			FROM ProdutoOrcamento
			JOIN Categoria on CategId = PrOrcCategoria
			LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
			WHERE PrOrcEmpresa = ".$_SESSION['EmpreId']." and PrOrcSubCategoria = '". $_POST['idSubCategoria']."' and PrOrcId in (".$lista.")
			";
} else {
	$sql = "SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcUnidadeMedida
			FROM ProdutoOrcamento
			JOIN Categoria on CategId = PrOrcCategoria
			LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
			WHERE PrOrcEmpresa = ".$_SESSION['EmpreId']." and PrOrcCategoria = '". $_POST['idCategoria']."' and PrOrcId in (".$lista.")
			";
}

//echo $sql;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);
//echo json_encode($sql);

$output = '';

$cont = 0;

foreach ($row as $item){
	
	$cont++;
	
	$id = $item['PrOrcId'];
	
	$quantidade = isset($_POST['produtoQuant'][$id]) ? $_POST['produtoQuant'][$id] : '';
	
	$output .= ' <div class="row" style="margin-top: 8px;">
					<div class="col-lg-9">
						<div class="row">
							<div class="col-lg-1">
								<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
								<input type="hidden" id="inputIdProduto'.$cont.'" name="inputIdProduto'.$cont.'" value="'.$item['PrOrcId'].'" class="idProduto">
							</div>
							<div class="col-lg-11">
								<input type="text" id="inputProduto'.$cont.'" name="inputProduto'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['PrOrcDetalhamento'].'" value="'.$item['PrOrcNome'].'" readOnly>
							</div>
						</div>
					</div>								
					<div class="col-lg-1">
						<input type="text" id="inputUnidade'.$cont.'" name="inputUnidade'.$cont.'" class="form-control-border-off" value="'.$item['PrOrcUnidadeMedida'].'" readOnly>
					</div>
					<div class="col-lg-2">
						<input type="text" id="inputQuantidade'.$cont.'" name="inputQuantidade'.$cont.'" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="'.$quantidade.'">
					</div>	
				</div>';	
}

$output .= '<input type="hidden" id="totalRegistros" name="totalRegistros" value="'.$cont.'" >';

echo $output;

?>
