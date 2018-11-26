<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Orçamento';

include('global_assets/php/conexao.php');

if(isset($_POST['inputTipo'])){
		
	try{
		
		$sql = "INSERT INTO Fornecedor (ForneTipo, ForneNome, ForneRazaoSocial, ForneCnpj, ForneInscricaoMunicipal, ForneInscricaoEstadual, 
										ForneCategoria, ForneSubCategoria, ForneCpf, ForneRg, ForneOrgaoEmissor, ForneUf, ForneSexo, 
										ForneAniversario, ForneCep, ForneEndereco, ForneNumero, ForneComplemento, ForneBairro, ForneCidade, 
										ForneEstado, ForneContato, ForneTelefone, ForneCelular, ForneEmail, ForneSite, ForneObservacao,
									    ForneBanco, ForneAgencia, ForneConta, ForneInformacaoAdicional, ForneIpi, ForneFrete, ForneIcms, 
									    ForneOutros, ForneStatus, ForneUsuarioAtualizador, ForneEmpresa)
				VALUES (:sTipo, :sNome, :sRazaoSocial, :sCnpj, :sInscricaoMunicipal, :sInscricaoEstadual, :iCategoria, :iSubCategoria, 
						:sCpf, :sRg, :sOrgaoEmissor, :sUf, :sSexo, :dAniversario, :sCep, :sEndereco, :sNumero, :sComplemtento, :sBairo, 
						:sCidade, :sEstado, :sContato, :sTelefone, :sCelular, :sEmail, :sSite, :sObservacao, :iBanco, :sAgencia, 
						:sConta, :sInformacaoAdicional, :iIpi, :iFrete, :iIcms, :iOutros,:bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNumero' => date()."/".$_POST['inputLote'],
						':sTipo' => $_POST['radioTipo'] == "on" ? "P" : "S",
						':dData' => gravadata($_POST['inputData']),
						':sLote' => $_POST['inputLote'],
						':iCategoria' => $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'],
						':sConteudo' => $_POST['txtareaConteudo'],
						':iFornecedor' => $_POST['inputFornecedor'],
						':iSolicitante' => $_SESSION['UsuarId'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Fornecedor incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir fornecedor!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("fornecedor.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fornecedor</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	

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
					
					<form name="formFornecedor" method="post" class="form-validate" action="fornecedorNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Fornecedor</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" checked data-fouc>
												Pessoa Física
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" class="form-input-styled" data-fouc>
												Pessoa Jurídica
											</label>
										</div>										
									</div>									
								</div>
								
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputCpf">CPF</label>
										<input type="text" id="inputCpf" name="inputCpf" class="form-control">
									</div>	
								</div>
								
								<div class="col-lg-4" style="display:none;">
									<div class="form-group">				
										<label for="inputCnpj">CNPJ</label>
										<input type="text" id="inputCnpj" name="inputCnpj" class="form-control">
									</div>	
								</div>
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome Completo" required>
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputRg">RG</label>
												<input type="text" id="inputRg" name="inputRg" class="form-control" placeholder="RG">
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ."
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
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ."
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
									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaConteudo" name="txtareaConteudo" placeholder="Corpo do orçamento (informe aqui o texto que você queira que apareça no orçamento)"></textarea>
									</div>
								</div>
							</div>		
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados do Fornecedor</h5>
									<br>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNomeForncedor">Fornecedor</label>
												<input type="text" id="inputNomeFornecedor" name="inputNomeFornecedor" class="form-control">
												<input type="hidden" id="inputFornecedor" name="inputFornecedor">
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

										<div class="col-lg-3">
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
									<h5 class="mb-0 font-weight-semibold">Dados dos Produtos</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNomeProduto">Produto</label>
												<input type="text" id="inputNomeProduto" name="inputNomeProduto" class="form-control">
												<input type="hidden" id="inputProduto" name="inputProduto">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputQuantidade">Quantidade</label>
												<input type="text" id="inputQuantidade" name="inputQuantidade" class="form-control" readOnly>
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
												<input type="text" id="inputNomeSolicitante" name="inputNomeSolicitante" class="form-control">
												<input type="hidden" id="inputSolicitante" name="inputSolicitante">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailSolicitante">E-mail</label>
												<input type="text" id="inputEmailSolicitante" name="inputEmailSolicitante" class="form-control" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefoneSolicitante">Telefone</label>
												<input type="text" id="inputTelefoneSolicitante" name="inputTelefoneSolicitante" class="form-control" readOnly>
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
						</form>								

					</div>
					<!-- /card-body -->
					
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
