<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo TR';

include('global_assets/php/conexao.php');

$sql = ("SELECT UsuarId, UsuarNome, UsuarEmail, UsuarTelefone
		 FROM Usuario
		 Where UsuarId = ".$_SESSION['UsuarId']."
		 ORDER BY UsuarNome ASC");
$result = $conn->query("$sql");
$rowUsuario = $result->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['inputData'])){
	
	try{
		
		$sql = ("SELECT COUNT(isnull(TrRefNumero,0)) as Numero
				 FROM TermoReferencia
				 Where TrRefEmpresa = ".$_SESSION['EmpreId']."");
		$result = $conn->query("$sql");
		$rowNumero = $result->fetch(PDO::FETCH_ASSOC);		
		
		$sNumero = (int)$rowNumero['Numero'] + 1;
		$sNumero = str_pad($sNumero,6,"0",STR_PAD_LEFT);
			
		$sql = "INSERT INTO TermoReferencia (TrRefNumero, TrRefData, TrRefCategoria, TrRefSubCategoria, TrRefConteudo,
											 TrRefSolicitante, TrRefStatus, TrRefUsuarioAtualizador, TrRefEmpresa)
				VALUES (:sNumero, :dData, :iCategoria, :iSubCategoria, :sConteudo, :iSolicitante, 
						:bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
			
		$result->execute(array(
						':sNumero' => $sNumero,
						':dData' => gravaData($_POST['inputData']),
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':sConteudo' => $_POST['txtareaConteudo'],
						':iSolicitante' => $_SESSION['UsuarId'],
						':bStatus' => 1,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Termo de referência incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir termo de referência!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();die;
	}
	
	irpara("tr.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Termo de Referência</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.	min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<!-- /theme JS files -->
	
	<!-- JS file path -->
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<!-- Uniform plugin file path -->
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
		
			$('#summernote').summernote();
			
			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
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
			
			$("#enviar").on('click', function(e){
				
				e.preventDefault();	
				
				var cmbCategoria = $('#cmbCategoria').val();
				
				if (cmbCategoria == '' || cmbCategoria == '#'){
					alerta('Atenção','Informe a categoria!','error');
					$('#cmbCategoria').focus();
					return false;
				}
			
				$("#formTR").submit();
			});
						
		}); //document.ready
		
		//Mostra o "Filtrando..." na combo SubCategoria
		function Filtrando(){
			$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
		}
		
		function ResetSubCategoria(){
			$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
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
				<div class="card">
					
					<form name="formTR" id="formTR" method="post" class="form-validate" action="trNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo TR</h5>
						</div>
						
						<div class="card-body">								
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">							
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>
																				
										<div class="col-lg-5">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and CategStatus = 1
															     ORDER BY CategNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-5">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
												</select>
											</div>
										</div>										

									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaConteudo">Conteúdo personalizado</label>
										<!--<div id="summernote" name="txtareaConteudo"></div>-->
										<textarea rows="5" cols="5" class="form-control" id="summernote" name="txtareaConteudo" placeholder="Corpo da TR (informe aqui o texto que você queira que apareça na TR)"></textarea>
									</div>
								</div>
							</div>		
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados do Solicitante</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNomeSolicitante">Solicitante</label>
												<input type="text" id="inputNomeSolicitante" name="inputNomeSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarNome']; ?>" readOnly>
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputEmailSolicitante">E-mail</label>
												<input type="text" id="inputEmailSolicitante" name="inputEmailSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarEmail']; ?>" readOnly>
											</div>
										</div>									

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputTelefoneSolicitante">Telefone</label>
												<input type="text" id="inputTelefoneSolicitante" name="inputTelefoneSolicitante" class="form-control" value="<?php echo $rowUsuario['UsuarTelefone']; ?>" readOnly>
											</div>
										</div>									
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="tr.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
					
				</div>
				<!-- /info blocks -->

			</div>
			<!-- /content area -->			
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
