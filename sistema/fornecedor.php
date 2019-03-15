<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fornecedor';

include('global_assets/php/conexao.php');

$sql = ("SELECT ForneId, ForneNome, ForneCpf, ForneCnpj, ForneTelefone, ForneCelular, ForneStatus, CategNome
		 FROM Fornecedor
		 LEFT JOIN Categoria on CategId = ForneCategoria
	     WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY ForneNome ASC");
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
	<title>Lamparinas | Fornecedor</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script>
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaFornecedor(ForneId, ForneNome, ForneStatus, Tipo){
			
			if (Tipo == 'imprime'){
				
				document.getElementById('inputFornecedorCategoria').value = document.getElementById('cmbCategoria').value;
				
				document.formFornecedor.action = "fornecedorImprime.php";
				document.formFornecedor.setAttribute("target", "_blank");
			} else {
				document.getElementById('inputFornecedorId').value = ForneId;
				document.getElementById('inputFornecedorNome').value = ForneNome;
				document.getElementById('inputFornecedorStatus').value = ForneStatus;
						
				if (Tipo == 'edita'){	
					document.formFornecedor.action = "fornecedorEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formFornecedor, "Tem certeza que deseja excluir esse fornecedor?", "fornecedorExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formFornecedor.action = "fornecedorMudaSituacao.php";
				} 
			}
			
			document.formFornecedor.submit();
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
								<h3 class="card-title">Relação de Fornecedores</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="fornecedor.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos fornecedores da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right">
									<div class="dropdown p-0" style="float:right; margin-left: 5px;">										
										<a href="#collapse-imprimir-relacao" class="dropdown-toggle btn bg-slate-700 btn-icon" role="button" data-toggle="collapse" data-placement="bottom" data-container="body">
											<i class="icon-printer2"></i>																						
										</a>
									</div>
									<div>
										<a href="fornecedorNovo.php" class="btn btn-success" role="button">Novo Fornecedor</a>
									</div>
								</div>
								<div class="collapse" id="collapse-imprimir-relacao" style="margin-top: 5px;">
									<div class="row">
										<div class="col-lg-9">
										</div>
										<div class="col-lg-3">
											<div class="form-group">												
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2 dropdown-item">
													<option value="#">Filtrar por: Categoria (todas)</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and CategStatus = 1
																 ORDER BY CategNome ASC");
														$result = $conn->query("$sql");
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){															
															print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										
											<a href="#" onclick="atualizaFornecedor(0, '','', 'imprime');" class="form-control btn bg-slate-700 btn-icon" role="button" data-placement="bottom" data-container="body">
												<i class="icon-printer2"> Imprimir Relação</i>
											</a>
										</div>
									</div>
								</div>
							</div>
							
							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th width="30%">Nome</th>
										<th width="15%">CPF/CNPJ</th>
										<th width="15%">Telefone</th>										
										<th width="20%">Categoria</th>
										<th width="8%">Situação</th>
										<th class="text-center" width="7%">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['ForneStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['ForneStatus'] ? 'badge-success' : 'badge-secondary';
										$documento = $item['ForneCnpj'] == NULL ? $item['ForneCpf'] : $item['ForneCnpj'];
										$telefone = $item['ForneCelular'] == NULL ? $item['ForneTelefone'] : $item['ForneCelular'];
										
										print('
										<tr>
											<td>'.$item['ForneNome'].'</td>
											<td>'.formatarCPF_Cnpj($documento).'</td>
											<td>'.$telefone.'</td>
											<td>'.$item['CategNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaFornecedor('.$item['ForneId'].', \''.$item['ForneNome'].'\','.$item['ForneStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaFornecedor('.$item['ForneId'].', \''.$item['ForneNome'].'\','.$item['ForneStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaFornecedor('.$item['ForneId'].', \''.$item['ForneNome'].'\','.$item['ForneStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
														<!--<a href="#" onclick="atualizaFornecedor('.$item['ForneId'].', \''.$item['ForneNome'].'\','.$item['ForneStatus'].', \'imprime\');" class="list-icons-item"><i class="icon-printer2" data-popup="tooltip" data-placement="bottom" title="Gerar PDF"></i></a>-->
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
				
				<form name="formFornecedor" method="post">
					<input type="hidden" id="inputFornecedorId" name="inputFornecedorId" >
					<input type="hidden" id="inputFornecedorNome" name="inputFornecedorNome" >
					<input type="hidden" id="inputFornecedorStatus" name="inputFornecedorStatus" >
					<input type="hidden" id="inputFornecedorCategoria" name="inputFornecedorCategoria" >
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
