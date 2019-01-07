<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Orçamento';

include('global_assets/php/conexao.php');

$sql = ("SELECT OrcamId, OrcamNumero, OrcamData, OrcamCategoria, ForneNome, CategNome, OrcamStatus
		 FROM Orcamento
		 JOIN Fornecedor on ForneId = OrcamFornecedor
		 JOIN Categoria on CategId = OrcamCategoria
	     WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY OrcamData DESC");
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
	<title>Lamparinas | Orçamento</title>

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
		function atualizaOrcamento(OrcamId, OrcamNumero, OrcamCategoria, OrcamStatus, Tipo){
		
			document.getElementById('inputOrcamentoId').value = OrcamId;
			document.getElementById('inputOrcamentoNumero').value = OrcamNumero;
			document.getElementById('inputOrcamentoCategoria').value = OrcamCategoria;
			document.getElementById('inputOrcamentoStatus').value = OrcamStatus;
					
			if (Tipo == 'edita'){	
				document.formOrcamento.action = "orcamentoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formOrcamento, "Tem certeza que deseja excluir esse orcamento?", "orcamentoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formOrcamento.action = "orcamentoMudaSituacao.php";
			} else if (Tipo == 'produto'){
				document.formOrcamento.action = "orcamentoProduto.php";
			}
			
			document.formOrcamento.submit();
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
								<h5 class="card-title">Relação de Orçamentos</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								A relação abaixo faz referência aos orçamentos da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>
								<div class="text-right"><a href="orcamentoNovo.php" class="btn btn-success" role="button">Novo Orçamento</a></div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th width="10%">Data</th>
										<th width="14%">Nº Orçamento</th>
										<th width="28%">Fornecedor</th>
										<th width="28%">Categoria</th>
										<th width="10%">Situação</th>
										<th width="10%" class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['OrcamStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['OrcamStatus'] ? 'badge-success' : 'badge-secondary';
										
										//$telefone = isset($item['ForneTelefone']) ? $item['ForneTelefone'] : $item['ForneCelular'];
										
										print('
										<tr>
											<td>'.mostraData($item['OrcamData']).'</td>
											<td>'.$item['OrcamNumero'].'</td>
											<td>'.$item['ForneNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\','.$item['OrcamStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\','.$item['OrcamStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" title="Editar Orçamento"></i></a>
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\','.$item['OrcamStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" title="Excluir Orçamento"></i></a>
														<a href="#" onclick="atualizaOrcamento('.$item['OrcamId'].', \''.$item['OrcamNumero'].'\', \''.$item['OrcamCategoria'].'\','.$item['OrcamStatus'].', \'produto\');" class="list-icons-item"><i class="icon-basket" title="Listar Produtos"></i></a>
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
				
				<form name="formOrcamento" method="post">
					<input type="hidden" id="inputOrcamentoId" name="inputOrcamentoId" >
					<input type="hidden" id="inputOrcamentoNumero" name="inputOrcamentoNumero" >
					<input type="hidden" id="inputOrcamentoCategoria" name="inputOrcamentoCategoria" >
					<input type="hidden" id="inputOrcamentoStatus" name="inputOrcamentoStatus" >
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
