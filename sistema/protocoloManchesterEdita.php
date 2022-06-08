<?php 

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Editar Protocolo Manchester';

include('global_assets/php/conexao.php');


if(isset($_POST['inputProtocoloManchesterId'])){

	$iProtocoloManchester = $_POST['inputProtocoloManchesterId'];
	
	try{
	
		$sql = "SELECT AtPrMId, AtPrMNome,  AtPrMTempo, AtPrMCor, AtPrMDeterminantes, SituaChave
				FROM AtendimentoProtocoloManchester
				JOIN Situacao on SituaId = AtPrMStatus
				WHERE AtPrMId = $iProtocoloManchester ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);		

	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();

} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente
	
	header("Location: protocoloManchester.php");
	//irpara("protocoloManchester.php");
}


if(isset($_POST['inputNome'])){
		
	try{

		$conn->beginTransaction();
		
		$sql = "UPDATE AtendimentoProtocoloManchester SET AtPrMNome = :sNome, AtPrMTempo = :sTempo, AtPrMCor = :sCor, 
		               AtPrMDeterminantes = :sDeterminantes, AtPrMUsuarioAtualizador = :iUsuarioAtualizador
				WHERE AtPrMId = :iProtocoloManchester ";

		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sTempo' => $_POST['inputTempo'],
						':sCor' => $_POST['inputCor'],
						':sDeterminantes' => $_POST['txtDeterminantes'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iProtocoloManchester' => $_POST['inputProtocoloManchesterId']
						));				
		
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Protocolo Manchester alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {	
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar protocolo manchester!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		//$result->debugDumpParams();
		
		echo 'Error: ' . $e->getMessage();		
		exit;
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
	<title>Lamparinas | Protocolo Manchester</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

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
		
		
		
			$('#alterar').on('click', function(e) {

				e.preventDefault();

				var inputNomeNovo = $('#inputNome').val();
				var inputNomeVelho = $('#inputProtocoloNome').val();
				

				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();

				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "protocoloManchesterValida.php",
					data: ('nomeNovo=' + inputNomeNovo + '&nomeVelho=' + inputNomeVelho),
					success: function(resposta) {

					if (resposta == 1) {
						alerta('Atenção', 'Esse registro já existe!', 'error');
						return false;
					}

					$("#formProtocoloManchester").submit();
					}
				})

			})
		
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
					
					<form id="formProtocoloManchester" name="formProtocoloManchester" method="post" class="form-validate-jquery" action="protocoloManchesterEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Protocolo Manchester "<?php echo $row['AtPrMNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputProtocoloManchesterId" name="inputProtocoloManchesterId" value="<?php echo $row['AtPrMId']; ?>" >
						<input type="hidden" id="inputProtocoloNome" name="inputProtocoloNome" value="<?php echo $row['AtPrMNome']; ?>">
						<input type="hidden" id="inputProtocoloManchesterStatus" name="inputProtocoloManchesterStatus" value="<?php echo $row['SituaChave']; ?>" >
						
						<div class="card-body">
							
							<div class="media">
								
                                <div class="media-body">

                                    <div class="row">

                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="inputNome">Nome <span class="text-danger">*</span></label>
                                                <input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['AtPrMNome']; ?>" required>
                                            </div>
                                        </div>
										<div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputTempo">Tempo (min)<span class="text-danger">*</span></label>
                                                <input type="number" id="inputTempo" name="inputTempo" class="form-control" placeholder="Tempo" value="<?php echo $row['AtPrMTempo']; ?>" required>
                                            </div>
                                        </div>
										<div class="col-lg-2">
                                            <div class="form-group">
                                                <label for="inputCor">Cor <span class="text-danger">*</span></label>
                                                <input type="color" id="inputCor" name="inputCor" class="container" placeholder="Cor" value="<?php echo $row['AtPrMCor']; ?>" required>
                                            </div>
                                        </div>
                                        
                                       
                                                                                                                                            
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="txtDeterminantes">Determinantes Gerais</label>
                                                <textarea rows="5" cols="5" class="form-control" id="txtDeterminantes" name="txtDeterminantes" placeholder="Determinantes"><?php echo $row['AtPrMDeterminantes']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- media-body -->																
									
							</div> <!-- media -->

							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
									<?php
										if ($_POST['inputPermission']) {
											echo '<button id="alterar" class="btn btn-lg btn-principal" type="submit">Alterar</button>';
										}
									?>	
										<a href="protocoloManchester.php" class="btn btn-basic" role="button">Cancelar</a>
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
