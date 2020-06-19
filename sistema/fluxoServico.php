<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Fluxo Operacional Serviço';

include('global_assets/php/conexao.php');

//Se veio do fluxo.php
if (isset($_POST['inputFluxoOperacionalId'])) {
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	$iCategoria = $_POST['inputFluxoOperacionalCategoria'];
	$iSubCategoria = $_POST['inputFluxoOperacionalSubCategoria'];
} else if (isset($_POST['inputIdFluxoOperacional'])) {
	$iFluxoOperacional = $_POST['inputIdFluxoOperacional'];
	$iCategoria = $_POST['inputIdCategoria'];
	$iSubCategoria = $_POST['inputIdSubCategoria'];
} else {
	irpara("fluxo.php");
}

$bFechado = 0;
$countServico = 0;

$sql = "SELECT FlOpeValor
		FROM FluxoOperacional
		Where FlOpeId = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowFluxo = $result->fetch(PDO::FETCH_ASSOC);
$TotalFluxo = $rowFluxo['FlOpeValor'];

$sql = "SELECT isnull(SUM(FOXPrQuantidade * FOXPrValorUnitario),0) as TotalProduto
		FROM FluxoOperacionalXProduto
		Where FOXPrUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowProdutos = $result->fetch(PDO::FETCH_ASSOC);
$TotalProdutos = $rowProdutos['TotalProduto'];

$sql = "SELECT isnull(SUM(FOXSrQuantidade * FOXSrValorUnitario),0) as TotalServico
		FROM FluxoOperacionalXServico
		Where FOXSrUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowServicos = $result->fetch(PDO::FETCH_ASSOC);
$TotalServicos = $rowServicos['TotalServico'];

$TotalGeral = $TotalProdutos + $TotalServicos;

if ($TotalGeral == $TotalFluxo) {
	$bFechado = 1;
}

//Se está alterando
if (isset($_POST['inputIdFluxoOperacional'])) {
	$conn->beginTransaction();

	try {

		$sql = "DELETE FROM FluxoOperacionalXServico
				WHERE FOXSrFluxoOperacional = :iFluxoOperacional AND FOXSrUnidade = :iUnidade";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iFluxoOperacional' => $iFluxoOperacional,
			':iUnidade' => $_SESSION['UnidadeId']
		));

		for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

			$sql = "INSERT INTO FluxoOperacionalXServico (FOXSrFluxoOperacional, FOXSrServico, FOXSrQuantidade, FOXSrValorUnitario, 
					FOXSrUsuarioAtualizador, FOXSrUnidade)
					VALUES (:iFluxoOperacional, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iFluxoOperacional' => $iFluxoOperacional,
				':iServico' => $_POST['inputIdServico' . $i],
				':iQuantidade' => $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
				':fValorUnitario' => $_POST['inputValorUnitario' . $i] == '' ? null : gravaValor($_POST['inputValorUnitario' . $i]),
				':iUsuarioAtualizador' => $_SESSION['UsuarId'],
				':iUnidade' => $_SESSION['UnidadeId']
			));
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		echo 'Error: ' . $e->getMessage();

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Fluxo Operacional!!!";
		$_SESSION['msg']['tipo'] = "error";
	}
}

try {

	$sql = "SELECT FlOpeId, FlOpeNumContrato, ForneId, ForneNome, ForneTelefone, ForneCelular, CategNome, FlOpeCategoria,
				   SbCatNome, FlOpeSubCategoria, FlOpeNumProcesso, FlOpeValor, FlOpeStatus, SituaNome
			FROM FluxoOperacional
			JOIN Fornecedor on ForneId = FlOpeFornecedor
			JOIN Categoria on CategId = FlOpeCategoria
			JOIN SubCategoria on SbCatId = FlOpeSubCategoria
			JOIN Situacao on SituaId = FlOpeStatus
			WHERE FlOpeUnidade = " . $_SESSION['UnidadeId'] . " and FlOpeId = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT FOXSrServico
			FROM FluxoOperacionalXServico
			JOIN Servico on ServiId = FOXSrServico
			WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado = count($rowServicoUtilizado);

	foreach ($rowServicoUtilizado as $itemServicoUtilizado) {
		$aServicos[] = $itemServicoUtilizado['FOXSrServico'];
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
	<title>Lamparinas | Listando serviços do Fluxo Operacional</title>

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
				var servicos = $(this).val();
				//console.log(servicos);

				var cont = 1;
				var servicoId = [];
				var servicoQuant = [];
				var servicoValor = [];

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

				cont = 1;
				$.each($(".ValorUnitario"), function() {
					$id = servicoId[cont];

					servicoValor[$id] = $(this).val();
					cont++;
				});

				$.ajax({
					type: "POST",
					url: "fluxoFiltraServico.php",
					data: {
						idCategoria: inputCategoria,
						servicos: servicos,
						servicoId: servicoId,
						servicoQuant: servicoQuant,
						servicoValor: servicoValor
					},
					success: function(resposta) {
						//alert(resposta);
						$("#tabelaServicos").html(resposta).show();

						return false;

					}
				});
			});

			//Valida Registro
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				var inputValor = $('#inputValor').val().replace('.', '').replace(',', '.');
				var inputTotalGeral = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');
				var totalServicos = $('#totalRegistros').val();

				var cont = 1;

				for (i = 0; i <= totalServicos; i++) {
					var valorTotal = $(`#inputValorTotal${i}`).val()
					cont = valorTotal == '' ? 0 : 1;
					if ($(`#inputValorTotal${i}`).val() == '0,00') {
						alerta('Atenção', 'Preencha todas as quantidades e valores dos serviços selecionados ou retire da lista', 'error');
						return false;
					}
				}

				if (cont == 0) {
					alerta('Atenção', 'Preencha todas as quantidades e valores dos serviços selecionados ou retire da lista', 'error');
					return false;
				}

				//Verifica se o valor ultrapassou o total
				if (parseFloat(inputTotalGeral) > parseFloat(inputValor)) {
					alerta('Atenção', 'A soma dos totais ultrapassou o valor do contrato!', 'error');
					return false;
				}

				$("#formFluxoOperacionalServico").submit();

			}); // enviar	

			//Enviar para aprovação da Controladoria (via Bandeja)
			$('#enviarAprovacao').on('click', function(e) {

				e.preventDefault();

				confirmaExclusao(document.formFluxoOperacionalServico, "Essa ação enviará todo o Fluxo Operacional (com seus produtos e serviços) para aprovação da Controladoria. Tem certeza que deseja enviar?", "fluxoEnviar.php");
			});

		}); //document.ready

		//Mostra o "Filtrando..." na combo Servico
		function FiltraServico() {
			$('#cmbServico').empty().append('<option>Filtrando...</option>');
		}

		function ResetServico() {
			$('#cmbServico').empty().append('<option>Sem servico</option>');
		}

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

					<form name="formFluxoOperacionalServico" id="formFluxoOperacionalServico" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Servicos - Fluxo Operacional Nº Contrato "<?php echo $row['FlOpeNumContrato']; ?>"</h5>
						</div>

						<input type="hidden" id="inputIdFluxoOperacional" name="inputIdFluxoOperacional" class="form-control" value="<?php echo $row['FlOpeId']; ?>">
						<input type="hidden" id="inputStatus" name="inputStatus" class="form-control" value="<?php echo $row['FlOpeStatus']; ?>">

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
												<label for="inputTelefone">Telefone</label>
												<input type="text" id="inputTelefone" name="inputTelefone" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="text" id="inputCelular" name="inputCelular" class="form-control" value="<?php echo $row['ForneCelular']; ?>" readOnly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['FlOpeCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCategoriaNome">SubCategoria</label>
												<input type="text" id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control" value="<?php echo $row['SbCatNome']; ?>" readOnly>
												<input type="hidden" id="inputIdSubCategoria" name="inputIdSubCategoria" class="form-control" value="<?php echo $row['FlOpeSubCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputContrato">Contrato</label>
												<input type="text" id="inputContrato" name="inputContrato" class="form-control" value="<?php echo $row['FlOpeNumContrato']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputProcesso">Processo</label>
												<input type="text" id="inputProcesso" name="inputProcesso" class="form-control" value="<?php echo $row['FlOpeNumProcesso']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValor">Valor Total</label>
												<input type="text" id="inputValor" name="inputValor" class="form-control" value="<?php echo mostraValor($row['FlOpeValor']); ?>" readOnly>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbServico">Servico</label>
												<select id="cmbServico" name="cmbServico" class="form-control multiselect-filtering" multiple="multiple" data-fouc <?php if ($countServicoUtilizado and $_SESSION['PerfiChave'] != 'SUPER' and $_SESSION['PerfiChave'] != 'ADMINISTRADOR' and $_SESSION['PerfiChave'] != 'CONTROLADORIA' and $_SESSION['PerfiChave'] != 'CENTROADMINISTRATIVO') {
																																										echo "disabled";
																																									} ?>>
													<?php
													$sql = "SELECT ServiId, ServiNome
																FROM Servico
																JOIN Situacao on SituaId = ServiStatus
																WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO' and ServiCategoria = " . $iCategoria;
													if ($iSubCategoria) {
														$sql .= " and ServiSubCategoria = " . $iSubCategoria;
													}
													$sql .=	" ORDER BY ServiNome ASC";
													$result = $conn->query($sql);
													$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowServico as $item) {

														if (in_array($item['ServiId'], $aServicos) or $countServicoUtilizado == 0) {
															$seleciona = "selected";
														} else {
															$seleciona = "";
														}

														print('<option value="' . $item['ServiId'] . '" ' . $seleciona . '>' . $item['ServiNome'] . '</option>');
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
									<p class="mb-3">Abaixo estão listados todos os servicos selecionados acima. Para atualizar os valores, basta preencher as colunas <code>Quantidade</code> e <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<?php

									$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrQuantidade, FOXSrValorUnitario, MarcaNome
												FROM Servico
												JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
												LEFT JOIN Marca on MarcaId = ServiMarca
												WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
									$result = $conn->query($sql);
									$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
									$countServico = count($rowServicos);

									if (!$countServico) {
										$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
													FROM Servico
													JOIN Situacao on SituaId = ServiStatus
													WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and ServiCategoria = " . $iCategoria . " and 
													ServiSubCategoria = " . $iSubCategoria . " and SituaChave = 'ATIVO' ";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$countServico = count($rowServicos);
									}

									$cont = 0;

									print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-8">
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

									foreach ($rowServicos as $item) {

										$cont++;

										$iQuantidade = isset($item['FOXSrQuantidade']) ? $item['FOXSrQuantidade'] : '';
										$fValorUnitario = isset($item['FOXSrValorUnitario']) ? mostraValor($item['FOXSrValorUnitario']) : '';
										$fValorTotal = (isset($item['FOXSrQuantidade']) and isset($item['FOXSrValorUnitario'])) ? mostraValor($item['FOXSrQuantidade'] * $item['FOXSrValorUnitario']) : '';

										$fTotalGeral += (isset($item['FOXSrQuantidade']) and isset($item['FOXSrValorUnitario'])) ? $item['FOXSrQuantidade'] * $item['FOXSrValorUnitario'] : 0;

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
													<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12" value="' . $fValorUnitario . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												</div>											
											</div>');
									}

									print('
										<div class="row" style="margin-top: 8px;">
												<div class="col-lg-9">
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
								<div class="col-lg-6">
									<div class="form-group">
										<?php

										if ($bFechado) {
											print('
												<button class="btn btn-lg btn-success" id="enviar" style="margin-right:5px;">Alterar</button>
												<button class="btn btn-lg btn-default" id="enviarAprovacao">Enviar para Aprovação</button>');
										} else {
											if ($countServico) {
												print('<button class="btn btn-lg btn-success" id="enviar">Alterar</button>');
											} else {
												print('<button class="btn btn-lg btn-success" id="enviar" disabled>Alterar</button>');
											}
										}

										?>
										<a href="fluxo.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>

								<div class="col-lg-6" style="text-align: right; padding-right: 35px; color: red;">
									<?php
									if ($bFechado) {
										if ($row['SituaNome'] == 'PENDENTE') {
											print('<i class="icon-info3" data-popup="tooltip" data-placement="bottom"></i>Preenchimento Concluído (ENVIE PARA APROVAÇÃO)');
										} else {
											print('<i class="icon-info3" data-popup="tooltip" data-placement="bottom"></i>Preenchimento Concluído (' . $row['SituaNome'] . ')');
										}
									} else if (!$countServico) {
										print('<i class="icon-info3" data-popup="tooltip" data-placement="bottom"></i>Não há serviços cadastrados para a Categoria e SubCategoria informada');
									} else if ($TotalFluxo < $TotalGeral) {
										print('<i class="icon-info3" data-popup="tooltip" data-placement="bottom"></i>Os valores dos Produtos + Serviços ultrapassaram o valor total do Fluxo');
									}
									?>
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