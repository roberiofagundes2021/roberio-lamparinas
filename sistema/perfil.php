<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Perfil';

include('global_assets/php/conexao.php');

$sql = ("SELECT PerfiId, PerfiNome, PerfiChave, PerfiStatus
		 FROM Perfil		 
		 ORDER BY PerfiNome ASC");
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
	<title>Lamparinas | Perfil</title>

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
	<script src="global_assets/js/demo_pages/components_popups.js"></script
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaPerfil(PerfilId, PerfilNome, PerfilStatus, Tipo){
		
			document.getElementById('inputPerfilId').value = PerfilId;
			document.getElementById('inputPerfilNome').value = PerfilNome;
			document.getElementById('inputPerfilStatus').value = PerfilStatus;
					
			if (Tipo == 'edita'){	
				document.formPerfil.action = "perfilEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formPerfil, "Tem certeza que deseja excluir esse perfil?", "perfilExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formPerfil.action = "perfilMudaSituacao.php";
			}		
			
			document.formPerfil.submit();
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
								<h5 class="card-title">Relação de Perfis</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								Segue abaixo a relação de perfis disponíveis para os usuários do sistema.
								<div class="text-right"><a href="perfilNovo.php" class="btn btn-success" role="button">Novo Perfil</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Perfil</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['PerfiStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['PerfiStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['PerfiNome'].'</td>');
										
										print('<td><a href="#" onclick="atualizaPerfil('.$item['PerfiId'].', \''.$item['PerfiNome'].'\','.$item['PerfiStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaPerfil('.$item['PerfiId'].', \''.$item['PerfiNome'].'\','.$item['PerfiStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaPerfil('.$item['PerfiId'].', \''.$item['PerfiNome'].'\','.$item['PerfiStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formPerfil" method="post">
					<input type="hidden" id="inputPerfilId" name="inputPerfilId" >
					<input type="hidden" id="inputPerfilNome" name="inputPerfilNome" >
					<input type="hidden" id="inputPerfilStatus" name="inputPerfilStatus" >
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
