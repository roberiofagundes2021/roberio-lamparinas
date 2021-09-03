<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra Serviço';

include('global_assets/php/conexao.php');

//Se veio do ordemcompra.php
if(isset($_POST['inputOrdemCompraId'])){
	$iOrdemCompra = $_POST['inputOrdemCompraId'];
	$iFluxo = $_POST['inputOrdemCompraFlOpeId'];
	$iCategoria = $_POST['inputOrdemCompraCategoria'];
} else if (isset($_POST['inputIdOrdemCompra'])){
	$iOrdemCompra = $_POST['inputIdOrdemCompra'];
	$iFluxo = $_POST['inputOrdemCompraFlOpeId'];
	$iCategoria = $_POST['inputIdCategoria'];
} else {
	irpara("ordemcompra.php");
}

//Se está alterando
if(isset($_POST['inputIdOrdemCompra'])){
	$valid = true;

	if($_POST['inputTypeRequest'] != "reset"){
		for ($i = 1; $i <= $_POST['totalRegistros']; $i++){
			$sqlSaldo = "SELECT OCXSrOrdemCompra, OCXSrQuantidade,
			dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iFluxo', ".$_POST['inputIdServico'.$i].", 'S') as Saldo
			FROM OrdemCompraXServico WHERE OCXSrOrdemCompra = '$iOrdemCompra' and OCXSrServico = ".$_POST['inputIdServico'.$i];
			$resultSaldo = $conn->query($sqlSaldo);
			$OCXSe = $resultSaldo->fetch(PDO::FETCH_ASSOC);

			if(!isset($OCXSe['Saldo'])){
				$sqlSaldo = "SELECT dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iFluxo', ".$_POST['inputIdServico'.$i].", 'S') as Saldo";
				$resultSaldo = $conn->query($sqlSaldo);
				$OCXSe = $resultSaldo->fetch(PDO::FETCH_ASSOC);
			}

			$saldo = isset($OCXSe['Saldo'])?intval($OCXSe['Saldo']):0;
			$ordComQuant = isset($OCXSe['OCXSrQuantidade'])?intval($OCXSe['OCXSrQuantidade']):0;
			$quant = isset($_POST['inputQuantidade'.$i])?intval($_POST['inputQuantidade'.$i]):0;

			$saldo = $saldo != null?$saldo:0;
			$ordComQuant = $ordComQuant != null?$ordComQuant:0;
			$quant = $quant != null?$quant:0;

			if($quant > $saldo+$ordComQuant){
				$valid = false;
			}
		}
	}
		
	if($valid){
		$sql = "DELETE FROM OrdemCompraXServico
				WHERE OCXSrOrdemCompra = :iOrdemCompra AND OCXSrUnidade = :iUnidade";
		$result = $conn->prepare($sql);
		
		$result->execute(array(
			':iOrdemCompra' => $iOrdemCompra,
			':iUnidade' => $_SESSION['UnidadeId']
		));
		
		for ($i = 1; $i <= $_POST['totalRegistros']; $i++) {
			
			$sql = "INSERT INTO OrdemCompraXServico (OCXSrOrdemCompra, OCXSrServico, OCXSrQuantidade, OCXSrValorUnitario, OCXSrUsuarioAtualizador, OCXSrUnidade)
					VALUES (:iOrdemCompra, :iServico, :iQuantidade, :fValorUnitario, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);
			
			$result->execute(array(
				':iOrdemCompra' => $iOrdemCompra,
				':iServico' => $_POST['inputIdServico'.$i],
				':iQuantidade' => $_POST['inputQuantidade'.$i] == ''? null : $_POST['inputQuantidade'.$i],
				':fValorUnitario' => $_POST['inputValorUnitario'.$i] == '' ? null : gravaValor($_POST['inputValorUnitario'.$i]),
				':iUsuarioAtualizador' => $_SESSION['UsuarId'],
				':iUnidade' => $_SESSION['UnidadeId']
			));
			
			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "Ordem de Compra alterado!!!";
			$_SESSION['msg']['tipo'] = "success";
		}
	}else{
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "O saldo de um dos serviços não está mais disponível!!!";
		$_SESSION['msg']['tipo'] = "error";
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
	
	
	$sql = "SELECT OCXSrServico
			FROM OrdemCompraXServico
			JOIN Servico on ServiId = OCXSrServico
			WHERE ServiUnidade = ". $_SESSION['UnidadeId'] ." and ServiCategoria = ".$iCategoria." and OCXSrOrdemCompra = ".$row['OrComId']."";
	
	if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
		$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
	}	
	$result = $conn->query($sql);
	$rowServicoUtilizado = $result->fetchAll(PDO::FETCH_ASSOC);
	$countServicoUtilizado = count($rowServicoUtilizado);
	
	foreach ($rowServicoUtilizado as $itemServicoUtilizado){
		$aServicos[] = $itemServicoUtilizado['OCXSrServico'];
	}

	$sql = "SELECT COUNT(OCXSrServico) as Quant
			FROM OrdemCompraXServico
			WHERE OCXSrUnidade = ". $_SESSION['UnidadeId'] ." and OCXSrOrdemCompra = ".$iOrdemCompra." and 
			OCXSrQuantidade <> '' and OCXSrQuantidade <> 0 and OCXSrValorUnitario <> 0.00 ";
	$result = $conn->query($sql);
	$rowCompleto = $result->fetch(PDO::FETCH_ASSOC);

	$enviar = 0;

	//Verifica se o número de serviços é igual ao número de serviços com a quantidade e valor unitário preenchido para habilitar o botào "Enviar"
	if ($countServicoUtilizado == $rowCompleto['Quant'] && ($countServicoUtilizado != 0 && $rowCompleto['Quant'] != 0)){
		$enviar = 1;
	}	
	
} catch(PDOException $e) {
	echo 'Error: ' . $e->getMessage();die;
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
			$('.pula').keypress(function(e){
				/*
					* verifica se o evento é Keycode (para IE e outros browsers)
					* se não for pega o evento Which (Firefox)
				*/
				var tecla = (e.keyCode?e.keyCode:e.which);

				/* verifica se a tecla pressionada foi o ENTER */
				if(tecla == 13){
					/* guarda o seletor do campo que foi pressionado Enter */
					campo =  $('.pula');
					/* pega o indice do elemento*/
					indice = campo.index(this);
					/*soma mais um ao indice e verifica se não é null
					*se não for é porque existe outro elemento
					*/
					if(campo[indice+1] != null){
						/* adiciona mais 1 no valor do indice */
						proximo = campo[indice + 1];
						/* passa o foco para o proximo elemento */
						proximo.focus();
					}
				} else {
					return onlynumber(e);
				}

				/* impede o sumbit caso esteja dentro de um form */
				e.preventDefault(e);
				return false;
			});

			//Ao mudar a SubCategoria, filtra o servico via ajax (retorno via JSON)
			$('#cmbServico').on('change', function(e){
				
				var inputCategoria = $('#inputIdCategoria').val();
				var inputSubCategoria = $('#inputIdSubCategoria').val(); //alert(inputSubCategoria);
				var servicos = $(this).val();
				<?php 
					echo "var iFluxoOp = ".json_encode($iFluxo)."\n";
					echo "var iOrdemCompra = ".json_encode($iOrdemCompra)."\n";
				?>
				
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
					data: {idCategoria: inputCategoria, idSubCategoria: inputSubCategoria, servicos: servicos, servicoId: servicoId, servicoQuant: servicoQuant, servicoValor: servicoValor, iOrdemCompra: iOrdemCompra, iFluxoOp: iFluxoOp},
					success: function(resposta){

						$("#tabelaServicos").html(resposta).show();
						
						return false;
						
					}	
				});
			});

			//Enviar para aprovação do Centro Administrativo (via Bandeja)
			// $('#enviar').on('click', function(e){
				
			// 	e.preventDefault();		
				
			// 	confirmaExclusao(document.formOrdemCompraServico, "Essa ação enviará toda a Ordem de Compra (com seus produtos e serviços) para aprovação do Centro Administrativo. Tem certeza que deseja enviar?", "ordemcompraEnviar.php");
			// });			
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo Servico
		function FiltraServico(){
			$('#cmbServico').empty().append('<option value="">Filtrando...</option>');
		}

		function reset(id, type){
			if (type == "all"){
				var array = [];
				for(var x=1; x<=id; x++){
					array.push("inputQuantidade"+x)
				}
				confirmaReset(document.formOrdemCompraServico, "Tem certeza que deseja resetar TODAS as quantidades ?", "ordemcompraServico.php", array);
			}else{
				confirmaReset(document.formOrdemCompraServico, "Tem certeza que deseja resetar essa quantidade ?", "ordemcompraServico.php", id);
			}
		}

		function validaQuantInputModal(quantMax,quant,obj) {
			$('#'+obj.id).on('keyup', function() {
					if (parseInt($('#'+obj.id).val()) > (parseInt(quantMax)+parseInt(quant))) {
						$('#'+obj.id).val(parseInt(quantMax)+parseInt(quant))
					}
				})
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
							<h5 class="text-uppercase font-weight-bold">Listar Serviços - <?php echo $row['OrComTipo'] == 'C' ? 'Carta Contrato' : 'Ordem de Compra'; ?> Nº "<?php echo $row['OrComNumero']; ?>"</h5>
						</div>					
						<input type="hidden" id="inputTypeRequest" name="inputTypeRequest" value="submit">
						<input type="hidden" id="inputIdOrdemCompra" name="inputIdOrdemCompra" class="form-control" value="<?php echo $row['OrComId']; ?>">
						<input type="hidden" id="inputOrdemCompraFlOpeId" name="inputOrdemCompraFlOpeId" class="form-control" value="<?php echo $iFluxo; ?>">
						
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
																JOIN FluxoOperacionalXServico on FOXSrServico = ServiId and FOXSrFluxoOperacional = '$iFluxo'
																WHERE ServiUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave = 'ATIVO' and 
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
									<p class="mb-3">Abaixo estão listados todos os serviços da Categoria e SubCategoria selecionadas logo acima. Para atualizar os valores, basta preencher a coluna <code>Quantidade</code>  e depois clicar em <b>ALTERAR</b>.</p>

									<!--<div class="hot-container">
										<div id="example"></div>
									</div>-->
									
									<?php

										$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrValorUnitario, OCXSrQuantidade,
														dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iFluxo', ServiId, 'S') as SaldoOrdemCompra
														FROM Servico
														JOIN Situacao on SituaId = ServiStatus
														JOIN OrdemCompraXServico on OCXSrServico = ServiId and OCXSrOrdemCompra = '$iOrdemCompra'
														JOIN FluxoOperacionalXServico on FOXSrServico = ServiId and FOXSrFluxoOperacional = '$iFluxo'
														WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and ServiCategoria = ".$iCategoria."and SituaChave='ATIVO'";
										if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
											$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
										}
										$sql = $sql." ORDER BY ServiNome";
										$result = $conn->query($sql);
										$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
										$count = count($rowServicos);

										if(!$count>0){
											$sql = "SELECT ServiId, ServiNome, ServiDetalhamento, FOXSrValorUnitario,
														dbo.fnSaldoOrdemCompra($_SESSION[UnidadeId], '$iFluxo', ServiId, 'S') as SaldoOrdemCompra
														FROM Servico
														JOIN Situacao on SituaId = ServiStatus
														JOIN FluxoOperacionalXServico on FOXSrServico = ServiId and FOXSrFluxoOperacional = '$iFluxo'
														WHERE ServiUnidade = ".$_SESSION['UnidadeId']." and ServiCategoria = ".$iCategoria."and SituaChave='ATIVO'";
											if (isset($row['OrComSubCategoria']) and $row['OrComSubCategoria'] != '' and $row['OrComSubCategoria'] != null){
												$sql .= " and ServiSubCategoria = ".$row['OrComSubCategoria'];
											}
											$sql = $sql." ORDER BY ServiNome";
											$result = $conn->query($sql);
											$rowServicos = $result->fetchAll(PDO::FETCH_ASSOC);
											$count = count($rowServicos);
										}
										
										$cont = 0;
										
										print('
										<div class="row" style="margin-bottom: -20px;">
											<div class="col-lg-6">
												<div class="row">
													<div class="col-lg-2" style="max-width:60px">
														<label for="inputCodigo"><strong>Item</strong></label>
													</div>
													<div class="col-lg-10" style="width:100%">
														<label for="inputServico"><strong>Servico</strong></label>
													</div>
												</div>
											</div>
											<div class="col-lg-1">
												<div class="form-group">
													<label for="inputSaldo"><strong>Saldo</strong></label>
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
											<div class="col-lg-2">
												<div class="form-group">
													<label for="inputValorTotal"><strong>Valor Total</strong></label>
												</div>
											</div>
											<div class="col-sm-1">
												<div class="form-group">
													<label for=""><strong>Resetar</strong></label>
												</div>
											</div>
										</div>');
										
										print('<div id="tabelaServicos">');
										
										$fTotalGeral = 0;
										
										foreach ($rowServicos as $item){
											
											$cont++;
											
											$saldo = isset($item['SaldoOrdemCompra']) ? $item['SaldoOrdemCompra'] : 0;
											$iQuantidade = isset($item['OCXSrQuantidade']) ? $item['OCXSrQuantidade'] : 0;
											$fValorUnitario = isset($item['FOXSrValorUnitario']) ? mostraValor($item['FOXSrValorUnitario']) : 0;											
											$fValorTotal = mostraValor(intval($iQuantidade)*gravaValor($fValorUnitario));
											
											$fTotalGeral += gravaValor($fValorTotal);
											
											print('
											<div class="row" style="margin-top: 8px;">
												<div class="col-lg-6">
													<div class="row">
														<div class="col-lg-2" style="max-width:60px">
															<input type="text" id="inputItem'.$cont.'" name="inputItem'.$cont.'" class="form-control-border-off" value="'.$cont.'" readOnly>
															<input type="hidden" id="inputIdServico'.$cont.'" name="inputIdServico'.$cont.'" value="'.$item['ServiId'].'" class="idServico">
														</div>
														<div class="col-lg-10" style="width:100%">
															<input type="text" id="inputServico'.$cont.'" name="inputServico'.$cont.'" class="form-control-border-off" data-popup="tooltip" title="'.$item['ServiDetalhamento'].'" value="'.$item['ServiNome'].'" readOnly>
														</div>
													</div>
												</div>
												<div class="col-lg-1">
													<input type="text" id="inputSaldo'.$cont.'" readOnly name="Saldo'.$cont.'" class="form-control-border-off text-right" value="'.$saldo.'">
												</div>
												<div class="col-lg-1">
													<input type="text" id="inputQuantidade'.$cont.'" '.($saldo > 0?'':'').' name="inputQuantidade'.$cont.'" onkeypress="validaQuantInputModal('.$saldo.','.$iQuantidade.',this)" class="form-control-border Quantidade text-right pula" onChange="calculaValorTotal('.$cont.')" onkeypress="return onlynumber();" value="'.$iQuantidade.'">
												</div>	
												<div class="col-lg-1">
													<input readOnly type="text" id="inputValorUnitario'.$cont.'" name="inputValorUnitario'.$cont.'" class="form-control-border-off ValorUnitario text-right" onChange="calculaValorTotal('.$cont.')" onKeyUp="moeda(this)" maxLength="12" value="'.$fValorUnitario.'">
												</div>	
												<div class="col-lg-2">
													<input type="text" id="inputValorTotal'.$cont.'" name="inputValorTotal'.$cont.'" class="form-control-border-off text-right" value="'.$fValorTotal.'" readOnly>
												</div>
												<div class="col-lg-1 btn" style="text-align:center;" onClick="reset(`inputQuantidade'.$cont.'`, 0)">
													<i class="icon-reset" title="Resetar"></i>
												</div>
											</div>');											
											
										}
										
										print('<div class="row" style="margin-top: 8px;">
										<div class="col-lg-5">
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
										<div class="col-lg-2" style="padding-top: 5px; text-align: right;">
											<h3><b>Total:</b></h3>
										</div>	
										<div class="col-lg-2">
											<input type="text" id="inputTotalGeral" name="inputTotalGeral" class="form-control-border-off text-right" value="'.mostraValor($fTotalGeral).'" readOnly>
										</div>
										<div class="col-lg-1 btn" style="text-align:center;" onClick="reset('.count($rowServicos).',`all`)">
											<i class="icon-reset" title="Resetar"></i>
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
										<button class="btn btn-lg btn-principal" type="submit">Alterar</button>
										<?php
											// if ($enviar){
											// 	print('<button class="btn btn-lg btn-default" id="enviar">Enviar para Aprovação</button>');
											// }
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
