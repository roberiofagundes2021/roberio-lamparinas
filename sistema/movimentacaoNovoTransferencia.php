<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Nova Movimentação';

include('global_assets/php/conexao.php');

//Caso a chamada à página venha da liberação de uma solicitação na bandeja.

if (isset($_POST['inputSolicitacaoId'])) {

	$sql = "SELECT SlXPrQuantidade, ProduId, ProduNome, ProduValorVenda, UnMedNome
			FROM SolicitacaoXProduto
			JOIN Solicitacao on SolicId = SlXPrSolicitacao
			JOIN Produto on ProduId = SlXPrProduto
			JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			WHERE SlXPrUnidade = " . $_SESSION['UnidadeId'] . " and SolicId = " . $_POST['inputSolicitacaoId'] . "
    ";
	$result = $conn->query($sql);
	$produtosSolicitacao = $result->fetchAll(PDO::FETCH_ASSOC);
	$numProdutos = count($produtosSolicitacao);
	//var_dump($produtosSolicitacao);

	$idsProdutos = '';

	if ($numProdutos) {
		foreach ($produtosSolicitacao as $chave => $produto) {

			if ($chave == 0) {
				$idsProdutos .= '0, ' . $produto['ProduId'] . '';
			} else {
				$idsProdutos .= ', ' . $produto['ProduId'] . '';
			}
		}
	}

	$sql = "SELECT SlXPrQuantidade, ProduId, ProduNome, ProduValorVenda, UnMedNome
			FROM SolicitacaoXProduto
			JOIN Solicitacao on SolicId = SlXPrSolicitacao
			JOIN Produto on ProduId = SlXPrProduto
			JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
			WHERE SlXPrUnidade = " . $_SESSION['UnidadeId'] . " and SolicId = " . $_POST['inputSolicitacaoId'] . "
    ";
	$result = $conn->query($sql);
	$produtosSolicitacao = $result->fetchAll(PDO::FETCH_ASSOC);
	$numProdutos = count($produtosSolicitacao);
}

if (isset($_POST['inputData'])) {

	try {

		if ($_POST['cmbMotivo'] != '#') {
			$aMotivo = explode("#", $_POST['cmbMotivo']);
			$iMotivo = $aMotivo[0];
		} else {
			$iMotivo = null;
		}

		$origemArray = null;
		$idOrigem = null;
		$tipoOrigem = null;
		if ($_POST['cmbEstoqueOrigemLocalSetor'] != '#') {

			$origemArray = explode('#', $_POST['cmbEstoqueOrigemLocalSetor']);

			if (count($origemArray) > 2) {
				$idOrigem = $origemArray[0];
				$tipoOrigem = $origemArray[2];
			}
		} else if ($_POST['cmbEstoqueOrigem'] != '#') {
			$tipoOrigem = 'OrigemLocalTransferencia';
			$idOrigem = $_POST['cmbEstoqueOrigem'];
		}


		$destinoArray = null;
		$idDestino = null;
		$tipoDestino = null;
		if ($_POST['cmbDestinoLocalEstoqueSetor'] != '#') {

			$destinoArray = explode('#', $_POST['cmbDestinoLocalEstoqueSetor']);

			if (count($destinoArray) > 2) {
				$idDestino = $destinoArray[0];
				$tipoDestino = $destinoArray[2];
			}
		} else if ($_POST['cmbDestinoLocal'] != '#') {
			$tipoDestino = 'DestinoLocal';
			$idDestino = $_POST['cmbDestinoLocal'];
		} else if ($_POST['cmbDestinoSetor'] != '#') {
			$tipoDestino = 'DestinoSetor';
			$idDestino = $_POST['cmbDestinoSetor'];
		}
		var_dump($_POST);
		die;

		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimMotivo, MovimData, MovimFinalidade, MovimOrigemLocal, MovimOrigemSetor, MovimDestinoLocal, MovimDestinoSetor, MovimDestinoManual, 
										  MovimObservacao, MovimFornecedor, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie, MovimValorTotal, 
										  MovimChaveAcesso, MovimSituacao, MovimUsuarioAtualizador, MovimUnidade)
				VALUES (:sTipo, :iMotivo, :dData, :iFinalidade, :iOrigemLocal, :iOrigemSetor, :iDestinoLocal, :iDestinoSetor, :sDestinoManual, 
						:sObservacao, :iFornecedor, :iOrdemCompra, :sNotaFiscal, :dDataEmissao, :sNumSerie, :fValorTotal, 
						:sChaveAcesso, :iSituacao, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		/*echo $sql;
		echo "<br>";
		var_dump($_POST['inputTipo'], $_POST['cmbClassificacao'], $iMotivo, gravaData($_POST['inputData']), $_POST['cmbFinalidade'], $_POST['cmbOrigem'], $_POST['cmbDestinoLocal'],
		 $_POST['cmbDestinoSetor'], $_POST['inputDestinoManual'], $_POST['txtareaObservacao'], $_POST['cmbFornecedor'], $_POST['cmbOrdemCompra'], $_POST['inputNotaFiscal'],
		 gravaData($_POST['inputDataEmissao']), $_POST['inputNumSerie'], gravaValor($_POST['inputValorTotal']), $_POST['inputChaveAcesso'],
		 $_POST['cmbSituacao'], $_SESSION['UsuarId'], $_SESSION['EmpreId']);
		die;*/
		$conn->beginTransaction();

		$result->execute(array(
			':sTipo' => $_POST['inputTipo'],
			':iMotivo' => $iMotivo,
			':dData' => gravaData($_POST['inputData']),
			':iFinalidade' => isset($_POST['cmbFinalidade']) && $_POST['cmbFinalidade'] == '#' ? null : isset($_POST['cmbFinalidade']) ? $_POST['cmbFinalidade'] : null,

			':iOrigemLocal' => $tipoOrigem == 'Local' ? $idOrigem : $tipoOrigem == 'OrigemLocalTransferencia' ? $idOrigem : null,
			':iOrigemSetor' => $tipoOrigem == 'Setor' ? $idOrigem : null,

			':iDestinoLocal' => $tipoDestino == 'Local' ? $idDestino : $tipoDestino == 'DestinoLocal' ? $idDestino : null,

			':iDestinoSetor' => $tipoDestino == 'Setor' ? $idDestino : $tipoDestino == 'DestinoSetor' ? $idDestino : null,

			':sDestinoManual' => $_POST['inputDestinoManual'] == '' ? null : $_POST['inputDestinoManual'],
			':sObservacao' => $_POST['txtareaObservacao'],
			':iFornecedor' => $_POST['cmbFornecedor'] == '-1' ? null : $_POST['cmbFornecedor'],
			':iOrdemCompra' => $_POST['cmbOrdemCompra'] == '#' ? null : $_POST['cmbOrdemCompra'],
			':sNotaFiscal' => $_POST['inputNotaFiscal'] == '' ? null : $_POST['inputNotaFiscal'],
			':dDataEmissao' => $_POST['inputDataEmissao'] == '' ? null : gravaData($_POST['inputDataEmissao']),
			':sNumSerie' => $_POST['inputNumSerie'] == '' ? null : $_POST['inputNumSerie'],
			':fValorTotal' => $_POST['inputValorTotal'] == '' ? null : gravaValor($_POST['inputValorTotal']),
			':sChaveAcesso' => $_POST['inputChaveAcesso'] == '' ? null : $_POST['inputChaveAcesso'],
			':iSituacao' => $_POST['cmbSituacao'] == '#' ? null : $_POST['cmbSituacao'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$insertId = $conn->lastInsertId();

		try {

			$numItems = intval($_POST['inputNumItens']);

			for ($i = 1; $i <=  $numItems; $i++) {

				$campoSoma = $i;

				$campo = 'campo' . $i;

				//Aqui tenho que fazer esse IF, por causa das exclusões da Grid

				if (isset($_POST[$campo])) {
					//var_dump($campo);
					$registro = explode('#', $_POST[$campo]);

					if ($registro[0] == 'P') {

						$quantItens = intval($registro[3]);
						if (isset($registro[7])) {
							for ($i = 1; $i <= $quantItens; $i++) {
								// Incerindo o registro na tabela Patrimonio, caso o produto seja um bem permanente.

								if ($registro[7] == 2) {

									$sql = "SELECT COUNT(PatriNumero) as CONT
							              	FROM Patrimonio
										  	JOIN Situacao on SituaId = PatriStatus
										  	WHERE PatriUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
										  ";
									$result = $conn->query($sql);
									$patrimonios = $result->fetch(PDO::FETCH_ASSOC);
									$count = $patrimonios['CONT'];

									//Caso não seja o primeiro registro na tabela para esta empresa
									if ($count >= 1) {

										$ultimoPatri = $count;
										$numeroPatri = intval($ultimoPatri) + 1;
										$numeroPatriFinal = '';
										//var_dump($i);

										if ($numeroPatri < 10) $numeroPatriFinal = "000000" . $numeroPatri . "";
										if ($numeroPatri < 100 && $numeroPatri > 9) $numeroPatriFinal = "00000" . $numeroPatri . "";
										if ($numeroPatri < 1000 && $numeroPatri > 99) $numeroPatriFinal = "0000" . $numeroPatri . "";
										if ($numeroPatri < 10000 && $numeroPatri > 999) $numeroPatriFinal = "000" . $numeroPatri . "";
										if ($numeroPatri < 100000 && $numeroPatri > 9999) $numeroPatriFinal = "00" . $numeroPatri . "";
										if ($numeroPatri < 1000000 && $numeroPatri > 99999) $numeroPatriFinal = "0" . $numeroPatri . "";
										if ($numeroPatri < 10000000 && $numeroPatri > 999999) $numeroPatriFinal = $numeroPatri;


										// Selecionando o id da situacao 'ATIVO'
										$sql = "SELECT SituaId
												 FROM Situacao
												 WHERE SituaChave = 'ATIVO' 
												 ";
										$result = $conn->query($sql);
										$situacao = $result->fetch(PDO::FETCH_ASSOC);


										$sql = "INSERT INTO Patrimonio
												(PatriNumero, PatriStatus, PatriUsuarioAtualizador, PatriUnidade)
												VALUES 
												(:sNumero, :iStatus, :iUsuarioAtualizador, :iUnidade)";
										$result = $conn->prepare($sql);

										$result->execute(array(
											':sNumero' => $numeroPatriFinal,
											':iStatus' => $situacao['SituaId'],
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iUnidade' => $_SESSION['UnidadeId']
										));

										$insertIdPatrimonio = $conn->lastInsertId();


										$sql = "INSERT INTO MovimentacaoXProduto
						                        (MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrUnidade, MvXPrPatrimonio)
					                            VALUES 
						                        (:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iUnidade, :iPatrimonio)";
										$result = $conn->prepare($sql);

										$result->execute(array(
											':iMovimentacao' => $insertId,
											':iProduto' => $registro[1],
											':iQuantidade' => (int) $registro[3],
											':fValorUnitario' => isset($registro[2]) ? (float) $registro[2] : null,
											':sLote' => $registro[5],
											':dValidade' => $registro[6] != '0' ? $registro[6] : null,
											':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iUnidade' => $_SESSION['UnidadeId'],
											':iPatrimonio' => $insertIdPatrimonio
										));
									} else {

										//Caso seja o primeiro registro na tabela para esta empresa
										$numeroPatri = '0000001';

										// Selecionando o id da situacao 'ATIVO'
										$sql = "SELECT SituaId
												 FROM Situacao
												 WHERE SituaChave = 'ATIVO' 
												 ";
										$result = $conn->query($sql);
										$situacao = $result->fetch(PDO::FETCH_ASSOC);

										$sql = "INSERT INTO Patrimonio
												(PatriNumero, PatriStatus, PatriUsuarioAtualizador, PatriUnidade)
												VALUES 
												(:sNumero, :iStatus, :iUsuarioAtualizador, :iUnidade)";
										$result = $conn->prepare($sql);

										$result->execute(array(
											':sNumero' => $numeroPatri,
											':iStatus' => $situacao['SituaId'],
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iUnidade' => $_SESSION['UnidadeId']
										));

										$insertIdPatrimonio = $conn->lastInsertId();


										$sql = "INSERT INTO MovimentacaoXProduto
						                        (MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrUnidade, MvXPrPatrimonio)
					                            VALUES 
						                        (:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iUnidade, :iPatrimonio)";
										$result = $conn->prepare($sql);

										$result->execute(array(
											':iMovimentacao' => $insertId,
											':iProduto' => $registro[1],
											':iQuantidade' => (int) $registro[3],
											':fValorUnitario' => isset($registro[2]) ? (float) $registro[2] : null,
											':sLote' => $registro[5],
											':dValidade' => $registro[6] != '0' ? $registro[6] : gravaData('12/09/2333'),
											':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iUnidade' => $_SESSION['UnidadeId'],
											':iPatrimonio' => $insertIdPatrimonio
										));
									}
								} else {
									$quantItens = intval($registro[3]);

									for ($i = 1; $i <= $quantItens; $i++) {
										$sql = "INSERT INTO MovimentacaoXProduto
								            (MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrUnidade, MvXPrPatrimonio)
								            VALUES 
								            (:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iUnidade, :iPatrimonio)";
										$result = $conn->prepare($sql);

										$result->execute(array(
											':iMovimentacao' => $insertId,
											':iProduto' => $registro[1],
											':iQuantidade' => (int) $registro[3],
											':fValorUnitario' => isset($registro[2]) ? (float) $registro[2] : null,
											':sLote' => $registro[5],
											':dValidade' => $registro[6] != '0' ? $registro[6] : gravaData('12/09/2333'),
											':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
											':iUsuarioAtualizador' => $_SESSION['UsuarId'],
											':iUnidade' => $_SESSION['UnidadeId'],
											':iPatrimonio' => null
										));
									}
								}
								break 1;
							}
						} else {
							if ((int) $registro[3] > 0) {
								$sql = "INSERT INTO MovimentacaoXProduto
							            (MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrUnidade, MvXPrPatrimonio)
							            VALUES 
							            (:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iUnidade, :iPatrimonio)";
								$result = $conn->prepare($sql);

								$result->execute(array(
									':iMovimentacao' => $insertId,
									':iProduto' => $registro[1],
									':iQuantidade' => (int) $registro[3],
									':fValorUnitario' => isset($registro[2]) ? (float) $registro[2] : null,
									':sLote' => $registro[5],
									':dValidade' => $registro[6] != '0' ? $registro[6] : gravaData('12/09/2333'),
									':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
									':iUsuarioAtualizador' => $_SESSION['UsuarId'],
									':iUnidade' => $_SESSION['UnidadeId'],
									':iPatrimonio' => null
								));
							}
						}
					} else {
						$sql = "INSERT INTO MovimentacaoXServico
						        (MvXSrMovimentacao, MvXSrServico, MvXSrQuantidade, MvXSrValorUnitario, MvXSrLote, MvXSrUsuarioAtualizador, MvXSrUnidade)
					            VALUES 
						        (:iMovimentacao, :iServico, :iQuantidade, :fValorUnitario, :sLote, :iUsuarioAtualizador, :iUnidade)";
						$result = $conn->prepare($sql);

						$result->execute(array(
							':iMovimentacao' => $insertId,
							':iServico' => $registro[1],
							':iQuantidade' => (int) $registro[3],
							':fValorUnitario' => $registro[2] != '' ? (float) $registro[2] : null,
							':sLote' => $registro[5],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iUnidade' => $_SESSION['UnidadeId']
						));
					}
				}
			}
		} catch (PDOException $e) {
			$conn->rollback();
			echo 'Error1: ' . $e->getMessage();
			exit;
		}

		if (isset($_POST['cmbSituacao'])) {

			$sql = "SELECT SituaId, SituaNome, SituaChave
					FROM Situacao
					WHERE SituaId = " . $_POST['cmbSituacao'] . "
					";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			$destinoChave = '';

			if ($rowSituacao['SituaChave'] == 'AGUARDANDOLIBERACAO') $destinoChave = 'CENTROADMINISTRATIVO';
			if ($rowSituacao['SituaChave'] == 'PENDENTE') $destinoChave = 'ALMOXARIFADO';

			if ($rowSituacao['SituaChave'] != 'LIBERADO') {
				$sql = "SELECT PerfiId
				        FROM Perfil
				        WHERE PerfiChave = '" . $destinoChave . "' 
				        ";
				$result = $conn->query($sql);
				$rowPerfil = $result->fetch(PDO::FETCH_ASSOC);


				/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
				$sIdentificacao = 'Movimentação';

				$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandeSolicitante, 
								BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, BandeUnidade)
					VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iSolicitante, :sTabela, :iTabelaId, 
							:iStatus, :iUsuarioAtualizador, :iUnidade)";
				$result = $conn->prepare($sql);

				$result->execute(array(
					':sIdentificacao' => $sIdentificacao,
					':dData' => date("Y-m-d"),
					':sDescricao' => 'Liberar Movimentacao',
					':sURL' => '',
					':iSolicitante' => $_SESSION['UsuarId'],
					':sTabela' => 'Movimentacao',
					':iTabelaId' => $insertId,
					':iStatus' => $rowSituacao['SituaId'],
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iUnidade' => $_SESSION['UnidadeId']
				));

				$insertIdBande = $conn->lastInsertId();

				$sql = "INSERT INTO BandejaXPerfil (BnXPeBandeja, BnXPePerfil, BnXPeUnidade)
						VALUES (:iBandeja, :iPerfil, :iUnidade)";
				$result = $conn->prepare($sql);

				$result->execute(array(
					':iBandeja' => $insertIdBande,
					':iPerfil' => $rowPerfil['PerfiId'],
					':iUnidade' => $_SESSION['UnidadeId']
				));

				/* Fim Insere Bandeja */
			}
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação realizada!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao realizar movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
		exit;
	}

	irpara("movimentacao.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Movimentação</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script src="global_assets/js/lamparinas/jquery.maskMoney.js"></script> <!-- http://www.fabiobmed.com.br/criando-mascaras-para-moedas-com-jquery/ -->
	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		function produtosOrdemCompra(ordemCompra) {
			let inputLote = $('#inputLote').val();
			let numOrdemCompra = $('#cmbOrdemCompra').val()

			$.ajax({
				type: "POST",
				url: "movimentacaoAddProdutoOrdemCompra.php",
				data: {
					ordemCompra: ordemCompra,
					numOrdemCompra: numOrdemCompra,
					lote: inputLote
				},
				success: function(resposta) {
					$("#tabelaProdutoServico").html(resposta);

					let total = $('#total').html();

					$("#inputTotal").val(total);
					modalAcoes()
					mudarValores()

					let todasLinhas = $('.trGrid')
					$('#inputNumItens').val(todasLinhas.length)

				}

			});

			$.ajax({
				type: "POST",
				url: "movimentacaoSaldoOrdemCompra.php",
				data: {
					ordemCompra: ordemCompra,
				},
				success: function(resposta) {
					$("#inputTotalOrdemCompraCartaContrato").val(float2moeda(resposta)).attr('disabled', '').attr('valor', resposta);
				}
			})
		}

		function modalAcoes() {

			$('.btn-acoes').each((i, elem) => {
				$(elem).on('click', function() {
					$('#page-modal').fadeIn(200);

					let linha = $(elem).parent().parent()
					let todasLinhas = $(elem).parent().parent().parent()
					let saldoinicialModal = $(elem).parent().next().attr('saldoInicial') // selecionando o valor do input hidden

					if ($(elem).attr('idRow') == linha.attr('id')) {
						let tds = linha.children();
						let tipoProdutoServico = $(tds[8]).attr('tipo');



						let valores = [];

						let inputItem = $('<td></td>');
						let inputProdutoServico = $('<input type="text">');
						let inputQuantidade = $('<input type="text">');
						let inputSaldo = $('<input type="text">');
						let inputValidade = $('<input type="text">');

						let linhaTabela = '';

						tds.each((i, elem) => {
							valores[i] = $(elem).html();
						})

						inputItem.val(valores[0]);

						if (tipoProdutoServico != 'P') {

							cabecalho = `
							               
							                <tr class="bg-slate">
											     <th width="5%">Item</th>
											     <th width="75%">Serviço</th>
											     <th width="10%">Quantidade</th>
												 <th width="10%">Saldo</th>
											     <th width="10%"></th>
									     	</tr>
							                    `;

							linhaTabela = `<tr id='trModal'>
						                        <td>${valores[0]}</td>
												<td>${valores[1]}</td>
												<td><input id='quantidade' type="text" class="form-control" value="" style="text-align: center" autofocus></td>
												<td><input id='saldo' class="form-control" style="text-align: center"  value="${saldoinicialModal}" disabled></td>
											</tr>
						                  `;
						} else {
							cabecalho = `
							             	<tr class="bg-slate">
										        	<th width="5%">Item</th>
										        	<th width="45%">Produto</th>
										        	<th width="8%">Quantidade</th>
										        	<th width="10%">Saldo</th>
										        	<th width="10%">Lote</th>
										        	<th width="12%">Validade</th>
								    		</tr>
												`;

							linhaTabela = `<tr id='trModal'>
						                                    <td>${valores[0]}</td>
												            <td>${valores[1]}</td>
												            <td><input id='quantidade' quantMax='${valores[4]}' type="text" class="form-control" value="" style="text-align: center" autofocus></td>
												            <td><input id='saldo' type="text" class="form-control" value="${saldoinicialModal}" style="text-align: center"  disabled></td>
								                            <td><input id='lote' type="text" class="form-control" value="" style="text-align: center"></td>
															<td><input id='validade' type="date" class="form-control" value="" style="text-align: center"></td>
														</tr>
								                        `;
						}

						$('#thead-modal').html(cabecalho);

						$('#tbody-modal').html(linhaTabela);

						// Esta função não permite que o valor digitado pelo usuário seja maior que o valor de saldo.
						function validaQuantInputModal(quantMax) {
							$('#quantidade').on('keyup', function() {
								if (parseInt($('#quantidade').val()) > parseInt(quantMax)) {
									$('#quantidade').val(quantMax)
								}
							})
						}

						validaQuantInputModal($('#saldo').val())

						$('#quantidade').focus()
					}
				})
			})

			$('#modal-close').on('click', function() {
				$('#page-modal').fadeOut(200);
				$('body').css('overflow', 'scroll');
			})
		}

		function mudarValores() {
			$('#salvar').on('click', () => {

				let grid = $('.trGrid')
				let tdsModal = $('#trModal').children()

				grid.each((i1, elem1) => { // each sobre a grid
					let tr = $(elem1).children() // colocando todas as linhas em um 

					let td = tr.first()
					let indiceLinha = td.html()

					tdsModal.each((i, elem2) => {
						let indiceProdutoModal = $(elem2).html()
						let inputHiddenProdutoServico = $(`#campo${indiceLinha}`)
						let tipo = inputHiddenProdutoServico.attr('tipo')

						if (i == 0 && indiceProdutoModal == indiceLinha) {

							let novaQuantidade = $(tdsModal[2]).children().val() // pegando a quantidade digitada pelo usuário
							let saldo = $(tdsModal[3]).children().val() // pegando o saldo do produto
							let lote = $(tdsModal[4]).children().val() // pegando o lote digitado pelo usuário
							let validade = $(tdsModal[5]).children().val() // pegando a validade digitada pelo usuário

							let inputProdutoGridValores = inputHiddenProdutoServico.val()
							let arrayValInput = inputProdutoGridValores.split('#')

							// adicionando  novos dados no array
							arrayValInput[3] = novaQuantidade
							arrayValInput[4] = saldo
							arrayValInput[5] = lote
							arrayValInput[6] = validade

							var virgula = eval('/' + ',' + '/g') // buscando na string as ocorrências da ','

							var stringVallnput = arrayValInput.toString().replace(virgula, '#') // transformando novamente em string, e trocando as virgulas por #.

							inputHiddenProdutoServico.val(stringVallnput) // colocando a nova string com os valores no input do produto/servico.



							let quantInicial = inputHiddenProdutoServico.attr('quantInicial')
							let saldoInicial = inputHiddenProdutoServico.attr('saldoInicial')

							let novosValores = recalcValores(quantInicial, novaQuantidade, saldoInicial, arrayValInput[2])

							$(tr[3]).html(novosValores.quantAtualizada)
							$(tr[4]).html(novosValores.novoSaldo)
							$(tr[6]).html("R$ " + novosValores.valorTotal)
							$(tr[6]).attr('valorTotalSomaGeral', novosValores.somaTotalValorGeral)

							$('#inputNumItens').val()
							stringVallnput = ''

							// O input itemEditadoquantidade recebe como valor a ultima quantidade editata, para garantir que pelo menos uma quantidade de produtos ou serviços foi editada 
							$('#itemEditadoquantidade').val(novaQuantidade)
						}
					})
				})
				$('#page-modal').fadeOut(200);
				$('body').css('overflow', 'scroll');

				recalcValorTotal()
				calcSaldoOrdemCompra()
			})
		}

		function recalcValores(quantInicial, novaQuantidade, saldoInicial, valorUni) {


			let valorTotal = 0
			let novoSaldo = 0
			let quantAtualizada = 0
			/*quantInicial == novaQuantidade ? novoSaldo = saldoInicial : */
			novoSaldo = saldoInicial - novaQuantidade;

			//let valorTotal = novaQuantidade * valorUni;
			//let novoSaldo = saldoInicial - novaQuantidade;
			quantAtualizada = parseInt(novaQuantidade) + parseInt(quantInicial)

			return {
				quantAtualizada: quantAtualizada,
				valorTotal: float2moeda(quantAtualizada * valorUni),
				somaTotalValorGeral: novaQuantidade * valorUni,
				novoSaldo: novoSaldo
			};
		}

		function recalcValorTotal() {
			let novoTotalGeral = 0
			let velhoTotalGeral = $('#total').attr('valorTotalGeral')
			$('.trGrid').each((i, elem) => {
				$(elem).children().each((i, elem) => {
					if ($(elem).hasClass('valorTotal')) {

						if ($(elem).attr('valorTotalSomaGeral')) {
							novoTotalGeral += parseFloat($(elem).attr('valorTotalSomaGeral'))

							//$('#total').attr('valorTotalGeral', `${novoTotalGeral}`)
						}
					}
				})
			})

			$('#total').html(`R$ ${float2moeda(novoTotalGeral)}`).attr('valor', novoTotalGeral)
		}


		function inputsModal() {
			$('#tbody-modal')
		}

		function verificaTotalNotaFiscal() {
			let valorTotalNotaFiscal = $('#inputValorTotal').val().replace('.', '').replace(',', '.')
			let valorTotalNotaFiscalGrid = $('#total').attr('valor')

			if (parseFloat(valorTotalNotaFiscalGrid) != parseFloat(valorTotalNotaFiscal)) {
				alerta('Atenção', 'O valor total da Nota Fiscal informado não corresponde ao total da entrada.', 'error');
				$('#inputValorTotal').focus();
				$("#formMovimentacao").submit((e) => {
					e.preventDefault()
				})
				return false
			}

		}



		$(document).ready(function() {

			/* Início: Tabela Personalizada */
			$('#tabelaProdutoServicoSaida').DataTable({
				"order": [
					[0, "asc"]
				],
				autoWidth: false,
				responsive: true,
				columnDefs: [{
						orderable: true, //Item
						width: "5%",
						targets: [0]
					},
					{
						orderable: true, //Produto/Servico
						width: "30%",
						targets: [1]
					},
					{
						orderable: true, //Unidade Medida
						width: "15%",
						targets: [2]
					},
					{
						orderable: true, //Quantidade
						width: "10%",
						targets: [3]
					},
					{
						orderable: true, //Valor Unitário
						width: "10%",
						targets: [4]
					},
					{
						orderable: true, //Valor Total
						width: "10%",
						targets: [5]
					},
					{
						orderable: false, //Classificação
						width: "15%",
						targets: [6]
					},
					{
						orderable: false, //Ações
						width: "5%",
						targets: [7]
					}
				],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: {
						'first': 'Primeira',
						'last': 'Última',
						'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
						'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
					}
				}
			});


			// Select2 for length menu styling
			var _componentSelect2 = function() {
				if (!$().select2) {
					console.warn('Warning - select2.min.js is not loaded.');
					return;
				}

				// Initialize
				$('.dataTables_length select').select2({
					minimumResultsForSearch: Infinity,
					dropdownAutoWidth: true,
					width: 'auto'
				});
			};

			_componentSelect2();



			$('#cmbEstoqueOrigemLocalSetor').on('change', function(e) {
				const cmbOrigem = $('#cmbEstoqueOrigemLocalSetor').val();

				try {

					$.getJSON('filtraPatrimonio.php?idCategoria=' + cmbOrigem, function(dados) {
						let option = '<option value="#" "selected">Selecione o Produto</option>';
						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.ProduId + '#' + obj.ProduCustoFinal + '">' + obj.ProduNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							ResetProduto();
						}
					});


					FiltraCategoriaOrigem('#Categoria');

				} catch (err) {
					console.log('OrigemLocalSetor: ' + err);
				}

				filtraPatrimonioProdutoOrigem();
			});



			$('#cmbPatrimonio').on('change', function(e) {
				FiltraCategoriaPatrimonio({
					tipo: '#CategoriaPatrimonio',
					valor: e.target.value
				});
				// $('#cmbProduto').change();
			});

			function filtraPatrimonioProdutoOrigem() {
				const cmbOrigem = $('#cmbEstoqueOrigemLocalSetor').val().split('#')
				const selectPatrimonio = document.querySelector('#cmbPatrimonio');
				let tipoDeFiltro = 'Patrimonio'

				$('#cmbPatrimonio').html('<option value="#" "selected">Filtrando...</option>');
				try {
					$.ajax({
						type: "POST",
						url: "filtraPorOrigemTransferencia.php",
						data: {
							origem: cmbOrigem[0],
							tipoDeFiltro: tipoDeFiltro
						},
						success: function(resposta) {
							var option = '<option value="#" "selected">Selecione o Patrimônio</option>';
							if (resposta !== '') {
								$('#cmbPatrimonio').html('');
								$('#cmbPatrimonio').append(option)
								$('#cmbPatrimonio').append(resposta)
							} else {
								$('#cmbPatrimonio').html('<option value="#" "selected">Sem Patrimônios</option>');
							}
						}
					})
				} catch (err) {
					console.log('Erro ao filtrar patrimonios: ' + err);
				}
			}



			function FiltraCategoriaOrigem(props) {
				const cmbOrigemLocalSetor = $('#cmbEstoqueOrigemLocalSetor').val()
				let tipoDeFiltro = props;

				FiltraCategoria();

				$('#cmbCategoria').html('<option value="#" "selected">Filtrando...</option>');

				try {
					$.ajax({
						type: "POST",
						url: "filtraPorOrigemTransferencia.php",
						data: {
							origem: cmbOrigemLocalSetor,
							tipoDeFiltro: tipoDeFiltro
						},
						success: function(resposta) {
							var option = '<option value="#" "selected">Selecione a Categoria</option>';
							if (resposta !== 'sem dados') {
								$('#cmbCategoria').html('');
								$('#cmbCategoria').append(option)
								$('#cmbCategoria').append(resposta)

							} else {
								$('#cmbCategoria').html('<option value="#" "selected">Sem categorias</option>');
							}
						}
					})
				} catch (err) {
					console.log('Erro ao carregar dados da Categoria: ' + err);
				}
			}



			function FiltraCategoriaPatrimonio(props) {
				const cmbOrigemLocalSetor = $('#cmbEstoqueOrigemLocalSetor').val()

				FiltraCategoria();

				$('#cmbCategoria').html('<option value="#" "selected">Filtrando...</option>');

				if (props.valor === "#") {
					$('#cmbCategoria').html('<option value="" "selected">Selecione a Categoria</option>');
					$('#cmbSubCategoria').html('<option value="" "selected">Selecione a Subcategoria</option>');
					$('#cmbProduto').html('<option value="" "selected">Selecione o Produto</option>');
					$('#cmbCategoria').prop('disabled', '');
					$('#cmbSubCategoria').prop('disabled', '');
					$('#cmbProduto').prop('disabled', '');
					// $('#formMovimentacao').submit();
					var inputTipo = $('input[name="inputTipo"]:checked').val();
					var cmbProduto = $('#cmbProduto').val();
					var inputValorUnitario = $('#inputValorUnitario').val();
					$('#inputValorUnitario').val('');
					$('#inputLote').val('');
					$('#inputQuantidade').val('');

				} else {
					try {
						$.ajax({
							type: "POST",
							url: "filtraPorOrigemTransferencia.php",
							data: {
								origem: cmbOrigemLocalSetor,
								tipoDeFiltro: props.tipo,
								valor: props.valor,
								campo: 'categoria'
							},
							success: function(respostaCategoria) {
								var option = '<option value="#" >Selecione a Categoria</option>';
								if (respostaCategoria !== 'sem dados') {
									$('#cmbCategoria').html('');
									$('#cmbCategoria').append(option)
									$('#cmbCategoria').append(respostaCategoria);
									// $('#cmbCategoria').prop('disabled', 'disabled');

								} else {
									$('#cmbCategoria').html('<option value="" "selected">Sem Categorias</option>');
									// $('#cmbCategoria').prop('disabled', 'disabled');
								}
							}
						})
					} catch (err) {
						console.log('Erro ao carregar dados da Categoria: ' + err);
					}

					try {
						$.ajax({
							type: "POST",
							url: "filtraPorOrigemTransferencia.php",
							data: {
								origem: cmbOrigemLocalSetor,
								tipoDeFiltro: props.tipo,
								valor: props.valor,
								campo: 'subcategoria'
							},
							success: function(respostaSubCategoria) {
								var option = '<option value="#" >Selecione a SubCategoria</option>';
								if (respostaSubCategoria !== 'sem dados') {
									$('#cmbSubCategoria').html('');
									$('#cmbSubCategoria').append(option)
									$('#cmbSubCategoria').append(respostaSubCategoria);
									// $('#cmbSubCategoria').prop('disabled', 'disabled');
								} else {
									$('#cmbSubCategoria').html('<option value="" "selected">Sem Subcategorias</option>');
									// $('#cmbSubCategoria').prop('disabled', 'disabled');
								}
							}
						})
					} catch (err) {
						console.log('Erro ao carregar dados da Categoria: ' + err);
					}

					try {
						$.ajax({
							type: "POST",
							url: "filtraPorOrigemTransferencia.php",
							data: {
								origem: cmbOrigemLocalSetor,
								tipoDeFiltro: props.tipo,
								valor: props.valor,
								campo: 'produto'
							},
							success: function(respostaProduto) {
								var option = '<option value="#" >Selecione o produto</option>';
								if (respostaProduto !== 'sem dados') {
									$('#cmbProduto').html('');
									$('#cmbProduto').append(option)
									$('#cmbProduto').append(respostaProduto);
									// $('#cmbProduto').prop('disabled', 'disabled');

									var inputTipo = $('input[name="inputTipo"]:checked').val();
									var cmbProduto = $('#cmbProduto').val();
									var inputValorUnitario = $('#inputValorUnitario').val();

									var Produto = cmbProduto.split("#");
									var valor = Produto[1].replace(".", ",");

									if (valor != 'null' && valor) {
										$('#inputValorUnitario').val(valor);
									} else {
										$('#inputValorUnitario').val('0,00');
									}
									$('#inputQuantidade').focus();
								} else {
									$('#cmbProduto').html('<option value="" "selected">Sem Produtos</option>');
									// $('#cmbProduto').prop('disabled', 'disabled');
								}
							}
						})
					} catch (err) {
						console.log('Erro ao carregar dados da Categoria: ' + err);
					}



				}
			}



			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e) {
				const idCategoria = $('#cmbCategoria').val()
				const idSubCategoria = $('#cmbSubCategoria').val()
				const tipoDeFiltro = 'produto';

				Filtrando();

				const inputTipo = document.querySelector('input[name="inputTipo"]:checked').value;
				const cmbCategoria = document.querySelector('#cmbCategoria').value;
				const cmbSubCategoria = document.querySelector('#cmbSubCategoria').value;


				$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria, function(dados) {

					let option = '<option value="#">Selecione a SubCategoria</option>';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}
				}).fail(function(m) {

				});

			});




			//Impede que o input quantidade receba letras
			$('#inputQuantidade').on('keydown', () => {
				let valor = $('#inputQuantidade').val()

				if (valor == '´' || valor == '~' || valor == '`' || valor == ';') {
					$('#inputQuantidade').val('')
				}
				if (event.keyCode != '8' && event.keyCode != '48' && event.keyCode != '49' && event.keyCode != '50' && event.keyCode != '51' && event.keyCode != '52' && event.keyCode != '53' && event.keyCode != '54' && event.keyCode != '55' && event.keyCode != '56' && event.keyCode != '57' && event.keyCode != '96' && event.keyCode != '97' && event.keyCode != '98' && event.keyCode != '99' && event.keyCode != '100' && event.keyCode != '101' && event.keyCode != '102' && event.keyCode != '103' && event.keyCode != '104' && event.keyCode != '105' && event.keyCode != '106' && event.keyCode != '107') {
					return false
				}

				if (event.keyCode == '222' && event.keyCode != '219' && event.keyCode != '191') {
					return false
				}
			})



			$('#cmbSubCategoria').on('change', function(e) {
				const idCategoria = $('#cmbCategoria').val()
				const idSubCategoria = $('#cmbSubCategoria').val()
				const tipoDeFiltro = 'produto';

				FiltraCategoriaProduto({
					tipoDeFiltro: 'produto',
					idCategoria: idCategoria,
					idSubCategoria: idSubCategoria
				});
			});

			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			function FiltraCategoriaProduto(props) {

				$('#cmbProduto').html('<option value="#" "selected">Filtrando...</option>');


				try {
					$.ajax({
						type: "POST",
						url: "filtraProdutoTransferencia.php",
						data: {
							tipoDeFiltro: props.tipoDeFiltro,
							idCategoria: props.idCategoria,
							idSubCategoria: props.idSubCategoria
						},
						success: function(respostaProduto) {

							var option = '<option value="#" selected>Selecione o produto</option>';
							if (respostaProduto !== 'sem dados') {
								$('#cmbProduto').html('');
								$('#cmbProduto').append(option)
								$('#cmbProduto').append(respostaProduto);
							} else {
								$('#cmbProduto').html('<option value="" "selected">Sem Produtos</option>');
							}
						}
					})
				} catch (err) {
					console.log('Erro ao carregar dados da Categoria: ' + err);
				}

			};

			//Ao mudar o Produto, trazer o Valor Unitário do cadastro (retorno via JSON)
			$('#cmbProduto').on('change', function(e) {

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbProduto = $('#cmbProduto').val();
				var inputValorUnitario = $('#inputValorUnitario').val();

				var Produto = cmbProduto.split("#");
				var valor = Produto[1].replace(".", ",");

				if (valor != 'null' && valor) {
					$('#inputValorUnitario').val(valor);
				} else {
					$('#inputValorUnitario').val('0,00');
				}
				$('#inputQuantidade').focus();
			});


			$("input[type=radio][name=inputTipo]").click(function() {

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();

				if (inputNumItens > 0) {
					alerta('Atenção', 'O tipo não pode ser alterado quando se tem produto(s) na lista! Exclua-o(s) primeiro ou cancele e recomece o cadastro da movimentação.', 'error');
					return false;
				}

				$('#cmbCategoria').val("#");
				$('#inputQuantidade').val("");
				$('#inputValorUnitario').val("");
				$('#inputLote').val("");
				$('#inputValidade').val("");
			});



			$('#btnAdicionar').click(function() {
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				var cmbProduto = $('#cmbProduto').val();
				var Produto = cmbProduto.split("#");
				var inputQuantidade = $('#inputQuantidade').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				var inputTotal = $('#inputTotal').val();
				var inputLote = $('#inputLote').val();
				var inputValidade = $('#inputValidade').val();
				var cmbClassificacao = $('#cmbClassificacao').val();
				var inputIdProdutos = $('#inputIdProdutos').val(); //esse aqui guarda todos os IDs de produtos que estão na grid para serem movimentados

				//remove os espaços desnecessários antes e depois
				inputQuantidade = inputQuantidade.trim();

				//Verifica se o campo só possui espaços em branco
				if (inputQuantidade == '') {
					alerta('Atenção', 'Informe a quantidade antes de adicionar!', 'error');
					$('#inputQuantidade').focus();
					return false;
				}

				//Verifica se o campo só possui espaços em branco
				if (inputValorUnitario == '') {
					alerta('Atenção', 'Nenhum produto foi selecionado!', 'error');
					$('#cmbProduto').focus();
					return false;
				}

				//Verifica se a combo Classificação foi informada
				if (cmbClassificacao == '#') {

					if (inputProdutoServico == 'P') {
						alerta('Atenção', 'Informe a Classificação/Bens!', 'error');
						$('#cmbClassificacao').focus();
						return false;
					}
				}

				//Verifica se o campo já está no array
				// if (inputIdProdutos.includes(Produto[0])) {
				// 	alerta('Atenção', 'Esse produto já foi adicionado!', 'error');
				// 	$('#cmbProduto').focus();
				// 	return false;
				// }

				var resNumItens = parseInt(inputNumItens) + 1;
				var total = parseInt(inputQuantidade) * parseFloat(inputValorUnitario.replace(',', '.'));

				total = total + parseFloat(inputTotal);
				var totalFormatado = "R$ " + float2moeda(total).toString();

				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				let origem = $('#cmbEstoqueOrigem').val()
				$.ajax({
					type: "POST",
					url: "movimentacaoAddProduto.php",
					data: {
						tipo: inputTipo,
						numItens: resNumItens,
						idProduto: Produto[0],
						origem: origem,
						quantidade: inputQuantidade,
						classific: cmbClassificacao
					},
					success: function(resposta) {

						//var newRow = $("<tr>");

						//newRow.append(resposta);
						if (resposta != 'SEMESTOQUE') {

							var inputTipo = $('input[name="inputTipo"]:checked').val();

							$("#tabelaProdutoServicoSaida").append(resposta);

							//Adiciona mais um item nessa contagem
							$('#inputNumItens').val(resNumItens);
							$('#cmbProduto').val("#").change();
							$('#inputQuantidade').val('');
							$('#inputValorUnitario').val('');
							$('#inputTotal').val(total);
							$('#total').text(totalFormatado);
							$('#inputLote').val('');
							$('#inputValidade').val('');

							$('#inputProdutos').append('<input type="hidden" class="inputProdutoServicoClasse" id="campo' + resNumItens + '" name="campo' + resNumItens + '" value="' + 'P#' + Produto[0] + '#' + inputValorUnitario + '#' + inputQuantidade + '#' + 'SaldoValNull' + '#' + inputLote + '#' + inputValidade + '#' + cmbClassificacao + '">');

							inputIdProdutos = inputIdProdutos + ', ' + parseInt(Produto[0]);

							$('#inputIdProdutos').val(inputIdProdutos);

							$('input[name="inputTipo"]').each((i, elem) => {
								if ($(elem) != $('input[name="inputTipo"]:checked')) {
									$(elem).attr('disabled', '')
								}
							})

							function classBemSaidaSolicit(valor, idSelect) {
								let grid = $('.trGrid')

								grid.each((i1, elem1) => { // each sobre a grid
									let tr = $(elem1).children() // colocando todas as linhas em um 

									let td = tr.first()
									let indiceLinha = td.html()

									//let inputProdutoGridValores = inputHiddenProdutoServico.val()
									if (idSelect == indiceLinha) {

										let valueProdutoServicoArray = $(`#campo${indiceLinha}`).val().split('#')
										// adicionando  novos dados no array
										valueProdutoServicoArray[valueProdutoServicoArray.length - 1] = valor

										var ponto = eval('/' + '.' + '/g')

										valueProdutoServicoArray[2] = valueProdutoServicoArray[2].replace(',', '.')



										var virgula = eval('/' + ',' + '/g') // buscando na string as ocorrências da ','

										var stringVallnput = valueProdutoServicoArray.toString().replace(virgula, '#') // transformando novamente em string, e trocando as virgulas por #.

										$(`#campo${indiceLinha}`).val(stringVallnput) // colocando a nova string com os valores no input do produto/servico.
									}
								})
							}

							$('.selectClassific2').each((i, elem) => {

								$(elem).on('change', function(e) {

									let valor = $(elem).val()
									let idSelect = $(elem).attr('id')
									classBemSaidaSolicit(valor, idSelect)
								})
							})

							return false;
						} else {
							console.log(resposta)
							alerta('Atenção', 'Estoque indisponível!', 'error');
							return false;
						}
					}
				})

			}); //click

			function produtosSolicitacaoSaida() {
				$('.produtoSolicitacao').each((i, elem) => {
					var tds = $(elem).children()
					var idProdutoGrid = $(elem).attr('idProduSolicitacao')
					var idGridProdu = $(tds[0]).html()
					var quantProduGrid = $(tds[3]).html()
					var valUnitProduGrid = $(tds[5]).attr('valorUntPrSolici')

					$('#inputProdutos').append('<input type="hidden" class="inputProdutoServicoClasse" id="campo' + idGridProdu + '" name="campo' + idGridProdu + '" value="' + 'P#' + idProdutoGrid + '#' + valUnitProduGrid + '#' + quantProduGrid + '#' + 0 + '#' + 0 + '#' + 0 + '#' + 0 + '">');
				})
				//$('#inputProdutos').append('<input type="hidden" id="campo' + resNumItens + '" name="campo' + resNumItens + '" value="' + 'P#' + Produto[0] + '#' + inputValorUnitario + '#' + inputQuantidade + '#' + 'SaldoValNull' + '#' + inputLote + '#' + inputValidade + '#' + cmbClassificacao + '">');
			}
			produtosSolicitacaoSaida()

			$(document).on('click', '.btn_remove', function() {

				var inputTotal = $('#inputTotal').val();
				var button_id = $(this).attr("id");
				var Produto = button_id.split("#");
				var inputIdProdutos = $('#inputIdProdutos').val(); //array com o Id dos produtos adicionados
				var inputNumItens = $('#inputNumItens').val();

				var item = inputIdProdutos.split(",");

				var i;
				var arr = [];

				for (i = 0; i < item.length; i++) {
					arr.push(item[i]);
				}

				var index = arr.indexOf(Produto[0]);

				arr.splice(index, 1);

				$('#inputIdProdutos').val(arr);

				$("#row" + Produto[0] + "").remove(); //remove a linha da tabela
				$("#campo" + Produto[0] + "").remove(); //remove o campo hidden

				//Agora falta calcular o valor total novamente
				inputTotal = parseFloat(inputTotal) - parseFloat(Produto[1]);
				var totalFormatado = "R$ " + float2moeda(inputTotal).toString();

				$('#inputTotal').val(inputTotal);
				$('#total').text(totalFormatado);


				var resNumItens = parseInt(inputNumItens) - 1;
				$('#inputNumItens').val(resNumItens);

				if (resNumItens == 0) {
					$('#cmbFornecedor').prop('disabled', false);
				}
			})

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputTotal = $('#inputTotal').val();
				var cmbFinalidade = $('#cmbFinalidade').val();
				var cmbMotivo = $('#cmbMotivo').val();
				var cmbEstoqueOrigem = $('#cmbEstoqueOrigem').val();
				var cmbEstoqueOrigemLocalSetor = $('#cmbEstoqueOrigemLocalSetor').val();
				var cmbOrdemCompra = $('#cmbOrdemCompra').val()
				var cmbDestinoLocal = $('#cmbDestinoLocal').val();
				var cmbDestinoLocalEstoqueSetor = $('#cmbDestinoLocalEstoqueSetor').val();
				var cmbDestinoSetor = $('#cmbDestinoSetor').val();
				var inputDestinoManual = $('#inputDestinoManual').val();
				var inputValorTotal = $('#inputValorTotal').val().trim()
				var Motivo = cmbMotivo.split("#");
				var chave = Motivo[1];

				//remove os espaços desnecessários antes e depois
				inputDestinoManual = inputDestinoManual.trim();


				//Verifica se a combo Motivo foi informada
				if (cmbMotivo == '#') {
					alerta('Atenção', 'Informe o Motivo!', 'error');
					$('#cmbMotivo').focus();
					return false;
				}

				//Verifica se a combo Finalidade foi informada
				if (cmbFinalidade == '#') {
					alerta('Atenção', 'Informe a Finalidade!', 'error');
					$('#cmbFinalidade').focus();
					return false;
				}

				//Verifica se a combo Estoque de Origem foi informada
				if (cmbEstoqueOrigemLocalSetor == '#') {
					alerta('Atenção', 'Informe o Estoque de Origem!', 'error');
					$('#cmbEstoqueOrigem').focus();
					return false;
				}

				if (chave == 'DOACAO' || chave == 'DESCARTE' || chave == 'DEVOLUCAO' || chave == 'CONSIGNACAO') {

					//Verifica se o input Destino foi informado
					if (inputDestinoManual == '') {
						alerta('Atenção', 'Informe o Destino!', 'error');
						$('#inputDestinoManual').focus();
						return false;
					}
				} else {

					//Verifica se a combo Estoque de Destino foi informada
					if (cmbDestinoLocalEstoqueSetor == '#') {
						alerta('Atenção', 'Informe o Estoque de Destino!', 'error');
						$('#cmbDestinoLocal').focus();
						return false;
					}
				}


				//Verifica se tem algum produto na Grid
				if (inputTotal == '' || inputTotal == 0) {
					alerta('Atenção', 'Informe algum produto!', 'error');
					$('#cmbCategoria').focus();
					return false;
				}

				//desabilita as combos "Fornecedor" e "Situacao" na hora de gravar, senão o POST não o encontra
				$('#cmbSituacao').prop('disabled', false);

				$("#formMovimentacao").submit();
				//console.log(inputTipo)
			});



			//Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
			function Filtrando() {
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
				FiltraProduto();
			}

			//Mostra o "Filtrando..." na combo Produto
			function FiltraCategoria() {
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
			}

			//Mostra o "Filtrando..." na combo Produto
			function FiltraProduto() {
				$('#cmbProduto').empty().append('<option>Filtrando...</option>');
			}

			function FiltraOrdensCompra() {
				$('#cmbOrdemCompra').empty().append('<option>Filtrando...</option>');
			}

			function ResetCategoria() {
				$('#cmbCategoria').empty().append('<option>Sem Categoria</option>');
			}

			function ResetSubCategoria() {
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}

			function ResetProduto() {
				$('#cmbProduto').empty().append('<option>Sem produto</option>');
			}


			function classBemSaidaSolicit(valor, idSelect) {
				let grid = $('.trGrid')

				grid.each((i1, elem1) => { // each sobre a grid
					let tr = $(elem1).children() // colocando todas as linhas em um 

					let td = tr.first()
					let indiceLinha = td.html()

					//let inputProdutoGridValores = inputHiddenProdutoServico.val()
					if (idSelect == indiceLinha) {
						//let arrayValInput = inputProdutoGridValores.split('#')
						let valueProdutoServicoArray = $(`#campo${indiceLinha}`).val().split('#')
						// adicionando  novos dados no array
						valueProdutoServicoArray[7] = valor


						var virgula = eval('/' + ',' + '/g') // buscando na string as ocorrências da ','

						var stringVallnput = valueProdutoServicoArray.toString().replace(virgula, '#') // transformando novamente em string, e trocando as virgulas por #.

						$(`#campo${indiceLinha}`).val(stringVallnput) // colocando a nova string com os valores no input do produto/servico.
					}
				})
			}
			$('.selectClassific').each((i, elem) => {

				$(elem).on('change', function(e) {

					let valor = $(elem).val()
					let idSelect = $(elem).attr('id')
					classBemSaidaSolicit(valor, idSelect)
				})
			})



		}); //document.ready	

		function mudaTotalTitulo(tipoTela) {

			if (tipoTela == 'E') {
				$('#totalTitulo').html('Total (R$) Nota Fiscal:')
				$('#quantEditaEntradaSaida').html('Quant. Recebida')
			} else if (tipoTela == 'S') {
				$('#totalTitulo').html('Total (R$):')
				$('#quantEditaEntradaSaida').html('Quantidade')
			}

		}

		function limpaValorFormulario(tipo) {
			if (tipo == 'E') {
				$("#cmbEstoqueOrigem").val("#")
				$("#cmbEstoqueOrigemLocalSetor").val("#")
				$("#cmbDestinoLocalEstoqueSetor").val("#")
				$("#cmbDestinoSetor").val("#")
			} else if (tipo == 'S') {
				$("#cmbEstoqueOrigemLocalSetor").val("#")
				$("#cmbDestinoLocalEstoqueSetor").val("#")
				$("#cmbDestinoLocal").val("#")
			} else {
				$("#cmbDestinoLocal").val("#")
				$("#cmbDestinoSetor").val("#")
				$("#cmbEstoqueOrigem").val("#")
			}
		}

		Array.prototype.remove = function(start, end) {
			this.splice(start, end);
			return this;
		}

		Array.prototype.insert = function(pos, item) {
			this.splice(pos, 0, item);
			return this;
		}

		function selecionaTipo(tipo) {

			$('#divConteudo').css({
				"background-color": "#eeeded",
				"box-shadow": "none"
			});
			$('#divConteudo').html('<div style="text-align:center;"><img src="global_assets/images/lamparinas/loader-transparente.gif" width="200" /></div>');

			if (tipo == 'E') {

				location.href = 'movimentacaoNovoEntrada.php';

				document.getElementById('EstoqueOrigem').style.display = "none";
				document.getElementById('EstoqueOrigemLocalSetor').style.display = "none";
				document.getElementById('DestinoLocalEstoqueSetor').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "none";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "block";
				document.getElementById('dadosProduto').style.display = "none";
				document.getElementById('trEntrada').style.display = "table-row";
				document.getElementById('trSaida').style.display = "none";
				document.getElementById('Patrimonio').style.display = "none";

				mudaTotalTitulo('E')
				limpaValorFormulario('E')
			} else if (tipo == 'S') {

				location.href = 'movimentacaoNovoSaida.php';

				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('EstoqueOrigemLocalSetor').style.display = "none";
				document.getElementById('DestinoLocalEstoqueSetor').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('DestinoSetor').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "none";
				document.getElementById('dadosProduto').style.display = "flex";
				document.getElementById('trEntrada').style.display = "none";
				document.getElementById('trSaida').style.display = "table-row";
				document.getElementById('radiosProdutoServico').style.display = "flex";
				document.getElementById('Patrimonio').style.display = "none";


				mudaTotalTitulo('S')
				limpaValorFormulario('S')
			} else {
				document.getElementById('EstoqueOrigem').style.display = "none";
				document.getElementById('EstoqueOrigemLocalSetor').style.display = "block";
				document.getElementById('DestinoLocalEstoqueSetor').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "block";
				document.getElementById('dadosNF').style.display = "none";
				document.getElementById('dadosProduto').style.display = "flex";
				document.getElementById('radiosProdutoServico').style.display = "none";
				document.getElementById('Patrimonio').style.display = "flex";

				document.getElementById('formLote').style.display = "block";
				document.getElementById('formValidade').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				$('#tituloProdutoServico').html('Dados dos Produtos')
				$('[for=cmbProduto]').html('Produto')

				limpaValorFormulario('T')
			}



			if (tipo == 'S') {
				$('#cmbSituacao').children().each((i, elem) => {
					if (i == 2) {
						$(elem).attr('selected', '')
						let text = $(elem).html()
						$('#select2-cmbSituacao-container').attr('title', text)
						$('#select2-cmbSituacao-container').html(text)

					} else {
						$(elem).removeAttr('selected')
					}
				})
			} else {
				$('#cmbSituacao').children().each((i, elem) => {
					if (i == 1) {
						$(elem).attr('selected', '')
						let text = $(elem).html()
						$('#select2-cmbSituacao-container').attr('title', text)
						$('#select2-cmbSituacao-container').html(text)

					} else {
						$(elem).removeAttr('selected')
					}
				})
			}

		}

		$(document).ready(() => {
			$('[name=inputTipo]').each((i, elem) => {
				if ($(elem).attr('checked') && $(elem).val() == 'S') {
					document.getElementById('EstoqueOrigem').style.display = "block";
					document.getElementById('EstoqueOrigemLocalSetor').style.display = "none";
					document.getElementById('DestinoLocalEstoqueSetor').style.display = "none";
					document.getElementById('DestinoLocal').style.display = "none";
					document.getElementById('DestinoSetor').style.display = "block";
					document.getElementById('classificacao').style.display = "block";
					document.getElementById('motivo').style.display = "none";
					document.getElementById('dadosNF').style.display = "none";
					document.getElementById('dadosProduto').style.display = "flex";

					mudaTotalTitulo('S')
				}
			})

		})



		function selecionaProdutoServico(tipo) {
			if (tipo == 'P') {
				document.getElementById('formLote').style.display = "block";
				document.getElementById('formValidade').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				$('#tituloProdutoServico').html('Dados dos Produtos')
				$('[for=cmbProduto]').html('Produto')
			} else {
				document.getElementById('formLote').style.display = "none";
				document.getElementById('formValidade').style.display = "none";
				document.getElementById('classificacao').style.display = "none";
				$('#tituloProdutoServico').html('Dados dos Serviços')
				$('[for=cmbProduto]').html('Serviço')
			}
		}

		function selecionaMotivo(motivo) {
			var Motivo = motivo.split("#");
			var chave = Motivo[1];

			if (chave == 'DOACAO' || chave == 'DESCARTE' || chave == 'DEVOLUCAO' || chave == 'CONSIGNACAO') {
				document.getElementById('DestinoManual').style.display = "block";
				document.getElementById('DestinoLocalEstoqueSetor').style.display = "none";
			} else {
				document.getElementById('DestinoManual').style.display = "none";
				document.getElementById('DestinoLocalEstoqueSetor').style.display = "block";
				document.getElementById('DestinoManual').value = '';
			}
		}

		function verifcMumero(elem) {
			if (typeof $(elem).val() == 'string') {
				return false
			}
		}

		function float2moeda(num) {

			x = 0;

			if (num < 0) {
				num = Math.abs(num);
				x = 1;
			}
			if (isNaN(num)) num = "0";
			cents = Math.floor((num * 100 + 0.5) % 100);

			num = Math.floor((num * 100 + 0.5) / 100).toString();

			if (cents < 10) cents = "0" + cents;
			for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
				num = num.substring(0, num.length - (4 * i + 3)) + '.' +
				num.substring(num.length - (4 * i + 3));
			ret = num + ',' + cents;
			if (x == 1) ret = ' - ' + ret;

			return ret;

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

					<div class="card-header header-elements-inline">
						<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Movimentação</h5>
					</div>

					<div class="card-body">
						<div class="row">
							<div class="col-lg-4">
								<div class="form-group">
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input type="radio" name="inputTipo" value="E" class="form-input-styled" onclick="selecionaTipo('E')" data-fouc>
											Entrada
										</label>
									</div>
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input type="radio" name="inputTipo" value="S" class="form-input-styled" onclick="selecionaTipo('S')" data-fouc>
											Saída
										</label>
									</div>
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input type="radio" name="inputTipo" value="T" class="form-input-styled" checked data-fouc>
											Transferência
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /card-body -->

				</div>
				<!-- /info blocks -->

				<!-- Info blocks -->
				<div class="card" id="divConteudo">

					<form name="formMovimentacao" id="formMovimentacao" method="post" class="form-validate-jquery" action="movimentacaoNovoTransferencia.php">
						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data<span style="color: red">*</span></label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>

										<div class="col-lg-2" id="motivo">
											<div class="form-group">
												<label for="cmbMotivo">Motivo</label>
												<select id="cmbMotivo" name="cmbMotivo" class="form-control form-control-select2" onChange="selecionaMotivo(this.value)">
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT MotivId, MotivNome, MotivChave
																	FROM Motivo
																	JOIN Situacao on SituaId = MotivStatus
																	WHERE MotivEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO'
																	ORDER BY MotivNome ASC";
													$result = $conn->query($sql);
													$rowMotivo = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowMotivo as $item) {
														print('<option value="' . $item['MotivId'] . '#' . $item['MotivChave'] . '">' . $item['MotivNome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4" id="EstoqueOrigemLocalSetor">
											<div class="form-group">
												<label for="cmbEstoqueOrigemLocalSetor">Origem</label>
												<select id="cmbEstoqueOrigemLocalSetor" name="cmbEstoqueOrigemLocalSetor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT LcEstId as Id, LcEstNome as Nome, 'Local' as Referencia 
																		FROM LocalEstoque
																		JOIN Situacao on SituaId = LcEstStatus
																		WHERE LcEstUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		UNION
																		SELECT SetorId as Id, SetorNome as Nome, 'Setor' as Referencia 
																		FROM Setor
																		JOIN Situacao on SituaId = SetorStatus
																		WHERE SetorUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		Order By Nome";

													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['Id'] . '#' . $item['Nome'] . '#' . $item['Referencia'] . '">' . $item['Nome'] . '</option>');
													}

													?>
												</select>
											</div>
										</div>


										<div class="col-lg-4" id="DestinoLocalEstoqueSetor">
											<div class="form-group">
												<label for="cmbDestinoLocalEstoqueSetor">Destino<span style="color: red">*</span></label>
												<select id="cmbDestinoLocalEstoqueSetor" name="cmbDestinoLocalEstoqueSetor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT LcEstId as Id, LcEstNome as Nome, 'Local' as Referencia 
																		FROM LocalEstoque
																		JOIN Situacao on SituaId = LcEstStatus
																		WHERE LcEstUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		UNION
																		SELECT SetorId as Id, SetorNome as Nome, 'Setor' as Referencia 
																		FROM Setor
																		JOIN Situacao on SituaId = SetorStatus
																		WHERE SetorUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		Order By Nome";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['Id'] . '#' . $item['Nome'] . '#' . $item['Referencia'] . '">' . $item['Nome'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4" id="DestinoManual" style="display:none">
											<div class="form-group">
												<label for="inputDestinoManual">Destino<span style="color: red">*</span></label>
												<input type="text" id="inputDestinoManual" name="inputDestinoManual" class="form-control">
											</div>
										</div>

									</div>
								</div>
							</div>



							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaObservacao">Observação</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação" maxlength="4000"></textarea>
									</div>
								</div>
							</div>
							<br>



							<div class="row" id="dadosProduto">
								<div class="col-lg-12">
									<div class="row" id="Patrimonio">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbPatrimonio">Patrimônio</label>
												<select id="cmbPatrimonio" name="cmbPatrimonio" class="form-control form-control-select2">
													<option value="#">Informe a origem</option>
												</select>
											</div>
										</div>
									</div>
									<br>

									<h5 class="mb-0 font-weight-semibold" id="tituloProdutoServico">Dados dos Produtos</h5>
									<br>

									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="">Selecione</option>

												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="">Selecione</option>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control form-control-select2">
													<option value="">Selecione</option>
												</select>
											</div>
										</div>
									</div>



									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputQuantidade">Quantidade</label>
												<input type="text" maxlength="10" id="inputQuantidade" name="inputQuantidade" class="form-control" onKeyUp="onlynumber(this)">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorUnitario">Valor Unitário</label>
												<input type="text" id="inputValorUnitario" name="inputValorUnitario" class="form-control" readOnly>
											</div>
										</div>

										<div class="col-lg-2" id="formLote">
											<div class="form-group">
												<label for="inputLote">Lote</label>
												<input type="text" maxlength="50" id="inputLote" name="inputLote" class="form-control">
											</div>
										</div>


										<div class="col-lg-2">
											<div class="form-group">
												<button type="button" id="btnAdicionar" class="btn btn-lg btn-principal" style="margin-top:20px;">Adicionar</button>
												<!--<button id="adicionar" type="button">Teste</button>-->
											</div>
										</div>
									</div>
								</div>
							</div>



							<div class="row">
								<div class="col-lg-12">
									<?php
									if (isset($_POST['inputSolicitacaoId'])) {
										print('<table class="table" id="tabelaProdutoServicoSaida">');
									} else {
										print('<table class="table" id="tabelaProdutoServico">');
									}
									?>
									<thead>
										<?php
										if (isset($_POST['inputSolicitacaoId'])) {
											print('
												    <tr class="bg-slate" id="trSaida" >
												        <th>Item</th>
												        <th>Produto/Serviço</th>
												        <th style="text-align:center">Unidade Medida</th>
												        <th id="quantEditaEntradaSaida" style="text-align:center">Quantidade</th>
												        <th style="text-align:right">Valor Unitário</th>
												        <th style="text-align:right">Valor Total</th>
												        <th id="classificacaoSaida">Classificação</th>
												        <th class="text-center">Ações</th>
											        </tr>
												');
										} else {
											print('
                                             
											        <tr class="bg-slate" id="trEntrada">
											            <th>Item</th>
											            <th>Produto/Serviço</th>
											            <th style="text-align:center">Unidade Medida</th>
											            <th id="quantEditaEntradaSaida" style="text-align:center">Quant. Recebida</th>
											            <th style="text-align:center">Saldo</th>
											            <th style="text-align:right">Valor Unitário</th>
											            <th style="text-align:right">Valor Total</th>
											            <th class="text-center">Ações</th>
													</tr>
													<tr class="bg-slate" id="trSaida" style="display: none; width: 100%">
													    <th>Item</th>
													    <th>Produto/Serviço</th>
													    <th style="text-align:center">Unidade Medida</th>
													    <th id="quantEditaEntradaSaida" style="text-align:center">Quantidade</th>
													    <th style="text-align:right">Valor Unitário</th>
													    <th style="text-align:right">Valor Total</th>
													    <th id="classificacaoSaida">Classificação</th>
													    <th class="text-center">Ações</th>
												    </tr>
											');
										}


										?>
									</thead>
									<tbody>
										<?php
										print('<tr style="display:none;">
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
											            <td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
										            </tr>
								            ');
										?>
									</tbody>
									<tfoot>
										<tr>
											<th id="totalTitulo" colspan="6" style="text-align:right; font-size: 16px; font-weight:bold;">Total (R$): </th>
											<?php
											if (isset($_POST['inputSolicitacaoId'])) {
												print('
														    <th colspan="1">
														        <div id="total" style="text-align:right; font-size: 15px; font-weight:bold;">' . formataMoeda($totalGeral) . '</div>
													        </th>
															');

												print('<th colspan="1">

												       </th>
												');
											} else {
												print('
														    <th colspan="1">
														        <div id="total" style="text-align:right; font-size: 15px; font-weight:bold;">R$ 0,00</div>
													        </th>
												');
												print('<th colspan="2">

														</th>
												');
											}
											?>

										</tr>
									</tfoot>
									</table>
								</div>
							</div>
							<br>
							<br>



							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSituacao">Situação</label>
												<!--<option value="#">Selecione</option>-->
												<?php

												if (isset($_POST['inputSolicitacaoId'])) {
													$sql = "SELECT SituaId, SituaNome, SituaChave
															FROM Situacao
															WHERE SituaStatus = '1'
															ORDER BY SituaNome ASC";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													print('<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" disabled>');

													foreach ($row as $item) {
														if ($item['SituaChave'] == 'LIBERADO') {
															print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
														}
													}
												} else {
													if ($_SESSION['PerfiChave'] == 'CENTROADMINISTRATIVO' || $_SESSION['PerfiChave'] == 'ADMINISTRADOR') {
														$sql = "SELECT SituaId, SituaNome, SituaChave
																FROM Situacao
																WHERE SituaStatus = '1'
																ORDER BY SituaNome ASC";
														$result = $conn->query($sql);
														$row = $result->fetchAll(PDO::FETCH_ASSOC);

														print('<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2">');
														print('<option value="#">Selecione</option>');

														foreach ($row as $item) {
															if ($item['SituaChave'] == 'AGUARDANDOLIBERACAO' || $item['SituaChave'] == 'PENDENTE' || $item['SituaChave'] == 'LIBERADO') {
																if ($item['SituaChave'] == 'AGUARDANDOLIBERACAO') {
																	print('<option value="' . $item['SituaId'] . '" selected>' . $item['SituaNome'] . '</option>');
																} else {
																	print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
																}
															}
														}
													} else {

														$sql = "SELECT SituaId, SituaNome, SituaChave
																	FROM Situacao
																	WHERE SituaStatus = '1'
																	ORDER BY SituaNome ASC";
														$result = $conn->query($sql);
														$row = $result->fetchAll(PDO::FETCH_ASSOC);

														print('<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" disabled>');
														print('<option value="#">Selecione</option>');

														foreach ($row as $item) {
															if ($item['SituaChave'] == 'AGUARDANDOLIBERACAO') {
																print('<option value="' . $item['SituaId'] . '" selected>' . $item['SituaNome'] . '</option>');
															} else if ($item['SituaChave'] == 'LIBERADO') {
																print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
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

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="movimentacao.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>


				</div>
				<!-- /info blocks -->

				<div id="page-modal" class="custon-modal">
					<div class="custon-modal-container">
						<div class="card custon-modal-content">
							<div class="custon-modal-title">
								<i class=""></i>
								<p class="h3">Itens Recebidos</p>
								<i class=""></i>
							</div>
							<div class="card-footer mt-2 d-flex flex-column">
								<table class="table table-modal">
									<thead id="thead-modal">

									</thead>
									<tbody id="tbody-modal">

									</tbody>
								</table>
								<div class="row" style="margin-top: 10px;">
									<div class="col-lg-12">
										<div class="form-group">
											<button class="btn btn-lg btn-principal" id="salvar">Salvar</button>
											<a id="modal-close" class="btn btn-basic" role="button">Cancelar</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>

</html>