<?php 

function calculaValorProduto($valorProduto, $outrasDespesas = 0, $margemLucro){
   
   $porcentMargemLucro = ($margemLucro / 100);
   $valorProdutoA = floatval(str_replace(',', '.', str_replace('.', '', $valorProduto)));
   
    if($margemLucro != 0.00){
   	    if($outrasDespesas != 0.00){
            $valorProdutoTotal = $valorProdutoA + $outrasDespesas;
            $novoValorVenda = $valorProdutoTotal + ($valorProdutoTotal * $porcentMargemLucro);
 
            $valores['valorVenda'] = round($novoValorVenda, 2);
            $valores['valorTotal'] = $valorProdutoTotal;
        } else {
   	        //$novoValorVenda = floatval($valorProduto) + (floatval($valorProduto) * $porcentMargemLucro);
   	        $valorProdutoTotal = $valorProdutoA + $outrasDespesas;
   	        $novoValorVenda = $valorProdutoTotal + ($valorProdutoTotal * $porcentMargemLucro);
   	        $valores['valorVenda'] = round($novoValorVenda, 2);
   	        $valores['valorTotal'] = $valorProdutoTotal;
        }
   } else {
   	    $valorProdutoTotal = $valorProdutoA + $outrasDespesas;
   	    $valores['valorTotal'] = $valorProdutoTotal;
   }
   return  $valores;
}


include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Operacional Produto';

include('global_assets/php/conexao.php');

//Se veio do fluxo.php
if(isset($_POST['inputFluxoOperacionalId'])){
	$iFluxoOperacional = $_POST['inputFluxoOperacionalId'];
	$iCategoria = $_POST['inputFluxoOperacionalCategoria'];
	$iSubCategoria = $_POST['inputFluxoOperacionalSubCategoria'];
} else if (isset($_POST['inputIdFluxoOperacional'])){
	$iFluxoOperacional = $_POST['inputIdFluxoOperacional'];
	$iCategoria = $_POST['inputIdCategoria'];
	$iSubCategoria = $_POST['inputIdSubCategoria'];
} else {
	irpara("fluxo.php");
}


//Se está alterando
if(isset($_POST['inputIdFluxoOperacional'])){

	$sql = "SELECT ParamId, ParamValorAtualizadoFluxo
	        FROM Parametro
	        WHERE ParamEmpresa = ".$_SESSION["EmpreId"]."
	       ";
	$result = $conn->query($sql);
	$Parametro = $result->fetch(PDO::FETCH_ASSOC);
    // Selecionando dados de parametro.
	
	$sql = "DELETE FROM FluxoOperacionalXProduto
			WHERE FOXPrFluxoOperacional = :iFluxoOperacional AND FOXPrEmpresa = :iEmpresa";
	$result = $conn->prepare($sql);
	
	$result->execute(array(
					':iFluxoOperacional' => $iFluxoOperacional,
					':iEmpresa' => $_SESSION['EmpreId']
					));		
	
	for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
	
		$sql = "INSERT INTO FluxoOperacionalXProduto (FOXPrFluxoOperacional, FOXPrProduto, FOXPrQuantidade, FOXPrValorUnitario, FOXPrUsuarioAtualizador, FOXPrEmpresa)
				VALUES (:iFluxoOperacional, :iProduto, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
						':iFluxoOperacional' => $iFluxoOperacional,
						':iProduto' => $_POST['inputIdProduto'.$i],
						':iQuantidade' => $_POST['inputQuantidade'.$i] == '' ? null : $_POST['inputQuantidade'.$i],
						':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));

		$valorTotal = floatval(str_replace(',', '.', str_replace(',', '.', $_POST['inputValor'])));
		$TotalGeral = floatval(str_replace(',', '.', str_replace('.', '.', $_POST['inputTotalGeral'])));
        
		if($Parametro['ParamValorAtualizadoFluxo'] == 1 && $valorTotal === $TotalGeral){

			$sql = "SELECT *
                    FROM Produto
                    WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduId = ".$_POST['inputIdProduto'.$i]."
                   ";
            $result = $conn->query($sql);
	        $Produto = $result->fetch(PDO::FETCH_ASSOC);
            // Selecionando dados de produto.

			if($Produto['ProduMargemLucro'] != 0.00 ){
			   $valores = calculaValorProduto($_POST['inputValorUnitario'.$i], $Produto['ProduOutrasDespesas'], $Produto['ProduMargemLucro'] );
               //var_dump($Produto['ProduNome'], $custoFinal);

               try{
               	    $sql = "UPDATE Produto SET ProduValorCusto = :pValorUnitario, ProduCustoFinal = :pCustoFinal, ProduValorVenda = :pValorVenda
                        WHERE ProduId = ".$Produto['ProduId']."
		               ";
		            $result = $conn->prepare($sql);
		
		            $conn->beginTransaction();
		            $conn->rollback();		
		            $result->execute(array(
						':pValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						':pCustoFinal' => $valores['valorTotal'],
						':pValorVenda' => $valores['valorVenda'],
						));
               } catch(PDOException $e){
               	    $conn->rollback();
               	    echo 'Error: ' . $e->getMessage();exit;
               }
			} else {
				$valores = calculaValorProduto($_POST['inputValorUnitario'.$i], $Produto['ProduOutrasDespesas'], $Produto['ProduMargemLucro'] );
				try{
               	    $sql = "UPDATE Produto SET ProduValorCusto = :pValorUnitario, ProduCustoFinal = :pCustoFinal
                        WHERE ProduId = ".$Produto['ProduId']."
		               ";
		            $result = $conn->prepare($sql);
		
		            $conn->beginTransaction();
		            $conn->rollback();		
		            $result->execute(array(
						':pValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						':pCustoFinal' => $valores['valorTotal']
						));
               } catch(PDOException $e){
               	    $conn->rollback();
               	    echo 'Error: ' . $e->getMessage();exit;
               }
			}

		    /*$sql = "UPDATE Produto SET
                    WHERE ProduId = 
		            ";
		    $result = $conn->prepare($sql);
		
		    $conn->beginTransaction();		
		
		    $result->execute(array(
						':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
						));
		    */
	    }
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fluxo Operacional alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
	}
}	

try{
	
	$sql = "SELECT FlOpeId, FlOpeNumContrato, ForneId, ForneNome, ForneTelefone, ForneCelular, CategNome, FlOpeCategoria,
			SbCatNome, FlOpeSubCategoria, FlOpeNumProcesso, FlOpeValor
			FROM FluxoOperacional
			JOIN Fornecedor on ForneId = FlOpeFornecedor
			JOIN Categoria on CategId = FlOpeCategoria
			JOIN SubCategoria on SbCatId = FlOpeSubCategoria
			WHERE FlOpeEmpresa = ". $_SESSION['EmpreId'] ." and FlOpeId = ".$iFluxoOperacional;
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	$sql = "SELECT FOXPrProduto
			FROM FluxoOperacionalXProduto
			JOIN Produto on ProduId = FOXPrProduto
			WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
	$result = $conn->query($sql);
	$rowProdutoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countProdutoUtilizado = count($rowProdutoUtilizado);
	
	foreach ($rowProdutoUtilizado as $itemProdutoUtilizado){
		$aProdutos[] = $itemProdutoUtilizado['FOXPrProduto'];
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
	<title>Lamparinas | Listando produtos do Fluxo Operacional</title>

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
					url: "fluxoFiltraProduto.php",
					data: {idCategoria: inputCategoria, produtos: produtos, produtoId: produtoId, produtoQuant: produtoQuant, produtoValor: produtoValor},
					success: function(resposta){
						//alert(resposta);
						$("#tabelaProdutos").html(resposta).show();
						
						return false;
						
					}	
				});
			});
			
			//Valida Registro
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputValor = parseFloat($('#inputValor').val());
				var inputTotalGeral = $('#inputTotalGeral').val().replace('.', '').replace(',', '.');
				
				//Verifica se o valor ultrapassou o total
				if (parseFloat(inputTotalGeral) > parseFloat(inputValor)){
					alerta('Atenção','A soma dos totais ultrapassou o valor do contrato!','error');
					return false;
				}
				
				$( "#formFluxoOperacionalProduto" ).submit();
				
			}); // enviar			
						
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
					
					<form name="formFluxoOperacionalProduto" id="formFluxoOperacionalProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Listar Produtos - Fluxo Operacional Nº Contrato "<?php echo $row['FlOpeNumContrato']; ?>"</h5>
						</div>					
						
						<input type="hidden" id="inputIdFluxoOperacional" name="inputIdFluxoOperacional" class="form-control" value="<?php echo $row['FlOpeId']; ?>">
						
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
												<label for="inputTelefone">Telefone</label>
												<input type="text" id="inputTelefone" name="inputTelefone" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCelular">Celular</label>
												<input type="text" id="inputCelular" name="inputCelular" class="form-control" value="<?php echo $row['ForneCelular']; ?>" readOnly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCategoriaNome">Categoria</label>
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" value="<?php echo $row['CategNome']; ?>" readOnly>
												<input type="hidden" id="inputIdCategoria" name="inputIdCategoria" class="form-control" value="<?php echo $row['FlOpeCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputCategoriaNome">SubCategoria</label>
												<input type="text" id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control" value="<?php echo $row['SbCatNome']; ?>" readOnly>
												<input type="hidden" id="inputIdSubCategoria" name="inputIdSubCategoria" class="form-control" value="<?php echo $row['FlOpeSubCategoria']; ?>">
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputContrato">Contrato</label>
												<input type="text" id="inputContrato" name="inputContrato" class="form-control" value="<?php echo $row['FlOpeNumContrato']; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputProcesso">Processo</label>
												<input type="text" id="inputProcesso" name="inputProcesso" class="form-control" value="<?php echo $row['FlOpeNumProcesso']; ?>" readOnly>
											</div>
										</div>	
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValor">Valor Total</label>
												<input type="text" id="inputValor" name="inputValor" class="form-control" value="<?php echo $row['FlOpeValor']; ?>" readOnly>
											</div>
										</div>											
									</div>
									
									<div class="row">	
										<div class="col-lg-12">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control multiselect-filtering" multiple="multiple" data-fouc <?php if ($countProdutoUtilizado and $_SESSION['PerfiChave'] != 'SUPER' and $_SESSION['PerfiChave'] != 'CONTROLADORIA') { echo "disabled";} ?> >
													<?php 
														$sql = "SELECT ProduId, ProduNome
																FROM Produto										     
																WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduStatus = 1 and ProduCategoria = ".$iCategoria."
																ORDER BY ProduNome ASC";
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
									<p class="mb-3">Abaixo estão listados todos os produtos selecionados acima. Para atualizar os valores, basta preencher as colunas <code>Quantidade</code> e <code>Valor Unitário</code> e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php									

										$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla, FOXPrQuantidade, FOXPrValorUnitario, MarcaNome
												FROM Produto
												JOIN FluxoOperacionalXProduto on FOXPrProduto = ProduId
												LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
												LEFT JOIN Marca on MarcaId = ProduMarca
												WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and FOXPrFluxoOperacional = ".$iFluxoOperacional;
										$result = $conn->query($sql);
										$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowProdutos);
										
										if (!$count){
											$sql = "SELECT ProduId, ProduNome, ProduDetalhamento, UnMedSigla
													FROM Produto
													LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
													WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and ProduCategoria = ".$iCategoria." and ProduSubCategoria = ".$iSubCategoria." and ProduStatus = 1 ";
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
														<div class="col-lg-8">
															<label for="inputProduto"><strong>Produto</strong></label>
														</div>
														<div class="col-lg-3">
															<label for="inputMarca"><strong>Marca</strong></label>
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
											
											$iQuantidade = isset($item['FOXPrQuantidade']) ? $item['FOXPrQuantidade'] : '';
											$fValorUnitario = isset($item['FOXPrValorUnitario']) ? mostraValor($item['FOXPrValorUnitario']) : '';											
											$fValorTotal = (isset($item['FOXPrQuantidade']) and isset($item['FOXPrValorUnitario'])) ? mostraValor($item['FOXPrQuantidade'] * $item['FOXPrValorUnitario']) : '';
											
											$fTotalGeral += (isset($item['FOXPrQuantidade']) and isset($item['FOXPrValorUnitario'])) ? $item['FOXPrQuantidade'] * $item['FOXPrValorUnitario'] : 0;
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-8">
													<div class="row">
														<div class="col-lg-1">
															<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
															<input type="hidden" id="inputIdProduto'.$cont.'" name="inputIdProduto'.$cont.'" value="'.$item['ProduId'].'" class="idProduto">
														</div>
														<div class="col-lg-8">
															<input type="text" id="inputProduto'.$cont.'" name="inputProduto'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ProduDetalhamento'].'" value="'.$item['ProduNome'].'" readOnly>
														</div>
														<div class="col-lg-3">
															<input type="text" id="inputMarca'.$cont.'" name="inputMarca'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['MarcaNome'].'" value="'.$item['MarcaNome'].'" readOnly>
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
										<?php 
										
											if ($countProdutoUtilizado){
												if ($_SESSION['PerfiChave'] == 'SUPER' or $_SESSION['PerfiChave'] == 'CONTROLADORIA'){
													print('<button class="btn btn-lg btn-success" id="enviar">Alterar</button>');
												}
											} else{ 
												print('<button class="btn btn-lg btn-success" id="enviar">Alterar</button>');
											} 
										
										?>
										<a href="fluxo.php" class="btn btn-basic" role="button">Cancelar</a>
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
