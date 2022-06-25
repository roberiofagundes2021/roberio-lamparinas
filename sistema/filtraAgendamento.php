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
	if($tipoRequest == 'AGENDAMENTOS'){
		$sql = "SELECT AtendId,AtendDataRegistro,ClienNome,AtModNome,AtendResponsavel,AtendClassificacao,
		AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor
		FROM Atendimento
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
		JOIN Cliente ON ClienId = AtendCliente";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		foreach($row as $item){
			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$exc = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
			$acoes = "<div class='list-icons'>
						${att}
						${exc}
					</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			array_push($array, [
				'data' => [
					mostraData($item['AtendDataRegistro']),
					'14:00**',
					$item['ClienNome'],
					$item['AtendResponsavel'],
					'Procedimento**',
					$item['AtModNome'],
					$contato,
					"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",
					$acoes
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAgendamento' => $item['AtendId']
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
	
		echo json_encode($row);
	} elseif ($tipoRequest === 'MUDARSITUACAO'){
		$iAtendimento = $_POST['iAtendimento'];
		$iSituacao = $_POST['iSituacao'];
	
		$sql = "UPDATE Agendamento set AgendSituacao = $iSituacao WHERE AgendId = $iAtendimento";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Alterar Situação',
			'tipo' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!',
		]);
	} elseif ($tipoRequest === 'PACIENTES'){
	
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
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest === 'MODALIDADES'){
	
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
	} elseif ($tipoRequest === 'SERVICOS'){
		$sql = "SELECT ServiId,ServiCodigo,ServiNome,ServiDetalhamento,ServiCategoria,ServiSubCategoria,
		ServiValorCusto,ServiOutrasDespesas,ServiCustoFinal,ServiMargemLucro,ServiValorVenda,ServiFabricante,
		ServiMarca,ServiModelo,ServiNumSerie,ServiStatus,ServiEmpresa,ServiUsuarioAtualizador,ServiUnidade
		FROM Servico";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ServiId'],
				'nome' => $item['ServiNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest === 'MEDICOS'){
		$sql = "SELECT ProfiId,ProfiTipo,ProfiCodigo,ProfiNome,ProfiRazaoSocial,ProfiCnpj,
		ProfiInscricaoMunicipal,ProfiInscricaoEstadual,ProfiCpf,ProfiRg,ProfiOrgaoEmissor,ProfiUf,ProfiSexo,
		ProfiDtNascimento,ProfiProfissao,ProfiNumConselho,ProfiCNES,ProfiEspecialidade,ProfiCep,ProfiEndereco,
		ProfiNumero,ProfiComplemento,ProfiBairro,ProfiCidade,ProfiEstado,ProfiContato,ProfiTelefone,
		ProfiCelular,ProfiEmail,ProfiSite,ProfiObservacao,ProfiBanco,ProfiAgencia,ProfiConta,
		ProfiInformacaoAdicional,ProfiStatus,ProfiUsuarioAtualizador,ProfiUnidade
		FROM Profissional";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ProfiId'],
				'nome' => $item['ProfiNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest === 'LOCALATENDIMENTO'){
		$sql = "SELECT AtLocId,AtLocNome,AtLocStatus,AtLocUsuarioAtualizador,AtLocUnidade
		FROM AtendimentoLocal";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['AtLocId'],
				'nome' => $item['AtLocNome'],
			]);
		}

		echo json_encode($array);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'AGENDAMENTOS': $msg = 'Erro ao carregar agendamentos';break;
		case 'SITUACOES': $msg = 'Erro ao carregar situações';break;
		case 'MUDARSITUACAO': $msg = 'Erro ao atualizar situação do agendamento';break;
		default: $msg = 'Erro ao executar ação';break;
	}
	echo json_encode([
		'titulo' => 'Agendamento',
		'tipo' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage()
	]);
}
