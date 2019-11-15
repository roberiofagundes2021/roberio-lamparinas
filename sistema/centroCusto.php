<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Centro de Custo';

include('global_assets/php/conexao.php');

$sql = "SELECT CeCusId, CeCusNome, CeCusStatus
		FROM CentroCusto
	    WHERE CeCusEmpresa = ". $_SESSION['EmpreId'] ."
		ORDER BY CeCusNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
$count = count($row);
//var_dump($count);die;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Centro de Custo</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<!-- /theme JS files -->	
	
	<script>

		$(document).ready(function (){	
			$('#tblCentroCusto').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Centro de Custo
					width: "70%",
					targets: [0]
				},
				{ 
					orderable: true,   //Situação
					width: "15%",
					targets: [1]
				},
				{ 
					orderable: true,   //Ações
					width: "15%",
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
		})
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaCentroCusto(CeCusId, CeCusNome, CeCusStatus, Tipo){
		
			document.getElementById('inputCentroCustoId').value = CeCusId;
			document.getElementById('inputCentroCustoNome').value = CeCusNome;
			document.getElementById('inputCentroCustoStatus').value = CeCusStatus;
					
			if (Tipo == 'edita'){	
				document.formCentroCusto.action = "centroCustoEdita.php";		
			} else if (Tipo == 'exclui'){
				confirmaExclusao(document.formCentroCusto, "Tem certeza que deseja excluir esse Centro de Custo?", "centroCustoExclui.php");
			} else if (Tipo == 'mudaStatus'){
				document.formCentroCusto.action = "centroCustoMudaSituacao.php";
			} 
			
			document.formCentroCusto.submit();
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
								<h3 class="card-title">Relação de Centros de Custo</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="categoria.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos Centros de Custo da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right"><a href="centroCustoNovo.php" class="btn btn-success" role="button">Novo Centro de Custo</a></div>
							</div>					
							
							<!-- A table só filtra se colocar 6 colunas. Onde mudar isso? -->
							<table id="tblCentroCusto" class="table">
								<thead>
									<tr class="bg-slate">
										<th data-filter>Centro de Custo</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['CeCusStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['CeCusStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['CeCusNome'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCentroCusto('.$item['CeCusId'].', \''.$item['CeCusNome'].'\','.$item['CeCusStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCentroCusto('.$item['CeCusId'].', \''.$item['CeCusNome'].'\','.$item['CeCusStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCentroCusto('.$item['CeCusId'].', \''.$item['CeCusNome'].'\','.$item['CeCusStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
				
				<form name="formCentroCusto" method="post" action="centroCustoEdita.php">
					<input type="hidden" id="inputCentroCustoId" name="inputCentroCustoId" >
					<input type="hidden" id="inputCentroCustoNome" name="inputCentroCustoNome" >
					<input type="hidden" id="inputCentroCustoStatus" name="inputCentroCustoStatus" >
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
