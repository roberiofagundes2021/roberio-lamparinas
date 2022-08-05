<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Ordem de Compra';

include('global_assets/php/conexao.php');

$tipoRequest = $_POST['tipoRequest'];

// OBS.: Adicionar condicionais para trazer dados da unidade específica

/*
esse arquivo é único para atendimento, onde deve passar como parametro o campo "tipoRequest"
que irá indicar qual ação será executada
*/

try{
	$iUnidade = $_SESSION['UnidadeId'];
	$usuarioId = $_SESSION['UsuarId'];
	if(!isset($_SESSION['atendimentoNovo'])){
		$_SESSION['atendimentoNovo'] = [
			'paciente' => null,
			'responsavel' => null,
			'atendimento' => null,
			'servicos' => []
		];
	}

	if($tipoRequest == 'ATENDIMENTOS'){
		$acesso = $_POST['acesso'];
		$array = [];

		if($acesso == 'ATENDIMENTO'){
			$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendHorario,AtModNome,
			AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,
			AgendObservacao,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,
			SituaCor,ProfiNome,AtLocNome, SrVenNome
			FROM Agendamento
			JOIN AtendimentoModalidade ON AtModId = AgendModalidade
			JOIN Situacao ON SituaId = AgendSituacao
			JOIN Cliente ON ClienId = AgendCliente
			JOIN Profissional ON ProfiId = AgendProfissional
			JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
			JOIN ServicoVenda ON SrVenId = AgendServico
			WHERE AgendUnidade = $iUnidade";
			$result = $conn->query($sql);
			$rowAgendamento = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT AtendId,AtendNumRegistro,AtendDataRegistro,ClienNome,AtModNome,AtendClassificacao,
			AtendObservacao,AtendSituacao,AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeDesconto,
			ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,ProfiNome,SrVenNome
			FROM AtendimentoXServico
			JOIN Atendimento ON AtendId = AtXSeAtendimento
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN Profissional ON ProfiId = AtXSeProfissional
			JOIN ServicoVenda ON SrVenId = AtXSeServico
			JOIN AtendimentoLocal ON AtLocId = AtXSeAtendimentoLocal
			WHERE AtendUnidade = $iUnidade";
			$result = $conn->query($sql);
			$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);
			
			$dataAtendimento = [];
			$dataAgendamento = [];
			foreach($rowAgendamento as $item){
				$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				$exc = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							$exc
							<div class='dropdown'>													
								<a href='#' class='list-icons-item' data-toggle='dropdown'>
									<i class='icon-menu9'></i>
								</a>

								<div class='dropdown-menu dropdown-menu-right'>
									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Prioridade'></i> Prioridade</a>
									
									<div class='dropdown-divider'></div>

									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Gerar nota fiscal'></i> Gerar nota fiscal</a>
									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Anexos'></i> Anexos</a>
								</div>
							</div>
						</div>";
			
				$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
				
				$dataEspera = date('Y-m-d');
				$difference = diferencaEmHoras($dataEspera, $item['AgendData']);
				
				array_push($dataAgendamento, [
					'data' => [
						mostraData($item['AgendData']), // Data
						mostraHora($item['AgendHorario']), // Horario
						$difference,  // Espera
						'****',  // Nº Registro
						'****',  // Prontuário
						$item['ClienNome'],  // Paciente
						$item['ProfiNome'],  // Profissional
						$item['AtModNome'],  // Modalidade
						$item['SrVenNome'],  // Procedimento
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'iAgendamento' => $item['AgendId'],
						'sObservacao' => $item['AgendObservacao']
					]
				]);
			}
			foreach($rowAtendimento as $item){
				$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				$exc = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							$exc
							<div class='dropdown'>													
								<a href='#' class='list-icons-item' data-toggle='dropdown'>
									<i class='icon-menu9'></i>
								</a>

								<div class='dropdown-menu dropdown-menu-right'>
									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Prioridade'></i> Prioridade</a>
									
									<div class='dropdown-divider'></div>

									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Gerar nota fiscal'></i> Gerar nota fiscal</a>
									<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Anexos'></i> Anexos</a>
								</div>
							</div>
						</div>";
			
				$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
				
				$dataEspera = date('Y-m-d');
				$difference = diferencaEmHoras($dataEspera, $item['AtXSeData']);
				
				array_push($dataAtendimento, [
					'data' => [
						mostraData($item['AtXSeData']), // Data
						mostraHora($item['AtXSeHorario']), // Horario
						$difference,  // Espera
						$item['AtendNumRegistro'],  // Nº Registro
						'456456*',  // Prontuário
						$item['ClienNome'],  // Paciente
						$item['ProfiNome'],  // Profissional
						$item['AtModNome'],  // Modalidade
						$item['SrVenNome'],  // Procedimento
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'iAtendimento' => $item['AtendId'],
						'sObservacao' => $item['AtendObservacao']
					]
				]);
			}
			$array  = [
				'dataAgendamento' => $dataAgendamento,
				'dataAtendimento' => $dataAtendimento,
				'acesso' => $acesso,
				'titulo' => 'Alterar Situação',
				'tipo' => 'success',
				'menssagem' => 'Situação alterada com sucesso!!!'
			];
		} elseif($acesso == 'PROFISSIONAL'){
			$sql = "SELECT ProfiId, ProfiUsuario
			FROM Profissional
			WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$iProfissional = $row['ProfiId'];

			$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,AtModNome,AtClaChave,AtClaNome,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			WHERE SituaChave = 'EMESPERAVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = !$iUnidade
			ORDER BY AtXSeId DESC";
			$resultEspera = $conn->query($sql);
			$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,AtModNome,AtClaChave,AtClaNome,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			WHERE SituaChave = 'ATENDIDOVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = !$iUnidade
			ORDER BY AtXSeId DESC";
			$resultAtendido = $conn->query($sql);
			$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
			
			$espera = [];
			$atendido = [];

			foreach($rowEspera as $item){
				$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

				$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				// $exc = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							<div class='dropdown'>													
								<a href='#' class='list-icons-item' data-toggle='dropdown'>
									<i class='icon-menu9'></i>
								</a>

								<div class='dropdown-menu dropdown-menu-right'>
									<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
									
									<!-- <div class='dropdown-divider'></div> -->
								</div>
							</div>
						</div>";
			
				$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
				
				array_push($espera,[
					'data' => [
						mostraData($item['AtXSeData']),  // Data
						$item['AtXSeHorario'],  // Horario
						$difference,  // Espera
						$item['AtXSeId'],  // Nº Registro
						'2568*',  // Prontuário
						$item['ClienNome'],  // Paciente
						$item['SrVenNome'],  // Procedimento
						'Risco**',  // Risco
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'iAtendimento' => $item['AtendId'],
						'sObservacao' => $item['AtendObservacao']
					]]);
			}
			foreach($rowAtendido as $item){
				$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

				$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				// $exc = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							<div class='dropdown'>													
								<a href='#' class='list-icons-item' data-toggle='dropdown'>
									<i class='icon-menu9'></i>
								</a>

								<div class='dropdown-menu dropdown-menu-right'>
									<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
									
									<!-- <div class='dropdown-divider'></div> -->
								</div>
							</div>
						</div>";
			
				$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
				
				array_push($atendido,
				[
					'data' => [
						mostraData($item['AtXSeData']),  // Data
						$item['AtXSeHorario'],  // Horario
						$difference,  // Espera
						$item['AtXSeId'],  // Nº Registro
						'2568*',  // Prontuário
						$item['ClienNome'],  // Paciente
						$item['SrVenNome'],  // Procedimento
						'Risco**',  // Risco
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'iAtendimento' => $item['AtendId'],
						'sObservacao' => $item['AtendObservacao'],
						'AtClaChave' => $item['AtClaChave'],
						'AtClaNome' => $item['AtClaNome']
					]
				]);
			}
			$array = [
				'dataEspera' =>$espera,
				'dataAtendido' =>$atendido,
				'acesso' => $acesso,
				'titulo' => '',
				'tipo' => 'success',
				'menssagem' => ''
			];
		}
	
		echo json_encode($array);
	} elseif ($tipoRequest == 'SITUACOES'){
		$sql = "SELECT SituaId,SituaNome,SituaChave,SituaStatus,SituaUsuarioAtualizador,SituaCor
		FROM Situacao
		WHERE SituaChave in ('AGENDADOVENDA','ATENDIDOVENDA','EMESPERAVENDA','LIBERADOVENDA')";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
	
		echo json_encode([
			'data' => $row,
			'titulo' => '',
			'status' => 'success',
			'menssagem' => ''
		]);
	} elseif ($tipoRequest === 'MUDARSITUACAO'){
		$iAtendimento = $_POST['iAtendimento'];
		$situacao = isset($_POST['sSituacao'])?$_POST['sSituacao']:'';

		$sql = "SELECT SituaId
		FROM Situacao WHERE SituaChave = '$situacao'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$situacao = $row['SituaId'];
	
		$sql = "UPDATE Atendimento set AtendSituacao = $situacao
		WHERE AtendId = $iAtendimento";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Alterar Situação',
			'tipo' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!',
		]);
	} elseif ($tipoRequest == 'EXCLUI'){
		$iAtendimento = $_POST['iAtendimento'];
	
		$sql = "DELETE FROM Atendimento WHERE AtendId = $iAtendimento";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Excluir atendimento',
			'tipo' => 'success',
			'menssagem' => 'Atendimento excluido com sucesso!!!',
		]);
	} elseif($tipoRequest == 'SALVARPACIENTE'){
		$atendimentoSessao = $_SESSION['atendimentoNovo'];

		$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'ATIVO'";
		$result = $conn->query($sql);
		$rowStatus = $result->fetch(PDO::FETCH_ASSOC);

		$paciente = [
			'id' => 'NOVO',
			'pessoaTipo' => isset($_POST['pessoaTipo'])?$_POST['pessoaTipo']:'F',
			'prontuario' => isset($_POST['prontuario'])?$_POST['prontuario']:'null',
			'nome' => isset($_POST['nome'])?$_POST['nome']:'null',
			'cpf' => isset($_POST['cpf'])?$_POST['cpf']:'null',
			'rg' => isset($_POST['rg'])?$_POST['rg']:'null',
			'emissor' => isset($_POST['emissor'])?$_POST['emissor']:'null',
			'uf' => isset($_POST['uf'])?$_POST['uf']:'null',
			'sexo' => isset($_POST['sexo'])?$_POST['sexo']:'null',
			'nascimento' => isset($_POST['nascimento'])?$_POST['nascimento']:'null',
			'nomePai' => isset($_POST['nomePai'])?$_POST['nomePai']:'null',
			'nomeMae' => isset($_POST['nomeMae'])?$_POST['nomeMae']:'null',
			'profissao' => isset($_POST['profissao'])?$_POST['profissao']:'null',
			'cep' => isset($_POST['cep'])?$_POST['cep']:'null',
			'endereco' => isset($_POST['endereco'])?$_POST['endereco']:'null',
			'numero' => isset($_POST['numero'])?$_POST['numero']:'null',
			'complemento' => isset($_POST['complemento'])?$_POST['complemento']:'null',
			'bairro' => isset($_POST['bairro'])?$_POST['bairro']:'null',
			'cidade' => isset($_POST['cidade'])?$_POST['cidade']:'null',
			'estado' => isset($_POST['estado'])?$_POST['estado']:'null',
			'contato' => isset($_POST['contato'])?$_POST['contato']:'null',
			'telefone' => isset($_POST['telefone'])?$_POST['telefone']:'null',
			'celular' => isset($_POST['celular'])?$_POST['celular']:'null',
			'email' => isset($_POST['email'])?$_POST['email']:'null',
			'observacao' => isset($_POST['observacao'])?$_POST['observacao']:'null'
		];
		$sql = "INSERT INTO Cliente(ClienTipo,ClienNome,ClienRazaoSocial,
			ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
			ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienProfissao,ClienCep,ClienEndereco,
			ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
			ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade)
			VALUES('$paciente[pessoaTipo]','$paciente[nome]','$paciente[nome]','$paciente[cpf]','$paciente[rg]',
			'$paciente[emissor]','$paciente[uf]','$paciente[sexo]','$paciente[nascimento]','$paciente[nomePai]','$paciente[nomeMae]',
			'$paciente[profissao]','$paciente[cep]','$paciente[endereco]','$paciente[numero]','$paciente[complemento]',
			'$paciente[bairro]','$paciente[cidade]','$paciente[estado]','$paciente[contato]','$paciente[telefone]',
			'$paciente[celular]','$paciente[email]','$paciente[observacao]','$rowStatus[SituaId]','$usuarioId','$iUnidade')";
		$conn->query($sql);
		$pacienteId = $conn->lastInsertId();
			
		$_SESSION['atendimentoNovo']['paciente'] = $pacienteId;
		echo json_encode([
			'titulo' => 'Paciente',
			'tipo' => 'success',
			'menssagem' => 'Paciente adicionado!!',
			'id' => $pacienteId
		]);
	} elseif($tipoRequest == 'SALVARRESPONSAVEL'){
		$atendimentoSessao = $_SESSION['atendimentoNovo'];
		$pacienteId = $atendimentoSessao['paciente'];

		$responsavel = [
			'nomeResp' => isset($_POST['nomeResp'])?$_POST['nomeResp']: 'null',
			'parentescoResp' => isset($_POST['parentescoResp'])?$_POST['parentescoResp']: 'null',
			'nascimentoResp' => isset($_POST['nascimentoResp'])?$_POST['nascimentoResp']: 'null',
			'cepResp' => isset($_POST['cepResp'])?$_POST['cepResp']: 'null',
			'enderecoResp' => isset($_POST['enderecoResp'])?$_POST['enderecoResp']: 'null',
			'numeroResp' => isset($_POST['numeroResp'])?$_POST['numeroResp']: 'null',
			'complementoResp' => isset($_POST['complementoResp'])?$_POST['complementoResp']: 'null',
			'bairroResp' => isset($_POST['bairroResp'])?$_POST['bairroResp']: 'null',
			'cidadeResp' => isset($_POST['cidadeResp'])?$_POST['cidadeResp']: 'null',
			'estadoResp' => isset($_POST['estadoResp'])?$_POST['estadoResp']: 'null',
			'telefoneResp' => isset($_POST['telefoneResp'])?$_POST['telefoneResp']: 'null',
			'celularResp' => isset($_POST['celularResp'])?$_POST['celularResp']: 'null',
			'emailResp' => isset($_POST['emailResp'])?$_POST['emailResp']: 'null',
			'observacaoResp' => isset($_POST['observacaoResp'])?$_POST['observacaoResp']: 'null'
		];

		$sql = "INSERT INTO ClienteResponsavel(ClResCliente,ClResNome,CResParentesco,ClResNascimento,
			ClResCep,ClResEndereco,ClResNumero,ClResComplemento,ClResBairro,ClResCidade,ClResEstado,
			ClResTelefone,ClResCelular,ClResEmail,ClResObservacao)
			VALUES('$pacienteId', '$responsavel[nomeResp]','$responsavel[parentescoResp]','$responsavel[nascimentoResp]',
			'$responsavel[cepResp]','$responsavel[enderecoResp]','$responsavel[numeroResp]','$responsavel[complementoResp]',
			'$responsavel[bairroResp]','$responsavel[cidadeResp]','$responsavel[estadoResp]','$responsavel[telefoneResp]',
			'$responsavel[celularResp]','$responsavel[emailResp]','$responsavel[observacaoResp]')";
		$conn->query($sql);
		$responsavelId = $conn->lastInsertId();

		$atendimentoSessao['responsavel'] = $responsavelId;
		$_SESSION['atendimentoNovo'] = $atendimentoSessao;

		echo json_encode([
			'titulo' => 'Responsável',
			'tipo' => 'success',
			'menssagem' => 'Responsável adicionado!!',
			'paciente' => $responsavelId
		]);
	} elseif($tipoRequest == 'SALVARATENDIMENTO'){
		$atendimentoSessao = $_SESSION['atendimentoNovo'];

		$atendimento = [
			'dataRegistro' => isset($_POST['dataRegistro'])?$_POST['dataRegistro']:'null',
			'modalidade' => isset($_POST['modalidade'])?$_POST['modalidade']:'null',
			'classificacao' => isset($_POST['classificacao'])?$_POST['classificacao']:'null',
			'observacao' => isset($_POST['observacaoAtendimento'])?$_POST['observacaoAtendimento']:'null'
		];
		$atendimentoSessao['atendimento'] = $atendimento;
		$_SESSION['atendimentoNovo'] = $atendimentoSessao;
		$pacienteId = false;
		$responsavelId = false;

		// caso o paciente seja novo irá inseri=lo no banco
		if($atendimentoSessao['paciente']['id'] == 'NOVO'){
			$paciente = $atendimentoSessao['paciente'];

			$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'ATIVO'";
			$result = $conn->query($sql);
			$rowStatus = $result->fetch(PDO::FETCH_ASSOC);


			// pegar código de cliente, onde fica o CNPJ? ClienInscricaoMunicipal? ClienInscricaoEstadual?
			// ClienCartaoSus?

			// (ClienTipo,ClienCodigo,ClienNome,ClienRazaoSocial,ClienCnpj,
			// ClienInscricaoMunicipal,ClienInscricaoEstadual,ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
			// ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienCartaoSus,ClienProfissao,ClienCep,ClienEndereco,
			// ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
			// ClienEmail,ClienSite,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade)

			$sql = "INSERT INTO Cliente(ClienTipo,ClienNome,ClienRazaoSocial,
			ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
			ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienProfissao,ClienCep,ClienEndereco,
			ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
			ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade)
			VALUES($paciente[pessoaTipo],$paciente[nome],$paciente[nome],$paciente[cpf],$paciente[rg],
			$paciente[emissor],$paciente[uf],$paciente[sexo],$paciente[nascimento],$paciente[nomePai],$paciente[nomeMae],
			$paciente[profissao],$paciente[cep],$paciente[endereco],$paciente[numero],$paciente[complemento],
			$paciente[bairro],$paciente[cidade],$paciente[estado],$paciente[contato],$paciente[telefone],
			$paciente[celular],$paciente[email],$paciente[observacao],$rowStatus[SituaId],$usuarioId,$iUnidade)";
			$conn->query($sql);
			$pacienteId = $conn->lastInsertId();
		} else {
			$pacienteId = $atendimentoSessao['paciente']['id'];
		}

		// caso haja um responsável e ele for novo, será inserido no banco
		if($atendimentoSessao['responsavel']){
			$responsavel = $atendimentoSessao['responsavel'];
			if($atendimentoSessao['responsavel']['id'] == 'NOVO'){
				$sql = "INSERT INTO ClienteResponsavel(ClResCliente,ClResNome,CResParentesco,ClResNascimento,
				ClResCep,ClResEndereco,ClResNumero,ClResComplemento,ClResBairro,ClResCidade,ClResEstado,
				ClResTelefone,ClResCelular,ClResEmail,ClResObservacao)
				VALUES($pacienteId, $responsavel[nomeResp],$responsavel[parentescoResp],$responsavel[nascimentoResp],
				$responsavel[cepResp],$responsavel[enderecoResp],$responsavel[numeroResp],$responsavel[complementoResp],
				$responsavel[bairroResp],$responsavel[cidadeResp],$responsavel[estadoResp],$responsavel[telefoneResp],
				$responsavel[celularResp],$responsavel[emailResp],$responsavel[observacaoResp])";
				$conn->query($sql);
				$responsavelId = $conn->lastInsertId();
			}

			$responsavel = [
				'bairroResp' => isset($_POST['bairroResp'])?$_POST['bairroResp']: 'null',
				'cidadeResp' => isset($_POST['cidadeResp'])?$_POST['cidadeResp']: 'null',
				'estadoResp' => isset($_POST['estadoResp'])?$_POST['estadoResp']: 'null',
				'telefoneResp' => isset($_POST['telefoneResp'])?$_POST['telefoneResp']: 'null',
				'celularResp' => isset($_POST['celularResp'])?$_POST['celularResp']: 'null',
				'emailResp' => isset($_POST['emailResp'])?$_POST['emailResp']: 'null',
				'observacaoResp' => isset($_POST['observacaoResp'])?$_POST['observacaoResp']: 'null'
			];
		}

		echo json_encode([
			'titulo' => 'Atendimento',
			'tipo' => 'success',
			'menssagem' => 'Atendimento cadastrado!!',
			'atendimento' => $atendimentoSessao
		]);
	} elseif($tipoRequest == 'RESPONSAVEIS'){

		$sql = "SELECT ClResId,ClResCliente,ClResNome,CResParentesco,ClResNascimento,ClResCep,ClResEndereco,
		ClResNumero,ClResComplemento,ClResBairro,ClResCidade,ClResEstado,ClResTelefone,ClResCelular,ClResEmail,
		ClResObservacao
		FROM ClienteResponsavel";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($row as $item){
			array_push($array, [
				'id' => $item['ClResId'],
				'nome' => $item['ClResNome']
			]);
		}
		
		echo json_encode([
			'data' => $array,
			'titulo' => '',
			'tipo' => 'success',
			'menssagem' => ''
		]);
	} elseif ($tipoRequest == 'PACIENTES'){

		$sql = "SELECT ClienId,ClienNome
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
		$sCodigo = null;

		try{
			$sql = "SELECT COUNT(isnull(clienCodigo,0)) as Codigo
					FROM Cliente
					Where ClienUnidade = $iUnidade";
			//echo $sql;die;
			$result = $conn->query("$sql");
			$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);	
			
			$sCodigo = (int)$rowCodigo['Codigo'] + 1;
			$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);
		} catch(PDOException $e) {	
			echo 'Error1: ' . $e->getMessage();die;
		}
	
		// insere o novo usuário no banco
		$sql = "INSERT INTO  Cliente(clienCodigo,ClienNome,ClienTelefone,ClienCelular,ClienEmail,ClienObservacao,
		ClienTipo,ClienStatus,ClienUnidade,ClienUsuarioAtualizador)
		VALUES ('$sCodigo','$nomePaciente','$telefone','$celular','$email','$observacao','F',1,$iUnidade,$usuarioId)";
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
	} elseif($tipoRequest == 'RESPONSAVEL'){
		$iResponsavel = $_POST['iResponsavel'];
		$array = [];

		$sql = "SELECT ClResId,ClResCliente,ClResNome,CResParentesco,ClResNascimento,ClResCep,ClResEndereco,
		ClResNumero,ClResComplemento,ClResBairro,ClResCidade,ClResEstado,ClResTelefone,ClResCelular,ClResEmail,
		ClResObservacao
		FROM ClienteResponsavel WHERE ClResId = $iResponsavel";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		if($row){
			$array = [
				'id' => $item['ClResId'],
				'nomeResp' => $item['ClResNome'],
				'parentescoResp' => $item['CResParentesco'],
				'nascimentoResp' => $item['ClResNascimento'],
				'cepResp' => $item['ClResCep'],
				'enderecoResp' => $item['ClResEndereco'],
				'numeroResp' => $item['ClResNumero'],
				'complementoResp' => $item['ClResComplemento'],
				'bairroResp' => $item['ClResBairro'],
				'cidadeResp' => $item['ClResCidade'],
				'estadoResp' => $item['ClResEstado'],
				'telefoneResp' => $item['ClResTelefone'],
				'celularResp' => $item['ClResCelular'],
				'emailResp' => $item['ClResEmail'],
				'observacaoResp' => $item['ClResObservacao']
			];
		}
		
		echo json_encode([
			'data' => $array,
			'titulo' => 'Responsável',
			'tipo' => 'success',
			'menssagem' => 'Responsável selecionado!!'
		]);
	} elseif ($tipoRequest == 'SERVICOS'){
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
	} elseif ($tipoRequest == 'MEDICOS'){
		$sql = "SELECT ProfiId,ProfiNome
		FROM Profissional WHERE ProfiUnidade != $iUnidade";
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
	} elseif($tipoRequest == 'ADICIONARSERVICO'){
		$atendimentoSessao = $_SESSION['atendimentoNovo']['servicos'];

		$iServico = $_POST['servico'];
		$iMedico = $_POST['medicos'];
		$sData = explode('/',$_POST['dataAtendimento']);
		$sData = $sData[2].'-'.$sData[1].'-'.$sData[0];
		$sHora = $_POST['horaAtendimento'];
		$iLocal = $_POST['localAtendimento'];
		
		// $sqlServico = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorCusto,SrVenUnidade
		// FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
		$sql = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorVenda,SrVenUnidade
		FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
		$resultServico = $conn->query($sql);
		$resultServico = $resultServico->fetch(PDO::FETCH_ASSOC);

		// $sqlMedico = "SELECT ProfiId,ProfiNome,ProfiCpf,ProfiSexo,ProfiEndereco,ProfiCelular,ProfiTelefone
		// FROM Profissional WHERE ProfiId = $iMedico and ProfiUnidade = $iUnidade";
		$sql = "SELECT ProfiId,ProfiNome,ProfiCpf,ProfiSexo,ProfiEndereco,ProfiCelular,ProfiTelefone
		FROM Profissional WHERE ProfiId = $iMedico and ProfiUnidade != $iUnidade";
		$resultMedico = $conn->query($sql);
		$resultMedico = $resultMedico->fetch(PDO::FETCH_ASSOC);

		// $sqlLocal = "SELECT AtLocId,AtLocNome
		// FROM AtendimentoLocal WHERE AtLocId = $iLocal and AtLocUnidade = $iUnidade";
		$sql = "SELECT AtLocId,AtLocNome
		FROM AtendimentoLocal WHERE AtLocId = $iLocal and AtLocUnidade = $iUnidade";
		$resultLocal = $conn->query($sql);
		$resultLocal = $resultLocal->fetch(PDO::FETCH_ASSOC);

		$valorTotal = 0;
		foreach($atendimentoSessao as $item){
			if($item['iMedico'] == $iMedico && $item['data'] == $sData && $item['hora'] == $sHora){
				echo json_encode([
					'status' => 'error',
					'titulo' => 'Duplicação de registro',
					'menssagem' => 'Já foi adicionado registro com o mesmo Médico, Data e Hora',
					'array' => $atendimentoSessao,
				]);
				exit;
			}
		}

		array_push($atendimentoSessao, [
			'id' => "$resultServico[SrVenId]#$resultMedico[ProfiId]#$resultLocal[AtLocId]",
			'iServico' => $resultServico['SrVenId'],
			'iMedico' => $resultMedico['ProfiId'],
			'iLocal' => $resultLocal['AtLocId'],

			'servico' => $resultServico['SrVenNome'],
			'medico' => $resultMedico['ProfiNome'],
			'local' => $resultLocal['AtLocNome'],
			'sData' => mostraData($sData),
			'data' => $sData,
			'hora' => mostraHora($sHora),
			'valor' => $resultServico['SrVenValorVenda'],
		]);
		$_SESSION['atendimentoNovo']['servicos'] = $atendimentoSessao;

		echo json_encode([
			'array' => $atendimentoSessao,
			'valorTotal' => $valorTotal,
			'status' => 'success',
			'titulo' => 'Serviço',
			'menssagem' => 'Serviço adicionado!!!',
		]);
	} elseif ($tipoRequest == 'CHECKSERVICO'){
		$atendimentoSessao = $_SESSION['atendimentoNovo']['servicos'];

		if(isset($_POST['iAtendimento']) && $_POST['iAtendimento']){
			$iAtendimento = $_POST['iAtendimento'];
			
			$sql = "SELECT AtXSeId,AtXSeAtendimento,AtXSeServico,AtXSeProfissional,AtXSeData,AtXSeHorario,
			AtXSeAtendimentoLocal,AtXSeValor,AtXSeDesconto,AtXSeUsuarioAtualizador,AtXSeUnidade,
			ProfiId,AtLocId,AtLocNome,AtModNome,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,
			SituaCor,ProfiNome,SrVenNome,SrVenValorVenda,SrVenId
			FROM AtendimentoXServico
			JOIN Atendimento ON AtendId = AtXSeAtendimento
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN Profissional ON ProfiId = AtXSeProfissional
			JOIN AtendimentoLocal ON AtLocId = AtXSeAtendimentoLocal
			JOIN ServicoVenda ON SrVenId = AtXSeServico
			WHERE AtXSeAtendimento = $iAtendimento and AtXSeUnidade != $iUnidade";
			$result = $conn->query($sql);
			$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($rowAtendimento as $item){
				array_push($atendimentoSessao, [
					'id' => "$item[AtXSeId]#$item[ProfiId]#$item[AtLocId]",
					'iServico' => $item['SrVenId'],
					'iMedico' => $item['ProfiId'],
					'iLocal' => $item['AtLocId'],
			
					'servico' => $item['SrVenNome'],
					'medico' => $item['ProfiNome'],
					'local' => $item['AtLocNome'],
					'sData' => mostraData($item['AtXSeData']),
					'data' => $item['AtXSeData'],
					'hora' => mostraHora($item['AtXSeHorario']),
					'valor' => $item['SrVenValorVenda'],
				]);
			}
		}
		$valorTotal = 0;

		foreach($atendimentoSessao as $item){
			$valorTotal += $item['valor'];
		}
		$_SESSION['atendimentoNovo']['servicos'] = $atendimentoSessao;
		
		echo json_encode([
			'array' => $atendimentoSessao,
			'valorTotal' => $valorTotal
		]);
	} elseif ($tipoRequest == 'EXCLUISERVICO'){
		$oldId = $_POST['id']; // "id#id#id"

		$atendimentoSessao = $_SESSION['atendimentoNovo']['servicos'];

		foreach($atendimentoSessao as $key => $item){
			$iServico = $item['iServico'];
			$iMedico = $item['iMedico'];
			$iLocal = $item['iLocal'];

			$newId = "$iServico#$iMedico#$iLocal";

			if($newId == $oldId){
				array_splice($atendimentoSessao, $key, 1);
				$_SESSION['atendimentoNovo']['servicos'] = $atendimentoSessao;
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
		case 'MUDARSITUACAO': $msg = 'Erro ao atualizar situação do atendimentos!!';break;
		case 'EXCLUI': $msg = 'Erro ao excluir atendimento!!';break;
		case 'SALVARPACIENTE': $msg = 'Erro ao salvar paciente!!';break;
		case 'RESPONSAVEL': $msg = 'Erro ao buscar responsável!!';break;
		case 'SALVARRESPONSAVEL': $msg = 'Erro ao salvar responsável!!';break;
		default: $msg = 'Erro ao executar ação!!';break;
	}
	echo json_encode([
		'titulo' => 'Atendimento',
		'tipo' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage(),
		'sql' => $sql
	]);
}
