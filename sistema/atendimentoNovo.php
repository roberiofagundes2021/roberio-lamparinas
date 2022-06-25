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
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
    <script src="global_assets/js/demo_pages/form_layouts.js"></script>
    <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>	

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
    
    <!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script src="global_assets/js/plugins/ui/ripple.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/main/jquery.min.js"></script>
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	
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
					console.log(target);
				})
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
								<li class="nav-item"><a href="#triagem" class="nav-link rounded-top legitRipple" data-toggle="tab">Triágem</a></li>
							</ul>

							<div class="tab-content">
								<!-- dados do paciente -->
								<div id="paciente" class="mt-4 pl-2 tab-pane fade active show">
									<form id="submitPaciente" method="POST" >
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
												<label for="prontuario">Prontuário</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Nome</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">CPF</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">CNS</label>
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
												<label for="prontuario">RG</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Emissor</label>
											</div>
											<div class="col-lg-2">
												<label for="prontuario">UF</label>
											</div>
											<div class="col-lg-2">
												<label for="prontuario">Sexo</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Data de Nascimento</label>
											</div>

											<!-- campos -->
											<div class="col-lg-2">
												<input id="rg" name="rg" type="text" class="form-control" placeholder="RG">
											</div>
											<div class="col-lg-3">
												<input id="emissor" name="emissor" type="text" class="form-control" placeholder="Orgão Emissor">
											</div>
											<div class="col-lg-2">
												<select id="uf" name="uf" class="custom-select">
													<option selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-2">
												<select id="sexo" name="sexo" class="custom-select">
													<option value="" selected>selecionar</option>
													<option value="Masculino">Masculino</option>
													<option value="Feminino">Feminino</option>
												</select>
											</div>
											<div class="col-lg-3">
												<input id="Nascimento" name="nascimento" type="date" class="form-control" placeholder="dd/mm/aaaa">
											</div>
										</div>

										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-6">
												<label for="prontuario">Nome do Pai</label>
											</div>
											<div class="col-lg-6">
												<label for="prontuario">Nome da Mãe</label>
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
												<label for="prontuario">Profissão</label>
											</div>

											<!-- campos -->
											<div class="col-lg-12">
												<select id="profissao" name="profissao" class="custom-select">
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
												<label for="prontuario">CEP</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Endereco</label>
											</div>
											<div class="col-lg-2">
												<label for="prontuario">Nº</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Complemento</label>
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
												<label for="prontuario">Bairro</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Cidade</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Estado</label>
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
												<label for="prontuario">Nome</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Telefone</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Celular</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">E-mail</label>
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
												<label for="prontuario">Observação</label>
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
									</form>
								</div>

								<!-- dados do responsável -->
								<div id="responsavel" class="mt-4 pl-2 tab-pane fade">
									<form id="submitResponsavel" method="POST" >
										<div class="col-lg-12 my-3 text-black-50">
											<h5>Dados Pessoais do responsável</h5>
										</div>

										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-4">
												<label for="prontuario">Nome</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Parentesco</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Nascimento</label>
											</div>

											<!-- campos -->
											<div class="col-lg-4">
												<input id="nomeRep" name="nomeRep" type="text" class="form-control" placeholder="Nome">
											</div>
											<div class="col-lg-4">
												<select id="parentesco" name="parentesco" class="custom-select">
													<option value="" selected>selecionar</option>
													<option value="tio">Tia/Tio</option>
													<option value="pai">Mãe/Pai</option>
												</select>
											</div>
											<div class="col-lg-4">
												<input id="nascimentoRep" name="nascimentoRep" type="date" class="form-control">
											</div>
										</div>

										<div class="col-lg-12 my-3 text-black-50">
											<h5>Endereco do Responsável</h5>
										</div>

										<div class="col-lg-12 mb-4 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label for="prontuario">CEP</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Endereco</label>
											</div>
											<div class="col-lg-2">
												<label for="prontuario">Nº</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Complemento</label>
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
												<label for="prontuario">Bairro</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Cidade</label>
											</div>
											<div class="col-lg-4">
												<label for="prontuario">Estado</label>
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
												<label for="prontuario">Nome</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Telefone</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">Celular</label>
											</div>
											<div class="col-lg-3">
												<label for="prontuario">E-mail</label>
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
												<label for="prontuario">Observação</label>
											</div>

											<!-- campos -->
											<div class="col-lg-12">
												<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
											</div>
										</div>

										<!-- botões -->
										<div class="col-lg-12 ml-2 my-4 row">
											<button class="btn btn-lg btn-principal btnSalvar" id="salvarResponsavel" data-tipo="RESPONSAVEL" >salvar</button>
											<a href="atendimento.php" class="btn btn-lg" id="cancelar">Cancelar</a>
										</div>
									</form>
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
												<select id="paciente" name="paciente" class="custom-select">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-1" style="max-width:50px">
											<i class="icon-add py-2" style="cursor: pointer; font-size:25px;"></i>
										</div>
											<div class="col-lg-3">
												<select id="modalidade" name="modalidade" class="custom-select">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-3">
												<select id="classificacao" name="classificacao" class="custom-select">
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
												<select id="servico" name="servico" class="custom-select">
													<option value="" selected>selecionar</option>
												</select>
											</div>
											<div class="col-lg-3">
												<select id="medicos" name="medicos" class="custom-select">
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
												<select id="localAtendimento" name="localAtendimento" class="custom-select">
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
												<label for="prontuario">Observação</label>
											</div>

											<!-- campos -->
											<div class="col-lg-12">
												<textarea id="observacao" name="observacao" class="form-control" placeholder="Observações"></textarea>
											</div>
										</div>

										<div class="col-lg-12 mb-4">
											<!-- titulos -->
											<div class="col-lg-2">
												<label for="prontuario">Situacao</label>
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

								<!-- dados da triagem -->
								<div id="triagem" class="mt-4 pl-2 tab-pane fade">
									<form id="submitTriagem" method="POST" >

										<!-- botões -->
										<div class="col-lg-12 ml-2 my-4 row">
											<button class="btn btn-lg btn-principal btnSalvar" id="salvarTriagem" data-tipo="TRIAGEM" >salvar</button>
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
