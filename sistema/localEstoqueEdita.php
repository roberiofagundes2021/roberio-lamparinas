<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Local do Estoque';

include('global_assets/php/conexao.php');

if(isset($_POST['inputLocalEstoqueId'])){
	
	$iLocalEstoque = $_POST['inputLocalEstoqueId'];
        			
	$sql = "SELECT LcEstId, LcEstNome, LcEstUnidade
			FROM LocalEstoque
			WHERE LcEstId = $iLocalEstoque ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
			
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("localEstoque.php");
}

if(isset($_POST['inputNome'])){
	
	try{

		if (isset($_SESSION['EmpresaId'])){

			$sql = "UPDATE LocalEstoque SET LcEstNome = :sNome, LcEstChave = :sChave, LcEstUnidade = :iUnidade, LcEstUsuarioAtualizador = :iUsuarioAtualizador
					WHERE LcEstId = :iLocalEstoque";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
							':sChave' => formatarChave($_POST['inputNome']),
							':iUnidade' => $_POST['cmbUnidade'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iLocalEstoque' => $_POST['inputLocalEstoqueId']
							));

		} else {
			$sql = "UPDATE LocalEstoque SET LcEstNome = :sNome, LcEstChave = :sChave, LcEstUsuarioAtualizador = :iUsuarioAtualizador
					WHERE LcEstId = :iLocalEstoque";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
							':sChave' => formatarChave($_POST['inputNome']),
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iLocalEstoque' => $_POST['inputLocalEstoqueId']
							));
		}
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Local do Estoque alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar local do estoque!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("localEstoque.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Local do Estoque</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	<!-- /theme JS files -->

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputLocalEstoqueNome').val();
				var cmbUnidade   = $('#cmbUnidade').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();

				if (inputNomeNovo == '' || cmbUnidade == ''){
					$( "#formLocalEstoque" ).submit();
					return false;
				}				
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "localEstoqueValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho+'&unidade='+cmbUnidade),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formLocalEstoque" ).submit();
					}
				})
			})
		})
	</script>	

</head>

<body class="navbar-top <?php if (isset($_SESSION['EmpresaId'])) echo "sidebar-xs"; ?>">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<?php 
			if (isset($_SESSION['EmpresaId'])){ 
				include_once("menuLeftSecundario.php");
			} 
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">		
				
				<!-- Info blocks -->
				<div class="card">
					
					<form name="formLocalEstoque" id="formLocalEstoque" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Local do Estoque "<?php echo $row['LcEstNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputLocalEstoqueId" name="inputLocalEstoqueId" value="<?php echo $row['LcEstId']; ?>" >
						<input type="hidden" id="inputLocalEstoqueNome" name="inputLocalEstoqueNome" value="<?php echo $row['LcEstNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<?php 
									if (isset($_SESSION['EmpresaId'])){ 
										print('<div class="col-lg-6">');
									} else{
										print('<div class="col-lg-12">');  
									}
								?>
									<div class="form-group">
										<label for="inputNome">Local do Estoque<span class="text-danger"> *</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Local do Estoque" value="<?php echo $row['LcEstNome']; ?>" required autofocus>
									</div>
								</div>

								<?php 
							
									if (isset($_SESSION['EmpresaId'])){
										
										print('
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbUnidade">Unidade<span class="text-danger"> *</span></label>
												<select name="cmbUnidade" id="cmbUnidade" class="form-control form-control-select2" required>
													<option value="">Informe uma unidade</option>');
													
													$sql = "SELECT UnidaId, UnidaNome
															FROM Unidade
															JOIN Situacao on SituaId = UnidaStatus															     
															WHERE UnidaEmpresa = " . $_SESSION['EmpresaId'] . " and SituaChave = 'ATIVO'
															ORDER BY UnidaNome ASC";
													$result = $conn->query($sql);
													$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowUnidade as $item) {
														$seleciona = $item['UnidaId'] == $row['LcEstUnidade'] ? "selected" : "";
														print('<option value="'. $item['UnidaId'].'" '. $seleciona .'>' . $item['UnidaNome'] . '</option>');
													}

										print('												
												</select>
											</div>
										</div>
										');
									} else{
										print('<input type="hidden" id="cmbUnidade" value="0" >');
									}
								?>								
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
										<a href="localEstoque.php" class="btn btn-basic" role="button">Cancelar</a>
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
