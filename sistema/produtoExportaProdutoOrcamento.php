<?php

include_once("sessao.php");

include('global_assets/php/conexao.php');

$_SESSION['msg'] = array();

if (isset($_POST['inputProdutoId'])) {
    $sql = "SELECT ProduNome, ProduDetalhamento, ProduCategoria, ProduSubCategoria, ProduUnidadeMedida, ProduStatus
            FROM Produto
            WHERE ProduId = " . $_POST['inputProdutoId'] . " and ProduUnidade = " . $_SESSION['UnidadeId'] . "
           ";
    $result = $conn->query($sql);
    $Produto = $result->fetch(PDO::FETCH_ASSOC);

    if ($Produto) {
        try {
            $sql = "INSERT INTO ProdutoOrcamento (PrOrcNome, PrOrcDetalhamento, PrOrcCategoria, PrOrcSubCategoria, PrOrcUnidadeMedida, PrOrcSituacao, PrOrcUsuarioAtualizador, PrOrcUnidade) 
				VALUES (:sNome, :sDetalhamento, :iCategoria, :iSubCategoria, :iUnidadeMedida, :iSituacao, :iUsuarioAtualizador, :iUnidade)";
            $result = $conn->prepare($sql);

            $result->execute(array(
                ':sNome' => $Produto['ProduNome'],
                ':sDetalhamento' => $Produto['ProduDetalhamento'],
                ':iCategoria' => $Produto['ProduCategoria'],
                ':iSubCategoria' => $Produto['ProduSubCategoria'],
                ':iUnidadeMedida' => $Produto['ProduUnidadeMedida'],
                ':iSituacao' => $Produto['ProduStatus'],
                ':iUsuarioAtualizador' => $_SESSION['UsuarId'],
                ':iUnidade' => $_SESSION['UnidadeId']
            ));

            $_SESSION['msg']['titulo'] = "Sucesso";
            $_SESSION['msg']['mensagem'] = "Produto exportado!!!";
            $_SESSION['msg']['tipo'] = "success";
        } catch (PDOException $e) {

            $_SESSION['msg']['titulo'] = "Erro";
            $_SESSION['msg']['mensagem'] = "Erro ao exportar produto!!!";
            $_SESSION['msg']['tipo'] = "error";

            echo 'Error2: ' . $e->getMessage();
            die;
        }

        irpara("produto.php");
    }
}
