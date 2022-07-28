<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Agendamento';

// vai limpar todos os dados da sessão utilizadas em agendamento
$_SESSION['SERVICOS'] = [];

include('global_assets/php/conexao.php');

if(isset($_POST['iAgendamento'])){
	$iAgendamento = $_POST['iAgendamento'];

	$sql = "SELECT AgendId,AgendDataRegistro,AgendCliente,AgendModalidade,AgendClienteResponsavel,
	AgendObservacao,AtModNome,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor
	FROM Agendamento
	JOIN AtendimentoModalidade ON AtModId = AgendModalidade
	JOIN Situacao ON SituaId = AgendSituacao
	JOIN Cliente ON ClienId = AgendCliente
	WHERE AgendId = $iAgendamento";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
}

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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>	

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
    
    <!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<?php
		// essa parte do código transforma uma variáve php em Js para ser utilizado 
		echo isset($_POST['iAgendamento'])?
			'<script>
				var agendamento = '.json_encode($row).';
			</script>'
			:
			'<script>
				var agendamento = null;
			</script>';
	?>
	
	<script type="text/javascript" >
		$(document).ready(function() {
			let dataAtual = new Date().toLocaleString("pt-BR", {timeZone: "America/Bahia"});
			dataAtual = dataAtual.split(' ')[0];
			dataAtual = dataAtual.split('/')[2]+'-'+dataAtual.split('/')[1]+'-'+dataAtual.split('/')[0];
			$('#data').val(dataAtual);

			$('#servicoTable').hide();
			alteraSituacao();
			getCmbs();
			// setDataProfissional();	
			// setHoraProfissional();
			// se existir agendamento os dados serão preenchidos ao carregar a página
			if(agendamento){
				$('#data').val(agendamento.AgendDataRegistro)
				$('#observacao').val(agendamento.AgendObservacao)
				$('#tipoRequest').val('EDITAR')
				checkServicos(agendamento.AgendId)
				alteraSituacao(agendamento.SituaChave);
			}

			$('#salvarPaciente').on('click', function(e){
				e.preventDefault()

				let nomePaciente = $('#nomePaciente').val()
				let telefone = $('#telefone').val()
				let celular = $('#celular').val()
				let email = $('#email').val()
				let observacao = $('#observacao').val()

				let menssageError = ''

				switch(menssageError){
					case nomePaciente: menssageError = 'Informe o nome';break;
					case telefone || celular: menssageError = 'Informe o telefone ou celular';break;
					case email: menssageError = 'Informe o E-mail';break;
					default: menssageError = '';break;
				}

				if(menssageError){
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAgendamento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADDPACIENTENOVO',
						'nomePaciente': nomePaciente,
						'telefone': telefone,
						'celular': celular,
						'email': email,
						'observacao': observacao
					},
					success: function(response) {
						if(response.status  == 'success'){
							$('#paciente').empty();
							$('#paciente').append(`<option value=''>Selecione</option>`)
							response.array.forEach(item => {
								$('#paciente').append(`<option ${item.isSelected} value="${item.id}">${item.nome}</option>`)
							})
							alerta(response.titulo, response.menssagem, response.status)
							$('#page-modal-paciente').fadeOut();
						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#medico').on('change', function(){
				let iMedico = $(this).val()

				if(!iMedico){
					setHoraProfissional()
					setDataProfissional()
					return
				}
				$.ajax({
					type: 'POST',
					url: 'filtraAgendamento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SETDATAPROFISSIONAL',
						'iMedico': iMedico,
					},
					success: function(response) {
						if(response.status == 'success'){
							setDataProfissional(response.arrayData)
							$('#dataAtendimento').focus()

						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#inserirServico').on('click', function(e){
				e.preventDefault()
				// vai preencher a tabela servico e inseri-lo no array de servicos
				let menssageError = ''
				let servico = $('#servico').val()
				let medico = $('#medico').val()
				let data = $('#dataAtendimento').val()
				let hora = $('#horaAtendimento').val()
				let local = $('#localAtendimento').val()

				switch(menssageError){
					case servico: menssageError = 'informe o servico'; $('#servico').focus();break;
					case medico: menssageError = 'informe o médico'; $('#medico').focus();break;
					case data: menssageError = 'informe uma data'; $('#dataAtendimento').focus();break;
					case hora: menssageError = 'informe o horário'; $('#horaAtendimento').focus();break;
					case local: menssageError = 'informe o local de atendimento'; $('#localAtendimento').focus();break;
					default: menssageError = ''; break;
				}

				if(menssageError){
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAgendamento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADDSERVICO',
						'servico': servico,
						'medico': medico,
						'data': data,
						'hora': hora,
						'local': local
					},
					success: function(response) {
						if(response.status  == 'success'){
							resetServicoCmb()
							checkServicos()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#salvarAgendamento').on('click', function(e){
				e.preventDefault();
				let msg = ''
				let data = $('#data').val()
				let paciente = $('#paciente').val()
				let modalidade = $('#modalidade').val()
				let observacao = $('#observacao').val()
				let cmbSituacao = $('#cmbSituacao').val()

				switch(msg){
					case data: msg = 'informe uma data';$('#data').focus();break
					case paciente: msg = 'informe um paciente';$('#paciente').focus();break
					case modalidade: msg = 'Informe a modalidade';$('#modalidade').focus();break
					case cmbSituacao: msg = 'Informe a situação';$('#cmbSituacao').focus();break
				}

				if(msg){
					alerta('Campo Obrigatório!', msg, 'error')
					return
				}

				let servicos = false;
				$('.servicoItem').each(function(index, item){
					servicos = true;
				})

				if(!servicos){
					alerta('Campo Obrigatório!', 'Informe pelo menos um serviço', 'error')
					$('#servico').focus()
					return
				}
				
				let dados = agendamento?{
						'tipoRequest': 'ADDAGENDAMENTO',
						'data': data,
						'isUpdate': agendamento?agendamento.AgendId:false,
						'paciente': paciente,
						'modalidade': modalidade,
						'observacao': observacao,
						'cmbSituacao': cmbSituacao
					}:{
						'tipoRequest': 'ADDAGENDAMENTO',
						'data': data,
						'paciente': paciente,
						'modalidade': modalidade,
						'observacao': observacao,
						'cmbSituacao': cmbSituacao
					}
				
				$.ajax({
					type: 'POST',
					url: 'filtraAgendamento.php',
					dataType: 'json',
					data: dados,
					success: function(response) {
						if(response.status == 'success'){
							window.location.href = 'agendamento.php'
							alerta(response.titulo, response.menssagem, response.status)
						}else{
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#addPaciente').on('click', function(e){
				e.preventDefault();
				$('#page-modal-paciente').fadeIn();
			})

			$('#modal-close-x').on('click', ()=>{
				$('#iAtendimento').val('')
				$('#page-modal-paciente').fadeOut(200);
			})
		});
		// essa funcao vai checar se ja exixte algo no array de servicos
		// (para quando atualizar a página não sumir da grid)

		function checkServicos(idAgendamento){
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'CHECKSERVICO',
					'iAgendamento': idAgendamento
				},
				success: async function(response) {
					statusServicos = response.array.length?true:false;
					$('#dataServico').html('').show();
					let HTML = ''
					response.array.forEach(item => {
						let exc = `<a style='color: black; cursor:pointer' onclick='excluiServico(\"${item.id}\")' class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>`;
						let acoes = `<div class='list-icons'>
									${exc}
								</div>`;
						HTML += `
						<tr class='servicoItem'>
							<td class="text-center">${item.servico}</td>
							<td class="text-center">${item.medico}</td>
							<td class="text-center">${item.sData}</td>
							<td class="text-center">${item.hora}</td>
							<td class="text-center">${item.local}</td>
							<td class="text-right">R$ ${float2moeda(item.valor)}</td>
							<td class="text-center">${acoes}</td>
						</tr>`
					})
					if(statusServicos){
						$('#servicoTable').show();
					}else{
						$('#servicoTable').hide();
					}
					$('#dataAtendimento').val('')
					$('#horaAtendimento').val('')
					$('#servicoValorTotal').html(`${float2moeda(response.valorTotal)}`).show();
					$('#dataServico').html(HTML).show();
				}
			});
		}

		function setHoraProfissional(array){
			$('#modalHora').html('').show();
			$('#modalHora').html("<input id='horaAtendimento' name='horaAtendimento' type='text' class='form-control horaAtendimento' readonly aria-haspopup='true' aria-expanded='false' aria-readonly='false' aria-owns='P1503001435_root'>").show();

			// doc: https://amsul.ca/pickadate.js/time/
			$('.horaAtendimento').pickatime({
				// Regras
				interval: 30,
				disable: array?array:undefined,
				// disable: [
				// 	[1,30],
				// ],

				// Formats
				format: 'HH:i',
				formatLabel: undefined,
				formatSubmit: undefined,
				hiddenPrefix: undefined,
				hiddenSuffix: '_submit',
				
				// Time limits
				min: undefined,
				max: undefined,
				
				// Close on a user action
				closeOnSelect: true,
				closeOnClear: true,

				// eventos
				onSet: function(context) {
					// let hora = context.select
					let data = $('#dataAtendimento').val()
					let hora = $('#horaAtendimento').val()

					// data: DD/MM/YYYY => MM/DD/YYYY
					data = `${data.split('/')[1]}/${data.split('/')[0]}/${data.split('/')[2]}`

					// dataHora: MM/DD/YYYY HH:MM:SS
					let dataHora = `${data} ${hora}`

					// somente para atribuir à variável "dataHora" um valor do tipo DataTime
					dataHora = new Date(dataHora).toLocaleString("pt-BR", {timeZone: "America/Bahia"});
					console.log(dataHora)
				},
				onStart: undefined,
				onRender: undefined,
				onOpen: undefined,
				onClose: undefined,
				onStop: undefined,
			});
		}

		function setDataProfissional(array){
			$('#modalData').html('').show();
			$('#modalData').html("<input id='dataAtendimento' name='dataAtendimento' type='text' class='form-control dataAtendimento' readonly aria-haspopup='true' aria-expanded='false' aria-readonly='false' aria-owns='P1503001435_root'>").show();
			
			let arrayData = array?array:undefined;
			// doc: https://amsul.ca/pickadate.js/date/#disable-dates
			$('.dataAtendimento').pickadate({
				// regras. OBS: a data mínima é a data atual.
				// o mes começa em "0" ou seja: Janeiro => 0, Fevereiro => 1, etc.
				min: undefined,
				max: undefined,
				disable: arrayData,
				// disable: [true,[2022,6,20],[2022,6,22]],
				
				// strings
				monthsFull: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
				monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 't', 'Out', 'Nov', 'Dez'],
				weekdaysFull: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
				weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],

				// botões
				today: 'Hoje',
				clear: '',
				close: '',

				// Labels
				labelMonthNext: 'Seguinte',
				labelMonthPrev: 'Anterior',
				labelMonthSelect: 'Selecionar Mes',
				labelYearSelect: 'Selecionar ano',

				// Formats
				format: 'dd/mm/yyyy',
				formatSubmit: undefined,
				hiddenPrefix: undefined,
				hiddenSuffix: '_submit',
				hiddenName: undefined,

				// ações
				onClose: function() {
					$('#horaAtendimento').focus()
				},
				onSet: function(context) {
					let data = new Date(context.select).toLocaleString("pt-BR", {timeZone: "America/Bahia"});
					data = data.split(' ')[0]; // Formatando a string padrão: "dd/mm/yyyy HH:MM:SS" => "dd/mm/yyyy"
					let iMedico = $('#medico').val();
					console.log(data)
					$.ajax({
						type: 'POST',
						url: 'filtraAgendamento.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'SETHORAPROFISSIONAL',
							'data': data,
							'iMedico': iMedico
						},
						success: function(response) {
							if(response.status == 'success'){
								setHoraProfissional(response.arrayHora)
								$('#horaAtendimento').focus()
							} else {
								alerta(response.titulo, response.menssagem, response.status)
							}
						}
					});
					$('#horaAtendimento').focus()
				},
				onStart: undefined,
				onRender: undefined,
				onOpen: undefined,
				onStop: undefined,
			});
		}

		// exclui servico, o id é composto pelo idServico + idMedico + idLocal; EX.: 10#20#31

		function excluiServico(id){
			$.ajax({
				type: 'POST',
				url: 'filtraAgendamento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'EXCLUISERVICO',
					'id': id
				},
				success: function(response) {
					alerta(response.titulo, response.menssagem, response.status)
					checkServicos()
				}
			});
		}

		function getCmbs(){
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
						let opt = ''
						// caso exista algo na variável agendamento significa que o usuário esta alterando um valor
						// logo esses valores deveram vir preenchido com os dados desse agendamento
						if(agendamento){
							 opt = agendamento.AgendCliente == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						} else {
							opt = `<option value="${item.id}">${item.nome}</option>`
						}
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
						let opt = ''
						// caso exista algo na variável agendamento significa que o usuário esta alterando um valor
						// logo esses valores deveram vir preenchido com os dados desse agendamento
						if(agendamento){
							 opt = agendamento.AgendModalidade == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						} else {
							opt = `<option value="${item.id}">${item.nome}</option>`
						}
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
		}

		function resetServicoCmb(){
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
			$('#dataAtendimento').val('')
			$('#horaAtendimento').val('')
		}

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
					$('#cmbSituacao').append("<option selected value=''>selecione</option>")
					response.forEach(item => {
						let opt = item.SituaChave === situacao? `<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
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
								<div class="col-lg-12 my-3 text-black-50">
									<?php
										echo isset($_POST['iAgendamento'])?"<h5>Editar Agendamento</h5>":"<h5>Novo Agendamento</h5>"
									?>
								</div>

								<!-- esses inputs são para validar o tipo de operação que será feito no
								arquivo agendamentoFiltra.php -->
								<input type="hidden" id="tipoRequest" name="tipoRequest" value="NOVO" />

								<div class="col-lg-12 mb-4 row form-group">
									<!-- titulos -->
									<div class="col-lg-3">
										<label>Data do registro <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-6">
										<label>Paciente <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-3">
										<label>Modalidade <span class="text-danger">*</span></label>
									</div>

									<!-- campos -->
									<div class="col-lg-3">
										<input id="data" name="data" type="date" class="form-control">
									</div>
									<div class="col-lg-6 row m-0"> 
										<div class="col-lg-10">
											<select id="paciente" name="paciente" class="select-search">
												
											</select>
										</div>
										<div class="col-lg-2">
											<span class="action btn btn-principal legitRipple" id="addPaciente" style="user-select: none;">+</span>
										</div>
									</div>
									<div class="col-lg-3">
										<select id="modalidade" name="modalidade" class="form-control-select2">
											<!--  -->
										</select>
									</div>
								</div>

								<div class="col-lg-12 my-3 text-black-50">
									<h5>Serviços</h5>
								</div>

								<div class="col-lg-12 mb-4 row">
									<!-- titulos -->
									<div class="col-lg-3">
										<label>Serviços <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-2">
										<label>Médicos <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-3">
										<label>Data do Atendimento <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-2">
										<label>Horário <span class="text-danger">*</span></label>
									</div>
									<div class="col-lg-2">
										<label>Local do Atendimento <span class="text-danger">*</span></label>
									</div>

									<!-- campos -->
									<div class="col-lg-3">
										<select id="servico" name="servico" class="select-search" required>
											<!--  -->
										</select>
									</div>
									<div class="col-lg-2">
										<select id="medico" name="medico" class="select-search" required>
											<!--  -->
										</select>
									</div>
									<div id="modalData" class="col-lg-3">
										<input id="dataAtendimento" name="dataAtendimento" type="text"
										class="form-control dataAtendimento" readonly aria-haspopup="true" aria-expanded="false"
										aria-readonly="false" aria-owns="P1503001435_root">
										<!-- <input id="dataAtendimento" name="dataAtendimento" type="date" class="form-control" required> -->
									</div>
									<div id="modalHora" class="col-lg-2">
										<input id="horaAtendimento" name="horaAtendimento" type="text"
										class="form-control horaAtendimento" readonly 
										aria-haspopup="true" aria-expanded="false" aria-readonly="false" aria-owns="P1503001435_root">
										<!-- <input id="horaAtendimento" type="time" class="form-control" value="" required> -->
									</div>
									<div class="col-lg-2">
										<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2" required>
											<!--  -->
										</select>
									</div>
									<div class="col-lg-12 text-right mt-4">
										<button class="btn btn-lg btn-principal" id="inserirServico" >inserir</button>
									</div>
								</div>

								<div class="col-lg-12">
									<table class="table" id="servicoTable">
										<thead>
											<tr class="bg-slate text-center">
												<th>Procedimento</th>
												<th>Médico</th>
												<th>Data do Atendimento</th>
												<th>Horário</th>
												<th>Local</th>			
												<th>Valor</th>
												<th class="text-center">Ações</th>
											</tr>
										</thead>
										<tbody id="dataServico">
											
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5" class="text-right font-weight-bold" style="font-size: 16px;">
													<div>Valor(R$):</div>
												</th>
												<th colspan="1" class="mr-1">
													<div id="servicoValorTotal" class="text-center font-weight-bold" style="font-size: 15px;">R$ 0,00</div>
												</th>
											</tr>
										</tfoot>
									</table>
								</div>

								<div class="col-lg-12 mb-4 row mt-5">
									<!-- titulos -->
									<div class="col-lg-12">
										<label>Observação</label>
									</div>

									<!-- campos -->
									<div class="col-lg-12">
										<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
									</div>
								</div>

								<div class="col-lg-12">
									<label>Situação <span class="text-danger">*</span></label>
									<div class="form-group col-lg-2">
										<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" required>
											<!--  -->
										</select>
									</div>
								</div>

								<!-- botões -->
								<div class="col-lg-12 ml-2 my-4 row">
									<button class="btn btn-lg btn-principal" id="salvarAgendamento" data-tipo="AGENDAMENTO" >salvar</button>
									<a href="agendamento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
								</div>

								<!--Modal cadastrar Paciente-->
								<div id="page-modal-paciente" class="custon-modal">
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
																	<label>Nome <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-12">
																	<input id="nomePaciente" require name="nomePaciente" type="text" class="form-control">
																</div>
															</div>
															<div class="col-lg-12 my-4 row">
																<div class="col-lg-4">
																	<label>Telefone <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-4">
																	<label>Celular <span class="text-danger">*</span></label>
																</div>
																<div class="col-lg-4">
																	<label>E-mail <span class="text-danger">*</span></label>
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
																	<label>Observação</label>
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
													<button class="btn btn-lg btn-principal" id="salvarPaciente" data-tipo="PACIENTE" >incluir</button>
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
