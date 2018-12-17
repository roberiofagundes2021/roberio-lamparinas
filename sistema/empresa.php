<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Empresa';

unset($_SESSION['EmpresaId']);
unset($_SESSION['EmpresaNome']);

include('global_assets/php/conexao.php');

$sql = ("SELECT EmpreId, EmpreCnpj, EmpreRazaoSocial, EmpreNomeFantasia, EmpreStatus, dbo.fnLicencaVencimento(EmpreId) as Licenca
		 FROM Empresa
		 LEFT JOIN Licenca on LicenEmpresa = EmpreId
		 ORDER BY EmpreNomeFantasia ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//	var_dump($count);die;
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
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	<!-- /theme JS files -->	
	
	<script>
				
		function atualizaEmpresa(EmpresaId, EmpresaNome, EmpresaStatus, Tipo){

			document.getElementById('inputEmpresaId').value = EmpresaId;
			document.getElementById('inputEmpresaNome').value = EmpresaNome;
			document.getElementById('inputEmpresaStatus').value = EmpresaStatus;
					
			if (Tipo == 'edita'){	
				document.formEmpresa.action = "empresaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formEmpresa, "Tem certeza que deseja excluir essa empresa?", "empresaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formEmpresa.action = "empresaMudaSituacao.php";
			} else if (Tipo == 'usuario') {
				document.formEmpresa.action = "empresaUsuario.php";
			} else if (Tipo == 'licenca'){
				document.formEmpresa.action = "licenca.php";
			} else if (Tipo == 'menu'){
				document.formEmpresa.action = "menu.php";
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

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Relação de Empresas</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="empresa.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">As empresas cadastradas abaixo estarão aptas a utilizar o sistema, desde que ativas e com licença vigente.</p>
								<div class="text-right"><a href="empresaNovo.php" class="btn btn-success" role="button">Nova Empresa</a></div>
							</div>							

							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Nome Fantasia</th>
										<th>Razão Social</th>
										<th>CNPJ</th>
										<th>Situação</th>
										<th>Fim Licença</th>
										<th class="text-center">Ações</th>
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
											<td>'.formatarCPF_Cnpj($item['EmpreCnpj']).'</td>');
										
										if ($_SESSION['EmpreId'] != $item['EmpreId']) {
											print('<td><a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										} else {
											print('<td><a href="#" data-popup="tooltip" data-trigger="focus" title="Essa empresa está sendo usada por você no momento"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										}
										
										print('<td><span>'.$item['Licenca'].'</span></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
														<div class="dropdown">													
															<a href="#" class="list-icons-item" data-toggle="dropdown">
																<i class="icon-menu9"></i>
															</a>

															<div class="dropdown-menu dropdown-menu-right">
																<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'usuario\');" class="dropdown-item"><i class="icon-user-plus"></i> Usuários</a>
																<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'licenca\');" class="dropdown-item"><i class="icon-certificate"></i> Licença</a>
																<a href="#" onclick="atualizaEmpresa('.$item['EmpreId'].', \''.$item['EmpreNomeFantasia'].'\','.$item['EmpreStatus'].', \'menu\');" class="dropdown-item"><i class="icon-menu2"></i> Menu</a>
															</div>
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
				
				<form name="formEmpresa" method="post">
					<input type="hidden" id="inputEmpresaId" name="inputEmpresaId" >
					<input type="hidden" id="inputEmpresaNome" name="inputEmpresaNome" >
					<input type="hidden" id="inputEmpresaStatus" name="inputEmpresaStatus" >
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
