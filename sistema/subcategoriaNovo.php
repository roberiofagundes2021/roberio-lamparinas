<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Sub Categoria';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "INSERT INTO SubCategoria (SbCatNome, SbCatCategoria, SbCatStatus, SbCatUsuarioAtualizador, SbCatUnidade)
				VALUES (:sNome, :sCategoria, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sCategoria' => $_POST['cmbCategoria'] == '' ? null : $_POST['cmbCategoria'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Sub Categoria incluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir sub categoria!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
	
	irpara("subcategoria.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Sub Categoria</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<!--<script src="http://malsup.github.com/jquery.form.js"></script>-->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
	<!-- /theme JS files -->	

	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNome    = $('#inputNome').val();
				var cmbCategoria = $('#cmbCategoria').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNome.trim();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "subcategoriaValida.php",
					data: {nome : inputNome, categoria: cmbCategoria},
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Uma subcategoria com esse nome já está ligada a categoria selecionada.','error');
							return false;
						}
						
						$( "#formSubCategoria" ).submit();
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
					
					<form name="formSubCategoria" id="formSubCategoria" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Sub Categoria</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNome">Sub Categoria<span class="text-danger"> *</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Sub Categoria" required autofocus>
									</div>
								</div>
								<div class="col-lg-6">
									<label for="cmbCategoria">Categoria<span class="text-danger"> *</span></label>
									<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
										<option value="">Selecione</option>
										<?php 
											$sql = "SELECT CategId, CategNome
													FROM Categoria
													JOIN Situacao on SituaId = CategStatus
													WHERE SituaChave = 'ATIVO' and CategUnidade = ".$_SESSION['UnidadeId']."
													ORDER BY CategNome ASC";
											$result = $conn->query($sql);
											$row = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($row as $item){
												print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
											}
										
										?>
									</select>
								</div>						
							</div>
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="subcategoria.php" class="btn btn-basic" role="button">Cancelar</a>
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
