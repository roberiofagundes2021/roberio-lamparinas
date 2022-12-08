<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Modalidade';

include('global_assets/php/conexao.php');

//Essa consulta é para preencher a grid
$sql = "SELECT AtModId, AtModNome, AtModTipoRecebimento, AtModSituacao, SituaNome, SituaCor, SituaChave
		FROM AtendimentoModalidade
		JOIN Situacao on SituaId = AtModSituacao
	    WHERE AtModUnidade = ". $_SESSION['UnidadeId'] ."
		ORDER BY AtModNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//var_dump($count);die;

//Se estiver editando
if(isset($_POST['inputModalidadeId']) && $_POST['inputModalidadeId']){

	//Essa consulta é para preencher o campo Nome com a Modalidade a ser editar
	$sql = "SELECT AtModId, AtModNome, AtModTipoRecebimento
			FROM AtendimentoModalidade
			WHERE AtModId = " . $_POST['inputModalidadeId'];
	$result = $conn->query($sql);
	$rowModalidade = $result->fetch(PDO::FETCH_ASSOC);
		
	$_SESSION['msg'] = array();
} 

//Se estiver gravando (inclusão ou edição)
if (isset($_POST['inputEstadoAtual']) && substr($_POST['inputEstadoAtual'], 0, 5) == 'GRAVA'){

	try{

		//Edição
		if (isset($_POST['inputEstadoAtual']) && $_POST['inputEstadoAtual'] == 'GRAVA_EDITA'){
			
			$sql = "UPDATE AtendimentoModalidade SET AtModNome = :sNome, AtModChave = :sChave, AtModTipoRecebimento = :sRecebimento, AtModUsuarioAtualizador = :iUsuarioAtualizador
					WHERE AtModId = :iModalidade";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
                            ':sChave' => formatarChave($_POST['inputNome']),
							':sRecebimento' => $_POST['cmbTipoRecebimento'],
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iModalidade' => $_POST['inputModalidadeId']
							));
	
			$_SESSION['msg']['mensagem'] = "Modalidade alterado!!!";
	
		} else { //inclusão
		
			$sql = "INSERT INTO AtendimentoModalidade (AtModNome, AtModChave, AtModTipoRecebimento, AtModSituacao, AtModUsuarioAtualizador, AtModUnidade)
					VALUES (:sNome, :sChave,:sRecebimento, :bStatus, :iUsuarioAtualizador, :iUnidade)";
			$result = $conn->prepare($sql);
					
			$result->execute(array(
							':sNome' => $_POST['inputNome'],
                            ':sChave' => formatarChave($_POST['inputNome']),
							':sRecebimento' => $_POST['cmbTipoRecebimento'],
							':bStatus' => 1,
							':iUsuarioAtualizador' => $_SESSION['UsuarId'],
							':iUnidade' => $_SESSION['UnidadeId'],
							));
	
			$_SESSION['msg']['mensagem'] = "Modalidade incluído!!!";
					
		}
	
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['tipo'] = "success";
					
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro reportado com a Modalidade!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}

	irpara("atendimentoModalidade.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Modalidade</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>	
	
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	
		
	
	<script type="text/javascript">

		$(document).ready(function (){	
			$('#tblModalidade').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Modalidade
					width: "40%",
					targets: [0]
				},
				{
					orderable: true,   //Tipo de Recebimento
					width: "40%",
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
				var inputNomeVelho = $('#inputModalidadeNome').val();
				var inputEstadoAtual = $('#inputEstadoAtual').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNomeNovo.trim();

				//Se o usuário preencheu com espaços em branco ou não preencheu nada
				if (inputNome == ''){
					$('#inputNome').val('');
					$("#formModalidade").submit();
				} else {
				
					//Esse ajax está sendo usado para verificar no banco se o registro já existe
					$.ajax({
						type: "POST",
						url: "atendimentoModalidadeValida.php",
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
							
							$( "#formModalidade" ).submit();
						}
					})
				}	
			})							
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaModalidade(Permission, AtModId, AtModNome, AtModSituacao, Tipo){
		
			if (Permission == 1){
				document.getElementById('inputModalidadeId').value = AtModId;
				document.getElementById('inputModalidadeNome').value = AtModNome;
				document.getElementById('inputModalidadeStatus').value = AtModSituacao;
						
				if (Tipo == 'edita'){	
					document.getElementById('inputEstadoAtual').value = "EDITA";
					document.formModalidade.action = "atendimentoModalidade.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formModalidade, "Tem certeza que deseja excluir essa Modalidade?", "atendimentoModalidadeExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formModalidade.action = "atendimentoModalidadeMudaSituacao.php";
				} 
				
				document.formModalidade.submit();
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
								<h3 class="card-title">Relação de Modalidade</h3>
							</div>

							<div class="card-body">
								<form name="formModalidade" id="formModalidade" method="post" class="form-validate-jquery">

									<input type="hidden" id="inputModalidadeId" name="inputModalidadeId" value="<?php if (isset($_POST['inputModalidadeId'])) echo $_POST['inputModalidadeId']; ?>" >
									<input type="hidden" id="inputModalidadeNome" name="inputModalidadeNome" value="<?php if (isset($_POST['inputModalidadeNome'])) echo $_POST['inputModalidadeNome']; ?>" >
									<input type="hidden" id="inputModalidadeStatus" name="inputModalidadeStatus" >
									<input type="hidden" id="inputEstadoAtual" name="inputEstadoAtual" value="<?php if (isset($_POST['inputEstadoAtual'])) echo $_POST['inputEstadoAtual']; ?>" >

									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNome">Nome do Modalidade <span class="text-danger"> *</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Modalidade" value="<?php if (isset($_POST['inputModalidadeId'])) echo $rowModalidade['AtModNome']; ?>" required autofocus>
											</div>
										</div>
										<div class="col-lg-3">
											<label for="cmbTipoRecebimento">Tipo de Recebimento <span class="text-danger"> *</span></label>
											<select id="cmbTipoRecebimento" name="cmbTipoRecebimento" class="form-control form-control-select2" placeholder=" Tipo de Recebimento" required >>
												<option value="">Selecione um Tipo de Recebimento</option>
												<option value="À Vista" <?php if (isset($_POST['inputModalidadeId'])) if ($rowModalidade['AtModTipoRecebimento'] == 'À Vista') echo "selected"; ?>>À Vista</option>
												<option value="À Prazo" <?php if (isset($_POST['inputModalidadeId'])) if ($rowModalidade['AtModTipoRecebimento'] == 'À Prazo') echo "selected"; ?>>À Prazo</option>
											</select>
										</div>
										<div class="col-lg-6">
											<div class="form-group" style="padding-top:25px;">
												<?php

													//editando
													if (isset($_POST['inputModalidadeId'])){
														print('<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>');
														print('<a href="atendimentoModalidade.php" class="btn btn-basic" role="button">Cancelar</a>');
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
							<table id="tblModalidade" class="table">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Modalidade</th>
										<th data-filter>Tipo de Recebimento</th>
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
										
										print('
										<tr>
											<td>'.$item['AtModNome'].'</td>
											<td>'.$item['AtModTipoRecebimento'].'</td>
											
											');
										
										print('<td><a href="#" onclick="atualizaModalidade(1,'.$item['AtModId'].', \''.$item['AtModNome'].'\','.$situacaoChave.', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">');

										

										print('
										<div class="list-icons">
											<div class="list-icons list-icons-extended">
												<a href="#" onclick="atualizaModalidade(1,'.$item['AtModId'].', \''.$item['AtModNome'].'\', '.$item['AtModSituacao'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar" ></i></a>
												<a href="#" onclick="atualizaModalidade(1,'.$item['AtModId'].', \''.$item['AtModNome'].'\', '.$item['AtModSituacao'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
