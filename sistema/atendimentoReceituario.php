<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Receituário';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT TOP(1) AtRecId
FROM AtendimentoReceituario
WHERE AtRecAtendimento = $iAtendimentoId
ORDER BY AtRecId DESC";
$result = $conn->query($sql);
$rowReceituario= $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoReceituarioId = $rowReceituario?$rowReceituario['AtRecId']:null;

// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';


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
if(isset($iAtendimentoReceituarioId ) && $iAtendimentoReceituarioId ){

	//Essa consulta é para preencher o campo Receituário ao editar
	$sql = "SELECT AtRecReceituario, AtRecHoraFim, AtRecHoraInicio, AtRecDataInicio, AtRecDataFim
			FROM AtendimentoReceituario
			WHERE AtRecId = " . $iAtendimentoReceituarioId ;
	$result = $conn->query($sql);
	$rowReceituario = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();

	// Formatar Hora/Data

	$DataInicio = strtotime($rowReceituario['AtRecDataInicio']);
	$DataAtendimentoInicio = date("d/m/Y", $DataInicio);

	$DataFim = strtotime($rowReceituario['AtRecDataFim']);
	$DataAtendimentoFim = date("d/m/Y", $DataFim);

	$Inicio = strtotime($rowReceituario['AtRecHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($rowReceituario['AtRecHoraFim']);
	$HoraFim = date("H:i", $Fim);

} 



//Se estiver gravando (inclusão ou edição)
if (isset($_POST['txtareaConteudo']) ){
	try{
		//Edição
		if ($iAtendimentoReceituarioId){
		
			$sql = "UPDATE AtendimentoReceituario SET AtRecAtendimento = :sAtendimento, AtRecDataInicio = :dDataInicio, AtRecDataFim = :dDataFim, AtRecHoraInicio = :sHoraInicio,
						   AtRecHoraFim  = :sHoraFim, AtRecProfissional = :sProfissional, AtRecReceituario = :sReceituario, AtRecUnidade = :iUnidade
					WHERE AtRecId = :iAtendimentoReceituario";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dDataInicio' => gravaData($_POST['inputDataInicio']),
				':dDataFim' => date('m/d/Y'),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => $_POST['inputFim'],
				':sProfissional' => $userId,
				':sReceituario' => $_POST['txtareaConteudo'],
				':iUnidade' => $_SESSION['UnidadeId'],
				':iAtendimentoReceituario' => $iAtendimentoReceituarioId 
				));

			$_SESSION['msg']['mensagem'] = "Receituário alterada!!!";
			

		} else { //inclusão

			$sql = "INSERT INTO AtendimentoReceituario (AtRecAtendimento, AtRecDataInicio, AtRecDataFim, AtRecHoraInicio, AtRecHoraFim, AtRecProfissional, AtRecReceituario, AtRecUnidade)
						VALUES (:sAtendimento, :dDataInicio, :dDataFim, :sHoraInicio, :sHoraFim, :sProfissional,:sReceituario, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
				':sAtendimento' => $iAtendimentoId,
				':dDataInicio' => gravaData($_POST['inputDataInicio']),
				':dDataFim' => gravaData($_POST['inputDataFim']),
				':sHoraInicio' => $_POST['inputInicio'],
				':sHoraFim' => date('H:i'),
				':sProfissional' => $userId,
				':sReceituario' => $_POST['txtareaConteudo'],
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

	irpara("atendimentoReceituario.php");
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
	

			$( "#formAtendimentoReceituario" ).submit();
					
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
						<form name="formAtendimentoReceituario" id="formAtendimentoReceituario" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>RECEITUÁRIO</b></h3>
								</div>
							</div>

							<div> <?php include ('atendimentoDadosPaciente.php'); ?> </div>

							<div class="card">

								<div class="card-body">

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="inputNome">Receituário do Paciente </label>
												<textarea rows="5" cols="5"  id="summernote" name="txtareaConteudo" class="form-control" placeholder="Corpo do receituário (informe aqui o texto que você queira que apareça no receituário)" > <?php if (isset($iAtendimentoReceituarioId )) echo $rowReceituario['AtRecReceituario']; ?> </textarea>
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
