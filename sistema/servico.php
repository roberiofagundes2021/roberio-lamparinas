<?php 

include_once("sessao.php"); 
include('global_assets/php/conexao.php');

$_SESSION['PaginaAtual'] = 'Serviço';

if (isset($_SESSION['fotoAtual'])){
	unset($_SESSION['fotoAtual']);
}

$sql = ("SELECT ServId, ServCodigo, ServNome, CategNome, SbCatNome, ServValorVenda, ServStatus
		 FROM Servico
		 LEFT JOIN Categoria on CategId = ServCategoria
		 LEFT JOIN SubCategoria on SbCatId = ServSubCategoria
	     WHERE ServEmpresa = ". $_SESSION['EmpreId'] ."
		 ORDER BY ServNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Serviço</title>

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
			$('#tblServico').DataTable( {
				"order": [[ 1, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [{ 
					orderable: true,   //Codigo
					width: "10%",
					targets: [0]
				},
				{ 
					orderable: true,   //Produto
					width: "25%",
					targets: [1]
				},				
				{ 
					orderable: true,   //Categoria
					width: "20%",
					targets: [2]
				},
				{ 
					orderable: true,   //SubCategoria
					width: "20%",
					targets: [3]
				},
				{ 
					orderable: true,   //Preço Venda
					width: "15%",
					targets: [4]
				},
				{ 
					orderable: true,   //Situação
					width: "5%",
					targets: [5]
				},
				{ 
					orderable: false,  //Ações
					width: "5%",
					targets: [6]
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
				
				var arquivo = $('#arquivo').val();
				var id = $("input:file").attr('id');
				var tamanho =  1024 * 1024 * 10; //10MB

				//Verifica se o campo só possui espaços em branco
				if (arquivo == ''){
					alerta('Atenção','Selecione o arquivo de importação!','error');
					$('#arquivo').focus();
					return false;
				}
				
				//Verifica se a extensão é  diferente de CSV
				if (ext(arquivo) != 'csv'){
					alerta('Atenção','Por favor, envie arquivos com a seguinte extensão: CSV!','error');
					$('#arquivo').focus();
					return false;
				}
				
				//Verifica o tamanho do arquivo
				if ($('#'+id)[0].files[0].size > tamanho){
					alerta('Atenção','O arquivo enviado é muito grande, envie arquivos de até 10MB.','error');
					$('#arquivo').focus();
					return false;
				}								
				
				$( "#formUpload" ).submit();
				
			}); // enviar			
		});
		
		function ext(path) {
			var final = path.substr(path.lastIndexOf('/')+1);
			var separador = final.lastIndexOf('.');
			return separador <= 0 ? '' : final.substr(separador + 1);
		}			
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaProduto(ServicoId, ServicoNome, ServicoStatus, Tipo){
		
			if (Tipo == 'exportar'){	
				document.formServico.action = "servicoExportar.php";
				document.formServico.setAttribute("target", "_blank");	
			} else {			
				document.getElementById('inputServicoId').value = ServicoId;
				document.getElementById('inputServicoNome').value = ServicoNome;
				document.getElementById('inputServicoStatus').value = ServicoStatus;
						
				if (Tipo == 'edita'){	
					document.formServico.action = "servicoEdita.php";		
				} else if (Tipo == 'exclui'){
					confirmaExclusao(document.formServico, "Tem certeza que deseja excluir esse serviço?", "servicoExclui.php");
				} else if (Tipo == 'mudaStatus'){
					document.formServico.action = "servicoMudaSituacao.php";
				}		
			}
			
			document.formServico.submit();
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
								<h3 class="card-title">Relação de Serviços</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="perfil.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								<p class="font-size-lg">A relação abaixo faz referência aos serviços da empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b></p>
								<div class="text-right">
									<a href="servicoNovo.php" class="btn btn-success" role="button">Novo Serviço</a>
								</div>
								<div class="collapse" id="collapse-imprimir-relacao" style="margin-top: 15px; border-top:1px solid #ddd; padding-top: 10px;">
									<div class="row">
										<div class="col-lg-9">
											<a href="#"><h2>Modelo de importação</h2></a>
											<p style="font-weight: bold;">Nome do Serviço | Detalhamento do Serviço</p>
											<p>Observação: Favor utilizar o ; (ponto-e-vírgula) como delimitador ao gerar o arquivo CSV. O arquivo deve conter 3 colunas apenas, sendo que a primeira linha deve ter o cabeçalho acima.</p>
										</div>
										<div class="col-lg-3">
											<form name="formUpload" id="formUpload" method="post" enctype="multipart/form-data" action="servicoImporta.php">
												<input type="file" class="form-control" id="arquivo" name="arquivo">
												<button class="btn bg-slate-700 btn-icon" id="enviar"><i class="icon-printer2"> Importar serviços</i></button>
											</form>
										</div>
									</div>
								</div>
								<?php 

									if (isset($_SESSION['RelImportacao']) and $_SESSION['RelImportacao'] != '') {
										
										if (isset($_SESSION['Importacao']) and $_SESSION['Importacao'] == 'Erro'){
											$classe = 'alert alert-warning';
										} else {
											$classe = 'alert alert-success';
										}
										
										print('<div class="'.$classe.' alert-dismissible fade show" role="alert" style="margin-top: 10px;">'.$_SESSION['RelImportacao'].'
										          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
												  <span aria-hidden="true">&times;</span>
												  </button>
											   </div>');										
										unset($_SESSION['RelImportacao']);
										//echo "<script> alerta('Atenção','".$_SESSION['RelImportacao']."','error'); </script>";  //Nao sei porque nao aparece
									}
								
								?>
							</div>
							
							<table class="table" id="tblServico">
								<thead>
									<tr class="bg-slate">
										<th>Código</th>
										<th>Servico</th>
										<th>Categoria</th>
										<th>SubCategoria</th>
										<th>Preço Venda</th>
										<th>Situação</th>
										<th class="text-center">Ações</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['ServStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['ServStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['ServCodigo'].'</td>
											<td>'.$item['ServNome'].'</td>
											<td>'.$item['CategNome'].'</td>
											<td>'.$item['SbCatNome'].'</td>
											<td>'.formataMoeda($item['ServValorVenda']).'</td>
											');
										
										print('<td><a href="#" onclick="atualizaProduto('.$item['ServId'].', \''.$item['ServNome'].'\','.$item['ServStatus'].', \'mudaStatus\');"><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></a></td>');
										
										print('<td class="text-center">
												<div class="list-icons">
													<div class="list-icons list-icons-extended">
														<a href="#" onclick="atualizaProduto('.$item['ServId'].', \''.$item['ServNome'].'\','.$item['ServStatus'].', \'edita\');" class="list-icons-item"><i class="icon-pencil7"></i></a>
														<a href="#" onclick="atualizaProduto('.$item['ServId'].', \''.$item['ServNome'].'\','.$item['ServStatus'].', \'exclui\');" class="list-icons-item"><i class="icon-bin"></i></a>
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
				
				<form name="formServico" method="post">
					<input type="hidden" id="inputServicoId" name="inputServicoId" >
					<input type="hidden" id="inputServicoNome" name="inputServicoNome" >
					<input type="hidden" id="inputServicoStatus" name="inputServicoStatus" >
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