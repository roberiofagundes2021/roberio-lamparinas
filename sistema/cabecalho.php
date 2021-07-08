			<?php

				//Recupera todos os menus do sistema caso esteja usando permissao personalizada
				$userId = $_SESSION['UsuarId'];
				$sqlUser = "SELECT UsuarPermissaoPerfil
				FROM Usuario
				Where UsuarId = '$userId'";
			
				$resultUserId = $conn->query($sqlUser);
				$usuaXPerm = $resultUserId->fetch(PDO::FETCH_ASSOC);

				if($usuaXPerm['UsuarPermissaoPerfil'] == 0){
				$sqlConfig = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo, MenuSetorPublico, MenuPosicao,
							MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave, MenuSetorPrivado,
							UsXPeVisualizar, UsXPeAtualizar, UsXPeExcluir, UsXPeUnidade
							FROM menu
							join situacao on MenuStatus = SituaId
							join UsuarioXPermissao on UsXPeUsuario = '$userId' and UsXPeUnidade = '$unidade' and UsXPeMenu = MenuId
							where UPPER(MenuPosicao) = 'CONFIGURADOR'
							order by MenuOrdem asc";
				} else {
				$sqlConfig = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo, MenuSetorPublico, MenuPosicao,
						MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave, PrXPeId, PrXPePerfil, MenuSetorPrivado
						PrXPeMenu, PrXPeVisualizar, PrXPeAtualizar,  PrXPeExcluir, PrXPeUnidade
						FROM menu
						join situacao on MenuStatus = SituaId
						join PerfilXPermissao on MenuId = PrXPeMenu and PrXPePerfil = '$perfilId' and PrXPeUnidade  = '$unidade'
						where UPPER(MenuPosicao) = 'CONFIGURADOR'
						order by MenuOrdem asc";
				}
				$resultConfig = $conn->query($sqlConfig);
				$config = $resultConfig->fetchAll(PDO::FETCH_ASSOC);			
			?>

			<!-- Page header -->
			<div class="page-header page-header-light">
				<div class="page-header-content header-elements-md-inline" style="display:none;">
					<div class="page-title d-flex">
						<h4><i class="icon-new-tab2 mr-2"></i> <span class="font-weight-semibold"><?php echo $_SESSION['PaginaAtual']; ?></h4>
						<a href="faq.php" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
					</div>

					<div class="header-elements d-none">
						<div class="d-flex justify-content-center">
							<a href="#" class="btn btn-link btn-float font-size-sm font-weight-semibold text-default">
								<i class="icon-bars-alt text-pink-300"></i>
								<span>Statistics</span>
							</a>
							<a href="#" class="btn btn-link btn-float font-size-sm font-weight-semibold text-default">
								<i class="icon-calculator text-pink-300"></i>
								<span>Invoices</span>
							</a>
							<a href="#" class="btn btn-link btn-float font-size-sm font-weight-semibold text-default">
								<i class="icon-calendar5 text-pink-300"></i>
								<span>Schedule</span>
							</a>
						</div>
					</div>
				</div>

				<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline" style="margin-top:4px;">
					<div class="d-flex">
						<div class="breadcrumb">
							<a href="index.php" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
							<!--<a href="general_faq.html" class="breadcrumb-item">General pages</a>-->
							<span class="breadcrumb-item active"><?php echo $_SESSION['PaginaAtual']; ?></span>
						</div>

						<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
					</div>

					<div class="header-elements d-none">
						<div class="breadcrumb justify-content-center">
							<a href="faq.php" class="breadcrumb-elements-item">
								<i class="icon-comment-discussion mr-2"></i>
								Suporte
							</a>

							<?php
							if ($_SESSION['PerfiChave'] == "SUPER" or $_SESSION['PerfiChave'] == "ADMINISTRADOR") {
								print('
										<div class="breadcrumb-elements-item dropdown p-0">
											<a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown">
												<i class="icon-gear mr-2"></i>
												Configurador
											</a>

											<div class="dropdown-menu dropdown-menu-right">');
								foreach($config as $conf){
									print('<a href="'.$conf['MenuUrl'].'" class="dropdown-item"><i class="'.
										$conf['MenuIco'].'"></i>'.$conf['MenuNome'].'</a>');
								}
								print('</div>
								</div>');
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<!-- /page header -->