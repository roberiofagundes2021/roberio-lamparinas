<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Categoria';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "INSERT INTO Categoria (CategNome, CategStatus, CategUsuarioAtualizador, CategEmpresa)
				VALUES (:sNome, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Categoria incluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir categoria!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("categoria.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Categoria</title>

	<?php include_once("head.php"); ?>
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNome = $('#inputNome').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
					alerta('Atenção','Informe a categoria!','error');
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "categoriaValida.php",
					data: ('nome='+inputNome),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formCategoria" ).submit();
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
					
					<form name="formCategoria" id="formCategoria" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Categoria</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="inputNome">Nome da Categoria</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Categoria" required autofocus>
									</div>
								</div>
							</div>
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="categoria.php" class="btn btn-basic" role="button">Cancelar</a>
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
