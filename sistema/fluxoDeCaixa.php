<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Realizado';

include('global_assets/php/conexao.php');

// try {
// 	$sql = "SELECT *
// 	FROM Cliente
// 		WHERE ClienUnidade = " . $_SESSION['UnidadeId'] . "
// 	ORDER BY ClienNome ASC";
// 	$result = $conn->query($sql);
// 	$row = $result->fetchAll(PDO::FETCH_ASSOC);
// 	//$count = count($row);
// } catch (Exception $e) {
// 	echo ($e);
// }

// $d = date("d");
// $m = date("m");
// $Y = date("Y");

// // $dataInicio = date("Y-m-01"); //30 dias atrás
// $dataInicio = date("Y-m-d");
// $dataFim = date("Y-m-d");
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
			// MUDANCA DO CAMPO DE DATAS
			const buttonDay = document.querySelector('#submitDay');
			const buttonMonth = document.querySelector('#submitMonth');
			const dateInitial = document.querySelector('#inputDataInicio');
			const dateEnd = document.querySelector('#inputDataFim');

			buttonDay.addEventListener('click', (e) => {
				e.preventDefault();
				dateInitial.type = 'date';
				dateEnd.type = 'date';

				buttonDay.style.background = '#607D8B';
				buttonDay.style.color = 'white';

				buttonMonth.style.background = '#CCCCCC';
				buttonMonth.style.color = 'black';
			})

			buttonMonth.addEventListener('click', (e) => {
				e.preventDefault();
				dateInitial.type = 'month';
				dateEnd.type = 'month';

				buttonMonth.style.background = '#607D8B';
				buttonMonth.style.color = 'white';

				buttonDay.style.background = '#CCCCCC';
				buttonDay.style.color = 'black';
			})
		})
	</script>

</head>

<body class="navbar-top">

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
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<form name="formFluxoOperacional" method="post">
									<div class="row">
										<div class="text-left col-lg-2 pt-3">
												<span>Exibição: </span>
												<button id="submitDay" class="btn" style="margin-left: 1rem; background:#607D8B; color:white;">Dia</button>
												<button id="submitMonth" class="btn">Mês</button>
                    </div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="" >
													<!-- value="<?php echo $dataInicio; ?>" -->
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
                                                      print('<option value="' . $item['CnCusId'] . '" selected>' . $item['CnCusNome'] . '</option>');
                                                    }

                                                    ?>
                        </select>
                      </div>
                    </div>

										<div class="col-lg-3">
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
									</div>
								</form>
							</div>


							<div class="row">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" >
											<div class="row">
												<div class="col-lg-2">
												</div>
												<div class="col-lg-2" style='text-align:center; border-top: 2px solid #1B3280; padding-top: 1rem;'>
													<span><strong>2021</strong></span><br/>
													<span><strong>JAN</strong></span>
												</div>
											</div>
										</div>
								</div>
							</div>

							<!-- <div class="row">
								<div class="col-lg-12">
									 Basic responsive configuration
										<div class="card-body" >
											<div class="row">
												<div class="col-lg-2">
												</div>
												<div class="col-lg-2" style='text-align:center; border-top: 2px solid #1B3280; padding-top: 1rem;'>
													<span><strong>2021</strong></span><br/>
													<span><strong>JAN</strong></span>
												</div>
											</div>
										</div>
								</div>
							</div> -->

							
							<div class="row" style="margin-bottom: 1rem;">
								<!-- SALDO INICIAL -->
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" style="padding-top: 0;">
											<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
												<div class="col-lg-2" style="border-right: 1px dotted black;">
													<span><strong>Saldo Inicial</strong></span>
												</div>

												<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
													<div class="row">
														<div class='col-md-6'>
															<span>2000,00</span>
														</div>

														<div class='col-md-6'>
															<span>3000,00</span>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
							<!-- SALDO INICIAL -->

							<!-- ENTRADA -->
							<div class="row">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
									<div class="card-body" style="padding-top: 0; padding-bottom: 0">
										<div class="row" style="background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;">
											<div class="col-lg-2" style="border-right: 1px dotted black;"><strong>ENTRADA</strong></div> 

											<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
												<div class="row">
													<div class='col-md-6'>
														<span><strong>Previsto</strong></span>
													</div>

													<div class='col-md-6'>
														<span><strong>Realizado</strong></span>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="card-body" style="padding-top: 0;">
										<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
											<div class="col-lg-2" style="border-right: 1px dotted black;">
												<span>Lista com os Centros de Custo</span>
											</div>

											<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
												<div class="row">
													<div class='col-md-6'>
														<span>2000,00</span>
													</div>

													<div class='col-md-6'>
														<span>3000,00</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							

							<!-- TOTAL ENTRADA -->
							<div class="row">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" style="padding-top: 0;">
											<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
												<div class="col-lg-2" style="border-right: 1px dotted black;">
												<span><strong>TOTAL</strong></span>
												</div>

												<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
													<div class="row">
														<div class='col-md-6'>
															<span>2000,00</span>
														</div>

														<div class='col-md-6'>
															<span>3000,00</span>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
							<!-- TOTAL ENTRADA -->
							<!-- ENTRADA -->

							<!-- SAIDA -->
							<div class="row" style="margin-top: 1rem;">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
									<div class="card-body" style="padding-top: 0; padding-bottom: 0">
										<div class="row" style="background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;">
											<div class="col-lg-2" style="border-right: 1px dotted black;"><strong>SAÍDA</strong></div> 

											<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
												<div class="row">
													<div class='col-md-6'>
														<span><strong>Previsto</strong></span>
													</div>

													<div class='col-md-6'>
														<span><strong>Realizado</strong></span>
													</div>
												</div>
											</div>
										</div>
									</div>

										<div class="card-body" style="padding-top: 0;">
											<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
												<div class="col-lg-2" style="border-right: 1px dotted black;">
													<span>Lista com os Centros de Custo</span>
												</div>

												<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
													<div class="row">
														<div class='col-md-6'>
															<span>2000,00</span>
														</div>

														<div class='col-md-6'>
															<span>3000,00</span>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
							
							<!-- TOTAL SAIDA -->
							<div class="row" >
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" style="padding-top: 0;">
											<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
												<div class="col-lg-2" style="border-right: 1px dotted black;">
													<span><strong>TOTAL</strong></span>
												</div>

												<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
													<div class="row">
														<div class='col-md-6'>
															<span>2000,00</span>
														</div>

														<div class='col-md-6'>
															<span>3000,00</span>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
							<!-- TOTAL SAIDA -->
							<!-- SAIDA -->

							<!-- SALDO FINAL -->
							<div class="row" style="margin-top: 1rem;">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" style="padding-top: 0;">
											<div class="row" style="background: #CCCCCC; line-height: 3rem; box-sizing:border-box">
												<div class="col-lg-2" style="border-right: 1px dotted black;">
												<span><strong>SALDO FINAL</strong></span>
												</div>

												<div class="dataOpeningBalance col-lg-2" style="border-right: 1px dotted black; text-align:center;">
													<div class="row">
														<div class='col-md-6'>
															<span>2000,00</span>
														</div>

														<div class='col-md-6'>
															<span>3000,00</span>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
							<!-- SALDO FINAL -->

							<!-- SALDO FINAL -->
							<div class="row" style="margin-top: 2rem;">
								<div class="col-lg-12">
									<!-- Basic responsive configuration -->
										<div class="card-body" style="padding-top: 0;">
											<div class="row col-lg-12" style="background: #607D8B; color:white; line-height: 3rem; box-sizing:border-box">
												<span><strong>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA): </strong></span>
											</div>

											<div class="row col-lg-12" style="background: #fff; line-height: 3rem; box-sizing:border-box">
												<span>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</span>
											</div>
										</div>
								</div>
							</div>
							
							<!-- SALDO FINAL -->

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
