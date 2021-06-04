<?php 

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Serviço de Orçamento';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{

		$sql = "INSERT INTO ServicoOrcamento (SrOrcNome, SrOrcServico, SrOrcDetalhamento, SrOrcCategoria, SrOrcSubcategoria, SrOrcSituacao, SrOrcUsuarioAtualizador, 
				SrOrcUnidade) 
				VALUES (:sNome, :iServico, :sDetalhamento, :iCategoria, :iSubCategoria, :iSituacao, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						
						':sNome' => $_POST['inputNome'],
						':iServico' => $_POST['cmbServico'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['inputCategoriaId'],
						':iSubCategoria' => $_POST['inputSubCategoriaId'],
						':iSituacao' => $_POST['inputSituacao'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_SESSION['UnidadeId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Serviço incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir serviço!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error2: ' . $e->getMessage();die;
		
	}
	
	irpara("servicoOrcamento.php");
} 

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Novo Serviço para Orçamento</title>

	<?php include_once("head.php"); ?>

	<!---------------------------------Scripts Universais------------------------------------>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>	
	
	<!-- Validação -->
    <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	

	<script type="text/javascript">
	    
		$(document).ready(()=>{
        
			$("#cmbServico").change((e)=>{

                const servicoId = $(e.target).val()
			
                $.getJSON('filtraCategoria.php?idServico='+servicoId, function (dados){
					
					if (dados.length){
						
						$.each(dados, function(i, obj){					
							$('#inputCategoriaId').val(obj.CategId);
							$('#inputCategoriaNome').val(obj.CategNome);
						});

					} else {
						Reset();
					}
				});

                $.getJSON('filtraSubCategoria.php?idServico='+servicoId, function (dados){
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							$('#inputSubCategoriaId').val(obj.SbCatId);
							$('#inputSubCategoriaNome').val(obj.SbCatNome);
						});

					} else {
						Reset();
					}					
				});
            });

			function Reset(){
				$('#inputCategoriaId').val("");
				$('#inputCategoriaNome').val("");
				$('#inputSubCategoriaId').val("");
				$('#inputSubCategoriaNome').val("");
			}
	    
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
		
				e.preventDefault();
				
				let cmbServico = $('#cmbServico').val();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "servicoOrcamentoValida.php",
					data: ('IdServico='+cmbServico),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse serviço de referência já foi utilizado!','error');
							return false;
						}

						$("#formServico").submit();				
					}
				})				
			})

			$('#cancelar').on('click', function(e){
				
				e.preventDefault();
				
				$(window.document.location).attr('href',"servicoOrcamento.php");
				
			}); // cancelar
		});
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
				<!-------------------------------------------------------------------------------------------------------------------------------->
				<div class="card">
					
					<form name="formServico" id="formServico" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Serviço</h5>
						</div>
						<input id="inputSituacao" type="hidden" value="1" name="inputSituacao">
						<div class="card-body">
							<div class="media">
								<div class="media-body">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome <span class="text-danger">*</span></label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbServico">Serviço de Referência <span class="text-danger">*</span></label>
												<select id="cmbServico" name="cmbServico" class="form-control select-search" required>
													<option value="">Selecione</option>
													<?php 
													$sql = "SELECT ServiId, ServiNome
															FROM Servico
															JOIN Situacao on SituaId = ServiStatus
															WHERE ServiUnidade = ". $_SESSION['UnidadeId'] ." and SituaChave = 'ATIVO'
															ORDER BY ServiNome ASC";
													$result = $conn->query($sql);
													$rowServico = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowServico as $item){
														print('<option value="'.$item['ServiId'].'">'.$item['ServiNome'].'</option>');
													}
													
													?>
												</select>
											</div>
										</div>										
									</div>
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtDetalhamento">Detalhamento</label>
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do serviço"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br /> 
							<div class="row">
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<input type="hidden" id="inputCategoriaId" name="inputCategoriaId">
												<input type="text" id="inputCategoriaNome" name="inputCategoriaNome" class="form-control" readOnly>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<input type="hidden" id="inputSubCategoriaId" name="inputSubCategoriaId">
												<input type="text" id="inputSubCategoriaNome" name="inputSubCategoriaNome" class="form-control" readOnly>
											</div>
										</div>
									</div>
								</div>
								<br>
								<div class="row" style="margin-top: 40px;">
									<div class="col-lg-12">								
										<div class="form-group">
											<button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
											<a href="servicoOrcamento.php" class="btn btn-basic" id="cancelar">Cancelar</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
					<!-------------------------------------------------------------------------------------------------------------------------------->
				</div>
				<!-- /content area -->

			</div>
			<!-- /Content content -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->
	
	</div>
	<!-- /page content -->

	<?php include_once("alerta.php"); ?>

</body>

</html>
