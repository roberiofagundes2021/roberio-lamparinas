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

/* VALIDA SE OS DADOS VIERAM DA MESMA PÁGINA */
if (isset($_POST['inputData'])) {

	try {
		//var_dump($_POST);die;

		$conn->beginTransaction();

		$sqlMovi = "SELECT MAX(MovimNumRecibo) as MovimNumRecibo
        FROM Movimentacao
        WHERE MovimUnidade = '$_SESSION[UnidadeId]'";
		$resultMovi = $conn->query($sqlMovi);
		$rowMovi = $resultMovi->fetch(PDO::FETCH_ASSOC);

		$newMovi = explode('/', $rowMovi['MovimNumRecibo']);
		$newMovi = (intval($newMovi[0])+1).'/'.(date("Y"));

		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimNumRecibo, MovimData, MovimOrigemLocal, MovimDestinoSetor, MovimObservacao, 
				MovimValorTotal, MovimSituacao, MovimUnidade, MovimUsuarioAtualizador)
				VALUES (:sTipo, :dMovi, :dData, :iOrigemLocal, :iDestinoSetor, :sObservacao, :fValorTotal, :iSituacao, 
				:iUnidade, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sTipo' => 'S',  // Saída
			':dMovi' => $newMovi,  // Número incremental
			':dData' => gravaData($_POST['inputData']),
			':iOrigemLocal' => $_POST['cmbEstoqueOrigem'],
			':iDestinoSetor' => $_POST['cmbDestinoSetor'],
			':sObservacao' => $_POST['txtareaObservacao'],
			':fValorTotal' => $_POST['inputTotal'],
			':iSituacao' => $_POST['cmbSituacao'],
			':iUnidade' => $_SESSION['UnidadeId'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId']
		));

		$insertId = $conn->lastInsertId();

		$numItems = intval($_POST['inputNumItens']);

		for ($i = 1; $i <=  $numItems; $i++) {

			$campo = 'campo' . $i;

			//Aqui tenho que fazer esse IF, por causa das exclusões da Grid
			if (isset($_POST[$campo])) {

				$registro = explode('#', $_POST[$campo]);

				if ($registro[0] == 'P') {

					$quantItens = intval($registro[3]);

					//Se classificação
					if (isset($registro[7])) {
							
						// Se produto é um bem permanente (Insere na tabela Patrimonio).
						if ($registro[7] == 2) {

							// Selecionando o id da situacao 'ATIVO'
							$sql = "SELECT SituaId
									FROM Situacao
									WHERE SituaChave = 'ATIVO' ";
							$result = $conn->query($sql);
							$situacao = $result->fetch(PDO::FETCH_ASSOC);

							$sql = "SELECT COUNT(PatriNumero) as CONT
									FROM Patrimonio
									JOIN Situacao on SituaId = PatriStatus
									WHERE PatriUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO' ";
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

							} else {

								//Caso seja o primeiro registro na tabela para esta empresa
								$numeroPatriFinal = '0000001';
							}

							$sql = "INSERT INTO Patrimonio
									(PatriNumero, PatriNumSerie, PatriEstadoConservacao, PatriProduto, PatriStatus, PatriUsuarioAtualizador, PatriUnidade)
									VALUES 
									(:sNumero, :sNumSerie, :iEstadoConservacao, :iProduto, :iStatus, :iUsuarioAtualizador, :iUnidade)";
							$result = $conn->prepare($sql);

							$result->execute(array(
								':sNumero' => $numeroPatriFinal,
								':sNumSerie' => null,
								':iEstadoConservacao' => null,
								':iProduto' => $registro[1],
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
								':sLote' => $registro[5] != '' ? $registro[5] : null,
								':dValidade' => $registro[6] != '' ? $registro[6] : null,
								':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
								':iUsuarioAtualizador' => $_SESSION['UsuarId'],
								':iUnidade' => $_SESSION['UnidadeId'],
								':iPatrimonio' => $insertIdPatrimonio
							));
							
						} else {

							$sql = "INSERT INTO MovimentacaoXProduto
								(MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrUnidade, MvXPrPatrimonio)
								VALUES 
								(:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iUnidade, :iPatrimonio)";
							$result = $conn->prepare($sql);

							$result->execute(array(
								':iMovimentacao' => $insertId,
								':iProduto' => $registro[1],
								':iQuantidade' => (int) $registro[3],
								':fValorUnitario' => isset($registro[2]) ? gravaValor($registro[2]) : null,
								':sLote' => $registro[5],
								':dValidade' => $registro[6] != '0' ? $registro[6] : null,
								':iClassificacao' => isset($registro[7]) ? (int) $registro[7] : null,
								':iUsuarioAtualizador' => $_SESSION['UsuarId'],
								':iUnidade' => $_SESSION['UnidadeId'],
								':iPatrimonio' => null
							));
						}
					} else {
						if ((int) $registro[3] > 0) { //Quantidade > 0
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
								':iPatrimonio' => null
							));
						}
					}
				} else {
					$sql = "INSERT INTO MovimentacaoXServico
							(MvXSrMovimentacao, MvXSrServico, MvXSrQuantidade, MvXSrValorUnitario, MvXSrUsuarioAtualizador, MvXSrUnidade)
							VALUES 
							(:iMovimentacao, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
					$result = $conn->prepare($sql);

					$result->execute(array(
						':iMovimentacao' => $insertId,
						':iServico' => $registro[1],
						':iQuantidade' => (int) $registro[3],
						':fValorUnitario' => $registro[2] != '' ? gravaValor($registro[2]) : null,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
					));
				}
			}
		}

		$conn->commit();

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação de saída realizada!!!";
		$_SESSION['msg']['tipo'] = "success";
	} catch (PDOException $e) {

		$conn->rollback();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao realizar movimentação de saída!!!";
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
		document.addEventListener("DOMContentLoaded", () => {
			initFunction();
		});

		function initFunction() {
			writeFields();
			writeDataTable();
		}

		function writeFields() {
			const inputTipo = document.querySelector('input[type="radio"][name="inputTipo"]:checked');

			if (inputTipo.checked == true && inputTipo.value == 'S') {
				document.querySelector('#EstoqueOrigem').style.display = "block";
				document.querySelector('#DestinoSetor').style.display = "block";
				document.querySelector('#classificacao').style.display = "block";
				document.querySelector('#dadosProduto').style.display = "flex";
				mudaTotalTitulo();
			}
		};

		function writeDataTable() {

			/* Início: Tabela Personalizada */
			$(document).ready(function() {
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
				})

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
			});
		};

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
						}
					}
				})
			})

			$('#total').html(`R$ ${float2moeda(novoTotalGeral)}`).attr('valor', novoTotalGeral)
		}

		function inputsModal() {
			$('#tbody-modal')
		}

		$(document).ready(function() {

			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e) {

				Filtrando();

				var inputProdutoServico = $('input[name="inputProdutoServico"]:checked').val();
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria=' + cmbCategoria + '&produtoServico=' + inputProdutoServico, function(dados) {

					var option = '<option value="#">Selecione a SubCategoria</option>';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}
				})

				if (inputProdutoServico == 'S') {

					$.getJSON('filtraServico.php?idCategoria=' + cmbCategoria, function(dados) {

						var option = '<option value="#" "selected">Selecione o Serviço</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {

								option += '<option value="' + obj.ServiId + '#' + obj.ServiCustoFinal + '">' + obj.ServiNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							ResetServico();
						}
					}).fail(function(m) {
						//console.log(m);
					});

				} else {

					$.getJSON('filtraProduto.php?idCategoria=' + cmbCategoria, function(dados) {

						var option = '<option value="#" "selected">Selecione o Produto</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.ProduId + '#' + obj.ProduCustoFinal + '">' + obj.ProduNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							ResetProduto();
						}
					});
				}


			});

			function filtraCategoriaOrigem() {

				let cmbOrigem = $('#cmbEstoqueOrigem').val();
				let tipoDeFiltro = 'Categoria';
				var inputProdutoServico = $('input[name="inputProdutoServico"]:checked').val();

				$('#cmbCategoria').html('<option value="#" "selected">Filtrando...</option>');

				$.ajax({
					type: "POST",
					url: "filtraPorOrigem.php",
					data: {
						origem: cmbOrigem,
						tipoDeFiltro: tipoDeFiltro
					},
					success: function(resposta) {

						var option = '<option value="#" "selected">Selecione a Categoria</option>';

						if (resposta) {
							$('#cmbCategoria').html('');
							$('#cmbCategoria').append(option)
							$('#cmbCategoria').append(resposta)

						} else {
							$('#cmbCategoria').html('<option value="#" "selected">Sem categorias</option>');
						}

					} //.fail(function(m) {
					//console.log(m);
					//});
				})

				Filtrando();

				$.getJSON('filtraSubCategoria.php?idCategoria=-1', function(dados) {

					var option = '<option value="#">Selecione a SubCategoria</option>';

					if (dados.length) {

						$.each(dados, function(i, obj) {
							option += '<option value="' + obj.SbCatId + '">' + obj.SbCatNome + '</option>';
						});

						$('#cmbSubCategoria').html(option).show();
					} else {
						$('#cmbSubCategoria').empty().append('<option value="#">Selecione</option>');
					}
				});

				if (inputProdutoServico == 'S') {
					$.getJSON('filtraServico.php?idCategoria=-1', function(dados) {

						var option = '<option value="#" "selected">Selecione o Serviço</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {

								option += '<option value="' + obj.ServiId + '#' + obj.ServiCustoFinal + '">' + obj.ServiNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							$('#cmbProduto').empty().append('<option value="#">Selecione</option>');
						}
					})
				} else {
					$.getJSON('filtraProduto.php?idCategoria=-1', function(dados) {

						var option = '<option value="#" "selected">Selecione o Produto</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.ProduId + '#' + obj.ProduCustoFinal + '">' + obj.ProduNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							$('#cmbProduto').empty().append('<option value="#">Selecione</option>');
						}
					});
				}
			}

			$('#cmbEstoqueOrigem').on('change', function(e) {
				filtraCategoriaOrigem();
			})
			//filtraCategoriaOrigem();

			function filtraPatrimonioProdutoOrigem() {
				let cmbOrigem = $('#cmbEstoqueOrigemLocalSetor').val().split('#')
				let tipoDeFiltro = 'Patrimonio'

				$('#cmbPatrimonio').html('<option value="#" "selected">Filtrando...</option>');

				$.ajax({
					type: "POST",
					url: "filtraPorOrigem.php",
					data: {
						origem: cmbOrigem[0],
						tipoDeFiltro: tipoDeFiltro
					},
					success: function(resposta) {
						var option = '<option value="#" "selected">Selecione o Patrimônio</option>';
						console.log(resposta);
						if (resposta) {
							$('#cmbPatrimonio').html('');
							$('#cmbPatrimonio').append(option)
							$('#cmbPatrimonio').append(resposta)
						} else {
							$('#cmbPatrimonio').html('<option value="#" "selected">Sem Patrimônios</option>');
						}
					} //.fail(function(m) {
					//console.log(m);
					//});
				})
			}

			$('#cmbEstoqueOrigemLocalSetor').on('change', function(e) {
				filtraPatrimonioProdutoOrigem()
			})
			//filtraPatrimonioProdutoOrigem() 

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

			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbSubCategoria').on('change', function(e) {

				//Escreve "Filtrando..." no select cmbProduto
				FiltraProduto();

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputProdutoServico = $('input[name="inputProdutoServico"]:checked').val();
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();

				//Se Serviço
				if (inputProdutoServico == 'S') {

					$.getJSON('filtraServico.php?idCategoria=' + cmbCategoria + '&idSubCategoria=' + cmbSubCategoria, function(dados) {

						var option = '<option value="#" "selected">Selecione o Serviço</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {

								option += '<option value="' + obj.ServiId + '#' + obj.ServiCustoFinal + '">' + obj.ServiNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							ResetServico();
						}
					}).fail(function(m) {
						//console.log(m);
					});

				} else { //Se Produto

					$.getJSON('filtraProduto.php?idCategoria=' + cmbCategoria + '&idSubCategoria=' + cmbSubCategoria, function(dados) {

						var option = '<option value="#" "selected">Selecione o Produto</option>';

						if (dados.length) {

							$.each(dados, function(i, obj) {
								option += '<option value="' + obj.ProduId + '#' + obj.ProduCustoFinal + '">' + obj.ProduNome + '</option>';
							});

							$('#cmbProduto').html(option).show();
						} else {
							ResetProduto();
						}
					}).fail(function(m) {

					});
				}
			});

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
				var inputProdutoServico = $('input[name="inputProdutoServico"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				var cmbProduto = $('#cmbProduto').val();
				var Item = cmbProduto.split("#");
				var inputQuantidade = $('#inputQuantidade').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				var inputTotal = $('#inputTotal').val();
				var inputLote = $('#inputLote').val();
				var inputValidade = $('#inputValidade').val();
				var cmbClassificacao = $('#cmbClassificacao').val();
				var inputIdProdutos = $('#inputIdProdutos').val(); //esse aqui guarda todos os IDs de produtos que estão na grid para serem movimentados
				var inputIdServicos = $('#inputIdServicos').val(); //esse aqui guarda todos os IDs de serviços que estão na grid para serem movimentados
				console.log('Id_Produtos: '+inputIdProdutos);
				console.log('Id_Servicos: '+inputIdServicos);
				console.log('Item: '+Item[0]);

				//remove os espaços desnecessários antes e depois
				inputQuantidade = inputQuantidade.trim();

				//Verifica se o campo só possui espaços em branco
				if (inputValorUnitario == '') {
					alerta('Atenção', 'Nenhum item foi selecionado!', 'error');
					$('#cmbProduto').focus();
					return false;
				}

				//Verifica se o campo só possui espaços em branco
				if (inputQuantidade == '') {
					alerta('Atenção', 'Informe a quantidade antes de adicionar!', 'error');
					$('#inputQuantidade').focus();
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

				//Verifica se o ID do Item tem 1 caracter, se sim adiciona um zero antes, para o 2, por exemplo, não aparecer no 20
				//if(Item[0].length == 1) Item[0] = "0"+Item[0];

				//Verifica se o campo já está no array
				if (inputProdutoServico == 'P'){

					if (cmbProduto == '#' || cmbProduto == ''){
						alerta('Atenção', 'Informe o produto!', 'error');						
						$('#cmbProduto').focus();
						return false;
					}

					if (inputIdProdutos.includes(Item[0])) {
						alerta('Atenção', 'Esse produto já foi adicionado!', 'error');						
						$('#cmbProduto').focus();
						return false;
					}
				} else{

					if (cmbProduto == '#' || cmbProduto == ''){
						alerta('Atenção', 'Informe o serviço!', 'error');						
						$('#cmbProduto').focus();
						return false;
					}

					if (inputIdServicos.includes(Item[0])) {
						alerta('Atenção', 'Esse serviço já foi adicionado!', 'error');
						$('#cmbProduto').focus();
						return false;
					}
				}

				var resNumItens = parseInt(inputNumItens) + 1;
				var total = parseInt(inputQuantidade) * parseFloat(inputValorUnitario.replace(',', '.'));

				total = total + parseFloat(inputTotal);
				var totalFormatado = "R$ " + float2moeda(total).toString();

				if (inputProdutoServico == 'P') {
					//Esse ajax está sendo usado para verificar no banco se o registro já existe
					let origem = $('#cmbEstoqueOrigem').val()
					$.ajax({
						type: "POST",
						url: "movimentacaoAddProduto.php",
						data: {
							tipo: inputTipo,
							numItens: resNumItens,
							idProduto: Item[0],
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

								$('#inputProdutos').append('<input type="hidden" class="inputProdutoServicoClasse" id="campo' + resNumItens + '" name="campo' + resNumItens + '" value="' + 'P#' + Item[0] + '#' + inputValorUnitario + '#' + inputQuantidade + '#' + 'SaldoValNull' + '#' + inputLote + '#' + inputValidade + '#' + cmbClassificacao + '">');

								if (inputProdutoServico == 'P') {
									inputIdProdutos = inputIdProdutos + ', ' + Item[0];
									$('#inputIdProdutos').val(inputIdProdutos);
								} else{
									inputIdServicos = inputIdServicos + ', ' + Item[0];
									$('#inputIdServicos').val(inputIdServicos);
								}

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

				} else {
					//Esse ajax está sendo usado para verificar no banco se o registro já existe
					$.ajax({
						type: "POST",
						url: "movimentacaoAddServico.php",
						data: {
							tipo: inputTipo,
							numItens: resNumItens,
							idServico: Item[0],
							quantidade: inputQuantidade
						},
						success: function(resposta) {

							//var newRow = $("<tr>");
							if (resposta != 'SEMESTOQUE') {
								//newRow.append(resposta);	    
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

								$('#inputProdutos').append('<input type="hidden" class="inputProdutoServicoClasse" id="campo' + resNumItens + '" name="campo' + resNumItens + '" value="' + 'S#' + Item[0] + '#' + inputValorUnitario + '#' + inputQuantidade + '#' + 'SaldoValNull' + '#' + inputLote + '#' + inputValidade + '#' + cmbClassificacao + '">');

								inputIdServicos = inputIdServicos + ', ' + Item[0];

								$('#inputIdServicos').val(inputIdServicos);

								return false;
							} else {
								alerta('Atenção', 'Estoque indisponível!', 'error');
								return false;
							}

						}
					})
				}
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

				var inputProdutoServico = $('input[name="inputProdutoServico"]:checked').val();
				var inputTotal = $('#inputTotal').val();
				var button_id = $(this).attr("id");
				var Produto = button_id.split("#");
				var inputIdProdutos = $('#inputIdProdutos').val(); //array com o Id dos produtos adicionados
				var inputIdServicos = $('#inputIdServicos').val(); //array com o Id dos servicos adicionados
				var inputNumItens = $('#inputNumItens').val();

				var item = '';

				if(inputProdutoServico == 'P'){
					item = inputIdProdutos.split(",");
				} else{
					item = inputIdServicos.split(",");
				}				

				var i;
				var arr = [];

				for (i = 0; i < item.length; i++) {
					arr.push(item[i]);
				}

				var index = arr.indexOf(Produto[0]);

				arr.splice(index, 1);

				if (inputProdutoServico == 'P'){
					$('#inputIdProdutos').val(arr);
				} else{
					$('#inputIdServicos').val(arr);
				}				

				$("#row" + Produto[0] + "").remove(); //remove a linha da tabela
				$("#campo" + Produto[0] + "").remove(); //remove o campo hidden

				//Agora falta calcular o valor total novamente
				inputTotal = parseFloat(inputTotal) - parseFloat(Produto[1]);
				var totalFormatado = "R$ " + float2moeda(inputTotal).toString();

				$('#inputTotal').val(inputTotal);
				$('#total').text(totalFormatado);

				var resNumItens = parseInt(inputNumItens) - 1;
				$('#inputNumItens').val(resNumItens);

			})

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputTotal = $('#inputTotal').val();
				var cmbEstoqueOrigem = $('#cmbEstoqueOrigem').val();
				var cmbDestinoSetor = $('#cmbDestinoSetor').val();
				var inputValorTotal = $('#inputValorTotal').val();

				//Verifica se a combo Estoque de Origem foi informada
				if (cmbEstoqueOrigem == '') {
					alerta('Atenção', 'Informe o Estoque de Origem!', 'error');
					$('#cmbEstoqueOrigem').focus();
					$("#formMovimentacao").submit();
					return false;
				}

				//Verifica se a combo Estoque de Destino foi informada
				if (cmbDestinoSetor == '') {
					alerta('Atenção', 'Informe o Estoque de Destino!', 'error');
					$('#cmbDestinoSetor').focus();
					$("#formMovimentacao").submit();
					return false;
				}

				//Verifica se tem algum produto na Grid
				if (inputTotal == '' || inputTotal == 0) {
					alerta('Atenção', 'Informe algum produto ou serviço!', 'error');
					$('#cmbCategoria').focus();
					return false;
				}

				if ($('input[name="inputTipo"]:checked').attr('saidaSolicitacao')) {
					alert('Saida Solicitação!');
					const submitProduto = {}

					$('.inputProdutoServicoClasse').each((i, elem) => {
						let nomeInput = $(elem).attr('name')
						let valorInput = $(elem).val()
						submitProduto[`${nomeInput}`] = valorInput
					})

					document.getElementById('classificacao').style.display = "block";
					document.getElementById('dadosProduto').style.display = "flex";

					submitProduto.inputData = document.querySelector('#inputData').value;
					submitProduto.cmbEstoqueOrigem = $('#cmbEstoqueOrigem').val()
					submitProduto.cmbDestinoSetor = $('#cmbDestinoSetor').val()
					submitProduto.txtareaObservacao = $('#txtareaObservacao').val()
					submitProduto.cmbSituacao = $('#cmbSituacao').val()
					submitProduto.inputTipo = 'S'
					submitProduto.inputValorTotal = $('#inputValorTotal').val()
					submitProduto.inputNumItens = $('#inputNumItens').val()

					let contSelectClass = $('.selectClassific').length
					let contSelectClassVal = 0

					$('.selectClassific').each((i, elem) => {
						let valor = $(elem).val()

						if (valor != '#') {
							contSelectClassVal++
						}
					})

					if (contSelectClass == contSelectClassVal) {
						$.ajax({
							type: "POST",
							url: "movimentacaoNovoSaida.php",
							data: submitProduto,
							success: function(resposta) {
								//window.location.href = "index.php";
								console.log(resposta);
							}
						})
					} else {
						alerta('Atenção', 'Informe a classificação dos produtos incluidos!', 'error');
						return false;
					}

				} else {
					document.querySelector("#formMovimentacao").submit();
				}
			});
		});

		function selecionaProdutoServico(tipo) {
			if (tipo == 'P') {
				document.getElementById('formLote').style.display = "block";
				document.getElementById('formValidade').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				$('#tituloProdutoServico').html('Dados dos Produtos');
				$('#labelProdutoServico').html('Produto');
			} else {
				document.getElementById('formLote').style.display = "none";
				document.getElementById('formValidade').style.display = "none";
				document.getElementById('classificacao').style.display = "none";
				$('#tituloProdutoServico').html('Dados dos Serviços');
				$('#labelProdutoServico').html('Serviço');
			}

			$('#cmbEstoqueOrigem').trigger("change"); //aciona o OnChange do cmbEstoqueOrigem, esse método selecionaProdutoServico não pode ficar dentro do $(document).ready(function() { se não esse gatilho não é acionado.
		};

		//Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
		function Filtrando() {
			$('#cmbSubCategoria').empty().append('<option value="#">Filtrando...</option>');
			FiltraProduto();
		}

		//Mostra o "Filtrando..." na combo Produto
		function FiltraCategoria() {
			$('#cmbCategoria').empty().append('<option value="#">Filtrando...</option>');
		}

		//Mostra o "Filtrando..." na combo Produto
		function FiltraProduto() {
			$('#cmbProduto').empty().append('<option value="#">Filtrando...</option>');
		}

		function ResetCategoria() {
			$('#cmbCategoria').empty().append('<option value="#">Sem Categoria</option>');
		}

		function ResetSubCategoria() {
			$('#cmbSubCategoria').empty().append('<option value="#">Sem Subcategoria</option>');
		}

		function ResetProduto() {
			$('#cmbProduto').empty().append('<option value="#">Sem produto</option>');
		}

		function ResetServico() {
			$('#cmbProduto').empty().append('<option value="#">Sem serviço</option>');
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
			});
		};

		$('.selectClassific').each((i, elem) => {
			$(elem).on('change', function(e) {
				let valor = $(elem).val();
				let idSelect = $(elem).attr('id');
				classBemSaidaSolicit(valor, idSelect);
			})
		});

		function mudaTotalTitulo() {
			$('#totalTitulo').html('Total (R$):');
			$('#quantEditaEntradaSaida').html('Quantidade');
		};

		Array.prototype.remove = function(start, end) {
			this.splice(start, end);
			return this;
		};

		Array.prototype.insert = function(pos, item) {
			this.splice(pos, 0, item);
			return this;
		};

		function selecionaTipo(tipo) {
			if (tipo == 'E') {
				window.location.href = "movimentacaoNovoEntrada.php";
			} else if (tipo == 'T') {
				window.location.href = "movimentacaoNovoTransferencia.php";
			} else
				window.location.href = "movimentacaoNovoSaida.php";
		};


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

			for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
				num = num.substring(0, num.length - (4 * i + 3)) + '.' +
					num.substring(num.length - (4 * i + 3));
			}
			ret = num + ',' + cents;

			if (x == 1) ret = ' - ' + ret;
			return ret;
		};
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
											<input type="radio" name="inputTipo" value="S" class="form-input-styled" checked data-fouc>
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

					<form name="formMovimentacao" id="formMovimentacao" method="POST" class="form-validate-jquery" action="movimentacaoNovoSaida.php">
						<div class="card-body">

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Dados da Saída</h5>
									<br>

									<div class="row">
										<div class="col-lg-12">
											<div class="row">
												<div class="col-lg-2">
													<div class="form-group">
														<label for="inputData">Data<span style="color: red">*</span></label>
														<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
													</div>
												</div>

												<div class="col-lg-4" id="EstoqueOrigem">
													<div class="form-group">
														<label for="cmbEstoqueOrigem">Origem<span style="color: red">*</span></label>
														<select id="cmbEstoqueOrigem" name="cmbEstoqueOrigem" class="form-control form-control-select2" required>
															<option value="">Selecione</option>
															<?php

															$sql = "SELECT UsXUnLocalEstoque, SetorNome
																	FROM EmpresaXUsuarioXPerfil
																	JOIN UsuarioXUnidade on UsXUnEmpresaUsuarioPerfil = EXUXPId
																	JOIN Setor ON SetorId = UsXUnSetor
																	WHERE EXUXPUsuario = " . $_SESSION['UsuarId'] . " and UsXUnUnidade = " . $_SESSION['UnidadeId'] . "
																	";
															$result = $conn->query($sql);
															$usuarioPerfil = $result->fetch(PDO::FETCH_ASSOC);

															$sql = "SELECT LcEstId, LcEstNome
																		FROM LocalEstoque
																		JOIN Situacao ON SituaId = LcEstStatus
																		WHERE LcEstUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		ORDER BY LcEstNome ASC";
															$result = $conn->query($sql);
															$row = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($row as $item) {
																if ($item['LcEstId'] == $usuarioPerfil['UsXUnLocalEstoque']) {
																	print('<option value="' . $item['LcEstId'] . '" selected>' . $item['LcEstNome'] . '</option>');
																} else {
																	print('<option value="' . $item['LcEstId'] . '" "' . $usuarioPerfil['UsXUnLocalEstoque'] . '">' . $item['LcEstNome'] . '</option>');
																}
															}

															?>
														</select>
													</div>
												</div>

												<div class="col-lg-4" id="DestinoSetor">
													<div class="form-group">
														<label for="cmbDestinoSetor">Destino<span style="color: red">*</span></label>
														<select id="cmbDestinoSetor" name="cmbDestinoSetor" class="form-control form-control-select2" <?php if (isset($_POST['inputSolicitacaoId'])) echo 'disabled' ?> required>
															<option value="">Selecione</option>
															<?php

															if (isset($_POST['inputSolicitacaoId'])) {
																$sql = "SELECT SetorId, SetorNome
																		FROM Setor 
																		JOIN UsuarioXUnidade on UsXUnSetor = SetorId
																		JOIN EmpresaXUsuarioXPerfil on EXUXPId = UsXUnEmpresaUsuarioPerfil						
																		JOIN Solicitacao ON SolicSolicitante = EXUXPUsuario
																		WHERE SolicId = " . $_POST['inputSolicitacaoId'] . " and UsXUnUnidade = " . $_SESSION['UnidadeId'] . "
																		";
																$result = $conn->query($sql);
																$usuarioPerfil = $result->fetch(PDO::FETCH_ASSOC);

																print('<option value="' . $usuarioPerfil['SetorId'] . '" selected>' . $usuarioPerfil['SetorNome'] . '</option>');

															} else {
																$sql = "SELECT SetorId, SetorNome
																		FROM Setor
																		JOIN Situacao on SituaId = SetorStatus
																		WHERE SetorUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																		ORDER BY SetorNome ASC";
																$result = $conn->query($sql);
																$row = $result->fetchAll(PDO::FETCH_ASSOC);

																foreach ($row as $item) {
																	print('<option value="' . $item['SetorId'] . '">' . $item['SetorNome'] . '</option>');
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
												<label for="txtareaObservacao">Observação</label>
												<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação" maxlength="4000"></textarea>
											</div>
										</div>
									</div>
									<br>
									<br>

									<div class="row" id="dadosProduto">
										<div class="col-lg-12">
											<div id="radiosProdutoServico" class="col-lg-4 px-0">
												<div class="form-group">
													<div class="form-check form-check-inline">
														<label class="form-check-label">
															<input type="radio" name="inputProdutoServico" value="P" class="form-input-styled" onclick="selecionaProdutoServico('P')" checked data-fouc>
															Produto
														</label>
													</div>
													<div class="form-check form-check-inline">
														<label class="form-check-label">
															<input type="radio" name="inputProdutoServico" value="S" class="form-input-styled" onclick="selecionaProdutoServico('S')" data-fouc>
															Serviço
														</label>
													</div>
												</div>
											</div>
											<h5 class="mb-0 font-weight-semibold" id="tituloProdutoServico">Dados dos Produtos</h5>
											<br>
											<div class="row" id="Patrimonio" style="display: none">
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbPatrimonio">Patrimônio</label>
														<select id="cmbPatrimonio" name="cmbPatrimonio" class="form-control form-control-select2">
															<option value="#">Informe a origem</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbCategoria">Categoria</label>
														<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
															<option value="#">Selecione</option>
														</select>
													</div>
												</div>

												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbSubCategoria">SubCategoria</label>
														<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
															<option value="#">Selecione</option>
														</select>
													</div>
												</div>

												<div class="col-lg-4">
													<div class="form-group">
														<label for="cmbProduto"><span id="labelProdutoServico">Produto</span></label>
														<select id="cmbProduto" name="cmbProduto" class="form-control form-control-select2">
															<option value="#">Selecione</option>
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

												<div class="col-lg-2" id="formValidade">
													<div class="form-group">
														<label for="inputValidade">Validade</label>
														<input type="date" id="inputValidade" name="inputValidade" class="form-control">
													</div>
												</div>

												<div class="col-lg-2" id="classificacao" style="display:none;">
													<div class="form-group">
														<label for="cmbClassificacao">Classificação/Bens</label>
														<select id="cmbClassificacao" name="cmbClassificacao" class="form-control form-control-select2">
															<option value="#">Selecione</option>
															<?php
															$sql = "SELECT ClassId, ClassNome
																		FROM Classificacao
																		JOIN Situacao on SituaId = ClassStatus
																		WHERE SituaChave = 'ATIVO'
																		ORDER BY ClassNome ASC";
															$result = $conn->query($sql);
															$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);

															foreach ($rowClassificacao as $item) {
																print('<option value="' . $item['ClassId'] . '">' . $item['ClassNome'] . '</option>');
															}
															?>
														</select>
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

									<div id="inputProdutos">
										
									</div>

									<?php
										if (isset($_POST['inputSolicitacaoId'])) {
											print('<input type="hidden" id="inputNumItens" name="inputNumItens" value="' . $numProdutos . '">');
										} else {
											print('<input type="hidden" id="inputNumItens" name="inputNumItens" value="0">');
										}
										?>
										<input type="hidden" id="itemEditadoquantidade" name="itemEditadoquantidade" value="0">
										<?php
										if (isset($_POST['inputSolicitacaoId'])) {
											print('<input type="hidden" id="inputIdProdutos" name="inputIdProdutos" value="' . $idsProdutos . '">');
											print('<input type="hidden" id="inputIdServicos" name="inputIdServicos" value="' . $idsProdutos . '">');
										} else {
											print('<input type="hidden" id="inputIdProdutos" name="inputIdProdutos" value="0">');
											print('<input type="hidden" id="inputIdServicos" name="inputIdServicos" value="0">');
										}
										?>
										<input type="hidden" id="inputProdutosRemovidos" name="inputProdutosRemovidos" value="0">
										<?php
										if (isset($_POST['inputSolicitacaoId'])) {
											$totalGeral = 0;

											foreach ($produtosSolicitacao  as $produto) {
												$totalGeral += $produto['SlXPrQuantidade'] * $produto['ProduValorVenda'];
											}

											print('<input type="hidden" id="inputTotal" name="inputTotal" value="' . $totalGeral . '">');
										} else {
											print('<input type="hidden" id="inputTotal" name="inputTotal" value="0">');
										}
										?>

									<div class="row">
										<div class="col-lg-12">
											<?php
											print('<table class="table" id="tabelaProdutoServicoSaida">');
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
														<tr class="bg-slate" id="trSaida" style="width: 100%">
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
												if (isset($_POST['inputSolicitacaoId'])) {
													$idProdutoSolicitacao = 0;
													$totalGeral = 0;

													print('<tr style="display:none;">
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
												} else {
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
												}
												?>

												<?php
												if (isset($_POST['inputSolicitacaoId'])) {

													$idProdutoSolicitacao = 0;
													$totalGeral = 0;

													foreach ($produtosSolicitacao  as $produto) {

														$idProdutoSolicitacao++;

														$valorCusto = formataMoeda($produto['ProduValorVenda']);
														$valorTotal = formataMoeda($produto['SlXPrQuantidade'] * $produto['ProduValorVenda']);
														$valorTotalSemFormatacao = $produto['SlXPrQuantidade'] * $produto['ProduValorVenda'];

														$totalGeral += $produto['SlXPrQuantidade'] * $produto['ProduValorVenda'];

														$linha = '';

														$linha .= "
																<tr class='produtoSolicitacao trGrid' id='row" . $idProdutoSolicitacao . "' idProduSolicitacao='" . $produto['ProduId'] . "'>
																		<td>" . $idProdutoSolicitacao . "</td>
																		<td>" . $produto['ProduNome'] . "</td>
																		<td style='text-align:center'>" . $produto['UnMedNome'] . "</td>
																		<td style='text-align:center'>" . $produto['SlXPrQuantidade'] . "</td>
																		<td valorUntPrSolici='" . $produto['ProduValorVenda'] . "' style='text-align:right'>" . $valorCusto . "</td>
																		<td style='text-align:right'>" . $valorTotal . "</td>
																
															";

														$linha .= '
																	<td style="text-align:center">
																		<div class="d-flex flex-row ">
																			<select id="' . $idProdutoSolicitacao . '" name="cmbClassificacao" class="form-control form-control-select2 selectClassific">
																				<option value="#">Selecione</option>
															';

														$sql = "SELECT ClassId, ClassNome
																	FROM Classificacao
																	JOIN Situacao on SituaId = ClassStatus
																	WHERE SituaChave = 'ATIVO'
																	ORDER BY ClassNome ASC";
														$result = $conn->query($sql);
														$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);

														foreach ($rowClassificacao as $item) {
															$linha .= '<option value="' . $item['ClassId'] . '">' . $item['ClassNome'] . '</option>';
														}

														$linha .= "
																			</select>
																		</div>
																	</td>
																	<td style='text-align:center;'><span name='remove' id='" . $idProdutoSolicitacao . "#" . $valorTotalSemFormatacao . "' class='btn btn_remove'>X</span></td>
																</tr>
															";

														print($linha);
													}
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th id="totalTitulo" colspan="5" style="text-align:right; font-size: 16px; font-weight:bold;">Total (R$): </th>
													<?php
													if (isset($_POST['inputSolicitacaoId'])) {
														print('
																<th colspan="1">
																	<div id="total" style="text-align:right; font-size: 15px; font-weight:bold;">' . formataMoeda($totalGeral) . '</div>
																</th>
																<th colspan="1">
																</th>
															');
													} else {
														print('
																<th colspan="1">
																	<div id="total" style="text-align:right; font-size: 15px; font-weight:bold;">R$ 0,00</div>
																</th>
																<th colspan="2">
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

														$sql = "SELECT SituaId, SituaNome, SituaChave
															FROM Situacao
															WHERE SituaStatus = '1'
															ORDER BY SituaNome ASC";
														$result = $conn->query($sql);
														$row = $result->fetchAll(PDO::FETCH_ASSOC);

														print('<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" readOnly>');

														foreach ($row as $item) {
															if ($item['SituaChave'] == 'LIBERADO') {
																print('<option value="' . $item['SituaId'] . '">' . $item['SituaNome'] . '</option>');
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
							</div>
						</div>
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