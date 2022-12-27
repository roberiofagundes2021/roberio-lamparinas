<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Leito';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if (isset($_SESSION['iAtendimentoId']) && $iAtendimentoId == null) {
	$iAtendimentoId = $_SESSION['iAtendimentoId'];
}
$_SESSION['iAtendimentoId'] = null;

if(!$iAtendimentoId){	
	irpara("atendimentoEletivoListagem.php");
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
			   AtTriAltura, AtTriImc, AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca, AtTriTempAXI, AtClRCor
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoTriagem ON AtTriAtendimento = AtendId
		LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
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
	$sql = "SELECT *
			FROM AtendimentoEletivo
			WHERE AtEleId = " . $iAtendimentoEletivoId ;
	$result = $conn->query($sql);
	$rowEletivo = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$DataInico = strtotime($rowEletivo['AtEleDataInicio']);
	$DataAtendimentoInicio = date("d/m/Y", $DataInico);

	$DataFim = strtotime($rowEletivo['AtEleDataFim']);
	$DataAtendimentoFim = date("d/m/Y", $DataFim);

	$Inicio = strtotime($rowEletivo['AtEleHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowEletivo['AtEleHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 
//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputInicio']) ){
	try{
		//Edição
		if ($iAtendimentoEletivoId){
		
			$sql = "UPDATE AtendimentoEletivo SET AtEleAtendimento = :sAtendimento, AtEleDataInicio = :dDataInicio, AtEleDataFim = :dDataFim, AtEleHoraInicio = :sHoraInicio,
						   	AtEleHoraFim  = :sHoraFim, AtEleProfissional = :sProfissional,  AtEleQueixaPrincipal = :sQueixaPrincipal,
						   	AtEleHistoriaMolestiaAtual = :sHistoriaMolestiaAtual, AtEleHistoriaPatologicaPregressa = :sHistoriaPatologicaPregressa,
							AtEleExameFisico = :sExameFisico, AtEleHipoteseDiagnostica = :sHipoteseDiagnostica, AtEleDigitacaoLivre = :sDigitacaoLivre, AtEleUnidade = :iUnidade
					WHERE AtEleId = :iAtendimentoEletivo";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dDataInicio' => gravaData($_POST['inputDataInicio']),
				':dDataFim' => date('m/d/Y'),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sQueixaPrincipal' => $_POST['txtareaConteudo1'],
				':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'],
				':sHistoriaPatologicaPregressa' => $_POST['txtareaConteudo3'],
				':sExameFisico' => $_POST['txtareaConteudo4'],
				':sHipoteseDiagnostica' => $_POST['txtareaConteudo5'],
				':sDigitacaoLivre' => $_POST['txtareaConteudo6'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoEletivo' => $iAtendimentoEletivoId 
				));

			$_SESSION['msg']['mensagem'] = "Anamnese alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoEletivo(AtEleAtendimento, AtEleDataInicio, AtEleDataFim, AtEleHoraInicio, AtEleHoraFim, AtEleProfissional, AtEleQueixaPrincipal, 
								AtEleHistoriaMolestiaAtual, AtEleHistoriaPatologicaPregressa,	AtEleExameFisico, AtEleHipoteseDiagnostica, AtEleDigitacaoLivre, AtEleUnidade)
						VALUES (:sAtendimento, :dDataInicio, :dDataFim, :sHoraInicio, :sHoraFim, :sProfissional, :sQueixaPrincipal, 
								:sHistoriaMolestiaAtual, :sHistoriaPatologicaPregressa, :sExameFisico, :sHipoteseDiagnostica, :sDigitacaoLivre, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dDataInicio' => gravaData($_POST['inputDataInicio']),
				':dDataFim' => gravaData($_POST['inputDataFim']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sQueixaPrincipal' => $_POST['txtareaConteudo1'],
				':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'],
				':sHistoriaPatologicaPregressa' => $_POST['txtareaConteudo3'],
				':sExameFisico' => $_POST['txtareaConteudo4'],
				':sHipoteseDiagnostica' => $_POST['txtareaConteudo5'],
				':sDigitacaoLivre' => $_POST['txtareaConteudo6'],
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

	$_SESSION['iAtendimentoId'] = $iAtendimentoId;
	irpara("atendimentoEletivo.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Leito</title>

	<?php include_once("head.php"); ?>

	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/lamparinas/layout.min.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/lamparinas/components.min.css" rel="stylesheet" type="text/css">

	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/plugins/ui/ripple.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script type="text/javascript">

		$(document).ready(function() {
			
			$('#enviar').on('click', function(e){
				e.preventDefault();
				$( "#formAtendimentoEletivo" ).submit();
			})

			$(".caracteressummernote1").text((500 - $("#summernote1").val().length) + ' restantes'); //restantes no input1
			$(".caracteressummernote2").text((500 - $("#summernote2").val().length) + ' restantes'); //restantes no input2
			$(".caracteressummernote3").text((500 - $("#summernote3").val().length) + ' restantes'); //restantes no input3
			$(".caracteressummernote4").text((500 - $("#summernote4").val().length) + ' restantes'); //restantes no input4
			$(".caracteressummernote5").text((500 - $("#summernote5").val().length) + ' restantes'); //restantes no input5
			$(".caracteressummernote6").text((1000 - $("#summernote6").val().length) + ' restantes'); //restantes no input6
		}); //document.ready

		function contarCaracteres(params) {

			var limite = params.maxLength;
			var informativo = " restantes.";
			var caracteresDigitados = params.value.length;
			var caracteresRestantes = limite - caracteresDigitados;

			if (caracteresRestantes <= 0) {
				var texto = $(`textarea[id=${params.id}]`).val();
				$(`textarea[id=${params.id}]`).val(texto.substr(0, limite));
				$(".caracteres" + params.id).text("0 " + informativo);
			} else {
				$(".caracteres" + params.id).text(caracteresRestantes + " " + informativo);
			}
		}
      
	</script>

	<style>
		.cardLeitos{
			padding: 5px;
			min-width: 200px;
			max-width: 300px;
			min-height: 200px;
			max-height: 200px;
			background-color:beige;
			border:1px solid rgba(0,0,0,0.1);
		}

		.cardLeitos i {
			font-size: 60px;
			margin: 10px;
		}
	</style>

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
									<h3 class="card-title"><b>Leito Internação Hospitalar</b></h3>
								</div>
							</div>

							<div> <?php include ('atendimentoDadosPaciente.php'); ?> </div>

							<div class="card">
								<div class="card-header header-elements-inline">
									<h5 class="card-title"><b>Filtros</b></h5>
								</div>

								<div class="mt-3 col-lg-12">
									<div class="row col-lg-12 text-left mb-2">
										<div class="col-lg-3">Acomodação</div>
										<div class="col-lg-3">Internação</div>
										<div class="col-lg-3">Especialidade</div>
										<div class="col-lg-2">Ala</div>
										<div class="col-lg-1"> </div>
									</div>

									<div class="row col-lg-12 mb-3">
										<div class="col-lg-3">
											<select id="acomodacao" name="acomodacao" class="select-search">
												<option value="">selecione</option>
											</select>
										</div>
										<div class="col-lg-3">
											<select id="internacao" name="internacao" class="select-search">
												<option value="">selecione</option>
											</select>
										</div>
										<div class="col-lg-3">
											<select id="especialidade" name="especialidade" class="select-search">
												<option value="">selecione</option>
											</select>
										</div>
										<div class="col-lg-2">
											<select id="ala" name="ala" class="select-search">
												<option value="">selecione</option>
											</select>
										</div>
										<div class="col-lg-1">
											<button class="btn btn-principal" id="filtrar">Filtrar</button>
										</div>
									</div>
								</div>
							</div>

							<div class="card p-2">
								<div class="card-header header-elements-inline">
									<h5 class="card-title"><b>Dados do Leito</b></h5>
								</div>

								<div id="dadosLeitos" class="">
									<div class='cardLeitos text-center'>
										<p class='m-0' id='tituloCardLeito'>Leito 5</p>
										<p id='subTituloCardLeito'>Recuperação</p>

										<i class='icon-bed2'></i>

										<div>
											<p id='tipoLeito'>Cirúrgica</p>
											<input id='checkLeito' type='radio' />
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
