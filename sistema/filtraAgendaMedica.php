<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra';

include('global_assets/php/conexao.php');

$tipoRequest = $_POST['tipoRequest'];

/*
esse arquivo é único para atendimento, onde deve passar como parametro o campo "tipoRequest"
que irá indicar qual ação será executada
*/

try{
	$iUnidade = $_SESSION['UnidadeId'];
	$usuarioId = $_SESSION['UsuarId'];
	$tipoRequest = $_POST['tipoRequest'];

	if($tipoRequest == 'PROFISSIONAIS'){
		$sql = "SELECT ProfiId,ProfiNome
		FROM Profissional WHERE ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		foreach($row as $item){
			array_push($array, [
				'id' => $item['ProfiId'],
				'nome' => $item['ProfiNome']
			]);
		}
	
		echo json_encode([
			'data' => $array,
			'status' => 'success',
			'titulo' => 'Profissionais',
			'menssagem' => 'Profissionais encontrados!!!',
		]);
	}else if($tipoRequest == 'AGENDA'){
		$iProfissional = $_POST['iProfissional'];

		// PrAgeId,PrAgeProfissional,PrAgeData,PrAgeHoraInicio,PrAgeHoraFim,PrAgeIntervalo,
		// PrAgeAtendimentoLocal,PrAgeUsuarioAtualizador,PrAgeUnidade

		$sql = "SELECT PrAgeId,PrAgeData
		FROM ProfissionalAgenda WHERE PrAgeProfissional = $iProfissional";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		foreach($row as $item){
			$data = explode('-',$item['PrAgeData']);

			array_push($array, [
				'id' => $item['PrAgeId'],
				'data' => $data
			]);
		}
	
		echo json_encode([
			'data' => $array,
			'status' => 'success',
			'titulo' => 'Agenda',
			'menssagem' => 'Agenda encontrada!!!',
		]);
	}else if($tipoRequest == 'AGENDAPROFISSIONAL'){
		$iProfissional = $_POST['iProfissional'];
		$data = $_POST['data'];

		// formatando data dd/mm/yyyy => yyyy-mm-dd
		$data = explode('/',$data);
		$data = $data[2].'-'.$data[1].'-'.$data[0];

		// PrAgeId,PrAgeProfissional,PrAgeData,PrAgeHoraInicio,PrAgeHoraFim,PrAgeIntervalo,
		// PrAgeAtendimentoLocal,PrAgeUsuarioAtualizador,PrAgeUnidade

		$sql = "SELECT PrAgeHoraInicio,PrAgeHoraFim
		FROM ProfissionalAgenda
		WHERE PrAgeProfissional = $iProfissional and PrAgeData = '$data'";
		$result = $conn->query($sql);
		$rowHorarios = $result->fetchAll(PDO::FETCH_ASSOC);

		// montar um array com os horarios dentro do intervalo: inicio e fim
		$arrayHora = [];
		foreach($rowHorarios as $itemAgenda){
			$hI = intval(explode('.',$itemAgenda['PrAgeHoraInicio'])[0]);
			$hF = intval(explode('.',$itemAgenda['PrAgeHoraFim'])[0]);
			$loop = true;

			while($loop){
				if($hI < $hF){
					array_push($arrayHora,$hI);
					$hI++;
				} else {
					array_push($arrayHora,$hF);
					$loop = false;
				}
			}
		}
		$arrayAgenda = [];

		foreach($arrayHora as $horario){
			$sql = "SELECT AtXSeId,AtXSeData,AtXSeHorario,AtLocNome,ClienNome,SrVenNome
				FROM AtendimentoXServico
				LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
				LEFT JOIN Cliente ON ClienId = AtendCliente
				LEFT JOIN AtendimentoLocal ON AtLocId = AtXSeAtendimentoLocal
				LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
				WHERE AtXSeProfissional = $iProfissional and AtXSeData = '$data' and AtXSeUnidade = $iUnidade
				and AtXSeHorario like '%$horario%'
				ORDER BY AtXSeHorario";
			$result = $conn->query($sql);
			$row = $result->fetchAll(PDO::FETCH_ASSOC);

			if(COUNT($row)){
				foreach($row as $item){
					array_push($arrayAgenda, [
						$item['AtXSeData'],
						$item['AtXSeHorario'],
						$item['AtLocNome']?$item['AtLocNome']:'',
						$item['ClienNome']?$item['ClienNome']:'',
						$item['SrVenNome']?$item['SrVenNome']:'',
						'',
					]);
				}
			} else {
				$horario = str_pad($horario,2,0,STR_PAD_LEFT);
				$horario = str_pad($horario,5,":00",STR_PAD_RIGHT);
				array_push($arrayAgenda, [
					$data,
					$horario,
					'',
					'',
					'',
					'',
				]);
			}
		}
	
		echo json_encode([
			'data' => $arrayAgenda,
			'status' => 'success',
			'titulo' => 'Agenda',
			'menssagem' => 'Agenda encontrada!!!',
		]);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'AGENDAMENTOS': $msg = 'Erro ao carregar agendamentos';break;
		default: $msg = "Erro ao executar ação COD.: $tipoRequest";break;
	}

	echo json_encode([
		'titulo' => 'Agenda médica',
		'tipo' => 'error',
		'menssagem' => $msg,
		'sql' => $sql,
		'error' => $e->getMessage()
	]);
}
