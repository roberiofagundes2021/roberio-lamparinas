<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Menu';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "INSERT INTO Menu (MenuNome, MenuUrl, MenuPai, MenuLevel, MenuIco, MenuHome, MenuOrdem, MenuStatus, MenuUsuarioAtualizador, MenuEmpresa)
				VALUES (:sNome, :sUrl, :iPai, :iLevel, :sIco, :bHome, :iOrdem, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sUrl' => $_POST['inputUrl'],
						':iPai' => $_POST['inputPai'],
						':iLevel' => $_POST['inputLevel'],
						':sIco' => $_POST['inputIco'],
						':bHome' => $_POST['inputHome'],
						':iOrdem' => $_POST['inputOrdem'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Menu incluída!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir menu!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("menu.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Menu</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<!-- /theme JS files -->


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
					
					<form name="formMenu" method="post" class="form-validate" action="menuNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Menu</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNome">Nome</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
									</div>
								</div>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputUrl">URL</label>
										<input type="text" id="inputUrl" name="inputUrl" class="form-control" placeholder="URL" required>
									</div>
								</div>
							</div>
							
							<div class="row">			
								<div class="col-lg-6">
									<label for="cmbPai">Menu Pai</label>
									<select id="cmbPai" name="cmbPai" class="form-control form-control-select2">
										<option value="#">Selecione</option>
										<?php 
											$sql = ("SELECT MenuId, MenuNome, MenuLevel, MenuOrdem
													 FROM Menu
													 WHERE MenuStatus = 1 and MenuEmpresa = ".$_SESSION['EmpreId']."
													 ORDER BY MenuNome ASC");
											$result = $conn->query("$sql");
											$row = $result->fetchAll(PDO::FETCH_ASSOC);
											
											print('<option value=0>Principal</option>');
											
											foreach ($row as $item){
												print('<option value="'.$item['MenuId'].'">'.$item['MenuNome'].'</option>');
											}
										
										?>
									</select>
								</div>	
								<div class="col-lg-6">
									<div class="form-group">
										<label for="e1_element">Ícone</label>
										<select id="e1_element" name="e1_element" class="form-control select-icons" data-fouc>
											<optgroup label="Services">
												<option value="wordpress2" data-icon="icon-wordpress2">icon-wordpress2</option>
												<option value="icon-home2" data-icon="icon-home2">icon-home2</option>
											</optgroup>
										</select>
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 30px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" type="submit">Incluir</button>
										<a href="menu.php" class="btn btn-basic" role="button">Cancelar</a>
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
