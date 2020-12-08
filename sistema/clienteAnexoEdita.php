<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Anexo';

include('global_assets/php/conexao.php');

if(isset($_POST['inputClienteAnexoId'])){
	
	$iClienteAnexo = $_POST['inputClienteAnexoId'];
		
	$sql = "SELECT ClAneId, ClAneData, ClAneNome, ClAneArquivo, ClAneCliente
			FROM ClienteAnexo
			WHERE ClAneId = $iClienteAnexo ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	//irpara("clienteAnexo.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE ClienteAnexo SET ClAneData = :iData, ClAneNome = :sNome, ClAneArquivo = :iArquivo, ClAneCliente = :iCliente, ClAneUsuarioAtualizador = :iUsuarioAtualizador
				WHERE ClAneId = :iClienteAnexo";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
                        ':iData' => $_POST['inputData'],
                        ':sNome' => $_POST['inputNome'],
						':iArquivo' => $_POST['inputArquivo'],
						':iCliente' => $_SESSION['idCliente'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iClienteAnexo' => $_POST['inputClienteAnexoId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Anexo alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Anexo!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("clienteAnexo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Anexo</title>

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
				
				var inputData  = $('#inputData').val();
				//var inputNomeVelho = $('#inputCaixaNome').val();
				var inputNome   = $('#inputNome').val();
				var inputArquivo   = $('#inputArquivo').val();
				var inputClienteAnexoId = $('#inputClienteAnexoId').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				$( "#formClienteAnexo" ).submit();
			})
		})
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
					
					<form name="formClienteAnexo" id="formClienteAnexo" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Anexo "<?php echo $row['ClAneArquivo']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputClienteAnexoId" name="inputClienteAnexoId" value="<?php echo $row['ClAneId']; ?>">
						<input type="hidden" id="inputClienteAnexoNome" name="inputClienteAnexoNome" value="<?php echo $row['ClAneNome']; ?>">
						
						<div class="card-body">								
							<div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="inputData">Data</label>
                                        <input type="text" id="inputData" name="inputData" class="form-control" placeholder="Data" value="<?php echo date('d/m/Y'); ?>"  readOnly>
                                    </div>
							    </div>
								<div class="col-lg-9">
									<div class="form-group">
										<label for="inputNome">Descrição<span class="text-danger"> *</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Descrição" value="<?php echo $row['ClAneNome']; ?>" required autofocus>
									</div>
								</div>
                            </div>	
                            <div class="row">
								<div class="col-lg-12">
									<label for="inputArquivo">Arquivo<span class="text-danger"> *</span></label>
									<input type="text" id="inputArquivo" name="inputArquivo" class="form-control"value="<?php echo $row['ClAneArquivo']; ?>">
								</div>						
							</div>
								
							<div class="row" style="margin-top: 30px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
										<a href="clienteAnexo.php" class="btn btn-basic">Cancelar</a>
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
