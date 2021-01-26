<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Comissão do Processo Licitatório';

include('global_assets/php/conexao.php');

if (isset($_POST['inputTRId'])){
	
	$_SESSION['TRId'] = $_POST['inputTRId'];
	$_SESSION['TRNumero'] = $_POST['inputTRNumero'];
}

$sql = "SELECT TRXEqTermoReferencia,TRXEqUsuario,TRXEqPresidente, TRXEqUnidade, UsuarLogin		
		FROM TRXEquipe
		JOIN Usuario on UsuarId = TRXEqUsuario
		WHERE TRXEqUnidade = ". $_SESSION['UnidadeId'] ." and TRXEqTermoReferencia = ".$_SESSION['TRId']."
		ORDER BY UsuarLogin ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

if(isset($_POST['cmbUsuario'])){
	
	try{
		
		$sql = "INSERT INTO TRXEquipe (TRXEqTermoReferencia, TRXEqUsuario, TRXEqPresidente, TRXEqUnidade)
				VALUES (:iTermoReferencia, :iUsuario, :iPresidente, :iTRXEqUnidade)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':iTermoReferencia' => $_POST['inputTRId'],
						':iUsuario' => $_POST['cmbUsuario'],
						':iPresidente' => false,
						':iTRXEqUnidade' => $_SESSION['UnidadeId'],
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Membro incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir o Membro!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("trComissao.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Comissão do Processo Licitatório</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<!-- /theme JS files -->	
	
	<script type="text/javascript">

		$(document).ready(function (){	

			//Valida Registro Duplicado
			$('#adicionar').on('click', function(e) {

				e.preventDefault();

				var cmbUsuario = $('#cmbUsuario').val();

				//remove os espaços desnecessários antes e depois
				cmbUsuarioNovo = cmbUsuario.trim();

				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "trComissaoValida.php",
					data: ('usuario=' + cmbUsuario),
					success: function(resposta) {

						if (resposta == 1) {
							alerta('Atenção', 'Esse registro já existe!', 'error');
							return false;
						}

						$("#formComissao").submit();
					}
				})
			})


			$('#tblComissao').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
			    columnDefs: [
				{
					orderable: true,   //Membro
					width: "70%",
					targets: [0]
				},
				{ 
					orderable: false,   //Presidente
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
		});
			
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaComissao(TRXEqTermoReferencia, TRXEqUsuario, Tipo){
		
			document.getElementById('inputTRId').value = TRXEqTermoReferencia;
			document.getElementById('inputUsuarioId').value = TRXEqUsuario;
					
				
			 if (Tipo == 'exclui'){
				confirmaExclusao(document.formComissao, "Tem certeza que deseja excluir essa comissão?", "trComissaoExclui.php");
			}
			
			document.formComissao.submit();
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
				<form name="formComissao" id="formComissao" method="post">
					<input type="hidden" id="inputTRId" name="inputTRId" value="<?php echo $_SESSION['TRId']; ?>">
					<input type="hidden" id="inputTRNumero" name="inputTRNumero" value="<?php echo $_SESSION['TRNumero']; ?>">
					<input type="hidden" id="inputUsuarioId" name="inputUsuarioId" >

					<!-- Info blocks -->		
					<div class="row">
						<div class="col-lg-12">
							<!-- Basic responsive configuration -->
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title">Relação da Comissão do Processo Licitatório</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-lg-12" class="card-body">	
												A relação abaixo faz referência a Comissão do Processo Licitatório da <span style="color: #FF0000; font-weight: bold;">TR nº <?php echo $_SESSION['TRNumero']; ?></span> da unidade <b><?php echo $_SESSION['UnidadeNome']; ?></b>	
										</div>		
									</div>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbUsuario"> Membro<span class="text-danger">
														*</span></label>
												<select id="cmbUsuario" name="cmbUsuario" class="form-control select">
												<option value="">Selecione</option>
													<?php
													$sql = "SELECT UsuarId, UsuarLogin
																FROM Usuario
																JOIN EmpresaXUsuarioXPerfil ON EXUXPUsuario = UsuarId
																JOIN Situacao on SituaId = EXUXPStatus
																WHERE EXUXPUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
																ORDER BY UsuarLogin ASC";
													$result = $conn->query($sql);
													$rowEquipe = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowEquipe as $item) {
														print('<option value="' . $item['UsuarId'] . '">' . $item['UsuarLogin'] . '</option>');
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-lg-3">
											<button class="btn btn-lg btn-principal" style="margin-top: 25px;" id="adicionar">Adicionar</button>
										</div>
										<div class="col-lg-3">
											<div class="text-right" style="margin-top: 40px;"><a href="tr.php" role="button"><< Termo de Referência</a>&nbsp;&nbsp;&nbsp;
											</div>
										</div>
									</div>										
								</div>	

								<table id="tblComissao" class="table">
									<thead>
										<tr class="bg-slate">
											<th>Membro</th>
											<th>Presidente</th>
											<th class="text-center">Ações</th>
										</tr>
									</thead>
									<tbody>
									<?php
										foreach ($row as $item){
											
											
											print('
											<tr>
												<td>'.$item['UsuarLogin'].'</td>
												<td>'.$item['TRXEqPresidente'].'</td>
												');
											
											
											print('<td class="text-center">
													<div class="list-icons">
														<div class="list-icons list-icons-extended">
															<a href="#" onclick="atualizaComissao('.$item['TRXEqTermoReferencia'].', '.$item['TRXEqUsuario'].', \'exclui\');" class="list-icons-item"><i class="icon-bin" data-popup="tooltip" data-placement="bottom" title="Exluir"></i></a>
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
