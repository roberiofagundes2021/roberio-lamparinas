<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'TR Produto';

include('global_assets/php/conexao.php');

//Se veio do tr.php
if (isset($_POST['inputTRId'])) {
	$iTR = $_POST['inputTRId'];
	$iCategoria = $_POST['inputTRCategoria'];

} else if (isset($_POST['inputIdTR'])) {
	$iTR = $_POST['inputIdTR'];
	$iCategoria = $_POST['inputIdCategoria'];

} else {
	irpara("tr.php");
}

//Se está alterando
if (isset($_POST['inputIdTR'])) {
	try {
		$conn->beginTransaction();

		$sql = "
			DELETE 
				FROM TermoReferenciaXProduto
			 WHERE TRXPrTermoReferencia = :iTR 
				 AND TRXPrUnidade 				= :iUnidade
		";
		
		$result = $conn->prepare($sql);
		$result->execute(array(
			':iTR' 			=> $iTR,
			':iUnidade' => $_SESSION['UnidadeId']
		));

		for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
			$sql = "
				INSERT INTO 
					TermoReferenciaXProduto (
						TRXPrTermoReferencia, 
						TRXPrProduto, 
						TRXPrQuantidade, 
						TRXPrValorUnitario, 
						TRXPrTabela, 
						TRXPrUsuarioAtualizador, 
						TRXPrUnidade
					)
					VALUES (
						:iTR, 
						:iProduto, 
						:iQuantidade, 
						:fValorUnitario, 
						:sTabela, 
						:iUsuarioAtualizador, 
						:iUnidade
					)";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iTR' 									=> $iTR,
				':iProduto' 						=> $_POST['inputIdProduto' . $i],
				':iQuantidade' 					=> $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
				':fValorUnitario' 			=> null,
				':sTabela' 							=> $_POST['inputTabelaProduto' . $i],
				':iUsuarioAtualizador' 	=> $_SESSION['UsuarId'],
				':iUnidade' 						=> $_SESSION['UnidadeId']
			));
		}

		/* Verifica e remove dados da Bandeja */
		$sql = "
			DELETE 
				FROM Bandeja
			 WHERE BandeTabelaId = :iTR
				 AND BandeUnidade  = :iUnidade
		";
		$result = $conn->prepare($sql);
		$result->execute(array(
			':iTR' 			=> $iTR,
			':iUnidade' => $_SESSION['UnidadeId']
		));

		/* Verifica e remove dados da tabela Bandeja X Perfil */
		$sql = "
			DELETE 
				FROM BandejaXPerfil
			 WHERE BnXPeBandeja = :iTR
				 AND BnXPeUnidade = :iUnidade
		";
		$result = $conn->prepare($sql);
		$result->execute(array(
			':iTR' 			=> $iTR,
			':iUnidade' => $_SESSION['UnidadeId']
		));

		/* Atualiza o Status do Termo de Referência */
		$sql = "
			UPDATE TermoReferencia
			   SET TrRefStatus = 
				  		(SELECT SituaId 
								 FROM Situacao
								WHERE SituaChave = 'PENDENTE')
			 WHERE TrRefId 				= :iTR
			    AND TrRefUnidade 	= :iUnidade 
		";
		$result = $conn->prepare($sql);
		$result->execute(array(
			':iTR' 			=> $iTR,
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$conn->commit();
						
		$_SESSION['msg']['titulo'] 		= "Sucesso";
		$_SESSION['msg']['mensagem'] 	= "TR alterada!!!";
		$_SESSION['msg']['tipo'] 			= "success";

	} catch(PDOException $e){

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] 		= "Erro";
		$_SESSION['msg']['mensagem'] 	= "Erro ao alterar a TR!!!";
		$_SESSION['msg']['tipo'] 			= "error";	

		alerta('Error1: ' . $e->getMessage());
	}
}

try {
	$sql = "
		SELECT TrXOrId
			FROM TRXOrcamento
		 WHERE TrXOrUnidade = " . $_SESSION['UnidadeId'] . " 
		   AND TrXOrTermoReferencia = ".$iTR."
	";
	$result = $conn->query($sql);
	$rowOrcamentosTR = $result->fetchAll(PDO::FETCH_ASSOC);

	// Select para verificar o parametro ParamProdutoOrcamento.
	$sql = "
		SELECT ParamProdutoOrcamento
			FROM Parametro
		 WHERE ParamEmpresa = " . $_SESSION['EmpreId'] . " 
	";
	$result = $conn->query($sql);
	$rowParametro = $result->fetch(PDO::FETCH_ASSOC);

	// Select para o TR.
	$sql = "
		SELECT *
			FROM TermoReferencia
			JOIN Categoria on CategId = TrRefCategoria
			JOIN Situacao on SituaId = TrRefStatus
		 WHERE TrRefUnidade = " . $_SESSION['UnidadeId'] . " 
		   AND TrRefId = " . $iTR;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = " 
		SELECT TRXSCSubcategoria
			FROM TRXSubcategoria
		 WHERE TRXSCTermoReferencia = " . $iTR . " 
		   AND TRXSCUnidade = " . $_SESSION['UnidadeId'] . "
	";
	$result = $conn->query($sql);
	$rowSubCat = $result->fetchAll(PDO::FETCH_ASSOC);
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	$sql = "SELECT SbCatId, SbCatNome
				 FROM SubCategoria
				 JOIN TRXSubcategoria on TRXSCSubcategoria = SbCatId
				 WHERE SBCatUnidade = " . $_SESSION['UnidadeId'] . " AND TRXSCTermoReferencia = " . $iTR;
	$result = $conn->query($sql);
	$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$aSubCategorias = '';

	foreach ($rowSubCategoria as $item) {
		
		if ($aSubCategorias == '') {
			$aSubCategorias .= $item['SbCatId'];
		} else {
			$aSubCategorias .= ", ".$item['SbCatId'];
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	//Select que verifica a tabela de origem dos produtos dessa TR.
	$sql = "
		SELECT TRXPrProduto
			FROM TermoReferenciaXProduto
			JOIN ProdutoOrcamento on PrOrcId = TRXPrProduto
		 WHERE TRXPrUnidade = " . $_SESSION['UnidadeId'] . " 
		   AND TRXPrTermoReferencia = " . $iTR . " 
			 AND TRXPrTabela = 'ProdutoOrcamento'
	";
	$result = $conn->query($sql);
	$rowProdutoUtilizado1 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countProdutoUtilizado1 = count($rowProdutoUtilizado1);

	if (count($rowProdutoUtilizado1) >= 1) {
		foreach ($rowProdutoUtilizado1 as $itemProdutoUtilizado) {
			$aProdutos1[] = $itemProdutoUtilizado['TRXPrProduto'];
		}
	} else {
		$aProdutos1 = [];
	}

	$sql = "
		SELECT TRXPrProduto
			FROM TermoReferenciaXProduto
			JOIN Produto on ProduId = TRXPrProduto
		 WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " 
		   AND TRXPrTermoReferencia = " . $iTR . " 
			 AND TRXPrTabela = 'Produto'
	";
	$result = $conn->query($sql);
	$rowProdutoUtilizado2 = $result->fetchAll(PDO::FETCH_ASSOC);
	$countProdutoUtilizado2 = count($rowProdutoUtilizado2);
	
	if (count($rowProdutoUtilizado2) >= 1) {
		foreach ($rowProdutoUtilizado2 as $itemProdutoUtilizado) {
			$aProdutos2[] = $itemProdutoUtilizado['TRXPrProduto'];
		}
	} else {
		$aProdutos2[] = [];
	}

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
	<title>Lamparinas | Listando produtos do TR</title>

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

			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbProduto').on('change', function(e) {

				let inputCategoria 		= $('#inputIdCategoria').val();
				let inputSubCategoria = $('#inputSubCategoria').val();
				let produtos 					= $(this).val();
				let tr 								= $('#inputIdTR').val();
				let cont 							= 1;
				let produtoId 				= [];
				let produtoQuant 			= [];

				// Aqui é para cada "class" faça
				$.each($(".idProduto"), function() {
					produtoId[cont] = $(this).val();
					cont++;
				});

				cont = 1;
				//aqui fazer um for que vai até o ultimo cont (dando cont++ dentro do for)
				$.each($(".Quantidade"), function() {
					$id 							= produtoId[cont];
					produtoQuant[$id] = $(this).val();
					cont++;
				});

				$.ajax({
					type: "POST",
					url: "trFiltraProduto.php",
					data: {
						idTr: tr,
						idCategoria: inputCategoria,
						idSubCategoria: inputSubCategoria,
						produtos: produtos,
						produtoId: produtoId,
						produtoQuant: produtoQuant
					},
					success: function(resposta) {
						$("#tabelaProdutos").html(resposta).show();
						return false;
					}
				});
			});

			function disabledSelect(){
				let btnSubmit = $('#btnsubmit')
				let selectProdutos = $('#ProdutoRow')

				if(btnSubmit.attr('disabled')){
					selectProdutos.css('display', 'none')
				}
			}
			disabledSelect()

		});

		//Mostra o "Filtrando..." na combo Produto
		function FiltraProduto() {
			$('#cmbProduto').empty().append('<option>Filtrando...</option>');
		}

		function ResetProduto() {
			$('#cmbProduto').empty().append('<option>Sem produto</option>');
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
					<form name="formTRProduto" id="formTRProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - TR Nº "<?php echo $row['TrRefNumero']; ?>"</h5>
						</div>
						<input type="hidden" id="inputIdTR" name="inputIdTR" class="form-control" value="<?php echo $row['TrRefId']; ?>">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['TrRefCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria(s)</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT SbCatId, SbCatNome
																FROM SubCategoria
																JOIN Situacao on SituaId = SbCatStatus	
																WHERE SbCatUnidade = ". $_SESSION['UnidadeId'] ." and SbCatId in (".$aSubCategorias.")
																ORDER BY SbCatNome ASC"; 
														$result = $conn->query($sql);
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														$count = count($rowSubCategoria);														
																
														foreach ( $rowSubCategoria as $item){	
															print('<option value="'.$item['SbCatId,'].'"disabled selected>'.$item['SbCatNome'].'</option>');	
														}                    
													?>
												</select>
											</div>
										</div>
									</div>
									<div id="ProdutoRow" class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php
													if (count($aProdutos1) >= 1) {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $valueSubCat) {
																$sql = "
																	SELECT PrOrcId, PrOrcNome
																		FROM ProdutoOrcamento
																		JOIN Situacao on SituaId = PrOrcSituacao				     
																	 WHERE PrOrcSubCategoria = " . $valueSubCat['TRXSCSubcategoria'] . " 
																	   AND SituaChave = 'ATIVO' and PrOrcUnidade = " . $_SESSION['UnidadeId'] . " 
																		 AND PrOrcCategoria = " . $iCategoria;

																if (isset($row['TrRefSubCategoria']) and $row['TrRefSubCategoria'] != '' and $row['TrRefSubCategoria'] != null) {
																	$sql .= " and PrOrcSubCategoria = " . $row['TrRefSubCategoria'];
																}

																$sql .= " ORDER BY PrOrcNome ASC";
																$result = $conn->query($sql);
																$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

																foreach ($rowProduto as $item) {
																	if (in_array($item['PrOrcId'], $aProdutos1) or $countProdutoUtilizado1 == 0) {
																		$seleciona = "selected";
																		print('<option value="' . $item['PrOrcId'] . '" ' . $seleciona . '>' . $item['PrOrcNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['PrOrcId'] . '" ' . $seleciona . '>' . $item['PrOrcNome'] . '</option>');
																	}
																}
															}
														}
													} else {
														if (count($rowSubCat) >= 1) {
															foreach ($rowSubCat as $subcategoria) {
																$sql = "
																	SELECT ProduId, ProduNome
																		FROM Produto
																		JOIN Situacao on SituaId = ProduStatus		
																	 WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " 
																	   AND SituaChave = 'ATIVO' and ProduCategoria = " . $iCategoria . "";

																if ($subcategoria['TRXSCSubcategoria'] != '' and $subcategoria['TRXSCSubcategoria'] != null) {
																	$sql .= " and ProduSubCategoria = " . $subcategoria['TRXSCSubcategoria'];
																}

																$sql .= " ORDER BY ProduNome ASC";
																$result = $conn->query($sql);
																$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

																foreach ($rowProduto as $item) {
																	if (in_array($item['ProduId'], $aProdutos2)) {
																		$seleciona = "selected";
																		print('<option value="' . $item['ProduId'] . '" ' . $seleciona . '>' . $item['ProduNome'] . '</option>');
																	} else {
																		$seleciona = "";
																		print('<option value="' . $item['ProduId'] . '" ' . $seleciona . '>' . $item['ProduNome'] . '</option>');
																	}
																}
															}
														} else {
															$sql = "
																SELECT ProduId, ProduNome
																	FROM Produto
																	JOIN Situacao on SituaId = ProduStatus		
																 WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " 
																   AND SituaChave = 'ATIVO' 
																	 AND ProduCategoria = " . $iCategoria . "";

															$sql .= " ORDER BY ProduNome ASC";
															$result = $conn->query($sql);
															$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowProduto as $item) {
																if (in_array($item['ProduId'], $aProdutos2)) {
																	$seleciona = "selected";
																	print('
																		<option value="' . $item['ProduId'] . '" ' . $seleciona . '>' . $item['ProduNome'] . '</option>
																	');
																} else {
																	$seleciona = "";
																	print('
																		<option value="' . $item['ProduId'] . '" ' . $seleciona . '>' . $item['ProduNome'] . '</option>
																	');
																}
															}
														}
													}
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Custom header text -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Produtos</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os produtos selecionadas acima. Para atualizar os valores, basta preencher a coluna <code>Quantidade</code> e depois clicar em <b>ALTERAR</b>.</p>

									<?php
									if (count($aProdutos1) >= 1) {

										$sql = "
											SELECT PrOrcId, 
														 PrOrcNome, 
														 PrOrcDetalhamento, 
														 PrOrcUnidadeMedida, 
														 TRXPrQuantidade, 
														 TRXPrTabela, 
														 UnMedNome, 
														 UnMedSigla
												FROM ProdutoOrcamento
												JOIN TermoReferenciaXProduto 
													ON TRXPrProduto = PrOrcId
												JOIN UnidadeMedida 
												  ON UnMedId = PrOrcUnidadeMedida
											 WHERE PrOrcUnidade 				= " . $_SESSION['UnidadeId'] . " 
											   AND TRXPrTermoReferencia = " . $iTR  . " 
												 AND TRXPrTabela 					= 'ProdutoOrcamento'
										";
										$result = $conn->query($sql);
										$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										$cont = 0;

										print('
											<div class="row" style="margin-bottom: -20px;">
												<div class="col-lg-9">
														<div class="row">
															<div class="col-lg-1">
																<label for="inputCodigo"><strong>Item</strong></label>
															</div>
															<div class="col-lg-11">
																<label for="inputProduto"><strong>Produto</strong></label>
															</div>
														</div>
													</div>												
												<div class="col-lg-1">
													<div class="form-group">
														<label for="inputUnidade"><strong>Unidade</strong></label>
													</div>
												</div>
												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputQuantidade"><strong>Quantidade</strong></label>
													</div>
												</div>	
											</div>
										');

										print('<div id="tabelaProdutos">');

										foreach ($rowProdutos as $item) {
											$cont++;

											$iQuantidade = isset($item['TRXPrQuantidade']) ? $item['TRXPrQuantidade'] : '';
										
											print('
												<div class="row" style="margin-top: 8px;">
													<div class="col-lg-9">
														<div class="row">
															<div class="col-lg-1">
																<input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
																<input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['PrOrcId'] . '" class="idProduto">
															</div>
															<div class="col-lg-11">
																<input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['PrOrcDetalhamento'] . '" value="' . $item['PrOrcNome'] . '" readOnly>
															</div>
														</div>
													</div>								
													<div class="col-lg-1">
														<input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedSigla'] . '" readOnly>
													</div>
											');

											if(count($rowOrcamentosTR) >= 1) {
												print('
													<div class="col-lg-2">
														<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '" readOnly>
													</div>	
												');
											} else {
												print('
													<div class="col-lg-2">
														<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
													</div>	
												');
											}
											print('
												</div>
											');

											print('
												<input type="hidden" id="inputTabelaProduto' . $cont . '" name="inputTabelaProduto' . $cont . '" value="' . $item['TRXPrTabela'] . '">
											');
										}

										print('
											<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >
										');

										print('
											</div>
										');

									} else {

										$sql = "
											SELECT TRXPrQuantidade, 
														 TRXPrTabela, 
														 ProduId, 
														 ProduNome, 
														 ProduDetalhamento, 
														 ProduUnidadeMedida, 
														 UnMedNome, 
														 UnMedSigla
												FROM TermoReferenciaXProduto
												JOIN Produto 
													ON ProduId = TRXPrProduto
												LEFT 
													JOIN UnidadeMedida 
														ON UnMedId = ProduUnidadeMedida
											 WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " 
											   AND TRXPrTermoReferencia = " . $iTR . " 
												 AND TRXPrTabela = 'Produto'
										";
										$result 			= $conn->query($sql);
										$rowProdutos 	= $result->fetchAll(PDO::FETCH_ASSOC);
										$count 				= count($rowProdutos);
										$cont = 0;

										print('
											<div class="row" style="margin-bottom: -20px;">
												<div class="col-lg-9">
														<div class="row">
															<div class="col-lg-1">
																<label for="inputCodigo"><strong>Item</strong></label>
															</div>
															<div class="col-lg-11">
																<label for="inputProduto"><strong>Produto</strong></label>
															</div>
														</div>
													</div>												
												<div class="col-lg-1">
													<div class="form-group">
														<label for="inputUnidade"><strong>Unidade</strong></label>
													</div>
												</div>
												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputQuantidade"><strong>Quantidade</strong></label>
													</div>
												</div>	
											</div>
										');

										print('
											<div id="tabelaProdutos">
										');

										foreach ($rowProdutos as $item) {
											$cont++;
											$iQuantidade = isset($item['TRXPrQuantidade']) ? $item['TRXPrQuantidade'] : '';

											print('
												<div class="row" style="margin-top: 8px;">
													<div class="col-lg-9">
														<div class="row">
															<div class="col-lg-1">
																<input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
																
																<input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['ProduId'] . '" class="idProduto">
																
																<input type="hidden" id="inputTabelaProduto' . $cont . '" name="inputTabelaProduto' . $cont . '" value="' . $item['TRXPrTabela'] . '">
															</div>

															<div class="col-lg-11">
																<input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ProduDetalhamento'] . '" value="' . $item['ProduNome'] . '" readOnly>
															</div>

														</div>
													</div>								
													
													<div class="col-lg-1">
														<input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedSigla'] . '" readOnly>
													</div>
											');

											if(count($rowOrcamentosTR) >= 1) {
												print('
													<div class="col-lg-2">
														<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '" readOnly>
													</div>	
												');
											} else {
												print('
													<div class="col-lg-2">
														<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
													</div>	
												');
											}
											print('
												</div>
											');

											print('
												<input type="hidden" id="inputTabelaProduto' . $cont . '" name="inputTabelaProduto' . $cont . '" value="' . $item['TRXPrTabela'] . '">
											');
										}

										print('
											<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >
										');

										print('
											</div>
										');
									}

									?>
								</div>
							</div>
							<!-- /custom header text -->
							<?php 
							
							    if(count($rowOrcamentosTR) >= 1){
									print('
										<div class="row" style="margin-top: 10px;">
											<div class="row justify-content-center col-lg-12">
												<div class="form-group col-12 col-lg-6">
													<button id="btnsubmit" class="btn btn-lg btn-principal" disabled type="submit">Alterar</button>
													<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
												</div>
												
												<div class="row justify-content-end align-content-center col-12 col-lg-6">
													<p style="color: red; margin: 0px"><i class="icon-info3"></i>A lista de produtos não pode ser alterada enquanto houver orçamentos para esse Termo de Referência.</p>
												</div>
											</div>
										</div>
									');

								} else {

									print('
										<div class="row" style="margin-top: 10px;">
											<div class="col-lg-12">
												<div class="form-group">
													<button class="btn btn-lg btn-principal" type="submit">Alterar</button>
													<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
												</div>
											</div>
										</div>
									');
								}
							 
							?>
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