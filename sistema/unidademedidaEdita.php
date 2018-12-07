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
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<!-- /theme JS files -->	

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
					
					<form name="formUnidadeMedida" method="post" class="form-validate" action="unidademedidaEdita.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Unidade de Medida "<?php echo $row['UnMedNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputUnidadeMedidaId" name="inputUnidadeMedidaId" value="<?php echo $row['UnMedId']; ?>" >
						
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
										<button class="btn btn-lg btn-success" type="submit">Alterar</button>
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
