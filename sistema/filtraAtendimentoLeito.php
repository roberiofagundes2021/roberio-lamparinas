<?php 

include_once("sessao.php");

include('global_assets/php/conexao.php');

$typeRequest = $_POST['tipoRequest'];
$usuaId = $_SESSION['UsuarId'];
$iUnidade = $_SESSION['UnidadeId'];

try{
  if($typeRequest == "LEITOS"){
    $acomodacao = isset($_POST['acomodacao'])?$_POST['acomodacao']:null;
    $internacao = isset($_POST['internacao'])?$_POST['internacao']:null;
    $especialidade = isset($_POST['especialidade'])?$_POST['especialidade']:null;
    $ala = isset($_POST['ala'])?$_POST['ala']:null;
/*
    if($acomodacao){
      $sql = "SELECT TpIntId
        FROM TipoInternacao
        WHERE TpIntUnidade = $iUnidade and TpIntTipoAcomodacao = $acomodacao";
      $result = $conn->query($sql);
      $result = $result->fetchAll(PDO::FETCH_ASSOC);

      $internacaoTipos = '()';
      if(COUNT($result)){
        $internacaoTipos = '(';
        foreach($result as $intern){
          $internacaoTipos .= "$intern[TpIntId],";
        }
        $internacaoTipos = substr($internacaoTipos, 0, -1);
        $internacaoTipos .= ')';
      }
    }*/

    $quartos = [];

    $sql = "SELECT QuartId, QuartNome
      FROM Quarto
      WHERE QuartUnidade = $iUnidade";

    if($acomodacao){
      $sql .= " AND QuartTipoInternacao in $internacaoTipos";
    }
    if($internacao){
      $sql .= " AND QuartTipoInternacao = $internacao";
    }

    $result = $conn->query($sql);
    $resultQuartos = $result->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT LeitoId,QuartId,LeitoNome,QuartNome,TpIntNome,EsLeiNome,LeitoStatus,LeitoUsuarioAtualizador,LeitoUnidade,AtXLeId
      FROM Leito
      JOIN EspecialidadeLeito ON EsLeiId = LeitoEspecialidade
      JOIN Quarto ON QuartId = LeitoQuarto
      JOIN TipoInternacao ON TpIntId = EsLeiTipoInternacao
      LEFT JOIN AtendimentoXLeito ON AtXLeLeito = LeitoId
      WHERE LeitoUnidade = $iUnidade";

    if($acomodacao){
      $sql .= " AND QuartTipoInternacao in $internacaoTipos";
    }
    if($internacao){
      $sql .= " AND QuartTipoInternacao = $internacao";
    }
    if($especialidade){
      $sql .= " AND EsLeiId = $especialidade";
    }
    // if($ala){
    //   $sql .= " AND QuartId = $ala";
    // }
    $sql .= " ORDER BY LeitoNome";
    $result = $conn->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'quartos' =>$resultQuartos,
      'leitos' =>$result,
    ]);
  }elseif($typeRequest == "ACOMODACAO"){
    $sql = "SELECT TpAcoId, TpAcoNome
      FROM TipoAcomodacao
      WHERE TpAcoUnidade = $iUnidade";
    $result = $conn->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }elseif($typeRequest == "INTERNACAO"){
    $acomodacao = isset($_POST['acomodacao'])?$_POST['acomodacao']:null;

    $sql = "SELECT TpIntId, TpIntNome
      FROM TipoInternacao
      WHERE TpIntUnidade = $iUnidade";

    if($acomodacao){
      $sql .= " AND TpIntTipoAcomodacao = $acomodacao";
    }

    $result = $conn->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }elseif($typeRequest == "ESPECIALIDADE"){
    $internacao= isset($_POST['internacao'])?$_POST['internacao']:null;

    $sql = "SELECT EsLeiId, EsLeiNome
            FROM EspecialidadeLeito
            WHERE EsLeiUnidade = $iUnidade";

    if($internacao){
      $sql .= " AND EsLeiTipoInternacao = $internacao";
    }
    
    $result = $conn->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }elseif($typeRequest == "ALA"){

    $sql = "SELECT AlaId, AlaNome
            FROM Ala
            WHERE AlaUnidade = $iUnidade";
    
    $result = $conn->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }
}catch(PDOException $e) {
  // $conn->rollback();
  $_SESSION['msg']['titulo'] = "Erro";
  $_SESSION['msg']['mensagem'] = "Erro ao executar ação!!!";
  $_SESSION['msg']['tipo'] = "error";

  echo json_encode([
    'type' => $typeRequest,
    'err' => $e
  ]);
}
?>