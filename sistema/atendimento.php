<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Atendimento';

include('global_assets/php/conexao.php');

// a requisição é feita ao carregar a página via AJAX no arquivo filtraAtendimento.php

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Ordem de Compra</title>

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
		getAtendimentos();
			
		$(document).ready(function() {
			
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
					width: "5%",
					targets: [1]
				},
				{ 
					orderable: true,   //Paciente
					width: "10%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Médico
					width: "10%",
					targets: [3]
				},
				{ 
					orderable: true,   //Prrocedimento
					width: "10%",
					targets: [4]
				},
				{ 
					orderable: true,   //Modalidade
					width: "15%",
					targets: [5]
				},
				{ 
					orderable: true,   //Contato
					width: "20%",
					targets: [6]
				},
				{
					orderable: true,   //Situação
					width: "10%",
					targets: [7]
				},
				{ 
					orderable: true,   //Ações
					width: "10%",
					targets: [8]
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

			$('#modal-close-x').on('click', ()=>{
				$('#iAtendimento').val('')
				$('#page-modal-situacao').fadeOut(200);
			})

			$('#cmbSituacao').on('change', ()=>{
				let cmbSituacao = $('#cmbSituacao').val();
				$('iSituacao').val(cmbSituacao)
			})

			$('#mudarSituacao').on('click', ()=>{
				$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MUDARSITUACAO',
					'iAtendimento': $('#iAtendimento').val(),
					'iSituacao': $('#cmbSituacao').val()
				},
				success: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#iAtendimento').val('')
					$('#page-modal-situacao').fadeOut(200);
					getAtendimentos();
				},
				error: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
					$('#iAtendimento').val('')
					$('#page-modal-situacao').fadeOut(200);
					getAtendimentos();
				}
			});
			})
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function getAtendimentos(){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'ATENDIMENTOS'
				},
				success: function(response) {
					//|--Aqui é criado o DataTable caso seja a primeira vez q é executado e o clear é para evitar duplicação na tabela depois da primeira pesquisa

					let table = $('#AgendamentoTable').DataTable().clear().draw()

					table = $('#AgendamentoTable').DataTable()
					let rowNode

					response.forEach(item => {
						rowNode = table.row.add(item.data).draw().node()
						$(rowNode).attr('class', 'text-center')
						$(rowNode).attr('data-atendimento', item.identify.iAtendimento)
						$(rowNode).find('td:eq(7)').attr('onclick', `alteraSituacao('${item.identify.situacao}', this)`)
						$(rowNode).find('td:eq(7)').attr('data-atendimento', `${item.identify.iAtendimento}`)
					})
				}
			});
		}
		function alteraSituacao(situacao, element){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'SITUACOES'
				},
				success: function(response) {
					$('#iAtendimento').val($(element).data('atendimento'))
					$('#cmbSituacao').empty()
					response.forEach(item => {
						let opt = item.SituaChave === situacao? `<option selected value="${item.SituaId}">${item.SituaNome}</option>`:`<option value="${item.SituaId}">${item.SituaNome}</option>`
						$('#cmbSituacao').append(opt)
					})
					$('#page-modal-situacao').fadeIn(200);
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

				<!-- Info blocks -->		
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h5 class="card-title">Relação de Atendimentos</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>					

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9">
										 A relação abaixo faz referência aos atendimentos da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b>
									</div>
									<div class="col-lg-3">
										<div class="text-right"><a href="atendimentoNovo.php" class="btn btn-principal" role="button">Novo Atendimento</a></div>
									</div>
								</div>
							</div>

							<table class="table" id="AgendamentoTable">
								<thead>
									<tr class="bg-slate text-center">
										<th>Data</th>
										<th>Horario</th>
										<th>Paciente</th>
										<th>Médico</th>
										<th>Procedimento</th>			
										<th>Modalidade</th>
										<th>Contato</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody id="dataAtendimentos">

								</tbody>
							</table>
						</div>
						<!-- /basic responsive configuration -->

					</div>
				</div>

				<!--Modal Editar Situação-->
                <div id="page-modal-situacao" class="custon-modal">
                    <div class="custon-modal-container" style="max-width: 300px;">
                        <div class="card custon-modal-content">
                            <div class="custon-modal-title mb-2">
                                <p class="h3">Dados Produto</p>
                                <i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                            </div>
							<form id="editaSituacao" name="incluirProduto" method="POST" class="form-validate-jquery">
								<div class="px-3 ">
									<div class="d-flex flex-row ">
										<div class="col-lg-12">
											<label for="cmbSituacao">Situação <span class="text-danger">*</span></label>
											<div class="form-group">
												<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" required>
													<!--  -->
												</select>
											</div>
										</div>
									</div>
								</div>
							</form>
							<div class="text-right m-2"><button id="mudarSituacao" class="btn btn-principal" role="button">Confirmar</button></div>
                        </div>
                    </div>
                </div>
			</div>
			<!-- /content area -->

			<!-- inf block -->
			<input id="iAtendimento" name="iAtendimento" type="hidden" value="" />
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>