<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Plano de Contas';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$sql = "INSERT INTO PlanoContas (PlConNome, PlConCentroCusto, PlConStatus, PlConUsuarioAtualizador, PlConUnidade)
				VALUES (:sNome, :sCentroCusto, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sCentroCusto' => $_POST['cmbCentroCusto'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Plano de Contas incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Plano de Contas!!!";
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

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNome    = $('#inputNome').val();
				var cmbCentroCusto = $('#cmbCentroCusto').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "planoContasValida.php",
					data: {nome : inputNome, centroCusto : cmbCentroCusto},
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Já exite Centro de Custo ligado a um Plano de Contas com este nome!','error');
							return false;
						}
						console.log(resposta)
						
						$( "#formPlanoContas" ).submit();
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
					
					<form name="formPlanoContas" id="formPlanoContas" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Plano de Contas</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<label for="inputNome">Plano de Contas<span class="text-danger"> *</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Plano de Contas" required autofocus>
									</div>
								</div>
								<div class="col-lg-6">
									<label for="cmbCentroCusto">Centro de Custo<span class="text-danger"> *</span></label>
									<select id="cmbCentroCusto" name="cmbCentroCusto" class="form-control form-control-select2" required>
										<option value="">Selecione</option>
										<?php 
											$sql = "SELECT CnCusId, CnCusNome
													FROM CentroCusto
													JOIN Situacao on SituaId = CnCusStatus
													WHERE CnCusUnidade = ".$_SESSION['UnidadeId']." and SituaChave = 'ATIVO'
													ORDER BY CnCusNome ASC";
											$result = $conn->query($sql);
											$row = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($row as $item){
												print('<option value="'.$item['CnCusId'].'">'.$item['CnCusNome'].'</option>');
											}
										
										?>
									</select>
								</div>						
							</div>
															
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="planoContas.php" class="btn btn-basic" role="button">Cancelar</a>
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
