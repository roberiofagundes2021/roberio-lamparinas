<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Receituário';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT TOP(1) AtSExId
FROM AtendimentoSolicitacaoExame
WHERE AtSExAtendimento = $iAtendimentoId
ORDER BY AtSExId DESC";
$result = $conn->query($sql);
$rowSolicitacaoExame= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoSolicitacaoExameId = $rowSolicitacaoExame?$rowSolicitacaoExame['AtSExId']:null;

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
if(isset($iAtendimentoSolicitacaoExameId ) && $iAtendimentoSolicitacaoExameId ){

	//Essa consulta é para preencher o campo Receituário ao editar
	$sql = "SELECT AtSExSolicitacaoExame, AtSExHoraFim, AtSExHoraInicio, AtSExData
			FROM AtendimentoSolicitacaoExame
			WHERE AtSExId = " . $iAtendimentoSolicitacaoExameId ;
	$result = $conn->query($sql);
	$rowSolicitacaoExame = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$Data = strtotime($rowSolicitacaoExame['AtSExData']);
	$DataAtendimento = date("d/m/Y", $Data);

	$Inicio = strtotime($rowSolicitacaoExame['AtSExHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowSolicitacaoExame['AtSExHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 



//Se estiver gravando (inclusão ou edição)
if (isset($_POST['txtareaConteudo']) ){
	try{
		//Edição
		if ($iAtendimentoSolicitacaoExameId){
		
			$sql = "UPDATE AtendimentoSolicitacaoExame SET AtSExAtendimento = :sAtendimento, AtSExData = :dData, AtSExHoraInicio = :sHoraInicio,
						   AtSExHoraFim  = :sHoraFim, AtSExProfissional = :sProfissional, AtSExSolicitacaoExame = :sSolicitacaoExame, AtSExUnidade = :iUnidade
					WHERE AtSExId = :iAtendimentoSolicitacaoExame";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => $_POST['inputFim'],
				':sProfissional' => $userId,
				':sSolicitacaoExame' => $_POST['txtareaConteudo'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoSolicitacaoExame' => $iAtendimentoSolicitacaoExameId 
				));

			$_SESSION['msg']['mensagem'] = "Receituário alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoSolicitacaoExame (AtSExAtendimento, AtSExData, AtSExHoraInicio, AtSExHoraFim, AtSExProfissional, AtSExSolicitacaoExame, AtSExUnidade)
						VALUES (:sAtendimento, :dData, :sHoraInicio, :sHoraFim, :sProfissional,:sSolicitacaoExame, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sSolicitacaoExame' => $_POST['txtareaConteudo'],
				':iUnidade' => $_SESSION['UnidadeId'],
			));

			$_SESSION['msg']['mensagem'] = "Receituário incluída!!!";

		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Receituário!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoSolicitacaoExame.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Receituário</title>

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
	

			$( "#formAtendimentoSolicitacaoExame" ).submit();
					
		})

	}); //document.ready
			
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
						<form name="formAtendimentoSolicitacaoExame" id="formAtendimentoSolicitacaoExame" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>SOLICITAÇÃO DE EXAME</b></h3>
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
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php if (isset($iAtendimentoSolicitacaoExameId )){ echo $DataAtendimento;} else { echo date('d/m/Y'); } ?>" readOnly> 
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputInicio">Início do Atendimento</label>
												<input type="text" id="inputInicio" name="inputInicio" class="form-control"  value="<?php if (isset($iAtendimentoSolicitacaoExameId )){ echo $HoraInicio;} else { echo date('H:i'); } ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFim">Témino do Atendimento</label>
												<input type="text" id="inputFim" name="inputFim" class="form-control" value="<?php if (isset($iAtendimentoSolicitacaoExameId )) echo $HoraFim; ?>" readOnly>
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

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="inputNome">Receituário do Paciente </label>
												<textarea rows="5" cols="5"  id="summernote" name="txtareaConteudo" class="form-control" placeholder="Corpo da solicitacao de exame (informe aqui o texto que você queira que apareça na solicitacao de exame)" > <?php if (isset($iAtendimentoSolicitacaoExameId )) echo $rowSolicitacaoExame['AtSExSolicitacaoExame']; ?> </textarea>
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
