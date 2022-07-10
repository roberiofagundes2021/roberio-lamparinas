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

	if($tipoRequest == 'AGENDAMENTOS'){
		// $sql = "SELECT AgendId,AgendDataRegistro,AgendCliente,AgendModalidade,AgendClienteResponsavel,
		// AgendObservacao,AtModNome,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor
		// FROM Agendamento
		// JOIN AtendimentoModalidade ON AtModId = AgendModalidade
		// JOIN Situacao ON SituaId = AgendSituacao
		// JOIN Cliente ON ClienId = AgendCliente
		// WHERE AgendUnidade = $iUnidade";
		$sql = "SELECT AgendId,AgendDataRegistro,AgendCliente,AgendModalidade,AgendClienteResponsavel,
		AgendObservacao,AtModNome,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
		AgXSeServico,ProfiNome,AgXSeData,AgXSeHorario,AtLocNome, SrVenNome
		FROM AgendamentoXServico
		JOIN Agendamento ON AgendId = AgXSeAgendamento
		JOIN AtendimentoModalidade ON AtModId = AgendModalidade
		JOIN Situacao ON SituaId = AgendSituacao
		JOIN Cliente ON ClienId = AgendCliente
		JOIN Profissional ON ProfiId = AgXSeProfissional
		JOIN AtendimentoLocal ON AtLocId = AgXSeAtendimentoLocal
		JOIN ServicoVenda ON SrVenId = AgXSeServico
		WHERE AgendUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		foreach($row as $item){
			$att = "<a style='color: black' href='#' onclick='atualizaAgendamento(\"EDITA\", $item[AgendId])' class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$exc = "<a style='color: black' href='#' onclick='atualizaAgendamento(\"EXCLUI\", $item[AgendId])' class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
			$acoes = "<div class='list-icons'>
						${att}
						${exc}
					</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			array_push($array, [
				'data' => [
					mostraData($item['AgXSeData']),
					$item['AgXSeHorario'],
					$item['ClienNome'],
					$item['ProfiNome'],
					$item['SrVenNome'],
					$item['AtModNome'],
					$contato,
					"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",
					$acoes
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAgendamento' => $item['AgendId'],
					'sObservacao' => $item['AgendObservacao']
				]
			]);
		}
	
		echo json_encode($array);
	} elseif ($tipoRequest == 'SITUACOES'){
		$sql = "SELECT SituaId,SituaNome,SituaChave,SituaStatus,SituaUsuarioAtualizador,SituaCor
		FROM Situacao
		WHERE SituaChave in ('AGENDADO','CONFIRMADO','CANCELADO')";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['SituaId'],
				'nome' => $item['SituaNome'],
				'SituaChave' => $item['SituaChave'],
			]);
		}
	
		echo json_encode($array);
	} elseif ($tipoRequest == 'MUDARSITUACAO'){
		$iAgendamento = $_POST['iAgendamento'];
		$iSituacao = $_POST['iSituacao'];
		$sObservacao = $_POST['sObservacao'];
	
		$sql = "UPDATE Agendamento SET AgendSituacao = $iSituacao, AgendObservacao = '$sObservacao'
		WHERE AgendId = $iAgendamento and AgendUnidade = $iUnidade";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Alterar Situação',
			'tipo' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!'
		]);
	} elseif ($tipoRequest == 'PACIENTES'){
	
		$sql = "SELECT ClienId,ClienTipo,ClienCodigo,ClienNome,ClienRazaoSocial,ClienCnpj,
		ClienInscricaoMunicipal,ClienInscricaoEstadual,ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienCartaoSus,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,
		ClienCelular,ClienEmail,ClienSite,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
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
	} elseif ($tipoRequest == 'ADDPACIENTENOVO'){

		$nomePaciente = $_POST['nomePaciente'];
		$telefone = $_POST['telefone'];
		$celular = $_POST['celular'];
		$email = $_POST['email'];
		$observacao = $_POST['observacao'];
	
		// insere o novo usuário no banco
		$sql = "INSERT INTO  Cliente(ClienNome,ClienTelefone,ClienCelular,ClienEmail,ClienObservacao,
		ClienTipo,ClienStatus,ClienUnidade,ClienUsuarioAtualizador)
		VALUES ('$nomePaciente','$telefone','$celular','$email','$observacao','J',1,$iUnidade,$usuarioId)";
		$conn->query($sql);

		$lestIdCliente = $conn->lastInsertId();

		// busca todos os usuários com o novo inserido para adicionalo ja selecionado no select
		$sql = "SELECT ClienId,ClienTipo,ClienCodigo,ClienNome,ClienRazaoSocial,ClienCnpj,
		ClienInscricaoMunicipal,ClienInscricaoEstadual,ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienCartaoSus,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,
		ClienCelular,ClienEmail,ClienSite,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
		FROM Cliente";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ClienId'],
				'nome' => $item['ClienNome'],
				'isSelected' => $item['ClienId'] == $lestIdCliente? 'selected':''
			]);
		}
		echo json_encode([
			'titulo' => 'Incluir paciente',
			'status' => 'success',
			'menssagem' => 'Paciente inserido com sucesso!!!',
			'array' => $array,
		]);
	} elseif ($tipoRequest == 'MODALIDADES'){
	
		$sql = "SELECT AtModId,AtModNome,AtModChave,AtModSituacao,AtModUsuarioAtualizador
		FROM AtendimentoModalidade";
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

		$sql = "SELECT SrVenId,SrVenNome
		FROM ServicoVenda WHERE SrVenUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['SrVenId'],
				'nome' => $item['SrVenNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'CLASSIFICACAO'){
		$sql = "SELECT AtClaId,AtClaNome,AtClaNomePersonalizado,AtClaChave,AtClaModelo,AtClaStatus,
		AtClaUsuarioAtualizador,AtClaUnidade
		FROM AtendimentoClassificacao WHERE AtClaUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['AtClaId'],
				'nome' => $item['AtClaNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'MEDICOS'){
		$sql = "SELECT ProfiId,ProfiTipo,ProfiCodigo,ProfiNome,ProfiRazaoSocial,ProfiCnpj,
		ProfiInscricaoMunicipal,ProfiInscricaoEstadual,ProfiCpf,ProfiRg,ProfiOrgaoEmissor,ProfiUf,ProfiSexo,
		ProfiDtNascimento,ProfiProfissao,ProfiNumConselho,ProfiCNES,ProfiEspecialidade,ProfiCep,ProfiEndereco,
		ProfiNumero,ProfiComplemento,ProfiBairro,ProfiCidade,ProfiEstado,ProfiContato,ProfiTelefone,
		ProfiCelular,ProfiEmail,ProfiSite,ProfiObservacao,ProfiBanco,ProfiAgencia,ProfiConta,
		ProfiInformacaoAdicional,ProfiStatus,ProfiUsuarioAtualizador,ProfiUnidade
		FROM Profissional WHERE ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ProfiId'],
				'nome' => $item['ProfiNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'LOCALATENDIMENTO'){
		$sql = "SELECT AtLocId,AtLocNome,AtLocStatus,AtLocUsuarioAtualizador,AtLocUnidade
		FROM AtendimentoLocal WHERE AtLocUnidade = $iUnidade";
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
		$data = $_POST['data'];
		$paciente = $_POST['paciente'];
		$modalidade = $_POST['modalidade'];
		$observacao = $_POST['observacao'];
		$cmbSituacao = $_POST['cmbSituacao'];
		$servicos = $_SESSION['SERVICOS'];

		foreach($servicos as $item){
			$iMedico = $item['iMedico'];
			$data = $item['data'];
			$hora = $item['hora'];

			$sql = "SELECT AgXSeId FROM AgendamentoXServico 
			WHERE AgXSeData = '$data' and AgXSeHorario = '$hora' and AgXSeProfissional = '$iMedico'";
			$result = $conn->query($sql);
			$row = $result->fetchAll(PDO::FETCH_ASSOC);

			if(COUNT($row)){
				echo json_encode([
					'titulo' => 'Conflito de serviço',
					'status' => 'error',
					'menssagem' => 'Um dos serviços já possui cadastro com o mesmo Profissional, data e horário',
				]);
				exit;
			}
		}

		if(!isset($_POST['isUpdate'])){
			$tipoRequest = 'ATTAGENDAMENTO';
			$sql = "INSERT INTO Agendamento(AgendDataRegistro,AgendCliente,AgendModalidade,
			AgendObservacao,AgendSituacao,AgendUnidade,AgendUsuarioAtualizador)
			VALUES('$data','$paciente','$modalidade','$observacao','$cmbSituacao','$iUnidade','$usuarioId')";
			$conn->query($sql);
			$lastIdInsert = $conn->lastInsertId();
	
			$array = [];
			foreach($servicos as $servico){
				$iServico = $servico['iServico'];
				$iMedico = $servico['iMedico'];
				$data = $servico['data'];
				$hora = $servico['hora'];
				$iLocal = $servico['iLocal'];
	
				$sql = "INSERT INTO AgendamentoXServico(AgXSeAgendamento,AgXSeServico,AgXSeProfissional,
				AgXSeData,AgXSeHorario,AgXSeAtendimentoLocal,AgXSeUsuarioAtualizador,AgXSeUnidade)
				VALUES($lastIdInsert,$iServico,$iMedico,'$data','$hora',$iLocal,$usuarioId,$iUnidade)";
				array_push($array, $sql);
			}
	
			foreach($array as $sql){
				$conn->query($sql);
			}
	
			// vai limpar todos os dados da sessão utilizadas em agendamento após o cadastro
			$_SESSION['SERVICOS'] = [];
	
			echo json_encode([
				'titulo' => 'Incluir agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento inserido com sucesso!!!',
			]);
		} else {
			$isUpdate = $_POST['isUpdate'];

			$sql = "UPDATE Agendamento SET
			AgendDataRegistro = '$data',
			AgendCliente = $paciente,
			AgendModalidade = $modalidade,
			AgendObservacao = '$observacao',
			AgendSituacao = $cmbSituacao,
			AgendUsuarioAtualizador = $usuarioId
			WHERE AgendId = $isUpdate";
			$conn->query($sql);

			$sql = "DELETE FROM AgendamentoXServico WHERE AgXSeAgendamento = $isUpdate and AgXSeUnidade = $iUnidade";
			$conn->query($sql);

			$array = [];
			foreach($servicos as $servico){
				$iServico = $servico['iServico'];
				$iMedico = $servico['iMedico'];
				$data = $servico['data'];
				$hora = $servico['hora'];
				$iLocal = $servico['iLocal'];
	
				$sql = "INSERT INTO AgendamentoXServico(AgXSeAgendamento,AgXSeServico,AgXSeProfissional,
				AgXSeData,AgXSeHorario,AgXSeAtendimentoLocal,AgXSeUsuarioAtualizador,AgXSeUnidade)
				VALUES($isUpdate,$iServico,$iMedico,'$data','$hora',$iLocal,$usuarioId,$iUnidade)";
				array_push($array, $sql);
			}
	
			foreach($array as $sql){
				$conn->query($sql);
			}

			// vai limpar todos os dados da sessão utilizadas em agendamento após o cadastro
			$_SESSION['SERVICOS'] = [];
	
			echo json_encode([
				'titulo' => 'Atualizar agendamento',
				'status' => 'success',
				'menssagem' => 'Agendamento atualizado com sucesso!!!',
			]);
		}
	} elseif ($tipoRequest == 'EDITAR'){
		$iAgendamento = isset($_POST['iAgendamento'])?$_POST['iAgendamento']:'';

		$data = isset($_POST['data'])?$_POST['data']:'';
		$data = explode('/', $data)[0];


		$paciente = isset($_POST['paciente'])?$_POST['paciente']:'';
		$modalidade = isset($_POST['modalidade'])?$_POST['modalidade']:'';

		$responsavel = isset($_POST['responsavel'])?$_POST['responsavel']:'';
		$cmbSituacao = isset($_POST['cmbSituacao'])?$_POST['cmbSituacao']:'';
		$observacao = isset($_POST['observacao'])?$_POST['observacao']:'';
	
		$sql = "UPDATE Agendamento SET AgendDataRegistro = '$data',AgendCliente = $paciente,
		AgendModalidade = $modalidade,AgendClienteResponsavel = $responsavel,
		AgendObservacao = '$observacao',AgendSituacao = $cmbSituacao,AgendUsuarioAtualizador = $usuarioId
		WHERE AgendId = $iAgendamento and AgendUnidade = $iUnidade";

		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Editar agendamento',
			'tipo' => 'success',
			'menssagem' => 'Agendamento atualizado com sucesso!!!',
		]);
		irpara('agendamento.php');
	} elseif ($tipoRequest == 'EXCLUI'){
		$iAgendamento = $_POST['iAgendamento'];
	
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
	} elseif ($tipoRequest == 'ADDSERVICO'){
		if(!isset($_SESSION['SERVICOS'])){
			$_SESSION['SERVICOS'] = [];
		}
		$arrayServico = $_SESSION['SERVICOS'];

		$iServico = $_POST['servico'];
		$iMedico = $_POST['medico'];
		$sData = explode('/',$_POST['data']);
		$sData = $sData[2].'-'.$sData[1].'-'.$sData[0];
		$sHora = $_POST['hora'];
		$iLocal = $_POST['local'];

		// $sqlServico = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorCusto,SrVenUnidade
		// FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
		$sql = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorVenda,SrVenUnidade
		FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
		$resultServico = $conn->query($sql);
		$resultServico = $resultServico->fetch(PDO::FETCH_ASSOC);

		// $sqlMedico = "SELECT ProfiId,ProfiNome,ProfiCpf,ProfiSexo,ProfiEndereco,ProfiCelular,ProfiTelefone
		// FROM Profissional WHERE ProfiId = $iMedico and ProfiUnidade = $iUnidade";
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

		$valorTotal = 0;

		foreach($arrayServico as $item){
			$iServico = $item['iServico'];
			$iMedico = $item['iMedico'];
			$iLocal = $item['iLocal'];
			

			if($iServico == $resultServico['SrVenId'] && $iMedico == $resultMedico['ProfiId'] && $iLocal == $resultLocal['AtLocId']){
				echo json_encode([
					'status' => 'error',
					'titulo' => 'Duplicação de registro',
					'menssagem' => 'Já foi adicionado registro com o mesmo Médico, Procedimento e Local',
				]);
				exit;
			}
		}

		array_push($arrayServico, [
			'id' => "$resultServico[SrVenId]#$resultMedico[ProfiId]#$resultLocal[AtLocId]",
			'iServico' => $resultServico['SrVenId'],
			'iMedico' => $resultMedico['ProfiId'],
			'iLocal' => $resultLocal['AtLocId'],

			'servico' => $resultServico['SrVenNome'],
			'medico' => $resultMedico['ProfiNome'],
			'local' => $resultLocal['AtLocNome'],
			'sData' => mostraData($sData),
			'data' => $sData,
			'hora' => $sHora,
			'valor' => $resultServico['SrVenValorVenda'],
		]);

		// esse loop serve para ja calcular o valor total da venda
		foreach($arrayServico as $item){
			$valorTotal += $item['valor'];
		}

		$_SESSION['SERVICOS'] = $arrayServico;
		echo json_encode([
			'array' => $arrayServico,
			'valorTotal' => $valorTotal,
			'status' => 'success',
			'titulo' => 'Serviço',
			'menssagem' => 'Serviço adicionado!!!',
		]);
	} elseif ($tipoRequest == 'CHECKSERVICO'){
		$arrayServico = isset($_SESSION['SERVICOS'])?$_SESSION['SERVICOS']:[];

		if(isset($_POST['iAgendamento'])){
			$iAgendamento = $_POST['iAgendamento'];

			$sqlAgendamento = "SELECT AgXSeId,AgXSeAgendamento,AgXSeServico,AgXSeProfissional,AgXSeData,AgXSeHorario,
			AgXSeAtendimentoLocal,AgXSeUsuarioAtualizador,AgXSeUnidade, SrVenId, SrVenNome, SrVenValorVenda,
			ProfiId, ProfiNome, AtLocId, AtLocNome
			FROM AgendamentoXServico
			JOIN ServicoVenda ON SrVenId = AgXSeServico
			JOIN Profissional ON ProfiId = AgXSeProfissional
			JOIN AtendimentoLocal ON AtLocId = AgXSeAtendimentoLocal
			WHERE AgXSeAgendamento = $iAgendamento and AgXSeUnidade = $iUnidade";
			$resultAgendamento = $conn->query($sqlAgendamento);
			$rowAgendamento = $resultAgendamento->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($rowAgendamento as $item){
				array_push($arrayServico, [
					'id' => "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]",
					'iServico' => $item['SrVenId'],
					'iMedico' => $item['ProfiId'],
					'iLocal' => $item['AtLocId'],
			
					'servico' => $item['SrVenNome'],
					'medico' => $item['ProfiNome'],
					'local' => $item['AtLocNome'],
					'sData' => mostraData($item['AgXSeData']),
					'data' => $item['AgXSeData'],
					'hora' => $item['AgXSeHorario'],
					'valor' => $item['SrVenValorVenda'],
				]);
			}
		}
		$valorTotal = 0;

		foreach($arrayServico as $item){
			$valorTotal += $item['valor'];
		}
		$_SESSION['SERVICOS'] = $arrayServico;
		
		echo json_encode([
			'array' => $arrayServico,
			'valorTotal' => $valorTotal
		]);
	} elseif ($tipoRequest == 'EXCLUISERVICO'){
		$id = $_POST['id'];

		// idSeervico # idMedico # idLocal
		$id = explode('#',$id);

		if(!isset($_SESSION['SERVICOS'])){
			$_SESSION['SERVICOS'] = [];
		}
		$arrayServico = $_SESSION['SERVICOS'];

		foreach($arrayServico as $key => $item){
			$iServico = $item['iServico'];
			$iMedico = $item['iMedico'];
			$iLocal = $item['iLocal'];

			if($iServico == $id[0] && $iMedico == $id[1] && $iLocal == $id[2]){
				array_splice($arrayServico, $key, 1);
				$_SESSION['SERVICOS'] = $arrayServico;
				echo json_encode([
					'status' => 'success',
					'titulo' => 'Excluir serviço',
					'menssagem' => 'Serviço Excluído!!!',
				]);
				break;
			}
		}
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

		$sql = "SELECT PrAgeData, PrAgeHoraInicio, PrAgeHoraFim
		FROM ProfissionalAgenda
		WHERE PrAgeData = '$data' and PrAgeProfissional = $iMedico and PrAgeUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$arrayHora = [true,];
		foreach($row as $item){
			$horaI = explode(':', $item['PrAgeHoraInicio']);
			$horaF = explode(':', $item['PrAgeHoraFim']);
			
			array_push($arrayHora,
			[
				'from' => [intval($horaI[0]), intval($horaI[1])],
				'to' => [intval($horaF[0]), intval($horaF[1])],
			]);
		}

		echo json_encode([
			'arrayHora' => $arrayHora,
			'status' => 'success',
			'titulo' => 'Data',
			'menssagem' => 'Hora do profissional selecionado!!!',
		]);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'AGENDAMENTOS': $msg = 'Erro ao carregar agendamentos';break;
		case 'MUDARSITUACAO': $msg = 'Erro ao atualizar situação do agendamento';break;
		case 'ADDAGENDAMENTO': $msg = 'Erro ao incluir novo agendamento';break;
		case 'ATTAGENDAMENTO': $msg = 'Erro ao atualizar agendamento';break;
		case 'ADDSERVICO': $msg = 'Erro ao incluir novo serviço';break;
		case 'EDITAR': $msg = 'Erro ao atualizar agendamento';break;
		case 'EXCLUI': $msg = 'Erro ao excluir agendamento';break;
		case 'EXCLUISERVICO': $msg = 'Erro ao excluir serviço';break;
		case 'ADDPACIENTENOVO': $msg = 'Erro ao inserir novo paciente';break;
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
