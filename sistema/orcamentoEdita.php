<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Orçamento';

include('global_assets/php/conexao.php');

//Se veio do orcamento.php
if(isset($_POST['inputOrcamentoId'])){
	
	$iOrcamento = $_POST['inputOrcamentoId'];
	
	try{
		
		$sql = "SELECT OrcamId, OrcamNumero, OrcamTipo, OrcamData, OrcamCategoria, OrcamConteudo, OrcamFornecedor, ForneContato, ForneEmail, 
					   ForneTelefone, ForneCelular, OrcamSolicitante, UsuarNome, UsuarEmail, UsuarTelefone
				FROM Orcamento
				JOIN Usuario on UsuarId = OrcamSolicitante
				JOIN Fornecedor on ForneId = OrcamFornecedor
				WHERE OrcamId = $iOrcamento ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("orcamento.php");
}

if(isset($_POST['inputTipo'])){	

	try{
		
		$sql = "UPDATE Orcamento SET OrcamTipo = :sTipo, OrcamCategoria = :iCategoria, OrcamConteudo = :sConteudo,
									 OrcamFornecedor = :iFornecedor, OrcamUsuarioAtualizador = :iUsuarioAtualizador
				WHERE OrcamId = :iOrcamento";
		$result = $conn->prepare($sql);
		
		$conn->beginTransaction();		

		$aFornecedor = explode("#",$_POST['cmbFornecedor']);
		$iFornecedor = $aFornecedor[0];
		
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':iCategoria' => $_POST['cmbCategoria'],
						':sConteudo' => $_POST['txtareaConteudo'],
						':iFornecedor' => $iFornecedor,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iOrcamento' => $_POST['inputOrcamentoId']
						));

		$sql = "DELETE FROM OrcamentoXProduto
				WHERE OrXPrOrcamento = :iOrcamento and OrXPrEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);	
		
		$result->execute(array(
							':iOrcamento' => $_POST['inputOrcamentoId'],
							':iEmpresa' => $_SESSION['EmpreId']));
							
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
	
	irpara("orcamento.php");
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
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
			
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
						
		}); //document.ready
							
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
					
					<form name="formOrcamento" id="formOrcamento" method="post" class="form-validate" action="orcamentoEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Orçamento Nº "<?php echo $_POST['inputOrcamentoNumero']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputOrcamentoId" name="inputOrcamentoId" value="<?php echo $row['OrcamId']; ?>" >
						<input type="hidden" id="inputOrcamentoNumero" name="inputOrcamentoNumero" value="<?php echo $row['OrcamNumero']; ?>" >						
						
						<div class="card-body">								
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										
										<div class="col-lg-3">
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="P" name="inputTipo" class="form-input-styled" data-fouc <?php if ($row['OrcamTipo'] == 'P') echo "checked"; ?>>
														Produto
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputTipo" value="S" name="inputTipo" class="form-input-styled" data-fouc <?php if ($row['OrcamTipo'] == 'S') echo "checked"; ?>>
														Serviço
													</label>
												</div>										
											</div>
										</div>										
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo mostraData($row['OrcamData']); ?>" readOnly>
											</div>
										</div>
																				
										<div class="col-lg-7">
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
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){
															$seleciona = $item['CategId'] == $row['OrcamCategoria'] ? "selected" : "";
															print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
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
										<textarea rows="5" cols="5" class="form-control" id="txtareaConteudo" name="txtareaConteudo" placeholder="Corpo do orçamento (informe aqui o texto que você queira que apareça no orçamento)"><?php echo $row['OrcamConteudo']; ?></textarea>
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
															$seleciona = $item['ForneId'] == $row['OrcamFornecedor'] ? "selected" : "";
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

</body>
</html>
