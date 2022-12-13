<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Serviço';

include('global_assets/php/conexao.php');

if (isset($_POST['inputNome'])) {

	try {

		$sql = "INSERT INTO ServicoVenda (SrVenCodigo, SrVenNome, SrVenTipoServico, SrVenGrupo, SrVenSubGrupo, SrVenPlanoConta, SrVenDetalhamento, SrVenValorCusto, SrVenOutrasDespesas, SrVenCustoFinal, 
		                                  SrVenMargemLucro, SrVenValorVenda, SrVenStatus, SrVenUsuarioAtualizador, SrVenUnidade) 
				VALUES ( :sCodigo, :sNome, :iTipoServico, :iGrupo, :iSubGrupo, :sPlanoConta, :sDetalhamento, :fValorCusto, :fOutrasDespesas, :fCustoFinal, 
				         :fMargemLucro, :fValorVenda, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sCodigo' => $_POST['inputCodigo'],
			':sNome' => $_POST['inputNome'],
			':iTipoServico' => $_POST['tipoServico'],
			':iGrupo' => $_POST['grupo'],
			':iSubGrupo' => $_POST['subGrupo'],
			':sPlanoConta' => $_POST['cmbPlanoConta'],
			':sDetalhamento' => $_POST['txtDetalhamento'],
			':fValorCusto' => $_POST['inputValorCusto'] == null ? null : gravaValor($_POST['inputValorCusto']),
			':fOutrasDespesas' => $_POST['inputOutrasDespesas'] == null ? null : gravaValor($_POST['inputOutrasDespesas']),
			':fCustoFinal' => $_POST['inputCustoFinal'] == null ? null : gravaValor($_POST['inputCustoFinal']),
			':fMargemLucro' => $_POST['inputMargemLucro'] == null ? null : gravaValor($_POST['inputMargemLucro']),
			':fValorVenda' => $_POST['inputValorVenda'] == null ? null : gravaValor($_POST['inputValorVenda']),
			':bStatus' => 1,
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Serviço incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir serviço !!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error2: ' . $e->getMessage();
		die;
	}

	irpara("servicoVenda.php");
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

	<!-- Theme JS files -->
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$('#incluirServico').on('click', function(e) {
			e.preventDefault();
			let menssageError = ''
			let inputCodigo = $('#inputCodigo').val()
			let inputNome = $('#inputNome').val()
			let tipoServico = $('#tipoServico').val()
			let grupo = $('#grupo').val()
			let subGrupo = $('#subgrupo').val()
			let cmbPlanoConta = $('#cmbPlanoConta').val()
			let modalidades = $('#modalidades').val()

			switch (menssageError) {
				case servico:
					menssageError = 'informe o tipo do serviço';
					$('#servico').focus();
					break;
				case grupo:
					menssageError = 'informe o grupo';
					$('#grupo').focus();
					break;
				case subGrupo:
					menssageError = 'informe o subrupo';
					$('#subGrupo').focus();
					break;
				case cmbPlanoConta:
					menssageError = 'informe o plano de contas';
					$('#cmbPlanoConta').focus();
					break;
				case modalidades:
					menssageError = 'informe a modalidade';
					$('#modalidades').focus();
					break;
				default:
					menssageError = '';
					break;
			}

			if (menssageError) {
				alerta('Campo Obrigatório!', menssageError, 'error')
				return
			}

			$.ajax({
				type: 'POST',
				url: 'filtraServicoVenda.php',
				dataType: 'json',
				data: {
					'tipoRequest': 'ADICIONARSERVICO',
					'inputCodigo': inputCodigo,
					'inputNome': inputNome,
					'tipoServico': tipoServico,
					'grupo': grupo,
					'subGrupo': subGrupo,
					'cmbPlanoConta': cmbPlanoConta,
					'modalidades': modalidades,
				},
				success: function(response) {
					if (response.status == 'success') {
						resetServicoCmb()
						checkServicos()
						alerta(response.titulo, response.menssagem, response.status)
					} else {
						alerta(response.titulo, response.menssagem, response.status);
					}
				},
				error: function(response) {
					alerta(response.titulo, response.menssagem, response.status);
				}
			});
		})

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

		$('#cancelar').on('click', function(e) {
			e.preventDefault();
			$(window.document.location).attr('href', "servicoVenda.php");
		});

		$('#grupo').on('change', function(e) {
			// vai preencher subGrupo
			e.preventDefault();
			if ($('#grupo').val() == "") {
				$('#subGrupo').empty();
				let opt = '<option value="">Selecione primeiro um grupo</option>';
				$('#subGrupo').append(opt);
			} else {

				$.ajax({
					type: 'POST',
					url: 'filtraServicoVenda.php',
					dataType: 'json',
					data: {
						'tipoRequest': 'OBTERSUBGRUPOS',
						'grupo': $('#grupo').val()
					},
					success: function(response) {
						$('#subGrupo').empty();
						$('#subGrupo').append(`<option value=''>Selecione</option>`)
						response.forEach(item => {
							let opt = `<option value="${item.AtSubId}">${item.AtSubNome}</option>`
							$('#subGrupo').append(opt)
						})
					}
				});
			}

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

					<form id="formServico" name="formServico" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Serviço</h5>
						</div>

						<div class="card-body">
							<div class="media">
								<div class="media-body">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCodigo">Código <span class="text-danger">*</span></label>
												<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código" required>
											</div>
										</div>

										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome <span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="tipoServico">Tipos de Serviços <span class="text-danger">*</span></label>
												<select id="tipoServico" name="tipoServico" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT TpSerId,TpSerCodigo,TpSerNome
															FROM TipoServico ts
															JOIN Situacao s ON SituaId = ts.TpSerStatus
															WHERE s.SituaChave = 'ATIVO' AND ts.TpSerUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY ts.TpSerNome asc;";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
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
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do Serviço"></textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="grupo">Grupo<span class="text-danger">*</span></label>
												<select id="grupo" name="grupo" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT AtGruId,AtGruNome
															FROM AtendimentoGrupo a
													        JOIN Situacao s ON SituaId = a.AtGruStatus
													        WHERE s.SituaChave = 'ATIVO' AND a.AtGruUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY a.AtGruNome asc;";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
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
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
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
													$sql = "SELECT AtModId,AtModNome
															FROM AtendimentoModalidade am 
															JOIN Situacao s on SituaId = am.AtModSituacao
															WHERE s.SituaChave = 'ATIVO' AND am.AtModUnidade = " . $_SESSION['UnidadeId'] . " ORDER BY am.AtModNome asc;";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['AtModId'] . '">' . $item['AtModNome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorCusto">Valor de Custo</label>
												<input type="text" id="inputValorCusto" name="inputValorCusto" class="form-control" placeholder="Valor de Custo" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputOutrasDespesas">Outras Despesas</label>
												<input type="text" id="inputOutrasDespesas" name="inputOutrasDespesas" class="form-control" placeholder="Outras Despesas" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCustoFinal">Custo Final</label>
												<input type="text" id="inputCustoFinal" name="inputCustoFinal" class="form-control" placeholder="Custo Final" readOnly>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputMargemLucro">Margem de Lucro (%)</label>
												<input type="text" id="inputMargemLucro" name="inputMargemLucro" class="form-control" placeholder="Margem Lucro" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorVenda">Valor de Venda</label>
												<input type="text" id="inputValorVenda" name="inputValorVenda" class="form-control" placeholder="Valor de Venda" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>
									</div>

									<div class="row" style="margin-top: 40px;">
										<div class="col-lg-12">
											<div class="form-group">
												<button class="btn btn-lg btn-principal" id="incluirServico">Incluir</button>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<table class="table" id="precoServicosTable" style="width: 83rem;">
												<thead>
													<tr class="bg-slate text-left">
														<th style="width: 36rem;">Modalidade do Serviço</th>
														<th style="width: 5rem;">Valor de venda</th>
														<th style="width: 5rem;">Situação</th>
														<th style="width: 3rem;">Ações</th>
													</tr>
												</thead>
												<tbody id="precoServicosRows">
													<?php
													$sql = "SELECT *
															FROM DBLamparinas.dbo.ServicoVendaXModalidade
															WHERE SVXMoStatus = 1
															AND SVXMoUnidade = " . $_SESSION['UnidadeId'] . ";";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
													foreach ($row as $item) {
														print('<tr>
																<td>Modalidade 1</td>
																<td>Valor de venda 1</td>
																<td>Situação 1</td>
																<td>Ações 1</td>
															</tr>');
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="salvar">Salvar</button>
										<a href="servicoVenda.php" class="btn btn-lg" id="cancelar">Cancelar</a>
									</div>
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