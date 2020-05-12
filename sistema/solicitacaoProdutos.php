<?php
include_once("sessao.php");

include('global_assets/php/conexao.php');

if (isset($_POST['solicitacaoId'])) {

    $sql = "SELECT SolicId, SlXPrQuantidade, ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduUnidade, ProduId, NULL) as Estoque
            FROM Solicitacao
            JOIN SolicitacaoXProduto on SlXPrSolicitacao = SolicId
            JOIN Produto on ProduId = SlXPrProduto
            JOIN Categoria on CategId = ProduCategoria
            JOIN Situacao on SituaId = ProduStatus
            WHERE SolicId = " . $_POST['solicitacaoId'] . " and ProduUnidade = " . $_SESSION['UnidadeId'] . "
            ";
    $result = $conn->query($sql);
    $rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rowProdutos as $item) {
        print('
            <div class="custon-modal-produto">
                <div class="custon-modal-produTitle d-flex flex-column col-12 col-sm-5 col-lg-7">
                    <p>' . $item['ProduNome'] . '</p>
                    <p>' . $item['CategNome'] . '</p>
                </div>
                <div class="modal-controles col-12 col-sm-7 col-lg-5 row justify-content-md-center align-items-center mx-0">
                    <p class="col-12 col-sm-5 text-center">Quantidade:</p>
                    <span class="col-12 col-sm-6" style="text-align: center" type="text" class="form-control touchspin-set-value" style="display: block;">'.$item['SlXPrQuantidade'].'</span>
                </div>
            </div>
        ');
    }
}
