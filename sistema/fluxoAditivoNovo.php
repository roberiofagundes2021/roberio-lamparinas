<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Aditivo';

include('global_assets/php/conexao.php');

$iFluxoOperacional = $_POST['inputFluxoId'];

$sql = "SELECT Top 1 isnull(AditiNumero, 0) as Aditivo
        FROM Aditivo
        WHERE AditiFluxoOperacional = ".$iFluxoOperacional."
        ORDER BY AditiNumero DESC
       ";
$result = $conn->query($sql);
$rowNumero = $result->fetch(PDO::FETCH_ASSOC);
$iProxAditivo = $rowNumero['Aditivo'] + 1;

$sql = "SELECT Top 1 FlOpeDataFim as ProxData
        FROM FluxoOperacional
        WHERE FlOpeId = ".$iFluxoOperacional."
       ";
$result = $conn->query($sql);
$rowFluxo = $result->fetch(PDO::FETCH_ASSOC);
$iProxData = date('Y-m-d',strtotime("+1 day", strtotime($rowFluxo['ProxData'])));	//Adiciona 1 dia na data

if($rowNumero['Aditivo'] > 0) {

	$sql = "SELECT Top 1 isnull(AditiDtFim, '1900-01-01') as ProxData
	        FROM Aditivo
	        WHERE AditiFluxoOperacional = ".$iFluxoOperacional."
	        ORDER BY AditiDtFim DESC
	       ";
	$result = $conn->query($sql);
	$rowDataFim = $result->fetch(PDO::FETCH_ASSOC);
	
	if($rowDataFim['ProxData'] != '1900-01-01'){
		$iProxData = date('Y-m-d',strtotime("+1 day", strtotime($rowDataFim['ProxData'])));	//Adiciona 1 dia na data
	}
}

if(isset($_POST['inputDataInicio'])){

	try{
		
		$conn->beginTransaction();

		$sql = "INSERT INTO Aditivo (AditiFluxoOperacional, AditiNumero, AditiDtCelebracao, AditiDtInicio, AditiDtFim, 
									 AditiValor, AditiUsuarioAtualizador, AditiUnidade)
				VALUES (:iFluxo, :iNumero, :dDataCelebracao, :dDataInicio, :dDataFim, 
						:fValor, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						':iFluxo' => $iFluxoOperacional,
						':iNumero' => $iProxAditivo,
						':dDataCelebracao' => $_POST['inputDataCelebracao'] == '' ? null : gravaData($_POST['inputDataCelebracao']),						
						':dDataInicio' => $_POST['inputDataInicio'] == '' ? null : $_POST['inputDataInicio'],
						':dDataFim' => $_POST['inputDataFim'] == '' ? null : $_POST['inputDataFim'],
						':fValor' => $_POST['inputValor'] == '' ? null : gravaValor($_POST['inputValor']),
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
	    				
		$conn->commit();
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Aditivo incluído!!!";
		$_SESSION['msg']['tipo'] = "success";	
		
	} catch(PDOException $e) {
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir Aditivo!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
	
	irpara("fluxoAditivo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Aditivo - Fluxo Operacional</title>

	<?php include_once("head.php"); ?>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	<!-- CV Documentacao: https://jqueryvalidation.org/ -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {

			$('#enviar').on('click', function(e){

				e.preventDefault();				
				
				var inputDataInicio = $('#inputDataInicio').val();
				var inputDataFim = $('#inputDataFim').val();
				var inputValor = $('#inputValor').val().replace('.', '').replace(',', '.');		

				if (inputDataInicio == '' && inputDataFim == '' && (inputValor == 0 || inputValor == '')){
					alerta('Atenção','Informe as datas ou o valor do aditivo!','error');
					$('#inputDataInicio').focus();
					return false;				
				}
				
				if (inputDataFim < inputDataInicio){
					alerta('Atenção','A Data Fim deve ser maior que a Data Início!','error');
					$('#inputDataFim').focus();
					return false;				
				}				
				
				$( "#formAditivo" ).submit();				
				
			});
		
		});	
		
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
					
					<form name="formAditivo" id="formAditivo" method="post" >
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Aditivo</h5>
						</div>
						
						<div class="card-body">								
						
							<h5 class="mb-0 font-weight-semibold">Dados do(s) Aditivo(s)</h5>
							<br>

							<input type="hidden" id="inputFluxoId" name="inputFluxoId" class="form-control" value="<?php echo $iFluxoOperacional; ?>">

							<div class="row">
								<div class="col-lg-1">
									<div class="form-group">
										<label for="inputNumero">Nº Aditivo  <span class="text-danger">*</span></label>
										<input type="text" id="inputNumero" name="inputNumero" class="form-control" value="<?php echo $iProxAditivo; ?>" readOnly>
									</div>
								</div>

								<div class="col-lg-2">
									<div class="form-group">
										<label for="inputDataCelebracao">Data Celebração <span class="text-danger">*</span></label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="text" id="inputDataCelebracao" name="inputDataCelebracao" class="form-control" placeholder="Data Celebracao" value="<?php echo date('d/m/Y'); ?>" readOnly>
										</div>
									</div>
								</div>

								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputDataInicio">Data Início</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $iProxData; ?>">
										</div>
									</div>
								</div>
								
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputDataFim">Data Fim</label>
										<div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text"><i class="icon-calendar22"></i></span>
											</span>
											<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" autofocus>
										</div>
									</div>
								</div>
								
								<div class="col-lg-3">
									<div class="form-group">
										<label for="inputValor">Valor Total</label>
										<input type="text" id="inputValor" name="inputValor" class="form-control" placeholder="Valor Total" onKeyUp="moeda(this)" maxLength="12">
									</div>
								</div>								
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="fluxoAditivo.php" class="btn btn-basic" role="button" id="cancelar">Cancelar</a>
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
