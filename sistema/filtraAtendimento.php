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
	if($tipoRequest == 'ATENDIMENTOS'){
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
					'iAtendimento' => $item['AtendId']
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
	
		$sql = "UPDATE Atendimento set AtendSituacao = $iSituacao WHERE AtendId = $iAtendimento";
		$result = $conn->query($sql);

		echo json_encode([
			'titulo' => 'Alterar Situação',
			'tipo' => 'success',
			'menssagem' => 'Situação alterada com sucesso!!!',
		]);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'ATENDIMENTOS': $msg = 'Erro ao carregar atendimentos';break;
		case 'SITUACOES': $msg = 'Erro ao carregar situações';break;
		case 'MUDARSITUACAO': $msg = 'Erro ao atualizar situação do atendimentos';break;
		default: $msg = 'Erro ao executar ação';break;
	}
	echo json_encode([
		'titulo' => 'Atendimento',
		'tipo' => 'error',
		'menssagem' => $msg,
		'error' => $e->getMessage()
	]);
}
