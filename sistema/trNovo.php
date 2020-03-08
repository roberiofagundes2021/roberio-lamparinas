<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Termo de Referência';

include('global_assets/php/conexao.php');

if (isset($_POST['inputData'])) {

	$sql = "SELECT ParamProdutoOrcamento, ParamServicoOrcamento
	        FROM Parametro
	        WHERE ParamEmpresa = " . $_SESSION['EmpreId'] . " 
	        ";
	$result = $conn->query($sql);
	$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

	$parametro = '';
	isset($rowParametro['ParamProdutoOrcamento']) && $rowParametro['ParamProdutoOrcamento'] != 0 ? $parametro = 'ProdutoOrcamento' : $parametro = 'Produto';

	$tipoTr = '';

	if (isset($_POST['TrProduto']) && isset($_POST['TrServico'])) {
		$tipoTr = 'PS';
	} else if (isset($_POST['TrProduto'])) {
		$tipoTr = 'P';
	} else if (isset($_POST['TrServico'])) {
		$tipoTr = 'S';
	}

	try {

		$conn->beginTransaction();

		$sql = ("SELECT COUNT(isnull(TrRefNumero,0)) as Numero
				 FROM TermoReferencia
				 Where TrRefEmpresa = " . $_SESSION['EmpreId'] . "");
		$result = $conn->query("$sql");
		$rowNumero = $result->fetch(PDO::FETCH_ASSOC);

		$sNumero = (int) $rowNumero['Numero'] + 1;
		$sNumero = str_pad($sNumero, 6, "0", STR_PAD_LEFT);

		$sql = "INSERT INTO TermoReferencia (TrRefNumero, TrRefData, TrRefCategoria, TrRefConteudo, TrRefTipo,
											 TrRefStatus, TrRefUsuarioAtualizador, TrRefEmpresa)
				VALUES (:sNumero, :dData, :iCategoria, :sConteudo, :sTipo, 
						:bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sNumero' => $sNumero,
			':dData' => gravaData($_POST['inputData']),
			':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
			':sConteudo' => $_POST['txtareaConteudo'],
			':sTipo' => $tipoTr,
			':bStatus' => 1,
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));

		// Começo do cadastro de subcategorias da TR
		$insertId = $conn->lastInsertId();



		if (isset($_POST['cmbSubCategoria'])) {

			try {
				$sql = "INSERT INTO TRXSubcategoria
							(TRXSCTermoReferencia, TRXSCSubCategoria, TRXSCEmpresa)
						VALUES 
							(:iTermoReferencia, :iSubCategoria, :iEmpresa)";
				$result = $conn->prepare($sql);

				foreach ($_POST['cmbSubCategoria'] as $key => $value) {

					$result->execute(array(
						':iTermoReferencia' => $insertId,
						':iSubCategoria' => $value,
						':iEmpresa' => $_SESSION['EmpreId']
					));
				}

				if ($tipoTr == 'PS') {
					// Gravando os produtos da categoria e subcategoria(s)
					if ($rowParametro['ParamProdutoOrcamento'] != 0) {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT PrOrcId
									FROM ProdutoOrcamento
									WHERE PrOrcSubcategoria = " . $value . "";
							$result = $conn->query($sql);
							$produtos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($produtos as $produto) {
								try {
									$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
											VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($produto) {

										$result->execute(array(
											':iTR' => $insertId,
											':iProduto' => $produto['PrOrcId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					} else {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT ProduId
									FROM Produto
									WHERE ProduSubCategoria = " . $value . "";
							$result = $conn->query($sql);
							$produtos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($produtos as $produto) {
								try {
									$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
											VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($produto) {

										$result->execute(array(
											':iTR' => $insertId,
											':iProduto' => $produto['ProduId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					}

					// Gravando os serviços da categoria e subcategoria(s)
					if ($rowParametro['ParamServicoOrcamento'] != 0) {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT SrOrcId
									FROM ServicoOrcamento
									WHERE SrOrcSubcategoria = " . $value . "";
							$result = $conn->query($sql);
							$servicos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($servicos as $servico) {
								try {
									$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrProduto, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
											VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($servico) {

										$result->execute(array(
											':iTR' => $insertId,
											':iServico' => $servico['SrOrcId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					} else {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT ServiId
									FROM Servico
									WHERE ServiSubCategoria = " . $value . "";
							$result = $conn->query($sql);
							$servicos = $result->fetchAll(PDO::FETCH_ASSOC);

							foreach ($servicos as $servico) {
								try {
									$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrServico, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
											VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($servico) {

										$result->execute(array(
											':iTR' => $insertId,
											':iServico' => $servico['ServiId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					}
				} else if ($tipoTr == 'P') {
					// Gravando os produtos da categoria e subcategoria(s)
					if ($rowParametro['ParamProdutoOrcamento'] != 0) {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT PrOrcId
									FROM ProdutoOrcamento
									WHERE PrOrcSubcategoria = " . $value . "";
							$result = $conn->query($sql);
							$produtos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($produtos as $produto) {
								try {
									$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
											VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($produto) {

										$result->execute(array(
											':iTR' => $insertId,
											':iProduto' => $produto['PrOrcId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					} else {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT ProduId
									FROM Produto
									WHERE ProduSubCategoria = " . $value . "";
							$result = $conn->query($sql);
							$produtos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($produtos as $produto) {
								try {
									$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrTabela, TRXPrUsuarioAtualizador, TRXPrEmpresa)
											VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);
									var_dump($sql);

									if ($produto) {

										$result->execute(array(
											':iTR' => $insertId,
											':iProduto' => $produto['ProduId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					}
				} else {
					// Gravando os serviços da categoria e subcategoria(s)
					if ($rowParametro['ParamServicoOrcamento'] != 0) {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT SrOrcId
									FROM ServicoOrcamento
									WHERE SrOrcSubcategoria = " . $value . "";
							$result = $conn->query($sql);
							$servicos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($servicos as $servico) {
								try {
									$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrProduto, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
											VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($servico) {

										$result->execute(array(
											':iTR' => $insertId,
											':iServico' => $servico['SrOrcId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					} else {
						foreach ($_POST['cmbSubCategoria'] as $value) {
							$sql = "SELECT ServiId
									FROM Servico
									WHERE ServiSubCategoria = " . $value . "";
							$result = $conn->query($sql);
							$servicos = $result->fetchAll(PDO::FETCH_ASSOC);


							foreach ($servicos as $servico) {
								try {
									$sql = "INSERT INTO TermoReferenciaXServico (TRXSrTermoReferencia, TRXSrProduto, TRXSrQuantidade, TRXSrValorUnitario, TRXSrTabela, TRXSrUsuarioAtualizador, TRXSrEmpresa)
											VALUES (:iTR, :iServico, :iQuantidade, :fValorUnitario, :sTabela, :iUsuarioAtualizador, :iEmpresa)";
									$result = $conn->prepare($sql);

									if ($servico) {

										$result->execute(array(
											':iTR' => $insertId,
											':iServico' => $servico['ServiId'],
											':iQuantidade' => null,
											':fValorUnitario' => null,
											':sTabela' => $parametro,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iEmpresa' => $_SESSION['EmpreId'],
										));
									}
								} catch (PDOException $e) {

									echo 'Error: ' . $e->getMessage();
									exit;
								}
							}
						}
					}
				}
			} catch (PDOException $e) {
				$conn->rollback();
				echo 'Error: ' . $e->getMessage();
				exit;
			}
		}

		$conn->commit();

		// Fim de cadastro

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Termo de referência incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir termo de referência!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		die;
	}

	irpara("tr.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Termo de Referência</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.	min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->

	<!-- JS file path -->
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<!-- Uniform plugin file path -->
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			$('#summernote').summernote();

			$('#formTR').validate({ // initialize the plugin
				rules: {
					'TrProduto': {
						required: true,
						minlength: 1
					}
				},
				messages: {
					'TrProduto': {
						required: "*",
						maxlength: "Check no more than {0} boxes"
					}
				}
			});

			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e) {

				Filtrando();

				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function(dados) {

					var option = null;

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}
				});

			});

			$("#enviar").on('click', function(e) {

				e.preventDefault();

				var cmbCategoria = $('#cmbCategoria').val();

				if (cmbCategoria == '' || cmbCategoria == '#') {
					alerta('Atenção', 'Informe a categoria!', 'error');
					$('#cmbCategoria').focus();
					return false;
				}

				$("#formTR").submit();
			});

		}); //document.ready

		//Mostra o "Filtrando..." na combo SubCategoria
		function Filtrando() {
			$('#cmbSubCategoria').empty().append('<option value="#">Filtrando...</option>');
		}

		function ResetSubCategoria() {
			$('#cmbSubCategoria').empty().append('<option value="#">Sem Subcategoria</option>');
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

					<form name="formTR" id="formTR" method="post" action="trNovo.php" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Termo de Referência</h5>
						</div>

						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputData">O TR terá:</label>
												<div class="d-flex flex-row">
													<div class="p-1 m-0 d-flex flex-row">
														<input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox">
														<label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
													</div>
													<div class="p-1 m-0 d-flex flex-row">
														<input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox">
														<label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
													</div>
												</div>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = " . $_SESSION['EmpreId'] . " and CategStatus = 1
															     ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['CategId'] . '">' . $item['CategNome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group" style="border-bottom:1px solid #ddd;">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control form-control-select2 select" multiple="multiple" data-fouc>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<!--<div id="summernote" name="txtareaConteudo"></div>-->
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo da TR (informe aqui o texto que você queira que apareça na TR)"></textarea>
									</div>
								</div>
							</div>
							<br>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
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

</body>

</html>