<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Ordem de Compra';

include('global_assets/php/conexao.php');

$sql = ("SELECT UsuarId, UsuarNome, UsuarEmail, UsuarTelefone
		 FROM Usuario
		 Where UsuarId = ".$_SESSION['UsuarId']."
		 ORDER BY UsuarNome ASC");
$result = $conn->query("$sql");
$rowUsuario = $result->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['inputData'])){
	
	try{
		
		/*
		$sql = ("SELECT COUNT(isnull(OrComNumero,0)) as Numero
				 FROM OrdemCompra
				 Where OrComEmpresa = ".$_SESSION['EmpreId']."");
		$result = $conn->query("$sql");
		$rowNumero = $result->fetch(PDO::FETCH_ASSOC);		
		
		$sNumero = (int)$rowNumero['Numero'] + 1;
		$sNumero = str_pad($sNumero,6,"0",STR_PAD_LEFT); */
			
		$sql = "INSERT INTO OrdemCompra (OrComTipo, OrComDtEmissao, OrComNumero, OrComLote, OrComNumProcesso, OrComCategoria, OrComSubCategoria, OrComConteudo, OrComFornecedor,
									     OrComValorFrete, OrComTotalPedido, OrComSolicitante, OrComLocalEntrega, OrComEnderecoEntrega, OrComDtEntrega, OrComObservacao, 
										 OrComSituacao, OrComUsuarioAtualizador, OrComEmpresa)
				VALUES (:sTipo, :dData, :sNumero, :sLote, :sProcesso, :iCategoria, :iSubCategoria, :sConteudo, :iFornecedor, :fValorFrete, :fTotalPedido, :iSolicitante,
						:iLocalEntrega, :sEnderecoEntrega, :dDataEntrega, :sObservacao, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
		
		$aFornecedor = explode("#",$_POST['cmbFornecedor']);
		$iFornecedor = $aFornecedor[0];
		
		/*
		echo $sql."<br>";
		
		var_dump($_POST['inputTipo'], gravaData($_POST['inputData']), $_POST['inputNumero'], $_POST['inputLote'], $_POST['inputProcesso'],
			  $_POST['cmbCategoria'], $_POST['cmbSubCategoria'], $_POST['txtareaConteudo'], $iFornecedor, null, null,
			  $_SESSION['UsuarId'], $_POST['cmbLocalEstoque'], $_POST['inputEnderecoEntrega'], gravaData($_POST['inputDataEntrega']),
			  $_POST['txtareaObservacao'], 1, $_SESSION['UsuarId'], $_SESSION['EmpreId']);
		*/
		
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':dData' => gravaData($_POST['inputData']),
						':sNumero' => $_POST['inputNumero'],
						':sLote' => $_POST['inputLote'],
						':sProcesso' => $_POST['inputProcesso'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':sConteudo' => $_POST['txtareaConteudo'],
						':iFornecedor' => $iFornecedor,
						':fValorFrete' => null,
						':fTotalPedido' => null,
						':iSolicitante' => $_SESSION['UsuarId'],
						':iLocalEntrega' => $_POST['cmbLocalEstoque'] == '#' ? null : $_POST['cmbLocalEstoque'],
						':sEnderecoEntrega' => $_POST['inputEnderecoEntrega'],
						':dDataEntrega' => gravaData($_POST['inputDataEntrega']),
						':sObservacao' => $_POST['txtareaObservacao'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
/*		$insertId = $conn->lastInsertId();
		
		$sql = "UPDATE Orcamento SET OrcamNumero = :sNumero
				WHERE OrcamId = :iOrcamento";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNumero' => str_pad($insertId,6,"0",STR_PAD_LEFT);
						':iOrcamento' => $insertId,
						));
*/		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Ordem de compra incluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir ordem de compra!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
	
	irpara("ordemcompra.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Orçamento</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>	

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- /theme JS files -->
	
	<!-- JS file path -->
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<!-- Uniform plugin file path -->
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
		
			$('#summernote').summernote();
			
			//Ao informar o fornecedor, trazer os demais dados dele (contato, e-mail, telefone)
			$('#cmbFornecedor').on('change', function(e){				
				
				var Fornecedor = $('#cmbFornecedor').val();
				var Forne = Fornecedor.split('#');
				
				$('#inputContato').val(Forne[1]);
				$('#inputEmailFornecedor').val(Forne[2]);
				if(Forne[3] != "" && Forne[3] != "(__) ____-____"){
					$('#inputTelefoneFornecedor').val(Forne[3]);
				} else {
					$('#inputTelefoneFornecedor').val(Forne[4]);
				}
			});
			
			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
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
				
			});
			
			//Ao mudar a categoria, filtra a subcategoria via ajax (retorno via JSON)
			$('#cmbUnidade').on('change', function(e){

				FiltraLocalEstoque();
				
				var cmbUnidade = $('#cmbUnidade').val();

				if (cmbUnidade == '#'){
					ResetLocalEstoque();
				} else {
				
					$.getJSON('filtraLocalEstoque.php?idUnidade=' + cmbUnidade, function (dados){
						
						var option = '';

						if (dados.length){						
							
							$.each(dados, function(i, obj){
								option += '<option value="'+obj.LcEstId+'">' + obj.LcEstNome + '</option>';
							});						
							
							$('#cmbLocalEstoque').html(option).show();
						} else {
							ResetLocalEstoque();
						}					
					});
				}
			});			
			
			$("#enviar").on('click', function(e){
				
				e.preventDefault();	
				
				var cmbCategoria = $('#cmbCategoria').val();
				
				if (cmbCategoria == '' || cmbCategoria == '#'){
					alerta('Atenção','Informe a categoria!','error');
					$('#cmbCategoria').focus();
					return false;
				}
			
				$("#formOrdemCompra").submit();
			});
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo SubCategoria
		function Filtrando(){
			$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
		}
		
		function FiltraLocalEstoque(){
			$('#cmbLocalEstoque').empty().append('<option>Filtrando...</option>');
		}		
		
		function ResetLocalEstoque(){
			$('#cmbLocalEstoque').empty().append('<option>Sem Local do Estoque</option>');
		}		
		
		function ResetSubCategoria(){
			$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
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
					
					<form name="formOrdemCompra" id="formOrdemCompra" method="post" class="form-validate" action="ordemcompraNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Ordem de Compra</h5>
						</div>
						
						<div class="card-body">								
								
							<div class="row">				
								
								<div class="col-lg-12">
									
									<div class="row">										
										<div class="col-lg-4">
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" name="inputTipo" value="C" class="form-input-styled" checked data-fouc>
														Carta Contrato
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" name="inputTipo" value="O" class="form-input-styled" data-fouc>
														Ordem de Compra
													</label>
												</div>										
											</div>
										</div>										
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data da Emissão</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputNumero">Número</label>
												<input type="text" id="inputNumero" name="inputNumero" class="form-control">
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
												<label for="inputProcesso">Processo</label>
												<input type="text" id="inputProcesso" name="inputProcesso" class="form-control">
											</div>
										</div>	
									</div>
									
									<div class="row">											
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and CategStatus = 1
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
												</select>
											</div>
										</div>										

									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<!--<div id="summernote" name="txtareaConteudo"></div>-->
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo do orçamento (informe aqui o texto que você queira que apareça no orçamento)"></textarea>
									</div>
								</div>
							</div>		
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados do Fornecedor</h5>
									<br>
									<div class="row">
										<div class="col-lg-4">
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
												<label for="inputContato">Contato</label>
												<input type="text" id="inputContato" name="inputContato" class="form-control" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailFornecedor">E-mail</label>
												<input type="text" id="inputEmailFornecedor" name="inputEmailFornecedor" class="form-control" readOnly>
											</div>
										</div>									

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefoneFornecedor">Telefone</label>
												<input type="text" id="inputTelefoneFornecedor" name="inputTelefoneFornecedor" class="form-control" readOnly>
											</div>
										</div>									
									</div>
								</div>
							</div>
							<br>
														
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados do Solicitante</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNomeSolicitante">Solicitante</label>
												<input type="text" id="inputNomeSolicitante" name="inputNomeSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarNome']; ?>" readOnly>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailSolicitante">E-mail</label>
												<input type="text" id="inputEmailSolicitante" name="inputEmailSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarEmail']; ?>" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefoneSolicitante">Telefone</label>
												<input type="text" id="inputTelefoneSolicitante" name="inputTelefoneSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarTelefone']; ?>" readOnly>
											</div>
										</div>									
									</div>
								</div>
							</div>
							<br>
								
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados da Entrega</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<label for="cmbUnidade">Unidade</label>
											<select id="cmbUnidade" name="cmbUnidade" class="form-control form-control-select2">
												<option value="#">Selecione</option>
												<?php 
													$sql = ("SELECT UnidaId, UnidaNome
															 FROM Unidade
															 WHERE UnidaStatus = 1 and UnidaEmpresa = ".$_SESSION['EmpreId']."
															 ORDER BY UnidaNome ASC");
													$result = $conn->query("$sql");
													$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowUnidade as $item){
														print('<option value="'.$item['UnidaId'].'">'.$item['UnidaNome'].'</option>');
													}
												
												?>
											</select>
										</div>										
									
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbLocalEstoque">Local / Almoxarifado</label>
												<select id="cmbLocalEstoque" name="cmbLocalEstoque" class="form-control form-control-select2">
													<?php 
														$sql = ("SELECT LcEstId, LcEstNome
																 FROM LocalEstoque															     
																 WHERE LcEstEmpresa = ". $_SESSION['EmpreId'] ." and LcEstStatus = 1
																 ORDER BY LcEstNome ASC");
														$result = $conn->query("$sql");
														$rowLocal = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowLocal as $item){															
															print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
									</div>
									
									<div class="row">									
										<div class="col-lg-10">
											<div class="form-group">
												<label for="inputEnderecoEntrega">Endereço da Entrega</label>
												<input type="text" id="inputEnderecoEntrega" name="inputEnderecoEntrega" class="form-control">
											</div>
										</div>									

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataEntrega">Data da Entrega</label>
												<input type="text" id="inputDataEntrega" name="inputDataEntrega" class="form-control">
											</div>
										</div>	
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtareaObservacao">Observação</label>											
												<textarea rows="3" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
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

</body>
</html>
