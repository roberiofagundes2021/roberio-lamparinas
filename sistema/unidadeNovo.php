<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Unidade';

include('global_assets/php/conexao.php');

if(isset($_POST['inputNome'])){

	try{
		
		$conn->beginTransaction();

		$sql = "INSERT INTO Unidade (UnidaNome, UnidaCep, UnidaEndereco, UnidaNumero, UnidaComplemento, UnidaBairro, 
                      UnidaCidade, UnidaEstado, UnidaStatus, UnidaUsuarioAtualizador, UnidaEmpresa)
            VALUES (:sNome, :sCep, :sEndereco, :sNumero, :sComplemento, :sBairro, 
                :sCidade, :sEstado, :bStatus, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
      ':sNome' => $_POST['inputNome'],
      ':sCep' => $_POST['inputCep'],
      ':sEndereco' => $_POST['inputEndereco'],
      ':sNumero' => $_POST['inputNumero'],
      ':sComplemento' => $_POST['inputComplemento'],
      ':sBairro' => $_POST['inputBairro'],
      ':sCidade' => $_POST['inputCidade'],
      ':sEstado' => $_POST['cmbEstado'] == "" ? null : $_POST['cmbEstado'],
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iEmpresa' => $_SESSION['EmpresaId'],
    ));

    $unidadeIdNovo = $conn->lastInsertId();

    // criar novos perfis para a nova unidade
    $sqlPerfisPadrao = "SELECT PerfiId, PerfiNome, PerfiChave, PerfiStatus, SituaNome, SituaChave, SituaCor
                        FROM Perfil
                        JOIN Situacao on SituaId = PerfiStatus
                        WHERE PerfiUnidade is null and PerfiPadrao = 1 and SituaChave = 'ATIVO'";
    $sqlPerfisPadrao = $conn->query($sqlPerfisPadrao);
    $sqlPerfisPadrao = $sqlPerfisPadrao->fetchAll(PDO::FETCH_ASSOC);

    $usuaId = $_SESSION['UsuarId'];

    $sqlPerfil = "INSERT INTO Perfil(PerfiNome,PerfiChave,PerfiStatus,PerfiUsuarioAtualizador,PerfiUnidade,PerfiPadrao) VALUES ";

    foreach($sqlPerfisPadrao as $perfPadrao){
      $sqlPerfil .= " ('".$perfPadrao['PerfiNome']."','".$perfPadrao['PerfiChave']."',".$perfPadrao['PerfiStatus'].",".$usuaId.",".$unidadeIdNovo.",0),";
    }
    $sqlPerfil = substr_replace($sqlPerfil, "", -1);
    $conn->query($sqlPerfil);

    // inserir em PerfilXPermissao -------------------------------------------------------------------
    $sqlPerfilXPermissao = "INSERT INTO PerfilXPermissao (PrXPePerfil,PrXPeMenu,PrXPeUnidade,PrXPeInserir,PrXPeVisualizar,
    PrXPeAtualizar,PrXPeExcluir,PrXPeSuperAdmin) VALUES ";

    // inserir em PadraoPerfilXPermissao -------------------------------------------------------------
    $sqlPadraoPerfilXPermissao = "INSERT INTO PadraoPerfilXPermissao (PaPrXPePerfil, PaPrXPeMenu,PaPrXPeUnidade,PaPrXPeInserir,
    PaPrXPeVisualizar,PaPrXPeAtualizar,PaPrXPeExcluir,PaPrXPeSuperAdmin) VALUES ";

    $sql = "SELECT B.PerfiId as PerfiId, PaPerMenu, PaPerInserir, PaPerVisualizar, PaPerAtualizar, PaPerExcluir, PaPerSuperAdmin
            FROM Perfil A
            JOIN PadraoPermissao on PaPerPerfil = A.PerfiId
            JOIN Perfil B on B.PerfiChave = A.PerfiChave
            WHERE A.PerfiUnidade = ".$unidadeIdNovo;
    $result = $conn->query($sql);
    $rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);    

    $cont = 0;
    foreach ($rowPerfil as $itemPerfil){
        $sqlPerfilXPermissao .= " (".$itemPerfil['PerfiId'].", ".$itemPerfil['PaPerMenu'].", ".$unidadeIdNovo.", ".$itemPerfil['PaPerInserir'].",".
        $itemPerfil['PaPerVisualizar'].", ".$itemPerfil['PaPerAtualizar'].", ".$itemPerfil['PaPerExcluir'].", ".$itemPerfil['PaPerSuperAdmin']."),";
  
        $sqlPadraoPerfilXPermissao .= " (".$itemPerfil['PerfiId'].", ".$itemPerfil['PaPerMenu'].", ".$unidadeIdNovo.", ".$itemPerfil['PaPerInserir'].",".
        $itemPerfil['PaPerVisualizar'].", ".$itemPerfil['PaPerAtualizar'].", ".$itemPerfil['PaPerExcluir'].", ".$itemPerfil['PaPerSuperAdmin']."),";  

        $cont++;

        if ($cont > 800){

            // Insere na base para não atingir o limite de 1000 linhas por INSERT
            $sqlPerfilXPermissao = substr_replace($sqlPerfilXPermissao, "", -1);
            $sqlPadraoPerfilXPermissao = substr_replace($sqlPadraoPerfilXPermissao, "", -1);
            $conn->query($sqlPerfilXPermissao);
            $conn->query($sqlPadraoPerfilXPermissao);     
                    
            // recria o inserir em PerfilXPermissao -------------------------------------------------------------------
            $sqlPerfilXPermissao = "INSERT INTO PerfilXPermissao (PrXPePerfil,PrXPeMenu,PrXPeUnidade,PrXPeInserir,PrXPeVisualizar,
            PrXPeAtualizar,PrXPeExcluir,PrXPeSuperAdmin) VALUES ";

            // recria o inserir em PadraoPerfilXPermissao -------------------------------------------------------------
            $sqlPadraoPerfilXPermissao = "INSERT INTO PadraoPerfilXPermissao (PaPrXPePerfil, PaPrXPeMenu,PaPrXPeUnidade,PaPrXPeInserir,
            PaPrXPeVisualizar,PaPrXPeAtualizar,PaPrXPeExcluir,PaPrXPeSuperAdmin) VALUES ";            

            $cont = 0;
        }
    }

    $sqlPerfilXPermissao = substr_replace($sqlPerfilXPermissao, "", -1);
    $sqlPadraoPerfilXPermissao = substr_replace($sqlPadraoPerfilXPermissao, "", -1);
    $conn->query($sqlPerfilXPermissao);
    $conn->query($sqlPadraoPerfilXPermissao);

    // FIM---------------------------------------------------------------------------------------------
    
    /* Após criar a Unidade deve se cadastrar o Local de Estoque Padrão para essa Unidade nova criada */
    $sql = "INSERT INTO LocalEstoque (LcEstNome, LcEstChave, LcEstStatus, LcEstUsuarioAtualizador, LcEstUnidade)
					VALUES (:sNome, :sChave, :bStatus, :iUsuarioAtualizador, :iUnidade)";
    $result = $conn->prepare($sql);
        
    $result->execute(array(
      ':sNome' => 'GESTAO ANTERIOR',
      ':sChave' => 'GESTAOANTERIOR',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));

  
		/* Após criar a Unidade deve se cadastrar as Formas de Pagamento Padrão para essa Unidade nova criada */
		$sql = "INSERT INTO FormaPagamento (FrPagNome, FrPagChave, FrPagStatus, FrPagUsuarioAtualizador, FrPagUnidade)
				VALUES (:sNome, :sChave, :bStatus, :iUsuarioAtualizador, :iUnidade)";
		$result = $conn->prepare($sql);
				
		$result->execute(array(
      ':sNome' => 'Boleto Bancário',
      ':sChave' => 'BOLETO',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));
		$result->execute(array(
      ':sNome' => 'Cartão de Crédito',
      ':sChave' => 'CARTAOCREDITO',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));				
		$result->execute(array(
      ':sNome' => 'Cartão de Débito',
      ':sChave' => 'CARTAODEBITO',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));
		$result->execute(array(
      ':sNome' => 'Cheque',
      ':sChave' => 'CHEQUE',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));	
		$result->execute(array(
      ':sNome' => 'Dinheiro',
      ':sChave' => 'DINHEIRO',
      ':bStatus' => 1,
      ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
      ':iUnidade' => $unidadeIdNovo
    ));							
					
		$conn->commit();			

		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Unidade incluída!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao incluir unidade!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage();
    echo "<br>";
    echo "CONT: ".$cont;
    echo "<br><br>";
    echo $sqlPerfil;
    echo "<br><br>";
    echo $sql;
    echo "<br>";
    echo $sqlPerfilXPermissao;
    echo "<br>";
    echo $sqlPadraoPerfilXPermissao;
    die;
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

  <!-- Passo a passo -->
  <script src="global_assets/js/plugins/forms/wizards/steps.min.js"></script>
  <script src="global_assets/js/demo_pages/form_wizard.js"></script>

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

        $("#cmbEstado").removeClass("form-control-select2");

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
                //$("#cmbEstado").find('option[value="MA"]').attr('selected','selected');
                //$('#cmbEstado :selected').text();
                //$("#cmbEstado").find('option:selected').text();
                //document.getElementById("cmbEstado").options[5].selected = true;
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
      });    

      //Valida Registro Duplicado
      $('#enviar').on('click', function(e) {

        e.preventDefault();

        // subistitui qualquer espaço em branco no campo "CEP" antes de enviar para o banco
        var cep = $("#inputCep").val()
        cep = cep.replace(' ','')
        $("#inputCep").val(cep)

        let inputNome = $('#inputNome').val();
        let inputCep = $('#inputCep').val();
        let inputEndereco = $('#inputEndereco').val();
        let inputNumero = $('#inputNumero').val();
        let inputBairro = $('#inputBairro').val();
        let inputCidade = $('#inputCidade').val();
        let cmbEstado = $('#cmbEstado').val();

        //remove os espaços desnecessários antes e depois
        inputNome = inputNome.trim();

        //Verifica se o campo só possui espaços em branco
        if (!inputNome || !inputCep || !inputEndereco || !inputNumero || !inputBairro || !inputCidade || !cmbEstado) {
          $("#formUnidade").submit();
          $('#inputNome').focus();
          return false;
        }

        //Esse ajax está sendo usado para verificar no banco se o registro já existe
        $.ajax({
          type: "POST",
          url: "unidadeValida.php",
          data: ('nome=' + inputNome),
          success: function(resposta) {

            if (resposta == 1) {
              alerta('Atenção', 'Esse registro já existe!', 'error');
              return false;
            }

            $("#formUnidade").submit();
          }
        })
      })

      // Basic wizard setup
      $('.steps-basic-unidade').steps({
          headerTag: 'h6',
          bodyTag: 'fieldset',
          transitionEffect: 'fade',
          titleTemplate: '<span class="number">#index#</span> #title#',
          labels: {
              previous: '<i class="icon-arrow-left13 mr-2" /> Voltar',
              next: 'Avançar <i class="icon-arrow-right14 ml-2" />',
              finish: 'Enviar <i class="icon-arrow-right14 ml-2" />'
          },
          onStepChanging: function (event, currentIndex, newIndex) {
              
              if (currentIndex == 0){

                // subistitui qualquer espaço em branco no campo "CEP" antes de enviar para o banco
                var cep = $("#inputCep").val()
                cep = cep.replace(' ','')
                $("#inputCep").val(cep)

                let inputNome = $('#inputNome').val();
                let inputCep = $('#inputCep').val();
                let inputEndereco = $('#inputEndereco').val();
                let inputNumero = $('#inputNumero').val();
                let inputComplemento = $('#inputComplemento').val();
                let inputBairro = $('#inputBairro').val();
                let inputCidade = $('#inputCidade').val();
                let cmbEstado = $('#cmbEstado').val();
                var unidadeId = 0;

                //remove os espaços desnecessários antes e depois
                inputNome = inputNome.trim();

                //Verifica se o campo só possui espaços em branco
                if (!inputNome || !inputCep || !inputEndereco || !inputNumero || !inputBairro || !inputCidade || !cmbEstado) {
                  $("#formUnidade").submit();
                  $('#inputNome').focus();
                  console.log('Passou aqui')
                  return false;
                }
                
                //Esse ajax está sendo usado para gravar a unidade no banco
                $.ajax({
                  type: "POST",
                  url: "unidadeGrava.php",
                  data: {
                    nome: inputNome,
                    cep: inputCep,
                    endereco: inputEndereco,
                    numero: inputNumero,
                    complemento: inputComplemento,
                    bairro: inputBairro,
                    cidade: inputCidade,
                    estado: cmbEstado,
                    etapa: 1
                  },
                  success: async function(resposta) {
                   
                    unidadeId = resposta;
                  }
                })
                console.log(unidadeId)
                //return true;                
              }

              if (currentIndex == 1){
                alert(newIndex);
                console.log(currentIndex)
              }

              if (currentIndex == 2){
                alert(newIndex);
                console.log(currentIndex)
              }

              
              
          }/* ,
          onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ':disabled';
            return form.valid();
          },
          onFinished: function (event, currentIndex) {
              alert('Formumlário submetido.');
          } */
      });    
    })
  </script>

  <style>

      .passos {
          position: relative;
          display: block;
          width: 100%;
      }

      .passos>ul {
          display: table;
          width: 100%;
          table-layout: fixed;
          margin: 0;
          padding: 0;
          list-style: none;
      }

      .passos>ul>li {
          display: table-cell;
          width: auto;
          vertical-align: top;
          text-align: center;
          position: relative;
      }

      .passos>ul>li a {
          position: relative;
          padding-top: 3rem;
          margin-top: 1.25rem;
          margin-bottom: 1.25rem;
          display: block;
          outline: 0;
          color: #999;
      }
      
      .passos>ul>li:after, .passos>ul>li:before {
          content: '';
          display: block;
          position: absolute;
          top: 2.375rem;
          width: 50%;
          height: 2px;
          background-color: #00bcd4;
          z-index: 9;
      }   
      
      .passos>ul>li:before {
          left: 0;
      }

      .passos>ul>li:after {
          right: 0;
      }      

      /* Remove os traços antes e depois dos números */
      .passos>ul>li:first-child:before,.passos>ul>li:last-child:after{
          content:none
      }

      .passos>ul>li.current:after, .passos>ul>li.current~li:after, .passos>ul>li.current~li:before {
          background-color: #eee;
      }

      .passos>ul>li.current>a {
          color: #333;
          cursor: default;
      }      

      .passos>ul>li.current .number1{
          font-size: 0;
          border-color: #00bcd4;
          background-color: #fff;
          color: #00bcd4;
      }

      .passos>ul>li.current .number:after {
          content: '\e913';
          font-family: icomoon;
          display: inline-block;
          font-size: 1rem;
          line-height: 2.125rem;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          transition: all ease-in-out .15s;
      }  
      
      @media screen and (prefers-reduced-motion:reduce){
          .passos>ul>li.current .number1:after{
              transition:none
          }
      }  
      
      .passos>ul>li.disabled a{
          cursor:default
      }
      .passos>ul>li.done a,.passos>ul>li.done a:focus,.passos>ul>li.done a:hover{
          color:#999
      }
      .passos>ul>li.done .number1{
          font-size:0;
          background-color:#00bcd4;
          border-color:#00bcd4;
          color:#fff
      } 
      
      .passos>ul>li.done .number1:after{
          content:'\ed6f';
          font-family:icomoon;
          display:inline-block;
          font-size:1rem;
          line-height:2.125rem;
          -webkit-font-smoothing:antialiased;
          -moz-osx-font-smoothing:grayscale;
          transition:all ease-in-out .15s
      }
      @media screen and (prefers-reduced-motion:reduce){
          .passos>ul>li.done .number1:after{
              transition:none
          }
      }
      .passos>ul>li.error .number1{
          border-color:#f44336;
          color:#f44336
      }
      .card>.card-header:not([class*=bg-])>.passos>ul{
          border-top:1px solid rgba(0,0,0,.125)
      }
      @media (max-width:991.98px){
          .passos>ul{
              margin-bottom:1.25rem
          }
          .passos>ul>li{
              display:block;
              float:left;
              width:50%
          }
          .passos>ul>li>a{
              margin-bottom:0
          }
          .passos>ul>li:first-child:before,.passos>ul>li:last-child:after{
              content:''
          }
          .passos>ul>li:last-child:after{
              background-color:#00bcd4
          }
      }
      @media (max-width:767.98px){
          .passos>ul>li{
              width:100%
          }
          .passos>ul>li.current:after{
              background-color:#00bcd4
          }
      }      

      .passos .number1 {
          background-color: #fff;
          color: #ccc;
          display: inline-block;
          position: absolute;
          top: 0;
          left: 50%;
          margin-left: -1.1875rem;
          border: 2px solid #eee;
          font-size: .875rem;
          z-index: 10;
          line-height: 2.125rem;
          text-align: center;
          width: 2.375rem;
          height: 2.375rem;
          border-radius: 50%;
      }

      .number1:after {
          content: '\e913';
          font-family: icomoon;
          display: inline-block;
          font-size: 1rem;
          line-height: 2.125rem;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          transition: all ease-in-out .15s;
      }

      .passos .current-info {
          display: none;
      }

  </style>  

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

          <div class="card-header bg-white header-elements-inline">
						<h6 class="card-title text-uppercase font-weight-bold">Cadastrar Nova Unidade</h6>
					</div>

          <form name="formUnidade" id="formUnidade" method="post" class="form-validate-jquery wizard-form ">

          <div class="passos">
            
            <ul role="tablist">
              <li role="tab" class="first current" aria-disabled="false" aria-selected="true">
                <a id="formUnidade-t-0" href="#formUnidade-h-0" aria-controls="formUnidade-p-0" class="">
                  <span class="current-info audible">current step: </span>
                  <span class="number1">1</span> Passo 1
                </a>
              </li>

              <li role="tab" class="disabled" aria-disabled="true">
                <a id="formUnidade-t-1" href="#formUnidade-h-1" aria-controls="formUnidade-p-1" class="disabled">
                  <span class="number1">2</span> Passo2
                </a>
              </li>
              <li role="tab" class="disabled last" aria-disabled="true">
                <a id="formUnidade-t-2" href="#formUnidade-h-2" aria-controls="formUnidade-p-2" class="disabled">
                  <span class="number1">3</span> Passo 3
                </a>
              </li>
            </ul>
          </div>

            <!--<h6>Passo 1</h6>-->
						<fieldset>
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="inputNome">Nome da Unidade <span class='text-danger'>*</span></label>
                      <input type="text" id="inputNome" name="inputNome" class="form-control" placeholder="Unidade" required autofocus>
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
                          <input type="text" id="inputCep" name="inputCep" class="form-control" placeholder="CEP" maxLength="8" required>
                        </div>
                      </div>

                      <div class="col-lg-5">
                        <div class="form-group">
                          <label for="inputEndereco">Endereço <span class='text-danger'>*</span></label>
                          <input type="text" id="inputEndereco" name="inputEndereco" class="form-control" placeholder="Endereço" required>
                        </div>
                      </div>

                      <div class="col-lg-1">
                        <div class="form-group">
                          <label for="inputNumero">Nº <span class='text-danger'>*</span></label>
                          <input type="text" id="inputNumero" name="inputNumero" class="form-control" placeholder="Número" required>
                        </div>
                      </div>

                      <div class="col-lg-5">
                        <div class="form-group">
                          <label for="inputComplemento">Complemento</label>
                          <input type="text" id="inputComplemento" name="inputComplemento" class="form-control" placeholder="complemento">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label for="inputBairro">Bairro <span class='text-danger'>*</span></label>
                          <input type="text" id="inputBairro" name="inputBairro" class="form-control" placeholder="Bairro" required>
                        </div>
                      </div>

                      <div class="col-lg-5">
                        <div class="form-group">
                          <label for="inputCidade">Cidade <span class='text-danger'>*</span></label>
                          <input type="text" id="inputCidade" name="inputCidade" class="form-control" placeholder="Cidade" required>
                        </div>
                      </div>

                      <div class="col-lg-3">
                        <div class="form-group">
                          <label for="cmbEstado">Estado <span class='text-danger'>*</span></label>
                          <select id="cmbEstado" name="cmbEstado" class="form-control" required>
                            <!-- retirei isso da class: form-control-select2 para que funcionasse a seleção do texto do estado, além do valor -->
                            <option value="">Selecione um estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                            <option value="ES">Estrangeiro</option>
                          </select>
                        </div>
                      </div>
                    </div> <!-- row -->
                  </div> <!-- col-lg-12 -->
                </div> <!-- row -->

                <div class="row" style="margin-top: 10px;">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <button class="btn btn-lg btn-principal" id="enviar">Incluir</button>
                      <a href="unidade.php" class="btn btn-basic" role="button">Cancelar</a>
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
<!--
            <h6>Passo2</h6>
						<fieldset>
              Teste2
            </fieldset>

            <h6>Passo3</h6>
						<fieldset>
              Teste3
            </fieldset>
    -->           
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