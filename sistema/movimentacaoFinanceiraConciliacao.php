<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Relação de Movimentações Financeiras';
$_SESSION['Conciliacao'] = true;

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
      paginate: false,
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
          orderable: true, //Nª doc
          width: "8%",
          targets: [2]
        },
        {
          orderable: true, //Entrada
          width: "10%",
          targets: [3]
        },
        {
          orderable: true, //Saída
          width: "10%",
          targets: [4]
        },
        {
          orderable: true, //Saldo
          width: "10%",
          targets: [5]
        },
        {
          orderable: true, //Saldo Conciliado
          width: "19%",
          targets: [6]
        },
        {
          orderable: true, //Situação
          width: "5%",
          targets: [7]
        }
        ,
        {
          orderable: false, //Ações
          width: "3%",
          targets: [8]
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

    function atualizaTotal() {
      let childres = $('tbody').children();
      let totalEntrada = 0;
      let totalSaida = 0; 
      let totalSaldo = 0;
      let totalSaldoConciliado = 0;
      let linhas = childres.splice(1, childres.length);

      linhas.forEach(elem => {
        let valorFormFloatEntrada = 0;
        let listaTds = $(elem).children();
        let valorEntrada = $(listaTds[3]).html();
        valorFormFloatEntrada = isNaN(valorEntrada) ? parseFloat(valorEntrada.replace(".", "").replace(",", ".")) : 0;
        totalEntrada += valorFormFloatEntrada;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaida = 0;
        let listaTds = $(elem).children();
        let valorSaida = $(listaTds[4]).html();
        valorFormFloatSaida = isNaN(valorSaida) ?  parseFloat(valorSaida.replace(".", "").replace(",", ".")) : 0;
        totalSaida += valorFormFloatSaida;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaldo = 0;
        let listaTds = $(elem).children();
        let valorSaldo = $(listaTds[5]).html();
        valorFormFloatSaldo = isNaN(valorSaldo) ? parseFloat(valorSaldo.replace(".", "").replace(",", ".")) : 0;
        totalSaldo += valorFormFloatSaldo;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaldoConciliado = 0;
        let listaTds = $(elem).children();
        let valorSaldoConciliado = $(listaTds[6]).html();
        let conciliado = $(listaTds[7]).html().split(' ');
        conciliado = conciliado[48].split('=');
        conciliado = conciliado[1].replace(/[^\d]+/g,'');
        
        if (parseInt(conciliado) > 0) {
          valorFormFloatSaldoConciliado = isNaN(valorSaldoConciliado) ? parseFloat(valorSaldoConciliado.replace(".", "").replace(",", ".")) : 0;
        } 
        totalSaldoConciliado += valorFormFloatSaldoConciliado;
      });

      $('#footer-total').remove();
      totalEntrada < 0 ? divTotalEntrada = `<div id='footer-total' style='position:absolute; left: 51%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalEntrada)}</div>` : divTotalEntrada = `<div id='footer-total' style='position:absolute; left: 51%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalEntrada)}</div>`;

      $('#footer-total').remove();
      totalSaida < 0 ? divTotalSaida = `<div id='footer-total' style='position:absolute; left: 61%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaida)}</div>` : divTotalSaida = `<div id='footer-total' style='position:absolute; left: 61%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaida)}</div>`;

      $('#footer-total').remove();
      totalSaldo < 0 ? divTotalSaldo = `<div id='footer-total' style='position:absolute; left: 71%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaldo)}</div>` : divTotalSaldo = `<div id='footer-total' style='position:absolute; left: 71%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaldo)}</div>`;

      $('#footer-total').remove();
      totalSaldoConciliado < 0 ? divTotalSaldoConciliado = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaldoConciliado)}</div>` : divTotalSaldoConciliado = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaldoConciliado)}</div>`;

      $('.datatable-footer').append(divTotalEntrada);
      $('.datatable-footer').append(divTotalSaida);
      $('.datatable-footer').append(divTotalSaldo);
      $('.datatable-footer').append(divTotalSaldoConciliado);
    }


    function Filtrar(carregamentoPagina) {
      console.log('entrou');
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
      const url = "movimentacaoFinanceiraConciliacaoFiltra.php";
      const tipoFiltro = carregamentoPagina ? 'CarregamentoPagina' : 'FiltroNormal';

      var inputsValues = {
        inputPeriodoDe: periodoDe,
        inputAte: ate,
        cmbContaBanco: contaBanco,
        cmbCentroDeCustos: centroDeCustos,
        cmbPlanoContas: planoContas,
        cmbFormaDeRecebimento: FormaPagamento,
        cmbStatus: status,
        statusTipo: statusTipo,
        tipoFiltro: tipoFiltro,
      };

      /*
      $.post(
        url,
        inputsValues,
        (data) => {
          if (data) {
            $('tbody').html(data)
            $('#imprimir').removeAttr('disabled')
            resultadosConsulta = data

            atualizaTotal();

          } else {
            let msg2 = $(
              '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>'
            )
            $('tbody').html(msg2)
            $('#imprimir').attr('disabled', '')
            $('#footer-total').remove()
            $('#footer-total').remove()
            $('#footer-total').remove()
            $('#footer-total').remove()
          }
        }
      );
      */

      $.ajax({
          type: "POST",
          url: url,
          dataType: "json",
          data: inputsValues,
          success: function(resposta) {
            //|--Aqui é criado o DataTable caso seja a primeira vez q é executado e o clear é para evitar duplicação na tabela depois da primeira pesquisa
            let table 
            table = $('#tblMovimentacaoFinanceira').DataTable()
            table = $('#tblMovimentacaoFinanceira').DataTable().clear().draw()
            //--|

            table = $('#tblMovimentacaoFinanceira').DataTable()

            let rowNode
            let entrada = 0
            let entradaTotal = 0
            let saida = 0
            let saidaTotal = 0
            let saldo = 0
            let saldoTotal = 0
            let saldoConciliacao = 0
            let saldoConciliacaoTotal = 0

            resposta.forEach(item => {
              rowNode = table.row.add(item.data).draw().node()

              saldo = parseFloat(item.data[5].replace(",", "."))
              saldoConciliado = parseFloat(item.data[6].replace(",", "."))

              // adiciona os atributos nas tags <td>
              $(rowNode).find('td').eq(3).attr('style', 'text-align: right; color: green;')
              $(rowNode).find('td').eq(4).attr('style', 'text-align: right; color: red;')
              
              if(saldo >= 0) {
                $(rowNode).find('td').eq(5).attr('style', 'text-align: right; color: green;')
              }else {
                $(rowNode).find('td').eq(5).attr('style', 'text-align: right; color: red;')
              }

              if(saldoConciliado >= 0) {
                $(rowNode).find('td').eq(6).attr('style', 'text-align: right; color: green;')
              }else {
                $(rowNode).find('td').eq(6).attr('style', 'text-align: right; color: red;')
              }

              $(rowNode).find('td').eq(8).attr('style', 'text-align: center;')

              entrada = item.data[3].replace(".", "")
              entrada = entrada.replace(",", ".")
              entradaTotal += parseFloat(entrada)

              saida = (item.data[4] != null) ? item.data[4] : '0,00'
              saida = saida.replace(".", "")
              saida = saida.replace(",", ".")
              saidaTotal += parseFloat(saida)

              saldo = item.data[5].replace(".", "")
              saldo = saldo.replace(",", ".")
              saldoTotal += parseFloat(saldo)

              saldoConciliacao = item.data[6].replace(".", "")
              saldoConciliacao = saldoConciliacao.replace(",", ".")
              saldoConciliacaoTotal += parseFloat(saldoConciliacao)
            })

            saidaTotal = (saidaTotal > 0) ? -Math.abs(saidaTotal) : saidaTotal
            corSaldoTotal = (saldoTotal >= 0) ? 'green' : 'red'
            corConciliacaoTotal = (saldoConciliacaoTotal >= 0) ? 'green' : 'red'

            divTotal = `
              <div id='footer-total' class='row' style='position:absolute; text-align: right; font-weight: bold; width: 100%; margin-top: 0.9%; font-size: 10px;'>
                <div style="width: 47.2%; color: green;">
                  Total: ${float2moeda(entradaTotal)}
                </div>

                <div style="width: 9.1%; color: red;">
                  Total: ${float2moeda(saidaTotal)}
                </div>

                <div style="width: 9.1%; color: ${corSaldoTotal};">
                  Total: ${float2moeda(saldoTotal)}
                </div>

                <div style="width: 14.8%; color: ${corConciliacaoTotal};">
                  Total: ${float2moeda(saldoConciliacaoTotal)}
                </div>`                    

            $('#footer-total').remove() //Para evitar que os valores se sobrescrevam
            
            $('.datatable-footer').append(divTotal)
          },
          error: function(e) { 
            table = $('#tblMovimentacaoFinanceira').DataTable()
            table = $('#tblMovimentacaoFinanceira').DataTable().clear().draw()


            let tabelaVazia = $(
              '<tr class="odd"><td valign="top" colspan="7" class="dataTables_empty">Sem resultados...</td></tr>'
            )

            $('tbody').html(tabelaVazia)

            $('#footer-total').remove()
          }
        })
    }

    $('#submitPesquisar').on('click', (e) => {
      e.preventDefault();
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

     //Ao mudar a centro de custo, filtra o Plano de Contas via ajax (retorno via JSON)
     $('#cmbCentroDeCustos').on('change', function(e) {

        FiltraPlanoContas();

          var cmbCentroDeCustos = $('#cmbCentroDeCustos').val();

          $.getJSON('filtraPlanoContas.php?idCentroCusto=' + cmbCentroDeCustos, function(dados) {

            var option = '<option value="">Todos</option>';

            if (dados.length) {

              $.each(dados, function(i, obj) {
                option += '<option value="' + obj.PlConId + '">' + obj.PlConCodigo + ' - ' + obj.PlConNome + '</option>';
              });

              $('#cmbPlanoContas').html(option).show();
            } else {
              ResetPlanoContas();
            }
          });
        }); 

        function FiltraPlanoContas() {
          $('#cmbPlanoContas').empty().append('<option value="">Filtrando...</option>');    
          }

          function ResetPlanoContas() {
          $('#cmbPlanoContas').empty().append('<option value="">Sem Plano de Contas</option>');
          }    

  });


  const atualizarConciliado = () => {
    event.preventDefault();

    const custom = event.target.id.split('#');
    custom.push(event.target.value == 1 ? 0 : 1);

    /*
    function atualizaTotal() {
      let childres = $('tbody').children();
      let totalEntrada = 0;
      let totalSaida = 0; 
      let totalSaldo = 0;
      let totalSaldoConciliado = 0;
      let linhas = childres.splice(1, childres.length);

      linhas.forEach(elem => {
        let valorFormFloatEntrada = 0;
        let listaTds = $(elem).children();
        let valorEntrada = $(listaTds[3]).html();
        valorFormFloatEntrada = isNaN(valorEntrada) ? parseFloat(valorEntrada.replace(".", "").replace(",", ".")) : 0;
        totalEntrada += valorFormFloatEntrada;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaida = 0;
        let listaTds = $(elem).children();
        let valorSaida = $(listaTds[4]).html();
        valorFormFloatSaida = isNaN(valorSaida) ?  parseFloat(valorSaida.replace(".", "").replace(",", ".")) : 0;
        totalSaida += valorFormFloatSaida;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaldo = 0;
        let listaTds = $(elem).children();
        let valorSaldo = $(listaTds[5]).html();
        valorFormFloatSaldo = isNaN(valorSaldo) ? parseFloat(valorSaldo.replace(".", "").replace(",", ".")) : 0;
        totalSaldo += valorFormFloatSaldo;
      });

      linhas.forEach(elem => {
        let valorFormFloatSaldoConciliado = 0;
        let listaTds = $(elem).children();
        let valorSaldoConciliado = $(listaTds[6]).html();
        let conciliado = $(listaTds[7]).html().split(' ');
        conciliado = conciliado[48].split('=');
        conciliado = conciliado[1].replace(/[^\d]+/g,'');
        
        if (parseInt(conciliado) > 0) {
          valorFormFloatSaldoConciliado = isNaN(valorSaldoConciliado) ? parseFloat(valorSaldoConciliado.replace(".", "").replace(",", ".")) : 0;
        } 
        totalSaldoConciliado += valorFormFloatSaldoConciliado;
      });

      $('#footer-total').remove();
      totalEntrada < 0 ? divTotalEntrada = `<div id='footer-total' style='position:absolute; left: 52%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalEntrada)}</div>` : divTotalEntrada = `<div id='footer-total' style='position:absolute; left: 52%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalEntrada)}</div>`;

      $('#footer-total').remove();
      totalSaida < 0 ? divTotalSaida = `<div id='footer-total' style='position:absolute; left: 61%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaida)}</div>` : divTotalSaida = `<div id='footer-total' style='position:absolute; left: 61%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaida)}</div>`;

      $('#footer-total').remove();
      totalSaldo < 0 ? divTotalSaldo = `<div id='footer-total' style='position:absolute; left: 71%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaldo)}</div>` : divTotalSaldo = `<div id='footer-total' style='position:absolute; left: 71%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaldo)}</div>`;

      $('#footer-total').remove();
      totalSaldoConciliado < 0 ? divTotalSaldoConciliado = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:red;'>Total: ${float2moeda(totalSaldoConciliado)}</div>` : divTotalSaldoConciliado = `<div id='footer-total' style='position:absolute; left: 86.8%; font-weight: bold; width: 200px; color:green;'>Total: ${float2moeda(totalSaldoConciliado)}</div>`;

      $('.datatable-footer').append(divTotalEntrada);
      $('.datatable-footer').append(divTotalSaida);
      $('.datatable-footer').append(divTotalSaldo);
      $('.datatable-footer').append(divTotalSaldoConciliado);
    }
    */


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
      const url = "movimentacaoFinanceiraConciliacaoFiltra.php";
      const tipoFiltro = carregamentoPagina ? 'CarregamentoPagina' : 'FiltroNormal';

      var inputsValues = {
        inputPeriodoDe: periodoDe,
        inputAte: ate,
        cmbContaBanco: contaBanco,
        cmbCentroDeCustos: centroDeCustos,
        cmbPlanoContas: planoContas,
        cmbFormaDeRecebimento: FormaPagamento,
        cmbStatus: status,
        statusTipo: statusTipo,
        tipoFiltro: tipoFiltro,
        tpConciliado: custom[1],
        valorConciliado: custom[2],
        idConciliado: custom[0],
      };

      $.post(
        url,
        inputsValues,
        (data) => {
          if (data) {
            $('tbody').html(data)
            $('#imprimir').removeAttr('disabled')
            resultadosConsulta = data

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

    Filtrar(true);
  }

  function atualizaConciliacao(idConciliacao, tipoConta) {
    document.getElementById('inputConciliacaoId').value = idConciliacao;
    document.getElementById('inputPermissionAtualiza').value = 1;
    
    if(tipoConta == 'ContaAReceber') {
      document.formEditaConciliacao.action = "contasAReceberNovoLancamento.php";
    }else if(tipoConta == 'ContaAPagar') {
      document.formEditaConciliacao.action = "contasAPagarNovoLancamento.php";
    }
    
    document.formEditaConciliacao.submit();
  }
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
                  <h3 class="card-title">Conciliação das Movimentações Financeiras</h3>
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
                          <input type="date" id="inputPeriodoDe" name="inputPeriodoDe" class="form-control" min="1800-01-01" max="2100-12-12" value="<?php 
                                          if (isset($_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe'])) {
                                            echo $_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe'];
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
                          <input type="date" id="inputAte" name="inputAte" class="form-control" min="1800-01-01" max="2100-12-12" value="<?php 
                                          if (isset($_SESSION['MovimentacaoFinanceiraConciliacaoAte'])) 
                                            echo $_SESSION['MovimentacaoFinanceiraConciliacaoAte'];
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
                            $sql = "SELECT CnCusId, CnCusNome, CnCusCodigo
                                      FROM CentroCusto
                                      JOIN Situacao 
                                        ON SituaId = CnCusStatus
                                      WHERE CnCusUnidade = " . $_SESSION['UnidadeId'] . " 
                                        and SituaChave = 'ATIVO'
                                  ORDER BY CnCusCodigo ASC";
                            $result = $conn->query($sql);
                            $rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($rowCentroDeCustos as $item) {
                              print('<option value="' . $item['CnCusId'] . '">'. $item['CnCusCodigo'] .' - '. $item['CnCusNome'] . '</option>');
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
                              $sql = "SELECT PlConId, PlConNome, PlConCodigo
                                        FROM PlanoConta
                                        JOIN Situacao 
                                          ON SituaId = PlConStatus
                                        WHERE PlConUnidade = " . $_SESSION['UnidadeId'] . " 
                                          AND SituaChave = 'ATIVO'
                                    ORDER BY PlConCodigo ASC";
                              $result = $conn->query($sql);
                              $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);

                              foreach ($rowPlanoContas as $item) {
                                print('<option value="' . $item['PlConId'] . '">'. $item['PlConCodigo'] .' - '. $item['PlConNome'] . '</option>');
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
                      <th>Documento</th>
                      <th style='text-align: right;'>Entrada</th>
                      <th style='text-align: right;'>Saída</th>
                      <th style='text-align: right;'>Saldo</th>
                      <th style='text-align: right;'>Saldo Conciliado</th>
                      <th>Situação</th>
                      <th>Conciliado</th>
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
				<form name="formEditaConciliacao" method="post">
					<input type="hidden" id="inputConciliacaoId" name="inputConciliacaoId" >
          <input type="hidden" id="inputPermissionAtualiza" name="inputPermissionAtualiza" >
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