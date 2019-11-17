<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Banco';

include('global_assets/php/conexao.php');

$sql = ("SELECT BancoId, BancoCodigo, BancoNome, BancoStatus
		 FROM Banco
		 ORDER BY BancoCodigo ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//	var_dump($count);die;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Banco</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblBanco').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,  //Codigo
					width: "15%",
					targets: [0]
				},	
				{
					orderable: true,   //Nome do Banco
					width: "55%",
					targets: [1]
				},	
				{
					orderable: true,   //Situacao
					width: "15%",
					targets: [2]
				},	
				{
					orderable: false,   //Acoes
					width: "15%",
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
		});
		
		function atualizaBanco(BancoId, BancoNome, BancoStatus, Tipo){

			document.getElementById('inputBancoId').value = BancoId;
			document.getElementById('inputBancoNome').value = BancoNome;
			document.getElementById('inputBancoStatus').value = BancoStatus;
					
			if (Tipo == 'edita'){	
				document.formBanco.action = "bancoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formBanco, "Tem certeza que deseja excluir esse Banco?", "bancoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formBanco.action = "bancoMudaSituacao.php";
			} 
			
			document.formBanco.submit();
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
								<h3 class="card-title">Relação de Bancos</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="Banco.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">Segue abaixo a relação de bancos disponíveis para os usuários do sistema.</p>
								<div class="text-right"><a href="bancoNovo.php" class="btn btn-success" role="button">Novo Banco</a></div>
							</div>							

							<table id="tblBanco" class="table">
								<thead>
									<tr class="bg-slate">
										<th>Código do Banco</th>
										<th>Nome do Banco</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){

										$situacao = $item['BancoStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['BancoStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['BancoCodigo'].'</td>
											<td>'.$item['BancoNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaBanco('.$item['BancoId'].', \''.$item['BancoNome'].'\','.$item['BancoStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
																				
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaBanco('.$item['BancoId'].', \''.$item['BancoNome'].'\','.$item['BancoStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaBanco('.$item['BancoId'].', \''.$item['BancoNome'].'\','.$item['BancoStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formBanco" method="post" action="bancoEdita.php">
					<input type="hidden" id="inputBancoId" name="inputBancoId" >
					<input type="hidden" id="inputBancoNome" name="inputBancoNome" >
					<input type="hidden" id="inputBancoStatus" name="inputBancoStatus" >
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
