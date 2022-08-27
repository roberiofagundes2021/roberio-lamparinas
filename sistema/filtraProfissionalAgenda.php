<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Agenda';

include('global_assets/php/conexao.php');

$tipoRequest = $_POST['tipoRequest'];

if(!isset($_SESSION['agendaProfissional'])){
	$_SESSION['agendaProfissional'] = [];
}

try{
	$iUnidade = $_SESSION['UnidadeId'];
	$usuarioId = $_SESSION['UsuarId'];

	if($tipoRequest == 'AGENDA'){
		$iProfissional = $_POST['iProfissional'];

		$sql = "SELECT PrAgeId,PrAgeProfissional,PrAgeData,PrAgeHoraInicio,PrAgeHoraFim,PrAgeAtendimentoLocal,
			PrAgeUsuarioAtualizador,PrAgeUnidade,AtLocNome,AtLocCor
			FROM ProfissionalAgenda
			JOIN AtendimentoLocal ON AtLocId = PrAgeAtendimentoLocal
			WHERE PrAgeProfissional = $iProfissional";
		$result = $conn->query($sql);
		$rowAgenda = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($rowAgenda as $item){
			$horaInicio = explode('.',$item['PrAgeHoraInicio'])[0];
			$horaFim = explode('.',$item['PrAgeHoraFim'])[0];

			$dataI = date_create($item['PrAgeData']);
			$dataI = date_format($dataI,'Y/m/d');

			$inicio = "$dataI $horaInicio";
			$fim = "$dataI $horaFim";

			array_push($array,[
				'id'=> $item['PrAgeId'],
				'url'=> '',
				'tipInsert'=> 'ATT',
				'localId' => $item['PrAgeAtendimentoLocal'],
				'title'=> $item['AtLocNome'],
				'start'=> $inicio,
				'end'=> $fim,
				'color'=> $item['AtLocCor']?$item['AtLocCor']:'#546E7A'
			]);
		}
		$_SESSION['agendaProfissional'] = $array;
	
		echo json_encode($array);
	} else if($tipoRequest == 'LOCAIS'){
		$sql = "SELECT AtLocId,AtLocNome,AtLocCor
			FROM AtendimentoLocal
			WHERE AtLocUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowLocal = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($rowLocal as $item){
			array_push($array,[
				'idLocal'=> $item['AtLocId'],
				'nome'=> $item['AtLocNome'],
				'cor'=> $item['AtLocCor']?$item['AtLocCor']:'#546E7A'
			]);
		}
		echo json_encode($array);
	} else if($tipoRequest == 'SETAGENDA'){
		$arrayAgenda = $_SESSION['agendaProfissional'];
		$id = $_POST['id'];
		$localId = $_POST['localId'];
		$title = $_POST['title'];

		$horaI = $_POST['dataI']; // "dd/mm/yyyy hh:MM:ss"
		$horaI = explode(' ',$horaI);
		$data = explode('/',$horaI[0]);
		$data = $data[2].'-'.$data[1].'-'.$data[0]; // "yyyy/mm/dd hh:MM:ss"

		$horaF = $_POST['dataF'];
		$horaF = explode(' ',$horaF)[1];

		foreach($arrayAgenda as $key=>$item){
			if($item['id'] == $id){
				$arrayAgenda[$key]['title'] = $title;
				$arrayAgenda[$key]['start'] = ($data.' '.$horaI[1]);
				$arrayAgenda[$key]['end'] = ($data.' '.$horaF);
				$_SESSION['agendaProfissional'] = $arrayAgenda;
				echo json_encode($arrayAgenda);
				exit;
			}
		}

		array_push($arrayAgenda, [
			'id' => $id,
			'localId' => $localId,
			'start' => ($data.' '.$horaI[1]),
			'end' => ($data.' '.$horaF),
			'tipInsert'=> 'NEW',
			'title'=> $title,
			'color'=> '#546E7A',
			'url'=> '',
		]);
		$_SESSION['agendaProfissional'] = $arrayAgenda;
		echo json_encode($arrayAgenda);
	} else if($tipoRequest == 'SALVAAGENDA'){
		$arrayAgenda = $_SESSION['agendaProfissional'];
		$iProfissional = $_POST['iProfissional'];
		$msg = '';

		foreach($arrayAgenda as $item){
			switch($item){
				case !isset($item['tipInsert']):$msg = 'Informe o início e fim do agendamento!!';break;
				case !$item['start']:$msg = 'Data de início não informada!!';break;
				case !$item['end']:$msg = 'Data de fim não informada!!';break;
				case (!$item['id'] || $item['id'] == 'N/A'):$msg = 'Card de data sem alteração!!';break;
				default: $msg = '';break;
			}
		}

		if($msg){
			echo json_encode([
				'status'=> 'ERRO',
				'titulo'=> 'Dados incompletos!!',
				'menssagem' => $msg
			]);
			exit;
		}

		$arraySql = [];

		foreach($arrayAgenda as $item){
			$start = explode(' ',$item['start']); //"22-08-2022 09:00:00"
			$end = explode(' ',$item['end']); //"22-08-2022 09:00:00"

			$data = $start[0]; // "dd/mm/yyyy"

			if($item['tipInsert'] == 'NEW'){
				$sql = "INSERT INTO ProfissionalAgenda(PrAgeProfissional,PrAgeData,PrAgeHoraInicio,
				PrAgeHoraFim,PrAgeAtendimentoLocal,PrAgeUsuarioAtualizador,PrAgeUnidade) VALUES
				('$iProfissional','$data', '$start[1]','$end[1]','$item[localId]','$usuarioId','$iUnidade')";

				array_push($arraySql, $sql);
			} elseif($item['tipInsert'] == 'ATT'){
				$sql = "UPDATE ProfissionalAgenda SET
				PrAgeData='$data',
				PrAgeHoraInicio='$start[1]',
				PrAgeHoraFim='$end[1]',
				PrAgeAtendimentoLocal='$item[localId]',
				PrAgeUsuarioAtualizador='$usuarioId'
				WHERE PrAgeId = $item[id]";

				array_push($arraySql, $sql);
			}
		}
		if(COUNT($arraySql)){
			foreach($arraySql as $sql){
				$conn->query($sql);
			}
		}
		echo json_encode([
			'status'=> 'success',
			'titulo'=> 'Agenda salva!!',
			'menssagem' => 'Agenda do profissional salva com sucesso!!',
			'sql' =>$arraySql
			// 'sql' =>$arrayAgenda
		]);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'SALVAAGENDA': $msg = 'Erro ao salvar agenda!!';break;
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

