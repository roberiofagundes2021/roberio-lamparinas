<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Protocolo Manchester';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "INSERT INTO AtendimentoProtocoloManchester (AtPrMNome, AtPrMTempo, AtPrMCor, 
							AtPrMDeterminantes, AtPrMStatus, AtPrMUsuarioAtualizador, AtPrMUnidade) 
				VALUES ( :sNome, :sTempo, :sCor, :sDeterminantes, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sTempo' => $_POST['inputTempo'],
						':sCor' => $_POST['inputCor'],
						':sDeterminantes' => $_POST['txtDeterminantes'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Protocolo Manchester incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir protocolo manchester !!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error2: ' . $e->getMessage();die;
		
	}
	
	irpara("protocoloManchester.php");
} 

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Protocolos Manchester</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<!--<script src="http://malsup.github.com/jquery.form.js"></script>-->

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	

			//Limpa o campo Nome quando for digitado só espaços em branco
			$("#inputNome").on('blur', function(e){
				
				var inputNome = $('#inputNome').val();

				inputNome = inputNome.trim();
				
				if (inputNome.length == 0){
					$('#inputNome').val('');
				}	
			});        	
			
			
			$('#cancelar').on('click', function(e){
				
				e.preventDefault();		
				
				$(window.document.location).attr('href',"protocoloManchester.php");
				
			}); // cancelar		
		});		
		
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
					
					<form id="formProtocoloManchester" name="formProtocoloManchester" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Protocolo Manchester</h5>
						</div>
						
						<div class="card-body">								
							
							<div class="media">								
								
								<div class="media-body">
									<div class="row">	
										<div class="col-lg-8">
											<div class="form-group">
												<label for="inputNome">Nome <span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTempo">Tempo (min)<span class="text-danger">*</span></label>
												<input type="number" id="inputTempo" name="inputTempo" class="form-control" placeholder="Tempo" required>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputCor">Cor <span class="text-danger">*</span></label>
												<input type="color" id="inputCor" name="inputCor" class="container" placeholder="Cor" required>
											</div>
										</div>
										
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDeterminantes">Determinantes Gerais</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDeterminantes" name="txtDeterminantes" placeholder="Determinantes"></textarea>
											</div>
										</div>
									</div>
																		
								</div> <!-- media-body -->
								
							</div> <!-- media -->

							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="protocoloManchester.php" class="btn btn-lg" id="cancelar">Cancelar</a>
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
