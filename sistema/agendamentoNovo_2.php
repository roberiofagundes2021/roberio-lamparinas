<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Agendamento';
$_SESSION['agendaProfissional'] = [];

include('global_assets/php/conexao.php');

// as duas lista "$visaoAtendente" e "$visaoProfissional" representam os perfis
// que podem ver a tela de atendimento na visão do atendente ou profissional respectivamente

$iUnidade = $_SESSION['UnidadeId'];
$iEmpresa = $_SESSION['EmpreId'];
$usuarioId = $_SESSION['UsuarId'];

$sql = "SELECT P.ProfiId as id,P.ProfiNome as nome,PF.ProfiCbo as cbo,PF.ProfiNome as profissao
	FROM Profissional P
	JOIN Profissao PF ON PF.ProfiId = P.ProfiProfissao
	WHERE P.ProfiUnidade = $iUnidade";
$result = $conn->query($sql);
$rowProfissionais = $result->fetchAll(PDO::FETCH_ASSOC);

// $sql = "SELECT UnidaId,UnidaNome
// 	FROM Unidade
// 	WHERE UnidaId = $iUnidade";
// $result = $conn->query($sql);
// $Unidade = $result->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Agendamento</title>

	<?php include_once("head.php"); ?>

	<!-- ///////////////////////////////////////////////////////////////////////////////////// -->
	<!-- Theme JS files -->
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>

    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<!-- /theme JS files -->	

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/plugins/ui/fullcalendar/fullcalendar.min.js"></script>
	<script src="global_assets/js/plugins/ui/fullcalendar/lang/pt-br.js"></script>
	<style>
		.excluirContainer {
			width: 100%;
			height: 220px;
			padding: 10px;
			background-color: #ccc;
			color: #333;
			opacity: 0.2;
			border: 1px solid #333;
		}
		textarea{
            height:80px;
        }
		.btnCuston{
			text-transform: uppercase;
			padding: 10px;
			font-size: 12px;
			line-height: 1.3;
			border-radius: 0.25rem;
			border: 0px;
		}
	</style>
	<?php
		echo "<script>
				iUnidade = $iUnidade
				iEmpresa = $iEmpresa
			</script>"
	?>

	<script type="text/javascript">
		var viwerCalendar = 'agendaWeek'
		var selectCalendar = false
		// const socket = WebSocketConnect(iUnidade,iEmpresa)
		// socket.onmessage = function (event) {
		// 	menssage = JSON.parse(event.data)
		// 	if(menssage.type == 'AGENDA'){
		// 		getAgenda()
		// 	}
		// };
		$(document).ready(function(){
			getAgenda()
			getCmbs()

			$('#salvarAgenda').on('click', ()=>{
				$('#salvarAgenda').html("<img src='global_assets/images/lamparinas/loader-transparente2.gif' style='width: 17px'>");
				$("#salvarAgenda").prop('disabled', true);
				$.ajax({
					type: 'POST',
					url: 'filtraProfissionalAgenda.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SALVAAGENDA',
						'iProfissional': $('#iProfissional').val()
					},
					success: function(response) {
						alerta(response.titulo, response.menssagem, response.status)
						// window.location.href = "profissional.php"
						getAgenda()
						$('#salvarAgenda').html('Salvar');
						$("#salvarAgenda").prop('disabled', false);
						socket.sendMenssage({
							'type':'AGENDA'
						});
					}
				});
			})
			$('#modal-close-x').on('click', function(){
				$('#page-modal-horario').fadeOut(200);
			})
			$('#definirHorario').on('click', function(e){
				e.preventDefault()
				$('#page-modal-horario').fadeOut(200)

				let id = $('#idEvent').val()
				let horaAgendaInicio = $('#horaAgendaInicio').val()
				let horaAgendaFim = $('#horaAgendaFim').val()
				let horaIntervalo = $('#horaIntervalo').val()

				$.ajax({
					type: 'POST',
					url: 'filtraProfissionalAgenda.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SETHORAAGENDA',
						'id':id,
						'horaAgendaInicio':horaAgendaInicio,
						'horaAgendaFim':horaAgendaFim,
						'horaIntervalo':horaIntervalo,
					},
					success: function(response){
						refreshAgenda()
						alerta(response.titulo, response.menssagem, response.status)
					}
				});
			})
			$('#profissional').on('change', function(e){
				e.preventDefault()
				getAgenda()
			})

			$("#textObservacao").on('input', function(e){
                cantaCaracteres('textObservacao', 800, 'caracteresInputObservacao')
            })

			$('#novoAgendamento').on('click', function(e){
				e.preventDefault()
				const date = new Date();

				let dia = date.getDate() > 9?date.getDate():`0${date.getDate()}`
				let mes = date.getMonth()+1 > 9?date.getMonth()+1:`0${date.getMonth()+1}`
				let ano = date.getFullYear()

				let hora = date.getHours()>9?date.getHours():`0${date.getHours()}`
				let minuto = date.getMinutes()>9?date.getMinutes():`0${date.getMinutes()}`

				$('#inputData').val(`${ano}-${mes}-${dia}`)
				$('#inputHora').val(`${hora}:${minuto}`)
				$('#textObservacao').val('')
				$('#idAgendamento').val('')
				getCmbs()

				$('#page-modal-agendamento').fadeIn(200)
			})
			$('#modal-close-x').on('click', function(e){
				e.preventDefault()
				$('#page-modal-agendamento').fadeOut(200)
			})

			$('#formAgendamentoNovo').submit(function(e){
				e.preventDefault()
			})
			$('#novoPaciente').submit(function(e){
				e.preventDefault()
			})
			$('#formFiltro').submit(function(e){
				e.preventDefault()
			})

			$('#addPaciente').submit(function(e){
				e.preventDefault()
			})

			$('#servico').on('change', function(e){
				// vai preencher cmbMedico
				if($(this).val()){
					$.ajax({
						type: 'POST',
						url: 'filtraProfissionalAgenda_2.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'MEDICOS',
							'servico': $(this).val(),
							'data': $('#inputData').val(),
							'hora': $('#inputHora').val()
						},
						success: function(response) {
							$('#medico').empty()
							$('#medico').append(`<option value=''>Selecione</option>`)
							response.forEach(item => {
								let opt = `<option value="${item.id}">${item.nome}</option>`
								$('#medico').append(opt)
							})
						}
					});
				}else{
					$('#medico').empty()
					$('#medico').append(`<option value=''>Selecione</option>`)
				}
			})

			$('#medico').on('change', function(e){
				// vai preencher cmbLocal
				if($(this).val()){
					$.ajax({
						type: 'POST',
						url: 'filtraProfissionalAgenda_2.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'LOCALATENDIMENTO',
							'iMedico': $(this).val()
						},
						success: function(response) {
							$('#localAtendimento').empty()
							$('#localAtendimento').append(`<option value=''>Selecione</option>`)
							response.forEach(item => {
								let opt = `<option value="${item.id}">${item.nome}</option>`
								$('#localAtendimento').append(opt)
							})
						}
					});
				}else{
					$('#localAtendimento').empty()
					$('#localAtendimento').append(`<option value=''>Selecione</option>`)
				}
			})

			$('#inserirAgendamento').on('click', function(e){
				let menssageError = ''
				switch (menssageError) {
					case $('#data').val():
						menssageError = 'Informe a data!!';
						$('#data').focus();
						break;
					case $('#hora').val():
						menssageError = 'Informe o horário!!';
						$('#hora').focus();
						break;
					case $('#paciente').val():
						menssageError = 'Informe o paciente!!';
						$('#paciente').focus();
						break;
					case $('#modalidade').val():
						menssageError = 'Informe a modalidade!!';
						$('#modalidade').focus();
						break;
					case $('#servico').val():
						menssageError = 'Informe o serviço!!';
						$('#servico').focus();
						break;
					case $('#profissional').val():
						menssageError = 'Informe o profissional!!';
						$('#profissional').focus();
						break;
					case $('#localAtendimento').val():
						menssageError = 'Informe o local!!';
						$('#localAtendimento').focus();
						break;
					case $('#situacao').val():
						menssageError = 'Informe a Situação!!';
						$('#situacao').focus();
						break;
					default:
						menssageError = '';
						break;
				}

				if (menssageError) {
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				if(!$('#idAgendamento').val() && $('#inputData').val() < updateDateTime().dataAtual || ($('#inputData').val() == updateDateTime().dataAtual && $('#inputHora').val() < updateDateTime().horaAtual)){
					alerta('Data e Hora inválida!', 'Data e hora do registro não pode ser retroativa', 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraProfissionalAgenda_2.php',
					dataType: 'json',
					data: {
						'tipoRequest': 'ADDAGENDAMENTO',
						'data':$('#inputData').val(),
						'hora':$('#inputHora').val(),
						'paciente':$('#paciente').val(),
						'modalidade':$('#modalidade').val(),
						'servico':$('#servico').val(),
						'profissional':$('#medico').val(),
						'local':$('#localAtendimento').val(),
						'situacao':$('#situacao').val(),
						'observacao':$('#textObservacao').val(),
						'idAgendamento': $('#idAgendamento').val()
					},
					success: function(response) {
						$('#page-modal-agendamento').fadeOut(200)
						getAgenda()
					}
				});
			})

			$('#addPaciente').on('click', function(e){
				e.preventDefault();
				$('#page-modal-paciente').fadeIn();
			})
			$('#modalPaciente-close-x').on('click', () => {
				$('#iAtendimento').val('')
				$('#page-modal-paciente').fadeOut(200)
			})
			$('#salvarPacienteModal').on('click', function(e) {
				e.preventDefault()

				let menssageError = ''
				switch (menssageError) {
					case $('#nomeNew').val():
						menssageError = 'Informe o nome!!';
						$('#nomeNew').focus();
						break;
					case $('#telefoneNew').val() || $('#celularNew').val():
						menssageError = 'Informe um telefone ou celular!!';
						$('#telefoneNew').focus();
						break;
					case $('#emailNew').val():
						menssageError = 'Informe um E-mail!!';
						$('#emailNew').focus();
						break;
					default:
						menssageError = '';
						break;
				}

				if (menssageError) {
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				if($('#cpfNew').val()){
					var cpfSoNumeros = $('#cpfNew').val().replace(/[^\d]+/g, '');
					if(!validaCPF(cpfSoNumeros)){
						alerta('CPF Inválido!', 'Digite um CPF válido!!', 'error')
						return
					}
				}

				if($("#nascimentoNew").val()){
					let dataPreenchida = $("#nascimentoNew").val();
					if(!validaDataNascimento(dataPreenchida)){
						$('#nascimentoNew').val('');
						alerta('Atenção', 'Data de nascimento não pode ser futura!', 'error');
						$('#nascimentoNew').focus();
						return
					}
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
					dataType: 'json',
					data: {
						'tipoRequest': 'SALVARPACIENTE',
						'prontuario': $('#prontuarioNew').val(),
						'nome': $('#nomeNew').val(),
						'nomeSocial': $('#nomeSocialNew').val(),
						'cpf': cpfSoNumeros,
						'cns': $('#cnsNew').val(),
						'rg': $('#rgNew').val(),
						'emissor': $('#emissorNew').val(),
						'uf': $('#ufNew').val(),
						'sexo': $('#sexoNew').val(),
						'nascimento': $('#nascimentoNew').val(),
						'nomePai': $('#nomePaiNew').val(),
						'nomeMae': $('#nomeMaeNew').val(),
						'racaCor': $('#racaCorNew').val(),
						'naturalidade': $('#naturalidadeNew').val(),
						'profissao': $('#profissaoNew').val(),
						'estadoCivil': $('#estadoCivilNew').val(),
						'cep': $('#cepNew').val(),
						'endereco': $('#enderecoNew').val(),
						'numero': $('#numeroNew').val(),
						'complemento': $('#complementoNew').val(),
						'bairro': $('#bairroNew').val(),
						'cidade': $('#cidadeNew').val(),
						'estado': $('#estadoNew').val(),
						'contato': $('#contatoNew').val(),
						'telefone': $('#telefoneNew').val(),
						'celular': $('#celularNew').val(),
						'email': $('#emailNew').val(),
						'observacao': $('#observacaoNew').val()
					},
					success: async function(response) {
						if (response.status == 'success') {
							alerta(response.titulo, response.menssagem, response.status)
							getCmbs({'pacienteID': response.id})
							$('#page-modal-paciente').fadeOut(200)
						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#config').on('click', function(e){
				e.preventDefault()
				getFilters()
				$('#inputDataInicioBloqueio').val('')
				$('#inputHoraInicioBloqueio').val('')
				$('#inputDataFimBloqueio').val('')
				$('#inputHoraFimBloqueio').val('')
				$('#recorrente').prop('checked', false)
				$('#repeticao').prop('checked', false)
				$('#segunda').prop('checked', false)
				$('#terca').prop('checked', false)
				$('#quarta').prop('checked', false)
				$('#quinta').prop('checked', false)
				$('#sexta').prop('checked', false)
				$('#sabado').prop('checked', false)
				$('#domingo').prop('checked', false)
				$('#repeticao').val('')
				$('#quantidadeRecorrencia').val('')
				$('#dataRecorrencia').val('')
				$('#cardRecorrend').addClass('d-none')
				$('#page-modal-config').fadeIn(200)
			})
			$('#modalConfig-close-x').on('click', function(e){
				e.preventDefault()
				$('#page-modal-config').fadeOut(200)
			})

			$('#recorrente').on('change', function(e){
				if($('#recorrente').is(':checked')){
					$('#cardRecorrend').removeClass('d-none')
				}else{
					$('#cardRecorrend').addClass('d-none')
				}
			})

			$('#filtro').on('click', function(e){
				e.preventDefault()
				getFilters()
				$('#page-modal-filtro').fadeIn(200)
			})
			$('#modalFiltro-close-x').on('click', function(e){
				e.preventDefault()
				$('#page-modal-filtro').fadeOut(200)
			})

			$('#filtrarAgendamento').on('click', function(e){
				e.preventDefault()
				let obj = {
					'status':null,
					'recepcao':null,
				}
				if($('#statusFiltro').val()){
					obj.status = $('#statusFiltro').val()
				}
				if($('#recepcaoFiltro').val()){
					obj.recepcao = $('#recepcaoFiltro').val()
				}
				getAgenda(obj)
				$('#page-modal-filtro').fadeOut(200)
			})

			$('#selecionarCalendario').on('click',function(e){
				e.preventDefault()
				selectCalendar = true
				$('#page-modal-config').fadeOut(200)
				setTimeout(() => {
					selectCalendar = false
				}, 5000)
			})

			$('#salvarEvento').on('click', function(e){
				e.preventDefault()
				console.log($('#recorrente').is(':checked'))

				let msg = ''
				switch(msg){
					case $('#medicoConfig').val():msg="Informe o profissional";break;
					case $('#bloqueio').val():msg="Informe o Título de Bloqueio ";break;
					case $('#inputDataInicioBloqueio').val():msg="Informe a data de início";break;
					case $('#inputHoraInicioBloqueio').val():msg="Informe a hora de início";break;
					case $('#inputDataFimBloqueio').val():msg="Informe a data de fim";break;
					case $('#inputHoraFimBloqueio').val():msg="Informe a hora de fim";break;
					default:msg = '';break;
				}
				// caso seja recorrente...
				if($('#recorrente').is(':checked')){
					switch(msg){
						case $('#repeticao').val():msg="Informe a quantidade de repetiçoes";break;
						default:msg = '';break;
					}
				}

				if(msg){
					alerta('Campo Obrigatório', msg,'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraProfissionalAgenda_2.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADDEVENTO',
						'medicoConfig':$('#medicoConfig').val(),
						'bloqueio':$('#bloqueio').val(),
						'justificativa':$('#justificativa').val(),
						'inputDataInicioBloqueio':$('#inputDataInicioBloqueio').val(),
						'inputHoraInicioBloqueio':$('#inputHoraInicioBloqueio').val(),
						'inputDataFimBloqueio':$('#inputDataFimBloqueio').val(),
						'inputHoraFimBloqueio':$('#inputHoraFimBloqueio').val(),
						'repeticao':$('#repeticao').val(),
						'segunda':$('#segunda').is(':checked')?1:0,
						'terca':$('#terca').is(':checked')?1:0,
						'quarta':$('#quarta').is(':checked')?1:0,
						'quinta':$('#quinta').is(':checked')?1:0,
						'sexta':$('#sexta').is(':checked')?1:0,
						'sabado':$('#sabado').is(':checked')?1:0,
						'domingo':$('#domingo').is(':checked')?1:0,
						'repeticao':$('#repeticao').val(),
						'quantidadeRecorrencia':$('#quantidadeRecorrencia').val(),
						'dataRecorrencia':$('#dataRecorrencia').val(),
					},
					success: async function(response) {
						alerta(response.titulo, response.menssagem, response.status)
						$('#page-modal-config').fadeOut(200)
					}
				});
			})
		})

		function updateDateTime(){
			let dataAtual = new Date().toLocaleString("pt-BR", {timeZone: "America/Bahia"});
			let horaAtual = dataAtual.split(' ')[1];

			horaAtual = horaAtual.split(':');
			horaAtual = `${horaAtual[0]}:${horaAtual[1]}`
			
			dataAtual = dataAtual.split(' ')[0];
			dataAtual = dataAtual.split('/')[2]+'-'+dataAtual.split('/')[1]+'-'+dataAtual.split('/')[0];

			return {
				'dataAtual':dataAtual,
				'horaAtual':horaAtual
			}
		}

		function getAgenda(filtro){
			if($('div.fc-agendaWeek-view').length){
				viwerCalendar = 'agendaWeek'
			} else if($('div.fc-month-view').length){
				viwerCalendar = 'month'
			}

			// iniciar o calendário
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'AGENDAMENTOS',
					'profissionais': $('#profissional').val(),
					'status':filtro && filtro.status?filtro.status:null,
					'recepcao':filtro && filtro.recepcao?filtro.recepcao:null
				},
				success: async function(response) {
					clearModal()

					let events = []
					await response.forEach(item =>{
						let cor = ''
						switch(item.situacao.cor){
							case 'primary':cor = '#2196F3';break;
							case 'secondary':cor = '#777';break;
							case 'success':cor = '#4CAF50';break;
							case 'info':cor = '#00BCD4';break;
							case 'warning':cor = '#FF7043';break;
							case 'danger':cor = '#F44336';break;
							case 'light':cor = '#fafafa';break;
							case 'dark':cor = '#324148';break;
							case 'white':cor = '#fff';break;
							case 'blue':cor = '#03A9F4';break;
							case 'green':cor = '#8BC34A';break;
							case 'red':cor = '#d60000';break;
							case 'yellow':cor = '#f0ff1a';break;
							case 'black':cor = '#000';break;
							case 'orange':cor = '#ff6e00';break;
							default: cor='';bbreak;
						}
						events.push({
							id: item.id,
							title: item.cliente.nome,
							status: item.status,
							start: `${item.data} ${item.hora.split('.')[0]}`,
							end: null,
							color: cor,
						})
					})
					// reseta o calendário
					$('#fullcalendar-external').fullCalendar('destroy')
					
					// Initialize the calendar
					$('#fullcalendar-external').fullCalendar({
						header: {
							left: 'prev,next today',
							center: 'title',
							right: 'month,agendaWeek'
						},
						editable: true,
						defaultDate: updateDateTime().dataAtual,
						events: events,
						timeZone: 'America/Bahia',
						locale: 'pt-br',
						droppable: true,
						defaultView: viwerCalendar,
						selectable: true,
						eventDurationEditable:false,
						disableResizing: true,
						eventClick: function(event, jsEvent, view) {
							$.ajax({
								type: 'POST',
								url: 'filtraProfissionalAgenda_2.php',
								dataType: 'json',
								data:{
									'tipoRequest': 'GETAGENDAMENTO',
									'id': event.id
								},
								success: function(response){
									let readOnlyOption = response.data < updateDateTime().dataAtual || (response.data == updateDateTime().dataAtual && response.hora < updateDateTime().horaAtual)?true:false
									
									$('#idAgendamento').val(event.id)
									$('#inputData').val(response.data)
									$('#inputHora').val(response.hora)
									$('#textObservacao').val(response.observacao)
									$('#tituloModal').html('Editar Agendamento')

									getCmbs({
										'pacienteID':response.cliente,
										'modalidadeID':response.modalidade,
										'servicoID':response.servico,
										'medicoID':response.profissional,
										'localAtendimentoID':response.local,
										'situacaoID':response.situacao,
									})

									$('#inputData').attr('readonly', readOnlyOption)
									$('#inputHora').attr('readonly', readOnlyOption)
									$('#textObservacao').attr('readonly', readOnlyOption)
									$('#paciente').attr('disabled', readOnlyOption)
									$('#modalidade').attr('disabled', readOnlyOption)
									$('#servico').attr('disabled', readOnlyOption)
									$('#localAtendimento').attr('disabled', readOnlyOption)
									$('#medico').attr('disabled', readOnlyOption)
									// $('#situacao').attr('disabled', readOnlyOption)
									if(readOnlyOption){
										$('#addPaciente').addClass('d-none')
									}else{
										$('#addPaciente').removeClass('d-none')
									}

									$('#page-modal-agendamento').fadeIn(200)
								}
							})
						},
						eventDrop: function(event, jsEvent, ui, view) {
							let data = event.start.format()
							data = data.split('T')
							
							if(data[0] < updateDateTime().dataAtual || (data[0] == updateDateTime().dataAtual && data[1] < updateDateTime().horaAtual)){
								alerta('Data e Hora inválida!', 'Data e hora do registro não pode ser retroativa', 'error')
								getAgenda()
								return
							}

							$.ajax({
								type: 'POST',
								url: 'filtraProfissionalAgenda_2.php',
								dataType: 'json',
								data:{
									'tipoRequest': 'UPDATEDATA',
									'id': event.id,
									'data': data[0],
									'hora': data[1],
								},
								success: function(response){}
							})
						},
						select: function(start, end, jsEvent, view) {
							let inicio = start.format().split('T')
							let fim = end.format().split('T')
							$('#idAgendamento').val('')

							$('#tituloModal').html('Novo Agendamento')
							
							if(inicio[0] < updateDateTime().dataAtual || (inicio[0] == updateDateTime().dataAtual && inicio[1] < updateDateTime().horaAtual)){
								alerta('Data e Hora inválida!', 'Data e hora do registro não pode ser retroativa', 'error')
								getAgenda()
								return
							}

							if(selectCalendar){
								selectCalendar = false
								// getFilters()
								$('#inputDataInicioBloqueio').val(inicio[0])
								$('#inputDataFimBloqueio').val(fim[0])
								if(inicio[1]){
									$('#inputHoraInicioBloqueio').val(inicio[1])
									$('#inputHoraFimBloqueio').val(fim[1])
								}
								$('#page-modal-config').fadeIn(200)
							}else{
								getCmbs()
								$('#inputData').val(inicio[0])
								if(inicio[1]){
									$('#inputHora').val(inicio[1])
								}

								$('#inputData').attr('readonly', false)
								$('#inputHora').attr('readonly', false)
								$('#textObservacao').attr('readonly', false)
								$('#paciente').attr('disabled', false)
								$('#modalidade').attr('disabled', false)
								$('#servico').attr('disabled', false)
								$('#localAtendimento').attr('disabled', false)
								$('#medico').attr('disabled', false)
								$('#addPaciente').removeClass('d-none')
								$('#textObservacao').val('')

								$('#page-modal-agendamento').fadeIn(200)
							}
						},
						isRTL: false
					});
				}
			});
		}

		function formatDate(start,end){
			let dataI = ''
			let dataF = ''
			// as vezes ao vir do banco o campo "start" e "end" está como string,
			// e quando ele é criado aqui vem como array. Assim precisa desse switch.
			if(start){
				switch (typeof start){
					case 'object': // "[YYYY,MM,DD,HH,mm,ss]"
						let dia = start[2] > 9?start[2]:'0'+start[2]
						let mes = start[1] > 9?(start[1]+1):'0'+(start[1]+1)

						let hora = start[3] > 9?start[3]:'0'+start[3]
						let minuto = start[4] > 9?start[4]:'0'+start[4]

						dataI = start[0]+'-'+mes+'-'+dia+'T'+hora+':'+minuto+':00';
						dataI = new Date(dataI).toLocaleString("pt-BR", {timeZone: "America/Bahia"});
						break;
					default:dataI = new Date(start).toLocaleString("pt-BR", {timeZone: "America/Bahia"});break;
				}
			}
			if(end){
				switch (typeof end){
					case 'object': // "[YYYY,MM,DD,HH,mm,ss]"
						let dia = end[2] > 9?end[2]:'0'+end[2]
						let mes = end[1] > 9?(end[1]+1):'0'+(end[1]+1)

						let hora = end[3] > 9?end[3]:'0'+end[3]
						let minuto = end[4] > 9?end[4]:'0'+end[4]

						dataF = end[0]+'-'+mes+'-'+dia+'T'+hora+':'+minuto+':00';
						dataF = new Date(dataF).toLocaleString("pt-BR", {timeZone: "America/Bahia"});
						break;
					default:dataF = new Date(end).toLocaleString("pt-BR", {timeZone: "America/Bahia"});break;
				}
			}
			return {dataI:dataI,dataF:dataF}
		}

		function clearModal(){
			$('#horaAgendaInicio').val('')
			$('#horaAgendaFim').val('')
			$('#horaIntervalo').val('')	
		}

		function getCmbs(obj){
			// vai preencher cmbPaciente
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data: {
					'tipoRequest': 'PACIENTES'
				},
				success: function(response) {
					$('#paciente').empty()
					$('#paciente').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let id = obj && obj.pacienteID? obj.pacienteID:null
						let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						$('#paciente').append(opt)
					})
				}
			});

			// vai preencher cmbModalidade
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MODALIDADES'
				},
				success: function(response) {
					$('#modalidade').empty();
					$('#modalidade').append(`<option value=''>Selecione</option>`)
					
					response.forEach(item => {
						let id = obj && obj.modalidadeID? obj.modalidadeID:null
						let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						$('#modalidade').append(opt)
					})
				}
			});

			// vai preencher cmbServicos
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SERVICOS'
				},
				success: function(response) {
					$('#servico').empty();
					$('#servico').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let id = obj && obj.servicoID? obj.servicoID:null
						let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						$('#servico').append(opt)
					})
				}
			});

			// vai preencher cmbSituacao
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SITUACAO'
				},
				success: function(response) {
					$('#situacao').empty();
					$('#situacao').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let id = obj && obj.situacaoID? obj.situacaoID:null
						let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						$('#situacao').append(opt)
					})
				}
			});

			// vai preencher cmbLocalAtendimento
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'LOCALATENDIMENTO'
				},
				success: function(response) {
					$('#localAtendimento').empty();
					$('#localAtendimento').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let id = obj && obj.localAtendimentoID? obj.localAtendimentoID:null
						let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						$('#localAtendimento').append(opt)
					})
				}
			});

			if(obj && obj.servicoID){
				// vai preencher cmbMedico
				$.ajax({
					type: 'POST',
					url: 'filtraProfissionalAgenda_2.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'MEDICOS',
						'servico': obj.servicoID,
						'data': '',
						'hora': '',
					},
					success: function(response){
						$('#medico').empty()
						$('#medico').append(`<option value=''>Selecione</option>`)
						response.forEach(item => {
							let id = obj && obj.medicoID? obj.medicoID:null
							let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
							$('#medico').append(opt)
						})
					}
				});

				// if(obj && obj.medicoID){
				// 	// vai preencher cmbLocalAtendimento
				// 	$.ajax({
				// 		type: 'POST',
				// 		url: 'filtraProfissionalAgenda_2.php',
				// 		dataType: 'json',
				// 		data:{
				// 			'tipoRequest': 'LOCALATENDIMENTO',
				// 			'iMedico': obj.medicoID
				// 		},
				// 		success: function(response) {
				// 			$('#localAtendimento').empty();
				// 			$('#localAtendimento').append(`<option value=''>Selecione</option>`)
				// 			response.forEach(item => {
				// 				let id = obj && obj.localAtendimentoID? obj.localAtendimentoID:null
				// 				let opt = id == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
				// 				$('#localAtendimento').append(opt)
				// 			})
				// 		}
				// 	});


				// }
			}else{
				$('#medico').empty()
				$('#medico').append(`<option value=''>Selecione</option>`)

				$('#localAtendimento').empty();
				$('#localAtendimento').append(`<option value=''>Selecione</option>`)
			}
		}

		function getFilters(){
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MEDICOS'
				},
				success: function(response){
					$('#medicoConfig').empty()
					$('#medicoConfig').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#medicoConfig').append(opt)
					})
				}
			});
			$.ajax({
				type: 'POST',
				url: 'filtraProfissionalAgenda_2.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SITUACAO'
				},
				success: function(response){
					$('#statusFiltro').empty()
					$('#statusFiltro').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#statusFiltro').append(opt)
					})
				}
			});
			// falta o de recepcaoFiltro
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
				<div class="card">
					<div class="card-header header-elements-inline">
						<h3 class="card-title">Relação de Agendamentos</h3>
					</div>
					
					<div class="card-body">
						<div class="row">
							<!-- titulo -->
							<div class="col-lg-4">Filtrar Profissionais</div>
							<div class="col-lg-4"></div>
							<div class="col-lg-4"></div>
							
							<!-- campos -->
							<div class="col-lg-4">
								<select id="profissional" name="profissional[]" class="form-control multiselect-filtering" multiple="multiple">
									<?php
										foreach($rowProfissionais as $item){
											echo "<option value='$item[id]' selected>$item[nome] - $item[cbo] - $item[profissao]</option>";
										}
									?>
								</select>
							</div>

							<div class="col-lg-4"></div>

							<div class="col-lg-4 text-right">
								<div class="text-right">
									<i id="config" class="fab-icon-open icon-gear p-3" style="cursor: pointer"></i>
									<i id="filtro" class="fab-icon-open icon-filter3 p-3" style="cursor: pointer"></i>
									<button id="novoAgendamento" class='btn btn-principal'>Novo Agendamento</button>
									<a href="#collapse-imprimir-relacao" class="btn bg-slate-700 btn-icon" role="button" data-toggle="collapse" data-placement="bottom" data-container="body">
										<i class="icon-printer2"></i>																						
									</a>
								</div>
							</div>	
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<div id="fullcalendar-external"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!--Modal Editar Situação-->
			<div id="page-modal-agendamento" class="custon-modal">
				<div class="custon-modal-container" style="max-width: 900px;">
					<div class="card custon-modal-content">
						<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
							<p id="tituloModal" class="h5">Novo Agendamento</p>
							<i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
						</div>
						<div class="px-0">
							<input type="hidden" id="idAgendamento" name="idAgendamento" value="">
							<form id="formAgendamentoNovo" class="form-validate-jquery">
								<div class="col-lg-12 p-0">
									<!-- linha 1 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-2">Data <span class="text-danger">*</span></div>
										<div class="col-lg-2">Hora <span class="text-danger">*</span></div>
										<div class="col-lg-5">Paciente <span class="text-danger">*</span></div>
										<div class="col-lg-3">Modalidade <span class="text-danger">*</span></div>

										<div class="col-lg-2">
											<input type="date" id="inputData" name="inputData" class="form-control" required value="<?php echo date('Y-m-d')?>">
										</div>
										<div class="col-lg-2">										
											<input type="time" id="inputHora" name="inputHora" class="form-control" required value="<?php echo date('H:i')?>">
										</div>	
										<div class="col-lg-5 row">
											<div class="col-lg-9">
												<select id="paciente" name="paciente" readonly class="form-control select-search" required></select>
											</div>
												<div class="col-lg-3">
												<span class="action btn btn-principal legitRipple" id="addPaciente" style="user-select: none;">
													<i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
												</span>
											</div>
										</div>
										<div class="col-lg-3">
											<select id="modalidade" name="modalidade" class="select-search" required></select>
										</div>
									</div>

									<!-- linha 2 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-12 my-3 text-black-50">
											<h5 class="mb-0 font-weight-semibold">Serviços</h5>
										</div>

										<div class="col-lg-12 mb-2 row">
											<!-- titulos -->
											<div class="col-lg-4">
												<label>Serviços <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Médicos <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Local do Atendimento <span class="text-danger">*</span></label>
											</div>

											<!-- campos -->
											<div class="col-lg-4">
												<select id="servico" name="servico" class="select-search" required>
													<option value=''>Selecione</option>
												</select>
											</div>
											<div class="col-lg-4">
												<select id="medico" name="medico" class="select-search" required>
													<option value=''>Selecione</option>
												</select>
											</div>
											<div class="col-lg-4">
												<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2" required>
													<option value=''>Selecione</option>
												</select>
											</div>
										</div>
									</div>

									<div class="col-lg-12 p-2 m-0">
										<div class="col-lg-12">Observações</div>
										<div class="col-lg-12">
											<textarea id="textObservacao" name="textObservacao" class="form-control" rows="4" cols="4" maxLength="800" placeholder="Digite aqui as observações..."></textarea>
											<small class="text-muted form-text">
												Máx. 800 caracteres<br>
												<span id="caracteresInputObservacao"></span>
											</small>
										</div>
									</div>

									<div class="col-lg-12 p-2 m-0">
										<div class="col-lg-4">Situação <span class="text-danger">*</span></div>
										<div class="col-lg-8"></div>

										<div class="col-lg-4">
											<select id="situacao" name="situacao" class="form-control form-control-select2" required></select>
										</div>
										<div class="col-lg-8"></div>
									</div>

									<!-- linha X -->
									<div class="col-lg-12 py-3" style="margin-top: -5px;">
										<div class="col-lg-4">
											<button class="btn btn-lg btn-principal" id="inserirAgendamento">Salvar</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<div id="page-modal-paciente" class="custon-modal">
				<div class="custon-modal-container" style="max-width: 800px; height: 95%;">
					<div class="card custon-modal-content" style="height: 95%;">
						<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
							<p class="h5">Novo paciente</p>
							<i id="modalPaciente-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
						</div>
						<div class="px-0" style="overflow-y: scroll;">
							<div class="d-flex flex-row">
								<div class="col-lg-12">
									<form id="novoPaciente" name="alterarSituacao" method="POST" class="form-validate-jquery">
										<div class="form-group">

											<div class="card-header header-elements-inline" style="margin-left: -10px;">
												<h5 class="text-uppercase font-weight-bold">Dados Pessoais do paciente</h5>
											</div>

											<div class="col-lg-12 mb-4 row">
												<!-- titulos -->
												<div class="col-lg-6">
													<label>Nome <span class="text-danger">*</span></label>
												</div>
												<div class="col-lg-6">
													<label>Nome Social</label>
												</div>

												<!-- campos -->
												<div class="col-lg-6">
													<input id="nomeNew" name="nomeNew" type="text" class="form-control" placeholder="Nome completo" required>
												</div>
												<div class="col-lg-6">
													<input id="nomeSocialNew" name="nomeSocialNew" type="text" class="form-control" placeholder="Nome Social">
												</div>
											</div>

											<div class="col-lg-12 my-3 text-black-50">
												<h5 class="mb-0 font-weight-semibold">Contato</h5>
											</div>

											<div class="col-lg-12 mb-4 row">
												<!-- titulos -->
												<div class="col-lg-4">
													<label>Nome</label>
												</div>
												<div class="col-lg-2">
													<label>Telefone <span class="text-danger">*</span></label>
												</div>
												<div class="col-lg-2">
													<label>Celular <span class="text-danger">*</span></label>
												</div>
												<div class="col-lg-4">
													<label>E-mail <span class="text-danger">*</span></label>
												</div>

												<!-- campos -->
												<div class="col-lg-4">
													<input id="contatoNew" name="contatoNew" type="text" class="form-control" placeholder="Contato">
												</div>
												<div class="col-lg-2">
													<input id="telefoneNew" name="telefoneNew" type="text" class="form-control" placeholder="Telefone" data-mask="(99) 9999-9999" required>
												</div>
												<div class="col-lg-2">
													<input id="celularNew" name="celularNew" type="text" class="form-control" placeholder="Celular" data-mask="(99) 99999-9999" required>
												</div>
												<div class="col-lg-4">
													<input id="emailNew" name="emailNew" type="text" class="form-control" placeholder="E-mail" required>
												</div>
											</div>

											<div class="card card-collapsed">
												<div class="card-header header-elements-inline">
													<h3 class="card-title">Outros dados</h3>
													<div class="header-elements">
														<div class="list-icons">
															<a class="list-icons-item" data-action="collapse"></a>
															<!-- <a href="perfil.php" class="list-icons-item" data-action="reload"></a> -->
															<!--<a class="list-icons-item" data-action="remove"></a>-->
														</div>
													</div>
												</div>
												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-4">
														<label>CPF</label>
													</div>
													<div class="col-lg-4">
														<label>CNS</label>
													</div>
													<div class="col-lg-4">
														<label>RG</label>
													</div>

													<!-- campos -->
													<div class="col-lg-4">
														<input id="cpfNew" name="cpfNew" type="text" class="form-control" placeholder="CPF" data-mask="999.999.999-99">
													</div>
													<div class="col-lg-4">
														<input id="cnsNew" name="cnsNew" type="text" class="form-control" placeholder="Cartão do SUS">
													</div>
													<div class="col-lg-4">
														<input id="rgNew" name="rgNew" type="text" class="form-control" placeholder="RG" data-mask="99.999.999-99">
													</div>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-3">
														<label>Emissor</label>
													</div>
													<div class="col-lg-2">
														<label>UF</label>
													</div>
													<div class="col-lg-3">
														<label>Sexo</label>
													</div>
													<div class="col-lg-4">
														<label>Data de Nascimento</label>
													</div>

													<!-- campos -->
													<div class="col-lg-3">
														<input id="emissorNew" name="emissorNew" type="text" class="form-control" placeholder="Orgão Emissor">
													</div>
													<div class="col-lg-2">
														<select id="ufNew" name="ufNew" class="form-control form-control-select2" placeholder="UF">
															<option value="">Selecione</option>
															<option value="AC">AC</option>
															<option value="AL">AL</option>
															<option value="AP">AP</option>
															<option value="AM">AM</option>
															<option value="BA">BA</option>
															<option value="CE">CE</option>
															<option value="DF">DF</option>
															<option value="ES">ES</option>
															<option value="GO">GO</option>
															<option value="MA">MA</option>
															<option value="MT">MT</option>
															<option value="MS">MS</option>
															<option value="MG">MG</option>
															<option value="PA">PA</option>
															<option value="PB">PB</option>
															<option value="PR">PR</option>
															<option value="PE">PE</option>
															<option value="PI">PI</option>
															<option value="RJ">RJ</option>
															<option value="RN">RN</option>
															<option value="RS">RS</option>
															<option value="RO">RO</option>
															<option value="RR">RR</option>
															<option value="SC">SC</option>
															<option value="SP">SP</option>
															<option value="SE">SE</option>
															<option value="TO">TO</option>
														</select>
													</div>
													<div class="col-lg-3">
														<select id="sexoNew" name="sexoNew" class="form-control form-control-select2">
															<option value="" selected>selecionar</option>
															<option value="M">Masculino</option>
															<option value="F">Feminino</option>
														</select>
													</div>
													<div class="col-lg-4">
														<input id="nascimentoNew" name="nascimentoNew" type="date" class="form-control" placeholder="dd/mm/aaaa">
													</div>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-6">
														<label>Nome do Pai</label>
													</div>
													<div class="col-lg-6">
														<label>Nome da Mãe</label>
													</div>

													<!-- campos -->
													<div class="col-lg-6">
														<input id="nomePaiNew" name="nomePaiNew" type="text" class="form-control" placeholder="Nome do Pai">
													</div>
													<div class="col-lg-6">
														<input id="nomeMaeNew" name="nomeMaeNew" type="text" class="form-control" placeholder="Nome da Mãe">
													</div>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-3">
														<label>Raça/Cor</label>
													</div>
													<div class="col-lg-3">
														<label>Estado Civil</label>
													</div>
													<div class="col-lg-3">
														<label>Naturalidade</label>
													</div>
													<div class="col-lg-3">
														<label>Profissão</label>
													</div>

													<!-- campos -->
													<div class="col-lg-3">
														<select id="racaCorNew" name="racaCorNew" class="form-control form-control-select2">
															<option value="#">Selecione</option>
															<option value="Branca">Branca</option>
															<option value="Preta">Preta</option>
															<option value="Parda">Parda</option>
															<option value="Amarela">Amarela</option>
															<option value="Indígena">Indígena</option>
														</select>
													</div>
													<div class="col-lg-3">
														<select id="estadoCivilNew" name="estadoCivilNew" class="form-control form-control-select2">
															<option value="#">Selecione</option>
															<option value="ST">Solteiro</option>
															<option value="CS">Casado</option>
															<option value="SP">Separado</option>
															<option value="DV">Divorciado</option>
															<option value="VI">Viúvo</option>
														</select>
													</div>
													<div class="col-lg-3">
														<input id="naturalidadeNew" name="naturalidadeNew" type="text" class="form-control" placeholder="Naturalidade">
													</div>
													<div class="col-lg-3">
														<input id="profissaoNew" name="profissaoNew" type="text" class="form-control" placeholder="Profissão" required>
													</div>
												</div>

												<div class="col-lg-12 my-3 text-black-50">
													<h5 class="mb-0 font-weight-semibold">Endereço do Paciente</h5>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-3">
														<label>CEP</label>
													</div>
													<div class="col-lg-4">
														<label>Endereço</label>
													</div>
													<div class="col-lg-2">
														<label>Nº</label>
													</div>
													<div class="col-lg-3">
														<label>Complemento</label>
													</div>

													<!-- campos -->
													<div class="col-lg-3">
														<input id="cepNew" name="cepNew" type="text" class="form-control" placeholder="CEP">
													</div>
													<div class="col-lg-4">
														<input id="enderecoNew" name="enderecoNew" type="text" class="form-control" placeholder="EX.: Rua, Av">
													</div>
													<div class="col-lg-2">
														<input id="numeroNew" name="numeroNew" type="text" class="form-control" placeholder="Número">
													</div>
													<div class="col-lg-3">
														<input id="complementoNew" name="complementoNew" type="text" class="form-control" placeholder="Complemento">
													</div>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-4">
														<label>Bairro</label>
													</div>
													<div class="col-lg-4">
														<label>Cidade</label>
													</div>
													<div class="col-lg-4">
														<label>Estado</label>
													</div>

													<!-- campos -->
													<div class="col-lg-4">
														<input id="bairroNew" name="bairroNew" type="text" class="form-control" placeholder="Bairro">
													</div>
													<div class="col-lg-4">
														<input id="cidadeNew" name="cidadeNew" type="text" class="form-control" placeholder="Cidade">
													</div>
													<div class="col-lg-4">
														<select id="estadoNew" name="estadoNew" class="form-control form-control-select2" placeholder="Estado">
															<option value="#">Selecione um estado</option>
															<option value="AC">Acre</option>
															<option value="AL">Alagoas</option>
															<option value="AP">Amapá</option>
															<option value="AM">Amazonas</option>
															<option value="BA">Bahia</option>
															<option value="CE">Ceará</option>
															<option value="DF">Distrito Federal</option>
															<option value="ES">Espírito Santo</option>
															<option value="GO">Goiás</option>
															<option value="MA">Maranhão</option>
															<option value="MT">Mato Grosso</option>
															<option value="MS">Mato Grosso do Sul</option>
															<option value="MG">Minas Gerais</option>
															<option value="PA">Pará</option>
															<option value="PB">Paraíba</option>
															<option value="PR">Paraná</option>
															<option value="PE">Pernambuco</option>
															<option value="PI">Piauí</option>
															<option value="RJ">Rio de Janeiro</option>
															<option value="RN">Rio Grande do Norte</option>
															<option value="RS">Rio Grande do Sul</option>
															<option value="RO">Rondônia</option>
															<option value="RR">Roraima</option>
															<option value="SC">Santa Catarina</option>
															<option value="SP">São Paulo</option>
															<option value="SE">Sergipe</option>
															<option value="TO">Tocantins</option>
															<option value="ES">Estrangeiro</option>	
														</select>
													</div>
												</div>

												<div class="col-lg-12 mb-4 row">
													<!-- titulos -->
													<div class="col-lg-12">
														<label>Observação</label>
													</div>

													<!-- campos -->
													<div class="col-lg-12">
														<textarea id="observacaoNew" name="observacaoNew" class="form-control" placeholder="Observações"></textarea>
													</div>
												</div>
											</div>
										</div>
										<div class="text-right m-2">
											<button id="salvarPacienteModal" class="btn btn-principal" role="button">Confirmar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="page-modal-filtro" class="custon-modal">
				<div class="custon-modal-container" style="max-width: 900px;">
					<div class="card custon-modal-content">
						<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
							<p id="tituloModal" class="h5">Filtro</p>
							<i id="modalFiltro-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
						</div>
						<div class="px-0">
							<form id="formFiltro" class="form-validate-jquery">
								<div class="col-lg-12 p-0">
									<!-- linha 1 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-6">Status</div>
										<div class="col-lg-6">Recepção</div>

										<div class="col-lg-6">
											<select id="statusFiltro" name="statusFiltro" class="select-search" required>
												<option value=''>Selecione</option>
											</select>
										</div>
										<div class="col-lg-6">
											<select id="recepcaoFiltro" name="recepcaoFiltro" class="select-search" required>
												<option value=''>Selecione</option>
											</select>
										</div>
									</div>

									<!-- linha X -->
									<div class="col-lg-12 py-3" style="margin-top: -5px;">
										<div class="col-lg-4">
											<button class="btn btn-lg btn-principal" id="filtrarAgendamento">Aplicar</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<div id="page-modal-config" class="custon-modal">
				<div class="custon-modal-container" style="max-width: 900px;">
					<div class="card custon-modal-content">
						<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
							<p id="tituloModal" class="h5">Agendar Evento</p>
							<i id="modalConfig-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
						</div>
						<div class="px-0">
							<form id="formConfig" class="form-validate-jquery">
								<div class="col-lg-12 p-0">
									<!-- linha 1 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-6">Profissional <span class="text-danger">*</span></div>
										<div class="col-lg-6"></div>

										<div class="col-lg-6">
											<select id="medicoConfig" name="medicoConfig" class="select-search" required>
												<option value=''>Selecione</option>
											</select>
										</div>
										<div class="col-lg-6"></div>
									</div>

									<!-- linha 2 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-6">Título Bloqueio <span class="text-danger">*</span></div>
										<div class="col-lg-6">Justificativa</div>

										<div class="col-lg-6">
											<input type="text" id="bloqueio" name="bloqueio" class="form-control" required>
										</div>
										<div class="col-lg-6">
											<input type="text" id="justificativa" name="justificativa" class="form-control">
										</div>
									</div>

									<!-- linha 3 -->
									<div class="col-lg-12 row p-2 m-0">
										<div class="col-lg-3">Data Início<span class="text-danger">*</span></div>
										<div class="col-lg-2">Hora Início<span class="text-danger">*</span></div>
										<div class="col-lg-3">Data Fim<span class="text-danger">*</span></div>
										<div class="col-lg-2">Hora Fim<span class="text-danger">*</span></div>
										<div class="col-lg-2"></div>

										<div class="col-lg-3">
											<input type="date" id="inputDataInicioBloqueio" name="inputDataInicioBloqueio" class="form-control" required value="<?php echo date('Y-m-d')?>">
										</div>
										<div class="col-lg-2">
											<input type="time" id="inputHoraInicioBloqueio" name="inputHoraInicioBloqueio" class="form-control" required value="<?php echo date('H:i')?>">
										</div>

										<div class="col-lg-3">
											<input type="date" id="inputDataFimBloqueio" name="inputDataFimBloqueio" class="form-control" required value="<?php echo date('Y-m-d')?>">
										</div>
										<div class="col-lg-2">
											<input type="time" id="inputHoraFimBloqueio" name="inputHoraFimBloqueio" class="form-control" required value="<?php echo date('H:i')?>">
										</div>
										<div class="col-lg-2">
											<button class="btn btn-secondary" id="selecionarCalendario">Selecionar</button>
										</div>
									</div>

									<!-- linha 4 -->
									<div class="col-lg-12 row p-3 m-0">
										<input type="checkbox" id="recorrente" name="recorrente">
										<div class="pl-2">Bloqueio Recorrente</div>
									</div>

									<!-- cardOnOff -->
									<div id="cardRecorrend" class="d-none">
										<!-- linha 1 -->
										<div class="col-lg-12 row p-2 m-0">
											<div class="col-lg-6">Repete a cada <span class="text-danger">*</span></div>
											<div class="col-lg-3 mb-1">Quantidade</div>
											<div class="col-lg-3"></div>

											<div class="col-lg-6">
												<select id="repeticao" name="repeticao" class="select-search" required>
													<option value=''>Selecione</option>
													<option value='1'>1 Semana</option>
													<option value='2'>2 Semanas</option>
													<option value='3'>3 Semanas</option>
													<option value='4'>4 Semanas</option>
												</select>
											</div>
											<div class="col-lg-3">
												<input type="number" id="quantidadeRecorrencia" name="quantidadeRecorrencia" class="form-control" required value="0" max="99">
											</div>
											<div class="col-lg-3"></div>
										</div>

										<!-- linha 2 -->
										<div class="col-lg-12 row p-2 m-0">
											<div class="col-lg-6 mb-1">Dias úteis </div>
											<div class="col-lg-6"></div>

											<div class="col-lg-2">
												<input id="segunda" name="segunda" type="checkbox">
												Segunda-Feira
											</div>
											<div class="col-lg-2">
												<input id="terca" name="terca" type="checkbox">
												Terça-Feira
											</div>
											<div class="col-lg-2">
												<input id="quarta" name="quarta" type="checkbox">
												Quarta-Feira
											</div>
											<div class="col-lg-2">
												<input id="quinta" name="quinta" type="checkbox">
												Quinta-Feira
											</div>
											<div class="col-lg-2">
												<input id="sexta" name="sexta" type="checkbox">
												Sexta-Feira
											</div>
											<div class="col-lg-2"></div>
										</div>

										<!-- linha 3 -->
										<div class="col-lg-12 row p-2 m-0">
											<div class="col-lg-6 mb-1">Final de semana</div>
											<div class="col-lg-6"></div>

											<div class="col-lg-2">
												<input id="sabado" name="sabado" type="checkbox">
												Sábado
											</div>
											<div class="col-lg-2">
												<input id="domingo" name="domingo" type="checkbox">
												Domingo
											</div>
											<div class="col-lg-8"></div>
										</div>

										<!-- linha 4 -->
										<div class="col-lg-12 row p-2 m-0">
											<div class="col-lg-12 mb-1">Término da recorrência</div>

											<div class="col-lg-4 mb-1">Data Final</div>
											<div class="col-lg-8"></div>

											<div class="col-lg-4">
												<input type="date" id="dataRecorrencia" name="dataRecorrencia" class="form-control" required value="">
											</div>
											<div class="col-lg-8"></div>
										</div>
									</div>

									<!-- linha X -->
									<div class="col-lg-12 py-3" style="margin-top: -5px;">
										<div class="col-lg-4">
											<button class="btn btn-lg btn-principal" id="salvarEvento">Incluir</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<?php include_once("footer.php"); ?>
		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
