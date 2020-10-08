<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Nova Movimentação';

include('global_assets/php/conexao.php');

if (isset($_POST['inputData'])) {

	try {

		var_dump($_POST);die;

		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimMotivo, MovimData, MovimFinalidade, MovimOrigemLocal, MovimOrigemSetor, MovimDestinoLocal, MovimDestinoSetor, MovimDestinoManual, 
										  MovimObservacao, MovimFornecedor, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie, MovimValorTotal, 
										  MovimChaveAcesso, MovimSituacao, MovimUsuarioAtualizador, MovimUnidade)
				VALUES (:sTipo, :iMotivo, :dData, :iFinalidade, :iOrigemLocal, :iOrigemSetor, :iDestinoLocal, :iDestinoSetor, :sDestinoManual, 
						:sObservacao, :iFornecedor, :iOrdemCompra, :sNotaFiscal, :dDataEmissao, :sNumSerie, :fValorTotal, 
						:sChaveAcesso, :iSituacao, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		/*echo $sql;
		echo "<br>";
		var_dump($_POST['inputTipo'], $_POST['cmbClassificacao'], gravaData($_POST['inputData']), $_POST['cmbDestinoLocal'],
		 $_POST['txtareaObservacao'], $_POST['cmbFornecedor'], $_POST['cmbOrdemCompra'], $_POST['inputNotaFiscal'],
		 gravaData($_POST['inputDataEmissao']), $_POST['inputNumSerie'], gravaValor($_POST['inputValorTotal']), $_POST['inputChaveAcesso'],
		 $_POST['cmbSituacao'], $_SESSION['UsuarId'], $_SESSION['EmpreId']);
		die;*/
		$conn->beginTransaction();

		$result->execute(array(
			':sTipo' => $_POST['inputTipo'],
			':iMotivo' => null,
			':dData' => gravaData($_POST['inputData']),
			':iFinalidade' => null,

			':iOrigemLocal' => null,
			':iOrigemSetor' => null,

			':iDestinoLocal' => $_POST['cmbDestinoLocal'],
			':iDestinoSetor' => null,
			':sDestinoManual' => null,

			':sObservacao' => $_POST['txtareaObservacao'],
			':iFornecedor' => $_POST['cmbFornecedor'] == '#' ? null : $_POST['cmbFornecedor'],
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
												<td><input id='quantidade' type="text" class="form-control" value="" onkeypress="return onlynumber(event)" style="text-align: center" autofocus></td>
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
												<td><input id='quantidade' quantMax='${valores[4]}' type="text" class="form-control" value="" onkeypress="return onlynumber(event)" style="text-align: center" autofocus></td>
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

		function calcSaldoOrdemCompra() {
			let valorTotal = $('#total').attr('valor')
			let valorSaldoOrdemCompra = $("#totalSaldo").attr('valorTotalInicial')

			let calcSaldoAtual = (parseFloat(valorSaldoOrdemCompra) - parseFloat(valorTotal))

			if (calcSaldoAtual < 0) {
				alerta('Atenção', 'O valor total da Ordem de Compra foi ultrapaçado.', 'error');
				$('#totalSaldo').html('R$ ' + float2moeda(calcSaldoAtual)).attr('valor', calcSaldoAtual)
				return
			} else {
				$('#totalSaldo').html('R$ ' + float2moeda(calcSaldoAtual)).attr('valor', calcSaldoAtual)
			}
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
			$('#tabelaProdutoServico').DataTable({
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
						width: "21%",
						targets: [1]
					},
					{
						orderable: true, //Unidade Medida
						width: "12%",
						targets: [2]
					},
					{
						orderable: true, //Quantidade
						width: "12%",
						targets: [3]
					},
					{
						orderable: true, //Saldo
						width: "10%",
						targets: [4]
					},
					{
						orderable: true, //Valor Unitário
						width: "12%",
						targets: [5]
					},
					{
						orderable: true, //Valor Total
						width: "10%",
						targets: [6]
					},
					{
						orderable: false, //Classificação
						width: "13%",
						targets: [7]
					},
					{
						orderable: false, //Ações
						width: "5%",
						targets: [8]
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

			//Ao mudar o fornecedor, filtra a categoria, subcategoria e produto via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e) {

				var inputFornecedor = $('#inputFornecedor').val();
				var cmbFornecedor = $('#cmbFornecedor').val();

				$('#inputFornecedor').val(cmbFornecedor);

				FiltraOrdensCompra();

				$.get('filtraOrdemCompra.php?idFornecedor=' + cmbFornecedor, function(dados) {

					var option = '<option value="#">Selecione</option>';
					if (dados) {
						$('#cmbOrdemCompra').html(option).show();
						$('#cmbOrdemCompra').append(dados).show();

					} else {
						$('#cmbOrdemCompra').html(option).show();
					}
				});

			});

			$('#cmbOrdemCompra').on('change', function() {
				var ordemCompra = '';
				$('#cmbOrdemCompra').children().each((i, elem) => {
					if ($(elem).val() == $('#cmbOrdemCompra').val()) {
						ordemCompra = $(elem).attr('idOrdemCompra');
					}
				});

				if (ordemCompra) {

					produtosOrdemCompra(ordemCompra)
				}

			})

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				var inputTotal = $('#inputTotal').val();
				var cmbOrdemCompra = $('#cmbOrdemCompra').val()
				var cmbDestinoLocal = $('#cmbDestinoLocal').val();
				var inputValorTotal = $('#inputValorTotal').val().trim()

				//Verifica se a combo Estoque de Destino foi informada
				if (cmbDestinoLocal == '#') {
					alerta('Atenção', 'Informe o Estoque de Destino!', 'error');
					$('#cmbDestinoLocal').focus();
					return false;
				}

				if (cmbOrdemCompra == '#') {
					alerta('Atenção', 'Informe a Ordem Compra / Carta Contrato!', 'error');
					$('#cmbDestinoLocal').focus();
					return false;
				}

				// Verifica se pelomento um produto ou serviço foi editado, na entrada.
				if ($('#itemEditadoquantidade').val() == 0) {
					alerta('Atenção', 'Informe a quantidade de itens que deseja dar entrada no sistema!', 'error');
					return false;
				}

				if (inputValorTotal == '') {
					alerta('Atenção', 'Informe o valor Total da nota fiscal!', 'error');
					return false;
				}

				verificaTotalNotaFiscal();

				//Verifica se tem algum produto na Grid
				if (inputTotal == '' || inputTotal == 0) {
					alerta('Atenção', 'Informe algum produto!', 'error');
					$('#cmbCategoria').focus();
					return false;
				}

				//desabilita as combos "Fornecedor" e "Situacao" na hora de gravar, senão o POST não o encontra
				$('#cmbFornecedor').prop('disabled', false);
				$('#cmbSituacao').prop('disabled', false);

				$("#formMovimentacao").submit();
				
				//console.log(inputTipo)
			});

			function FiltraOrdensCompra() {
				$('#cmbOrdemCompra').empty().append('<option>Filtrando...</option>');
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

		Array.prototype.remove = function(start, end) {
			this.splice(start, end);
			return this;
		}

		Array.prototype.insert = function(pos, item) {
			this.splice(pos, 0, item);
			return this;
		}

		function selecionaTipo(tipo) {

			$('#divConteudo').css({"background-color":"#eeeded",
									"box-shadow":"none"
									});
			$('#divConteudo').html('<div style="text-align:center;"><img src="global_assets/images/lamparinas/loader-transparente.gif" width="200" /></div>');

			if (tipo == 'E') {				

				location.href='movimentacaoNovoEntrada.php';

				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('classificacao').style.display = "none";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "block";
				document.getElementById('dadosProduto').style.display = "none";
				document.getElementById('trEntrada').style.display = "table-row";
			} else if (tipo == 'S') {

				location.href='movimentacaoNovoSaida.php';

				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "none";
				document.getElementById('dadosProduto').style.display = "flex";
				document.getElementById('trEntrada').style.display = "none";
			} else {

				location.href='movimentacaoNovoTransferencia.php';

				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "block";
				document.getElementById('dadosNF').style.display = "none";
				document.getElementById('dadosProduto').style.display = "flex";

				document.getElementById('formLote').style.display = "block";
				document.getElementById('formValidade').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				$('#tituloProdutoServico').html('Dados dos Produtos')
				$('[for=cmbProduto]').html('Produto')
			}

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
											<input type="radio" name="inputTipo" value="E" class="form-input-styled" checked data-fouc>
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
											<input type="radio" name="inputTipo" value="T" class="form-input-styled" onclick="selecionaTipo('T')" data-fouc>
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
					
					<form name="formMovimentacao" id="formMovimentacao" method="post" class="form-validate-jquery" action="movimentacaoNovo.php">
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

										<div class="col-lg-4" id="DestinoLocal">
											<div class="form-group">
												<label for="cmbDestinoLocal">Destino<span style="color: red">*</span></label>
												<select id="cmbDestinoLocal" name="cmbDestinoLocal" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT LcEstId, LcEstNome
															FROM LocalEstoque
															JOIN Situacao on SituaId = LcEstStatus
															WHERE LcEstUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
															ORDER BY LcEstNome ASC";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item) {
														print('<option value="' . $item['LcEstId'] . '">' . $item['LcEstNome'] . '</option>');
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
										<label for="txtareaObservacao">Observação</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação" maxlength="4000"></textarea>
									</div>
								</div>
							</div>
							<br>

							<div class="row" id="dadosNF">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Dados da Nota Fiscal</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbFornecedor">Fornecedor<span style="color: red">*</span></label>
												<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php
													$sql = "SELECT ForneId, ForneNome
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

										<input type="hidden" id="inputFornecedor" name="inputFornecedor" value="#">

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbOrdemCompra">Nº Ordem Compra / Carta Contrato<span style="color: red">*</span></label>
												<select id="cmbOrdemCompra" name="cmbOrdemCompra" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
												</select>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTotalOrdemCompraCartaContrato">Total (R$) Ordem de Compra/Carta Contrato</label>
												<input type="text" id="inputTotalOrdemCompraCartaContrato" name="inputTotalOrdemCompraCartaContrato" class="form-control" onKeyUp="moeda(this)" maxLength="11">
											</div>
										</div>
									</div> <!-- row -->

									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNotaFiscal">Nº Nota Fiscal</label>
												<input type="text" id="inputNotaFiscal" name="inputNotaFiscal" class="form-control" placeholder="Nº NF" maxlength="50">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataEmissao">Data Emissão</label>
												<input type="text" id="inputDataEmissao" name="inputDataEmissao" class="form-control" placeholder="Data NF">
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Nº Série Nota Fiscal</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" maxLength="30" placeholder="Nº Série">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorTotal">Total (R$) Nota Fiscal<span style="color: red">*</span></label>
												<input type="text" id="inputValorTotal" name="inputValorTotal" class="form-control" onKeyUp="moeda(this)" maxLength="11">
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputChaveAcesso">Chave de Acesso</label>
												<input type="text" id="inputChaveAcesso" name="inputChaveAcesso" class="form-control" placeholder="Chave de Acesso NF" maxlength="100">
											</div>
										</div>

									</div> <!-- row -->
								</div> <!-- col-lg-12 -->
							</div> <!-- row -->
							<br>

							<div id="inputProdutos">
								<input type="hidden" id="inputNumItens" name="inputNumItens" value="0">
								<input type="hidden" id="itemEditadoquantidade" name="itemEditadoquantidade" value="0">
								<input type="hidden" id="inputIdProdutos" name="inputIdProdutos" value="0">
								<input type="hidden" id="inputProdutosRemovidos" name="inputProdutosRemovidos" value="0">
								<input type="hidden" id="inputTotal" name="inputTotal" value="0">
							</div>

							<div class="row">
								<div class="col-lg-12">
									<table class="table" id="tabelaProdutoServico">
										<thead>
											<?php
												print('
													<tr class="bg-slate" id="trEntrada">
														<th>Item</th>
														<th>Produto/Serviço</th>
														<th style="text-align:center">Unidade Medida</th>
														<th style="text-align:center">Quant. Recebida</th>
														<th style="text-align:center">Saldo</th>
														<th style="text-align:right">Valor Unitário</th>
														<th style="text-align:right">Valor Total</th>
														<th class="text-center">Ações</th>
													</tr>
												');
											?>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<th id="totalTitulo" colspan="6" style="text-align:right; font-size: 16px; font-weight:bold;">Total (R$) Nota Fiscal: </th>
												<?php
													print('
															<th colspan="1">
																<div id="total" style="text-align:right; font-size: 15px; font-weight:bold;">R$ 0,00</div>
															</th>
													');
													print('<th colspan="2">

															</th>
													');
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