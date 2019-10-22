<?php 

include_once("sessao.php");

<<<<<<< HEAD
$_SESSION['PaginaAtual'] = 'Editar Produto de Orçamento';
=======
$_SESSION['PaginaAtual'] = 'Editar Produto';
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9

include('global_assets/php/conexao.php');

$sql = ("SELECT PrOrcId, PrOrcNome, PrOrcDetalhamento, PrOrcCategoria, PrOrcSubcategoria, PrOrcUnidadeMedida 
	FROM ProdutoOrcamento
	WHERE PrOrcId = ". $_POST['inputPrOrcId'] ." and PrOrcEmpresa = ". $_SESSION['EmpreId'] ." ");
$result = $conn->query("$sql");
$row = $result->fetch(PDO::FETCH_ASSOC);
//$count = count($row);

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | UnidadeMedida</title>

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

	    		// No evento de selecionar a categoria as subcategorias são carregadas altomaticamente
                $("#cmbCategoria").change((e)=>{
                  
                    Filtrando()
                    let option = '<option>Selecione a SubCategoria</option>';
                    const categId = $('#cmbCategoria').val()
                    const selectedId = $('#cmbSubCategoria').attr('valId')
                    console.log(selectedId)

                    $.getJSON('filtraSubCategoria.php?idCategoria='+categId, function (dados){
					    //let option = '<option>Selecione a SubCategoria</option>';
					
					    if (dados.length){
						
						    $.each(dados, function(i, obj){
						    	if(obj.SbCatId == selectedId){
						    		console.log('teste')
						    		option +=`<option value="${obj.SbCatId}" selected>${obj.SbCatNome}</option>`
						    	} else{
						    		option +=`<option value="${obj.SbCatId}">${obj.SbCatNome}</option>`
						    	}
						    });						
						
						    $('#cmbSubCategoria').html(option).show();
					    } else {
						    Reset();
					    }					
				    });
                })

                // No carregamento da pagina é regatada a opção já cadastrada no banco
                $(document).ready(()=>{
                	Filtrando()
                    let option = '<option>Selecione a SubCategoria</option>';
                    const categId = $('#cmbCategoria').val()
                    const selectedId = $('#cmbSubCategoria').attr('valId')
                    console.log(selectedId)

                    $.getJSON('filtraSubCategoria.php?idCategoria='+categId, function (dados){
					    //let option = '<option>Selecione a SubCategoria</option>';
					
					    if (dados.length){
						
						    $.each(dados, function(i, obj){
						    	if(obj.SbCatId == selectedId){
						    		console.log('teste')
						    		option +=`<option value="${obj.SbCatId}" selected>${obj.SbCatNome}</option>`
						    	} else{
						    		option +=`<option value="${obj.SbCatId}">${obj.SbCatNome}</option>`
						    	}
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
<<<<<<< HEAD

		<script type="text/javascript" >
 
        $(document).ready(function() {
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

=======
 



		<script type="text/javascript">
			$('#enviar').on('click', function(e){
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
				
				e.preventDefault();
				
				let inputNome = $('#inputNome').val();

<<<<<<< HEAD
				// Até tudo funcionando --- Agora é só validar e persistir no banco.


=======

				let inputDetalhamento = $('#txtDetalhamento').val();
				let inputCategoria = $('#cmbCategoria').val();
				let inputSubCategoria = $('#cmbSubCategoria').val();
				let inputPrOrcSituacao = $('#PrOrcSituacao').val();
				let inputPrOrcUsuarioAtualizador = $('#PrOrcUsuarioAtualizador').val();
				let inputPrOrcEmpresa = $('#PrOrcEmpresa').val();
				
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				inputDetalhamento = inputNome.trim();
				inputCategoria = inputNome.trim();
				inputSubCategoria = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
					alerta('Atenção','Informe o nome do produto!','error');
					$('#inputNome').focus();
					return false;
				}
				
				$( "#formProduto" ).attr('action', 'produtoOrcamentoEditaAction.php').submit();
				
			}); // enviar
		</script>


        <script type="text/javascript">
        	$(document).ready(()=>{
        		$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNome = $('#inputNome').val();
				
				console.log('teste')
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNome == ''){
<<<<<<< HEAD
					alerta('Atenção','Informe um nome para o produto!','error');
					$('#inputNome').focus();
					return false;
				} 
				$( "#formProduto" ).attr('action', 'produtoOrcamentoEditaAction.php').submit();
			})

			$('#cancelar').on('click', function(e){
				
				e.preventDefault();
				
				var inputFoto = $('#inputFoto').val();
				
				$(window.document.location).attr('href',"produtoOrcamento.php");
				
			}); // cancelar
=======
					alerta('Atenção','Informe o nome do produto!','error');
					$('#inputNome').focus();
					return false;
				}
				
				$( "#formProduto" ).attr('action', 'produtoOrcamentoEditaAction.php').submit();
				
			}); // enviar
        	})
        </script>


		<script type="text/javascript" >
 
        $(document).ready(function() {
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){

				
				e.preventDefault();
				
				let inputNome = $('#inputNome').val();

				// Até tudo funcionando --- Agora é só validar e persistir no banco.


				//remove os espaços desnecessários antes e depois
				inputNome = inputNome.trim();
				
				//Verifica se o campo só possui espaços em branco
				/*if (inputNome == ''){
					alerta('Atenção','Informe o modelo!','error');
					$('#inputNome').focus();
					return false;
				}*/
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "modeloValida.php",
					data: ('nome='+inputNome),
					success: function(resposta){
						
						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formModelo" ).submit();
					}
				})
			})
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
	<script src="global_assets/js/demo_pages/components_popups.js"></script>

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
							<h5 class="text-uppercase font-weight-bold">Editar Produto</h5>
=======
							<h5 class="text-uppercase font-weight-bold">Editar Produto de Orçamento</h5>
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
						</div>
						<div class="card-body">
							<div class="media">
								<div class="media-body">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputNome">Nome</label>
												<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required value="<?php echo $row['PrOrcNome']; ?>">
												<input id="inputId" type="hidden" value="<?php echo $row['PrOrcId'] ?>" name="inputId">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="inputUnidadeMedida">Unidade de Medida</label>
												<select id="cmbUnidadeMedida" class="form-control form-control-select2" name="cmbUnidadeMedida">
													<option><?php echo $row['PrOrcUnidadeMedida'] ?></option>
													<?php 
													$sql = ("SELECT UnMedNome, UnMedSigla
														FROM UnidadeMedida													     
														WHERE UnMedStatus = 1 and UnMedEmpresa = ". $_SESSION['EmpreId'] ."
														ORDER BY UnMedNome ASC");
													$result = $conn->query("$sql");
													$rowUnMed = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowUnMed as $item){															
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
												<textarea rows="5" cols="5" class="form-control" id="txtDetalhamento" name="txtDetalhamento" placeholder="Detalhamento do produto"><?php echo $row['PrOrcDetalhamento'] ?></textarea>
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
													<?php 
													$sql = ("SELECT CategId, CategNome
														FROM Categoria															     
														WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
														ORDER BY CategNome ASC");
													$result = $conn->query("$sql");
													$rowCateg = $result->fetchAll(PDO::FETCH_ASSOC);

													foreach ($rowCateg as $item){															
														if($item['CategId'] == $row['PrOrcCategoria']){
															print('<option value="'.$item['CategId'].'" selected>'.$item['CategNome'].'</option>');
														} else {
															print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
														}
													}
													
													?>
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2" valId="<?php echo $row['PrOrcSubcategoria']; ?>">
													<option id="selec"></option>
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
<<<<<<< HEAD
											<button class="btn btn-lg btn-success" id="enviar">Editar</button>
=======
											<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
>>>>>>> a7a5cf95908e606cec3c40cb7827128c33476de9
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
