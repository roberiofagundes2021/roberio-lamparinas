<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Orçamento Serviço';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if(isset($_POST['inputOrcamentoId'])){
	$iOrcamento = $_POST['inputOrcamentoId'];
	$iCategoria = $_POST['inputOrcamentoCategoria'];
} else if (isset($_POST['inputIdOrcamento'])){
	$iOrcamento = $_POST['inputIdOrcamento'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("orcamento.php");
}

//Se está alterando
if(isset($_POST['inputIdOrcamento'])){
	
	$sql = "DELETE FROM OrcamentoXServico
			WHERE OrXSvOrcamento = :iOrcamento AND OrXSvEmpresa = :iEmpresa";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':iOrcamento' => $iOrcamento,
					':iEmpresa' => $_SESSION['EmpreId']
					));		
	
	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
	
		$sql = "INSERT INTO OrcamentoXServico (OrXSvOrcamento, OrXSvServico, OrXSvUsuarioAtualizador, OrXSvEmpresa)
				VALUES (:iOrcamento, :iServico, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iOrcamento' => $iOrcamento,
						':iServico' => $_POST['inputIdServico'.$i],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Orçamento alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}	

try{
	
	$sql = "SELECT *
			FROM Orcamento
			LEFT JOIN Fornecedor on ForneId = OrcamFornecedor
			JOIN Categoria on CategId = OrcamCategoria
			WHERE OrcamEmpresa = ". $_SESSION['EmpreId'] ." and OrcamId = $iOrcamento ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	
	$sql = "SELECT OrXSvServico
			FROM OrcamentoXServico
			JOIN Servico on ServiId = OrXSvServico
			WHERE ServiEmpresa = ". $_SESSION['EmpreId'] ." and OrXSvOrcamento = ".$iOrcamento;	
	$result = $conn->query($sql);
	$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado = count($rowServicoUtilizado);
	
	foreach ($rowServicoUtilizado as $itemServicoUtilizado){
		$aServicos[] = $itemServicoUtilizado['OrXSvServico'];
	}

	$sql = "SELECT SbCatId, SbCatNome
			FROM SubCategoria
			JOIN OrcamentoXSubCategoria on OrXSCSubCategoria = SbCatId
			WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and OrXSCOrcamento = $iOrcamento
			ORDER BY SbCatNome ASC";
	$result = $conn->query($sql);
	$rowBD = $result->fetchAll(PDO::FETCH_ASSOC);
	
	foreach ($rowBD as $item){
		$aSubCategorias[] = $item['SbCatId'];
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
	<title>Lamparinas | Listando serviços do Orçamento</title>

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

			//Ao mudar a SubCategoria, filtra o serviço via ajax (retorno via JSON)
			$('#cmbServico').on('change', function(e){
				
				var inputCategoria = $('#inputIdCategoria').val();
				var inputSubCategoria = $('#inputIdSubCategoria').val(); //alert(inputSubCategoria);
				var servico = $(this).val();
				//console.log(serviço);
				
				var cont = 1;
				var servicoId = [];
				
				// Aqui é para cada "class" faça
				$.each( $(".idServico"), function() {		
					servicoId[cont] = $(this).val();
					cont++;
				});
				
				cont = 1;
				
				$.ajax({
					type: "POST",
					url: "orcamentoFiltraServico.php",
					data: {idCategoria: inputCategoria, idSubCategoria: inputSubCategoria, servico: servico, servicoId: servicoId},
					success: function(resposta){
						//alert(resposta);
						$("#tabelaServicos").html(resposta).show();
						
						return false;
						
					}	
				});
			});
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo Serviço
		function FiltraServico(){
			$('#cmbServico').empty().append('<option>Filtrando...</option>');
		}
		
		function ResetServico(){
			$('#cmbServico').empty().append('<option>Sem servico</option>');
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
					
					<form name="formOrcamentoServico" id="formOrcamentoServico" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Serviços - Orçamento Nº "<?php echo $row['OrcamNumero']; ?>"</h5>
						</div>					
						
						<input type="hidden" id="inputIdOrcamento" name="inputIdOrcamento" class="form-control" value="<?php echo $row['OrcamId']; ?>">
						
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
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['OrcamCategoria']; ?>">
											</div>
										</div>
									
										<div class="col-lg-6">
											<div class="form-group" style="border-bottom:1px solid #ddd;">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria[]" class="form-control form-control-select2" multiple="multiple" data-fouc>
													<!--<option value="#">Selecione uma subcategoria</option>-->
													<?php
												        if (isset($row['OrcamCategoria'])){
													        $sql = "SELECT SbCatId, SbCatNome
															    	FROM SubCategoria
															    	JOIN Situacao on SituaId = SbCatStatus														 
															     	WHERE SbCatEmpresa = ". $_SESSION['EmpreId'] ." and SbCatCategoria = ".$row['OrcamCategoria']." and SituaChave = 'ATIVO'
															     	ORDER BY SbCatNome ASC";
													        $result = $conn->query($sql);
													        $rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													        $count = count($rowSubCategoria);

														    foreach ($rowSubCategoria as $item){
															    $seleciona = in_array($item['SbCatId'], $aSubCategorias) ? "selected" : "";
															    print('<option value="'.$item['SbCatId'].'" '. $seleciona .'>'.$item['SbCatNome'].'</option>');
														    }
													        
												        }
											        ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbServico">Serviços</label>
												<select id="cmbServico" name="cmbServico" class="form-control multiselect-filtering" multiple="multiple" data-fouc>
													<?php 
														$sql = "SELECT ServiId, ServiNome
																FROM Servico										     
																WHERE ServiEmpresa = ". $_SESSION['EmpreId'] ." and ServiStatus = 1 and ServiCategoria = ".$iCategoria;
														
														if (isset($row['OrcamSubCategoria']) and $row['OrcamSubCategoria'] != '' and $row['OrcamSubCategoria'] != null){
															$sql .= " and ServiSubCategoria = ".$row['OrcamSubCategoria'];
														}
														
														$sql .= " ORDER BY ServiNome ASC";
														$result = $conn->query($sql);
														$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);														
														
														foreach ($rowServicos as $item){	
															
															if (in_array($item['ServiId'], $aServicos) or $countServicoUtilizado == 0) {
																$seleciona = "selected";
																print('<option value="'.$item['ServiId'].'" '.$seleciona.'>'.$item['ServiNome'].'</option>');
															} else {
																$seleciona = "";
																print('<option value="'.$item['ServiId'].'" '.$seleciona.'>'.$item['ServiNome'].'</option>');
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
									<p class="mb-3">Abaixo estão listados todos os serviços da Categoria e SubCategoria selecionadas logo acima.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php									

										$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
												FROM Servico
												JOIN OrcamentoXServico on OrXSvServico = ServiId
												WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and OrXSvOrcamento = ".$iOrcamento;
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowServicos);
										
										if (!$count){
											$sql = "SELECT ServiId, ServiNome, ServiDetalhamento
													FROM Servico
													WHERE ServiEmpresa = ".$_SESSION['EmpreId']." and ServiCategoria = ".$iCategoria." and ServiStatus = 1 
													ORDER BY ServiNome ASC
													";
													
											if (isset($row['OrcamSubCategoria']) and $row['OrcamSubCategoria'] != '' and $row['OrcamSubCategoria'] != null){
												$sql .= " and ServiSubCategoria = ".$row['OrcamSubCategoria'];
											}
											$result = $conn->query($sql);
											$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										}
										
										$cont = 0;
										
										print('
										    <div class="row" style="margin-bottom: -20px;">
											    <div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<label for="inputCodigo"><strong>Item</strong></label>
														</div>
														<div class="col-lg-10" style="padding-left: 41px">
															<label for="inputServico"><strong>Serviço</strong></label>
														</div>
													</div>
												</div>																					
										    </div>');
										
										print('<div id="tabelaServicos">');
										
										$fTotalGeral = 0;
										
										foreach ($rowServicos as $item){
											
											$cont++;
											
											//$iQuantidade = isset($item['OrXPrQuantidade']) ? $item['OrXPrQuantidade'] : '';
											//$fValorUnitario = isset($item['OrXPrValorUnitario']) ? mostraValor($item['OrXPrValorUnitario']) : '';											
											//$fValorTotal = (isset($item['OrXPrQuantidade']) and isset($item['OrXPrValorUnitario'])) ? mostraValor($item['OrXPrQuantidade'] * $item['OrXPrValorUnitario']) : '';
											
											//$fTotalGeral += (isset($item['OrXPrQuantidade']) and isset($item['OrXPrValorUnitario'])) ? $item['OrXPrQuantidade'] * $item['OrXPrValorUnitario'] : 0;
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-12">
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
	
	<?php include_once("alerta.php"); ?>

</body>
</html>
