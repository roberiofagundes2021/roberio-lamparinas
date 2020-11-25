<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Caixa';

include('global_assets/php/conexao.php');

if(isset($_POST['inputCaixaId'])){
	
	$iCaixa = $_POST['inputCaixaId'];
		
	$sql = "SELECT CaixaId, CaixaNome, CaixaConta, CaixaOperador
			FROM Caixa
			WHERE CaixaId = $iCaixa ";
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	//irpara("caixa.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE Caixa SET CaixaNome = :sNome, CaixaConta = :iConta, CaixaOperador = :iOperador, CaixaUsuarioAtualizador = :iUsuarioAtualizador
				WHERE CaixaId = :iCaixa";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':iConta' => $_POST['cmbConta'],
						':iOperador' => $_POST['cmbOperador'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iCaixa' => $_POST['inputCaixaId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Caixa alterado!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar Caixa!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("caixa.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Caixa</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>	
	<!-- /theme JS files -->	
	
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script type="text/javascript" >

        $(document).ready(function() {
			
			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();
				
				var inputNomeNovo  = $('#inputNome').val();
				//var inputNomeVelho = $('#inputCaixaNome').val();
				var cmbConta   = $('#cmbConta').val();
				var cmbOperador   = $('#cmbOperador').val();
				var inputCaixaId = $('#inputCaixaId').val();
				
				//remove os espaços desnecessários antes e depois
				inputNomeNovo = inputNomeNovo.trim();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "caixaValida.php",
					data: {nome: inputNomeNovo, caixaId: inputCaixaId},
					success: function(resposta){
						console.log(resposta)
						if(resposta == 1){
							alerta('Atenção','Já existe um Caixa com este nome!!','error');
							return false;
						}
						
						$( "#formCaixa" ).submit();
					}
				})
			})
		})
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
					
					<form name="formCaixa" id="formCaixa" method="post" class="form-validate-jquery">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Editar Caixa "<?php echo $row['CaixaNome']; ?>"</h5>
						</div>
						
						<input type="hidden" id="inputCaixaId" name="inputCaixaId" value="<?php echo $row['CaixaId']; ?>">
						<input type="hidden" id="inputCaixaNome" name="inputCaixaNome" value="<?php echo $row['CaixaNome']; ?>">
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">
										<label for="inputNome">Caixa<span class="text-danger"> *</span></label>
										<input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Caixa" value="<?php echo $row['CaixaNome']; ?>" required autofocus>
									</div>
								</div>
								<div class="col-lg-4">
									<label for="cmbConta">Conta<span class="text-danger"> *</span></label>
									<select id="cmbConta" name="cmbConta" class="form-control form-control-select2" required>
										<option value="">Selecione</option>
										<?php 
											$sql ="SELECT CnBanId,CnBanNome
											       FROM ContaBanco
											       JOIN Situacao on SituaId = CnBanStatus
												   WHERE CnBanUnidade = ".$_SESSION['UnidadeId']." and SituaChave = 'ATIVO'
											       ORDER BY CnBanNome ASC";
											$result = $conn->query($sql);
											$rowConta = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($rowConta as $item){
												$seleciona = $item['CnBanId'] == $row['CaixaConta'] ? "selected" : "";
												print('<option value="'.$item['CnBanId'].'" '. $seleciona .'>'.$item['CnBanNome'].'</option>');
											}
										
										?>
									</select>
								</div>								
								<div class="col-lg-4">
									<label for="cmbOperador">Operador<span class="text-danger"> *</span></label>
									<select id="cmbOperador" name="cmbOperador" class="form-control form-control-select2" required>
										<option value="">Selecione</option>
										<?php 
											$sql = "SELECT UsuarId, UsuarNome
											        FROM Usuario
											        JOIN Situacao on SituaId = UsuarId
											        ORDER BY UsuarNome ASC";
											$result = $conn->query($sql);
											$rowOperador = $result->fetchAll(PDO::FETCH_ASSOC);
											
											foreach ($rowOperador as $item){
												$seleciona = $item['UsuarId'] == $row['CaixaOperador'] ? "selected" : "";
												print('<option value="'.$item['UsuarId'].'" '. $seleciona .'>'.$item['UsuarNome'].'</option>');
											}
										
										?>
									</select>
								</div>								
							</div>
								
							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
										<a href="caixa.php" class="btn btn-basic">Cancelar</a>
									</div>
								</div>
							</div>
						</form>								

					</div>
					<!-- /card-body -->
					
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
