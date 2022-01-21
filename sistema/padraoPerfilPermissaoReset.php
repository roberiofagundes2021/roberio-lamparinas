<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Permissões';

include('global_assets/php/conexao.php');

	// $inicio1 = microtime(true);

	$unidade = $_POST['unidade'];
	$PerfilId = $_POST['PerfilId'];
	$modulo = $_POST['modulo'];
	// busca o perfil a ter permissões alteradas
	$sqlPerfil = "SELECT PerfiId, PerfiChave FROM Perfil
	WHERE PerfiId = '$PerfilId' and PerfiUnidade = $unidade";

	$sqlPerfil = $conn->query($sqlPerfil);
	$sqlPerfil = $sqlPerfil->fetch(PDO::FETCH_ASSOC);

	// busca todos os menu observando as chaves de "PaPrXPePerfil", "PaPrXPeUnidade" e "MenuModulo"
	$sqlMenus = "SELECT MenuId FROM menu
	join situacao on MenuStatus = SituaId
	join PadraoPerfilXPermissao on MenuId = PaPrXPeMenu and PaPrXPePerfil = '$PerfilId' and PaPrXPeUnidade = $unidade";

	if($modulo !== 'all'){
		$sqlMenus .= " WHERE MenuModulo = '$modulo'";
	}
	$resultMenus = $conn->query($sqlMenus);
	$Menus = $resultMenus->fetchAll(PDO::FETCH_ASSOC);

	// monta um array com os IDs a serem procurados ex.: (2, 3, 4, 5, 15)
	$MenusArray = '(';

	for($x=0; $x < COUNT($Menus); $x++){
		$MenusArray .= $Menus[$x]['MenuId'].",";
	}
	$MenusArray = substr($MenusArray, 0, -1);
	$MenusArray .= ')';

	// busca no banco todos os PadraoPermissao  que tem o ID presente no array "$MenusArray"
	$sqlPadrao = "SELECT PaPerPerfil, PaPerMenu, PaPerVisualizar, PaPerAtualizar, PaPerExcluir,
	PaPerInserir, PaPerSuperAdmin FROM PadraoPermissao
	JOIN Perfil ON PerfiId = PaPerPerfil
	WHERE PerfiChave = '$sqlPerfil[PerfiChave]' and PaPerMenu in $MenusArray";
	$resultPadrao = $conn->query($sqlPadrao);
	$Padroes = $resultPadrao->fetchAll(PDO::FETCH_ASSOC);

	// var_dump($sqlPadrao);
	// exit;

	// caso encontre registros executa essa proxima etapa
	if(COUNT($Padroes) > 0){
		$arrayUpdate = [];

		// Laço para adicionar os dados padrões da tabela "PadraoPerfilXPermissao"
		// na tabela "PerfilXPermissao" levando em conta as chaves "PrXPePerfil", "PrXPeUnidade" e "PrXPeMenu"
		// dessa forma so existe um unico registro, o SQL é adicionado ao array "$arrayUpdate"
		foreach($Padroes as $padrao){
			$visualizar = $padrao['PaPerVisualizar'];
			$inserir = $padrao['PaPerInserir'];
			$atualizar = $padrao['PaPerAtualizar'];
			$excluir = $padrao['PaPerExcluir'];
			$admin = $padrao['PaPerSuperAdmin'];

			$sqlUpdate = "UPDATE PadraoPerfilXPermissao set 
			PaPrXPeVisualizar = $visualizar,
			PaPrXPeInserir = $inserir,
			PaPrXPeAtualizar = $atualizar,
			PaPrXPeExcluir = $excluir
			WHERE PaPrXPePerfil = $PerfilId and PaPrXPeUnidade = $unidade
			and PaPrXPeMenu = ". $padrao['PaPerMenu'];
			array_push($arrayUpdate, $sqlUpdate);
		}

		// laço para executar todos os comandos de uma só vez
		foreach($arrayUpdate as $sql){
			$conn->query($sql);
		}
	}
	$_SESSION['msg']['titulo'] = "Sucesso";
	$_SESSION['msg']['mensagem'] = $modulo !== 'all'?"Padrão de Permissão Restaurada!":"Padrões de Permissões Restauradas!";
	$_SESSION['msg']['tipo'] = "success";

	// $total1 = microtime(true) - $inicio1;
	// echo '<span style="background-color:yellow; padding: 10px; font-size:24px;">Tempo de execução do script: ' . round($total1, 2).' segundos</span>';

	irpara("perfil.php");
	try{} catch(PDOException $e) {
	// var_dump($e);
	$_SESSION['msg']['titulo'] = "Erro";
	$_SESSION['msg']['mensagem'] = "Erro ao atualizar Premissão!!!";
	$_SESSION['msg']['tipo'] = "error";
	irpara("perfil.php");
	
	// echo 'Error: ' . $e->getMessage();
}
?>
