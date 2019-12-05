
		<!-- Main sidebar -->
		<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

			<!-- Sidebar mobile toggler -->
			<div class="sidebar-mobile-toggler text-center">
				<a href="#" class="sidebar-mobile-main-toggle">
					<i class="icon-arrow-left8"></i>
				</a>
				<span class="font-weight-semibold">Navigation</span>
				<a href="#" class="sidebar-mobile-expand">
					<i class="icon-screen-full"></i>
					<i class="icon-screen-normal"></i>
				</a>
			</div>
			<!-- /sidebar mobile toggler -->


			<!-- Sidebar content -->
			<div class="sidebar-content">

				<!-- User menu -->
				<div class="sidebar-user-material">
					<div class="sidebar-user-material-body">
						<div class="card-body text-center">
							<a href="index.php">
								<!-- src="global_assets/images/placeholders/placeholder.jpg" class="rounded-circle shadow-1 -->
								<img src="global_assets/images/lamparinas/logo-lamparinas_200x200.jpg" class="img-fluid shadow-5 mb-3" width="100" height="100" alt="" style="padding-top:8px;">
							</a>
							<h6 class="mb-0 text-white text-shadow-dark"><?php echo nomeSobrenome($_SESSION['UsuarNome'],2); ?></h6>
							<span class="font-size-sm text-white text-shadow-dark"><?php echo $_SESSION['EmpreNomeFantasia']; ?></span>
						</div>
													
						<div class="sidebar-user-material-footer">
							<a href="#user-nav" class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle" data-toggle="collapse"><span>Minha Conta</span></a>
						</div>
					</div>

					<div class="collapse" id="user-nav">
						<ul class="nav nav-sidebar">
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="icon-user-plus"></i>
									<span>Meu Perfil</span>
								</a>
							</li>
							<!--<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="icon-coins"></i>
									<span>Minha bandeja</span>
								</a>
							</li>-->
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="icon-comment-discussion"></i>
									<span>Minha bandeja</span>
									<span class="badge bg-teal-400 badge-pill align-self-center ml-auto">5</span>
								</a>
							</li>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="icon-cog5"></i>
									<span>Configurar Conta</span>
								</a>
							</li>
							<li class="nav-item">
								<a href="sair.php" class="nav-link">
									<i class="icon-switch2"></i>
									<span>Sair</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<!-- /user menu -->


				<!-- Main navigation -->
				<div class="card card-sidebar-mobile">
					<ul class="nav nav-sidebar" data-nav-type="accordion">

						<!-- Main -->
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Principal</div> <i class="icon-menu" title="Main"></i></li>
						<li class="nav-item">
							<a href="index.php" class="nav-link active">
								<i class="icon-home4"></i>
								<span>
									Página Inicial
								</span>
							</a>
						</li>
								<!-- /main -->

						<!-- Forms -->
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Controle de Estoque</div> <i class="icon-menu" title="Forms"></i></li>
						
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"> <span>Apoio</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Text editors">
								<li class="nav-item"><a href="categoria.php" class="nav-link">Categoria</a></li>
								<li class="nav-item"><a href="subcategoria.php" class="nav-link">SubCategoria</a></li>								
								<li class="nav-item"><a href="marca.php" class="nav-link">Marca</a></li>
								<li class="nav-item"><a href="modelo.php" class="nav-link">Modelo</a></li>
								<li class="nav-item"><a href="fabricante.php" class="nav-link">Fabricante</a></li>
								<li class="nav-item"><a href="unidademedida.php" class="nav-link">Unidade de Medida</a></li>
								<li class="nav-item"><a href="produtoOrcamento.php" class="nav-link">Produtos para Orçamento</a></li>		
								<li class="nav-item"><a href="localestoque.php" class="nav-link">Local do Estoque</a></li>
								<li class="nav-item"><a href="centroCusto.php" class="nav-link">Centro de Custo</a></li>
								<li class="nav-item"><a href="planoContas.php" class="nav-link">Plano de Contas</a></li>
								<li class="nav-item"><a href="formaPagamento.php" class="nav-link">Forma de Pagamento</a></li>								
							</ul>
						</li>
						
						<li class="nav-item">
							<a href="fornecedor.php" class="nav-link"><i class="icon-users2"></i> <span>Fornecedor</span></a>
						</li>
						<li class="nav-item">
							<a href="produto.php" class="nav-link"><i class="icon-gift"></i> <span>Produto</span></a>
						</li>
						<li class="nav-item">
							<a href="servico.php" class="nav-link"><i class="icon-cogs"></i><span>Serviços</span></a>
						</li>						
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-bag"></i> <span>Compras</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Text editors">
								<li class="nav-item"><a href="tr.php" class="nav-link">Termo de Referência</a></li>
								<li class="nav-item"><a href="orcamento.php" class="nav-link">Orçamento</a></li>
								<li class="nav-item"><a href="solicitacao.php" class="nav-link">Solicitação</a></li>
								<li class="nav-item"><a href="ordemcompra.php" class="nav-link">Ordem de Compra</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-stack2"></i> <span>Gerenciamento do Estoque</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Text editors">
								<li class="nav-item"><a href="movimentacao.php" class="nav-link">Movimentação</a></li>
								<li class="nav-item"><a href="fluxo.php" class="nav-link">Fluxo Operacional</a></li>
								<li class="nav-item"><a href="estoqueMinimoImprime.php" class="nav-link">Estoque Minimo</a></li>
							</ul>
						</li>						
						
						<li class="nav-item">
							<a href="inventario.php" class="nav-link"><i class="icon-paste2"></i> <span>Inventário</span></a>
						</li>						
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-stack-text"></i> <span>Relatórios</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Form layouts">
								<li class="nav-item"><a href="relatorioMovimentacao.php" class="nav-link">Movimentação</a></li>
								<li class="nav-item"><a href="form_layout_vertical_styled.html" class="nav-link disabled">Custom styles <span class="badge bg-transparent align-self-center ml-auto">Coming soon</span></a></li>
								<li class="nav-item-divider"></li>
								<li class="nav-item"><a href="form_layout_horizontal.html" class="nav-link">Horizontal form</a></li>
							</ul>
						</li>
						<!-- /forms -->

					</ul>
				</div>
				<!-- /main navigation -->

			</div>
			<!-- /sidebar content -->
			
		</div>
		<!-- /main sidebar -->
