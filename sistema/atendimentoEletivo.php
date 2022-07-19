<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Anamnese';

include('global_assets/php/conexao.php');

$iAtendimentoId = '3';
$iAtendimentoEletivoId = '24';
$userId = $_SESSION['UsuarId'];


//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarNome
		FROM Usuario
		WHERE UsuarId = $userId ";
$result = $conn->query($sql);
$rowUser = $result->fetch(PDO::FETCH_ASSOC);

//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtModNome, ClienId, ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,
               ClienNomeMae, ClienCartaoSus, ClienCelular, ClienStatus, ClienUsuarioAtualizador, ClienUnidade, ClResNome
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE  AtendId = $iAtendimentoId 
		ORDER BY AtendNumRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoCliente = $row['AtendCliente'] ;
$iAtendimentoId = $row['AtendId'];


//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Femenino';
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
		if (isset($iAtendimentoEletivoId ) <> ''){
		
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

			$sql = "INSERT INTO AtendimentoEletivo (AtEleAtendimento, AtEleData, AtEleHoraInicio, AtEleHoraFim, AtEleProfissional, AtEleAnamnese, AtEleUnidade)
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
			
		}); //document.ready
        
		$('#enviar').on('click', function(e){
			
			e.preventDefault();
	

			$( "#formAtendimentoEletivo" ).submit();
				
			
		})
			
		// Calculo da idade do paciente.
		
		<?php
			$date1 = new DateTime($row['ClienDtNascimento']);
			$date2 = new DateTime();
			$interval = $date1->diff($date2); 
		?>



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
				<div class="row">
					
					<div class="col-lg-12">
							<!-- Basic responsive configuration -->

						<form name="formAtendimentoEletivo" id="formAtendimentoEletivo" method="post" class="form-validate-jquery">
						<input type="hidden" id="inputAtendimentoEletivoId" name="inputAtendimentoEletivoId" value="<?php if (isset($iAtendimentoEletivoId )) echo $iAtendimentoEletivoId ; ?>" >
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">ANAMNESE</h3>
								</div>
							</div>

							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Dados do Paciente</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label>Prontuário Eletrônico  : <?php echo $row['ClienCodigo']; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Nº do Registro  : <?php echo $row['AtendNumRegistro']; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Modalidade : <?php echo $row['AtModNome'] ; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>CNS  : <?php echo $row['ClienCartaoSus']; ?></label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6">
											<p class="font-size-lg"><b><?php echo $row['ClienNome']; ?></b></p>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Sexo : <?php echo $sexo ; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Telefone  : <?php echo $row['ClienCelular']; ?></label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label>Data Nascimento  : <?php echo mostraData($row['ClienDtNascimento']); ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Idade  : <?php echo " " . $interval->y . " anos, " . $interval->m." meses, ".$interval->d." dias"; ?></label> 
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Mãe : <?php echo $row['ClienNomeMae'] ; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Responsavel  : <?php echo $row['ClResNome']; ?></label>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-lg-3">
										<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php if (isset($iAtendimentoEletivoId )){ echo $DataAtendimento;} else { echo date('d/m/Y'); } ?>" readOnly> 
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputInicio">Início do Atendimento</label>
												<input type="text" id="inputInicio" name="inputInicio" class="form-control"  value="<?php if (isset($iAtendimentoEletivoId )){ echo $HoraInicio;} else { echo date('H:i'); } ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFim">Témino do Atendimento</label>
												<input type="text" id="inputFim" name="inputFim" class="form-control" value="<?php if (isset($iAtendimentoEletivoId )) echo $HoraFim; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputProfissional">Profissional</label>
												<input type="text" id="inputProfissional" name="inputProfissional" class="form-control"  value="<?php echo $rowUser['UsuarNome']; ?>" readOnly>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="card">

								<div class="card-body">

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="inputNome">Anamnese do Paciente </label>
												<textarea rows="5" cols="5"  id="summernote" name="txtareaConteudo" class="form-control" placeholder="Corpo do anamnese (informe aqui o texto que você queira que apareça no anamnese)" > <?php if (isset($iAtendimentoEletivoId )) echo $rowEletivo['AtEleAnamnese']; ?> </textarea>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group" style="padding-top:25px;">
												<button class="btn btn-lg btn-principal" id="enviar">Salvar</button>
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
