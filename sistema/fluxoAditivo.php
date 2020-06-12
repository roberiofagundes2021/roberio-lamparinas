<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Fluxo Operacional Aditivo';

include('global_assets/php/conexao.php');

//Se veio do fluxo.php
if (isset($_POST['inputFluxoOperacionalId'])) {

	$_SESSION['FluxoId'] = $_POST['inputFluxoOperacionalId'];
}

$iFluxoOperacional = $_SESSION['FluxoId'];

try {

	$sql = "SELECT FlOpeId, FlOpeNumContrato, FlOpeCategoria, FlOpeSubCategoria, ForneId, ForneNome, ForneTelefone, ForneCelular, CategNome, FlOpeCategoria,
				   SbCatNome, FlOpeSubCategoria, FlOpeNumProcesso, FlOpeValor, FlOpeDataInicio, FlOpeDataFim, FlOpeStatus, SituaChave
			FROM FluxoOperacional
			JOIN Fornecedor on ForneId = FlOpeFornecedor
			JOIN Categoria on CategId = FlOpeCategoria
			JOIN SubCategoria on SbCatId = FlOpeSubCategoria
			JOIN Situacao on SituaId = FlOpeStatus
			WHERE FlOpeUnidade = " . $_SESSION['UnidadeId'] . " and FlOpeId = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT AditiId, AditiNumero, AditiDtInicio, AditiDtFim, AditiValor, AditiDtCelebracao
			FROM Aditivo
			WHERE AditiUnidade = " . $_SESSION['UnidadeId'] . " and AditiFluxoOperacional = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$rowAditivo = $result->fetchAll(PDO::FETCH_ASSOC);
	$countAditivos = count($rowAditivo);

	$sql = "SELECT Top 1 isnull(AditiDtFim, FlOpeDataFim) as DataFim
			FROM FluxoOperacional
			LEFT JOIN Aditivo on AditiFluxoOperacional = FlOpeId
			WHERE FlOpeId = " . $iFluxoOperacional . "
			ORDER BY AditiDtFim DESC";
	$result = $conn->query($sql);
	$rowDataFim = $result->fetch(PDO::FETCH_ASSOC);
	$dataFim = $rowDataFim['DataFim'];
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
	<title>Lamparinas | Aditivos do Fluxo Operacional</title>

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

			//Valida Registro
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				var inputValor = parseFloat($('#inputValor').val());
				var inputTotalGeral = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');

				//Verifica se o valor ultrapassou o total
				if (parseFloat(inputTotalGeral) > parseFloat(inputValor)) {
					alerta('Atenção', 'A soma dos totais ultrapassou o valor do contrato!', 'error');
					return false;
				}

				$("#formAditivo").submit();

			}); // enviar			

		}); //document.ready

		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaAditivo(FluxoId, AditiId, Categoria, SubCategoria, Situacao, Tipo) {

			document.getElementById('inputFluxoId').value = FluxoId;
			document.getElementById('inputAditivoId').value = AditiId;
			document.getElementById('inputCategoria').value = Categoria;
			document.getElementById('inputSubCategoria').value = SubCategoria;

			if (Situacao != 'ATIVO') {
				alerta('Atenção', 'Aditivos só podem ser criados com o Fluxo Operacional com a situação ATIVO.', 'error');
				return false;
			} else if (Tipo == 'novo') {
				document.formAditivo.action = "fluxoAditivoNovo.php";
			} else if (Tipo == 'edita') {
				document.formAditivo.action = "fluxoAditivoEdita.php";
			} else if (Tipo == 'exclui') {
				confirmaExclusao(document.formAditivo, "Tem certeza que deseja excluir esse aditivo?", "fluxoAditivoExclui.php");
			} else if (Tipo == 'produto') {
				document.formAditivo.action = "fluxoAditivoProduto.php";
			} else if (Tipo == 'servico') {
				document.formAditivo.action = "fluxoAditivoServico.php";
			}

			document.formAditivo.submit();
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

					<form name="formAditivo" id="formAditivo" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Aditivos - Fluxo Operacional Nº Contrato "<?php echo $row['FlOpeNumContrato']; ?>"</h5>
						</div>

						<input type="hidden" id="inputIdFluxoOperacional" name="inputIdFluxoOperacional" class="form-control" value="<?php echo $row['FlOpeId']; ?>">

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
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputContrato">Contrato</label>
												<input type="text" id="inputContrato" name="inputContrato" class="form-control" value="<?php echo $row['FlOpeNumContrato']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputProcesso">Processo</label>
												<input type="text" id="inputProcesso" name="inputProcesso" class="form-control" value="<?php echo $row['FlOpeNumProcesso']; ?>" readOnly>
											</div>
										</div>
										<!--<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValor">Valor Total</label>
												<input type="text" id="inputValor" name="inputValor" class="form-control" value="<?php echo mostraValor($row['FlOpeValor']); ?>" readOnly>
											</div>
										</div>-->
									</div>
								</div>
							</div>

							<!-- Info blocks -->
							<div class="row">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
									<div class="card">
										<div class="card-header header-elements-inline">
											<h3 class="card-title">Relação dos Aditivos</h3>
											<div class="header-elements">
												<div class="list-icons">
													<a class="list-icons-item" data-action="collapse"></a>
													<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
													<!--<a class="list-icons-item" data-action="remove"></a>-->
												</div>
											</div>
										</div>

										<div class="card-body">
											<div class="row">
												<div class="col-lg-6 font-size-lg">A relação abaixo faz referência aos aditivos do fluxo acima</div>
												<div class="col-lg-6 text-right">
													<a href="fluxo.php" class="btn btn-classic" role="button">Voltar</a>
													<a href="#" onclick="atualizaAditivo('<?php echo $row['FlOpeId']; ?>', '0', '<?php echo $row['FlOpeCategoria'] ?>', '<?php echo $row['FlOpeSubCategoria'] ?>','<?php echo $row['SituaChave']; ?>', 'novo');" class="btn btn-success" role="button">Novo Aditivo</a>
												</div>
											</div>
										</div>

										<table class="table" id="tblFluxo">
											<thead>
												<tr class="bg-slate">
													<th width="30%">Principal/Aditivos</th>
													<th width="15%">Data Início</th>
													<th width="15%">Data Fim</th>
													<th width="15%">Valor</th>
													<th width="15%">Data da Celebração</th>
													<th width="10%" class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody>
												<?php

												$total = $row['FlOpeValor'];

												print('
												<tr>
													<td>Termo Base</td>
													<td>' . mostraData($row['FlOpeDataInicio']) . '</td>
													<td>' . mostraData($row['FlOpeDataFim']) . '</td>
													<td>' . mostraValor($row['FlOpeValor']) . '</td>
													<td></td>
													<td></td>
												</tr>
												');

												$cont = 1;

												foreach ($rowAditivo as $item) {

													$total += $item['AditiValor'];

													print('
													<tr>
														<td>' . $item['AditiNumero'] . 'º Termo Aditivo</td>
														<td>' . mostraData($item['AditiDtInicio']) . '</td>
														<td>' . mostraData($item['AditiDtFim']) . '</td>
														<td>' . mostraValor($item['AditiValor']) . '</td>
														<td>' . mostraData($item['AditiDtCelebracao']) . '</td>
														');

													print('<td class="text-center">');

													if ($cont == $countAditivos) {
														if (mostraValor($item['AditiValor']) != '0,00') {
															print('<div class="list-icons m-2">
																		<div class="list-icons list-icons-extended">
																		
																			<!--<a href="#"  class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>-->
																				<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'exclui\', \'\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>		
																		</div>
																	</div>');

															print('
																<div class="list-icons m-2">
																	<div class="list-icons list-icons-extended">
																		
																		<div class="dropdown">													
																			<a href="#" class="list-icons-item" data-toggle="dropdown">
																				<i class="icon-menu9"></i>
																			</a>
																			
																			<div class="dropdown-menu dropdown-menu-right">
																				<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'produto\', \'\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																				<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'servico\', \'\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Serviços"></i> Listar Serviços</a>															
																				<div class="dropdown-divider">
																			</div>');
														} else {
															print('<div class="list-icons">
																		<div class="list-icons list-icons-extended">
																		
																			<!--<a href="#"  class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>-->
																				<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'exclui\', \'\');"  class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>		
																		</div>
																	</div>');
														}
													} else {
														if (mostraValor($item['AditiValor']) != '0,00') {
															print('
														<div class="list-icons m-2">
															<div class="list-icons list-icons-extended">
																
																<div class="dropdown">													
																	<a href="#" class="list-icons-item" data-toggle="dropdown">
																		<i class="icon-menu9"></i>
																	</a>
																	
																	<div class="dropdown-menu dropdown-menu-right">
																		<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'produto\', \'\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Produtos"></i> Listar Produtos</a>
																		<a href="#" onclick="atualizaAditivo(' . $row['FlOpeId'] . ', \'' . $item['AditiId'] . '\', \'' . $row['FlOpeCategoria'] . '\', \'' . $row['FlOpeSubCategoria'] . '\', \'' . $row['SituaChave'] . '\', \'servico\', \'\');" class="dropdown-item"><i class="icon-stackoverflow" title="Listar Serviços"></i> Listar Serviços</a>															
																		<div class="dropdown-divider">
																	</div>');
														}
													}

													print('</td>');
													print('</tr>');

													$cont++;
												}

												print('
												<tr style="background-color:#eeeeee; font-weight: bold">
													<td></td>
													<td>' . mostraData($row['FlOpeDataInicio']) . '</td>
													<td>' . mostraData($dataFim) . '</td>
													<td>' . mostraValor($total) . '</td>
													<td></td>
													<td></td>
												</tr>
												');

												?>

											</tbody>
										</table>
									</div>
									<!-- /basic responsive configuration -->

								</div>
							</div>

							<!-- /info blocks -->

							<form name="formAditivo" method="post">
								<input type="hidden" id="inputFluxoId" name="inputFluxoId">
								<input type="hidden" id="inputAditivoId" name="inputAditivoId">
								<input type="hidden" id="inputCategoria" name="inputCategoria">
								<input type="hidden" id="inputSubCategoria" name="inputSubCategoria">
							</form>

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