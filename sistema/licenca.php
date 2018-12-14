<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Licença';

include('global_assets/php/conexao.php');

if (isset($_POST['inputEmpresaId'])){
	$_SESSION['EmpresaId'] = $_POST['inputEmpresaId'];
	$_SESSION['EmpresaNome'] = $_POST['inputEmpresaNome'];
} else if (!isset($_SESSION['EmpresaId'])) {
	irpara("empresa.php");
}

$sql = ("SELECT LicenId, LicenDtInicio, LicenDtFim, LicenLimiteUsuarios, LicenStatus, EmpreNomeFantasia
		 FROM Licenca
		 JOIN Empresa on EmpreId = LicenEmpresa
		 WHERE EmpreId = ".$_SESSION['EmpresaId']."
		 ORDER BY LicenDtInicio DESC"); 
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Licenças</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>		
	<!-- /theme JS files -->	
	
	<script>
		
		function atualizaLicenca(LicenId, LicenStatus, Tipo){

			document.getElementById('inputLicencaId').value = LicenId;
			document.getElementById('inputLicencaStatus').value = LicenStatus;
				
			if (Tipo == 'edita'){	
				document.formLicenca.action = "licencaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formLicenca, "Tem certeza que deseja excluir essa licença?", "licencaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formLicenca.action = "licencaMudaSituacao.php";
			}
			
			document.formLicenca.submit();
		}
			
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
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h5 class="card-title">Relação das Licenças</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a href="empresa.php" class="icon-backward2"> Voltar</a>
										<!--<a class="list-icons-item" data-action="collapse"></a>-->
										<!--<a href="empresa.php" class="list-icons-item" data-action="reload"></a>-->
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								As licenças abaixo são da empresa <b><?php echo $_SESSION['EmpresaNome']; ?></b>.
								<div class="text-right"><a href="licencaNovo.php" class="btn btn-success" role="button">Nova Licença</a></div>
							</div>							

							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Data Início</th>
										<th>Data Fim</th>
										<th>Limite Usuários</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['LicenStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['LicenStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.mostradata($item['LicenDtInicio']).'</td>
											<td>'.mostradata($item['LicenDtFim']).'</td>
											<td>'.$item['LicenLimiteUsuarios'].'</td>');
										
										print('<td><a href="#" onclick="atualizaLicenca('.$item['LicenId'].', '.$item['LicenStatus'].', \'mudaStatus\')"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
																				
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaLicenca('.$item['LicenId'].', '.$item['LicenStatus'].', \'edita\')" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaLicenca('.$item['LicenId'].', '.$item['LicenStatus'].', \'exclui\')" class="list-icons-item"><i class="icon-bin"></i></a>														
													</div>
												</div>
											</td>
										</tr>');
									}
								?>

								</tbody>
							</table>
						</div>
						<!-- /basic responsive configuration -->

					</div>
				</div>				
				
				<!-- /info blocks -->
				
				<form name="formLicenca" method="post" action="licencaEdita.php">
					<input type="hidden" id="inputLicencaId" name="inputLicencaId" >
					<input type="hidden" id="inputLicencaStatus" name="inputLicencaStatus" >
				</form>

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
