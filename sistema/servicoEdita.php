<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Servico';

include('global_assets/php/conexao.php');

if(isset($_POST['inputServicoId'])){
	
	$iServico = $_POST['inputServicoId'];
	
	try{
		
		$sql = "SELECT *
				FROM Servico
				WHERE ServId = $iServico ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);		
		
		$valorCusto = mostraValor($row['ServValorCusto']);
		$valorVenda	= mostraValor($row['ServValorVenda']);
		$outrasDespesas = mostraValor($row['ServOutrasDespesas']);
		$custoFinal = mostraValor($row['ServCustoFinal']);
		$margemLucro = mostraValor($row['ServMargemLucro']);
		$numSerie = $row['ServNumSerie'];
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("servico.php");
}

if(isset($_POST['inputNome'])){
		
	try{
		
		$sql = "UPDATE Servico SET ServCodigo = :sCodigo, ServNome = :sNome, ServDetalhamento = :sDetalhamento, 
		               ServCategoria = :iCategoria, ServSubCategoria = :iSubCategoria, ServValorCusto = :fValorCusto, ServOutrasDespesas = :fOutrasDespesas,
		               ServCustoFinal = :fCustoFinal, ServMargemLucro = :fMargemLucro, ServValorVenda = :fValorVenda, ServFabricante = :iFabricante, 
		               ServMarca = :iMarca, ServModelo = :iModelo, ServNumSerie = :sNumSerie, ServUsuarioAtualizador = :iUsuarioAtualizador 
		        WHERE ServId = :iServico";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCodigo' => $_POST['inputCodigo'],
						':sNome' => $_POST['inputNome'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':fValorCusto' => $_POST['inputValorCusto'] == null ? null : gravaValor($_POST['inputValorCusto']),						
						':fOutrasDespesas' => $_POST['inputOutrasDespesas'] == null ? null : gravaValor($_POST['inputOutrasDespesas']),
						':fCustoFinal' => $_POST['inputCustoFinal'] == null ? null : gravaValor($_POST['inputCustoFinal']),
						':fMargemLucro' => $_POST['inputMargemLucro'] == null ? null : gravaValor($_POST['inputMargemLucro']),
						':fValorVenda' => $_POST['inputValorVenda'] == null ? null : gravaValor($_POST['inputValorVenda']),
						':iFabricante' => $_POST['cmbFabricante'] == '#' ? null : $_POST['cmbFabricante'],
						':iMarca' => $_POST['cmbMarca'] == '#' ? null : $_POST['cmbMarca'],
						':iModelo' => $_POST['cmbModelo'] == '#' ? null : $_POST['cmbModelo'],
						':sNumSerie' => $_POST['inputNumSerie'] == '' ? null : $_POST['inputNumSerie'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iServico' => $_POST['inputServicoId']
						));
		
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Serviço alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar serviço!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		//$result->debugDumpParams();
		
		echo 'Error: ' . $e->getMessage();		
		exit;
	}
	
	irpara("servico.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Serviço</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>	
	<!-- /theme JS files -->	
	
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>	

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >
		
		//Ao carregar a página tive que executar o que o onChange() executa para que a combo da SubCategoria já venha filtrada, além de selecionada, é claro.
		window.onload = function(){

			var cmbSubCategoria = $('#cmbSubCategoria').val();
			
			Filtrando();
			
			var cmbCategoria = $('#cmbCategoria').val();

			$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
				
				var option = '<option value="">Selecione a SubCategoria</option>';
				
				if (dados.length){						
					
					$.each(dados, function(i, obj){

						if(obj.SbCatId == cmbSubCategoria){							
							option += '<option value="'+obj.SbCatId+'" selected>'+obj.SbCatNome+'</option>';
						} else {							
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						}
					});
					
					$('#cmbSubCategoria').html(option).show();
				} else {
					Reset();
				}					
			});
		}	

        $(document).ready(function() {	
		
			//Aqui sou obrigado a instanciar novamente a utilização do fancybox
			$(".fancybox").fancybox({
				// options
			});	
	
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="">Selecione a SubCategoria</option>';
					
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

				inputMargemLucro = 0;
				
				if (inputCustoFinal != 0.00){
					inputMargemLucro = lucro / parseFloat(inputCustoFinal) * 100;
				}
				
				inputMargemLucro = float2moeda(inputMargemLucro).toString();
				
				$('#inputMargemLucro').val(inputMargemLucro);				

			});

			function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			}
			
			function Reset(){
				$('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
			}			
		
		});

	</script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
					
					<form name="formServico" method="post" class="form-validate-jquery" action="servicoEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Serviço "<?php echo $row['ServNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputServicoId" name="inputServicoId" value="<?php echo $row['ServId']; ?>" >
						
						<div class="card-body">
							
							<div class="media">
								
								<div class="media-body">

									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCodigo">Código do Serviço</label>
												<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código Interno" value="<?php echo $row['ServCodigo']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-10">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['ServNome']; ?>" required>
											</div>
										</div>																								
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDetalhamento">Detalhamento</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do produto"><?php echo $row['ServDetalhamento']; ?></textarea>
											</div>
										</div>
									</div>
									
								</div> <!-- media-body -->																
									
							</div> <!-- media -->

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Classificação</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY CategNome ASC");
														$result = $conn->query("$sql");
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){
															$seleciona = $item['CategId'] == $row['ServCategoria'] ? "selected" : "";
															print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php 
														$sql = ("SELECT SbCatId, SbCatNome
																 FROM SubCategoria															     
																 WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowSubCategoria as $item){
															$seleciona = $item['SbCatId'] == $row['ServSubCategoria'] ? "selected" : "";
															print('<option value="'.$item['SbCatId'].'" '. $seleciona .'>'.$item['SbCatNome'].'</option>');
														}
													
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
												<input type="text" id="inputValorCusto" name="inputValorCusto" class="form-control" placeholder="Valor de Custo" value="<?php echo $valorCusto; ?>" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputOutrasDespesas">Outras Despesas</label>
												<input type="text" id="inputOutrasDespesas" name="inputOutrasDespesas" class="form-control" placeholder="Outras Despesas" value="<?php echo $outrasDespesas; ?>" onKeyUp="moeda(this)" maxLength="12">
											</div>
										</div>			
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCustoFinal">Custo Final</label>
												<input type="text" id="inputCustoFinal" name="inputCustoFinal" class="form-control" placeholder="Custo Final" value="<?php echo $custoFinal; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputMargemLucro">Margem de Lucro (%)</label>
												<input type="text" id="inputMargemLucro" name="inputMargemLucro" class="form-control" placeholder="Margem Lucro" value="<?php echo $margemLucro; ?>" onKeyUp="moeda(this)" maxLength="6">
											</div>
										</div>										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputValorVenda">Valor de Venda</label>
												<input type="text" id="inputValorVenda" name="inputValorVenda" class="form-control" placeholder="Valor de Venda" value="<?php echo $valorVenda; ?>" onKeyUp="moeda(this)" maxLength="12">
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
													<option value="">Selecione</option>
													<?php 
														$sql = ("SELECT MarcaId, MarcaNome
																 FROM Marca															     
																 WHERE MarcaStatus = 1 and MarcaEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY MarcaNome ASC");
														$result = $conn->query("$sql");
														$rowMarca = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowMarca as $item){
															$seleciona = $item['MarcaId'] == $row['ServMarca'] ? "selected" : "";
															print('<option value="'.$item['MarcaId'].'" '. $seleciona .'>'.$item['MarcaNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
							
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbModelo">Modelo</label>
												<select id="cmbModelo" name="cmbModelo" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php 
														$sql = ("SELECT ModelId, ModelNome
																 FROM Modelo
																 WHERE ModelStatus = 1 and ModelEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY ModelNome ASC");
														$result = $conn->query("$sql");
														$rowModelo = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowModelo as $item){
															$seleciona = $item['ModelId'] == $row['ServModelo'] ? "selected" : "";
															print('<option value="'.$item['ModelId'].'" '. $seleciona .'>'.$item['ModelNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbFabricante">Fabricante</label>
												<select id="cmbFabricante" name="cmbFabricante" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php 
														$sql = ("SELECT FabriId, FabriNome
																 FROM Fabricante
																 WHERE FabriStatus = 1 and FabriEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY FabriNome ASC");
														$result = $conn->query("$sql");
														$rowFabricante = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowFabricante as $item){
															$seleciona = $item['FabriId'] == $row['ServFabricante'] ? "selected" : "";
															print('<option value="'.$item['FabriId'].'" '. $seleciona .'>'.$item['FabriNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
								
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Número de Série</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" placeholder="Número de Série" value="<?php echo $numSerie; ?>">
											</div>
										</div>								
									</div>
								</div>
							</div>
							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
										<a href="servico.php" class="btn btn-basic" role="button">Cancelar</a>
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
