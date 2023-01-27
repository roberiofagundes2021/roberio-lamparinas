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
	$iEmpresa = $_SESSION['EmpreId'];
	if(!isset($_SESSION['atendimento'])){
		$_SESSION['atendimento'] = [
			'paciente' => '',
			'responsavel' => '',
			'atendimentoServicos' => []
		];
	}
	
 	 // feito consultas para buscar a profissão do profissional do atendimento
	$sql = "SELECT P.ProfiNome as ProfissaoNome, P.ProfiChave as ProfissaoChave
			FROM Profissional
			JOIN Profissao P ON P.ProfiId = Profissional.ProfiProfissao
			WHERE ProfiUsuario = $usuarioId";
	$result = $conn->query($sql);
	$rowProfissao = $result->fetch(PDO::FETCH_ASSOC);

	// feito consultas para buscar de acordo com a classificação do atendimento
	// (ATENDIMENTOSAMBULATORIAIS, ATENDIMENTOSHOSPITALARES, ATENDIMENTOSELETIVOS)
	if($tipoRequest == 'ATENDIMENTOS'){
		$array = [];
		$hoje = date('Y-m-d');

		$sql = "SELECT AgendId,AgendDataRegistro,AgendData,AgendHorario,AtModNome,
			AgendClienteResponsavel,AgendAtendimentoLocal,AgendServico,
			AgendObservacao,AgendJustificativa,ClienNome,ClienCodigo,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave, ClienDtNascimento,
			SituaCor,Profissional.ProfiNome as ProfissionalNome,AtLocNome, SrVenNome, ProfiCbo, Profissao.ProfiNome as ProfissaoNome
			FROM Agendamento
			LEFT JOIN AtendimentoModalidade ON AtModId = AgendModalidade
			LEFT JOIN Situacao ON SituaId = AgendSituacao
			LEFT JOIN Cliente ON ClienId = AgendCliente
			LEFT JOIN Profissional ON Profissional.ProfiId = AgendProfissional
			LEFT JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
			LEFT JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
			LEFT JOIN ServicoVenda ON SrVenId = AgendServico
			WHERE AgendUnidade = $iUnidade and SituaChave in ('AGENDADO','CONFIRMADO','FILAESPERA')
			AND AgendData = '$hoje'
			and AgendAtendimento is null";
		$result = $conn->query($sql);
		$rowAgendamento = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtendNumRegistro,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtendClassificacao,AtClaChave,
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
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			WHERE AtendUnidade = $iUnidade";
		$result = $conn->query($sql);
		$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId, AtendNumRegistro, AtOEnDataInicio as AtendData, AtOEnHoraInicio as AtendHora, AtOEnId as AtEntrada,SituaNome, SituaCor,AtClaChave,SituaChave,
			Conduta = 'Observação Hospitalar',ClienNome,ClienCodigo, Profissional.ProfiNome as ProfissionalNome, ProfiCbo, ClienDtNascimento
			FROM Atendimento
			LEFT JOIN AtendimentoObservacaoEntrada ON AtendId = AtOEnAtendimento
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN Profissional ON Profissional.ProfiId = AtendimentoObservacaoEntrada.AtOEnProfissional
			LEFT JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			WHERE SituaChave = 'AGUARDANDOLIBERACAOATENDIMENTO'
			AND AtendId  in (SELECT AtOEnAtendimento FROM AtendimentoObservacaoEntrada WHERE AtOEnUnidade = $iUnidade)
			AND AtendUnidade = $iUnidade
			UNION ALL 
			SELECT AtendId, AtendNumRegistro, AtIEnDataInicio as AtendData, AtIEnHoraInicio as AtendHora,  AtIEnId as AtEntrada,SituaNome, SituaCor,AtClaChave,SituaChave,
			Conduta = 'Internação Hospitalar', ClienNome,ClienCodigo, Profissional.ProfiNome as ProfissionalNome, ProfiCbo, ClienDtNascimento
			FROM Atendimento
			LEFT JOIN AtendimentoInternacaoEntrada ON AtendId = AtIEnAtendimento
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN Profissional ON Profissional.ProfiId = AtendimentoInternacaoEntrada.AtIEnProfissional 
			LEFT JOIN Profissao ON Profissional.ProfiProfissao = Profissao.ProfiId
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			WHERE SituaChave = 'AGUARDANDOLIBERACAOATENDIMENTO'
			AND AtendId  in (SELECT AtIEnAtendimento FROM AtendimentoInternacaoEntrada WHERE AtIEnUnidade = $iUnidade)
			AND AtendUnidade = $iUnidade
			ORDER BY AtendId";
		$result = $conn->query($sql);
		$rowSolicitacao = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$dataAtendimento = [];
		$dataAgendamento = [];
		$dataSolicitacao = [];
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
			$id = $item['AtendId'];
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
								".($item['AtClaChave']!='ELETIVO'?"<a href='#' onclick='adimissaoLeito($id)' class='dropdown-item'><i class='icon-stackoverflow' title='Leito'></i> Adimissão em Leito</a>":"")."
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
		foreach ($rowSolicitacao as $item) {

			$id = $item['AtendId'];
			$acoes = "<div class='list-icons'>
	
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'>
								<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Gestão de Leitos'></i> Gestão de Leitos</a>
								<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Tabela de Gastos'></i> Tabela de Gastos</a>
								<a href='#' onclick='' class='dropdown-item'><i class='icon-stackoverflow' title='Formularios'></i> Formularios</a>
								".($item['AtClaChave']!='ELETIVO'?"<a href='#' onclick='adimissaoLeito($id)' class='dropdown-item'><i class='icon-stackoverflow' title='Leito'></i> Adimissão em Leito</a>":"")."
							</div>
						</div>
					</div>";

			array_push($dataSolicitacao, [
				'data' => [
					mostraData($item['AtendData']) . " - " . mostraHora($item['AtendHora']), // Data - Hora
					$item['AtendNumRegistro'],  // Nº Registro
					$item['ClienNome'],  // Paciente
					calculaIdadeSimples($item['ClienDtNascimento']), // Idade paciente
					$item['ProfissionalNome'],  // Profissional
					$item['Conduta'],  // Conduta
					"<span style='cursor:pointer' class='badge badge-flat border-$item[SituaCor] text-$item[SituaCor]'>$item[SituaNome]</span>",  // Situação
					$acoes,  // Ações
				],
				'identify' => [
					'situacao' => $item['SituaChave'],
					'id' => $item['AtendId'],
					'prontuario' => 'Prontuário: '.($item['ClienCodigo']?$item['ClienCodigo']:'NaN'),
					'cbo' => 'CBO: '.($item['ProfiCbo']?$item['ProfiCbo']:'NaN')
				]
			]);
			
		}

		$array  = [
			'dataAgendamento' => $dataAgendamento,
			'dataAtendimento' => $dataAtendimento,
			'dataSolicitacao' => $dataSolicitacao,
			'contadorSolicitacoes' => count($rowSolicitacao),
			'titulo' => 'Atendimentos',
			'status' => 'success',
			'menssagem' => ''
		];	
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

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,AtendNumRegistro,
			AtClRNome,AtClRNomePersonalizado,AtClRTempo,AtClRDeterminantes,AtClRCor,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,
			SituaNome,SituaChave,SituaCor,AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMESPERA' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'AMBULATORIAL'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}
		
		$sql .= " ORDER BY AtClRTempo ASC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeUnidade = $iUnidade	AND AtClaChave = 'AMBULATORIAL'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}

		$sql .=" ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmAtendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMOBSERVACAO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'AMBULATORIAL'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}
		
		$sql .= " ORDER BY AtXSeId DESC";
		$resultObservacao = $conn->query($sql);
		$rowObservacao = $resultObservacao->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'ATENDIDO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'AMBULATORIAL'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}

		$sql .= " ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];
		$observacao = [];

		$sql = "SELECT AtClRId,AtClRNome,AtClRNomePersonalizado,AtClRCor,AtClRDeterminantes
			FROM AtendimentoClassificacaoRisco WHERE AtClRUnidade = $iUnidade";
		$resultRiscos = $conn->query($sql);
		$rowRiscos = $resultRiscos->fetchAll(PDO::FETCH_ASSOC);

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'>
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			}elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($espera,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
				
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			}elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($emAtendimento,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowObservacao as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			}elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($observacao,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Visualizar</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Visualizar'></i> Visualizar</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							<a href='#' class='dropdown-item receituario' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Receituário'></i> Receituário</a>
						</div>
					</div>";
			}elseif ($rowProfissao['ProfissaoNome'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($atendido,
			[
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
			'contadorEmObservacao' => count($observacao),
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
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtClRTempo,AtClRCor,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMESPERA' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'HOSPITALAR'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql.= " AND AtXSeProfissional = $iProfissional ";
		}
		
		$sql.= " ORDER BY AtClRTempo ASC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'HOSPITALAR'
			";
		
		$sql .= " AND AtXSeProfissional = $iProfissional ";

		$sql .=	" ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmAtendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'EMOBSERVACAO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'HOSPITALAR'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}
		
		$sql .= " ORDER BY AtXSeId DESC";
		$resultObservacao = $conn->query($sql);
		$rowObservacao = $resultObservacao->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome, AtendNumRegistro,
			AtClRNome,AtClRCor,AtClRNomePersonalizado,AtClRDeterminantes,AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda
			FROM AtendimentoXServico
			LEFT JOIN Atendimento ON AtendId = AtXSeAtendimento
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN Situacao ON SituaId = AtendSituacao
			LEFT JOIN Cliente ON ClienId = AtendCliente
			LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN ServicoVenda ON SrVenId = AtXSeServico
			LEFT JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
			WHERE SituaChave = 'ATENDIDO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'HOSPITALAR'
			";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){	
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}
		
		$sql .= " ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];
		$observacao = [];

		$sql = "SELECT AtClRId,AtClRNome,AtClRNomePersonalizado,AtClRCor,AtClRDeterminantes
		FROM AtendimentoClassificacaoRisco WHERE AtClRUnidade = $iUnidade";
		$resultRiscos = $conn->query($sql);
		$rowRiscos = $resultRiscos->fetchAll(PDO::FETCH_ASSOC);

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			} elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($espera,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			} elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($emAtendimento,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowObservacao as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
							<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
						</div>
					</div>";
			} elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($observacao,[
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
					'prontuario' => $item['ClienCodigo']
				]]);
		}
		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Visualizar</button>";
			$acoes = "<div class='list-icons'>";

			if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
				$acoes .= "
					$att
					<div class='dropdown'>													
						<a href='#' class='list-icons-item' data-toggle='dropdown'>
							<i class='icon-menu9'></i>
						</a>

						<div class='dropdown-menu dropdown-menu-right'> 
							<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Visualizar'></i> Visualizar</a>
							<div class='dropdown-divider'></div>
							<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
							<a href='#' class='dropdown-item receituario' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Receituário'></i> Receituário</a>
						</div>
					</div>";
			} elseif ($rowProfissao['ProfissaoNome'] == 'MEDICO'){
				$acoes .= "$atender";
			}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div class='btn dropdown-toggle' data-toggle='dropdown' style='height: 35px; width: 35px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
							</div>";
		
			$contato = $item['ClienCelular']?$item['ClienCelular']:($item['ClienTelefone']?$item['ClienTelefone']:'não informado');
			
			array_push($atendido,
			[
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

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,
		AtClRNome,AtClRTempo,AtClRNomePersonalizado,AtClRDeterminantes,
		AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,AtendNumRegistro,
		AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda, AtClRCor
		FROM AtendimentoXServico
		JOIN Atendimento ON AtendId = AtXSeAtendimento
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
		JOIN Cliente ON ClienId = AtendCliente
		JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		JOIN ServicoVenda ON SrVenId = AtXSeServico
		JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
		LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
		JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
		WHERE SituaChave = 'EMESPERA' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'ELETIVO'
		";
		
		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}

		$sql .=	" ORDER BY AtClRTempo ASC";
		$resultEspera = $conn->query($sql);
		$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,AtClRNome,AtClRNomePersonalizado,AtClRDeterminantes,
		AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,AtendNumRegistro,
		AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda, AtClRCor
		FROM AtendimentoXServico
		JOIN Atendimento ON AtendId = AtXSeAtendimento
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
		JOIN Cliente ON ClienId = AtendCliente
		JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		JOIN ServicoVenda ON SrVenId = AtXSeServico
		JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
		LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
		JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
		WHERE SituaChave = 'ATENDIDO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'ELETIVO'
		";

		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}

		$sql .= " ORDER BY AtXSeId DESC";
		$resultAtendido = $conn->query($sql);
		$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);

		$sql = "SELECT AtendId,AtXSeId,AtendDataRegistro,ClienNome,ClienCodigo,AtModNome,AtClaChave,AtClaNome,AtClRNome,AtClRNomePersonalizado,AtClRDeterminantes,
		AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,AtendNumRegistro,
		AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId,SrVenNome,SVXMoValorVenda, AtClRCor
		FROM AtendimentoXServico
		JOIN Atendimento ON AtendId = AtXSeAtendimento
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
		JOIN Cliente ON ClienId = AtendCliente
		JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		JOIN ServicoVenda ON SrVenId = AtXSeServico
		JOIN ServicoVendaXModalidade ON SVXMoServicoVenda = SrVenId
		LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
		JOIN AtendimentoClassificacaoRisco ON AtClRId = AtendClassificacaoRisco
		WHERE SituaChave = 'EMATENDIMENTO' AND AtXSeUnidade = $iUnidade AND AtClaChave = 'ELETIVO'
		";

		if ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
			$sql .= " AND AtXSeProfissional = $iProfissional ";
		}
			
		$sql .=	" ORDER BY AtXSeId DESC";
		$resultEmAtendimento = $conn->query($sql);
		$rowEmAtendimento = $resultEmAtendimento->fetchAll(PDO::FETCH_ASSOC);
		
		$espera = [];
		$atendido = [];
		$emAtendimento = [];

		$sql = "SELECT AtClRId,AtClRNome,AtClRNomePersonalizado,AtClRCor,AtClRDeterminantes,AtClRTempo
		FROM AtendimentoClassificacaoRisco WHERE AtClRUnidade = $iUnidade
		ORDER BY AtClRTempo ASC";
		$resultRiscos = $conn->query($sql);
		$rowRiscos = $resultRiscos->fetchAll(PDO::FETCH_ASSOC);

		foreach($rowEspera as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				} elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
			
			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];

			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div data-toggle='dropdown' style='height: 30px; width: 30px; cursor: pointer; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
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
				]
			]);
		}

		foreach($rowEmAtendimento as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Atender</button>";
			$acoes = "<div class='list-icons'>";

				if (!$rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {					
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Atender'></i> Atender</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";

			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];
			
			$classificacao = "<div style='cursor: pointer;' class='btn-group justify-content-center' title='$nome \n $item[AtClRDeterminantes]'>
								<div data-toggle='dropdown' style='height: 30px; width: 30px; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
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

		foreach($rowAtendido as $item){
			$difference = diferencaEmHoras($item['AtXSeData'], date('Y-m-d'));

			$att = "<a style='color: black' href='#' onclick='atualizaAtendimento(); class='list-icons-item'><i class='icon-pencil7' title='Editar Atendimento'></i></a>";
			$atender = "<button href='#'  type='button' class='btn btn-success btn-sm atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'>Visualizar</button>";
			$acoes = "<div class='list-icons'>";

				if ($rowProfissao['ProfissaoChave'] == 'ENFERMEIRO' || $rowProfissao['ProfissaoChave'] == 'TECNICODEENFERMAGEM') {
					
					$acoes .= "
						$att
						<div class='dropdown'>													
							<a href='#' class='list-icons-item' data-toggle='dropdown'>
								<i class='icon-menu9'></i>
							</a>

							<div class='dropdown-menu dropdown-menu-right'> 
								<a href='#' class='dropdown-item atender' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Visualizar'></i> Visualizar</a>
								<div class='dropdown-divider'></div>
								<a href='#' class='dropdown-item triagem' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Triagem'></i> Triagem</a>
								<a href='#' class='dropdown-item classificacao' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Classificação de Risco'></i> Classificação de Risco</a>
								<a href='#' class='dropdown-item historico' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Histórico do Paciente'></i> Histórico do Paciente</a>
								<a href='#' class='dropdown-item receituario' data-situachave='$item[SituaChave]' data-clachave='$item[AtClaChave]' data-clanome='$item[AtClaNome]' data-atendimento='$item[AtendId]' data-eletivo='$item[AtEleId]'><i class='icon-stackoverflow' title='Receituário'></i> Receituário</a>
							</div>
						</div>";
				}elseif ($rowProfissao['ProfissaoChave'] == 'MEDICO'){
					$acoes .= "$atender";
				}	
			$acoes .= "</div>";
					
			$borderColor = "";
			switch($item['AtClRCor']){
				case '#ff630f':$borderColor='#cd5819';break;
				case '#fa0000':$borderColor='#c10000';break;
				case '#fbff00':$borderColor='#adb003';break;
				case '#00ff1e':$borderColor='#2db93e';break;
				case '#0008ff':$borderColor='#010579';break;
				default: $borderColor = '#FFFF';break;
			}

			// essa etapa monta o submenu de riscos
			$riscos = "";
			foreach($rowRiscos as $risco){
				// .($risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'])
				$nome = $risco['AtClRNomePersonalizado']?$risco['AtClRNomePersonalizado']:$risco['AtClRNome'];
				$borderColorSubMenu = '';
				switch($risco['AtClRCor']){
					case '#ff630f':$borderColorSubMenu='#cd5819';break;
					case '#fa0000':$borderColorSubMenu='#c10000';break;
					case '#fbff00':$borderColorSubMenu='#adb003';break;
					case '#00ff1e':$borderColorSubMenu='#2db93e';break;
					case '#0008ff':$borderColorSubMenu='#010579';break;
					default: $borderColorSubMenu = '#FFFF';break;
				}
				$indicador = "<div style='height: 20px; width: 20px; background-color: $risco[AtClRCor]; border-radius: 13px;border: 2px solid $borderColorSubMenu;'></div>";

				$riscos .= "<div class='dropdown-item' onclick='mudaRisco($item[AtendId], $risco[AtClRId])' title='$risco[AtClRDeterminantes]'>
								<div class='col-lg-10'>$nome</div>
								<div class='col-lg-2'>$indicador</div>
							</div>";
			}

			$nome = $item['AtClRNomePersonalizado']?$item['AtClRNomePersonalizado']:$item['AtClRNome'];
			
			$classificacao = "<div class='btn-group justify-content-center' title='$nome \n$item[AtClRDeterminantes]'>
								<div data-toggle='dropdown' style='height: 30px; width: 30px; cursor: pointer; background-color: $item[AtClRCor]; border-radius: 20px;border: 2px solid $borderColor;' ></div>
								<div class='dropdown-menu'>
									$riscos
								</div>
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
			'acesso' => $acesso,
			'titulo' => '',
			'status' => 'success',
			'menssagem' => ''
		];
	
		echo json_encode($array);
	} elseif ($tipoRequest == 'SITUACOES'){
		$tipo = $_POST['tipo'];
		$list = $tipo == 'AGENDAMENTO'?"'AGENDADO','CONFIRMADO','CANCELADO','FILAESPERA'":
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

		// em algumas telas esse valor não é o id e sim a SITUACHAVE, dessa forma será necessario
		// pegar o ID da situação informada
		if(!is_int($situacao)){
			$sqlSituacao = "SELECT SituaId FROM Situacao WHERE SituaChave = '$situacao'";
			$resultSituacao = $conn->query($sqlSituacao);
			$rowSituacao = $resultSituacao->fetch(PDO::FETCH_ASSOC);
	
			$situacao = $rowSituacao['SituaId'];
		}
	
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

			$sql="SELECT AtModTipoRecebimento FROM AtendimentoModalidade WHERE AtModId = '$_POST[modalidade]' ";
			$result = $conn->query($sql);
			$resultModalidade = $result->fetch(PDO::FETCH_ASSOC);

			if ($resultModalidade['AtModTipoRecebimento'] == "À Vista") {
				$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'LIBERADO'";
			} else if ($resultModalidade['AtModTipoRecebimento'] == "À Prazo") {
				$sql = "SELECT SituaId FROM Situacao WHERE SituaChave = 'EMESPERA'";
			}
			$result = $conn->query($sql);
			$rowSituacao = $result->fetch(PDO::FETCH_ASSOC);

			if($tipo == 'ATENDIMENTO' && $status == 'EDITA'){
				$sql = "UPDATE Atendimento SET
					AtendDataRegistro = '$atendimento[dataRegistro]',
					AtendCliente = '$cliente[id]',
					AtendModalidade = '$atendimento[modalidade]',
					AtendClassificacaoRisco = '$atendimento[classificacaoRisco]',
					AtendResponsavel = '".($responsavel?$responsavel['id']:'')."',
					AtendClassificacao = '$atendimento[classificacao]',
					AtendObservacao = '$atendimento[observacao]',
					AtendSituacao = '$rowSituacao[SituaId]',
					AtendUsuarioAtualizador = '$usuarioId',
					AtendUnidade = '$iUnidade'
					WHERE AtendId = $iAtendimento";
				$conn->query($sql);

				// caso esteja editando sera criado novos atendimentos a partir dos serviços inseridos
				foreach($atendimentoServicos as $atendimentoServico){
					if($atendimentoServico['status'] == 'new'){
						$sql = "SELECT AtendNumRegistro FROM Atendimento WHERE AtendNumRegistro LIKE '%A$mes-%'
							ORDER BY AtendId DESC";
						$result = $conn->query($sql);
						$rowCodigo = $result->fetchAll(PDO::FETCH_ASSOC);
				
						$intaValCodigo = COUNT($rowCodigo)?intval(explode('-',$rowCodigo[0]['AtendNumRegistro'])[1])+1:1;
				
						$numRegistro = "A$mes-$intaValCodigo";

						$sql = "INSERT INTO Atendimento(AtendNumRegistro,AtendDataRegistro,AtendCliente,AtendAgendamento,
							AtendModalidade,AtendResponsavel,AtendClassificacao,AtendClassificacaoRisco,AtendObservacao,AtendSituacao,
							AtendUsuarioAtualizador,AtendUnidade)
							VALUES('$numRegistro','$atendimento[dataRegistro]','$cliente[id]','$iAgendamento','$atendimento[modalidade]',
							'".($responsavel?$responsavel['id']:'')."',$atendimento[classificacao],'$atendimento[classificacaoRisco]','$atendimento[observacao]',
							$rowSituacao[SituaId],$usuarioId,$iUnidade)";
						$conn->query($sql);
				
						$iAtendimento = $conn->lastInsertId();

						$desconto = $atendimentoServico['desconto']?$atendimentoServico['desconto']:0;
						$grupo = $atendimentoServico['iGrupo']?$atendimentoServico['iGrupo']:'null';
						$subGrupo = $atendimentoServico['iSubGrupo']?$atendimentoServico['iSubGrupo']:'null';
						
						$sql = "INSERT INTO AtendimentoXServico(AtXSeAtendimento,AtXSeGrupo,AtXSeSubGrupo,AtXSeServico,AtXSeProfissional,AtXSeDesconto,
							AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeUsuarioAtualizador,AtXSeUnidade)
							VALUES('$iAtendimento',$grupo,$subGrupo,
							'$atendimentoServico[iServico]','$atendimentoServico[iMedico]','$desconto',
							'$atendimentoServico[data]','$atendimentoServico[hora]','$atendimentoServico[iLocal]',
							'$atendimentoServico[valor]','$usuarioId','$iUnidade')";
						$conn->query($sql);
					}
				}
			}else{
				foreach($atendimentoServicos as $atendimentoServico){
					$sql = "SELECT AtendNumRegistro FROM Atendimento WHERE AtendNumRegistro LIKE '%A$mes-%'
						ORDER BY AtendId DESC";
					$result = $conn->query($sql);
					$rowCodigo = $result->fetchAll(PDO::FETCH_ASSOC);
			
					$intaValCodigo = COUNT($rowCodigo)?intval(explode('-',$rowCodigo[0]['AtendNumRegistro'])[1])+1:1;
			
					$numRegistro = "A$mes-$intaValCodigo";

					$sql = "INSERT INTO Atendimento(AtendNumRegistro,AtendDataRegistro,AtendCliente,AtendAgendamento,
						AtendModalidade,AtendResponsavel,AtendClassificacao,AtendClassificacaoRisco,AtendObservacao,AtendSituacao,
						AtendUsuarioAtualizador,AtendUnidade)
						VALUES('$numRegistro','$atendimento[dataRegistro]',$cliente[id],'$iAgendamento','$atendimento[modalidade]',
						'$responsavel','$atendimento[classificacao]','$atendimento[classificacaoRisco]','$atendimento[observacao]',
					$rowSituacao[SituaId],$usuarioId,$iUnidade)";
					$conn->query($sql);
			
					$iAtendimento = $conn->lastInsertId();
	
					if($atendimentoServico['status'] != 'rem'){
						$desconto = $atendimentoServico['desconto']?$atendimentoServico['desconto']:0;
						$grupo = $atendimentoServico['iGrupo']?$atendimentoServico['iGrupo']:'null';
						$subGrupo = $atendimentoServico['iSubGrupo']?$atendimentoServico['iSubGrupo']:'null';
						$valor = $atendimentoServico['valor']?$atendimentoServico['valor']:0;
						
						$sql = "INSERT INTO AtendimentoXServico(AtXSeAtendimento,AtXSeGrupo,AtXSeSubGrupo,AtXSeServico,AtXSeProfissional,AtXSeDesconto,
							AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtXSeValor,AtXSeUsuarioAtualizador,AtXSeUnidade)
							VALUES($iAtendimento,$grupo,$subGrupo,
							$atendimentoServico[iServico],$atendimentoServico[iMedico],$desconto,
							'$atendimentoServico[data]','$atendimentoServico[hora]',$atendimentoServico[iLocal],
							$valor,$usuarioId,$iUnidade)";
						$conn->query($sql);
					}
				}
			}

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

		$sql = "SELECT ClienId,ClienNome, ClienCodigo
		FROM Cliente WHERE ClienUnidade = $iUnidade";
		$result = $conn->query($sql);

		$array = [];
		foreach($result as $item){
			array_push($array,[
				'id' => $item['ClienId'],
				'nome' => $item['ClienNome'],
				'codigo' => $item['ClienCodigo']
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
				'idCliente' => $row['ClienId'],
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
		FROM AtendimentoModalidade
		WHERE AtModUnidade = $iUnidade ";
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
		$grupo = isset($_POST['grupo'])?$_POST['grupo']:false;
		$subgrupo = isset($_POST['subGrupo'])?$_POST['subGrupo']:false;

		$sql = "SELECT SrVenId,SrVenNome,SrVenCodigo
		FROM ServicoVenda WHERE SrVenUnidade = $iUnidade";

		$sql .= $grupo?" and SrVenGrupo = $grupo":"";
		$sql .= $subgrupo?" and SrVenSubGrupo = $subgrupo":"";

		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['SrVenId'],
				'nome' => $item['SrVenNome'],
				'codigo' => $item['SrVenCodigo'],
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'MEDICOS'){
		$servico = isset($_POST['servico'])?$_POST['servico']:false;

		if($servico){
			$sql = "SELECT ProfiId,ProfiNome
			FROM ProfissionalXServicoVenda
			JOIN Profissional ON ProfiId = PrXSVProfissional
			WHERE PrXSVServicoVenda = $servico and ProfiUnidade = $iUnidade";
		}else{
			$sql = "SELECT ProfiId,ProfiNome
			FROM Profissional
			WHERE ProfiUnidade = $iUnidade";
		}
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
		AND PrAgeData = '$hoje'
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

		$iGrupo = isset($_POST['grupo'])?$_POST['grupo']:null;
		$iSubGrupo = isset($_POST['subGrupo'])?$_POST['subGrupo']:null;
		$iServico = $_POST['servico'];
		$iMedico = $_POST['medicos'];
		$sData = explode('/',$_POST['dataAtendimento']);
		$sData = $sData[2].'-'.$sData[1].'-'.$sData[0];
		$sHora = $_POST['horaAtendimento'];
		$iLocal = $_POST['localAtendimento'];
		
		// $sqlServico = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SrVenValorCusto,SrVenUnidade
		// FROM ServicoVenda WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
		$sql = "SELECT SrVenId,SrVenNome,SrVenDetalhamento,SVXMoValorVenda,SrVenUnidade
		FROM ServicoVenda 
		LEFT JOIN ServicoVendaXModalidade ON SrVenId = SVXMoServicoVenda
		WHERE SrVenId = $iServico and SrVenUnidade = $iUnidade";
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
			'iGrupo' => $iGrupo,
			'iSubGrupo' => $iSubGrupo,

			'servico' => $resultServico['SrVenNome'],
			'medico' => $resultMedico['ProfiNome'],
			'local' => $resultLocal['AtLocNome'],
			'sData' => mostraData($sData),
			'data' => $sData,
			'hora' => mostraHora($sHora),
			'valor' => $resultServico['SVXMoValorVenda'],
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
					SituaCor,ProfiNome,SrVenNome,SVXMoValorVenda,SrVenId,AtXSeGrupo,AtXSeSubGrupo
					FROM AtendimentoXServico
					JOIN Atendimento ON AtendId = AtXSeAtendimento
					JOIN AtendimentoModalidade ON AtModId = AtendModalidade
					JOIN Situacao ON SituaId = AtendSituacao
					JOIN Cliente ON ClienId = AtendCliente
					JOIN Profissional ON ProfiId = AtXSeProfissional
					JOIN AtendimentoLocal ON AtLocId = AtXSeAtendimentoLocal
					JOIN ServicoVenda ON SrVenId = AtXSeServico
					LEFT JOIN ServicoVendaXModalidade ON SrVenId = SVXMoServicoVenda
					WHERE AtXSeUnidade = $iUnidade and AtXSeAtendimento = $iAtendimento";
				$result = $conn->query($sql);
				$rowAtendimento = $result->fetchAll(PDO::FETCH_ASSOC);
	
				// esse loop duplo serve para evitar duplicações e evitar que os itens incluídos localmente não
				// desapareçam
				foreach($rowAtendimento as $item){
					if(COUNT($atendimentoSessao)){
						$duplicate = false;
						foreach($atendimentoSessao as $item2){
							if("$item[SrVenId]#$item[ProfiId]#$item[AtLocId]" == $item2['id']){
								$duplicate = true;
							}
						}
						if(!$duplicate){
							array_push($atendimentoSessao, [
								'id' => "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]",
								'iServico' => $item['SrVenId'],
								'iMedico' => $item['ProfiId'],
								'iLocal' => $item['AtLocId'],
								'iGrupo' => $item['AtXSeGrupo'],
								'iSubGrupo' => $item['AtXSeSubGrupo'],
						
								'servico' => $item['SrVenNome'],
								'medico' => $item['ProfiNome'],
								'local' => $item['AtLocNome'],
								'sData' => mostraData($item['AtXSeData']),
								'data' => $item['AtXSeData'],
								'hora' => mostraHora($item['AtXSeHorario']),
								'valor' => $item['SVXMoValorVenda'],
								'status' => 'att',
								'desconto' => $item['AtXSeDesconto']
							]);
						}
					}else{
						array_push($atendimentoSessao, [
							'id' => "$item[SrVenId]#$item[ProfiId]#$item[AtLocId]",
							'iServico' => $item['SrVenId'],
							'iMedico' => $item['ProfiId'],
							'iLocal' => $item['AtLocId'],
							'iGrupo' => $item['AtXSeGrupo'],
							'iSubGrupo' => $item['AtXSeSubGrupo'],
					
							'servico' => $item['SrVenNome'],
							'medico' => $item['ProfiNome'],
							'local' => $item['AtLocNome'],
							'sData' => mostraData($item['AtXSeData']),
							'data' => $item['AtXSeData'],
							'hora' => mostraHora($item['AtXSeHorario']),
							'valor' => $item['SVXMoValorVenda'],
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
				SituaCor,ProfiNome,AtLocNome,SrVenNome,SVXMoValorVenda,SrVenId
				FROM Agendamento
				JOIN AtendimentoModalidade ON AtModId = AgendModalidade
				JOIN Situacao ON SituaId = AgendSituacao
				JOIN Cliente ON ClienId = AgendCliente
				JOIN Profissional ON ProfiId = AgendProfissional
				JOIN AtendimentoLocal ON AtLocId = AgendAtendimentoLocal
				JOIN ServicoVenda ON SrVenId = AgendServico
				LEFT JOIN ServicoVendaXModalidade ON SrVenId = SVXMoServicoVenda
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
							'valor' => $item['SVXMoValorVenda'],
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
		WHERE AtClRUnidade = $iUnidade";
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
		$sql = "SELECT AtGruId,AtGruNome
		FROM AtendimentoGrupo
		WHERE AtGruUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['AtGruId'],
				'nome' => $item['AtGruNome']
			]);
		}

		echo json_encode($array);
	} elseif ($tipoRequest == 'SUBGRUPO'){
		$grupo = isset($_POST['grupo'])?$_POST['grupo']:false;

		$sql = "SELECT AtSubId,AtSubNome
		FROM AtendimentoSubGrupo
		WHERE AtSubUnidade = $iUnidade";

		$sql .= $grupo?" and AtSubGrupo = $grupo":"";

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
	} elseif($tipoRequest == 'SETRISCO'){
		$id = $_POST['id'];
		$risco = $_POST['risco'];

		$sql = "UPDATE Atendimento SET AtendClassificacaoRisco = $risco
		WHERE Atendid = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Risco',
			'menssagem' => 'Classificação de risco atualizada!!!',
		]);
	} elseif($tipoRequest == 'ESPECIALIDADES'){
		$id = $_POST['id'];

		$sql = "SELECT EspecId,EspecNome
		FROM ProfissionalXEspecialidade
		JOIN Especialidade ON EspecId = PrXEsEspecialidade
		WHERE PrXEsProfissional = $id and PrXEsUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['EspecId'],
				'nome' => $item['EspecNome'],
			]);
		}

		echo json_encode($array);
	} elseif($tipoRequest == 'MODELOS'){
		$chave = $_POST['chave'];

		$sql = "SELECT AtTMoId,AtTMoNome
		FROM AtendimentoTipoModelo
		WHERE AtTMoChave = '$chave'";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['AtTMoId'],
				'nome' => $item['AtTMoNome']
			]);
		}

		echo json_encode($array);
	} elseif($tipoRequest == 'CONTEUDOMODELO'){
		$id = $_POST['id'];

		$sql = "SELECT TOP (1) AtModId,AtModConteudo
		FROM AtendimentoModelo
		WHERE AtModTipoModelo = '$id'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode([
			'id' => $row['AtModId'],
			'conteudo' => $row['AtModConteudo']
		]);
	} elseif($tipoRequest == 'CID10'){
		$sql = "SELECT Cid10Id,Cid10Capitulo,Cid10Codigo,Cid10Descricao
		FROM Cid10";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['Cid10Id'],
				'capitulo' => $item['Cid10Capitulo'],
				'codigo' => $item['Cid10Codigo'],
				'descricao' => $item['Cid10Descricao'],
			]);
		}

		echo json_encode($array);
	} elseif($tipoRequest == 'SALVARENCAMINHAMENTO'){
		$id = $_POST['id'];
		$dataI = $_POST['dataI'];
		$horaI = $_POST['horaI'];
		$dataF = $_POST['dataF'];
		$horaF = $_POST['horaF'];
		$profissional = $_POST['profissional'];
		$profissionalDestino = $_POST['profissionalDestino'];
		$especialidade = $_POST['especialidade'];
		$modelo = $_POST['modelo'];
		$cid = $_POST['cid'];
		$encaminhamentoMedico = $_POST['encaminhamentoMedico'];
	
		$dataI = explode('/',$dataI);
		$dataF = explode('/',$dataF);

		$dataI = $dataI[2].'-'.$dataI[1].'-'.$dataI[0];
		$dataF = $dataF[2].'-'.$dataF[1].'-'.$dataF[0];

		$sql = "INSERT INTO AtendimentoEncaminhamentoMedico(AtEMeAtendimento,AtEMeDataInicio,
		AtEMeHoraInicio,AtEMeDataFim,AtEMeHoraFim,AtEMeProfissional,AtEMeProfissionalDestino,
		AtEMeEspecialidade,AtEMeModelo,AtEMeCid10,AtEMeEncaminhamentoMedico,AtEMeUnidade)
		VALUES('$id','$dataI','$horaI','$dataF','$horaF','$profissional','$profissionalDestino',
		'$especialidade','$modelo','$cid','$encaminhamentoMedico','$iUnidade')";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Incluir Encaminhamento',
			'menssagem' => 'Encaminhamento inserido com sucesso!!!'
		]);
	} elseif ($tipoRequest == 'ENCAMINHAMENTOS'){
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

		$iAtendimento = $_POST['id'];
	
		$sql = "SELECT AtEMeId,AtEMeDataInicio,AtEMeHoraInicio,ProfiNome,EspecNome
			FROM AtendimentoEncaminhamentoMedico
			JOIN Profissional ON ProfiId = AtEMeProfissionalDestino
			JOIN Especialidade ON EspecId = AtEMeEspecialidade
			WHERE AtEMeAtendimento = $iAtendimento";
		$result = $conn->query($sql);
		$rowEncaminhamento = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rowEncaminhamento as $item){
			$data = explode('-',$item['AtEMeDataInicio']);
			$data = $data[2].'/'.$data[1].'/'.$data[0];

			$hora = explode(':',$item['AtEMeHoraInicio']);
			$hora = $hora[0].':'.$hora[1];
			array_push($array,[
				'id'=>$item['AtEMeId'],
				'data'=>$data,
				'hora'=>$hora,
				'profissional'=>$item['ProfiNome'],
				'especialidade'=>$item['EspecNome'],
			]);
		}
		
		echo json_encode($array);
	} elseif ($tipoRequest == 'EXCLUIRENCAMINHAMENTO'){
		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoEncaminhamentoMedico
		WHERE AtEMeId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Encaminhamento',
			'menssagem' => 'Encaminhamento excluído!!!',
		]);
	} elseif ($tipoRequest == 'ATENDMODELOS') {

		$sql = "SELECT AtModId,AtModDescricao, AtTMoChave
		FROM AtendimentoModelo
        JOIN AtendimentoTipoModelo ON AtModTipoModelo = AtTMoId
        AND AtTMoChave IN ('ATESTADOMEDICO', 'ATESTADOMEDICOCOMCID', 
                        'DECLARACAOCOMPARECIMENTO', 'DECLARACAOCOMPARECIMENTOACOMPANHANTE', 
                        'RELATORIOMEDICO', 'DIGITACAOLIVRE')";
		$result = $conn->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];
		foreach($row as $item){
			array_push($array,[
				'id' => $item['AtModId'],
				'nome' => $item['AtModDescricao'],
				'chave' => $item['AtTMoChave']
			]);
		}
		echo json_encode($array);
             
    } elseif($tipoRequest == 'MODELOCONTEUDO'){
		$id = $_POST['id'];

		$sql = "SELECT TOP (1) AtModId,AtModConteudo
		FROM AtendimentoModelo
		WHERE AtModId = '$id'";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode([
			'id' => $row['AtModId'],
			'conteudo' => $row['AtModConteudo']
		]);
	} elseif($tipoRequest == 'SALVARDOCUMENTO'){        
        
        $idAtendimento = $_POST['idAtendimento'];		
		$profissional = $_POST['profissional'];
        $modelo = $_POST['modelo'];		
		$descricao = $_POST['descricao'];
		$cid = $_POST['cid'] == '' ? "NULL" : $_POST['cid'];
        
        $dataHora = date('Y-m-d H:i:s');

        $sql = "INSERT INTO AtendimentoDocumento(
            AtDocAtendimento, AtDocModelo, AtDocCid10, AtDocDescricao, AtDocDataHora, AtDocProfissional, AtDocUnidade)
            VALUES('$idAtendimento', '$modelo', $cid, '$descricao', '$dataHora', '$profissional', '$iUnidade')";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Incluir Documento',
			'menssagem' => 'Documento inserido com sucesso!!!'
		]);
	} elseif ($tipoRequest == 'DOCUMENTOS'){
		$atendimentoSessao = $_SESSION['atendimento']['atendimentoServicos'];

		$iAtendimento = $_POST['id'];

        $sql = "SELECT AtDocId, AtDocAtendimento, AtDocModelo, AtDocDescricao, AtDocDataHora, Cid10Codigo,
            AtDocProfissional,Profissional.ProfiNome, ProfiCbo, AtTMoNome
			FROM AtendimentoDocumento
            JOIN AtendimentoModelo ON AtDocModelo = AtModId
            JOIN AtendimentoTipoModelo ON AtModTipoModelo = AtTMoId
			JOIN Profissional ON AtDocProfissional = Profissional.ProfiId
			LEFT JOIN Cid10 ON AtDocCid10 = Cid10Id
			JOIN Profissao ON ProfiProfissao = Profissao.ProfiId
			WHERE AtDocAtendimento = $iAtendimento";

		$result = $conn->query($sql);
		$rowEncaminhamento = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($rowEncaminhamento as $item){

            $dataHr = explode(' ', $item['AtDocDataHora']);
            $data = $dataHr[0];

            $hora = explode('.', $dataHr[1]);
            $hora = $hora[0];

			array_push($array,[
				'id'=>$item['AtDocId'],
				'dataHora'=> mostraData($data) . ' ' . mostraHora($hora) ,
                'tipoDocumento' => $item['AtTMoNome'],
				'profissional'=>$item['ProfiNome'],
				'cbo'=>$item['ProfiCbo'],
				'cid10'=>$item['Cid10Codigo'] == ''? '--' : $item['Cid10Codigo']
			]);
		}
		
		echo json_encode($array);
	} elseif ($tipoRequest == 'EXCLUIRDOCUMENTO'){
		$id = $_POST['id'];
	
		$sql = "DELETE FROM AtendimentoDocumento
		WHERE AtDocId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Documento',
			'menssagem' => 'Documento excluído com sucesso!!!',
		]);
	} elseif ($tipoRequest == 'SETTIPOBUSCAMEDICAMENTO') {

		if ( $_POST['tipoBusca'] == 'MEDICAMENTO') {
			$_SESSION['tipoPesquisa'] = 'MEDICAMENTO' ;
		} elseif ($_POST['tipoBusca'] == 'SOLUCAO') {
			$_SESSION['tipoPesquisa'] = 'SOLUCAO' ;
		} elseif ($_POST['tipoBusca'] == 'SOLUCAODILUENTE') {
			$_SESSION['tipoPesquisa'] = 'SOLUCAODILUENTE' ;
		}
		
		echo json_encode([
			'status' =>'success'
		]);
		
		} elseif ($tipoRequest == 'PESQUISARPRODUTOS') {
		
		$categoria = $_POST['categoria'];
		$subCategoria = $_POST['subCategoria'];
		$nomeProduto = $_POST['nomeProduto'];
		
		if(!$categoria && $subCategoria){
			$sql = "SELECT ProduId, ProduCodigo, ProduNome, CategNome, SbCatNome, UnMedNome, UnMedId, TpFisNome
			FROM Produto	
			JOIN UnidadeMedida ON ProduUnidadeMedida = UnMedId
			JOIN TipoFiscal ON ProduTipoFiscal = TpFisId
			JOIN Categoria ON ProduCategoria = CategId
			JOIN SubCategoria ON ProduSubCategoria = SbCatId
			WHERE ProduSubCategoria = $subCategoria
			AND ProduNome like '%$nomeProduto%'
			AND ProduEmpresa = $iEmpresa";
		}elseif(!$subCategoria && $categoria){
			$sql = "SELECT ProduId, ProduCodigo, ProduNome, CategNome, SbCatNome, UnMedNome, UnMedId, TpFisNome 
			FROM Produto
			JOIN UnidadeMedida ON ProduUnidadeMedida = UnMedId
			JOIN TipoFiscal ON ProduTipoFiscal = TpFisId
			JOIN Categoria ON ProduCategoria = CategId
			JOIN SubCategoria ON ProduSubCategoria = SbCatId
			WHERE ProduCategoria = $categoria
			AND ProduNome like '%$nomeProduto%'
			AND ProduEmpresa = $iEmpresa";
		}elseif ((!$categoria) && (!$subCategoria)) {
			$sql = "SELECT ProduId, ProduCodigo, ProduNome, CategNome, SbCatNome, UnMedNome, UnMedId, TpFisNome 
			FROM Produto
			JOIN UnidadeMedida ON ProduUnidadeMedida = UnMedId
			JOIN TipoFiscal ON ProduTipoFiscal = TpFisId
			JOIN Categoria ON ProduCategoria = CategId
			JOIN SubCategoria ON ProduSubCategoria = SbCatId
			WHERE ProduNome like '%$nomeProduto%'
			AND ProduEmpresa = $iEmpresa";
		}else{
			$sql = "SELECT ProduId, ProduCodigo, ProduNome, CategNome, SbCatNome, UnMedNome, UnMedId,  TpFisNome 
			FROM Produto
			JOIN UnidadeMedida ON ProduUnidadeMedida = UnMedId
			JOIN TipoFiscal ON ProduTipoFiscal = TpFisId
			JOIN Categoria ON ProduCategoria = CategId
			JOIN SubCategoria ON ProduSubCategoria = SbCatId
			WHERE (ProduCategoria = $categoria OR ProduSubCategoria = $subCategoria)
			AND ProduNome like '%$nomeProduto%'
			AND ProduEmpresa = $iEmpresa";			
		}
		$result = $conn->query($sql);
		$rowProdutos = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		
		foreach ($rowProdutos as $key => $item ) {
			
			array_push($array,[
				'item' => $key + 1,
				'id' => $item['ProduId'],
				'produCodigo'=>$item['ProduCodigo'],
				'descricao'=> $item['ProduNome'],
				'categoria' => $item['CategNome'],
				'subCategoria'=>$item['SbCatNome'],
				'unidade'=>$item['UnMedNome'],
				'unidadeId' => $item['UnMedId'],
				'classificacao'=>$item['TpFisNome'],
			]);
		
		}
		
		echo json_encode($array);
	} elseif ($tipoRequest == 'SALVAROBSERVACAOENTRADA') {
	
		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];
		$historiaEntrada = $_POST['historiaEntrada'];
		$cid10 = $_POST['cid10'];
		$servico = $_POST['servico'];
	
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');
	
		$sql = "SELECT AtOEnId 
		FROM AtendimentoObservacaoEntrada
		WHERE AtOEnAtendimento = $iAtendimentoId
		AND AtOEnUnidade = $iUnidade";	
		$result = $conn->query($sql);
		$resultadoBusca = $result->fetch(PDO::FETCH_ASSOC);
	
	
	
		if ($resultadoBusca == false) {
			$sql = "INSERT INTO AtendimentoObservacaoEntrada
			(AtOEnAtendimento, AtOEnDataInicio, AtOEnHoraInicio, AtOEnProfissional, AtOEnHistoria, AtOEnCid10, AtOEnProcedimento, AtOEnUnidade)
			VALUES('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$historiaEntrada', '$cid10', '$servico', '$iUnidade' )";	
			
		} else {
			$sql = "UPDATE AtendimentoObservacaoEntrada SET
			AtOEnDataInicio = '$dataInicio', 
			AtOEnHoraInicio = '$horaInicio', 
			AtOEnProfissional = '$profissional', 
			AtOEnHistoria = '$historiaEntrada', 
			AtOEnCid10 = '$cid10', 
			AtOEnProcedimento = '$servico', 
			AtOEnUnidade = '$iUnidade'
			WHERE AtOEnId = $$resultadoBusca[AtOEnId]";			
		}
	
		$result = $conn->query($sql);
	
		echo json_encode([
			'titulo' => 'Observação Hospitalar',
			'status' => 'success',
			'menssagem' => 'Observacão salva com Sucesso!'
		]);
		
	} elseif ($tipoRequest == 'SALVARINTERNACAOENTRADA') {
	
		$iAtendimentoId = $_POST['iAtendimentoId'];
		$profissional = $_POST['profissional'];
		$sinaisESintomasClinicos = $_POST['sinaisESintomasClinicos'];
		$justificativaInternacao = $_POST['justificativaInternacao'];
		$resultProvaDiagnostica = $_POST['resultProvaDiagnostica'];
		$cid10 = $_POST['cid10'];
		$servico = $_POST['servico'];
	
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');
	
		$sql = "SELECT AtIEnId 
		FROM AtendimentoInternacaoEntrada
		WHERE AtIEnAtendimento = $iAtendimentoId
		AND AtIEnUnidade = $iUnidade";	
		$result = $conn->query($sql);
		$resultadoBusca = $result->fetch(PDO::FETCH_ASSOC);
	
	
	
		if ($resultadoBusca == false) {
			$sql = "INSERT INTO AtendimentoInternacaoEntrada
			(AtIEnAtendimento, AtIEnDataInicio, AtIEnHoraInicio, AtIEnProfissional, AtIEnPrincipaisSinais, AtIEnJustificativa, AtIEnPrincipaisResultados, AtIEnCid10, AtIEnProcedimento, AtIEnUnidade)
			VALUES('$iAtendimentoId', '$dataInicio', '$horaInicio', '$profissional', '$sinaisESintomasClinicos','$justificativaInternacao','$resultProvaDiagnostica', '$cid10', '$servico', '$iUnidade' )";	
			
		} else {
			$sql = "UPDATE AtendimentoInternacaoEntrada SET
			AtIEnDataInicio = '$dataInicio', 
			AtIEnHoraInicio = '$horaInicio', 
			AtIEnProfissional = '$profissional', 
			AtIEnPrincipaisSinais = '$sinaisESintomasClinicos', 
			AtIEnJustificativa = '$justificativaInternacao', 
			AtIEnPrincipaisResultados = '$resultProvaDiagnostica', 
			AtIEnCid10 = '$cid10', 
			AtIEnProcedimento = '$servico', 
			AtIEnUnidade = '$iUnidade'
			WHERE AtIEnId = $$resultadoBusca[AtIEnId]";			
		}
	
		$result = $conn->query($sql);
	
		echo json_encode([
			'titulo' => 'Internação Hospitalar',
			'status' => 'success',
			'menssagem' => 'Internação salva com Sucesso!'
		]);
		
	} elseif ($tipoRequest == 'GETSINAISVITAIS') {

		$iAtendimentoTriagemId = $_POST['id'];

		$sql = "SELECT AtTriPressaoSistolica, AtTriPressaoDiatolica, AtTriFreqCardiaca, AtTriFreqRespiratoria, AtTriTempAXI,
		AtTriSPO, AtTriHGT, AtTriAlergia, AtTriDiabetes, AtTriHipertensao, AtTriNeoplasia, AtTriUsoMedicamento, AtTriAlergiaDescricao,
		AtTriDiabetesDescricao, AtTriHipertensaoDescricao, AtTriNeoplasiaDescricao, AtTriUsoMedicamentoDescricao
		FROM AtendimentoTriagem
		WHERE AtTriId = " . $iAtendimentoTriagemId ;
		$result = $conn->query($sql);
		$rowTriagem = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($rowTriagem);

	} elseif ($tipoRequest == 'INCLUIRANOTACAOTECENFERMAGEM') {

		$justificativaAnotacao = $_POST['justificativaAnotacao'] == "" ? null : $_POST['justificativaAnotacao'];
		$peso = $_POST['peso'] == "" ? 0 : gravaValor($_POST['peso']);
		$anotacao = $_POST['anotacao'];
		$inputSistolica = $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'];
		$inputDiatolica = $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'];
		$inputCardiaca = $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'];
		$inputRespiratoria = $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'];
		$inputTemperatura = $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'];
		$inputSPO = $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'];
		$inputHGT = $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'];
		$profissional = $_POST['profissional'] == "" ? null : $_POST['profissional'];

		$inputPrevisaoAlta = $_POST['inputPrevisaoAlta'] == '' ? 'null' : "'" . $_POST['inputPrevisaoAlta'] . "'";;
		$inputTipoInternacao = $_POST['inputTipoInternacao'];
		$inputEspLeito = $_POST['inputEspLeito'];
		$inputAla = $_POST['inputAla'];
		$inputQuarto = $_POST['inputQuarto'];
		$inputLeito = $_POST['inputLeito'];

		$inputAlergia = $_POST['inputAlergia'];
		$inputAlergiaDescricao = $_POST['inputAlergiaDescricao'];
		$inputDiabetes = $_POST['inputDiabetes'];
		$inputDiabetesDescricao = $_POST['inputDiabetesDescricao'];
		$inputHipertensao = $_POST['inputHipertensao'];
		$inputHipertensaoDescricao = $_POST['inputHipertensaoDescricao'];
		$inputNeoplasia = $_POST['inputNeoplasia'];
		$inputNeoplasiaDescricao = $_POST['inputNeoplasiaDescricao'];
		$inputUsoMedicamento = $_POST['inputUsoMedicamento'];
		$inputUsoMedicamentoDescricao = $_POST['inputUsoMedicamentoDescricao'];

		$tipo = $_POST['tipo'];

		$dataHoraAtual = date('Y-m-d H:i:s');
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		if (isset($tipo) && $tipo == 'INSERT') {

			$iAtendimentoId = $_POST['iAtendimentoId'];

			$sql = "INSERT INTO  EnfermagemAnotacaoTecnico(EnAnTAtendimento,EnAnTDataInicio,EnAnTHoraInicio, EnAnTPrevisaoAlta ,EnAnTTipoInternacao , EnAnTEspecialidadeLeito , EnAnTAla , EnAnTQuarto , EnAnTLeito ,
														EnAnTProfissional,EnAnTPas,EnAnTPad,EnAnTFreqCardiaca,EnAnTFreqRespiratoria,
														EnAnTTemperatura,EnAnTSPO,EnAnTHGT, EnAnTAlergia, EnAnTAlergiaDescricao, EnAnTDiabetes, EnAnTDiabetesDescricao, EnAnTHipertensao, 
														EnAnTHipertensaoDescricao, EnAnTNeoplasia, EnAnTNeoplasiaDescricao, EnAnTUsoMedicamento, EnAnTUsoMedicamentoDescricao, 
														EnAnTDataHora,EnAnTJustificativaLancRetroativo,EnAnTPeso,EnAnTAnotacao,EnAnTEditavel,EnAnTUnidade)
			VALUES ('$iAtendimentoId', '$dataInicio', '$horaInicio',$inputPrevisaoAlta,	'$inputTipoInternacao',	'$inputEspLeito', '$inputAla','$inputQuarto',	'$inputLeito',
			 		'$profissional', '$inputSistolica', '$inputDiatolica', '$inputCardiaca', '$inputRespiratoria',
					'$inputTemperatura', '$inputSPO', '$inputHGT', '$inputAlergia', '$inputAlergiaDescricao', '$inputDiabetes', '$inputDiabetesDescricao', '$inputHipertensao', '$inputHipertensaoDescricao', 
					'$inputNeoplasia', '$inputNeoplasiaDescricao', '$inputUsoMedicamento', '$inputUsoMedicamentoDescricao',  '$dataHoraAtual', '$justificativaAnotacao', '$peso', '$anotacao', 1, '$iUnidade')";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Incluir Anotação',
				'menssagem' => 'Anotação inserida com sucesso!!!'
			]);		
			
		} else {

			$idAnotacao = $_POST['idAnotacao'];

			$sql = "UPDATE EnfermagemAnotacaoTecnico SET
			EnAnTProfissional = '$profissional',
			EnAnTPrevisaoAlta = $inputPrevisaoAlta,
			EnAnTTipoInternacao = '$inputTipoInternacao',
			EnAnTEspecialidadeLeito = '$inputEspLeito',
			EnAnTAla = '$inputAla',
			EnAnTQuarto = '$inputQuarto',
			EnAnTLeito = '$inputLeito',
			EnAnTPas = '$inputSistolica',
			EnAnTPad = '$inputDiatolica',
			EnAnTFreqCardiaca = '$inputCardiaca',
			EnAnTFreqRespiratoria = '$inputRespiratoria',
			EnAnTTemperatura = '$inputTemperatura',
			EnAnTSPO = '$inputSPO',
			EnAnTHGT = '$inputHGT',
			EnAnTAlergia = '$inputAlergia', 
			EnAnTAlergiaDescricao = '$inputAlergiaDescricao', 
			EnAnTDiabetes = '$inputDiabetes', 
			EnAnTDiabetesDescricao = '$inputDiabetesDescricao', 
			EnAnTHipertensao = '$inputHipertensao', 
			EnAnTHipertensaoDescricao = '$inputHipertensaoDescricao',
			EnAnTNeoplasia = '$inputNeoplasia', 
			EnAnTNeoplasiaDescricao = '$inputNeoplasiaDescricao', 
			EnAnTUsoMedicamento = '$inputUsoMedicamento', 
			EnAnTUsoMedicamentoDescricao = '$inputUsoMedicamentoDescricao',
			EnAnTDataHora = '$dataHoraAtual',
			EnAnTJustificativaLancRetroativo = '$justificativaAnotacao',
			EnAnTPeso = '$peso',
			EnAnTAnotacao = '$anotacao'
			WHERE EnAnTId = '$idAnotacao'";

			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Alterar Anotação',
				'menssagem' => 'Anotação alterada com sucesso!!!'
			]);

		}

	} elseif ($tipoRequest == 'SALVARANOTACAOTECENFERMAGEM') {


		$iAtendimentoId = $_POST['iAtendimentoId'];
		$dataFim = date('Y-m-d'); 
		$horaFim =date('H:i:s');


		$sql = "UPDATE EnfermagemAnotacaoTecnico SET
			EnAnTDataFim = '$dataFim',
			EnAnTHoraFim = '$horaFim',
			EnAnTEditavel = 0	
			WHERE EnAnTAtendimento = '$iAtendimentoId'
			AND EnAnTProfissional = '$usuarioId'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Anotação Técnico de Enfermagem',
			'menssagem' => 'Dados Salvos com Sucesso!!!'
		]);

		
	} elseif ($tipoRequest == 'GETANOTACOESTECENFERMAGEM') {

		$iAtendimento = $_POST['id'];
	
		$sql = "SELECT *
			FROM EnfermagemAnotacaoTecnico
			WHERE EnAnTAtendimento = $iAtendimento";

		$result = $conn->query($sql);
		$anotacoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($anotacoes as $key => $item){

			array_push($array,[
				'item' => ($key + 1),
				'id'=>$item['EnAnTId'],
				'dataHora'=> mostraData($item['EnAnTDataInicio']) . ' ' . mostraHora($item['EnAnTHoraInicio']),
				'justificativa' => substr($item['EnAnTJustificativaLancRetroativo'], 0, 100) . '...',
				'anotacao'=> substr($item['EnAnTAnotacao'], 0, 100) . '...',
				'justificativaCompleta' => $item['EnAnTJustificativaLancRetroativo'],
				'anotacaoCompleta' => $item['EnAnTAnotacao'],
				'peso' => $item['EnAnTPeso'],
				'editavel' => $item['EnAnTEditavel']
			]);
		}
		
		echo json_encode($array);
		
	} elseif ($tipoRequest == 'GETANOTACAOTECENFERMAGEM') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM EnfermagemAnotacaoTecnico
		WHERE EnAnTId = $id
		AND EnAnTUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
	} elseif ($tipoRequest == 'DELETEANOTACAOTECENFERMAGEM') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM EnfermagemAnotacaoTecnico
		WHERE EnAnTId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Anotação Técnico de Enfermagem',
			'menssagem' => 'Anotação excluída!!!',
		]);
		
	} elseif ($tipoRequest == 'INCLUIREVOLUCAOENFERMAGEM') {

		$justificativaEvolucao = $_POST['justificativaEvolucao'] == "" ? null : $_POST['justificativaEvolucao'];
		$evolucaoEnfermagem = $_POST['evolucaoEnfermagem'];

		$inputPrevisaoAlta = $_POST['inputPrevisaoAlta'] == '' ? 'null' : "'" . $_POST['inputPrevisaoAlta'] . "'";;
		$inputTipoInternacao = $_POST['inputTipoInternacao'];
		$inputEspLeito = $_POST['inputEspLeito'];
		$inputAla = $_POST['inputAla'];
		$inputQuarto = $_POST['inputQuarto'];
		$inputLeito = $_POST['inputLeito'];

		$inputSistolica = $_POST['inputSistolica'] == "" ? null : $_POST['inputSistolica'];
		$inputDiatolica = $_POST['inputDiatolica'] == "" ? null : $_POST['inputDiatolica'];
		$inputCardiaca = $_POST['inputCardiaca'] == "" ? null : $_POST['inputCardiaca'];
		$inputRespiratoria = $_POST['inputRespiratoria'] == "" ? null : $_POST['inputRespiratoria'];
		$inputTemperatura = $_POST['inputTemperatura'] == "" ? null : $_POST['inputTemperatura'];
		$inputSPO = $_POST['inputSPO'] == "" ? null : $_POST['inputSPO'];
		$inputHGT = $_POST['inputHGT'] == "" ? null : $_POST['inputHGT'];
		$inputPeso = $_POST['inputPeso'] == "" ? 0 : gravaValor($_POST['inputPeso']);

		$inputAlergia = $_POST['inputAlergia'];
		$inputAlergiaDescricao = $_POST['inputAlergiaDescricao'];
		$inputDiabetes = $_POST['inputDiabetes'];
		$inputDiabetesDescricao = $_POST['inputDiabetesDescricao'];
		$inputHipertensao = $_POST['inputHipertensao'];
		$inputHipertensaoDescricao = $_POST['inputHipertensaoDescricao'];
		$inputNeoplasia = $_POST['inputNeoplasia'];
		$inputNeoplasiaDescricao = $_POST['inputNeoplasiaDescricao'];
		$inputUsoMedicamento = $_POST['inputUsoMedicamento'];
		$inputUsoMedicamentoDescricao = $_POST['inputUsoMedicamentoDescricao'];

		$tipo = $_POST['tipo'];

		$dataHoraAtual = date('Y-m-d H:i:s');
		$dataInicio = date('Y-m-d'); 
		$horaInicio =date('H:i:s');

		if (isset($tipo) && $tipo == 'INSERT') {

			$iAtendimentoId = $_POST['iAtendimentoId'];

			$sql = "INSERT INTO  EnfermagemEvolucao(EnEvoAtendimento,EnEvoDataInicio,EnEvoHoraInicio, EnEvoPrevisaoAlta, EnEvoTipoInternacao, EnEvoEspecialidadeLeito, EnEvoAla, EnEvoQuarto, EnEvoLeito,
														EnEvoProfissional,EnEvoPas,EnEvoPad,EnEvoFreqCardiaca,EnEvoFreqRespiratoria,
														EnEvoTemperatura,EnEvoSPO,EnEvoHGT, EnEvoPeso, EnEvoAlergia, EnEvoAlergiaDescricao, EnEvoDiabetes, EnEvoDiabetesDescricao, EnEvoHipertensao, 
														EnEvoHipertensaoDescricao, EnEvoNeoplasia, EnEvoNeoplasiaDescricao, EnEvoUsoMedicamento, EnEvoUsoMedicamentoDescricao,
														EnEvoDataHora,EnEvoJustificativaLancRetroativo,EnEvoEvolucao,EnEvoEditavel,EnEvoUnidade)

			VALUES ('$iAtendimentoId', '$dataInicio', '$horaInicio',$inputPrevisaoAlta,	'$inputTipoInternacao',	'$inputEspLeito', '$inputAla','$inputQuarto',	'$inputLeito',
			 		'$usuarioId', '$inputSistolica', '$inputDiatolica', '$inputCardiaca', '$inputRespiratoria',
					'$inputTemperatura', '$inputSPO', '$inputHGT', '$inputPeso' , '$inputAlergia', '$inputAlergiaDescricao', '$inputDiabetes', '$inputDiabetesDescricao', '$inputHipertensao', 
					'$inputHipertensaoDescricao', '$inputNeoplasia', '$inputNeoplasiaDescricao', '$inputUsoMedicamento', '$inputUsoMedicamentoDescricao',  '$dataHoraAtual', '$justificativaEvolucao', '$evolucaoEnfermagem', 1, '$iUnidade')";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Incluir Evolução',
				'menssagem' => 'Evolução inserida com sucesso!!!'
			]);		
			
		} else {

			$idEvolucao = $_POST['idEvolucao'];

			$sql = "UPDATE EnfermagemEvolucao SET

			EnEvoPrevisaoAlta = $inputPrevisaoAlta,
			EnEvoTipoInternacao = '$inputTipoInternacao',
			EnEvoEspecialidadeLeito = '$inputEspLeito',
			EnEvoAla = '$inputAla',
			EnEvoQuarto = '$inputQuarto',
			EnEvoLeito = '$inputLeito',

			EnEvoProfissional = '$usuarioId',
			EnEvoPas = '$inputSistolica',
			EnEvoPad = '$inputDiatolica',
			EnEvoFreqCardiaca = '$inputCardiaca',
			EnEvoFreqRespiratoria = '$inputRespiratoria',
			EnEvoTemperatura = '$inputTemperatura',
			EnEvoSPO = '$inputSPO',
			EnEvoHGT = '$inputHGT',
			EnEvoPeso = '$inputPeso',
			EnEvoAlergia = '$inputAlergia', 
			EnEvoAlergiaDescricao = '$inputAlergiaDescricao', 
			EnEvoDiabetes = '$inputDiabetes', 
			EnEvoDiabetesDescricao = '$inputDiabetesDescricao', 
			EnEvoHipertensao = '$inputHipertensao', 
			EnEvoHipertensaoDescricao = '$inputHipertensaoDescricao',
			EnEvoNeoplasia = '$inputNeoplasia', 
			EnEvoNeoplasiaDescricao = '$inputNeoplasiaDescricao', 
			EnEvoUsoMedicamento = '$inputUsoMedicamento', 
			EnEvoUsoMedicamentoDescricao = '$inputUsoMedicamentoDescricao',
			EnEvoDataHora = '$dataHoraAtual',
			EnEvoJustificativaLancRetroativo = '$justificativaEvolucao',
			EnEvoEvolucao = '$evolucaoEnfermagem'
			WHERE EnEvoId = '$idEvolucao'";

			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Alterar Evolução',
				'menssagem' => 'Evolução alterada com sucesso!!!'
			]);

		}

	} elseif ($tipoRequest == 'SALVAREVOLUCAOENFERMAGEM') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$dataFim = date('Y-m-d'); 
		$horaFim =date('H:i:s');

		$sql = "UPDATE EnfermagemEvolucao SET
			EnEvoDataFim = '$dataFim',
			EnEvoHoraFim = '$horaFim',
			EnEvoEditavel = 0	
			WHERE EnEvoAtendimento = '$iAtendimentoId'
			AND EnEvoProfissional = '$usuarioId'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Anotação Técnico de Enfermagem',
			'menssagem' => 'Dados Salvos com Sucesso!!!'
		]);

		
	} elseif ($tipoRequest == 'GETEVOLUCOESENFERMAGEM') {

		$iAtendimento = $_POST['id'];
	
		$sql = "SELECT *
			FROM EnfermagemEvolucao
			WHERE EnEvoAtendimento = $iAtendimento";

		$result = $conn->query($sql);
		$evolucoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($evolucoes as $key => $item){

			array_push($array,[
				'item' => ($key + 1),
				'id'=>$item['EnEvoId'],
				'dataHora'=> mostraData($item['EnEvoDataInicio']) . ' ' . mostraHora($item['EnEvoHoraInicio']),
				'justificativa' => substr($item['EnEvoJustificativaLancRetroativo'], 0, 100) . '...',
				'evolucao'=> substr($item['EnEvoEvolucao'], 0, 100) . '...',
				'justificativaCompleta' => $item['EnEvoJustificativaLancRetroativo'],
				'evolucaoCompleta' => $item['EnEvoEvolucao'],
				'editavel' => $item['EnEvoEditavel']
			]);
		}
		
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'GETEVOLUCAOENFERMAGEM') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM EnfermagemEvolucao
		WHERE EnEvoId = $id
		AND EnEvoUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
	} elseif ($tipoRequest == 'DELETEEVOLUCAOENFERMAGEM') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM EnfermagemEvolucao
		WHERE EnEvoId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Evolução de Enfermagem',
			'menssagem' => 'Evolução excluída!!!',
		]);
		
	} elseif ($tipoRequest == 'GETANOTACOESTECENFERMAGEMRN') {

		$iAtendimento = $_POST['id'];
	
		$sql = "SELECT *
			FROM EnfermagemAnotacaoTecnicoRN
			WHERE EnAnTAtendimento = $iAtendimento";

		$result = $conn->query($sql);
		$anotacoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($anotacoes as $key => $item){

			array_push($array,[
				'item' => ($key + 1),
				'id'=>$item['EnAnTId'],
				'dataHora'=> mostraData($item['EnAnTDataInicio']) . ' ' . mostraHora($item['EnAnTHoraInicio']),
				'fc' => $item['EnAnTFreqCardiaca'],
				'fr' => $item['EnAnTFreqRespiratoria'],
				'temperatura' => $item['EnAnTTemperatura'],
				'spo' => $item['EnAnTSPO'],
				'peso' => mostraValor($item['EnAnTPeso']),
				'anotacao' => $item['EnAnTAnotacaoEnfermagem'],
				'editavel' => $item['EnAnTEditavel']
			]);
		}
		
		echo json_encode($array);
		
	} elseif ($tipoRequest == 'INCLUIRANOTACAOTECENFERMAGEMRN') {

		$tipo = $_POST['tipo'];

		$iAtendimentoId = $_POST['iAtendimentoId'];

		$inputDataInicio = date('Y-m-d');
		$inputInicio = date('H:i:s');
		$inputPrevisaoAlta = $_POST['inputPrevisaoAlta'] == '' ? 'null' : "'" . $_POST['inputPrevisaoAlta'] . "'";;
		$inputTipoInternacao = $_POST['inputTipoInternacao'];
		$inputEspLeito = $_POST['inputEspLeito'];
		$inputAla = $_POST['inputAla'];
		$inputQuarto = $_POST['inputQuarto'];
		$inputLeito = $_POST['inputLeito'];

		$profissional = $_POST['profissional'];

		$inputNomeMae = $_POST['inputNomeMae'];
		$inputDtNascimento = $_POST['inputDtNascimento'] == '' ? 'null' : "'" . $_POST['inputDtNascimento'] . "'";
		$inputHrNascimento = $_POST['inputHrNascimento'] == '' ? 'null' : "'" . $_POST['inputHrNascimento'] . "'";
		$cmbSexo = $_POST['cmbSexo'];
		$cmbChoroPresente = $_POST['cmbChoroPresente']== '' ? 'null' : "'" . $_POST['cmbChoroPresente'] . "'";
		$cmbSuccao = $_POST['cmbSuccao']== '' ? 'null' : "'" . $_POST['cmbSuccao'] . "'";
		
		$inputAmamentacao = $_POST['inputAmamentacao'] == '' ? 'null' : $_POST['inputAmamentacao'];
		$inputAmamentacaoDescricao = $_POST['inputAmamentacaoDescricao'];

		$inputCardiacaM = $_POST['inputCardiacaM'];
		$inputRespiratoriaM = $_POST['inputRespiratoriaM'];
		$inputTemperaturaM = $_POST['inputTemperaturaM'];
		$inputSPOM = $_POST['inputSPOM'];
		$inputHGTM = $_POST['inputHGTM'];
		$inputPesoM = $_POST['inputPesoM'] == '' ? 'null' : gravaValor($_POST['inputPesoM']);

		$checkAtividadeHipoativo = $_POST['checkAtividadeHipoativo'];
		$checkAtividadeSonolento = $_POST['checkAtividadeSonolento'];
		$checkAtividadeAtivo = $_POST['checkAtividadeAtivo'];
		$checkAtividadeChoroso = $_POST['checkAtividadeChoroso'];
		$checkAtividadeGemente = $_POST['checkAtividadeGemente'];
		$inputAtividadeDescricao = $_POST['inputAtividadeDescricao'];

		$checkColoracaoCorado = $_POST['checkColoracaoCorado'];
		$checkColoracaoHipocorado = $_POST['checkColoracaoHipocorado'];
		$checkColoracaoCianotico = $_POST['checkColoracaoCianotico'];
		$checkColoracaoIcterico = $_POST['checkColoracaoIcterico'];
		$checkColoracaoPletorico = $_POST['checkColoracaoPletorico'];
		$inputColoracaoDescricao = $_POST['inputColoracaoDescricao'];

		$cmbHidratacao = $_POST['cmbHidratacao']  == '' ? 'null' : "'" . $_POST['cmbHidratacao'] . "'";
		$cmbAbdome = $_POST['cmbAbdome'] == '' ? 'null' : $_POST['cmbAbdome'];

		$inputPele = $_POST['inputPele'];
		$inputPeleDescricao = $_POST['inputPeleDescricao'];

		$inputPadraoRespiratorio = $_POST['inputPadraoRespiratorio'];
		$inputPadraoRespDescricao = $_POST['inputPadraoRespDescricao'];

		$checkCotoLimpoSeco = $_POST['checkCotoLimpoSeco'];
		$checkCotoGelatinoso = $_POST['checkCotoGelatinoso'];
		$checkCotoMumificado = $_POST['checkCotoMumificado'];
		$checkCotoUmido = $_POST['checkCotoUmido'];
		$checkCotoSujo = $_POST['checkCotoSujo'];
		$checkCotoFetido = $_POST['checkCotoFetido'];
		$inputCotoDescricao = $_POST['inputCotoDescricao'];

		$inputAnotacoesDescricao = $_POST['inputAnotacoesDescricao'];

		if (isset($tipo) && $tipo == 'INSERT') {

			$iAtendimentoId = $_POST['iAtendimentoId'];

			$sql = "INSERT INTO  EnfermagemAnotacaoTecnicoRN
			(EnAnTAtendimento,EnAnTDataInicio,EnAnTHoraInicio,EnAnTPrevisaoAlta,EnAnTTipoInternacao,EnAnTEspecialidadeLeito,
			EnAnTAla,EnAnTQuarto,EnAnTLeito,EnAnTProfissional,EnAnTNomeMae,	EnAnTDataNascimento,EnAnTHoraNascimento,
			EnAnTSexo,EnAnTChoroPresente,EnAnTSuccao,EnAnTAmamentacao,EnAnTAmamentacaoDescricao,EnAnTFreqCardiaca,
			EnAnTFreqRespiratoria,EnAnTTemperatura,EnAnTSPO,EnAnTHGT,EnAnTPeso,	EnAnTAtividadeHipoativo,EnAnTAtividadeSonolento,
			EnAnTAtividadeAtivo,EnAnTAtividadeChoroso,EnAnTAtividadeGemente,EnAnTAtividadeDescricao,EnAnTColoracaoCorado,
			EnAnTColoracaoHipoCorado,EnAnTColoracaoCianotico,EnAnTColoracaoIcterico,EnAnTColoracaoPletorico,EnAnTColoracaoDescricao,
			EnAnTHidratacao,EnAnTAbdome,EnAnTPele,EnAnTPeleDescricao,EnAnTPadraoRespiratorio,EnAnTPadraoRespiratorioDescricao,
			EnAnTCotoLimpoSeco,	EnAnTCotoGelatinoso,EnAnTCotoMumificado,EnAnTCotoUmido,	EnAnTCotoSujo,EnAnTCotoFetido,
			EnAnTCotoDescricao,	EnAnTAnotacaoEnfermagem,EnAnTEditavel,	EnAnTUnidade)
			VALUES 
			('$iAtendimentoId', '$inputDataInicio', '$inputInicio',	$inputPrevisaoAlta,	'$inputTipoInternacao',	'$inputEspLeito', 
			'$inputAla','$inputQuarto',	'$inputLeito',	'$profissional','$inputNomeMae',$inputDtNascimento,	$inputHrNascimento,
			'$cmbSexo',	$cmbChoroPresente,$cmbSuccao,	$inputAmamentacao,	'$inputAmamentacaoDescricao','$inputCardiacaM',
			'$inputRespiratoriaM','$inputTemperaturaM','$inputSPOM','$inputHGTM',$inputPesoM,$checkAtividadeHipoativo,$checkAtividadeSonolento,
			$checkAtividadeAtivo,$checkAtividadeChoroso,$checkAtividadeGemente,	'$inputAtividadeDescricao',	$checkColoracaoCorado,
			$checkColoracaoHipocorado,$checkColoracaoCianotico,	$checkColoracaoIcterico,$checkColoracaoPletorico,'$inputColoracaoDescricao',
			$cmbHidratacao,	$cmbAbdome,	'$inputPele','$inputPeleDescricao',	'$inputPadraoRespiratorio',	'$inputPadraoRespDescricao',
			$checkCotoLimpoSeco,$checkCotoGelatinoso,$checkCotoMumificado,$checkCotoUmido,$checkCotoSujo,$checkCotoFetido,
			'$inputCotoDescricao',	'$inputAnotacoesDescricao', 1, '$iUnidade')";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Incluir Anotação',
				'menssagem' => 'Anotação inserida com sucesso!!!'
			]);		
			
		} else {

			$idAnotacao = $_POST['idAnotacao'];

			$sql = "UPDATE EnfermagemAnotacaoTecnicoRN SET
			EnAnTDataInicio = '$inputDataInicio',
			EnAnTHoraInicio = '$inputInicio',
			EnAnTPrevisaoAlta = $inputPrevisaoAlta,
			EnAnTTipoInternacao = '$inputTipoInternacao',
			EnAnTEspecialidadeLeito = '$inputEspLeito',
			EnAnTAla = '$inputAla',
			EnAnTQuarto = '$inputQuarto',
			EnAnTLeito = '$inputLeito',
			EnAnTProfissional = '$profissional',
			EnAnTNomeMae = '$inputNomeMae',
			EnAnTDataNascimento = $inputDtNascimento,
			EnAnTHoraNascimento = $inputHrNascimento,
			EnAnTSexo = '$cmbSexo',
			EnAnTChoroPresente = $cmbChoroPresente,
			EnAnTSuccao = $cmbSuccao,
			EnAnTAmamentacao = $inputAmamentacao,
			EnAnTAmamentacaoDescricao = '$inputAmamentacaoDescricao',
			EnAnTFreqCardiaca = '$inputCardiacaM',
			EnAnTFreqRespiratoria = '$inputRespiratoriaM',
			EnAnTTemperatura = '$inputTemperaturaM',
			EnAnTSPO = '$inputSPOM',
			EnAnTHGT = '$inputHGTM',
			EnAnTPeso = $inputPesoM,
			EnAnTAtividadeHipoativo = $checkAtividadeHipoativo,
			EnAnTAtividadeSonolento = $checkAtividadeSonolento,
			EnAnTAtividadeAtivo = $checkAtividadeAtivo,
			EnAnTAtividadeChoroso = $checkAtividadeChoroso,
			EnAnTAtividadeGemente = $checkAtividadeGemente,
			EnAnTAtividadeDescricao = '$inputAtividadeDescricao',
			EnAnTColoracaoCorado = $checkColoracaoCorado,
			EnAnTColoracaoHipoCorado = $checkColoracaoHipocorado,
			EnAnTColoracaoCianotico = $checkColoracaoCianotico,
			EnAnTColoracaoIcterico = $checkColoracaoIcterico,
			EnAnTColoracaoPletorico = $checkColoracaoPletorico,
			EnAnTColoracaoDescricao = '$inputColoracaoDescricao',
			EnAnTHidratacao = $cmbHidratacao,
			EnAnTAbdome = $cmbAbdome,
			EnAnTPele = '$inputPele',
			EnAnTPeleDescricao = '$inputPeleDescricao',
			EnAnTPadraoRespiratorio = '$inputPadraoRespiratorio',
			EnAnTPadraoRespiratorioDescricao = '$inputPadraoRespDescricao',
			EnAnTCotoLimpoSeco = $checkCotoLimpoSeco,
			EnAnTCotoGelatinoso = $checkCotoGelatinoso,
			EnAnTCotoMumificado = $checkCotoMumificado,
			EnAnTCotoUmido = $checkCotoUmido,
			EnAnTCotoSujo = $checkCotoSujo,
			EnAnTCotoFetido = $checkCotoFetido,
			EnAnTCotoDescricao = '$inputCotoDescricao',
			EnAnTAnotacaoEnfermagem = '$inputAnotacoesDescricao',
			EnAnTUnidade = '$iUnidade'
			WHERE EnAnTId = '$idAnotacao'";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Alterar Anotação',
				'menssagem' => 'Anotação alterada com sucesso!!!'
			]);

		}

	} elseif ($tipoRequest == 'SALVARANOTACAOTECENFERMAGEMRN') {

		$iAtendimentoId = $_POST['iAtendimentoId'];
		$dataFim = date('Y-m-d'); 
		$horaFim =date('H:i:s');

		$sql = "UPDATE EnfermagemAnotacaoTecnicoRN SET
		EnAnTDataFim = '$dataFim',
		EnAnTHoraFim = '$horaFim',
		EnAnTEditavel = 0	
		WHERE EnAnTAtendimento = '$iAtendimentoId'";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Anotação Técnico de Enfermagem RN',
			'menssagem' => 'Dados Salvos com Sucesso!!!'
		]);

		
	} elseif ($tipoRequest == 'DELETEANOTACAOTECENFERMAGEMRN') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM EnfermagemAnotacaoTecnicoRN
		WHERE EnAnTId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Anotação Técnico de Enfermagem RN',
			'menssagem' => 'Anotação excluída!!!',
		]);
		
	} elseif ($tipoRequest == 'GETANOTACAOTECENFERMAGEMRN') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM EnfermagemAnotacaoTecnicoRN
		WHERE EnAnTId = $id
		AND EnAnTUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
	} elseif ($tipoRequest == 'INCLUIREVOLUCAOADMISSAOPREPARTO') {

		$tipo = $_POST['tipo'];
		$idAdmissao = $_POST['idAdmissao'] == "" ? null : $_POST['idAdmissao'];
		$cmbRealizadoToque = $_POST['cmbRealizadoToque'] == "" ? null : $_POST['cmbRealizadoToque'];
		$inputDilatacao = $_POST['inputDilatacao'] == "" ? null : $_POST['inputDilatacao'];
		$inputApagamento = $_POST['inputApagamento'] == "" ? null : $_POST['inputApagamento'];
		$cmbApresentacao = $_POST['cmbApresentacao'] == "" ? null : $_POST['cmbApresentacao'];
		$cmbPlanoLee = $_POST['cmbPlanoLee'] == "" ? null : $_POST['cmbPlanoLee'];
		$inputLiquido = $_POST['inputLiquido'] == "" ? null : $_POST['inputLiquido'];
		$cmbMeconio = $_POST['cmbMeconio'] == "" ? null : $_POST['cmbMeconio'];

		$dataHoraAtual = date('Y-m-d H:i:s');

		if (isset($tipo) && $tipo == 'INSERT') {

			$sql = "INSERT INTO  EnfermagemAdmissaoPrePartoEvolucao(
				EnAPEAdmissaoPreParto, EnAPEDataHora, EnAPERealizadoToque, EnAPEDilatacao, EnAPEApagamento,
				EnAPEApresentacao, EnAPEPlano, EnAPELiquido, EnAPEMeconio, EnAPEEditavel, EnAPEUnidade )
			VALUES (
				'$idAdmissao', '$dataHoraAtual', '$cmbRealizadoToque', '$inputDilatacao','$inputApagamento', 
				'$cmbApresentacao', '$cmbPlanoLee', '$inputLiquido', '$cmbMeconio',  1, '$iUnidade')";
			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Incluir Evolução',
				'menssagem' => 'Evolução inserida com sucesso!!!'
			]);		
			
		} else {

			$idEvolucao = $_POST['idEvolucao'];

			$sql = "UPDATE EnfermagemAdmissaoPrePartoEvolucao SET
			EnAPEAdmissaoPreParto = '$idAdmissao', 
			EnAPEDataHora = '$dataHoraAtual', 
			EnAPERealizadoToque = '$cmbRealizadoToque', 
			EnAPEDilatacao = '$inputDilatacao', 
			EnAPEApagamento = '$inputApagamento',
			EnAPEApresentacao = '$cmbApresentacao', 
			EnAPEPlano = '$cmbPlanoLee', 
			EnAPELiquido = '$inputLiquido', 
			EnAPEMeconio = '$cmbMeconio'
			WHERE EnAPEId = '$idEvolucao'";

			$conn->query($sql);

			echo json_encode([
				'status' => 'success',
				'titulo' => 'Alterar Evolução',
				'menssagem' => 'Evolução alterada com sucesso!!!'
			]);

		}

	}elseif ($tipoRequest == 'GETADMISSOESPREPARTO') {

		$idAdmissao = $_POST['idAdmissao'] == "" ? null : $_POST['idAdmissao'];
	
		$sql = "SELECT *
			FROM EnfermagemAdmissaoPrePartoEvolucao
			WHERE EnAPEAdmissaoPreParto = $idAdmissao";

		$result = $conn->query($sql);
		$admissoes = $result->fetchAll(PDO::FETCH_ASSOC);

		$array = [];

		foreach($admissoes as $key => $item){

			$dataHora = explode(" ", $item['EnAPEDataHora']);

			array_push($array,[
				'item' => ($key + 1),
				'id'=>$item['EnAPEId'],
				'dataHora'=> mostraData($dataHora[0]) . ' ' . mostraHora($dataHora[1]),
				'dilatacao' => $item['EnAPEDilatacao'],
				'apagamento' => $item['EnAPEApagamento'],
				'planoLee' => $item['EnAPEPlano'],
				'liquido' => $item['EnAPELiquido'],
				'meconio' => $item['EnAPEMeconio'],
				'editavel' => $item['EnAPEEditavel']
			]);
		}
		
		echo json_encode($array);
		
	}elseif ($tipoRequest == 'DELETEADMISSAOPREPARTO') {

		$id = $_POST['id'];
	
		$sql = "DELETE FROM EnfermagemAdmissaoPrePartoEvolucao
		WHERE EnAPEId = $id";
		$conn->query($sql);

		echo json_encode([
			'status' => 'success',
			'titulo' => 'Evolução de Admissão',
			'menssagem' => 'Evolução excluída!!!',
		]);
		
	} elseif ($tipoRequest == 'GETADMISSAOEVOLUCAO') {

		$id = $_POST['id'];

		$sql = "SELECT *
		FROM EnfermagemAdmissaoPrePartoEvolucao
		WHERE EnAPEId = $id
		AND EnAPEUnidade = $iUnidade";
		$result = $conn->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		echo json_encode($row);
		
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
