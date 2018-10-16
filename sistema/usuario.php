<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Usuário';

include('global_assets/php/conexao.php');

$sql = ("SELECT UsuarId, UsuarCpf, UsuarNome, UsuarLogin, EXUXPStatus
		 FROM Usuario
		 LEFT JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
		 LEFT JOIN Empresa on EXUXPEmpresa = EmpreId
		 Where EmpreId = ".$_SESSION['EmpreId']."
		 ORDER BY UsuarNome ASC");
$result = $conn->query("$sql");
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Usuário</title>

	<?php include_once("head.php"); ?>

</head>

<body class="navbar-top">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->
				<div class="row">
					<div class="col-lg-12">
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h5 class="card-title">Relação de Usuários</h5>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								Os usuários cadastrados abaixo pertencem a empresa <b><?php echo $_SESSION['EmpreNomeFantasia']; ?></b>.
								<div class="text-right"><a href="usuarioNovo.php" class="btn btn-success" role="button">Novo usário</a></div>
							</div>							

							<table class="table datatable-responsive">
								<thead>
									<tr class="bg-slate">
										<th>Nome</th>
										<th>Login</th>
										<th>CPF</th>
										<th>Situação</th>										
										<th class="text-center">Acões</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($row as $item){
										
										$situacao = $item['EXUXPStatus'] ? 'Ativo' : 'Inativo';
										$situacaoClasse = $item['EXUXPStatus'] ? 'badge-success' : 'badge-secondary';
										
										print('
										<tr>
											<td>'.$item['UsuarNome'].'</td>
											<td>'.$item['UsuarLogin'].'</td>
											<td>'.formatarCnpj($item['UsuarCpf']).'</td>
											<td><span class="badge '.$situacaoClasse.'">'.$situacao.'</span></td>											
											<td class="text-center">
												<div class="list-icons">
													<div class="dropdown">
														<a href="#" class="list-icons-item" data-toggle="dropdown">
															<i class="icon-menu9"></i>
														</a>

														<div class="dropdown-menu dropdown-menu-right">
															<a href="#" onclick="atualizaUsuario('.$item['UsuarId'].', \'edita\')" class="dropdown-item"><i class="icon-pencil7"></i> Editar</a>
															<a href="#" onclick="atualizaUsuario('.$item['UsuarId'].', \'exclui\')" class="dropdown-item"><i class="icon-bin"></i> Excluir</a>
														</div>
													</div>
												</div>
											</td>
										</tr>');
									}
								?>

								</tbody>
							</table>
						</div>
						<!-- /basic responsive configuration -->

					</div>
				</div>				

				<!-- /info blocks -->
				
				<form name="formUsuario" method="post" action="usuarioEdita.php">
					<input type="hidden" id="inputUsuarioId" name="inputUsuarioId" >
				</form>

			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
