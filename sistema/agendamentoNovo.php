<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Agendamento';

include('global_assets/php/conexao.php');

// a requisição é feita ao carregar a página via AJAX no arquivo filtraAtendimento.php

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Agendamentos</title>

	<?php include_once("head.php"); ?>
	<style>
		table td{
			padding: 1rem !important;
		}
	</style>

	<!-- Theme JS files -->
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>	

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
    
    <!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script type="text/javascript" >			
		$(document).ready(function() {
			$('#incluirAgendamento').on('click', function(e){
				e.preventDefault();
				console.log('Incluir Serviço');
			})

			$('.btnSalvar').each(function() {
				let target = $(this).data('tipo');
				$(this).on('click', (e) => {
					e.preventDefault();
					console.log(target);
				})
			});
			$('#cmbSituacao').on('change', function(e){
				e.preventDefault();
				let val = $('#cmbSituacao').val();
				console.log(val);
			})

			$('#addPaciente').on('click', function(e){
				e.preventDefault();
				$('#page-modal-situacao').fadeIn();
			})

			$('#modal-close-x').on('click', ()=>{
				$('#iAtendimento').val('')
				$('#page-modal-situacao').fadeOut(200);
			})

			// vai preencher cmbPaciente
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'PACIENTES'
				},
				success: function(response) {
					$('#paciente').empty();
					$('#paciente').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#paciente').append(opt)
					})
				}
			});
			// vai preencher cmbModalidade
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MODALIDADES'
				},
				success: function(response) {
					$('#modalidade').empty();
					$('#modalidade').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#modalidade').append(opt)
					})
				}
			});
			// vai preencher cmbServicos
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SERVICOS'
				},
				success: function(response) {
					$('#servico').empty();
					$('#servico').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#servico').append(opt)
					})
				}
			});
			// vai preencher cmbMedicos
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MEDICOS'
				},
				success: function(response) {
					$('#medico').empty();
					$('#medico').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#medico').append(opt)
					})
				}
			});
			// vai preencher cmbLocalAtendimento
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'LOCALATENDIMENTO'
				},
				success: function(response) {
					$('#localAtendimento').empty();
					$('#localAtendimento').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#localAtendimento').append(opt)
					})
				}
			});

		});

		alteraSituacao();

		function alteraSituacao(situacao, element){
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SITUACOES'
				},
				success: function(response) {
					$('#iAgendamento').val($(element).data('agendamento'))
					$('#cmbSituacao').empty()
					response.forEach(item => {
						let opt = item.SituaChave === situacao? `<option selected value="${item.SituaId}">${item.SituaNome}</option>`:`<option value="${item.SituaId}">${item.SituaNome}</option>`
						$('#cmbSituacao').append(opt)
					})
				}
			});
		}
	</script>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">	
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<!-- dados do agendamento -->
							<div id="agendamento" class="formDados mt-4 pl-2" style="display: block">
								<form id="submitAgendamento" method="POST">
									<div class="col-lg-12 my-3 text-black-50">
										<h5>Novo Agendamento</h5>
									</div>

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-3">
											<label for="prontuario">Data do registro <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-6">
											<label for="prontuario">Paciente <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-3">
											<label for="prontuario">Modalidade <span class="text-danger">*</span></label>
										</div>

										<!-- campos -->
										<div class="col-lg-3">
											<input id="data" name="data" type="date" class="form-control">
										</div>
										<div class="col-lg-5">
											<select id="paciente" name="paciente" class="form-control form-control-select2">
												<!--  -->
											</select>
										</div>
										<div class="col-lg-1" style="max-width:50px">
											<i class="icon-add py-2" id="addPaciente" style="cursor: pointer; font-size:25px;"></i>
										</div>
										<div class="col-lg-3">
											<select id="modalidade" name="modalidade" class="form-control form-control-select2">
												<!--  -->
											</select>
										</div>
									</div>

									<div class="col-lg-12 my-3 text-black-50">
										<h5>Serviços</h5>
									</div>

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-2">
											<label for="prontuario">Serviços <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-3">
											<label for="prontuario">Médicos <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-2">
											<label for="prontuario">Data do Atendimento <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-2">
											<label for="prontuario">Horário <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-3">
											<label for="prontuario">Local do Atendimento <span class="text-danger">*</span></label>
										</div>

										<!-- campos -->
										<div class="col-lg-2">
											<select id="servico" name="servico" class="form-control form-control-select2">
												<!--  -->
											</select>
										</div>
										<div class="col-lg-3">
											<select id="medico" name="medico" class="form-control form-control-select2">
												<!--  -->
											</select>
										</div>
										<div class="col-lg-2">
											<input id="dataAtendimento" name="dataAtendimento" type="date" class="form-control">
										</div>
										<div class="col-lg-2">
											<input type="time" class="form-control" value="">
										</div>
										<div class="col-lg-3">
											<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2">
												<!--  -->
											</select>
										</div>
									</div>

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-12">
											<label for="prontuario">Observação</label>
										</div>

										<!-- campos -->
										<div class="col-lg-12">
											<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
										</div>
									</div>

									<div class="col-lg-12">
										<label for="cmbSituacao">Situação <span class="text-danger">*</span></label>
										<div class="form-group col-lg-2">
											<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" required>
												<!--  -->
											</select>
										</div>
									</div>

									<!-- botões -->
									<div class="col-lg-12 ml-2 my-4 row">
										<button class="btn btn-lg btn-principal btnSalvar" id="salvarAgendamento" data-tipo="AGENDAMENTO" >salvar</button>
										<a href="agendamento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
									</div>
								</form>

								<!--Modal Editar Situação-->
								<div id="page-modal-situacao" class="custon-modal">
									<div class="custon-modal-container" style="min-width: 350px;">
										<div class="card custon-modal-content">
											<div class="custon-modal-title mb-2">
												<p class="h3">Cadastrar Paciente</p>
											</div>
											<form id="editaSituacao" name="incluirProduto" method="POST" class="form-validate-jquery">
												<div class="px-3 ">
													<div class="d-flex flex-row ">
														<div class="col-lg-12">
															<div class="col-lg-12 row">
																<div class="col-lg-12">
																	<label for="prontuario">Nome <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-12">
																	<input id="nomePaciente" require name="nomePaciente" type="text" class="form-control">
																</div>
															</div>
															<div class="col-lg-12 my-4 row">
																<div class="col-lg-4">
																	<label for="prontuario">Telefone <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-4">
																	<label for="prontuario">Celular <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-4">
																	<label for="prontuario">E-mail <span class="text-danger">*</span></label>
																</div>

																<div class="col-lg-4">
																	<input require id="telefone" name="telefone" type="text" class="form-control" placeholder="Res./Com.">
																</div>
																<div class="col-lg-4">
																	<input require id="celular" name="celular" type="text" class="form-control" placeholder="Celular">
																</div>
																<div class="col-lg-4">
																	<input require id="email" name="email" type="text" class="form-control" placeholder="E-mail">
																</div>
															</div>
															<div class="col-lg-12 mb-4 row">
																<!-- titulos -->
																<div class="col-lg-12">
																	<label for="prontuario">Observação</label>
																</div>

																<!-- campos -->
																<div class="col-lg-12">
																	<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-12 ml-2 my-4 row text-right">
													<button class="btn btn-lg btn-principal btnSalvar" id="salvarPaciente" data-tipo="PACIENTE" >incluir</button>
													<button type="button" class="btn btn-link legitRipple" id="modal-close-x">Cancelar</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include_once("footer.php"); ?>
		</div>
	</div>
	<?php include_once("alerta.php"); ?>
</body>

</html>
