<?php

include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Editar Serviço';
include('global_assets/php/conexao.php');

//Esse if foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente
if (!isset($_POST['inputServicoId'])) {
	header("Location: servicoVenda.php");
} else {
	$iUnidade = $_SESSION['UnidadeId'];
	$sqlDadosFormulario = "SELECT SrVenId,SrVenCodigo,SrVenNome,SrVenDetalhamento,SrVenTipoServico,SrVenGrupo,SrVenSubGrupo,SrVenPlanoConta,s.SituaChave
	FROM ServicoVenda sv
	JOIN Situacao s ON s.SituaStatus = sv.SrVenStatus 
	WHERE SrVenId = " . $_POST['inputServicoId'] . "and SrVenUnidade = " . $iUnidade . ";";
	$resultDadosFormulario = $conn->query($sqlDadosFormulario);
	$resultDadosFormulario = $resultDadosFormulario->fetch(PDO::FETCH_ASSOC);

	$sqlTiposServico = "SELECT TpSerId,TpSerCodigo,TpSerNome
	FROM TipoServico ts
	JOIN Situacao s ON SituaId = ts.TpSerStatus
	WHERE s.SituaChave = 'ATIVO' AND ts.TpSerUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY ts.TpSerNome asc;";
	$tiposServico = $conn->query($sqlTiposServico);
	$tiposServico = $tiposServico->fetchAll(PDO::FETCH_ASSOC);

	$sqlGrupos = "SELECT AtGruId,AtGruNome
	FROM AtendimentoGrupo a
	JOIN Situacao s ON SituaId = a.AtGruStatus
	WHERE s.SituaChave = 'ATIVO' AND a.AtGruUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY a.AtGruNome asc;";
	$grupos = $conn->query($sqlGrupos);
	$grupos = $grupos->fetchAll(PDO::FETCH_ASSOC);

	$sqlSubgrupos = ("SELECT AtSubId, AtSubNome, AtSubGrupo
		FROM AtendimentoSubGrupo ag
		JOIN Situacao s ON SituaId = ag.AtSubStatus  
		WHERE s.SituaChave = 'ATIVO' AND ag.AtSubUnidade  = $iUnidade
		ORDER BY ag.AtSubNome  asc;"
	);
	$subGrupos = $conn->query($sqlSubgrupos);
	$subGrupos = $subGrupos->fetchAll(PDO::FETCH_ASSOC);
	$arraySubgrupos = [];
	foreach ($subGrupos as $item) {
		array_push($arraySubgrupos, [
			'AtSubId' => $item['AtSubId'],
			'AtSubNome' => $item['AtSubNome'],
			'AtGrupoId' => $item['AtSubGrupo']
		]);
	}

	$sqlModalidades = "SELECT AtModId,AtModNome
	FROM AtendimentoModalidade am 
	JOIN Situacao s on SituaId = am.AtModSituacao
	WHERE s.SituaChave = 'ATIVO' AND am.AtModUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY am.AtModNome asc;";
	$modalidades = $conn->query($sqlModalidades);
	$modalidades = $modalidades->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Serviço</title>

	<?php include_once("head.php"); ?>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		function sleep(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
		}

		$(document).ready(function() {
			let tipoServico = $("#inputServicoTipo").val();
			$(`#tipoServico option[value=${tipoServico}]`).prop('selected', 'selected').change();

			let grupo = $("#inputGrupo").val();
			$(`#grupo option[value=${grupo}]`).prop('selected', 'selected').change();

			let subgrupo = $("#inputSubgrupo").val();
			atualizaSubGrupos();
			$(`#subGrupo option[value=${subgrupo}]`).prop('selected', 'selected').change();

			let planoDeConta = $("#inputPlanoDeConta").val();
			$(`#cmbPlanoConta option[value=${planoDeConta}]`).prop('selected', 'selected').change();
		});

		function atualizaSubGrupos() {
			if ($('#grupo').val() == "") {
				$('#subGrupo').empty();
				let opt = '<option value="">Selecione primeiro um grupo</option>';
				$('#subGrupo').append(opt);
			} else {
				$('#subGrupo').empty();
				let grupo = $('#grupo').val();
				let possiveisSubgrupos = [];
				$("#possiveisSubgrupos option").each(function() {
					let val = $(this).val();
					possiveisSubgrupos.push(JSON.parse(val));
				});
				let subGrupos = possiveisSubgrupos.filter(subGrupo => subGrupo.AtGrupoId == grupo);
				subGrupos.forEach(item => {
					let opt = `<option value="${item.AtSubId}">${item.AtSubNome}</option>`;
					$('#subGrupo').append(opt);
				})
			}
		}

		//Limpa o campo Nome quando for digitado só espaços em branco
		$("#inputNome").on('blur', function(e) {
			var inputNome = $('#inputNome').val();
			inputNome = inputNome.trim();
			if (inputNome.length == 0) {
				$('#inputNome').val('');
			}
		});

		//Ao mudar o Custo, atualiza o CustoFinal
		$('#inputValorCusto').on('blur', function(e) {

			var inputValorCusto = $('#inputValorCusto').val().replaceAll('.', '').replace(',', '.');
			var inputOutrasDespesas = $('#inputOutrasDespesas').val().replaceAll('.', '').replace(',', '.');
			var inputMargemLucro = $('#inputMargemLucro').val().replaceAll('.', '').replace(',', '.');

			if (inputValorCusto == null || inputValorCusto.trim() == '') {
				inputValorCusto = 0.00;
			}

			if (inputOutrasDespesas == null || inputOutrasDespesas.trim() == '') {
				inputOutrasDespesas = 0.00;
			}

			var inputCustoFinal = parseFloat(inputValorCusto) + parseFloat(inputOutrasDespesas);

			inputCustoFinal = float2moeda(inputCustoFinal).toString();

			$('#inputCustoFinal').val(inputCustoFinal);

			if (inputMargemLucro != null && inputMargemLucro.trim() != '' && inputMargemLucro.trim() != 0.00) {
				atualizaValorVenda();
			}
		});

		//Ao mudar o Custo, atualiza o CustoFinal
		$('#inputOutrasDespesas').on('blur', function(e) {

			var inputValorCusto = $('#inputValorCusto').val().replaceAll('.', '').replace(',', '.');
			var inputOutrasDespesas = $('#inputOutrasDespesas').val().replaceAll('.', '').replace(',', '.');
			var inputMargemLucro = $('#inputMargemLucro').val().replaceAll('.', '').replace(',', '.');

			if (inputValorCusto == null || inputValorCusto.trim() == '') {
				inputValorCusto = 0.00;
			}

			if (inputOutrasDespesas == null || inputOutrasDespesas.trim() == '') {
				inputOutrasDespesas = 0.00;
			}

			var inputCustoFinal = parseFloat(inputValorCusto) + parseFloat(inputOutrasDespesas);

			inputCustoFinal = float2moeda(inputCustoFinal).toString();

			$('#inputCustoFinal').val(inputCustoFinal);

			if (inputMargemLucro != null && inputMargemLucro.trim() != '' && inputMargemLucro.trim() != 0.00) {
				atualizaValorVenda();
			}
		});

		//Ao mudar a Margem de Lucro, atualiza o Valor de Venda
		$('#inputMargemLucro').on('blur', function(e) {

			atualizaValorVenda();
		});

		//Ao mudar o Valor de Venda, atualiza a Margem de Lucro
		$('#inputValorVenda').on('blur', function(e) {

			var inputCustoFinal = $('#inputCustoFinal').val().replaceAll('.', '').replace(',', '.');
			var inputValorVenda = $('#inputValorVenda').val().replaceAll('.', '').replace(',', '.');

			if (inputCustoFinal == null || inputCustoFinal.trim() == '') {
				inputCustoFinal = 0.00;
			}

			if (inputValorVenda == null || inputValorVenda.trim() == '') {
				inputValorVenda = 0.00;
			}

			//alert(parseFloat(inputMargemLucro) * parseFloat(inputCustoFinal));
			var lucro = parseFloat(inputValorVenda) - parseFloat(inputCustoFinal);

			inputMargemLucro = 0;

			if (inputCustoFinal != 0.00 && inputValorVenda != 0.00) {
				inputMargemLucro = lucro / parseFloat(inputCustoFinal) * 100;
			}

			inputMargemLucro = float2moeda(inputMargemLucro).toString();

			$('#inputMargemLucro').val(inputMargemLucro);

		});

		function atualizaValorVenda() {
			var inputCustoFinal = $('#inputCustoFinal').val().replaceAll('.', '').replace(',', '.');
			var inputMargemLucro = $('#inputMargemLucro').val().replace(',', '.');

			if (inputCustoFinal == null || inputCustoFinal.trim() == '') {
				inputCustoFinal = 0.00;
			}

			if (inputMargemLucro == null || inputMargemLucro.trim() == '') {
				inputMargemLucro = 0.00;
			}

			var inputValorVenda = inputMargemLucro == 0.00 ? 0.00 : parseFloat(inputCustoFinal) + (parseFloat(inputMargemLucro) * parseFloat(inputCustoFinal)) / 100;

			inputValorVenda = float2moeda(inputValorVenda).toString();

			$('#inputValorVenda').val(inputValorVenda);
		}

		$('#alterar').on('click', (e) => {
			e.preventDefault();
			$('#formServico').submit();
		});

		$('#grupo').on('change', function(e) {
			// vai preencher subGrupo

		});
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

					<form id="formServico" name="formServico" method="post" class="form-validate-jquery" action="servicoVendaEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Serviço<?php echo $resultDadosFormulario['SrVenNome']; ?>"</h5>
						</div>

						<input type="hidden" id="inputServicoId" name="inputServicoId" value="<?php echo $resultDadosFormulario['SrVenId']; ?>">
						<input type="hidden" id="inputServicoStatus" name="inputServicoStatus" value="<?php echo $resultDadosFormulario['SituaChave']; ?>">
						<input type="hidden" id="inputServicoTipo" name="inputServicoTipo" value="<?php echo $resultDadosFormulario['SrVenTipoServico']; ?>">
						<input type="hidden" id="inputGrupo" name="inputGrupo" value="<?php echo $resultDadosFormulario['SrVenGrupo']; ?>">
						<input type="hidden" id="inputSubgrupo" name="inputSubgrupo" value="<?php echo $resultDadosFormulario['SrVenSubGrupo']; ?>">
						<select style="display:none" id="possiveisSubgrupos" name="Subgrupos">
							<?php foreach ($arraySubgrupos as $item) {
								print("<option>" . json_encode($item) . "</option>");
							} ?>"
						</select>
						<input type="hidden" id="inputPlanoDeConta" name="inputPlanoDeConta" value="<?php echo $resultDadosFormulario['SrVenPlanoConta']; ?>">



						<div class="card-body">

							<div class="media">

								<div class="media-body">

									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCodigo">Código</label>
												<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código" value="<?php echo $resultDadosFormulario['SrVenCodigo']; ?>">
											</div>
										</div>

										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome <span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $resultDadosFormulario['SrVenNome']; ?>" required>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="tipoServico">Tipos de Serviços <span class="text-danger">*</span></label>
												<select id="tipoServico" name="tipoServico" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													foreach ($tiposServico as $item) {
														print('<option value="' . $item['TpSerId'] . '">' . $item['TpSerCodigo'] . " - " . $item['TpSerNome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDetalhamento">Detalhamento</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do serviço"><?php echo $resultDadosFormulario['SrVenDetalhamento']; ?></textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="grupo">Grupo<span class="text-danger">*</span></label>
												<select onchange='atualizaSubGrupos()' id="grupo" name="grupo" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													foreach ($grupos as $item) {
														print('<option value="' . $item['AtGruId'] . '">' . $item['AtGruNome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="subGrupo">Subgrupo<span class="text-danger">*</span></label>
												<select id="subGrupo" name="subGrupo" class="form-control form-control-select2" required>
													<option value="">Selecione primeiro um grupo</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbPlanoConta">Plano de Conta <span class="text-danger">*</span></label>
												<select id="cmbPlanoConta" name="cmbPlanoConta" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT PlConId, PlConNome
                                                            FROM PlanoConta
                                                            JOIN Situacao on SituaId = PlConStatus
                                                            WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                                            ORDER BY PlConNome ASC";
													$result = $conn->query($sql);
													$rowPlanoConta = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowPlanoConta as $item) {
														$seleciona = $item['PlConId'] == $row['SrVenPlanoConta'] ? "selected" : "";
														print('<option value="' . $item['PlConId'] . '" ' . $seleciona . '>' . $item['PlConNome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<h5 class="mb-0 font-weight-semibold">Preço do Serviço</h5>
											<br>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="modalidades">Modalidades <span class="text-danger">*</span></label>
												<select id="modalidades" name="modalidades" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													foreach ($modalidades as $item) {
														print('<option value="' . $item['AtModId'] . '">' . $item['AtModNome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorCusto">Valor de Custo</label>
												<input type="text" id="inputValorCusto" name="inputValorCusto" class="form-control" placeholder="Valor de Custo" value="" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputOutrasDespesas">Outras Despesas</label>
												<input type="text" id="inputOutrasDespesas" name="inputOutrasDespesas" class="form-control" placeholder="Outras Despesas" value="" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCustoFinal">Custo Final</label>
												<input type="text" id="inputCustoFinal" name="inputCustoFinal" class="form-control" placeholder="Custo Final" value="" readOnly>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputMargemLucro">Margem de Lucro (%)</label>
												<input type="text" id="inputMargemLucro" name="inputMargemLucro" class="form-control" placeholder="Margem Lucro" value="" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputValorVenda">Valor de Venda</label>
												<input type="text" id="inputValorVenda" name="inputValorVenda" class="form-control" placeholder="Valor de Venda" value="" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<br>
						<div class="row" style="margin-top: 40px;">
							<div class="col-lg-12">
								<div class="form-group">
									<?php
									if ($_POST['inputPermission']) {
										echo '<button id="alterar" class="btn btn-lg btn-principal" type="submit">Alterar</button>';
									}
									?>
									<a href="servicoVenda.php" class="btn btn-basic" role="button">Cancelar</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

			<?php include_once("footer.php"); ?>

		</div>
	</div>
</body>

</html>