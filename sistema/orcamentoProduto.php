<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Orçamento';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if(isset($_POST['inputOrcamentoId'])){
	
	$iOrcamento = $_POST['inputOrcamentoId'];
	
	try{
		
		$sql = "SELECT *
				FROM Produto
				JOIN Categoria on CategId = ProduCategoria
				WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduCategoria = ".$_POST['inputOrcamentoCategoria'];
		$result = $conn->query("$sql");
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("orcamento.php");
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Listando produtos do Orçamento</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/tables/handsontable/handsontable.min.js"></script>
	<script src="global_assets/js/demo_pages/handsontable_basic.js"></script>
	

	<!-- /theme JS files -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
			
			//Ao informar o fornecedor, trazer os demais dados dele (contato, e-mail, telefone)
			$('#cmbFornecedor').on('change', function(e){				
				
				var Fornecedor = $('#cmbFornecedor').val();
				var Forne = Fornecedor.split('#');
				
				$('#inputContato').val(Forne[1]);
				$('#inputEmailFornecedor').val(Forne[2]);
				if(Forne[3] != "" && Forne[3] != "(__) ____-____"){
					$('#inputTelefoneFornecedor').val(Forne[3]);
				} else {
					$('#inputTelefoneFornecedor').val(Forne[4]);
				}
			});
			
			var data = [
			  ["Item", "Produto", "SubCategoria", "Unidade de Medida", "Quantidade", "Valor Unitário", "Valor Total"],
			  [1, "Papel Chamex A", 11, 12, 13],
			  [2, 20, 11, 14, 13],
			  [3, 30, 15, 12, 13]
			];

			var container = document.getElementById('example');
			var hot = new Handsontable(container, {
			  data: data,
			  rowHeaders: true,
			  colHeaders: true,
			  filters: true,
			  dropdownMenu: true
			});			
						
		}); //document.ready
							
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
					
					<form name="formOrcamentoProduto" id="formOrcamentoProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - Orçamento Nº "<?php echo $_POST['inputOrcamentoNumero']; ?>"</h5>
						</div>					
						
						<div class="card-body">								
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
																														
										<div class="col-lg-7">
											<div class="form-group">
												<label for="cmbCategoria">Sub Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT SbCatId, SbCatNome
																 FROM SubCategoria															     
																 WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and SbCatStatus = 1 and SbCatCategoria = ".$_POST['inputOrcamentoCategoria']."
															     ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowSubCategoria as $item){															
															print('<option value="'.$item['SbCatId'].'" >'.$item['SbCatNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>
							
							<!-- Custom header text -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h5 class="card-title">Relação de Produtos</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">The following example demonstrates rows and columns headers with custom text using <code>colHeaders</code> and <code>rowHeaders</code> options. Setting <code>true</code> or <code>false</code> will enable or disable the default column headers (A, B, C). You can also define an array <code>['One', 'Two', 'Three', ...]</code> or a function to define the headers. If a function is set the index of the column is passed as a parameter.</p>

									<div class="hot-container">
										<div id="example"></div>
									</div>
								</div>
							</div>
							<!-- /custom header text -->
							
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
										<a href="orcamento.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
					
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
