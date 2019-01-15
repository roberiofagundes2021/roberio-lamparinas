<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Setor';

include('global_assets/php/conexao.php');

if (!isset($_SESSION['EmpresaId'])) {
	irpara("empresa.php");
}

$sql = ("SELECT SetorId, SetorNome, UnidaNome, SetorStatus
		 FROM Setor
		 JOIN Unidade ON UnidaId = SetorUnidade
	     WHERE SetorEmpresa = ". $_SESSION['EmpresaId'] ."
		 ORDER BY UnidaNome, SetorNome ASC");
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
	<title>Lamparinas | Setor</title>

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
	
	<script language ="javascript">
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaSetor(SetorId, SetorNome, SetorStatus, Tipo){
		
			document.getElementById('inputSetorId').value = SetorId;
			document.getElementById('inputSetorNome').value = SetorNome;
			document.getElementById('inputSetorStatus').value = SetorStatus;
					
			if (Tipo == 'edita'){	
				document.formSetor.action = "setorEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formSetor, "Tem certeza que deseja excluir esse setor?", "setorExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formSetor.action = "setorMudaSituacao.php";
			} else if (Tipo == 'imprime'){
				document.formSetor.action = "setorImprime.php";
				document.formSetor.setAttribute("target", "_blank");
			}
			
			document.formSetor.submit();
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
								<h3 class="card-title">Relação de Setores</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="setor.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos setores da empresa <b><?php echo $_SESSION['EmpresaNome']; ?></b></p>
								<div class="text-right"><a href="setorNovo.php" class="btn btn-success" role="button">Novo Setor</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th width="40%">Setor</th>
										<th width="40%">Unidade</th>
										<th width="10%">Situação</th>
										<th width="10%" class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SetorStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['SetorStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['SetorNome'].'</td>
											<td>'.$item['UnidaNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaSetor('.$item['SetorId'].', \''.$item['SetorNome'].'\','.$item['SetorStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaSetor('.$item['SetorId'].', \''.$item['SetorNome'].'\','.$item['SetorStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaSetor('.$item['SetorId'].', \''.$item['SetorNome'].'\','.$item['SetorStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>							
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
				
				<form name="formSetor" method="post">
					<input type="hidden" id="inputSetorId" name="inputSetorId" >
					<input type="hidden" id="inputSetorNome" name="inputSetorNome" >
					<input type="hidden" id="inputSetorStatus" name="inputSetorStatus" >
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
