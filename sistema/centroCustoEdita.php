<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Centro de Custo';

include('global_assets/php/conexao.php');

if(isset($_POST['inputCentroCustoId'])){
	
	$iCentroCusto = $_POST['inputCentroCustoId'];
        	
	try{
		
		$sql = "SELECT CeCusId, CeCusNome
				FROM CentroCusto
				WHERE CeCusId = $iCentroCusto ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("centroCusto.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE CentroCusto SET CeCusNome = :sNome, CeCusUsuarioAtualizador = :iUsuarioAtualizador
				WHERE CeCusId = :iCentroCusto";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iCentroCusto' => $_POST['inputCentroCustoId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Centro de Custo alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Centro de Custo!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("centroCusto.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Centro de Custo</title>

	<?php include_once("head.php"); ?>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputCentroCustoNome').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				
				
				$.ajax({
					type: "POST",
				    url: "centroCustoValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;								
						}
						
						  $( "#formCentroCusto" ).submit();
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
					
					<form name="formCentroCusto" id="formCentroCusto" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Centro de Custo "<?php echo $row['CeCusNome']; ?>"</h5>
						</div>
						<input type="hidden" id="inputCentroCustoId" name="inputCentroCustoId" value="<?php echo $row['CeCusId']; ?>" >
						<input type="hidden" id="inputCentroCustoNome" name="inputCentroCustoNome" value="<?php echo $row['CeCusNome']; ?>" >
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome do Centro de Custo</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Centro de Custo" value="<?php echo $row['CeCusNome']; ?>" required autofocus>
									</div>
								</div>
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
										<a href="centroCusto.php" class="btn btn-basic" role="button">Cancelar</a>
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
