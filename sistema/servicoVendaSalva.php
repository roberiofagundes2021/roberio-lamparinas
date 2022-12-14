<?php

include_once("sessao.php");
$_SESSION['PaginaAtual'] = 'Salvar Serviço';
include('global_assets/php/conexao.php');

try {
    $iUnidade = $_SESSION['UnidadeId'];
    $usuarioId = $_SESSION['UsuarId'];

    $dataArray = array(
        ':sSrVenCodigo' => $_POST['SrVenCodigo'],
        ':sSrVenNome' => $_POST['SrVenNome'],
        ':iSrVenTipoServico' => $_POST['SrVenTipoServico'],
        ':sSrVenDetalhamento' => $_POST['SrVenDetalhamento'],
        ':iSrVenGrupo' => $_POST['SrVenGrupo'],
        ':iSrVenSubGrupo' => $_POST['SrVenSubGrupo'],
        ':iSrVenPlanoConta' => $_POST['SrVenPlanoConta'],
        ':iSrVenStatus' => 1,
        ':iSrVenUsuarioAtualizador' => $usuarioId,
        ':iSrVenUnidade' => $iUnidade
    );

    if (isset($_POST['SrVenId'])) {
        $dataArray[':sSrVenId'] = $_POST['SrVenId'];
        $sql = $sql = ("UPDATE
                ServicoVenda
            SET
                SrVenId = :sSrVenId,
                AtAmbDataInicio = :sSrVenCodigo,
                SrVenNome= :sSrVenNome,
                SrVenTipoServico= :iSrVenTipoServico,
                SrVenDetalhamento= :sSrVenDetalhamento,
                SrVenGrupo= :iSrVenGrupo,
                SrVenSubGrupo= :iSrVenSubGrupo,
                SrVenPlanoConta = :iSrVenPlanoConta,
                SrVenStatus= :iSrVenStatus,
                SrVenUsuarioAtualizador= :iSrVenUsuarioAtualizador,
                SrVenUnidade= :iSrVenUnidade
            WHERE
                SrVenId = ". $POST['SrVenId'] .";"
        );
        $mensagem = "Serviço editado com sucesso!";        
    } else {
        $sql = "INSERT INTO ServicoVenda (
        SrVenCodigo,SrVenNome,SrVenTipoServico,
        SrVenDetalhamento,SrVenGrupo,SrVenSubGrupo,SrVenPlanoConta,
        SrVenStatus,SrVenUsuarioAtualizador,SrVenUnidade)
    	VALUES (
            :sSrVenCodigo, :sSrVenNome, :iSrVenTipoServico,
            :sSrVenDetalhamento, :iSrVenGrupo, :iSrVenSubGrupo,
            :iSrVenPlanoConta, :iSrVenStatus, :iSrVenUsuarioAtualizador,
            :iSrVenUnidade
        )";
        $mensagem = "Serviço criado com sucesso!";
    }
    $result = $conn->prepare($sql);
    $result->execute($dataArray);
    echo json_encode([
        'status' => 'success',
        'titulo' => 'Serviço',
        'mensagem' => $mensagem = $mensagem,
    ]);
    $_SESSION['msg']['mensagem'] = $mensagem;
} catch (PDOException $e) {
    $msg = "Erro ao salvar serviço.";
    echo json_encode([
        'titulo' => 'Agenda médica',
        'tipo' => 'error',
        'mensagem' => $msg,
        'sql' => $sql,
        'error' => $e->getMessage()
    ]);
}
