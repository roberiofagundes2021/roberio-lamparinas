<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Editar Unidade';

include('global_assets/php/conexao.php');

if(isset($_POST['inputUnidadeId'])){
	
	$iUnidade = $_POST['inputUnidadeId'];
        	
	try{
		
		$sql = "SELECT *
				FROM Unidade
				WHERE UnidaId = $iUnidade ";
		$result = $conn->query("$sql");
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$_SESSION['msg'] = array();
} else {  //Esse else foi criado para se caso o usuário der um REFRESH na página. Nesse caso não terá POST e campos não reconhecerão o $row da consulta acima (daí ele deve ser redirecionado) e se quiser continuar editando terá que clicar no ícone da Grid novamente

	irpara("unidade.php");
}

if(isset($_POST['inputNome'])){
	
	try{
		
		$sql = "UPDATE Unidade SET UnidaNome = :sNome, UnidaCep = :sCep, UnidaEndereco = :sEndereco, UnidaNumero = :sNumero, 
								   UnidaComplemento = :sComplemento, UnidaBairro = :sBairro, UnidaCidade = :sCidade, 
								   UnidaEstado = :sEstado, UnidaUsuarioAtualizador = :iUsuarioAtualizador
				WHERE UnidaId = :iUnidade";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
						':sNome' => $_POST['inputNome'],
						':sCep' => trim($_POST['inputCep']) == "" ? null : $_POST['inputCep'],
						':sEndereco' => $_POST['inputEndereco'],
						':sNumero' => $_POST['inputNumero'],
						':sComplemento' => $_POST['inputComplemento'],
						':sBairro' => $_POST['inputBairro'],
						':sCidade' => $_POST['inputCidade'],
						':sEstado' => $_POST['cmbEstado'] == "" ? null : $_POST['cmbEstado'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iUnidade' => $_POST['inputUnidadeId']
						));

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade alterada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao alterar unidade!!!";
		$_SESSION['msg']['tipo'] = "error";		
		
		echo 'Error: ' . $e->getMessage();
	}
	
	irpara("unidade.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Lamparinas | Unidade</title>

  <?php include_once("head.php"); ?>

  <!-- Validação -->
  <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
  <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
  <script src="global_assets/js/demo_pages/form_validation.js"></script>

  <script type="text/javascript">
  $(document).ready(function() {

    function limpa_formulário_cep() {
      // Limpa valores do formulário de cep.
      $("#inputEndereco").val("");
      $("#inputBairro").val("");
      $("#inputCidade").val("");
      $("#cmbEstado").val("");
    }

    //Quando o campo cep perde o foco.
    $("#inputCep").blur(function() {

      //Nova variável "cep" somente com dígitos.
      var cep = $(this).val().replace(/\D/g, '');

      //Verifica se campo cep possui valor informado.
      if (cep != "") {

        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if (validacep.test(cep)) {

          //Preenche os campos com "..." enquanto consulta webservice.
          $("#inputEndereco").val("...");
          $("#inputBairro").val("...");
          $("#inputCidade").val("...");
          $("#cmbEstado").val("...");

          //Consulta o webservice viacep.com.br/
          $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

            if (!("erro" in dados)) {

              //Atualiza os campos com os valores da consulta.
              $("#inputEndereco").val(dados.logradouro);
              $("#inputBairro").val(dados.bairro);
              $("#inputCidade").val(dados.localidade);
              $("#cmbEstado").val(dados.uf);
              $("#cmbEstado").find('option:selected').text();
            } //end if.
            else {
              //CEP pesquisado não foi encontrado.
              limpa_formulário_cep();
              alerta("Erro", "CEP não encontrado.", "erro");
            }
          });
        } //end if.
        else {
          //cep é inválido.
          limpa_formulário_cep();
          alerta("Erro", "Formato de CEP inválido.", "erro");
        }
      } //end if.
      else {
        //cep sem valor, limpa formulário.
        limpa_formulário_cep();
      }
    }); //cep

    //Valida Registro Duplicado
    $('#enviar').on('click', function(e) {

      e.preventDefault();

      let inputNomeNovo = $('#inputNome').val();
      let inputNomeVelho = $('#inputUnidadeNome').val();
      let inputCep = $('#inputCep').val();
      let inputEndereco = $('#inputEndereco').val();
      let inputNumero = $('#inputNumero').val();
      let inputBairro = $('#inputBairro').val();
      let inputCidade = $('#inputCidade').val();
      let cmbEstado = $('#cmbEstado').val();

      //remove os espaços desnecessários antes e depois
      inputNomeNovo = inputNomeNovo.trim();

      //Verifica se o campo só possui espaços em branco
      if (!inputNomeNovo || !inputCep || !inputEndereco || !inputNumero || !inputBairro || !inputCidade || !cmbEstado) {
        $("#formUnidade").submit();
        $('#inputNome').focus();
        return false;
      }

      //Esse ajax está sendo usado para verificar no banco se o registro já existe
      $.ajax({
        type: "POST",
        url: "unidadeValida.php",
        data: ('nomeNovo=' + inputNomeNovo + '&nomeVelho=' + inputNomeVelho),
        success: function(resposta) {

          if (resposta == 1) {
            alerta('Atenção', 'Esse registro já existe!', 'error');
            return false;
          }

          $("#formUnidade").submit();
        }
      })

    })
  })
  </script>

</head>

<body class="navbar-top sidebar-xs">

  <?php include_once("topo.php"); ?>

  <!-- Page content -->
  <div class="page-content">

    <?php include_once("menu-left.php"); ?>

    <?php include_once("menuLeftSecundario.php"); ?>

    <!-- Main content -->
    <div class="content-wrapper">

      <?php include_once("cabecalho.php"); ?>

      <!-- Content area -->
      <div class="content">

        <!-- Info blocks -->
        <div class="card">

          <form name="formUnidade" id="formUnidade" method="post" class="form-validate-jquery">
            <div class="card-header header-elements-inline">
              <h5 class="text-uppercase font-weight-bold">Editar Unidade "<?php echo $row['UnidaNome']; ?>"</h5>
            </div>

            <input type="hidden" id="inputUnidadeId" name="inputUnidadeId" value="<?php echo $row['UnidaId']; ?>">
            <input type="hidden" id="inputUnidadeNome" name="inputUnidadeNome" value="<?php echo $row['UnidaNome']; ?>">

            <div class="card-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="inputNome">Nome da Unidade <span class='text-danger'>*</span></label>
                    <input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Unidade" value="<?php echo $row['UnidaNome']; ?>" required autofocus>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12">
                  <h5 class="mb-0 font-weight-semibold">Endereço</h5>
                  <br>
                  <div class="row">
                    <div class="col-lg-1">
                      <div class="form-group">
                        <label for="inputCep">CEP <span class='text-danger'>*</span></label>
                        <input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" value="<?php echo $row['UnidaCep']; ?>" maxLength="8" required>
                      </div>
                    </div>

                    <div class="col-lg-5">
                      <div class="form-group">
                        <label for="inputEndereco">Endereço <span class='text-danger'>*</span></label>
                        <input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço" value="<?php echo $row['UnidaEndereco']; ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-1">
                      <div class="form-group">
                        <label for="inputNumero">Nº <span class='text-danger'>*</span></label>
                        <input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" value="<?php echo $row['UnidaNumero']; ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-5">
                      <div class="form-group">
                        <label for="inputComplemento">Complemento</label>
                        <input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento" value="<?php echo $row['UnidaComplemento']; ?>">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="inputBairro">Bairro <span class='text-danger'>*</span></label>
                        <input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro" value="<?php echo $row['UnidaBairro']; ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-5">
                      <div class="form-group">
                        <label for="inputCidade">Cidade <span class='text-danger'>*</span></label>
                        <input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade" value="<?php echo $row['UnidaCidade']; ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="cmbEstado">Estado <span class='text-danger'>*</span></label>
                        <select id="cmbEstado" name="cmbEstado" class="form-control form-control-select2" required>
                          <option value="">Selecione um estado</option>
                          <option value="AC" <?php if ($row['UnidaEstado'] == 'AC') echo "selected"; ?>>Acre</option>
                          <option value="AL" <?php if ($row['UnidaEstado'] == 'AL') echo "selected"; ?>>Alagoas</option>
                          <option value="AP" <?php if ($row['UnidaEstado'] == 'AP') echo "selected"; ?>>Amapá</option>
                          <option value="AM" <?php if ($row['UnidaEstado'] == 'AM') echo "selected"; ?>>Amazonas</option>
                          <option value="BA" <?php if ($row['UnidaEstado'] == 'BA') echo "selected"; ?>>Bahia</option>
                          <option value="CE" <?php if ($row['UnidaEstado'] == 'CE') echo "selected"; ?>>Ceará</option>
                          <option value="DF" <?php if ($row['UnidaEstado'] == 'DF') echo "selected"; ?>>Distrito Federal</option>
                          <option value="ES" <?php if ($row['UnidaEstado'] == 'ES') echo "selected"; ?>>Espírito Santo</option>
                          <option value="GO" <?php if ($row['UnidaEstado'] == 'GO') echo "selected"; ?>>Goiás</option>
                          <option value="MA" <?php if ($row['UnidaEstado'] == 'MA') echo "selected"; ?>>Maranhão</option>
                          <option value="MT" <?php if ($row['UnidaEstado'] == 'MT') echo "selected"; ?>>Mato Grosso</option>
                          <option value="MS" <?php if ($row['UnidaEstado'] == 'MS') echo "selected"; ?>>Mato Grosso do Sul</option>
                          <option value="MG" <?php if ($row['UnidaEstado'] == 'MG') echo "selected"; ?>>Minas Gerais</option>
                          <option value="PA" <?php if ($row['UnidaEstado'] == 'PA') echo "selected"; ?>>Pará</option>
                          <option value="PB" <?php if ($row['UnidaEstado'] == 'PB') echo "selected"; ?>>Paraíba</option>
                          <option value="PR" <?php if ($row['UnidaEstado'] == 'PR') echo "selected"; ?>>Paraná</option>
                          <option value="PE" <?php if ($row['UnidaEstado'] == 'PE') echo "selected"; ?>>Pernambuco</option>
                          <option value="PI" <?php if ($row['UnidaEstado'] == 'PI') echo "selected"; ?>>Piauí</option>
                          <option value="RJ" <?php if ($row['UnidaEstado'] == 'RJ') echo "selected"; ?>>Rio de Janeiro</option>
                          <option value="RN" <?php if ($row['UnidaEstado'] == 'RN') echo "selected"; ?>>Rio Grande do Norte</option>
                          <option value="RS" <?php if ($row['UnidaEstado'] == 'RS') echo "selected"; ?>>Rio Grande do Sul</option>
                          <option value="RO" <?php if ($row['UnidaEstado'] == 'RO') echo "selected"; ?>>Rondônia</option>
                          <option value="RR" <?php if ($row['UnidaEstado'] == 'RR') echo "selected"; ?>>Roraima</option>
                          <option value="SC" <?php if ($row['UnidaEstado'] == 'SC') echo "selected"; ?>>Santa Catarina</option>
                          <option value="SP" <?php if ($row['UnidaEstado'] == 'SP') echo "selected"; ?>>São Paulo</option>
                          <option value="SE" <?php if ($row['UnidaEstado'] == 'SE') echo "selected"; ?>>Sergipe</option>
                          <option value="TO" <?php if ($row['UnidaEstado'] == 'TO') echo "selected"; ?>>Tocantins</option>
                          <option value="ES" <?php if ($row['UnidaEstado'] == 'ES') echo "selected"; ?>>Estrangeiro</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <br>

              <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12">
                  <div class="form-group">
                    <button class="btn btn-lg btn-principal" id="enviar">Alterar</button>
                    <a href="unidade.php" class="btn btn-basic" role="button">Cancelar</a>
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