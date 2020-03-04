<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Parâmetro';

include('global_assets/php/conexao.php');

if (isset($_SESSION['EmpresaId'])) {
	$sql = "SELECT ParamId, ParamEmpresaPublica, ParamValorAtualizadoFluxo, ParamValorAtualizadoOrdemCompra, ParamValorObsImpreRetirada, ParamProdutoOrcamento
	        FROM Parametro
	        WHERE ParamEmpresa = " . $_SESSION['EmpresaId'] . "";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
} else {
	irpara("empresa.php");
}

if (isset($_POST['inputIdEmpresa'])) {

	try {
		//var_dump($_POST);die;
		$sql = "UPDATE Parametro SET ParamEmpresaPublica = :iEmpresaPublica, ParamValorAtualizadoFluxo = :iValorAtualizadoFluxo, 
					   ParamValorAtualizadoOrdemCompra = :iValorAtualizadoOrdemCompra, ParamProdutoOrcamento = :iProdutoOrcamento, ParamUsuarioAtualizador = :iUsuarioAtualizador, ParamValorObsImpreRetirada = :iValorObsImpreRetirada
				WHERE ParamEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iEmpresaPublica' => isset($_POST['inputEmpresaPublica']) && $_POST['inputEmpresaPublica'] == "on" ? 1 : 0,
			':iValorAtualizadoFluxo' => isset($_POST['inputValorFluxo']) && $_POST['inputValorFluxo'] == "on" ? 1 : 0,
			':iValorAtualizadoOrdemCompra' => isset($_POST['inputValorOrdemCompra']) && $_POST['inputValorOrdemCompra'] == "on" ? 1 : 0,
			':iValorObsImpreRetirada' => isset($_POST['inputValorObsImpreRetirada']) && $_POST['inputValorObsImpreRetirada'] == "on" ? 1 : 0,
			':iProdutoOrcamento' => isset($_POST['inputProdutoOrcamento']) && $_POST['inputProdutoOrcamento'] == "on" ? 1 : 0,
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpresaId']
		));
		//die;

	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao atualizar parâmetro!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		die;
	}

	irpara("parametro.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Parâmetro</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script> <!-- CV Documentacao: https://jqueryvalidation.org/ -->

	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			//Garantindo que ninguém mude a empresa na tela de parâmetro
			//$('#cmbEmpresa').prop("disabled", true);	

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				$("#formParametro").submit();
				alerta("Sucesso", "Parâmetro atualizado!!!", "success")

			});

		});
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>

	<!-- Page content -->
	<div class="page-content">

		<?php include_once("menu-left.php"); ?>

		<?php include_once("menuLeftSecundario.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->
				<div class="card">

					<form name="formParametro" id="formParametro" method="post">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Parâmetros</h5>
						</div>

						<div class="card-body">
							<p class="font-size-lg">Parâmetros da empresa <b><?php echo $_SESSION['EmpresaNome']; ?></b></p>
						</div>

						<input type="hidden" id="inputIdEmpresa" name="inputIdEmpresa" class="form-control" value="<?php echo $row['ParamId']; ?>">

						<div class="card-body">

							<div class="row">
								<div class="col-lg-6">
									<!-- Switch single -->
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Empresa Pública <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputEmpresaPublica" id="inputEmpresaPublica" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($row['ParamEmpresaPublica']) echo "checked"; ?>>
												</label>
											</div>
										</div>
									</div>
									<!-- /switch single -->
								</div>
							</div>

							<div class="row">
								<div class="col-lg-6">
									<!-- Switch group -->
									<div class="form-group row">
										<label class="col-form-label col-lg-3">Valor do Produto será atualizado <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputValorFluxo" id="inputValorFluxo" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($row['ParamValorAtualizadoFluxo']) echo "checked"; ?>>
													Fluxo Previsto
												</label>
											</div>

											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputValorOrdemCompra" id="inputValorOrdemCompra" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($row['ParamValorAtualizadoOrdemCompra']) echo "checked"; ?>>
													Ordem de Compra/Carta Contrato
												</label>
											</div>
										</div>
									</div>
									<!-- /switch group -->
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<!-- Switch single -->
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Mostrar "Observação" na impressão das saídas e transferências<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputValorObsImpreRetirada" id="inputValorObsImpreRetirada" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($row['ParamValorObsImpreRetirada']) echo "checked"; ?>>
												</label>
											</div>
										</div>
									</div>
									<!-- /switch single -->
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<!-- Switch single -->
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Usar "Produtos para Orçamento" nos Orçamentos da TR<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputProdutoOrcamento" id="inputProdutoOrcamento" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($row['ParamProdutoOrcamento']) echo "checked"; ?>>
												</label>
											</div>
										</div>
									</div>
									<!-- /switch single -->
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Atualizar</button>
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