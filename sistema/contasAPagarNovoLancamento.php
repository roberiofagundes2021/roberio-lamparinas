<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Lançamento';

include('global_assets/php/conexao.php');

if(isset($_POST['cmbPlanoContas'])){

    if(isset($_POST['inputEditar'])){

        try{

            $sql = "SELECT SituaId
		            FROM Situacao
		            WHERE SituaChave = 'ATIVO'";
            $result = $conn->query($sql);
            $situacao = $result->fetch(PDO::FETCH_ASSOC);
		    
		    $sql = "UPDATE ContasAPagar SET CnAPaPlanoContas = :iPlanoContas, CnAPaFornecedor = :iFornecedor, CnAPaContaBanco = :iContaBanco, CnAPaFormaPagamento = :iFormaPagamento, CnAPaNumDocumento = :sNumDocumento,
                                            CnAPaNotaFiscal = :sNotaFiscal, CnAPaDtEmissao = :dateDtEmissao, CnAPaOrdemCompra = :iOrdemCompra, CnAPaDescricao = :sDescricao, CnAPaDtVencimento = :dateDtVencimento, CnAPaValorAPagar = :fValorAPagar,
                                            CnAPaDtPagamento = :dateDtPagamento, CnAPaValorPago = :fValorPago, CnAPaObservacao = :sObservacao, CnAPaStatus = :iStatus, CnAPaUsuarioAtualizador = :iUsuarioAtualizador, CnAPaUnidade = :iUnidade
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
                                ':fValorPago' => (float)$_POST['inputValorTotalPago'],
                                ':sObservacao' => $_POST['inputObservacao'],
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

            $sql = "SELECT SituaId
                    FROM Situacao
                    WHERE SituaChave = 'ATIVO'
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
                            ':fValorPago' => (float)$_POST['inputValorTotalPago'],
                            ':sObservacao' => $_POST['inputObservacao'],
                            ':iStatus' => $situacao['SituaId'],
                            ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                            ':iUnidade' => $_SESSION['UnidadeId']
                            ));
                            
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
            function habilitarPagamento(){

                $("#habilitarPagamento").on('click', ( e ) => {

                    e.preventDefault()
                    $dataPagamento = $("#inputDataVencimento").val()
                    $valorTotalPago = $("#inputValor").val()
                    $("#inputDataPagamento").val($dataPagamento)
                    $("#inputValorTotalPago").val($valorTotalPago)

                    document.getElementById('jurusDescontos').style = "";

                })
                $("#jurusDescontos")
            }
            habilitarPagamento()
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
                                <form name="formMovimentacao" method="post" class="p-3">
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
                                                value="<?php if(isset($lancamento)) echo $lancamento['CnAPaDescricao'] ?>"
                                                    rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="d-flex flex-column">
                                                <div class="row justify-content-between m-0">
                                                    <h5>Valor à Pagar</h5>
                                                    <a href="#">Parcelar</a>
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
                                                        <a id="jurusDescontos" href="" style="color: currentColor; cursor: not-allowed; opacity: 0.5; text-decoration: none; pointer-events: none;"> Juros/Descontos</a>
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
                                                                    name="inputDataPagamento" class="form-control">
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <label for="inputValorTotalPago">Valor Total
                                                                    Pago</label>
                                                                <input type="text" onKeyUp="moeda(this)" maxLength="12"
                                                                    id="inputValorTotalPago" name="inputValorTotalPago"
                                                                    value="<?php if(isset($lancamento)) echo $lancamento['CnAPaValorPago'] ?>"
                                                                    class="form-control">
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
                                </form>

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

    </div>
    <!-- /page content -->

    <?php include_once("alerta.php"); ?>

</body>

</html>