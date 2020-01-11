<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Realizado';

include('global_assets/php/conexao.php');

$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
$iSubCategoria = $_POST['inputFluxoOperacionalSubCategoria'];

$sql = "SELECT FlOpeId, FlOpeFornecedor, FlOpeCategoria, FlOpeSubCategoria, FlOpeDataInicio, FlOpeDataFim, 
			   FlOpeNumContrato, FlOpeNumProcesso, FlOpeValor, FlOpeStatus
		FROM FluxoOperacional
	    WHERE FlOpeEmpresa = ". $_SESSION['EmpreId'] ." and FlOpeId = ". $iFluxoOperacional . "";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
//$count = count($row);

if ($row['FlOpeDataFim'] > date("Y-m-d")){
	$dataFim = date("Y-m-d");
} else {
	$dataFim = $row['FlOpeDataFim'];
}

$sql = "SELECT FOXPrProduto
		FROM FluxoOperacionalXProduto
		JOIN Produto on ProduId = FOXPrProduto
		WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
$result = $conn->query($sql);
$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
$countProdutoUtilizado = count($rowProdutoUtilizado);

foreach ($rowProdutoUtilizado as $itemProdutoUtilizado){
	$aProdutos[] = $itemProdutoUtilizado['FOXPrProduto'];
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
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			//Ao mudar o Fornecedor, filtra a categoria e a SubCategoria via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e){
				
				FiltraCategoria();
				FiltraSubCategoria();
				FiltraProduto();
				
				var cmbFornecedor = $('#cmbFornecedor').val();
				
				$.getJSON('filtraCategoria.php?idFornecedor='+cmbFornecedor, function (dados){
					
					//var option = '<option value="#">Selecione a Categoria</option>';
					var option = '';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.CategId+'">'+obj.CategNome+'</option>';
						});						
						
						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}					
				});
				
				$.getJSON('filtraSubCategoria.php?idFornecedor='+cmbFornecedor, function (dados){
					
					if (dados.length > 1){
						var option = '<option value="#" "selected">Selecione a SubCategoria</option>';
					} else {
						var option = '';
					}
					
					if (dados.length){
						
						$.each(dados, function(i, obj){							
							option += '<option value="'+obj.SbCatId+'">' + obj.SbCatNome + '</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});

				$.getJSON('filtraProduto.php?idFornecedor='+cmbFornecedor, function (dados){

					if (dados.length){

						var option = '';
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
						});	

						//alert(option);

						//$('#cmbProduto').removeClass('form-control multiselect-filtering').addClass('form-control form-control-select2');
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});


				$('#inputNumContrato').val('');
				$('#inputNumProcesso').val('');
			});	
						
			//Mostra o "Filtrando..." na combo Categoria
			function FiltraCategoria(){
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
			}
			
			//Mostra o "Filtrando..." na combo SubCategoria
			function FiltraSubCategoria(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			}

			//Mostra o "Filtrando..." na combo Produto
			function FiltraProduto(){
				$('#cmbProduto').empty().append('<option>Filtrando...</option>');
			}			
			
			function ResetCategoria(){
				$('#cmbCategoria').empty().append('<option value="">Sem Categoria</option>');
			}

			function ResetSubCategoria(){
				$('#cmbSubCategoria').empty().append('<option value="">Sem SubCategoria</option>');
			}

			function ResetProduto(){
				$('#cmbProduto').empty().append('<option>Sem produto</option>');
			}

		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaFluxoOperacional(FlOpeId, FlOpeCategoria, FlOpeSubCategoria, FlOpeStatus, Tipo){

			document.getElementById('inputFluxoOperacionalId').value = FlOpeId;
			document.getElementById('inputFluxoOperacionalCategoria').value = FlOpeCategoria;
			document.getElementById('inputFluxoOperacionalSubCategoria').value = FlOpeSubCategoria;
			document.getElementById('inputFluxoOperacionalStatus').value = FlOpeStatus;
					
			if (Tipo == 'edita'){	
				document.formFluxoOperacional.action = "fluxoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formFluxoOperacional, "Tem certeza que deseja excluir esse fluxo?", "fluxoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formFluxoOperacional.action = "fluxoMudaSituacao.php";
			} else if (Tipo == 'produto'){
				document.formFluxoOperacional.action = "fluxoProduto.php";
			} else if (Tipo == 'imprime'){
				document.formFluxoOperacional.action = "fluxoImprime.php";
				document.formFluxoOperacional.setAttribute("target", "_blank");
			}
			
			document.formFluxoOperacional.submit();
		}		
			
	</script>

</head>

<body class="navbar-top">

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
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<form name="formFluxoOperacional" method="post">

									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbFornecedor">Fornecedor</label>
												<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php 
														$sql = "SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
																FROM Fornecedor														     
																WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
																ORDER BY ForneNome ASC";
														$result = $conn->query($sql);
														$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowFornecedor as $item){
															$seleciona = $item['ForneId'] == $row['FlOpeFornecedor'] ? "selected" : "";												
															print('<option value="'.$item['ForneId'].'" '. $seleciona .'>'.$item['ForneNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php 
														$sql = "SELECT CategId, CategNome
																FROM Categoria
																JOIN Fornecedor on ForneCategoria = CategId
																WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and ForneId = ".$row['FlOpeFornecedor']."
																ORDER BY CategNome ASC";
														$result = $conn->query($sql);
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){			
															$seleciona = $item['CategId'] == $row['FlOpeCategoria'] ? "selected" : "";
															print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
														}
													
													?>											
												</select>
											</div>
										</div>
										
										<div class="col-lg-4">
											<label for="cmbSubCategoria">SubCategoria</label>
											<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2" required>
												<option value="">Selecione</option>
												<?php 
												 
													$sql = "SELECT SbCatId, SbCatNome
															FROM SubCategoria
															LEFT JOIN FornecedorXSubCategoria on FrXSCSubCategoria = SbCatId
															WHERE SbCatEmpresa = ".$_SESSION['EmpreId']." and FrXSCFornecedor = '". $row['FlOpeFornecedor']."' and SbCatStatus = 1
															Order By SbCatNome ASC";
													$result = $conn->query($sql);
													$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowSubCategoria as $item){			
														$seleciona = $item['SbCatId'] == $row['FlOpeSubCategoria'] ? "selected" : "";
														print('<option value="'.$item['SbCatId'].'" '. $seleciona .'>'.$item['SbCatNome'].'</option>');
													}
												
												?>										
											</select>
										</div>	
									</div>

									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc >
													<?php 
														$sql = "SELECT ProduId, ProduNome
																FROM Produto										     
																WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduStatus = 1 and ProduSubCategoria = ".$iSubCategoria."
																ORDER BY ProduNome ASC";
														$result = $conn->query($sql);
														$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);														
														
														foreach ($rowProduto as $item){	
															
															if (in_array($item['ProduId'], $aProdutos) or $countProdutoUtilizado == 0) {
																$seleciona = "selected";
															} else {
																$seleciona = "";
															}													
															
															print('<option value="'.$item['ProduId'].'" '.$seleciona.'>'.$item['ProduNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $row['FlOpeDataInicio']; ?>" >
												</div>
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataFim">Data Fim <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $dataFim; ?>" >
												</div>
											</div>
										</div>								
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNumContrato">Número do Contrato</label>
												<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº do Contrato" value="<?php echo $row['FlOpeNumContrato']; ?>" >
											</div>
										</div>
												
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNumProcesso">Número do Processo</label>
												<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo" value="<?php echo $row['FlOpeNumProcesso']; ?>" >
											</div>
										</div>

									</div>

									<div class="text-right"><a href="fluxoRealizado.php" class="btn btn-success" role="button">Filtrar</a></div>
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
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">

								<?php
									
									$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario, MarcaNome
											FROM Produto
											JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
											LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											LEFT JOIN Marca on MarcaId = ProduMarca
											WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
									$result = $conn->query($sql);
									$rowPrevisto = $result->fetchAll(PDO::FETCH_ASSOC);
									
									$cont = 0;

								?>
								
								<table class="table" id="tblFluxo">
									<thead>
										<tr class="bg-slate">
											<th width="4%">Item</th>
											<th width="26%">Produto</th>
											<th width="15%">Marca</th>
											<th width="9%">Unidade</th>
											<th width="9%">Quant.</th>									
											<th width="9%">Valor Unit.</th>										
											<th width="9%">Valor Total</th>
											<th width="7%" style="background-color: #ccc; color:#333;">Saldo (Qt)</th>
											<th width="7%" style="background-color: #ccc; color:#333;">Saldo (R$)</th>
											<th width="5%" style="background-color: #ccc; color:#333;">%</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$cont = 1;

											foreach ($rowPrevisto as $item){

												$iQuantidadePrevista = isset($item['FOXPrQuantidade']) ? $item['FOXPrQuantidade'] : '';
												$fValorUnitarioPrevisto = isset($item['FOXPrValorUnitario']) ? mostraValor($item['FOXPrValorUnitario']) : '';											
												$fValorTotalPrevisto = (isset($item['FOXPrQuantidade']) and isset($item['FOXPrValorUnitario'])) ? $item['FOXPrQuantidade'] * $item['FOXPrValorUnitario'] : '';


												$sql = "SELECT ISNULL(SUM(MvXPrQuantidade), 0) as Controle, MvXPrValorUnitario
														FROM Movimentacao														
														JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
														WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MvXPrProduto = ".$item['ProduId']." and MovimData between '".$row['FlOpeDataInicio']."' and '".$dataFim."' and MovimTipo = 'E' 
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
													<td>'.$item['ProduNome'].'</td>
													<td>'.$item['MarcaNome'].'</td>
													<td>'.$item['UnMedSigla'].'</td>
													<td>'.$iQuantidadePrevista.'</td>
													<td>'.$fValorUnitarioPrevisto.'</td>											
													<td>'.mostraValor($fValorTotalPrevisto).'</td>
													<td style="background-color: #eee; color:#333;">'.$controle.'</td>
													<td style="background-color: #eee; color:#333;">'.$saldo.'</td>
													<td style="background-color: #eee; color:#333;">'.$porcentagem.'%</td>
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
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">

								<?php
									
									$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario, MarcaNome
											FROM Produto
											JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
											LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											LEFT JOIN Marca on MarcaId = ProduMarca
											WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
									$result = $conn->query($sql);
									$rowRealizado = $result->fetchAll(PDO::FETCH_ASSOC);
									
									$cont = 0;

								?>
								
								<table class="table" id="tblFluxo">
									<thead>
										<tr class="bg-slate">
											<th width="4%">Item</th>
											<th width="26%">Produto</th>
											<th width="15%">Marca</th>
											<th width="9%">Unidade</th>
											<th width="9%">Quant.</th>									
											<th width="9%">Valor Unit.</th>										
											<th width="9%">Valor Total</th>
											<th width="7%" style="background-color: #ccc; color:#333;">Total (Qt)</th>
											<th width="7%" style="background-color: #ccc; color:#333;">Total (R$)</th>
											<th width="5%" style="background-color: #ccc; color:#333;">%</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$cont = 1;

											foreach ($rowRealizado as $item){

												$iQuantidadePrevista = isset($item['FOXPrQuantidade']) ? $item['FOXPrQuantidade'] : 0;
												$fValorUnitarioPrevisto = isset($item['FOXPrValorUnitario']) ? mostraValor($item['FOXPrValorUnitario']) : '0.00';
												$fValorTotalPrevisto = (isset($item['FOXPrQuantidade']) and isset($item['FOXPrValorUnitario'])) ? $item['FOXPrQuantidade'] * $item['FOXPrValorUnitario']: 0.00;

												$sql = "SELECT ISNULL(SUM(MvXPrQuantidade), 0) as Controle, MvXPrValorUnitario
														FROM Movimentacao														
														JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
														WHERE MovimEmpresa = ".$_SESSION['EmpreId']." and MvXPrProduto = ".$item['ProduId']." and MovimData between '".$row['FlOpeDataInicio']."' and '".$dataFim."' and MovimTipo = 'E' 
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
													<td>'.$item['ProduNome'].'</td>
													<td>'.$item['MarcaNome'].'</td>
													<td>'.$item['UnMedSigla'].'</td>
													<td>'.$iQuantidadeRealizada.'</td>
													<td>'.$fValorUnitarioRealizado.'</td>											
													<td>'.mostraValor($fValorTotalRealizado).'</td>
													<td style="background-color: #eee; color:#333;">'.$iQuantidadeRealizada.'</td>
													<td style="background-color: #eee; color:#333;">'.mostraValor($fValorTotalRealizado).'</td>
													<td style="background-color: #eee; color:#333;">'.$porcentagem.'%</td>
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
