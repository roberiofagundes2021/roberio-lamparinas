<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['servico']) and $_POST['servico'] != ''){
	$servicos = $_POST['servico'];
	$numServicos = count($servicos);
	
	$lista = "";
	
	for ($i=0; $i < $numServicos; $i++){
		$lista .= $servicos[$i] . ",";
	}
	
	//retira a última vírgula
	$lista = substr($lista, 0, -1);
} else{
	$lista = 0;
}

//echo $servico; 

if (isset($_POST['idSubCategoria']) && $_POST['idSubCategoria'] != '#' and $_POST['idSubCategoria'] != ''){

	$sql = "SELECT ServId, ServNome, ServDetalhamento
			FROM Servico
			JOIN Categoria on CategId = ServCategoria
			WHERE ServEmpresa = ".$_SESSION['ServId']." and ServSubCategoria = '". $_POST['idSubCategoria']."' and ServId in (".$lista.")
			";
} else {
	$sql = "SELECT ServId, ServNome, ServDetalhamento
			FROM Servico
			JOIN Categoria on CategId = ServCategoria
			WHERE ServEmpresa = ".$_SESSION['EmpreId']." and ServCategoria = '". $_POST['idCategoria']."' and ServId in (".$lista.")
			";
}

//echo $sql;

$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);
//echo json_encode($sql);

$output = '';

$cont = 0;
$fTotalGeral = 0;

foreach ($row as $item){
	
	$cont++;
	
	$id = $item['ServId'];	
	
	$output .= ' <div class="row" style="margin-top: 8px;">
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-1">
								<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
								<input type="hidden" id="inputIdServico'.$cont.'" name="inputIdServico'.$cont.'" value="'.$item['ServId'].'" class="idServico">
							</div>
							<div class="col-lg-11">
								<input type="text" id="inputServico'.$cont.'" name="inputServico'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ServDetalhamento'].'" value="'.$item['ServNome'].'" readOnly>
							</div>
						</div>
					</div>								
				</div>';	
}

$output .= ' <div class="row" style="margin-top: 8px;">
				<div class="col-lg-8">
					<div class="row">
						<div class="col-lg-1">
							
						</div>
						<div class="col-lg-8">
							
						</div>
						<div class="col-lg-3">
							
						</div>
					</div>
				</div>										
			</div>';

$output .= '<input type="hidden" id="totalRegistros" name="totalRegistros" value="'.$cont.'" >';

echo $output;

?>