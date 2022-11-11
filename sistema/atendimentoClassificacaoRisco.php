<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Atestado Médico';

include('global_assets/php/conexao.php'); 

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

$sql = "SELECT AtendId, AtendClassificacaoRisco, AtClRId, AtClRNome, AtClRTempo, AtClRCor, AtClRDeterminantes
		FROM Atendimento
		LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
	    WHERE AtendUnidade = ". $_SESSION['UnidadeId'] ." AND AtendId = ". $iAtendimentoId ." 
		ORDER BY AtClRNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);


	if(isset($_POST['cmbClassificacaoRisco'])){
	
		try{
				
			$sql = "UPDATE Atendimento SET AtendClassificacaoRisco = :sClassificacaoRisco, AtendUsuarioAtualizador = :iUsuarioAtualizador
					WHERE AtendId = :iAtendimento";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sClassificacaoRisco' => $_POST['cmbClassificacaoRisco'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iAtendimento' => $iAtendimentoId
							));

			$_SESSION['msg']['titulo'] = "Sucesso";
			$_SESSION['msg']['mensagem'] = "ClassificacaoRisco incluído!!!";
			$_SESSION['msg']['tipo'] = "success";
			
		} catch(PDOException $e) {
			
			$_SESSION['msg']['titulo'] = "Erro";
			$_SESSION['msg']['mensagem'] = "Erro ao incluir ClassificacaoRisco!!!";
			$_SESSION['msg']['tipo'] = "error";		
			
			echo 'Error: ' . $e->getMessage();
		}
	}
	
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Classificação de Risco</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>


	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
	
	<script type="text/javascript">
		
        
	$('#enviar').on('click', function(e){
		
		e.preventDefault();


		$( "#formClassificacaoRisco" ).submit()	
		
	})
		
	
	</script>

</head>

<body class="navbar-top">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php
			include_once("menu-left.php");
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->
				<div class="card">
					
					<form id="formClassificacaoRisco" name="formClassificacaoRisco" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold"> Classificação de Risco</h5>
						</div>
						
						<div class="card-body">								
							
							<div class="media">								
								
								<div class="media-body">
									<div class="row">	
										<div class="col-lg-8">
											<label for="cmbClassificacaoRisco">Classificação de Risco<span class="text-danger"> *</span></label>
											<select id="cmbClassificacaoRisco" name="cmbClassificacaoRisco" class="form-control select-search" required>
												<option value="">Selecione</option>
												<?php 
													$sql = "SELECT AtClRId, AtClRNome,AtClRTempo, AtClRCor, AtClRDeterminantes
															FROM AtendimentoClassificacaoRisco
															JOIN Situacao ON SituaId = AtClRStatus
															WHERE AtClRUnidade = " . $_SESSION['UnidadeId'] . " AND SituaChave = 'ATIVO'
														    ORDER BY AtClRNome ASC";
													$result = $conn->query($sql);
													$rowGrupo = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowGrupo as $item) {
														if ($item['AtClRId'] == $row['AtendClassificacaoRisco']) {
														  print('<option value="' . $item['AtClRId'] . '" selected="selected">' . $item['AtClRNome'] . '</option>');
														} else {
														  print('<option value="' . $item['AtClRId'] . '">' . $item['AtClRNome'] . '</option>');
														}
													}

												?>
											</select>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputTempo">Tempo (min)<span class="text-danger">*</span></label>
												<input type="number" id="inputTempo" name="inputTempo" class="form-control" placeholder="Tempo">
											</div>
										</div>
										<div class="col-lg-2">
											<label for="inputTempo">Cor<span class="text-danger">*</span></label>
											<div class="form-group" style="margin-left: 10px; margin-Top: 5px; height: 40px; width: 40px; background-color: ; border-radius: 50px;" >
                                            </div>
                                        </div>
										
									</div>
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDeterminantes">Determinantes Gerais</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDeterminantes" name="txtDeterminantes" placeholder="Determinantes"></textarea>
											</div>
										</div>
									</div>
																		
								</div> <!-- media-body -->
								
							</div> <!-- media -->

							<br>
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
										<a href="atendimento.php" class="btn btn-lg" role="button">Cancelar</a>
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

	<?php include_once("alerta.php"); ?>

</body>

</html>
