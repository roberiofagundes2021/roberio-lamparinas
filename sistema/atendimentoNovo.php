<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Atendimento';

include('global_assets/php/conexao.php');

// a requisição é feita ao carregar a página via AJAX no arquivo filtraAtendimento.php

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
	</style>
	
	<!-- Theme JS files -->
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <script src="global_assets/js/demo_pages/form_layouts.js"></script>
    <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/demo_pages/form_select2.js"></script>

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
			$('#incluirServico').on('click', function(e){
				e.preventDefault();
				console.log('Incluir Serviço');
			})

			$('.btnSalvar').each(function() {
				let target = $(this).data('tipo');
				$(this).on('click', (e) => {
					e.preventDefault();
					if(target == 'PACIENTE'){
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
							},
							error: function(response) {
								alerta(response.titulo, response.menssagem, response.tipo);
							}
						});
					} else if(target == 'RESPONSAVEL'){
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
							},
							error: function(response) {
								alerta(response.titulo, response.menssagem, response.tipo);
							}
						});
					}
				})
			});

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

			// incluir responsáveis cadastrados
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'RESPONSAVEIS'
				},
				success: function(response) {
					$('#parentescoCadatrado').html("<option selected value=''>selecione</option>")
					response.data.forEach(function(item, index){
						$('#parentescoCadatrado').append('<option value="'+item.id+'">'+item.nome+'</option>');
					});
				},
				error: function(response) {
				}
			});
		});
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
							<ul class="nav nav-tabs nav-justified">
								<li class="nav-item"><a href="#paciente" class="nav-link rounded-top legitRipple active show" data-toggle="tab">Paciente</a></li>
								<li class="nav-item"><a href="#responsavel" class="nav-link rounded-top legitRipple" data-toggle="tab">Responsável</a></li>
								<li class="nav-item"><a href="#atendimento" class="nav-link rounded-top legitRipple" data-toggle="tab">Atendimento</a></li>
							</ul>

							<div class="tab-content">
								<!-- dados do paciente -->
								<div id="paciente" class="mt-4 pl-2 tab-pane fade active show">
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
											<input id="prontuario" name="prontuario" type="text" class="form-control" placeholder="Prontuário Eletrônico">
										</div>
										<div class="col-lg-3">
											<input id="nome" name="nome" type="text" class="form-control" placeholder="Nome completo">
										</div>
										<div class="col-lg-3">
											<input id="cpf" name="cpf" type="text" class="form-control" placeholder="CPF">
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
											<input id="rg" name="rg" type="text" class="form-control" placeholder="RG">
										</div>
										<div class="col-lg-3">
											<input id="emissor" name="emissor" type="text" class="form-control" placeholder="Orgão Emissor">
										</div>
										<div class="col-lg-2">
											<select id="uf" name="uf" class="select-search">
												<option value="" selected>selecionar</option>
												<option value='BA'>BA</option>
											</select>
										</div>
										<div class="col-lg-2">
											<select id="sexo" name="sexo" class="form-control form-control-select2">
												<option value="" selected>selecionar</option>
												<option value="Masculino">Masculino</option>
												<option value="Feminino">Feminino</option>
											</select>
										</div>
										<div class="col-lg-3">
											<input id="nascimento" name="nascimento" type="date" class="form-control" placeholder="dd/mm/aaaa">
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
											<input id="nomePai" name="nomePai" type="text" class="form-control" placeholder="Nome do Pai">
										</div>
										<div class="col-lg-6">
											<input id="nomeMae" name="nomeMae" type="text" class="form-control" placeholder="Nome da Mãe">
										</div>
									</div>

									<div class="col-lg-12 mb-4 row">
										<!-- titulos -->
										<div class="col-lg-12">
											<label>Profissão <span class="text-danger">*</span></label>
										</div>

										<!-- campos -->
										<div class="col-lg-12">
											<select id="profissao" name="profissao" class="form-control form-control-select2">
												<option selected>selecionar</option>
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
											<input id="cep" name="cep" type="text" class="form-control" placeholder="CEP">
										</div>
										<div class="col-lg-4">
											<input id="endereco" name="endereco" type="text" class="form-control" placeholder="EX.: Rua, Av">
										</div>
										<div class="col-lg-2">
											<input id="numero" name="numero" type="text" class="form-control" placeholder="Número">
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
											<input id="bairro" name="bairro" type="text" class="form-control" placeholder="Bairro">
										</div>
										<div class="col-lg-4">
											<input id="cidade" name="cidade" type="text" class="form-control" placeholder="Cidade">
										</div>
										<div class="col-lg-4">
											<input id="estado" name="estado" type="text" class="form-control" placeholder="Estado">
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
											<input id="contato" name="contato" type="text" class="form-control" placeholder="Contato">
										</div>
										<div class="col-lg-3">
											<input id="telefone" name="telefone" type="text" class="form-control" placeholder="Res. / Com.">
										</div>
										<div class="col-lg-3">
											<input id="celular" name="celular" type="text" class="form-control" placeholder="Celular">
										</div>
										<div class="col-lg-3">
											<input id="email" name="email" type="text" class="form-control" placeholder="E-mail">
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

									<!-- botões -->
									<div class="col-lg-12 ml-2 my-4 row">
										<button class="btn btn-lg btn-principal btnSalvar" id="salvarPaciente" data-tipo="PACIENTE" >salvar</button>
										<a href="atendimento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
									</div>
								</div>

								<!-- dados do responsável -->
								<div id="responsavel" class="mt-4 pl-2 tab-pane fade">
									<div class="col-lg-12 row mb-lg-5 text-black-50">
										<div class="col-lg-8">
											<h5>Dados Pessoais do responsável</h5>
										</div>	
										<div class="col-lg-4">
											<div class="col-lg-12">
												<label>Já cadastrado</label>
											</div>
											<div class="col-lg-12">
												<select id="parentescoCadatrado" name="parentescoCadatrado" class="select-search">
												</select>
											</div>
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

									<!-- botões -->
									<div class="col-lg-12 ml-2 my-4 row">
										<button class="btn btn-lg btn-principal btnSalvar" id="salvarResponsavel" data-tipo="RESPONSAVEL" >salvar</button>
										<a href="atendimento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
									</div>
								</div>

								<!-- dados do atendimento -->
								<div id="atendimento" class="mt-4 pl-2 tab-pane fade">
									<form id="submitAtendimento" method="POST" >
										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-2">
												<label>Data do Registro</label>
											</div>
											<div class="col-lg-3">
												<label>Paciente</label>
											</div>
											<div class="col-lg-1">
												<!-- icone -->
											</div>
											<div class="col-lg-3">
												<label>Modalidade</label>
											</div>
											<div class="col-lg-3">
												<label>Classificação do Atendimento</label>
											</div>

											<!-- campos -->
											<div class="col-lg-2">
												<input id="dataRegistro" name="dataRegistro" type="date" class="form-control" placeholder="Nome">
											</div>
											<div class="col-lg-3">
												<select id="paciente" name="paciente" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-1" style="max-width:50px">
												<i class="icon-add py-2" style="cursor: pointer; font-size:25px;"></i>
											</div>
											<div class="col-lg-3">
												<select id="modalidade" name="modalidade" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-3">
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
											<div class="col-lg-3">
												<label>Serviço</label>
											</div>
											<div class="col-lg-3">
												<label>Médicos</label>
											</div>
											<div class="col-lg-2">
												<label>Data do Atendimento</label>
											</div>
											<div class="col-lg-1">
												<label>Horário</label>
											</div>
											<div class="col-lg-3">
												<label>Local do Atendimento</label>
											</div>

											<!-- campos -->
											<div class="col-lg-3">
												<select id="servico" name="servico" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-3">
												<select id="medicos" name="medicos" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-2">
												<input id="dataAtendimento" name="dataAtendimento" type="date" class="form-control">
											</div>
											<div class="col-lg-1">
												<input id="horaAtendimento" name="horaAtendimento" type="text" class="form-control">
											</div>
											<div class="col-lg-3">
												<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2">
													<option value="" selected>selecionar</option>
												</select>
											</div>
										</div>

										<!-- btnAddServico -->
										<div class="col-lg-12 my-4 text-right px-4">
											<button id="incluirServico" class="btn btn-lg btn-principal" id="salvarAtendimento" data-tipo="INCLUIRSERVICO" >incluir</button>
										</div>

										<div class="col-lg-12 mb-4">
											<table class="table" id="tblServicos" style="background-color: rgba(0,0,0, 0.05)">
												<thead>
													<tr class="bg-slate">
														<th>Procedimento</th>
														<th>Médicos</th>
														<th>Data do Atendimento</th>
														<th>Horário</th>
														<th>Local do Atendimento</th>
														<th>Valor</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Cirurgia do coração</td>
														<td>Dr. Rubens Pereira</td>
														<td>14/05/2022</td>
														<td>14:00</td>
														<td>Setor de Cirurgia</td>
														<td>R$ 400,00</td>
													</tr>
													<tr>
														<td>Exame de Sangue</td>
														<td>Dra. Fernanda Pessoa</td>
														<td>13/05/2022</td>
														<td>10:00</td>
														<td>Clínica X</td>
														<td>R$ 100,00</td>
													</tr>
												</tbody>
												<tfoot>
													<tr>
														<th colspan="5" class="text-right font-weight-bold" style="font-size: 16px;">
															<div>Valor(R$):</div>
														</th>
														<th colspan="1" class="mr-1">
															<div id="total" class="text-center font-weight-bold" style="font-size: 15px;">R$ 500,00</div>
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
												<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
											</div>
										</div>

										<div class="col-lg-12 mb-4">
											<!-- titulos -->
											<div class="col-lg-2">
												<label>Situacao</label>
											</div>

											<!-- campos -->
											<div class="col-lg-2">
												<select id="situacao" name="situacao" class="custom-select">
													<option value="" selected>Agendado</option>
													<option value="" selected>Liberado</option>
													<option value="" selected>Atendido</option>
													<option value="" selected>Em espera</option>
												</select>
											</div>
										</div>
										

										<!-- botões -->
										<div class="col-lg-12 ml-2 my-4 row">
											<button class="btn btn-lg btn-principal btnSalvar" id="salvarAtendimento" data-tipo="ATENDIMENTO" >salvar</button>
											<a href="atendimento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
										</div>
									</form>
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
