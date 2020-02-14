<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Nova Solicitação';

include('global_assets/php/conexao.php');

$sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduEmpresa, ProduId, NULL) as Estoque
		FROM Produto
		JOIN Categoria on CategId = ProduCategoria
		JOIN Situacao on SituaId = ProduStatus
	    WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
		ORDER BY ProduNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

















/*for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
		
			$sql = "INSERT INTO FluxoOperacionalXProduto (FOXPrFluxoOperacional, FOXPrProduto, FOXPrQuantidade, FOXPrValorUnitario, FOXPrUsuarioAtualizador, FOXPrEmpresa)
					VALUES (:iFluxoOperacional, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
			
			$result->execute(array(
							':iFluxoOperacional' => $iFluxoOperacional,
							':iProduto' => $_POST['inputIdProduto'.$i],
							':iQuantidade' => $_POST['inputQuantidade'.$i] == '' ? null : $_POST['inputQuantidade'.$i],
							':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpreId']
							));

			$valorTotal = floatval(str_replace(',', '.', str_replace('.', '', $_POST['inputValor'])));
			$TotalGeral = floatval(str_replace(',', '.', str_replace('.', '', $_POST['inputTotalGeral'])));

			$sql = "SELECT SituaId
					FROM Situacao
					Where SituaChave = 'PENDENTE' ";
			$result = $conn->query("$sql");
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			//Atualiza a Situação do Fluxo se estiver fechado o valor total dos produtos com o valor total do Fluxo e a situação estiver PENDENTE. Daí deve passar para ATIVO.
			if ($valorTotal === $TotalGeral && $_POST['inputStatus'] == $rowSituacao['SituaId']){		
					
				/* Atualiza o Status do Fluxo para "Aguardando Liberação"
				$sql = "SELECT SituaId
						FROM Situacao
						Where SituaChave = 'AGUARDANDOLIBERACAO' ";
				$result = $conn->query("$sql");
				$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

				$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :bStatus
                        WHERE FlOpeId = :iFluxo 
		               ";
	            $result = $conn->prepare($sql);
			
	            $result->execute(array(
					':bStatus' => $rowSituacao['SituaId'],
					':iFluxo' => $iFluxoOperacional					
					));
	            // Fim Atualiza 

				$sql = "SELECT PerfiId
						FROM Perfil
						Where PerfiChave IN ('ADMINISTRADOR','CONTROLADORIA') ";
				$result = $conn->query("$sql");
				$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);

				$sql = "SELECT FlOpeNumContrato, FlOpeNumProcesso
						FROM FluxoOperacional
						Where FlOpeId = ".$iFluxoOperacional;
				$result = $conn->query("$sql");
				$rowFluxo = $result->fetch(PDO::FETCH_ASSOC);

				// Verifica se a Bandeja já tem um registro com BandeTabela: FluxoOperacional e BandeTabelaId: IdFluxoAtual, evitando duplicação
				$sql = "SELECT BandeId, SituaChave
						FROM Bandeja
						JOIN Situacao on SituaId = BandeStatus
						Where BandeTabela = 'FluxoOperacional' and BandeTabelaId =  ".$iFluxoOperacional;
				$result = $conn->query("$sql");
				$rowBandeja = $result->fetch(PDO::FETCH_ASSOC);
				$count = count($rowBandeja);

				if (!$count){
				
					// Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA 
					$sIdentificacao = 'Fluxo Operacional (Nº Contrato: '.$rowFluxo['FlOpeNumContrato'].' | Nº Processo: '.$rowFluxo['FlOpeNumProcesso'].')';
				
					$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandePerfilDestino, BandeSolicitante, BandeTabela, BandeTabelaId,
												 BandeStatus, BandeUsuarioAtualizador, BandeEmpresa)
							VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iPerfilDestino, :iSolicitante, :sTabela, :iTabelaId, :iStatus, :iUsuarioAtualizador, :iEmpresa)";
					$result = $conn->prepare($sql);
							
					$result->execute(array(
									':sIdentificacao' => $sIdentificacao,
									':dData' => date("Y-m-d"),
									':sDescricao' => 'Liberar Fluxo',
									':sURL' => '',
									':iPerfilDestino' => $rowPerfil['PerfiId'],  //Tem que tirar esse campo do banco, já que agora tem uma tabela BandejaXPerfil
									':iSolicitante' => $_SESSION['UsuarId'],
									':sTabela' => 'FluxoOperacional',
									':iTabelaId' => $iFluxoOperacional,
									':iStatus' => $rowSituacao['SituaId'],
									':iUsuarioAtualizador' => $_SESSION['UsuarId'],
									':iEmpresa' => $_SESSION['EmpreId']						
									));

					$insertId = $conn->lastInsertId();

					foreach ($rowPerfil as $item){
					
						$sql = "INSERT INTO BandejaXPerfil (BnXPeBandeja, BnXPePerfil, BnXPeEmpresa)
								VALUES (:iBandeja, :iPerfil, :iEmpresa)";
						$result = $conn->prepare($sql);
								
						$result->execute(array(
										':iBandeja' => $insertId,
										':iPerfil' => $item['PerfiId'],
										':iEmpresa' => $_SESSION['EmpreId']						
										));					
					}
					/* Fim Insere Bandeja 
				
				} else{
					if ($rowBandeja['SituaChave'] == 'ATIVO'){
						$sql = "UPDATE Bandeja SET BandeData = :dData, BandeSolicitante = :iSolicitante, BandeStatus = :iStatus, BandeUsuarioAtualizador = :iUsuarioAtualizador
								WHERE BandeEmpresa = :iEmpresa and BandeId = :iIdBandeja";
						$result = $conn->prepare($sql);
								
						$result->execute(array(
										':dData' => date("Y-m-d"),
										':iSolicitante' => $_SESSION['UsuarId'],
										':iStatus' => $rowSituacao['SituaId'],
										':iUsuarioAtualizador' => $_SESSION['UsuarId'],
										':iEmpresa' => $_SESSION['EmpreId'],
										':iIdBandeja' => $rowBandeja['BandeId']														
										));
					}
				}
			}*/















?>

<!DOCTYPE html>
<html lang="pt-BR">

<head id="hea">
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

	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/plugins/ui/fab.min.js"></script>
	<script src="global_assets/js/plugins/ui/sticky.min.js"></script>
	<script src="global_assets/js/plugins/ui/prism.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_fab.js"></script>

	<!-- btn group do modal-->
	<!--<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>-->
	<script src="global_assets/js/demo_pages/form_input_groups.js"></script>

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

				function contaClicks(direc) {
					return
				}

				/*function editaQuantidade() {
					$('.quant-edit').each((i, elem) => {
						$(elem).on('click', function() {
							$('[idProdu]').each((i, elemInp) => {
								//console.log(contClck)
								if ($(elemInp).attr('idProdu') == $(elem).attr('id')) {
									let contClck = $(elemInp).val();
									if ($(elem).hasClass('bootstrap-touchspin-up')) {
										contClck++
										//$(elemInp).val(contClck);
										console.log(contClck)
									}
									if ($(elem).hasClass('bootstrap-touchspin-down')) {
										if (contClck > 0) {
											contClck--
											//$(elemInp).val(contClck);
											console.log(contClck)
										}
									}
								}
							})
						})
					})
				}
				editaQuantidade()*/

				function editaCarrinho() {
					$('.quant-edit').each((i, elem) => {
						$(elem).on('click', function() {
							//if ($(elem).hasClass('bootstrap-touchspin-down')) {
							$('[idProdu]').each((i, elemInp) => {

								if ($(elemInp).attr('idProdu') == $(elem).attr('id')) {
									let quantidade = $(elemInp).val()
									let quantidadeEstoque = $(elemInp).attr('quantiestoque')
									console.log(quantidadeEstoque)
									let id = $(elemInp).attr('idProdu')
									const url = 'solicitacaoAlteraCarrinho.php'

									if ($(elem).hasClass('bootstrap-touchspin-up')) {
										if (quantidade <= (quantidadeEstoque - 1)) {
											quantidade++
										}
										//$(elemInp).val(contClck);
										console.log(quantidade)
									}
									if ($(elem).hasClass('bootstrap-touchspin-down')) {
										if (quantidade > 0) {
											quantidade--
											//$(elemInp).val(contClck);
											console.log(quantidade)
										}
									}

									let dataPost = {
										inputQuantidadeProduto: quantidade,
										inputIdProduto: id
									}
									console.log(dataPost)

									$.post(
										url,
										dataPost,
										function(data) {
											//console.log(data)
											if (!data) {
												$(elemInp).val(0)
											} else {
												$(elemInp).val(data)
											}
										}
									)
								}
							})
							//} else if ($(elem).hasClass('bootstrap-touchspin-up')) {
							//	console.log('+')
							//}
						})
					})
				}
				editaCarrinho()

				function verificarCarrinhoButtonConcluir() {
					let url = 'solicitacaoVerificarCarrinho.php'
					$.post(
						url,
						function(data) {
							let verifExistProduQuantMaiorZero = 0
							if (data) {
								let carrinho = JSON.parse(data)

								carrinho.forEach(item => {
									if (item.quantidade > 0) {
										verifExistProduQuantMaiorZero += 1
									}
								})
							}
							if (verifExistProduQuantMaiorZero >= 1) {
								$('#confirmar-solicitacao').removeAttr('disabled')
							}
						}
					)
				}

				function verificarCarrinho() {

					// Esta função verifica quais produtos já estão no array em $_SESSION['Carrinho'],
					// para que então eles fiquem com o botão desabilitado no carregamento da página, ou 
					// na chamada ajax no momento da pesquisa. Para isso, faz uma chamada para 
					// 'solicitacaoVerificarCarrinho.php', recebendo um JSON criado a partir de 
					// $_SESSION['Carrinho'], que é utilizado para saber quais produtos carregados
					// na pagina já estão no carrinho.

					let url = 'solicitacaoVerificarCarrinho.php'
					$.post(
						url,
						function(data) {
							// Convertendo a string JSON em um array de Objetos
							let verifExistProduQuantMaiorZero = 0
							if (data) {
								console.log(data)
								let carrinho = JSON.parse(data)

								// Iterando sobre o array para ter acesso aos valores id de cada Objeto 
								carrinho.forEach(item => {
									if (item.quantidade > 0) {
										verifExistProduQuantMaiorZero += 1
									}
									$('.add-cart').each((i, elem) => {
										if ($(elem).attr('produId') == item.id && item.quantidade != 0) {
											// Desabilitando o botão e trocando o conteúdo.
											elem.setAttribute('disabled', '')
											$(elem).html('PRODUTO ADICIONADO')
										}
									})
								})
							}
							if (verifExistProduQuantMaiorZero >= 1) {
								$('#confirmar-solicitacao').removeAttr('disabled')
							} else {
								$('#confirmar-solicitacao').attr('disabled', '')
							}
						}
					)
				}
				verificarCarrinho()

				function excluirItemCarrinho() {
					$('[indexExcluir]').each((i, elem) => {
						$(elem).on('click', () => {
							$('[idProdu]').each((i, elemProdu) => {
								if ($(elem).attr('indexExcluir') == $(elemProdu).attr('idprodu')) {
									let elemParent = $(elemProdu).parent().parent().parent()

									let id = $(elemProdu).attr('idprodu')
									const url = 'solicitacaoAlteraCarrinho.php'
									elemParent.fadeOut(400)

									let dataPost = {
										inputQuantidadeProduto: 0,
										inputIdProduto: id
									}

									$.post(
										url,
										dataPost,
										function(data) {
											console.log(data)
											$('.add-cart').each((i, elemButton) => {
												if ($(elemButton).attr('produid') == $(elem).attr('indexExcluir')) {
													let icon = $('<i class="icon-cart-add mr-2"></i>')
													let text = ' Adicionar ao carrinho'
													$(elemButton).html(icon).append(text)
													$(elemButton).removeAttr('disabled')
												}
											})
											verificarCarrinho()
										}
									)
								}
							})
						})
					})
				}
				excluirItemCarrinho()


				function carrinho() {
					$('.add-cart').each((i, elem) => {
						$(elem).on('click', () => {
							let id = $(elem).attr('produId')

							$.post(
								'solicitacaoNovoCarrinho.php', {
									inputProdutoId: id
								},
								function(data) {
									if (data) {
										elem.setAttribute('disabled', '')
										$(elem).html('PRODUTO ADICIONADO')
										$('.custon-modal-lista').append(data)
										//editaQuantidade()
										editaCarrinho()
										excluirItemCarrinho()
										verificarCarrinho()
									}
								}
							)
						})
					})
				}
				carrinho()

				function finalizarSolicitacao() {
					$('#confirmar-solicitacao').on('click', function() {
						let url = 'solicitacaoVerificarCarrinho.php'
						$.post(
							url,
							function(data) {
								if (data) {
									let carrinho = JSON.parse(data)
									if (carrinho.length > 0) {
										console.log('existem produtos para solicitação')
										console.log(carrinho)
                                       // let observacao = $('#txtObservacao').val()
										//$('#inputObservacao').val(observacao)

										$('#solicitacao').submit()
									} else {
										console.log('não existem produtos para solicitação')
									}
									/*carrinho.forEach(item => {
										$('.add-cart').each((i, elem) => {
											if ($(elem).attr('produId') == item.id && item.quantidade != 0) {
												// Desabilitando o botão e trocando o conteúdo.
												elem.setAttribute('disabled', '')
												$(elem).html('PRODUTO ADICIONADO')
											}
										})
									})*/
								}
							}
						)
					})
				}
				finalizarSolicitacao()

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

								if (data) {
									$('#cards-produto').removeClass('justify-content-center px-2')
									$('#cards-produto').html(data)
									resultadosConsulta = data

									// Estas duas funções são chamadas a cada requisição Ajax realizada, onde 
									// novos elementos são carregados na tela, para que possam agir sobre eles,
									// como no carregamento da pagina.
									carrinho()
									verificarCarrinho()
								} else {
									semResultados()
								}
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

			(function modal() {
				$('#btn-modal').on('click', function() {
					$('#page-modal').addClass('custon-modal-show')
					$('body').css('overflow', 'hidden')

					$('#modal-close').on('click', function() {
						$('#page-modal').removeClass('custon-modal-show')
						$('body').css('overflow', 'scroll')
					})
				})
			})()
			/**/
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

							<!--Buttom Modal-->
							<ul id="btn-modal" class="fab-menu fab-menu-fixed fab-menu-bottom-right" data-fab-toggle="click">
								<li>
									<a class="fab-menu-btn btn bg-blue btn-float rounded-round btn-icon">
										<i class="fab-icon-open icon-cart"></i>
										<i class="fab-icon-close icon-cart"></i>
									</a>
								</li>
							</ul>

							<div id="cards-produto" class="col-12 row m-0 px-0">
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


									if ($item['Estoque'] > 0) {
										print('
		                                    <div class="col-xl-2 col-sm-3">
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
					                                    <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

					                                    <button produId=' . $item['ProduId'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart"><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
				                                    </div>
			                                    </div>
	                                	    </div>							
                                    	');
									} else {
										print('
		                                    <div class="col-xl-2 col-sm-3">
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
					                                    <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

					                                    <button produId=' . $item['ProduId'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart" disabled><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
				                                    </div>
			                                    </div>
	                                	    </div>							
                                    	');
									}
								}
								?>
							</div>
							<form id="addItemCart" name="addItemCart" action="" method="POST">
								<input id="inputProdutoId" type="hidden" name="inputProdutoId">
							</form>
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

	<!-- /modal -->
	<div id="page-modal" class="custon-modal">
		<div class="custon-modal-container">
			<div class="card custon-modal-content">
				<div class="custon-modal-title">
					<i class="fab-icon-open icon-cart p-3"></i>
					<p class="h3">Produtos Selecionados</p>
					<i id="modal-close" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
				</div>
				<div class="custon-modal-lista d-flex flex-column">
					<?php
					if (isset($_SESSION['Carrinho'])) {

						foreach ($_SESSION['Carrinho'] as $item) {
							if ($item['quantidade'] > 0) {
								$sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduEmpresa, ProduId, NULL) as Estoque
		                            FROM Produto
		                            JOIN Categoria on CategId = ProduCategoria
									JOIN Situacao on SituaId = ProduStatus
	                                WHERE ProduId = " . $item['id'] . " and ProduEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
		                            ";
								$result = $conn->query($sql);
								$row = $result->fetch(PDO::FETCH_ASSOC);

								print('
							
							        <div class="custon-modal-produto">
							            <div class="custon-modal-produTitle d-flex flex-column col-12 col-sm-5 col-lg-8">
							            	<p>' . $row['ProduNome'] . '</p>
							            	<p>' . $row['CategNome'] . '</p>
							            </div>
							            <div class="modal-controles col-12 col-sm-7 col-lg-4 row justify-content-md-center align-items-center mx-0">
							            	<div class="input-group bootstrap-touchspin col-9 col-sm-9">
							            		<span class="input-group-prepend">
							            			<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-down quant-edit" type="button">–</button>
							            		</span>
							            		<span class="input-group-prepend bootstrap-touchspin-prefix d-none">
							            			<span class="input-group-text"></span>
							            		</span>
							            		<input quantiEstoque="' . $row['Estoque'] . '" idProdu="' . $row['ProduId'] . '" style="text-align: center" type="text" value="' . $item['quantidade'] . '" class="form-control touchspin-set-value" style="display: block;">
							            		<span class="input-group-append bootstrap-touchspin-postfix d-none">
							            			<span class="input-group-text"></span>
							            		</span>
							            		<span class="input-group-append">
							            			<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-up quant-edit" type="button">+</button>
							            		</span>
							            	</div>
							            	<div class="col-3 col-sm-3 row m-0">
							            	    <button class="btn" indexExcluir=' . $row['ProduId'] . '><i class="fab-icon-open icon-bin2 excluir-item"></i></button>
							            	</div>
							            </div>
						            </div>
							
							     ');
							}
						}
					}
					?>
				</div>
				<div class="card-footer mt-2 d-flex flex-column">
					<form id="solicitacao" method="POST" action="solicitacaoNovoComcluir.php">
					    <!--<input id="inputObservacao" type="hidden" name="inputObservacao">-->
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label for="txtObservacao">Observação</label>
									<textarea rows="3" cols="5" class="form-control textarea-modal" id="txtObservacao" name="txtObservacao" placeholder="Observações sobre a solicitação..."></textarea>
								</div>
							</div>
						</div>
					</form>
					<button id="confirmar-solicitacao" class="btn btn-success" disabled>Confirmar</button>
				</div>
			</div>
		</div>
	</div>

</body>

</html>