<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Empresa';

include('global_assets/php/conexao.php');

$sql = ("SELECT EmpreId, EmpreCnpj, EmpreRazaoSocial, EmpreNomeFantasia, EmpreStatus
		 FROM Empresa
		 LEFT JOIN Licenca on LicenEmpresa = EmpreId
		 ORDER BY EmpreNomeFantasia ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Empresa</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<!-- /theme JS files -->	
	
	<script>
		
		function atualizaEmpresa(EmpresaId, Tipo){
			
			document.getElementById('inputEmpresaId').value = EmpresaId;
			
			if (Tipo == 'edita'){	
				document.formEmpresa.action = "empresaEdita.php";		
			} else if (Tipo == 'exclui'){
				document.formEmpresa.action = "empresaExclui.php";
			}
			
			document.formEmpresa.submit();
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

				<?php 

				if ($_SESSION['msg']){
					/*<button type="button" class="btn btn-light" id="noty_top_center">Launch <i class="icon-play3 ml-2"></i></button>
					<button type="button" class="btn btn-success legitRipple" id="noty_success"></button>*/
					echo $_SESSION['msg'];
				}
				
				?>

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h5 class="card-title">Relação de Empresas</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								As empresas cadastradas abaixo estarão aptas a utilizar o sistema, desde que ativas e com licença vigente.
								<div class="text-right"><a href="empresaNovo.php" class="btn btn-success" role="button">Nova Empresa</a></div>
							</div>							

							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Nome Fantasia</th>
										<th>Razão Social</th>
										<th>CNPJ</th>
										<th>Situação</th>
										<th>Licença</th>
										<th class="text-center">Acões</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['EmpreStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['EmpreStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['EmpreNomeFantasia'].'</td>
											<td>'.$item['EmpreRazaoSocial'].'</td>
											<td>'.formatarCnpj($item['EmpreCnpj']).'</td>
											<td><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></td>
											<td><span class="badge '.$situacaoClasse.'">'.$item['diasAVencer'].'</span></td>
											<td class="text-center">
												<div class="list-icons">
													<div class="dropdown">
														<a href="#" class="list-icons-item" data-toggle="dropdown">
															<i class="icon-menu9"></i>
														</a>

														<div class="dropdown-menu dropdown-menu-right">
															<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \'edita\')" class="dropdown-item"><i class="icon-pencil7"></i> Editar</a>
															<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \'exclui\')" class="dropdown-item"><i class="icon-bin"></i> Excluir</a>
														</div>
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
				
				<form name="formEmpresa" method="post" action="empresaEdita.php">
					<input type="hidden" id="inputEmpresaId" name="inputEmpresaId" >
				</form>

			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
