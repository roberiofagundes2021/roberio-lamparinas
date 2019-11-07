<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Unidade de Medida';

include('global_assets/php/conexao.php');

if(isset($_POST['inputUnidadeMedidaId'])){
	
	$iUnidadeMedida = $_POST['inputUnidadeMedidaId'];
        	
	try{
		
		$sql = "SELECT UnMedId, UnMedNome, UnMedSigla
				FROM UnidadeMedida
				WHERE UnMedId = $iUnidadeMedida ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("unidademedida.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE UnidadeMedida SET UnMedNome = :sNome, UnMedSigla = :sSigla, UnMedUsuarioAtualizador = :iUsuarioAtualizador
				WHERE UnMedId = :iUnidadeMedida";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sSigla' => $_POST['inputSigla'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidadeMedida' => $_POST['inputUnidadeMedidaId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade de Medida alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar unidade de medida!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("unidademedida.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Unidade de Medida</title>

	<?php include_once("head.php"); ?>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputUnidadeMedidaNome').val();
				var inputSigla = $('#inputSigla').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				inputSigla = inputSigla.trim();
				
				//Verifica se o campo só possui espaços em branco
				/*if (inputNomeNovo == ''){
					alerta('Atenção','Informe a unidade de medida!','error');
					$('#inputNome').focus();
					return false;
				}
				
				//Verifica se o campo só possui espaços em branco
				if (inputSigla == ''){
					alerta('Atenção','Informe a sigla!','error');
					$('#inputSigla').focus();
					return false;
				}*/				
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "unidademedidaValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;								
						}
						
						$( "#formUnidadeMedida" ).submit();
					}
				})

			})
		})
	
	</script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
					
					<form name="formUnidadeMedida" id="formUnidadeMedida" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Unidade de Medida "<?php echo $row['UnMedNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputUnidadeMedidaId" name="inputUnidadeMedidaId" value="<?php echo $row['UnMedId']; ?>" >
						<input type="hidden" id="inputUnidadeMedidaNome" name="inputUnidadeMedidaNome" value="<?php echo $row['UnMedNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-8">
									<div class="form-group">
										<label for="inputNome">Nome</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Unidade de Medida" value="<?php echo $row['UnMedNome']; ?>" required autofocus>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputNome">Sigla</label>
										<input type="text" id="inputSigla" name="inputSigla" class="form-control" placeholder="Sigla" value="<?php echo $row['UnMedSigla']; ?>" required>
									</div>
								</div>								
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
										<a href="unidademedida.php" class="btn btn-basic" role="button">Cancelar</a>
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
