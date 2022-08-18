<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Agenda';

include('global_assets/php/conexao.php');

$tipoRequest = $_POST['tipoRequest'];

try{
	$iUnidade = $_SESSION['UnidadeId'];
	$usuarioId = $_SESSION['UsuarId'];

	if($tipoRequest == 'AGENDA'){
		$iProfissional = $_POST['iProfissional'];

		$sql = "SELECT PrAgeId,PrAgeProfissional,PrAgeData,PrAgeHoraInicio,PrAgeHoraFim,PrAgeAtendimentoLocal,
			PrAgeUsuarioAtualizador,PrAgeUnidade,AtLocNome
			FROM ProfissionalAgenda
			JOIN AtendimentoLocal ON AtLocId = PrAgeAtendimentoLocal
			WHERE PrAgeProfissional = $iProfissional";
		$result = $conn->query($sql);
		$rowAgenda = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($rowAgenda as $item){
			$horaInicio = explode('.',$item['PrAgeHoraInicio'])[0];
			$horaFim = explode('.',$item['PrAgeHoraFim'])[0];

			$inicio = "$item[PrAgeData]T$horaInicio";
			$fim = "$item[PrAgeData]T$horaFim";

			array_push($array,[
				'id'=> $item['PrAgeId'],
				'url'=> '',
				'title'=> $item['AtLocNome'],
				'start'=> $inicio,
				'end'=> $fim,
				'color'=> '#546E7A'
			]);
		}
	
		echo json_encode($array);
	} else if($tipoRequest == 'LOCAIS'){
		$sql = "SELECT AtLocNome
			FROM AtendimentoLocal
			WHERE AtLocUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowLocal = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($rowLocal as $item){
			array_push($array,[
				'nome'=> $item['AtLocNome'],
				'cor'=> '#546E7A'
			]);
		}
		echo json_encode($array);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'MUDARSITUACAO': $msg = 'Erro ao atualizar situação do atendimentos!!';break;
		default: $msg = 'Erro ao executar ação!!';break;
	}
	echo json_encode([
		'titulo' => 'Agenda',
		'status' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage(),
		'sql' => $sql
	]);
}
?>

