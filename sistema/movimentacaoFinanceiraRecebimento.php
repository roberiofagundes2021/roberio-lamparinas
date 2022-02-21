<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Lançamento - Contas a Receber';

include('global_assets/php/conexao.php');

if (isset($_POST['inputDataEmissao'])) {

    if (isset($_POST['cmbFormaDePagamento'])){
        $aFormaPagamento = explode('#', $_POST['cmbFormaDePagamento']);                                
        $idFormaPagamento = $aFormaPagamento[0];
    }
    
    if (isset($_POST['inputEditar'])) {
      try {

        $sql = "SELECT SituaId
                FROM Situacao
                WHERE SituaChave = 'RECEBIDA'
            ";
        $result = $conn->query($sql);
        $situacao = $result->fetch(PDO::FETCH_ASSOC);

        try {
          $sql = "UPDATE ContasAReceber SET       CnAReDtEmissao                    = :dDtEmissao,
                                                  CnARePlanoContas                  = :iPlanoContas,  
                                                  CnAReCliente                      = :iCliente, 
                                                  CnAReDescricao                    = :sDescricao,
                                                  CnAReNumDocumento                 = :sNumDocumento,
                                                  CnAReContaBanco                   = :iContaBanco, 
                                                  CnAReFormaPagamento               = :iFormaPagamento,
                                                  CnAReVenda                        = :iVenda,
                                                  CnAReDtVencimento                 = :dDtVencimento,
                                                  CnAReValorAReceber                = :fValorAReceber, 
                                                  CnAReDtRecebimento                = :dDtRecebimento,
                                                  CnAReValorRecebido                = :fValorRecebido,
                                                  CnAReTipoJuros                    = :sTipoJuros, 
                                                  CnAReJuros                        = :fJuros, 
                                                  CnAReTipoDesconto                 = :sTipoDesconto, 
                                                  CnAReDesconto                     = :fDesconto,  
                                                  CnAReObservacao                   = :sObservacao, 
                                                  CnAReNumCheque                    = :sNumCheque,
                                                  CnAReValorCheque                  = :fValorCheque,
                                                  CnAReDtEmissaoCheque              = :dDtEmissaoCheque,
                                                  CnAReDtVencimentoCheque           = :dDtVencimentoCheque,
                                                  CnAReBancoCheque                  = :iBancoCheque,
                                                  CnAReAgenciaCheque                = :iAgenciaCheque,
                                                  CnAReContaCheque                  = :iContaCheque,
                                                  CnAReNomeCheque                   = :iNomeCheque,
                                                  CnAReCpfCheque                    = :iCpfCheque,
                                                  CnAReStatus                       = :iStatus, 
                                                  CnAReUsuarioAtualizador           = :iUsuarioAtualizador, 
                                                  CnAReUnidade                      = :iUnidade
                  WHERE CnAReId = " . $_POST['inputContaId'] . "";

          $result = $conn->prepare($sql);
          $result->execute(array(
            ':dDtEmissao'           => isset($_POST['inputDataEmissao']) ? $_POST['inputDataEmissao'] : null,
            ':iPlanoContas'         => isset($_POST['cmbPlanoContas']) ? intval($_POST['cmbPlanoContas']) : null,
            ':iCliente'             => isset($_POST['cmbCliente']) ? intval($_POST['cmbCliente']) : 0,
            ':sDescricao'           => $_POST['inputDescricao'],
            ':sNumDocumento'        => isset($_POST['inputNumeroDocumento']) ? $_POST['inputNumeroDocumento'] : null,
            ':iContaBanco'          => isset($_POST['cmbContaBanco']) ? intval($_POST['cmbContaBanco']) : null,
            ':iFormaPagamento'      => isset($idFormaPagamento) ? $idFormaPagamento : null,
            ':iVenda'               =>  null,
            ':dDtVencimento'        => $_POST['inputDataRecebimento'],
            ':fValorAReceber'       => floatval(gravaValor($_POST['inputValorTotal'])),
            ':dDtRecebimento'       => isset($_POST['inputDataRecebimento']) ? $_POST['inputDataRecebimento'] : null,
            ':fValorRecebido'       => isset($_POST['inputValorTotal']) ? floatval(gravaValor($_POST['inputValorTotal'])) : null,
            ':sTipoJuros'           => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
            ':fJuros'               => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
            ':sTipoDesconto'        => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
            ':fDesconto'            => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
            ':sObservacao'          => isset($_POST['inputObservacao']) ? $_POST['inputObservacao'] : null,
            ':sNumCheque'           => isset($_POST['inputNumCheque']) ? $_POST['inputNumCheque'] : null,
            ':fValorCheque'         => isset($_POST['inputValorCheque']) ? floatval(gravaValor($_POST['inputValorCheque'])) : null,
            ':dDtEmissaoCheque'     => isset($_POST['inputDtEmissaoCheque']) ? $_POST['inputDtEmissaoCheque'] : null,
            ':dDtVencimentoCheque'  => isset($_POST['inputDtVencimentoCheque']) ? $_POST['inputDtVencimentoCheque'] : null,
            ':iBancoCheque'         => isset($_POST['cmbBancoCheque']) ? intval($_POST['cmbBancoCheque']) : null,
            ':iAgenciaCheque'       => isset($_POST['inputAgenciaCheque']) ? $_POST['inputAgenciaCheque'] : null,
            ':iContaCheque'         => isset($_POST['inputContaCheque']) ? $_POST['inputContaCheque'] : null,
            ':iNomeCheque'          => isset($_POST['inputNomeCheque']) ? $_POST['inputNomeCheque'] : null,
            ':iCpfCheque'           => isset($_POST['inputCpfCheque']) ? $_POST['inputCpfCheque'] : null,   
            ':iStatus'              => intval($situacao['SituaId']),
            ':iUsuarioAtualizador'  => intval($_SESSION['UsuarId']),
            ':iUnidade'             => intval($_SESSION['UnidadeId'])
          ));
        } catch (Exception $e) {
            echo 'Error: ',  $e->getMessage(), "\n";
            die;
        }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Lançamento editado!!!";
        $_SESSION['msg']['tipo'] = "success";

      } catch (PDOException $e) {
        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro ao editar lançamento!!!";
        $_SESSION['msg']['tipo'] = "error";

        echo 'Error: ' . $e->getMessage();
        die;
      }
    } else {
      try {
          try {
              $sql = "SELECT SituaId
                        FROM Situacao
                        WHERE SituaChave = 'RECEBIDA'
              ";

              $result = $conn->query($sql);
              $situacao = $result->fetch(PDO::FETCH_ASSOC);

          } catch (Exception $e) {
              echo 'Error: ',  $e->getMessage(), "\n";
          }

          try {
            $sql = "INSERT INTO ContasAReceber ( CnAReDtEmissao,
                                                  CnARePlanoContas, 
                                                  CnAReCliente,
                                                  CnAReDescricao,
                                                  CnAReNumDocumento,  
                                                  CnAReContaBanco,                   
                                                  CnAReFormaPagamento,
                                                  CnAReVenda,                              
                                                  CnAReDtVencimento, 
                                                  CnAReValorAReceber,
                                                  CnAReDtRecebimento, 
                                                  CnAReValorRecebido,
                                                  CnAReTipoJuros, 
                                                  CnAReJuros,
                                                  CnAReTipoDesconto, 
                                                  CnAReDesconto, 
                                                  CnAReObservacao, 
                                                  CnAReNumCheque,                    
                                                  CnAReValorCheque,                  
                                                  CnAReDtEmissaoCheque,             
                                                  CnAReDtVencimentoCheque,          
                                                  CnAReBancoCheque,                 
                                                  CnAReAgenciaCheque,                
                                                  CnAReContaCheque,                  
                                                  CnAReNomeCheque,                  
                                                  CnAReCpfCheque,          
                                                  CnAReStatus, 
                                                  CnAReUsuarioAtualizador, 
                                                  CnAReUnidade)
                VALUES ( :dDtEmissao, 
                          :iPlanoContas, 
                          :iCliente,
                          :sDescricao,
                          :sNumDocumento, 
                          :iContaBanco, 
                          :iFormaPagamento,
                          :iVenda,
                          :dDtVencimento, 
                          :fValorAReceber, 
                          :dDtRecebimento, 
                          :fValorRecebido,
                          :sTipoJuros, 
                          :fJuros, 
                          :sTipoDesconto, 
                          :fDesconto,  
                          :sObservacao,
                          :sNumCheque,
                          :fValorCheque,
                          :dDtEmissaoCheque,
                          :dDtVencimentoCheque,
                          :iBancoCheque,
                          :iAgenciaCheque,
                          :iContaCheque,
                          :iNomeCheque,
                          :iCpfCheque, 
                          :iStatus, 
                          :iUsuarioAtualizador, 
                          :iUnidade)";


            $result = $conn->prepare($sql);
            $result->execute(array(
                ':dDtEmissao'           => isset($_POST['inputDataEmissao']) ? $_POST['inputDataEmissao'] : null,
                ':iPlanoContas'         => isset($_POST['cmbPlanoContas']) ? intval($_POST['cmbPlanoContas']) : null,
                ':iCliente'             => 0,
                ':sDescricao'           => $_POST['inputDescricao'],
                ':sNumDocumento'        => isset($_POST['inputNumeroDocumento']) ? $_POST['inputNumeroDocumento'] : null,
                ':iContaBanco'          => isset($_POST['cmbContaBanco']) ? intval($_POST['cmbContaBanco']) : null,
                ':iFormaPagamento'      => isset($idFormaPagamento) ? $idFormaPagamento : null,
                ':iVenda'               =>  null,
                ':dDtVencimento'        => $_POST['inputDataRecebimento'],
                ':fValorAReceber'       => floatval(gravaValor($_POST['inputValorTotal'])),
                ':dDtRecebimento'       => isset($_POST['inputDataRecebimento']) ? $_POST['inputDataRecebimento'] : null,
                ':fValorRecebido'       => isset($_POST['inputValorTotal']) ? floatval(gravaValor($_POST['inputValorTotal'])) : null,
                ':sTipoJuros'           => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                ':fJuros'               => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
                ':sTipoDesconto'        => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                ':fDesconto'            => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
                ':sObservacao'          => isset($_POST['inputObservacao']) ? $_POST['inputObservacao'] : null,
                ':sNumCheque'           => isset($_POST['inputNumCheque']) ? $_POST['inputNumCheque'] : null,
                ':fValorCheque'         => isset($_POST['inputValorCheque']) ? floatval(gravaValor($_POST['inputValorCheque'])) : null,
                ':dDtEmissaoCheque'     => isset($_POST['inputDtEmissaoCheque']) ? $_POST['inputDtEmissaoCheque'] : null,
                ':dDtVencimentoCheque'  => isset($_POST['inputDtVencimentoCheque']) ? $_POST['inputDtVencimentoCheque'] : null,
                ':iBancoCheque'         => isset($_POST['cmbBancoCheque']) ? intval($_POST['cmbBancoCheque']) : null,
                ':iAgenciaCheque'       => isset($_POST['inputAgenciaCheque']) ? $_POST['inputAgenciaCheque'] : null,
                ':iContaCheque'         => isset($_POST['inputContaCheque']) ? $_POST['inputContaCheque'] : null,
                ':iNomeCheque'          => isset($_POST['inputNomeCheque']) ? $_POST['inputNomeCheque'] : null,
                ':iCpfCheque'           => isset($_POST['inputCpfCheque']) ? $_POST['inputCpfCheque'] : null,   
                ':iStatus'              => intval($situacao['SituaId']),
                ':iUsuarioAtualizador'  => intval($_SESSION['UsuarId']),
                ':iUnidade'             => intval($_SESSION['UnidadeId'])
            ));
          } catch (Exception $e) {
              echo 'Error: ',  $e->getMessage(), "\n";
              die;
          }

        $_SESSION['msg']['titulo'] = "Sucesso";
        $_SESSION['msg']['mensagem'] = "Lançamento incluído!!!";
        $_SESSION['msg']['tipo'] = "success";

      } catch (PDOException $e) {

        $_SESSION['msg']['titulo'] = "Erro";
        $_SESSION['msg']['mensagem'] = "Erro ao incluir Lançamento!!!";
        $_SESSION['msg']['tipo'] = "error";
        echo 'Error: ' . $e->getMessage();
        die;
      }
    } 
  irpara("movimentacaoFinanceira.php");
}

// SE TIVER EDITANDO 
if (isset($_POST['inputMovimentacaoFinanceiraId'])) {
    try {
        $sql = "SELECT  CnAReId,
                        CnAReDtEmissao,  
                        CnARePlanoContas, 
                        CnAReCliente, 
                        CnAReDescricao, 
                        CnAReNumDocumento,
                        CnAReContaBanco, 
                        CnAReFormaPagamento,
                        CnAReVenda,
                        CnAReDtVencimento, 
                        CnAReValorAReceber, 
                        CnAReDtRecebimento, 
                        CnAReValorRecebido, 
                        CnAReTipoJuros, 
                        CnAReJuros, 
                        CnAReTipoDesconto, 
                        CnAReDesconto, 
                        CnAReObservacao,
                        CnAReNumCheque,                    
                        CnAReValorCheque,                  
                        CnAReDtEmissaoCheque,             
                        CnAReDtVencimentoCheque,           
                        CnAReBancoCheque,                 
                        CnAReAgenciaCheque,                
                        CnAReContaCheque,                  
                        CnAReNomeCheque,                  
                        CnAReCpfCheque, 
                        CnAReStatus, 
                        CnAReUsuarioAtualizador, 
                        CnAReUnidade,
                        FrPagChave            
    		       FROM ContasAReceber
                   LEFT JOIN FormaPagamento on FrPagId = CnAReFormaPagamento
    		       WHERE CnAReUnidade = " . $_SESSION['UnidadeId'] . " and CnAReId = " . $_POST['inputMovimentacaoFinanceiraId'] . "";

        $result = $conn->query($sql);
        $lancamento = $result->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
    }
}

$dataInicio = date("Y-m-d");
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Lamparinas | Lançar Contas</title>

  <?php include_once("head.php"); ?>

  <!-- Validação -->
  <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
  <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
  <script src="global_assets/js/demo_pages/form_validation.js"></script>
  <!--/ Validação -->

  <!-- Theme JS files -->
  <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
  <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

  <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
  <script src="global_assets/js/demo_pages/form_layouts.js"></script>
  <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

  <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
  <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
  <!-- /theme JS files -->

  <script type="text/javascript">
  $(document).ready(function() {

    var input = document.getElementById('inputDataRecebimento');
    input.addEventListener('change', function() {
        var agora = new Date();
        var escolhida = new Date(this.value);
        if (escolhida < agora) {
            this.value = [agora.getFullYear(), agora.getMonth() + 1, agora.getDate()].map(v => v < 10 ? '0' + v : v).join('-');
        }
    });  

    function preencheDatas() {
      $dataRecebimento = new Date
      $dia = parseInt($dataRecebimento.getDate()) <= 9 ? `0${parseInt($dataRecebimento.getDate())}` : parseInt($dataRecebimento.getDate());
      $mes = parseInt($dataRecebimento.getMonth()) + 1 <= 9 ? `0${parseInt($dataRecebimento.getMonth()) + 1}` : parseInt($dataRecebimento.getMonth()) + 1;
      $ano = $dataRecebimento.getFullYear();

      $fullDataRecebimento = `${$ano}-${$mes}-${$dia}`;

      if ($('#inputDataEmissao').val() == "")
        $('#inputDataEmissao').val($fullDataRecebimento);
      if ($('#inputDataRecebimento').val() == "")
        $('#inputDataRecebimento').val($fullDataRecebimento);
    }


    $('#cmbFormaDePagamento').on('change', function(e) {
      verificaFormaPagamento();
    });

    function verificaFormaPagamento() {
      let filhos = $('#cmbFormaDePagamento').children();
      let valorcmb = $('#cmbFormaDePagamento').val();

      filhos.each((i, elem) => {
        let formaPagamento = $(elem).attr('chaveformaPagamento');
        let valOption = $(elem).attr('value');

        if (valOption == valorcmb) {
          if (formaPagamento == 'CHEQUE') {
            showButtonCheque();
            return false;
          } else {
            hiddenButtonCheque();
          }
        }
      });
    }

    function limparCheque() {
      $("#inputNumCheque").val("");
      $("#inputValorCheque").val("");
      $("#inputDtVencimentoCheque").val("");
      $("#cmbBancoCheque").val("");
      $("#inputAgenciaCheque").val("");
      $("#inputContaCheque").val("");
      $("#inputNomeCheque").val("");
      $("#inputCpfCheque").val("");
    }

    function hiddenButtonCheque() {
      $('#divFormaDePagamento').removeClass('col-lg-3').addClass('col-lg-4');
      $('#divBtnCheque').removeClass('col-lg-1').css("display", "none");
      $('#btnCheque').fadeOut('300');
    }

    function showButtonCheque() {
      $('#divFormaDePagamento').removeClass('col-lg-4').addClass('col-lg-3');
      $('#divBtnCheque').addClass('col-lg-1').css("display", "flex");
      $('#btnCheque').fadeIn('300');
      $('#pageModalCheque').fadeIn('200');
    }

    function modalCheque() {
      $('#btnCheque').on('click', (e) => {
        e.preventDefault();
        $('#pageModalCheque').fadeIn(200);
      });

      $('#modalCloseCheque').on('click', function() {
        $('#cmbFormaDePagamento').val('');
        $('#select2-cmbFormaDePagamento-container').prop('title',
          'Todos').html('Todos');
        hiddenButtonCheque();
        $('#pageModalCheque').fadeOut(200);
        $('body').css('overflow', 'scroll');
        limparCheque();
        $("#lancamento").submit();
      });

      $("#salvarCheque").on('click', function() {
        $('#pageModalCheque').fadeOut(200);
        $('body').css('overflow', 'scroll');
      });
    }


    function pagamento() {
      const inputDataEmissao = $('#inputDataEmissao').val();
      const inputDescricao = $('#inputDescricao').val();
      const cmbPlanoContas = $('#cmbPlanoContas').val();
      const cmbContaBanco = $('#cmbContaBanco').val();
      const cmbFormaDePagamento = $('#cmbFormaDePagamento').val();
      const inputDataRecebimento = $('#inputDataRecebimento').val();
      const inputValorTotal = $('#inputValorTotal').val();

      if (inputDataEmissao === "" || inputDescricao === "" || cmbPlanoContas === "" || cmbContaBanco === "" || cmbFormaDePagamento === "" || inputDataRecebimento === "" || inputValorTotal === "") {
        $("#lancamento").submit();
        return false;
      } else {
        const cmbFormaDePagamento = $("#cmbFormaDePagamento").val()

        if (cmbFormaDePagamento !== '') {
          const formaPagamento = cmbFormaDePagamento.split('#');
          const inputNumCheque = $("#inputNumCheque").val();
          const inputValorCheque = $("#inputValorCheque").val();
          const cmbBancoCheque = $("#cmbBancoCheque").val();
          const inputAgenciaCheque = $("#inputAgenciaCheque").val();
          const inputContaCheque = $("#inputContaCheque").val();
          const inputNomeCheque = $("#inputNomeCheque").val();
          const inputCpfCheque = $("#inputCpfCheque").val();

          if (formaPagamento[1] === "CHEQUE" && (inputNumCheque === "" || inputValorCheque === "" || cmbBancoCheque === "" || inputAgenciaCheque === "" || inputContaCheque === "" || inputNomeCheque === "" || inputCpfCheque === "")) {
            alerta('Atenção', 'Você selecionou a forma de pagamento cheque, portanto, favor preencher os dados do cheque.')
            return false;
          }

          if (formaPagamento[1] === "CHEQUE" && (inputValorTotal !== inputValorCheque)) {
            alerta('Atenção', 'O valor total do cheque é diferente do valor do recebimento!')
            return false;
          }
        }
      }

      $("#lancamento").submit();
    }


    function modalJurosDescontos() {
      $('#jurusDescontos').on('click', (e) => {
        e.preventDefault();

        const inputValorTotal = $('#inputValorTotal').val();
        $('#inputValorAPagarJD').val(inputValorTotal);

        $('#pageModalJurosDescontos').fadeIn(200);
        $('.cardJuDes').css('width', '500px').css('margin', '0px auto')
      });

      $('#modalCloseJurosDescontos').on('click', function() {
        $('#pageModalJurosDescontos').fadeOut(200);
        $('body').css('overflow', 'scroll');

        $("#inputJurosJD").val("");
        $("#inputDescontoJD").val("");
        $("#inputValorTotalAPagarJD").val("");
        $('#inputValorAPagarJD').val("");
      });

      $("#salvarJurosDescontos").on('click', function() {
        $('#pageModalJurosDescontos').fadeOut(200);
        $('body').css('overflow', 'scroll');
      });
    }

    function calcularJuros() {
      let juros = 0;
      let desconto = 0;
      let valorTotal = 0;
      let jurosTipo = $("#cmbTipoJurosJD").val();
      let descontoTipo = $("#cmbTipoDescontoJD").val();
      let descontoValor = moedatofloat($("#inputDescontoJD").val());
      let valorAPagar = moedatofloat($("#inputValorAPagarJD").val());
      let jurosValor = moedatofloat($("#inputJurosJD").val());

      jurosTipo === 'P' ? juros = (valorAPagar * (jurosValor / 100)) : juros = jurosValor;
      descontoTipo === 'P' ? desconto = (valorAPagar * (descontoValor / 100)) : desconto = descontoValor;

      valorTotal = ((valorAPagar + juros) - desconto);

      $("#inputValorTotalAPagarJD").val(float2moeda(valorTotal));
      $("#inputValorTotal").val(float2moeda(valorTotal));
    };

    $("#inputJurosJD").keyup(() => {
      calcularJuros();
    });
    $("#inputDescontoJD").keyup(() => {
      calcularJuros();
    });
    $("#cmbTipoJurosJD").change(() => {
      calcularJuros();
    });
    $("#cmbTipoDescontoJD").change(() => {
      calcularJuros();
    });


    $("#salvar").on('click', (e) => {
      e.preventDefault();
      pagamento();
    });


    preencheDatas();
    modalCheque();
    modalJurosDescontos();
    verificaFormaPagamento();
  });


  function selecionaTipo(tipo) {
    if (tipo == 'P') {
      window.location.href = "movimentacaoFinanceiraPagamento.php";
    } else if (tipo == 'R') {
      window.location.href = "movimentacaoFinanceiraRecebimento.php";
    } else
      window.location.href = "movimentacaoFinanceiraTransferencia.php";
  };
  </script>

</head>

<body class="navbar-top sidebar-right-visible sidebar-xs">

  <?php include_once("topo.php"); ?>

  <!-- Page content -->
  <div class="page-content">

    <?php include_once("menu-left.php"); ?>

    <!-- Main content -->
    <div class="content-wrapper">

      <?php include_once("cabecalho.php"); ?>

      <!-- Content area -->
      <div class="content">

        <form id="lancamento" name="lancamento" class="form-validate-jquery" method="post" class="p-3">
          <!-- Info blocks -->
          <input type="hidden" id="inputPagamentoParcial" name="inputPagamentoParcial">
          <div class="row">
            <div class="col-lg-12">
              <!-- Basic responsive configuration -->
              <div class="card">
                <div class="card-header header-elements-inline">
                  <h3 class="card-title">Novo/Editar Lançamento</h3>
                  <div class="header-elements">
                    <div class="list-icons">
                      <a class="list-icons-item" data-action="collapse"></a>
                      <a href="relatorioMovimentacao.php" class="list-icons-item" data-action="reload"></a>
                      <!--<a class="list-icons-item" data-action="remove"></a>-->
                    </div>
                  </div>
                </div>

                <div class="card-body">

                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <div class="form-check form-check-inline">
                          <label class="form-check-label">
                            <input type="radio" name="inputTipo" value="P" class="form-input-styled" onclick="selecionaTipo('P')" data-fouc>
                            Pagamento
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="form-check-label">
                            <input type="radio" name="inputTipo" value="R" class="form-input-styled" onclick="selecionaTipo('R')" data-fouc checked>
                            Recebimento
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="form-check-label">
                            <input type="radio" name="inputTipo" value="T" class="form-input-styled" onclick="selecionaTipo('T')" data-fouc>
                            Transferência
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <br />

                  <?php
                    if (isset($lancamento)) {
                        echo '<input type="hidden" name="inputEditar" value="sim">';
                        echo '<input type="hidden" name="inputContaId" value="' . $lancamento['CnAReId'] . '">';
                    }
                  ?>

                  <div class="row">
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="inputDataEmissao">Data de Emissão <span class="text-danger">*</span></label>          
                        <input type="date" id="inputDataEmissao" name="inputDataEmissao" class="form-control" placeholder="Data" value="<?php echo date("Y-m-d") ?>"  readOnly>
                     </div>
                    </div>

                    <div class=" col-lg-6">
                      <div class="form-group">
                        <label for="inputDescricao">Descrição <span class='text-danger'>*</span></label>
                        <input type="text" id="inputDescricao" name="inputDescricao" class="form-control" placeholder="Descrição" value="<?php if (isset($lancamento)) echo $lancamento['CnAReDescricao'] ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="inputNumeroDocumento">Número Documento</label>
                        <input type="text" id="inputNumeroDocumento" name="inputNumeroDocumento" class="form-control" placeholder="Nº Documento" value="<?php if (isset($lancamento)) echo $lancamento['CnAReNumDocumento'] ?>">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="cmbPlanoContas">Plano de Contas <span class="text-danger">*</span></label>
                        <select id="cmbPlanoContas" name="cmbPlanoContas" class="form-control form-control-select2" required>
                          <option value="">Selecionar</option>
                          <?php
                              $sql = "SELECT PlConId, PlConCodigo, PlConNome
												        			FROM PlanoConta
												        			JOIN Situacao on SituaId = PlConStatus
												        			WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
												        			ORDER BY PlConCodigo ASC";
                              $result = $conn->query($sql);
                              $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

                              foreach ($rowPlanoContas as $item) {
                                  if (isset($lancamento)) {
                                      if ($lancamento['CnARePlanoContas'] == $item['PlConId']) {
                                          print('<option value="' . $item['PlConId'] . '" selected>' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');
                                      } else {
                                          print('<option value="' . $item['PlConId'] . '">' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');
                                      }
                                  } else {
                                      print('<option value="' . $item['PlConId'] . '">' . $item['PlConCodigo'] . ' - ' . $item['PlConNome'] . '</option>');
                                  }
                              }
                            ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-5">
                      <div class="form-group">
                        <label for="cmbContaBanco">Conta/Banco <span class="text-danger">*</span></label>
                        <select id="cmbContaBanco" name="cmbContaBanco" class="form-control form-control-select2" required>
                          <option value="" selected>Todos</option>
                          <?php
                              $sql = "SELECT CnBanId,
                                             CnBanNome
                                        FROM ContaBanco
                                        JOIN Situacao 
                                          ON SituaId = CnBanStatus
                                       WHERE CnBanUnidade = " . $_SESSION['UnidadeId'] . " 
                                         AND SituaChave = 'ATIVO'
                                    ORDER BY CnBanNome ASC";
                              $result = $conn->query($sql);
                              $rowContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);

                              foreach ($rowContaBanco as $item) {
                                  if (isset($lancamento)) {
                                      if ($lancamento['CnAReContaBanco'] == $item['CnBanId']) {
                                          print('<option value="' . $item['CnBanId'] . '" selected>' . $item['CnBanNome'] . '</option>');
                                      } else {
                                          print('<option value="' . $item['CnBanId'] . '">' . $item['CnBanNome'] . '</option>');
                                      }
                                  } else {
                                      print('<option value="' . $item['CnBanId'] . '">' . $item['CnBanNome'] . '</option>');
                                  }
                              }
                            ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-4" id='divFormaDePagamento'>
                      <div class="form-group">
                        <label for="cmbFormaDePagamento">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select id="cmbFormaDePagamento" name="cmbFormaDePagamento" class="form-control form-control-select2" required>
                          <option value="" selected>Todos</option>
                          <?php
                            try {
                                $sql = "SELECT FrPagId, FrPagNome, FrPagChave
                                        FROM FormaPagamento
                                        JOIN Situacao on SituaId = FrPagStatus
                                        WHERE FrPagUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                        ORDER BY FrPagNome ASC";
                                $result = $conn->query($sql);
                                $rowFormaPagamento = $result->fetchAll(PDO::FETCH_ASSOC);

                                try {
                                    foreach ($rowFormaPagamento as $item) {
                                        if (isset($lancamento)) {
                                            if ($lancamento['CnAReFormaPagamento'] == $item['FrPagId']) {
                                                print('<option value="' . $item['FrPagId'] . '#'.$item['FrPagChave']. '" chaveformaPagamento="'.$item['FrPagChave']. '" selected>' . $item['FrPagNome'] . '</option>');
                                            } else {
                                                print('<option value="' . $item['FrPagId'] . '#'.$item['FrPagChave']. '" chaveformaPagamento="'.$item['FrPagChave']. '" >' . $item['FrPagNome'] . '</option>');
                                            }
                                        } else {
                                            print('<option value="' . $item['FrPagId'] . '#'.$item['FrPagChave']. '" chaveformaPagamento="'.$item['FrPagChave']. '" >' . $item['FrPagNome'] . '</option>');
                                        }
                                    }
                                } catch (Exception $e) {
                                    echo 'Error: ',  $e->getMessage(), "\n";
                                }
                            } catch (Exception $e) {
                                echo 'Error: ',  $e->getMessage(), "\n";
                            }
                          ?>
                        </select>
                      </div>

                    </div>

                    <div class="" id='divBtnCheque' style="display:none;
                                justify-content: center;
                                align-items: center;
                                margin-bottom: 1.25rem;">
                      <a href="#" id="btnCheque">
                        <img src="./global_assets/images/lamparinas/icon-cheque.png" alt="Icone Cheque" style='height: 100%;'>
                      </a>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12 col-lg-6">
                      <div class="d-flex flex-column">

                        <div class="row justify-content-between m-0">
                          <h5>Valor Recebido</h5>
                          <div class="row pr-2" style="margin-top: 5px;">
                            <a id="jurusDescontos" href="">Juros/Descontos</a>
                          </div>
                        </div>
                        <div class=" card">
                          <div class="card-body p-4" style="background-color: #f8f8f8; border: 1px solid #ccc">
                            <div class="row">
                              <div class="form-group col-6">
                                <label for="inputDataRecebimento">Data do Recebimento <span class="text-danger">*</span></label>
                                <input type="date" id="inputDataRecebimento" name="inputDataRecebimento" class="form-control" placeholder="Data do Pagamento" value="<?php if (isset($lancamento)) echo $lancamento['CnAReDtRecebimento'] ?>" required>
                              </div>

                              <div class="form-group col-6">
                                <label for="inputValorTotal">Valor Total Recebido (=) <span class="text-danger">*</span> </label>
                                <input type="text" onKeyUp="moeda(this)" maxLength="12" id="inputValorTotal" name="inputValorTotal" class="form-control" placeholder='0,00' value="<?php if (isset($lancamento)) echo number_format($lancamento['CnAReValorRecebido'], 2, ',', '.'); ?>" required>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label for="inputObservacao">Observação</label>
                        <textarea id="inputObservacao" class="form-control" name="inputObservacao" rows="3" value="<?php if (isset($lancamento)) echo $lancamento['CnAReObservacao']; ?>"></textarea>
                      </div>
                    </div>
                  </div>

                  <?php 
                    if ($_SESSION['MovFinancPermissionAtualiza']) {
                        echo' <button id="salvar" class="btn btn-principal">Salvar</button>';
                      }
                  ?>
                  <?php if($_SESSION['Conciliacao'] === true) { ?>
                    <a href="movimentacaoFinanceiraConciliacao.php" class="btn">Cancelar</a>
                  <?php } else { ?>
                    <a href="movimentacaoFinanceira.php" class="btn">Cancelar</a>
                  <?php } ?>
                </div>

              </div>
              <!-- /basic responsive configuration -->

            </div>
          </div>

          <!-- /info blocks -->

          <!--------------------------------------------------------------------------------------->
          <!--Modal Cheque-->
          <div id="pageModalCheque" class="custon-modal">
            <div class="custon-modal-container">
              <div class="card custon-modal-content">
                <div class="custon-modal-title">
                  <i class=""></i>
                  <p class="h3">Detalhamento do Cheque</p>
                  <i class=""></i>
                </div>
                <div class="px-3 pt-3">
                  <div class="d-flex flex-row p-1">
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputNumCheque">Nº do Cheque<span class="text-danger">*</span></label>
                        <input type="text" id="inputNumCheque" name="inputNumCheque" class="form-control" placeholder="Número do Cheque" value="<?php if (isset($lancamento)) echo $lancamento['CnAReNumCheque'] ?>">
                      </div>
                    </div>
                    <div class='col-lg-3'>
                      <div class="form-group">
                        <label for="inputValorCheque">Valor<span class="text-danger">*</span></label>
                        <div class="input-group">
                          <input type="text" id="inputValorCheque" onKeyUp="moeda(this)" maxLength="12" name="inputValorCheque" class="form-control" value="<?php if (isset($lancamento)) echo $lancamento['CnAReValorCheque'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputDtEmissaoCheque">Data da Emissão<span class="text-danger">*</span></label>
                        <input id="inputDtEmissaoCheque" class="form-control" type="date" name="inputDtEmissaoCheque" value="<?php if (isset($lancamento)) echo $lancamento['CnAReDtEmissaoCheque'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputDtVencimentoCheque">Data do Vencimento<span class="text-danger">*</span></label>
                        <input id="inputDtVencimentoCheque" class="form-control" type="date" name="inputDtVencimentoCheque" value="<?php if (isset($lancamento)) echo $lancamento['CnAReDtVencimentoCheque'] ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="px-3 pt-3">
                  <div class="d-flex flex-row p-1">
                    <div class="col-lg-6">
                      <label for="cmbBancoCheque">Banco<span class="text-danger">*</span></label>
                      <select id="cmbBancoCheque" name="cmbBancoCheque" class="form-control form-control-select2" value="<?php if (isset($lancamento)) echo $lancamento['CnAReBancoCheque'] ?>">
                        <option value="">Selecione um banco</option>
                        <?php 
													$sql = "SELECT BancoId, BancoCodigo, BancoNome
															FROM Banco
															JOIN Situacao on SituaId = BancoStatus
															WHERE SituaChave = 'ATIVO'
															ORDER BY BancoCodigo ASC";
													$result = $conn->query($sql);
													$row = $result->fetchAll(PDO::FETCH_ASSOC);
                      
                          foreach ($row as $item) {
                              if (isset($lancamento)) {
                                  if ($lancamento['CnAReBancoCheque'] == $item['BancoId']) {
                                    print('<option value="'.$item['BancoId'].'" selected>'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
                                  } else {
                                    print('<option value="'.$item['BancoId'].'">'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
                                  }
                              } else {
                                  print('<option value="'.$item['BancoId'].'">'.$item['BancoCodigo'] . " - " . $item['BancoNome'].'</option>');
                              }
                          }
												?>
                      </select>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputAgenciaCheque">Agência<span class="text-danger">*</span></label>
                        <input type="text" id="inputAgenciaCheque" name="inputAgenciaCheque" class="form-control" placeholder="Número da Agência" value="<?php if (isset($lancamento)) echo $lancamento['CnAReAgenciaCheque'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputContaCheque">Conta<span class="text-danger">*</span></label>
                        <input type="text" id="inputContaCheque" name="inputContaCheque" class="form-control" placeholder="Número da Conta" value="<?php if (isset($lancamento)) echo $lancamento['CnAReContaCheque'] ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-3 pt-3">
                  <div class="d-flex flex-row p-1">
                    <div class="col-lg-9">
                      <div class="form-group">
                        <label for="inputNomeCheque">Nome<span class="text-danger">*</span></label>
                        <input type="text" id="inputNomeCheque" name="inputNomeCheque" class="form-control" placeholder="Nome Completo" value="<?php if (isset($lancamento)) echo $lancamento['CnAReNomeCheque'] ?>">
                      </div>
                    </div>

                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputCpfCheque">CPF<span class="text-danger">*</span></label>
                        <input type="text" id="inputCpfCheque" name="inputCpfCheque" class="form-control" placeholder="CPF" data-mask="999.999.999-99" value="<?php if (isset($lancamento)) echo $lancamento['CnAReCpfCheque'] ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer mt-2 d-flex flex-column">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <a class="btn btn-lg btn-principal" id="salvarCheque">Salvar</a>
                        <a id="modalCloseCheque" class="btn btn-basic" role="button">Cancelar</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--------------------------------------------------------------------------------------->

          <!--------------------------------------------------------------------------------------->
          <!--Modal Juros e Descontos-->
          <div id="pageModalJurosDescontos" class="custon-modal">
            <div class="custon-modal-container">
              <div class="card cardJuDes custon-modal-content">
                <div class="custon-modal-title">
                  <i class=""></i>
                  <p class="h3">Juros e descontos</p>
                  <i class=""></i>
                </div>
                <div class="p-5">

                  <div class="d-flex flex-row justify-content-between">
                    <div class="form-group" style="width: 200px">
                      <label for="cmbTipoJurosJD">Tipo</label>
                      <select id="cmbTipoJurosJD" name="cmbTipoJurosJD" class="form-control form-control-select2">
                        <option value="P">Porcentagem</option>
                        <option value="V">Valor</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="inputJurosJD">Juros</label>
                      <input id="inputJurosJD" maxLength="12" class="form-control" type="text" name="inputJurosJD">
                    </div>
                  </div>
                  <div class="d-flex flex-row justify-content-between">
                    <div class="form-group" style="width: 200px">
                      <label for="cmbTipoDescontoJD">Tipo</label>
                      <select id="cmbTipoDescontoJD" name="cmbTipoDescontoJD" class="form-control form-control-select2">
                        <option value="P">Porcentagem</option>
                        <option value="V">Valor</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="inputDescontoJD">Desconto</label>
                      <input id="inputDescontoJD" maxLength="12" class="form-control" type="text" name="inputDescontoJD">
                    </div>
                  </div>
                  <div class="d-flex flex-row justify-content-between">
                    <div class="form-group" style="width: 200px">
                      <label for="inputValorAPagarJD">Valor à Receber</label>
                      <input id="inputValorAPagarJD" onKeyUp="moeda(this)" maxLength="12" class="form-control" type="text" name="inputValorAPagarJD" readOnly>
                    </div>
                    <div class="form-group">
                      <label for="inputValorTotalAPagarJD">Valor Total à Receber</label>
                      <input id="inputValorTotalAPagarJD" onKeyUp="moeda(this)" maxLength="12" class="form-control" type="text" name="inputValorTotalAPagarJD" readOnly>
                    </div>
                  </div>
                </div>

                <div class="card-footer mt-2 d-flex flex-column">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <a class="btn btn-lg btn-principal" id="salvarJurosDescontos">Ok</a>
                        <a id="modalCloseJurosDescontos" class="btn btn-basic" role="button">Cancelar</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--------------------------------------------------------------------------------------->
        </form>
      </div>
      <!-- /content area -->

      <?php include_once("footer.php"); ?>

    </div>
    <!-- /main content -->

    <?php include_once("sidebar-right.php"); ?>

  </div>
  <!-- /page content -->

  <?php include_once("alerta.php"); ?>

</body>

</html>