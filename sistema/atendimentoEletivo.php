<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Anamnese';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT TOP(1) AtEleId
FROM AtendimentoEletivo
WHERE AtEleAtendimento = $iAtendimentoId
ORDER BY AtEleId DESC";
$result = $conn->query($sql);
$rowEletivo = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoEletivoId = $rowEletivo?$rowEletivo['AtEleId']:null;

//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, A.ProfiUsuario, A.ProfiId as ProfissionalId, A.ProfiNome as ProfissionalNome, PrConNome, B.ProfiCbo as ProfissaoCbo
		FROM Usuario
		JOIN Profissional A ON A.ProfiUsuario = UsuarId
		LEFT JOIN Profissao B ON B.ProfiId = A.ProfiProfissao
		LEFT JOIN ProfissionalConselho ON PrConId = ProfiConselho
		WHERE UsuarId =  ". $_SESSION['UsuarId'] . " ";
$result = $conn->query($sql);
$rowUser = $result->fetch(PDO::FETCH_ASSOC);
$userId = $rowUser['ProfissionalId'];

//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtModNome, AtendClassificacaoRisco, ClienId, ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,
               ClienNomeMae, ClienCartaoSus, ClienCelular, ClienStatus, ClienUsuarioAtualizador, ClienUnidade, ClResNome, AtTriPeso,
			   AtTriAltura, AtTriImc, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca, AtTriTempAXI
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoTriagem ON AtTriAtendimento = AtendId
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE  AtendId = $iAtendimentoId 
		ORDER BY AtendNumRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoCliente = $row['AtendCliente'] ;
$iAtendimentoId = $row['AtendId'];

//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Feminino';
} else{
    $sexo = 'Masculino';
}

//Se estiver editando
if(isset($iAtendimentoEletivoId ) && $iAtendimentoEletivoId ){

	//Essa consulta é para preencher o campo Anamnese ao editar
	$sql = "SELECT AtEleAnamnese, AtEleHoraFim, AtEleHoraInicio, AtEleData
			FROM AtendimentoEletivo
			WHERE AtEleId = " . $iAtendimentoEletivoId ;
	$result = $conn->query($sql);
	$rowEletivo = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$Data = strtotime($rowEletivo['AtEleData']);
	$DataAtendimento = date("d/m/Y", $Data);

	$Inicio = strtotime($rowEletivo['AtEleHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowEletivo['AtEleHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 
//Se estiver gravando (inclusão ou edição)
if (isset($_POST['txtareaConteudo']) ){
	try{
		//Edição
		if ($iAtendimentoEletivoId){
		
			$sql = "UPDATE AtendimentoEletivo SET AtEleAtendimento = :sAtendimento, AtEleData = :dData, AtEleHoraInicio = :sHoraInicio,
						   AtEleHoraFim  = :sHoraFim, AtEleProfissional = :sProfissional, AtEleAnamnese = :sAnamnese, AtEleUnidade = :iUnidade
					WHERE AtEleId = :iAtendimentoEletivo";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => $_POST['inputFim'],
				':sProfissional' => $userId,
				':sAnamnese' => $_POST['txtareaConteudo'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoEletivo' => $iAtendimentoEletivoId 
				));

			$_SESSION['msg']['mensagem'] = "Anamnese alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoEletivo(AtEleAtendimento, AtEleData, AtEleHoraInicio, AtEleHoraFim, AtEleProfissional, AtEleAnamnese, AtEleUnidade)
						VALUES (:sAtendimento, :dData, :sHoraInicio, :sHoraFim, :sProfissional,:sAnamnese, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sAnamnese' => $_POST['txtareaConteudo'],
				':iUnidade' => $_SESSION['UnidadeId'],
			));

			$_SESSION['msg']['mensagem'] = "Anamnese incluída!!!";

		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Anamnese salva!!";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Anamnese!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoEletivo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Anamnese</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
	
	<script type="text/javascript">

		$(document).ready(function() {

			$('#summernote').summernote();
			
			$('#enviar').on('click', function(e){
				e.preventDefault();
				$( "#formAtendimentoEletivo" ).submit();
			})
		}); //document.ready
      
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php
			include_once("menu-left.php");
			include_once("menuLeftSecundarioVenda.php");
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					
					<div class="col-lg-12">
						<form id='dadosPost'>
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
						</form>
						<!-- Basic responsive configuration -->
						<form name="formAtendimentoEletivo" id="formAtendimentoEletivo" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>ANAMNESE</b></h3>
								</div>
							</div>

							<div> <?php include ('atendimentoDadosPaciente.php'); ?> </div>

							<div class="card">
								<div class="card-body">
									<div class="row">
										<div class="col-lg-9">
											<div class="form-group" >
												<label for="inputNome"><h4>Anamnese do Paciente</h4></label>
												<textarea rows="5" cols="5"  id="summernote" name="txtareaConteudo" class="form-control" placeholder="Corpo do anamnese (informe aqui o texto que você queira que apareça no anamnese)" > <?php if (isset($iAtendimentoEletivoId )) echo $rowEletivo['AtEleAnamnese']; ?> </textarea>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="card-body" style="margin-Top: -20px;">
												<div class="form-group" style="margin-left: -15px;">
													<h5 class="text-uppercase font-weight-bold">Risco/Vulnerabilidade</h5>
												</div>
												<div class="form-group" style="margin-left: -15px; margin-Top: -10px; height: 40px; width: 40px; background-color: <?php //echo $row['AtPrMCor']; ?>; border-radius: 50px;" >

												</div>
											</div>	
											<div class="card-body" style="margin-Top: -25px;">
												<div class="form-group" style="margin-left: -15px;">
													<h5 class="text-uppercase font-weight-bold">Medições</h5>
												</div>
												
												<div class="form-group" style="margin-left: -15px; margin-Top: -10px;" >
													<label>Peso: <?php echo mostraValor($row['AtTriPeso']); ?> Kg</label>
													<br>
													<label>Altura: <?php echo mostraValor($row['AtTriAltura']); ?> cm</label>
													<br>
													<label>IMC: <?php echo mostraValor($row['AtTriImc']); ?> Kg/m2</label>
													<br>
													<label>Pressão Arterial: <?php echo $row['AtTriPressaoSistolica']; ?>/<?php echo $row['AtTriPressaoDiatolica']; ?> mmHg</label>
													<br>
													<label>Frequência Cardíaca: <?php echo $row['AtTriFreqCardiaca']; ?> bpm</label>
													<br>
													<label>Temperatura AXI: <?php echo $row['AtTriTempAXI']; ?> ºC</label>
												</div>
											</div>	
										</div>									
									</div>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group" style="padding-top:25px;">
												<button class="btn btn-lg btn-principal" id="enviar">Salvar</button>
												<a href="atendimento.php" class="btn btn-basic" role="button">Cancelar</a>
											</div>
										</div>
									</div>    
									
								</div>
							</div>
						</form>
							<!-- /basic responsive configuration -->
					</div>
					
				</div>				
				
				<!-- /info blocks -->

			</div>
			<!-- /content area -->
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
