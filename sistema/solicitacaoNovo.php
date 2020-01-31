<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Nova Solicitação';

include('global_assets/php/conexao.php');

$sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome
		FROM Produto
		JOIN Categoria on CategId = ProduCategoria
	    WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and ProduStatus = 1
		ORDER BY ProduNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Nova Solicitação</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>

	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			//Aqui sou obrigado a instanciar novamente a utilização do fancybox
			$(".fancybox").fancybox({
				// options
			});


			(function selectSubcateg() {
				const cmbCategoria = $('#cmbCategoria')

				cmbCategoria.on('change', () => {
					Filtrando()
					const valCategoria = $('#cmbCategoria').val()

					$.getJSON('filtraSubCategoria.php?idCategoria=' + valCategoria, function(dados) {

						var option = '<option value="">Selecione a SubCategoria</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
							});

							$('#cmbSubCategoria').html(option).show();
						} else {
							Reset();
						}
					});
				})


				let resultadosConsulta = '';
				let inputsValues = {};

				(function Filtrar() {

					$('#submitFiltro').on('click', (e) => {
						e.preventDefault()

						let pesquisaProduto = $('#inputPesquisaProduto').val()
						let categoria = $('#cmbCategoria').val()
						let subCategoria = $('#cmbSubCategoria').val()
						let marca = $('#cmbMarca').val()
						let fabricante = $('#cmbFabricante').val()
						let modelo = $('#cmbModelo').val()

						let url = "solicitacaoFiltraProdutos.php";

						inputsValues = {
							inputPesquisaProduto: pesquisaProduto,
							inputCategoria: categoria,
							inputSubCategoria: subCategoria,
							inputMarca: marca,
							inputFabricante: fabricante,
							inputModelo: modelo,
						};
						console.log(inputsValues)

						$.post(
							url,
							inputsValues,
							(data) => {

								//if (data) {
									$('#cards-produto').removeClass('justify-content-center px-2')
									$('#cards-produto').html(data)
									//resultadosConsulta = data
								//} else {
                                  // semResultados()
								//}
							}
						);
					})
				})()

				function semResultados() {
					const msg = $('<div class="card" style="width: 100%"><p class="text-center m-2">Sem resultados...</p></div>')
					const pagina0 = $('<li class="page-item active"><a href="#" class="page-link page-link-white">0</a></li>')
					const btnDireita = $('<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-right"></i></a></li>')
                    const btnEsquerda = $('<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-left"></i></a></li>')
                    
					$('#cards-produto').html(msg)
					$('#cards-produto').addClass('justify-content-center px-2').css('width', '100%')
                    
					$('.pagination').html('')
					$('.pagination').append(btnDireita).append(pagina0).append(btnEsquerda)
				}
			})()

			function Filtrando() {
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			}

			function Reset() {
				$('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
			}
		});
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>

	<!-- Page content -->
	<div class="page-content">

		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>

			<!-- Content area -->
			<div class="content">

				<!-- Inner container -->
				<div class="d-flex align-items-start flex-column flex-md-row">

					<!-- Left content -->
					<div class="w-100 order-2 order-md-1">

						<div id="produtos" class="row">
							<!--Search Filter-->
							<div id="filter" class="col-12">
								<div class="card py-3">
									<div class="card-bod">
										<form class="col-12" id="pesquisa" action="">
											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label for="inputPesquisaProduto">Pesquisa</label>
														<div class="form-group form-group-feedback form-group-feedback-right">
															<input id="inputPesquisaProduto" type="search" class="form-control" placeholder="Buscar">
															<div class="form-control-feedback">
																<i class="icon-search4 font-size-base text-muted"></i>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbCategoria">Categoria</label>
														<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
															<option value="">Selecionar</option>
															<?php
															$sql = ("SELECT CategId, CategNome
																             FROM Categoria														     
																             WHERE CategStatus = 1 and CategEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY CategNome ASC");
															$result = $conn->query("$sql");
															$rowCateg = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowCateg as $item) {
																print('<option value="' . $item['CategId'] . '">' . $item['CategNome'] . '</option>');
															}
															?>
														</select>
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbSubCategoria">SubCategoria</label>
														<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
															<option value="">Selecionar</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbMarca">Marca</label>
														<select id="cmbMarca" name="cmbMarca" class="form-control form-control-select2">
															<option value="">Selecionar</option>
															<?php
															$sql = ("SELECT MarcaId, MarcaNome
																             FROM Marca														     
																             WHERE MarcaStatus = 1 and MarcaEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY MarcaNome ASC");
															$result = $conn->query("$sql");
															$rowMarca = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowMarca as $item) {
																print('<option value="' . $item['MarcaId'] . '">' . $item['MarcaNome'] . '</option>');
															}
															?>
														</select>
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbFabricante">Fabricante</label>
														<select id="cmbFabricante" name="cmbFabricante" class="form-control form-control-select2">
															<option value="">Selecionar</option>
															<?php
															$sql = ("SELECT FabriId, FabriNome
																             FROM Fabricante														     
																             WHERE FabriStatus = 1 and FabriEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY FabriNome ASC");
															$result = $conn->query("$sql");
															$rowFabri = $result->fetchAll(PDO::FETCH_ASSOC);
															var_dump($rowFabri);

															foreach ($rowFabri as $item) {
																print('<option value="' . $item['FabriId'] . '">' . $item['FabriNome'] . '</option>');
															}
															?>
														</select>
													</div>
												</div>
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbModelo">Modelo</label>
														<select id="cmbModelo" name="cmbModelo" class="form-control form-control-select2">
															<option value="">Selecionar</option>
															<?php
															$sql = ("SELECT ModelId, ModelNome
																             FROM Modelo														     
																             WHERE ModelStatus = 1 and ModelEmpresa = " . $_SESSION['EmpreId'] . "
																             ORDER BY ModelNome ASC");
															$result = $conn->query("$sql");
															$rowModel = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowModel as $item) {
																print('<option value="' . $item['ModelId'] . '">' . $item['ModelNome'] . '</option>');
															}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="text-right">
												<div>
													<button id="submitFiltro" class="btn btn-success">Consultar</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div id="cards-produto" class="row m-0">
								<?php

								$sFoto = '';

								foreach ($row as $item) {

									if ($item['ProduFoto'] != null) {

										//Depois verifica se o arquivo físico ainda existe no servidor
										if (file_exists("global_assets/images/produtos/" . $item['ProduFoto'])) {
											$sFoto = "global_assets/images/produtos/" . $item['ProduFoto'];
										} else {
											$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
										}
									} else {
										$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
									}


									print('
	
		<div class="col-xl-3 col-sm-6">
			<div class="card">
				<div class="card-body">
					<div class="card-img-actions">
						<a href="' . $sFoto . '" class="fancybox">
							<img src="' . $sFoto . '" class="card-img"  alt="" style="max-height:290px;">
							<span class="card-img-actions-overlay card-img">
								<i class="icon-plus3 icon-2x"></i>
							</span>
						</a>
					</div>
				</div>

				<div class="card-body bg-light text-center">
					<div class="mb-2">
						<h6 class="font-weight-semibold mb-0">
							<a href="#" class="text-default">' . $item['ProduNome'] . '</a>
						</h6>

						<a href="#" class="text-muted">' . $item['CategNome'] . '</a>
					</div>

					<div>
						<i class="icon-star-full2 font-size-base text-warning-300"></i>
						<i class="icon-star-full2 font-size-base text-warning-300"></i>
						<i class="icon-star-full2 font-size-base text-warning-300"></i>
						<i class="icon-star-full2 font-size-base text-warning-300"></i>
						<i class="icon-star-full2 font-size-base text-warning-300"></i>
					</div>

					<div class="text-muted mb-3">85 em estoque</div>

					<button type="button" class="btn bg-teal-400"><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
				</div>
			</div>
		</div>							
	
	
	');
								}
								?>
							</div>

						</div>
						<!-- /grid -->


						<!-- Pagination -->
						<div class="d-flex justify-content-center pt-3 mb-3">
							<ul class="pagination shadow-1">
								<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-right"></i></a></li>
								<li class="page-item active"><a href="#" class="page-link page-link-white">1</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">2</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">3</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">4</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">5</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-left"></i></a></li>
							</ul>
						</div>
						<!-- /pagination -->

					</div>
					<!-- /left content -->

				</div>
				<!-- /inner container -->

			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>

</html>