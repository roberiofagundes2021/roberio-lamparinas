<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Editar TR';

include('global_assets/php/conexao.php');

//Se veio do tr.php
if (isset($_POST['inputTRId'])) {

	$iTR = $_POST['inputTRId'];
	///////////////////////////////////////////////////////
	$sql = "SELECT *
	        FROM TermoReferencia 
			WHERE TrRefEmpresa = " . $_SESSION['EmpreId'] . " and TrRefId = " . $iTR . "
		   ";
	$result = $conn->query($sql);
	$rowTr = $result->fetch(PDO::FETCH_ASSOC);

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

		$sql = "SELECT TrRefId, TrRefNumero, TrRefData, TrRefCategoria, TrRefSubCategoria, TrRefConteudo
				FROM TermoReferencia
				WHERE TrRefId = $iTR ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);


		$sql = "SELECT SbCatId, SbCatNome
				 FROM SubCategoria
				 JOIN TRXSubcategoria on TRXSCSubcategoria = SbCatId
				 WHERE SbCatEmpresa = " . $_SESSION['EmpreId'] . " and TRXSCTermoReferencia = $iTR
				 ORDER BY SbCatNome ASC";
		$result = $conn->query($sql);
		$rowBD = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rowBD as $item) {
			$aSubCategorias[] = $item['SbCatId'];
		}


		$sql = "SELECT *
		        FROM TRXOrcamento
		        WHERE TrXOrTermoReferencia = " . $row['TrRefId'] . "";
		$result = $conn->query($sql);
		$rowTrOr = $result->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}

	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("tr.php");
}

if (isset($_POST['inputData'])) {

	try {

		$sql = "UPDATE TermoReferencia SET TrRefCategoria = :iCategoria, TrRefConteudo = :sConteudo, TrRefTipo = :sTipo,
										   TrRefUsuarioAtualizador = :iUsuarioAtualizador
				WHERE TrRefId = :iTR";
		$result = $conn->prepare($sql);

		$conn->beginTransaction();

		$result->execute(array(
			':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
			//':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
			':sConteudo' => $_POST['txtareaConteudo'],
			':sTipo' => $tipoTr,
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iTR' => $iTR
		));

		$sql = "DELETE FROM TRXSubcategoria
				WHERE TRXSCTermoReferencia = :iTermoReferencia and TRXSCEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iTermoReferencia' => $_POST['inputTRId'],
			':iEmpresa' => $_SESSION['EmpreId']
		));


		if (isset($_POST['cmbSubCategoria'])) {

			try {
				$sql = "INSERT INTO TRXSubcategoria
							(TRXSCTermoReferencia, TRXSCSubcategoria, TRXSCEmpresa)
						VALUES 
							(:iTermoReferencia, :iTrSubCategoria, :iTrEmpresa)";
				$result = $conn->prepare($sql);

				foreach ($_POST['cmbSubCategoria'] as $key => $value) {

					$result->execute(array(
						':iTermoReferencia' => $_POST['inputTRId'],
						':iTrSubCategoria' => $value,
						':iTrEmpresa' => $_SESSION['EmpreId']
					));
				}
			} catch (PDOException $e) {
				//$conn->rollback();
				echo 'Error: ' . $e->getMessage();
				exit;
			}
		}

		// Excluindo os produtos de TermoReferenciaXProduto atrelados a esta TR.
		//if (isset($_POST['inputTRProdutoExclui']) and $_POST['inputTRProdutoExclui']) {

			$sql = "DELETE FROM TermoReferenciaXProduto
					WHERE TRXPrTermoReferencia = :iTr and TRXPrEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iTr' => $iTR,
				':iEmpresa' => $_SESSION['EmpreId']
			));
		//}


		// Cadastrandos novos produtos atrelados a esta TR.
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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
									':iTR' => $iTR,
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

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Termo de Referência alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Termo de Referência!!!";
		$_SESSION['msg']['tipo'] = "error";

		$conn->rollback();

		echo 'Error: ' . $e->getMessage();
		exit;
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
	<title>Lamparinas | Orçamento</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<!-- /theme JS files -->

	<!-- JS file path -->
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<!-- Uniform plugin file path -->
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {
			function validarSubcategoria(inputValida) {
				confirmaExclusao(document.formTR, "Existem produtos com quantidades ou valores lançados em orçamentos dessa TR, portanto, a Categoria e Subcategoria não podem ser alteradas. Apenas alterações no Conteúdo Personalizado são permitidas. Confirmar alteração?", "trEdita.php");
			}


			$('#summernote').summernote();

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

			if ($('#inputValidar').val() >= 1) {
				$('.select2-selection__choice__remove').each((i, elem) => {
					console.log($(elem).remove());
				})

				setInterval(() => {
					if ($('.select2-container--default').hasClass('select2-container--open')) {
						$('.select2-container--default').removeClass('select2-container--open');
					}
				}, 1)
			}

			$("#enviar").on('click', function(e) {

				e.preventDefault();

				//Antes
				var inputCategoria = $('#inputTRCategoria').val();
				var inputSubCategoria = $('#inputTRSubCategoria').val();
				if (inputSubCategoria == '' || inputSubCategoria == null) {
					inputSubCategoria = '#';
				}

				//Depois
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();

				if (cmbCategoria == '' || cmbCategoria == '#') {
					alerta('Atenção', 'Informe a categoria!', 'error');
					$('#cmbCategoria').focus();
					return false;
				}

				//Tem produto cadastrado para esse TR na tabela TermoReferenciaXProduto?
				var inputProduto = $('#inputTRProduto').val();

				//Exclui os produtos desse TR?
				var inputExclui = $('#inputTRProdutoExclui').val();

				//Aqui verifica primeiro se tem produtos preenchidos, porque do contrário deixa mudar
				/*if (inputProduto > 0){

					//Verifica se o a categoria ou subcategoria foi alterada
					if (inputSubCategoria != cmbSubCategoria){

						inputExclui = 1;
						$('#inputTRProdutoExclui').val(inputExclui);
						
						confirmaExclusao(document.formTR, "Tem certeza que deseja alterar o TR? Existem produtos com quantidades ou valores lançados!", "trEdita.php");
						
					} else{
						inputExclui = 0;
						$('#inputTRProdutoExclui').val(inputExclui);
					}
				}*/

				if ($('#inputValidar').val() >= 1) {
					validarSubcategoria($('#inputValidar').val())

					$('.select2-selection__choice__remove').each((i, elem) => {
						console.log($(elem).remove());
					})

					setInterval(() => {
						if ($('.select2-container--default').hasClass('select2-container--open')) {
							$('.select2-container--default').removeClass('select2-container--open');
						}
					}, 1)
				}

				$("#formTR").submit();

			}); // enviar			
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

					<form name="formTR" id="formTR" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar TR Nº "<?php echo $_POST['inputTRNumero']; ?>"</h5>
						</div>
						<?php
						$countExt = 0;
						foreach ($rowTrOr as $Orcamento) {
							if ($Orcamento) {
								$sql = "SELECT TXOXPValorUnitario
                                       FROM TRXOrcamentoXProduto
                                       WHERE TXOXPOrcamento = " . $Orcamento['TrXOrId'] . "";
								$result = $conn->query($sql);
								$rowOrPr = $result->fetchAll(PDO::FETCH_ASSOC);
								$countInt = 0;
								foreach ($rowOrPr as $produto) {
									if ($produto['TXOXPValorUnitario']) {
										$countInt++;
									}
								}

								if ($countInt > 0) {
									$countExt++;
								}
							}
						}
						if ($countExt >= 1) {
							print('<input type="hidden" id="inputValidar" name="inputValidar" value="' . $countExt . ' "  >');
							if ($rowBD) {
								foreach ($rowBD as $subcategoria) {
									print('<input type="hidden" class="inputSubCategoriaValidacao" name="inputSubCategoriaValidacao" value="' . $subcategoria['SbCatId'] . ' "  >');
								}
							}
						}
						?>
						<input type="hidden" id="inputTRId" name="inputTRId" value="<?php echo $row['TrRefId']; ?>">
						<input type="hidden" id="inputTRNumero" name="inputTRNumero" value="<?php echo $row['TrRefNumero']; ?>">
						<input type="hidden" id="inputTRCategoria" name="inputTRCategoria" value="<?php echo $row['TrRefCategoria']; ?>">
						<input type="hidden" id="inputTRSubCategoria" name="inputTRSubCategoria" value="<?php echo $row['TrRefSubCategoria']; ?>">
						<input type="hidden" id="inputTRProdutoExclui" name="inputTRProdutoExclui" value="0">

						<?php

						?>

						<?php

						$sql = "SELECT TRXPrTermoReferencia
									FROM TermoReferenciaXProduto
									WHERE TRXPrTermoReferencia = " . $iTR . " and TRXPrEmpresa = " . $_SESSION['EmpreId'];
						$result = $conn->query($sql);
						$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);
						$countProduto = count($rowProduto);

						print('<input type="hidden" id="inputTRProduto" name="inputTRProduto" value="' . $countProduto . '" >');
						?>

						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo mostraData($row['TrRefData']); ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTipo">O TR terá:</label>
												<div class="d-flex flex-row">
													<?php
													if (count($rowTrOr) >= 1) {
														if ($rowTr['TrRefTipo'] == 'PS') {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox" checked disabled>
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox" checked disabled>
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															');
														} else if ($rowTr['TrRefTipo'] == 'P') {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox" checked disabled> 
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox" disabled>
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															 ');
														} else {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox" disabled>
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox" checked disabled>
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															 ');
														}
													} else {
														if ($rowTr['TrRefTipo'] == 'PS') {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox" checked>
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox" checked>
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															');
														} else if ($rowTr['TrRefTipo'] == 'P') {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox" checked> 
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox">
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															 ');
														} else {
															print('
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrProduto" value="P" name="TrProduto" class="form-check-input-styled" type="checkbox">
																	 <label for="TrProduto" class="ml-1" style="margin-bottom: 2px">Produto</label>
																 </div>
																 <div class="p-1 m-0 d-flex flex-row">
																	 <input id="TrServico" value="S" name="TrServico" class="form-check-input-styled" type="checkbox" checked>
																	 <label for="TrServico" class="ml-1" style="margin-bottom: 2px">Serviço</label>
																 </div>
															 ');
														}
													}
													?>
												</div>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<?php
												    if (count($rowTrOr) >= 1){
												    	print('<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" value="'.$row['TrRefCategoria'].'" disabled>');
												    } else {
														print('<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">');
													}
												?>
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT CategId, CategNome
																FROM Categoria															     
																WHERE CategEmpresa = " . $_SESSION['EmpreId'] . " and CategStatus = 1
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
												<?php
												    if (count($rowTrOr) >= 1){
												    	print('<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control select form-control-select2" multiple="multiple" data-fouc disabled>');
												    } else {
														print('<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control select form-control-select2" multiple="multiple" data-fouc>');
													}
												?>
													<?php
													if (isset($row['TrRefCategoria'])) {
														$sql = ("SELECT SbCatId, SbCatNome
															         FROM SubCategoria														 
															         WHERE SbCatEmpresa = " . $_SESSION['EmpreId'] . " and SbCatCategoria = " . $row['TrRefCategoria'] . " and SbCatStatus = 1
															         ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														$count = count($rowSubCategoria);

														if ($count) {
															foreach ($rowSubCategoria as $item) {
																$seleciona = in_array($item['SbCatId'], $aSubCategorias) ? "selected" : "";
																print('<option value="' . $item['SbCatId'] . '" ' . $seleciona . '>' . $item['SbCatNome'] . '</option>');
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

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo do TR (informe aqui o texto que você queira que apareça no TR)"><?php echo $row['TrRefConteudo']; ?></textarea>
									</div>
								</div>
							</div>
							<br>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button type="submit" class="btn btn-lg btn-success" id="enviar">Alterar</button>
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