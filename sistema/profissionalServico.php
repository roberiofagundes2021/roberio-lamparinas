<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Serviço do Profissional';

include('global_assets/php/conexao.php');

if(isset($_POST['inputProfissionalId'])){
	$iProfissional = $_POST['inputProfissionalId'];
} else {
	irpara("profissional.php");
}

//Essa consulta é para preencher a grid
$sql = "SELECT PrXSVServicoVenda, PrXSVRecebimento
		FROM ProfissionalXServicoVenda
	    WHERE PrXSVUnidade = ". $_SESSION['UnidadeId'] ." AND PrXSVProfissional = $iProfissional
		ORDER BY PrXSVServicoVenda ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

//Se estiver editando
if(isset($_POST['inputProfissionaServicoId']) && $_POST['inputProfissionaServicoId']){

	//Essa consulta é para preencher o campo a ser editar
	$sql = "SELECT *
			FROM ProfissionalXServicoVenda
			WHERE PrXSVProfissional = ". $_POST['inputProfissionaServicoId'];
	$result = $conn->query($sql);
	$rowProfissionaServico = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();
} 

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA'){

	try{

		//Edição
		if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA'){
			
			$sql = "UPDATE ProfissionalXServicoVenda SET PrXSVServicoVenda = :sServicoVenda, PrXSVRecebimento = :sRecebimento
					WHERE PrXSVProfissional = :iProfissional";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sServicoVenda' => $_POST['inputServicoVenda'],
							':sRecebimento' => $_POST['inputRecebimento'],
							':iProfissional' => $_POST['inputProfissionaServicoId']
							));
	
			$_SESSION['msg']['mensagem'] = "Serviço do Profissional alterado!!!";
	
		} else { //inclusão
		
			$sql = "INSERT INTO ProfissionalXServicoVenda (PrXSVProfissional, PrXSVServicoVenda,  PrXSVRecebimento,, PrXSVUnidade)
					VALUES (:sServicoVenda,:sRecebimento, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
                            ':iProfissional' => $iProfissional,
							':sServicoVenda' => $_POST['inputServicoVenda'],
							':sRecebimento' => $_POST['inputRecebimento'],
							':iUnidade' => $_SESSION['UnidadeId'],
							));
	
			$_SESSION['msg']['mensagem'] = "Serviço do Profissional incluído!!!";
					
		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com o Serviço do Profissional!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("profissional.php");
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Serviços do Profissional</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
	
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblProfissionaServico').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Serviço
					width: "70%",
					targets: [0]
				},
				{ 
					orderable: true,   //Recebimento
					width: "20%",
					targets: [1]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
					targets: [2]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
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


			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputServicoVendaNovo = $('#inputServicoVenda').val();
				var inputServicoVendaVelho = $('#inputServicoVendaNome').val();
				var inputEstadoAtual = $('#inputEstadoAtual').val();				

				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "profissionalServicoValida.php",
					data: ('nomeNovo='+inputServicoVendaNovo+'&nomeVelho='+inputServicoVendaVelho+'&estadoAtual='+inputEstadoAtual),
					success: function(resposta){

						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}

						if (resposta == 'EDITA'){
							document.getElementById('inputEstadoAtual').value = 'GRAVA_EDITA';
						} else{
							document.getElementById('inputEstadoAtual').value = 'GRAVA_NOVO';
						}						
						
						$( "#formProfissionaServico" ).submit();
					}
				})
									
			})
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaProfissionaServico(Permission, PrXSVProfissional, PrXSVServicoVenda, Tipo){
		
			if (Permission == 1){
				document.getElementById('inputProfissionaServicoId').value = PrXSVProfissional;
				document.getElementById('inputServicoVendaNome').value = PrXSVServicoVenda;
						
				if (Tipo == 'edita'){	
					document.getElementById('inputEstadoAtual').value = "EDITA";
					document.formProfissionaServico.action = "profissionaServico.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formProfissionaServico, "Tem certeza que deseja excluir esse serviço?", "profissionaServicoExclui.php");
				} else if (Tipo == 'imprime'){
					document.formProfissionaServico.action = "profissionaServicoImprime.php";
					document.formProfissionaServico.setAttribute("target", "_blank");
				}
				
				document.formProfissionaServico.submit();
			} else{
				alerta('Permissão Negada!','');
			}
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
								<h3 class="card-title">Relação de Serviços do Profissional </h3>
							</div>

							<div class="card-body">
								<form name="formProfissionaServico" id="formProfissionaServico" method="post" class="form-validate-jquery">

									<input type="hidden" id="inputProfissionaServicoId" name="inputProfissionaServicoId" value="<?php if (isset($_POST['inputProfissionaServicoId'])) echo $_POST['inputProfissionaServicoId']; ?>" >
									<input type="hidden" id="inputServicoVendaNome" name="inputServicoVendaNome" value="<?php if (isset($_POST['inputServicoVendaNome'])) echo $_POST['inputServicoVendaNome']; ?>" >
									<input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>" >

									<div class="row">
										<div class="col-lg-7">
											<label for="inputServicoVenda">Serviço <span class="text-danger"> *</span></label>
											<select id="inputServicoVenda" name="inputServicoVenda" class="form-control select-search" required>
												<option value="">Selecione</option>
												<?php 
													$sql = "SELECT SrVenId, SrVenNome, SituaNome, SituaChave, SituaCor
															FROM ServicoVenda
															JOIN Situacao ON SituaId = SrVenStatus
															WHERE SituaChave = 'ATIVO' and SrVenUnidade = ". $_SESSION['UnidadeId'] ."
															ORDER BY SrVenNome ASC";
													$result = $conn->query($sql);
													$rowServicoVenda = $result->fetchAll(PDO::FETCH_ASSOC);
													
													foreach ($rowServicoVenda as $item){
														$seleciona = $item['SrVenId'] == $rowProfissionaServico['PrXSVServicoVenda'] ? "selected" : "";
														print('<option value="'.$item['SrVenId'].'" '. $seleciona .'>'.$item['SrVenNome'].'</option>');
													}
												

												?>
											</select>
										</div>

										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputRecebimento">Recebimento (%) <span class="text-danger"> *</span></label>
												<input type="number" min="1" max="100" id="inputRecebimento" name="inputRecebimento" class="form-control" placeholder="Recebimento (%)" value="<?php if (isset($_POST['inputProfissionaServicoId'])) echo $rowProfissionaServico['PrXSVRecebimento']; ?>" required autofocus>
											</div>
										</div>
										<div class="col-lg-3">
											<div class="form-group" style="padding-top:25px;">
												<?php

													//editando
													if (isset($_POST['inputProfissionaServicoId'])){
														print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
														print('<a href="profissional.php" class="btn btn-basic" role="button">Cancelar</a>');
													} else{ //inserindo
														print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
													}

												?>
											</div>
										</div>
									</div>
								</form>
							</div>
							
							<table id="tblProfissionaServico" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Serviço</th>
										<th>Recebimento</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										print('
										<tr>
											<td>'.$item['PrXSVServicoVenda'].'</td>
											<td>'.$item['PrXSVRecebimento'].'</td>
											');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaProfissionaServico(1,'.$item['PrXSVProfissional'].', \''.$item['PrXSVServicoVenda'].'\', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaProfissionaServico(1,'.$item['PrXSVProfissional'].', \''.$item['PrXSVServicoVenda'].'\', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
													</div>
												</div>
											</td>
										</tr>');
									}
								?>

								</tbody>
							</table>
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
