<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Nova Licenca';

include('global_assets/php/conexao.php');

if (isset($_POST['inputDataInicio'])) {

	$sql = "SELECT MAX(LicenId)
	        FROM Licenca 
	        JOIN Situacao on SituaId = LicenStatus 
	        WHERE LicenEmpresa = " . $_SESSION['EmpresaId'] . "
        ";
	$result = $conn->query($sql);
	$rowMAXId = $result->fetch(PDO::FETCH_ASSOC);

	try {

		if ($rowMAXId['']) {
			$sql = "SELECT LicenDtFim, SituaId
		        FROM Licenca 
				JOIN Situacao on SituaId = LicenStatus 
				WHERE LicenId = " . $rowMAXId[''] . " and LicenEmpresa = " . $_SESSION['EmpresaId'] . "
			   ";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);

			if (strtotime($_POST['inputDataInicio']) > strtotime($row['LicenDtFim'])) {
				$sql = "INSERT INTO Licenca (LicenEmpresa, LicenDtInicio, LicenDtFim, LicenLimiteUsuarios, LicenStatus, LicenUsuarioAtualizador)
				VALUES (:iEmpresa, :dDtInicio, :dDtFim, :iLimiteUsuarios, :bStatus, :iUsuarioAtualizador)";
				$result = $conn->prepare($sql);

				$result->execute(array(
					':iEmpresa' => $_SESSION['EmpresaId'],
					':dDtInicio' => $_POST['inputDataInicio'],
					':dDtFim' => $_POST['inputDataFim'],
					':iLimiteUsuarios' => $_POST['inputLimiteUsuarios'],
					':bStatus' => $row['SituaId'],
					':iUsuarioAtualizador' => $_SESSION['UsuarId']
				));

				$_SESSION['msg']['titulo'] = "Sucesso";
				$_SESSION['msg']['mensagem'] = "Licença incluída!!!";
				$_SESSION['msg']['tipo'] = "success";
			} else {
				$_SESSION['msg']['titulo'] = "Erro";
				$_SESSION['msg']['mensagem'] = "Erro ao incluir Licença!!! A data inicial da nova licença deve ser superior a data fim da última licença.";
				$_SESSION['msg']['tipo'] = "error";
			}
		} else {

			$sql = "SELECT LicenDtFim, SituaId
				    FROM Situacao on SituaChave = 'ATIVO' 
				    WHERE LicenId = " . $rowMAXId[''] . " and LicenEmpresa = " . $_SESSION['EmpresaId'] . "
			   ";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "INSERT INTO Licenca (LicenEmpresa, LicenDtInicio, LicenDtFim, LicenLimiteUsuarios, LicenStatus, LicenUsuarioAtualizador)
				VALUES (:iEmpresa, :dDtInicio, :dDtFim, :iLimiteUsuarios, :bStatus, :iUsuarioAtualizador)";
			$result = $conn->prepare($sql);

			$result->execute(array(
				':iEmpresa' => $_SESSION['EmpresaId'],
				':dDtInicio' => $_POST['inputDataInicio'],
				':dDtFim' => $_POST['inputDataFim'],
				':iLimiteUsuarios' => $_POST['inputLimiteUsuarios'],
				':bStatus' => $row['SituaId'],
				':iUsuarioAtualizador' => $_SESSION['UsuarId']
			));

			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "Licença incluída!!!";
			$_SESSION['msg']['tipo'] = "success";
		}
	} catch (PDOException $e) {

		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Licença!!!";
		$_SESSION['msg']['tipo'] = "error";

		echo 'Error: ' . $e->getMessage();
	}

	irpara("licenca.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Licença</title>

	<?php include_once("head.php"); ?>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->

	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script> <!-- CV Documentacao: https://jqueryvalidation.org/ -->

	<!-- Adicionando Javascript -->
	<script type="text/javascript">
		$(document).ready(function() {

			//Garantindo que ninguém mude a empresa na tela de inclusão
			$('#cmbEmpresa').prop("disabled", true);

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e) {

				e.preventDefault();

				var inputDataInicio = $('#inputDataInicio').val();
				var inputDataFim = $('#inputDataFim').val();

				if (inputDataFim < inputDataInicio) {
					alerta('Atenção', 'A Data Fim deve ser maior que a Data Início!', 'error');
					$('#inputDataFim').focus();
					return false;
				}

				//Aqui falta verificar se a licença com data maior e ativa é menor que a data início (TEM QUE SER)

				$('#cmbEmpresa').prop("disabled", false);

				$("#formLicenca").submit();

			});

			$('#cancelar').on('click', function(e) {

				$('#cmbEmpresa').prop("disabled", false);
				$(window.document.location).attr('href', "licenca.php");
			});

		});
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>

	<!-- Page content -->
	<div class="page-content">

		<?php include_once("menu-left.php"); ?>

		<?php include_once("menuLeftSecundario.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->
				<div class="card">

					<form name="formLicenca" id="formLicenca" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Licença</h5>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-lg-4">
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

								<div class="col-lg-4">
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

								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputLimiteUsuarios">Limite de Usuários</label>
										<input type="text" id="inputLimiteUsuarios" name="inputLimiteUsuarios" class="form-control" placeholder="Limite de Usuários">
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="licenca.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
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