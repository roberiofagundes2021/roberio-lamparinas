<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Inventário';

include('global_assets/php/conexao.php');

$sql = ("SELECT UsuarId, UsuarNome, UsuarEmail, UsuarTelefone
		 FROM Usuario
		 Where UsuarId = ".$_SESSION['UsuarId']."
		 ORDER BY UsuarNome ASC");
$result = $conn->query("$sql");
$rowUsuario = $result->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['inputData'])){
				
	try{
		
		$sql = ("Select SituaId from Situacao 
				Where SituaChave = 'PENDENTE'");
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
		$iSituacao = $row['SituaId'];
		
		$sql = "INSERT INTO Inventario (InvenData, InvenNumero, InvenDataLimite, InvenSolicitante, InvenObservacao, 
										InvenSituacao, InvenUsuarioAtualizador, InvenEmpresa)
				VALUES (:dData, :sNumero, :dDataLimite, :iSolicitante, :sObservacao, 
						:iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);

		$conn->beginTransaction();
		
		$result->execute(array(
						':dData' => gravaData($_POST['inputData']),
						':sNumero' => $_POST['inputNumero'],
						':dDataLimite' => gravaData($_POST['inputDataLimite']),
						':iSolicitante' => $_SESSION['UsuarId'],
						':sObservacao' => $_POST['txtObservacao'],
						':iSituacao' => $iSituacao,
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));

		$insertId = $conn->lastInsertId(); 

		if ($_POST['cmbLocalEstoque']){
			
			try{
				$sql = "INSERT INTO InventarioXLocalEstoque 
							(InXLEInventario, InXLELocal)
						VALUES 
							(:iInventario, :iLocal)";
				$result = $conn->prepare($sql);

				foreach ($_POST['cmbLocalEstoque'] as $key => $value){

					$result->execute(array(
									':iInventario' => $insertId,
									':iLocal' => $value									
									));
				}
				
				$conn->commit();
				
			} catch(PDOException $e) {
				$conn->rollback();
				echo 'Error: ' . $e->getMessage();exit;
			}
		}
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Inventário incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {		
		
		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir inventário!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
		exit;
	}
	
	irpara("inventario.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Inventário</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<!-- /theme JS files -->	

	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {
			            
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
						
				var inputNumero = $('#inputNumero').val();
				
				//remove os espaços desnecessários antes e depois
				inputNumero = inputNumero.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputNumero == ''){
					alerta('Atenção','Informe o número do inventario!','error');
					$('#inputNumero').focus();
					return false;
				}			
								
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "inventarioValida.php",
					data: {numero: inputNumero},
					success: function(resposta){

						if(resposta == 1){
							alerta('Atenção','Esse registro já existe!','error');
							return false;
						}
						
						$( "#formInventario" ).submit();
					}
				}); //ajax
				
			}); // enviar

            
        }); // document.ready
       

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
					
					<form name="formInventario" id="formInventario" method="post" class="form-validate">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Novo Inventário</h5>
						</div>
						
						<div class="card-body">

							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputData">Data de Emissão</label>
										<input type="text" id="inputData" name="inputData" class="form-control" placeholder="Data de Emissão" value="<?php echo date('d/m/Y'); ?>" readOnly>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputNumero">Número</label>
										<input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" autofocus required>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputDataLimite">Data Limite</label>
										<input type="text" id="inputDataLimite" name="inputDataLimite" class="form-control" placeholder="Data Limite">
									</div>
								</div>	
							</div>	
							
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group" style="border-bottom:1px solid #ddd;">
										<label for="cmbLocalEstoque">Locais do Estoque</label>
										<select id="cmbLocalEstoque" name="cmbLocalEstoque[]" class="form-control select" multiple="multiple" data-fouc>
											<?php 
												$sql = ("SELECT LcEstId, LcEstNome
														 FROM LocalEstoque															     
														 WHERE LcEstEmpresa = ". $_SESSION['EmpreId'] ." and LcEstStatus = 1
														 ORDER BY LcEstNome ASC");
												$result = $conn->query("$sql");
												$row = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($row as $item){															
													print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
												}
											
											?>
										</select>
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
									
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label for="txtObservacao">Observação</label>
												<textarea rows="5" cols="5" class="form-control" id="txtObservacao" name="txtObservacao" placeholder="Observação"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>									
							
							<div class="row" style="margin-top: 40px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="inventario.php" class="btn btn-basic" role="button">Cancelar</a>
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
