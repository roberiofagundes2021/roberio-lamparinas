<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Relação de Movimentações Financeiras';

include('global_assets/php/conexao.php');
/*ClienId, ClienNome, ClienCpf, ClienCnpj, ClienTelefone, ClienCelular, ClienStatus, Cate*/
try {
    $sql = "SELECT *
		FROM Cliente
	    WHERE ClienUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY ClienNome ASC";
    $result = $conn->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);
    //$count = count($row);
} catch (Exception $e) {
    echo ($e);
}

$d = date("d");
$m = date("m");
$Y = date("Y");

// $dataInicio = date("Y-m-01"); //30 dias atrás
$dataInicio = date("Y-m-d");
$dataFim = date("Y-m-d");

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

  <script type="text/javascript">
  $(document).ready(function() {
    let resultadosConsulta = '';
    let inputsValues = {};

    $.fn.dataTable.moment('DD/MM/YYYY'); //Para corrigir a ordenação por data			

    /* Início: Tabela Personalizada */
    $('#tblMovimentacaoFinanceira').DataTable({
      "order": [
        [1, "desc"]
      ],
      autoWidth: false,
      responsive: true,
      columnDefs: [{
          orderable: true, //Data
          width: "10%",
          targets: [0]
        },
        {
          orderable: true, //Histórico
          width: "25%",
          targets: [1]
        },
        {
          orderable: true, //Conta Caixa
          width: "22%",
          targets: [2]
        },
        {
          orderable: true, //Nª doc
          width: "8%",
          targets: [3]
        },
        {
          orderable: true, //Entrada
          width: "10%",
          targets: [4]
        },
        {
          orderable: true, //Saída
          width: "10%",
          targets: [5]
        },
        {
          orderable: true, //Saldo
          width: "10%",
          targets: [6]
        },
        {
          orderable: false, //Ações
          width: "5%",
          targets: [6]
        }
      ],
      dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
      language: {
        search: '<span>Filtro:</span> _INPUT_',
        searchPlaceholder: 'filtra qualquer coluna...',
        lengthMenu: '<span>Mostrar:</span> _MENU_',
        paginate: {
          'first': 'Primeira',
          'last': 'Última',
          'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
          'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
        }
      }
    });

    function excluirConta() {
      let contas = $('.excluirConta').each((i, elem) => {
        $(elem).on('click', (e) => {
          const id = $(elem).attr('idContaExcluir');
          const tipo = $(elem).attr('tipo');

          $('#idMov').val(id);
          $('#tipoMov').val(tipo);

          e.preventDefault;
          confirmaExclusao(document.contaExclui, "Tem certeza que deseja excluir essa Conta?", `movimentacaoFinanceiraExclui.php`);
          document.contaExclui.submit();
        })
      })

    }
    excluirConta();

    function atualizaTotal() {
      let childres = $('tbody').children()
      let total = 0
      let linhas = childres.splice(1, childres.length)
      linhas.forEach(elem => {
        let listaTds = $(elem).children()
        let valor = $(listaTds[5]).html()
        let valorFormFloat = parseFloat(valor.replace(".", "").replace(",", "."))

        total += valorFormFloat
      })
      $('#footer-total').remove()

      if (total < 0) {
        divTotal = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(total)}</div>`
      } else {
        divTotal = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(total)}</div>`
      }

      $('.datatable-footer').append(divTotal);
    }


    function Filtrar(carregamentoPagina) {
      let cont = false;

      const msg = $('<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty"><img src="global_assets/images/lamparinas/loader.gif" style="width: 120px"></td></tr>');

      $('tbody').html(msg);

      const periodoDe = $('#inputPeriodoDe').val();
      const ate = $('#inputAte').val();
      const contaBanco = $('#cmbContaBanco').val();
      const centroDeCustos = $('#cmbCentroDeCustos').val();
      const planoContas = $('#cmbPlanoContas').val();
      const FormaPagamento = $('#cmbFormaDeRecebimento').val();
      const statusArray = $('#cmbStatus').val().split('|');
      const status = statusArray[0];
      const statusTipo = statusArray[1];
      const url = "movimentacaoFinanceiraFiltra.php";
      const tipoFiltro = carregamentoPagina ? 'CarregamentoPagina' : 'FiltroNormal';

      inputsValues = {
        inputPeriodoDe: periodoDe,
        inputAte: ate,
        cmbContaBanco: contaBanco,
        cmbCentroDeCustos: centroDeCustos,
        cmbPlanoContas: planoContas,
        cmbFormaDeRecebimento: FormaPagamento,
        cmbStatus: status,
        statusTipo: statusTipo,
        tipoFiltro: tipoFiltro
      };

      $.post(
        url,
        inputsValues,
        (data) => {
          if (data) {
            $('tbody').html(data)
            $('#imprimir').removeAttr('disabled')
            resultadosConsulta = data

            excluirConta();
            atualizaTotal();

          } else {
            let msg2 = $(
              '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>'
            )
            $('tbody').html(msg2)
            $('#imprimir').attr('disabled', '')
            $('#footer-total').remove()
          }
        }
      );
    }

    $('#submitPesquisar').on('click', (e) => {
      e.preventDefault()
      Filtrar(false);
    })

    Filtrar(true);

    $('#novoLacamento').on('click', (e) => {
      location.href = "movimentacaoFinanceiraPagamento.php";
      return false;
    })

    function imprime() {
      let url = 'movimentacaoFinanceiraImprime.php';

      $('#imprimir').on('click', (e) => {
        console.log(resultadosConsulta);
        e.preventDefault()
        if (resultadosConsulta) {
          $('#inputResultado').val(resultadosConsulta)
          $('#inputDataDe_imp').val(inputsValues.inputPeriodoDe)
          $('#inputDataAte_imp').val(inputsValues.inputAte)
          $('#cmbContaBanco_imp').val(inputsValues.cmbContaBanco)
          $('#cmbCentroDeCustos_imp').val(inputsValues.cmbCentroDeCustos)
          $('#cmbPlanoContas_imp').val(inputsValues.cmbPlanoContas)
          $('#cmbFormaDeRecebimento_imp').val(inputsValues.cmbFormaDeRecebimento)
          $('#inputStatus_imp').val(inputsValues.cmbStatus)
          $('#inputStatusTipo_imp').val(inputsValues.statusTipo)


          $('#formImprime').attr('action', url)

          $('#formImprime').submit()
        }
      })
    }
    imprime()
  });
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

        <!-- Info blocks -->
        <div class="row">
          <div class="col-lg-12">

            <!-- Basic responsive configuration -->
            <div class="card">
              <div class="card-header">
                <div class="header-elements-inline">
                  <h3 class="card-title">Relação de Movimentações Financeiras</h3>
                  <div class="header-elements">
                    <div class="list-icons">
                      <a class="list-icons-item" data-action="collapse"></a>
                      <a href="relatorioMovimentacao.php" class="list-icons-item" data-action="reload"></a>
                    </div>
                  </div>
                </div>
                <br>
                <p>A relação abaixo faz referência às movimentações financeiras da empresa <?php echo($_SESSION['EmpreNomeFantasia']) ?></p>
              </div>

              <div class="card-body">

                <form id="formImprime" method="POST" target="_blank">
                  <input id="inputResultado" type="hidden" name="resultados"></input>
                  <input id="inputDataDe_imp" type="hidden" name="inputDataDe_imp"></input>
                  <input id="inputDataAte_imp" type="hidden" name="inputDataAte_imp"></input>
                  <input id="cmbContaBanco_imp" type="hidden" name="cmbContaBanco_imp"></input>
                  <input id="cmbCentroDeCustos_imp" type="hidden" name="cmbCentroDeCustos_imp"></input>
                  <input id="cmbPlanoContas_imp" type="hidden" name="cmbPlanoContas_imp"></input>
                  <input id="cmbFormaDeRecebimento_imp" type="hidden" name="cmbFormaDeRecebimento_imp"></input>
                  <input id="inputStatus_imp" type="hidden" name="inputStatus_imp"></input>
                  <input id="inputStatusTipo_imp" type="hidden" name="inputStatusTipo_imp"></input>
                  <input id="inputTipoFiltro_imp" type="hidden" name="inputTipoFiltro_imp"></input>
                </form>

                <form name="contaExclui" method="POST">
                  <input type="hidden" name="idMov" id="idMov">
                  <input type="hidden" name="tipoMov" id="tipoMov">
                </form>

                <form name="formMovimentacao" method="post" class="p-3">
                  <div class="row">
                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="inputPeriodoDe">Período de</label>
                        <div class="input-group">
                          <span class="input-group-prepend">
                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                          </span>
                          <input type="date" id="inputPeriodoDe" name="inputPeriodoDe" class="form-control" value="<?php 
                                          if (isset($_SESSION['MovFinancPeriodoDe'])) {
                                            echo $_SESSION['MovFinancPeriodoDe'];
                                          }else 
                                            echo $dataInicio; 
                                        ?>">
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="inputAte">Até</label>
                        <div class="input-group">
                          <span class="input-group-prepend">
                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                          </span>
                          <input type="date" id="inputAte" name="inputAte" class="form-control" value="<?php 
                                          if (isset($_SESSION['MovFinancAte'])) 
                                            echo $_SESSION['MovFinancAte'];
                                          else 
                                            echo $dataFim; 
                                        ?>">
                        </div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="cmbContaBanco">Conta / Banco</label>
                        <select id="cmbContaBanco" name="cmbContaBanco" class="form-control form-control-select2">
                          <option value="">Todos</option>
                          <?php
                                                    $sql = "SELECT CnBanId,
                                                                   CnBanNome
                                                              FROM ContaBanco
                                                              JOIN Situacao 
                                                                ON SituaId = CnBanStatus
                                                             WHERE CnBanUnidade = " . $_SESSION['UnidadeId'] . " 
                                                               and SituaChave = 'ATIVO'
                                                          ORDER BY CnBanNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($rowContaBanco as $item) {
                                                      print('<option value="' . $item['CnBanId'] . '">' . $item['CnBanNome'] . '</option>');
                                                    }

                                                    ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="cmbCentroDeCustos">Centro de Custos</label>
                        <select id="cmbCentroDeCustos" name="cmbCentroDeCustos" class="form-control form-control-select2">
                          <option value="">Todos</option>
                          <?php
                                                    $sql = "SELECT CnCusId,
                                                                   CnCusNome
                                                              FROM CentroCusto
                                                              JOIN Situacao 
                                                                ON SituaId = CnCusStatus
                                                             WHERE CnCusUnidade = " . $_SESSION['UnidadeId'] . " 
                                                               and SituaChave = 'ATIVO'
                                                          ORDER BY CnCusNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($rowCentroDeCustos as $item) {
                                                      print('<option value="' . $item['CnCusId'] . '">' . $item['CnCusNome'] . '</option>');
                                                    }

                                                    ?>
                        </select>
                      </div>
                    </div>


                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="cmbPlanoContas">Plano de Contas</label>
                        <select id="cmbPlanoContas" name="cmbPlanoContas" class="form-control form-control-select2">
                          <option value="">Todos</option>
                          <?php
                                                    $sql = "SELECT PlConId, PlConNome
                                                              FROM PlanoContas
                                                              JOIN Situacao 
                                                                ON SituaId = PlConStatus
                                                             WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " 
                                                               AND SituaChave = 'ATIVO'
                                                          ORDER BY PlConNome ASC";
                                                    $result = $conn->query($sql);
                                                    $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($rowPlanoContas as $item) {
                                                      print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
                                                    }

                                                    ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-3">
                      <div class="form-group">
                        <label for="cmbFormaDeRecebimento">Forma de Pagamento/Recebimento</label>
                        <select id="cmbFormaDeRecebimento" name="cmbFormaDeRecebimento" class="form-control form-control-select2">
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
                                                    $rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);

                                                    try {
                                                        print('<option value=0  selected>Todos</option>');

                                                        foreach ($rowSituacao as $item) {
                                                            if (isset($item['FrPagId'])) {
                                                                print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
                                                                echo ($item['FrPagId']);
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                                                    }
                                                    ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-2">
                      <div class="form-group">
                        <label for="cmbStatus">Status</label>
                        <select id="cmbStatus" name="cmbStatus" class="form-control form-control-select2">
                          <option value="">Todos</option>
                          <?php
                                                    try {
                                                        $sql = "SELECT SituaId, SituaNome, SituaChave
                                                                FROM Situacao
                                                                WHERE SituaStatus = 1
                                                                ORDER BY SituaNome ASC";
                                                        $result = $conn->query($sql);
                                                        $rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);

                                                        try {
                                                            foreach ($rowSituacao as $item) {
                                                                if ($item['SituaChave'] == 'RECEBIDA' || $item['SituaChave'] === 'PAGA' || $item['SituaChave'] === 'TRANSFERIDA') {
                                                                    if (isset($_SESSION['ContPagStatus'])) {
                                                                        if ($item['SituaId'] == $_SESSION['ContPagStatus']) {
                                                                            print('<option value="' . $item['SituaId'] . '|' . $item['SituaChave'] . '" selected>' . $item['SituaNome'] . '</option>');
                                                                        } else {
                                                                            print('<option value="' . $item['SituaId'] . '|' . $item['SituaChave'] . '">' . $item['SituaNome'] . '</option>');
                                                                        }
                                                                    } else {
                                                                        print('<option value="' . $item['SituaId'] . '|' . $item['SituaChave'] . '">' . $item['SituaNome'] . '</option>');
                                                                    }
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                                                        }
                                                    } catch (Exception $e) {
                                                        echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                                                    }
                                                    ?>
                        </select>
                      </div>
                    </div>

                    <div class="text-right col-lg-4 pt-3">
                      <button id="submitPesquisar" class="btn btn-principal">Pesquisar</button>

                      <button id="novoLacamento" class="btn btn-outline bg-slate-600 text-slate-600 border-slate">Novo Lançamento</button>

                      <button id="imprimir" class="btn bg-secondary"><i class="icon-printer2"></i></button>
                    </div>

                  </div>
                </form>

                <table class="table" id="tblMovimentacaoFinanceira">
                  <thead>
                    <tr class="bg-slate">
                      <th>Data</th>
                      <th>Histórico</th>
                      <th>Conta / Banco</th>
                      <th>Documento</th>
                      <th style='text-align: right;'>Entrada</th>
                      <th style='text-align: right;'>Saída</th>
                      <th style='text-align: right;'>Saldo</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                  <tfoot>
                    <div style="width: 100%; background-color: red">

                    </div>
                  </tfoot>
                </table>

              </div>

            </div>

            <!-- /basic responsive configuration -->
          </div>
        </div>

        <!-- /info blocks -->
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