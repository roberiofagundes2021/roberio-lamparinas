<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Parâmetro';

include('global_assets/php/conexao.php');

$sql = "SELECT ParamId, ParamTipo, ParamValorAtualizado
		FROM Parametro
	    WHERE ParamEmpresa = ". $_SESSION['EmpresaId'];
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$count = count($row);
//var_dump($count);die;

if(isset($_POST['inputIdEmpresa'])){

	try{
		
		$sql = "UPDATE Parametro SET ParamTipo = :sTipo, ParamValorAtualizado = :sValorAtualizado, ParamUsuarioAtualizador = :iUsuarioAtualizador
				WHERE ParamEmpresa = :iEmpresa";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':sValorAtualizado' => $_POST['cmbValorAtualizado'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpresaId']
						));
		
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	

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
					
					<form name="formParametro" id="formParametro" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Parâmetros</h5>
						</div>
						
						<div class="card-body">
							<p class="font-size-lg">Parâmetros da empresa <b><?php echo $_SESSION['EmpresaNome']; ?></b></p>							
						</div>
						
						<input type="hidden" id="inputIdEmpresa" name="inputIdEmpresa" class="form-control" value="<?php echo $idEmpresa; ?>">
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="Pr" class="form-input-styled" data-fouc <?php if ($row['ParamTipo'] == 'Pu') echo "checked"; ?>>
												Empresa Pública
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="Pu" class="form-input-styled" data-fouc <?php if ($row['ParamTipo'] == 'Pr') echo "checked"; ?>>
												Empresa Privada
											</label>
										</div>										
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="cmbValorAtualizado">Valor do Produto será atualizado:</label>
										<select id="cmbValorAtualizado" name="cmbValorAtualizado" class="form-control form-control-select2">
											<option value="PREVISTO">Somente no Fluxo Previsto</option>
											<option value="ORDEMCOMPRA">Somente na Ordem de Compra/Carta Contrato</option>
											<option value="AMBOS">Ambos</option>
										</select>
									</div>
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
