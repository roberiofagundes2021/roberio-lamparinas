<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Tabela de Gastos';

include('global_assets/php/conexao.php');

if(!isset($_SESSION['atendimentoTabelaServicos'])){
	$_SESSION['atendimentoTabelaServicos'] = [];
}

$tipoRequest = $_POST['tipoRequest'];
$iUnidade = $_SESSION['UnidadeId'];
$usuarioId = $_SESSION['UsuarId'];

if($tipoRequest == 'PROCEDIMENTOS'){		
	$sql = "SELECT SrVenId, SrVenNome
			FROM ServicoVenda
			JOIN Situacao on SituaId = SrVenStatus
			WHERE SituaChave = 'ATIVO' and SrVenUnidade = $iUnidade
			ORDER BY SrVenNome ASC";
	$result = $conn->query($sql);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);
	
	$array = [];
	foreach($rows as $item){
		array_push($array, [
			'id' => $item['SrVenId'],
			'nome' => $item['SrVenNome']
		]);
	}

	echo json_encode($array);
} elseif ($tipoRequest == 'PROFISSIONAL'){
	$sql = "SELECT ProfiId, ProfiNome
			FROM Profissional
			JOIN Situacao on SituaId = ProfiStatus
			WHERE SituaChave = 'ATIVO' and ProfiUnidade = $iUnidade
			ORDER BY ProfiNome ASC";
	$result = $conn->query($sql);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	$array = [];
	foreach($rows as $item){
		array_push($array,[
			'id' => $item['ProfiId'],
			'nome' => $item['ProfiNome']
		]);
	}

	echo json_encode($array);
} elseif ($tipoRequest == 'LOCAIS'){
	$sql = "SELECT AtLocId, AtLocNome
		FROM AtendimentoLocal
		JOIN Situacao on SituaId = AtLocStatus
		WHERE SituaChave = 'ATIVO' and AtLocUnidade = $iUnidade
		ORDER BY AtLocNome ASC";
	$result = $conn->query($sql);
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);

	$array = [];
	foreach($rows as $item){
		array_push($array,[
			'id' => $item['AtLocId'],
			'nome' => $item['AtLocNome']
		]);
	}

	echo json_encode($array);
} elseif ($tipoRequest == 'SETDATAPROFISSIONAL'){
	$iMedico = $_POST['iMedico'];

	$sql = "SELECT PrAgeData, PrAgeHoraInicio, PrAgeHoraFim
	FROM ProfissionalAgenda WHERE PrAgeProfissional = $iMedico and PrAgeUnidade = $iUnidade";
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);

	$arrayData = [true];
	foreach($row as $item){
		$data = explode('-', $item['PrAgeData']);
		
		array_push($arrayData,
		[
			intval($data[0]),
			intval($data[1])-1,
			intval($data[2])
		]);
	}

	echo json_encode([
		'arrayData' => $arrayData,
		'status' => 'success',
		'titulo' => 'Data',
		'menssagem' => 'Data do profissional selecionado!!!',
	]);
} elseif ($tipoRequest == 'SETHORAPROFISSIONAL'){
	$iMedico = $_POST['iMedico'];
	$data = $_POST['data'];
	$data = explode('/', $data); // dd/mm/yyyy
	$data = $data[2].'-'.$data[1].'-'.$data[0]; // yyyy-mm-dd

	$sql = "SELECT PrAgeData, PrAgeHoraInicio, PrAgeHoraFim, PrAgeIntervalo
	FROM ProfissionalAgenda
	WHERE PrAgeData = '$data' and PrAgeProfissional = $iMedico and PrAgeUnidade = $iUnidade";
	$result = $conn->query($sql);
	$row = $result->fetchAll(PDO::FETCH_ASSOC);

	$arrayHora = [true,];
	$intervalo = 30;
	foreach($row as $item){
		$horaI = explode(':', $item['PrAgeHoraInicio']);
		$horaF = explode(':', $item['PrAgeHoraFim']);

		if($item['PrAgeIntervalo']){
			$intervalo = intval($item['PrAgeIntervalo']);
		}
		
		array_push($arrayHora,
		[
			'from' => [intval($horaI[0]), intval($horaI[1])],
			'to' => [intval($horaF[0]), intval($horaF[1])],
		]);
	}

	echo json_encode([
		'arrayHora' => $arrayHora,
		'intervalo'=> $intervalo,
		'status' => 'success',
		'titulo' => 'Data',
		'menssagem' => 'Hora do profissional selecionado!!!',
	]);
} elseif($tipoRequest == 'ADICIONARSERVICO'){
	$atendimentoTabelaServicos = $_SESSION['atendimentoTabelaServicos'];

	$iServico = $_POST['servico'];
	$iMedico = $_POST['medicos'];
	$sData = explode('/',$_POST['dataAtendimento']);
	$sData = $sData[2].'-'.$sData[1].'-'.$sData[0];
	$sHora = $_POST['horaAtendimento'];
	$iLocal = $_POST['localAtendimento'];
	
	$sql = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorVenda,SrVenUnidade
	FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
	$resultServico = $conn->query($sql);
	$resultServico = $resultServico->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT ProfiId,ProfiNome,ProfiCpf,ProfiSexo,ProfiEndereco,ProfiCelular,ProfiTelefone
	FROM Profissional WHERE ProfiId = $iMedico and ProfiUnidade = $iUnidade";
	$resultMedico = $conn->query($sql);
	$resultMedico = $resultMedico->fetch(PDO::FETCH_ASSOC);

	// $sqlLocal = "SELECT AtLocId,AtLocNome
	// FROM AtendimentoLocal WHERE AtLocId = $iLocal and AtLocUnidade = $iUnidade";
	$sql = "SELECT AtLocId,AtLocNome
	FROM AtendimentoLocal WHERE AtLocId = $iLocal and AtLocUnidade = $iUnidade";
	$resultLocal = $conn->query($sql);
	$resultLocal = $resultLocal->fetch(PDO::FETCH_ASSOC);

	foreach($atendimentoTabelaServicos as $item){
		if($item['iMedico'] == $iMedico && $item['data'] == $sData && $item['hora'] == $sHora){
			echo json_encode([
				'status' => 'error',
				'titulo' => 'Duplicação de registro',
				'menssagem' => 'Já foi adicionado registro com o mesmo Médico, Data e Hora',
				'array' => $atendimentoTabelaServicos,
			]);
			exit;
		}
	}

	array_push($atendimentoTabelaServicos, [
		'id' => "$resultServico[SrVenId]#$resultMedico[ProfiId]#$resultLocal[AtLocId]",
		'iServico' => $resultServico['SrVenId'],
		'iMedico' => $resultMedico['ProfiId'],
		'iLocal' => $resultLocal['AtLocId'],

		'status' => 'new',
		'servico' => $resultServico['SrVenNome'],
		'medico' => $resultMedico['ProfiNome'],
		'local' => $resultLocal['AtLocNome'],
		'sData' => mostraData($sData),
		'data' => $sData,
		'hora' => mostraHora($sHora),
		'valor' => $resultServico['SrVenValorVenda'],
		'desconto' => 0
	]);
	$_SESSION['atendimentoTabelaServicos'] = $atendimentoTabelaServicos;

	echo json_encode([
		'status' => 'success',
		'titulo' => 'Serviço',
		'menssagem' => 'Serviço adicionado!!!',
	]);
} elseif ($tipoRequest == 'CHECKSERVICO'){
	$atendimentoSessao = $_SESSION['atendimentoTabelaServicos'];

	if(isset($_POST['iAtendimento']) && $_POST['iAtendimento']){
		$iAtendimento = $_POST['iAtendimento'];

		$sql = "SELECT AtTGaId,AtTGaAtendimento,AtTGaServico,AtTGaProfissional,AtTGaDataRegistro,AtTGaHorario
			,AtTGaDataAtendimento,AtTGaAtendimentoLocal,AtTGaValor,AtTGaDesconto,
			ProfiId,AtLocId,AtLocNome,AtModNome,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,
			SituaCor,ProfiNome,SrVenNome,SrVenValorVenda,SrVenId
			FROM AtendimentoTabelaGasto
			JOIN Atendimento ON AtendId = AtTGaAtendimento
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN Profissional ON ProfiId = AtTGaProfissional
			JOIN AtendimentoLocal ON AtLocId = AtTGaAtendimentoLocal
			JOIN ServicoVenda ON SrVenId = AtTGaServico
			WHERE AtTGaUnidade = $iUnidade and AtTGaAtendimento = $iAtendimento";
		$result = $conn->query($sql);
		$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);

		// esse loop duplo serve para evitar duplicações e evitar que os itens incluídos localmente ao editar
		// não desapareçam
		foreach($rowAtendimento as $item){
			$hasItem = false;
			foreach($atendimentoSessao as $item2){
				if($item2['id'] == $item['AtTGaId']){
					$hasItem = true;
				}
			}
			if(!$hasItem){
				array_push($atendimentoSessao, [
					'id' => $item['AtTGaId'],
					'iServico' => $item['SrVenId'],
					'iMedico' => $item['ProfiId'],
					'iLocal' => $item['AtLocId'],

					'status' => 'att',
					'servico' => $item['SrVenNome'],
					'medico' => $item['ProfiNome'],
					'local' => $item['AtLocNome'],
					'sData' => mostraData($item['AtTGaDataAtendimento']),
					'data' => $item['AtTGaDataAtendimento'],
					'hora' => mostraHora($item['AtTGaHorario']),
					'valor' => $item['AtTGaValor'],
					'desconto' => $item['AtTGaDesconto']?$item['AtTGaDesconto']:0
				]);
			}
		}
	}
	$valorTotal = 0;
	$desconto = 0;

	foreach($atendimentoSessao as $item){
		$valor = $item['valor'] - $item['desconto'];
		$valorTotal += $valor;
		$desconto += $item['desconto'];
	}
	$_SESSION['atendimentoTabelaServicos'] = $atendimentoSessao;
	
	echo json_encode([
		'array' => $atendimentoSessao,
		'valorTotal' => $valorTotal,
		'desconto' => $desconto
	]);
} elseif($tipoRequest == 'FECHARCONTA'){
	$atendimentoSessao = $_SESSION['atendimentoTabelaServicos'];

	if(!COUNT($atendimentoSessao)){
		echo json_encode([
			'titulo' => 'Atendimento',
			'status' => 'error',
			'menssagem' => 'Atendimento deve ter ao menos 1(um) procedimento!!'
		]);
		exit;
	}

	$id = isset($_POST['atendimento'])?$_POST['atendimento']:'';
	$cliente = isset($_POST['cliente'])?$_POST['cliente']:'';

	if($id && $cliente){
		$sql = "INSERT INTO AtendimentoTabelaGasto(AtTGaAtendimento,AtTGaDataRegistro,AtTGaServico,
			AtTGaProfissional,AtTGaDataAtendimento,AtTGaHorario,AtTGaAtendimentoLocal,AtTGaValor,AtTGaDesconto,
			AtTGaUsuarioAtualizador,AtTGaUnidade)
			VALUES ";
		$dataAtual = date("Y-m-d");

		$arraySql = [];

		foreach($atendimentoSessao as $item){
			if($item['status'] == 'new'){
				$sql = "INSERT INTO AtendimentoTabelaGasto(AtTGaAtendimento,AtTGaDataRegistro,AtTGaServico,
				AtTGaProfissional,AtTGaDataAtendimento,AtTGaHorario,AtTGaAtendimentoLocal,AtTGaValor,AtTGaDesconto,
				AtTGaUsuarioAtualizador,AtTGaUnidade)
				VALUES ('$id', '$dataAtual','$item[iServico]','$item[iMedico]','$item[data]','$item[hora]',
				'$item[iLocal]',$item[valor],'$item[desconto]','$usuarioId','$iUnidade')";
			} elseif($item['status'] == 'att'){
				$sql = "UPDATE AtendimentoTabelaGasto SET
				AtTGaServico = '$item[iServico]',
				AtTGaProfissional = '$item[iMedico]',
				AtTGaDataAtendimento = '$item[data]',
				AtTGaHorario = '$item[hora]',
				AtTGaAtendimentoLocal = '$item[iLocal]',
				AtTGaValor = '$item[valor]',
				AtTGaDesconto = '$item[desconto]',
				AtTGaUsuarioAtualizador = '$usuarioId'
				WHERE AtTGaId = '$item[id]'";
			} elseif($item['status'] == 'rem'){
				$sql = "DELETE FROM AtendimentoTabelaGasto
				WHERE AtTGaId = '$item[id]'";
			}
			array_push($arraySql,$sql);
		}
		foreach($arraySql as $sql){
			$conn->query($sql);
		}

		echo json_encode([
			'titulo' => 'Fechamento de conta',
			'status' => 'success',
			'menssagem' => 'Conta fechada!!'
		]);
	}else{
		echo json_encode([
			'titulo' => 'Fechamento de conta',
			'status' => 'error',
			'menssagem' => 'Erro ao fechar conta!!'
		]);
	}
} elseif($tipoRequest == 'SETDESCONTO'){
	$atendimentoSessao = $_SESSION['atendimentoTabelaServicos'];

	$id = $_POST['id'];
	$desconto = $_POST['desconto'];

	foreach($atendimentoSessao as $key => $item){
		if($item['id'] == $id){
			$atendimentoSessao[$key]['desconto'] = floatval($desconto);
		}
	}

	$_SESSION['atendimentoTabelaServicos'] = $atendimentoSessao;

	echo json_encode([
		'status' => 'success',
		'titulo' => 'Desconto',
		'menssagem' => 'Desconto adicionado!!!',
	]);
} elseif($tipoRequest == 'GETDESCONTO'){
	$atendimentoSessao = $_SESSION['atendimentoTabelaServicos'];

	$id = $_POST['id'];
	$desconto = null;

	foreach($atendimentoSessao as $item){
		if($item['id'] == $id){
			$desconto = $item['desconto'];
		}
	}

	echo json_encode([
		'desconto' => $desconto,
		'status' => 'success',
		'titulo' => 'Desconto',
		'menssagem' => '',
	]);
} elseif ($tipoRequest == 'EXCLUISERVICO'){
	$id = $_POST['id']; // "SrVenId#ProfiId#AtLocId"
	$atendimentoSessao = $_SESSION['atendimentoTabelaServicos'];

	foreach($atendimentoSessao as $key => $item){

		if($item['id'] == $id){
			$atendimentoSessao[$key]['status'] = 'rem';
			$_SESSION['atendimentoTabelaServicos'] = $atendimentoSessao;
			echo json_encode([
				'status' => 'success',
				'titulo' => 'Excluir gasto',
				'menssagem' => 'Gasto Excluído!!!',
			]);
			break;
		}
	}
} 