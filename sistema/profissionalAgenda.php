<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Agenda';

include('global_assets/php/conexao.php');

// as duas lista "$visaoAtendente" e "$visaoProfissional" representam os perfis
// que podem ver a tela de atendimento na visão do atendente ou profissional respectivamente

$iUnidade = $_SESSION['UnidadeId'];
$usuarioId = $_SESSION['UsuarId'];
$iProfissional = $_POST['inputProfissionalId'];

$sql = "SELECT ProfiNome
	FROM Profissional
	WHERE ProfiId = $iProfissional";
$result = $conn->query("$sql");
$rowProfissional = $result->fetch(PDO::FETCH_ASSOC);
// as requisições são feitas ao carregar a página via AJAX no arquivo filtraProfissionalAgenda.php
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Agenda</title>

	<?php include_once("head.php"); ?>

	<!-- ///////////////////////////////////////////////////////////////////////////////////// -->
	<!-- <script src="global_assets/js/demo_pages/fullcalendar_advanced.js"></script> -->

	<!-- Global stylesheets -->
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap_limitless.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/layout.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/plugins/ui/ripple.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/ui/fullcalendar/fullcalendar.min.js"></script>
	<script src="global_assets/js/plugins/ui/fullcalendar/lang/pt-br.js"></script>
	
	<!-- /theme JS files -->
	<!-- ///////////////////////////////////////////////////////////////////////////////////// -->
	
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
		$(document).ready(function(){
			$('#modal-close-x').on('click', ()=>{
				$('#page-modal-agenda').fadeOut(200);
			})
		})

		var dataAtual = new Date().toLocaleString("pt-BR", {timeZone: "America/Bahia"});
		dataAtual = dataAtual.split(' ')[0];
		dataAtual = dataAtual.split('/')[2]+'-'+dataAtual.split('/')[1]+'-'+dataAtual.split('/')[0];

		$.ajax({
			type: 'POST',
			url: 'filtraProfissionalAgenda.php',
			dataType: 'json',
			data:{
				'tipoRequest': 'LOCAIS',
			},
			success: function(response) {
				$('#locaisAtendimento').html('').show()
				response.forEach(function(item){
					$('#locaisAtendimento').append(`<div class="fc-event" data-color="${item.cor}">${item.nome}</div>`)
				});
			}
		});

		document.addEventListener('DOMContentLoaded', function() {
			FullCalendarAdvanced.init();
		});

		var FullCalendarAdvanced = function() {
			var _componentFullCalendarEvents = function(agenda) {
				if (!$().fullCalendar || typeof Switchery == 'undefined' || !$().draggable) {
					console.warn('Warning - fullcalendar.min.js, switchery.min.js or jQuery UI is not loaded.');
					return;
				}
				var eventColors = agenda;
				// [
				// 	{
				// 		id: 999,
				// 		url: 'http://google.com/',
				// 		title: 'Meeting',
				// 		start: '2014-11-12T10:30:00',
				// 		end: '2014-11-12T12:30:00',
				// 		color: '#546E7A'
				// 	}
				// ];

				// Initialize the calendar
				$('.fullcalendar-external').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					editable: true,
					defaultDate: dataAtual,
					events: eventColors,
					locale: 'pt-br',
					droppable: true, // this allows things to be dropped onto the calendar
						drop: function() {
							$(this).remove();
							console.log($(this).data());
							$('#page-modal-agenda').fadeIn();
					},
					isRTL: $('html').attr('dir') == 'rtl' ? true : false
				});

				// Initialize the external events
				$('#external-events .fc-event').each(function() {
					// Different colors for events
					$(this).css({'backgroundColor': $(this).data('color'), 'borderColor': $(this).data('color')});

					// Store data so the calendar knows to render an event upon drop
					$(this).data('event', {
						title: $.trim($(this).html()), // use the element's text as the event title
						color: $(this).data('color'),
						stick: true // maintain when user navigates (see docs on the renderEvent method)
					});

					// Make the event draggable using jQuery UI
					$(this).draggable({
						zIndex: 999,
						revert: true, // will cause the event to go back to its
						revertDuration: 0 // original position after the drag
					});
				});
			};
			return {
				init: function() {
					$.ajax({
						type: 'POST',
						url: 'filtraProfissionalAgenda.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'AGENDA',
							'iProfissional': $('#iProfissional').val()
						},
						success: function(response) {
							_componentFullCalendarEvents(response);
							_componentPickatime();
						}
					});
				}
			}
		}();
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
				<form method="POST">
					<?php
						echo "<input id='iProfissional' type='hidden' value='$iProfissional'>";
					?>
				</form>
				<div class="card">
					<div class="card-header header-elements-inline">
						<h5 class="card-title">Agenda do Profissional</h5>
						<div class="header-elements">
							<div class="list-icons">
		                		<a class="list-icons-item" data-action="collapse"></a>
		                		<!-- <a class="list-icons-item" data-action="reload"></a>
		                		<a class="list-icons-item" data-action="remove"></a> -->
		                	</div>
	                	</div>
					</div>
					
					<div class="card-body">
						<p class="font-size-lg">A relação abaixo faz referência à agenda do profissional <b><?php echo $rowProfissional['ProfiNome']; ?></b></p>

						<div class="row">
							<div class="col-md-3">
								<div class="mb-3" id="external-events">
									<h6>Draggable Events</h6>
									<div id="locaisAtendimento" class="fc-events-container mb-3">
										<div class="fc-event" data-color="#546E7A">Sauna and stuff</div>
										<div class="fc-event" data-color="#26A69A">Lunch time</div>
										<div class="fc-event" data-color="#546E7A">Meeting with Fred</div>
										<div class="fc-event" data-color="#FF7043">Shopping</div>
										<div class="fc-event" data-color="#5C6BC0">Restaurant</div>
									</div>

									<div class="">
										
									</div>
								</div>
							</div>

							<div class="col-md-9">
								<div class="fullcalendar-external"></div>
							</div>
						</div>
					</div>
				</div>

				<!-- modal para setar a hora de inicio e fim -->
				<div id="page-modal-agenda" class="custon-modal">
                    <div class="custon-modal-container" style="max-width: 300px;">
                        <div class="card custon-modal-content">
                            <div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
                                <p class="h5">Hora de início e Fim</p>
                                <i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
                            </div>
							<div class="px-0">
								<div class="d-flex flex-row">
									<div class="col-lg-12">
										<form id="editaSituacao" name="alterarSituacao" method="POST" class="form-validate-jquery">
											<div class="form-group">
												<div class="col-lg-12 mt-2">
													<div class="col-lg-12">
														<label>Início <span class="text-danger">*</span></label>
													</div>
													<div class="col-lg-12">
														<input id="horaAgendaInicio" type="time" class="form-control" value="" required>
													</div>
												</div>
												<div class="col-lg-12 mt-2">
													<div class="col-lg-12">
														<label>Fim <span class="text-danger">*</span></label>
													</div>
													<div class="col-lg-12">
														<input id="horaAgendaFim" type="time" class="form-control" value="" required>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
								<div class="text-right m-2"><button id="mudarSituacao" class="btn btn-principal" role="button">Confirmar</button></div>
							</div>
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
