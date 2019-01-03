<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Unidade';

include('global_assets/php/conexao.php');

if(isset($_POST['inputUnidadeId'])){
	
	$iUnidade = $_POST['inputUnidadeId'];
        	
	try{
		
		$sql = "SELECT UnidaId, UnidaNome
				FROM Unidade
				WHERE UnidaId = $iUnidade ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("unidade.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE Unidade SET UnidaNome = :sNome, UnidaUsuarioAtualizador = :iUsuarioAtualizador
				WHERE UnidaId = :iUnidade";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_POST['inputUnidadeId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar unidade!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("unidade.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Unidade</title>

	<?php include_once("head.php"); ?>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputUnidadeNome').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNomeNovo == ''){
					alerta('Atenção','Informe a unidade!','error');
					$('#inputNome').focus();
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "unidadeValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;								
						}
						
						$( "#formUnidade" ).submit();
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
					
					<form name="formUnidade" id="formUnidade" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Unidade "<?php echo $row['UnidaNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputUnidadeId" name="inputUnidadeId" value="<?php echo $row['UnidaId']; ?>" >
						<input type="hidden" id="inputUnidadeNome" name="inputUnidadeNome" value="<?php echo $row['UnidaNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome da Unidade</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Unidade" value="<?php echo $row['UnidaNome']; ?>" required autofocus>
									</div>
								</div>
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
										<a href="unidade.php" class="btn btn-basic" role="button">Cancelar</a>
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
