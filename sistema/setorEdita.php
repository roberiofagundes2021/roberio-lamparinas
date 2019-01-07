<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Setor';

include('global_assets/php/conexao.php');

if(isset($_POST['inputSetorId'])){
	
	$iSetor = $_POST['inputSetorId'];
        	
	try{
		
		$sql = "SELECT SetorId, SetorNome
				FROM Setor
				WHERE SetorId = $iSetor ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("setor.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE Setor SET SetorNome = :sNome, SetorUsuarioAtualizador = :iUsuarioAtualizador
				WHERE SetorId = :iSetor";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iSetor' => $_POST['inputSetorId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Setor alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar setor!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("setor.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Setor</title>

	<?php include_once("head.php"); ?>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputSetorNome').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNomeNovo == ''){
					alerta('Atenção','Informe o setor!','error');
					$('#inputNome').focus();
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "setorValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;								
						}
						
						$( "#formSetor" ).submit();
					}
				})

			})
		})
	
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>
		
		<?php include_once("menuLeftSecundario.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">		
				
				<!-- Info blocks -->
				<div class="card">
					
					<form name="formSetor" id="formSetor" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Setor "<?php echo $row['SetorNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputSetorId" name="inputSetorId" value="<?php echo $row['SetorId']; ?>" >
						<input type="hidden" id="inputSetorNome" name="inputSetorNome" value="<?php echo $row['SetorNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome do Setor</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Setor" value="<?php echo $row['SetorNome']; ?>" required autofocus>
									</div>
								</div>
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
										<a href="setor.php" class="btn btn-basic" role="button">Cancelar</a>
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
