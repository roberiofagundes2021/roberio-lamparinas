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
			$('#salvarAgenda').on('click', ()=>{
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
						FullCalendarAdvanced.init();
					}
				});
			})
		})

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
					$('#locaisAtendimento').append(`<div data-agenda="${item.id}" class="fc-event" data-local="${item.idLocal}" data-color="${item.cor}">${item.nome}</div>`)
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
				// agenda = [
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
						right: 'month,agendaWeek'
					},
					editable: true,
					defaultDate: dataAtual,
					events: eventColors,
					timeZone: 'America/Bahia',
					locale: 'pt-br',
					droppable: true,
						drop: function() { //ao soltar
							// $(this).remove();
							let id = event.id?event.id:(event._id?event._id:'N/A')
							let localId = event.localId
							let title = event.title
							let dtI=event.start?event.start._i:''
							let dtF=event.end?event.end._i:''

							let data = formatDate(dtI, dtF)

							$.ajax({
								type: 'POST',
								url: 'filtraProfissionalAgenda.php',
								dataType: 'json',
								data:{
									'tipoRequest': 'SETAGENDA',
									'id':id,
									'localId':localId,
									'title':title,
									'dataI':data.dataI,
									'dataF':data.dataF,
									'dataF':data.dataF
								},
								success: function(response) {
									console.log(response)
								}
							});
						},
						eventClick: function(event, jsEvent, view) { //ao clicar em cima
							console.log(event)
							let dtI=event.start?event.start._i:''
							let dtF=event.end?event.end._i:''

							let data = formatDate(dtI, dtF)

							console.log('Início: '+data.dataI)
							console.log('Fim: '+data.dataF)
						},
						eventDrop: function(event, jsEvent, ui, view) { // ao arrastar e soltar
							let id = event.id?event.id:event._id
							let localId = event.localId
							let title = event.title
							let dtI=event.start?event.start._i:''
							let dtF=event.end?event.end._i:''

							let data = formatDate(dtI, dtF)

							$.ajax({
								type: 'POST',
								url: 'filtraProfissionalAgenda.php',
								dataType: 'json',
								data:{
									'tipoRequest': 'SETAGENDA',
									'id':id,
									'localId':localId,
									'title':title,
									'dataI':data.dataI,
									'dataF':data.dataF,
									'dataF':data.dataF
								},
								success: function(response) {
									console.log(response)
								}
							});
						},
						eventResize: function(event, delta, revertFunc) { // ao redimencionar o horário
							let id = event.id?event.id:event._id
							let localId = event.localId
							let title = event.title
							let dtI=event.start?event.start._i:''
							let dtF=event.end?event.end._i:''

							let data = formatDate(dtI, dtF)

							$.ajax({
								type: 'POST',
								url: 'filtraProfissionalAgenda.php',
								dataType: 'json',
								data:{
									'tipoRequest': 'SETAGENDA',
									'id':id,
									'localId':localId,
									'title':title,
									'dataI':data.dataI,
									'dataF':data.dataF,
									'dataF':data.dataF
								},
								success: function(response) {
									console.log(response)
								}
							});

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
						agenda: $(this).data('agenda'),
						localId: $(this).data('local'),
						stick: true, // maintain when user navigates (see docs on the renderEvent method)
						start: null,
						end: null,
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
										<!-- <div class="fc-event" data-color="#546E7A">Sauna and stuff</div> -->
									</div>

									<div class="">
										
									</div>
								</div>
							</div>

							<div class="col-md-9">
								<div class="fullcalendar-external"></div>
							</div>
						</div>
						<div class="text-left m-2">
							<button id="salvarAgenda" class="btn btn-principal" role="button">Salvar</button>
							<a href="profissional.php" class="btn btn-lg" id="cancelar">Cancelar</a>
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
