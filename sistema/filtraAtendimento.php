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
	
 	 // feito consultas para buscar a profissão do profissional do atendimento
	$sql = "SELECT Profissao.ProfiNome as ProfissaoNome
			FROM Profissional
			JOIN Profissao ON Profissao.ProfiId = Profissional.ProfiProfissao
			WHERE ProfiUsuario = $usuarioId";
	$result = $conn->query($sql);
	$rowProfissao = $result->fetch(PDO::FETCH_ASSOC);

	// feito consultas para buscar de acordo com a classificação do atendimento
	// (ATENDIMENTOSAMBULATORIAIS, ATENDIMENTOSHOSPITALARES, ATENDIMENTOSELETIVOS)
	if($tipoRequest == 'ATENDIMENTOS'){
		$acesso = $_POST['acesso'];
		$array = [];
		$hoje = date('Y-m-d');

		if($acesso == 'ATENDIMENTO'){
			$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendHorario,AtModNome,
				AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,
				AgendObservacao,AgendJustificativa,ClienNome,ClienCodigo,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave, ClienDtNascimento,
				SituaCor,Profissional.ProfiNome as ProfissionalNome,AtLocNome, SrVenNome, ProfiCbo, Profissao.ProfiNome as ProfissaoNome
				FROM Agendamento
				JOIN AtendimentoModalidade ON AtModId = AgendModalidade
				JOIN Situacao ON SituaId = AgendSituacao
				JOIN Cliente ON ClienId = AgendCliente
				JOIN Profissional ON Profissional.ProfiId = AgendProfissional
				JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
				JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
				JOIN ServicoVenda ON SrVenId = AgendServico
				WHERE AgendUnidade = $iUnidade and SituaChave in ('AGENDADOVENDA','CONFIRMADO','FILAESPERA')
				and AgendAtendimento is null";
			$result = $conn->query($sql);
			$rowAgendamento = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT AtendId,AtendNumRegistro,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtendClassificacao,
				AtendObservacao,AtendJustificativa,AtendSituacao,AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeDesconto, ClienDtNascimento,
				ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,Profissional.ProfiNome as ProfissionalNome,SrVenNome, 
				Profissao.ProfiNome as ProfissaoNome, ProfiCbo,AtClRNome,AtClRNomePersonalizado,AtClRTempo,AtClRCor,AtClRDeterminantes
				FROM AtendimentoXServico
				LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
				LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
				LEFT JOIN Situacao ON SituaId = AtendSituacao
				LEFT JOIN Cliente ON ClienId = AtendCliente
				LEFT JOIN Profissional ON Profissional.ProfiId = AtXSeProfissional
				LEFT JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
				LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
				LEFT JOIN AtendimentoLocal ON AtLocId = AtXSeAtendimentoLocal
				LEFT JOIN AtendimentoClassificacaoRisco ON AtendClassificacaoRisco = AtClRId
				WHERE AtendUnidade = $iUnidade";
			$result = $conn->query($sql);
			$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);
			
			$dataAtendimento = [];
			$dataAgendamento = [];
			foreach($rowAgendamento as $item){
				$att = "<a style='color: black' href='#' data-tipo='AGENDAMENTO' onclick='atualizaAtendimento(this)' class='list-icons-item' data-agendamento='$item[AgendId]'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				$exc = "<a style='color: black' href='#'  data-tipo='AGENDAMENTO' onclick='excluiAtendimento(this)' class='list-icons-item' data-agendamento='$item[AgendId]'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$aud = "<a style='color: black' href='#'  data-tipo='AGENDAMENTO' onclick='auditoria(this)' class='list-icons-item' data-id='$item[AgendId]'><i class='icon-eye4' title='Auditoria'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							$exc
							$aud
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
						mostraData($item['AgendData']) . " - " . mostraHora($item['AgendHorario']), // Data - Hora
						$difference,  // Espera
						$item['ClienNome'],  // Paciente
						calculaIdadeSimples($item['ClienDtNascimento']), // Idade Paciente
						$item['ProfissionalNome'],  // Profissional
						$item['AtModNome'],  // Modalidade
						$item['SrVenNome'],  // Procedimento
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'id' => $item['AgendId'],
						'sJustificativa' => $item['AgendJustificativa'],
						'prontuario' => 'Prontuário: '.($item['ClienCodigo']?$item['ClienCodigo']:'NaN'),
						'cbo' => 'CBO: '.($item['ProfiCbo']?$item['ProfiCbo']:'NaN'),
					]
				]);
			}
			foreach($rowAtendimento as $item){
				$att = "<a class='list-icons-item' href='#' data-tipo='ATENDIMENTO' onclick='atualizaAtendimento(this)' style='color: black' data-atendimento='$item[AtendId]'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
				$exc = "<a class='list-icons-item' href='#' data-tipo='ATENDIMENTO' onclick='excluiAtendimento(this)' style='color: black' data-atendimento='$item[AtendId]'><i class='icon-bin' title='Excluir Atendimento'></i></a>";
				$aud = "<a style='color: black' href='#' data-tipo='ATENDIMENTO' onclick='auditoria(this)' class='list-icons-item' data-id='$item[AtendId]'><i class='icon-eye4' title='Auditoria'></i></a>";
				$acoes = "<div class='list-icons'>
							$att
							$exc
							$aud
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
						mostraData($item['AtXSeData']) . " - " . mostraHora($item['AtXSeHorario']), // Data - Hora
						$difference,  // Espera
						$item['AtendNumRegistro'],  // Nº Registro
						$item['ClienNome'],  // Paciente
						calculaIdadeSimples($item['ClienDtNascimento']), // Idade paciente
						$item['ProfissionalNome'],  // Profissional
						$item['AtModNome'],  // Modalidade
						$item['SrVenNome'],  // Procedimento
						"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
						$acoes,  // Ações
					],
					'identify' => [
						'situacao' => $item['SituaChave'],
						'id' => $item['AtendId'],
						'sJustificativa' => $item['AtendJustificativa'],
						'prontuario' => 'Prontuário: '.($item['ClienCodigo']?$item['ClienCodigo']:'NaN'),
						'cbo' => 'CBO: '.($item['ProfiCbo']?$item['ProfiCbo']:'NaN'),
						'class' => $item['AtClRNomePersonalizado']?'Classificação - '.$item['AtClRNomePersonalizado']:($item['AtClRNome']?'Classificação - '.$item['AtClRNome']:'Sem Classificação!'),
						'classTemp' => ($item['AtClRTempo']?$item['AtClRTempo']:''),
						'classCor' => ($item['AtClRCor']?$item['AtClRCor']:'#FFF'),
						'classDeterminante' => ($item['AtClRDeterminantes']?$item['AtClRDeterminantes']:'')
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
									<div class='dropdown-divider'></div>
									<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
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
						'id' => $item['AtendId'],
						'sJustificativa' => $item['AtendObservacao']
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
									<div class='dropdown-divider'></div>
									<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
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
						'id' => $item['AtendId'],
						'sJustificativa' => $item['AtendObservacao'],
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
	} elseif($tipoRequest == 'ATENDIMENTOSAMBULATORIAIS'){
		$acesso = $_POST['acesso'];
		$array = [];

		$sql = "SELECT ProfiId, ProfiUsuario
				FROM Profissional
				WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iProfissional = $row['ProfiId'];

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			AND AtClaChave = 'AMBULATORIAL'
			ORDER BY AtXSeId DESC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'AMBULATORIAL'
			ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmAtendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			WHERE SituaChave = 'EMOBSERVACAO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'AMBULATORIAL'
			ORDER BY AtXSeId DESC";
		$resultObservacao = $conn->query($sql);
		$rowObservacao = $resultObservacao->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			AND AtClaChave = 'AMBULATORIAL'
			ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];
		$observacao = [];

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($espera,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($emAtendimento,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowObservacao as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($observacao,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($atendido,
			[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'],  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'AtClaChave' => $item['AtClaChave'],
					'AtClaNome' => $item['AtClaNome'],
					'prontuario' => $item['ClienCodigo']
				]
			]);
		}
		$array = [
			'dataEspera' =>$espera,
			'dataAtendido' =>$atendido,
			'dataEmAtendimento' => $emAtendimento,
			'dataObservacao' => $observacao,
			'acesso' => $acesso,
			'titulo' => '',
			'status' => 'success',
			'menssagem' => ''
		];
	
		echo json_encode($array);
	} elseif($tipoRequest == 'ATENDIMENTOSHOSPITALARES'){
		$acesso = $_POST['acesso'];
		$array = [];

		$sql = "SELECT ProfiId, ProfiUsuario
				FROM Profissional
				WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iProfissional = $row['ProfiId'];

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			AND AtClaChave = 'INTERNACAO'
			ORDER BY AtXSeId DESC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'INTERNACAO'
			ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmATendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			WHERE SituaChave = 'EMOBSERVACAO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'INTERNACAO'
			ORDER BY AtXSeId DESC";
		$resultObservacao = $conn->query($sql);
		$rowObservacao = $resultObservacao->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
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
			AND AtClaChave = 'INTERNACAO'
			ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];
		$observacao = [];

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($espera,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($emAtendimento,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowObservacao as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($observacao,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($atendido,
			[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'],  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					'Risco**',  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'AtClaChave' => $item['AtClaChave'],
					'AtClaNome' => $item['AtClaNome'],
					'prontuario' => $item['ClienCodigo']
				]
			]);
		}
		$array = [
			'dataEspera' =>$espera,
			'dataAtendido' =>$atendido,
			'dataEmAtendimento' => $emAtendimento,
			'dataObservacao' => $observacao,
			'acesso' => $acesso,
			'titulo' => '',
			'status' => 'success',
			'menssagem' => ''
		];
	
		echo json_encode($array);
	} elseif($tipoRequest == 'ATENDIMENTOSELETIVOS'){
		$acesso = $_POST['acesso'];
		$array = [];

		$sql = "SELECT ProfiId, ProfiUsuario
				FROM Profissional
				WHERE ProfiUsuario = $usuarioId and ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$iProfissional = $row['ProfiId'];

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda, AtClRCor
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMESPERAVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'ELETIVO'
			ORDER BY AtXSeId DESC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda, AtClRCor
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'ATENDIDOVENDA' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'ELETIVO'
			ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda, AtClRCor
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'ELETIVO'
			ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmAtendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SrVenValorVenda, AtClRCor
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMOBSERVACAO' AND AtXSeProfissional = $iProfissional AND AtXSeUnidade = $iUnidade
			AND AtClaChave = 'ELETIVO'
			ORDER BY AtXSeId DESC";
		$resultObservacao = $conn->query($sql);
		$rowObservacao = $resultObservacao->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];
		$observacao = [];

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
				
			/* -- Deixar um de backup caso precise voltar
			$acoes = "<div class='list-icons'>
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>";
								if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {
									$acoes .="<div class='dropdown-divider'></div>
									<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
									<div class='dropdown-divider'></div>
									<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
									<div class='dropdown-divider'></div>
									<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>";
								}	
							$acoes .=" </div>
						</div>
					</div>";*/
			
					$classificacao = "<div class='list-icons'>
										<div style='height: 25px; width: 25px; background-color: $item[AtClRCor]; border-radius: 13px;' >
									</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($espera,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'],  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					$classificacao,  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}

		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
			
					$classificacao = "<div class='list-icons'>
										<div style='height: 25px; width: 25px; background-color: $item[AtClRCor]; border-radius: 13px;' >
									</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($emAtendimento,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'],  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					$classificacao,  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}

		foreach($rowObservacao as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
			
					$classificacao = "<div class='list-icons'>
										<div style='height: 25px; width: 25px; background-color: $item[AtClRCor]; border-radius: 13px;' >
									</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($observacao,[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'],  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					$classificacao,  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'prontuario' => $item['ClienCodigo']
				]]);
		}

		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoNome'] == 'Enfermeiro' || $rowProfissao['ProfissaoNome'] == 'Técnico de  Enfermagem') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item classificacao' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item ' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoNome'] == 'Médico'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
					
			$classificacao = "<div class='list-icons'>
								<div style='height: 25px; width: 25px; background-color: $item[AtClRCor]; border-radius: 13px;' >
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($atendido,
			[
				'data' => [
					mostraData($item['AtXSeData']) . " - " . $item['AtXSeHorario'] ,  // Data - hora
					$difference,  // Espera
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					$item['SrVenNome'],  // Procedimento
					$classificacao,  // Risco
					"<span class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'iAtendimento' => $item['AtendId'],
					'sJustificativa' => $item['AtendObservacao'],
					'AtClaChave' => $item['AtClaChave'],
					'AtClaNome' => $item['AtClaNome'],
					'prontuario' => $item['ClienCodigo']
				]
			]);
		}
		$array = [
			'dataEspera' =>$espera,
			'dataAtendido' =>$atendido,
			'dataEmAtendimento' => $emAtendimento,
			'dataObservacao' => $observacao,
			'acesso' => $acesso,
			'titulo' => '',
			'status' => 'success',
			'menssagem' => ''
		];
	
		echo json_encode($array);
	} elseif ($tipoRequest == 'SITUACOES'){
		$tipo = $_POST['tipo'];
		$list = $tipo == 'AGENDAMENTO'?"'AGENDADOVENDA','CONFIRMADO','CANCELADO','FILAESPERA'":
		"'NAOESPEROU'";
		$sql = "SELECT SituaId,SituaNome,SituaChave
		FROM Situacao
		WHERE SituaChave in ($list)";
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
		$tipo = $_POST['tipo'];
		$situacao = $_POST['iSituacao'];
		$sJustificativa = $_POST['sJustificativa'];
	
		$sql = $tipo == 'AGENDAMENTO'? "UPDATE Agendamento set AgendSituacao = '$situacao', AgendJustificativa = '$sJustificativa'
		WHERE AgendId = $iAtendimento":
		"UPDATE Atendimento set AtendSituacao = '$situacao', AtendJustificativa = '$sJustificativa'
		WHERE AtendId = $iAtendimento";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Alterar Situação',
			'status' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!',
		]);
	} elseif ($tipoRequest == 'EXCLUI'){
		$iAtendimento = $_POST['id'];

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

		$sql = "SELECT COUNT(isnull(clienCodigo,0)) as Codigo
				FROM Cliente
				Where ClienUnidade = " . $_SESSION['UnidadeId'] . "";
		$result = $conn->query("$sql");
		$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);

		$cod = (int)$rowCodigo['Codigo'] + 1;
		$cod = str_pad($cod, 6, "0", STR_PAD_LEFT);

		$paciente = [
			'id' => 'NOVO',
			'prontuario' => isset($_POST['prontuario'])?$_POST['prontuario']:'null',
			'nome' => isset($_POST['nome'])?$_POST['nome']:'null',
			'nomeSocial' => isset($_POST['nomeSocial'])?$_POST['nomeSocial']:'null',
			'cpf' => isset($_POST['cpf'])?$_POST['cpf']:'null',
			'cns' => isset($_POST['cns'])?$_POST['cns']:'null',
			'rg' => isset($_POST['rg'])?$_POST['rg']:'null',
			'emissor' => isset($_POST['emissor'])?$_POST['emissor']:'null',
			'uf' => isset($_POST['uf'])?$_POST['uf']:'null',
			'sexo' => isset($_POST['sexo'])?$_POST['sexo']:'null',
			'nascimento' => isset($_POST['nascimento'])?$_POST['nascimento']:'',
			'nomePai' => isset($_POST['nomePai'])?$_POST['nomePai']:'null',
			'nomeMae' => isset($_POST['nomeMae'])?$_POST['nomeMae']:'null',
			'racaCor' => isset($_POST['racaCor'])?$_POST['racaCor']:'null',
			'estadoCivil' => isset($_POST['estadoCivil'])?$_POST['estadoCivil']:'null',
			'naturalidade' => isset($_POST['naturalidade'])?$_POST['naturalidade']:'null',
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
		$sql = "INSERT INTO Cliente(ClienCodigo,ClienNome,ClienNomeSocial,ClienCpf,ClienCartaoSus,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
			ClienNaturalidade,ClienRacaCor,ClienEstadoCivil,
			ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienProfissao,ClienCep,ClienEndereco,
			ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
			ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade)
			VALUES('$cod','$paciente[nome]','$paciente[nomeSocial]','$paciente[cpf]','$paciente[cns]','$paciente[rg]',
			'$paciente[emissor]','$paciente[uf]','$paciente[sexo]','$paciente[naturalidade]','$paciente[racaCor]',
			'$paciente[estadoCivil]',
			'$paciente[nascimento]','$paciente[nomePai]','$paciente[nomeMae]',
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
			'responsavel' => $responsavelId
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
			'classificacaoRisco' => isset($_POST['classificacaoRisco'])?$_POST['classificacaoRisco']:'',
			'grupo' => isset($_POST['grupo'])?$_POST['grupo']:'',
			'subgrupo' => isset($_POST['subgrupo'])?$_POST['subgrupo']:'',
			'classificacao' => isset($_POST['classificacao'])?$_POST['classificacao']:'',
			'situacao' => isset($_POST['situacao'])?$_POST['situacao']:'',
			'observacao' => isset($_POST['observacaoAtendimento'])?$_POST['observacaoAtendimento']:''
		];
		$cliente = $_POST['cliente'];
		$responsavel = $_POST['responsavel'];
		$tipo = isset($_POST['tipo'])?$_POST['tipo']:null;

		$iAtendimento = isset($_POST['iAtendimento'])?$_POST['iAtendimento']:null;
		$iAgendamento = $tipo!='ATENDIMENTO'?$iAtendimento:null;

		$status = isset($_POST['status'])?$_POST['status']:null;

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
			
			if($tipo == 'ATENDIMENTO' && $status == 'EDITA'){
				$sql = "UPDATE Atendimento SET
				AtendDataRegistro = '$atendimento[dataRegistro]',
				AtendCliente = '$cliente[id]',
				AtendModalidade = '$atendimento[modalidade]',
				AtendResponsavel = ".($responsavel?$responsavel['id']:'').",
				AtendClassificacao = '$atendimento[classificacao]',
				AtendObservacao = '$atendimento[observacao]',
				AtendSituacao = '$rowSituacao[SituaId]',
				AtendUsuarioAtualizador = '$usuarioId',
				AtendUnidade = '$iUnidade'
				WHERE AtendId = $iAtendimento";
				$conn->query($sql);

				$sql = "DELETE FROM AtendimentoXServico WHERE AtXSeAtendimento = $iAtendimento";
				$conn->query($sql);
			}else{
				$sql = "INSERT INTO Atendimento(AtendNumRegistro,AtendDataRegistro,AtendCliente,
					AtendModalidade,AtendResponsavel,AtendClassificacao,AtendObservacao,AtendSituacao,
					AtendUsuarioAtualizador,AtendUnidade)
					VALUES('$numRegistro','$atendimento[dataRegistro]','$cliente[id]','$atendimento[modalidade]',
					'".($responsavel?$responsavel['id']:'')."',$atendimento[classificacao],'$atendimento[observacao]',
				$rowSituacao[SituaId],$usuarioId,$iUnidade)";
				$conn->query($sql);
		
				$iAtendimento = $conn->lastInsertId();
				if($iAgendamento){
					$sql = "UPDATE Agendamento SET AgendAtendimento=$iAtendimento WHERE AgendId = $iAgendamento";
					$conn->query($sql);
				}
			}

			$sql = "INSERT INTO AtendimentoXServico(AtXSeAtendimento,AtXSeServico,AtXSeProfissional,AtXSeDesconto
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeUsuarioAtualizador,AtXSeUnidade)
			VALUES ";
	
			foreach($atendimentoServicos as $atendimentoServico){
				$sql .= "('$iAtendimento','$atendimentoServico[iServico]','$atendimentoServico[iMedico]','$atendimentoServico[desconto]'
				'$atendimentoServico[data]','$atendimentoServico[hora]','$atendimentoServico[iLocal]',
				'$atendimentoServico[valor]','$usuarioId','$iUnidade'),";
			}
			$sql = substr($sql, 0, -1);
			$conn->query($sql);

			$sql = "UPDATE Cliente SET
				ClienNome= '$cliente[nome]',
				ClienNomeSocial= '$cliente[nomeSocial]',
				ClienCpf= '$cliente[cpf]',
				ClienCartaoSus= '$cliente[cns]',
				ClienRg= '$cliente[rg]',
				ClienOrgaoEmissor= '$cliente[emissor]',
				ClienUf= '$cliente[uf]',
				ClienSexo= '$cliente[sexo]',
				ClienDtNascimento= '$cliente[nascimento]',
				ClienNomePai= '$cliente[nomePai]',
				ClienNomeMae= '$cliente[nomeMae]',
				ClienRacaCor= '$cliente[racaCor]',
				ClienEstadoCivil= '$cliente[estadoCivil]',
				ClienNaturalidade= '$cliente[naturalidade]',
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

		$sql = "SELECT ClienId,ClienNome,ClienNomeSocial,ClienCodigo,
		ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienRacaCor,ClienEstadoCivil,ClienNaturalidade,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienCartaoSus,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,ClienCelular,
		ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
		FROM Cliente WHERE ClienId = $iPaciente and ClienUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		$array = [];
		if($row){
			$array = [
				'status' => 'success',
				'prontuario' => $row['ClienCodigo'],
				'nome' => $row['ClienNome'],
				'nomeSocial' => $row['ClienNomeSocial'],
				'cpf' => $row['ClienCpf'],
				'cns' => $row['ClienCartaoSus'],
				'rg' => $row['ClienRg'],
				'emissor' => $row['ClienOrgaoEmissor'],
				'uf' => $row['ClienUf'],
				'sexo' => $row['ClienSexo'],
				'nascimento' => $row['ClienDtNascimento'],
				'nomePai' => $row['ClienNomePai'],
				'nomeMae' => $row['ClienNomeMae'],
				'racaCor' => $row['ClienRacaCor'],
				'estadoCivil' => $row['ClienEstadoCivil'],
				'naturalidade' => $row['ClienNaturalidade'],
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
		ClienStatus,ClienUnidade,ClienUsuarioAtualizador)
		VALUES ('$sCodigo','$nomePaciente','$telefone','$celular','$email','$observacao','F',1,$iUnidade,$usuarioId)";
		$conn->query($sql);

		$lestIdCliente = $conn->lastInsertId();

		// busca todos os usuários com o novo inserido para adicionalo ja selecionado no select
		$sql = "SELECT ClienId,ClienCodigo,ClienNome,ClienNomeSocial,ClienCpf,ClienRg,ClienOrgaoEmissor,ClienUf,ClienSexo,
		ClienDtNascimento,ClienNomePai,ClienNomeMae,ClienRacaCor,ClienEstadoCivil,ClienNaturalidade,ClienProfissao,ClienCep,ClienEndereco,
		ClienNumero,ClienComplemento,ClienBairro,ClienCidade,ClienEstado,ClienContato,ClienTelefone,
		ClienCelular,ClienEmail,ClienObservacao,ClienStatus,ClienUsuarioAtualizador,ClienUnidade
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
		$sql = "SELECT SrVenId,SrVenNome,SrVenCodigo
		FROM ServicoVenda WHERE SrVenUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['SrVenId'],
				'nome' => $item['SrVenNome'],
				'codigo' => $item['SrVenCodigo'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'MEDICOS'){
		$servico = $_POST['servico'];

		$sql = "SELECT ProfiId,ProfiNome
		FROM ProfissionalXServicoVenda
		JOIN Profissional ON ProfiId = PrXSVProfissional
		WHERE PrXSVServicoVenda = $servico and ProfiUnidade = $iUnidade";
		$result = $conn->query($sql);
		$result = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ProfiId'],
				'nome' => $item['ProfiNome'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'LOCALATENDIMENTO'){

		$iMedico = $_POST['iMedico'];
		$hoje = date('Y-m-d');

		$sql = "SELECT AtLocId,AtLocNome,AtLocStatus,AtLocUsuarioAtualizador,AtLocUnidade
		FROM AtendimentoLocal
		JOIN ProfissionalAgenda ON PrAgeAtendimentoLocal = AtLocId
		WHERE PrAgeProfissional = $iMedico
		AND PrAgeData >= '$hoje'
		AND AtLocUnidade = $iUnidade";
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
			'status' => 'new',
			'desconto' => 0
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

		if($_POST['tipo'] == 'ATENDIMENTO'){
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
									'status' => 'att',
									'desconto' => $item['AtXSeDesconto']
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
							'status' => 'att',
							'desconto' => $item['AtXSeDesconto']
						]);
					}
				}
			}
		}else{
			if(isset($_POST['iAtendimento']) && $_POST['iAtendimento']){
				$iAtendimento = $_POST['iAtendimento'];
	
				$sql = "SELECT AgendId,ProfiId,AtLocId,AgendProfissional,AgendAtendimentoLocal,AgendDataRegistro,
				AgendData,AgendHorario,AtModNome,
				AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,
				AgendObservacao,ClienNome, ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,
				SituaCor,ProfiNome,AtLocNome,SrVenNome,SrVenValorVenda,SrVenId
				FROM Agendamento
				JOIN AtendimentoModalidade ON AtModId = AgendModalidade
				JOIN Situacao ON SituaId = AgendSituacao
				JOIN Cliente ON ClienId = AgendCliente
				JOIN Profissional ON ProfiId = AgendProfissional
				JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
				JOIN ServicoVenda ON SrVenId = AgendServico
				WHERE AgendId = $iAtendimento and AgendUnidade = $iUnidade";
				$result = $conn->query($sql);
				$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);
	
				// esse loop duplo serve para evitar duplicações e evitar que os itens incluídos localmente não
				// desapareçam
				foreach($rowAtendimento as $item){
					$inArray = false;
					foreach($atendimentoSessao as $item2){
						if(($item2['id'] == "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]")){
							$inArray = true;
						}
					}
					if(!$inArray){
						array_push($atendimentoSessao, [
							'id' => "$item[AgendServico]#$item[ProfiId]#$item[AtLocId]",
							'iServico' => $item['SrVenId'],
							'iMedico' => $item['ProfiId'],
							'iLocal' => $item['AtLocId'],
					
							'servico' => $item['SrVenNome'],
							'medico' => $item['ProfiNome'],
							'local' => $item['AtLocNome'],
							'sData' => mostraData($item['AgendData']),
							'data' => $item['AgendData'],
							'hora' => mostraHora($item['AgendHorario']),
							'valor' => $item['SrVenValorVenda'],
							'status' => 'att',
							'desconto' => 0
						]);
					}
				}
			}
		}
		$valorTotal = 0;
		$valorTotalDesconto = 0;

		foreach($atendimentoSessao as $item){
			$valorTotal += $item['valor'] - $item['desconto'];
			$valorTotalDesconto += $item['desconto'];
		}
		$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;
		
		echo json_encode([
			'array' => $atendimentoSessao,
			'valorTotal' => $valorTotal,
			'valorTotalDesconto' => $valorTotalDesconto
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
				if($item['status'] == 'new'){
					array_splice($atendimentoSessao, $key, 1);
				} else {
					$atendimentoSessao[$key]['status'] = 'rem';
				}
				$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;
				echo json_encode([
					'status' => 'success',
					'titulo' => 'Excluir serviço',
					'menssagem' => 'Serviço Excluído!!!',
					'id' => $newId
				]);
				break;
			}
		}
	} elseif ($tipoRequest == 'SETDATAPROFISSIONAL'){
		$iMedico = $_POST['iMedico'];
		$localAtend = $_POST['localAtend'];
		$hoje = date('Y-m-d');

		$sql = "SELECT PrAgeData, PrAgeHoraInicio, PrAgeHoraFim
		FROM ProfissionalAgenda WHERE PrAgeProfissional = $iMedico and PrAgeUnidade = $iUnidade
		AND PrAgeAtendimentoLocal = $localAtend
		AND PrAgeData >= '$hoje'
		ORDER BY PrAgeData ASC";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$arrayData = [];
		foreach($row as $item){
			$data = explode('-', $item['PrAgeData']);
			$data = $data[2].'/'.$data[1].'/'.$data[0];
			
			array_push($arrayData, $data);
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

		$arrayHora = [true];
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
	} elseif ($tipoRequest == 'AUDITORIA'){
		$tipo = $_POST['tipo'];
		$id = $_POST['id'];

		$sql = $tipo == 'AGENDAMENTO'? "SELECT UsuarNome, ProfiNome, ClienNome, AgendData as dataRegistro, AgendHorario as horaRegistro, AgendDataRegistro as dtHrRegistro
			FROM Agendamento
			JOIN Cliente ON ClienId = AgendCliente
			JOIN Profissional ON ProfiId = AgendProfissional
			JOIN Usuario ON UsuarId = AgendUsuarioAtualizador
			WHERE AgendUnidade = $iUnidade and AgendId = $id":
			"SELECT UsuarNome, ProfiNome, ClienNome, AtXSeData as dataRegistro, AtXSeHorario as horaRegistro, AtendDataRegistro as dtHrRegistro
			FROM Atendimento
			JOIN AtendimentoXServico ON AtXSeAtendimento = AtendId
			JOIN Cliente ON ClienId = AtendCliente
			JOIN Usuario ON UsuarId = AtXSeUsuarioAtualizador
			JOIN Profissional ON ProfiId = AtXSeProfissional";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode([
			'auditoria' => $row,
			'status' => 'success',
			'titulo' => 'Data',
			'menssagem' => 'Hora do profissional selecionado!!!',
		]);
	} elseif ($tipoRequest == 'CLASSIFICACAORISCOS'){
		$sql = "SELECT AtClRId,AtClRNome,AtClRNomePersonalizado,AtClRCor,
		AtClRDeterminantes
		FROM AtendimentoClassificacaoRisco
		WHERE AtClRUnidade != $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['AtClRId'],
				'nome' => $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'],
				'cor' => $item['AtClRCor'],
				'determinante' => $item['AtClRDeterminantes']
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'GRUPO'){
		$sql = "SELECT GrupoId,GrupoNome
		FROM Grupo
		WHERE GrupoUnidade != $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['GrupoId'],
				'nome' => $item['GrupoNome']
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'SUBGRUPO'){
		$sql = "SELECT AtSubId,AtSubNome
		FROM AtendimentoSubGrupo
		WHERE AtSubUnidade != $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['AtSubId'],
				'nome' => $item['AtSubNome']
			]);
		}

		echo json_encode($array);
	} elseif($tipoRequest == 'SETDESCONTO'){
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

		$id = $_POST['iServico'];
		$desconto = $_POST['desconto'];

		$valorTotal = 0;

		foreach($atendimentoSessao as $key=>$item){
			if($item['id'] == $id){
				$atendimentoSessao[$key]['desconto'] = $desconto;
			}
		}
		$_SESSION['atendimento']['atendimentoServicos'] = $atendimentoSessao;

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Desconto',
			'menssagem' => 'Desconto adicionado!!!',
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
