<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'TR Serviço';

include('global_assets/php/conexao.php');

//Se veio do tr.php
if (isset($_POST['inputTRId'])) {
	$iTR = $_POST['inputTRId'];
	$iCategoria = $_POST['inputTRCategoria'];
} else if (isset($_POST['inputIdTR'])) {
	$iTR = $_POST['inputIdTR'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("tr.php");
}

//Se está alterando
if (isset($_POST['inputIdTR'])) {
	echo 'entrou';

	$sql = "DELETE FROM TermoReferenciaXServico
			WHERE TRXSrTermoReferencia = :iTR AND TRXSrUnidade = :iUnidade";
	$result = $conn->prepare($sql);

	$result->execute(array(
		':iTR' => $iTR,
		':iUnidade' => $_SESSION['UnidadeId']
	));


	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

		$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrUnidade)
				VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iTR' => $iTR,
			':iServico' => $_POST['inputIdServico' . $i],
			':iQuantidade' => $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
			':fValorUnitario' => null,
			':sTabela' => $_POST['inputTabelaServico' . $i],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "TR alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}

try {

	$sql = "SELECT TrXOrId
			FROM TRXOrcamento
			WHERE TrXOrUnidade = " . $_SESSION['UnidadeId'] . " and TrXOrTermoReferencia = ".$iTR."
			";
	$result = $conn->query($sql);
	$rowOrcamentosTR = $result->fetchAll(PDO::FETCH_ASSOC);


	// Select para verificar o parametro ParamServicoOrcamento.
	$sql = "SELECT ParamServicoOrcamento
			FROM Parametro
			WHERE ParamEmpresa = " . $_SESSION['EmpreId'] . " 
			";
	$result = $conn->query($sql);
	$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

	// Select para o TR.
	$sql = "SELECT *
			FROM TermoReferencia
			JOIN Categoria on CategId = TrRefCategoria
			JOIN Situacao on SituaId = TrRefStatus
			WHERE TrRefUnidade = " . $_SESSION['UnidadeId'] . " and TrRefId = " . $iTR;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);


	$sql = "SELECT TRXSCSubcategoria
		    FROM TRXSubcategoria
		    WHERE TRXSCTermoReferencia = " . $iTR . " and TRXSCUnidade = " . $_SESSION['UnidadeId'] . "";
	$result = $conn->query($sql);
	$rowSubCat = $result->fetchAll(PDO::FETCH_ASSOC);
	
	//Select que verifica a tabela de origem dos servicos dessa TR.
	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN ServicoOrcamento on SrOrcId = TRXSrServico
			WHERE TRXSrUnidade = " . $_SESSION['UnidadeId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'ServicoOrcamento'";
	$result = $conn->query($sql);
	$rowServicoOrcamentoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoOrcamentoUtilizado = count($rowServicoOrcamentoUtilizado);

	if (count($rowServicoOrcamentoUtilizado) >= 1) {
		foreach ($rowServicoOrcamentoUtilizado as $itemServicoOrcamentoUtilizado) {
			$aServicosOrcamento[] = $itemServicoOrcamentoUtilizado['TRXSrServico'];
		}
	} else {
		$aServicosOrcamento = [];
	}

	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN Servico on ServiId = TRXSrServico
			WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'Servico'";
	$result = $conn->query($sql);
	$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado = count($rowServicoUtilizado);
	
	if (count($rowServicoUtilizado) >= 1) {
		foreach ($rowServicoUtilizado as $itemServicoUtilizado) {
			$aServicos[] = $itemServicoUtilizado['TRXSrServico'];
		}
	} else {
		$aServicos[] = [];
	}

} catch (PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Listando serviços do TR</title>

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
		$(document).ready(function() {

			//Ao mudar a SubCategoria, filtra o servico via ajax (retorno via JSON)
			$('#cmbServico').on('change', function(e) {

				var inputCategoria = $('#inputIdCategoria').val();
				var inputSubCategoria = $('#inputSubCategoria').val();
				var servicos = $(this).val();
				console.log(servicos)
				var tr = $('#inputIdTR').val();
				//console.log(servicos);

				var cont = 1;
				var servicoId = [];
				var servicoQuant = [];

				// Aqui é para cada "class" faça
				$.each($(".idServico"), function() {
					servicoId[cont] = $(this).val();
					cont++;
				});

				cont = 1;
				//aqui fazer um for que vai até o ultimo cont (dando cont++ dentro do for)
				$.each($(".Quantidade"), function() {
					$id = servicoId[cont];

					servicoQuant[$id] = $(this).val();
					cont++;
				});

				$.ajax({
					type: "POST",
					url: "trFiltraServico.php",
					data: {
						idTr: tr,
						idCategoria: inputCategoria,
						idSubCategoria: inputSubCategoria,
						servicos: servicos,
						servicoId: servicoId,
						servicoQuant: servicoQuant
					},
					success: function(resposta) {
						//alert(resposta);
						console.log(resposta)
						$("#tabelaServicos").html(resposta).show();

						return false;

					}
				});
			});

			function disabledSelect(){
				let btnSubmit = $('#btnsubmit')
				let selectServicos = $('#ServicoRow')

				if(btnSubmit.attr('disabled')){
                    selectServicos.css('display', 'none')
				}
			}
			disabledSelect()

		}); //document.ready

		//Mostra o "Filtrando..." na combo Servico
		function FiltraServico() {
			$('#cmbServico').empty().append('<option>Filtrando...</option>');
		}

		function ResetServico() {
			$('#cmbServico').empty().append('<option>Sem servico</option>');
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
					<form name="formTRServico" id="formTRServico" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Serviços - TR Nº "<?php echo $row['TrRefNumero']; ?>"</h5>
						</div>
						<input type="hidden" id="inputIdTR" name="inputIdTR" class="form-control" value="<?php echo $row['TrRefId']; ?>">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['TrRefCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria(as)</label>
												<div class="d-flex flex-row" style="padding-top: 7px;">
													<?php
														$sql = "
															SELECT SbCatId, SbCatNome
																FROM SubCategoria
																JOIN TRXSubcategoria 
																	ON TRXSCSubcategoria = SbCatId
															WHERE SBCatUnidade = " . $_SESSION['UnidadeId'] . "
																AND TRXSCTermoReferencia = " . $iTR;
														$result = $conn->query($sql);
														$rowSbCat = $result->fetchAll(PDO::FETCH_ASSOC);

														$subCategName = '';
														$max = count($rowSbCat); 
														$count = 1;

														foreach ($rowSbCat as $subcategoria) {
															if($count == $max) {
																$subCategName .= $subcategoria['SbCatNome'];
															} else {
																$subCategName .= $subcategoria['SbCatNome'].', ';
															}
														
															print('<input type="hidden" id="inputSubCategoria" name="inputSubCategoria" value="' . $subcategoria['SbCatId'] . '">');

															$count++;
														}

														print('<input type="text" class="form-control pb-0" value="' . $subCategName . '" readOnly>');
													?>
												</div>
											</div>
										</div>
									</div>
									<div id="ServicoRow" class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbServico">Serviço</label>
												<select id="cmbServico" name="cmbServico" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php
													if (count($aServicosOrcamento) >= 1) {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $valueSubCat) {
																$sql = "
																	SELECT SrOrcId, SrOrcNome
																	FROM ServicoOrcamento
																	JOIN Situacao ON SituaId = SrOrcSituacao				     
																	WHERE SrOrcSubCategoria = " . $valueSubCat['TRXSCSubcategoria'] . " 
																	AND SituaChave = 'ATIVO' 
																	AND SrOrcUnidade = " . $_SESSION['UnidadeId'] . " 
																	AND SrOrcCategoria = " . $iCategoria;

																if (isset($row['TrRefSubCategoria']) and $row['TrRefSubCategoria'] != '' and $row['TrRefSubCategoria'] != null) {
																	$sql .= " and SrOrcSubCategoria = " . $row['TrRefSubCategoria'];
																}
																$sql .= " ORDER BY SrOrcNome ASC";
																$result = $conn->query($sql);
																$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);
																foreach ($rowServico as $item) {
																	if (in_array($item['SrOrcId'], $aServicosOrcamento) or $countServicoOrcamentoUtilizado == 0) {
																		$seleciona = "selected";
																		print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																	}
																}
															}
														} else{
															$sql = "
																SELECT SrOrcId, SrOrcNome
																FROM ServicoOrcamento
																JOIN Situacao ON SituaId = SrOrcSituacao		
																WHERE SrOrcUnidade = " . $_SESSION['UnidadeId'] . " 
																AND SituaChave = 'ATIVO' 
																AND SrOrcCategoria = " . $iCategoria;
															$sql .= " ORDER BY SrOrcNome ASC";
															$result = $conn->query($sql);
															$rowServicoOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowServicoOrcamento as $item) {
																if (in_array($item['SrOrcId'], $aServicosOrcamento)) {
																	$seleciona = "selected";
																	print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																} else {
																	$seleciona = "";
																	print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																}
															}															
														}
													} else {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $subcategoria) {
																$sql = "
																	SELECT ServiId, ServiNome
																    FROM Servico
																	JOIN Situacao ON SituaId = ServiStatus		
																    WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " 
																	AND SituaChave = 'ATIVO' 
																	AND ServiCategoria = " . $iCategoria . "
																";
																if ($subcategoria['TRXSCSubcategoria'] != '' and $subcategoria['TRXSCSubcategoria'] != null) {
																	$sql .= " and ServiSubCategoria = " . $subcategoria['TRXSCSubcategoria'];
																}
																$sql .= " ORDER BY ServiNome ASC";
																$result = $conn->query($sql);
																$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);

																foreach ($rowServico as $item) {
																	if (in_array($item['ServiId'], $aServicos)) {
																		$seleciona = "selected";
																		print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																	}
																}
															}
														} else{
															$sql = "
																SELECT ServiId, ServiNome
															    FROM Servico
																JOIN Situacao ON SituaId = ServiStatus		
															    WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " 
																AND SituaChave = 'ATIVO' 
																AND ServiCategoria = " . $iCategoria . "
															";
															$sql .= " ORDER BY ServiNome ASC";
															$result = $conn->query($sql);
															$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowServico as $item) {
																if (in_array($item['ServiId'], $aServicos)) {
																	$seleciona = "selected";
																	print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																} else {
																	$seleciona = "";
																	print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																}
															}

														}
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
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os serviços selecionadas acima. Para atualizar os valores, basta preencher a coluna <code>Quantidade</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->

									<?php

									if (count($aServicosOrcamento) >= 1) {

										$sql = "
											SELECT SrOrcId, SrOrcNome, SrOrcDetalhamento,
											TRXSrQuantidade, TRXSrTabela
											FROM ServicoOrcamento
											JOIN TermoReferenciaXServico on TRXSrServico = SrOrcId
											WHERE SrOrcUnidade = " . $_SESSION['UnidadeId'] . " 
											AND TRXSrTermoReferencia = " . $iTR  . " 
												AND TRXSrTabela = 'ServicoOrcamento'
										";
										$result = $conn->query($sql);
										$rowServicosOrcamento = $result->fetchAll(PDO::FETCH_ASSOC);

										$cont = 0;

										print('
											<div class="row" style="margin-bottom: -20px;">
												<div class="col-lg-10">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-11">
															<label for="inputServico"><strong>Serviço</strong></label>
														</div>
													</div>
												</div>
												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputQuantidade"><strong>Quantidade</strong></label>
													</div>
												</div>	
											</div>');

										print('<div id="tabelaServicos">');

										foreach ($rowServicosOrcamento as $item) {

											$cont++;

											$iQuantidade = isset($item['TRXSrQuantidade']) ? $item['TRXSrQuantidade'] : '';
										
											print('
												<div class="row" style="margin-top: 8px;">
													<div class="col-lg-10">
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
											');

											if(count($rowOrcamentosTR) >= 1) {
												print('
														<div class="col-lg-2">
															<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '" readOnly>
														</div>	
												');
											} else {
												print('
														<div class="col-lg-2">
															<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
														</div>	
												');
											}
											print('
													</div>
											');

											print('<input type="hidden" id="inputTabelaServico' . $cont . '" name="inputTabelaServico' . $cont . '" value="' . $item['TRXSrTabela'] . '">');
										}

										print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >');

										print('</div>');
									} else {

										$sql = "
											SELECT TRXSrQuantidade,	TRXSrTabela, ServiId, 
											ServiNome, ServiDetalhamento
											FROM TermoReferenciaXServico
											JOIN Servico ON ServiId = TRXSrServico
											WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " 
											AND TRXSrTermoReferencia = " . $iTR . " 
											AND TRXSrTabela = 'Servico'
										";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowServicos);

										$cont = 0;

										print('
											<div class="row" style="margin-bottom: -20px;">
												<div class="col-lg-10">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-11">
															<label for="inputServico"><strong>Serviço</strong></label>
														</div>
													</div>
												</div>												
												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputQuantidade"><strong>Quantidade</strong></label>
													</div>
												</div>	
											</div>');

										print('<div id="tabelaServicos">');

										foreach ($rowServicos as $item) {

											$cont++;

											$iQuantidade = isset($item['TRXSrQuantidade']) ? $item['TRXSrQuantidade'] : '';

											print('
													<div class="row" style="margin-top: 8px;">
														<div class="col-lg-10">
															<div class="row">
																<div class="col-lg-1">
																	<input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>																	
																	<input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['ServiId'] . '" class="idServico">																	
																	<input type="hidden" id="inputTabelaServico' . $cont . '" name="inputTabelaServico' . $cont . '" value="' . $item['TRXSrTabela'] . '">
																</div>

																<div class="col-lg-11">
																	<input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ServiDetalhamento'] . '" value="' . $item['ServiNome'] . '" readOnly>
																</div>
															</div>
														</div>	
											');

											if(count($rowOrcamentosTR) >= 1) {
												print('
														<div class="col-lg-2">
															<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '" readOnly>
														</div>	
												');
											} else {
												print('
														<div class="col-lg-2">
															<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
														</div>	
												');
											}
											print('
													</div>
											');

											print('<input type="hidden" id="inputTabelaServico' . $cont . '" name="inputTabelaServico' . $cont . '" value="' . $item['TRXSrTabela'] . '">');
										}

										print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >');

										print('</div>');
									}

									?>
								</div>
							</div>
							<!-- /custom header text -->
							<?php 
							
							    if(count($rowOrcamentosTR) >= 1){
									print('
									<div class="row" style="margin-top: 10px;">
								        <div class="row justify-content-center col-lg-12">
									        <div class="form-group col-12 col-lg-6">
										        <button id="btnsubmit" class="btn btn-lg btn-principal" disabled type="submit">Alterar</button>
										        <a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
											</div>
											<div class="row justify-content-end align-content-center col-12 col-lg-6">
											    <p style="color: red; margin: 0px"><i class="icon-info3"></i>A lista de serviços não pode ser alterada enquanto houver orçamentos para esse Termo de Referência.</p>
										    </div>
								        </div>
							        </div>
									');
								} else {
									print('
									<div class="row" style="margin-top: 10px;">
								        <div class="col-lg-12">
									        <div class="form-group">
										        <button class="btn btn-lg btn-principal" type="submit">Alterar</button>
										        <a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
									        </div>
								        </div>
							        </div>
									');
								}
							 
							?>
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