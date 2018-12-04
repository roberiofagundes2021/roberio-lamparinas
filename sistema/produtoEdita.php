<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Produto';

include('global_assets/php/conexao.php');

if(isset($_POST['inputProdutoId'])){
	
	$iProduto = $_POST['inputProdutoId'];
	
	try{
		
		$sql = "SELECT *
				FROM Produto
				WHERE ProduId = $iProduto ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);		
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
}

if(isset($_POST['inputCodigo'])){	
		
	try{
		
		$sql = "UPDATE Produto SET ProduCodigo = :sCodigo, ProduCodigoBarras = :sCodigoBarras, ProduNome = :sNome, 
								   ProduCategoria = :iCategoria, ProduSubCategoria = :iSubCategoria, ProduValorCusto = :fValorCusto, 
								   ProduDespesasAcessorias = :fDespesasAcessorias, ProduOutrasDespesas = :fOutrasDespesas, 
								   ProduCustoFinal = :fCustoFinal, ProduValorVenda = :fValorVenda, ProduEstoqueMinimo = :iEstoqueMinimo, 
								   ProduMarca = :iMarca, ProduModelo = :iModelo, ProduNumSerie = :sNumSerie, 
								   ProduFabricante = :iFabricante, ProduUnidadeMedida = :iUnidadeMedida, ProduTipoFiscal = :iTipoFiscal, 
								   ProduNcmFiscal = :iNcmFiscal, ProduOrigemFiscal = :iOrigemFiscal, ProduCest = :iCest, 
								   ProduUsuarioAtualizador = :iUsuarioAtualizador
				WHERE ProduId = :iProduto";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCodigo' => $_POST['inputCodigo'],
						':sCodigBarras' => $_POST['inputCodigoBarras'],
						':sNome' => $_POST['inputNome'],
						':iCategoria' => $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'],
						':fValorCusto' => gravavalor($_POST['inputValorCusto']),
						':fDespesasAcessorias' => gravavalor($_POST['inputDespesasAcessorias']),
						':fOutrasDespesas' => gravavalor($_POST['inputOutrasDespesas']),
						':fCustoFinal' => gravavalor($_POST['inputCustoFinal']),
						':fValorVenda' => gravavalor($_POST['inputValorVenda']),
						':iEstoqueMinimo' => $_POST['inputEstoqueMinimo'],
						':iMarca' => $_POST['cmbMarca'],
						':iModelo' => $_POST['cmbModelo'],
						':sNumSerie' => $_POST['inputNumSerie'],
						':iFabricante' => $_POST['cmbFabricante'],
						':iUnidadeMedida' => $_POST['cmbUnidadeMedida'],
						':iTipoFiscal' => $_POST['cmbTipoFiscal'],
						':iNcmFiscal' => $_POST['cmbNcmFiscal'],
						':iOrigemFiscal' => $_POST['cmbOrigemFiscal'],
						':iCest' => $_POST['inputCest'],						
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iProduto' => $_POST['inputProdutoId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Produto alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar produto!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	
	irpara("produto.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Produto</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>	

	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_pnotify.js"></script>
	
	<script src="global_assets/js/lamparinas/custom.js"></script>	
	<!-- /theme JS files -->	
	
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
					
					<form name="formProduto" method="post" class="form-validate" action="produtoEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Produto "<?php echo $row['ProduNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputProdutoId" name="inputProdutoId" value="<?php echo $row['ProduId']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputCodigo">Código do Produto</label>
										<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código Interno" value="<?php echo $row['ProduCodigo']; ?>" required>
									</div>
								</div>	
								
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputCodigoBarras">Código de Barras</label>
										<input type="text" id="inputCodigoBarras" name="inputCodigoBarras" class="form-control" placeholder="Código de Barras" value="<?php echo $row['ProduCodigoBarras']; ?>">
									</div>	
								</div>
								
								<div class="col-lg-3">
									<div class="form-group">				
										<label for="inputEstoqueMinimo">Estoque Mínimo</label>
										<input type="text" id="inputEstoqueMinimo" name="inputEstoqueMinimo" class="form-control" placeholder="Estoque Mínimo" value="<?php echo $row['ProduEstoqueMinimo']; ?>">
									</div>	
								</div>
								
								<div class="col-lg-3">
									<div class="form-group">				
										<img src="image.jpg" />
									</div>
								</div>								
								
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['ProduNome']; ?>" required>
									</div>
								</div>

							</div>
							
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbCategoria">Categoria</label>
										<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
											<option value="#">Selecione uma categoria</option>
											<?php 
												$sql = ("SELECT CategId, CategNome
														 FROM Categoria															     
														 WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY CategNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													$seleciona = $item['CategId'] == $row['ProduCategoria'] ? "selected" : "";
													print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbSubCategoria">SubCategoria</label>
										<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
											<option value="#">Selecione uma subcategoria</option>
											<?php 
												$sql = ("SELECT SbCatId, SbCatNome
														 FROM SubCategoria															     
														 WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY SbCatNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													$seleciona = $item['SbCatId'] == $row['ProduSubCategoria'] ? "selected" : "";
													print('<option value="'.$item['SbCatId'].'" '. $seleciona .'>'.$item['SbCatNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
							</div>							
									
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputValorCusto">Valor de Custo</label>
										<input type="text" id="inputValorCusto" name="inputValorCusto" class="form-control" placeholder="Valor de Custo" value="<?php echo $row['ProduValorCusto']; ?>">
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbMarca">Marca</label>
										<select id="cmbMarca" name="cmbMarca" class="form-control form-control-select2">
											<option value="#">Selecione uma Marca</option>
											<?php 
												$sql = ("SELECT MarcaId, MarcaNome
														 FROM Marca															     
														 WHERE MarcaStatus = 1 and MarcaEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY MarcaNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													$seleciona = $item['MarcaId'] == $row['ProduMarca'] ? "selected" : "";
													print('<option value="'.$item['MarcaId'].'" '. $seleciona .'>'.$item['MarcaNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>								
							</div>

							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputDespesasAcessorias">Despesas Acessórias</label>
										<input type="text" id="inputDespesasAcessorias" name="inputDespesaAcessoria" class="form-control" placeholder="Despesa Acessoria" value="<?php echo $row['ProduDespesaAcessoria']; ?>">
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label for="cmbModelo">Modelo</label>
										<select id="cmbModelo" name="cmbModelo" class="form-control form-control-select2">
											<option value="#">Selecione uma Marca</option>
											<?php 
												$sql = ("SELECT ModelId, ModelNome
														 FROM Modelo
														 WHERE ModelStatus = 1 and ModelEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY ModelNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													$seleciona = $item['ModelId'] == $row['ProduModelo'] ? "selected" : "";
													print('<option value="'.$item['ModelId'].'" '. $seleciona .'>'.$item['ModelNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>								
							</div>

							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputOutrasDespesas">Outras Despesas</label>
										<input type="text" id="inputOutrasDespesas" name="inputOutrasDespesas" class="form-control" placeholder="Outras Despesas" value="<?php echo $row['ProduOutrasDespesas']; ?>">
									</div>
								</div>

								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNumSerie">Número de Série</label>
										<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" placeholder="Número de Série" value="<?php echo $row['ProduNumSerie']; ?>">
									</div>
								</div>								
							</div>
							
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputCustoFinal">Custo Final</label>
										<input type="text" id="inputCustoFinal" name="inputCustoFinal" class="form-control" placeholder="Custo Final" value="<?php echo $row['ProduCustoFinal']; ?>">
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputValorVenda">Valor de Venda</label>
										<input type="text" id="inputValorVenda" name="inputValorVenda" class="form-control" placeholder="Valor de Venda" value="<?php echo $row['ProduValorVenda']; ?>">
									</div>
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbFabricante">Fabricante</label>
										<select id="cmbFabricante" name="cmbFabricante" class="form-control form-control-select2">
											<option value="#">Selecione um Fabricante</option>
											<?php 
												$sql = ("SELECT FabriId, FabriNome
														 FROM Fabricante
														 WHERE FabriStatus = 1 and FabriEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY FabriNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){
													$seleciona = $item['FabriId'] == $row['ProduFabricante'] ? "selected" : "";
													print('<option value="'.$item['FabriId'].'" '. $seleciona .'>'.$item['FabriNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
							</div>
							<br>
									
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados Fiscais</h5>
									<br>
									<div class="row">								
										<div class="col-lg-3">
											<label for="cmbUnidadeMedida">Unidade de Medida</label>
											<select id="cmbUnidadeMedida" name="cmbUnidadeMedida" class="form-control form-control-select2">
												<option value="#">Selecione uma Unidade de Medida</option>
												<?php 
													$sql = ("SELECT UnMedId, UnMedNome
															 FROM UnidadeMedida
															 WHERE UnMedStatus = 1
															 ORDER BY UnMedNome ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item){
														print('<option value="'.$item['UnMedId'].'">'.$item['UnMedNome'].'</option>');
													}
												
												?>
											</select>
										</div>
										
										<div class="col-lg-3">
											<label for="cmbTipoFiscal">Tipo</label>
											<select id="cmbTipoFiscal" name="cmbTipoFiscal" class="form-control form-control-select2">
												<option value="#">Selecione um Tipo</option>
												<?php 
													$sql = ("SELECT TpFisId, TpFisNome
															 FROM TipoFiscal
															 WHERE TpFisStatus = 1
															 ORDER BY TpFisNome ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($row as $item){
														print('<option value="'.$item['TpFisId'].'">'.$item['TpFisNome'].'</option>');
													}
												
												?>
											</select>
										</div>
																			
										<div class="col-lg-4">
											<label for="cmbOrigemFiscal">Origem</label>
											<select id="cmbOrigemFiscal" name="cmbOrigemFiscal" class="form-control form-control-select2">
												<option value="#">Selecione uma Origem</option>
												<?php 
													$sql = ("SELECT OrFisId, OrFisNome
															 FROM OrigemFiscal
															 WHERE OrFisStatus = 1
															 ORDER BY OrFisNome ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($row as $item){
														$seleciona = $item['OrFisNome'] == 'Nacional' ? "selected" : "";
														print('<option value="'.$item['OrFisId'].'" '.$seleciona.'>'.$item['OrFisNome'].'</option>');
													}
												
												?>
											</select>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCest">CEST</label>
												<input type="text" id="inputCest" name="inputCest" class="form-control" placeholder="CEST">
											</div>
										</div>										
									</div> <!-- /row -->
									
									<div class="row">
										<div class="col-lg-12">
											<label for="cmbNcmFiscal">NCM</label>
											<select id="cmbNcmFiscal" name="cmbNcmFiscal" class="form-control form-control-select2">
												<option value="#">Selecione um NCM</option>
												<?php 
													$sql = ("SELECT BancoId, BancoCodigo, BancoNome
															 FROM Banco
															 WHERE BancoStatus = 1
															 ORDER BY BancoCodigo ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($row as $item){
														print('<option value="'.$item['BancoId'].'">'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
													}
												
												?>
											</select>
										</div>

									</div>
								</div> <!-- /col -->
							</div>	<!-- /row -->

							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
										<a href="produto.php" class="btn btn-basic" role="button">Cancelar</a>
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
