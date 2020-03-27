<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra Serviço';

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
	
	$sql = "DELETE FROM OrdemCompraXServico
			WHERE OCXSrOrdemCompra = :iOrdemCompra AND OCXSrEmpresa = :iEmpresa";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':iOrdemCompra' => $iOrdemCompra,
					':iEmpresa' => $_SESSION['EmpreId']
					));		
	
	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
	
		$sql = "INSERT INTO OrdemCompraXServico (OCXSrOrdemCompra, OCXSrServico, OCXSrQuantidade, OCXSrValorUnitario, OCXSrUsuarioAtualizador, OCXSrEmpresa)
				VALUES (:iOrdemCompra, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iOrdemCompra' => $iOrdemCompra,
						':iServico' => $_POST['inputIdServico'.$i],
						':iQuantidade' => $_POST['inputQuantidade'.$i] == '' ? null : $_POST['inputQuantidade'.$i],
						':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
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
			WHERE OrComEmpresa = ". $_SESSION['EmpreId'] ." and OrComId = ".$iOrdemCompra;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	
	$sql = "SELECT OCXSrServico
			FROM OrdemCompraXServico
			JOIN Servico on ServiId = OCXSrServico
			WHERE ServiEmpresa = ". $_SESSION['EmpreId'] ." and ServiCategoria = ".$iCategoria." and OCXSrOrdemCompra = ".$row['OrComId']."";
	
	if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
		$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
	}	
	$result = $conn->query($sql);
	$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado = count($rowServicoUtilizado);
	
	foreach ($rowServicoUtilizado as $itemServicoUtilizado){
		$aServicos[] = $itemServicoUtilizado['OCXSrServico'];
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
	<title>Lamparinas | Listando serviços da Ordem de Compra</title>

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

			//Ao mudar a SubCategoria, filtra o servico via ajax (retorno via JSON)
			$('#cmbServico').on('change', function(e){
				
				var inputCategoria = $('#inputIdCategoria').val();
				var inputSubCategoria = $('#inputIdSubCategoria').val(); //alert(inputSubCategoria);
				var servicos = $(this).val();
				//console.log(servicos);
				
				var cont = 1;
				var servicoId = [];
				var servicoQuant = [];
				var servicoValor = [];
				
				// Aqui é para cada "class" faça
				$.each( $(".idServico"), function() {			
					servicoId[cont] = $(this).val();
					cont++;
				});
				
				cont = 1;
				//aqui fazer um for que vai até o ultimo cont (dando cont++ dentro do for)
				$.each( $(".Quantidade"), function() {
					$id = servicoId[cont];
					
					servicoQuant[$id] = $(this).val();
					cont++;
				});				
				
				cont = 1;
				$.each( $(".ValorUnitario"), function() {
					$id = servicoId[cont];
					
					servicoValor[$id] = $(this).val();
					cont++;
				});
				
				$.ajax({
					type: "POST",
					url: "ordemcompraFiltraServico.php",
					data: {idCategoria: inputCategoria, idSubCategoria: inputSubCategoria, servicos: servicos, servicoId: servicoId, servicoQuant: servicoQuant, servicoValor: servicoValor},
					success: function(resposta){
						//alert(resposta);
						$("#tabelaServicos").html(resposta).show();
						
						return false;
						
					}	
				});
			});
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo Servico
		function FiltraServico(){
			$('#cmbServico').empty().append('<option value="">Filtrando...</option>');
		}
		
		function ResetServico(){
			$('#cmbServico').empty().append('<option value="">Sem serviço</option>');
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
					
					<form name="formOrdemCompraServico" id="formOrdemCompraServico" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Serviços - Ordem de Compra Nº "<?php echo $row['OrComNumero']; ?>"</h5>
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
												<label for="cmbServico">Serviço</label>
												<select id="cmbServico" name="cmbServico" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT ServiId, ServiNome
																FROM Servico
																JOIN Situacao on SituaId = ServiStatus										     
																WHERE ServiEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'ATIVO' and 
																ServiCategoria = ".$iCategoria;
														
														if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
															$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
														}
														
														$sql .= " ORDER BY ServiNome ASC";
														$result = $conn->query($sql);
														$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);														
														
														foreach ($rowServico as $item){	
															
															if (in_array($item['ServiId'], $aServicos) or $countServicoUtilizado == 0) {
																$seleciona = "selected";
															} else {
																$seleciona = "";
															}													
															
															print('<option value="'.$item['ServiId'].'" '.$seleciona.'>'.$item['ServiNome'].'</option>');
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
									<h5 class="card-title">Relação de Serviços</h5>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
											<a class="list-icons-item" data-action="reload"></a>
											<a class="list-icons-item" data-action="remove"></a>
										</div>
									</div>
								</div>

								<div class="card-body">
									<p class="mb-3">Abaixo estão listados todos os serviços da Categoria e SubCategoria selecionadas logo acima. Para atualizar os valores, basta preencher as colunas <code>Quantidade</code> e <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php									

										$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, OCXSrQuantidade, OCXSrValorUnitario
												FROM Servico
												JOIN OrdemCompraXServico on OCXSrServico = ServiId
												WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and OCXSrOrdemCompra = ".$iOrdemCompra;
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowServicos);
										
										if (!$count){
											$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
													FROM Servico
													JOIN Situacao on SituaId = ServiStatus
													WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and ServiCategoria = ".$iCategoria." and 
													SituaChave = 'ATIVO' ";
													
											if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
												$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
											}
											$result = $conn->query($sql);
											$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
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
														<label for="inputServico"><strong>Servico</strong></label>
													</div>
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
										
										print('<div id="tabelaServicos">');
										
										$fTotalGeral = 0;
										
										foreach ($rowServicos as $item){
											
											$cont++;
											
											$iQuantidade = isset($item['OCXSrQuantidade']) ? $item['OCXSrQuantidade'] : '';
											$fValorUnitario = isset($item['OCXSrValorUnitario']) ? mostraValor($item['OCXSrValorUnitario']) : '';											
											$fValorTotal = (isset($item['OCXSrQuantidade']) and isset($item['OCXSrValorUnitario'])) ? mostraValor($item['OCXSrQuantidade'] * $item['OCXSrValorUnitario']) : '';
											
											$fTotalGeral += (isset($item['OCXSrQuantidade']) and isset($item['OCXSrValorUnitario'])) ? $item['OCXSrQuantidade'] * $item['OCXSrValorUnitario'] : 0;
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
															<input type="hidden" id="inputIdServico'.$cont.'" name="inputIdServico'.$cont.'" value="'.$item['ServiId'].'" class="idServico">
														</div>
														<div class="col-lg-11">
															<input type="text" id="inputServico'.$cont.'" name="inputServico'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ServiDetalhamento'].'" value="'.$item['ServiNome'].'" readOnly>
														</div>
													</div>
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
												<div class="col-lg-9">
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
