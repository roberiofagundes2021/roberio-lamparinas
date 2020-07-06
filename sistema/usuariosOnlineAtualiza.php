<?php
include_once("sessao.php");

include('global_assets/php/conexao.php');

if (isset($_POST['timesTampUsuarioOnline'])) {

    $ultimoAcesso = intval($_POST['timesTampUsuarioOnline']);
    $usuario = $_SESSION['UsuarId'];

    $sql = " UPDATE Usuario set UsuarDataAcesso = :sDataAcesso WHERE UsuarId = :id ";
    $result = $conn->prepare($sql);
    $result->bindParam(':sDataAcesso', $ultimoAcesso);
    $result->bindParam(':id', $usuario);
    $result->execute();

    $tempo = $ultimoAcesso - 1000;

    $sql = "SELECT UsuarId, UsuarNome, UsuarDataAcesso, SetorNome
                FROM Usuario
                JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario =  UsuarId
                JOIN Setor on SetorId =  EXUXPSetor
                WHERE UsuarDataAcesso >= $tempo and EXUXPUnidade = " . $_SESSION['UnidadeId'] . "
                ORDER BY UsuarDataAcesso";
    $result = $conn->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($row as $usuario) {
        print('
                <li class="media">
                   <div class="media-body">
                       <p class="media-title font-weight-semibold">' . $usuario['UsuarNome'] . '</p>
                       <span class="d-block text-muted font-size-sm">' . $usuario['SetorNome'] . '</span>
                   </div>
                   <div class="ml-3 align-self-center"><span class="badge badge-mark border-success"></span></div>
                </li>
           ');
    }
}
