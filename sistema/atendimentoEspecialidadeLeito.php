<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Tipo da Especilalidade do Leito';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT EsLeiId, EsLeiNome, EsLeiStatus, SituaNome, SituaCor, SituaChave, 
		dbo.fnClassificacaoAtendimento(EsLeiId, EsLeiUnidade, 'EspecialidadeLeito') as Classificacao
		FROM EspecialidadeLeito
		JOIN Situacao on SituaId = EsLeiStatus
	    WHERE EsLeiUnidade = ". $_SESSION['UnidadeId'] ."
		ORDER BY EsLeiNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//var_dump($count);die;

//Se estiver editando
if(isset($_POST['inputEspecialidadeLeitoId']) && $_POST['inputEspecialidadeLeitoId']){

	//Essa consulta é para preencher o campo Nome com o tipo da Especilalidade do Leito a ser editado
	$sql = "SELECT EsLeiId, EsLeiNome, ELXClClassificacao
			FROM EspecialidadeLeito
			LEFT JOIN EspecialidadeLeitoXClassificacao on ELXClEspecialidadeLeito = EsLeiId
			WHERE EsLeiId = " . $_POST['inputEspecialidadeLeitoId'] ." and EsLeiUnidade = ".$_SESSION['UnidadeId'];
	$result = $conn->query($sql);
	$rowEspecialidadeLeito = $result->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rowEspecialidadeLeito as $item) {
		$aClassificacao[] = $item['ELXClClassificacao'];
		$EspecialidadeLeitoNome = $item['EsLeiNome'];
	}
		
	$_SESSION['msg'] = array();
} 

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA'){

	try{

		//Edição
		if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA'){
			
			$sql = "UPDATE EspecialidadeLeito SET EsLeiNome = :sNome, EsLeiUsuarioAtualizador = :iUsuarioAtualizador
					WHERE EsLeiId = :iEspecialidadeLeito";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iEspecialidadeLeito' => $_POST['inputEspecialidadeLeitoId']
							));

			$sql = "DELETE FROM EspecialidadeLeitoXClassificacao 
					WHERE ELXClEspecialidadeLeito = :iEspecialidadeLeito";
			$result = $conn->prepare($sql);
		
			$result->execute(array(':iEspecialidadeLeito' => $_POST['inputEspecialidadeLeitoId']));

			$insertId = $conn->lastInsertId();
			
			//Grava as Classificações
			if ($_POST['cmbClassificacao']) {

				$sql = "INSERT INTO EspecialidadeLeitoXClassificacao (ELXClEspecialidadeLeito, ELXClClassificacao, ELXClUnidade)
						VALUES (:iEspecialidadeLeito, :iClassificacao, :iUnidade)";
				$result = $conn->prepare($sql);
	
				foreach ($_POST['cmbClassificacao'] as $key => $value) {
	
					$result->execute(array(
						':iEspecialidadeLeito' =>   $_POST['inputEspecialidadeLeitoId'],
						':iClassificacao' => $value,
						':iUnidade' => $_SESSION['UnidadeId']			
					));
				}
			}
	
			$_SESSION['msg']['mensagem'] = "Tipo da especilalidade do leito alterado!!!";
	
		} else { //inclusão
		
			$sql = "INSERT INTO EspecialidadeLeito (EsLeiNome, EsLeiStatus, EsLeiUsuarioAtualizador, EsLeiUnidade)
					VALUES (:sNome, :bStatus, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
							':bStatus' => 1,
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iUnidade' => $_SESSION['UnidadeId'],
							));

			$insertId = $conn->lastInsertId();
			
			//Grava as Classificações
			if ($_POST['cmbClassificacao']) {

				$sql = "INSERT INTO EspecialidadeLeitoXClassificacao (ELXClEspecialidadeLeito, ELXClClassificacao, ELXClUnidade)
						VALUES (:iEspecialidadeLeito, :iClassificacao, :iUnidade)";
				$result = $conn->prepare($sql);
	
				foreach ($_POST['cmbClassificacao'] as $key => $value) {
	
					$result->execute(array(
						':iEspecialidadeLeito' =>  $insertId,
						':iClassificacao' => $value,
						':iUnidade' => $_SESSION['UnidadeId']			
					));
				}
			}
	
			$_SESSION['msg']['mensagem'] = "Tipo da especilalidade do leito incluído!!!";
					
		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com o tipo da Especilalidade do Leito!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoEspecialidadeLeito.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Tipo da Especilalidade do leito</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>

	
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>	
	
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
		
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblEspecialidadeLeito').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Tipo da especilalidade do leito
					width: "45%",
					targets: [0]
				},
				{
					orderable: true,   //Classificação
					width: "35%",
					targets: [1]
				},
				{ 
					orderable: true,   //Situação
					width: "10%",
					targets: [2]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
					targets: [3]
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
				
				var inputNomeNovo = $('#inputNome').val();
				var inputNomeVelho = $('#inputEspecialidadeLeitoNome').val();
				var inputEstadoAtual = $('#inputEstadoAtual').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNomeNovo.trim();

				//Se o usuário preencheu com espaços em branco ou não preencheu nada
				if (inputNome == ''){
					$('#inputNome').val('');
					$("#formEspecialidadeLeito").submit();
				} else {
				
					//Esse ajax está sendo usado para verificar no banco se o registro já existe
					$.ajax({
						type: "POST",
						url: "atendimentoEspecialidadeLeitoValida.php",
						data: ('nomeNovo='+inputNome+'&nomeVelho='+inputNomeVelho+'&estadoAtual='+inputEstadoAtual),
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
							
							$( "#formEspecialidadeLeito" ).submit();
						}
					})
				}	
			})							
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaEspecialidadeLeito(Permission, EsLeiId, EsLeiNome, EsLeiStatus, Tipo){
		
			if (Permission == 1){
				document.getElementById('inputEspecialidadeLeitoId').value = EsLeiId;
				document.getElementById('inputEspecialidadeLeitoNome').value = EsLeiNome;
				document.getElementById('inputEspecialidadeLeitoStatus').value = EsLeiStatus;
						
				if (Tipo == 'edita'){	
					document.getElementById('inputEstadoAtual').value = "EDITA";
					document.formEspecialidadeLeito.action = "atendimentoEspecialidadeLeito.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formEspecialidadeLeito, "Tem certeza que deseja excluir esse tipo da especilalidade do leito?", "atendimentoEspecialidadeLeitoExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formEspecialidadeLeito.action = "atendimentoEspecialidadeLeitoMudaSituacao.php";
				} 
				
				document.formEspecialidadeLeito.submit();
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
								<h3 class="card-title">Relação da Especilalidade do Leito</h3>
							</div>

							<div class="card-body">
								<form name="formEspecialidadeLeito" id="formEspecialidadeLeito" method="post" class="form-validate-jquery">

									<input type="hidden" id="inputEspecialidadeLeitoId" name="inputEspecialidadeLeitoId" value="<?php if (isset($_POST['inputEspecialidadeLeitoId'])) echo $_POST['inputEspecialidadeLeitoId']; ?>" >
									<input type="hidden" id="inputEspecialidadeLeitoNome" name="inputEspecialidadeLeitoNome" value="<?php if (isset($_POST['inputEspecialidadeLeitoNome'])) echo $_POST['inputEspecialidadeLeitoNome']; ?>" >
									<input type="hidden" id="inputEspecialidadeLeitoStatus" name="inputEspecialidadeLeitoStatus" >
									<input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>" >

									<div class="row">
										<div class="col-lg-5">
											<div class="form-group">
												<label for="inputNome">Nome do Tipo da Especilalidade do Leito <span class="text-danger"> *</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Tipo da Especilalidade do Leito" value="<?php if (isset($_POST['inputEspecialidadeLeitoId'])) echo $EspecialidadeLeitoNome; ?>" required autofocus>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbClassificacao">Classificação<span class="text-danger"> *</span></label>
												<select id="cmbClassificacao" name="cmbClassificacao[]" class="form-control multiselect-filtering" multiple="multiple">
													<option value="H" <?php if (isset($_POST['inputEspecialidadeLeitoId'])) if (in_array('H', $aClassificacao)) echo "selected"; ?>>Hospitalar</option>
													<option value="A" <?php if (isset($_POST['inputEspecialidadeLeitoId'])) if (in_array('A', $aClassificacao)) echo "selected"; ?>>Ambulátorial</option>
												</select>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group" style="padding-top:25px;">
												<?php

													//editando
													if (isset($_POST['inputEspecialidadeLeitoId'])){
														print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
														print('<a href="atendimentoEspecialidadeLeito.php" class="btn btn-basic" role="button">Cancelar</a>');
													} else{ //inserindo
														print('<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>');
													}

												?>
											</div>
										</div>
									</div>
								</form>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table id="tblEspecialidadeLeito" class="table">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Tipo da Especilalidade do Leito</th>
										<th data-filter>Classificação</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['SituaNome'];
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										$situacaoChave ='\''.$item['SituaChave'].'\'';
										$Classificacao = $item['Classificacao'];
										print('
										<tr>
											<td>'.$item['EsLeiNome'].'</td>
											<td>'.$Classificacao.'</td>
											');
										
										print('<td><a href="#" onclick="atualizaEspecialidadeLeito(1,'.$item['EsLeiId'].', \''.$item['EsLeiNome'].'\','.$situacaoChave.', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">');

										

										print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaEspecialidadeLeito(1,'.$item['EsLeiId'].', \''.$item['EsLeiNome'].'\', '.$item['EsLeiStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaEspecialidadeLeito(1,'.$item['EsLeiId'].', \''.$item['EsLeiNome'].'\', '.$item['EsLeiStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
											</div>
										</div>								
										');            
											
										
										print('
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
