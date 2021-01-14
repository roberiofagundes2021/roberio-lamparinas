<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Lançamento - Movimentação Financeira(Pagamento)';

include('global_assets/php/conexao.php');

if (isset($_POST['cmbPlanoContas'])) {

    if (isset($_POST['inputEditar'])) {

        try {

            if (isset($_POST['inputValorTotalPago'])) {
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

            $sql = "UPDATE ContasAPagar SET CnAPaPlanoContas = :iPlanoContas, CnAPaFornecedor = :iFornecedor, CnAPaContaBanco = :iContaBanco, CnAPaFormaPagamento = :iFormaPagamento, CnAPaNumDocumento = :sNumDocumento,
                                            CnAPaNotaFiscal = :sNotaFiscal, CnAPaDtEmissao = :dateDtEmissao, CnAPaOrdemCompra = :iOrdemCompra, CnAPaDescricao = :sDescricao, CnAPaDtVencimento = :dateDtVencimento, CnAPaValorAPagar = :fValorAPagar,
                                            CnAPaDtPagamento = :dateDtPagamento, CnAPaValorPago = :fValorPago, CnAPaObservacao = :sObservacao, CnAPaStatus = :iStatus, CnAPaUsuarioAtualizador = :iUsuarioAtualizador, CnAPaUnidade = :iUnidade,
                                            CnAPaTipoJuros = :sTipoJuros, CnAPaJuros = :fJuros, CnAPaTipoDesconto = :sTipoDesconto, CnAPaDesconto = :fDesconto
		    		WHERE CnAPaId = " . $_POST['inputContaId'] . "";
            $result = $conn->prepare($sql);

            $result->execute(array(
                ':iPlanoContas' => $_POST['cmbPlanoContas'],
                ':iFornecedor' => $_POST['cmbFornecedor'],
                ':iContaBanco' => $_POST['cmbContaBanco'],
                ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                ':dateDtEmissao' => $_POST['inputDataEmissao'],
                ':iOrdemCompra' => isset($_POST['cmbOrdemCarta']) ? $_POST['cmbOrdemCarta'] : null,
                ':sDescricao' => $_POST['inputDescricao'],
                ':dateDtVencimento' => $_POST['inputDataVencimento'],
                ':fValorAPagar' => floatval(gravaValor($_POST['inputValor'])),
                ':dateDtPagamento' => $_POST['inputDataPagamento'],
                ':fValorPago' => isset($_POST['inputValorTotalPago']) ? floatval(gravaValor($_POST['inputValorTotalPago'])) : null,
                ':sObservacao' => $_POST['inputObservacao'],
                ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
                ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
                ':iStatus' => $situacao['SituaId'],
                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iUnidade' => $_SESSION['UnidadeId']
            ));

            if (isset($_POST['inputPagamentoParcial'])) {
                if (intval($_POST['inputPagamentoParcial']) != 0) {
                    $sql = "SELECT SituaId
                            FROM Situacao
                            WHERE SituaChave = 'APAGAR'
                     ";
                    $result = $conn->query($sql);
                    $situacao = $result->fetch(PDO::FETCH_ASSOC);

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
                        ':iFornecedor' => $_POST['cmbFornecedor'],
                        ':iContaBanco' => $_POST['cmbContaBanco'],
                        ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                        ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                        ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                        ':dateDtEmissao' => $_POST['inputDataEmissao'],
                        ':iOrdemCompra' => isset($_POST['cmbOrdemCarta']) ? $_POST['cmbOrdemCarta'] : null,
                        ':sDescricao' => $_POST['inputDescricao'],
                        ':dateDtVencimento' => $_POST['inputDataVencimento'],
                        ':fValorAPagar' => $_POST['inputPagamentoParcial'],
                        ':dateDtPagamento' => $_POST['inputDataPagamento'],
                        ':fValorPago' => null,
                        ':sObservacao' => $_POST['inputObservacao'],
                        ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                        ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
                        ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                        ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
                        ':iStatus' => $situacao['SituaId'],
                        ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                        ':iUnidade' => $_SESSION['UnidadeId']
                    ));
                }
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


            if (isset($_POST['inputNumeroParcelas'])) {

                $numParcelas = intVal($_POST['inputNumeroParcelas']);

                for ($i = 1; $i <= $numParcelas; $i++) {
                    $sql = "SELECT SituaId
                    FROM Situacao
                    WHERE SituaChave = 'APAGAR'
                    ";
                    $result = $conn->query($sql);
                    $situacao = $result->fetch(PDO::FETCH_ASSOC);

                    $sql = "INSERT INTO ContasAPagar ( CnAPaPlanoContas, CnAPaFornecedor, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento,
                                                  CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaOrdemCompra, CnAPaDescricao, CnAPaDtVencimento, CnAPaValorAPagar,
                                                  CnAPaDtPagamento, CnAPaValorPago, CnAPaObservacao, CnAPaStatus, CnAPaUsuarioAtualizador, CnAPaUnidade)
                            VALUES ( :iPlanoContas, :iFornecedor, :iContaBanco, :iFormaPagamento,:sNumDocumento, :sNotaFiscal, :dateDtEmissao, :iOrdemCompra,
                                    :sDescricao, :dateDtVencimento, :fValorAPagar, :dateDtPagamento, :fValorPago, :sObservacao, :iStatus, :iUsuarioAtualizador, :iUnidade)";
                    $result = $conn->prepare($sql);

                    $result->execute(array(

                        ':iPlanoContas' => $_POST['cmbPlanoContas'],
                        ':iFornecedor' => $_POST['cmbFornecedor'],
                        ':iContaBanco' => $_POST['cmbContaBanco'],
                        ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                        ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                        ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                        ':dateDtEmissao' => $_POST['inputDataEmissao'],
                        ':iOrdemCompra' => isset($_POST['cmbOrdemCarta']) ? $_POST['cmbOrdemCarta'] : null,
                        ':sDescricao' => $_POST['inputParcelaDescricao' . $i . ''],
                        ':dateDtVencimento' => $_POST['inputParcelaDataVencimento' . $i . ''],
                        ':fValorAPagar' => floatval(gravaValor($_POST['inputParcelaValorAPagar' . $i . ''])),
                        ':dateDtPagamento' => $_POST['inputDataPagamento'],
                        ':fValorPago' => isset($_POST['inputValorTotalPago']) ? floatval(gravaValor($_POST['inputValorTotalPago'])) : null,
                        ':sObservacao' => $_POST['inputObservacao'],
                        ':iStatus' => $situacao['SituaId'],
                        ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                        ':iUnidade' => $_SESSION['UnidadeId']
                    ));
                }
            } else {

                if (isset($_POST['inputValorTotalPago'])) {
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
                    ':iFornecedor' => $_POST['cmbFornecedor'],
                    ':iContaBanco' => $_POST['cmbContaBanco'],
                    ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                    ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                    ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                    ':dateDtEmissao' => $_POST['inputDataEmissao'],
                    ':iOrdemCompra' => isset($_POST['cmbOrdemCarta']) ? $_POST['cmbOrdemCarta'] : null,
                    ':sDescricao' => $_POST['inputDescricao'],
                    ':dateDtVencimento' => $_POST['inputDataVencimento'],
                    ':fValorAPagar' => floatval(gravaValor($_POST['inputValor'])),
                    ':dateDtPagamento' => $_POST['inputDataPagamento'],
                    ':fValorPago' => isset($_POST['inputValorTotalPago']) ? floatval(gravaValor($_POST['inputValorTotalPago'])) : null,
                    ':sObservacao' => $_POST['inputObservacao'],
                    ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                    ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
                    ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                    ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
                    ':iStatus' => $situacao['SituaId'],
                    ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                    ':iUnidade' => $_SESSION['UnidadeId']
                ));

                if (isset($_POST['inputPagamentoParcial'])) {
                    if (intval($_POST['inputPagamentoParcial']) != 0) {
                        $sql = "SELECT SituaId
                                FROM Situacao
                                WHERE SituaChave = 'APAGAR'
                         ";
                        $result = $conn->query($sql);
                        $situacao = $result->fetch(PDO::FETCH_ASSOC);

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
                            ':iFornecedor' => $_POST['cmbFornecedor'],
                            ':iContaBanco' => $_POST['cmbContaBanco'],
                            ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                            ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                            ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                            ':dateDtEmissao' => $_POST['inputDataEmissao'],
                            ':iOrdemCompra' => isset($_POST['cmbOrdemCarta']) ? $_POST['cmbOrdemCarta'] : null,
                            ':sDescricao' => $_POST['inputDescricao'],
                            ':dateDtVencimento' => $_POST['inputDataVencimento'],
                            ':fValorAPagar' => $_POST['inputPagamentoParcial'],
                            ':dateDtPagamento' => $_POST['inputDataPagamento'],
                            ':fValorPago' => null,
                            ':sObservacao' => $_POST['inputObservacao'],
                            ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                            ':fJuros' => isset($_POST['inputJurosJD']) ? floatval(gravaValor($_POST['inputJurosJD'])) : null,
                            ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                            ':fDesconto' => isset($_POST['inputDescontoJD']) ? floatval(gravaValor($_POST['inputDescontoJD'])) : null,
                            ':iStatus' => $situacao['SituaId'],
                            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                            ':iUnidade' => $_SESSION['UnidadeId']
                        ));
                    }
                }
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

    irpara("contasAPagar.php");
}
//$count = count($row);

if (isset($_GET['lancamentoId'])) {
    $sql = "SELECT CnAPaId, CnAPaPlanoContas, CnAPaFornecedor, CnAPaNotaFiscal, CnAPaDtEmissao, CnAPaDescricao, CnAPaDtVencimento, 
            CnAPaValorAPagar, CnAPaDtPagamento, CnAPaValorPago, CnAPaContaBanco, CnAPaFormaPagamento, CnAPaNumDocumento, OrComNumero
    		FROM ContasAPagar
            LEFT JOIN OrdemCompra on OrComId = CnAPaOrdemCompra
    		WHERE CnAPaUnidade = " . $_SESSION['UnidadeId'] . " and CnAPaId = " . $_GET['lancamentoId'] . "";
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
  <title>Lamparinas | Relatório de Movimentação</title>

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

  <!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
  <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

  <!-- Validação -->
  <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
  <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
  <script src="global_assets/js/demo_pages/form_validation.js"></script>

  <script type="text/javascript">
  $(document).ready(function() {

    let styleJurosDescontos = ''

    function geararParcelas(parcelas, valorTotal, dataVencimento, periodicidade) {
      $("#parcelasContainer").html("")
      let descricao = $("#inputDescricao").val()

      let valorParcela = float2moeda(parseFloat(valorTotal) / parcelas)
      console.log(dataVencimento)
      let numeroParcelas = `<input type="hidden" value="${parcelas}" name="inputNumeroParcelas">`
      // let dataVencimento = dataVencimento
      $("#parcelasContainer").append(numeroParcelas)
      let cont = 0
      let iAnterior = 0
      for (let i = 1; i <= parcelas; i++) {

        let novaDataVencimento = ''

        let somadorPeriodicidade = periodicidade == 1 ? 0 : periodicidade == 2 ? 2 :
          periodicidade == 3 ? 3 : 6
        if (i > 1) {
          let dataArray = dataVencimento.split("-")
          let mes = parseInt(dataArray[1])
          let novoMes = 0
          let ano = parseInt(dataArray[0])

          novoMes = mes + i > 9 ? (mes + (i - 1)).toString() : `0${(mes + (i - 1)).toString()}`

          if (novoMes > 12) {
            cont++
            ano = ano + 1
            novoMes = cont > 9 ? cont : `0${cont}`
          }

          dataArray[1] = novoMes
          dataArray[0] = ano
          novaDataVencimento = `${dataArray[0]}-${dataArray[1]}-${dataArray[2]}`
        } else {
          novaDataVencimento = dataVencimento
        }

        let elem = `<div class="d-flex flex-row justify-content-center">
                                    <p class="col-1 p-2 pl-4">${i}</p>
                                    <div class="form-group col-5 p-2">
                                        <input type="text" class="form-control" id="inputParcelaDescricao${i}" name="inputParcelaDescricao${i}" value="${descricao} ${i}/${parcelas}">
                                    </div>
                                    <div class="form-group col-3 p-2">
                                        <input type="date" class="form-control" id="inputParcelaDataVencimento${i}" name="inputParcelaDataVencimento${i}" value="${novaDataVencimento}">
                                    </div>
                                    <div class="form-group col-3 p-2">
                                        <input type="text" class="form-control" id="inputParcelaValorAPagar${i}" name="inputParcelaValorAPagar${i}" value="${valorParcela}">
                                    </div> 
                                </div>`

        $("#parcelasContainer").append(elem)
      }
    }


    function parcelamento() {
      $('#gerarParcelas').on('click', (e) => {
        e.preventDefault()
        let parcelas = $("#cmbParcelas").val()
        let valorTotal = $("#valorTotal").val()
        let dataVencimento = $("#inputDataVencimento").val()
        let periodicidade = $("#cmbPeriodicidade").val()

        geararParcelas(parcelas, valorTotal, dataVencimento, periodicidade)
      })
    }
    parcelamento()

    function limparJurosDescontos() {
      $("#inputVencimentoJD").val("")
      $("#inputValorAPagarJD").val("")
      $("#inputJurosJD").val("")
      $("#inputDescontoJD").val("")
      $("#inputDataPagamentoJD").val("")
      $("#inputValorTotalAPagarJD").val("")
    }

    function preencherJurosDescontos() {
      $valorAPagar = $("#inputValor").val()
      $dataVencimento = $("#inputDataVencimento").val()
      $dataPagamento = $("#inputDataPagamento").val()
      $valorTotalPago = $("#inputValorTotalPago").val()

      $("#inputVencimentoJD").val($dataVencimento)
      $("#inputValorAPagarJD").val($valorTotalPago)
      $("#inputDataPagamentoJD").val($dataPagamento)
    }

    function habilitarPagamento() {
      $valorTotalPago = $("#inputValor").val()
      $dataPagamento = new Date
      $dia = parseInt($dataPagamento.getDate()) <= 9 ?
        `0${parseInt($dataPagamento.getDate())}` : parseInt($dataPagamento.getDate())
      $mes = parseInt($dataPagamento.getMonth()) + 1 <= 9 ?
        `0${parseInt($dataPagamento.getMonth()) + 1}` : parseInt($dataPagamento.getMonth()) + 1
      $ano = $dataPagamento.getFullYear()

      $fullDataPagamento = `${$ano}-${$mes}-${$dia}`

      $("#inputDataPagamento").val($fullDataPagamento)
    }
    habilitarPagamento();


    function modalJurosDescontos() {
      $('#jurusDescontos').on('click', (e) => {
        preencherJurosDescontos();
        e.preventDefault()
        $('#pageModalJurosDescontos').fadeIn(200);
        $('.cardJuDes').css('width', '500px').css('margin', '0px auto')

        let dataVencimento = $("#inputDataVencimento").val()
        let valor = $("#inputValor").val()

        $("#inputValorAPagarJD").val(valor)
        $("#inputVencimentoJD").val(dataVencimento)
      })

      let valorTotal = $('#inputValor').val()

      $('#valorTotal').val(valorTotal)

      $('#modalCloseJurosDescontos').on('click', function() {
        $('#pageModalJurosDescontos').fadeOut(200);
        $('body').css('overflow', 'scroll');

        limparJurosDescontos()
      })

      $("#salvarJurosDescontos").on('click', function() {
        $('#pageModalJurosDescontos').fadeOut(200);
        $('body').css('overflow', 'scroll');
      })
    }
    modalJurosDescontos()

    function calcularJuros() {
      let jurosTipo = $("#cmbTipoJurosJD").val()
      let jurosValor = $("#inputJurosJD").val()
      let juros = 0

      let valorAPagar = $("#inputValorAPagarJD").val()

      if (jurosTipo == 'P') {
        juros = (valorAPagar * (jurosValor / 100))
      } else {
        juros = jurosValor
      }

      let descontoTipo = $("#cmbTipoDescontoJD").val()
      let descontoValor = $("#inputDescontoJD").val()
      let desconto = 0

      if (descontoTipo == 'P') {
        desconto = (valorAPagar * (descontoValor / 100))
      } else {
        desconto = descontoValor
      }

      let valorTotal = 0


      valorTotal = ((parseFloat(valorAPagar) + parseFloat(juros)) - parseFloat(desconto))

      $("#inputValorTotalAPagarJD").val(float2moeda(valorTotal))
      $("#inputValorTotalPago").val(float2moeda(valorTotal))

    }

    $("#inputJurosJD").keyup(() => {
      calcularJuros()
    })
    $("#inputDescontoJD").keyup(() => {
      calcularJuros()
    })
    $("#cmbTipoJurosJD").change(() => {
      calcularJuros()
    })
    $("#cmbTipoDescontoJD").change(() => {
      calcularJuros()
    })

    function pagamento() {
      let valorTotal = $('#inputValor').val()
      let valorPago = $('#inputValorTotalPago').val()

      let valorTotalf = parseFloat(valorTotal.replace(".", "").replace(",", "."))
      let valorPagof = parseFloat(valorPago.replace(".", "").replace(",", "."))
      let valorRestante = (valorTotalf - valorPagof)

      let planoContas = $("#cmbPlanoContas").val()
      let cmbFornecedor = $("#cmbFornecedor").val()
      let inputDescricao = $("#inputDescricao").val()
      let cmbContaBanco = $("#cmbContaBanco").val()
      let cmbFormaPagamento = $("#cmbFormaPagamento").val()
      let inputNumeroDocumento = $("#inputNumeroDocumento").val()

      if ($("#habilitarPagamento").hasClass('clicado')) {
        $("#cmbContaBanco").prop('required', true)
        $("#cmbFormaPagamento").prop('required', true)
      }
      // && cmbContaBanco != '' && cmbFormaPagamento != '' && inputNumeroDocumento != ''
      if (planoContas != '' && cmbFornecedor != '' && inputDescricao != '') {
        if (valorPagof < valorTotalf && valorPagof) {
          $("#inputPagamentoParcial").val(valorRestante)
          $('#inputValor').val(valorPago)

          // $dataPagamento = $("#inputDataPagamento").val()
          // $valorTotalPago = $("#inputValorTotalPago").val()
          if ($("#habilitarPagamento").hasClass('clicado')) {
            $("#cmbContaBanco").prop('required', true)
            $("#cmbFormaPagamento").prop('required', true)

            confirmaExclusao(document.lancamento,
              "O valor pago é menor que o valor total da conta. Será gerado uma nova conta com o valor restante. Deseja continuar?",
              'contasAPagarNovoLancamento.php');
          } else {
            confirmaExclusao(document.lancamento,
              "O valor pago é menor que o valor total da conta. Será gerado uma nova conta com o valor restante. Deseja continuar?",
              'contasAPagarNovoLancamento.php');
          }

          document.lancamento.submit()
        } else {
          if ($("#habilitarPagamento").hasClass('clicado')) {

            $("#cmbContaBanco").prop('required', true)
            $("#cmbFormaPagamento").prop('required', true)

            $("#lancamento").submit()
          } else {
            $("#cmbContaBanco").prop('required', false)
            $("#cmbFormaPagamento").prop('required', false)
            $("#lancamento").submit()
          }
        }
      } else {
        if ($("#habilitarPagamento").hasClass('clicado')) {

          $("#cmbContaBanco").prop('required', true)
          $("#cmbFormaPagamento").prop('required', true)

          $("#lancamento").submit()
        } else {
          $("#cmbContaBanco").prop('required', false)
          $("#cmbFormaPagamento").prop('required', false)
          $("#lancamento").submit()
        }
      }
    }

    $("#salvar").on('click', (e) => {
      e.preventDefault()
      pagamento()
    })
  })

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

<body class="navbar-top sidebar-right-visible">

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
                  <div class="row">
                    <div class="col-lg-4">
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
                </div>

                <div class="card-body">
                  <?php
                                    if (isset($lancamento)) {
                                        echo '<input type="hidden" name="inputEditar" value="sim">';
                                        echo '<input type="hidden" name="inputContaId" value="' . $lancamento['CnAPaId'] . '">';
                                    }

                                    ?>
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputDataEmissao">Data de Emissão</label>
                        <input type="date" id="inputDataEmissao" name="inputDataEmissao" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaDtEmissao'] ?>" class="form-control" placeholder="Data de Emissão">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="cmbPlanoContas">Plano de Contas <span class="text-danger">*</span></label>
                        <select id="cmbPlanoContas" name="cmbPlanoContas" class="form-control form-control-select2" required>
                          <option value="">Selecionar</option>
                          <?php
                                                    $sql = "SELECT PlConId, PlConNome
												        			FROM PlanoContas
												        			JOIN Situacao on SituaId = PlConStatus
												        			WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
												        			ORDER BY PlConNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);


                                                    foreach ($rowPlanoContas as $item) {
                                                        if (isset($lancamento)) {
                                                            if ($lancamento['CnAPaPlanoContas'] == $item['PlConId']) {
                                                                print('<option value="' . $item['PlConId'] . '" selected>' . $item['PlConNome'] . '</option>');
                                                            } else {
                                                                print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
                                                            }
                                                        } else {
                                                            print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
                                                        }
                                                    }
                                                    ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-5">
                      <div class="form-group">
                        <label for="cmbFornecedor">Fornecedor <span class="text-danger">*</span></label>
                        <select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2" required>
                          <option value="">Selecionar</option>
                          <?php
                                                    $sql = "SELECT ForneId, ForneNome
															FROM Fornecedor
															JOIN Situacao on SituaId = ForneStatus
															WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
															ORDER BY ForneNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($rowFornecedor as $item) {
                                                        if (isset($lancamento)) {
                                                            if ($lancamento['CnAPaFornecedor'] == $item['ForneId']) {
                                                                print('<option value="' . $item['ForneId'] . '" selected>' . $item['ForneNome'] . '</option>');
                                                            } else {
                                                                print('<option value="' . $item['ForneId'] . '">' . $item['ForneNome'] . '</option>');
                                                            }
                                                        } else {
                                                            print('<option value="' . $item['ForneId'] . '">' . $item['ForneNome'] . '</option>');
                                                        }
                                                    }
                                                    ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label for="inputDescricao">Descrição <span class="text-danger">*</span></label>
                        <input type="text" id="inputDescricao" class="form-control" name="inputDescricao" rows="3" required <?php if (isset($lancamento)) echo $lancamento['CnAPaDescricao'] ?>>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputOrdemCarta">Ordem Compra/C. Contrato</label>
                        <input type="text" id="inputOrdemCompra" name="inputOrdemCompra" value="<?php if (isset($lancamento)) echo $lancamento['OrComNumero'] ?>" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="inputNotaFiscal">Nº Nota Fiscal/Documento</label>
                        <input type="text" id="inputNotaFiscal" name="inputNotaFiscal" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaNotaFiscal'] ?>" class="form-control">
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
                                <label for="inputDataPagamento">Data do Pagamento</label>
                                <input type="date" id="inputDataPagamento" value="<?php if (isset($lancamento)) echo $lancamento['CnAPaDtPagamento'] ?>" name="inputDataPagamento" class="form-control" readOnly>
                              </div>
                              <div class="form-group col-6">
                                <label for="inputValorTotalPago">Valor Total Pago (=)</label>
                                <input type="text" onKeyUp="moeda(this)" maxLength="12" id="inputValorTotalPago" name="inputValorTotalPago" value="<?php if (isset($lancamento)) echo mostraValor($lancamento['CnAPaValorPago']) ?>" class="form-control">
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
                        <textarea id="inputObservacao" class="form-control" name="inputObservacao" rows="3"></textarea>
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
                      <label for="inputVencimentoJD">Data do Vencimento</label>
                      <input id="inputVencimentoJD" class="form-control" type="date" name="inputVencimentoJD" readOnly>
                    </div>
                    <div class="form-group">
                      <label for="inputValorAPagarJD">Valor à Pagar</label>
                      <input id="inputValorAPagarJD" onKeyUp="moeda(this)" maxLength="12" class="form-control" type="text" name="inputValorAPagarJD" readOnly>
                    </div>
                  </div>
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
                      <label for="inputDataPagamentoJD">Data do Pagamento</label>
                      <input id="inputDataPagamentoJD" value="<?php echo date("Y-m-d") ?>" class="form-control" type="date" name="inputDataPagamentoJD" readOnly>
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