<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Contrato ';

include('global_assets/php/conexao.php');

$sql = "SELECT TrRefId, TrRefNumero, TrRefCategoria, CategNome, TrRefConteudoInicio, TrRefConteudoFim
		FROM TermoReferencia
		JOIN Categoria ON CategId = TrRefCategoria
		WHERE TrRefUnidade = " . $_SESSION['UnidadeId'] . " and TrRefId = 160";	
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT ParamEmpresaPublica
		FROM Parametro
	    WHERE ParamEmpresa = " . $_SESSION['EmpreId'];
$result = $conn->query($sql);
$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

if ($rowParametro['ParamEmpresaPublica']) {
	$bObrigatorio = "required";
} else {
	$bObrigatorio = "";
}

if (isset($_POST['inputDataInicio'])) {

	try {

		$conn->beginTransaction();

		$sql = "SELECT SituaId
		FROM Situacao
		Where SituaChave = 'PENDENTE' ";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "INSERT INTO FluxoOperacional (FlOpeTermoReferencia, FlOpeFornecedor, FlOpeCategoria, FlOpeDataInicio, FlOpeDataFim, FlOpeNumContrato, FlOpeNumProcesso, FlOpeModalidadeLicitacao,
											  FlOpeValor, FlOpeConteudoInicio, FlOpeConteudoFim, FlOpeStatus, FlOpeUsuarioAtualizador, FlOpeEmpresa, FlOpeUnidade)
				VALUES (:iTermoReferencia, :iFornecedor, :iCategoria, :dDataInicio, :dDataFim, :iNumContrato, :iNumProcesso, :iModalidadeLicitacao,
						:fValor, :sFlOpeConteudoInicio, :sFlOpeConteudoFim, :bStatus, :iUsuarioAtualizador, :iEmpresa, :iUnidade)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
			':iTermoReferencia' => $_POST['inputTermoReferencia'] == '' ? null : $_POST['inputTermoReferencia'],
			':iFornecedor' => $_POST['cmbFornecedor'],
			':iCategoria' => $_POST['cmbCategoria'] == '' ? null : $_POST['cmbCategoria'],
			//':iSubCategoria' => $_POST['cmbSubCategoria'] == '' ? null : $_POST['cmbSubCategoria'],
			':dDataInicio' => $_POST['inputDataInicio'] == '' ? null : $_POST['inputDataInicio'],
			':dDataFim' => $_POST['inputDataFim'] == '' ? null : $_POST['inputDataFim'],
			':iNumContrato' => $_POST['inputNumContrato'],
			':iNumProcesso' => $_POST['inputNumProcesso'],
			':iModalidadeLicitacao' => $_POST['cmbModalidadeLicitacao'] == '' ? null : $_POST['cmbModalidadeLicitacao'],
			':fValor' => gravaValor($_POST['inputValor']),
			':sFlOpeConteudoInicio' => $_POST['txtareaConteudoInicio'],
			':sFlOpeConteudoFim' => $_POST['txtareaConteudoFim'],
			':bStatus' => $rowSituacao['SituaId'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iEmpresa' => $_SESSION['EmpreId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));
			
		$insertId = $conn->lastInsertId();	
		
		if ($_POST['cmbSubCategoria']){
			
			
			$sql = "INSERT INTO FluxoOperacionalXSubCategoria 
						(FOXSCFluxo, FOXSCSubCategoria, FOXSCUnidade)
					VALUES 
						(:iFluxo, :iSubCategoria, :iUnidade)";
			$result = $conn->prepare($sql);

			foreach ($_POST['cmbSubCategoria'] as $key => $value){

				$result->execute(array(
								':iFluxo' => $insertId,
								':iSubCategoria' => $value,
								':iUnidade' => $_SESSION['UnidadeId']
								));
			}
		}
		
		/*
		$sql = "SELECT *
				FROM Produto
				JOIN Categoria on CategId = ProduCategoria
				JOIN SubCategoria on SbCatId = ProduSubCategoria
				Where ProduEmpresa = ".$_SESSION['EmpreId']." and CategId = ".$_POST['cmbCategoria']." and SbCatId = ".$_POST['cmbSubCategoria'];
		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($rowProdutos as $item){
		
			$sql = "INSERT INTO FluxoOperacionalXProduto (FOXPrFluxoOperacional, FOXPrProduto, FOXPrQuantidade, FOXPrValorUnitario, FOXPrUsuarioAtualizador, FOXPrEmpresa)
					VALUES (:iFluxoOperacional, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':iFluxoOperacional' => $insertId,
							':iProduto' => $item['ProduId'],
							':iQuantidade' => NULL,
							':fValorUnitario' => NULL,
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpreId']
							));		
		} 
		*/
		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = " Contrato incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir o contrato!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		die;
	}

	irpara("contrato.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Contrato</title>

	<?php include_once("head.php"); ?>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script> <!-- CV Documentacao: https://jqueryvalidation.org/ -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			//Inicializa o editor de texto que será usado pelos campos "Conteúdo Personalizado - Inicialização" e "Conteúdo Personalizado - Finalização"
			$('#summernoteInicio').summernote();
			$('#summernoteFim').summernote();


			//Ao mudar o Fornecedor, filtra a categoria e a SubCategoria via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e) {

				FiltraCategoria();
				FiltraSubCategoria();

				var cmbFornecedor = $('#cmbFornecedor').val();
				var validator = $("#formFluxoOperacional").validate();

				validator.element("#cmbFornecedor"); //Valida apenas esse elemento nesse momento de alteração

				$.getJSON('filtraCategoria.php?idFornecedor=' + cmbFornecedor, function(dados) {

					//var option = '<option value="#">Selecione a Categoria</option>';
					var option = '';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.CategId + '">' + obj.CategNome + '</option>';
						});

						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}

					validator.element("#cmbCategoria"); //Valida apenas esse elemento nesse momento de alteração
				});

				$.getJSON('filtraSubCategoria.php?idFornecedor=' + cmbFornecedor, function(dados) {

					if (dados.length > 1) {
						var option = '<option value="">Selecione a SubCategoria</option>';
					} else {
						var option = '';
					}

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}

					validator.element("#cmbSubCategoria"); //Valida apenas esse elemento nesse momento de alteração
				});



			});

			//Mostra o "Filtrando..." na combo Categoria
			function FiltraCategoria() {
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
			}

			//Mostra o "Filtrando..." na combo SubCategoria
			function FiltraSubCategoria() {
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			}

			function ResetCategoria() {
				$('#cmbCategoria').empty().append('<option value="">Sem Categoria</option>');
			}

			function ResetSubCategoria() {
				$('#cmbSubCategoria').empty().append('<option value="">Sem SubCategoria</option>');
			}

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				var cmbFornecedor = $('#cmbFornecedor').val();
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				var inputDataInicio = $('#inputDataInicio').val();
				var inputDataFim = $('#inputDataFim').val();
				var inputValor = $('#inputValor').val().replace('.', '').replace(',', '.');

				if (inputDataFim < inputDataInicio) {
					alerta('Atenção', 'A Data Fim deve ser maior que a Data Início!', 'error');
					$('#inputDataFim').focus();
					return false;
				}

				$("#formFluxoOperacional").submit();

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

				<!-- Info blocks -->
				<div class="card">

					<form name="formFluxoOperacional" id="formFluxoOperacional" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Contrato</h5>
						</div>

						<div class="card-body">

							<h5 class="mb-0 font-weight-semibold">Termo de Referência</h5>
							<br>
                            <div class="row">
                                 <div class="col-lg-3">
									<div class="form-group">
										<label for="inputTermoReferencia">Nº do Termo de Referência</label>
										<input type="text" id="inputTermoReferencia" name="inputTermoReferencia" class="form-control" placeholder="Nº da TR" value="<?php echo $row['TrRefNumero']; ?>" readOnly>
									</div>
								</div>
                            </div>

							<h5 class="mb-0 font-weight-semibold">Dados do Fornecedor</h5>
							<br>
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbFornecedor">Fornecedor <span class="text-danger">*</span></label>
										<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2" required>
											<option value="">Selecione</option>
											<?php
											$sql = "SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
													FROM Fornecedor
													JOIN Situacao on SituaId = ForneStatus
													WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
													ORDER BY ForneNome ASC";
											$result = $conn->query($sql);
											$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);

											foreach ($rowFornecedor as $item) {
												print('<option value="' . $item['ForneId'] . '">' . $item['ForneNome'] . '</option>');
											}

											?>
										</select>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbCategoria">Categoria <span class="text-danger">*</span> </label>
										<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" disabled>
											<option value="">Selecione</option>
											<?php
											$sql = "SELECT CategId, CategNome
													FROM Categoria
													JOIN TermoReferencia on TrRefCategoria = CategId
													JOIN Situacao on SituaId = CategStatus
													WHERE CategUnidade = " . $_SESSION['UnidadeId'] . " 
													ORDER BY CategNome ASC";
											$result = $conn->query($sql);
											$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

											foreach ($rowCategoria as $item) {
												$seleciona = $item['CategId'] == $row['TrRefCategoria'] ? "selected" : "";
												print('<option value="' . $item['CategId'] . '" ' . $seleciona . '>' . $item['CategNome'] . '</option>');
											}

											?>
										</select>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group" style="border-bottom:1px solid #ddd;">
										<label for="cmbSubCategoria">SubCategoria</label>
										<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control select" multiple="multiple" data-fouc>
										</select>
									</div>
								</div>
							</div>

							<h5 class="mb-0 font-weight-semibold">Dados do Contrato</h5>
							<br>
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" required>
										</div>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputDataFim">Data Fim <span class="text-danger">*</span></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" required>
										</div>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputNumContrato">Nº Ata Registro <?php if ($bObrigatorio) echo '<span class="text-danger">*</span>'; ?></label>
										<input type="text" id="inputNumContrato" name="inputNumContrato" class="form-control" placeholder="Nº Ata Registro" <?php echo $bObrigatorio; ?>>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="cmbModalidadeLicitacao">Modalidade de Licitação</label>
										<select id="cmbModalidadeLicitacao" name="cmbModalidadeLicitacao" class="form-control form-control-select2">
											<option value="">Selecione</option>
											<?php
											$sql = "SELECT MdLicId, MdLicNome
													FROM ModalidadeLicitacao
													JOIN Situacao on SituaId = MdLicStatus
													WHERE SituaChave = 'ATIVO'
													ORDER BY MdLicNome ASC";
											$result = $conn->query($sql);
											$rowModalidade = $result->fetchAll(PDO::FETCH_ASSOC);

											foreach ($rowModalidade as $item) {
												print('<option value="' . $item['MdLicId'] . '">' . $item['MdLicNome'] . '</option>');
											}
											?>
										</select>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputNumProcesso">Número do Processo <?php if ($bObrigatorio) echo '<span class="text-danger">*</span>'; ?></label>
										<input type="text" id="inputNumProcesso" name="inputNumProcesso" class="form-control" placeholder="Nº do Processo" <?php echo $bObrigatorio; ?>>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputValor">Valor Total <span class="text-danger">*</span></label>
										<input type="text" id="inputValor" name="inputValor" class="form-control" placeholder="Valor Total" onKeyUp="moeda(this)" maxLength="12" required>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo Personalizado - Introdução</label>
										<!--<div id="summernote" name="txtareaConteudo"></div>-->
										<textarea rows="5" cols="5" class="form-control" id="summernoteInicio" name="txtareaConteudoInicio" placeholder="Corpo do Contrato (informe aqui o texto que você queira que apareça no Contrato)"><?php echo $row['TrRefConteudoInicio']; ?></textarea>
									</div>
								</div>
							</div>
							<br>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudoFinalizacao">Conteúdo Personalizado - Finalização</label>
										<!--<div id="summernote" name="txtareaConteudo"></div>-->
										<textarea rows="5" cols="5" class="form-control" id="summernoteFim" name="txtareaConteudoFim" placeholder="Considerações Finais do Contrato (informe aqui o texto que você queira que apareça no término Contrato)"><?php echo $row['TrRefConteudoFim']; ?></textarea>
									</div>
								</div>
							</div>
							<br>
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="contrato.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
									</div>
								</div>
							</div>
					</form>

				</div>
				<!-- /card-body -->

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