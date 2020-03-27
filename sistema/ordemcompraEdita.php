<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Editar Ordem de Compra';

include('global_assets/php/conexao.php');

//Se veio da ordemcompra.php
if (isset($_POST['inputOrdemCompraId'])) {

	$iOrdemCompra = $_POST['inputOrdemCompraId'];

	try {

		$sql = "SELECT OrComId, OrComTipo, OrComDtEmissao, OrComNumero, OrComLote, OrComNumAta, OrComNumProcesso, OrComCategoria, 
					   OrComSubCategoria, OrComConteudo, OrComFornecedor, ForneContato, ForneEmail, ForneTelefone, ForneCelular, 
					   OrComValorFrete, OrComTotalPedido, OrComSolicitante, OrComUnidade, OrComLocalEntrega, 
					   OrComEnderecoEntrega, OrComDtEntrega, OrComObservacao, UsuarNome, UsuarEmail, UsuarTelefone
				FROM OrdemCompra
				JOIN Usuario on UsuarId = OrComSolicitante
				JOIN Fornecedor on ForneId = OrComFornecedor
				WHERE OrComId = $iOrdemCompra ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}

	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("ordemcompra.php");
}

if (isset($_POST['inputTipo'])) {

	try {

		$iOrdemCompra = $_POST['inputOrdemCompraId'];

		$sql = "UPDATE OrdemCompra SET OrComTipo = :sTipo, OrComNumero = :sNumero, OrComLote = :sLote, OrComNumProcesso = :sProcesso, 
									   OrComCategoria = :iCategoria, OrComSubCategoria = :iSubCategoria, OrComConteudo = :sConteudo,  
									   OrComFornecedor = :iFornecedor, OrComUnidade = :iUnidade, OrComLocalEntrega = :iLocalEntrega, 
									   OrComEnderecoEntrega = :sEnderecoEntrega, OrComDtEntrega = :dDataEntrega, 
									   OrComObservacao = :sObservacao,  OrComUsuarioAtualizador = :iUsuarioAtualizador
				WHERE OrComId = :iOrdemCompra";
		$result = $conn->prepare($sql);

		$conn->beginTransaction();

		$aFornecedor = explode("#", $_POST['cmbFornecedor']);
		$iFornecedor = $aFornecedor[0];

		$result->execute(array(
			':sTipo' => $_POST['inputTipo'],
			':sNumero' => $_POST['inputNumero'],
			':sLote' => $_POST['inputLote'],
			':sProcesso' => $_POST['inputProcesso'],
			':iCategoria' => $_POST['cmbCategoria'],
			':iSubCategoria' => $_POST['cmbSubCategoria'] == '' ? null : $_POST['cmbSubCategoria'],
			':sConteudo' => $_POST['txtareaConteudo'],
			':iFornecedor' => $iFornecedor,
			':iUnidade' => $_POST['cmbUnidade'] == '' ? null : $_POST['cmbUnidade'],
			':iLocalEntrega' => $_POST['cmbLocalEstoque'] == '' ? null : $_POST['cmbLocalEstoque'],
			':sEnderecoEntrega' => $_POST['inputEnderecoEntrega'],
			':dDataEntrega' => gravaData($_POST['inputDataEntrega']),
			':sObservacao' => $_POST['txtareaObservacao'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iOrdemCompra' => $iOrdemCompra
		));

		if (isset($_POST['inputOrdemCompraProdutoExclui']) and $_POST['inputOrdemCompraProdutoExclui']) {

			$sql = "DELETE FROM OrdemCompraXProduto
					WHERE OCXPrOrdemCompra = :iOrdemCompra and OCXPrEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iOrdemCompra' => $iOrdemCompra,
				':iEmpresa' => $_SESSION['EmpreId']
			));
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Ordem de Compra alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar ordem de compra!!!";
		$_SESSION['msg']['tipo'] = "error";

		$conn->rollback();

		echo 'Error: ' . $e->getMessage();
		exit;
	}

	irpara("ordemcompra.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Ordem de Compra</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<!-- /theme JS files -->

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		
		window.onload = function() {

			//Ao carregar a página é verificado se é PF ou PJ para aparecer os campos relacionados e esconder o que não estiver
			var tipo = $('input[name="inputTipo"]:checked').val();

			selecionaTipo(tipo);

		}

		$(document).ready(function() {

			$('#summernote').summernote();

			//Ao informar o fornecedor, trazer os demais dados dele (contato, e-mail, telefone)
			$('#cmbFornecedor').on('change', function(e){				
				
				var Fornecedor = $('#cmbFornecedor').val();
				var Forne = Fornecedor.split('#');
				
				$('#inputContato').val(Forne[1]);
				$('#inputEmailFornecedor').val(Forne[2]);
				if(Forne[3] != "" && Forne[3] != "(__) ____-____"){
					$('#inputTelefoneFornecedor').val(Forne[3]);
				} else {
					$('#inputTelefoneFornecedor').val(Forne[4]);
				}
				
				$.getJSON('filtraCategoria.php?idFornecedor='+Forne[0], function (dados){
					
					//var option = '<option value="#">Selecione a Categoria</option>';
					var option = '';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.CategId+'">'+obj.CategNome+'</option>';
						});						
						
						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}					
				});
				
				$.getJSON('filtraSubCategoria.php?idFornecedor='+Forne[0], function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});				
				
			});

			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e) {

				Filtrando();

				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function(dados) {

					var option = '<option value="#">Selecione a SubCategoria</option>';

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

			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbUnidade').on('change', function(e) {

				FiltraLocalEstoque();

				var cmbUnidade = $('#cmbUnidade').val();

				if (cmbUnidade == '#') {
					ResetLocalEstoque();
				} else {

					$.getJSON('filtraLocalEstoque.php?idUnidade=' + cmbUnidade, function(dados) {

						var option = '';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.LcEstId + '">' + obj.LcEstNome + '</option>';
							});

							$('#cmbLocalEstoque').html(option).show();
						} else {
							ResetLocalEstoque();
						}
					});
				}
			});

			$("#enviar").on('click', function(e) {

				e.preventDefault();

				//Antes
				var inputCategoria = $('#inputOrdemCompraCategoria').val();
				var inputSubCategoria = $('#inputOrdemCompraSubCategoria').val();

				//Depois
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();

				//Tem produto cadastrado para essa ordem de compra na tabela OrdemCompraXProduto?
				var inputProduto = $('#inputOrdemCompraProduto').val();

				//Tem serviço cadastrado para essa ordem de compra na tabela OrdemCompraXServico?
				var inputServico = $('#inputOrdemCompraServico').val();

				//Exclui os produtos dessa Ordem de Compra?
				var inputExclui = $('#inputOrdemCompraProdutoExclui').val();

				//Aqui verifica primeiro se tem produtos preenchidos, porque do contrário deixa mudar
				if (inputProduto > 0 || inputServico > 0) {

					//Verifica se o a categoria ou subcategoria foi alterada
					if (inputSubCategoria != cmbSubCategoria) {

						inputExclui = 1;
						$('#inputOrdemCompraProdutoExclui').val(inputExclui);

						confirmaExclusao(document.formOrdemCompra, "Tem certeza que deseja alterar a ordem de compra? Existem produtos ou serviços com quantidades ou valores lançados!", "ordemcompraEdita.php");

					} else {
						inputExclui = 0;
						$('#inputOrdemCompraProdutoExclui').val(inputExclui);
					}
				}

				$("#formOrdemCompra").submit();

			}); // enviar			
		}); //document.ready

		//Mostra o "Filtrando..." na combo SubCategoria
		function Filtrando() {
			$('#cmbSubCategoria').empty().append('<option value="">Filtrando...</option>');
		}

		function FiltraLocalEstoque() {
			$('#cmbLocalEstoque').empty().append('<option value="">Filtrando...</option>');
		}

		function ResetLocalEstoque() {
			$('#cmbLocalEstoque').empty().append('<option value="">Sem Local do Estoque</option>');
		}

		function ResetSubCategoria() {
			$('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
		}

		function selecionaTipo(tipo) {

			if (tipo == 'C') {
				document.getElementById('Ata').style.display = "block";
				document.getElementById('Lote').style.display = "none";
			} else {
				document.getElementById('Ata').style.display = "none";
				document.getElementById('Lote').style.display = "block";
			}
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

					<form name="formOrdemCompra" id="formOrdemCompra" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Ordem de Compra/Contrato Nº "<?php echo $_POST['inputOrdemCompraNumero']; ?>"</h5>
						</div>

						<input type="hidden" id="inputOrdemCompraId" name="inputOrdemCompraId" value="<?php echo $row['OrComId']; ?>">
						<input type="hidden" id="inputOrdemCompraNumero" name="inputOrdemCompraNumero" value="<?php echo $row['OrComNumero']; ?>">
						<input type="hidden" id="inputOrdemCompraCategoria" name="inputOrdemCompraCategoria" value="<?php echo $row['OrComCategoria']; ?>">
						<input type="hidden" id="inputOrdemCompraSubCategoria" name="inputOrdemCompraSubCategoria" value="<?php echo $row['OrComSubCategoria']; ?>">
						<input type="hidden" id="inputOrdemCompraProdutoExclui" name="inputOrdemCompraProdutoExclui" value="0">

						<?php

						$sql = "SELECT OCXPrOrdemCompra
								FROM OrdemCompraXProduto
								WHERE OCXPrOrdemCompra = " . $iOrdemCompra . " and OCXPrEmpresa = " . $_SESSION['EmpreId'];
						$result = $conn->query($sql);
						$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);
						$countProduto = count($rowProduto);

						print('<input type="hidden" id="inputOrdemCompraProduto" name="inputOrdemCompraProduto" value="' . $countProduto . '" >');

						$sql = "SELECT OCXSrOrdemCompra
								FROM OrdemCompraXServico
								WHERE OCXSrOrdemCompra = " . $iOrdemCompra . " and OCXSrEmpresa = " . $_SESSION['EmpreId'];
						$result = $conn->query($sql);
						$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);
						$countServico = count($rowServico);

						print('<input type="hidden" id="inputOrdemCompraServico" name="inputOrdemCompraServico" value="' . $countServico . '" >');

						?>

						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<div class="row">

										<div class="col-lg-4">
											<div class="form-group">
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="C" name="inputTipo" class="form-input-styled" data-fouc onclick="selecionaTipo('C')" <?php if ($row['OrComTipo'] == 'C') echo "checked"; ?>>
														Carta Contrato
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="O" name="inputTipo" class="form-input-styled" data-fouc onclick="selecionaTipo('O')" <?php if ($row['OrComTipo'] == 'O') echo "checked"; ?>>
														Ordem de Compra
													</label>
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data da Emissão <span class="text-danger">*</span></label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo mostraData($row['OrComDtEmissao']); ?>" readOnly required>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNumero">Número <span class="text-danger">*</span></label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control" value="<?php echo $row['OrComNumero']; ?>" required>
											</div>
										</div>

										<div class="col-lg-2" id="Ata" style="display: <?php if ($row['OrComTipo'] == 'O') echo 'none' ?>">
											<div class="form-group">
												<label for="inputNumAta">Nº Ata Registro</label>
												<input type="text" id="inputNumAta" name="inputNumAta" class="form-control" value="<?php echo $row['OrComNumAta']; ?>">
											</div>
										</div>

										<div class="col-lg-2" id="Lote" style="display: <?php if ($row['OrComTipo'] == 'C') echo 'none' ?>">
											<div class="form-group">
												<label for="inputLote">Lote</label>
												<input type="text" id="inputLote" name="inputLote" class="form-control" value="<?php echo $row['OrComLote']; ?>">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputProcesso">Processo</label>
												<input type="text" id="inputProcesso" name="inputProcesso" class="form-control" value="<?php echo $row['OrComNumProcesso']; ?>">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
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
																	WHERE ForneEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
																	ORDER BY ForneNome ASC";
															$result = $conn->query($sql);
															$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowFornecedor as $item) {
																$seleciona = $item['ForneId'] == $row['OrComFornecedor'] ? "selected" : "";
																print('<option value="' . $item['ForneId'] . '#' . $item['ForneContato'] . '#' . $item['ForneEmail'] . '#' . $item['ForneTelefone'] . '#' . $item['ForneCelular'] . '" ' . $seleciona . '>' . $item['ForneNome'] . '</option>');
															}

															?>
														</select>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label for="inputContato">Contato</label>
														<input type="text" id="inputContato" name="inputContato" class="form-control" value="<?php echo $row['ForneContato']; ?>" readOnly>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="form-group">
														<label for="inputEmailFornecedor">E-mail</label>
														<input type="text" id="inputEmailFornecedor" name="inputEmailFornecedor" class="form-control" value="<?php echo $row['ForneEmail']; ?>" readOnly>
													</div>
												</div>

												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputTelefoneFornecedor">Telefone</label>
														<input type="text" id="inputTelefoneFornecedor" name="inputTelefoneFornecedor" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
													</div>
												</div>
											</div>
										</div>
									</div>
									<br>


									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria <span class="text-danger">*</span></label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT CategId, CategNome
															FROM Categoria
															JOIN Situacao on SituaId = CategStatus
															WHERE CategEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
															ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCategoria as $item) {
														$seleciona = $item['CategId'] == $row['OrComCategoria'] ? "selected" : "";
														print('<option value="' . $item['CategId'] . '" ' . $seleciona . '>' . $item['CategNome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>

										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php
													$sql = "SELECT SbCatId, SbCatNome
															FROM SubCategoria
															JOIN Situacao on SituaId = SbCatStatus															     
															WHERE SbCatEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
															ORDER BY SbCatNome ASC";
													$result = $conn->query($sql);
													$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowSubCategoria as $item) {
														$seleciona = $item['SbCatId'] == $row['OrComSubCategoria'] ? "selected" : "";
														print('<option value="' . $item['SbCatId'] . '" ' . $seleciona . '>' . $item['SbCatNome'] . '</option>');
													}

													?>
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
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo do orçamento (informe aqui o texto que você queira que apareça no orçamento)"><?php echo $row['OrComConteudo']; ?></textarea>
									</div>
								</div>
							</div>
							<br>

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Dados do Solicitante</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNomeSolicitante">Solicitante <span class="text-danger">*</span></label>
												<input type="text" id="inputNomeSolicitante" name="inputNomeSolicitante" class="form-control" value="<?php echo $row['UsuarNome']; ?>" readOnly required>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailSolicitante">E-mail</label>
												<input type="text" id="inputEmailSolicitante" name="inputEmailSolicitante" class="form-control" value="<?php echo $row['UsuarEmail']; ?>" readOnly>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefoneSolicitante">Telefone</label>
												<input type="text" id="inputTelefoneSolicitante" name="inputTelefoneSolicitante" class="form-control" value="<?php echo $row['UsuarTelefone']; ?>" readOnly>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br>

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Dados da Entrega</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<label for="cmbUnidade">Unidade</label>
											<select id="cmbUnidade" name="cmbUnidade" class="form-control form-control-select2">
												<option value="">Selecione</option>
												<?php
												$sql = "SELECT UnidaId, UnidaNome
														FROM Unidade
														JOIN Situacao on SituaId = UnidaStatus
														WHERE UnidaEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
														ORDER BY UnidaNome ASC";
												$result = $conn->query($sql);
												$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);

												foreach ($rowUnidade as $item) {
													$seleciona = $item['UnidaId'] == $row['OrComUnidade'] ? "selected" : "";
													print('<option value="' . $item['UnidaId'] . '" ' . $seleciona . '>' . $item['UnidaNome'] . '</option>');
												}
												?>
											</select>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbLocalEstoque">Local / Almoxarifado</label>
												<select id="cmbLocalEstoque" name="cmbLocalEstoque" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php
													if (isset($row['OrComUnidade'])) {
														$sql = "SELECT LcEstId, LcEstNome
														        FROM LocalEstoque
														        JOIN Situacao on SituaId = LcEstStatus 												     
														        WHERE LcEstEmpresa = " . $_SESSION['EmpreId'] . " and LcEstUnidade = " . $row['OrComUnidade'] . " and SituaChave = 'ATIVO'
														        ORDER BY LcEstNome ASC";
														$result = $conn->query($sql);
														$rowLocal = $result->fetchAll(PDO::FETCH_ASSOC);

														foreach ($rowLocal as $item) {
															$seleciona = $item['LcEstId'] == $row['OrComLocalEntrega'] ? "selected" : "";
															print('<option value="' . $item['LcEstId'] . '" ' . $seleciona . '>' . $item['LcEstNome'] . '</option>');
														}
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-10">
											<div class="form-group">
												<label for="inputEnderecoEntrega">Endereço da Entrega</label>
												<input type="text" id="inputEnderecoEntrega" name="inputEnderecoEntrega" class="form-control" value="<?php echo $row['OrComEnderecoEntrega']; ?>">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataEntrega">Previsão de Entrega</label>
												<input type="text" id="inputDataEntrega" name="inputDataEntrega" class="form-control" value="<?php echo mostraData($row['OrComDtEntrega']); ?>">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtareaObservacao">Observação</label>
												<textarea rows="3" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao"><?php echo $row['OrComObservacao']; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<div class="btn btn-lg btn-success" id="enviar">Alterar</div>
										<a href="ordemcompra.php" class="btn btn-basic" role="button">Cancelar</a>
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

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		//Ao carregar a página tive que executar o que o onChange() executa para que a combo da SubCategoria já venha filtrada, além de selecionada, é claro.
		window.onload = function() {

			var cmbSubCategoria = $('#cmbSubCategoria').val();

			Filtrando();

			var cmbCategoria = $('#cmbCategoria').val();

			$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function(dados) {

				var option = '<option value="#">Selecione a SubCategoria</option>';

				if (dados.length) {

					$.each(dados, function(i, obj) {

						if (obj.SbCatId == cmbSubCategoria) {
							option += '<option value="' + obj.SbCatId + '" selected>' + obj.SbCatNome + '</option>';
						} else {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						}
					});

					$('#cmbSubCategoria').html(option).show();
				} else {
					ResetSubCategoria();
				}
			});
		}
	</script>

</body>

</html>