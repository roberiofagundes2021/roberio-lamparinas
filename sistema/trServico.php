<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'TR Servico';

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

	$sql = "DELETE FROM TermoReferenciaXServico
			WHERE TRXSrTermoReferencia = :iTR AND TRXSrEmpresa = :iEmpresa";
	$result = $conn->prepare($sql);

	$result->execute(array(
		':iTR' => $iTR,
		':iEmpresa' => $_SESSION['EmpreId']
	));


	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

		$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
				VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iTR' => $iTR,
			':iServico' => $_POST['inputIdServico' . $i],
			':iQuantidade' => $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
			':fValorUnitario' => null,
			':sTabela' => $_POST['inputTabelaServico' . $i],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "TR alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}

try {

	//Verifiva se tem algum orçamento para essa TR
	$sql = "SELECT COUNT(TrXOrId) as QtdeOrcamentos
			FROM TRXOrcamento
			WHERE TrXOrEmpresa = " . $_SESSION['EmpreId'] . " and TrXOrTermoReferencia = ".$iTR;
	$result = $conn->query($sql);
	$rowOrcamentosTR = $result->fetch(PDO::FETCH_ASSOC);

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
			WHERE TrRefEmpresa = " . $_SESSION['EmpreId'] . " and TrRefId = " . $iTR ." and SituaChave = 'ATIVO'";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = " SELECT TRXSCSubcategoria
		     FROM TRXSubcategoria
		     WHERE TRXSCTermoReferencia = " . $row['TrRefId'] . " and TRXSCEmpresa = " . $_SESSION['EmpreId'] . "
		";
	$result = $conn->query($sql);
	$rowSubCat = $result->fetchAll(PDO::FETCH_ASSOC);

	//Select que verifica a tabela de origem dos Servico dessa TR.
	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN ServicoOrcamento on SrOrcId = TRXSrServico
			WHERE TRXSrEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'ServicoOrcamento'";
	$result = $conn->query($sql);
	$rowServicoUtilizado1 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado1 = count($rowServicoUtilizado1);

	if (count($rowServicoUtilizado1) >= 1) {
		foreach ($rowServicoUtilizado1 as $itemServicoUtilizado) {
			$aServico1[] = $itemServicoUtilizado['TRXSrServico'];
		}
	} else {
		$aServico1 = [];
	}

	$sql = "SELECT TRXSrServico
			FROM TermoReferenciaXServico
			JOIN Servico on ServiId = TRXSrServico
			WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'Servico'";
	$result = $conn->query($sql);
	$rowServicoUtilizado2 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado2 = count($rowServicoUtilizado2);
	
	if (count($rowServicoUtilizado2) >= 1) {
		foreach ($rowServicoUtilizado2 as $itemServicoUtilizado) {
			$aServico2[] = $itemServicoUtilizado['TRXSrServico'];
		}
	} else {
		$aServico2[] = [];
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
	<title>Lamparinas | Listando Servico do TR</title>

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
				var inputSubCategoria = $('#inputIdSubCategoria').val();
				var servico = $(this).val();
				console.log(servico)
				var tr = $('#inputIdTR').val();
				//console.log(servico);

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
						servico: servico,
						servicoId: servicoId,
						servicoQuant: servicoQuant
					},
					success: function(resposta) {
						//alert(resposta);
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
			$('#cmbServico').empty().append('<option>Sem servicos</option>');
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
													$sql = "SELECT SbCatId, SbCatNome
                                                            FROM SubCategoria
                                                            JOIN TRXSubcategoria on TRXSCSubcategoria = SbCatId
                                                            WHERE SBCatEmpresa = " . $_SESSION['EmpreId'] . " and TRXSCTermoReferencia = " . $iTR;
													$result = $conn->query($sql);
													$rowSbCat = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowSbCat as $subcategoria) {
														print('<input type="text" class="form-control pb-0" value="' . $subcategoria['SbCatNome'] . '" readOnly>');
														print('<input type="hidden" id="inputSubCategoria" name="inputSubCategoria" value="' . $subcategoria['SbCatId'] . '">');
													}
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
													if (count($aServico1) >= 1) {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $valueSubCat) {
																$sql = "SELECT SrOrcId, SrOrcNome
														                FROM ServicoOrcamento
																		JOIN Situacao on SituaId = SrOrcSituacao				     
																		WHERE SrOrcSubCategoria = " . $valueSubCat['TRXSCSubcategoria'] . " and SituaChave = 'ATIVO' and SrOrcEmpresa = " . $_SESSION['EmpreId'] . " and SrOrcCategoria = " . $iCategoria;
																if (isset($row['TrRefSubCategoria']) and $row['TrRefSubCategoria'] != '' and $row['TrRefSubCategoria'] != null) {
																	$sql .= " and SrOrcSubCategoria = " . $row['TrRefSubCategoria'];
																}
																$sql .= " ORDER BY SrOrcNome ASC";
																$result = $conn->query($sql);
																$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);
																foreach ($rowServico as $item) {
																	if (in_array($item['SrOrcId'], $aServico1) or $countServicoUtilizado1 == 0) {
																		$seleciona = "selected";
																		print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['SrOrcId'] . '" ' . $seleciona . '>' . $item['SrOrcNome'] . '</option>');
																	}
																}
															}
														}
													} else {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $subcategoria) {
																$sql = "SELECT ServiId, ServiNome
																         FROM Servico
																		 JOIN Situacao on SituaId = ServiStatus		
																         WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO' and ServiCategoria = " . $iCategoria . "";
																if ($subcategoria['TRXSCSubcategoria'] != '' and $subcategoria['TRXSCSubcategoria'] != null) {
																	$sql .= " and ServiSubCategoria = " . $subcategoria['TRXSCSubcategoria'];
																}
																$sql .= " ORDER BY ServiNome ASC";
																$result = $conn->query($sql);
																$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);
																foreach ($rowServico as $item) {
																	if (in_array($item['ServiId'], $aServico2) or $countServicoUtilizado2 == 0) {
																		$seleciona = "selected";
																		print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
																	}
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
									<h5 class="card-title">Relação de Servicos</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os Servicos selecionados acima. Para atualizar os valores, basta preencher a coluna <code>Quantidade</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->

									<?php

									if (count($aServico1) >= 1) {

										$sql = "SELECT SrOrcId, SrOrcNome, SrOrcDetalhamento, SrOrcUnidadeMedida, TRXSrQuantidade, TRXSrTabela
									            FROM ServicoOrcamento
									            JOIN TermoReferenciaXServico on TRXSrServico = SrOrcId
									            WHERE SrOrcEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR  . " and TRXSrTabela = 'ServicoOrcamento'";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);

										$cont = 0;

										print('
							                    <div class="row" style="margin-bottom: -20px;">
							                    	<div class="col-lg-10">
							                    			<div class="row">
							                    				<div class="col-lg-1">
							                    					<label for="inputCodigo"><strong>Item</strong></label>
							                    				</div>
							                    				<div class="col-lg-11">
							                    					<label for="inputServico"><strong>Servico</strong></label>
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
								                    	<div class="col-lg-2">
								                    		<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
								                    	</div>	
													</div>');

											print('<input type="hidden" id="inputTabelaServico' . $cont . '" name="inputTabelaServico' . $cont . '" value="' . $item['TRXSrTabela'] . '">');
										}

										print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >');

										print('</div>');
									} else {

										$sql = "SELECT TRXSrQuantidade, TRXSrTabela, ServiId, ServiNome, ServiDetalhamento
									            FROM TermoReferenciaXServico
									            JOIN Servico on ServiId = TRXSrServico
									            WHERE ServiEmpresa = " . $_SESSION['EmpreId'] . " and TRXSrTermoReferencia = " . $iTR . " and TRXSrTabela = 'Servico'";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowServicos);

										//var_dump($rowServicos);
										$cont = 0;

										print('
							                    <div class="row" style="margin-bottom: -20px;">
							                    	<div class="col-lg-10">
							                    			<div class="row">
							                    				<div class="col-lg-1">
							                    					<label for="inputCodigo"><strong>Item</strong></label>
							                    				</div>
							                    				<div class="col-lg-11">
							                    					<label for="inputServico"><strong>Servico</strong></label>
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
								                    	<div class="col-lg-2">
								                    		<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
								                    	</div>	
													</div>');

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
							
							    if($rowOrcamentosTR['QtdeOrcamentos'] >= 1){
									print('
									<div class="row" style="margin-top: 10px;">
								        <div class="row justify-content-center col-lg-12">
									        <div class="form-group col-12 col-lg-6">
										        <button id="btnsubmit" class="btn btn-lg btn-success" disabled type="submit">Alterar</button>
										        <a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
											</div>
											<div class="row justify-content-end align-content-center col-12 col-lg-6">
											    <p style="color: red; margin: 0px"><i class="icon-info3"></i>A lista de Servicos não pode ser alterada enquanto houver orçamentos para essa TR.</p>
										    </div>
								        </div>
							        </div>
									');
								} else {
									print('
									<div class="row" style="margin-top: 10px;">
								        <div class="col-lg-12">
									        <div class="form-group">
										        <button class="btn btn-lg btn-success" type="submit">Alterar</button>
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