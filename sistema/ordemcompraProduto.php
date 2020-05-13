<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra Produto';

include('global_assets/php/conexao.php');

//Se veio do ordemcompra.php
if(isset($_POST['inputOrdemCompraId'])){
	$iOrdemCompra = $_POST['inputOrdemCompraId'];
	$iCategoria = $_POST['inputOrdemCompraCategoria'];
} else if (isset($_POST['inputIdOrdemCompra'])){
	$iOrdemCompra = $_POST['inputIdOrdemCompra'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("ordemcompra.php");
}

//Se está alterando
if(isset($_POST['inputIdOrdemCompra'])){
	
	$sql = "DELETE FROM OrdemCompraXProduto
			WHERE OCXPrOrdemCompra = :iOrdemCompra AND OCXPrUnidade = :iUnidade";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':iOrdemCompra' => $iOrdemCompra,
					':iUnidade' => $_SESSION['UnidadeId']
					));		
	
	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
	
		$sql = "INSERT INTO OrdemCompraXProduto (OCXPrOrdemCompra, OCXPrProduto, OCXPrQuantidade, OCXPrValorUnitario, OCXPrUsuarioAtualizador, OCXPrUnidade)
				VALUES (:iOrdemCompra, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iOrdemCompra' => $iOrdemCompra,
						':iProduto' => $_POST['inputIdProduto'.$i],
						':iQuantidade' => $_POST['inputQuantidade'.$i] == '' ? null : $_POST['inputQuantidade'.$i],
						':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Ordem de Compra alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}	

try{
	
	$sql = "SELECT *
			FROM OrdemCompra
			JOIN Fornecedor on ForneId = OrComFornecedor
			JOIN Categoria on CategId = OrComCategoria
			LEFT JOIN SubCategoria on SbCatId = OrComSubCategoria
			WHERE OrComUnidade = ". $_SESSION['UnidadeId'] ." and OrComId = ".$iOrdemCompra;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	
	$sql = "SELECT OCXPrProduto
			FROM OrdemCompraXProduto
			JOIN Produto on ProduId = OCXPrProduto
			WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and ProduCategoria = ".$iCategoria." and OCXPrOrdemCompra = ".$row['OrComId']."";
	
	if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
		$sql .= " and ProduSubCategoria = ".$row['OrComSubCategoria'];
	}	
	$result = $conn->query($sql);
	$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countProdutoUtilizado = count($rowProdutoUtilizado);
	
	foreach ($rowProdutoUtilizado as $itemProdutoUtilizado){
		$aProdutos[] = $itemProdutoUtilizado['OCXPrProduto'];
	}
	
	$sql = "SELECT COUNT(OCXPrProduto) as Quant
			FROM OrdemCompraXProduto
			WHERE OCXPrUnidade = ". $_SESSION['UnidadeId'] ." and OCXPrOrdemCompra = ".$iOrdemCompra." and 
			OCXPrQuantidade <> '' and OCXPrQuantidade <> 0 and OCXPrValorUnitario <> 0.00 ";
	$result = $conn->query($sql);
	$rowCompleto = $result->fetch(PDO::FETCH_ASSOC);

	$enviar = 0;

	//Verifica se o número de produtos é igual ao número de produtos com a quantidade e valor unitário preenchido para habilitar o botào "Enviar"
	if ($countProdutoUtilizado == $rowCompleto['Quant'] && ($countProdutoUtilizado != 0 && $rowCompleto['Quant'] != 0)){
		$enviar = 1;
	}

} catch(PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Listando produtos da Ordem de Compra</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>	

	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<!-- /theme JS files -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	

			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbProduto').on('change', function(e){
				
				var inputCategoria = $('#inputIdCategoria').val();
				var inputSubCategoria = $('#inputIdSubCategoria').val(); //alert(inputSubCategoria);
				var produtos = $(this).val();
				//console.log(produtos);
				
				var cont = 1;
				var produtoId = [];
				var produtoQuant = [];
				var produtoValor = [];
				
				// Aqui é para cada "class" faça
				$.each( $(".idProduto"), function() {			
					produtoId[cont] = $(this).val();
					cont++;
				});
				
				cont = 1;
				//aqui fazer um for que vai até o ultimo cont (dando cont++ dentro do for)
				$.each( $(".Quantidade"), function() {
					$id = produtoId[cont];
					
					produtoQuant[$id] = $(this).val();
					cont++;
				});				
				
				cont = 1;
				$.each( $(".ValorUnitario"), function() {
					$id = produtoId[cont];
					
					produtoValor[$id] = $(this).val();
					cont++;
				});
				
				$.ajax({
					type: "POST",
					url: "ordemcompraFiltraProduto.php",
					data: {idCategoria: inputCategoria, idSubCategoria: inputSubCategoria, produtos: produtos, produtoId: produtoId, produtoQuant: produtoQuant, produtoValor: produtoValor},
					success: function(resposta){
						//alert(resposta);
						$("#tabelaProdutos").html(resposta).show();
						
						return false;
						
					}	
				});
			});

			//Enviar para aprovação do Centro Administrativo (via Bandeja)
			$('#enviar').on('click', function(e){
				
				e.preventDefault();		
				
				confirmaExclusao(document.formOrdemCompraProduto, "Essa ação enviará toda a Ordem de Compra (com seus produtos e serviços) para aprovação do Centro Administrativo. Tem certeza que deseja enviar?", "ordemcompraEnviar.php");
			});			
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo Produto
		function FiltraProduto(){
			$('#cmbProduto').empty().append('<option>Filtrando...</option>');
		}
		
		function ResetProduto(){
			$('#cmbProduto').empty().append('<option>Sem produto</option>');
		}
		
		function calculaValorTotal(id){
			
			var ValorTotalAnterior = $('#inputValorTotal'+id+'').val() == '' ? 0 : $('#inputValorTotal'+id+'').val().replace('.', '').replace(',', '.');
			var TotalGeralAnterior = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');
			
			var Quantidade = $('#inputQuantidade'+id+'').val().trim() == '' ? 0 : $('#inputQuantidade'+id+'').val();
			var ValorUnitario = $('#inputValorUnitario'+id+'').val() == '' ? 0 : $('#inputValorUnitario'+id+'').val().replace('.', '').replace(',', '.');
			var ValorTotal = 0;
			
			var ValorTotal = parseFloat(Quantidade) * parseFloat(ValorUnitario);
			var TotalGeral = float2moeda(parseFloat(TotalGeralAnterior) - parseFloat(ValorTotalAnterior) + ValorTotal).toString();
			
			ValorTotal = float2moeda(ValorTotal).toString();
			
			$('#inputValorTotal'+id+'').val(ValorTotal);
			
			$('#inputTotalGeral').val(TotalGeral);			
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
					
					<form name="formOrdemCompraProduto" id="formOrdemCompraProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - Ordem de Compra Nº "<?php echo $row['OrComNumero']; ?>"</h5>
						</div>					
						
						<input type="hidden" id="inputIdOrdemCompra" name="inputIdOrdemCompra" class="form-control" value="<?php echo $row['OrComId']; ?>">
						
						<div class="card-body">		
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputFornecedor">Fornecedor</label>
												<input type="text" id="inputFornecedor" name="inputFornecedor" class="form-control" value="<?php echo $row['ForneNome']; ?>" readOnly>
												<input type="hidden" id="inputIdFornecedor" name="inputIdFornecedor" class="form-control" value="<?php echo $row['ForneId']; ?>">
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
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['OrComCategoria']; ?>">
											</div>
										</div>
									
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputSubCategoria">Sub Categoria</label>
												<input type="text" id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control" value="<?php echo $row['SbCatNome']; ?>" readOnly>
												<input type="hidden" id="inputIdSubCategoria" name="inputIdSubCategoria" class="form-control" value="<?php echo $row['OrComSubCategoria']; ?>">
											</div>
										</div>
									</div>
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT ProduId, ProduNome
																FROM Produto
																JOIN Situacao on SituaId = ProduStatus
																WHERE ProduUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave = 'ATIVO' and 
																ProduCategoria = ".$iCategoria;
														
														if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
															$sql .= " and ProduSubCategoria = ".$row['OrComSubCategoria'];
														}
														
														$sql .= " ORDER BY ProduNome ASC";
														$result = $conn->query($sql);
														$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);														
														
														foreach ($rowProduto as $item){	
															
															if (in_array($item['ProduId'], $aProdutos) or $countProdutoUtilizado == 0) {
																$seleciona = "selected";
															} else {
																$seleciona = "";
															}													
															
															print('<option value="'.$item['ProduId'].'" '.$seleciona.'>'.$item['ProduNome'].'</option>');
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

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php									

										$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, OCXPrQuantidade, OCXPrValorUnitario
												FROM Produto
												JOIN OrdemCompraXProduto on OCXPrProduto = ProduId
												JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
												WHERE ProduUnidade = ".$_SESSION['UnidadeId']." and OCXPrOrdemCompra = ".$iOrdemCompra;
										$result = $conn->query($sql);
										$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowProdutos);
										
										if (!$count){
											$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla
													FROM Produto
													JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
													WHERE ProduUnidade = ".$_SESSION['UnidadeId']." and ProduCategoria = ".$iCategoria." and ProduStatus = 1 ";
													
											if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
												$sql .= " and ProduSubCategoria = ".$row['OrComSubCategoria'];
											}
											$result = $conn->query($sql);
											$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										} 
										
										$cont = 0;
										
										print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-11">
															<label for="inputProduto"><strong>Produto</strong></label>
														</div>
													</div>
												</div>												
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputUnidade"><strong>Unidade</strong></label>
												</div>
											</div>
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputQuantidade"><strong>Quantidade</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorUnitario" title="Valor Unitário"><strong>Valor Unit.</strong></label>
												</div>
											</div>	
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputValorTotal"><strong>Valor Total</strong></label>
												</div>
											</div>											
										</div>');
										
										print('<div id="tabelaProdutos">');
										
										$fTotalGeral = 0;
										
										foreach ($rowProdutos as $item){
											
											$cont++;
											
											$iQuantidade = isset($item['OCXPrQuantidade']) ? $item['OCXPrQuantidade'] : '';
											$fValorUnitario = isset($item['OCXPrValorUnitario']) ? mostraValor($item['OCXPrValorUnitario']) : '';											
											$fValorTotal = (isset($item['OCXPrQuantidade']) and isset($item['OCXPrValorUnitario'])) ? mostraValor($item['OCXPrQuantidade'] * $item['OCXPrValorUnitario']) : '';
											
											$fTotalGeral += (isset($item['OCXPrQuantidade']) and isset($item['OCXPrValorUnitario'])) ? $item['OCXPrQuantidade'] * $item['OCXPrValorUnitario'] : 0;
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
															<input type="hidden" id="inputIdProduto'.$cont.'" name="inputIdProduto'.$cont.'" value="'.$item['ProduId'].'" class="idProduto">
														</div>
														<div class="col-lg-11">
															<input type="text" id="inputProduto'.$cont.'" name="inputProduto'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ProduDetalhamento'].'" value="'.$item['ProduNome'].'" readOnly>
														</div>
													</div>
												</div>								
												<div class="col-lg-1">
													<input type="text" id="inputUnidade'.$cont.'" name="inputUnidade'.$cont.'" class="form-control-border-off" value="'.$item['UnMedSigla'].'" readOnly>
												</div>
												<div class="col-lg-1">
													<input type="text" id="inputQuantidade'.$cont.'" name="inputQuantidade'.$cont.'" class="form-control-border Quantidade" onChange="calculaValorTotal('.$cont.')" onkeypress="return onlynumber();" value="'.$iQuantidade.'">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorUnitario'.$cont.'" name="inputValorUnitario'.$cont.'" class="form-control-border ValorUnitario" onChange="calculaValorTotal('.$cont.')" onKeyUp="moeda(this)" maxLength="12" value="'.$fValorUnitario.'">
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputValorTotal'.$cont.'" name="inputValorTotal'.$cont.'" class="form-control-border-off" value="'.$fValorTotal.'" readOnly>
												</div>											
											</div>');											
											
										}
										
										print('
										<div class="row" style="margin-top: 8px;">
												<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															
														</div>
														<div class="col-lg-8">
															
														</div>
														<div class="col-lg-3">
															
														</div>
													</div>
												</div>								
												<div class="col-lg-1">
													
												</div>
												<div class="col-lg-1">
													
												</div>	
												<div class="col-lg-1" style="padding-top: 5px; text-align: right;">
													<h5><b>Total:</b></h5>
												</div>	
												<div class="col-lg-1">
													<input type="text" id="inputTotalGeral" name="inputTotalGeral" class="form-control-border-off" value="'.mostraValor($fTotalGeral).'" readOnly>
												</div>											
											</div>'										
										);
										
										
										print('<input type="hidden" id="totalRegistros" name="totalRegistros" value="'.$cont.'" >');
										
										print('</div>');									
										
									?>
									
								</div>
							</div>
							<!-- /custom header text -->
							
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
										<?php
											if ($enviar){
												print('<button class="btn btn-lg btn-default" id="enviar">Enviar para Aprovação</button>');
											}
										?>
										<a href="ordemcompra.php" class="btn btn-basic" role="button">Cancelar</a>
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
	
	<?php include_once("alerta.php"); ?>

</body>
</html>
