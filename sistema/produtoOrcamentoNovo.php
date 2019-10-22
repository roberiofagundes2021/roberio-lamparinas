<?php 

include_once("sessao.php");

<<<<<<< HEAD
$_SESSION['PaginaAtual'] = 'Novo Produto de Orçamento';
=======
$_SESSION['PaginaAtual'] = 'Unidade de Medida';
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9

include('global_assets/php/conexao.php');

$sql = ("SELECT UnMedId, UnMedNome, UnMedSigla, UnMedStatus
	FROM UnidadeMedida
	WHERE UnMedEmpresa = ". $_SESSION['EmpreId'] ."
	ORDER BY UnMedNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>
<?php

if(isset($_POST['inputNome'])){

	try{
		$sql = "SELECT COUNT(isnull(ProduCodigo,0)) as Codigo
				FROM Produto
				Where ProduEmpresa = ".$_SESSION['EmpreId']."";
		//echo $sql;die;
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);
		
		$sCodigo = (int)$rowCodigo['Codigo'] + 1;
		$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);
	} catch(PDOException $e) {
		echo 'Error1: ' . $e->getMessage();die;
	}
	
	try{
		
		$sql = "INSERT INTO ProdutoOrcamento (PrOrcNome, PrOrcDetalhamento, PrOrcCategoria, PrOrcSubcategoria, PrOrcUnidadeMedida, PrOrcSituacao, PrOrcUsuarioAtualizador, PrOrcEmpresa) 
				VALUES (:sNome, :sDetalhamento, :iCategoria, :iSubCategoria, :iUnidadeMedida, :iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$result->execute(array(
						
						':sNome' => $_POST['inputNome'],
						':sDetalhamento' => $_POST['txtDetalhamento'],
						':iCategoria' => $_POST['cmbCategoria'] == '#' ? null : $_POST['cmbCategoria'],
						':iSubCategoria' => $_POST['cmbSubCategoria'] == '#' ? null : $_POST['cmbSubCategoria'],
						':iUnidadeMedida' => $_POST['cmbUnidadeMedida'] == '#' ? null : $_POST['cmbUnidadeMedida'],
						':iSituacao' => $_POST['inputSituacao'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Produto incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir produto!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error2: ' . $e->getMessage();die;
		
	}
	
	irpara("produtoOrcamento.php");
} 

?>




<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<<<<<<< HEAD
	<title>Lamparinas | Novo Produto para Orçamento</title>
=======
	<title>Lamparinas | UnidadeMedida</title>
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9

<!---------------------------------Scripts Universais------------------------------------>
    <script src="http://malsup.github.com/jquery.form.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>	
	<!--<script src="global_assets/js/main/jquery.form.js"></script>-->
	
	<!-- /theme JS files -->
	
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>

<!-----------------------------------------Validação do formulário e Seleção altomatica de Subcategorias---------------------------------------->
	<?php include_once("head.php"); ?>

	    <script type="text/javascript">
	    	$(document).ready(()=>{
                $("#cmbCategoria").change((e)=>{
                  
                    Filtrando()

                    const categId = $(e.target).val()

                    $.getJSON('filtraSubCategoria.php?idCategoria='+categId, function (dados){
					    let option = '<option>Selecione a SubCategoria</option>';
					
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
				   $('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
			    }
			
			    function Reset(){
				   $('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			    }
	    	})
		</script>

		<script type="text/javascript" >
 
        $(document).ready(function() {
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				
				e.preventDefault();
				
				let inputNome = $('#inputNome').val();

				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
					alerta('Atenção','Informe o nome do produto!','error');
					$('#inputNome').focus();
					return false;
				}
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "produtoOrcamentoValida.php",
					data: ('nome='+inputNome),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Já existe um produto com esse nome!','error');
							return false;
						}
						
						$( "#formProduto" ).submit();
					}
				})
			})
<<<<<<< HEAD

			$('#cancelar').on('click', function(e){
				
				e.preventDefault();
				
				var inputFoto = $('#inputFoto').val();
				
				$(window.document.location).attr('href',"produtoOrcamento.php");
				
			}); // cancelar
=======
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
		})
	</script>
<!------------------------------------Fim de validação do formulário e Seleção altomatica de Subcategorias------------------------------------>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
<<<<<<< HEAD
	<script src="global_assets/js/demo_pages/components_popups.js"></script>		
=======
	<script src="global_assets/js/demo_pages/components_popups.js"></script
	<!-- /theme JS files -->	
	
	<script>

	//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
	function atualizaUnidadeMedida(UnMedId, UnMedNome, UnMedStatus, Tipo){
		
		document.getElementById('inputUnidadeMedidaId').value = UnMedId;
		document.getElementById('inputUnidadeMedidaNome').value = UnMedNome;
		document.getElementById('inputUnidadeMedidaStatus').value = UnMedStatus;

		if (Tipo == 'edita'){	
			document.formUnidadeMedida.action = "unidademedidaEdita.php";		
		} else if (Tipo == 'exclui'){
			confirmaExclusao(document.formUnidadeMedida, "Tem certeza que deseja excluir essa unidade de medida?", "unidademedidaExclui.php");
		} else if (Tipo == 'mudaStatus'){
			document.formUnidadeMedida.action = "unidademedidaMudaSituacao.php";
		} else if (Tipo == 'imprime'){
			document.formUnidadeMedida.action = "unidademedidaImprime.php";
			document.formUnidadeMedida.setAttribute("target", "_blank");
		}

		document.formUnidadeMedida.submit();
	}		
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9

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
					
					<form id="formProduto" name="formProduto" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
<<<<<<< HEAD
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Produto</h5>
=======
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Produto Para Licitação</h5>
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
						</div>
						<input id="inputSituacao" type="hidden" value="1" name="inputSituacao">
						<div class="card-body">
							<div class="media">
								<div class="media-body">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputUnidadeMedida">Unidade de Medida</label>
												<select id="cmbUnidadeMedida" class="form-control form-control-select2" name="cmbUnidadeMedida">
													<option>Selecione</option>
													<?php 
													$sql = ("SELECT UnMedNome, UnMedSigla
														FROM UnidadeMedida													     
														WHERE UnMedStatus = 1 and UnMedEmpresa = ". $_SESSION['EmpreId'] ."
														ORDER BY UnMedNome ASC");
													$result = $conn->query("$sql");
													$row = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($row as $item){															
														print('<option value="'.$item['UnMedSigla'].'">'.$item['UnMedNome'].'</option>');
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
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do produto"></textarea>
											</div>
										</div>
									</div>

								</div> <!-- media-body -->
								
								<!--<div style="text-align:center;">
									<div id="visualizar">										
										<img class="ml-3" src="global_assets/images/lamparinas/sem_foto.gif" alt="Produto" style="max-height:250px; border:2px solid #ccc;">
									</div>
									<br>
									<button id="addFoto" class="ml-3 btn btn-lg btn-success" style="width:90%">Adicionar Foto...</button>	
								</div>-->
								
							</div> <!-- media -->

							<div class="row">
								<div class="col-lg-12">
									<h5 class="mb-0 font-weight-semibold">Classificação</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
													$sql = ("SELECT CategId, CategNome
														FROM Categoria															     
														WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
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
										<div class="col-lg-6">
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
								<br>
								<div class="row" style="margin-top: 40px;">
									<div class="col-lg-12">								
										<div class="form-group">
											<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
											<button class="btn btn-lg btn-basic" id="cancelar">Cancelar</button>
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
