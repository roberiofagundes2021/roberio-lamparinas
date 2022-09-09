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


//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtClaNome, AtendDataRegistro, AtModNome, ClienId, ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,
               ClienNomeMae, ClienCartaoSus, ClienCelular, ClResNome, AtClaChave
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		JOIN Situacao ON SituaId = AtendSituacao
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
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	
	<script type="text/javascript">

		$(document).ready(function() {	

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
					orderable: true,   //Data do Registro
					width: "15%",
					targets: [0]
				},
				{ 
					orderable: true,   //Serviço
					width: "15%",
					targets: [1]
				},
				{ 
					orderable: true,   //Profissional
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
					width: "15%",
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
		

			function modalDescontos() {

				$('#descontos').on('click', (e) => {
					e.preventDefault()
					$('#pageModalDescontos').fadeIn(200);
					$('.cardDes').css('width', '500px').css('margin', '0px auto')
				})

				$('#modalCloseDescontos').on('click', function() {
					$('#pageModalDescontos').fadeOut(200);
					$('body').css('overflow', 'scroll');

					limparDescontos()
				})

				$("#salvarDescontos").on('click', function() {
					$('#pageModalDescontos').fadeOut(200);
					$('body').css('overflow', 'scroll');
				})
			}
			modalDescontos()

			divTotal = `<div class="row">
                            <div class="col-lg-8">
								<button class="btn btn-lg btn-principal" id="fecharConta">Fechar Conta</button>
								<a href="atendimento.php" class="btn btn-basic" role="button">Voltar</a>
							</div>
							<div class="col-lg-4">	
								<div style='font-weight: bold;'>Desconto: </div> <br> 
								<div style='font-weight: bold;'>TOTAL A PAGAR: </div>
							</div>
						</div> <br>`
        	$('.datatable-footer').append(divTotal);

		}); //document.ready
		
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
								<form name="formTabelaGastos" id="formTabelaGastos" method="post" class="form-validate-jquery">

									<div class="row">
										<div class="col-lg-3">
											<label for="cmbProcedimentos">Procedimentos<span class="text-danger"> *</span></label>
											<select id="cmbProcedimentos" name="cmbProcedimentos" class="form-control select-search" required>
												<option value="">Selecione</option>
												<?php 
													$sql = "SELECT SrVenId, SrVenNome
															FROM ServicoVenda
															JOIN Situacao on SituaId = SrVenStatus
															WHERE SituaChave = 'ATIVO' and SrVenUnidade = ".$_SESSION['UnidadeId']."
															ORDER BY SrVenNome ASC";
													$result = $conn->query($sql);
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowCategoria as $item){
														print('<option value="'.$item['SrVenId'].'">'.$item['SrVenNome'].'</option>');
													}

													foreach ($rowUnidade as $item) {
														print('<option value="' . $item['UnidaId'] . '">' . $item['UnidaNome'] . '</option>');
													}
												

												?>
											</select>
										</div>
										<div class="col-lg-2">
											<label for="cmbProfissional">Profissional<span class="text-danger"> *</span></label>
											<select id="cmbProfissional" name="cmbProfissional" class="form-control select-search" required>
												<option value="">Selecione</option>
												<?php 
													$sql = "SELECT ProfiId, ProfiNome
															FROM Profissional
															JOIN Situacao on SituaId = ProfiStatus
															WHERE SituaChave = 'ATIVO' and ProfiUnidade = ".$_SESSION['UnidadeId']."
															ORDER BY ProfiNome ASC";
													$result = $conn->query($sql);
													$rowProfissional = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowProfissional as $item){
														
														print('<option value="'.$item['ProfiId'].'">'.$item['ProfiNome'].'</option>');
													}
												

												?>
											</select>
										</div>	
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataAtendimento">Data do Atendimento <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputData" name="inputData" class="form-control" value="<?php echo $row['AtendDataRegistro']; ?>" >
												</div>
											</div>
										</div>
										<div class="col-lg-1">
											<div class="form-group">
												<label for="inputHorario">Horário <span class="text-danger">*</span></label>
												<div class="input-group">
													<input type="time" id="inputHorario" name="inputHorario" class="form-control" >
												</div>
											</div>
										</div>	
										<div class="col-lg-2">
											<label for="cmbLocalAtendimento">Local do Atendimento<span class="text-danger"> *</span></label>
											<select id="cmbLocalAtendimento" name="cmbLocalAtendimento" class="form-control select-search" required>
												<option value="">Selecione</option>
												<?php 
													$sql = "SELECT AtLocId, AtLocNome
															FROM AtendimentoLocal
															JOIN Situacao on SituaId = AtLocStatus
															WHERE SituaChave = 'ATIVO' and AtLocUnidade = ".$_SESSION['UnidadeId']."
															ORDER BY AtLocNome ASC";
													$result = $conn->query($sql);
													$rowAtendimentoLocal = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowAtendimentoLocal as $item){
														print('<option value="'.$item['AtLocId'].'">'.$item['AtLocNome'].'</option>');
													}
												

												?>
											</select>
										</div>				
										<div class="col-lg-2">
											<div class="form-group" style="padding-top:25px;">
												<?php

													//editando
													if (isset($_POST['inputSubCategoriaId'])){
														print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
														print('<a href="subCategoria.php" class="btn btn-basic" role="button">Cancelar</a>');
													} else{ //inserindo
														print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
													}

												?>
											</div>
										</div>
									</div>

									<!--Modal Desconto-->
									<div id="pageModalDescontos" class="custon-modal">
										<div class="custon-modal-container">
											<div class="card cardDes custon-modal-content">
												<div class="custon-modal-title">
													<i class=""></i>
													<p class="h3">Descontos</p>
													<i class=""></i>
												</div>
												
												<div class="p-5">
													<div class="d-flex flex-row justify-content-between">
														<div class="col-lg-12" style="text-align:center;">
															<div class="form-group">
																<label for="inputDesconto">Valor</label>
																<input id="inputDesconto" maxLength="12" class="form-control" type="text" name="inputDesconto">
															</div>
														</div>
													</div>
												</div>

												<div class="card-footer mt-2 d-flex flex-column">
													<div class="row" style="margin-top: 10px;">
														<div class="col-lg-12">
															<div class="form-group">
																<a class="btn btn-lg btn-principal" id="salvarDescontos">Ok</a>
																<a id="modalCloseDescontos" class="btn btn-basic" role="button">Cancelar</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<table class="table" id="tblTabelaGastos">
										<thead>
											<tr class="bg-slate">
												<th>Data do Registro</th>
												<th>Serviço</th>
												<th>Profissional</th>
												<th>Data do Atendimento</th>
												<th>Local do Atendimento</th>
												<th>Valor</th>
												<th class="text-center">Ações</th>
											</tr>
										</thead>
										<tbody>
											<?php	
												foreach ($rowTGasto as $item){	
													
													print( '
													<tr>													
														<td>'.mostraData($item['AtTGaDataRegistro']).'</td>
														<td>'.$item['SrVenNome'].'</td>
														<td>'.$item['ProfiNome'].'</td>
														<td>'.mostraData($item['AtendDataRegistro']).'</td>
														<td>'.$item['AtTGaAtendimentoLocal'].'</td>
														<td>'.mostraValor($item['AtTGaValor']).'</td>');														
														
														print('<td class="text-center">
															<div class="list-icons">
																<div class="list-icons list-icons-extended">
																	<a id="descontos" href="" class="list-icons-item"><i class="icon-coins" data-popup="tooltip" data-placement="bottom" title="Desconto"></i></a>
																	<a href="#" onclick="atualizaTabelaGastos( 1 ,'.$item['AtendId'].', \''.$item['AtClaNome'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
																</div>

															</div>
														</td>
													</tr>');

												}	
											?>
										</tbody>
									</table>
								</div>		
							</div>
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
