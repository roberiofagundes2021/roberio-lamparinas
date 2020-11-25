<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Caixa';

include('global_assets/php/conexao.php');

$sql = "SELECT CaixaId,CaixaNome,CaixaConta,CaixaOperador,CaixaStatus,UsuarNome,CnBanNome,SituaNome, SituaCor, SituaChave
       FROM Caixa
       JOIN ContaBanco on CnBanId = CaixaConta
       JOIN Usuario on UsuarId = CaixaOperador
       JOIN Situacao on SituaId = CaixaStatus
	   WHERE CaixaUnidade = ". $_SESSION['UnidadeId'] ."
	   ORDER BY CaixaNome ASC";
	
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Caixa</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){
			
			/* Início: Tabela Personalizada */
			$('#tblCaixa').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Caixa
					width: "30%",
					targets: [0]
				},	
				{
					orderable: true,   //Conta
					width: "30%",
					targets: [1]
				},
                {
					orderable: true,   //Usuário
					width: "30%",
					targets: [2]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [3]
				},
				{ 
					orderable: false,   //Ações
					width: "5%",
					targets: [4]
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
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaCaixa(CaixaId,CaixaNome, CaixaStatus, Tipo){
		
			document.getElementById('inputCaixaId').value = CaixaId;
			document.getElementById('inputCaixaNome').value = CaixaNome;
			document.getElementById('inputCaixaStatus').value = CaixaStatus;
					
			if (Tipo == 'edita'){	
				document.formCaixa.action = "caixaEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formCaixa, "Tem certeza que deseja excluir esse Caixa?", "caixaExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formCaixa.action = "caixaMudaSituacao.php";
			}
			
			document.formCaixa.submit();
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
								<h3 class="card-title">Relação dos Caixas</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="subcategoria.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos Caixas da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
								<div class="text-right"><a href="caixaNovo.php" class="btn btn-principal" role="button">Novo Caixa</a></div>
							</div>
							
							<table id="tblCaixa" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Caixa</th>
										<th>Conta</th>
                                        <th>Operador</th>
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
											<td>'.$item['CaixaNome'].'</td>
											<td>'.$item['CnBanNome'].'</td>
											<td>'.$item['UsuarNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCaixa('.$item['CaixaId'].', \''.$item['CaixaNome'].'\','.$situacaoChave.', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCaixa('.$item['CaixaId'].', \''.$item['CaixaNome'].'\','.$item['CaixaStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCaixa('.$item['CaixaId'].', \''.$item['CaixaNome'].'\','.$item['CaixaStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formCaixa" method="post">
					<input type="hidden" id="inputCaixaId" name="inputCaixaId" >
					<input type="hidden" id="inputCaixaNome" name="inputCaixaNome" >
					<input type="hidden" id="inputCaixaStatus" name="inputCaixaStatus" >
				</form>

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
