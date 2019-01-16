<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Movimentação';

include('global_assets/php/conexao.php');

if(isset($_POST['inputData'])){
		
	try{
		
		echo $_POST['inputNumItens'];
		
		for ($i=1; $i <= $_POST['inputNumItens']; $i++) {
		
			$campo = 'campo'.$i;
			echo " - Teste: ".$_POST[$campo];
		}
		
		die;
		
		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimData, MovimFinalidade, MovimOrigem, MovimDestinoLocal, MovimDestinoSetor, MovimObservacao,
									      MovimFornecedor, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie, MovimValorTotal, 
										  MovimChaveAcesso, MovimSituacao, MovimEmpresa, OrcamUsuarioAtualizador)
				VALUES (:sTipo, :dData, :iFinalidade, :iOrigem, :iDestinoLocal, :iDestinoSetor, :sObservacao, :iFornecedor, :iOrdemCompra,
						:sNotaFiscal, :dDataEmissao, sNumSerie, :fValorTotal, :sChaveAcesso, :iSituacao, :iEmpresa, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sTipo' => $_POST['radioTipo'],
						':dData' => gravaData($_POST['inputData']),
						':iFinalidade' => $_POST['cmbFinalidade'],
						':iOrigem' => $_POST['cmbOrigem'],
						':iDestinoLocal' => $_POST['cmbDestinoLocal'],
						':iDestinoSetor' => $_POST['cmbDestinoSetor'],
						':sObservacao' => $_POST['txtareaObservacao'],
						':iFornecedor' => $_POST['cmbFornecedor'],
						':iSolicitante' => $_SESSION['UsuarId'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Orçamento incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir orçamento!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("orcamento.php");
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
	<!-- /theme JS files -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
	
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
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
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
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
				
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				
				$.getJSON('filtraProduto.php?idCategoria='+cmbCategoria+'&idSubCategoria='+cmbSubCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';

					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.ProduId+'">'+obj.ProduNome+'</option>';
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	
						
			$('#btnAdicionar').click(function(){
				
				var inputNumItens = $('#inputNumItens').val();
				var cmbProduto = $('#cmbProduto').val();
				var inputQuantidade = $('#inputQuantidade').val();	
				var inputLote = $('#inputLote').val();
				var inputValidade = $('#inputValidade').val();
				
				var resNumItens = parseInt(inputNumItens) + 1;		
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "orcamentoAddProduto.php",
					data: {numItens: resNumItens, idProduto: cmbProduto, quantidade: inputQuantidade},
					success: function(resposta){
						
						//alert(resposta);
						//return false;
											
						var newRow = $("<tr>");
						
						newRow.append(resposta);	    
						$("#tabelaProdutos").append(newRow);
												
						//Adiciona mais um item nessa contagem
						$('#inputNumItens').val(resNumItens);
						$('#cmbProduto').val("#").change();						
						$('#inputQuantidade').val('');
						$('#inputLote').val('');
						$('#inputValidade').val('');
						
						$('#inputProdutos').append('<input type="text" id="campo'+resNumItens+'" name="campo'+resNumItens+'" value="'+cmbProduto+'#'+inputQuantidade+'#'+inputLote+'#'+inputValidade+'">');
						
						return false;
						
					}
				})	
			}); //click
			
		}); //document.ready
		
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
		
        function selecionaTipo(tipo) {
			if (tipo == 'E'){
				document.getElementById('EstoqueOrigem').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('motivo').style.display = "none";
			} else if (tipo == 'S') {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('DestinoSetor').style.display = "block";
				document.getElementById('motivo').style.display = "none";
			} else {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('motivo').style.display = "block";
			}
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
								
								<div class="col-lg-4" id="motivo" style="display:none;">
									<div class="form-group">
										<label for="cmbMotivo">Motivo</label>
										<select id="cmbMotivo" name="cmbMotivo" class="form-control form-control-select2">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT MotivId, MotivNome
														 FROM Motivo
														 WHERE MotivStatus = 1 and MotivEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY MotivNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){															
													print('<option value="'.$item['MotivId'].'">'.$item['MotivNome'].'</option>');
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
										
									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaObservacao">Observação</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservação" placeholder="Observação"></textarea>
									</div>
								</div>
							</div>
							<br>
							
							<div class="row">
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
														$sql = ("SELECT ForneId, ForneNome, ForneContato, ForneEmail, ForneTelefone, ForneCelular
																 FROM Fornecedor														     
																 WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
															     ORDER BY ForneNome ASC");
														$result = $conn->query("$sql");
														$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowFornecedor as $item){															
															print('<option value="'.$item['ForneId'].'#'.$item['ForneContato'].'#'.$item['ForneEmail'].'#'.$item['ForneTelefone'].'#'.$item['ForneCelular'].'">'.$item['ForneNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumeroContrato">Nº Ordem Compra / Carta Contrato</label>
												<input type="text" id="inputNumeroContrato" name="inputNumeroContrato" class="form-control">
											</div>
										</div>	
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumeroNF">Nº Nota Fiscal</label>
												<input type="text" id="inputNumeroNF" name="inputNumeroNF" class="form-control">
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
												<label for="inputNumeroSerie">Nº Série</label>
												<input type="text" id="inputNumeroSerie" name="inputNumeroSerie" class="form-control">
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
													<?php 
													/*	$sql = ("SELECT ProduId, ProduNome
																 FROM Produto				     
																 WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduStatus = 1
																 ORDER BY ProduNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['ProduId'].'">'.$item['ProduNome'].'</option>');
														}
													*/
													?>
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
								<input type="text" id="inputNumItens" name="inputNumItens" value="0">
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
