<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Realizado';

include('global_assets/php/conexao.php');

$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
$iCategoria = $_POST['inputFluxoOperacionalCategoria'];

$sql = "SELECT FlOpeId, FlOpeFornecedor, FlOpeCategoria, FlOpeSubCategoria, FlOpeDataInicio, FlOpeDataFim, 
			   FlOpeNumContrato, FlOpeNumProcesso, FlOpeValor, FlOpeStatus, ForneRazaoSocial, CategNome 
		FROM FluxoOperacional
		JOIN Fornecedor ON ForneId = FlOpeFornecedor
		JOIN Categoria ON CategId = FlOpeCategoria	 
	    WHERE FlOpeUnidade = ". $_SESSION['UnidadeId'] ." and FlOpeId = ". $iFluxoOperacional . "";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
//$count = count($row);

if ($row['FlOpeDataFim'] > date("Y-m-d")){
	$dataFim = date("Y-m-d");
} else {
	$dataFim = $row['FlOpeDataFim'];
}

if (isset($_POST['inputSelecionados'])){
	
	$aSelecionados = explode("#", $_POST['inputSelecionados']);

/*	foreach ($selecionados as $key => $valor) {
		echo $valor."<br>";
	}	*/
}

/*
$sql = "SELECT FOXPrProduto
		FROM FluxoOperacionalXProduto
		LEFT JOIN Produto on ProduId = FOXPrProduto
		WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
$result = $conn->query($sql);
$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
$countProdutoUtilizado = count($rowProdutoUtilizado);

foreach ($rowProdutoUtilizado as $itemProdutoUtilizado){
	$aProdutos[] = $itemProdutoUtilizado['FOXPrProduto'];
}
*/

//SubCategorias para esse fornecedor
$sql = "SELECT SbCatId, SbCatNome, FOXSCSubCategoria
		FROM SubCategoria
		LEFT JOIN FluxoOperacionalXSubCategoria on FOXSCSubCategoria = SbCatId
		WHERE SbCatUnidade = " . $_SESSION['UnidadeId'] . " and FOXSCFluxo = $iFluxoOperacional
		ORDER BY SbCatNome ASC";
	$result = $conn->query($sql);
	$rowBD = $result->fetchAll(PDO::FETCH_ASSOC);

	$sSubCategorias = '';

	foreach ($rowBD as $item){

	if ($sSubCategorias == ''){
		$sSubCategorias .= $item['SbCatId'];	
	} else {
		$sSubCategorias .= ", ".$item['SbCatId'];
	}
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fluxo Realizado</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files --> 
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>

	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>	

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>		
	
	<!-- /theme JS files -->

</head>

<body class="navbar-top  sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Fluxo Operacional</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
									</div>
								</div>
							</div>

							<div class="card-body">
								<form name="formFluxoOperacional" method="post">

									<input type="hidden" id="inputFluxoOperacionalId" name="inputFluxoOperacionalId" value="<?php echo $_POST['inputFluxoOperacionalId']; ?>" />

									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbFornecedor">Fornecedor</label>
												<input type="text" id="cmbFornecedor" name="cmbFornecedor" class="form-control"  value="<?php echo $row['ForneRazaoSocial']; ?>" readOnly >
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<input type="text" id="cmbCategoria" name="cmbCategoria" class="form-control"  value="<?php echo $row['CategNome']; ?>" readOnly >
											</div>
										</div>
										
										<div class="col-lg-4">
											<label for="inputSubCategoriaNome">SubCategoria(s)</label>
											<select id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
												<?php 
													$sql = "SELECT SbCatId, SbCatNome
															FROM SubCategoria
															JOIN Situacao on SituaId = SbCatStatus	
															WHERE SbCatUnidade = ". $_SESSION['UnidadeId'] ." and SbCatId in (".$sSubCategorias.")
															ORDER BY SbCatNome ASC"; 
													$result = $conn->query($sql);
													$rowBD = $result->fetchAll(PDO::FETCH_ASSOC);
													$count = count($rowBD);														
															
													foreach ( $rowBD as $item){	
														print('<option value="'.$item['SbCatId,'].'"disabled selected>'.$item['SbCatNome'].'</option>');	
													}                    
												?>
											</select>
										</div>	
									</div>

									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbProduto">Produto/Serviço</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc >
													<?php 
														$sql = "SELECT ProduId as Id, ProduNome as Nome, ProduDetalhamento as Detalhamento, UnMedSigla as UnidadeMedida, FOXPrQuantidade as Quantidade, FOXPrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
																FROM Produto
																JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
																JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
																LEFT JOIN Marca on MarcaId = ProduMarca
																JOIN SubCategoria on SbCatId = ProduSubCategoria
																WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional."
																UNION
																SELECT ServiId as Id, ServiNome as Nome, ServiDetalhamento as Detalhamento, '' as UnidadeMedida, FOXSrQuantidade as Quantidade, FOXSrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
																FROM Servico
																JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
																LEFT JOIN Marca on MarcaId = ServiMarca
																JOIN SubCategoria on SbCatId = ServiSubCategoria
																WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and FOXSrFluxoOperacional = ".$iFluxoOperacional."
																ORDER BY SubCategoria";
														$result = $conn->query($sql);
														$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);
														
														$itensSelecionados = '';

														$seleciona = "selected";
															
														foreach ($rowProduto as $item){	
															
															// Está filtrando
															if (isset($_POST['inputSelecionados'])){
																if (!in_array($item['Id'], $aSelecionados)){
																	$seleciona = "";
																}
															}

															print('<option value="'.$item['Id'].'" '.$seleciona.'>'.$item['Nome'].'</option>');
															
															// Está filtrando
															if (isset($_POST['inputSelecionados'])){
																if (in_array($item['Id'], $aSelecionados)){
																	$itensSelecionados .= $item['Id']."#";
																}
															} else{
																$itensSelecionados .= $item['Id']."#";
															}																
														}
													
													?>
												</select>

												<input type="hidden" name="inputSelecionados" value="<?php echo $itensSelecionados; ?>">
											</div>
										</div>

										<div class="col-lg-1" style="min-width: 200px;">
											<div class="form-group">
												<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $row['FlOpeDataInicio']; ?>" readOnly >
												</div>
											</div>
										</div>
										
										<div class="col-lg-1" style="min-width: 200px;">
											<div class="form-group">
												<label for="inputDataFim">Data Fim <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $row['FlOpeDataFim']; ?>" readOnly >
												</div>
											</div>
										</div>								
										
										<div class="col-lg-2" style="max-width: 210px;">
											<div class="form-group">
												<label for="inputNumContrato">Nº Contrato</label>
												<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº do Contrato" value="<?php echo $row['FlOpeNumContrato']; ?>" readOnly >
											</div>
										</div>
												
										<div class="col-lg-2" style="max-width: 210px;">
											<div class="form-group">
												<label for="inputNumProcesso">Nº Processo</label>
												<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo" value="<?php echo $row['FlOpeNumProcesso']; ?>" readOnly>
											</div>
										</div>

										<div class="col-lg-2" style="max-width: 210px;">
											<div class="form-group">
												<label for="inputValor">Valor Total</label>
												<input type="text" id="inputValor" name="inputValor" class="form-control" value="<?php echo mostraValor($row['FlOpeValor']); ?>" readOnly>
											</div>
										</div>	

									</div>
									<div class="col-lg-12">	
											<div class="text-right">
												<a href="contrato.php" class="btn btn-basic" role="button"><< Fluxo Operacional/Contrato</a>
												<button type="submit" class="btn btn-principal">Filtrar</button>
											</div>
									</div>
								</form>
							</div>
						</div>
						<!-- /basic responsive configuration -->
					</div>
				</div>								
				<!-- /info blocks -->

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Fluxo Previsto</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
									</div>
								</div>
							</div>

							<div class="card-body">

								<?php
									
									$sql = "SELECT ProduId as Id, ProduNome as Nome, ProduDetalhamento as Detalhamento, UnMedSigla as UnidadeMedida, FOXPrQuantidade as Quantidade, FOXPrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
											FROM Produto
											JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
											JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											LEFT JOIN Marca on MarcaId = ProduMarca
											JOIN SubCategoria on SbCatId = ProduSubCategoria
											WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional."
											UNION
											SELECT ServiId as Id, ServiNome as Nome, ServiDetalhamento as Detalhamento, '' as UnidadeMedida, FOXSrQuantidade as Quantidade, FOXSrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
											FROM Servico
											JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
											LEFT JOIN Marca on MarcaId = ServiMarca
											JOIN SubCategoria on SbCatId = ServiSubCategoria
											WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and FOXSrFluxoOperacional = ".$iFluxoOperacional."
											ORDER BY SubCategoria, Nome ASC";
									$result = $conn->query($sql);
									$rowPrevisto = $result->fetchAll(PDO::FETCH_ASSOC);
									
									$cont = 0;

								?>
								
								<table class="table" id="tblFluxo">
									<thead>
										<tr class="bg-slate">
											<th width="4%">Item</th>
											<th width="26%">Produto/Serviço</th>
											<th width="15%">Marca</th>
											<th width="9%" style="text-align:center;">Unidade</th>
											<th width="9%" style="text-align:center;">Quant.</th>									
											<th width="9%" style="text-align:right;">Valor Unit.</th>										
											<th width="9%" style="text-align:right;">Valor Total</th>
											<th width="7%" style="background-color: #ccc; color:#333; text-align:right;">Saldo (Qt)</th>
											<th width="7%" style="background-color: #ccc; color:#333; text-align:right;">Saldo (R$)</th>
											<th width="5%" style="background-color: #ccc; color:#333; text-align:center;">%</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$cont = 1;

											foreach ($rowPrevisto as $item){

												$iQuantidadePrevista = isset($item['Quantidade']) ? $item['Quantidade'] : '';
												$fValorUnitarioPrevisto = isset($item['ValorUnitario']) ? mostraValor($item['ValorUnitario']) : '';											
												$fValorTotalPrevisto = (isset($item['Quantidade']) and isset($item['ValorUnitario'])) ? $item['Quantidade'] * $item['ValorUnitario'] : '';


												$sql = "SELECT ISNULL(SUM(MvXPrQuantidade), 0) as Controle, MvXPrValorUnitario
														FROM Movimentacao														
														JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
														WHERE MovimUnidade = ".$_SESSION['UnidadeId']." and MvXPrProduto = ".$item['Id']." and MovimData between '".$row['FlOpeDataInicio']."' and '".$dataFim."' and MovimTipo = 'E' 
														GROUP By MvXPrQuantidade, MvXPrValorUnitario";
												$result = $conn->query($sql);
												$rowMovimentacao = $result->fetch(PDO::FETCH_ASSOC);

												$iQuantidadeRealizada = $rowMovimentacao['Controle'] != '' ? $rowMovimentacao['Controle'] : 0;
												$fValorUnitarioRealizado = isset($rowMovimentacao['MvXPrValorUnitario']) ? mostraValor($rowMovimentacao['MvXPrValorUnitario']) : 0;						
												$fValorTotalRealizado = (isset($iQuantidadeRealizada) and isset($rowMovimentacao['MvXPrValorUnitario'])) ? $iQuantidadeRealizada * $rowMovimentacao['MvXPrValorUnitario'] : 0;

												$controle = $iQuantidadePrevista - $iQuantidadeRealizada;
												$saldo = mostraValor($fValorTotalPrevisto - $fValorTotalRealizado);
												$porcentagem = $controle * 100 / $iQuantidadePrevista;

												print('
												<tr>
													<td>'.$cont.'</td>
													<td data-popup="tooltip" title="'.$item['Detalhamento'].'">'.$item['Nome'].'</td>
													<td>'.$item['Marca'].'</td>
													<td style="text-align:center;">'.$item['UnidadeMedida'].'</td>
													<td style="text-align:center;">'.$iQuantidadePrevista.'</td>
													<td style="text-align:right;">'.$fValorUnitarioPrevisto.'</td>											
													<td style="text-align:right;">'.mostraValor($fValorTotalPrevisto).'</td>
													<td style="background-color: #eee; color:#333; text-align:center;">'.$controle.'</td>
													<td style="background-color: #eee; color:#333; text-align:right;">'.$saldo.'</td>
													<td style="background-color: #eee; color:#333; text-align:center;">'.$porcentagem.'%</td>
												</tr>');

												$cont++;
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>				
				<!-- /info blocks -->

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Fluxo Realizado</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
									</div>
								</div>
							</div>

							<div class="card-body">

								<?php
									
									$sql = "SELECT ProduId as Id, ProduNome as Nome, ProduDetalhamento as Detalhamento, UnMedSigla as UnidadeMedida, FOXPrQuantidade as Quantidade, FOXPrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
											FROM Produto
											JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
											JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											LEFT JOIN Marca on MarcaId = ProduMarca
											JOIN SubCategoria on SbCatId = ProduSubCategoria
											WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional."											
											UNION
											SELECT ServiId as Id, ServiNome as Nome, ServiDetalhamento as Detalhamento, '' as UnidadeMedida, FOXSrQuantidade as Quantidade, FOXSrValorUnitario as ValorUnitario, MarcaNome as Marca, SbCatNome as SubCategoria
											FROM Servico
											JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
											LEFT JOIN Marca on MarcaId = ServiMarca
											JOIN SubCategoria on SbCatId = ServiSubCategoria
											WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and FOXSrFluxoOperacional = ".$iFluxoOperacional."
											ORDER BY SubCategoria, Nome ASC";
									$result = $conn->query($sql);
									$rowRealizado = $result->fetchAll(PDO::FETCH_ASSOC);
									
									$cont = 0;

								?>
								
								<table class="table" id="tblFluxo">
									<thead>
										<tr class="bg-slate">
											<th width="4%">Item</th>
											<th width="26%">Produto/Serviço</th>
											<th width="15%">Marca</th>
											<th width="9%" style="text-align:center;">Unidade</th>
											<th width="9%" style="text-align:center;">Quant.</th>									
											<th width="9%" style="text-align:right;">Valor Unit.</th>										
											<th width="9%" style="text-align:right;">Valor Total</th>
											<th width="7%" style="background-color: #ccc; color:#333; text-align:right;">Total (Qt)</th>
											<th width="7%" style="background-color: #ccc; color:#333; text-align:right;">Total (R$)</th>
											<th width="5%" style="background-color: #ccc; color:#333; text-align:center;">%</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$cont = 1;

											foreach ($rowRealizado as $item){

												$iQuantidadePrevista = isset($item['Quantidade']) ? $item['Quantidade'] : 0;
												$fValorUnitarioPrevisto = isset($item['ValorUnitario']) ? mostraValor($item['ValorUnitario']) : '0.00';
												$fValorTotalPrevisto = (isset($item['Quantidade']) and isset($item['ValorUnitario'])) ? $item['Quantidade'] * $item['ValorUnitario']: 0.00;

												$sql = "SELECT ISNULL(SUM(MvXPrQuantidade), 0) as Controle, MvXPrValorUnitario
														FROM Movimentacao														
														JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
														WHERE MovimUnidade = ".$_SESSION['UnidadeId']." and MvXPrProduto = ".$item['Id']." and MovimData between '".$row['FlOpeDataInicio']."' and '".$dataFim."' and MovimTipo = 'E' 
														GROUP By MvXPrQuantidade, MvXPrValorUnitario";
												$result = $conn->query($sql);
												$rowMovimentacao = $result->fetch(PDO::FETCH_ASSOC);

												$iQuantidadeRealizada = $rowMovimentacao['Controle'] != '' ? $rowMovimentacao['Controle'] : 0;
												$fValorUnitarioRealizado = isset($rowMovimentacao['MvXPrValorUnitario']) ? mostraValor($rowMovimentacao['MvXPrValorUnitario']) : 0;						
												$fValorTotalRealizado = (isset($iQuantidadeRealizada) and isset($rowMovimentacao['MvXPrValorUnitario'])) ? $iQuantidadeRealizada * $rowMovimentacao['MvXPrValorUnitario'] : 0;

												$controle = $iQuantidadePrevista - $iQuantidadeRealizada;
												$saldo = mostraValor($fValorTotalPrevisto - $fValorTotalRealizado); 
												$porcentagem = $iQuantidadeRealizada * 100 / $iQuantidadePrevista;

												print('
												<tr>
													<td>'.$cont.'</td>
													<td data-popup="tooltip" title="'.$item['Detalhamento'].'">'.$item['Nome'].'</td>
													<td>'.$item['Marca'].'</td>
													<td style="text-align:center;">'.$item['UnidadeMedida'].'</td>
													<td style="text-align:center;">'.$iQuantidadeRealizada.'</td>
													<td style="text-align:right;">'.mostraValor($fValorUnitarioRealizado).'</td>
													<td style="text-align:right;">'.mostraValor($fValorTotalRealizado).'</td>
													<td style="background-color: #eee; color:#333; text-align:center;">'.$iQuantidadeRealizada.'</td>
													<td style="background-color: #eee; color:#333; text-align:right;">'.mostraValor($fValorTotalRealizado).'</td>
													<td style="background-color: #eee; color:#333; text-align:center;">'.$porcentagem.'%</td>
												</tr>');

												$cont++;

											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>				
				<!-- /info blocks -->				


			</div>
			<!-- /content area -->
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
