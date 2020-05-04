<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Modelo';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "INSERT INTO Modelo (ModelNome, ModelStatus, ModelUsuarioAtualizador, ModelUnidade)
				VALUES (:sNome, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Modelo incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir modelo!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("modelo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Modelo</title>

	<?php include_once("head.php"); ?>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNome = $('#inputNome').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNomeNovo == ''){
					alerta('Atenção','Informe o modelo!','error');
					$('#inputNome').focus();
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "modeloValida.php",
					data: ('nome='+inputNome),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formModelo" ).submit();
					}
				})
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
					
					<form name="formModelo" id="formModelo" method="post" class="form-validate-jquery" action="modeloNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Modelo</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome do Modelo <span class="text-danger">*</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Modelo" required autofocus>
									</div>
								</div>
							</div>
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="modelo.php" class="btn btn-basic" role="button">Cancelar</a>
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
