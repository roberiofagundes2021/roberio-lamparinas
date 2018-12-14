<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar SubCategoria';

include('global_assets/php/conexao.php');

if(isset($_POST['inputSubCategoriaId'])){
	
	$iSubCategoria = $_POST['inputSubCategoriaId'];
        	
	try{
		
		$sql = "SELECT SbCatId, SbCatNome, SbCatCategoria
				FROM SubCategoria
				WHERE SbCatId = $iSubCategoria ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("subcategoria.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE SubCategoria SET SbCatNome = :sNome, SbCatCategoria = :iCategoria, SbCatUsuarioAtualizador = :iUsuarioAtualizador
				WHERE SbCatId = :iSubCategoria";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iCategoria' => $_POST['cmbCategoria'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iSubCategoria' => $_POST['inputSubCategoriaId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Sub Categoria alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar sub categoria!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
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
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	<!-- /theme JS files -->	
	
	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				var inputNomeVelho = $('#inputSubCategoriaNome').val();
				var cmbCategoria   = $('#cmbCategoria').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNomeNovo == ''){
					alerta('Atenção','Informe a sub categoria!','error');
					$('#inputNome').focus();
					return false;
				}

				//Verifica se o campo só possui espaços em branco
				if (cmbCategoria == '#'){
					alerta('Atenção','Informe a categoria!','error');
					$('#cmbCategoria').focus();
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "subcategoriaValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						alert(resposta); // aqui deveria vir zero
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
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
					
					<form name="formSubCategoria" id="formSubCategoria" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Sub Categoria "<?php echo $row['SbCatNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputSubCategoriaId" name="inputSubCategoriaId" value="<?php echo $row['SbCatId']; ?>" >
						<input type="hidden" id="inputSubCategoriaNome" name="inputSubCategoriaNome" value="<?php echo $row['SbCatNome']; ?>" >
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNome">Sub Categoria</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Sub Categoria" value="<?php echo $row['SbCatNome']; ?>" required autofocus>
									</div>
								</div>
								<div class="col-lg-6">
									<label for="cmbCategoria">Categoria</label>
									<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
										<option value="#">Selecione</option>
										<?php 
											$sql = ("SELECT CategId, CategNome
													 FROM Categoria
													 WHERE CategStatus = 1 and CategEmpresa = ".$_SESSION['EmpreId']."
													 ORDER BY CategNome ASC");
											$result = $conn->query("$sql");
											$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($rowCategoria as $item){
												$seleciona = $item['CategId'] == $row['SbCatCategoria'] ? "selected" : "";
												print('<option value="'.$item['CategId'].'" '. $seleciona .'>'.$item['CategNome'].'</option>');
											}
										
										?>
									</select>
								</div>								
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
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
