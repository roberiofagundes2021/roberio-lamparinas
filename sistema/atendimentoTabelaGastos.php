<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Tabela de Gastos';

include('global_assets/php/conexao.php');

$iAtendimentoId = 9; //isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';

$_SESSION['atendimentoTabelaServicos'] = [];

//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtClaNome, AtendDataRegistro, AtModNome, ClienId,
		ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,ClienNomeMae, ClienCartaoSus, ClienCelular,
		ClResNome, AtClaChave
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		LEFT JOIN Situacao ON SituaId = AtendSituacao
	    WHERE AtendId = $iAtendimentoId and AtendUnidade = ".$_SESSION['UnidadeId']."
		ORDER BY AtendDataRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoId = $row['AtendId'];
$iClienteId = $row['ClienId'];

$sql = "SELECT AtTGaId, AtTGaAtendimento, AtTGaDataRegistro, AtTGaServico, AtTGaProfissional, AtTGaHorario, AtTGaAtendimentoLocal, 
               AtTGaValor, AtTGaDesconto, AtTGaDesconto, AtendCliente, AtendDataRegistro, SrVenNome, ProfiNome, AtLocNome
		FROM AtendimentoTabelaGasto
		JOIN Atendimento ON AtendId = AtTGaAtendimento
		JOIN Cliente ON ClienId = AtendCliente
		JOIN ServicoVenda ON SrVenId = AtTGaServico
		JOIN Profissional ON ProfiId = AtTGaProfissional
		JOIN AtendimentoLocal ON AtLocId = AtTGaAtendimentoLocal
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE AtendCliente = $iClienteId and AtTGaUnidade = ".$_SESSION['UnidadeId']."
		ORDER BY AtTGaDataRegistro ASC";
$result = $conn->query($sql);
$rowTGasto = $result->fetchAll(PDO::FETCH_ASSOC);


$iAtendimentoHistoricoId = $row['AtendId'];

//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Femenino';
} else{
    $sexo = 'Masculino';
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Tabela de Gastos</title>

	<?php include_once("head.php"); ?>
	
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/lamparinas/layout.min.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/lamparinas/components.min.css" rel="stylesheet" type="text/css">

	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/plugins/ui/ripple.min.js"></script>

	<script src="global_assets/js/plugins/forms/wizards/steps.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
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
	
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
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
		echo '<script>
				var atendimento = '.json_encode($row).';
			</script>';
	?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			getCmbs()
			checkServicos()
			setDataProfissional()
			setHoraProfissional()

            /* Início: Tabela Personalizada */
			$('#tblTabelaGastos').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
				searching: false,
				ordering: true, 
				paging: false,
			    columnDefs: [
				{ 
					orderable: true,   //Serviço
					width: "15%",
					targets: [0]
				},
				{ 
					orderable: true,   //Profissional
					width: "20%",
					targets: [1]
				},
				{ 
					orderable: true,   //Horado Atendimento
					width: "15%",
					targets: [2]
				},				
				{ 
					orderable: true,   //Data do Atendimento
					width: "15%",
					targets: [3]
				},
				{ 
					orderable: true,   //Local do Atendimento
					width: "15%",
					targets: [4]
				},
				{ 
					orderable: true,   //Valor
					width: "10%",
					targets: [5]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
					targets: [6]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer">',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
                
			});

			divTotal = `<div class="row">
                            <div class="col-lg-8">
								<button class="btn btn-lg btn-principal" id="fecharConta">Fechar Conta</button>
								<a href="atendimento.php" class="btn btn-basic" role="button">Voltar</a>
							</div>
							<div id="tabelaValores" class="col-lg-4">	
								<div style='font-weight: bold;'>Desconto: </div> <br> 
								<div style='font-weight: bold;'>TOTAL A PAGAR: </div>
							</div>
						</div> <br>`
        	$('.datatable-footer').append(divTotal);

			$('#inserirServico').on('click',function(e){
				$('#formTabelaGastos').submit()
			})

			$('#modal-close-x').on('click', function(e){
				e.preventDefault()
				$('#pageModalDescontos').fadeOut(200)
			})

			$('#formTabelaGastos').submit(function(e){
				e.preventDefault()
				let menssageError = ''
				let procedimentos  = $('#procedimentos').val()
				let profissional  = $('#profissional').val()
				let dataAtendimento  = $('#dataAtendimento').val()
				let horaAtendimento  = $('#horaAtendimento').val()
				let localAtendimento  = $('#localAtendimento').val()

				switch(menssageError){
					case procedimentos: menssageError = 'informe o procedimento'; $('#procedimentos').focus();break;
					case profissional: menssageError = 'informe o profissional'; $('#profissional').focus();break;
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
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADICIONARSERVICO',
						'servico': procedimentos,
						'medicos': profissional,
						'dataAtendimento': dataAtendimento,
						'horaAtendimento': horaAtendimento,
						'localAtendimento': localAtendimento
					},
					success: function(response) {
						if(response.status == 'success'){
							getCmbs()
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

			$('#profissional').on('change', function(){
				let iMedico = $(this).val()

				if(!iMedico){
					setHoraProfissional()
					setDataProfissional()
					return
				}
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
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

			$('#fecharConta').on('click', function(e){
				e.preventDefault();
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'FECHARCONTA',
						'atendimento': atendimento.AtendId,
						'cliente': atendimento.ClienId,
					},
					success: function(response) {
						if(response.status == 'success'){
							getCmbs()
							checkServicos()
							window.location.href='atendimento.php'
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#setDesconto').on('click', function(e){
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SETDESCONTO',
						'id':$('#itemId').val(),
						'desconto':$('#inputDesconto').val(),
					},
					success: async function(response) {
						checkServicos()
						$('#pageModalDescontos').fadeOut(200)
						alerta(response.titulo, response.menssagem, response.status)
					}
				});
			})

			$('#inputDesconto').on('input', function(e){
				let novoValor = parseFloat($('#itemModalValue').val()) - ($('#inputDesconto').val()?parseFloat($('#inputDesconto').val()):0)
				$('#inputModalValorF').val(`R$ ${float2moeda(novoValor)}`)
			})

		}); //document.ready

		function getCmbs(){
			// vai preencher PROCEDIMENTOS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'PROCEDIMENTOS'
				},
				success: function(response) {
					$('#procedimentos').empty();
					$('#procedimentos').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = ''
						// caso exista algo na variável atendimento significa que o usuário esta alterando um valor
						// logo esses valores deveram vir preenchido com os dados desse atendimento
						if(atendimento){
							 opt = atendimento.AtendModalidade == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						} else {
							opt = `<option value="${item.id}">${item.nome}</option>`
						}
						$('#procedimentos').append(opt)
					})
				}
			});
			// vai preencher PROFISSIONAL
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'PROFISSIONAL'
				},
				success: function(response) {
					$('#profissional').empty();
					$('#profissional').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#profissional').append(opt)
					})
				}
			});
			// vai preencher LOCAIS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'LOCAIS'
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

		function checkServicos(){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'CHECKSERVICO',
					'iAtendimento': atendimento?atendimento['AtendId']:''
				},
				success: async function(response) {
					statusServicos = response.array.length?true:false;
					if(statusServicos){
						$('#dataServico').html('');

						let HTML = ''
						response.array.forEach(item => {
							if(item.status != 'rem'){
								let exc = `<a class='list-icons-item removeItem' style='color: black; cursor:pointer' data-id="${item.id}"><i class='icon-bin' title='Excluir Atendimento'></i></a>`;
								let popup = `<a class='list-icons-item openPopUp' style="color:${(item.desconto?'#50b900':'#000')}; cursor:pointer" data-value="${float2moeda(item.valor)}" data-titulo="${item.servico}" data-id="${item.id}"><i class='icon-cash' title='Desconto'></i></a>`;
								
								let acoes = `<div class='list-icons'>
											${popup}
											${exc}
										</div>`;
								HTML += `
								<tr class='servicoItem'>
									<td class="text-center">${item.servico}</td>
									<td class="text-center">${item.medico}</td>
									<td class="text-center">${item.hora}</td>
									<td class="text-center">${item.sData}</td>
									<td class="text-center">${item.local}</td>
									<td class="text-right">R$ ${float2moeda(item.valor)}</td>
									<td class="text-center">${acoes}</td>
								</tr>`
							}
						})
						$('#tabelaValores').html(`
							<div style='font-weight: bold;'>Desconto: R$${float2moeda(response.desconto)}</div> <br> 
							<div style='font-weight: bold;'>TOTAL A PAGAR: R$${float2moeda(response.valorTotal)}</div>
						`)
						$('#dataServico').html(HTML).show();
						$('#servicoTable').show();

						$('.openPopUp').each(function(index,element){
							$(element).on('click', function(e){
								e.preventDefault()
								$('#itemId').val($(this).data('id'))
								$('#inputModalValorB').val(`R$ ${$(this).data('value')}`)
								$('#itemModalValue').val($(this).data('value'))
								$('#tituloModal').html(`Descontos do serviço <strong>${$(this).data('titulo')}</strong>`)
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'GETDESCONTO',
										'id': $(this).data('id')
									},
									success: async function(response) {
										if(response.status == 'success'){
											let desconto = response.desconto?response.desconto:0;
											$('#inputDesconto').val(desconto)
											$('#pageModalDescontos').fadeIn(200)
										}else{
											alerta(response.titulo, response.menssagem, response.status)
										}
									}
								});
							})
						})

						$('.removeItem').each(function(index,element){
							$(element).on('click', function(e){
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'EXCLUISERVICO',
										'id': $(this).data('id')
									},
									success: function(response) {
										alerta(response.titulo, response.menssagem, response.status)
										checkServicos()
									}
								});
							})
						})
					}else{
						$('#servicoTable').hide();
					}
				}
			});
		}

		function setDataProfissional(arrayData){
			$('#dataAgenda').html('')
			$('#dataAgenda').html('<input id="dataAtendimento" name="dataAtendimento" type="text" class="form-control pickadate">')

			let array = arrayData?arrayData:undefined
			// Events
			$('#dataAtendimento').pickadate({
				weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
				monthsFull: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
				monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
				today: '',
				close: '',
				clear: 'Limpar',
				labelMonthNext: 'Próximo',
				labelMonthPrev: 'Anterior',
				labelMonthSelect: 'Escolha um mês na lista suspensa',
				labelYearSelect: 'Escolha um ano na lista suspensa',
				selectMonths: false,
				selectYears: false,
				showMonthsShort: true,
				closeOnSelect: true,
				closeOnClear: true,
				formatSubmit: 'yyyy/mm/dd',
				format: 'dd/mm/yyyy',
				disable: array,
				onStart: function() {
					// console.log('onStart event')
				},
				onRender: function() {
					$('.picker__day').each(function(){
						let hasClass = !$(this).hasClass('picker__day--disabled') // verifica se NÃO está desabilitado...
						let hasSelected = $(this).hasClass('picker__day--selected') // verifica se está selecionado...

						if(hasClass){
							$(this).addClass((hasSelected?
							'':
							'font-weight-bold text-black border'))
						}
					})
				},
				onOpen: function() {
					$('.picker__day').each(function(){
						let hasClass = !$(this).hasClass('picker__day--disabled') // verifica se NÃO está desabilitado...
						let hasSelected = $(this).hasClass('picker__day--selected') // verifica se está selecionado...

						if(hasClass){
							$(this).addClass((hasSelected?
							'':
							'font-weight-bold text-black border'))
						}
					})
				},
				onClose: function() {
					// console.log('onClose event')
				},
				onStop: function() {
					// console.log('onStop event')
				},
				onSet: function(context) {
					let data = new Date(context.select).toLocaleString("pt-BR", {timeZone: "America/Bahia"});
					data = data.split(' ')[0]; // Formatando a string padrão: "dd/mm/yyyy HH:MM:SS" => "dd/mm/yyyy"
					let iMedico = $('#profissional').val();

					$.ajax({
						type: 'POST',
						url: 'filtraAtendimentoTabelaGastos.php',
						dataType: 'json',
						data:{
							'tipoRequest': 'SETHORAPROFISSIONAL',
							'data': data,
							'iMedico': iMedico
						},
						success: function(response) {
							if(response.status == 'success'){
								setHoraProfissional(response.arrayHora, response.intervalo)
								$('#horaAtendimento').focus()
							} else {
								alerta(response.titulo, response.menssagem, response.status)
							}
						}
					});
				}
			});
		}

		function setHoraProfissional(array,interv){
			$('#modalHora').html('').show();
			$('#modalHora').html('<input id="horaAtendimento" name="horaAtendimento" type="text" class="form-control pickatime-disabled">');

			let arrayTime = array?array:undefined
			let intervalo = interv?interv:30
			// doc: https://amsul.ca/pickadate.js/time/
			$('#horaAtendimento').pickatime({
				// Regras
				interval: intervalo,
				disable: arrayTime,
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
		
		<?php
			include_once("menu-left.php");
			include_once("menuLeftSecundarioVenda.php");
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					
					<div class="col-lg-12">
						<form id='dadosPost'>
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
						</form>
						<!-- Basic responsive configuration -->
						
						<?php
							echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
						?>
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title"><b>TABELA DE GASTOS</b></h3>
							</div>
						</div>

						<div class="card card-collapsed">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Dados do Paciente</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Prontuário Eletrônico: <?php echo $row['ClienCodigo']; ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Nº do Registro: <?php echo $row['AtendNumRegistro']; ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Modalidade: <?php echo $row['AtModNome'] ; ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>CNS: <?php echo $row['ClienCartaoSus']; ?></label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6">
										<h4><b><?php echo strtoupper($row['ClienNome']); ?></b></h4>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Sexo: <?php echo $sexo ; ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Telefone: <?php echo $row['ClienCelular']; ?></label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group">
											<label>Data Nascimento: <?php echo mostraData($row['ClienDtNascimento']); ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Idade: <?php echo calculaIdade($row['ClienDtNascimento']); ?></label> 
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Mãe: <?php echo $row['ClienNomeMae'] ; ?></label>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label>Responsável: <?php echo $row['ClResNome']; ?></label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Procedimentos</h3>
								</div>
							<div class="card-body">
								<form id="formTabelaGastos" name="formTabelaGastos" method="post" class="form-validate-jquery">
									<div class="col-lg-12 mb-2 row">
										<!-- titulos -->
										<div class="col-lg-2">
											<label>Procedimentos <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-3">
											<label>Profissional <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-3">
											<label>Data <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-1">
											<label>Horário <span class="text-danger">*</span></label>
										</div>
										<div class="col-lg-2">
											<label>Local do Atendimento <span class="text-danger">*</span></label>
										</div>

										<!-- campos -->
										<div class="col-lg-2">
											<select id="procedimentos" name="procedimentos" class="select-search" required>
												<!--  -->
											</select>
										</div>
										<div class="col-lg-3">
											<select id="profissional" name="profissional" class="select-search" required>
												<!--  -->
											</select>
										</div>
										<div id="dataAgenda" class="col-lg-3">
											<input id="dataAtendimento" name="dataAtendimento" type="text" class="form-control pickadate" required>
										</div>
										<div id="modalHora" class="col-lg-1">										
											<input id="horaAtendimento" name="horaAtendimento" type="text" class="form-control pickatime-disabled" required>
										</div>
										<div class="col-lg-2">
											<select id="localAtendimento" name="localAtendimento" class="form-control form-control-select2" required>
												<!--  -->
											</select>
										</div>
										<div class="col-lg-1" style="margin-top: -5px;">
											<a id="inserirServico" class="btn btn-lg btn-principal">Incluir</a>
										</div>
									</div>
								</form>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<table class="table" id="tblTabelaGastos">
										<thead>
											<tr class="bg-slate">
												<th>Serviço</th>
												<th>Profissional</th>
												<th>Hora</th>
												<th>Data</th>
												<th>Local</th>
												<th>Valor</th>
												<th class="text-center">Ações</th>
											</tr>
										</thead>
										<tbody id="dataServico">
										</tbody>
									</table>
								</div>		
							</div>
						</div>
							<!-- /basic responsive configuration -->

							<!--Modal Desconto-->
							<div id="pageModalDescontos" class="custon-modal">
								<div class="custon-modal-container" style="max-width: 500px;">
									<div class="card custon-modal-content">
										<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
											<p id='tituloModal' class="h5">Desconto</p>
											<i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
										</div>
										<div class="px-0">
											<div class="d-flex flex-row">
												<div class="col-lg-12">
													<form id="editaSituacao" name="alterarSituacao" method="POST" class="form-validate-jquery">
														<div class="form-group">
															<div class="custon-modal-title">
																<i class=""></i>
																<p class="h3">Descontos</p>
																<i class=""></i>
															</div>
															
															<div class="p-5">
																<div class="d-flex flex-row justify-content-between">
																	<div class="col-lg-12" style="text-align:center;">
																		<div class="form-group row">
																			<div class="col-lg-4">
																				<label>Desconto</label>
																			</div>
																			<div class="col-lg-4">
																				<label>Valor</label>
																			</div>
																			<div class="col-lg-4">
																				<label>Valor Final</label>
																			</div>

																			<div class="col-lg-4">
																				<input id="inputDesconto" maxLength="12" class="form-control" type="number" name="inputDesconto">
																			</div>
																			<div class="col-lg-4">
																				<input id="inputModalValorB" maxLength="12" class="form-control" type="text" readonly>
																			</div>
																			<div class="col-lg-4">
																				<input id="inputModalValorF" maxLength="12" class="form-control" type="text" readonly>
																			</div>

																			<input id="itemId" name="itemId" type="hidden" value=''>
																			<input id="itemModalValue" name="itemId" type="hidden" value=''>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</form>
												</div>
											</div>
											<div class="text-right m-2">
												<button id="setDesconto" class="btn btn-principal" role="button">Confirmar</button>
											</div>
										</div>
									</div>
								</div>
							</div>
					</div>
				</div>
				<!-- /info blocks -->
			</div>
			<!-- /content area -->
			<?php include_once("footer.php"); ?>
		</div>
		<!-- /main content -->
	</div>
	<!-- /page content -->
	<?php include_once("alerta.php"); ?>

</body>

</html>
