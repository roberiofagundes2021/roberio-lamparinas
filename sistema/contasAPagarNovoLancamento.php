<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Lançamento';

include('global_assets/php/conexao.php');

if(isset($_POST['cmbPlanoContas'])){

    if(isset($_POST['inputEditar'])){

        try{

            $sql = "SELECT SituaId
		            FROM Situacao
		            WHERE SituaChave = 'APAGAR'";
            $result = $conn->query($sql);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
		    
		    $sql = "UPDATE ContasAPagar SET CnAPaPlanoContas = :iPlanoContas, CnAPaFornecedor = :iFornecedor, CnAPaContaBanco = :iContaBanco, CnAPaFormaPagamento = :iFormaPagamento, CnAPaNumDocumento = :sNumDocumento,
                                            CnAPaNotaFiscal = :sNotaFiscal, CnAPaDtEmissao = :dateDtEmissao, CnAPaOrdemCompra = :iOrdemCompra, CnAPaDescricao = :sDescricao, CnAPaDtVencimento = :dateDtVencimento, CnAPaValorAPagar = :fValorAPagar,
                                            CnAPaDtPagamento = :dateDtPagamento, CnAPaValorPago = :fValorPago, CnAPaObservacao = :sObservacao, CnAPaStatus = :iStatus, CnAPaUsuarioAtualizador = :iUsuarioAtualizador, CnAPaUnidade = :iUnidade,
                                            CnAPaTipoJuros = :sTipoJuros, CnAPaJuros = :fJuros, CnAPaTipoDesconto = :sTipoDesconto, CnAPaDesconto = :fDesconto
		    		WHERE CnAPaId = ".$_POST['inputContaId']."";
		    $result = $conn->prepare($sql);
		    		
		    $result->execute(array(
                                ':iPlanoContas' => $_POST['cmbPlanoContas'],
                                ':iFornecedor' => $_POST['cmbFornecedor'],
                                ':iContaBanco' => $_POST['cmbContaBanco'],
                                ':iFormaPagamento' => $_POST['cmbFormaPagamento'],
                                ':sNumDocumento' => $_POST['inputNumeroDocumento'],
                                ':sNotaFiscal' => $_POST['inputNotaFiscal'],
                                ':dateDtEmissao' => $_POST['inputDataEmissao'],
                                ':iOrdemCompra' => $_POST['cmbOrdemCarta'],
                                ':sDescricao' => $_POST['inputDescricao'],
                                ':dateDtVencimento' => $_POST['inputDataVencimento'],
                                ':fValorAPagar' => (float)$_POST['inputValor'],
                                ':dateDtPagamento' => $_POST['inputDataPagamento'],
                                ':fValorPago' => isset($_POST['inputValorTotalPago']) ? (float)$_POST['inputValorTotalPago'] : null,
                                ':sObservacao' => $_POST['inputObservacao'],
                                ':sTipoJuros' => isset($_POST['cmbTipoJurosJD']) ? $_POST['cmbTipoJurosJD'] : null,
                                ':fJuros' => isset($_POST['inputJurosJD']) ? (float)$_POST['inputJurosJD'] : null,
                                ':sTipoDesconto' => isset($_POST['cmbTipoDescontoJD']) ? $_POST['cmbTipoDescontoJD'] : null,
                                ':fDesconto' => isset($_POST['inputDescontoJD']) ? $_POST['inputDescontoJD'] : null,
                                ':iStatus' => $situacao['SituaId'],
                                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                                ':iUnidade' => $_SESSION['UnidadeId']
                            ));
                        
            $_SESSION['msg']['titulo'] = "Sucesso";
            $_SESSION['msg']['mensagem'] = "Lançamento editado!!!";
            $_SESSION['msg']['tipo'] = "success";
            
        } catch(PDOException $e) {
            
            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Erro ao editar lançamento!!!";
            $_SESSION['msg']['tipo'] = "error";	
            
            echo 'Error: ' . $e->getMessage();die;
        }

    } else {

        try{


            if(isset($_POST['inputNumeroParcelas'])){
                
                $numParcelas = intVal($_POST['inputNumeroParcelas']);
            
                for($i = 1; $i <= $numParcelas; $i++){
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
                            ':iOrdemCompra' => $_POST['cmbOrdemCarta'],
                            ':sDescricao' => $_POST['inputParcelaDescricao'.$i.''],
                            ':dateDtVencimento' => $_POST['inputParcelaDataVencimento'.$i.''],
                            ':fValorAPagar' => (float)$_POST['inputParcelaValorAPagar'.$i.''],
                            ':dateDtPagamento' => $_POST['inputDataPagamento'],
                            ':fValorPago' => isset($_POST['inputValorTotalPago']) ? (float)$_POST['inputValorTotalPago'] : null,
                            ':sObservacao' => $_POST['inputObservacao'],
                            ':iStatus' => $situacao['SituaId'],
                            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                            ':iUnidade' => $_SESSION['UnidadeId']
                    ));
                }
            } else {
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
                                ':iOrdemCompra' => $_POST['cmbOrdemCarta'],
                                ':sDescricao' => $_POST['inputDescricao'],
                                ':dateDtVencimento' => $_POST['inputDataVencimento'],
                                ':fValorAPagar' => (float)$_POST['inputValor'],
                                ':dateDtPagamento' => $_POST['inputDataPagamento'],
                                ':fValorPago' => isset($_POST['inputValorTotalPago']) ? (float)$_POST['inputValorTotalPago'] : null,
                                ':sObservacao' => $_POST['inputObservacao'],
                                ':iStatus' => $situacao['SituaId'],
                                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                                ':iUnidade' => $_SESSION['UnidadeId']
                                ));
            }
                            
            $_SESSION['msg']['titulo'] = "Sucesso";
            $_SESSION['msg']['mensagem'] = "Lançamento incluído!!!";
            $_SESSION['msg']['tipo'] = "success";
            
        } catch(PDOException $e) {
            
            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Erro ao incluir Lançamento!!!";
            $_SESSION['msg']['tipo'] = "error";	
            
            echo 'Error: ' . $e->getMessage();die;
        }

    }
     
    irpara("contasAPagar.php");
}
//$count = count($row);

if(isset($_GET['lancamentoId'])){
    $sql = "SELECT *
    		FROM ContasAPagar
    		WHERE CnAPaUnidade = " . $_SESSION['UnidadeId'] . " and CnAPaId = ".$_GET['lancamentoId']."";
    $result = $conn->query($sql);
    $lancamento = $result->fetch(PDO::FETCH_ASSOC);
}
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
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <!-- Validação -->
    <script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
    <script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
    <script src="global_assets/js/demo_pages/form_validation.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {

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
                $("#inputValorAPagarJD").val($valorAPagar)
                $("#inputDataPagamentoJD").val($dataPagamento)

            }

            function habilitarPagamento() {

                $("#habilitarPagamento").on('click', (e) => {
                    e.preventDefault()

                    if (!$("#habilitarPagamento").hasClass('clicado')) {
                        $valorTotalPago = $("#inputValor").val()
                        $dataPagamento = new Date
                        $dia = $dataPagamento.getDate()
                        $mes = parseInt($dataPagamento.getMonth()) + 1 <= 9 ? `0${parseInt($dataPagamento.getMonth()) + 1}` : parseInt($dataPagamento.getMonth()) + 1
                        $ano = $dataPagamento.getFullYear()
                        $fullDataPagamento = `${$ano}-${$mes}-${$dia}`

                        $("#inputDataPagamento").val($fullDataPagamento)
                        $("#inputValorTotalPago").val($valorTotalPago).removeAttr('disabled')

                        styleJurosDescontos = document.getElementById('jurusDescontos').style

                        document.getElementById('jurusDescontos').style = "";

                        $("#habilitarPagamento").addClass('clicado')
                        $("#habilitarPagamento").html('Desativar Pagamento')
                        preencherJurosDescontos()
                    } else {

                        $("#inputDataPagamento").val("")
                        $("#inputValorTotalPago").val("")
                        $("#inputValorTotalPago").attr('disabled', '')
                        document.getElementById('jurusDescontos').style =
                            "color: currentColor; cursor: not-allowed; opacity: 0.5; text-decoration: none; pointer-events: none;";

                        $("#habilitarPagamento").removeClass('clicado')
                        $("#habilitarPagamento").html('Habilitar Pagamento')
                        limparJurosDescontos()
                    }

                })
                $("#jurusDescontos")
            }
            habilitarPagamento()

            function modalParcelar() {

                $('#btnParcelar').on('click', (e) => {
                    e.preventDefault()
                    $('#pageModalParcelar').fadeIn(200);

                    let valorTotal = $('#inputValor').val()
                    $('#valorTotal').val(valorTotal)
                })

                $('#modalCloseParcelar').on('click', function () {
                    $('#pageModalParcelar').fadeOut(200);
                    $('body').css('overflow', 'scroll');
                    $("#parcelasContainer").html("")
                })

                $("#salvarParcelas").on('click', function () {
                    $('#pageModalParcelar').fadeOut(200);
                    $('body').css('overflow', 'scroll');
                })
            }
            modalParcelar()

            function modalJurosDescontos() {
                $('#jurusDescontos').on('click', (e) => {
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

                $('#modalCloseJurosDescontos').on('click', function () {
                    $('#pageModalJurosDescontos').fadeOut(200);
                    $('body').css('overflow', 'scroll');

                    limparJurosDescontos()
                })

                $("#salvarJurosDescontos").on('click', function () {
                    $('#pageModalJurosDescontos').fadeOut(200);
                    $('body').css('overflow', 'scroll');
                })
            }
            modalJurosDescontos()

            function calcularJuros(){
                let jurosTipo = $("#cmbTipoJurosJD").val()
                let jurosValor = $("#inputJurosJD").val()
                let juros = 0

                let valorAPagar = $("#inputValorAPagarJD").val()

                if(jurosTipo == 'P'){
                    juros = (valorAPagar * (jurosValor / 100))
                } else {
                    juros = jurosValor
                }

                let descontoTipo = $("#cmbTipoDescontoJD").val()
                let descontoValor = $("#inputDescontoJD").val()
                let desconto= 0

                if(descontoTipo == 'P'){
                    desconto = (valorAPagar * (descontoValor / 100))
                } else {
                    desconto = descontoValor
                }

                let valorTotal = 0
                

                valorTotal = ((parseFloat(valorAPagar) + parseFloat(juros)) - parseFloat(desconto))

                $("#inputValorTotalAPagarJD").val(float2moeda(valorTotal))
                $("#inputValorTotalPago").val(float2moeda(valorTotal))

            }

            $("#inputJurosJD").keyup(( ) =>{
                calcularJuros()
            })
            $("#inputDescontoJD").keyup(( ) =>{
                calcularJuros()
            })
            $("#cmbTipoJurosJD").change(( ) =>{
                calcularJuros()
            })
            $("#cmbTipoDescontoJD").change(( ) =>{
                calcularJuros()
            })
            


            $("#salvar").on('click', (e) => {
                e.preventDefault()
                $dataPagamento = $("#inputDataPagamento").val()
                $valorTotalPago = $("#inputValorTotalPago").val()
                if($dataPagamento != '' && $valorTotalPago != ''){
                    $("#cmbContaBanco").attr('required', '')
                    $("#cmbFormaPagamento").attr('required', '')
                }
                $("#lancamento").submit()
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
                <form id="lancamento" name="lancamento" method="post" class="p-3">
                    <!-- Info blocks -->
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Basic responsive configuration -->
                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <h3 class="card-title">Novo Lançamento</h3>
                                    <div class="header-elements">
                                        <div class="list-icons">
                                            <a class="list-icons-item" data-action="collapse"></a>
                                            <a href="relatorioMovimentacao.php" class="list-icons-item"
                                                data-action="reload"></a>
                                            <!--<a class="list-icons-item" data-action="remove"></a>-->
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <?php 
                                    if(isset($lancamento)){
                                        echo '<input type="hidden" name="inputEditar" value="sim">';
                                        echo '<input type="hidden" name="inputContaId" value="'.$lancamento['CnAPaId'].'">';
                                    }
                                    
                                   ?>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="cmbPlanoContas">Plano de Contas <span
                                                        class="text-danger">*</span></label>
                                                <select id="cmbPlanoContas" name="cmbPlanoContas"
                                                    class="form-control form-control-select2" required>
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
                                                    if(isset($lancamento)){
                                                        if($lancamento['CnAPaPlanoContas'] == $item['PlConId']){
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
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="cmbFornecedor">Fornecedor <span
                                                        class="text-danger">*</span></label>
                                                <select id="cmbFornecedor" name="cmbFornecedor"
                                                    class="form-control form-control-select2" required>
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
                                                    if(isset($lancamento)){
                                                        if($lancamento['CnAPaFornecedor'] == $item['ForneId']){
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
                                    <div class="row justify-content-between">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="cmbContaBanco">Conta/Banco</label>
                                                <select id="cmbContaBanco" name="cmbContaBanco"
                                                    class="form-control form-control-select2">
                                                    <option value="">Selecionar</option>
                                                    <?php
												$sql = "SELECT CnBanId, CnBanNome
															FROM ContaBanco
															JOIN Situacao on SituaId = CnBanStatus
															WHERE CnBanUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
															ORDER BY CnBanNome ASC";
												$result = $conn->query($sql);
												$rowContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);
												foreach ($rowContaBanco as $item) {
                                                    if(isset($lancamento)){
                                                        if($lancamento['CnAPaContaBanco'] == $item['CnBanId']){
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
                                                <label for="cmbFormaPagemento">Forma de Pagamento</label>
                                                <select id="cmbFormaPagamento" name="cmbFormaPagamento"
                                                    class="form-control form-control-select2">
                                                    <option value="">Selecionar</option>
                                                    <?php
												$sql = "SELECT FrPagId, FrPagNome
															FROM FormaPagamento
															JOIN Situacao on SituaId = FrPagStatus
															WHERE FrPagUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
															ORDER BY FrPagNome ASC";
												$result = $conn->query($sql);
												$rowFormaPagamento = $result->fetchAll(PDO::FETCH_ASSOC);
												foreach ($rowFormaPagamento as $item) {
                                                    if(isset($lancamento)){
                                                        if($lancamento['CnAPaFormaPagamento'] == $item['FrPagId']){
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
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputNumeroDocumento">Nº Documento</label>
                                                <input type="text" id="inputNumeroDocumento" name="inputNumeroDocumento"
                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaNumDocumento'] ?>"
                                                    class="form-control" placeholder="Nº Documento">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputNotaFiscal">Nº Nota Fiscal/Documento</label>
                                                <input type="text" id="inputNotaFiscal" name="inputNotaFiscal"
                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaNotaFiscal'] ?>"
                                                    class="form-control" placeholder="Nº Nota Fiscal/Documento">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputDataEmissao">Data de Emissão</label>
                                                <input type="date" id="inputDataEmissao" name="inputDataEmissao"
                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaDtEmissao'] ?>"
                                                    class="form-control" placeholder="Data de Emissão">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputOrdemCarta">Ordem Compra/Carta Contrato</label>
                                                <select id="cmbOrdemCarta" name="cmbOrdemCarta"
                                                    class="form-control form-control-select2">
                                                    <option value="">Selecionar</option>
                                                    <?php
												$sql = "SELECT OrComId, OrComNumero
															FROM OrdemCompra
															JOIN Situacao on SituaId = OrComSituacao
															WHERE OrComUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'LIBERADO'
															";
												$result = $conn->query($sql);
												$rowOrdemCompra = $result->fetchAll(PDO::FETCH_ASSOC);
												foreach ($rowOrdemCompra as $item) {
                                                    if(isset($lancamento)){
                                                        if($lancamento['CnAPaOrdemCompra'] == $item['OrComId']){
                                                            print('<option value="' . $item['OrComId'] . '" selected>' . $item['OrComNumero'] . '</option>');
                                                        } else {
                                                            print('<option value="' . $item['OrComId'] . '">' . $item['OrComNumero'] . '</option>');
                                                        }
                                                    } else {
                                                        print('<option value="' . $item['OrComId'] . '">' . $item['OrComNumero'] . '</option>');
                                                    }
												}
												?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="inputDescricao">Descrição <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="inputDescricao" class="form-control" name="inputDescricao"
                                                    rows="3"
                                                    required><?php if(isset($lancamento)) echo $lancamento['CnAPaDescricao'] ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="d-flex flex-column">
                                                <div class="row justify-content-between m-0">
                                                    <h5>Valor à Pagar</h5>
                                                    <?php 
                                                       if(!isset($lancamento)){
                                                           print('<a href="#" id="btnParcelar">Parcelar</a>');
                                                       }
                                                    ?>
                                                </div>
                                                <div class="card">
                                                    <div class="card-body p-4">
                                                        <div class="row">
                                                            <div class="form-group col-6">
                                                                <label for="inputDataVencimento">Data do
                                                                    Vencimento</label>
                                                                <input type="date" id="inputDataVencimento"
                                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaDtVencimento'] ?>"
                                                                    name="inputDataVencimento" class="form-control">
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <label for="inputValor">Valor</label>
                                                                <input type="text" onKeyUp="moeda(this)" maxLength="12"
                                                                    id="inputValor" name="inputValor"
                                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaValorAPagar'] ?>"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="d-flex flex-column">
                                                <div class="row justify-content-between m-0">
                                                    <h5>Valor Pago</h5>
                                                    <div class="row pr-2">
                                                        <a id="habilitarPagamento" href="#">Habilitar Pagamento </a>
                                                        <span class="mx-2">|</span>
                                                        <a id="jurusDescontos" href=""
                                                            style="color: currentColor; cursor: not-allowed; opacity: 0.5; text-decoration: none; pointer-events: none;">
                                                            Juros/Descontos</a>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-body p-4">
                                                        <div class="row">
                                                            <div class="form-group col-6">
                                                                <label for="inputDataPagamento">Data do
                                                                    Pagamento</label>
                                                                <input type="date" id="inputDataPagamento"
                                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaDtPagamento'] ?>"
                                                                    name="inputDataPagamento" class="form-control" readOnly>
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <label for="inputValorTotalPago">Valor Total
                                                                    Pago</label>
                                                                <input type="text" onKeyUp="moeda(this)" maxLength="12"
                                                                    id="inputValorTotalPago" name="inputValorTotalPago"
                                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaValorPago'] ?>"
                                                                    class="form-control" disabled>
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
                                                <textarea id="inputObservacao" class="form-control"
                                                    name="inputObservacao" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="salvar" class="btn btn-principal">Salvar</button>
                                    <a href="contasAPagar.php" class="btn">Cancelar</a>
                                </div>

                            </div>
                            <!-- /basic responsive configuration -->

                        </div>
                    </div>

                    <!-- /info blocks -->

                    <!--------------------------------------------------------------------------------------->
                    <!--Modal Parcelar-->
                    <div id="pageModalParcelar" class="custon-modal">
                        <div class="custon-modal-container">
                            <div class="card custon-modal-content">
                                <div class="custon-modal-title">
                                    <i class=""></i>
                                    <p class="h3">Parcelamento</p>
                                    <i class=""></i>
                                </div>
                                <div class="px-5 pt-5">
                                    <div class="d-flex flex-row p-2">
                                        <div class='col-lg-3'>
                                            <div class="form-group">
                                                <label for="valorTotal">Valor Total</label>
                                                <div class="input-group">
                                                    <input type="text" id="valorTotal" onKeyUp="moeda(this)"
                                                        maxLength="12" name="valorTotal" class="form-control" readOnly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="numeroSerie">Periodicidade</label>
                                            <div class="form-group">
                                                <select id="cmbPeriodicidade" name="cmbPeriodicidade"
                                                    class="form-control form-control-select2">
                                                    <option value="">Selecionar</option>
                                                    <option value="1">Mensal</option>
                                                    <option value="2">Bimestral</option>
                                                    <option value="3">Trimestral</option>
                                                    <option value="4">Semestral</option>
                                                    <!-- <?php
                                                // $sql = "SELECT EstCoId, EstCoNome
                                                //         FROM EstadoConservacao
                                                //         JOIN Situacao on SituaId = EstCoStatus
                                                //         WHERE SituaChave = 'ATIVO'
                                                //         ORDER BY EstCoNome ASC";
                                                // $result = $conn->query($sql);
                                                // $rowEstCo = $result->fetchAll(PDO::FETCH_ASSOC);

                                                // foreach ($rowEstCo as $item) {
                                                //     print('<option value="' . $item['EstCoId'] . '">' . $item['EstCoNome'] . '</option>');
                                                // }
                                                ?> -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-1">
                                            <label for="cmbParcelas">Parcelas</label>
                                            <div class="form-group">
                                                <select id="cmbParcelas" name="cmbParcelas"
                                                    class="form-control form-control-select2">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-1">
                                            <button class="btn btn-lg btn-primary mt-2" id="gerarParcelas">Gerar
                                                Parcelas</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row px-5">
                                    <div class="col-12 d-flex flex-row justify-content-center">
                                        <p class="col-1 p-2" style="background-color:#f2f2f2">Item</p>
                                        <p class="col-5 p-2" style="background-color:#f2f2f2">Descrição</p>
                                        <p class="col-3 p-2" style="background-color:#f2f2f2">Vencimento</p>
                                        <p class="col-3 p-2" style="background-color:#f2f2f2">Valor</p>
                                    </div>
                                </div>
                                <div id="parcelasContainer" class="d-flex flex-column px-5"
                                    style="overflow-Y: scroll; max-height: 300px">

                                </div>
                                <div class="card-footer mt-2 d-flex flex-column">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <a class="btn btn-lg btn-principal" id="salvarParcelas">OK</a>
                                                <a id="modalCloseParcelar" class="btn btn-basic"
                                                    role="button">Cancelar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--------------------------------------------------------------------------------------->

                    <!--------------------------------------------------------------------------------------->
                    <!--Modal Parcelar-->
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
                                            <input id="inputVencimentoJD" class="form-control" type="date"
                                                name="inputVencimentoJD" readOnly>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputValorAPagarJD">Valor à Pagar</label>
                                            <input id="inputValorAPagarJD" onKeyUp="moeda(this)" maxLength="12"
                                                class="form-control" type="text" name="inputValorAPagarJD" readOnly>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="form-group" style="width: 200px">
                                            <label for="cmbTipoJurosJD">Tipo</label>
                                            <select id="cmbTipoJurosJD" name="cmbTipoJurosJD"
                                                class="form-control form-control-select2">
                                                <option value="P">Porcentagem</option>
                                                <option value="V">Valor</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputJurosJD">Juros</label>
                                            <input id="inputJurosJD" maxLength="12"
                                                class="form-control" type="text" name="inputJurosJD">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="form-group" style="width: 200px">
                                            <label for="cmbTipoDescontoJD">Tipo</label>
                                            <select id="cmbTipoDescontoJD" name="cmbTipoDescontoJD"
                                                class="form-control form-control-select2">
                                                <option value="P">Porcentagem</option>
                                                <option value="V">Valor</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputDescontoJD">Desconto</label>
                                            <input id="inputDescontoJD" maxLength="12"
                                                class="form-control" type="text" name="inputDescontoJD">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row justify-content-between">
                                        <div class="form-group" style="width: 200px">
                                            <label for="inputDataPagamentoJD">Data do Pagamento</label>
                                            <input id="inputDataPagamentoJD" value="<?php echo date("Y-m-d") ?>"
                                                class="form-control" type="date" name="inputDataPagamentoJD" readOnly>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputValorTotalAPagarJD">Valor Total à Pagar</label>
                                            <input id="inputValorTotalAPagarJD" onKeyUp="moeda(this)" maxLength="12"
                                                class="form-control" type="text" name="inputValorTotalAPagarJD"
                                                readOnly>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer mt-2 d-flex flex-column">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <a class="btn btn-lg btn-principal"
                                                    id="salvarJurosDescontos">Ok</a>
                                                <a id="modalCloseJurosDescontos" class="btn btn-basic"
                                                    role="button">Cancelar</a>
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

    </div>
    <!-- /page content -->

    <?php include_once("alerta.php"); ?>

</body>

</html>