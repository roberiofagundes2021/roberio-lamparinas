<?php 

include('global_assets/php/conexao.php');
include_once("sessao.php"); 
$_SESSION['PaginaAtual'] = 'Fluxo Realizado';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Fluxo Realizado</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files --> 
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>

	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>	

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>		
	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('.form-check-label > input[type="checkbox"][value="multiselect-all"]').prop( "checked", true );
		 });


		document.addEventListener('DOMContentLoaded', () => {
			// Atribuição dos campos de filtro da tela
			const buttonDay = document.querySelector('#submitDay');
			const buttonMonth = document.querySelector('#submitMonth');
			const inputDateInitial = document.querySelector('#inputDataInicio');
			const inputDateEnd = document.querySelector('#inputDataFim');
			const submitPesquisar = document.querySelector('#submitPesquisar');
			const cmbCentroDeCustosItens = $(".centroDeCustosClass");
/////////////////////////////////////
			cmbCentroDeCustosItens.each((i, element) => {
				
				if($(element).hasClass('multiselect-item')){
					let check = $(element).children().children().first();
                    let value = $(check).val()
					console.log(value)
					check.click((e) => {
						try {
						    $.post(
						    	url,
						    	request,
						    	(response) => {
						    		if (response) {
						    			
						    		}
						    	}
						    );
					    } catch(err) {
					        console.error('Houve um error: ',err);
					    }
					})
		
				}
			});
////////////////////////////////////////

			inputDateInitial.addEventListener('change', (e) => {
				const monthInitial = (inputDateInitial.value).split('-')[1] ? (inputDateInitial.value).split('-')[1] : "";
				const monthEnd = (inputDateEnd.value).split('-')[1] ? (inputDateEnd.value).split('-')[1] : "";
				const dayInitial = (inputDateInitial.value).split('-')[2] ? (inputDateInitial.value).split('-')[2] : "";
				const dayEnd = (inputDateEnd.value).split('-')[2] ? (inputDateEnd.value).split('-')[2] : "";
				const typeDate = document.querySelector('.btn.active').textContent;
				const yearInitial = (inputDateInitial.value).split('-')[0] ? (inputDateInitial.value).split('-')[0] : "";
				const yearEnd = (inputDateEnd.value).split('-')[0] ? (inputDateEnd.value).split('-')[0] : "";

				if (typeDate === 'Dia') {
					if ((monthEnd !== '' && monthEnd !== null) && (monthInitial !== '' && monthInitial !== null) && (monthEnd !== monthInitial)) {
						alerta('Atenção','A data final está com um mês diferente da data que você está informando. Você só pode pesquisar períodos dentro do mesmo mês!', 'error');
						inputDateInitial.value = "";
					} else if ((dayInitial !== '' && dayInitial !== null) && (dayEnd !== '' && dayEnd !== null) && (dayInitial > dayEnd)) {
						alerta('Atenção','A data inicial tem que ser menor que a data final!', 'error');
						inputDateInitial.value = "";
					}
				}

				if (typeDate === 'Mês') {
					if ((yearInitial !== '' && yearInitial !== null) && (yearEnd !== '' && yearEnd !== null) && (yearInitial != yearEnd)) {
						alerta('Atenção','Informe um período detro do mesmo ano!', 'error');
						inputDateInitial.value = "";
					}
				}
				
			});


			inputDateEnd.addEventListener('change', (e) => {
				const monthInitial = (inputDateInitial.value).split('-')[1] ? (inputDateInitial.value).split('-')[1] : "";
				const monthEnd = (inputDateEnd.value).split('-')[1] ? (inputDateEnd.value).split('-')[1] : "";
				const dayInitial = (inputDateInitial.value).split('-')[2] ? (inputDateInitial.value).split('-')[2] : "";
				const dayEnd = (inputDateEnd.value).split('-')[2] ? (inputDateEnd.value).split('-')[2] : "";
				const typeDate = document.querySelector('.btn.active').textContent;
				const yearInitial = (inputDateInitial.value).split('-')[0] ? (inputDateInitial.value).split('-')[0] : "";
				const yearEnd = (inputDateEnd.value).split('-')[0] ? (inputDateEnd.value).split('-')[0] : "";

				if (typeDate === 'Dia') {
					if ((monthEnd !== '' && monthEnd !== null) && (monthInitial !== '' && monthInitial !== null) && (monthEnd !== monthInitial)) {
						alerta('Atenção','A data inicial está com um mês diferente da data que você está informando. Você só pode pesquisar períodos dentro do mesmo mês!', 'error');
						inputDateEnd.value = "";
					} else if ((dayInitial !== '' && dayInitial !== null) && (dayEnd !== '' && dayEnd !== null) && (dayEnd < dayInitial)) {
						alerta('Atenção','A data final tem que ser maior que a data inicial!', 'error');
						inputDateEnd.value = "";
					}
				}

				if (typeDate === 'Mês') {
					if ((yearInitial !== '' && yearInitial !== null) && (yearEnd !== '' && yearEnd !== null) && (yearInitial != yearEnd)) {
						alerta('Atenção','Informe um período detro do mesmo ano!', 'error');
						inputDateInitial.value = "";
					}
				}
			});


			buttonDay.addEventListener('click', (e) => {
				e.preventDefault();
				inputDateInitial.type = 'date';
				inputDateEnd.type = 'date';

				buttonDay.style.background = '#607D8B';
				buttonDay.style.color = 'white';
				buttonDay.classList.add('active');

				buttonMonth.style.background = '#CCCCCC';
				buttonMonth.style.color = 'black';
				buttonMonth.classList.remove('active');
			})


			buttonMonth.addEventListener('click', (e) => {
				e.preventDefault();
				inputDateInitial.type = 'month';
				inputDateEnd.type = 'month';

				buttonMonth.style.background = '#607D8B';
				buttonMonth.style.color = 'white';
				buttonMonth.classList.add('active');

				buttonDay.style.background = '#CCCCCC';
				buttonDay.style.color = 'black';
				buttonDay.classList.remove('active');
			});


			//cmbCentroDeCustos
			$("#cmbCentroDeCustos").click(e => {
				console.log('teste')
			})


			submitPesquisar.addEventListener('click', (e) => {
				e.preventDefault();
				const cmbCentroDeCustosReq = $('#cmbCentroDeCustos').val();
				const cmbPlanoContasReq = $('#cmbPlanoContas').val();
				const msg = $('<div style="width:100%; text-align:center;"><img src="global_assets/images/lamparinas/loader.gif" style="width: 120px"></div>');

				if (inputDateInitial.value === '' || inputDateInitial.value === null){
					alerta('Atenção','Informe o período inicial!', 'error');
					return false;
				}
				
				if (inputDateEnd.value === '' || inputDateEnd.value === null){
					alerta('Atenção','Informe o período final!', 'error');
					return false;
				}
				
				if (cmbCentroDeCustos === '' || cmbCentroDeCustos === null){
					alerta('Atenção','Selecione pelo menos um Centro de Custo!', 'error');
					return false;
				}
										
				if (cmbPlanoContas === '' || cmbPlanoContas === null){
					alerta('Atenção','Selecione pelo menos um Plano de Contas!', 'error');
					return false;
				}

				const getData = () => {
					let url = "fluxoDeCaixaFiltra.php";
					const typeDate = buttonDay.classList.contains('active') ? 'D' : 'M';
					const dayInitial = (inputDateInitial.value).split('-')[2] ? (inputDateInitial.value).split('-')[2] : "";
					const dayEnd = (inputDateEnd.value).split('-')[2] ? (inputDateEnd.value).split('-')[2] : "";
					const quantityDays = dayEnd - dayInitial;
					const quantityPages = Math.ceil(quantityDays / 4);

					request = {
						quantityPages: quantityPages,
						typeDate: typeDate,
						quantityDays: quantityDays,
						dayInitial: dayInitial,
						dayEnd: dayEnd,
						inputDateInitial: `${inputDateInitial.value}-01`,
						inputDateEnd: `${inputDateEnd.value}-01`,
						cmbCentroDeCustos: cmbCentroDeCustosReq,
						cmbPlanoContas: cmbPlanoContasReq,
					};
					// console.log(request)
					/* Adicionando os dados do filtro para o formulário que será enviado para o exportar */

					$("#quantityPages").val(quantityPages)
					$("#typeDate").val(typeDate)
					$("#quantityDays").val(quantityDays)
					$("#dayInitial").val(dayInitial)
					$("#dayEnd").val(dayEnd)
					$("#inputDateInitial").val(`${inputDateInitial.value}-01`)
					$("#inputDateEnd").val(`${inputDateEnd.value}-01`)
					$("#inputCentroDeCustos").val(cmbCentroDeCustosReq)
					$("#inputPlanoContas").val(cmbPlanoContasReq)
					// console.log($("#cmbPlanoContas").val())

					const form = $("#formFluxoDeCaixaExportar").children()
					// console.log(form)
					form.each((i, e)=>{
                       console.log($(e).val())
					})

					$('#dataResponse').html(msg);

					try {
						$.post(
							url,
							request,
							(response) => {
								if (response) {
									$('#dataResponse').html('');
									$('#dataResponse').html(response);
								}
							}
						);
					} catch(err) {
					console.error('Houve um error: ',err);
					}
				} 

				getData();

			});

		});

		function exportarTabelaExcel() {
            document.formFluxoDeCaixaExportar.action = "fluxoDeCaixaExportar.php";
			document.formFluxoDeCaixaExportar.submit();
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
								<h3 class="card-title">Fluxo de Caixa</h3>
							</div>

							<div class="card-body">
								<form name="formFluxoOperacional" method="post">
									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="submitDay">Exibição </label>
												<div class="input-group">
													<button id="submitDay" class="btn active" style="background:#607D8B; color:white;">Dia</button>
													<button id="submitMonth" class="btn">Mês</button>
												</div>
											</div>
                   						 </div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="" >
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataFim">Data Fim <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="" >
												</div>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group container-cmbCentroDeCustos" >
												<label for="cmbCentroDeCustos">Centro de Custos</label>
												<select id="cmbCentroDeCustos" name="cmbCentroDeCustos" class="form-control multiselect-select-all" multiple="multiple" data-fouc>
													<?php
														$sql = "SELECT CnCusId,
																	CnCusNome
																FROM CentroCusto
																JOIN Situacao 
																	ON SituaId = CnCusStatus
																WHERE CnCusUnidade = " . $_SESSION['UnidadeId'] . " 
																and SituaChave = 'ATIVO'
															ORDER BY CnCusNome ASC";
														$result = $conn->query($sql);
														$rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);

														foreach ($rowCentroDeCustos as $item) {
															print('<option value="' . $item['CnCusId'] . '" class="centroDeCustosClass" selected>' . $item['CnCusNome'] . '</option>');
														}
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-2">
											<div class="form-group container-cmbPlanoContas">
												<label for="cmbPlanoContas">Plano de Contas</label>
												<select id="cmbPlanoContas" name="cmbPlanoContas" class="form-control multiselect-select-all" multiple="multiple" data-fouc>
													<?php
														$sql = "SELECT PlConId, PlConNome
																FROM PlanoContas
																JOIN Situacao 
																ON SituaId = PlConStatus
																WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " 
																		AND SituaChave = 'ATIVO'
																ORDER BY PlConNome ASC";
														$result = $conn->query($sql);
														$rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

														foreach ($rowPlanoContas as $item) {
															print('<option value="' . $item['PlConId'] . '" selected>' . $item['PlConNome'] . '</option>');
														}

														?>
												</select>
											</div>
										</div>

										<div class="text-left col-lg-1 pt-3">
											<button id="submitPesquisar" class="btn btn-principal" style='margin-left:1rem'><i class="fas fa-search"></i></button>
										</div>
										<div class="text-left col-lg-1 pt-3">
									        <a href="#" onclick="exportarTabelaExcel()" class="btn bg-slate-700 btn-icon" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Exportar Produtos"><i class="icon-drawer-out"></i></a>									
								        </div>
									</div>
								</form>
							</div>
							<!-- <div class="row mb-2">
								<div class="col-lg-11">		
								</div>
								<div class="col-lg-1">
									<a href="#" style="float:left; margin-left: 5px;" onclick="exportarTabelaExcel()" class="btn bg-slate-700 btn-icon" role="button" data-popup="tooltip" data-placement="bottom" data-container="body" title="Exportar Produtos"><i class="icon-drawer-out"></i></a>									
								</div>
							</div> -->

							<div id="dataResponse"></div>

							<form id="formFluxoDeCaixaExportar" name="formFluxoDeCaixaExportar" method="post">
					            <input type="hidden" id="quantityPages" name="quantityPages">
					            <input type="hidden" id="typeDate" name="typeDate">
					            <input type="hidden" id="quantityDays" name="quantityDays">
					            <input type="hidden" id="dayInitial" name="dayInitial">
					            <input type="hidden" id="dayEnd" name="dayEnd">
					            <input type="hidden" id="inputDateInitial" name="inputDateInitial">
					            <input type="hidden" id="inputDateEnd" name="inputDateEnd">
					            <input type="hidden" id="inputCentroDeCustos" name="inputCentroDeCustos">
					            <input type="hidden" id="inputPlanoContas" name="inputPlanoContas">
				            </form>

						<!-- FIM DO CARD -->	
						</div>
						<!-- /basic responsive configuration -->
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
