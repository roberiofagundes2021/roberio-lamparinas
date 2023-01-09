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

	if($tipoRequest == 'ATENDIMENTOS'){		
		$sql = "SELECT AtendId, ClienNome
				FROM Atendimento
				JOIN Cliente on ClienId = AtendCliente
				JOIN Situacao on SituaId = AtendSituacao
				JOIN AtendimentoModalidade on AtModId = AtendModalidade
				LEFT JOIN CaixaRecebimento on CxRecAtendimento = AtendId
				WHERE AtendUnidade = ".$_SESSION['UnidadeId']." and AtendId not in (SELECT CxRecAtendimento FROM CaixaRecebimento)
				AND SituaChave = 'LIBERADO'
				AND AtModTipoRecebimento = 'À Vista'                                                      
				ORDER BY ClienNome";
		$result = $conn->query($sql);
		$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
		
		$array = [];
		foreach($rowFornecedor as $item){
			array_push($array, [
				'id'=>$item['AtendId'],
				'nome'=>$item['ClienNome']
			]);
		}
		echo json_encode($array);
	}
}catch(PDOException $e) {
	$msg = '';
	switch($tipoRequest){
		case 'ATENDIMENTOS': $msg = 'Erro ao carregar atendimentos do caixa';break;
		default: $msg = "Erro ao executar ação COD.: $tipoRequest";break;
	}

	echo json_encode([
		'titulo' => 'Caixa PDV',
		'tipo' => 'error',
		'menssagem' => $msg,
		'sql' => $sql,
		'error' => $e->getMessage()
	]);
}
