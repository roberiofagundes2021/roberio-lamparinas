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
$sql = "SELECT UsuarId, ProfiUsuario, Profissional.ProfiId, Profissional.ProfiNome, ProfiNumConselho, ProfiCbo
		FROM Usuario
		JOIN Profissional ON ProfiUsuario = UsuarId
		JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
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
    $sexo = 'Feminino';
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
												  AtTriNeoplasia = :sNeoplasia, AtTriNeoplasiaDescricao = :sNeoplasiaDescricao, AtTriUsoMedicamento = :sUsoMedicamento, AtTriUsoMedicamentoDescricao = :sUsoMedicamentoDescricao, AtTriObservacao = :sObservacao, AtTriUnidade = :iUnidade,
												  AtTriCiap2 = :sMotivoQueixaSelect , AtTriMotivoConsulta = :sMotivoQueixa, AtTriMomentoColeta = :sMomentoColeta, AtTriImc = :sImc, AtTriGuicemiaCapilar = :sGlicemiaCapilar
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
				':sMotivoQueixaSelect' => $_POST['cmbMotivo'] == "" ? null : $_POST['cmbMotivo'],
				':sMotivoQueixa' => $_POST['summernoteMotivo'] == "" ? null : $_POST['summernoteMotivo'],
				':sMomentoColeta' => $_POST['inputMomentoColeta'] == "" ? null : $_POST['inputMomentoColeta'],
				':sImc' => $_POST['inputImc'] == "" ? null : $_POST['inputImc'],
				':sGlicemiaCapilar' => $_POST['inputGlicemiaCapilar'] == "" ? null : $_POST['inputGlicemiaCapilar'],
				':iAtendimentoTriagem' => $iAtendimentoTriagemId

				));

			$_SESSION['msg']['mensagem'] = "Triagem alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoTriagem (AtTriAtendimento, AtTriData, AtTriHoraInicio, AtTriHoraFim, AtTriProfissional, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca,  AtTriFreqRespiratoria,
			                                        AtTriTempAXI, AtTriSPO, AtTriHGT, AtTriQueixaPrincipal, AtTriPeso, AtTriAltura, AtTriAlergia, AtTriAlergiaDescricao, AtTriDiabetes, AtTriDiabetesDescricao, 
													AtTriHipertensao, AtTriHipertensaoDescricao, AtTriNeoplasia, AtTriNeoplasiaDescricao, AtTriUsoMedicamento, AtTriUsoMedicamentoDescricao, AtTriObservacao, AtTriUnidade,
													AtTriCiap2, AtTriMotivoConsulta, AtTriMomentoColeta, AtTriImc, AtTriGuicemiaCapilar)
						VALUES (:sAtendimento, :dData, :sHoraInicio, :sHoraFim, :sProfissional, :sPressaoSistolica, :sPressaoDiatolica, :sFreqCardiaca, :sFreqRespiratoria,
						        :sTempAXI, :sSPO, :sHGT, :sQueixaPrincipal, :sPeso, :sAltura, :sAlergia, :sAlergiaDescricao, :sDiabetes, :sDiabetesDescricao, 
								:sHipertensao, :sHipertensaoDescricao, :sNeoplasia, :sNeoplasiaDescricao, :sUsoMedicamento, :sUsoMedicamentoDescricao, :sObservacao, :iUnidade,
								:sMotivoQueixaSelect, :sMotivoQueixa, :sMomentoColeta, :sImc , :sGlicemiaCapilar )";
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
				':iUnidade' => $_SESSION['UnidadeId'],
				':sMotivoQueixaSelect' => $_POST['cmbMotivo'] == "" ? null : $_POST['cmbMotivo'],
				':sMotivoQueixa' => $_POST['summernoteMotivo'] == "" ? null : $_POST['summernoteMotivo'],
				':sMomentoColeta' => $_POST['inputMomentoColeta'] == "" ? null : $_POST['inputMomentoColeta'],
				':sImc' => $_POST['inputImc'] == "" ? null : $_POST['inputImc'],
				':sGlicemiaCapilar' => $_POST['inputGlicemiaCapilar'] == "" ? null : $_POST['inputGlicemiaCapilar']

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

		window.onload = function(){
			//Ao carregar a página é verificado se é Sim ou Não para aparecer a descrição ou esconder
			
			var tipo = $('input[name="inputAlergia"]:checked').val();
			var tipo1 = $('input[name="inputDiabetes"]:checked').val();
			var tipo2 = $('input[name="inputHipertensao"]:checked').val();
			var tipo3 = $('input[name="inputNeoplasia"]:checked').val();
			var tipo4 = $('input[name="inputUsoMedicamento"]:checked').val();

			selecionaAlergiaDescricao(tipo);

			selecionaDiabeteDescricao(tipo1);
			
			selecionaHipertensaoDescricao(tipo2);
			
			selecionaNeoplasiaDescricao(tipo3)
			
			selecionaUsoMedicamentoDescricao(tipo4)

		}

		$(document).ready(function() {	

			$('#summernote').summernote();
            $('#summernoteQueixa').summernote();
            $('#summernoteMotivo').summernote();
			$('#servicoTable').hide();

			addProcedimentosPadrao();
			checkServicos();

			$('#incluirServico').on('click', function(e) {
				e.preventDefault();

				let menssageError = '';
				let procedimento = $('#cmbProcRealizado').val();
				let triagemId = $('#iAtTriId').val();

				switch (menssageError) {
					case procedimento:
						menssageError = 'Informe o Procedimento';
						$('#cmbProcRealizado').focus();
						break;
					default:
						menssageError = '';
						break;
				}

				if (menssageError) {
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				//chamar requisicao
				$.ajax({
					type: 'POST',
					url: 'filtraTriagem.php',
					dataType: 'json',
					data: {
						'tipoRequest': 'ADICIONARPROCEDIMENTO',
						'procedimento': procedimento,
						'triagemId': triagemId			
					},
					success: function(response) {
						if (response.status == 'success') {
							checkServicos()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status);
						}
					},
					error: function(response) {
						alerta(response.titulo, response.menssagem, response.status);
					}
				});

			});
			
			$('#enviar').on('click', function(e){
			
			e.preventDefault();
	
			$( "#formAtendimentoTriagem" ).submit();
					
			})

		}); //document.ready


		function addProcedimentosPadrao() {

			let triagemId = $('#iAtTriId').val();

			$.ajax({
				type: 'POST',
				url: 'filtraTriagem.php',
				dataType: 'json',
				data: {
					'tipoRequest' : 'ADDPROCEDIMENTOSPADRAO',
					'triagemId' : triagemId
				},
				success: function(response) {
					if (response.status == 'success') {
						checkServicos()							
					}					
				}
			});
			
		}

		function checkServicos() {

			let triagemId = $('#iAtTriId').val();

			$.ajax({
			type: 'POST',
				url: 'filtraTriagem.php',
				dataType: 'json',
				data: {
					'tipoRequest': 'CHECKSERVICO',
					'triagemId': triagemId
				},
				success: async function(response) {
					statusServicos = response.array.length ? true : false;
					if (statusServicos) {

						$('#dataServico').html('');

						let HTML = '';

						response.array.forEach(item => {

							let exc = `<a style='color: black; cursor:pointer' onclick='excluiServico(\"${item.AtTXSTriagem}\", \"${item.AtTXSServicoVenda}\")' class='list-icons-item'><i class='icon-bin' title='Excluir Procedimento'></i></a>`;
							let acoes = `<div class='list-icons'>
										${exc}
									</div>`;
							HTML += `
							<tr class='servicoItem'>
								<td class="text-center"> ${item.SrVenCodigo} - ${item.SrVenNome}</td>
								<td class="text-center">${acoes}</td>
							</tr>`;

						});

						$('#dataServico').html(HTML).show();
						$('#servicoTable').show();

					}else {
						$('#servicoTable').hide();
					}
				}
			});

		}

		function excluiServico(idTriagem, idServico) {
				
				$.ajax({
					type: 'POST',
					url: 'filtraTriagem.php',
					dataType: 'json',
					data: {
						'tipoRequest': 'EXCLUIPROCEDIMENTO',
						'idTriagem': idTriagem,
						'idServico' : idServico
					},
					success: function(response) {

						if (response.status == 'success') {
							checkServicos()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status);
						}
						
					}
				});
			}


		function selecionaAlergiaDescricao(tipo) {
			if (tipo == 'S'){
				document.getElementById('dadosAlergia').style.display = "block";	
			} else {			
				document.getElementById('dadosAlergia').style.display = "none";		
			}
		}

		function selecionaDiabeteDescricao(tipo1) {
			if (tipo1 == 'S'){	
				document.getElementById('dadosDiabete').style.display = "block";
			} else {						
				document.getElementById('dadosDiabete').style.display = "none";
			}
		}
			
		function selecionaHipertensaoDescricao(tipo2) {
			if (tipo2 == 'S'){	
				document.getElementById('dadosHipertencao').style.display = "block";
			} else {						
				document.getElementById('dadosHipertencao').style.display = "none";
			}
		}

		function selecionaNeoplasiaDescricao(tipo3) {
			if (tipo3 == 'S'){	
				document.getElementById('dadosNeoplasia').style.display = "block";
			} else {						
				document.getElementById('dadosNeoplasia').style.display = "none";
			}
		}

		function selecionaUsoMedicamentoDescricao(tipo4) {
			if (tipo4 == 'S'){	
				document.getElementById('dadosMedicamento').style.display = "block";
			} else {						
				document.getElementById('dadosMedicamento').style.display = "none";
			}
		}

		function addGridProcRealizados(proc) {
			/* caso precise alterar a função para adicionar o procedimento quando preencher os campos
			onkeyup: addGridProcRealizados('inputSinVitais'), addGridProcRealizados('inputAntropometria'), addGridProcRealizados('inputGlicemia')
			if (proc == 'inputSinVitais') { }
			if (proc == 'inputAntropometria') { }
			if (proc == 'inputGlicemia') { }
			*/
		}

		function calcularImc() {

			let peso = $('#inputPeso').val();
			let altura = $('#inputAltura').val();
			peso = parseFloat(peso);
			altura = altura.replace(",", "");
			altura = altura / 100;
			imc = peso / (altura * altura);
			$('#inputImc').val(parseFloat(imc).toFixed(2));

		}

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
							<input type='hidden' id='iAtTriId' name='iAtTriId' value='<?php echo $iAtendimentoTriagemId != null ? $iAtendimentoTriagemId : '' ?>' />
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>TRIAGEM</b></h3>
								</div>
							</div>

							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Dados do Paciente</h3>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
										</div>
									</div>
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
												<label>Idade: <?php echo calculaIdade($row['ClienDtNascimento']); ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Mãe : <?php echo $row['ClienNomeMae'] ; ?></label>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label>Responsável  : <?php echo $row['ClResNome']; ?></label>
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

								<div class="card-body" >
									<div class="row" style="margin-top: 20px;">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="">Motivo da Consulta (CIAP2)</label>
												<select id="cmbMotivo" name="cmbMotivo" class="form-control form-control-select2">
													<option value="">Selecione um Motivo</option>

													<?php
														$sql = "SELECT Ciap2Id,Ciap2Descricao
																FROM Ciap2
																ORDER BY Ciap2Descricao ASC";
														$result = $conn->query($sql);
														$row = $result->fetchAll(PDO::FETCH_ASSOC);

														foreach ($row as $item) {
															if (isset($iAtendimentoTriagemId) && ($rowTriagem['AtTriCiap2'] ==  $item['Ciap2Id']) ) {																
																print('<option value="' . $item['Ciap2Id'] . '" selected>' . $item['Ciap2Descricao'] . '</option>');
															} else {
																print('<option value="' . $item['Ciap2Id'] . '">' . $item['Ciap2Descricao'] . '</option>');
															}
															
														}
													?>

												</select>
											</div>
										</div>
									</div>

									<div class="row" style="margin-top: 20px;" >
										<div class="col-lg-12">
											<div class="form-group">
												<label for="">Motivo da Consulta (descricao)</label>
												<textarea rows="5" cols="5"  id="summernoteMotivo" name="summernoteMotivo" class="form-control" placeholder="Descrição (Queixa principal)"><?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriMotivoConsulta']; ?></textarea>
											</div>
										</div>
									</div>
								</div>

								<div class="card-header header-elements-inline">
									<h3 class="card-title">Sinais Vitais</h3>
								</div>

								<div class="card-body">

									<div class="row" >
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
												<div class="input-group">												
													<input type="number" onKeyUp="" id="inputRespiratoria" name="inputRespiratoria" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriFreqRespiratoria']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">mpm</span>	
													</span>
												</div>
											</div>
										</div>
										
										<div class="col-lg-2" style="margin-right: 10px;">
											<div class="form-group">
												<label for="inputTemperatura">Temperatura AXI </label>
												<div class="input-group">
												<input type="number" id="inputTemperatura" name="inputTemperatura" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriTempAXI']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text"><img src="global_assets/images/lamparinas/termometro.png" width="32" style="margin-top: -13px;" /></span>
													</span>
													
												</div>
											</div>
										</div>
										<div class="col-lg-1" style="margin-right: 20px;">
											<div class="form-group">
												<label for="inputSPO">SPO<sub>2</sub> </label>
												<div class="input-group">
													<input type="number" onKeyUp="" id="inputSPO" name="inputSPO" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriSPO']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">%</span>	
													</span>
												</div>
											</div>
										</div>
										<div class="col-lg-1">
											<div class="form-group">
											<label for="inputHGT">HGT </label>
												<input type="number" id="inputHGT" name="inputHGT" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriHGT']; ?>">
											</div>
										</div>
									</div>

								</div>

								<div class="card-header header-elements-inline">
									<h3 class="card-title">Antropometria</h3>
								</div>

								<div class="card-body">

									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div class="form-group">
												<label for="inputPeso">Peso </label>
												<div class="input-group">
												<input type="text" onKeyUp="moeda(this); calcularImc() "  maxLength="6" id="inputPeso" name="inputPeso" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo mostraValor($rowTriagem['AtTriPeso']); ?>">
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
												<input type="text" onKeyUp="moeda(this); calcularImc()" maxLength="6" id="inputAltura" name="inputAltura" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo mostraValor($rowTriagem['AtTriAltura']); ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">Cm</span>
													</span>
													
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputAltura">IMC </label>
												<div class="input-group">
													<input type="number" maxLength="6" id="inputImc" name="inputImc" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriImc']; ?>" readonly>																										
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="card-header header-elements-inline">
									<h3 class="card-title">Glicemia</h3>
								</div>

								<div class="card-body">

									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<div class="form-group">
												<label for="inputGlicemiaCapilar">Glicemia Capilar </label>
												<div class="input-group">
												<input type="text" onKeyUp="" maxLength="3" id="inputGlicemiaCapilar" name="inputGlicemiaCapilar" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo $rowTriagem['AtTriGuicemiaCapilar']; ?>">
													<span class="input-group-prepend">
														<span class="input-group-text">mg/dL</span>		
													</span>
													
												</div>
											</div>
										</div>
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputMomentoColeta">Momento da Coleta </label>
												<div class="input-group">
													<input type="time"  maxLength="6" id="inputMomentoColeta" name="inputMomentoColeta" class="form-control" placeholder="" value="<?php if (isset($iAtendimentoTriagemId )) echo mostraHora($rowTriagem['AtTriMomentoColeta']); ?>">																			
												</div>
											</div>
										</div>

									</div>

								</div>

								<div class="card-body">                 										
									<br>
									<div class="row">
										<div class="col-lg-2"  style="margin-right: 20px;">
											<label for="inputAlergia">Alergia</label>
											<div class="form-group">							
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputAlergia" name="inputAlergia" value="S" class="form-input-styled" data-fouc onclick="selecionaAlergiaDescricao('S')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriAlergia'] == 'S') echo "checked"; }?>>
														Sim
													</label>                     
												</div>                                              
												
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputAlergia" name="inputAlergia" value="N" class="form-input-styled" data-fouc onclick="selecionaAlergiaDescricao('N')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriAlergia'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
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
														<input type="radio" id="inputDiabetes" name="inputDiabetes" value="S" class="form-input-styled" data-fouc onclick="selecionaDiabeteDescricao('S')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriDiabetes'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputDiabetes" name="inputDiabetes" value="N" class="form-input-styled" data-fouc onclick="selecionaDiabeteDescricao('N')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriDiabetes'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
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
														<input type="radio" id="inputHipertensao" name="inputHipertensao" value="S" class="form-input-styled" data-fouc onclick="selecionaHipertensaoDescricao('S')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriHipertensao'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputHipertensao" name="inputHipertensao" value="N" class="form-input-styled" data-fouc  onclick="selecionaHipertensaoDescricao('N')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriHipertensao'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
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
														<input type="radio" id="inputNeoplasia" name="inputNeoplasia" value="S" class="form-input-styled" data-fouc onclick="selecionaNeoplasiaDescricao('S')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriNeoplasia'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputNeoplasia" name="inputNeoplasia" value="N" class="form-input-styled" data-fouc onclick="selecionaNeoplasiaDescricao('N')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriNeoplasia'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
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
														<input type="radio" id="inputUsoMedicamento" name="inputUsoMedicamento" value="S" class="form-input-styled" data-fouc data-fouc onclick="selecionaUsoMedicamentoDescricao('S')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriUsoMedicamento'] == 'S') echo "checked"; }?>>
														Sim
													</label>
												</div>
												<div class="form-check form-check-inline">
													<label class="form-check-label">
														<input type="radio" id="inputUsoMedicamento" name="inputUsoMedicamento" value="N" class="form-input-styled" data-fouc onclick="selecionaUsoMedicamentoDescricao('N')" <?php if (isset($iAtendimentoTriagemId )) { if ($rowTriagem['AtTriUsoMedicamento'] == 'N') echo "checked"; }else{ echo "checked"; }?>>
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

										<div class="col-lg-5">
											<div class="form-group">
												<label for="">Procedimentos Realizado </label>

												<div class="row">
													
													<div class="col-lg-11">														
														<select id="cmbProcRealizado" name="cmbProcRealizado" class="form-control form-control-select2">
															<option value="" selected>Selecione um Procedimento</option>

															<?php
                                                            $sql = "SELECT SrVenId,SrVenCodigo, SrVenNome
																	FROM ServicoVenda
																	WHERE SrVenUnidade = ". $_SESSION['UnidadeId'] ."
																	ORDER BY SrVenNome ASC";
                                                            $result = $conn->query($sql);
                                                            $row = $result->fetchAll(PDO::FETCH_ASSOC);

                                                            foreach ($row as $item) {
                                                                print('<option value="' . $item['SrVenId'] . '">' . $item['SrVenCodigo'] . ' - ' . $item['SrVenNome'] . '</option>');
                                                            }
                                                            ?>

														</select>
													</div>
																										
													<div class="col-lg-1">
														<button id="incluirServico" class="btn btn-lg btn-light" data-tipo="INCLUIRSERVICO">
															<i class="icon-plus3 p-0" style="cursor: pointer; color: black"></i>
														</button>														
													</div>

												</div>

											</div>

											<div class="row">
												<div class="col-lg-12">

													<table class="table" id="servicoTable">
														<thead>
															<tr class="bg-slate text-center">
																<th >Procedimento</th>
																<th></th>
																
															</tr>
														</thead>
														<tbody id="dataServico">
	
														</tbody>
													</table>

												</div>
											</div>
										</div>
										
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group" style="padding-top:25px;">
												<button class="btn btn-lg btn-success" id="enviar">Salvar</button>
												<button class="btn btn-lg btn-secondary" id="imprimir" style="margin-left: 5px;" >Imprimir</button>

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
