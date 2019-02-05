<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Movimentação';

include('global_assets/php/conexao.php');

if(isset($_POST['inputData'])){
		
	try{
		
		if($_POST['cmbMotivo'] != '#'){
			$aMotivo = explode("#",$_POST['cmbMotivo']);
			$iMotivo = $aMotivo[0];
		} else{
			$iMotivo = null;
		}
		
		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimClassificacao, MovimMotivo, MovimData, MovimFinalidade, MovimOrigem, MovimDestinoLocal, MovimDestinoSetor, MovimDestinoManual, 
										  MovimObservacao, MovimFornecedor, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie, MovimValorTotal, 
										  MovimChaveAcesso, MovimSituacao, MovimUsuarioAtualizador, MovimEmpresa)
				VALUES (:sTipo, :iClassificacao, :iMotivo, :dData, :iFinalidade, :iOrigem, :iDestinoLocal, :iDestinoSetor, :iDestinoManual, 
						:sObservacao, :iFornecedor, :iOrdemCompra, :sNotaFiscal, :dDataEmissao, :sNumSerie, :fValorTotal, 
						:sChaveAcesso, :iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);	

/*		echo $sql;
		echo "<br>";
		var_dump($_POST['inputTipo'], gravaData($_POST['inputData']), $_POST['cmbFinalidade'], $_POST['cmbOrigem'], $_POST['cmbDestinoLocal'],
		 $_POST['cmbDestinoSetor'], $_POST['txtareaObservacao'], $_POST['cmbFornecedor'], $_POST['cmbOrdemCompra'], $_POST['inputNotaFiscal'],
		 gravaData($_POST['inputDataEmissao']), $_POST['inputNumSerie'], gravaValor($_POST['inputValorTotal']), $_POST['inputChaveAcesso'],
		 $_POST['cmbSituacao'], $_SESSION['UsuarId'], $_SESSION['EmpreId']);
		die;*/
		$conn->beginTransaction();				
				
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':iClassificacao' => $_POST['cmbClassificacao'] == '#' ? null : $_POST['cmbClassificacao'],
						':iMotivo' => $iMotivo,
						':dData' => gravaData($_POST['inputData']),
						':iFinalidade' => $_POST['cmbFinalidade'],
						':iOrigem' => $_POST['cmbOrigem'] == '#' ? null : $_POST['cmbOrigem'],
						':iDestinoLocal' => $_POST['cmbDestinoLocal'] == '#' ? null : $_POST['cmbDestinoLocal'],
						':iDestinoSetor' => $_POST['cmbDestinoSetor'] == '#' ? null : $_POST['cmbDestinoSetor'],
						':iDestinoManual' => $_POST['inputDestinoManual'],
						':sObservacao' => $_POST['txtareaObservacao'],
						':iFornecedor' => $_POST['cmbFornecedor'] == '#' ? null : $_POST['cmbFornecedor'],
						':iOrdemCompra' => $_POST['cmbOrdemCompra'],
						':sNotaFiscal' => $_POST['inputNotaFiscal'],
						':dDataEmissao' => gravaData($_POST['inputDataEmissao']),
						':sNumSerie' => $_POST['inputNumSerie'],
						':fValorTotal' => gravaValor($_POST['inputValorTotal']),
						':sChaveAcesso' => $_POST['inputChaveAcesso'],
						':iSituacao' => $_POST['cmbSituacao'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
						
		$insertId = $conn->lastInsertId();
					
		try{
			$sql = "INSERT INTO MovimentacaoXProduto
						(MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrUsuarioAtualizador, MvXPrEmpresa)
					VALUES 
						(:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
		
			for ($i=1; $i <= $_POST['inputNumItens']; $i++) {
		
				$campo = 'campo'.$i;
				$registro = explode('#', $_POST[$campo]);	
				
				$result->execute(array(
								':iMovimentacao' => $insertId,
								':iProduto' => $registro[0],
								':iQuantidade' => $registro[1],
								':fValorUnitario' => gravaValor($registro[2]),
								':sLote' => $registro[3],
								':dValidade' => gravaData($registro[4]),
								':iUsuarioAtualizador' => $_SESSION['UsuarId'],
								':iEmpresa' => $_SESSION['EmpreId']
								));				
			}
			
		} catch(PDOException $e) {
			$conn->rollback();
			echo 'Error: ' . $e->getMessage();exit;
		}
		
		$conn->commit();		
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação realizada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao realizar movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage(); exit;
	}
	
	irpara("movimentacao.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Movimentação</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.	min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/lamparinas/jquery.maskMoney.js"></script>  <!-- http://www.fabiobmed.com.br/criando-mascaras-para-moedas-com-jquery/ -->
	<!-- /theme JS files -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
		
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});
				
				$.getJSON('filtraProduto.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								option += '<option value="'+obj.ProduId+'#'+obj.ProduValorCusto+'">'+obj.ProduNome+'</option>';
							} else {
								option += '<option value="'+obj.ProduId+'#'+obj.ProduCustoFinal+'">'+obj.ProduNome+'</option>';
							}
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	
					
			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbSubCategoria').on('change', function(e){
				
				FiltraProduto();
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				
				$.getJSON('filtraProduto.php?idCategoria='+cmbCategoria+'&idSubCategoria='+cmbSubCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';

					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								option += '<option value="'+obj.ProduId+'#'+obj.ProduValorCusto+'">'+obj.ProduNome+'</option>';
							} else {
								option += '<option value="'+obj.ProduId+'#'+obj.ProduCustoFinal+'">'+obj.ProduNome+'</option>';
							}

						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	

			//Ao mudar o Produto, trazer o Valor Unitário do cadastro (retorno via JSON)
			$('#cmbProduto').on('change', function(e){
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbProduto = $('#cmbProduto').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				
				var Produto = cmbProduto.split("#");				
				var valor = Produto[1].replace(".",",");
				
				$('#inputValorUnitario').val(valor);
			});	
			
			$("input[type=radio][name=inputTipo]").click(function(){
				var inputNumItens = $('#inputNumItens').val();
				
				if(inputNumItens > 0){
					alerta('Atenção','O tipo não pode ser alterado quando se tem produto(s) na lista! Exclua-o(s) primeiro ou cancele e recomece o cadastro da movimentação.','error');
					return false;
				}
			});
			
			$('#btnAdicionar').click(function(){
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				var cmbProduto = $('#cmbProduto').val();
				
				var Produto = cmbProduto.split("#");
				
				var inputQuantidade = $('#inputQuantidade').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				var inputTotal = $('#inputTotal').val();
				var inputLote = $('#inputLote').val();
				var inputValidade = $('#inputValidade').val();
				
				var resNumItens = parseInt(inputNumItens) + 1;	
				var total = parseInt(inputQuantidade) * parseInt(inputValorUnitario);
				
				total = total + parseFloat(inputTotal);
				var totalFormatado = "R$ " + float2moeda(total).toString();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "movimentacaoAddProduto.php",
					data: {tipo: inputTipo, numItens: resNumItens, idProduto: Produto[0], quantidade: inputQuantidade},
					success: function(resposta){
																	
						var newRow = $("<tr>");
						
						newRow.append(resposta);	    
						$("#tabelaProdutos").append(newRow);
												
						//Adiciona mais um item nessa contagem
						$('#inputNumItens').val(resNumItens);
						$('#cmbProduto').val("#").change();						
						$('#inputQuantidade').val('');
						$('#inputValorUnitario').val('');
						$('#inputTotal').val(total);
						$('#total').text(totalFormatado);
						$('#inputLote').val('');
						$('#inputValidade').val('');
						
						$('#inputProdutos').append('<input type="hidden" id="campo'+resNumItens+'" name="campo'+resNumItens+'" value="'+Produto[0]+'#'+inputQuantidade+'#'+inputValorUnitario+'#'+inputLote+'#'+inputValidade+'">');												
						
						return false;
						
					}
				})	
			}); //click
			
			//Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
			function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
				FiltraProduto();
			}		
			
			//Mostra o "Filtrando..." na combo Produto
			function FiltraProduto(){
				$('#cmbProduto').empty().append('<option>Filtrando...</option>');
			}		
			
			function ResetSubCategoria(){
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}
			
			function ResetProduto(){
				$('#cmbProduto').empty().append('<option>Sem produto</option>');
			}				
			
		}); //document.ready	
		
		function selecionaTipo(tipo) {
			if (tipo == 'E'){
				document.getElementById('EstoqueOrigem').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "block";
			} else if (tipo == 'S') {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('DestinoSetor').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "none";
			} else {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "none";
				document.getElementById('motivo').style.display = "block";
				document.getElementById('dadosNF').style.display = "none";
			}
		}	

		function selecionaMotivo(motivo) {
			var Motivo = motivo.split("#");
			var chave = Motivo[1];
			
			if (chave == 'DOACAO' || chave == 'DESCARTE' || chave == 'DEVOLUCAO' || chave == 'CONSIGNACAO'){
				document.getElementById('DestinoManual').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
			} else {
				document.getElementById('DestinoManual').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoManual').value = '';
			}
		}	
		
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
					
					<form name="formMovimentacao" id="formMovimentacao" method="post" class="form-validate" action="movimentacaoNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Movimentação</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="E" class="form-input-styled" onclick="selecionaTipo('E')" checked data-fouc>
												Entrada
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="S" class="form-input-styled" onclick="selecionaTipo('S')" data-fouc>
												Saída
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="T" class="form-input-styled" onclick="selecionaTipo('T')" data-fouc>
												Transferência
											</label>
										</div>										
									</div>
								</div>

								<div class="col-lg-4" id="classificacao">
									<div class="form-group">
										<label for="cmbClassificacao">Classificação/Bens</label>
										<select id="cmbClassificacao" name="cmbClassificacao" class="form-control form-control-select2">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT ClassId, ClassNome
														 FROM Classificacao
														 WHERE ClassStatus = 1
														 ORDER BY ClassNome ASC");
												$result = $conn->query("$sql");
												$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowClassificacao as $item){
													print('<option value="'.$item['ClassId'].'">'.$item['ClassNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
								
								<div class="col-lg-4" id="motivo" style="display:none;">
									<div class="form-group">
										<label for="cmbMotivo">Motivo</label>
										<select id="cmbMotivo" name="cmbMotivo" class="form-control form-control-select2" onChange="selecionaMotivo(this.value)">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT MotivId, MotivNome, MotivChave
														 FROM Motivo
														 WHERE MotivStatus = 1 and MotivEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY MotivNome ASC");
												$result = $conn->query("$sql");
												$rowMotivo = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowMotivo as $item){
													print('<option value="'.$item['MotivId'].'#'.$item['MotivChave'].'">'.$item['MotivNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
								
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>
																				
										<div class="col-lg-2">
											<div class="form-group">
												<label for="cmbFinalidade">Finalidade</label>
												<select id="cmbFinalidade" name="cmbFinalidade" class="form-control select">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT FinalId, FinalNome
																 FROM Finalidade
																 WHERE FinalStatus = 1
															     ORDER BY FinalNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['FinalId'].'">'.$item['FinalNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4" id="EstoqueOrigem" style="display:none;">
											<div class="form-group">
												<label for="cmbSubOrigem">Estoque Origem</label>
												<select id="cmbSubOrigem" name="cmbOrigem" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT LcEstId, LcEstNome
																 FROM LocalEstoque
																 WHERE LcEstStatus = 1 and LcEstEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY LcEstNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4" id="DestinoLocal">
											<div class="form-group">
												<label for="cmbDestinoLocal">Estoque Destino</label>
												<select id="cmbDestinoLocal" name="cmbDestinoLocal" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT LcEstId, LcEstNome
																 FROM LocalEstoque
																 WHERE LcEstStatus = 1 and LcEstEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY LcEstNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
														}
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4" id="DestinoSetor" style="display:none">
											<div class="form-group">
												<label for="cmbDestinoSetor">Destino (Setor)</label>
												<select id="cmbDestinoSetor" name="cmbDestinoSetor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT SetorId, SetorNome
																 FROM Setor
																 WHERE SetorStatus = 1 and SetorEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY SetorNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['SetorId'].'">'.$item['SetorNome'].'</option>');
														}
													?>
												</select>
											</div>
										</div>	
										
										<div class="col-lg-4" id="DestinoManual" style="display:none">
											<div class="form-group">
												<label for="inputDestinoManual">Destino</label>
												<input type="text" id="inputDestinoManual" name="inputDestinoManual" class="form-control">
											</div>											
										</div>
									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaObservacao">Observação</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"></textarea>
									</div>
								</div>
							</div>
							<br>
							
							<div class="row" id="dadosNF">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados da Nota Fiscal</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbFornecedor">Fornecedor</label>
												<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT ForneId, ForneNome
																 FROM Fornecedor														     
																 WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
															     ORDER BY ForneNome ASC");
														$result = $conn->query("$sql");
														$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowFornecedor as $item){															
															print('<option value="'.$item['ForneId'].'">'.$item['ForneNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="cmbOrdemCompra">Nº Ordem Compra / Carta Contrato</label>
												<input type="text" id="cmbOrdemCompra" name="cmbOrdemCompra" class="form-control">
											</div>
										</div>	
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNotaFiscal">Nº Nota Fiscal</label>
												<input type="text" id="inputNotaFiscal" name="inputNotaFiscal" class="form-control">
											</div>
										</div>										
									</div> <!-- row -->
									
									<div class="row">

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputDataEmissao">Data Emissão</label>
												<input type="text" id="inputDataEmissao" name="inputDataEmissao" class="form-control">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Nº Série</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" maxLength="30">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputValorTotal">Valor Total</label>
												<input type="text" id="inputValorTotal" name="inputValorTotal" class="form-control" onKeyUp="moeda(this)" maxLength="11">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputChaveAcesso">Chave de Acesso</label>
												<input type="text" id="inputChaveAcesso" name="inputChaveAcesso" class="form-control">
											</div>
										</div>
										
									</div> <!-- row -->
								</div> <!-- col-lg-12 -->
							</div> <!-- row -->
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados dos Produtos</h5>
									<br>
									
									<div class="row">
										<div class="col-lg-4">
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

										<div class="col-lg-4">
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
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control form-control-select2">
													<option value="#">Selecione</option>
												</select>
											</div>
										</div>										
									</div>							
													
									<div class="row">
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputQuantidade">Quantidade</label>
												<input type="text" id="inputQuantidade" name="inputQuantidade" class="form-control">												
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorUnitario">Valor Unitário</label>
												<input type="text" id="inputValorUnitario" name="inputValorUnitario" class="form-control" onKeyUp="moeda(this)" maxLength="10">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputLote">Lote</label>
												<input type="text" id="inputLote" name="inputLote" class="form-control">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValidade">Validade</label>
												<input type="text" id="inputValidade" name="inputValidade" class="form-control">
											</div>
										</div>										
										
										<div class="col-lg-2">
											<div class="form-group">												
												<button type="button" id="btnAdicionar" class="btn btn-lg btn-success" style="margin-top:20px;">Adicionar</button>
												<!--<button id="adicionar" type="button">Teste</button>-->
											</div>
										</div>										
									</div>
								</div>
							</div>						
							
							<div id="inputProdutos">
								<input type="hidden" id="inputNumItens" name="inputNumItens" value="0">
								<input type="hidden" id="inputTotal" name="inputTotal" value="0">
							</div>
							
							<div class="row">
								<div class="col-lg-12">	
										<table class="table" id="tabelaProdutos">
											<thead>
												<tr class="bg-slate">
													<th width="5%">Item</th>
													<th width="40%">Produto</th>
													<th width="14%">Unidade Medida</th>
													<th width="8%">Quantidade</th>
													<th width="14%">Valor Unitário</th>
													<th width="14%">Valor Total</th>
													<th width="5%" class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody>
												<tr style="display:none;">
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												</tr>
											</tbody>
									        <tfoot>
												<tr>
													<th colspan="5" style="text-align:right; font-size: 16px; font-weight:bold;">Total:</th>
													<th colspan="2"><div id="total" style="text-align:left; font-size: 15px; font-weight:bold;"></div></th>
												</tr>
											</tfoot>
										</table>
								</div>
							</div>							
							<br>
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="inputSituacao">Situação</label>
												<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT SituaId, SituaNome
																 FROM Situacao
																 WHERE SituaStatus = 1
															     ORDER BY SituaNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['SituaId'].'">'.$item['SituaNome'].'</option>');
														}													
													?>
												</select>
												
											</div>
										</div>
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
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
