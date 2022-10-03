<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Atendimento Ambulatorial';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT TOP(1) AtAmbId
FROM AtendimentoAmbulatorial
WHERE AtAmbAtendimento = $iAtendimentoId
ORDER BY AtAmbId DESC";
$result = $conn->query($sql);
$rowAmbulatorial= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoAmbulatorialId = $rowAmbulatorial?$rowAmbulatorial['AtAmbId']:null;

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
    $sexo = 'Feminino';
} else{
    $sexo = 'Masculino';
}

//Se estiver editando
if(isset($iAtendimentoAmbulatorialId ) && $iAtendimentoAmbulatorialId ){

	//Essa consulta é para preencher o campo do Atendimento Ambulatorial ao editar
	$sql = "SELECT *
			FROM AtendimentoAmbulatorial
			WHERE AtAmbId = " . $iAtendimentoAmbulatorialId ;
	$result = $conn->query($sql);
	$rowAmbulatorial = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$Data = strtotime($rowAmbulatorial['AtAmbData']);
	$DataAtendimento = date("d/m/Y", $Data);

	$Inicio = strtotime($rowAmbulatorial['AtAmbHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowAmbulatorial['AtAmbHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 



//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputInicio']) ){
	try{
		//Edição
		if ($iAtendimentoAmbulatorialId){
		
			$sql = "UPDATE AtendimentoAmbulatorial SET AtAmbAtendimento = :sAtendimento, AtAmbData = :dData, AtAmbHoraInicio = :sHoraInicio,
						   AtAmbHoraFim  = :sHoraFim, AtAmbProfissional = :sProfissional, AtAmbQueixaPrincipal = :sQueixaPrincipal,
						   AtAmbHistoriaMolestiaAtual = :sHistoriaMolestiaAtual, AtAmbExameFisico = :sExameFisico, 
						   AtAmbSuspeitaDiagnostico = :sSuspeitaDiagnostico, AtAmbExameSolicitado = :sExameSolicitado, 
						   AtAmbPrescricao = :sPrescricao, AtAmbOutrasObservacoes = :sOutrasObservacoes, AtAmbUnidade = :iUnidade
					WHERE AtAmbId = :iAtendimentoAmbulatorial";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => $_POST['inputFim'],
				':sProfissional' => $userId,
				':sQueixaPrincipal' => $_POST['txtareaConteudo1'],
				':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'],
				':sExameFisico' => $_POST['txtareaConteudo3'],
				':sSuspeitaDiagnostico' => $_POST['txtareaConteudo4'],
				':sExameSolicitado' => $_POST['txtareaConteudo5'],
				':sPrescricao' => $_POST['txtareaConteudo6'],
				':sOutrasObservacoes' => $_POST['txtareaConteudo7'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoAmbulatorial' => $iAtendimentoAmbulatorialId 
				));

			$_SESSION['msg']['mensagem'] = "Atendimento Ambulatorial alterado!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoAmbulatorial (AtAmbAtendimento, AtAmbData, AtAmbHoraInicio, AtAmbHoraFim, AtAmbProfissional, AtAmbQueixaPrincipal, AtAmbHistoriaMolestiaAtual,
			 											AtAmbExameFisico, AtAmbSuspeitaDiagnostico, AtAmbExameSolicitado, AtAmbPrescricao, AtAmbOutrasObservacoes, AtAmbUnidade)
						VALUES (:sAtendimento, :dData, :sHoraInicio, :sHoraFim, :sProfissional,:sQueixaPrincipal,:sHistoriaMolestiaAtual, :sExameFisico, 
						        :sSuspeitaDiagnostico, :sExameSolicitado, :sPrescricao, :sOutrasObservacoes, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dData' => gravaData($_POST['inputData']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sQueixaPrincipal' => $_POST['txtareaConteudo1'],
				':sHistoriaMolestiaAtual' => $_POST['txtareaConteudo2'],
				':sExameFisico' => $_POST['txtareaConteudo3'],
				':sSuspeitaDiagnostico' => $_POST['txtareaConteudo4'],
				':sExameSolicitado' => $_POST['txtareaConteudo5'],
				':sPrescricao' => $_POST['txtareaConteudo6'],
				':sOutrasObservacoes' => $_POST['txtareaConteudo7'],
				':iUnidade' => $_SESSION['UnidadeId'],
			));

			$_SESSION['msg']['mensagem'] = "Atendimento Ambulatorial incluído!!!";

		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com o Atendimento Ambulatorial!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoAmbulatorial.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Atendimento Ambulatorial</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
    <script src="../../../../global_assets/js/demo_pages/components_collapsible.js"></script>

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

			$('#summernote1').summernote();
            $('#summernote2').summernote();
            $('#summernote3').summernote();
            $('#summernote4').summernote();
            $('#summernote5').summernote();
            $('#summernote6').summernote();
            $('#summernote7').summernote();
	
        
		$('#enviar').on('click', function(e){
			
			e.preventDefault();
	

			$( "#formAtendimentoAmbulatorial" ).submit();
					
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
						<form name="formAtendimentoAmbulatorial" id="formAtendimentoAmbulatorial" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>ATENDIMENTO AMBULATORIAL</b></h3>
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
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php if (isset($iAtendimentoAmbulatorialId )){ echo $DataAtendimento;} else { echo date('d/m/Y'); } ?>" readOnly> 
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputInicio">Início do Atendimento</label>
												<input type="text" id="inputInicio" name="inputInicio" class="form-control"  value="<?php if (isset($iAtendimentoAmbulatorialId )){ echo $HoraInicio;} else { echo date('H:i'); } ?>" readOnly>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputFim">Témino do Atendimento</label>
												<input type="text" id="inputFim" name="inputFim" class="form-control" value="<?php if (isset($iAtendimentoAmbulatorialId )) echo $HoraFim; ?>" readOnly>
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
                                                <a href="#collapse1-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.0. Queixa Principal (QP)</a>   
                                                <div class="collapse" id="collapse1-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote1" name="txtareaConteudo1" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça na queixa principal)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbQueixaPrincipal']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse2-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.1. História da Moléstia Atual (HMA)</a>   
                                                <div class="collapse" id="collapse2-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote2" name="txtareaConteudo2" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça nna história da moléstia atual)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbHistoriaMolestiaAtual']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse3-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.2. Exame Físico</a>   
                                                <div class="collapse" id="collapse3-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote3" name="txtareaConteudo3" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça no exame físico)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbExameFisico']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse4-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.3. Suspeita Diagnóstico</a>   
                                                <div class="collapse" id="collapse4-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote4" name="txtareaConteudo4" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça na suspeita de diaginostico)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbSuspeitaDiagnostico']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse5-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.4. Exame Solicitado</a>   
                                                <div class="collapse" id="collapse5-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote5" name="txtareaConteudo5" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça na solicitação de exames)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbExameSolicitado']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse6-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.5. Prescrição</a>   
                                                <div class="collapse" id="collapse6-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote6" name="txtareaConteudo6" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça na prescrição)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbPrescricao']; ?> </textarea>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-lg-12">
											<div class="form-group">
                                                <a href="#collapse7-link" class="font-weight-semibold collapsed" data-toggle="collapse" aria-expanded="false"> 1.6. Outras Observações</a>   
                                                <div class="collapse" id="collapse7-link" style="">
                                                    <div class="mt-3">
                                                        <textarea rows="5" cols="5"  id="summernote7" name="txtareaConteudo7" class="form-control" placeholder="Corpo do ambulatorial (informe aqui o texto que você queira que apareça no observação)" > <?php if (isset($iAtendimentoAmbulatorialId )) echo $rowAmbulatorial['AtAmbOutrasObservacoes']; ?> </textarea>
                                                    </div>
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
