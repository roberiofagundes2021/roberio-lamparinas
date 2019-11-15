<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Plano de Contas';

include('global_assets/php/conexao.php');

if(isset($_POST['inputPlanoContasId'])){
	
	$iPlanoContas = $_POST['inputPlanoContasId'];
        	
	try{
		
		$sql = "SELECT PlConId, PlConNome, PlConCentroCusto
				FROM PlanoContas
				WHERE PlConId = $iPlanoContas ";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("planoContas.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE PlanoContas SET PlConNome = :sNome, PlConCentroCusto = :iCentroCusto, PlConUsuarioAtualizador = :iUsuarioAtualizador
				WHERE PlConId = :iPlanoContas";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iCentroCusto' => $_POST['cmbCentroCusto'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iPlanoContas' => $_POST['inputPlanoContasId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Plano de Contas alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Plano de Contas!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("planoContas.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Plano de Contas</title>

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
				var inputNomeVelho = $('#inputPlanoContasNome').val();
				var cmbCentroCusto   = $('#cmbCentroCusto').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Verifica se o campo só possui espaços em branco
				/*if (inputNomeNovo == ''){
					alerta('Atenção','Informe a sub categoria!','error');
					$('#inputNome').focus();
					return false;
				}

				//Verifica se o campo só possui espaços em branco
				if (cmbCategoria == '#'){
					alerta('Atenção','Informe a categoria!','error');
					$('#cmbCategoria').focus();
					return false;
				}*/
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "planoContasValida.php",
					data: ('nomeNovo='+inputNomeNovo+'&nomeVelho='+inputNomeVelho),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formPlanoContas" ).submit();
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
					
					<form name="formPlanoContas" id="formPlanoContas" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Plano de Contas "<?php echo $row['PlConNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputPlanoContasId" name="inputPlanoContasId" value="<?php echo $row['PlConId']; ?>">
						<input type="hidden" id="inputPlanoContasNome" name="inputPlanoContasNome" value="<?php echo $row['PlConNome']; ?>">
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNome">Plano de Contas</label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Plano de Contas" value="<?php echo $row['PlConNome']; ?>" required autofocus>
									</div>
								</div>
								<div class="col-lg-6">
									<label for="cmbCentroCusto">Categoria</label>
									<select id="cmbCentroCusto" name="cmbCentroCusto" class="form-control form-control-select2" required>
										<option value="">Selecione</option>
										<?php 
											$sql = "SELECT CeCusId, CeCusNome
													 FROM CentroCusto
													 WHERE CeCusStatus = 1 and CeCusEmpresa = ".$_SESSION['EmpreId']."
													 ORDER BY CeCusNome ASC";
											$result = $conn->query($sql);
											$rowCentroCusto = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($rowCentroCusto as $item){
												$seleciona = $item['CeCusId'] == $row['PlConCentroCusto'] ? "selected" : "";
												print('<option value="'.$item['CeCusId'].'" '. $seleciona .'>'.$item['CeCusNome'].'</option>');
											}
										
										?>
									</select>
								</div>								
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Alterar</button>
										<a href="planoContas.php" class="btn btn-basic">Cancelar</a>
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
