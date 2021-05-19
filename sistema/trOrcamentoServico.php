<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Orçamento Servico';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if (isset($_POST['inputOrcamentoId'])) {
	$iOrcamento = $_POST['inputOrcamentoId'];
	$iCategoria = $_POST['inputOrcamentoCategoria'];
} else if (isset($_POST['inputIdOrcamento'])) {
	$iOrcamento = $_POST['inputIdOrcamento'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("orcamento.php");
}

//Se está alterando
if (isset($_POST['inputIdOrcamento'])) {

	$sql = "DELETE FROM  TRXOrcamentoXServico
			WHERE TXOXSOrcamento = :iOrcamento AND TXOXSUnidade = :iUnidade";
	$result = $conn->prepare($sql);

	$result->execute(array(
		':iOrcamento' => $iOrcamento,
		':iUnidade' => $_SESSION['UnidadeId']
	));

	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

		$sql = "INSERT INTO  TRXOrcamentoXServico (TXOXSOrcamento, TXOXSServico, TXOXSQuantidade, TXOXSValorUnitario, TXOXSUsuarioAtualizador, TXOXSUnidade)
				VALUES (:iOrcamento, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iOrcamento' => $iOrcamento,
			':iServico' => $_POST['inputIdServico' . $i],
			':iQuantidade' => $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
			':fValorUnitario' => $_POST['inputValorUnitario' . $i] == '' ? null : gravaValor($_POST['inputValorUnitario' . $i]),
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Orçamento alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}

$sql = "SELECT *
		FROM TRXOrcamento
		LEFT JOIN Fornecedor on ForneId = TrXOrFornecedor
		JOIN Categoria on CategId = TrXOrCategoria
		LEFT JOIN SubCategoria on SbCatId = TrXOrSubCategoria
		WHERE TrXOrUnidade = " . $_SESSION['UnidadeId'] . " and TrXOrId = " . $iOrcamento;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$iTR = $row['TrXOrTermoReferencia'];

//////////////////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT SbCatId, SbCatNome
		FROM SubCategoria
		JOIN TRXSubcategoria on TRXSCSubcategoria = SbCatId
		WHERE SbCatUnidade = " . $_SESSION['UnidadeId'] . " and TRXSCTermoReferencia = " . $_SESSION['TRId'] . "
		ORDER BY SbCatNome ASC";
$result = $conn->query($sql);
$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
		
$aSubCategorias = '';

foreach ($rowSubCategoria as $item) {
	
	if ($aSubCategorias == '') {
		$aSubCategorias .= $item['SbCatId'];
	} else {
		$aSubCategorias .= ", ".$item['SbCatId'];
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT TXOXSServico
		FROM  TRXOrcamentoXServico
		JOIN Servico on ServiId = TXOXSServico
		WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento;
$result = $conn->query($sql);
$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
$countServicoUtilizado = count($rowServicoUtilizado);

foreach ($rowServicoUtilizado as $itemServicoUtilizado) {
	$aServico[] = $itemServicoUtilizado['TXOXSServico'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Listando servicos do Orçamento</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>

	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">

		function calculaValorTotal(id) {

			var ValorTotalAnterior = $('#inputValorTotal' + id + '').val() == '' ? 0 : $('#inputValorTotal' + id + '').val().replace('.', '').replace(',', '.');
			var TotalGeralAnterior = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');

			var Quantidade = $('#inputQuantidade' + id + '').val().trim() == '' ? 0 : $('#inputQuantidade' + id + '').val();
			var ValorUnitario = $('#inputValorUnitario' + id + '').val() == '' ? 0 : $('#inputValorUnitario' + id + '').val().replace('.', '').replace(',', '.');
			var ValorTotal = 0;

			var ValorTotal = parseFloat(Quantidade) * parseFloat(ValorUnitario);
			var TotalGeral = float2moeda(parseFloat(TotalGeralAnterior) - parseFloat(ValorTotalAnterior) + ValorTotal).toString();
			ValorTotal = float2moeda(ValorTotal).toString();

			$('#inputValorTotal' + id + '').val(ValorTotal);
			$('#inputTotalGeral').val(TotalGeral);
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
				<div class="card">

					<form name="formOrcamentoServico" id="formOrcamentoServico" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Serviços - Orçamento Nº "<?php echo $row['TrXOrNumero']; ?>"</h5>
						</div>

						<input type="hidden" id="inputIdOrcamento" name="inputIdOrcamento" class="form-control" value="<?php echo $row['TrXOrId']; ?>">

						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputFornecedor">Fornecedor</label>
												<input type="text" id="inputFornecedor" name="inputFornecedor" class="form-control" value="<?php echo $row['ForneNome']; ?>" readOnly>
												<input type="hidden" id="inputIdFornecedor" name="inputIdFornecedor" class="form-control" value="<?php echo $row['ForneId']; ?>">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCelular">Telefone</label>
												<input type="text" id="inputTelefone" name="inputTelefone" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefone">Celular</label>
												<input type="text" id="inputCelular" name="inputCelular" class="form-control" value="<?php echo $row['ForneCelular']; ?>" readOnly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['TrXOrCategoria']; ?>">
											</div>
										</div>

										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria(s)</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT SbCatId, SbCatNome
																FROM SubCategoria
																JOIN Situacao on SituaId = SbCatStatus	
																WHERE SbCatUnidade = ". $_SESSION['UnidadeId'] ." and SbCatId in (".$aSubCategorias.")
																ORDER BY SbCatNome ASC"; 
														$result = $conn->query($sql);
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														$count = count($rowSubCategoria);														
																
														foreach ( $rowSubCategoria as $item){	
															print('<option value="'.$item['SbCatId,'].'"disabled selected>'.$item['SbCatNome'].'</option>');	
														}                    
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Custom header text -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Serviços</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os serviços da Categoria e SubCategoria selecionadas logo acima. Para atualizar os valores, basta preencher a coluna <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->

									<?php

									// Selects para identificar quais servicos de TermoReferenciaXServico pertencem a TR deste Orçamento e a qual tabela eles pertencem
									$sql = "SELECT SrOrcId, SrOrcNome, SrOrcDetalhamento, TRXSrQuantidade
											FROM ServicoOrcamento
											JOIN TermoReferenciaXServico on TRXSrServico = SrOrcId
											WHERE SrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'ServicoOrcamento'";
									$result = $conn->query($sql);
									$rowServicosOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);

									$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, TRXSrQuantidade
											FROM Servico
											JOIN TermoReferenciaXServico on TRXSrServico = ServiId
											WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'Servico'";
									$result = $conn->query($sql);
									$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);

									$cont = 0;

									print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-11">
															<label for="inputServico"><strong>Servico</strong></label>
														</div>
													</div>
												</div>												
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputQuantidade"><strong>Quantidade</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorUnitario" title="Valor Unitário"><strong>Valor Unit.</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorTotal"><strong>Valor Total</strong></label>
												</div>
											</div>											
										</div>');

									print('<div id="tabelaServicos">');

									$fTotalGeral = 0;

									if (count($rowServicosOrcamento) >= 1) {

										$sql = "SELECT *
											    FROM  TRXOrcamentoXServico 
												JOIN ServicoOrcamento on SrOrcId = TXOXSServico
											    WHERE TXOXSUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento;
										$result = $conn->query($sql);
										$rowServicosOrc = $result->fetchAll(PDO::FETCH_ASSOC);
										$countServicoOrc = count($rowServicosOrc);

										if ($countServicoOrc >= 1) {

											foreach ($rowServicosOrc as  $item) {

												$cont++;

												$iQuantidade = isset($item['TXOXSQuantidade']) ? $item['TXOXSQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXSValorUnitario']) ? mostraValor($item['TXOXSValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? mostraValor($item['TXOXSQuantidade'] * $item['TXOXSValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? $item['TXOXSQuantidade'] * $item['TXOXSValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-9">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['SrOrcId'] . '" class="idServico">
														       </div>
														       <div class="col-lg-11">
															        <input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['SrOrcDetalhamento'] . '" value="' . $item['SrOrcNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border-off Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" readOnly value="' . $iQuantidade . '">
												        </div>	
												        <div class="col-lg-1">
													         <input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12" value="' . $fValorUnitario . '">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												        </div>											
													</div>');
											}
										} else {

											foreach ($rowServicosOrcamento as $item) {

												$cont++;

												$iQuantidade = isset($item['TRXSrQuantidade']) ? $item['TRXSrQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXSValorUnitario']) ? mostraValor($item['TXOXSValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? mostraValor($item['TXOXSQuantidade'] * $item['TXOXSValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? $item['TXOXSQuantidade'] * $item['TXOXSValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-9">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['SrOrcId'] . '" class="idServico">
														        </div>
														        <div class="col-lg-11">
															        <input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['SrOrcDetalhamento'] . '" value="' . $item['SrOrcNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border-off Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" readOnly value="' . $iQuantidade . '">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												        </div>
												   </div>');
											}
										}
									} else {

										$sql = "SELECT *
											    FROM  TRXOrcamentoXServico 
												JOIN Servico on ServiId = TXOXSServico
											    WHERE TXOXSUnidade = " . $_SESSION['UnidadeId'] . " and TXOXSOrcamento = " . $iOrcamento;
										$result = $conn->query($sql);
										$rowTRServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$countServicos = count($rowTRServicos);

										if ($countServicos >= 1) {

											foreach ($rowTRServicos as $item) {

												$cont++;

												$iQuantidade = isset($item['TXOXSQuantidade']) ? $item['TXOXSQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXSValorUnitario']) ? mostraValor($item['TXOXSValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? mostraValor($item['TXOXSQuantidade'] * $item['TXOXSValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? $item['TXOXSQuantidade'] * $item['TXOXSValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-9">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['ServiId'] . '" class="idServico">
														       </div>
														       <div class="col-lg-11">
															        <input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ServiDetalhamento'] . '" value="' . $item['ServiNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border-off Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" readOnly value="' . $iQuantidade . '">
												        </div>	
												        <div class="col-lg-1">
													         <input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12" value="' . $fValorUnitario . '">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												        </div>											
													</div>');
											}
										} else {

											foreach ($rowServicos as $item) {

												$cont++;

												$iQuantidade = isset($item['TRXSrQuantidade']) ? $item['TRXSrQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXSValorUnitario']) ? mostraValor($item['TXOXSValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? mostraValor($item['TXOXSQuantidade'] * $item['TXOXSValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXSQuantidade']) and isset($item['TXOXSValorUnitario'])) ? $item['TXOXSQuantidade'] * $item['TXOXSValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-9">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['ServiId'] . '" class="idServico">
														        </div>
														        <div class="col-lg-11">
															        <input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ServiDetalhamento'] . '" value="' . $item['ServiNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border-off Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" readOnly value="' . $iQuantidade . '">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12">
												        </div>	
												        <div class="col-lg-1">
													        <input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												        </div>											
												   </div>');
											}
										}
									}

									print('
										<div class="row" style="margin-top: 8px;">
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
												<div class="col-lg-1">
													
												</div>
												<div class="col-lg-1">
													
												</div>	
												<div class="col-lg-1" style="padding-top: 5px; text-align: right;">
													<h5><b>Total:</b></h5>
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputTotalGeral" name="inputTotalGeral" class="form-control-border-off" value="' . mostraValor($fTotalGeral) . '" readOnly>
												</div>											
											</div>');

									print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >');

									print('</div>');

									?>

								</div>
							</div>
							<!-- /custom header text -->


							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" type="submit">Alterar</button>
										<a href="trOrcamento.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>

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