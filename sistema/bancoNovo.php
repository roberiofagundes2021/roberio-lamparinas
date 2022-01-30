<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Novo Banco';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome']) && isset($_POST['inputCodigo'])){

	try{
		
		$sql = "INSERT INTO Banco (BancoCodigo, BancoNome, BancoStatus, BancoUsuarioAtualizador)
				    VALUES (:sCodigo, :sNome, :bStatus, :iUsuarioAtualizador)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sCodigo' => $_POST['inputCodigo'],
						':sNome' => $_POST['inputNome'],
						':bStatus' => intval(1),
						':iUsuarioAtualizador' => intval($_SESSION['UsuarId'])
						));
						
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Banco incluído!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir banco!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("banco.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Lamparinas | Banco</title>

  <?php include_once("head.php"); ?>

  <!-- Validação -->
  <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
  <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
  <script src="global_assets/js/demo_pages/form_validation.js"></script>


  <script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>

  <script type="text/javascript">
  $(document).ready(function() {

    //Valida Registro Duplicado
    $('#enviar').on('click', function(e) {

      e.preventDefault();

      var inputCodigo = $('#inputCodigo').val();
      var inputNome = $('#inputNome').val();

      //remove os espaços desnecessários antes e depois
      inputCodigo = inputCodigo.trim();
      inputNome = inputNome.trim();

      if (inputCodigo == '' || inputNome == '') {
        $("#formBanco").submit();
      }

      //Esse ajax está sendo usado para verificar no banco se o registro já existe
      $.ajax({
        type: "POST",
        url: "bancoValida.php",
        data: {
          codigo: inputCodigo,
          nome: inputNome
        },
        success: function(resposta) {
          if (resposta == '1') {
            alerta('Atenção', 'Esse registro já existe (código ou nome)!', 'error');
            return false;
          } else {
            $("#formBanco").submit();
          }
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

          <form name="formBanco" id="formBanco" method="post" class="form-validate-jquery">
            <div class="card-header header-elements-inline">
              <h5 class="text-uppercase font-weight-bold">Cadastrar Novo Banco</h5>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-lg-2">
                  <div class="form-group">
                    <label for="inputCodigo">Código da Banco <span class='text-danger'>*</span></label>
                    <input type="text" id="inputCodigo" name="inputCodigo" class="form-control" placeholder="Código" maxlength="3" required autofocus>
                  </div>
                </div>
                <div class="col-lg-10">
                  <div class="form-group">
                    <label for="inputNome">Nome do Banco<span class='text-danger'>*</span></label>
                    <input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Nome" required>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12">
                  <div class="form-group">
                    <button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
                    <a href="banco.php" class="btn btn-basic" role="button">Cancelar</a>
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