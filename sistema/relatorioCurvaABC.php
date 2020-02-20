<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Fluxo Realizado';

include('global_assets/php/conexao.php');


if(isset($_POST['inputDataInicio'])) {

	$dataInicio = $_POST['inputDataInicio'];
	$dataFim = $_POST['inputDataFim'];
	$iUnidade = isset($_POST['cmbUnidade']) ? $_POST['cmbUnidade'] : 'NULL';
	$iSetor = isset($_POST['cmbSetor']) ? $_POST['cmbSetor'] : 'Teste';
	$iCategoria = isset($_POST['cmbCategoria']) ? $_POST['cmbCategoria'] : NULL;
	$iSubCategoria = isset($_POST['cmbSubCategoria']) ? $_POST['cmbSubCategoria'] : NULL;
	$iClassificacao = isset($_POST['cmbClassificacao']) ? $_POST['cmbClassificacao'] : NULL;
									
	$sql = "SELECT ProduId, ProduNome, MvXPrValorUnitario, dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, ". $iSetor .", $iCategoria, $iSubCategoria, $iClassificacao, $dataInicio, $dataFim) as Saidas,
				   (MvXPrValorUnitario * dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, $iSetor, $iCategoria, $iSubCategoria, $iClassificacao, $dataInicio, $dataFim)) as ValorTotal
			FROM Produto
			JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
			JOIN Movimentacao on MovimId = MvXPrMovimentacao
			JOIN Situacao on SituaId = MovimSituacao
			WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and MovimTipo = 'S' and SituaChave = 'FINALIZADO' and MovimData between ".$dataInicio." and ".$dataFim;

	if ($iUnidade){
		
		if($iSetor){
			$sql .= " and MovimDestinoSetor = ".$iSetor;
		} else {
			$sql .= " and MovimDestinoLocal = ".$iSetor; //Só que pra isso a combo Setor deveria vir Setores e Locais de Estoque. Será assim mesmo ou é pra vir só Setor?
		}
	}

	if ($iCategoria){
		$sql .= " and ProduCategoria = ".$iCategoria;
	}

	if ($iSubCategoria){
		$sql .= " and ProduSubCategoria = ".$iSubCategoria;
	}

	if ($iClassificacao){
		$sql .= " and MvXPrClassificacao = ".$iClassificacao;
	}

	echo $sql;die;
	
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	
	$cont = 0;
	
	$esconder = ' style="display:block;" ';
} else {
	$esconder = ' style="display:none;" ';
}


$timestamp = strtotime("-30 days");

$dataInicio = date('Y-m-d', $timestamp);
$dataFim = date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Curva ABC</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files --> 
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>

	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	<!-- CV Documentacao: https://jqueryvalidation.org/ -->	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {			
		
			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				FiltraSubCategoria();
				
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});				
			});

			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbUnidade').on('change', function(e){
				
				FiltraSetor();
				
				var cmbUnidade = $('#cmbUnidade').val();

				$.getJSON('filtraSetor.php?idUnidade='+cmbUnidade, function (dados){
					
					var option = '<option value="#">Selecione o Setor</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SetorId+'">'+obj.SetorNome+'</option>';
						});						
						
						$('#cmbSetor').html(option).show();
					} else {
						ResetSetor();
					}					
				});				
			});	

		});

		//Mostra o "Filtrando..." na combo SubCategoria
		function FiltraSubCategoria(){
			$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
		}			

		//Mostra o "Filtrando..." na combo Setor
		function FiltraSetor(){
			$('#cmbSetor').empty().append('<option>Filtrando...</option>');
		}

		function ResetSubCategoria(){
			$('#cmbSubCategoria').empty().append('<option>Sem SubCategoria</option>');
		}

		function ResetSetor(){
			$('#cmbSetor').empty().append('<option>Sem Setor</option>');
		}		

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
								<h3 class="card-title">Relatório Curva ABC</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="fluxo.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<form name="formCurvaABC" method="post">

									<div class="row">

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputDataInicio">Data Início <span class="text-danger">*</span></label>
												<div class="input-group">
													<span class="input-group-prepend">
														<span class="input-group-text"><i class="icon-calendar22"></i></span>
													</span>
													<input type="date" id="inputDataInicio" name="inputDataInicio" class="form-control" placeholder="Data Início" value="<?php echo $dataInicio; ?>" required>
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
													<input type="date" id="inputDataFim" name="inputDataFim" class="form-control" placeholder="Data Fim" value="<?php echo $dataFim; ?>" required>
												</div>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbUnidade">Unidade</label>
												<select id="cmbUnidade" name="cmbUnidade" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php 
														$sql = "SELECT UnidaId, UnidaNome
																FROM Unidade
																JOIN Situacao on SituaId = UnidaStatus
																WHERE UnidaEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'ATIVO'
																ORDER BY UnidaNome ASC";
														$result = $conn->query($sql);
														$rowUnidade = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowUnidade as $item){
															//$seleciona = $item['UnidaId'] == $row['FlOpeFornecedor'] ? "selected" : "";
															print('<option value="'.$item['UnidaId'].'">'.$item['UnidaNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbSetor">Setor</label>
												<select id="cmbSetor" name="cmbSetor" class="form-control form-control-select2">
													<option value="">Selecione</option>													
												</select>
											</div>
										</div>										
									</div>
									
									<div class="row">

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="">Selecione</option>
													<?php 
														$sql = "SELECT CategId, CategNome
																FROM Categoria
																JOIN Situacao on SituaId = CategStatus
																WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'ATIVO'
																ORDER BY CategNome ASC";
														$result = $conn->query($sql);
														$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowCategoria as $item){			
															//$seleciona = $item['CategId'] == $row['FlOpeCategoria'] ? "selected" : "";
															print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
														}
													
													?>											
												</select>
											</div>
										</div>
										
										<div class="col-lg-4">
											<label for="cmbSubCategoria">SubCategoria</label>
											<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
												<option value="">Selecione</option>
												<?php 
												 
													$sql = "SELECT SbCatId, SbCatNome
															FROM SubCategoria
															JOIN Situacao on SituaId = SbCatStatus
															WHERE SbCatEmpresa = ".$_SESSION['EmpreId']." and SituaChave = 'ATIVO'
															Order By SbCatNome ASC";
													$result = $conn->query($sql);
													$rowSubCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowSubCategoria as $item){			
														//$seleciona = $item['SbCatId'] == $row['FlOpeSubCategoria'] ? "selected" : "";
														print('<option value="'.$item['SbCatId'].'">'.$item['SbCatNome'].'</option>');
													}
												
												?>										
											</select>
										</div>	

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbClassificacao">Classificação</label>
												<select id="cmbClassificacao" name="cmbClassificacao/Bens" class="form-control form-control-select2" >
													<option value="">Selecione</option>
													<?php 
														$sql = "SELECT ClassId, ClassNome
																FROM Classificacao
																JOIN Situacao on SituaId = ClassStatus
																WHERE SituaChave = 'ATIVO'
																ORDER BY ClassNome ASC";
														$result = $conn->query($sql);
														$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowClassificacao as $item){
															print('<option value="'.$item['ClassId'].'">'.$item['ClassNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="text-right">
										<button id="enviar" class="btn btn-success" role="button">Filtrar</button> 
										<button id="imprimir" class="btn btn-secondary btn-icon" disabled>
                                            <i class="icon-printer2"> Imprimir</i>
                                        </button>
									</div>

								</form>
							</div>
						</div>
						<!-- /basic responsive configuration -->
					</div>
				</div>								
				<!-- /info blocks -->

				<!-- Info blocks -->		
				<div class="row" id="resultado" <?php echo $esconder; ?>>
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Resultado da Pesquisa</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="relatorioCurvaABC.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								
								<table class="table" id="tblCurvaABC">
									<thead>
										<tr class="bg-slate">											
											<th width="40%">Produto</th>
											<th width="10%">Valor Unit.</th>
											<th width="10%">Saída</th>
											<th width="10%">Valor Total</th>									
											<th width="10%">Porcentagem</th>										
											<th width="10%">% Acumulada</th>
											<th width="10%" style="background-color: #ccc; color:#333;">Classificação</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$cont = 1;

											foreach ($row as $item){

												print('
												<tr>
													<td>'.$item['ProduNome'].'</td>
													<td>'.mostraValor($item['MvXPrValorUnitario']).'</td>
													<td>'.$item['Saidas'].'</td>
													<td>'.mostraValor($item['ValorTotal']).'</td>
													<td></td>											
													<td></td>
													<td style="background-color: #eee; color:#333;"></td>
												</tr>');

												$cont++;
											}
										?>
									</tbody>
								</table>
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
