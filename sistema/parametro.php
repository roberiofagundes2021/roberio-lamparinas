<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Parâmetro';

include('global_assets/php/conexao.php');

$sql = "SELECT ParamId, ParamTipo, ParamValorAtualizado
		FROM Parametro
	    WHERE ParamEmpresa = ". $_SESSION['EmpresaId'];
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

$aParametros = array();

foreach ($row as $item){
	$aParametros[$item['ParamTipo']] = $item['ParamValorAtualizado'];
}

if(isset($_POST['inputIdEmpresa'])){
var_dump($_POST);die;
	try{
		
		foreach ($_POST as $key => $value) {
			$sql = "UPDATE Parametro SET ParamTipo = :sTipo, ParamValorAtualizado = :sValorAtualizado, ParamUsuarioAtualizador = :iUsuarioAtualizador
					WHERE ParamEmpresa = :iEmpresa";
			$result = $conn->prepare($sql);
				
			$result->execute(array(
							':sTipo' => $key,
							':sValorAtualizado' => $value == "on" ? 1 : 0,
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEmpresa' => $_SESSION['EmpresaId']
							));
		}
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Parâmetro atualizado!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao atualizar parâmetro!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("parametro.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Parâmetro</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	<!-- CV Documentacao: https://jqueryvalidation.org/ -->	
	
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<!-- /theme JS files -->	
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
		
			//Garantindo que ninguém mude a empresa na tela de parâmetro
			//$('#cmbEmpresa').prop("disabled", true);	
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				e.preventDefault();
				
				$( "#formParametro" ).submit();				
				
			});		
		
		});	
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
					
					<form name="formParametro" id="formParametro" method="post">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Parâmetros</h5>
						</div>
						
						<div class="card-body">
							<p class="font-size-lg">Parâmetros da empresa <b><?php echo $_SESSION['EmpresaNome']; ?></b></p>							
						</div>
						
						<input type="hidden" id="inputIdEmpresa" name="inputIdEmpresa" class="form-control" value="<?php echo $idEmpresa; ?>">
						
						<div class="card-body">
							
							<div class="row">
								<div class="col-lg-6">
									<!-- Switch single -->
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Empresa Pública <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="inputEmpresaPublica" id="inputEmpresaPublica" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($aParametros['EmpresaPublica']) echo "checked"; ?>>
												</label>
											</div>
										</div>
									</div>
									<!-- /switch single -->
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-6">
									<!-- Switch group -->
									<div class="form-group row">
										<label class="col-form-label col-lg-3">Valor do Produto será atualizado <span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="ValorFluxo" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($aParametros['ValorAtualizadoFluxoPrevisto']) echo "checked"; ?>>
													Fluxo Previsto
												</label>
											</div>

											<div class="form-check form-check-switch form-check-switch-left">
												<label class="form-check-label d-flex align-items-center">
													<input type="checkbox" name="ValorOrdem" data-on-text="Sim" data-off-text="Não" class="form-input-switch" <?php if ($aParametros['ValorAtualizadoOrdemCompra']) echo "checked"; ?>>
													Ordem de Compra/Carta Contrato
												</label>
											</div>
										</div>
									</div>
									<!-- /switch group -->

								</div>
							</div>

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Atualizar</button>
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
