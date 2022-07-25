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
			'paciente' => [],
			'responsavel' => [],
			'atendimento' => [],
			'triagem' => []
		];
	}

	if($tipoRequest == 'ATENDIMENTOS'){
		$acesso = $_POST['acesso'];
		$array = [];

		if($acesso == 'ATENDIMENTO'){
			$sql = "SELECT AtendId,AtendDataRegistro,ClienNome,AtModNome,AtendClassificacao,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,ProfiNome,SrVenNome
			FROM Atendimento
			JOIN AtendimentoXServico ON AtXSeAtendimento = AtendId
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN Profissional ON ProfiId = AtXSeProfissional
			JOIN ServicoVenda ON SrVenId = AtXSeServico";
			$result = $conn->query($sql);
			$row = $result->fetchAll(PDO::FETCH_ASSOC);
			
			$data = [];
			foreach($row as $item){
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
				array_push($data, [
					'data' => [
						mostraData($item['AtXSeData']),  // Data
						$item['AtXSeHorario'],  // Horario
						'04:20*',  // Espera
						'286*',  // Nº Registro
						'2568*',  // Prontuário
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
				'data' => $data,
				'acesso' => $acesso,
				'titulo' => 'Alterar Situação',
				'tipo' => 'success',
				'menssagem' => 'Situação alterada com sucesso!!!'
			];
		} elseif($acesso == 'PROFISSIONAL'){
			$sql = "SELECT AtendId,AtendDataRegistro,ClienNome,AtModNome,AtClaChave,AtClaNome,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId
			FROM AtendimentoXServico
			JOIN Atendimento ON AtendId = AtXSeAtendimento
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			WHERE SituaChave = 'EMESPERAVENDA' AND AtXSeProfissional != $usuarioId";
			$resultEspera = $conn->query($sql);
			$rowEspera = $resultEspera->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT AtendId,AtendDataRegistro,ClienNome,AtModNome,AtClaChave,AtClaNome,
			AtendObservacao,AtendSituacao,ClienCelular,ClienTelefone,ClienEmail,SituaNome,SituaChave,SituaCor,
			AtXSeData,AtXSeHorario,AtXSeAtendimentoLocal,AtEleId
			FROM AtendimentoXServico
			JOIN Atendimento ON AtendId = AtXSeAtendimento
			JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			JOIN Situacao ON SituaId = AtendSituacao
			JOIN Cliente ON ClienId = AtendCliente
			JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			WHERE SituaChave = 'ATENDIDOVENDA' AND AtXSeProfissional != $usuarioId";
			$resultAtendido = $conn->query($sql);
			$rowAtendido = $resultAtendido->fetchAll(PDO::FETCH_ASSOC);
			
			$espera = [];
			$atendido = [];

			foreach($rowEspera as $item){
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
						'04:20*',  // Espera
						'286*',  // Nº Registro
						'2568*',  // Prontuário
						$item['ClienNome'],  // Paciente
						'Procedimento**',  // Procedimento
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
						'04:20*',  // Espera
						'286*',  // Nº Registro
						'2568*',  // Prontuário
						$item['ClienNome'],  // Paciente
						'Procedimento**',  // Procedimento
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
	
		echo json_encode($row);
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

		$paciente = [
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
		$atendimentoSessao['paciente'] = $paciente;
		$_SESSION['atendimentoNovo'] = $atendimentoSessao;
		echo json_encode([
			'titulo' => 'Paciente',
			'tipo' => 'success',
			'menssagem' => 'Paciente adicionado!!',
			'paciente' => $paciente
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
	} elseif($tipoRequest == 'SALVARRESPONSAVEL'){
		$atendimentoSessao = $_SESSION['atendimentoNovo'];

		$responsavel = [
			'id' => isset($_POST['id'])?$_POST['id']: 'null',
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
		$atendimentoSessao['responsavel'] = $responsavel;
		$_SESSION['atendimentoNovo'] = $atendimentoSessao;
		echo json_encode([
			'titulo' => 'Responsável',
			'tipo' => 'success',
			'menssagem' => 'Responsável adicionado!!',
			'paciente' => $responsavel
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
		'error' => $e->getMessage()
	]);
}
