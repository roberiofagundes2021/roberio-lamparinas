<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Relatório de Movimentação';

include('global_assets/php/conexao.php');

$sql = ("SELECT ForneId, ForneNome, ForneCpf, ForneCnpj, ForneTelefone, ForneCelular, ForneStatus, CategNome
		 FROM Fornecedor
		 LEFT JOIN Categoria on CategId = ForneCategoria
	     WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY ForneNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

$d = date("d"); 
$m = date("m"); 
$Y = date("Y"); 

$dataInicio = date("Y-m-d", mktime(0, 0, 0, $m, $d-30, $Y)); //30 dias atrás
$dataFim = date ("Y-m-d");


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Relatório de Movimentação</title>

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
	
	<script type="text/javascript">
		
        $(document).ready(function() {	
			
			//Ao mudar o fornecedor, filtra a categoria, subcategoria e produto via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e){
				
				var cmbTipo = $('#cmbTipo').val();
				var inputFornecedor = $('#inputFornecedor').val();
				var cmbFornecedor = $('#cmbFornecedor').val();
				
				$('#inputFornecedor').val(cmbFornecedor);
				
				FiltraCategoria();
				Filtrando();

				$.getJSON('filtraCategoria.php?idFornecedor='+cmbFornecedor, function (dados){

					var option = '<option value="#">Selecione a Categoria</option>';

					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.CategId+'">'+obj.CategNome+'</option>';
						});						
						
						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}					
				});				
				
				$.getJSON('filtraSubCategoria.php?idFornecedor='+cmbFornecedor, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});
				
				$.getJSON('filtraProduto.php?idFornecedor='+cmbFornecedor, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});			
			
			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});
				
				$.getJSON('filtraProduto.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	
					
			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbSubCategoria').on('change', function(e){
				
				FiltraProduto();
				
				var cmbTipo = $('#cmbTipo').val();
				var cmbFornecedor = $('#cmbFornecedor').val();				
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				
				if (cmbTipo == 'S' || cmbTipo == 'T'){
					cmbFornecedor = '#';
				}
				
				$.getJSON('filtraProduto.php?idFornecedor='+cmbFornecedor+'&idCategoria='+cmbCategoria+'&idSubCategoria='+cmbSubCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	

			//Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
			function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
				FiltraProduto();
			}		

			//Mostra o "Filtrando..." na combo Produto
			function FiltraCategoria(){
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
			}
			
			//Mostra o "Filtrando..." na combo Produto
			function FiltraProduto(){
				$('#cmbProduto').empty().append('<option>Filtrando...</option>');
			}		
			
			function ResetCategoria(){
				$('#cmbCategoria').empty().append('<option>Sem Categoria</option>');
			}			
			
			function ResetSubCategoria(){
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}
			
			function ResetProduto(){
				$('#cmbProduto').empty().append('<option>Sem produto</option>');
			}				
						
		});		
					
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
								<h3 class="card-title">Relatório de Movimentação</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="relatorioMovimentacao.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">Utilize os filtros abaixo para gerar o relatório.</p>
								<br>
								<div class="row">
									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputDataInicio">Data Início</label>
											<div class="input-group">
												<span class="input-group-prepend">
													<span class="input-group-text"><i class="icon-calendar22"></i></span>
												</span>
												<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" value="<?php echo $dataInicio; ?>">
											</div>
										</div>
									</div>
									<div class="col-lg-2">
										<div class="form-group">
											<label for="inputDataFim">Data Fim</label>
											<div class="input-group">
												<span class="input-group-prepend">
													<span class="input-group-text"><i class="icon-calendar22"></i></span>
												</span>
												<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" value="<?php echo $dataFim; ?>">
											</div>
										</div>
									</div>										
									<div class="col-lg-2">
										<div class="form-group">
											<label for="cmbTipo">Tipo</label>
											<select id="cmbTipo" name="cmbTipo" class="form-control form-control-select2">
												<option value="#">Todos</option>
												<option value="E">Entrada</option>
												<option value="S">Saída</option>
												<option value="T">Transferência</option>
											</select>
										</div>
									</div>								
									<div class="col-lg-6">
										<div class="form-group">
											<label for="cmbFornecedor">Fornecedor</label>
											<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2">
												<option value="-1">Todos</option>
												<?php 
													$sql = ("SELECT ForneId, ForneNome
															 FROM Fornecedor														     
															 WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
															 ORDER BY ForneNome ASC");
													$result = $conn->query("$sql");
													$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowFornecedor as $item){															
														print('<option value="'.$item['ForneId'].'">'.$item['ForneNome'].'</option>');
													}
												
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label for="cmbCategoria">Categoria</label>
											<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
												<option value="#">Todas</option>
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
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="#">Todas</option>
													<?php 
														$sql = ("SELECT SbCatId, SbCatNome
																 FROM SubCategoria															     
																 WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['SbCatId'].'">'.$item['SbCatNome'].'</option>');
														}
													?>
												</select>
										</div>
									</div>									
								</div>
								<div class="row">
									<div class="col-lg-2">
										<div class="form-group">
											<label for="cmbCodigo">Código</label>
											<select id="cmbCodigo" name="cmbCodigo" class="form-control form-control-select2">
												<option value="#">Todas</option>
												<?php 
													$sql = ("SELECT ProduCodigo
															 FROM Produto															     
															 WHERE ProduStatus = 1 and ProduEmpresa = ". $_SESSION['EmpreId'] ."
															 ORDER BY ProduNome ASC");
													$result = $conn->query("$sql");
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowCategoria as $item){															
														print('<option value="'.$item['ProduCodigo'].'">'.$item['ProduCodigo'].'</option>');
													}
												
												?>
											</select>
										</div>
									</div>
									<div class="col-lg-10">
										<div class="form-group">
											<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control form-control-select2">
													<option value="#">Todas</option>
													<?php 
														$sql = ("SELECT ProduId, ProduNome
																 FROM Produto															     
																 WHERE ProduStatus = 1 and ProduEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY ProduNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['ProduId'].'">'.$item['ProduNome'].'</option>');
														}
													?>
												</select>
										</div>
									</div>									
								</div>								
										
								<div class="text-right">
									<div>
										<a href="fornecedorNovo.php" class="btn btn-success btn-icon" role="button">
											<i class="icon-printer2"> Imprimir</i>
										</a>
									</div>
								</div>
								
							</div>
							
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
