<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Financeiro / Movimentação do Financeiro / Novo Lançamento';

include('global_assets/php/conexao.php');

if (isset($_POST['inputDataEmissao'])) {
    if (isset($_POST['inputEditar'])) { //EDIÇÃO
      try {
        $sql = "SELECT SituaId
                FROM Situacao
                WHERE SituaChave = 'PAGA'";
                
        $result = $conn->query($sql);
        $situacao = $result->fetch(PDO::FETCH_ASSOC);

        $sql = "UPDATE ContasAPagar SET CnAPaPlanoContas = :iPlanoContas, CnAPaFornecedor = :iFornecedor, CnAPaContaBanco = :iContaBanco, CnAPaFormaPagamento = :iFormaPagamento, CnAPaNumDocumento = :sNumDocumento,
                                        CnAPaNotaFiscal = :sNotaFiscal, CnAPaDtEmissao = :dateDtEmissao, CnAPaOrdemCompra = :iOrdemCompra, CnAPaDescricao = :sDescricao, CnAPaDtVencimento = :dateDtVencimento, CnAPaValorAPagar = :fValorAPagar,
                                        CnAPaDtPagamento = :dateDtPagamento, CnAPaValorPago = :fValorPago, CnAPaObservacao = :sObservacao, CnAPaStatus = :iStatus, CnAPaUsuarioAtualizador = :iUsuarioAtualizador, CnAPaUnidade = :iUnidade,
                                        CnAPaTipoJuros = :sTipoJuros, CnAPaJuros = :fJuros, CnAPaTipoDesconto = :sTipoDesconto, CnAPaDesconto = :fDesconto
        WHERE CnAPaId = " . $_POST['inputContaId'] . "";
        $result = $conn->prepare($sql);

        $result->execute(array(
            ':iPlanoContas' => $_POST['cmbPlanoContas'],
            ':iFornecedor' => 0,
            ':iContaBanco' => $_POST['cmbContaBanco'],
            ':iFormaPagamento' => $_POST['cmbFormaDePagamento'],
            ':sNumDocumento' => isset($_POST['inputNumeroDocumento']) ? $_POST['inputNumeroDocumento'] : null,
            ':sNotaFiscal' => null,
            ':dateDtEmissao' => $_POST['inputDataEmissao'],
            ':iOrdemCompra' => null,
            ':sDescricao' => $_POST['inputDescricao'],
            ':dateDtVencimento' => $_POST['inputDataPagamento'],
            ':fValorAPagar' => floatval(gravaValor($_POST['inputValorTotal'])),
            ':dateDtPagamento' => $_POST['inputDataPagamento'],
            ':fValorPago' => isset($_POST['inputValorTotal']) ? floatval(gravaValor($_POST['inputValorTotal'])) : null,
            ':sObservacao' => isset($_POST['inputObservacao']) ? $_POST['inputObservacao'] : null,
            ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
            ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
            ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
            ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
            ':iStatus' => $situacao['SituaId'],
            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
            ':iUnidade' => $_SESSION['UnidadeId']
        ));

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
        
    } else { //INSERÇÃO
        try {
          if (isset($_POST['inputValorTotal'])) {
            $sql = "SELECT SituaId
                      FROM Situacao
                     WHERE SituaChave = 'PAGA'
                    ";
            $result = $conn->query($sql);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
          } else {
            $sql = "SELECT SituaId
                    FROM Situacao
                    WHERE SituaChave = 'APAGAR'
                ";
            $result = $conn->query($sql);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
          }

          $sql = "INSERT INTO ContasAPagar ( CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento,
                                        CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                        CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaTipoJuros, CnAPaJuros, 
                                        CnAPaTipoDesconto, CnAPaDesconto, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                  VALUES ( :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento,:sNumDocumento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                          :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :sTipoJuros, :fJuros, 
                          :sTipoDesconto, :fDesconto, :iStatus, :iUsuarioAtualizador, :iUnidade)";
          $result = $conn->prepare($sql);

          $result->execute(array(
              ':iPlanoContas' => $_POST['cmbPlanoContas'],
              ':iFornecedor' => 0,
              ':iContaBanco' => $_POST['cmbContaBanco'],
              ':iFormaPagamento' => $_POST['cmbFormaDePagamento'],
              ':sNumDocumento' => isset($_POST['inputNumeroDocumento']) ? $_POST['inputNumeroDocumento'] : null,
              ':sNotaFiscal' => null,
              ':dateDtEmissao' => $_POST['inputDataEmissao'],
              ':iOrdemCompra' => null,
              ':sDescricao' => $_POST['inputDescricao'],
              ':dateDtVencimento' => $_POST['inputDataPagamento'],
              ':fValorAPagar' => floatval(gravaValor($_POST['inputValorTotal'])),
              ':dateDtPagamento' => $_POST['inputDataPagamento'],
              ':fValorPago' => isset($_POST['inputValorTotal']) ? floatval(gravaValor($_POST['inputValorTotal'])) : null,
              ':sObservacao' => isset($_POST['inputObservacao']) ? $_POST['inputObservacao'] : null,
              ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
              ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
              ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
              ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
              ':iStatus' => $situacao['SituaId'],
              ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
              ':iUnidade' => $_SESSION['UnidadeId']
          ));

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

if (isset($_GET['lancamentoId'])) {
  
    $sql = "SELECT CnAPaId, CnAPaPlanoContas, CnAPaDtEmissao, CnAPaDescricao, CnAPaDtPagamento, 
                   CnAPaValorPago, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento, 
                   CnAPaObservacao
    		      FROM ContasAPagar
         LEFT JOIN OrdemCompra on OrComId = CnAPaOrdemCompra
             WHERE CnAPaUnidade = " . $_SESSION['UnidadeId'] . " 
               AND CnAPaId = " . $_GET['lancamentoId'] . "";

    $result = $conn->query($sql);
    $lancamento = $result->fetch(PDO::FETCH_ASSOC);
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

  <!-- Theme JS files -->
  <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
  <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

  <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
  <script src="global_assets/js/demo_pages/form_layouts.js"></script>
  <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

  <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
  <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
  <!-- /theme JS files -->

  <!-- Validação -->
  <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
  <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
  <script src="global_assets/js/demo_pages/form_validation.js"></script>

  <script type="text/javascript">
  $(document).ready(function() {

    var input = document.getElementById('inputDataPagamento');
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
      if ($('#inputDataPagamento').val() == "")
        $('#inputDataPagamento').val($fullDataRecebimento);
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
      $("#lancamento").submit();
    });

    preencheDatas();
    modalJurosDescontos();
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
                            <input type="radio" name="inputTipo" value="P" class="form-input-styled" onclick="selecionaTipo('P')" data-fouc checked>
                            Pagamento
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <label class="form-check-label">
                            <input type="radio" name="inputTipo" value="R" class="form-input-styled" onclick="selecionaTipo('R')" data-fouc>
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
                        echo '<input type="hidden" name="inputContaId" value="' . $lancamento['CnAPaId'] . '">';
                    }
                  ?>

                  <div class="row">
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="inputDataEmissao">Data de Emissão <span class="text-danger">*</span></label>
                        <input type="date" id="inputDataEmissao" name="inputDataEmissao" class="form-control" placeholder="Data" value="<?php echo date("Y-m-d") ?>"  readOnly>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group">
                        <label for="inputDescricao">Descrição <span class='text-danger'>*</span></label>
                        <input type="text" id="inputDescricao" name="inputDescricao" class="form-control" placeholder="Descrição" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaDescricao'] ?>" required>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="inputNumeroDocumento">Número Documento</label>
                        <input type="text" id="inputNumeroDocumento" name="inputNumeroDocumento" class="form-control" placeholder="Nº Documento" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaNumDocumento'] ?>">
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
                                      FROM PlanoContas
                                      JOIN Situacao on SituaId = PlConStatus
                                      WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                      ORDER BY PlConCodigo ASC";

                            $result = $conn->query($sql);
                            $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($rowPlanoContas as $item) {
                                if (isset($lancamento)) {
                                    if ($lancamento['CnAPaPlanoContas'] == $item['PlConId']) {
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
                                    if ($lancamento['CnAPaContaBanco'] == $item['CnBanId']) {
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

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="cmbFormaDePagamento">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select id="cmbFormaDePagamento" name="cmbFormaDePagamento" class="form-control form-control-select2" required>
                          <option value="" selected>Todos</option>
                          <?php
                              $sql = "SELECT FrPagId,
                                             FrPagNome
                                        FROM FormaPagamento
                                        JOIN Situacao 
                                          ON SituaId = FrPagStatus
                                       WHERE FrPagUnidade = " . $_SESSION['UnidadeId'] . " 
                                         AND SituaChave = 'ATIVO'
                                    ORDER BY FrPagNome ASC";
                              $result = $conn->query($sql);
                              $rowFormaPagamento = $result->fetchAll(PDO::FETCH_ASSOC);

                              foreach ($rowFormaPagamento as $item) {
                                if (isset($lancamento)) {
                                    if ($lancamento['CnAPaFormaPagamento'] == $item['FrPagId']) {
                                        print('<option value="' . $item['FrPagId'] . '" selected>' . $item['FrPagNome'] . '</option>');
                                    } else {
                                        print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
                                    }
                                } else {
                                    print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
                                }
                              }
                            ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12 col-lg-6">
                      <div class="d-flex flex-column">

                        <div class="row justify-content-between m-0">
                          <h5>Valor Pago</h5>
                          <div class="row pr-2" style="margin-top: 5px;">
                            <a id="jurusDescontos" href="">
                              Juros/Descontos</a>
                          </div>
                        </div>
                        <div class=" card">
                          <div class="card-body p-4" style="background-color: #f8f8f8; border: 1px solid #ccc">
                            <div class="row">
                              <div class="form-group col-6">
                                <label for="inputDataPagamento">Data do Pagamento <span class="text-danger">*</span></label>
                                <input type="date" id="inputDataPagamento" name="inputDataPagamento" class="form-control" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaDtPagamento'] ?>" required>
                              </div>
                              <div class="form-group col-6">
                                <label for="inputValorTotal">Valor Total Pago (=) <span class="text-danger">*</span> </label>
                                <input type="text" onKeyUp="moeda(this)" maxLength="12" id="inputValorTotal" name="inputValorTotal" class="form-control" placeholder='0,00' value="<?php if (isset($lancamento)) echo number_format($lancamento['CnAPaValorPago'], 2, ',', '.'); ?>" required>
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
                        <textarea id="inputObservacao" class="form-control" name="inputObservacao" rows="3"><?php if (isset($lancamento)) echo $lancamento['CnAPaObservacao']; ?></textarea>
                      </div>
                    </div>
                  </div>

                  <button id="salvar" class="btn btn-principal">Salvar</button>
                  <a href="movimentacaoFinanceira.php" class="btn">Cancelar</a>
                </div>

              </div>
              <!-- /basic responsive configuration -->

            </div>
          </div>

          <!-- /info blocks -->

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
                      <label for="inputValorAPagarJD">Valor à Pagar</label>
                      <input id="inputValorAPagarJD" onKeyUp="moeda(this)" maxLength="12" class="form-control" type="text" name="inputValorAPagarJD" readOnly>
                    </div>
                    <div class="form-group">
                      <label for="inputValorTotalAPagarJD">Valor Total à Pagar</label>
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