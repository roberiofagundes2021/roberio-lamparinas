<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Produto';

include('global_assets/php/conexao.php');

if(isset($_POST['inputCodigo'])){
		
	try{
		
		$sql = ("SELECT COUNT(isnull(ProduCodigo,0)) as Codigo
				 FROM Produto
				 Where ProduEmpresa = ".$_SESSION['EmpreId']."");
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);	
		
		$sCodigo = (int)$rowCodigo['Codigo'] + 1;
		$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);
		
		$sql = "INSERT INTO Produto (ProduCodigo, ProduCodigoBarras, ProduNome, ProduCategoria, ProduSubCategoria, ProduValorCusto, 
									 ProduDespesasAcessorias, ProduOutrasDespesas, ProduCustoFinal, ProduMargemLucro, ProduValorVenda, 
									 ProduEstoqueMinimo, ProduMarca, ProduModelo, ProduNumSerie, ProduFabricante, ProduUnidadeMedida, 
									 ProduTipoFiscal, ProduNcmFiscal, ProduOrigemFiscal, ProduCest, ProduStatus, 
									 ProduUsuarioAtualizador, ProduEmpresa) 
				VALUES (:sCodigo, :sCodigoBarras, :sNome, :iCategoria, :iSubCategoria, :fValorCusto, :fDespesasAcessorias,
						:fOutrasDespesas, :fCustoFinal, :fMargemLucro, :fValorVenda, :iEstoqueMinimo, :iMarca, :iModelo, :sNumSerie, 
						:iFabricante, :iUnidadeMedida, :iTipoFiscal, :iNcmFiscal, :iOrigemFiscal, :iCest, :bStatus, 
						:iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						':sCodigo' => $sCodigo,
						':sCodigoBarras' => $_POST['inputCodigoBarras'],
						':sNome' => $_POST['inputNome'],
						':iCategoria' => $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':fValorCusto' => $_POST['inputValorCusto'] == null ? null : gravaValor($_POST['inputValorCusto']),
						':fDespesasAcessorias' => $_POST['inputDespesasAcessorias'] == null ? null : gravaValor($_POST['inputDespesasAcessorias']),
						':fOutrasDespesas' => $_POST['inputOutrasDespesas'] == null ? null : gravaValor($_POST['inputOutrasDespesas']),
						':fCustoFinal' => $_POST['inputCustoFinal'] == null ? null : gravaValor($_POST['inputCustoFinal']),
						':fMargemLucro' => $_POST['inputMargemLucro'] == null ? null : gravaValor($_POST['inputMargemLucro']),
						':fValorVenda' => $_POST['inputValorVenda'] == null ? null : gravaValor($_POST['inputValorVenda']),
						':iEstoqueMinimo' => $_POST['inputEstoqueMinimo'] == '' ? null : $_POST['inputEstoqueMinimo'],
						':iMarca' => $_POST['cmbMarca'] == '#' ? null : $_POST['cmbMarca'],
						':iModelo' => $_POST['cmbModelo'] == '#' ? null : $_POST['cmbModelo'],
						':sNumSerie' => $_POST['inputNumSerie'] == '' ? null : $_POST['inputNumSerie'],
						':iFabricante' => $_POST['cmbFabricante'] == '#' ? null : $_POST['cmbFabricante'],
						':iUnidadeMedida' => $_POST['cmbUnidadeMedida'] == '#' ? null : $_POST['cmbUnidadeMedida'],
						':iTipoFiscal' => $_POST['cmbTipoFiscal'] == '#' ? null : $_POST['cmbTipoFiscal'],
						':iNcmFiscal' => $_POST['cmbNcmFiscal'] == '#' ? null : $_POST['cmbNcmFiscal'],
						':iOrigemFiscal' => $_POST['cmbOrigemFiscal'] == '#' ? null : $_POST['cmbOrigemFiscal'],
						':iCest' => $_POST['inputCest'] == '' ? null : $_POST['inputCest'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Produto incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir produto!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>	
	<!--<script src="global_assets/js/main/jquery.form.js"></script>-->
	<script src="http://malsup.github.com/jquery.form.js"></script>
	<!-- /theme JS files -->	
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
	
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option>Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						Reset();
					}					
				});
			});

			//Ao mudar o Custo, atualiza o CustoFinal
			$('#inputValorCusto').on('blur', function(e){
								
				var inputValorCusto = $('#inputValorCusto').val().replace('.', '').replace(',', '.');
				var inputOutrasDespesas = $('#inputOutrasDespesas').val().replace('.', '').replace(',', '.');
				
				if (inputValorCusto == null || inputValorCusto.trim() == '') {
					inputValorCusto = 0.00;
				}
				
				if (inputOutrasDespesas == null || inputOutrasDespesas.trim() == '') {
					inputOutrasDespesas = 0.00;
				}
				
				var inputCustoFinal = parseFloat(inputValorCusto) + parseFloat(inputOutrasDespesas);
				
				inputCustoFinal = float2moeda(inputCustoFinal).toString();
				
				$('#inputCustoFinal').val(inputCustoFinal);
			});
			
			//Ao mudar o Custo, atualiza o CustoFinal
			$('#inputOutrasDespesas').on('blur', function(e){
								
				var inputValorCusto = $('#inputValorCusto').val().replace('.', '').replace(',', '.');
				var inputOutrasDespesas = $('#inputOutrasDespesas').val().replace('.', '').replace(',', '.');
				
				if (inputValorCusto == null || inputValorCusto.trim() == '') {
					inputValorCusto = 0.00;
				}
				
				if (inputOutrasDespesas == null || inputOutrasDespesas.trim() == '') {
					inputOutrasDespesas = 0.00;
				}
				
				var inputCustoFinal = parseFloat(inputValorCusto) + parseFloat(inputOutrasDespesas);
				
				inputCustoFinal = float2moeda(inputCustoFinal).toString();
				
				$('#inputCustoFinal').val(inputCustoFinal);
			});			
			
			//Ao mudar a Margem de Lucro, atualiza o Valor de Venda
			$('#inputMargemLucro').on('blur', function(e){
								
				var inputCustoFinal = $('#inputCustoFinal').val().replace('.', '').replace(',', '.');
				var inputMargemLucro = $('#inputMargemLucro').val().replace(',', '.');				
				
				if (inputCustoFinal == null || inputCustoFinal.trim() == '') {
					inputCustoFinal = 0.00;
				}
				
				if (inputMargemLucro == null || inputMargemLucro.trim() == '') {
					inputMargemLucro = 0.00;
				}
								
				var inputValorVenda = parseFloat(inputCustoFinal) + (parseFloat(inputMargemLucro) * parseFloat(inputCustoFinal))/100;
				
				inputValorVenda = float2moeda(inputValorVenda).toString();
				
				$('#inputValorVenda').val(inputValorVenda);				

			});	
			
			//Ao mudar o Valor de Venda, atualiza a Margem de Lucro
			$('#inputValorVenda').on('blur', function(e){
								
				var inputCustoFinal = $('#inputCustoFinal').val().replace('.', '').replace(',', '.');
				var inputValorVenda = $('#inputValorVenda').val().replace('.', '').replace(',', '.');
				
				if (inputCustoFinal == null || inputCustoFinal.trim() == '') {
					inputCustoFinal = 0.00;
				}
				
				if (inputValorVenda == null || inputValorVenda.trim() == '') {
					inputValorVenda = 0.00;
				}
				
				//alert(parseFloat(inputMargemLucro) * parseFloat(inputCustoFinal));
				var lucro = parseFloat(inputValorVenda) - parseFloat(inputCustoFinal);		
				var inputMargemLucro = lucro / parseFloat(inputCustoFinal) * 100;
				
				inputMargemLucro = float2moeda(inputMargemLucro).toString();
				
				$('#inputMargemLucro').val(inputMargemLucro);				

			});	
			
			
			//Ao clicar no botão Adicionar Foto aciona o click do file que está hidden
			$('#addFoto').on('click', function(e){
								
				$('#imagem').trigger("click");
			});	
			
			// #imagem é o id do input, ao alterar o conteudo do input execurará a função baixo
			$('#imagem').on('change',function(){
				
				$('#visualizar').html('<img src="global_assets/images/lamparinas/ajax-loader.gif" alt="Enviando..."/>');
				
				var inputImagem = $('#imagem').val();
				//alert('Passou1');
				//$("#formFoto").submit();
				//$("#formFoto").ajaxForm(function(resposta) { 
				//	console.log(resposta); 
				//}); 
				//alert('Passou2');
				
				
				// Get form
				var form = $('#formFoto')[0];
				var data = new FormData(form);
				
				jQuery.ajax({
					url: 'upload.php',
					data: data,
					contentType: false,
					processData: false,
					cache: false,
					type: 'POST',
					success: function(data){
						alert(data);
					}
				});

/*				
				$.ajax({
					type: "POST",
					enctype: 'multipart/form-data',
					url: "upload.php",
					contentType: false,
					processData: false,
					data: {imagem: inputImagem},
					success: function(resposta){
						alert(resposta);
					
							return false;

						
						//$( "#formInventario" ).submit();
					}
				}); //ajax
				
				//$('#formFoto').submit(); */
				// Efetua o Upload sem dar refresh na pagina
				$('#formFoto').ajaxForm({
					target:'#visualizar' // o callback será no elemento com o id #visualizar
				}).submit();
			});			
			
			function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			}
			
			function Reset(){
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}
		});
		
		function float2moeda(num) {

		   x = 0;

		   if(num<0) {
			  num = Math.abs(num);
			  x = 1;
		   }
		   if(isNaN(num)) num = "0";
			  cents = Math.floor((num*100+0.5)%100);

		   num = Math.floor((num*100+0.5)/100).toString();

		   if(cents < 10) cents = "0" + cents;
			  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
				 num = num.substring(0,num.length-(4*i+3))+'.'
					   +num.substring(num.length-(4*i+3));
		   ret = num + ',' + cents;
		   if (x == 1) ret = ' - ' + ret;
		   
		   return ret;

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
					
					<form name="formProduto" method="post" class="form-validate" action="produtoNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Produto</h5>
						</div>
						
						<div class="card-body">								
							
							<div class="media">
								
								<div class="media-body">
									
									<div class="row">
										<div class="col-lg-8">
											<div class="form-group">
												<label for="inputCodigoBarras">Código de Barras</label>
												<input type="text" id="inputCodigoBarras" name="inputCodigoBarras" class="form-control" placeholder="Código de Barras" autofocus>
											</div>	
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">				
												<label for="inputEstoqueMinimo">Estoque Mínimo</label>
												<input type="text" id="inputEstoqueMinimo" name="inputEstoqueMinimo" class="form-control" placeholder="Estoque Mínimo">
											</div>	
										</div>						
									</div>
									
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDetalhamento">Detalhamento</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do produto"></textarea>
											</div>
										</div>
									</div>
																		
								</div> <!-- media-body -->
								
								<div style="text-align:center;">
									<div id="visualizar">
										<img class="ml-3" src="global_assets/images/lamparinas/sem_foto.gif" alt="Produto" style="max-height:250px; border:2px solid #ccc;">
									</div>
									<br>
									<button id="addFoto" class="ml-3 btn btn-lg btn-success" style="width:90%">Adicionar Foto</button>
									<form id="formFoto" method="post" enctype="multipart/form-data" action="upload.php">										
										<input type="file" id="imagem" name="imagem" style="display:none;" />
									</form>									
								</div>
								
							</div> <!-- media -->
														
							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Classificação</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY CategNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
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
													<option value="#">Selecione</option>
													<?php 
														/*$sql = ("SELECT SbCatId, SbCatNome
																 FROM SubCategoria															     
																 WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['SbCatId'].'">'.$item['SbCatNome'].'</option>');
														}
													  */
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-6">
									<h5 class="mb-0 font-weight-semibold">Custo</h5>
									<br>
								</div>
								<div class="col-lg-6">
									<h5 class="mb-0 font-weight-semibold">Venda</h5>
									<br>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorCusto">Valor de Custo</label>
												<input type="text" id="inputValorCusto" name="inputValorCusto" class="form-control" placeholder="Valor de Custo" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputOutrasDespesas">Outras Despesas</label>
												<input type="text" id="inputOutrasDespesas" name="inputOutrasDespesas" class="form-control" placeholder="Outras Despesas" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>			
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCustoFinal">Custo Final</label>
												<input type="text" id="inputCustoFinal" name="inputCustoFinal" class="form-control" placeholder="Custo Final" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputMargemLucro">Margem de Lucro (%)</label>
												<input type="text" id="inputMargemLucro" name="inputMargemLucro" class="form-control" placeholder="Margem Lucro" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputValorVenda">Valor de Venda</label>
												<input type="text" id="inputValorVenda" name="inputValorVenda" class="form-control" placeholder="Valor de Venda" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>
									</div>
								</div>						
							</div>

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Dados do Fabricante</h5>
									<br>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbMarca">Marca</label>
												<select id="cmbMarca" name="cmbMarca" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT MarcaId, MarcaNome
																 FROM Marca															     
																 WHERE MarcaStatus = 1 and MarcaEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY MarcaNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['MarcaId'].'">'.$item['MarcaNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbModelo">Modelo</label>
												<select id="cmbModelo" name="cmbModelo" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT ModelId, ModelNome
																 FROM Modelo
																 WHERE ModelStatus = 1 and ModelEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY ModelNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['ModelId'].'">'.$item['ModelNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>	
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbFabricante">Fabricante</label>
												<select id="cmbFabricante" name="cmbFabricante" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT FabriId, FabriNome
																 FROM Fabricante
																 WHERE FabriStatus = 1 and FabriEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY FabriNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['FabriId'].'">'.$item['FabriNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Número de Série</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" placeholder="Número de Série">
											</div>
										</div>	
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
												<option value="#">Selecione</option>
												<?php 
													$sql = ("SELECT UnMedId, UnMedNome, UnMedSigla
															 FROM UnidadeMedida
															 WHERE UnMedStatus = 1 and UnMedEmpresa = ".$_SESSION['EmpreId']."
															 ORDER BY UnMedNome ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item){
														print('<option value="'.$item['UnMedId'].'">'.$item['UnMedNome'] . ' (' . $item['UnMedSigla'] . ')' .'</option>');
													}
												
												?>
											</select>
										</div>
										
										<div class="col-lg-3">
											<label for="cmbTipoFiscal">Tipo</label>
											<select id="cmbTipoFiscal" name="cmbTipoFiscal" class="form-control form-control-select2">
												<option value="#">Selecione</option>
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
												<option value="#">Selecione</option>
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
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
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
