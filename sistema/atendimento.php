<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Atendimento';

include('global_assets/php/conexao.php');

// as duas lista "$visaoAtendente" e "$visaoProfissional" representam os perfis
// que podem ver a tela de atendimento na visão do atendente ou profissional respectivamente

$iUnidade = $_SESSION['UnidadeId'];
$usuarioId = $_SESSION['UsuarId'];
$acesso = 'ATENDIMENTO';

$sql = "SELECT ProfiId, ProfiUsuario
FROM Profissional
WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$acesso = isset($row['ProfiId'])?'PROFISSIONAL':'ATENDIMENTO';

// $acesso = isset($row['ProfiId'])?'PROFISSIONAL':'PROFISSIONAL';

// as requisições são feitas ao carregar a página via AJAX no arquivo filtraAtendimento.php
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Atendimento</title>

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
		// buscar todos os atendimento ao entrar na pagina
		
		$(document).ready(function() {
			getAtendimentos();
			
			$.fn.dataTable.moment('DD/MM/YYYY'); //Para corrigir a ordenação por data
			/* Início: Tabela Personalizada do Setor Publico */
			$('#AgendamentoTable').DataTable({
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Horario
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: true,   //Espera
					width: "5%",
					targets: [2]
				},
				{ 
					orderable: true,   //Prontuário
					width: "10%",
					targets: [3]
				},
				{ 
					orderable: true,   //Paciente
					width: "20%",
					targets: [4]
				},
				{ 
					orderable: true,   //Profissional
					width: "10%",
					targets: [5]
				},
				{ 
					orderable: true,   //Modalidade
					width: "20%",
					targets: [6]
				},
				{ 
					orderable: true,   //Procedimento
					width: "5%",
					targets: [7]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [8]
				},
				{ 
					orderable: true,   //Ações
					width: "5%",
					targets: [9]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: {
                        'first': 'Primeira',
                        'last': 'Última',
                        'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                        'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                    }
                }
			});

			$('#AtendimentoTable').DataTable({
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Horario
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: true,   //Espera
					width: "5%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Nº Registro
					width: "5%",
					targets: [3]
				},
				{ 
					orderable: true,   //Prontuário
					width: "5%",
					targets: [4]
				},
				{ 
					orderable: true,   //Paciente
					width: "20%",
					targets: [5]
				},
				{ 
					orderable: true,   //Profissional
					width: "10%",
					targets: [6]
				},
				{ 
					orderable: true,   //Modalidade
					width: "20%",
					targets: [7]
				},
				{ 
					orderable: true,   //Procedimento
					width: "5%",
					targets: [8]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [9]
				},
				{ 
					orderable: true,   //Ações
					width: "5%",
					targets: [10]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: {
                        'first': 'Primeira',
                        'last': 'Última',
                        'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                        'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                    }
                }
			});
			
			/* Início: Tabela Personalizada do Setor Publico */
			$('#AtendimentoTableEspera').DataTable({
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Horario
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: true,   //Espera
					width: "5%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Nº Registro
					width: "5%",
					targets: [3]
				},
				{ 
					orderable: true,   //Prontuário
					width: "5%",
					targets: [4]
				},
				{ 
					orderable: true,   //Paciente
					width: "20%",
					targets: [5]
				},
				{ 
					orderable: true,   //Procedimento
					width: "10%",
					targets: [6]
				},
				{ 
					orderable: true,   //Risco
					width: "20%",
					targets: [7]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [8]
				},
				{ 
					orderable: true,   //Ações
					width: "5%",
					targets: [9]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: {
                        'first': 'Primeira',
                        'last': 'Última',
                        'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                        'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                    }
                }
			});

			/* Início: Tabela Personalizada do Setor Publico */
			$('#AtendimentoTableAtendido').DataTable({
				"order": [[ 0, "desc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{
					orderable: true,   //Data
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Horario
					width: "10%",
					targets: [1]
				},
				{ 
					orderable: true,   //Espera
					width: "5%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Nº Registro
					width: "5%",
					targets: [3]
				},
				{ 
					orderable: true,   //Prontuário
					width: "5%",
					targets: [4]
				},
				{ 
					orderable: true,   //Paciente
					width: "20%",
					targets: [5]
				},
				{ 
					orderable: true,   //Procedimento
					width: "10%",
					targets: [6]
				},
				{ 
					orderable: true,   //Risco
					width: "20%",
					targets: [7]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [8]
				},
				{ 
					orderable: true,   //Ações
					width: "5%",
					targets: [9]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: {
                        'first': 'Primeira',
                        'last': 'Última',
                        'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                        'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                    }
                }
			});
			
			// Select2 for length menu styling
			var _componentSelect2 = function() {
				if (!$().select2) {
					console.warn('Warning - select2.min.js is not loaded.');
					return;
				}

				// Initialize
				$('.dataTables_length select').select2({
					minimumResultsForSearch: Infinity,
					dropdownAutoWidth: true,
					width: 'auto'
				});
			};

			_componentSelect2();
			
			/* Fim: Tabela Personalizada */
		});

		function setAttributs(){
			$('.atender').each(function(index, element){
				$(element).on('click', function(e){
					e.preventDefault()
					let iAtendimento = $(this).data('atendimento')
					let iAtendimentoEletivo = $(this).data('eletivo')
					let AtClaChave = $(this).data('clachave')
					let AtClaNome = $(this).data('clanome')

					$('#iAtendimentoId').val(iAtendimento)
					$('#iAtendimentoEletivoId').val(iAtendimentoEletivo)
					$('#ClaChave').val(AtClaChave)
					$('#ClaNome').val(AtClaNome)

					$('#dadosPost').attr('action', 'atendimentoEletivo.php')
					$('#dadosPost').attr('method', 'POST')
					$('#dadosPost').submit()
				})
			});

			// btn para editar ou excluir atendimento
			$('.atualizaAtendimento').each(function() {
				$(this).on('click',function(e){
					e.preventDefault()
					let atendimento = $(this).data('atendimento')

					$('#iAtendimentoId').val(atendimento)
					$('#dadosPost').attr('action', 'atendimentoNovo.php')
					$('#dadosPost').submit()
				})
			})

			$('.excluiAtendimento').each(function() {
				$(this).on('click',function(e){
					e.preventDefault()
					let atendimento = $(this).data('atendimento')
					$.ajax({
						type: 'POST',
						url: 'filtraAtendimento.php',
						dataType: 'json',
						data: {
							'tipoRequest': 'EXCLUI',
							'iAtendimento': atendimento
						},
						success: function(response) {
							if(response.status  == 'success'){
								alerta(response.titulo, response.menssagem, response.status)
								getAtendimentos()
							} else {
								alerta(response.titulo, response.menssagem, response.status)
							}
						}
					});
				})
			})
		}
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function getAtendimentos(){
			let acessoTipo = $('#sAcesso').val();
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data: {
					'tipoRequest': 'ATENDIMENTOS',
					'acesso': acessoTipo
				},
				success: function(response) {
					//|--Aqui é criado o DataTable caso seja a primeira vez q é executado e o clear é para evitar duplicação na tabela depois da primeira pesquisa

					if(response.acesso == 'ATENDIMENTO'){
						$('#AgendamentoTable').DataTable().clear().draw()
	
						tableAgendamento = $('#AgendamentoTable').DataTable()
						let rowNodeAgendamento
	
						response.dataAgendamento.forEach(item => {
							rowNodeAgendamento = tableAgendamento.row.add(item.data).draw().node()
							$(rowNodeAgendamento).attr('class', 'text-center')
							$(rowNodeAgendamento).find('td:eq(9)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeAgendamento).find('td:eq(9)').attr('onclick', `alteraSituacao('${item.identify.situacao}', this)`)
							$(rowNodeAgendamento).find('td:eq(10)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeAgendamento).find('td:eq(10)').attr('data-observacao', `${item.identify.sObservacao}`)
						})

						$('#AtendimentoTable').DataTable().clear().draw()

						tableAtendimento = $('#AtendimentoTable').DataTable()
						let rowNodeAtendimento

						response.dataAtendimento.forEach(item => {
							rowNodeAtendimento = tableAtendimento.row.add(item.data).draw().node()
							$(rowNodeAtendimento).attr('class', 'text-center')
							$(rowNodeAtendimento).find('td:eq(9)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeAtendimento).find('td:eq(9)').attr('onclick', `alteraSituacao('${item.identify.situacao}', this)`)
							$(rowNodeAtendimento).find('td:eq(10)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeAtendimento).find('td:eq(10)').attr('data-observacao', `${item.identify.sObservacao}`)
						})
					} else if (response.acesso == 'PROFISSIONAL'){
						let tableE = $('#AtendimentoTableEspera').DataTable().clear().draw()
						let tableA = $('#AtendimentoTableAtendido').DataTable().clear().draw()
	
						tableE = $('#AtendimentoTableEspera').DataTable()
						tableA = $('#AtendimentoTableAtendido').DataTable()
						let rowNodeE
						let rowNodeA
	
						response.dataEspera.forEach(item => {
							rowNodeE = tableE.row.add(item.data).draw().node()
							$(rowNodeE).attr('class', 'text-center')
							$(rowNodeE).find('td:eq(9)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeE).find('td:eq(9)').attr('data-observacao', `${item.identify.sObservacao}`)
						})
						response.dataAtendido.forEach(item => {
							rowNodeA = tableA.row.add(item.data).draw().node()
							$(rowNodeA).attr('class', 'text-center')
							$(rowNodeA).find('td:eq(8)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeA).find('td:eq(8)').attr('onclick', `alteraSituacao('${item.identify.situacao}', this)`)
							$(rowNodeA).find('td:eq(9)').attr('data-atendimento', `${item.identify.iAtendimento}`)
							$(rowNodeA).find('td:eq(9)').attr('data-observacao', `${item.identify.sObservacao}`)
						})
	
					}
					setAttributs()
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
				<form id='dadosPost' method="POST">
					<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='' />
					<input type='hidden' id='iAtendimentoEletivoId' name='iAtendimentoEletivoId' value='' />
					<input type='hidden' id='ClaChave' name='ClaChave' value='' />
					<input type='hidden' id='ClaNome' name='ClaNome' value='' />
				</form>

				<?php if ($acesso == 'ATENDIMENTO'){ ?>
					<!-- Visão Atendente -->		
					<div class="row">
						<div class="col-lg-12">
							<!-- Basic responsive configuration -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Relação de Atendimentos</h3>
								</div>

								<div class="card-body">
									<div class="row">
										<div class="col-lg-8">
											<p class="font-size-lg">A relação abaixo faz referência aos atendimentos da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
										</div>
										<div class="col-lg-4 text-right">
											<div class="text-right">
												<a href="agendaMedica.php" class="btn" role="button">Agenda médica</a>
												<a href="atendimentoNovo.php" class="btn btn-principal" role="button">Novo Atendimento</a>
												<a href="#collapse-imprimir-relacao" class="btn bg-slate-700 btn-icon" role="button" data-toggle="collapse" data-placement="bottom" data-container="body">
													<i class="icon-printer2"></i>																						
												</a>
											</div>
										</div>	
									</div>
								</div>
							</div>
							<!-- Agendamentos -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Agendamentos</h3>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
										</div>
									</div>
								</div>

								<table class="table" id="AgendamentoTable">
									<thead>
										<tr class="bg-slate text-center">
											<th>Data</th>
											<th>Horario</th>
											<th>Espera</th>
											<th>Prontuário</th>			
											<th>Paciente</th>
											<th>Profissional</th>
											<th>Modalidade</th>
											<th>Procedimento</th>
											<th>Situação</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>
 							<!-- Atendimentos -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Atendimentos</h3>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
										</div>
									</div>
								</div>

								<table class="table" id="AtendimentoTable">
									<thead>
										<tr class="bg-slate text-center">
											<th>Data</th>
											<th>Horario</th>
											<th>Espera</th>
											<th>Nº Registro</th>
											<th>Prontuário</th>			
											<th>Paciente</th>
											<th>Profissional</th>
											<th>Modalidade</th>
											<th>Procedimento</th>
											<th>Situação</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php } elseif ($acesso == 'PROFISSIONAL'){ ?>
					<!-- Visão Atendente -->		
					<div class="row">
						<div class="col-lg-12">
							<!-- Basic responsive configuration -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Relação de Atendimento</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-lg-9">
											<p class="font-size-lg">A relação abaixo faz referência aos atendimentos da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
										</div>
										<div class="col-lg-3">
											<div class="dropdown p-0" style="float:right; margin-left: 5px;">
												<div class="text-right col-sm-2 p-0"><a href="#" class="btn bg-secondary" role="button">Imprimir Relação</a></div>
											</div>
										</div>
									</div>
								</div>
							</div>
								
							<!-- Em espera -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Pacientes em espera</h3>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
										</div>
									</div>
								</div>

								<table class="table" id="AtendimentoTableEspera">
									<thead>
										<tr class="bg-slate text-center">
											<th>Data</th>
											<th>Horario</th>
											<th>Espera</th>
											<th>Nº Registro</th>
											<th>Prontuário</th>			
											<th>Paciente</th>
											<th>Procedimento</th>
											<th>Risco</th>
											<th>Situação</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody id="dataAtendimentos">

									</tbody>
								</table>
							</div>

							<!-- Atendidos -->
							<div  class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Pacientes Atendidos</h3>
									<div class="header-elements">
										<div class="list-icons">
											<a class="list-icons-item" data-action="collapse"></a>
										</div>
									</div>
								</div>

								<table class="table" id="AtendimentoTableAtendido">
									<thead>
										<tr class="bg-slate text-center">
											<th>Data</th>
											<th>Horario</th>
											<th>Espera</th>
											<th>Nº Registro</th>
											<th>Prontuário</th>			
											<th>Paciente</th>
											<th>Procedimento</th>
											<th>Risco</th>
											<th>Situação</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody id="dataAtendimentos">

									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php }?>

				<!--Modal Editar Situação-->
                <div id="page-modal-situacao" class="custon-modal">
                    <div class="custon-modal-container" style="max-width: 300px;">
                        <div class="card custon-modal-content">
                            <div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
                                <p class="h5">Situação do Atendimento</p>
                                <i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                            </div>
							<div class="px-0">
								<div class="d-flex flex-row">
									<div class="col-lg-12">
										<form id="editaSituacao" name="alterarSituacao" method="POST" class="form-validate-jquery">
											<div class="form-group">
												<div class="col-lg-12 mt-2">
													<div class="col-lg-12">
														<label for="cmbSituacao">Situação <span class="text-danger">*</span></label>
													</div>
													<div class="col-lg-12">
														<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" required>
															<!--  -->
														</select>
													</div>
												</div>
												<div class="col-lg-12 mt-4">
													<!-- titulos -->
													<div class="col-lg-12">
														<label>Observação <span class="text-danger">*</span></label>
													</div>

													<!-- campos -->
													<div class="col-lg-12">
														<textarea id="observacaoModal" name="observacaoModal" class="form-control" placeholder="Observações"></textarea>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
								<div class="text-right m-2">
									<button id="mudarSituacao" class="btn btn-principal" role="button">Confirmar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input id='iAtendimento' name='iAtendimento' type='hidden' value=''/>
				<?php
					echo "<input id='sAcesso' name='sAcesso' type='hidden' value='$acesso'/>"
				?>

				<!-- Agenda Médica -->
				<form id="formAgendaMedica" method="POST" action="agendaMedica.php">
					<input id="iOrigem" name="iOrigem" type="hidden" value="atendimento.php" />
				</form>
			</div>
			<?php include_once("footer.php"); ?>
		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
