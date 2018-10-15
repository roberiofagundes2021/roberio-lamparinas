			<!-- Page header -->
			<div class="page-header page-header-light">
				<div class="page-header-content header-elements-md-inline">
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

				<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
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

							<div class="breadcrumb-elements-item dropdown p-0">
								<a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown">
									<i class="icon-gear mr-2"></i>
									Configurador
								</a>

								<div class="dropdown-menu dropdown-menu-right">
									<a href="empresa.php" class="dropdown-item"><i class="icon-office"></i> Empresas</a>
									<a href="usuario.php" class="dropdown-item"><i class="icon-users"></i> Usuários</a>
									<a href="perfil.php" class="dropdown-item"><i class="icon-user-check"></i> Perfis</a>
									<div class="dropdown-divider"></div>
									<a href="#" class="dropdown-item"><i class="icon-gear"></i> Demais ajustes</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /page header -->
