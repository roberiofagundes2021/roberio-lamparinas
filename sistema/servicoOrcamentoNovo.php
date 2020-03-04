<?php 

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Serviço de Orçamento';

include('global_assets/php/conexao.php');

$sql = "SELECT UnMedId, UnMedNome, UnMedSigla, UnMedStatus
		FROM UnidadeMedida
		WHERE UnMedEmpresa = ". $_SESSION['EmpreId'] ."
		ORDER BY UnMedNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

if(isset($_POST['inputNome'])){

	try{
		$sql = "SELECT COUNT(isnull(ServiCodigo,0)) as Codigo
				FROM Servico
				Where ServiEmpresa = ".$_SESSION['EmpreId']."";
		//echo $sql;die;
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);
		
		$sCodigo = (int)$rowCodigo['Codigo'] + 1;
		$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);

		$sql = "INSERT INTO ServicoOrcamento (SrOrcNome, SrOrcDetalhamento, SrOrcCategoria, SrOrcSubcategoria, SrOrcUnidadeMedida, SrOrcSituacao, SrOrcUsuarioAtualizador, SrOrcEmpresa) 
				VALUES (:sNome, :sDetalhamento, :iCategoria, :iSubCategoria, :iUnidadeMedida, :iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						
						':sNome' => $_POST['inputNome'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['cmbCategoria'] == '' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '' ? null : $_POST['cmbSubCategoria'],
						':iUnidadeMedida' => $_POST['cmbUnidadeMedida'] == '' ? null : $_POST['cmbUnidadeMedida'],
						':iSituacao' => $_POST['inputSituacao'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
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

	
<!-----------------------------------------Validação do formulário e Seleção altomatica de Subcategorias---------------------------------------->
	<?php include_once("head.php"); ?>

	<script type="text/javascript">
	    
		$(document).ready(()=>{
        
			$("#cmbCategoria").change((e)=>{
               
                Filtrando()

                const categId = $(e.target).val()

                $.getJSON('filtraSubCategoria.php?idCategoria='+categId, function (dados){
				
					let option = '<option value="">Selecione a SubCategoria</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						Reset();
					}					
				});
            })

            function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option value="">Filtrando...</option>');
			}
			
			function Reset(){
				$('#cmbSubCategoria').empty().append('<option value="">Sem Subcategoria</option>');
			}
	    
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				
				e.preventDefault();
				
				let inputNome = $('#inputNome').val();

				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				/*/Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
					alerta('Atenção','Informe o nome do servico!','error');
					$('#inputNome').focus();
					return false;
				}*/
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "servicoOrcamentoValida.php",
					data: ('nome='+inputNome),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Já existe um serviço com esse nome!','error');
							return false;
						}						
					}
				})

				$( "#formServico" ).submit();
			})

			$('#cancelar').on('click', function(e){
				
				e.preventDefault();
				
				$(window.document.location).attr('href',"servicoOrcamento.php");
				
			}); // cancelar
		});
	</script>

	<!---------------------------------Scripts Universais------------------------------------>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<!-- Validação -->
    <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
												<label for="inputUnidadeMedida">Unidade de Medida <span class="text-danger">*</span></label>
												<select id="cmbUnidadeMedida" class="form-control form-control-select2" name="cmbUnidadeMedida" required>
													<option value="">Selecione</option>
													<?php 
													$sql = "SELECT UnMedId, UnMedNome, UnMedSigla
															FROM UnidadeMedida
															JOIN Situacao on SituaId = UnMedStatus
															WHERE UnMedEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'ATIVO'
															ORDER BY UnMedNome ASC";
													$result = $conn->query($sql);
													$rowUnidadeMedida = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowUnidadeMedida as $item){
														print('<option value="'.$item['UnMedId'].'">'.$item['UnMedNome'].'</option>');
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
							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Classificação</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria <span class="text-danger">*</span></label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2" required>
													<option value="">Selecione</option>
													<?php 
													$sql = "SELECT CategId, CategNome
															FROM Categoria
															JOIN Situacao on SituaId = CategStatus
															WHERE CategEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'ATIVO'
															ORDER BY CategNome ASC";
													$result = $conn->query($sql);
													$rowCategoria = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCategoria as $item){
														print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
													}
													
													?>
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="">Selecione</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<br>
								<div class="row" style="margin-top: 40px;">
									<div class="col-lg-12">								
										<div class="form-group">
											<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
											<a href="servicoOrcamento.php" class="btn btn-basic" id="cancelar">Cancelar</a>
										</div>
									</div>
								</div>
							</div>
							<!-- /card-body -->
						</form>
					</div>
					<!-------------------------------------------------------------------------------------------------------------------------------->
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
