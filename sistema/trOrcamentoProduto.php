<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Orçamento Produto';

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

	$sql = "DELETE FROM TRXOrcamentoXProduto
			WHERE TXOXPOrcamento = :iOrcamento AND TXOXPUnidade = :iUnidade";
	$result = $conn->prepare($sql);

	$result->execute(array(
		':iOrcamento' => $iOrcamento,
		':iUnidade' => $_SESSION['UnidadeId']
	));

	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

		$sql = "INSERT INTO TRXOrcamentoXProduto (TXOXPOrcamento, TXOXPProduto, TXOXPQuantidade, TXOXPValorUnitario, TXOXPUsuarioAtualizador, TXOXPUnidade)
				VALUES (:iOrcamento, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iOrcamento' => $iOrcamento,
			':iProduto' => $_POST['inputIdProduto' . $i],
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

$sql = "SELECT TXOXPProduto
		FROM TRXOrcamentoXProduto
		JOIN Produto on ProduId = TXOXPProduto
		WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento;
$result = $conn->query($sql);
$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
$countProdutoUtilizado = count($rowProdutoUtilizado);

foreach ($rowProdutoUtilizado as $itemProdutoUtilizado) {
	$aProdutos[] = $itemProdutoUtilizado['TXOXPProduto'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Listando produtos do Orçamento</title>

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

					<form name="formOrcamentoProduto" id="formOrcamentoProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - Orçamento Nº "<?php echo $row['TrXOrNumero']; ?>"</h5>
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
									<h5 class="card-title">Relação de Produtos</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os produtos da Categoria e SubCategoria selecionadas logo acima. Para atualizar os valores, basta preencher a coluna <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->

									<?php

									// Selects para identificar quais produtos de TermoReferenciaXProduto pertencem a TR deste Orçamento e a qual tabela eles pertencem
									$sql = "SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcUnidadeMedida, TRXPrQuantidade, UnMedNome
											FROM ProdutoOrcamento
											JOIN TermoReferenciaXProduto on TRXPrProduto = PrOrcId
											LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
											WHERE PrOrcUnidade = " . $_SESSION['UnidadeId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'ProdutoOrcamento'";
									$result = $conn->query($sql);
									$rowProdutosOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);

									$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, ProduUnidadeMedida, TRXPrQuantidade, UnMedNome
											FROM Produto
											JOIN TermoReferenciaXProduto on TRXPrProduto = ProduId
											LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and TRXPrTermoReferencia = " . $iTR . " and TRXPrTabela = 'Produto'";
									$result = $conn->query($sql);
									$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

									$cont = 0;

									print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-11">
															<label for="inputProduto"><strong>Produto</strong></label>
														</div>
													</div>
												</div>												
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputUnidade"><strong>Unidade</strong></label>
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

									print('<div id="tabelaProdutos">');

									$fTotalGeral = 0;
									
									//Verifica se o TR tem produtos usando a tabela ProdutoOrcamento
									if (count($rowProdutosOrcamento) >= 1) {

										$sql = "SELECT *
											    FROM TRXOrcamentoXProduto 
												JOIN ProdutoOrcamento on PrOrcId = TXOXPProduto
												LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
											    WHERE TXOXPUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento;
										$result = $conn->query($sql);
										$rowProdutosOrc = $result->fetchAll(PDO::FETCH_ASSOC);
										$countProdutoOrc = count($rowProdutosOrc);

										//Verifica se a tabela de TRXOrcamentoXProduto já tem algum registro referente a esse Orçamento, se sim, preenche os dados com as informações dessa tabela,
										//do contrário os dados devem vir do TR (já que o Orçamento deve refletir o TR), ou seja, no caso de ser um Orçamento novo que ainda não foi gravado.
										if ($countProdutoOrc >= 1) {

											foreach ($rowProdutosOrc as  $item) {

												$cont++;

												$iQuantidade = isset($item['TXOXPQuantidade']) ? $item['TXOXPQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXPValorUnitario']) ? mostraValor($item['TXOXPValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? mostraValor($item['TXOXPQuantidade'] * $item['TXOXPValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? $item['TXOXPQuantidade'] * $item['TXOXPValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-8">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['PrOrcId'] . '" class="idProduto">
														       </div>
														       <div class="col-lg-11">
															        <input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['PrOrcDetalhamento'] . '" value="' . $item['PrOrcNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedNome'] . '" readOnly>
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

											foreach ($rowProdutosOrcamento as $item) {

												$cont++;

												$iQuantidade = isset($item['TRXPrQuantidade']) ? $item['TRXPrQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXPValorUnitario']) ? mostraValor($item['TXOXPValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? mostraValor($item['TXOXPQuantidade'] * $item['TXOXPValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? $item['TXOXPQuantidade'] * $item['TXOXPValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-8">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['PrOrcId'] . '" class="idProduto">
														        </div>
														        <div class="col-lg-11">
															        <input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['PrOrcDetalhamento'] . '" value="' . $item['PrOrcNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['PrOrcUnidadeMedida'] . '" readOnly>
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
											    FROM TRXOrcamentoXProduto 
												JOIN Produto on ProduId = TXOXPProduto
												LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
											    WHERE TXOXPUnidade = " . $_SESSION['UnidadeId'] . " and TXOXPOrcamento = " . $iOrcamento;
										$result = $conn->query($sql);
										$rowTRProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										$countProdutos = count($rowTRProdutos);

										if ($countProdutos >= 1) {

											foreach ($rowTRProdutos as $item) {

												$cont++;

												$iQuantidade = isset($item['TXOXPQuantidade']) ? $item['TXOXPQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXPValorUnitario']) ? mostraValor($item['TXOXPValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? mostraValor($item['TXOXPQuantidade'] * $item['TXOXPValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? $item['TXOXPQuantidade'] * $item['TXOXPValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-8">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['ProduId'] . '" class="idProduto">
														       </div>
														       <div class="col-lg-11">
															        <input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ProduDetalhamento'] . '" value="' . $item['ProduNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedNome'] . '" readOnly>
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

											foreach ($rowProdutos as $item) {

												$cont++;

												$iQuantidade = isset($item['TRXPrQuantidade']) ? $item['TRXPrQuantidade'] : '';
												$fValorUnitario = isset($item['TXOXPValorUnitario']) ? mostraValor($item['TXOXPValorUnitario']) : '';
												$fValorTotal = (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? mostraValor($item['TXOXPQuantidade'] * $item['TXOXPValorUnitario']) : '';

												$fTotalGeral += (isset($item['TXOXPQuantidade']) and isset($item['TXOXPValorUnitario'])) ? $item['TXOXPQuantidade'] * $item['TXOXPValorUnitario'] : 0;

												print('
											        <div class="row" style="margin-top: 8px;">
												        <div class="col-lg-8">
													        <div class="row">
														        <div class="col-lg-1">
															        <input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															        <input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['ProduId'] . '" class="idProduto">
														        </div>
														        <div class="col-lg-11">
															        <input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ProduDetalhamento'] . '" value="' . $item['ProduNome'] . '" readOnly>
														        </div>
													        </div>
												        </div>								
												        <div class="col-lg-1">
													        <input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedNome'] . '" readOnly>
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