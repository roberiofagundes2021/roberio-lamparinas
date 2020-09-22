<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Aditivo';

include('global_assets/php/conexao.php');

$iFluxoOperacional = $_POST['inputFluxoId'];

if (isset($_POST['inputFluxoOperacionalId'])) {
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	$iCategoria = $_POST['inputFluxoOperacionalCategoria'];
	$iSubCategoria = $_POST['inputFluxoOperacionalSubCategoria'];
} else if (isset($_POST['inputIdFluxoOperacional'])) {
	$iFluxoOperacional = $_POST['inputIdFluxoOperacional'];
	$iCategoria = $_POST['inputIdCategoria'];
	$iSubCategoria = $_POST['inputIdSubCategoria'];
} else if (isset($_POST['inputFluxoId'])) {
	$iFluxoOperacional = $_POST['inputFluxoId'];
	$iCategoria = $_POST['inputIdCategoria'];
	$iSubCategoria = $_POST['inputIdSubCategoria'];
} else {
	irpara("fluxo.php");
}

$bFechado = 0;
$countProduto = 0;

$sql = "SELECT FlOpeValor, FlOpeStatus
		FROM FluxoOperacional
		Where FlOpeId = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowFluxo = $result->fetch(PDO::FETCH_ASSOC);
$TotalFluxo = $rowFluxo['FlOpeValor'];

$sql = "SELECT isnull(SUM(FOXPrQuantidade * FOXPrValorUnitario),0) as TotalProduto
		FROM FluxoOperacionalXProduto
		Where FOXPrUnidade = " . $_SESSION['UnidadeId'] . " and FOXPrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowProdutos = $result->fetch(PDO::FETCH_ASSOC);
$TotalProdutos = $rowProdutos['TotalProduto'];

$sql = "SELECT isnull(SUM(FOXSrQuantidade * FOXSrValorUnitario),0) as TotalServico
		FROM FluxoOperacionalXServico
		Where FOXSrUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
$result = $conn->query($sql);
$rowServicos = $result->fetch(PDO::FETCH_ASSOC);
$TotalServicos = $rowServicos['TotalServico'];

$TotalGeral = $TotalProdutos + $TotalServicos;

if ($TotalGeral == $TotalFluxo) {
	$bFechado = 1;
}

$sql = "SELECT Top 1 isnull(AditiNumero, 0) as Aditivo
        FROM Aditivo
		JOIN Situacao on SituaId = AditiStatus
        WHERE AditiFluxoOperacional = " . $iFluxoOperacional . " and SituaChave = 'ATIVO' 
        ORDER BY AditiNumero DESC
       ";
$result = $conn->query($sql);
$rowNumero = $result->fetch(PDO::FETCH_ASSOC);
$iProxAditivo = $rowNumero['Aditivo'] + 1;

$sql = "SELECT Top 1 FlOpeDataFim as ProxData
        FROM FluxoOperacional
        WHERE FlOpeId = " . $iFluxoOperacional . "
       ";
$result = $conn->query($sql);
$rowFluxo = $result->fetch(PDO::FETCH_ASSOC);
$iProxData = date('Y-m-d', strtotime("+1 day", strtotime($rowFluxo['ProxData'])));	//Adiciona 1 dia na data

if ($rowNumero['Aditivo'] > 0) {

	$sql = "SELECT Top 1 isnull(AditiDtFim, '1900-01-01') as ProxData
	        FROM Aditivo
			JOIN Situacao on SituaId = AditiStatus
	        WHERE AditiFluxoOperacional = " . $iFluxoOperacional . " and SituaChave = 'ATIVO' 
	        ORDER BY AditiDtFim DESC
	       ";
	$result = $conn->query($sql);
	$rowDataFim = $result->fetch(PDO::FETCH_ASSOC);

	if ($rowDataFim['ProxData'] != '1900-01-01') {
		$iProxData = date('Y-m-d', strtotime("+1 day", strtotime($rowDataFim['ProxData'])));	//Adiciona 1 dia na data
	}
}

if (isset($_POST['inputDataInicio'])) {

	$sql = "SELECT SituaId
		         FROM Situacao
		         Where SituaChave = 'ATIVO' ";
	$result = $conn->query($sql);
	$rowAditSitua = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT FlOpeStatus
		         FROM FluxoOperacional
		         Where FlOpeId = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$rowFluxoStatus = $result->fetch(PDO::FETCH_ASSOC);

	try {

		$conn->beginTransaction();

		$sql = "INSERT INTO Aditivo (AditiFluxoOperacional, AditiNumero, AditiDtCelebracao, AditiDtInicio, AditiDtFim, 
									 AditiValor, AditiStatusFluxo, AditiStatus, AditiUsuarioAtualizador, AditiUnidade)
				VALUES (:iFluxo, :iNumero, :dDataCelebracao, :dDataInicio, :dDataFim, 
						:fValor, :iStatusFluxo, :iStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':iFluxo' => $iFluxoOperacional,
			':iNumero' => $iProxAditivo,
			':dDataCelebracao' => $_POST['inputDataCelebracao'] == '' ? null : gravaData($_POST['inputDataCelebracao']),
			':dDataInicio' => $_POST['inputDataInicio'] == '' ? null : $_POST['inputDataInicio'],
			':dDataFim' => $_POST['inputDataFim'] == '' ? null : $_POST['inputDataFim'],
			':fValor' => $_POST['inputValor'] == '' ? null : gravaValor($_POST['inputValor']),
			':iStatusFluxo' => $rowFluxoStatus['FlOpeStatus'],
			':iStatus' => $rowAditSitua['SituaId'],
			':iUsuarioAtualizador' => $_SESSION['UsuarId'],
			':iUnidade' => $_SESSION['UnidadeId']
		));

		$_SESSION['AditivoNovo'] = $conn->lastInsertId();

		$conn->commit();

		if ($_POST['inputValor'] == '') {

			$sql = "SELECT SituaId
		            FROM Situacao
		            WHERE SituaChave = 'AGUARDANDOLIBERACAO'";
			$result = $conn->query($sql);
			$rowStatus = $result->fetch(PDO::FETCH_ASSOC);
			$bStatus = $rowStatus['SituaId'];

			$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :bStatus
	                 WHERE FlOpeId = :id";
			$result = $conn->prepare($sql);
			$result->bindParam(':bStatus', $bStatus);
			$result->bindParam(':id', $iFluxoOperacional);
			$result->execute();




			$sql = "SELECT SituaId, SituaNome, SituaChave
			FROM Situacao
			WHERE SituaStatus = 1 and SituaChave = 'AGUARDANDOLIBERACAO'";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT PerfiId
				FROM Perfil
				WHERE PerfiChave = 'CONTROLADORIA' 
				";
			$result = $conn->query($sql);
			$rowPerfil = $result->fetch(PDO::FETCH_ASSOC);

			/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
			$sIdentificacao = 'Fluxo Aditivo';

			$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandeSolicitante, 
				BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, BandeUnidade)
				VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iSolicitante, :sTabela, :iTabelaId, 
				:iStatus, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':sIdentificacao' => $sIdentificacao,
				':dData' => date("Y-m-d"),
				':sDescricao' => 'Liberar Fluxo Aditivo',
				':sURL' => '',
				':iSolicitante' => $_SESSION['UsuarId'],
				':sTabela' => 'Aditivo',
				':iTabelaId' => $_SESSION['AditivoNovo'],
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


			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "Aditivo realizado com sucesso!!!";
			$_SESSION['msg']['tipo'] = "success";

			unset($_SESSION['AditivoNovo']);

			irpara("fluxoAditivo.php");
		}
	} catch (PDOException $e) {

		$conn->rollback();

		echo 'Error: ' . $e->getMessage();
		die;
	}
}

if (isset($_POST['inputIdProduto1'])  || isset($_POST['inputIdServico1'])) {
	try {
		$conn->beginTransaction();



		$sql = "SELECT SituaId, SituaNome, SituaChave
		            FROM Situacao
		            WHERE SituaStatus = 1 and SituaChave = 'AGUARDANDOLIBERACAO'
		";
		$result = $conn->query($sql);
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

		$sql = "SELECT PerfiId
				        FROM Perfil
				        WHERE PerfiChave = 'CONTROLADORIA' 
				        ";
		$result = $conn->query($sql);
		$rowPerfil = $result->fetch(PDO::FETCH_ASSOC);

		/* Insere na Bandeja para Aprovação do perfil ADMINISTRADOR ou CONTROLADORIA */
		$sIdentificacao = 'Fluxo Aditivo';

		$sql = "INSERT INTO Bandeja (BandeIdentificacao, BandeData, BandeDescricao, BandeURL, BandeSolicitante, 
					    BandeTabela, BandeTabelaId, BandeStatus, BandeUsuarioAtualizador, BandeUnidade)
		                VALUES (:sIdentificacao, :dData, :sDescricao, :sURL, :iSolicitante, :sTabela, :iTabelaId, 
				        :iStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
			':sIdentificacao' => $sIdentificacao,
			':dData' => date("Y-m-d"),
			':sDescricao' => 'Liberar Fluxo Aditivo',
			':sURL' => '',
			':iSolicitante' => $_SESSION['UsuarId'],
			':sTabela' => 'Aditivo',
			':iTabelaId' => $_SESSION['AditivoNovo'],
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


		//Se está aincluindo Produtos
		if (isset($_POST['inputIdProduto1'])) {

			$sql = "DELETE FROM AditivoXProduto
				WHERE AdXPrAditivo = :iAditivo AND AdXPrUnidade = :iUnidade";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iAditivo' => $_SESSION['AditivoNovo'],
				':iUnidade' => $_SESSION['UnidadeId']
			));

			for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {

				$sql = "INSERT INTO AditivoXProduto (AdXPrAditivo, AdXPrProduto, AdXPrQuantidade, AdXPrValorUnitario, 
					AdXPrUsuarioAtualizador, AdXPrUnidade)
					VALUES (:iAditivo, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
				$result = $conn->prepare($sql);

				$result->execute(array(
					':iAditivo' => $_SESSION['AditivoNovo'],
					':iProduto' => $_POST['inputIdProduto' . $i],
					':iQuantidade' => $_POST['inputQuantidade' . $i] == '' ? null : $_POST['inputQuantidade' . $i],
					':fValorUnitario' => $_POST['inputValorUnitario' . $i] == '' ? null : gravaValor($_POST['inputValorUnitario' . $i]),
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iUnidade' => $_SESSION['UnidadeId']
				));
			}
		}

		//Se está aincluindo Serviços
		if (isset($_POST['inputIdServico1'])) {

			$sql = "DELETE FROM AditivoXServico
				WHERE AdXSrAditivo = :iAditivo AND AdXSrUnidade = :iUnidade";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iAditivo' => $_SESSION['AditivoNovo'],
				':iUnidade' => $_SESSION['UnidadeId']
			));

			for ($i = 1; $i <= $_POST['totalRegistrosServicos']; $i++) {

				$sql = "INSERT INTO AditivoXServico (AdXSrAditivo, AdXSrServico, AdXSrQuantidade, AdXSrValorUnitario, 
					AdXSrUsuarioAtualizador, AdXSrUnidade)
					VALUES (:iAditivo, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
				$result = $conn->prepare($sql);

				$result->execute(array(
					':iAditivo' => $_SESSION['AditivoNovo'],
					':iServico' => $_POST['inputIdServico' . $i],
					':iQuantidade' => $_POST['inputQuantidadeServico' . $i] == '' ? null : $_POST['inputQuantidadeServico' . $i],
					':fValorUnitario' => $_POST['inputValorUnitarioServico' . $i] == '' ? null : gravaValor($_POST['inputValorUnitarioServico' . $i]),
					':iUsuarioAtualizador' => $_SESSION['UsuarId'],
					':iUnidade' => $_SESSION['UnidadeId']
				));
			}
		}
		//// Mudando status do fluxo, após gravar produtos e serviços
		$sql = "SELECT SituaId
		            FROM Situacao
		            WHERE SituaChave = 'AGUARDANDOLIBERACAO'
		";
		$result = $conn->query($sql);
		$rowStatus = $result->fetch(PDO::FETCH_ASSOC);
		$bStatus = $rowStatus['SituaId'];

		$sql = "UPDATE FluxoOperacional SET FlOpeStatus = :bStatus
	                 WHERE FlOpeId = :id";
		$result = $conn->prepare($sql);
		$result->bindParam(':bStatus', $bStatus);
		$result->bindParam(':id', $iFluxoOperacional);
		$result->execute();

		$conn->commit();
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Aditivo realizado com sucesso!!!";
		$_SESSION['msg']['tipo'] = "success";

		unset($_SESSION['AditivoNovo']);

		irpara("fluxoAditivo.php");
	} catch (PDOException $e) {

		echo 'Error: ' . $e->getMessage();

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao realizar aditivo!!!";
		$_SESSION['msg']['tipo'] = "error";

		$conn->rollback();

		irpara("fluxoAditivo.php");
	}
}

// Após realizar todas os registros, redireciona para fluxoAditivo

try {

	$sql = "SELECT FlOpeId, FlOpeNumContrato, ForneId, ForneNome, ForneTelefone, ForneCelular, CategNome, FlOpeCategoria,
				   SbCatNome, FlOpeSubCategoria, FlOpeNumProcesso, FlOpeValor, FlOpeStatus, SituaNome
			FROM FluxoOperacional
			JOIN Fornecedor on ForneId = FlOpeFornecedor
			JOIN Categoria on CategId = FlOpeCategoria
			JOIN SubCategoria on SbCatId = FlOpeSubCategoria
			JOIN Situacao on SituaId = FlOpeStatus
			WHERE FlOpeUnidade = " . $_SESSION['UnidadeId'] . " and FlOpeId = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT AditiId
		FROM Aditivo
		Where AditiUnidade = " . $_SESSION['UnidadeId'] . " and AditiFluxoOperacional = " . $iFluxoOperacional;
	$result = $conn->query($sql);
	$rowAditivo = $result->fetch(PDO::FETCH_ASSOC);
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
	<title>Lamparinas | Aditivo - Fluxo Operacional</title>

	<?php include_once("head.php"); ?>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script> <!-- CV Documentacao: https://jqueryvalidation.org/ -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			$('#enviar1').on('click', function(e) {

				e.preventDefault();

				var inputDataInicio = $('#inputDataInicio').val();
				var inputDataFim = $('#inputDataFim').val();
				var inputValor = $('#inputValor').val().replace('.', '').replace(',', '.');

				if (inputDataInicio == '' && inputDataFim == '' && (inputValor == 0 || inputValor == '')) {
					alerta('Atenção', 'Informe as datas ou o valor do aditivo!', 'error');
					$('#inputDataInicio').focus();
					return false;
				}

				if (inputDataFim < inputDataInicio) {
					alerta('Atenção', 'A Data Fim deve ser maior que a Data Início!', 'error');
					$('#inputDataFim').focus();
					return false;
				}

				$("#formAditivo").submit();

			});

			$('#enviar2').on('click', function(e) {

				e.preventDefault();

				var inputValor = $('#inputValor').val().replace('.', '').replace(',', '.');
				var inputTotalGeral = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');
				var inputTotalGeralServico = $('#inputTotalGeralServico').val().replace('.', '').replace(',', '.');
				var somaTotais = parseFloat(inputTotalGeral) + parseFloat(inputTotalGeralServico);

				//Verifica se o valor ultrapassou o total do fluxo
				if (somaTotais > parseFloat(inputValor)) {
					alerta('Atenção', 'A soma dos totais ultrapassou o valor do aditivo!', 'error');
					return false;
				}

				//Verifica se o valor é menor que o total do fluxo
				if (somaTotais < parseFloat(inputValor)) {
					alerta('Atenção', 'A soma dos totais é menor que o valor do aditivo!', 'error');
					return false;
				}


				$("#formAditivo").submit();

			});

		});

		//Enviar para aprovação da Controladoria (via Bandeja)
		$('#enviarAprovacao').on('click', function(e) {

			e.preventDefault();

			confirmaExclusao(document.formFluxoOperacionalProduto, "Essa ação enviará todo o Fluxo Operacional (com seus produtos e serviços) para aprovação da Controladoria. Tem certeza que deseja enviar?", "fluxoEnviar.php");
		});

		function calculaValorTotal(id) {

			var ValorTotalAnterior = $('#inputValorTotal' + id + '').val() == '' ? 0 : $('#inputValorTotal' + id + '').val().replace('.', '').replace(',', '.');
			var TotalGeralAnterior = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');

			var Quantidade = $('#inputQuantidade' + id + '').val().trim() == '' ? 0 : $('#inputQuantidade' + id + '').val();
			var ValorUnitario = $('#inputValorUnitario' + id + '').val() == '' ? 0 : $('#inputValorUnitario' + id + '').val().replace('.', '').replace(',', '.');
			var ValorTotal = 0;

			var ValorTotal = parseFloat(Quantidade) * parseFloat(ValorUnitario);
			var TotalGeral = float2moeda(parseFloat(TotalGeralAnterior) - parseFloat(ValorTotalAnterior) + ValorTotal).toString();
			ValorTotal = float2moeda(ValorTotal).toString();

			$('#inputValorTotal' + id + '').val(ValorTotal);

			$('#inputTotalGeral').val(TotalGeral);
		}

		function calculaValorTotalServico(id) {

			var ValorTotalAnterior = $('#inputValorTotalServico' + id + '').val() == '' ? 0 : $('#inputValorTotalServico' + id + '').val().replace('.', '').replace(',', '.');
			var TotalGeralAnterior = $('#inputTotalGeralServico').val().replace('.', '').replace(',', '.');

			var Quantidade = $('#inputQuantidadeServico' + id + '').val().trim() == '' ? 0 : $('#inputQuantidadeServico' + id + '').val();
			var ValorUnitario = $('#inputValorUnitarioServico' + id + '').val() == '' ? 0 : $('#inputValorUnitarioServico' + id + '').val().replace('.', '').replace(',', '.');
			var ValorTotal = 0;

			var ValorTotal = parseFloat(Quantidade) * parseFloat(ValorUnitario);
			var TotalGeral = float2moeda(parseFloat(TotalGeralAnterior) - parseFloat(ValorTotalAnterior) + ValorTotal).toString();
			ValorTotal = float2moeda(ValorTotal).toString();

			$('#inputValorTotalServico' + id + '').val(ValorTotal);

			$('#inputTotalGeralServico').val(TotalGeral);
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////
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

					<form name="formAditivo" id="formAditivo" method="post">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Aditivo</h5>
						</div>

						<div class="card-body">

							<h5 class="mb-0 font-weight-semibold">Dados do(s) Aditivo(s)</h5>
							<br>

							<input type="hidden" id="inputFluxoId" name="inputFluxoId" class="form-control" value="<?php echo $iFluxoOperacional; ?>">
							<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $iCategoria; ?>">
							<input type="hidden" id="inputIdSubCategoria" name="inputIdSubCategoria" class="form-control" value="<?php echo $iSubCategoria; ?>">

							<div class="row">
								<div class="col-lg-1">
									<div class="form-group">
										<label for="inputNumero">Nº Aditivo <span class="text-danger">*</span></label>
										<input type="text" id="inputNumero" name="inputNumero" class="form-control" value="<?php echo $iProxAditivo; ?>" readOnly>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputDataCelebracao">Data Celebração <span class="text-danger">*</span></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="text" id="inputDataCelebracao" name="inputDataCelebracao" class="form-control" placeholder="Data Celebracao" value="<?php echo date('d/m/Y'); ?>" readOnly>
										</div>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputDataInicio">Data Início</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $iProxData; ?>" <?php isset($_POST['inputDataInicio']) ? print('disabled') : print('')  ?>>
										</div>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputDataFim">Data Fim</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php isset($_POST['inputDataInicio']) ? print($_POST['inputDataFim']) : print('')  ?>" <?php isset($_POST['inputDataInicio']) ? print('disabled') : print('')  ?> autofocus>
										</div>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputValor">Valor Total</label>
										<input type="text" id="inputValor" name="inputValor" class="form-control" placeholder="Valor Total" value="<?php isset($_POST['inputDataInicio']) ? print($_POST['inputValor']) : print('')  ?>" onKeyUp="moeda(this)" maxLength="12" <?php isset($_POST['inputDataInicio']) ? print('disabled') : print('')  ?>>
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 10px; display: <?php isset($_POST['inputDataInicio']) ? print('none') : print('block')   ?>">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar1">Incluir</button>
										<a href="fluxoAditivo.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
									</div>
								</div>
							</div>

							<!-- /card-body -->
							<!---------------------------------------------------------------------------------------------Produtos---------------------------------------------------------------------------------------------------------->
							<?php
							$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, MarcaNome
                                				FROM Produto
                                				JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
                                				LEFT JOIN Marca on MarcaId = ProduMarca
                                				JOIN Situacao on SituaId = ProduStatus
                                				WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and ProduCategoria = " . $iCategoria . " and 
                                				ProduSubCategoria = " . $iSubCategoria . " and SituaChave = 'ATIVO' ";
							$result = $conn->query($sql);
							$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
							$countProduto = count($rowProdutos);

							?>
							<div class="lista-produtos" style="display: <?php isset($_POST['inputDataInicio']) && $countProduto >= 1 ? print('block') : print('none')  ?>">
								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Produtos</h5>
								</div>

								<div class="card-body">

									<?php

									$cont = 0;
									if (isset($_POST['inputDataInicio'])) {
										print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-8">
												<div class="row">
													<div class="col-lg-1">
														<label for="inputCodigo"><strong>Item</strong></label>
													</div>
													<div class="col-lg-8">
														<label for="inputProduto"><strong>Produto</strong></label>
													</div>
													<div class="col-lg-3">
														<label for="inputMarca"><strong>Marca</strong></label>
													</div>
												</div>
											</div>												
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputUnidade"><strong>Unidade</strong></label>
												</div>
											</div>
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputQuantidade"><strong>Quantidade</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorUnitario" title="Valor Unitário"><strong>Valor Unit.</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorTotal"><strong>Valor Total</strong></label>
												</div>
											</div>											
										</div>');

										print('<div id="tabelaProdutos">');

										$fTotalGeral = 0;

										foreach ($rowProdutos as $item) {

											$cont++;

											$iQuantidade = isset($item['AdXPrQuantidade']) ? $item['AdXPrQuantidade'] : '';
											$fValorUnitario = isset($item['AdXPrValorUnitario']) ? mostraValor($item['AdXPrValorUnitario']) : '';
											$fValorTotal = (isset($item['AdXPrQuantidade']) and isset($item['AdXPrValorUnitario'])) ? mostraValor($item['AdXPrQuantidade'] * $item['AdXPrValorUnitario']) : '';

											$fTotalGeral += (isset($item['AdXPrQuantidade']) and isset($item['AdXPrValorUnitario'])) ? $item['AdXPrQuantidade'] * $item['AdXPrValorUnitario'] : 0;


											print('
											<div class="row" style="margin-top: 8px;" >
												<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															<input type="hidden" id="inputIdProduto' . $cont . '" name="inputIdProduto' . $cont . '" value="' . $item['ProduId'] . '" class="idProduto">
														</div>
														<div class="col-lg-8">
															<input type="text" id="inputProduto' . $cont . '" name="inputProduto' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ProduDetalhamento'] . '" value="' . $item['ProduNome'] . '" readOnly>
														</div>
														<div class="col-lg-3">
															<input type="text" id="inputMarca' . $cont . '" name="inputMarca' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['MarcaNome'] . '" value="' . $item['MarcaNome'] . '" readOnly>
														</div>
													</div>
												</div>								
												<div class="col-lg-1">
													<input type="text" id="inputUnidade' . $cont . '" name="inputUnidade' . $cont . '" class="form-control-border-off" value="' . $item['UnMedSigla'] . '" readOnly>
												</div>
												<div class="col-lg-1">
													<input type="text" id="inputQuantidade' . $cont . '" name="inputQuantidade' . $cont . '" class="form-control-border Quantidade" onChange="calculaValorTotal(' . $cont . ')" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorUnitario' . $cont . '" name="inputValorUnitario' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotal(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12" value="' . $fValorUnitario . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorTotal' . $cont . '" name="inputValorTotal' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												</div>											
											</div>');
										}
									}

									if (isset($_POST['inputDataInicio'])) {
										print('
										<div class="row" style="margin-top: 8px;">
											<div class="col-lg-8">
												<div class="row">
													<div class="col-lg-1">
														
													</div>
													<div class="col-lg-8">
														
													</div>
													<div class="col-lg-3">
														
													</div>
												</div>
											</div>								
											<div class="col-lg-1">
												
											</div>
											<div class="col-lg-1">
												
											</div>	
											<div class="col-lg-1" style="padding-top: 5px; text-align: right;">
												<h5><b>Total:</b></h5>
											</div>	
											<div class="col-lg-1">
												<input type="text" id="inputTotalGeral" name="inputTotalGeral" class="form-control-border-off" value="' . mostraValor($fTotalGeral) . '" readOnly>
											</div>											
										</div>');

										print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="' . $cont . '" >');
									}


									?>

								</div>
							</div>
							<!---------------------------------------------------------------------------------------------Serviços---------------------------------------------------------------------------------------------------------->
							<?php
							$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
							        			  FROM Servico
							        			  JOIN Situacao on SituaId = ServiStatus
							        			  WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and ServiCategoria = " . $iCategoria . " and 
							        			  ServiSubCategoria = " . $iSubCategoria . " and SituaChave = 'ATIVO' ";
							$result = $conn->query($sql);
							$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
							$countServico = count($rowServicos);
							?>
							<!------------------------------Se não existirem serviços ou se a requisição não estiver vindo do lugar certo-------------------------------------->
							<div class="lista-servicos" style="display: <?php isset($_POST['inputDataInicio']) && $countServico >= 1 ? print('block') : print('none')  ?>">
								<!-- Custom header text -->

								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Servicos</h5>
								</div>

								<div class="card-body">

									<?php

									$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrQuantidade, FOXSrValorUnitario, MarcaNome
												FROM Servico
												JOIN FluxoOperacionalXServico on FOXSrServico = ServiId
												LEFT JOIN Marca on MarcaId = ServiMarca
												WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and FOXSrFluxoOperacional = " . $iFluxoOperacional;
									$result = $conn->query($sql);
									$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
									$countServico = count($rowServicos);

									if (!$countServico) {
										$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
													FROM Servico
													JOIN Situacao on SituaId = ServiStatus
													WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and ServiCategoria = " . $iCategoria . " and 
													ServiSubCategoria = " . $iSubCategoria . " and SituaChave = 'ATIVO' ";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$countServico = count($rowServicos);
									}

									$cont = 0;
									if (isset($_POST['inputDataInicio'])) {

										print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-8">
															<label for="inputServico"><strong>Servico</strong></label>
														</div>
														<div class="col-lg-3">
															<label for="inputMarca"><strong>Marca</strong></label>
														</div>
													</div>
												</div>												
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputQuantidade"><strong>Quantidade</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorUnitario" title="Valor Unitário"><strong>Valor Unit.</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorTotal"><strong>Valor Total</strong></label>
												</div>
											</div>											
										</div>');

										print('<div id="tabelaServicos">');

										$fTotalGeral = 0;

										foreach ($rowServicos as $item) {

											$cont++;

											$iQuantidade = isset($item['FOXSrQuantidade']) ? $item['FOXSrQuantidade'] : '';
											$fValorUnitario = isset($item['FOXSrValorUnitario']) ? mostraValor($item['FOXSrValorUnitario']) : '';
											$fValorTotal = (isset($item['FOXSrQuantidade']) and isset($item['FOXSrValorUnitario'])) ? mostraValor($item['FOXSrQuantidade'] * $item['FOXSrValorUnitario']) : '';

											$fTotalGeral += (isset($item['FOXSrQuantidade']) and isset($item['FOXSrValorUnitario'])) ? $item['FOXSrQuantidade'] * $item['FOXSrValorUnitario'] : 0;


											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem' . $cont . '" name="inputItem' . $cont . '" class="form-control-border-off" value="' . $cont . '" readOnly>
															<input type="hidden" id="inputIdServico' . $cont . '" name="inputIdServico' . $cont . '" value="' . $item['ServiId'] . '" class="idServico">
														</div>
														<div class="col-lg-8">
															<input type="text" id="inputServico' . $cont . '" name="inputServico' . $cont . '" class="form-control-border-off" data-popup="tooltip" title="' . $item['ServiDetalhamento'] . '" value="' . $item['ServiNome'] . '" readOnly>
														</div>
														<div class="col-lg-3">
															<input type="text"   class="form-control-border-off" data-popup="tooltip"  readOnly>
														</div>
													</div>
												</div>
												<div class="col-lg-1">
													<input type="text" id="inputQuantidadeServico' . $cont . '" name="inputQuantidadeServico' . $cont . '" class="form-control-border Quantidade" onChange="calculaValorTotalServico(' . $cont . ')" onkeypress="return onlynumber();" value="' . $iQuantidade . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorUnitarioServico' . $cont . '" name="inputValorUnitarioServico' . $cont . '" class="form-control-border ValorUnitario" onChange="calculaValorTotalServico(' . $cont . ')" onKeyUp="moeda(this)" maxLength="12" value="' . $fValorUnitario . '">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorTotalServico' . $cont . '" name="inputValorTotalServico' . $cont . '" class="form-control-border-off" value="' . $fValorTotal . '" readOnly>
												</div>											
											</div>');
										}
									}

									if (isset($_POST['inputDataInicio'])) {
										print('
										<div class="row" style="margin-top: 8px;">
												<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															
														</div>
														<div class="col-lg-8">
															
														</div>
														<div class="col-lg-3">
															
														</div>
													</div>
												</div>
												<div class="col-lg-1">
													
												</div>	
												<div class="col-lg-1" style="padding-top: 5px; text-align: right;">
													<h5><b>Total:</b></h5>
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputTotalGeralServico" name="inputTotalGeralServico" class="form-control-border-off" value="' . mostraValor($fTotalGeral) . '" readOnly>
												</div>											
											</div>');

										print('<input type="hidden" id="totalRegistrosServicos" name="totalRegistrosServicos" value="' . $cont . '" >');

										print('</div>');
									}

									?>
								</div>
								<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
							</div>
							<div class="row" style="margin-top: 10px; display:<?php ($countProduto >= 1 || $countServico >= 1) &&  isset($_POST['inputDataInicio']) ? print('block') : print('none')  ?>">
								<div class="col-lg-6">
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar2" style="margin-right:5px;">Alterar</button>
										<a href="fluxoAditivo.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

			</div>
			<?php !isset($_POST['inputDataInicio']) ?  include_once("footer.php") : '' ?>
		</div>
		<!-- /info blocks -->
		<?php isset($_POST['inputDataInicio']) ?  include_once("footer.php") : '' ?>
	</div>
	<!-- /content area -->

	</div>
	<!-- /main content -->

	<!-- /page content -->

</body>

</html>