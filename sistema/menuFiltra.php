<?php
include_once("sessao.php");
include('global_assets/php/conexao.php');

$tipoRequisicao = $_POST['tipo'];
try{
	if($tipoRequisicao == "CRIAR"){
		// se existir o inputNome então ele deve inserir os dados no banco

		$MenuNome = isset($_POST['inputNome'])?$_POST['inputNome']:'';
		$MenuUrl = isset($_POST['url'])?$_POST['url']:'';
		$MenuIco = isset($_POST['icone'])?$_POST['icone']:'';
		$MenuModulo = isset($_POST['cmbModulo'])?$_POST['cmbModulo']:'';
		$MenuPai = isset($_POST['menuPai'])?$_POST['menuPai']:'';
		$MenuLevel = isset($_POST['nivel'])?$_POST['nivel']:1;
		$MenuOrdem = isset($_POST['ordem'])?$_POST['ordem']:1;
		$MenuSubMenu = isset($_POST['subMenu'])?$_POST['subMenu']:0;
		$MenuSetorPublico = isset($_POST['publico'])?$_POST['publico']:0;
		$MenuSetorPrivado = isset($_POST['privado'])?$_POST['privado']:0;

		$MenuPosicao = isset($_POST['posicao'])?$_POST['posicao']:'';
		$MenuUsuarioAtualizador = $_SESSION['UsuarId'];
		$MenuStatus = 1;

		$sql = "INSERT INTO Menu(MenuNome,MenuUrl,MenuIco,MenuModulo,MenuPai,MenuLevel,MenuOrdem,MenuSubMenu,MenuSetorPublico,
		MenuSetorPrivado,MenuPosicao,MenuUsuarioAtualizador,MenuStatus)
		VALUES('$MenuNome','$MenuUrl','$MenuIco',$MenuModulo,$MenuPai,$MenuLevel,$MenuOrdem,$MenuSubMenu,
		$MenuSetorPublico,$MenuSetorPrivado,'$MenuPosicao',$MenuUsuarioAtualizador,$MenuStatus)";
		$result = $conn->query($sql);

		$lastMenu = $conn->lastInsertId();
		// apos inserir o novo menu, será adicionado as permissões do novo menu assim como seus padrões

		$PadraoPerfilXPermissao = [];
		$PerfilXPermissao = [];
		$PadraoPermissao = [];
		$contLoop = 0;

		// seleciona as unidades
		$sqlUnidades = "SELECT UnidaId FROM Unidade";
		$sqlUnidades = $conn->query($sqlUnidades);
		$sqlUnidades = $sqlUnidades->fetchAll(PDO::FETCH_ASSOC);

		$sqlPerfil = "SELECT PerfiId, PerfiNome, PerfiChave, PerfiStatus
		FROM Perfil";
		$sqlPerfil = $conn->query($sqlPerfil);
		$sqlPerfil = $sqlPerfil->fetchAll(PDO::FETCH_ASSOC);

		foreach($sqlUnidades as $unidade){
			$sql_1 = "INSERT INTO PadraoPerfilXPermissao
			(PaPrXPePerfil,PaPrXPeMenu,PaPrXPeInserir,PaPrXPeVisualizar,PaPrXPeAtualizar,
			PaPrXPeExcluir,PaPrXPeSuperAdmin,PaPrXPeUnidade) VALUES ";

			$sql_2 = "INSERT INTO PerfilXPermissao
			(PrXPePerfil,PrXPeMenu,PrXPeInserir,PrXPeVisualizar,PrXPeAtualizar,
			PrXPeExcluir,PrXPeSuperAdmin,PrXPeUnidade) VALUES ";

			foreach($sqlPerfil as $perfil){
				$sql_1 .= "($perfil[PerfiId], $lastMenu, 1, 1, 1, 1, 0, $unidade[UnidaId]),";
				$sql_2 .= "($perfil[PerfiId], $lastMenu, 1, 1, 1, 1, 0, $unidade[UnidaId]),";
				$contLoop++;

				if($contLoop > 800){
					$sql_1 = substr_replace($sql_1 ,"", -1);
					array_push($PadraoPerfilXPermissao, $sql_1);

					$sql_2 = substr_replace($sql_2 ,"", -1);
					array_push($PerfilXPermissao, $sql_2);

					$contLoop = 0;

					$sql_1 = "INSERT INTO PadraoPerfilXPermissao
					(PaPrXPePerfil,PaPrXPeMenu,PaPrXPeInserir,PaPrXPeVisualizar,PaPrXPeAtualizar,
					PaPrXPeExcluir,PaPrXPeSuperAdmin,PaPrXPeUnidade) VALUES ";

					$sql_2 = "INSERT INTO PerfilXPermissao
					(PrXPePerfil,PrXPeMenu,PrXPeInserir,PrXPeVisualizar,PrXPeAtualizar,
					PrXPeExcluir,PrXPeSuperAdmin,PrXPeUnidade) VALUES ";
				}
			}
			if($contLoop > 0 && $contLoop <= 800){
				$sql_1 = substr_replace($sql_1 ,"", -1);
				$sql_2 = substr_replace($sql_2 ,"", -1);
				array_push($PadraoPerfilXPermissao, $sql_1);
				array_push($PerfilXPermissao, $sql_2);
				$contLoop = 0;
			}
		}
		// esse proximo looop vai inserir os dados na tabela PadraoPermissao onde não existe o campo unidade

		$contLoop = 0;
		$sql_3 = "INSERT INTO PadraoPermissao
		(PaPerPerfil,PaPerMenu,PaPerVisualizar,PaPerAtualizar,PaPerExcluir,PaPerInserir,
		PaPerSuperAdmin) VALUES ";

		foreach($sqlPerfil as $perfil){
			$contLoop++;
			$sql_3 .= "($perfil[PerfiId], $lastMenu, 1, 1, 1, 1, 0),";

			if($contLoop > 0 && $contLoop > 800){
				$sql_3 = substr_replace($sql_3 ,"", -1);
				array_push($PadraoPermissao, $sql_3);

				$contLoop = 0;

				$sql_3 = "INSERT INTO PadraoPermissao
				(PaPerPerfil,PaPerMenu,PaPerVisualizar,PaPerAtualizar,PaPerExcluir,PaPerInserir,
				PaPerSuperAdmin) VALUES ";
			}
		}
		if($contLoop <= 800){
			$sql_3 = substr_replace($sql_3 ,"", -1);
			array_push($PadraoPermissao, $sql_3);
			$contLoop = 0;
		}

		// ao finalizar teremos 3 arrays com os comandos para inserir no banco
		// (PadraoPerfilXPermissao,PerfilXPermissao,PadraoPermissao)

		// vai inserir dadso na tabela PadraoPermissao
		foreach($PadraoPermissao as $sql){
			$conn->query($sql);
		}

		// vai inserir dadso na tabela PerfilXPermissao
		foreach($PerfilXPermissao as $sql){
			$conn->query($sql);
		}
		
		// vai inserir dadso na tabela PadraoPerfilXPermissao
		foreach($PadraoPerfilXPermissao as $sql){
			$conn->query($sql);
		}
		echo json_encode([
			'titulo' => 'Criar Menu',
			'tipo' => 'success',
			'menssagem' => 'Menu criado com sucesso!!!'
		]);
	} elseif($tipoRequisicao == "EXCLUIR"){

		$sql = "DELETE FROM PadraoPermissao WHERE PaPerPerfil = $id";
		$conn->query($sql);

		$sql = "DELETE FROM PadraoPerfilXPermissao WHERE PaPrXPeId = $id";
		$conn->query($sql);

		$sql = "DELETE FROM PerfilXPermissao WHERE PrXPeId = $id";
		$conn->query($sql);

		$sql = "DELETE FROM Menu WHERE MenuId = $id";
		$conn->query($sql);
		
		irpara('menuLista.php');

	} elseif ($tipoRequisicao == "ATUALIZAR"){
		$id = $_POST['idMenu'];
		$MenuNome = isset($_POST['inputNome'])?$_POST['inputNome']:'';
		$MenuUrl = isset($_POST['url'])?$_POST['url']:'';
		$MenuIco = isset($_POST['icone'])?$_POST['icone']:'';
		$MenuModulo = isset($_POST['cmbModulo'])?$_POST['cmbModulo']:'';
		$MenuPai = isset($_POST['menuPai'])?$_POST['menuPai']:0;
		$MenuLevel = isset($_POST['nivel'])?$_POST['nivel']:1;
		$MenuOrdem = isset($_POST['ordem'])?$_POST['ordem']:1;
		$MenuSubMenu = isset($_POST['subMenu'])?$_POST['subMenu']:0;
		$MenuSetorPublico = isset($_POST['publico'])?$_POST['publico']:0;
		$MenuSetorPrivado = isset($_POST['privado'])?$_POST['privado']:0;
	
		$MenuPosicao = isset($_POST['posicao'])?$_POST['posicao']:"";
		$MenuUsuarioAtualizador = $_SESSION['UsuarId'];
		$MenuStatus = 1;
	
		$sql = "UPDATE Menu SET MenuNome = '$MenuNome', MenuUrl = '$MenuUrl', MenuIco = '$MenuIco',
		MenuModulo = $MenuModulo, MenuPai = $MenuPai, MenuLevel = $MenuLevel, MenuOrdem = $MenuOrdem,
		MenuSubMenu = $MenuSubMenu, MenuSetorPublico = $MenuSetorPublico, MenuSetorPrivado = $MenuSetorPrivado,
		MenuPosicao = '$MenuPosicao', MenuUsuarioAtualizador = $MenuUsuarioAtualizador, MenuStatus = $MenuStatus
		WHERE MenuId = $id";
		$result = $conn->query($sql);
		echo json_encode([
			'titulo' => 'Menu',
			'tipo' => 'success',
			'menssagem' => 'Menu atualizado!!!'
		]);
	}
} catch(PDOException $e) {
	var_dump($e);
	echo json_encode([
		'titulo' => 'Menu',
		'tipo' => 'error',
		'menssagem' => 'Erro ao executar tarefa!!!'
	]);
}
