<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Solicitação';

include('global_assets/php/conexao.php');

$sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome
		FROM Produto
		JOIN Categoria on CategId = ProduCategoria
	    WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduStatus = 1
		ORDER BY ProduNome ASC";
$result = $conn->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
//$count = count($row);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Nova Solicitação</title>
	
	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/demo_pages/extra_jgrowl_noty.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>	
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >
		
		$(document).ready(function() {	
		
			//Aqui sou obrigado a instanciar novamente a utilização do fancybox
			$(".fancybox").fancybox({
				// options
			});	
		});
		
	</script>	

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php include_once("menu-left.php"); ?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Inner container -->
				<div class="d-flex align-items-start flex-column flex-md-row">

					<!-- Left content -->
					<div class="w-100 order-2 order-md-1">
						
						<div class="row">
						
						<?php
							
							$sFoto = '';
							
							foreach ($row as $item){

								if ($item['ProduFoto'] != null){
											
									//Depois verifica se o arquivo físico ainda existe no servidor
									if (file_exists("global_assets/images/produtos/".$item['ProduFoto'])){
										$sFoto = "global_assets/images/produtos/".$item['ProduFoto'];
									} else {
										$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
									}			
								} else {
									$sFoto = "global_assets/images/lamparinas/sem_foto.gif";
								}

							
								print('
								
									<div class="col-xl-3 col-sm-6">
										<div class="card">
											<div class="card-body">
												<div class="card-img-actions">
													<a href="'.$sFoto.'" class="fancybox">
														<img src="'.$sFoto.'" class="card-img"  alt="" style="max-height:290px;">
														<span class="card-img-actions-overlay card-img">
															<i class="icon-plus3 icon-2x"></i>
														</span>
													</a>
												</div>
											</div>

											<div class="card-body bg-light text-center">
												<div class="mb-2">
													<h6 class="font-weight-semibold mb-0">
														<a href="#" class="text-default">'.$item['ProduNome'].'</a>
													</h6>

													<a href="#" class="text-muted">'.$item['CategNome'].'</a>
												</div>

												<div>
													<i class="icon-star-full2 font-size-base text-warning-300"></i>
													<i class="icon-star-full2 font-size-base text-warning-300"></i>
													<i class="icon-star-full2 font-size-base text-warning-300"></i>
													<i class="icon-star-full2 font-size-base text-warning-300"></i>
													<i class="icon-star-full2 font-size-base text-warning-300"></i>
												</div>

												<div class="text-muted mb-3">85 em estoque</div>

												<button type="button" class="btn bg-teal-400"><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
											</div>
										</div>
									</div>							
								
								
								');
							}
						?>

						</div>
						<!-- /grid -->


						<!-- Pagination -->
						<div class="d-flex justify-content-center pt-3 mb-3">
							<ul class="pagination shadow-1">
								<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-right"></i></a></li>
								<li class="page-item active"><a href="#" class="page-link page-link-white">1</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">2</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">3</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">4</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white">5</a></li>
								<li class="page-item"><a href="#" class="page-link page-link-white"><i class="icon-arrow-small-left"></i></a></li>
							</ul>
						</div>
						<!-- /pagination -->

					</div>
					<!-- /left content -->


					<!-- Right sidebar component -->
					<div class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-right border-0 shadow-0 order-1 order-md-2 sidebar-expand-md">

						<!-- Sidebar content -->
						<div class="sidebar-content">

							<!-- Categories -->
							<div class="card">
								<div class="card-header bg-transparent header-elements-inline">
									<span class="text-uppercase font-size-sm font-weight-semibold">Categorias</span>
									<div class="header-elements">
										<div class="list-icons">
					                		<a class="list-icons-item" data-action="collapse"></a>
				                		</div>
			                		</div>
								</div>
								
								<div class="form-group">
									<div class="font-size-xs text-uppercase text-muted mb-3">Items for</div>

									<div class="form-check">
										<label class="form-check-label">
											<input type="checkbox" class="form-input-styled" data-fouc>
											Men
										</label>	
									</div>

									<div class="form-check">
										<label class="form-check-label">
											<input type="checkbox" class="form-input-styled" data-fouc>
											Women
										</label>
									</div>

									<div class="form-check">
										<label class="form-check-label">
											<input type="checkbox" class="form-input-styled" data-fouc>
											Kids
										</label>
									</div>

									<div class="form-check">
										<label class="form-check-label">
											<input type="checkbox" class="form-input-styled" data-fouc>
											Unisex
										</label>
									</div>
								</div>	

								<div class="card-body">
									<form action="#">
										<div class="form-group form-group-feedback form-group-feedback-right">
											<input type="search" class="form-control" placeholder="Buscar">
											<div class="form-control-feedback">
												<i class="icon-search4 font-size-base text-muted"></i>
											</div>
										</div>
									</form>
								</div>

								<div class="card-body border-0 p-0">
									<ul class="nav nav-sidebar mb-2">
										<li class="nav-item nav-item-submenu nav-item-expanded nav-item-open">
											<a href="#" class="nav-link">Street wear</a>
											<ul class="nav nav-group-sub">
												<li class="nav-item"><a href="#" class="nav-link">Hoodies</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Jackets</a></li>
												<li class="nav-item"><a href="#" class="nav-link active">Pants</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Shirts</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Sweaters</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Tank tops</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Underwear</a></li>
											</ul>
										</li>
										<li class="nav-item nav-item-submenu">
											<a href="#" class="nav-link">Snow wear</a>
											<ul class="nav nav-group-sub">
												<li class="nav-item"><a href="#" class="nav-link">Fleece jackets</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Gloves</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Ski jackets</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Ski pants</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Snowboard jackets</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Snowboard pants</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Technical underwear</a></li>
											</ul>
										</li>
										<li class="nav-item nav-item-submenu">
											<a href="#" class="nav-link">Shoes</a>
											<ul class="nav nav-group-sub">
												<li class="nav-item"><a href="#" class="nav-link">Laces</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Sandals</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Skate shoes</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Slip ons</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Sneakers</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Winter shoes</a></li>
											</ul>
										</li>
										<li class="nav-item nav-item-submenu">
											<a href="#" class="nav-link">Accessories</a>
											<ul class="nav nav-group-sub">
												<li class="nav-item"><a href="#" class="nav-link">Beanies</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Belts</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Caps</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Sunglasses</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Headphones</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Video cameras</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Wallets</a></li>
												<li class="nav-item"><a href="#" class="nav-link">Watches</a></li>
											</ul>
										</li>
									</ul>
								</div>
							</div>
							<!-- /categories -->
							
																


							<!-- Filters -->
							<div class="card">
								<div class="card-header bg-transparent header-elements-inline">
									<span class="text-uppercase font-size-sm font-weight-semibold">Filter products</span>
									<div class="header-elements">
										<div class="list-icons">
					                		<a class="list-icons-item" data-action="collapse"></a>
				                		</div>
			                		</div>
								</div>

								<div class="card-body">
									<form action="#">
										<div class="form-group">
											<div class="form-group form-group-feedback form-group-feedback-left">
												<input type="search" class="form-control" placeholder="Search brand">
												<div class="form-control-feedback">
													<i class="icon-search4 font-size-base text-muted"></i>
												</div>
											</div>

											<div class="overflow-auto" style="max-height: 192px;">
												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														686
													</label>	
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														A.Lab
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Adidas
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														ALIS
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Analog
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Burton
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Atomic
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Armada
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														O'Neill
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Baja
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Baker
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Blue Parks
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Billabong
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Bonfire
													</label>
												</div>

												<div class="form-check">
													<label class="form-check-label">
														<input type="checkbox" class="form-input-styled" data-fouc>
														Brixton
													</label>
												</div>
											</div>
										</div>

										<div class="form-group">
											<div class="font-size-xs text-uppercase text-muted mb-3">Items for</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Men
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Women
												</label>
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Kids
												</label>
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Unisex
												</label>
											</div>
										</div>

										<div class="form-group mb-2">
											<div class="font-size-xs text-uppercase text-muted mb-3">Size</div>

											<div class="row row-labels">
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">XXS</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">XS</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">S</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-warning text-warning-800 d-flex justify-content-center p-2 mb-2">M</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">L</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">XL</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">XXL</a>
												</div>
												<div class="col-3">
													<a href="#" class="badge badge-flat border-grey text-grey-800 d-flex justify-content-center p-2 mb-2">XXXL</a>
												</div>
											</div>
										</div>

										<div class="form-group">
											<div class="font-size-xs text-uppercase text-muted mb-3">Color</div>

											<div class="row">
												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-primary rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Blue</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-warning rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Orange</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-teal rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Teal</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-pink rounded color-selector-active">
															<div class="py-1"></div>
															<i class="icon-checkmark3"></i>
														</a>
														<div class="font-size-sm text-center text-muted mt-1">Pink</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-grey-800 rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Black</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-purple rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Purple</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-success rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Green</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-danger rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Red</div>
													</div>
												</div>

												<div class="col-4">
													<div class="mb-2">
														<a href="#" class="d-block p-2 bg-info rounded"><div class="py-1"></div></a>
														<div class="font-size-sm text-center text-muted mt-1">Cyan</div>
													</div>
												</div>
											</div>
										</div>

										<div class="form-group">
											<div class="font-size-xs text-uppercase text-muted mb-3">Features</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Crew neck
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Chest pocket
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Raglan sleeves
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Polo neck
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													V-neck
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													High collar
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Hood
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Button strip
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Wide neck
												</label>	
											</div>

											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-input-styled" data-fouc>
													Kangaroo pocket
												</label>	
											</div>
										</div>

										<button type="submit" class="btn bg-blue btn-block">Filter</button>
									</form>
								</div>
							</div>
							<!-- /filters -->

						</div>
						<!-- /sidebar content -->

					</div>
					<!-- /right sidebar component -->

				</div>
				<!-- /inner container -->

			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
