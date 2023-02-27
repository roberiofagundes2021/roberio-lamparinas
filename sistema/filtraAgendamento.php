<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra';

include('global_assets/php/conexao.php');

$tipoRequest = isset($_POST['tipoRequest'])?$_POST['tipoRequest']:'';

try{
	$iUnidade = $_SESSION['UnidadeId'];
	$usuarioId = $_SESSION['UsuarId'];

	if($tipoRequest == 'AGENDAMENTOS'){
		$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendHorario,AtModNome,AgendModalidade,
		AgendClienteResponsavel,AgendAtendimentoLocal,AtLocId,AgendServico,AgendObservacao,
		C.ClienNome as ClienNome,C.ClienId as ClienId,C.ClienCelular as ClienCelular,C.ClienTelefone as ClienTelefone,C.ClienEmail as ClienEmail,C.ClienDtNascimento as ClienDtNascimento,C.ClienCodigo as ClienCodigo,
		CR.ClienNome as RespoNome,CR.ClienId as RespoId,CR.ClienCelular as RespoCelular,CR.ClienTelefone as RespoTelefone,CR.ClienEmail as RespoEmail,CR.ClienDtNascimento as RespoDtNascimento,CR.ClienCodigo as RespoCodigo,
		AgendSituacao,SituaNome,SituaChave,SituaCor,Profissional.ProfiNome as ProfissionalNome,
		AtLocNome, SrVenNome, ProfiCbo, Profissao.ProfiNome as ProfissaoNome
		FROM Agendamento
		JOIN AtendimentoModalidade ON AtModId = AgendModalidade
		JOIN Situacao ON SituaId = AgendSituacao
		JOIN Cliente C ON C.ClienId = AgendCliente
		LEFT JOIN Cliente CR ON CR.ClienId = AgendClienteResponsavel
		JOIN Profissional ON Profissional.ProfiId = AgendProfissional
		JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
		JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
		JOIN ServicoVenda ON SrVenId = AgendServico
		WHERE AgendUnidade = $iUnidade";

		if(isset($_POST['status']) && $_POST['status']){
			$sql .= " AND SituaId = $_POST[status]";
		}
		if(isset($_POST['recepcao']) && $_POST['recepcao']){
			// $sql .= " AND SituaId = $_POST[recepcao]";
		}

		$prof = "(null)";

		if(isset($_POST['profissionais'])){
			$prof = "(";
			foreach($_POST['profissionais'] as $key => $item){
				$prof .= "$item,";
			}
			$prof = substr($prof, 0, -1);
			$prof .= ")";
		}
		$sql .= " AND Profissional.ProfiId in $prof";

		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($row as $item){
			array_push($array,[
				'id' => $item['AgendId'],
				'status' => 'ATT',
				'registro' => $item['AgendDataRegistro'],
				'data' => $item['AgendData'],
				'hora' => $item['AgendHorario'],
				'servico' => $item['AgendServico'],
				'observacao' => $item['AgendObservacao'],

				'servico' => [
					'id' => $item['AgendServico'],
					'nome' => $item['SrVenNome'],
				],
				'cliente' => [
					'id' => $item['ClienId'],
					'nome' => $item['ClienNome'],
					'celular' => $item['ClienCelular'],
					'telefone' => $item['ClienTelefone'],
					'email' => $item['ClienEmail'],
					'nascimento' => $item['ClienDtNascimento'],
					'codigo' => $item['ClienCodigo']
				],
				'responsavel' => isset($item['RespoId']) && $item['RespoId']?[
					'id' => $item['RespoId'],
					'nome' => $item['RespoNome'],
					'celular' => $item['RespoCelular'],
					'telefone' => $item['RespoTelefone'],
					'email' => $item['RespoEmail'],
					'nascimento' => $item['RespoDtNascimento'],
					'codigo' => $item['RespoCodigo']
				]:null,
				'local' => [
					'id' => $item['AtLocId'],
					'nome' => $item['AgendAtendimentoLocal']
				],
				'modalidade' => [
					'id' => $item['AgendModalidade'],
					'nome' => $item['AtModNome']
				],
				'situacao' => [
					'id' => $item['AgendSituacao'],
					'nome' => $item['SituaNome'],
					'chave' => $item['SituaChave'],
					'cor' => $item['SituaCor']
				]
			]);
		}
		
		echo json_encode($array);
	} elseif($tipoRequest == 'GETAGENDAMENTO'){
		$id = $_POST['id'];

		$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendProfissional,AgendHorario,AgendModalidade,
			AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,AgendCliente, AgendObservacao,AgendSituacao
			FROM Agendamento
			WHERE AgendId = $id AND AgendUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowAgendamento = $result->fetch(PDO::FETCH_ASSOC);

		$hora = explode(':',$rowAgendamento['AgendHorario']);
		
		echo json_encode([
			'id' => $rowAgendamento['AgendId'],
			'registro' => $rowAgendamento['AgendDataRegistro'],
			'data' => $rowAgendamento['AgendData'],
			'hora' => "$hora[0]:$hora[1]",
			'servico' => $rowAgendamento['AgendServico'],
			'observacao' => $rowAgendamento['AgendObservacao'],
			'status' => 'ATT',

			'servico' => $rowAgendamento['AgendServico'],
			'cliente' => $rowAgendamento['AgendCliente'],
			'profissional' => $rowAgendamento['AgendProfissional'],
			'responsavel' => isset($rowAgendamento['RespoId']) && $rowAgendamento['RespoId']?$rowAgendamento['RespoId']:null,
			'local' => $rowAgendamento['AgendAtendimentoLocal'],
			'modalidade' => $rowAgendamento['AgendModalidade'],
			'situacao' => $rowAgendamento['AgendSituacao']
		]);
	} elseif ($tipoRequest == 'PACIENTES'){
	
		$sql = "SELECT ClienId,ClienCodigo,ClienNome
		,ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienCartaoSus,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,
		ClienCelular,ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
		FROM Cliente WHERE ClienUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ClienId'],
				'nome' => $item['ClienNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'MODALIDADES'){
	
		$sql = "SELECT AtModId,AtModNome,AtModChave,AtModSituacao,AtModUsuarioAtualizador
		FROM AtendimentoModalidade
		WHERE AtModUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['AtModId'],
				'nome' => $item['AtModNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'SERVICOS'){
		// $sql = "SELECT SrVenId,SrVenNome,SrVenPlanoConta,SrVenDetalhamento,SrVenValorCusto,
		// SrVenOutrasDespesas,SrVenCustoFinal,SrVenMargemLucro,SrVenValorVenda,SrVenStatus,
		// SrVenUsuarioAtualizador,SrVenUnidade
		// FROM Servico WHERE SrVenUnidade = $iUnidade";

		$sql = "SELECT SrVenId,SrVenNome,SrVenCodigo
		FROM ServicoVenda WHERE SrVenUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['SrVenId'],
				'nome' => $item['SrVenNome'],
				'codigo' => $item['SrVenCodigo']
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'MEDICOS'){
		$servico = isset($_POST['servico'])?$_POST['servico']:null;
		$data = isset($_POST['data'])?$_POST['data']:null;
		$hora = isset($_POST['hora'])?$_POST['hora']:null;

		$sql = "SELECT DISTINCT ProfiId,ProfiNome
		FROM ProfissionalXServicoVenda
		JOIN Profissional ON ProfiId = PrXSVProfissional
		WHERE ProfiUnidade = $iUnidade";

		if($servico){
			$sql .= "AND PrXSVServicoVenda = $servico";
		}

		$result = $conn->query($sql);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($result as $item){
			$arrayDatasRecorrente = [];
			$arrayDatasIntervalo = [];

			$sql = "SELECT AgBloDataHoraInicio,AgBloDataHoraFim,AgBloRecorrenteSegunda,AgBloRecorrenteTerca,
				AgBloRecorrenteQuarta,AgBloRecorrenteQuinta,AgBloRecorrenteSexta,AgBloRecorrenteSabado,
				AgBloRecorrenteDomingo,AgBloRecorrenteQuantidade,AgBloRecorrenteDataFinal,
				AgBloRecorrenteRepeticao
				FROM AgendamentoBloqueio
				WHERE AgBloProfissional = $item[ProfiId] AND AgBloUnidade = $iUnidade";
			$resultBloqueio = $conn->query($sql);
			$resultBloqueio = $resultBloqueio->fetchAll(PDO::FETCH_ASSOC);

			foreach($resultBloqueio as $bloqueio){
				$arrayDays = [];

				// vai montar um array com os dias da semana que estão marcados para bloqueio
				if($bloqueio['AgBloRecorrenteSegunda']){
					array_push($arrayDays,'Mon');
				}if($bloqueio['AgBloRecorrenteTerca']){
					array_push($arrayDays,'Tue');
				}if($bloqueio['AgBloRecorrenteQuarta']){
					array_push($arrayDays,'Wed');
				}if($bloqueio['AgBloRecorrenteQuinta']){
					array_push($arrayDays,'Thu');
				}if($bloqueio['AgBloRecorrenteSexta']){
					array_push($arrayDays,'Fri');
				}if($bloqueio['AgBloRecorrenteSabado']){
					array_push($arrayDays,'Sat');
				}if($bloqueio['AgBloRecorrenteDomingo']){
					array_push($arrayDays,'Sun');
				}

				$dt = explode(' ',$bloqueio['AgBloDataHoraInicio'])[0];
				$hr = explode(' ',$bloqueio['AgBloDataHoraInicio'])[1];
				// var_dump($arrayDays);
				// exit;

				// se o bloqueio for recorrente...
				if($bloqueio['AgBloRecorrenteQuantidade']){
					$data = date_create($dt); // pegando a data de início como objeto date...

					// define as datas de acordo à repetição
					if($bloqueio['AgBloRecorrenteRepeticao'][1]=="S"){// se for semanal "S"...
						for($x=0; $x <= intval($bloqueio['AgBloRecorrenteQuantidade'])-1; $x++){
							$dateLoop = date_create(date_format($data,"Y-m-d"));
							// pegando o "Domingo => Sun" como ultimo dia da semana no loop while
							do{
								if(in_array(date_format($dateLoop,"D"),$arrayDays)){
									array_push($arrayDatasRecorrente, date_format($dateLoop,"Y-m-d"));
								}
								$dateLoop = $dateLoop->modify("+1 Day");
							}while(date_format($dateLoop,"D") != 'Sun');

							$days = (intval($bloqueio['AgBloRecorrenteRepeticao'][0])*7);
							$data = $data->modify("+$days Day");
						}
					}
				}else{
					$dtF = explode(' ',$bloqueio['AgBloDataHoraFim'])[0];
					$hrF = explode(' ',$bloqueio['AgBloDataHoraFim'])[1];

					array_push($arrayDatasIntervalo, [
						'dataI' => $dt,
						'dataF' => $dtF,
						'horaI' => $hr,
						'horaF' => $hrF,
					]);
				}
			}

			array_push($array,[
				'id' => $item['ProfiId'],
				'nome' => $item['ProfiNome'],
				'datasRecorrente' => $arrayDatasRecorrente,
				'datasIntervalo' => $arrayDatasIntervalo,
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'LOCALATENDIMENTO'){
		// $iMedico = $_POST['iMedico'];
		$hoje = date('Y-m-d');

		// $sql= "SELECT DISTINCT AtLocId,AtLocNome,AtLocStatus,AtLocUsuarioAtualizador,AtLocUnidade
		// FROM AtendimentoLocal
		// JOIN ProfissionalAgenda ON PrAgeAtendimentoLocal = AtLocId
		// WHERE PrAgeProfissional = $iMedico AND PrAgeData >= '$hoje' AND AtLocUnidade = $iUnidade";
		// $result = $conn->query($sql);

		$sql= "SELECT DISTINCT AtLocId,AtLocNome,AtLocStatus,AtLocUsuarioAtualizador,AtLocUnidade
		FROM AtendimentoLocal
		WHERE AtLocUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['AtLocId'],
				'nome' => $item['AtLocNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'ADDAGENDAMENTO'){
		$registro = date('Y-m-d');
		$data = $_POST['data'];
		$hora = $_POST['hora'];
		$paciente = $_POST['paciente'];
		$modalidade = $_POST['modalidade'];
		$servico = $_POST['servico'];
		$profissional = $_POST['profissional'];
		$local = $_POST['local'];
		$observacao = $_POST['observacao'];
		$cmbSituacao = $_POST['situacao'];
		$isUpdate = $_POST['idAgendamento'];

		if($isUpdate){

			if($data < date('Y-m-d') || ($data == date('Y-m-d') && $hora < date('H:i'))){
				$sql = "UPDATE Agendamento SET
					AgendSituacao = $cmbSituacao,
					AgendUsuarioAtualizador = $usuarioId
					WHERE AgendId = $isUpdate";
			}else{
				$sql = "UPDATE Agendamento SET 
					AgendCliente = $paciente,
					AgendModalidade = $modalidade,
					AgendServico = $servico, 
					AgendProfissional = $profissional,
					AgendData = '$data',
					AgendHorario = '$hora',
					AgendAtendimentoLocal = $local,
					AgendObservacao = '$observacao',
					AgendSituacao = $cmbSituacao,
					AgendUnidade = $iUnidade,
					AgendUsuarioAtualizador = $usuarioId
					WHERE AgendId = $isUpdate";
			}
			$conn->query($sql);

			echo json_encode([
				'titulo' => 'Atualizar agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento atualizado com sucesso!!!',
			]);
		}else{
			if($data < date('Y-m-d') || ($data == date('Y-m-d') && $hora < date('H:i'))){
				echo json_encode([
					'titulo' => 'Incluir agendamento',
					'status' => 'error',
					'menssagem' => 'Agendamento com data retroativa!!!'
				]);
			}
			$sql = "INSERT INTO Agendamento(AgendDataRegistro,AgendCliente,AgendModalidade,
			AgendServico,AgendProfissional,AgendData,AgendHorario,AgendAtendimentoLocal,
			AgendObservacao,AgendSituacao,AgendUnidade,AgendUsuarioAtualizador)
			VALUES ('$registro','$paciente','$modalidade','$servico','$profissional','$data','$hora',
			'$local','$observacao','$cmbSituacao','$iUnidade','$usuarioId')";
			$result = $conn->query($sql);

			echo json_encode([
				'titulo' => 'Incluir agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento inserido com sucesso!!!',
				'sql' => $result
			]);
		}
	} elseif ($tipoRequest == 'ATTAGENDAMENTO'){
		$cmbSituacao = $_POST['situacao'];

		if($isUpdate){
			$sql = "UPDATE Agendamento SET
				AgendSituacao = $cmbSituacao,
				AgendUsuarioAtualizador = $usuarioId
				WHERE AgendId = $isUpdate";
			$conn->query($sql);

			echo json_encode([
				'titulo' => 'Atualizar agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento atualizado com sucesso!!!',
			]);
		}else{
			$sql = "INSERT INTO Agendamento(AgendDataRegistro,AgendCliente,AgendModalidade,
			AgendServico,AgendProfissional,AgendData,AgendHorario,AgendAtendimentoLocal,
			AgendObservacao,AgendSituacao,AgendUnidade,AgendUsuarioAtualizador)
			VALUES ('$registro','$paciente','$modalidade','$servico','$profissional','$data','$hora',
			'$local','$observacao','$cmbSituacao','$iUnidade','$usuarioId')";
			$result = $conn->query($sql);

			echo json_encode([
				'titulo' => 'Incluir agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento inserido com sucesso!!!',
				'sql' => $result
			]);
		}
	} elseif ($tipoRequest == 'ADDPACIENTENOVO'){
		$tipoRequest = isset($_POST['tipoRequest'])?$_POST['tipoRequest']:null;
		$prontuario = isset($_POST['prontuario'])?$_POST['prontuario']:null;
		$nome = isset($_POST['nome'])?$_POST['nome']:null;
		$nomeSocial = isset($_POST['nomeSocial'])?$_POST['nomeSocial']:null;
		$cpf = isset($_POST['cpf'])?$_POST['cpf']:null;
		$cns = isset($_POST['cns'])?$_POST['cns']:null;
		$rg = isset($_POST['rg'])?$_POST['rg']:null;
		$emissor = isset($_POST['emissor'])?$_POST['emissor']:null;
		$uf = isset($_POST['uf'])?$_POST['uf']:null;
		$sexo = isset($_POST['sexo'])?$_POST['sexo']:null;
		$nascimento = isset($_POST['nascimento'])?$_POST['nascimento']:null;
		$nomePai = isset($_POST['nomePai'])?$_POST['nomePai']:null;
		$nomeMae = isset($_POST['nomeMae'])?$_POST['nomeMae']:null;
		$racaCor = isset($_POST['racaCor'])?$_POST['racaCor']:null;
		$naturalidade = isset($_POST['naturalidade'])?$_POST['naturalidade']:null;
		$profissao = isset($_POST['profissao'])?$_POST['profissao']:null;
		$estadoCivil = isset($_POST['estadoCivil'])?$_POST['estadoCivil']:null;
		$cep = isset($_POST['cep'])?$_POST['cep']:null;
		$endereco = isset($_POST['endereco'])?$_POST['endereco']:null;
		$numero = isset($_POST['numero'])?$_POST['numero']:null;
		$complemento = isset($_POST['complemento'])?$_POST['complemento']:null;
		$bairro = isset($_POST['bairro'])?$_POST['bairro']:null;
		$cidade = isset($_POST['cidade'])?$_POST['cidade']:null;
		$estado = isset($_POST['estado'])?$_POST['estado']:null;
		$contato = isset($_POST['contato'])?$_POST['contato']:$_POST['nome'];
		$telefone = isset($_POST['telefone'])?$_POST['telefone']:null;
		$celular = isset($_POST['celular'])?$_POST['celular']:null;
		$email = isset($_POST['email'])?$_POST['email']:null;
		$observacao = isset($_POST['observacao'])?$_POST['observacao']:null;
		$sCodigo = null;

		$sql = "SELECT ClienId
				FROM Cliente
				Where ClienCpf = $cpf";
		$result = $conn->query("$sql");
		$rowCPF = $result->fetchAll(PDO::FETCH_ASSOC);

		if(COUNT($rowCPF)){
			echo json_encode([
				'titulo' => 'Incluir paciente',
				'status' => 'error',
				'menssagem' => 'CPF já cadastrado!!!'
			]);
		}

		$sql = "SELECT SituaId
				FROM Situacao
				Where SituaChave = 'ATIVO'";
		$result = $conn->query("$sql");
		$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);	

		$sql = "SELECT COUNT(isnull(clienCodigo,0)) as Codigo
				FROM Cliente
				Where ClienUnidade = $iUnidade";
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);	
		
		$sCodigo = (int)$rowCodigo['Codigo'] + 1;
		$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);
	
		// insere o novo usuário no banco
		$sql = "INSERT INTO  Cliente(ClienCodigo,ClienNome,ClienNomeSocial,ClienCpf,ClienRg,
		ClienOrgaoEmissor,ClienUf,ClienSexo,ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienEstadoCivil,
		ClienNaturalidade,ClienRacaCor,ClienCartaoSus,ClienProfissao,ClienCep,
		ClienEndereco,ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,
		ClienTelefone,ClienCelular,ClienEmail,ClienObservacao,ClienStatus,
		ClienUsuarioAtualizador,ClienUnidade)
		VALUES ('$sCodigo','$nomePaciente','$nomeSocial','$cpf','$rg','$emissor','$uf','$sexo','$nascimento','$nomePai','$nomeMae',
		'$estadoCivil','$naturalidade','$racaCor','$cns','$profissao','$cep','$endereco','$numero','$complemento',
		'$bairro','$cidade','$estado','$contato','$telefone','$celular','$email','$observacao',$rowSituacao[SituaId],$usuarioId,$iUnidade)";
		$conn->query($sql);

		$lestIdCliente = $conn->lastInsertId();
		echo json_encode([
			'titulo' => 'Incluir paciente',
			'status' => 'success',
			'menssagem' => 'Paciente inserido com sucesso!!!',
			'id' => $lestIdCliente,
		]);
	} elseif ($tipoRequest == 'SITUACAO'){
		$sql = "SELECT SituaId, SituaNome
			FROM Situacao
			WHERE SituaChave in ('AGENDADO','CONFIRMADO','FILAESPERA','ATENDIDO','CANCELADO')";
		$result = $conn->query($sql);
		$situacoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($situacoes as $item){
			array_push($array, [
				'id' => $item['SituaId'],
				'nome' => $item['SituaNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'UPDATEDATA'){
		$id = $_POST['id'];
		$data = $_POST['data'];
		$hora = $_POST['hora'];

		$sql = "UPDATE Agendamento SET
			AgendData = '$data',
			AgendHorario = '$hora'
			WHERE AgendId = $id";
		$conn->query($sql);

		echo json_encode($data);
	} elseif ($tipoRequest == 'ADDEVENTO'){
		$dataHoraInicio = '';
		$dataHoraFim = '';

		$medicoConfig = $_POST['medicoConfig'];
		$bloqueio = $_POST['bloqueio'];
		$justificativa = $_POST['justificativa'];
		$inputDataInicioBloqueio = $_POST['inputDataInicioBloqueio'];
		$inputHoraInicioBloqueio = $_POST['inputHoraInicioBloqueio'];
		$inputDataFimBloqueio = $_POST['inputDataFimBloqueio'];
		$inputHoraFimBloqueio = $_POST['inputHoraFimBloqueio'];
		$segunda = $_POST['segunda'];
		$terca = $_POST['terca'];
		$quarta = $_POST['quarta'];
		$quinta = $_POST['quinta'];
		$sexta = $_POST['sexta'];
		$sabado = $_POST['sabado'];
		$domingo = $_POST['domingo'];
		$recorrente = $_POST['recorrente'];
		$repeticao = $_POST['repeticao'];
		$quantidadeRecorrencia = $_POST['quantidadeRecorrencia']?($_POST['quantidadeRecorrencia']>99?99:$_POST['quantidadeRecorrencia']):0;
		$dataRecorrencia = $_POST['dataRecorrencia'];
		$dataHoraInicio = $inputDataInicioBloqueio.' '.$inputHoraInicioBloqueio;

		if(!$recorrente){
			$dataHoraFim = $inputDataFimBloqueio.' '.$inputHoraFimBloqueio;
		}

		$sql = "INSERT INTO AgendamentoBloqueio(AgBloProfissional,AgBloDescricao,AgBloJustificativa,
		AgBloDataHoraInicio,AgBloDataHoraFim,AgBloRecorrenteRepeticao,AgBloRecorrenteSegunda,
		AgBloRecorrenteTerca,AgBloRecorrenteQuarta,AgBloRecorrenteQuinta,AgBloRecorrenteSexta,
		AgBloRecorrenteSabado,AgBloRecorrenteDomingo,AgBloRecorrenteQuantidade,AgBloRecorrenteDataFinal,
		AgBloUnidade)
		VALUES($medicoConfig,'$bloqueio','$justificativa','$dataHoraInicio','$dataHoraFim',
		'$repeticao',$segunda,$terca,$quarta,$quinta,$sexta,$sabado,$domingo,$quantidadeRecorrencia,
		'$dataRecorrencia',$iUnidade)";
		$conn->query($sql);

		echo json_encode([
			'titulo' => 'Evento',
			'status' => 'success',
			'menssagem' => 'Evento agendado com sucesso!!!'
		]);
	} elseif ($tipoRequest == 'ADDCONFIGUNIDADE'){
		$horaAbertura = $_POST['inputHoraAberturaUnidade'];
		$horaFechamento = $_POST['inputHoraFechamentoUnidade'];
		$horaInicio = $_POST['inputHoraInicioUnidade'];
		$horaFim = $_POST['inputHoraFimUnidade'];
		$intervalo = $_POST['inputHoraIntervaloUnidade'];
		$observacao = $_POST['observacaoUnidade'];

		$segunda = $_POST['segunda'];
		$terca = $_POST['terca'];
		$quarta = $_POST['quarta'];
		$quinta = $_POST['quinta'];
		$sexta = $_POST['sexta'];
		$sabado = $_POST['sabado'];
		$domingo = $_POST['domingo'];

		$sql = "SELECT AgFUnId,AgFUnSegunda,AgFUnTerca,AgFUnQuarta,AgFUnQuinta,AgFUnSexta,AgFUnSabado,AgFUnDomingo,AgFUnObservacao,
		AgFUnHorarioAbertura,AgFUnHorarioFechamento,AgFUnHorarioAlmocoInicio,AgFUnHorarioAlmocoFim,AgFUnIntervaloAgenda
		FROM AgendamentoFuncionamentoUnidade WHERE AgFUnUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowConfig = $result->fetch(PDO::FETCH_ASSOC);

		
		if($rowConfig){
			$sql = "UPDATE AgendamentoFuncionamentoUnidade SET
			AgFUnSegunda= $segunda,
			AgFUnTerca = $terca,
			AgFUnQuarta = $quarta,
			AgFUnQuinta = $quinta,
			AgFUnSexta = $sexta,
			AgFUnSabado = $sabado,
			AgFUnDomingo = $domingo,
			AgFUnObservacao = '$observacao',
			AgFUnHorarioAbertura = '$horaAbertura',
			AgFUnHorarioFechamento = '$horaFechamento',
			AgFUnHorarioAlmocoInicio = '$horaInicio',
			AgFUnHorarioAlmocoFim = '$horaFim',
			AgFUnIntervaloAgenda = $intervalo
			WHERE AgFUnId = $rowConfig[AgFUnId] AND AgFUnUnidade = $iUnidade";
			$conn->query($sql);
		}else{
			$sql = "INSERT INTO AgendamentoFuncionamentoUnidade(AgFUnSegunda,AgFUnTerca,AgFUnQuarta,AgFUnQuinta,
			AgFUnSexta,AgFUnSabado,AgFUnDomingo,AgFUnObservacao,AgFUnHorarioAbertura,AgFUnHorarioFechamento,
			AgFUnHorarioAlmocoInicio,AgFUnHorarioAlmocoFim,AgFUnIntervaloAgenda,AgFUnUnidade)
			VALUES($segunda,$terca,$quarta,$quinta,$sexta,$sabado,$domingo,'$observacao','$horaAbertura','$horaFechamento',
			'$horaInicio','$horaFim',$intervalo,$iUnidade)";
			$conn->query($sql);
		}

		echo json_encode([
			'titulo' => 'Configuração de Unidade',
			'status' => 'success',
			'menssagem' => 'Configuração de Unidade salva com sucesso!!!'
		]);
	} elseif ($tipoRequest == 'EXCLUI'){
		$iAgendamento = $_POST['id'];
	
		$sql = "DELETE FROM AgendamentoXServico WHERE AgXSeAgendamento = $iAgendamento
		and AgXSeUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM Agendamento WHERE AgendId = $iAgendamento and AgendUnidade = $iUnidade";
		$conn->query($sql);

		echo json_encode([
			'titulo' => 'Excluir agendamento',
			'status' => 'success',
			'menssagem' => 'Agendamento excluido com sucesso!!!',
		]);
	} elseif($tipoRequest == 'GETCONFIG'){
		$array = [];

		$sql = "SELECT AgFUnId,AgFUnSegunda,AgFUnTerca,AgFUnQuarta,AgFUnQuinta,AgFUnSexta,AgFUnSabado,AgFUnDomingo,AgFUnObservacao,
		AgFUnHorarioAbertura,AgFUnHorarioFechamento,AgFUnHorarioAlmocoInicio,AgFUnHorarioAlmocoFim,AgFUnIntervaloAgenda
		FROM AgendamentoFuncionamentoUnidade WHERE AgFUnUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowConfig = $result->fetch(PDO::FETCH_ASSOC);

		if($rowConfig){
			$array = [
				'id' => $rowConfig['AgFUnId'],
				'segunda' => $rowConfig['AgFUnSegunda'],
				'terca' => $rowConfig['AgFUnTerca'],
				'quarta' => $rowConfig['AgFUnQuarta'],
				'quinta' => $rowConfig['AgFUnQuinta'],
				'sexta' => $rowConfig['AgFUnSexta'],
				'sabado' => $rowConfig['AgFUnSabado'],
				'domingo' => $rowConfig['AgFUnDomingo'],
				'observacao' => $rowConfig['AgFUnObservacao'],
				'abertura' => explode('.',$rowConfig['AgFUnHorarioAbertura'])[0],
				'fechamento' => explode('.',$rowConfig['AgFUnHorarioFechamento'])[0],
				'almocoInicio' => explode('.',$rowConfig['AgFUnHorarioAlmocoInicio'])[0],
				'almocoFim' => explode('.',$rowConfig['AgFUnHorarioAlmocoFim'])[0],
				'intervalo' => $rowConfig['AgFUnIntervaloAgenda']
			];
		}

		echo json_encode($array);
	} elseif($tipoRequest == 'CHECKAGENDAUNIDADE'){
		$data = $_POST['data'];
		$hora = isset($_POST['hora'])?$_POST['hora']:false;

		$diaAtual = date_create($data);
		$diaAtual = date_format($diaAtual,"D");

		switch($diaAtual){
			case 'Mon': $diaAtual = 'AgFUnSegunda';break;
			case 'Tue': $diaAtual = 'AgFUnTerca';break;
			case 'Wed': $diaAtual = 'AgFUnQuarta';break;
			case 'Thu': $diaAtual = 'AgFUnQuinta';break;
			case 'Fri': $diaAtual = 'AgFUnSexta';break;
			case 'Sat': $diaAtual = 'AgFUnSabado';break;
			case 'Sun': $diaAtual = 'AgFUnDomingo';break;
			default: $diaAtual = '';break;
		}

		$sql = "SELECT AgFUnSegunda,AgFUnTerca,AgFUnQuarta,AgFUnQuinta,AgFUnSexta,AgFUnSabado,AgFUnDomingo,
		AgFUnHorarioAbertura,AgFUnHorarioFechamento,AgFUnHorarioAlmocoInicio,AgFUnHorarioAlmocoFim
		FROM AgendamentoFuncionamentoUnidade WHERE AgFUnUnidade = $iUnidade";
		$result = $conn->query($sql);
		$config = $result->fetch(PDO::FETCH_ASSOC);

		if(($config[$diaAtual])){
			if($hora){
				$abertura = explode('.',$config['AgFUnHorarioAbertura'])[0];
				$fechamento = explode('.',$config['AgFUnHorarioFechamento'])[0];
				$almoçoI = explode('.',$config['AgFUnHorarioAlmocoInicio'])[0];
				$almoçoF = explode('.',$config['AgFUnHorarioAlmocoFim'])[0];
	
				if($hora >= $abertura && $hora < $fechamento){
					if($hora >= $almoçoI && $hora < $almoçoF){
						echo json_encode([
							'titulo' => 'Agendamento',
							'tipo' => 'error',
							'menssagem' => 'A o horário selecionado está reservado para almoço na unidade!!',
						]);
					}else{
						echo json_encode([
							'titulo' => 'Agendamento',
							'tipo' => 'success',
							'menssagem' => 'data e hora válida',
						]);
					}
				}else{
					echo json_encode([
						'titulo' => 'Agendamento',
						'tipo' => 'error',
						'menssagem' => 'O horário não condiz com o de funcionamento da unidade!!',
					]);
				}
			}
		}else{
			echo json_encode([
				'titulo' => 'Agendamento',
				'tipo' => 'error',
				'menssagem' => 'A data selecionado não condiz com o de funcionamento da unidade',
			]);
		}
		
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case '': $msg = 'Informe o tipo de requisição';break;
		case 'AGENDAMENTOS': $msg = 'Erro ao carregar agendamentos';break;
		default: $msg = "Erro ao executar ação COD.: $tipoRequest";break;
	}

	echo json_encode([
		'titulo' => 'Agendamento',
		'tipo' => 'error',
		'menssagem' => $msg,
		'sql' => $sql,
		'error' => $e->getMessage()
	]);
}
