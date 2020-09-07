<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Editar Serviço de Orçamento';

include('global_assets/php/conexao.php');

$sql = "SELECT TRXSrTermoReferencia
	            FROM TermoReferenciaXServico
				JOIN ServicoOrcamento on SrOrcId =TRXSrServico
	            JOIN TermoReferencia on TrRefId =TRXSrTermoReferencia
				JOIN Situacao on Situaid = TrRefStatus
	            WHERE TRXSrServico = " . $_POST['inputSrOrcId'] . " and (SituaChave = 'ATIVO' or SituaChave = 'AGUARDANDOLIBERACAO' or SituaChave = 'FINALIZADO')  and TRXSrUnidade = " . $_SESSION['UnidadeId'] . "
	            ";
$result = $conn->query($sql);
$rowTrs = $result->fetchAll(PDO::FETCH_ASSOC);
$contTRs = count($rowTrs);

$sql = "SELECT SrOrcId, SrOrcNome, SrOrcDetalhamento, SrOrcCategoria, SrOrcSubCategoria 
		FROM ServicoOrcamento
		WHERE SrOrcId = " . $_POST['inputSrOrcId'] . " and SrOrcUnidade = " . $_SESSION['UnidadeId'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Serviço para Orçamento</title>

	<?php include_once("head.php"); ?>

	<!---------------------------------Scripts Universais------------------------------------>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script type="text/javascript">
		$(document).ready(() => {

			// No evento de selecionar a categoria as subcategorias são carregadas altomaticamente
			$("#cmbCategoria").change((e) => {

				Filtrando();
				let option = null; //'<option>Selecione a SubCategoria</option>';
				const categId = $('#cmbCategoria').val();
				const selectedId = $('#cmbSubCategoria').attr('valId');

				$.getJSON('filtraSubCategoria.php?idCategoria=' + categId, function(dados) {
					//let option = '<option>Selecione a SubCategoria</option>';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							if (obj.SbCatId == selectedId) {
								option += '<option value="' + obj.SbCatId + '" selected>' + obj.SbCatNome + '</option>';
							} else {
								option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
							}
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						Reset();
					}
				});
			});

			// No carregamento da pagina é regatada a opção já cadastrada no banco
			$(document).ready(() => {
				Filtrando();
				let option = null; //'<option>Selecione a SubCategoria</option>';
				const categId = $('#cmbCategoria').val();
				const selectedId = $('#cmbSubCategoria').attr('valId');

				$.getJSON('filtraSubCategoria.php?idCategoria=' + categId, function(dados) {

					if (dados.length) {

						$.each(dados, function(i, obj) {
							if (obj.SbCatId == selectedId) {
								option += '<option value="' + obj.SbCatId + '" selected>' + obj.SbCatNome + '</option>';
							} else {
								option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
							}
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						Reset();
					}
				});
			})

			function Filtrando() {
				$('#cmbSubCategoria').empty().append('<option value="">Filtrando...</option>');
			}

			function Reset() {
				$('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
			}

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				let inputNome = $('#inputNome').val();

				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();

				
				$('#cmbCategoria').removeAttr('disabled')
				$('#cmbSubCategoria').removeAttr('disabled')

				$("#formServico").attr('action', 'servicoOrcamentoEditaAction.php').submit();
			});
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
				<!-------------------------------------------------------------------------------------------------------------------------------->
				<div class="card">

					<form id="formServico" name="formServico" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Serviço</h5>
						</div>
						<div class="card-body">
							<div class="media">
								<div class="media-body">
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="inputNome">Nome <span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['SrOrcNome']; ?>" required>
												<input id="inputId" type="hidden" value="<?php echo $row['SrOrcId'] ?>" name="inputId">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDetalhamento">Detalhamento</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do serviço"><?php echo $row['SrOrcDetalhamento'] ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Classificação</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria <span class="text-danger">*</span></label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required <?php $contTRs >= 1 ? print('disabled') : ''?>>
													<?php
													$sql = "SELECT CategId, CategNome
															FROM Categoria
															JOIN Situacao on SituaId = CategStatus
															WHERE CategUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
															ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$rowCateg = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCateg as $item) {
														$seleciona = $item['CategId'] == $row['SrOrcCategoria'] ? "selected" : "";
														print('<option value="' . $item['CategId'] . '" ' . $seleciona . '>' . $item['CategNome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2" valId="<?php echo $row['SrOrcSubcategoria']; ?>" <?php $contTRs >= 1 ? print('disabled') : ''?>>
													<option id="selec" required></option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Editar</button>
										<a href="servicoOrcamento.php" class="btn btn-basic" id="cancelar">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
				</div>
				<!-------------------------------------------------------------------------------------------------------------------------------->
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