<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Triagem';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT TOP(1) AtTriId
FROM AtendimentoTriagem
WHERE AtTriAtendimento = $iAtendimentoId
ORDER BY AtTriId DESC";
$result = $conn->query($sql);
$rowTriagem= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoTriagemId = $rowTriagem?$rowTriagem['AtTriId']:null;

// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';


//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, ProfiUsuario, ProfiId, ProfiNome
		FROM Usuario
		JOIN Profissional ON ProfiUsuario = UsuarId
		WHERE UsuarId =  ". $_SESSION['UsuarId'] . " ";
$result = $conn->query($sql);
$rowUser = $result->fetch(PDO::FETCH_ASSOC);
$userId = $rowUser['ProfiId'];

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
if(isset($iAtendimentoTriagemId ) && $iAtendimentoTriagemId ){

	//Essa consulta é para preencher o campo Triagem ao editar
	$sql = "SELECT *
			FROM AtendimentoTriagem
			WHERE AtTriId = " . $iAtendimentoTriagemId ;
	$result = $conn->query($sql);
	$rowTriagem = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$Data = strtotime($rowTriagem['AtTriData']);
	$DataAtendimento = date("d/m/Y", $Data);

	$Inicio = strtotime($rowTriagem['AtTriHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowTriagem['AtTriHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 



//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputAlergia']) ){
	try{
		//Edição
		if ($iAtendimentoTriagemId){
		
			$sql = "UPDATE AtendimentoTriagem SET AtTriAtendimento = :sAtendimento, AtTriData = :dData, AtTriHoraInicio = :sHoraInicio, AtTriHoraFim  = :sHoraFim, AtTriProfissional = :sProfissional, AtTriPressaoSistolica = :sPressaoSistolica, AtTriPressaoDiatolica = :sPressaoDiatolica,
			                                      AtTriFreqCardiaca = :sFreqCardiaca,  AtTriFreqRespiratoria = :sFreqRespiratoria, AtTriTempAXI = :sTempAXI, AtTriSPO = :sSPO, AtTriHGT = :sHGT, AtTriQueixaPrincipal = :sQueixaPrincipal, AtTriPeso = :sPeso, AtTriAltura = :sAltura,  
												  AtTriAlergia = :sAlergia, AtTriAlergiaDescricao = :sAlergiaDescricao, AtTriDiabetes = :sDiabetes, AtTriDiabetesDescricao = :sDiabetesDescricao, AtTriHipertensao = :sHipertensao, AtTriHipertensaoDescricao = :sHipertensaoDescricao,  
												  AtTriNeoplasia = :sNeoplasia, AtTriNeoplasiaDescricao = :sNeoplasiaDescricao, AtTriUsoMedicamento = :sUsoMedicamento, AtTriUsoMedicamentoDescricao = :sUsoMedicamentoDescricao, AtTriObservacao = :sObservacao, AtTriUnidade = :iUnidade	  
					WHERE AtTriId = :iAtendimentoTriagem";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => $_POST['inputFim'],
				':sProfissional' => $userId,
				':sPressaoSistolica' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
				':sPressaoDiatolica' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
				':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
				':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
				':sTempAXI' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
				':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
				':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
				':sQueixaPrincipal' => $_POST['txtareaQueixaPrincipal'] == "" ? null : $_POST['txtareaQueixaPrincipal'],
				':sPeso' => $_POST['inputPeso'] == "" ? null : floatval(gravaValor($_POST['inputPeso'])),
				':sAltura' => $_POST['inputAltura'] == "" ? null : floatval(gravaValor($_POST['inputAltura'])),
				':sAlergia' => $_POST['inputAlergia'] == "" ? null : $_POST['inputAlergia'],
				':sAlergiaDescricao' => $_POST['inputAlergiaDescricao'],
				':sDiabetes' => $_POST['inputDiabetes'] == "" ? null : $_POST['inputDiabetes'],
				':sDiabetesDescricao' => $_POST['inputDiabetesDescricao'],
				':sHipertensao' => $_POST['inputHipertensao'] == "" ? null : $_POST['inputHipertensao'],
				':sHipertensaoDescricao' => $_POST['inputHipertensaoDescricao'],
				':sNeoplasia' => $_POST['inputNeoplasia'] == "" ? null : $_POST['inputNeoplasia'],
				':sNeoplasiaDescricao' => $_POST['inputNeoplasiaDescricao'],
				':sUsoMedicamento' => $_POST['inputUsoMedicamento'] == "" ? null : $_POST['inputUsoMedicamento'],
				':sUsoMedicamentoDescricao' => $_POST['inputUsoMedicamentoDescricao'],
				':sObservacao' => $_POST['txtareaObservacao'] == "" ? null : $_POST['txtareaObservacao'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoTriagem' => $iAtendimentoTriagemId 
				));

			$_SESSION['msg']['mensagem'] = "Triagem alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoTriagem (AtTriAtendimento, AtTriData, AtTriHoraInicio, AtTriHoraFim, AtTriProfissional, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca,  AtTriFreqRespiratoria,
			                                        AtTriTempAXI, AtTriSPO, AtTriHGT, AtTriQueixaPrincipal, AtTriPeso, AtTriAltura, AtTriAlergia, AtTriAlergiaDescricao, AtTriDiabetes, AtTriDiabetesDescricao, 
													AtTriHipertensao, AtTriHipertensaoDescricao, AtTriNeoplasia, AtTriNeoplasiaDescricao, AtTriUsoMedicamento, AtTriUsoMedicamentoDescricao, AtTriObservacao, AtTriUnidade)
						VALUES (:sAtendimento, :dData, :sHoraInicio, :sHoraFim, :sProfissional, :sPressaoSistolica, :sPressaoDiatolica, :sFreqCardiaca, :sFreqRespiratoria,
						        :sTempAXI, :sSPO, :sHGT, :sQueixaPrincipal, :sPeso, :sAltura, :sAlergia, :sAlergiaDescricao, :sDiabetes, :sDiabetesDescricao, 
								:sHipertensao, :sHipertensaoDescricao, :sNeoplasia, :sNeoplasiaDescricao, :sUsoMedicamento, :sUsoMedicamentoDescricao, :sObservacao, :iUnidade )";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sPressaoSistolica' => $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'],
				':sPressaoDiatolica' => $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'],
				':sFreqCardiaca' => $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'],
				':sFreqRespiratoria' => $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'],
				':sTempAXI' => $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'],
				':sSPO' => $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'],
				':sHGT' => $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'],
				':sQueixaPrincipal' => $_POST['txtareaQueixaPrincipal'] == "" ? null : $_POST['txtareaQueixaPrincipal'],
				':sPeso' => $_POST['inputPeso'] == "" ? null : floatval(gravaValor($_POST['inputPeso'])),
				':sAltura' => $_POST['inputAltura'] == "" ? null : floatval(gravaValor($_POST['inputAltura'])),
				':sAlergia' => $_POST['inputAlergia'] == "" ? null : $_POST['inputAlergia'],
				':sAlergiaDescricao' => $_POST['inputAlergiaDescricao'],
				':sDiabetes' => $_POST['inputDiabetes'] == "" ? null : $_POST['inputDiabetes'],
				':sDiabetesDescricao' => $_POST['inputDiabetesDescricao'],
				':sHipertensao' => $_POST['inputHipertensao'] == "" ? null : $_POST['inputHipertensao'],
				':sHipertensaoDescricao' => $_POST['inputHipertensaoDescricao'],
				':sNeoplasia' => $_POST['inputNeoplasia'] == "" ? null : $_POST['inputNeoplasia'],
				':sNeoplasiaDescricao' => $_POST['inputNeoplasiaDescricao'],
				':sUsoMedicamento' => $_POST['inputUsoMedicamento'] == "" ? null : $_POST['inputUsoMedicamento'],
				':sUsoMedicamentoDescricao' => $_POST['inputUsoMedicamentoDescricao'],
				':sObservacao' => $_POST['txtareaObservacao'] == "" ? null : $_POST['txtareaObservacao'],
				':iUnidade' => $_SESSION['UnidadeId']
			));

			$_SESSION['msg']['mensagem'] = "Triagem incluída!!!";

		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Triagem!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoTriagem.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Triagem</title>

	<?php include_once("head.php"); ?>


	
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

    <script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
	
	<script type="text/javascript">

		$(document).ready(function() {	

			$('#summernote').summernote();
            $('#summernoteQueixa').summernote();
			
        
			$('#enviar').on('click', function(e){
			
			e.preventDefault();
	

			$( "#formAtendimentoTriagem" ).submit();
					
			})

		}); //document.ready


		function selecionaAlergiaDescricao(tipo) {
			if (tipo == 'SIM'){
				document.getElementById('dadosAlergia').style.display = "block";	
			} else {			
				document.getElementById('dadosAlergia').style.display = "none";		
			}
		}

		function selecionaDiabeteDescricao(tipo) {
			if (tipo == 'SIM'){	
				document.getElementById('dadosDiabete').style.display = "block";
			} else {						
				document.getElementById('dadosDiabete').style.display = "none";
			}
		}
			
		function selecionaHipertencaoDescricao(tipo) {
			if (tipo == 'SIM'){	
				document.getElementById('dadosHipertencao').style.display = "block";
			} else {						
				document.getElementById('dadosHipertencao').style.display = "none";
			}
		}

		function selecionaNeoplasiaDescricao(tipo) {
			if (tipo == 'SIM'){	
				document.getElementById('dadosNeoplasia').style.display = "block";
			} else {						
				document.getElementById('dadosNeoplasia').style.display = "none";
			}
		}

		function selecionaMedicamentoDescricao(tipo) {
			if (tipo == 'SIM'){	
				document.getElementById('dadosMedicamento').style.display = "block";
			} else {						
				document.getElementById('dadosMedicamento').style.display = "none";
			}
		}
		// Calculo da idade do paciente.
		
		<?php
			$date1 = new DateTime($row['ClienDtNascimento']);
			$date2 = new DateTime();
			$interval = $date1->diff($date2); 
		?>



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
						<form name="formAtendimentoTriagem" id="formAtendimentoTriagem" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>TRIAGEM</b></h3>
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
										<h4><b><?php echo strtoupper($row['ClienNome']); ?></b></h4>
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
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php if (isset($iAtendimentoTriagemId )){ echo $DataAtendimento;} else { echo date('d/m/Y'); } ?>" readOnly> 
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputInicio">Início do Atendimento</label>
												<input type="text" id="inputInicio" name="inputInicio" class="form-control"  value="<?php if (isset($iAtendimentoTriagemId )){ echo $HoraInicio;} else { echo date('H:i'); } ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFim">Témino do Atendimento</label>
												<input type="text" id="inputFim" name="inputFim" class="form-control" value="<?php if (isset($iAtendimentoTriagemId )) echo $HoraFim; ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputProfissional">Profissional</label>
												<input type="text" id="inputProfissional" name="inputProfissional" class="form-control"  value="<?php echo $rowUser['ProfiNome']; ?>" readOnly>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="card">

								<div class="card-body">

									<div class="row" style="margin-top: 20px;">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputPressaoArterial">Pressão Arterial</label>
												<div class="input-group">
												<input type="number" id="inputSistolica" name="inputSistolica" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriPressaoSistolica']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">X</span>	
													</span>
													<input type="number" id="inputDiatolica" name="inputDiatolica" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriPressaoDiatolica']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">mmHg</span>	
													</span>
												</div>
											</div>
										</div>
										
										<div class="col-lg-2" style="margin-right: 10px;">
											<div class="form-group">
												<label for="inputCardiaca">Frequência Cardíaca </label>
												<div class="input-group">
												<input type="number" id="inputCardiaca" name="inputCardiaca" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriFreqCardiaca']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">Bpm</span>	
													</span>
													
												</div>
											</div>
										</div>
										<div class="col-lg-2" style="margin-right: 20px;">
											<div class="form-group">
												<label for="inputRespiratoria">Frequência Respitatória </label>
												<input type="number" id="inputRespiratoria" name="inputRespiratoria" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriFreqRespiratoria']; ?>">
											</div>
										</div>
										
										<div class="col-lg-2" style="margin-right: 10px;">
											<div class="form-group">
												<label for="inputTemperatura">Temperatura AXI </label>
												<div class="input-group">
												<input type="number" id="inputTemperatura" name="inputTemperatura" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriTempAXI']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-eyedropper3"></i></span>
													</span>
													
												</div>
											</div>
										</div>
										<div class="col-lg-1" style="margin-right: 20px;">
											<div class="form-group">
											<label for="inputSPO">SpO2 </label>
												<input type="number" id="inputSPO" name="inputSPO" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriSPO']; ?>">
											</div>
										</div>
										<div class="col-lg-1">
											<div class="form-group">
											<label for="inputHGT">HGT </label>
												<input type="number" id="inputHGT" name="inputHGT" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriHGT']; ?>">
											</div>
										</div>
									</div>
                                   	<br>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtareaQueixaPrincipal">Queixa Principal </label>
												<textarea rows="5" cols="5"  id="summernoteQueixa" name="txtareaQueixaPrincipal" class="form-control" placeholder="Descrição (Queixa principal)"><?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriQueixaPrincipal']; ?></textarea>
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div class="form-group">
												<label for="inputPeso">Peso </label>
												<div class="input-group">
												<input type="text" onKeyUp="moeda(this)" maxLength="6" id="inputPeso" name="inputPeso" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo mostraValor($rowTriagem['AtTriPeso']); ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">Kg</span>		
													</span>
													
												</div>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAltura">Altura </label>
												<div class="input-group">
												<input type="text" onKeyUp="moeda(this)" maxLength="4" id="inputAltura" name="inputAltura" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo mostraValor($rowTriagem['AtTriAltura']); ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">Cm</span>
													</span>
													
												</div>
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<label for="inputAlergia">Alergia</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputAlergia" name="inputAlergia" value="S" class="form-input-styled" data-fouc onclick="selecionaAlergiaDescricao('SIM')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriAlergia'] == 'S') echo "checked"; }?>>
														Sim
													</label>                     
												</div>                                              
												
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputAlergia" name="inputAlergia" value="N" class="form-input-styled" data-fouc onclick="selecionaAlergiaDescricao('NAO')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriAlergia'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
														Não
													</label>
												</div>										
											</div>									
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<label for="inputDiabetes">Diabetes</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputDiabetes" name="inputDiabetes" value="S" class="form-input-styled" data-fouc onclick="selecionaDiabeteDescricao('SIM')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriDiabetes'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputDiabetes" name="inputDiabetes" value="N" class="form-input-styled" data-fouc onclick="selecionaDiabeteDescricao('NAO')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriDiabetes'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
														Não
													</label>
												</div>										
											</div>									
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<label for="inputHipertensao">Hipertensão</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputHipertensao" name="inputHipertensao" value="S" class="form-input-styled" data-fouc onclick="selecionaHipertencaoDescricao('SIM')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriHipertensao'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputHipertensao" name="inputHipertensao" value="N" class="form-input-styled" data-fouc  onclick="selecionaHipertencaoDescricao('NAO')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriHipertensao'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
														Não
													</label>
												</div>										
											</div>									
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<label for="inputNeoplasia">Neoplasia</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputNeoplasia" name="inputNeoplasia" value="S" class="form-input-styled" data-fouc onclick="selecionaNeoplasiaDescricao('SIM')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriNeoplasia'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputNeoplasia" name="inputNeoplasia" value="N" class="form-input-styled" data-fouc onclick="selecionaNeoplasiaDescricao('NAO')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriNeoplasia'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
														Não
													</label>
												</div>										
											</div>									
										</div>
										<div class="col-lg-2">
											<label for="inputUsoMedicamento">Uso de medicamento</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputUsoMedicamento" name="inputUsoMedicamento" value="S" class="form-input-styled" data-fouc data-fouc onclick="selecionaMedicamentoDescricao('SIM')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriUsoMedicamento'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputUsoMedicamento" name="inputUsoMedicamento" value="N" class="form-input-styled" data-fouc onclick="selecionaMedicamentoDescricao('NAO')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriUsoMedicamento'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
														Não
													</label>
												</div>										
											</div>									
										</div>
									</div>	
									<br>
									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div id="dadosAlergia" <?php if (!$iAtendimentoTriagemId) print('style="display:none"'); ?>>
												<div class="form-group">
													<label for="inputAlergiaDescricao">Descrição </label>
													<input type="text" id="inputAlergiaDescricao" name="inputAlergiaDescricao" class="form-control" placeholder="Descrição da Alergia" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriAlergiaDescricao']; ?>">
												</div>
											</div> 
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div id="dadosDiabete" <?php if (!$iAtendimentoTriagemId) print('style="display:none"'); ?>>
												<div class="form-group">
													<label for="inputDiabetesDescricao">Descrição </label>
													<input type="text" id="inputDiabetesDescricao" name="inputDiabetesDescricao" class="form-control" placeholder="Descrição da Diabetes" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriDiabetesDescricao']; ?>">
												</div>
											</div> 
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div id="dadosHipertencao" <?php if (!$iAtendimentoTriagemId) print('style="display:none"'); ?>>
												<div class="form-group">
													<label for="inputHipertensaoDescricao">Descrição </label>
													<input type="text" id="inputHipertensaoDescricao" name="inputHipertensaoDescricao" class="form-control" placeholder="Descrição da Hipertenção" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriHipertensaoDescricao']; ?>">
												</div>
											</div>
										</div>
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div id="dadosNeoplasia"<?php if (!$iAtendimentoTriagemId) print('style="display:none"'); ?>>
												<div class="form-group">
													<label for="inputNeoplasiaDescricao">Descrição </label>
													<input type="text" id="inputNeoplasiaDescricao" name="inputNeoplasiaDescricao" class="form-control" placeholder="Descrição da Neoplasia" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriNeoplasiaDescricao']; ?>">
												</div>
											</div>
										</div>
										<div class="col-lg-2">
											<div id="dadosMedicamento" <?php if (!$iAtendimentoTriagemId) print('style="display:none"'); ?>>
												<div class="form-group">
													<label for="inputUsoMedicamentoDescricao">Descrição </label>
													<input type="text" id="inputUsoMedicamentoDescricao" name="inputUsoMedicamentoDescricao" class="form-control" placeholder="Descrição do Medicamento" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriUsoMedicamentoDescricao']; ?>">
												</div>
											</div>
										</div>
									</div>	
									<br>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtareaObservacao">Observação </label>
												<textarea rows="5" cols="5"  id="summernote" name="txtareaObservacao" class="form-control" placeholder="Corpo do receituário (informe aqui o texto que você queira que apareça no receituário)" ><?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriObservacao']; ?></textarea>
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
