<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Orçamento';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if(isset($_POST['inputOrcamentoId'])){
	
	$iOrcamento = $_POST['inputOrcamentoId'];
	
	try{
		
		$sql = "SELECT *
				FROM Orcamento
				JOIN Fornecedor on ForneId = OrcamFornecedor
				WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ." and OrcamId = ".$iOrcamento;
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
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
	<!--<script src="global_assets/js/demo_pages/handsontable_basic.js"></script>-->
	<script src="global_assets/js/demo_pages/handsontable_advanced.js"></script>
	

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
			
			var inputCategoria = $('#inputCategoria').val();
			
			$.getJSON('filtraProdutosOrcamento.php?idCategoria='+inputCategoria, function (dados){
								
				var produtos = [
					['Item','Produto', 'Detalhamento', 'Unidade', 'Quantidade', 'Valor Unitário', 'Valor Total']
				];	

				var cont = 1;
				var registro = '';
				
				if (dados.length){

					$.each(dados, function(i, obj){
						registro = [[cont], [obj.ProduNome], [obj.ProduDetalhamento], [obj.UnMedSigla], "", "", [""]];
						produtos.push(registro);  //adiciona mais um item no array
						cont++;
					});	
					
					var container = document.getElementById('example');
					var hot = new Handsontable(container, {
					  data: produtos,
					  //columnSorting : true,
					 /* columns: [
						{data: 'id', type: 'numeric', width: 20}, 
						{data: 'produto', type: 'text'}, 
						{data: 'detalhamento', type: 'text'}, 
						{data: 'unidade', type: 'text', width: 20}, 
						{data: 'quantidade', type: 'numeric'}, 
						{data: 'valorunitario', type: 'numeric', numericFormat: {pattern: '0.00'}}, 
						{data: 'valortotal', type: 'numeric', numericFormat: {pattern: '0.00'}}
					   ], */
					  //rowHeaders: true,
					  //colHeaders: true,
					  //filters: true,
					  //dropdownMenu: true,
					  colWidths: [17, 100, 100, 30, 40, 40, 40],
					  manualRowResize: false,  //Pra que serve isso?
					  manualRowMove: false,   //Pra que serve isso?
					 // fixedRowsTop: 1,  // mantem o cabeçalho fixo
					  
					  //rowHeaders: true,
					  //colHeaders: ['Item','Produto', 'SubCategoria', 'UN', 'Quantidade', 'Valor Unitário', 'Valor Total'],
					  stretchH: 'all',
					  cells: function (row, col, prop, td) {
						 var cellProperties = {};

						 if (row === 0 || col === 0 || col === 1 || col === 2 || col === 3 || col === 6 || this.instance.getData()[row][col] === 'Read only') {
							cellProperties.readOnly = true; // make cell read-only if it is first row or the text reads 'readOnly'
						 }
						 
						 if (row === 0 || col === 0) {
							cellProperties.renderer = firstRowRenderer; // uses function directly
						 } 
						 else {
							cellProperties.renderer = "negativeValueRenderer"; // uses lookup map
						 } 
						 
						 /*
						 if (row != 0 && col != 4 && col != 5){
							alert('Entrou4');
							cellProperties.renderer = demaisRowRenderer;
						 }	else if (col === 4 && col === 5){
							alert('Entrou5');
							td.style.background = '#fff';						 
						 } */

						 return cellProperties;
					  }
					});
					
				} else {
					//ResetSubCategoria();
				}				
			});		
						
						
			// Maps function to lookup string
			Handsontable.renderers.registerRenderer('negativeValueRenderer', negativeValueRenderer);
			/*var produtos = [
			  ['Item','Produto', 'SubCategoria', 'Unidade', 'Quantidade', 'Valor Unitário', 'Valor Total'],
			  [1, 'Telha Intercalada', 'Telha', 'UN', '', '5,00', ''],
			  [2, 'Cimento', 'Telha', 'UN', '', '15,00', '']
			]; */ 	
						
		}); //document.ready
		
		// Renderizar linha do cabeçalho da tabela
        function firstRowRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);

            // Add styles to the table cell
            td.style.fontWeight = '500';
            td.style.color = '#1B5E20';
            td.style.background = '#E8F5E9';
        }	
      /*  
        // Renderizar demais linhas somente Leitura da tabela
        function demaisRowRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);

            // Add styles to the table cell
            td.style.fontWeight = '400';
            td.style.color = '#000';
            td.style.background = '#f5f5f5';
        }
	*/	
		// Renderizar valores negativos (cor vermelha)
        function negativeValueRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.TextRenderer.apply(this, arguments);
			var quant = 0;
			var valor = 0;
			
            // If row contains negative number, add class "negative"
            if (parseInt(value, 10) < 0) {
                td.className = 'text-danger';
            }
			
       /*     if (col === 4) {
				quant = value;
				//quant = parseFloat(value, 10);
				//valor = 
            }
			
			if (col === 5) {
				alert(quant);
			}*/
			

            // If empty cell, add grey background
            if (!value || value === '') {
                td.style.background = '#fff';
            } else {
				td.style.background = '#f5f5f5';
			}
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
				<div class="card">
					
					<form name="formOrcamentoProduto" id="formOrcamentoProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - Orçamento Nº "<?php echo $_POST['inputOrcamentoNumero']; ?>"</h5>
						</div>					
						
						<div class="card-body">								
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputFornecedor">Fornecedor</label>
												<input type="text" id="inputFornecedor" name="inputFornecedor" class="form-control" value="<?php echo $row['ForneNome']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCelular">Telefone</label>
												<input type="text" id="inputTelefone" name="inputTelefone" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefone">Celular</label>
												<input type="text" id="inputCelular" name="inputCelular" class="form-control" value="<?php echo $row['ForneCelular']; ?>" readOnly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $_POST['inputOrcamentoNomeCategoria']; ?>" readOnly>
												<input type="hidden" id="inputCategoria" name="inputCategoria" class="form-control" value="<?php echo $_POST['inputOrcamentoCategoria']; ?>">
											</div>
										</div>
									
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">Sub Categoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
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
									<p class="mb-3">Abaixo estão listados todos os produtos da Categoria e SubCategoria selecionadas logo acima. Para atualizar os valores, basta preencher as colunas <code>Quantidade</code> e <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<div class="hot-container">
										<div id="example"></div>
									</div>
								</div>
							</div>
							<!-- /custom header text -->
							
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" type="submit">Alterar</button>
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
