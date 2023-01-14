<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

$tipoRequest = $_POST['tipoRequest'];

try {

    $iUnidade = $_SESSION['UnidadeId'];
	$iEmpresa = $_SESSION['EmpreId'];
	$usuarioId = $_SESSION['UsuarId'];

    if ($tipoRequest == 'ADICIONARPROCEDIMENTO') {
     

    }elseif ($tipoRequest == 'INCLUIREVOLUCAODIARIA') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$evolucaoDiaria = $_POST['evolucaoDiaria'];
		$dataHoraAtual = date('Y-m-d H:i:s');
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "INSERT INTO  AtendimentoEvolucaoDiaria(AtEDiAtendimento, AtEDiDataInicio, AtEDiHoraInicio, AtEDiProfissional,AtEDiDataHora,AtEDiEvolucaoDiaria,AtEDiUnidade)
		VALUES ('$iAtendimentoId', '$dataInicio', '$horaInicio','$usuarioId','$dataHoraAtual','$evolucaoDiaria','$iUnidade')";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Incluir Evolução Diária',
			'menssagem' => 'Evolução Diária inserida com sucesso!!!'
		]);
		
	}elseif ($tipoRequest == 'ADICIONARMEDICAMENTO') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];
		$tipo = $_POST['tipo'];
		$medicamentoEstoqueMedicamentos = $_POST['medicamentoEstoqueMedicamentos'];
		$medicamentoDlMedicamentos = $_POST['medicamentoDlMedicamentos'];
		$selViaMedicamentos = $_POST['selViaMedicamentos'];
		$doseMedicamentos = $_POST['doseMedicamentos'];
		$selUnidadeMedicamentos = $_POST['selUnidadeMedicamentos'];
		$frequenciaMedicamentos = $_POST['frequenciaMedicamentos'];
		$selTipoAprazamentoMedicamentos = $_POST['selTipoAprazamentoMedicamentos'];
		$dataInicioMedicamentos = $_POST['dataInicioMedicamentos'] == "" ? null : $_POST['dataInicioMedicamentos'];
		$checkBombaInfusaoMedicamentos = $_POST['checkBombaInfusaoMedicamentos'];
		$checkInicioAdmMedicamentos = $_POST['checkInicioAdmMedicamentos'];
		$horaInicioAdmMedicamentos = $_POST['horaInicioAdmMedicamentos'];
		$complementoMedicamentos = $_POST['complementoMedicamentos'];
		$descricaoPosologiaMedicamentos = $_POST['descricaoPosologiaMedicamentos'];

		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "INSERT INTO  AtendimentoPrescricaoMedicamento
			(AtPMeAtendimento,AtPMeDataInicio, AtPMeHoraInicio, AtPMeProfissional, AtPMeTipo, AtPMeProdutoEmEstoque, AtPMeProdutoLivre, 
			AtPMeVia, AtPMeDose, AtPMeUnidadeMedida, AtPMeFrequencia, AtPMeTipoAprazamento, AtPMeDtInicioTratamento, AtPMeBombaInfusao,
			AtPMeInicioAdm, AtPMeHoraInicioAdm, AtPMeComplemento, AtPMePosologia, AtPMeUnidade)
		VALUES 
			('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$tipo', '$medicamentoEstoqueMedicamentos', '$medicamentoDlMedicamentos',
			'$selViaMedicamentos', '$doseMedicamentos', '$selUnidadeMedicamentos', '$frequenciaMedicamentos', '$selTipoAprazamentoMedicamentos', '$dataInicioMedicamentos',
			'$checkBombaInfusaoMedicamentos', '$checkInicioAdmMedicamentos', '$horaInicioAdmMedicamentos', '$complementoMedicamentos', '$descricaoPosologiaMedicamentos', '$iUnidade')";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Adicionar Medicamento',
			'menssagem' => 'Medicamento adicionado com sucesso!!!'
		]);
		
	}elseif ($tipoRequest == 'ADICIONARSOLUCAO') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];
		$tipo = $_POST['tipo'];
		$medicamentoEstoqueSolucoes = $_POST['medicamentoEstoqueSolucoes'] != '' ? "'" . $_POST['medicamentoEstoqueSolucoes'] . "'" : 'NULL';
		$medicamentoDlSolucoes = $_POST['medicamentoDlSolucoes'];
		$selViaSolucoes = $_POST['selViaSolucoes'];
		$doseSolucoes = $_POST['doseSolucoes'];
		$selUnidadeSolucoes = $_POST['selUnidadeSolucoes'];
		$frequenciaSolucoes = $_POST['frequenciaSolucoes'];
		$selTipoAprazamentoSolucoes = $_POST['selTipoAprazamentoSolucoes'];
		$dataInicioSolucoes = $_POST['dataInicioSolucoes'] == "" ? null : $_POST['dataInicioSolucoes'];	

		$diluenteSolucoes = $_POST['diluenteSolucoes'] != '' ? "'" . $_POST['diluenteSolucoes'] . "'" : 'NULL';
		$volumeSolucoes = $_POST['volumeSolucoes'] != '' ? "'" . $_POST['volumeSolucoes'] . "'" : 'NULL';
		$correrEmSolucoes = $_POST['correrEmSolucoes'] != '' ? "'" . $_POST['correrEmSolucoes'] . "'": 'NULL';
		$selUnTempoSolucoes = $_POST['selUnTempoSolucoes'] != '' ? "'" . $_POST['selUnTempoSolucoes'] . "'": 'NULL';
		$velocidadeInfusaoSolucoes = $_POST['velocidadeInfusaoSolucoes'] != '' ? "'" . $_POST['velocidadeInfusaoSolucoes'] . "'": 'NULL';

		$checkBombaInfusaoSolucoes = $_POST['checkBombaInfusaoSolucoes'];
		$checkInicioAdmSolucoes = $_POST['checkInicioAdmSolucoes'];
		$horaInicioAdmSolucoes = $_POST['horaInicioAdmSolucoes'];
		$complementoSolucoes = $_POST['complementoSolucoes'];
		$descricaoPosologiaSolucoes = $_POST['descricaoPosologiaSolucoes'];

		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "INSERT INTO  AtendimentoPrescricaoMedicamento
			(AtPMeAtendimento,AtPMeDataInicio, AtPMeHoraInicio, AtPMeProfissional, AtPMeTipo, AtPMeProdutoEmEstoque, AtPMeProdutoLivre, 
			AtPMeVia, AtPMeDose, AtPMeUnidadeMedida, AtPMeFrequencia, AtPMeTipoAprazamento, AtPMeDtInicioTratamento,
			AtPMeDiluente, AtPMeVolume, AtPMeCorrerEm, AtPMeUnidadeTempo, AtPMeVelocidadeInfusao,
			AtPMeBombaInfusao,	AtPMeInicioAdm, AtPMeHoraInicioAdm, AtPMeComplemento, AtPMePosologia, AtPMeUnidade)
		VALUES 
			('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$tipo', $medicamentoEstoqueSolucoes, '$medicamentoDlSolucoes',
			'$selViaSolucoes', '$doseSolucoes', '$selUnidadeSolucoes', '$frequenciaSolucoes', '$selTipoAprazamentoSolucoes', '$dataInicioSolucoes', 
			$diluenteSolucoes, $volumeSolucoes, $correrEmSolucoes, $selUnTempoSolucoes, $velocidadeInfusaoSolucoes, 
			'$checkBombaInfusaoSolucoes', '$checkInicioAdmSolucoes', '$horaInicioAdmSolucoes', '$complementoSolucoes', '$descricaoPosologiaSolucoes', '$iUnidade')";	
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Adicionar Solução',
			'menssagem' => 'Solução adicionada com sucesso!!!'
		]);
		
	}elseif ($tipoRequest == 'ADICIONARDIETA') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];
		$dataInicialDieta = $_POST['dataInicialDieta'];
		$dataFinalDieta = $_POST['dataFinalDieta'] != '' ? $_POST['dataFinalDieta'] : null;
		$selTipoDeDieta = $_POST['selTipoDeDieta'];
		$selViaDieta = $_POST['selViaDieta'];
		$freqDieta = $_POST['freqDieta'];
		$selTipoAprazamentoDieta = $_POST['selTipoAprazamentoDieta'];
		$checkBombaInfusaoDieta = $_POST['checkBombaInfusaoDieta'];
		$descricaoDieta = $_POST['descricaoDieta'];
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "INSERT INTO AtendimentoPrescricaoDieta(AtPDiAtendimento, AtPDiDataInicio, AtPDiHoraInicio,AtPDiProfissional,AtPDiDataInicioDieta, AtPDiDataFimDieta,
		AtPDiTipoDieta, AtPDiVia, AtPDiFrequencia, AtPDiTipoAprazamento, AtPDiBombaInfusao, AtPDiDescricaoDieta, AtPDiUnidade)
		VALUES ('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$dataInicialDieta', '$dataFinalDieta',
		'$selTipoDeDieta', '$selViaDieta', '$freqDieta', '$selTipoAprazamentoDieta', '$checkBombaInfusaoDieta', '$descricaoDieta', '$iUnidade' )";

		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Adicionar Dieta',
			'menssagem' => 'Dieta adicioanda com sucesso!!!'
		]);
		
	}elseif ($tipoRequest == 'ADICIONARCUIDADO') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];

		$dataInicialCuidados = $_POST['dataInicialCuidados'];
		$dataFinalCuidados = $_POST['dataFinalCuidados'];
		$selTipoDeCuidado = $_POST['selTipoDeCuidado'];
		$frequenciaCuidados = $_POST['frequenciaCuidados'];
		$selTipoAprazamentoCuidados = $_POST['selTipoAprazamentoCuidados'];
		$snCuidado = $_POST['snCuidado'];
		$complementoCuidados = $_POST['complementoCuidados'];
		$descricaoCuidados = $_POST['descricaoCuidados'];
		
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "INSERT INTO AtendimentoPrescricaoCuidado
		(AtPCuAtendimento, AtPCuDataInicio, AtPCuHoraInicio, AtPCuProfissional, AtPCuDataInicioCuidado, AtPCuDataFimCuidado, AtPCuTipoCuidado, AtPCuFrequencia,
		AtPCuTipoAprazamento, AtPCuSn, AtPCuComplemento, AtPCuDescricaoCuidado, AtPCuUnidade)
		VALUES ('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$dataInicialCuidados', '$dataFinalCuidados', '$selTipoDeCuidado', '$frequenciaCuidados',
		'$selTipoAprazamentoCuidados', '$snCuidado', '$complementoCuidados', '$descricaoCuidados', '$iUnidade')";

		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Adicionar Dieta',
			'menssagem' => 'Dieta adicioanda com sucesso!!!'
		]);

	}elseif ($tipoRequest == 'EVOLUCAODIARIA') {

		$iAtendimento = $_POST['id'];
	
		$sql = "SELECT *
			FROM AtendimentoEvolucaoDiaria
			WHERE AtEDiAtendimento = $iAtendimento";

		$result = $conn->query($sql);
		$evolucoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($evolucoes as $key => $item){

			array_push($array,[
				'item' => ($key + 1),
				'id'=>$item['AtEDiId'],
				'editavel' => $item['AtEDiEditavel'],
				'dataHora'=> mostraData($item['AtEDiDataInicio']) . ' ' . mostraHora($item['AtEDiHoraInicio']),
				'evolucao'=> substr($item['AtEDiEvolucaoDiaria'], 0, 100) . '...',
				'evolucaoCompl' => $item['AtEDiEvolucaoDiaria']
			]);
		}
		
		echo json_encode($array);		

	}elseif ($tipoRequest == 'MEDICAMENTOSSOLUCOES') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$sql = "SELECT AtPMeId, AtPMeTipo, AtPMeEditavel, AtPMeDtInicioTratamento, AtPMeProdutoEmEstoque, ProduCodigo, ProduNome ,ViaNome, AtPMePosologia, AtPMeProdutoLivre
		FROM AtendimentoPrescricaoMedicamento
		LEFT JOIN Produto ON AtPMeProdutoEmEstoque = ProduId
		JOIN Via ON AtPMeVia = ViaId
		WHERE AtPMeAtendimento = $iAtendimentoId
		AND AtPMeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rows as $key => $item){

			if ($item['AtPMeProdutoEmEstoque'] == '' || $item['AtPMeProdutoEmEstoque'] == NULL) {
				$dadosMedicamento = $item['AtPMeProdutoLivre'];
			}else{
				$dadosMedicamento = $item['ProduCodigo'] . ' - ' . $item['ProduNome'];
			}

			array_push($array, [

				'item' => ($key + 1),
				'id' => $item['AtPMeId'],
				'tipo' => $item['AtPMeTipo'],
				'editavel' => $item['AtPMeEditavel'],
				'dataIniTratamento' => mostraData($item['AtPMeDtInicioTratamento']),
				'dadosMedicamento' => $dadosMedicamento,
				'via' => $item['ViaNome'],
				'posologia' => $item['AtPMePosologia']

			]);
		}

		echo json_encode($array);
		
	}elseif ($tipoRequest == 'DIETA') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$sql = "SELECT AtPDiId, AtPDiDataInicioDieta, TpDieNome, AtPDiDescricaoDieta, AtPDiEditavel
		FROM AtendimentoPrescricaoDieta
		JOIN TipoDieta ON AtPDiTipoDieta = TpDieId
		WHERE AtPDiAtendimento = $iAtendimentoId
		AND AtPDiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rows as $key => $item){
			array_push($array, [

				'item' => ($key + 1),
				'id' => $item['AtPDiId'],
				'tipoDieta' => $item['TpDieNome'],
				'editavel' => $item['AtPDiEditavel'],
				'dataIniTratamento' => mostraData($item['AtPDiDataInicioDieta']),
				'descricaoDieta' => $item['AtPDiDescricaoDieta']

			]);
		}

		echo json_encode($array);
		
	} elseif ($tipoRequest == 'FILTRARSUBCATEGORIA') {

		$categoriaId = $_POST['categoriaId'];

		if ($categoriaId) {
			$sql = "SELECT * FROM SubCategoria
			WHERE  SbCatStatus = 1
			AND SbCatCategoria = $categoriaId
			AND SbCatEmpresa = $iEmpresa";
		} else {
			$sql = "SELECT * FROM SubCategoria
			WHERE  SbCatStatus = 1
			AND SbCatEmpresa = $iEmpresa";
		}
	
		$resultS = $conn->query($sql);
		$rowSubCategoria = $resultS->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rowSubCategoria as $item){

			array_push($array,[
				'id'=>$item['SbCatId'],
				'nome' => $item['SbCatNome'],
			]);
		}
		
		echo json_encode($array);	
		
	}elseif ($tipoRequest == 'CUIDADOS') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$sql = "SELECT AtPCuId, AtPCuDataInicioCuidado, TpCuiNome, AtPCuHoraInicio, AtPCuDescricaoCuidado, AtPCuEditavel 
		FROM AtendimentoPrescricaoCuidado
		JOIN TipoCuidado ON AtPCuTipoCuidado = TpCuiId
		WHERE AtPCuAtendimento = $iAtendimentoId
		AND AtPCuUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rows as $key => $item){
			array_push($array, [

				'item' => ($key + 1),
				'id' => $item['AtPCuId'],
				'tipoCuidado' => $item['TpCuiNome'],
				'editavel' => $item['AtPCuEditavel'],
				'dataHora' => mostraData($item['AtPCuDataInicioCuidado']) . " " . mostraHora($item['AtPCuHoraInicio']),
				'descricaoCuidado' => $item['AtPCuDescricaoCuidado']

			]);
		}

		echo json_encode($array);
		
	} elseif ($tipoRequest == 'EDITAREVOLUCAO') {

		$idEvolucao = $_POST['idEvolucao'];
		$evolucaoDiaria = $_POST['evolucaoDiaria'];
		$dataHoraAtual = date('Y-m-d H:i:s');
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "UPDATE AtendimentoEvolucaoDiaria SET
		AtEDiDataInicio = '$dataInicio', 
		AtEDiHoraInicio = '$horaInicio', 
		AtEDiDataHora = '$dataHoraAtual',
		AtEDiEvolucaoDiaria = '$evolucaoDiaria'
		WHERE AtEDiId = '$idEvolucao'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Atualizar Evolução Diária',
			'menssagem' => 'Evolução Diária alterada com sucesso!!!'
		]);

	} elseif ($tipoRequest == 'EDITARMEDICAMENTO') {

		$idMedicamentos = $_POST['idMedicamentos'];
		$profissional = $_POST['profissional'];
		$tipo = $_POST['tipo'];
		$medicamentoEstoqueMedicamentos = $_POST['medicamentoEstoqueMedicamentos'];
		$medicamentoDlMedicamentos = $_POST['medicamentoDlMedicamentos'];
		$selViaMedicamentos = $_POST['selViaMedicamentos'];
		$doseMedicamentos = $_POST['doseMedicamentos'];
		$selUnidadeMedicamentos = $_POST['selUnidadeMedicamentos'];
		$frequenciaMedicamentos = $_POST['frequenciaMedicamentos'];
		$selTipoAprazamentoMedicamentos = $_POST['selTipoAprazamentoMedicamentos'];
		$dataInicioMedicamentos = $_POST['dataInicioMedicamentos'] == "" ? null : $_POST['dataInicioMedicamentos'];
		$checkBombaInfusaoMedicamentos = $_POST['checkBombaInfusaoMedicamentos'];
		$checkInicioAdmMedicamentos = $_POST['checkInicioAdmMedicamentos'];
		$horaInicioAdmMedicamentos = $_POST['horaInicioAdmMedicamentos'];
		$complementoMedicamentos = $_POST['complementoMedicamentos'];
		$descricaoPosologiaMedicamentos = $_POST['descricaoPosologiaMedicamentos'];

		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "UPDATE AtendimentoPrescricaoMedicamento SET
			AtPMeDataInicio = '$dataInicio', 
			AtPMeHoraInicio = '$horaInicio', 
			AtPMeProfissional = '$profissional', 
			AtPMeTipo = '$tipo', 
			AtPMeProdutoEmEstoque = '$medicamentoEstoqueMedicamentos', 
			AtPMeProdutoLivre = '$medicamentoDlMedicamentos', 
			AtPMeVia = '$selViaMedicamentos', 
			AtPMeDose = '$doseMedicamentos', 
			AtPMeUnidadeMedida = '$selUnidadeMedicamentos', 
			AtPMeFrequencia = '$frequenciaMedicamentos', 
			AtPMeTipoAprazamento = '$selTipoAprazamentoMedicamentos', 
			AtPMeDtInicioTratamento = '$dataInicioMedicamentos', 
			AtPMeBombaInfusao = '$checkBombaInfusaoMedicamentos',
			AtPMeInicioAdm = '$checkInicioAdmMedicamentos', 
			AtPMeHoraInicioAdm = '$horaInicioAdmMedicamentos', 
			AtPMeComplemento = '$complementoMedicamentos', 
			AtPMePosologia = '$descricaoPosologiaMedicamentos '
			WHERE AtPMeId = '$idMedicamentos'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Editar Prescrição de Medicamento',
			'menssagem' => 'Medicamento alterado com sucesso!!!'
		]);

	} elseif ($tipoRequest == 'EDITARSOLUCAO') {

		$idSolucoes = $_POST['idSolucoes'];
		$profissional = $_POST['profissional'];
		$tipo = $_POST['tipo'];
		$medicamentoEstoqueSolucoes = $_POST['medicamentoEstoqueSolucoes'];
		$medicamentoDlSolucoes = $_POST['medicamentoDlSolucoes'];
		$selViaSolucoes = $_POST['selViaSolucoes'];
		$doseSolucoes = $_POST['doseSolucoes'];
		$selUnidadeSolucoes = $_POST['selUnidadeSolucoes'];
		$frequenciaSolucoes = $_POST['frequenciaSolucoes'];
		$selTipoAprazamentoSolucoes = $_POST['selTipoAprazamentoSolucoes'];
		$dataInicioSolucoes = $_POST['dataInicioSolucoes'] == "" ? null : $_POST['dataInicioSolucoes'];	
		$diluenteSolucoes = $_POST['diluenteSolucoes'];
		$volumeSolucoes = $_POST['volumeSolucoes'];
		$correrEmSolucoes = $_POST['correrEmSolucoes'];
		$selUnTempoSolucoes = $_POST['selUnTempoSolucoes'];
		$velocidadeInfusaoSolucoes = $_POST['velocidadeInfusaoSolucoes'];	
		$checkBombaInfusaoSolucoes = $_POST['checkBombaInfusaoSolucoes'];
		$checkInicioAdmSolucoes = $_POST['checkInicioAdmSolucoes'];
		$horaInicioAdmSolucoes = $_POST['horaInicioAdmSolucoes'];
		$complementoSolucoes = $_POST['complementoSolucoes'];
		$descricaoPosologiaSolucoes = $_POST['descricaoPosologiaSolucoes'];

		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "UPDATE AtendimentoPrescricaoMedicamento SET
			AtPMeDataInicio = '$dataInicio', 
			AtPMeHoraInicio = '$horaInicio', 
			AtPMeProfissional = '$profissional', 
			AtPMeTipo = '$tipo', 
			AtPMeProdutoEmEstoque = '$medicamentoEstoqueSolucoes', 
			AtPMeProdutoLivre = '$medicamentoDlSolucoes', 
			AtPMeVia = '$selViaSolucoes',
			AtPMeDose = '$doseSolucoes', 
			AtPMeUnidadeMedida = '$selUnidadeSolucoes', 
			AtPMeFrequencia = '$frequenciaSolucoes', 
			AtPMeTipoAprazamento = '$selTipoAprazamentoSolucoes', 
			AtPMeDtInicioTratamento = '$dataInicioSolucoes',
			AtPMeDiluente = '$diluenteSolucoes', 
			AtPMeVolume = '$volumeSolucoes', 
			AtPMeCorrerEm = '$correrEmSolucoes', 
			AtPMeUnidadeTempo = '$selUnTempoSolucoes', 
			AtPMeVelocidadeInfusao = '$velocidadeInfusaoSolucoes',
			AtPMeBombaInfusao = '$checkBombaInfusaoSolucoes',	
			AtPMeInicioAdm = '$checkInicioAdmSolucoes', 
			AtPMeHoraInicioAdm = '$horaInicioAdmSolucoes', 
			AtPMeComplemento = '$complementoSolucoes', 
			AtPMePosologia = '$descricaoPosologiaSolucoes'
			WHERE AtPMeId = '$idSolucoes'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Editar Prescrição de Solução',
			'menssagem' => 'Solução alterada com sucesso!!!'
		]);
		
	} elseif ($tipoRequest == 'EDITARDIETA') {

		$idDieta = $_POST['idDieta'];
		$profissional = $_POST['profissional'];
		$dataInicialDieta = $_POST['dataInicialDieta'];
		$dataFinalDieta = $_POST['dataFinalDieta'] != '' ? $_POST['dataFinalDieta'] : null;
		$selTipoDeDieta = $_POST['selTipoDeDieta'];
		$selViaDieta = $_POST['selViaDieta'];
		$freqDieta = $_POST['freqDieta'];
		$selTipoAprazamentoDieta = $_POST['selTipoAprazamentoDieta'];
		$checkBombaInfusaoDieta = $_POST['checkBombaInfusaoDieta'];
		$descricaoDieta = $_POST['descricaoDieta'];
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		$sql = "UPDATE AtendimentoPrescricaoDieta SET		
		AtPDiDataInicio = '$dataInicio', 
		AtPDiHoraInicio = '$horaInicio',
		AtPDiProfissional = '$profissional',
		AtPDiDataInicioDieta = '$dataInicialDieta', 
		AtPDiDataFimDieta = '$dataFinalDieta',
		AtPDiTipoDieta = '$selTipoDeDieta', 
		AtPDiVia = '$selViaDieta',
		AtPDiFrequencia = '$freqDieta', 
		AtPDiTipoAprazamento = '$selTipoAprazamentoDieta', 
		AtPDiBombaInfusao = '$checkBombaInfusaoDieta', 
		AtPDiDescricaoDieta = '$descricaoDieta'
		WHERE AtPDiId = '$idDieta'";

		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Editar Prescrição de Dieta',
			'menssagem' => 'Dieta alterada com sucesso!!!'
		]);

		# code...
	} elseif ($tipoRequest == 'EDITARCUIDADO') {

		$idCuidado = $_POST['idCuidado'];
		$profissional = $_POST['profissional'];
		$dataInicialCuidados = $_POST['dataInicialCuidados'];
		$dataFinalCuidados = $_POST['dataFinalCuidados'];
		$selTipoDeCuidado = $_POST['selTipoDeCuidado'];
		$frequenciaCuidados = $_POST['frequenciaCuidados'];
		$selTipoAprazamentoCuidados = $_POST['selTipoAprazamentoCuidados'];
		$snCuidado = $_POST['snCuidado'];
		$complementoCuidados = $_POST['complementoCuidados'];
		$descricaoCuidados = $_POST['descricaoCuidados'];		
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');
		
		$sql = "UPDATE AtendimentoPrescricaoCuidado SET		
		AtPCuDataInicio = '$dataInicio', 
		AtPCuHoraInicio = '$horaInicio', 
		AtPCuProfissional = '$profissional', 
		AtPCuDataInicioCuidado = '$dataInicialCuidados', 
		AtPCuDataFimCuidado = '$dataFinalCuidados', 
		AtPCuTipoCuidado = '$selTipoDeCuidado', 
		AtPCuFrequencia = '$frequenciaCuidados',
		AtPCuTipoAprazamento = '$selTipoAprazamentoCuidados', 
		AtPCuSn = '$snCuidado', 
		AtPCuComplemento = '$complementoCuidados', 
		AtPCuDescricaoCuidado = '$descricaoCuidados'
		WHERE AtPCuId = '$idCuidado'";

		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Editar Prescrição de Cuidado',
			'menssagem' => 'Cuidado alterado com sucesso!!!'
		]);

	} elseif ($tipoRequest == 'DELETEEVOLUCAO') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoEvolucaoDiaria
		WHERE AtEDiId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Evolução Diária',
			'menssagem' => 'Evolução excluída!!!',
		]);
		# code...
	} elseif ($tipoRequest == 'DELETEMEDICAMENTO') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoPrescricaoMedicamento
		WHERE AtPMeId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Prescrição de Medicamento',
			'menssagem' => 'Medicamento excluído!!!',
		]);
		# code...
	} elseif ($tipoRequest == 'DELETEDIETA') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoPrescricaoDieta
		WHERE AtPDiId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Prescrição de Dieta',
			'menssagem' => 'Dieta excluída!!!',
		]);
		
	} elseif ($tipoRequest == 'DELETECUIDADO') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoPrescricaoCuidado
		WHERE AtPCuId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Prescrição de Cuidado',
			'menssagem' => 'Cuidado excluído!!!',
		]);
	} elseif ($tipoRequest == 'GETEVOLUCAO') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM AtendimentoEvolucaoDiaria
		WHERE AtEDiId = $id
		AND AtEDiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
	} elseif ($tipoRequest == 'GETMEDICAMENTO') {

		$id = $_POST['id'];

		$sql = "SELECT AtPMeAtendimento,AtPMeDataInicio, AtPMeHoraInicio, AtPMeProfissional, AtPMeTipo, AtPMeProdutoEmEstoque, AtPMeProdutoLivre, 
		AtPMeVia, AtPMeDose, AtPMeUnidadeMedida, AtPMeFrequencia, AtPMeTipoAprazamento, AtPMeDtInicioTratamento,
		AtPMeDiluente, AtPMeVolume, AtPMeCorrerEm, AtPMeUnidadeTempo, AtPMeVelocidadeInfusao,
		AtPMeBombaInfusao,	AtPMeInicioAdm, AtPMeHoraInicioAdm, AtPMeComplemento, AtPMePosologia, AtPMeUnidade, ProduNome
		FROM AtendimentoPrescricaoMedicamento
		JOIN Produto ON AtPMeProdutoEmEstoque = ProduId
		WHERE AtPMeId = $id
		AND AtPMeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		$array = [$row];

		if ($row['AtPMeDiluente'] != null) {
			$sql = "SELECT ProduNome as NomeDiluente
			FROM AtendimentoPrescricaoMedicamento
			JOIN Produto ON AtPMeDiluente = ProduId
			WHERE AtPMeId = $id
			AND AtPMeUnidade = $iUnidade";
			$result = $conn->query($sql);
			$rowdiluente = $result->fetch(PDO::FETCH_ASSOC);

			array_push($array,
				$rowdiluente
			);			
	
		}

		echo json_encode($array);

	} elseif ($tipoRequest == 'GETDIETA') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM AtendimentoPrescricaoDieta
		WHERE AtPDiId = $id
		AND AtPDiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
	} elseif ($tipoRequest == 'GETCUIDADO') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM AtendimentoPrescricaoCuidado
		WHERE AtPCuId = $id
		AND AtPCuUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);

	} elseif ($tipoRequest == 'VIA') {

		$sql = "SELECT *
		FROM Via
		WHERE ViaStatus = 1
		AND ViaUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){

			array_push($array,[
				'id' => $item['ViaId'],
				'nome' => $item['ViaNome']
			]);
		}
	
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'FILTRAVIADIETA') {

		$tipoDieta = $_POST['tipoDieta'];
	
		$sql = "SELECT TOP(1) TpDieVia
		FROM TipoDieta
		WHERE TpDieStatus = 1
		AND TpDieId = $tipoDieta
		AND TpDieUnidade = $iUnidade";
		$result = $conn->query($sql);
		$tpDieVia = $result->fetch(PDO::FETCH_ASSOC);

		$tipoDieta = $tpDieVia?$tpDieVia['TpDieVia']:null;
		$array = [];
	
		if ($tipoDieta) {
			
			$sql = "SELECT *
			FROM Via
			WHERE ViaStatus = 1
			AND ViaId = $tipoDieta
			AND ViaUnidade = $iUnidade";			
			$result = $conn->query($sql);
			$row = $result->fetchAll(PDO::FETCH_ASSOC);	
			
			foreach($row as $item){		
				array_push($array,[
					'status' => 'success',
					'id' => $item['ViaId'],
					'nome' => $item['ViaNome']
				]);
			}
		}
	
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'UNIDADEMEDIDA') {

		$sql = "SELECT AtUMeId, AtUMeNome
		FROM AtendimentoUnidadeMedida
		WHERE AtUMeStatus = 1
		AND AtUMeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){

			array_push($array,[
				'id' => $item['AtUMeId'],
				'nome' => $item['AtUMeNome']
			]);
		}
	
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'TIPOAPRAZAMENTO') {

		$sql = "SELECT TpAprId, TpAprNome
		FROM TipoAprazamento
		WHERE TpAprStatus = 1
		AND TpAprUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){

			array_push($array,[
				'id' => $item['TpAprId'],
				'nome' => $item['TpAprNome']
			]);
		}
	
		echo json_encode($array);
		
	} elseif ($tipoRequest == 'TIPODIETA') {

		$sql = "SELECT TpDieId, TpDieNome
		FROM TipoDieta
		WHERE TpDieStatus = 1
		AND TpDieUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){

			array_push($array,[
				'id' => $item['TpDieId'],
				'nome' => $item['TpDieNome']
			]);
		}
	
		echo json_encode($array);
		
	} elseif ($tipoRequest == 'TIPOCUIDADO') {

		$sql = "SELECT TpCuiId, TpCuiNome
		FROM TipoCuidado
		WHERE TpCuiStatus = 1
		AND TpCuiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){

			array_push($array,[
				'id' => $item['TpCuiId'],
				'nome' => $item['TpCuiNome']
			]);
		}
	
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'SALVAREVOLUCAOPRESCRICAO') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$array = [
			"UPDATE AtendimentoEvolucaoDiaria SET AtEDiEditavel = 0 WHERE AtEDiAtendimento = '$iAtendimentoId'",
			"UPDATE AtendimentoPrescricaoMedicamento SET AtPMeEditavel = 0 WHERE AtPMeAtendimento = '$iAtendimentoId'",
			"UPDATE AtendimentoPrescricaoDieta SET AtPDiEditavel = 0 WHERE AtPDiAtendimento = '$iAtendimentoId'",
			"UPDATE AtendimentoPrescricaoCuidado SET AtPCuEditavel = 0 WHERE AtPCuAtendimento = '$iAtendimentoId'",
		];

		foreach ($array as $sql) {
			$conn->query($sql);
		}

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Observação Hospitalar',
			'menssagem' => 'Dados Salvos com Sucesso!!!'
		]);

	}elseif ($tipoRequest == 'FINALIZAROBSERVACAOENTRADA') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$sql = "SELECT AtOEnId 
		FROM AtendimentoObservacaoEntrada
		WHERE AtOEnAtendimento = $iAtendimentoId
		AND AtOEnUnidade = $iUnidade";	
		$result = $conn->query($sql);
		$resultadoBusca = $result->fetch(PDO::FETCH_ASSOC);

		if ($resultadoBusca) {

			$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'AGUARDANDOLIBERACAOATENDIMENTO'";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "UPDATE Atendimento set AtendSituacao = '$rowSituacao[SituaId]' WHERE AtendId = $iAtendimentoId";
			$result = $conn->query($sql);

			$array = [
				"UPDATE AtendimentoEvolucaoDiaria SET AtEDiEditavel = 0 WHERE AtEDiAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoMedicamento SET AtPMeEditavel = 0 WHERE AtPMeAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoDieta SET AtPDiEditavel = 0 WHERE AtPDiAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoCuidado SET AtPCuEditavel = 0 WHERE AtPCuAtendimento = '$iAtendimentoId'",
			];
	
			foreach ($array as $sql) {
				$conn->query($sql);
			}
	
			$_SESSION['iAtendimentoId'] = $iAtendimentoId;
	
			echo json_encode([
				'status' => 'success',
				'titulo' => 'Observação Hospitalar',
				'menssagem' => 'Dados Salvos com Sucesso!!!'
			]);
			
		} else {
			echo json_encode([
				'titulo' => 'Observação Hospitalar',
				'status' => 'error',
				'menssagem' => 'Você deve preencher e salvar a entrada do paciente antes de Finalizar Atendimento!'
			]);
			exit;
		}	

	}elseif ($tipoRequest == 'FINALIZARINTERNACAOENTRADA') {

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$sql = "SELECT AtIEnId 
		FROM AtendimentoInternacaoEntrada
		WHERE AtIEnAtendimento = $iAtendimentoId
		AND AtIEnUnidade = $iUnidade";	
		$result = $conn->query($sql);
		$resultadoBusca = $result->fetch(PDO::FETCH_ASSOC);

		if ($resultadoBusca) {

			$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'AGUARDANDOLIBERACAOATENDIMENTO'";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "UPDATE Atendimento set AtendSituacao = '$rowSituacao[SituaId]' WHERE AtendId = $iAtendimentoId";
			$result = $conn->query($sql);

			$array = [
				"UPDATE AtendimentoEvolucaoDiaria SET AtEDiEditavel = 0 WHERE AtEDiAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoMedicamento SET AtPMeEditavel = 0 WHERE AtPMeAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoDieta SET AtPDiEditavel = 0 WHERE AtPDiAtendimento = '$iAtendimentoId'",
				"UPDATE AtendimentoPrescricaoCuidado SET AtPCuEditavel = 0 WHERE AtPCuAtendimento = '$iAtendimentoId'",
			];
	
			foreach ($array as $sql) {
				$conn->query($sql);
			}
	
			$_SESSION['iAtendimentoId'] = $iAtendimentoId;
	
			echo json_encode([
				'status' => 'success',
				'titulo' => 'Internaçao Hospitalar',
				'menssagem' => 'Dados Salvos com Sucesso!!!'
			]);

		} else {
			echo json_encode([
				'titulo' => 'Internação Hospitalar',
				'status' => 'error',
				'menssagem' => 'Você deve preencher e salvar a entrada do paciente antes de Finalizar Atendimento!'
			]);
			exit;
		}
		
	}

} catch (\Throwable $e) {

    $msg = '';

	switch($tipoRequest){
		
		default: $msg = 'Erro ao executar ação!!';break;
	}
	echo json_encode([
		'titulo' => 'Observacao Hospitalar',
		'status' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage(),
		'sql' => $sql
	]);
}