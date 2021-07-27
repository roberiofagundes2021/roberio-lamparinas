<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if (isset($_POST['servicos']) and $_POST['servicos'] != ''){
	$servicos = $_POST['servicos'];
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
$iOrdemCompra = isset($_POST['iOrdemCompra'])?$_POST['iOrdemCompra']:'';
$iFluxoOp = isset($_POST['iFluxoOp'])?$_POST['iFluxoOp']:'';

$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrValorUnitario, OCXSrQuantidade,
				dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iOrdemCompra', ServiId, 'S') as SaldoOrdemCompra
				FROM Servico
				JOIN Categoria on CategId = ServiCategoria
				JOIN OrdemCompraXServico on OCXSrServico = ServiId and OCXSrOrdemCompra = '$iOrdemCompra'
				JOIN FluxoOperacionalXServico on FOXSrServico = ServiId and FOXSrFluxoOperacional = '$iFluxoOp'
				WHERE ServiUnidade = $_SESSION[UnidadeId] and ServiId in (".$lista.")";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);

if(!$count>0){
	$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrValorUnitario,
					dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iOrdemCompra', ServiId, 'S') as SaldoOrdemCompra
					FROM Servico
					JOIN Categoria on CategId = ServiCategoria
					JOIN FluxoOperacionalXServico on FOXSrServico = ServiId and FOXSrFluxoOperacional = '$iFluxoOp'
					WHERE ServiUnidade = $_SESSION[UnidadeId] and ServiId in (".$lista.")";
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	$count = count($row);
}

$output = '';

$cont = 0;
$fTotalGeral = 0;

foreach ($row as $item){
	
	$cont++;
	
	$id = $item['ServiId'];
	
	$valorUnitario = isset($item['FOXSrValorUnitario']) ? $item['FOXSrValorUnitario']:'';
	$quantidade = isset($item['OCXSrQuantidade']) ? $item['OCXSrQuantidade']:'';
	$saldo = isset($item['SaldoOrdemCompra']) ? $item['SaldoOrdemCompra']:'';
	$valorTotal = (isset($_POST['servicoQuant'][$id]) && isset($_POST['servicoValor'][$id])) ? mostraValor((float)$quantidade * (float)$valorUnitario) : '';
	
	$fTotalGeral += (isset($_POST['servicoQuant'][$id]) and isset($_POST['servicoValor'][$id])) ? (float)$quantidade * (float)$valorUnitario : 0;
	
	$output .= ' <div class="row" style="margin-top: 8px;">
									<div class="col-lg-7">
										<div class="row">
											<div class="col-lg-2">
												<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
												<input type="hidden" id="inputIdServico'.$cont.'" name="inputIdServico'.$cont.'" value="'.$item['ServiId'].'" class="idServico">
											</div>
											<div class="col-lg-10">
												<input type="text" id="inputServico'.$cont.'" name="inputServico'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ServiDetalhamento'].'" value="'.$item['ServiNome'].'" readOnly>
											</div>
										</div>
									</div>
									<div class="col-lg-1">
										<input type="text" id="inputSaldo'.$cont.'" readOnly name="Saldo'.$cont.'" class="form-control-border-off text-right" value="'.$saldo.'">
									</div>
									<div class="col-lg-1">
										<input type="text" id="inputQuantidade'.$cont.'" '.($saldo > 0?'':'readOnly').
										'name="inputQuantidade'.$cont.'" onkeypress="validaQuantInputModal('.$saldo.',this)" class="form-control-border Quantidade text-right" onChange="calculaValorTotal('.$cont.')" onkeypress="return onlynumber();" value="'.$quantidade.'">
									</div>	
									<div class="col-lg-1">
										<input readOnly type="text" id="inputValorUnitario'.$cont.'" name="inputValorUnitario'.$cont.'" class="form-control-border-off ValorUnitario text-right" onChange="calculaValorTotal('.$cont.')" onKeyUp="moeda(this)" maxLength="12" value="'.$valorUnitario.'">
									</div>	
									<div class="col-lg-2">
										<input type="text" id="inputValorTotal'.$cont.'" name="inputValorTotal'.$cont.'" class="form-control-border-off text-right" value="'.$valorTotal.'" readOnly>
									</div>											
								</div>';	
}

$output .= ' <div class="row" style="margin-top: 8px;">
				<div class="col-lg-7">
					<div class="row">
						<div class="col-lg-1">
							
						</div>
						<div class="col-lg-8">
							
						</div>
						<div class="col-lg-3">
							
						</div>
					</div>
				</div>								
				<div class="col-lg-1">
					
				</div>
				<div class="col-lg-1">
					
				</div>	
				<div class="col-lg-1" style="padding-top: 5px; text-align: right;">
					<h3><b>Total:</b></h3>
				</div>	
				<div class="col-lg-2">
					<input type="text" id="inputTotalGeral" name="inputTotalGeral" class="form-control-border-off text-right" value="'.mostraValor($fTotalGeral).'" readOnly>
				</div>											
			</div>';

$output .= '<input type="hidden" id="totalRegistros" name="totalRegistros" value="'.$cont.'" >';

echo $output;

?>
