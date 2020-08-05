<?php

include_once("sessao.php");

$_SESSION['PaginaAtual'] = 'Novo Lançamento';

include('global_assets/php/conexao.php');

$sql = "SELECT ForneId, ForneNome, ForneCpf, ForneCnpj, ForneTelefone, ForneCelular, ForneStatus, CategNome
		FROM Fornecedor
		JOIN Categoria on CategId = ForneCategoria
	    WHERE ForneUnidade = " . $_SESSION['UnidadeId'] . "
		ORDER BY ForneNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

$d = date("d");
$m = date("m");
$Y = date("Y");

$dataInicio = date("Y-m-d", mktime(0, 0, 0, $m, $d - 30, $Y)); //30 dias atrás
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
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>	

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
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="cmbPlanoContas">Plano de Contas <span class="text-danger">*</span></label>
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
														print('<option value="' . $item['PlConId'] . '">' . $item['PlConNome'] . '</option>');
													}

													?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="cmbFornecedor">Fornecedor <span class="text-danger">*</span></label>
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
														print('<option value="' . $item['ForneId'] . '">' . $item['ForneNome'] . '</option>');
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
														print('<option value="' . $item['CnBanId'] . '">' . $item['CnBanNome'] . '</option>');
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
														print('<option value="' . $item['FrPagId'] . '">' . $item['FrPagNome'] . '</option>');
													}

													?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputNumeroDocumento">Nº Documento</label>
                                                <input type="text" id="inputNumeroDocumento" name="inputNumeroDocumento"
                                                    class="form-control" placeholder="Nº Documento">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputNotaFiscal">Nº Nota Fiscal/Documento</label>
                                                <input type="text" id="inputNotaFiscal" name="inputNotaFiscal"
                                                    class="form-control" placeholder="Nº Nota Fiscal/Documento">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputDataEmissao">Data de Emissão</label>
                                                <input type="date" id="inputDataEmissao" name="inputDataEmissao"
                                                    class="form-control" placeholder="Data de Emissão">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="inputOrdemCarta">Ordem Compra/Carta Contrato</label>
                                                <input type="text" id="inputOrdemCarta" name="inputOrdemCarta"
                                                    class="form-control" placeholder="Ordem Compra/Carta Contrato">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="inputDescricao">Descrição <span class="text-danger">*</span></label>
                                                <textarea id="inputDescricao" class="form-control" name="inputDescricao"
                                                    rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <div class="d-flex flex-column">
                                                <h5>Valor à Pagar</h5>
                                                <div class="card">
                                                    <div class="card-body p-4">
                                                        <div class="row">
                                                            <div class="form-group col-6">
                                                                <label for="inputDataVencimento">Data do
                                                                    Vencimento</label>
                                                                <input type="text" id="inputDataVencimento"
                                                                    name="inputDataVencimento" class="form-control">
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <label for="inputValor">Valor</label>
                                                                <input type="text" id="inputValor" name="inputValor"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="d-flex flex-column">
                                                <h5>Valor Pago</h5>
                                                <div class="card">
                                                    <div class="card-body p-4">
                                                        <div class="row">
                                                            <div class="form-group col-6">
                                                                <label for="inputDataPagamento">Data do
                                                                    Pagamento</label>
                                                                <input type="text" id="inputDataPagamento"
                                                                    name="inputDataPagamento" class="form-control">
                                                            </div>
                                                            <div class="form-group col-6">
                                                                <label for="inputValorTotalPago">Valor Total
                                                                    Pago</label>
                                                                <input type="text" id="inputValorTotalPago"
                                                                    name="inputValorTotalPago" class="form-control">
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

                                    <button id="salvar" class="btn btn-success">Salvar</button>
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