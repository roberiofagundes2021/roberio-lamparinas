<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Banco';

include('global_assets/php/conexao.php');

if(isset($_POST['inputBancoId'])){
	
	$iBanco = $_POST['inputBancoId'];
        	
	try{
		
		$sql = "SELECT BancoId, BancoCodigo, BancoNome
				FROM Banco
				WHERE BancoId = $iBanco ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("banco.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE Banco SET BancoCodigo = :sCodigo, BancoNome = :sNome, BancoUsuarioAtualizador = :iUsuarioAtualizador
				WHERE BancoId = :iBanco";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCodigo' => $_POST['inputCodigo'],
						':sNome' => $_POST['inputNome'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iBanco' => $_POST['inputBancoId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Banco alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar banco!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("banco.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Banco</title>

	<?php include_once("head.php"); ?>

	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputCodigo    = $('#inputCodigo').val();
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputBancoNome').val();
								
				//remove os espaços desnecessários antes e depois
				inputCodigo = inputCodigo.trim();
				inputNomeNovo = inputNomeNovo.trim();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "bancoValida.php",
					data: ('codigo='+inputCodigo+'&nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe (código ou nome)!','error');
							return false;
						}
						
						$( "#formBanco" ).submit();
					}
				})
			})
		})
	</script>

	<!-- Validação -->
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
					
					<form name="formBanco" id="formBanco" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Banco "<?php echo $row['BancoNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputBancoId" name="inputBancoId" value="<?php echo $row['BancoId']; ?>" >
						<input type="hidden" id="inputBancoNome" name="inputBancoNome" value="<?php echo $row['BancoNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputCodigo">Código do Banco</label>
										<input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código" value="<?php echo $row['BancoCodigo']; ?>" required autofocus>
									</div>
								</div>																
								<div class="col-lg-10">
									<div class="form-group">
										<label for="inputNome">Nome do Banco</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" value="<?php echo $row['BancoNome']; ?>" required>
									</div>
								</div>
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
										<a href="banco.php" class="btn btn-basic" role="button">Cancelar</a>
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
