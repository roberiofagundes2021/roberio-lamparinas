<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Orçamento';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if(isset($_POST['inputOrcamentoId'])){
	
	$iOrcamento = $_POST['inputOrcamentoId'];
	
	try{
		
		$sql = "SELECT TrXOrId, TrXOrNumero, TrXOrTipo, TrXOrData, TrXOrCategoria, TrXOrSubCategoria, TrXOrConteudo, TrXOrFornecedor, 
					   ForneContato, ForneEmail, ForneTelefone, ForneCelular, TrXOrSolicitante, UsuarNome, UsuarEmail, UsuarTelefone
				FROM TRXOrcamento
				JOIN Usuario on UsuarId = TrXOrSolicitante
				LEFT JOIN Fornecedor on ForneId = TrXOrFornecedor
				WHERE TrXOrId = $iOrcamento ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("trOrcamento.php");
}

if(isset($_POST['inputTipo'])){	

	try{
		
		$iOrcamento = $_POST['inputOrcamentoId'];
		
		$sql = "UPDATE TRXOrcamento SET TrXOrTipo = :sTipo, TrXOrCategoria = :iCategoria, TrXOrSubCategoria = :iSubCategoria, TrXOrConteudo = :sConteudo,
									 TrXOrFornecedor = :iFornecedor, TrXOrUsuarioAtualizador = :iUsuarioAtualizador
				WHERE TrXOrId = :iOrcamento";
		$result = $conn->prepare($sql);
		
		$conn->beginTransaction();		

		$aFornecedor = explode("#",$_POST['cmbFornecedor']);
		$iFornecedor = $aFornecedor[0];
		
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':sConteudo' => $_POST['txtareaConteudo'],
						':iFornecedor' => $iFornecedor,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iOrcamento' => $iOrcamento
						));
		
		if (isset($_POST['inputOrcamentoProdutoExclui']) and $_POST['inputOrcamentoProdutoExclui']){
			
			$sql = "DELETE FROM TRXOrcamentoXProduto
					WHERE TXOXPOrcamento = :iOrcamento and TXOXPEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);	
			
			$result->execute(array(
								':iOrcamento' => $iOrcamento,
								':iEmpresa' => $_SESSION['EmpreId']));
		}
		
		$conn->commit();						
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Orçamento alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
				
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar orçamento!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		$conn->rollback();
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	
	irpara("trOrcamento.php");
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

			$("#enviar").on('click', function(e){
				
				e.preventDefault();				
				
				//Antes
				var inputCategoria = $('#inputOrcamentoCategoria').val();
				var inputSubCategoria = $('#inputOrcamentoSubCategoria').val();
				if (inputSubCategoria == '' || inputSubCategoria == null){
					inputSubCategoria = '#';
				}
				
				//Depois
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				
				if (cmbCategoria == '' || cmbCategoria == '#'){
					alerta('Atenção','Informe a categoria!','error');
					$('#cmbCategoria').focus();
					return false;
				}
				
				//Tem produto cadastrado para esse orçamento na tabela OrcamentoXProduto?
				var inputProduto = $('#inputOrcamentoProduto').val();
				
				//Exclui os produtos desse Orçamento?
				var inputExclui = $('#inputOrcamentoProdutoExclui').val();
				
				//Aqui verifica primeiro se tem produtos preenchidos, porque do contrário deixa mudar
				if (inputProduto > 0){

					//Verifica se o a categoria ou subcategoria foi alterada
					if (inputSubCategoria != cmbSubCategoria){

						inputExclui = 1;
						$('#inputOrcamentoProdutoExclui').val(inputExclui);
						
						confirmaExclusao(document.formOrcamento, "Tem certeza que deseja alterar o orçamento? Existem produtos com quantidades ou valores lançados!", "orcamentoEdita.php");
						
					} else{
						inputExclui = 0;
						$('#inputOrcamentoProdutoExclui').val(inputExclui);
					}
				}
				
				$( "#formOrcamento" ).submit();
				
			}); // enviar			
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo SubCategoria
		function Filtrando(){
			$('#cmbSubCategoria').empty().append('<option value="#">Filtrando...</option>');
		}		
		
		function ResetSubCategoria(){
			$('#cmbSubCategoria').empty().append('<option value="#">Sem Subcategoria</option>');
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
					
					<form name="formOrcamento" id="formOrcamento" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Orçamento Nº "<?php echo $_POST['inputOrcamentoNumero']; ?>"</h5>
						</div>						
						
						<input type="hidden" id="inputOrcamentoId" name="inputOrcamentoId" value="<?php echo $row['TrXOrId']; ?>" >
						<input type="hidden" id="inputOrcamentoNumero" name="inputOrcamentoNumero" value="<?php echo $row['TrXOrNumero']; ?>" >	
						<input type="hidden" id="inputOrcamentoCategoria" name="inputOrcamentoCategoria" value="<?php echo $row['TrXOrCategoria']; ?>" >
						<input type="hidden" id="inputOrcamentoSubCategoria" name="inputOrcamentoSubCategoria" value="<?php echo $row['TrXOrSubCategoria']; ?>" >
						<input type="hidden" id="inputOrcamentoProdutoExclui" name="inputOrcamentoProdutoExclui" value="0" >
						
						<?php
						
							$sql = "SELECT TXOXPOrcamento
									FROM TRXOrcamentoXProduto
									WHERE TXOXPOrcamento = ".$iOrcamento." and TXOXPEmpresa = ".$_SESSION['EmpreId'];
							$result = $conn->query($sql);
							$rowProduto = $result->fetchAll(PDO::FETCH_ASSOC);
							$countProduto = count($rowProduto);

							print('<input type="hidden" id="inputOrcamentoProduto" name="inputOrcamentoProduto" value="'.$countProduto.'" >');
						?>
						
						<div class="card-body">								
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										
										<div class="col-lg-3">
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="P" name="inputTipo" class="form-input-styled" data-fouc <?php if ($row['TrXOrTipo'] == 'P') echo "checked"; ?>>
														Produto
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="S" name="inputTipo" class="form-input-styled" data-fouc <?php if ($row['TrXOrTipo'] == 'S') echo "checked"; ?>>
														Serviço
													</label>
												</div>										
											</div>
										</div>										
										
										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo mostraData($row['TrXOrData']); ?>" readOnly>
											</div>
										</div>
																				
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = "SELECT CategId, CategNome
																FROM Categoria															     
																WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and CategStatus = 1
															    ORDER BY CategNome ASC";
														$result = $conn->query($sql);
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){
															$seleciona = $item['CategId'] == $row['TrXOrCategoria'] ? "selected" : "";
															print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
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
														$sql = "SELECT SbCatId, SbCatNome
																FROM SubCategoria															     
																WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
																ORDER BY SbCatNome ASC";
														$result = $conn->query($sql);
														$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowSubCategoria as $item){
															$seleciona = $item['SbCatId'] == $row['TrXOrSubCategoria'] ? "selected" : "";
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
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo do orçamento (informe aqui o texto que você queira que apareça no orçamento)"><?php echo $row['TrXOrConteudo']; ?></textarea>
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
															$seleciona = $item['ForneId'] == $row['TrXOrFornecedor'] ? "selected" : "";
															print('<option value="'.$item['ForneId'].'#'.$item['ForneContato'].'#'.$item['ForneEmail'].'#'.$item['ForneTelefone'].'#'.$item['ForneCelular'].'" '. $seleciona .'>'.$item['ForneNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputContato">Contato</label>
												<input type="text" id="inputContato" name="inputContato" class="form-control" value="<?php echo $row['ForneContato']; ?>" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailFornecedor">E-mail</label>
												<input type="text" id="inputEmailFornecedor" name="inputEmailFornecedor" class="form-control" value="<?php echo $row['ForneEmail']; ?>" readOnly>
											</div>
										</div>									

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTelefoneFornecedor">Telefone</label>
												<input type="text" id="inputTelefoneFornecedor" name="inputTelefoneFornecedor" class="form-control" value="<?php echo $row['ForneTelefone']; ?>" readOnly>
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
												<input type="text" id="inputNomeSolicitante" name="inputNomeSolicitante" class="form-control" value="<?php echo $row['UsuarNome']; ?>" readOnly>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailSolicitante">E-mail</label>
												<input type="text" id="inputEmailSolicitante" name="inputEmailSolicitante" class="form-control" value="<?php echo $row['UsuarEmail']; ?>" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefoneSolicitante">Telefone</label>
												<input type="text" id="inputTelefoneSolicitante" name="inputTelefoneSolicitante" class="form-control" value="<?php echo $row['UsuarTelefone']; ?>" readOnly>
											</div>
										</div>									
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<div class="btn btn-lg btn-success" id="enviar">Alterar</div>
										<a href="trOrcamento.php" class="btn btn-basic" role="button">Cancelar</a>
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
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >
	
		//Ao carregar a página tive que executar o que o onChange() executa para que a combo da SubCategoria já venha filtrada, além de selecionada, é claro.
		window.onload = function(){

			var cmbSubCategoria = $('#cmbSubCategoria').val();
			
			Filtrando();
			
			var cmbCategoria = $('#cmbCategoria').val();

			$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
				
				var option = '<option value="#">Selecione a SubCategoria</option>';
				
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
					ResetSubCategoria();
				}					
			});
		}
		
	</script>

</body>
</html>