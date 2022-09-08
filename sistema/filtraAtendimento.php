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
	if(!isset($_SESSION['atendimento'])){
		$_SESSION['atendimento'] = [
			'paciente' => '',
			'responsavel' => '',
			'atendimentoServicos' => []
		];
	}

	if($tipoRequest == 'ATENDIMENTOS'){
		$acesso = $_POST['acesso'];
		$array = [];

		if($acesso == 'ATENDIMENTO'){
			$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendHorario,AtModNome,
				AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,
				AgendObservacao,ClienNome,ClienCodigo,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,
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

			$sql = "SELECT AtendId,AtendNumRegistro,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtendClassificacao,
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
						$item['ClienCodigo'],  // Prontuário
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
				$att = "<a class='list-icons-item atualizaAtendimento' style='color: black' href='#' data-atendimento='$item[AtendId]'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				$exc = "<a class='list-icons-item excluiAtendimento' style='color: black' href='#' data-atendimento='$item[AtendId]'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
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
						$item['ClienCodigo'],  // Prontuário
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
				'status' => 'success',
				'menssagem' => 'Situação alterada com sucesso!!!'
			];
		} elseif($acesso == 'PROFISSIONAL'){
			$sql = "SELECT ProfiId, ProfiUsuario
				FROM Profissional
				WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$iProfissional = $row['ProfiId'];

			$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,
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
				WHERE SituaChave = 'EMESPERAVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
				ORDER BY AtXSeId DESC";
			$resultEspera = $conn->query($sql);
			$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,
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
				WHERE SituaChave = 'ATENDIDOVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
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
						$item['ClienCodigo'],  // Prontuário
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
						$item['ClienCodigo'],  // Prontuário
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
				'status' => 'success',
				'menssagem' => ''
			];
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
			'status' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!',
		]);
	} elseif ($tipoRequest == 'EXCLUI'){
		$iAtendimento = $_POST['iAtendimento'];

		$sql = "DELETE FROM AtendimentoAtestadoMedico WHERE AtAMeAtendimento = $iAtendimento
		and AtAMeUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM AtendimentoEletivo WHERE AtEleAtendimento = $iAtendimento
		and AtEleUnidade = $iUnidade";
		$conn->query($sql);
		
		$sql = "DELETE FROM AtendimentoReceituario WHERE AtRecAtendimento = $iAtendimento
		and AtRecUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM AtendimentoSolicitacaoExame WHERE AtSExAtendimento = $iAtendimento
		and AtSExUnidade = $iUnidade";
		$conn->query($sql);
		
		$sql = "DELETE FROM AtendimentoEncaminhamentoMedico WHERE AtEMeAtendimento = $iAtendimento
		and AtEMeUnidade = $iUnidade";
		$conn->query($sql);

	
		$sql = "DELETE FROM AtendimentoXServico WHERE AtXSeAtendimento = $iAtendimento
		and AtXSeUnidade = $iUnidade";
		$conn->query($sql);

		$sql = "DELETE FROM Atendimento WHERE AtendId = $iAtendimento";
		$conn->query($sql);

		echo json_encode([
			'titulo' => 'Excluir atendimento',
			'status' => 'success',
			'menssagem' => 'Atendimento excluido com sucesso!!!',
		]);
	} elseif($tipoRequest == 'SALVARPACIENTE'){
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
		echo json_encode([
			'titulo' => 'Paciente',
			'status' => 'success',
			'menssagem' => 'Paciente adicionado!!',
			'id' => $pacienteId
		]);
	} elseif($tipoRequest == 'SALVARRESPONSAVEL'){
		$pacienteId = $_POST['pacienteId'];
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

		echo json_encode([
			'titulo' => 'Responsável',
			'status' => 'success',
			'menssagem' => 'Responsável adicionado!!',
			'paciente' => $responsavelId
		]);
	} elseif($tipoRequest == 'SALVARATENDIMENTO'){
		$atendimentoServicos = $_SESSION['atendimento']['atendimentoServicos'];

		if(!COUNT($atendimentoServicos)){
			echo json_encode([
				'titulo' => 'Atendimento',
				'status' => 'error',
				'menssagem' => 'Atendimento deve ter ao menos 1(um) serviço!!'
			]);
			exit;
		}

		$atendimento = [
			'dataRegistro' => isset($_POST['dataRegistro'])?$_POST['dataRegistro']:'',
			'modalidade' => isset($_POST['modalidade'])?$_POST['modalidade']:'',
			'classificacao' => isset($_POST['classificacao'])?$_POST['classificacao']:'',
			'situacao' => isset($_POST['situacao'])?$_POST['situacao']:'',
			'observacao' => isset($_POST['observacaoAtendimento'])?$_POST['observacaoAtendimento']:''
		];
		$cliente = $_POST['cliente'];
		$responsavel = $_POST['responsavel'];

		if($cliente['id']){
			$mes = explode('-',$atendimento['dataRegistro']);
			$mes = $mes[1];
	
			$sql = "SELECT AtendNumRegistro FROM Atendimento WHERE AtendNumRegistro LIKE '%A$mes-%'
				ORDER BY AtendId DESC";
			$result = $conn->query($sql);
			$rowCodigo = $result->fetchAll(PDO::FETCH_ASSOC);
	
			$intaValCodigo = COUNT($rowCodigo)?intval(explode('-',$rowCodigo[0]['AtendNumRegistro'])[1])+1:1;
	
			$numRegistro = "A$mes-$intaValCodigo";
	
			$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'EMESPERAVENDA'";
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);
	
			$sql = "INSERT INTO Atendimento(AtendNumRegistro,AtendDataRegistro,AtendCliente,
				AtendModalidade,AtendResponsavel,AtendClassificacao,AtendObservacao,AtendSituacao,
				AtendUsuarioAtualizador,AtendUnidade)
				VALUES('$numRegistro','$atendimento[dataRegistro]','$cliente[id]','$atendimento[modalidade]',
				'".($responsavel?$responsavel['id']:'')."',$atendimento[classificacao],'$atendimento[observacao]',
			$rowSituacao[SituaId],$usuarioId,$iUnidade)";
			$result = $conn->query($sql);
	
			$atendimentoID = $conn->lastInsertId();
	
			if(COUNT($atendimentoServicos)){
				$sql = "INSERT INTO AtendimentoXServico(AtXSeAtendimento,AtXSeServico,AtXSeProfissional,AtXSeData,
				AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeUsuarioAtualizador,AtXSeUnidade)
				VALUES ";
		
				foreach($atendimentoServicos as $atendimentoServico){
					$sql .= "('$atendimentoID','$atendimentoServico[iServico]','$atendimentoServico[iMedico]',
					'$atendimentoServico[data]','$atendimentoServico[hora]','$atendimentoServico[iLocal]',
					'$atendimentoServico[valor]','$usuarioId','$iUnidade'),";
				}
				$sql = substr($sql, 0, -1);
				$conn->query($sql);
			}
	
			$sql = "UPDATE Cliente SET 
				ClienTipo= '$cliente[pessoaTipo]',
				ClienNome= '$cliente[nome]',
				ClienRazaoSocial= '$cliente[nome]',
				ClienCpf= '$cliente[cpf]',
				ClienRg= '$cliente[rg]',
				ClienOrgaoEmissor= '$cliente[emissor]',
				ClienUf= '$cliente[uf]',
				ClienSexo= '$cliente[sexo]',
				ClienDtNascimento= '$cliente[nascimento]',
				ClienNomePai= '$cliente[nomePai]',
				ClienNomeMae= '$cliente[nomeMae]',
				ClienProfissao= '$cliente[profissao]',
				ClienCep= '$cliente[cep]',
				ClienEndereco= '$cliente[endereco]',
				ClienNumero= '$cliente[numero]',
				ClienComplemento= '$cliente[complemento]',
				ClienBairro= '$cliente[bairro]',
				ClienCidade= '$cliente[cidade]',
				ClienEstado= '$cliente[estado]',
				ClienContato= '$cliente[contato]',
				ClienTelefone= '$cliente[telefone]',
				ClienCelular= '$cliente[celular]',
				ClienEmail= '$cliente[email]',
				ClienObservacao= '$cliente[observacao]',
				ClienUsuarioAtualizador= $usuarioId
				WHERE ClienId = $cliente[id]";
			$conn->query($sql);
	
			if($responsavel){
				$sql = "UPDATE ClienteResponsavel SET 
					ClResCliente='$cliente[id]',
					ClResNome='$responsavel[nomeResp]',
					CResParentesco='$responsavel[parentescoResp]',
					ClResNascimento='$responsavel[nascimentoResp]',
					ClResCep='$responsavel[cepResp]',
					ClResEndereco='$responsavel[enderecoResp]',
					ClResNumero='$responsavel[numeroResp]',
					ClResComplemento='$responsavel[complementoResp]',
					ClResBairro='$responsavel[bairroResp]',
					ClResCidade='$responsavel[cidadeResp]',
					ClResEstado='$responsavel[estadoResp]',
					ClResTelefone='$responsavel[telefoneResp]',
					ClResCelular='$responsavel[celularResp]',
					ClResEmail='$responsavel[emailResp]',
					ClResObservacao='$responsavel[observacaoResp]'
					WHERE ClResId = $responsavel[id]";
				$conn->query($sql);
			}
	
			$_SESSION['atendimento'] = [
				'paciente' => '',
				'responsavel' => '',
				'atendimentoServicos' => []
			];
	
			echo json_encode([
				'titulo' => 'Atendimento',
				'status' => 'success',
				'menssagem' => 'Atendimento cadastrado!!'
			]);
		}else{
			echo json_encode([
				'titulo' => 'Atendimento',
				'status' => 'error',
				'menssagem' => 'Erro ao cadastrar atendimento!!'
			]);
		}
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
			'status' => 'success',
			'menssagem' => ''
		]);
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
				'id' => $row['ClResId'],
				'nomeResp' => $row['ClResNome'],
				'parentescoResp' => $row['CResParentesco'],
				'nascimentoResp' => $row['ClResNascimento'],
				'cepResp' => $row['ClResCep'],
				'enderecoResp' => $row['ClResEndereco'],
				'numeroResp' => $row['ClResNumero'],
				'complementoResp' => $row['ClResComplemento'],
				'bairroResp' => $row['ClResBairro'],
				'cidadeResp' => $row['ClResCidade'],
				'estadoResp' => $row['ClResEstado'],
				'telefoneResp' => $row['ClResTelefone'],
				'celularResp' => $row['ClResCelular'],
				'emailResp' => $row['ClResEmail'],
				'observacaoResp' => $row['ClResObservacao']
			];
			$_SESSION['atendimento']['responsavel'] = $row['ClResId'];
		}
		
		echo json_encode([
			'data' => $array,
			'titulo' => 'Responsável',
			'status' => 'success',
			'menssagem' => 'Responsável selecionado!!'
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
	} elseif ($tipoRequest == 'PACIENTE'){
		$iPaciente = $_POST['iPaciente'];

		$sql = "SELECT ClienId,ClienTipo,ClienNome,ClienRazaoSocial,
		ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
		ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
		FROM Cliente WHERE ClienId = $iPaciente and ClienUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		$array = [];
		if($row){
			$array = [
				'status' => 'success',
				'tipoPessoa' => $row['ClienTipo'],
				// 'prontuario' => $item['ClienId'],
				'nome' => $row['ClienNome'],
				'cpf' => $row['ClienCpf'],
				// 'cns' => $row['ClienId'],
				'rg' => $row['ClienRg'],
				'emissor' => $row['ClienOrgaoEmissor'],
				'uf' => $row['ClienUf'],
				'sexo' => $row['ClienSexo'],
				'nascimento' => $row['ClienDtNascimento'],
				'nomePai' => $row['ClienNomePai'],
				'nomeMae' => $row['ClienNomeMae'],
				'profissao' => $row['ClienProfissao'],
				'cep' => $row['ClienCep'],
				'endereco' => $row['ClienEndereco'],
				'numero' => $row['ClienNumero'],
				'complemento' => $row['ClienComplemento'],
				'bairro' => $row['ClienBairro'],
				'cidade' => $row['ClienCidade'],
				'estado' => $row['ClienEstado'],
				'contato' => $row['ClienContato'],
				'telefone' => $row['ClienTelefone'],
				'celular' => $row['ClienCelular'],
				'email' => $row['ClienEmail'],
				'observacao' => $row['ClienObservacao']
			];
			$_SESSION['atendimento']['paciente'] = $row['ClienId'];
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
	} elseif($tipoRequest == 'ADICIONARSERVICO'){
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

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
		$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;

		echo json_encode([
			'array' => $atendimentoSessao,
			'valorTotal' => $valorTotal,
			'status' => 'success',
			'titulo' => 'Serviço',
			'menssagem' => 'Serviço adicionado!!!',
		]);
	} elseif ($tipoRequest == 'CHECKSERVICO'){
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

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
				WHERE AtXSeUnidade = $iUnidade and AtXSeAtendimento = $iAtendimento";
			$result = $conn->query($sql);
			$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);

			// esse loop duplo serve para evitar duplicações e evitar que os itens incluídos localmente não
			// desapareçam
			foreach($rowAtendimento as $item){
				if(COUNT($atendimentoSessao)){
					foreach($atendimentoSessao as $item2){
						if(($item2['id'] != "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]")){
							array_push($atendimentoSessao, [
								'id' => "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]",
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
				}else{
					array_push($atendimentoSessao, [
						'id' => "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]",
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
		}
		$valorTotal = 0;

		foreach($atendimentoSessao as $item){
			$valorTotal += $item['valor'];
		}
		$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;
		
		echo json_encode([
			'array' => $atendimentoSessao,
			'valorTotal' => $valorTotal
		]);
	} elseif ($tipoRequest == 'EXCLUISERVICO'){
		$oldId = $_POST['id']; // "SrVenId#ProfiId#AtLocId"
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

		foreach($atendimentoSessao as $key => $item){
			$iServico = $item['iServico'];
			$iMedico = $item['iMedico'];
			$iLocal = $item['iLocal'];

			$newId = "$iServico#$iMedico#$iLocal";

			if($newId == $oldId){
				array_splice($atendimentoSessao, $key, 1);
				$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;
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
		'status' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage(),
		'sql' => $sql
	]);
}
