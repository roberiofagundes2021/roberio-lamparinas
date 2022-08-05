<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Atendimento';

include('global_assets/php/conexao.php');

// a requisição é feita ao carregar a página via AJAX no arquivo filtraAtendimento.php

$iAtendimento = isset($_POST['iAtendimento'])?$_POST['iAtendimento']:false;
$iUnidade = $_SESSION['UnidadeId'];

if($iAtendimento){
	$sql = "SELECT AtendId,AtendAgendamento,AtendNumRegistro,AtendDataRegistro,AtendCliente,AtendModalidade,
	AtendResponsavel,AtendClassificacao,AtendObservacao,AtendSituacao,AtendUsuarioAtualizador,AtendUnidade,
	SituaNome,SituaChave
	FROM Atendimento
	JOIN Situacao ON SituaId = AtendSituacao
	WHERE AtendId = $iAtendimento and AtendUnidade = $iUnidade";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Atendimentos</title>

	<?php include_once("head.php"); ?>
	<style>
		table td{
			padding: 1rem !important;
		}
		.nav-tabs-bottom .nav-link.active:before{
			background-color: #375b82;
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
		echo $iAtendimento?
			'<script>
				var atendimento = '.json_encode($row).';
			</script>'
			:
			'<script>
				var atendimento = null;
			</script>';
	?>

	<script type="text/javascript" >			
		$(document).ready(function() {
			$('#servicoTable').hide()
			$('#novoPaciente').fadeOut()
			$('#voltarSelectPaciente').fadeOut()
			$('#novoResponsavel').fadeOut()

			let dataAtual = new Date().toLocaleString("pt-BR", {timeZone: "America/Bahia"})
			dataAtual = dataAtual.split(' ')[0]
			dataAtual = dataAtual.split('/')[2]+'-'+dataAtual.split('/')[1]+'-'+dataAtual.split('/')[0]
			$('#dataRegistro').val(dataAtual)

			$('#servicoTable').hide()
			getCmbs()
			checkServicos()

			if(atendimento){
				$('#dataRegistro').val(atendimento.AtendDataRegistro)
				$('#observacao').val(atendimento.AtendObservacao)
				$('#tipoRequest').val('EDITAR')
				checkServicos(atendimento.AtendId)
			}

			$('#incluirServico').on('click', function(e){
				e.preventDefault();
				let menssageError = ''
				let servico  = $('#servico').val()
				let medicos  = $('#medicos').val()
				let dataAtendimento  = $('#dataAtendimento').val()
				let horaAtendimento  = $('#horaAtendimento').val()
				let localAtendimento  = $('#localAtendimento').val()

				switch(menssageError){
					case servico: menssageError = 'informe o serviço'; $('#servico').focus();break;
					case medicos: menssageError = 'informe o médico'; $('#medicos').focus();break;
					case dataAtendimento: menssageError = 'informe uma data'; $('#dataAtendimento').focus();break;
					case horaAtendimento: menssageError = 'informe o horário'; $('#horaAtendimento').focus();break;
					case localAtendimento: menssageError = 'informe o local de atendimento'; $('#localAtendimento').focus();break;
					default: menssageError = ''; break;
				}

				if(menssageError){
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADICIONARSERVICO',
						'servico': servico,
						'medicos': medicos,
						'dataAtendimento': dataAtendimento,
						'horaAtendimento': horaAtendimento,
						'localAtendimento': localAtendimento
					},
					success: function(response) {
						if(response.status == 'success'){
							resetServicoCmb()
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
			})

			// FORMS

			$('#formPaciente').submit(function(e){
				e.preventDefault()
				if($('#pacienteId').val() == 'NOVO'){
					let msg = ''
					switch(msg){
						case $('#prontuario').val():  msg = 'Informe o prontuario!'; $('#prontuario').focus();break;
						case $('#nome').val():  msg = 'Informe o nome!'; $('#nome').focus();break;
						case $('#cpf').val():  msg = 'Informe o cpf!'; $('#cpf').focus();break;
						case $('#rg').val():  msg = 'Informe o rg!'; $('#rg').focus();break;
						case $('#emissor').val():  msg = 'Informe o emissor!'; $('#emissor').focus();break;
						case $('#uf').val():  msg = 'Informe o uf!'; $('#uf').focus();break;
						case $('#sexo').val():  msg = 'Informe o sexo!'; $('#sexo').focus();break;
						case $('#nascimento').val():  msg = 'Informe a data de nascimento!'; $('#nascimento').focus();break;
						case $('#nomePai').val() || $('#nomeMae').val():  msg = 'Informe o nome do pai ou da mãe!'; $('#nomePai').focus();break;
						case $('#profissao').val():  msg = 'Informe a profissão!'; $('#profissao').focus();break;
						case $('#cep').val():  msg = 'Informe o cep!'; $('#cep').focus();break;
						case $('#endereco').val():  msg = 'Informe o endereço!'; $('#endereco').focus();break;
						case $('#numero').val():  msg = 'Informe o número!'; $('#numero').focus();break;
						case $('#bairro').val():  msg = 'Informe o bairro!'; $('#bairro').focus();break;
						case $('#cidade').val():  msg = 'Informe a cidade!'; $('#cidade').focus();break;
						case $('#estado').val():  msg = 'Informe o estado!'; $('#estado').focus();break;
						case $('#contato').val():  msg = 'Informe o nome do contato!'; $('#contato').focus();break;
						case $('#telefone').val() || $('#celular').val():  msg = 'Informe um telefone ou celular!'; $('#telefone').focus();break;
						case $('#email').val():  msg = 'Informe um email!'; $('#email').focus();break;
						default:  msg = '';break;
					}
					if(msg){
						alerta('Campo obrigatório!', msg, 'error');
						return
					}
	
					$.ajax({
						type: 'POST',
						url: 'filtraAtendimento.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'SALVARPACIENTE',
							'pessoaTipo': $('#fisica').is(':checked')? 'F' : 'J',
							'prontuario': $('#prontuario').val(),
							'nome': $('#nome').val(),
							'cpf': $('#cpf').val(),
							'rg': $('#rg').val(),
							'emissor': $('#emissor').val(),
							'uf': $('#uf').val(),
							'sexo': $('#sexo').val(),
							'nascimento': $('#nascimento').val(),
							'nomePai': $('#nomePai').val(),
							'nomeMae': $('#nomeMae').val(),
							'profissao': $('#profissao').val(),
							'cep': $('#cep').val(),
							'endereco': $('#endereco').val(),
							'numero': $('#numero').val(),
							'complemento': $('#complemento').val(),
							'bairro': $('#bairro').val(),
							'cidade': $('#cidade').val(),
							'estado': $('#estado').val(),
							'contato': $('#contato').val(),
							'telefone': $('#telefone').val(),
							'celular': $('#celular').val(),
							'email': $('#email').val(),
							'observacao': $('#observacao').val()
						},
						success: function(response) {
							alerta(response.titulo, response.menssagem, response.tipo);
							$('#novoPaciente').fadeOut()
							$('#selectPaciente').fadeIn()
							$('#voltarSelectPaciente').fadeOut()
							$('#cancelar').fadeIn()
							$('#pacienteId').val('')

							getCmbs({pacienteID: response.id})
						},
						error: function(response) {
							alerta(response.titulo, response.menssagem, response.tipo);
						}
					});
				} else if(!$('#paciente').val()){
					alerta('Campo Obrigatório!', 'informe o paciente', 'error')
					return
				} else {
					$('#formPaciente').removeClass('active show')
					$('#formResponsavel').removeClass('active show')
					$('#FormAtendimento').removeClass('active show')

					$('#navPaciente').removeClass('active show')
					$('#navResponsavel').removeClass('active show')
					$('#navAtendimento').removeClass('active show')

					$('#formResponsavel').addClass('active show')
					$('#navResponsavel').addClass('active show')

					$('#cancelar').fadeIn()
					$('#voltarSelectPaciente').fadeOut()
				}
			})

			$('#formResponsavel').submit(function(e){
				e.preventDefault()
				if($('#responsavelId').val() == 'NOVO'){
					let msg = ''
					switch(msg){
						case $('#nomeResp').val(): msg = 'Informe o nome!!';$('#nomeResp').focus();break;
						case $('#parentescoResp').val(): msg = 'Informe o tipo de parentesco!!';$('#parentescoResp').focus();break;
						case $('#nascimentoResp').val(): msg = 'Informe a data de nascimento!!';$('#nascimentoResp').focus();break;
						case $('#cepResp').val(): msg = 'Informe o cep!!';$('#cepResp').focus();break;
						case $('#enderecoResp').val(): msg = 'Informe endereco!!';$('#enderecoResp').focus();break;
						case $('#numeroResp').val(): msg = 'Informe número!!';$('#numeroResp').focus();break;
						case $('#bairroResp').val(): msg = 'Informe o bairro!!';$('#bairroResp').focus();break;
						case $('#cidadeResp').val(): msg = 'Informe a cidade!!';$('#cidadeResp').focus();break;
						case $('#estadoResp').val(): msg = 'Informe o estado!!';$('#estadoResp').focus();break;
						case $('#telefoneResp').val() || $('#celularResp').val(): msg = 'Informe um telefone ou celular!!';$('#telefoneResp').focus();break;
						case $('#emailResp').val(): msg = 'Informe um email!!';$('#emailResp').focus();break;
						default:  msg = '';break;
					}

					if(msg){
						alerta('Campo obrigatório!', msg, 'error');
						return
					}

					$.ajax({
						type: 'POST',
						url: 'filtraAtendimento.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'SALVARRESPONSAVEL',
							'nomeResp': $('#nomeResp').val(),
							'parentescoResp': $('#parentescoResp').val(),
							'nascimentoResp': $('#nascimentoResp').val(),
							'cepResp': $('#cepResp').val(),
							'enderecoResp': $('#enderecoResp').val(),
							'numeroResp': $('#numeroResp').val(),
							'complementoResp': $('#complementoResp').val(),
							'bairroResp': $('#bairroResp').val(),
							'cidadeResp': $('#cidadeResp').val(),
							'estadoResp': $('#estadoResp').val(),
							'telefoneResp': $('#telefoneResp').val(),
							'celularResp': $('#celularResp').val(),
							'emailResp': $('#emailResp').val(),
							'observacaoResp': $('#observacaoResp').val()
						},
						success: function(response) {
							alerta(response.titulo, response.menssagem, response.tipo);
							$('#novoPaciente').fadeOut()
							$('#selectPaciente').fadeIn()
							$('#pacienteId').val('')

							getCmbs({responsavelID: response.id})
						},
						error: function(response) {
							alerta(response.titulo, response.menssagem, response.tipo);
						}
					});
				} else if(!$('#parentescoCadatrado').val()){
					alerta('Campo Obrigatório!', 'informe o responsável', 'error')
					return
				} else {
					$('#formPaciente').removeClass('active show')
					$('#formResponsavel').removeClass('active show')
					$('#FormAtendimento').removeClass('active show')

					$('#navPaciente').removeClass('active show')
					$('#navResponsavel').removeClass('active show')
					$('#navAtendimento').removeClass('active show')

					$('#FormAtendimento').addClass('active show')
					$('#navAtendimento').addClass('active show')
				}
			})

			$('#formAtendimento').submit(function(e){
				e.preventDefault()
				let msg = ''
				switch(msg){
					case $('#dataRegistro').val(): msg = 'Informe a data de registro!!';$('#dataRegistro').focus();break;
					case $('#modalidade').val(): msg = 'Informe a modalidade!!';$('#modalidade').focus();break;
					case $('#classificacao').val(): msg = 'Informe a classificação!!';$('#classificacao').focus();break;
					default:  msg = '';break;
				}
				// falta fazer a inserção no banco, so esta feito a validação
				if(msg){
					alerta('Campo obrigatório!', msg, 'error');
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SALVARATENDIMENTO',
						'dataRegistro': $('#dataRegistro').val(),
						'modalidade': $('#modalidade').val(),
						'classificacao': $('#classificacao').val(),
						'observacaoAtendimento': $('#observacaoAtendimento').val()
					},
					success: function(response) {
						alerta(response.titulo, response.menssagem, response.tipo);
					},
					error: function(response) {
						alerta(response.titulo, response.menssagem, response.tipo);
					}
				});
			})

			// /////////////////////////////////////

			$('#backSalvarResponsavel').on('click', function(e){
				e.preventDefault()
				$('#formPaciente').removeClass('active show')
				$('#formResponsavel').removeClass('active show')
				$('#FormAtendimento').removeClass('active show')

				$('#navPaciente').removeClass('active show')
				$('#navResponsavel').removeClass('active show')
				$('#navAtendimento').removeClass('active show')

				$('#formPaciente').addClass('active show')
				$('#navPaciente').addClass('active show')
			})

			$('#backSalvarAtendimento').on('click', function(e){
				e.preventDefault()
				$('#formPaciente').removeClass('active show')
				$('#formResponsavel').removeClass('active show')
				$('#FormAtendimento').removeClass('active show')

				$('#navPaciente').removeClass('active show')
				$('#navResponsavel').removeClass('active show')
				$('#navAtendimento').removeClass('active show')

				$('#formResponsavel').addClass('active show')
				$('#navResponsavel').addClass('active show')
			})

			$('#addPaciente').on('click', function(e){
				e.preventDefault()
				$('#pacienteId').val('NOVO')
				$('#selectPaciente').fadeOut()
				$('#novoPaciente').fadeIn()
				$('#cancelar').fadeOut()
				$('#voltarSelectPaciente').fadeIn()
			})

			$('#addResponsavel').on('click', function(e){
				e.preventDefault()
				$('#responsavelId').val('NOVO')
				$('#selectResponsavel').fadeOut()
				$('#novoResponsavel').fadeIn()
			})

			$('#voltarSelectPaciente').on('click', function(){
				$('#novoPaciente').fadeOut()
				$('#selectPaciente').fadeIn()
				$('#voltarSelectPaciente').fadeOut()
				$('#cancelar').fadeIn()
				$('#pacienteId').val('')

				getCmbs()
			})


			$('#parentescoCadatrado').on('change', function(){
				let iResponsavel = $(this).val();
				if(iResponsavel){
					$.ajax({
						type: 'POST',
						url: 'filtraAtendimento.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'RESPONSAVEL',
							'iResponsavel': iResponsavel
						},
						success: function(response) {
							if(response.tipo == 'success'){
								$('#nomeResp').val(response.data.nomeResp)
								$('#parentescoResp').val(response.data.parentescoResp)
								$('#nascimentoResp').val(response.data.nascimentoResp)
								$('#cepResp').val(response.data.cepResp)
								$('#enderecoResp').val(response.data.enderecoResp)
								$('#numeroResp').val(response.data.numeroResp)
								$('#complementoResp').val(response.data.complementoResp)
								$('#bairroResp').val(response.data.bairroResp)
								$('#cidadeResp').val(response.data.cidadeResp)
								$('#estadoResp').val(response.data.estadoResp)
								$('#telefoneResp').val(response.data.telefoneResp)
								$('#celularResp').val(response.data.celularResp)
								$('#emailResp').val(response.data.emailResp)
								$('#observacaoResp').val(response.data.observacaoResp)

								alerta(response.titulo, response.menssagem, response.tipo)
							}else{
								alerta(response.titulo, response.menssagem, response.tipo)
							}
						},
						error: function(response) {
						}
					});
				}
			});

			$('#medicos').on('change', function(){
				let iMedico = $(this).val()

				if(!iMedico){
					setHoraProfissional()
					setDataProfissional()
					return
				}
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
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
			});

			$('#salvarPacienteModal').on('click', function(e){
				e.preventDefault()

				let nomePaciente = $('#nomePacienteModal').val()
				let telefone = $('#telefoneModal').val()
				let celular = $('#celularModal').val()
				let email = $('#emailModal').val()
				let observacao = $('#observacaoModal').val()

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
				console.log(menssageError)
				return

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
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
							$('#pacienteAtendimento').empty();
							$('#pacienteAtendimento').append(`<option value=''>Selecione</option>`)
							response.array.forEach(item => {
								$('#pacienteAtendimento').append(`<option ${item.isSelected} value="${item.id}">${item.nome}</option>`)
							})
							alerta(response.titulo, response.menssagem, response.status)
							$('#page-modal-paciente').fadeOut();
						} else {
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

			$('.legitRipple').each(function(){
				console.log($(this).html())
			});

			resetServicoCmb()
		});

		function getCmbs(obj){
			// vai preencher cmbPaciente
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'PACIENTES'
				},
				success: function(response) {
					$('#paciente').empty();
					$('#paciente').append(`<option value=''>Selecione</option>`)
					let opt = ''

					// caso exista algo na variável atendimento significa que o usuário esta alterando um valor
					// logo esses valores deveram vir preenchido com os dados desse atendimento

					if(obj && obj.pacienteID){
						response.forEach(item =>{
							opt = obj.pacienteID  == item.id?
							 `<option selected value="${item.id}">${item.nome}</option>`:
							 `<option value="${item.id}">${item.nome}</option>`
							 $('#paciente').append(opt)
						})
					} else if(atendimento){
						response.forEach(item =>{
							opt = atendimento.AtendCliente  == item.id?
							 `<option selected value="${item.id}">${item.nome}</option>`:
							 `<option value="${item.id}">${item.nome}</option>`
							 $('#paciente').append(opt)
						})
					} else {
						response.forEach(item =>{
							opt = opt = `<option value="${item.id}">${item.nome}</option>`
							$('#paciente').append(opt)
						})
					}
				}
			});
			// vai preencher cmbModalidade
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MODALIDADES'
				},
				success: function(response) {
					$('#modalidade').empty();
					$('#modalidade').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = ''
						// caso exista algo na variável atendimento significa que o usuário esta alterando um valor
						// logo esses valores deveram vir preenchido com os dados desse atendimento
						if(atendimento){
							 opt = atendimento.AgendModalidade == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
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
				url: 'filtraAtendimento.php',
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
				url: 'filtraAtendimento.php',
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
				url: 'filtraAtendimento.php',
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
			// incluir responsáveis cadastrados
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'RESPONSAVEIS'
				},
				success: function(response) {
					if(obj && obj.responsavelID){
						$('#parentescoCadatrado').html("<option value=''>selecione</option>")
						response.data.forEach(function(item, index){
							let opt = obj.responsavelID == item.id?'<option selected value="'+item.id+'">'+item.nome+'</option>':'<option value="'+item.id+'">'+item.nome+'</option>'
							$('#parentescoCadatrado').append(opt);
						});
					} else {
						$('#parentescoCadatrado').html("<option selected value=''>selecione</option>")
						response.data.forEach(function(item, index){
							$('#parentescoCadatrado').append('<option value="'+item.id+'">'+item.nome+'</option>');
						});
					}
				},
				error: function(response) {
				}
			});
			//  incluir situação
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SITUACOES'
				},
				success: function(response) {
					if(response.status == 'success'){
						$('#situacao').html('<option value="">Selecione</option>')

						response.data.forEach(function(item, index){
							$('#situacao').append('<option value="'+item.SituId+'">'+item.SituaNome+'</option>')
						})
					}
				}
			});
		}

		function excluiServico(id){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
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

		function checkServicos(iAtendimento){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'CHECKSERVICO',
					'iAtendimento': iAtendimento
				},
				success: async function(response) {
					statusServicos = response.array.length?true:false;
					if(statusServicos){
						$('#dataServico').html('');

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
						$('#servicoValorTotal').html(`${float2moeda(response.valorTotal)}`).show();
						$('#dataServico').html(HTML).show();
						$('#servicoTable').show();
					}else{
						$('#servicoTable').hide();
					}
				}
			});
		}

		function resetServicoCmb(){
			// vai preencher cmbServicos
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
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
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MEDICOS'
				},
				success: function(response) {
					$('#medicos').empty();
					$('#medicos').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#medicos').append(opt)
					})
				}
			});
			// vai preencher cmbLocalAtendimento
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
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
					let iMedico = $('#medicos').val();
					$.ajax({
						type: 'POST',
						url: 'filtraAtendimento.php',
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
							<ul class="nav nav-tabs nav-tabs-bottom nav-justified">
								<li class="nav-item"><a id="navPaciente" href="#" class="nav-link rounded-top legitRipple active show" >Paciente</a></li>
								<li class="nav-item"><a id="navResponsavel" href="#" class="nav-link rounded-top legitRipple" >Responsável</a></li>
								<li class="nav-item"><a id="navAtendimento" href="#" class="nav-link rounded-top legitRipple" >Atendimento</a></li>
							</ul>

							<div class="tab-content">
								<!-- dados do paciente -->
								<form id="formPaciente" class="form-validate-jquery mt-4 p-2 tab-pane fade active show" action="#">
									<input id="pacienteId" type="hidden" name="pacienteId" value="">
									<div class="col-12 row text-center" id="selectPaciente">
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Selecione o paciente</h5>
										</div>
										<div class="col-11">
											<select id="paciente" name="paciente" class="select-search">
												
											</select>
										</div>
										<div class="col-1">
											<span class="action btn btn-principal legitRipple" id="addPaciente" style="user-select: none;">
												<i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
											</span>
										</div>
									</div>
									<div id="novoPaciente" class="fadeOut">
										<div class="row col-lg-12">
											<div class="col-lg-1 text-center">
												<input class="mr-1" id="fisica" name="pessoaTipo" type="radio" checked />
												<label for="fisica">Física</label>
											</div>
	
											<div class="col-lg-1 text-center">
												<input class="mr-1" id="juridica" name="pessoaTipo" type="radio" />
												<label for="fisica">Jurídica</label>
											</div>
										</div>
	
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Dados Pessoais</h5>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label>Prontuário <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Nome <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>CPF <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>CNS</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-3">
												<input id="prontuario" name="prontuario" type="text" class="form-control" placeholder="Prontuário Eletrônico" required>
											</div>
											<div class="col-lg-3">
												<input id="nome" name="nome" type="text" class="form-control" placeholder="Nome completo" required>
											</div>
											<div class="col-lg-3">
												<input id="cpf" name="cpf" type="text" class="form-control" placeholder="CPF" required>
											</div>
											<div class="col-lg-3">
												<input id="cns" name="cns" type="text" class="form-control" placeholder="Cartão do SUS">
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-2">
												<label>RG <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Emissor <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-2">
												<label>UF <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-2">
												<label>Sexo <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Data de Nascimento <span class="text-danger">*</span></label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-2">
												<input id="rg" name="rg" type="text" class="form-control" placeholder="RG" required>
											</div>
											<div class="col-lg-3">
												<input id="emissor" name="emissor" type="text" class="form-control" placeholder="Orgão Emissor" required>
											</div>
											<div class="col-lg-2">
												<select id="uf" name="uf" class="select-search" required>
													<option value="" selected>selecionar</option>
													<option value='BA'>BA</option>
												</select>
											</div>
											<div class="col-lg-2">
												<select id="sexo" name="sexo" class="form-control form-control-select2" required>
													<option value="" selected>selecionar</option>
													<option value="M">Masculino</option>
													<option value="F">Feminino</option>
												</select>
											</div>
											<div class="col-lg-3">
												<input id="nascimento" name="nascimento" type="date" class="form-control" placeholder="dd/mm/aaaa" required>
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-6">
												<label>Nome do Pai <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-6">
												<label>Nome da Mãe <span class="text-danger">*</span></label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-6">
												<input id="nomePai" name="nomePai" type="text" class="form-control" placeholder="Nome do Pai" required>
											</div>
											<div class="col-lg-6">
												<input id="nomeMae" name="nomeMae" type="text" class="form-control" placeholder="Nome da Mãe" required>
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-12">
												<label>Profissão <span class="text-danger">*</span></label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-12">
												<select id="profissao" name="profissao" class="form-control form-control-select2" required>
													<option selected value="">selecionar</option>
													<option value="1">Teste</option>
												</select>
											</div>
										</div>
	
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Endereco do Pacinte</h5>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label>CEP <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Endereco <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-2">
												<label>Nº <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Complemento</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-3">
												<input id="cep" name="cep" type="text" class="form-control" placeholder="CEP" required>
											</div>
											<div class="col-lg-4">
												<input id="endereco" name="endereco" type="text" class="form-control" placeholder="EX.: Rua, Av" required>
											</div>
											<div class="col-lg-2">
												<input id="numero" name="numero" type="text" class="form-control" placeholder="Número" required>
											</div>
											<div class="col-lg-3">
												<input id="complemento" name="complemento" type="text" class="form-control" placeholder="Complemento">
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-4">
												<label>Bairro <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Cidade <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Estado <span class="text-danger">*</span></label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-4">
												<input id="bairro" name="bairro" type="text" class="form-control" placeholder="Bairro" required>
											</div>
											<div class="col-lg-4">
												<input id="cidade" name="cidade" type="text" class="form-control" placeholder="Cidade" required>
											</div>
											<div class="col-lg-4">
												<input id="estado" name="estado" type="text" class="form-control" placeholder="Estado" required>
											</div>
										</div>
	
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Contato</h5>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label>Nome <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Telefone <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Celular <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>E-mail <span class="text-danger">*</span></label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-3">
												<input id="contato" name="contato" type="text" class="form-control" placeholder="Contato" required>
											</div>
											<div class="col-lg-3">
												<input id="telefone" name="telefone" type="text" class="form-control" placeholder="Res. / Com." required>
											</div>
											<div class="col-lg-3">
												<input id="celular" name="celular" type="text" class="form-control" placeholder="Celular" required>
											</div>
											<div class="col-lg-3">
												<input id="email" name="email" type="text" class="form-control" placeholder="E-mail" required>
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

									<!-- botões -->
									<div class="row col-12 my-4 ml-0 mr-0">
										<a class="col-2 btn btn-lg" href="atendimento.php" id="cancelar">Cancelar</a>
										<a class="col-2 btn btn-lg" href="#" id="voltarSelectPaciente">voltar</a>

										<div class="col-8" ></div>

										<button class="col-2 btn btn-lg btn-principal btnNext" id="salvarPaciente" data-tipo="PACIENTE">
											Próximo <i class="icon-arrow-right32"></i>
										</button>
									</div>
								</form>

								<!-- dados do responsável -->
								<form id="formResponsavel" class="form-validate-jquery mt-4 p-2 tab-pane fade" action="#">
									<input id="responsavelId" type="hidden" name="responsavelId" value="">
									<div class="col-12 row text-center" id="selectResponsavel">
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Selecione o responsável</h5>
										</div>
										<div class="col-11">
											<select id="parentescoCadatrado" name="parentescoCadatrado" class="select-search">
											</select>
										</div>
										<div class="col-1">
											<span class="action btn btn-principal legitRipple" id="addResponsavel" style="user-select: none;">
												<i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
											</span>
										</div>
									</div>	
									<div id="novoResponsavel" class="fadeOut">
										<div class="col-lg-12 row mb-lg-5 text-black-50">
											<div class="col-lg-8">
												<h5>Dados Pessoais do responsável</h5>
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-4">
												<label>Nome</label>
											</div>
											<div class="col-lg-4">
												<label>Parentesco</label>
											</div>
											<div class="col-lg-4">
												<label>Nascimento</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-4">
												<input id="nomeResp" name="nomeResp" type="text" class="form-control" placeholder="Nome">
											</div>
											<div class="col-lg-4">
												<select id="parentescoResp" name="parentesco" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
													<option value="tio">Tia/Tio</option>
													<option value="pai">Mãe/Pai</option>
												</select>
											</div>
											<div class="col-lg-4">
												<input id="nascimentoResp" name="nascimentoResp" type="date" class="form-control">
											</div>
										</div>
	
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Endereco do Responsável</h5>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label>CEP</label>
											</div>
											<div class="col-lg-4">
												<label>Endereco</label>
											</div>
											<div class="col-lg-2">
												<label>Nº</label>
											</div>
											<div class="col-lg-3">
												<label>Complemento</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-3">
												<input id="cepResp" name="cepResp" type="text" class="form-control" placeholder="CEP">
											</div>
											<div class="col-lg-4">
												<input id="enderecoResp" name="enderecoResp" type="text" class="form-control" placeholder="EX.: Rua, Av">
											</div>
											<div class="col-lg-2">
												<input id="numeroResp" name="numeroResp" type="text" class="form-control" placeholder="Número">
											</div>
											<div class="col-lg-3">
												<input id="complementoResp" name="complementoResp" type="text" class="form-control" placeholder="Complemento">
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
												<input id="bairroResp" name="bairroResp" type="text" class="form-control" placeholder="Bairro">
											</div>
											<div class="col-lg-4">
												<input id="cidadeResp" name="cidadeResp" type="text" class="form-control" placeholder="Cidade">
											</div>
											<div class="col-lg-4">
												<input id="estadoResp" name="estadoResp" type="text" class="form-control" placeholder="Estado">
											</div>
										</div>
	
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Contato</h5>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-4">
												<label>Telefone</label>
											</div>
											<div class="col-lg-4">
												<label>Celular</label>
											</div>
											<div class="col-lg-4">
												<label>E-mail</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-4">
												<input id="telefoneResp" name="telefoneResp" type="text" class="form-control" placeholder="Res. / Com.">
											</div>
											<div class="col-lg-4">
												<input id="celularResp" name="celularResp" type="text" class="form-control" placeholder="Celular">
											</div>
											<div class="col-lg-4">
												<input id="emailResp" name="emailResp" type="text" class="form-control" placeholder="E-mail">
											</div>
										</div>
	
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-12">
												<label>Observação</label>
											</div>
	
											<!-- campos -->
											<div class="col-lg-12">
												<textarea id="observacaoResp" name="observacaoResp" class="form-control" placeholder="Observações"></textarea>
											</div>
										</div>
									</div>

									<!-- botões -->
									<div class="row col-12 my-4 ml-0 mr-0">
										<button class="col-2 btn btn-lg btn-principal btnBack" id="backSalvarResponsavel" data-tipo="RESPONSAVEL" >
											<i class="icon-arrow-left32"></i> Anterior
										</button>

										<div class="col-8" ></div>

										<button class="col-2 btn btn-lg btn-principal btnNext" id="salvarResponsavel" data-tipo="RESPONSAVEL" >
											Próximo <i class="icon-arrow-right32"></i>
										</button>
									</div>
								</form>

								<!-- dados do atendimento -->
								<form id="formAtendimento" class="form-validate-jquery mt-4 p-2 tab-pane fade" action="#">
									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-4">
											<label>Data do Registro</label>
										</div>
										<div class="col-lg-4">
											<label>Modalidade</label>
										</div>
										<div class="col-lg-4">
											<label>Classificação do Atendimento</label>
										</div>

										<!-- campos -->
										<div class="col-lg-4">
											<input id="dataRegistro" name="dataRegistro" type="date" class="form-control" placeholder="Nome">
										</div>
										<div class="col-lg-4">
											<select id="modalidade" name="modalidade" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
											</select>
										</div>
										<div class="col-lg-4">
											<select id="classificacao" name="classificacao" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
											</select>
										</div>
									</div>

									<div class="col-lg-12 my-3 text-black-50">
										<h5>Serviços</h5>
									</div>

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-2">
											<label>Serviço</label>
										</div>
										<div class="col-lg-2">
											<label>Médicos</label>
										</div>
										<div class="col-lg-3">
											<label>Data do Atendimento</label>
										</div>
										<div class="col-lg-2">
											<label>Horário</label>
										</div>
										<div class="col-lg-2">
											<label>Local do Atendimento</label>
										</div>

										<!-- campos -->
										<div class="col-lg-2">
											<select id="servico" name="servico" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
											</select>
										</div>
										<div class="col-lg-2">
											<select id="medicos" name="medicos" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
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
											<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
											</select>
										</div>
										<!-- btnAddServico -->
										<div class="col-lg-1 text-right">
											<button id="incluirServico" class="btn btn-lg btn-principal" data-tipo="INCLUIRSERVICO" >
												<i class="fab-icon-open icon-add-to-list p-0" style="cursor: pointer; color: black"></i>
											</button>
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

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-12">
											<label>Observação</label>
										</div>

										<!-- campos -->
										<div class="col-lg-12">
											<textarea id="observacaoAtendimento" name="observacaoAtendimento" class="form-control" placeholder="Observações"></textarea>
										</div>
									</div>

									<div class="col-lg-12 mb-4">
										<!-- titulos -->
										<div class="col-lg-2">
											<label>Situacao</label>
										</div>

										<!-- campos -->
										<div class="col-lg-2">
											<select id="situacao" name="situacao" class="form-control form-control-select2">
											</select>
										</div>
									</div>

									<!-- botões -->
									<div class="row col-12 my-4 ml-0 mr-0">
										<button class="col-2 btn btn-lg btn-principal btnBack" id="backSalvarAtendimento" data-tipo="ATENDIMENTO" >
											<i class="icon-arrow-left32"></i> Anterior
										</button>

										<div class="col-8" ></div>

										<button class="col-2 btn btn-lg btn-principal btnSalvar" id="salvarAtendimento" data-tipo="ATENDIMENTO" >
											Concluir <i class="icon-check"></i>
										</button>
									</div>
								</form>
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
