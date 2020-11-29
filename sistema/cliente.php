<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Cliente';

include('global_assets/php/conexao.php');

$sql = "SELECT ClienId, ClienNome, ClienCpf, ClienCnpj, ClienTelefone, ClienCelular, ClienStatus, ClienEmail, SituaNome, SituaCor, SituaChave
        FROM Cliente
        JOIN Situacao on SituaId = ClienStatus
        WHERE ClienUnidade = ". $_SESSION['UnidadeId'] ." 
        ORDER BY ClienNome ASC";
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
	<title>Lamparinas | Cliente</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/lamparinas/stop-back.js"></script>
	
	<!-- /theme JS files -->	
	
	<script type="text/javascript">
		
		$(document).ready(function() {
			
			/* Início: Tabela Personalizada */
			$('#tblCliente').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Nome
					width: "30%",
					targets: [0]
				},
				{ 
					orderable: true,   //CPF/CNPJ
					width: "20%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Telefone
					width: "15%",
					targets: [2]
				},
				{ 
					orderable: true,   //E-mail
					width: "25%",
					targets: [3]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [4]
				},
				{ 
					orderable: false,  //Ações
					width: "5%",
					targets: [5]
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
		function atualizaCliente(ClienId, ClienNome, ClienStatus, Tipo){
			
			if (Tipo == 'imprime'){
				
				document.getElementById('inputClienteEmail').value = document.getElementById('cmbEmail').value;
				
				document.formCliente.action = "clienteImprime.php";
				document.formCliente.setAttribute("target", "_blank");
			} else {
				document.getElementById('inputClienteId').value = ClienId;
				document.getElementById('inputClienteNome').value = ClienNome;
				document.getElementById('inputClienteStatus').value = ClienStatus;
				console.log(ClienId, ClienNome, ClienStatus, Tipo)
						
				if (Tipo == 'edita'){	
					document.formCliente.action = "ClienteEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formCliente, "Tem certeza que deseja excluir esse cliente", "clienteExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formCliente.action = "clienteMudaSituacao.php";
				} 
			}
			
			document.formCliente.submit();
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
								<h3 class="card-title">Relação de Clientes</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="cliente.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-9">
										<p class="font-size-lg">A relação abaixo faz referência aos clientes da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b></p>
									</div>
									<div class="col-lg-3">
										<div class="text-right">
											<div class="dropdown p-0" style="float:right; margin-left: 5px;">										
												<a href="#collapse-imprimir-relacao" class="dropdown-toggle btn bg-slate-700 btn-icon" role="button" data-toggle="collapse" data-placement="bottom" data-container="body">
													<i class="icon-printer2"></i>																						
												</a>
											</div>
											<div>
												<a href="clienteNovo.php" class="btn btn-principal" role="button">Novo Cliente</a>
											</div>
										</div>

										<div class="collapse" id="collapse-imprimir-relacao" style="margin-top: 5px;">
											<div class="row">
												<div class="col-lg-1">
												</div>
												<div class="col-lg-11">
													<div class="form-group">												
														<select id="cmbCliente" name="cmbCliente" class="form-control form-control-select2">
															<option value="#">Filtrar por: Cliente (todas)</option>
															<?php 
																$sql = "SELECT ClienId, ClienNome
																		FROM Cliente
																		JOIN Situacao on SituaId = ClienStatus		  
																		WHERE ClienUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave = 'ATIVO'
																		ORDER BY ClienNome ASC";
																$result = $conn->query($sql);
																$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);
																
																foreach ($rowCategoria as $item){															
																	print('<option value="'.$item['ClienId'].'">'.$item['ClienNome'].'</option>');
																}													
															?>
														</select>
													</div>
												
													<a href="#" onclick="atualizaCliente(0, '','', 'imprime');" class="form-control btn bg-slate-700 btn-icon" role="button" data-placement="bottom" data-container="body">
														<i class="icon-printer2"> Gerar PDF ou Imprimir</i>
													</a>
												</div>
											</div>
										</div>
									</div>	
								</div>
							</div>
							
							<table class="table" id="tblCliente">
								<thead>
									<tr class="bg-slate">
										<th>Nome</th>
										<th>CPF/CNPJ</th>
										<th>Celular</th>										
										<th>E-mail</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php

									foreach ($row as $item){
										
										$situacao = $item['SituaChave'] == 'ATIVO' ? 'Ativo' : 'Inativo';
										$situacaoClasse = 'badge badge-flat border-'.$item['SituaCor'].' text-'.$item['SituaCor'];
										$situacaoChave ='\''.$item['SituaChave'].'\'';
										$documento = $item['ClienCnpj'] == NULL ? $item['ClienCpf'] : $item['ClienCnpj'];
										$telefone = $item['ClienCelular'] == NULL ? $item['ClienTelefone'] : $item['ClienCelular'];
										
										print('
										<tr>
											<td>'.$item['ClienNome'].'</td>
											<td>'.formatarCPF_Cnpj($documento).'</td>
											<td>'.$telefone.'</td>
											<td>'.$item['ClienEmail'].'</td>
											');
										
										print('<td><a href="#" onclick="atualizaCliente('.$item['ClienId'].', \''.$item['ClienNome'].'\','.$situacaoChave.', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaCliente('.$item['ClienId'].', \''.$item['ClienNome'].'\','.$item['ClienStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7" data-popup="tooltip" data-placement="bottom" title="Editar"></i></a>
														<a href="#" onclick="atualizaCliente('.$item['ClienId'].', \''.$item['ClienNome'].'\','.$item['ClienStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>														
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
				
				<form name="formCliente" method="post">
					<input type="hidden" id="inputClienteId" name="inputClienteId" >
					<input type="hidden" id="inputClienteNome" name="inputClienteNome" >
					<input type="hidden" id="inputClienteStatus" name="inputClienteStatus" >
					<input type="hidden" id="inputClienteEmail" name="inputClienteEmail" >
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
