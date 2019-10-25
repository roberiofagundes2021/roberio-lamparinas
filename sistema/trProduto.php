<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'TR Produto';

include('global_assets/php/conexao.php');

//Se veio do tr.php
if(isset($_POST['inputTRId'])){
	$iTR = $_POST['inputTRId'];
	$iCategoria = $_POST['inputTRCategoria'];
} else if (isset($_POST['inputIdTR'])){
	$iTR = $_POST['inputIdTR'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("tr.php");
}

//Se está alterando
if(isset($_POST['inputIdTR'])){
	
	$sql = "DELETE FROM TermoReferenciaXProduto
			WHERE TRXPrTermoReferencia = :iTR AND TRXPrEmpresa = :iEmpresa";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':iTR' => $iTR,
					':iEmpresa' => $_SESSION['EmpreId']
					));		
	
	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
	
		$sql = "INSERT INTO TermoReferenciaXProduto (TRXPrTermoReferencia, TRXPrProduto, TRXPrQuantidade, TRXPrValorUnitario, TRXPrUsuarioAtualizador, TRXPrEmpresa)
				VALUES (:iTR, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iTR' => $iTR,
						':iProduto' => $_POST['inputIdProduto'.$i],
						':iQuantidade' => $_POST['inputQuantidade'.$i] == '' ? null : $_POST['inputQuantidade'.$i],
						':fValorUnitario' => null,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "TR alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}	

try{
	
	$sql = "SELECT *
			FROM TermoReferencia
			JOIN Categoria on CategId = TrRefCategoria
			LEFT JOIN SubCategoria on SbCatId = TrRefSubCategoria
			WHERE TrRefEmpresa = ". $_SESSION['EmpreId'] ." and TrRefId = ".$iTR;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	
	$sql = "SELECT TRXPrProduto
			FROM TermoReferenciaXProduto
			JOIN ProdutoOrcamento on PrOrcId = TRXPrProduto
			WHERE PrOrcEmpresa = ". $_SESSION['EmpreId'] ." and TRXPrTermoReferencia = ".$iTR;			
	$result = $conn->query($sql);
	$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countProdutoUtilizado = count($rowProdutoUtilizado);
	
	foreach ($rowProdutoUtilizado as $itemProdutoUtilizado){
		$aProdutos[] = $itemProdutoUtilizado['TRXPrProduto'];
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
	<title>Lamparinas | Listando produtos do TR</title>

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
				var inputSubCategoria = $('#inputIdSubCategoria').val();
				var produtos = $(this).val();
				//console.log(produtos);
				
				var cont = 1;
				var produtoId = [];
				var produtoQuant = [];
				
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
				
				$.ajax({
					type: "POST",
					url: "trFiltraProduto.php",
					data: {idCategoria: inputCategoria, idSubCategoria: inputSubCategoria, produtos: produtos, produtoId: produtoId, produtoQuant: produtoQuant},
					success: function(resposta){
						//alert(resposta);
						$("#tabelaProdutos").html(resposta).show();
						
						return false;
						
					}	
				});
			});
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo Produto
		function FiltraProduto(){
			$('#cmbProduto').empty().append('<option>Filtrando...</option>');
		}
		
		function ResetProduto(){
			$('#cmbProduto').empty().append('<option>Sem produto</option>');
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
					<form name="formTRProduto" id="formTRProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - TR Nº "<?php echo $row['TrRefNumero']; ?>"</h5>
						</div>					
						<input type="hidden" id="inputIdTR" name="inputIdTR" class="form-control" value="<?php echo $row['TrRefId']; ?>">
						<div class="card-body">
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['TrRefCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputSubCategoria">Sub Categoria</label>
												<input type="text" id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control" value="<?php echo $row['SbCatNome']; ?>" readOnly>
												<input type="hidden" id="inputIdSubCategoria" name="inputIdSubCategoria" class="form-control" value="<?php echo $row['TrRefSubCategoria']; ?>">
											</div>
										</div>
									</div>
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT PrOrcId, PrOrcNome
																FROM ProdutoOrcamento										     
																WHERE PrOrcEmpresa = ". $_SESSION['EmpreId'] ." and PrOrcSituacao = 1 and PrOrcCategoria = ".$iCategoria;
														if (isset($row['TrRefSubCategoria']) and $row['TrRefSubCategoria'] != '' and $row['TrRefSubCategoria'] != null){
															$sql .= " and PrOrcSubCategoria = ".$row['TrRefSubCategoria'];
														}
														$sql .= " ORDER BY PrOrcNome ASC";
														$result = $conn->query($sql);
														$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);														
														foreach ($rowProduto as $item){	
															if (in_array($item['PrOrcId'], $aProdutos) or $countProdutoUtilizado == 0) {
																$seleciona = "selected";
																print('<option value="'.$item['PrOrcId'].'" '.$seleciona.'>'.$item['PrOrcNome'].'</option>');
															} else {
																$seleciona = "";
																print('<option value="'.$item['PrOrcId'].'" '.$seleciona.'>'.$item['PrOrcNome'].'</option>');
															}														
															
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
									<p class="mb-3">Abaixo estão listados todos os produtos selecionadas acima. Para atualizar os valores, basta preencher a coluna <code>Quantidade</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php									

										$sql = "SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcUnidadeMedida, TRXPrQuantidade
												FROM ProdutoOrcamento
												JOIN TermoReferenciaXProduto on TRXPrProduto = PrOrcId
												LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
												WHERE PrOrcEmpresa = ".$_SESSION['EmpreId']." and TRXPrTermoReferencia = ".$iTR;
										$result = $conn->query($sql);
										$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowProdutos);
										
										if (!$count){
											$sql = "SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcUnidadeMedida
													FROM ProdutoOrcamento
													LEFT JOIN UnidadeMedida on UnMedId = PrOrcUnidadeMedida
													WHERE PrOrcEmpresa = ".$_SESSION['EmpreId']." and PrOrcCategoria = ".$iCategoria." and PrOrcSituacao = 1 ";
													
											if (isset($row['TrRefSubCategoria']) and $row['TrRefSubCategoria'] != '' and $row['TrRefSubCategoria'] != null){
												$sql .= " and PrOrcSubCategoria = ".$row['TrRefSubCategoria'];
											}
											$result = $conn->query($sql);
											$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										} 
										
										$cont = 0;
										
										print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-9">
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
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputQuantidade"><strong>Quantidade</strong></label>
												</div>
											</div>	
										</div>');
										
										print('<div id="tabelaProdutos">');
										
										foreach ($rowProdutos as $item){
											
											$cont++;
											
											$iQuantidade = isset($item['TRXPrQuantidade']) ? $item['TRXPrQuantidade'] : '';
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
															<input type="hidden" id="inputIdProduto'.$cont.'" name="inputIdProduto'.$cont.'" value="'.$item['PrOrcId'].'" class="idProduto">
														</div>
														<div class="col-lg-11">
															<input type="text" id="inputProduto'.$cont.'" name="inputProduto'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['PrOrcDetalhamento'].'" value="'.$item['PrOrcNome'].'" readOnly>
														</div>
													</div>
												</div>								
												<div class="col-lg-1">
													<input type="text" id="inputUnidade'.$cont.'" name="inputUnidade'.$cont.'" class="form-control-border-off" value="'.$item['PrOrcUnidadeMedida'].'" readOnly>
												</div>
												<div class="col-lg-2">
													<input type="text" id="inputQuantidade'.$cont.'" name="inputQuantidade'.$cont.'" class="form-control-border Quantidade" onkeypress="return onlynumber();" value="'.$iQuantidade.'">
												</div>	
											</div>');											
											
										}
										
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
										<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
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
