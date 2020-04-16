<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Painel de Controle';

include('global_assets/php/conexao.php');

$_SESSION['Pendente'] = 50;
$_SESSION['Liberado'] = 50;
$_SESSION['NaoLiberado'] = 50;

if (isset($_POST['cmbPerfil'])){
	$idPerfilLogado = $_POST['cmbPerfil'];
} else {
	$sql = "SELECT PerfiId
			FROM Perfil
			JOIN Situacao on SituaId = PerfiStatus		
			WHERE PerfiChave = '". $_SESSION['PerfiChave'] ."' and SituaChave = 'ATIVO'";
	$result = $conn->query($sql);
	$rowPerfilLogado = $result->fetch(PDO::FETCH_ASSOC);
	$idPerfilLogado = $rowPerfilLogado['PerfiId'];
}
//echo $idPerfilLogado;die;

/* AGUARDANDOLIBERACAO */
$sql = "SELECT Distinct BandeId, BandeIdentificacao, BandeData, BandeDescricao, BandeURL, UsuarNome, BandeTabela, BandeTabelaId, 
		SituaNome, DATEDIFF (DAY, BandeData, GETDATE ( )) as Intervalo, OrComNumero, OrComSituacao, OrComTipo
		FROM Bandeja
		JOIN Usuario on UsuarId = BandeSolicitante
		JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'AGUARDANDOLIBERACAO' and BnXPePerfil in (".$idPerfilLogado.")
		ORDER BY BandeData DESC, BandeId DESC";
//echo $sql;die;		
$result = $conn->query($sql);
$rowPendente = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(BandeId) as TotalPendente
		FROM Bandeja
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'AGUARDANDOLIBERACAO' and BnXPePerfil in (".$idPerfilLogado.")";
$result = $conn->query($sql);
$rowTotalPendente = $result->fetch(PDO::FETCH_ASSOC);
$totalPendente = $rowTotalPendente['TotalPendente'];

/* LIBERADAS */
$sql = "SELECT Distinct BandeId, BandeIdentificacao, BandeData, BandeDescricao, BandeURL, UsuarNome, BandeTabela, BandeTabelaId, 
		SituaNome, OrComNumero, OrComTipo
		FROM Bandeja
		JOIN Usuario on UsuarId = BandeSolicitante
		JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'LIBERADO' and BnXPePerfil in (".$idPerfilLogado.")
		ORDER BY BandeData DESC";
$result = $conn->query($sql);
$rowLiberado = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(BandeId) as TotalLiberado
		FROM Bandeja
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'LIBERADO' and BnXPePerfil in (".$idPerfilLogado.")";
$result = $conn->query($sql);
$rowTotalLiberado = $result->fetch(PDO::FETCH_ASSOC);
$totalLiberado = $rowTotalLiberado['TotalLiberado'];

/* NÃO LIBERADAS */
$sql = "SELECT Distinct BandeId, BandeIdentificacao, BandeData, BandeDescricao, BandeURL, UsuarNome, BandeTabela, BandeTabelaId, SituaNome
		FROM Bandeja
		JOIN Usuario on UsuarId = BandeSolicitante
		JOIN EmpresaXUsuarioXPerfil on EXUXPUsuario = UsuarId
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'NAOLIBERADO' and BnXPePerfil in (".$idPerfilLogado.")
		ORDER BY BandeData DESC";
$result = $conn->query($sql);
$rowNaoLiberado = $result->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(BandeId) as TotalNaoLiberado
		FROM Bandeja
		LEFT JOIN OrdemCompra on OrComId = BandeTabelaId
		LEFT JOIN FluxoOperacional on FlOpeId = BandeTabelaId
		LEFT JOIN Situacao on SituaId = BandeStatus
		LEFT JOIN BandejaXPerfil on BnXPeBandeja = BandeId
	    WHERE BandeEmpresa = ". $_SESSION['EmpreId'] ." and SituaChave = 'NAOLIBERADO' and BnXPePerfil in (".$idPerfilLogado.")";
$result = $conn->query($sql);
$rowTotalNaoLiberado = $result->fetch(PDO::FETCH_ASSOC);
$totalNaoLiberado = $rowTotalNaoLiberado['TotalNaoLiberado'];

$totalPorcentagem = 0;
$todos = 0;
$totalTodasAcoes = $totalPendente + $totalLiberado + $totalNaoLiberado;

if (isset($_POST['cmbSituacao'])){

	if ($_POST['cmbSituacao'] == 'AGUARDANDOLIBERACAO'){
		
		$totalAcoes = $totalPendente;
		$situacaoPorcentagem = "Aguardando Liberação";	

	} else if ($_POST['cmbSituacao'] == 'LIBERADO'){
		
		$totalAcoes = $totalLiberado;
		$situacaoPorcentagem = "Liberado";
		
	} else if ($_POST['cmbSituacao'] == 'NAOLIBERADO'){
		
		$totalAcoes = $totalNaoLiberado;
		$situacaoPorcentagem = "Não Liberado";

	} else { //Todos
		
		$totalAcoes = $totalPendente + $totalLiberado + $totalNaoLiberado;
		$situacaoPorcentagem = "Aguardando Liberação";
		$todos = 1;
	}

} else { //Por padrão lista só os Pendentes
	
	$totalAcoes = $totalPendente;
	$situacaoPorcentagem = "Aguardando Liberação";
}

if($totalAcoes){
	if ($todos){ // Se selecionou a Situação TODOS, mostre como padrão apenas o valor do AGUARDANDOLIBERACAO
		$totalPorcentagem = mostraValor($totalPendente / $totalTodasAcoes * 100);
	} else {
		$totalPorcentagem = mostraValor($totalAcoes / $totalTodasAcoes * 100);
	}
} else {
	$totalPorcentagem = 0;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Home</title>
	
	<?php include_once("head.php"); ?>
	
	<?php //include_once("acesso.php"); ?>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>	
	
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<!-- Gráfico das Ações - Formato Pizza -->
	<script src="global_assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	<script src="global_assets/js/demo_pages/dashboard.js"></script>

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	
    <script type="text/javascript" >

        $(document).ready(function() {		
			
			//Ao mudar a combo Perfil, filtra a tabela de Workflow pelo Perfil
			$('#cmbPerfil').on('change', function(e){
				
				$("#formWorkflow").submit();
			});
			
			//Ao mudar a combo Perfil, filtra a tabela de Workflow pelo Perfil
			$('#cmbSituacao').on('change', function(e){
				
				$("#formWorkflow").submit();
			});		

			
			/* Gráfico na Bandeja */
		    var _bandeja = function(element, size, pendente, liberado, naoliberado) {
		        
		        if (typeof d3 == 'undefined') {
		            console.warn('Warning - d3.min.js is not loaded.');
		            return;
		        }

		        // Initialize chart only if element exsists in the DOM
		        if($(element).length > 0) {		        	

		            // Basic setup
		            // ------------------------------

		            // Add data set
		            var data = [
		                {
		                    "status": "Aguardando Liberação",
		                    "icon": "<i class='status-mark border-blue-300 mr-2'></i>",
		                    "value": pendente,
		                    "color": "#29B6F6"
		                }, {
		                    "status": "Liberados",
		                    "icon": "<i class='status-mark border-success-300 mr-2'></i>",
		                    "value": liberado,
		                    "color": "#66BB6A"
		                }, {
		                    "status": "Não Liberados",
		                    "icon": "<i class='status-mark border-danger-300 mr-2'></i>",
		                    "value": naoliberado,
		                    "color": "#EF5350"
		                }
		            ];

		            // Main variables
		            var d3Container = d3.select(element),
		                distance = 2, // reserve 2px space for mouseover arc moving
		                radius = (size/2) - distance,
		                sum = d3.sum(data, function(d) { return d.value; })

		            // Tooltip
		            // ------------------------------

		            var tip = d3.tip()
		                .attr('class', 'd3-tip')
		                .offset([-10, 0])
		                .direction('e')
		                .html(function (d) {
		                    return '<ul class="list-unstyled mb-1">' +
		                        '<li>' + '<div class="font-size-base mb-1 mt-1">' + d.data.icon + d.data.status + '</div>' + '</li>' +
		                        '<li>' + 'Total: &nbsp;' + '<span class="font-weight-semibold float-right">' + d.value + '</span>' + '</li>' +
		                        '<li>' + '(%): &nbsp;' + '<span class="font-weight-semibold float-right">' + (100 / (sum / d.value)).toFixed(2) + '%' + '</span>' + '</li>' +
		                    '</ul>';
		                })

		            // Create chart
		            // ------------------------------

		            // Add svg element
		            var container = d3Container.append('svg').call(tip);
		            
		            // Add SVG group
		            var svg = container
		                .attr('width', size)
		                .attr('height', size)
		                .append('g')
		                    .attr('transform', 'translate(' + (size / 2) + ',' + (size / 2) + ')'); 

		            // Construct chart layout
		            // ------------------------------

		            // Pie
		            var pie = d3.layout.pie()
		                .sort(null)
		                .startAngle(Math.PI)
		                .endAngle(3 * Math.PI)
		                .value(function (d) { 
		                    return d.value;
		                }); 

		            // Arc
		            var arc = d3.svg.arc()
		                .outerRadius(radius)
		                .innerRadius(radius / 2);

		            //
		            // Append chart elements
		            //

		            // Group chart elements
		            var arcGroup = svg.selectAll('.d3-arc')
		                .data(pie(data))
		                .enter()
		                .append('g') 
		                    .attr('class', 'd3-arc')
		                    .style('stroke', '#fff')
		                    .style('cursor', 'pointer');
		            
		            // Append path
		            var arcPath = arcGroup
		                .append('path')
		                .style('fill', function (d) { return d.data.color; });

		            // Add tooltip
		            arcPath
		                .on('mouseover', function (d, i) {

		                    // Transition on mouseover
		                    d3.select(this)
		                    .transition()
		                        .duration(500)
		                        .ease('elastic')
		                        .attr('transform', function (d) {
		                            d.midAngle = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
		                            var x = Math.sin(d.midAngle) * distance;
		                            var y = -Math.cos(d.midAngle) * distance;
		                            return 'translate(' + x + ',' + y + ')';
		                        });
		                })

		                .on('mousemove', function (d) {
		                    
		                    // Show tooltip on mousemove
		                    tip.show(d)
		                        .style('top', (d3.event.pageY - 40) + 'px')
		                        .style('left', (d3.event.pageX + 30) + 'px');
		                })

		                .on('mouseout', function (d, i) {

		                    // Mouseout transition
		                    d3.select(this)
		                    .transition()
		                        .duration(500)
		                        .ease('bounce')
		                        .attr('transform', 'translate(0,0)');

		                    // Hide tooltip
		                    tip.hide(d);
		                });

		            // Animate chart on load
		            arcPath
		                .transition()
		                    .delay(function(d, i) { return i * 500; })
		                    .duration(500)
		                    .attrTween('d', function(d) {
		                        var interpolate = d3.interpolate(d.startAngle,d.endAngle);
		                        return function(t) {
		                            d.endAngle = interpolate(t);
		                            return arc(d);  
		                        }; 
		                    });
		        }
		    };
		    /* Fim Gráfico Bandeja */

		    var pendente = $('#inputTotalPendente').val();
		    var liberado = $('#inputTotalLiberado').val();
		    var naoliberado = $('#inputTotalNaoLiberado').val();

		    //O if é apenas para corrigir um bug que fica quando se tem todos os valores zerados no gráfico. Tipo fica piscando e não aparece nada.
		    if (pendente != 0 || liberado != 0 || naoliberado != 0){
		    	_bandeja('#grafico', 52, pendente, liberado, naoliberado);
		    }

		});
		
		//Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
		function atualizaBandeja(BandeId, BandeTabela, BandeTabelaId, Tipo){

			document.getElementById('inputBandejaId').value = BandeId;

			if(BandeTabela == 'OrdemCompra'){
				document.getElementById('inputOrdemCompraId').value = BandeTabelaId;
				
				if (Tipo == 'imprimir'){
					document.formBandeja.action = "ordemcompraImprime.php";
					document.formBandeja.setAttribute("target", "_blank");
					document.formBandeja.submit();
				} else {
					if (Tipo == 'liberar'){	
						document.getElementById('inputOrdemCompraStatus').value = 'LIBERADO'; //Liberado
						document.formBandeja.action = "ordemcompraBandejaMudaSituacao.php";	
						document.formBandeja.setAttribute("target", "_self");
						document.formBandeja.submit();	
					} else if (Tipo == 'naoliberar'){
			            bootbox.prompt({
			                title: 'Informe o motivo da não liberação',
			                inputType: 'textarea',
			                buttons: {
			                    confirm: {
			                        label: 'Enviar',
			                        className: 'btn-success'
			                    },
			                    cancel: {
			                        label: 'Cancelar',
			                        className: 'btn-link'
			                    }
			                },
			                callback: function (result) {

			                    if (result === null) {                                             
			                        bootbox.alert({
			                            title: 'Não Liberar',
			                            message: 'A não liberação foi cancelada!'
			                        });                              
			                    } else {
			                       
			                        document.getElementById('inputMotivo').value = result;
									document.getElementById('inputOrdemCompraStatus').value = 'NAOLIBERADO';
									document.formBandeja.action = "ordemcompraBandejaMudaSituacao.php";
									document.formBandeja.setAttribute("target", "_self");
									document.formBandeja.submit();
									
									/*
			                        bootbox.alert({
			                            title: 'Hi <strong>' + result + '</strong>',
			                            message: 'How are you doing today?'
			                        });*/                               
			                    }
			                }
			            });
					}
				}
			}

			if (BandeTabela == 'FluxoOperacional'){
				
				document.getElementById('inputFluxoId').value = BandeTabelaId;
				
				if (Tipo == 'imprimir'){
					document.formBandeja.action = "fluxoImprime.php";
					document.formBandeja.setAttribute("target", "_blank");
					document.formBandeja.submit();
				} else {
					if (Tipo == 'liberar'){	
						document.getElementById('inputFluxoStatus').value = 'ATIVO'; //LIberado
						document.formBandeja.action = "fluxoBandejaMudaSituacao.php";	
						document.formBandeja.setAttribute("target", "_self");
						document.formBandeja.submit();	
					} else if (Tipo == 'naoliberar'){

			            bootbox.prompt({
			                title: 'Informe o motivo da não liberação',
			                inputType: 'textarea',
			                buttons: {
			                    confirm: {
			                        label: 'Enviar',
			                        className: 'btn-success'
			                    },
			                    cancel: {
			                        label: 'Cancelar',
			                        className: 'btn-link'
			                    }
			                },
			                callback: function (result) {

			                    if (result === null) {                                             
			                        bootbox.alert({
			                            title: 'Não Liberar',
			                            message: 'A não liberação foi cancelada!'
			                        });                              
			                    } else {
			                       
			                        document.getElementById('inputMotivo').value = result;
									document.getElementById('inputFluxoStatus').value = 'NAOLIBERADO';
									document.formBandeja.action = "fluxoBandejaMudaSituacao.php";
									document.formBandeja.setAttribute("target", "_self");
									document.formBandeja.submit();
									
									/*
			                        bootbox.alert({
			                            title: 'Hi <strong>' + result + '</strong>',
			                            message: 'How are you doing today?'
			                        });*/                               
			                    }
			                }
			            });
					}
				}
			}

			if (BandeTabela == 'Solicitacao'){
				
				document.getElementById('inputSolicitacaoId').value = BandeTabelaId;

				if (Tipo == 'imprimir'){
					document.formBandeja.action = "solicitacaoImprime.php";
					document.formBandeja.setAttribute("target", "_blank");
					document.formBandeja.submit();
				} else {
					if (Tipo == 'liberar'){	
						document.getElementById('inputSolicitacaoStatus').value = 'LIBERADO'; //Liberado
						document.formBandeja.action = "movimentacaoNovo.php";	
						document.formBandeja.setAttribute("target", "_self");
						document.formBandeja.submit();	
					} else if (Tipo == 'naoliberar'){

			            bootbox.prompt({
			                title: 'Informe o motivo da não liberação',
			                inputType: 'textarea',
			                buttons: {
			                    confirm: {
			                        label: 'Enviar',
			                        className: 'btn-success'
			                    },
			                    cancel: {
			                        label: 'Cancelar',
			                        className: 'btn-link'
			                    }
			                },
			                callback: function (result) {

			                    if (result === null) {                                             
			                        bootbox.alert({
			                            title: 'Não Liberar',
			                            message: 'A não liberação foi cancelada!'
			                        });                              
			                    } else {
			                       
			                        document.getElementById('inputMotivo').value = result;
									document.getElementById('inputSolicitacaoStatus').value = 'NAOLIBERADO';
									document.formBandeja.action = "solicitacaoBandejaMudaSituacao.php";
									document.formBandeja.setAttribute("target", "_self");
									document.formBandeja.submit();
									
									/*
			                        bootbox.alert({
			                            title: 'Hi <strong>' + result + '</strong>',
			                            message: 'How are you doing today?'
			                        });*/                               
			                    }
			                }
			            });
					}
				}
			}
		}

	</script>

</head>

<!-- A classe "navbar-top" está sendo usada aqui porque no topo.php a nav-bar é "fixed-top" -->
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

				<!-- Support tickets -->
				<div class="card">
					
					<form id="formWorkflow" method="post">
						
						<div class="card-header header-elements-sm-inline">
							<h6 class="card-title">Fluxo de Trabalho (Workflow)</h6>
							<div class="header-elements">
								
							</div>

							<input type="hidden" id="inputTotalPendente" class="form-control" value="<?php echo $totalPendente; ?>">
							<input type="hidden" id="inputTotalLiberado" class="form-control" value="<?php echo $totalLiberado; ?>">
							<input type="hidden" id="inputTotalNaoLiberado" class="form-control" value="<?php echo $totalNaoLiberado; ?>">
						</div>

						<div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap">
							<div class="d-flex align-items-center mb-3 mb-md-0">
								<div id="grafico"></div>
								<div class="ml-3">
									<h5 class="font-weight-semibold mb-0"><?php echo $totalPorcentagem; ?>% <span class="text-blue font-size-sm font-weight-normal"><i class="icon-arrow-up12"></i> (<?php echo $situacaoPorcentagem; ?>)</span></h5>
									<span class="badge badge-mark border-blue mr-1"></span> <span class="text-muted">Gráfico de ações</span>
								</div>
							</div>

							<div class="d-flex align-items-center mb-3 mb-md-0">
								<a href="#" class="btn bg-transparent border-indigo-400 text-indigo-400 rounded-round border-2 btn-icon">
									<i class="icon-alarm-add"></i>
								</a>
								<div class="ml-3">
									<h5 class="font-weight-semibold mb-0"><?php echo $totalAcoes; ?></h5>
									<span class="text-muted">Total de ações</span>
								</div>
							</div>
							
							<?php 
								
								print('							
									<div>
										<b>Filtrar ações por:</b>
										<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2">');
											
											$sql = "SELECT SituaId, SituaNome, SituaChave
													FROM Situacao
													WHERE SituaStatus = 1 and SituaChave in ('AGUARDANDOLIBERACAO', 'LIBERADO', 'NAOLIBERADO')
													ORDER BY SituaNome ASC";
											$result = $conn->query($sql);
											$rowSituacao = $result->fetchAll(PDO::FETCH_ASSOC);
											
											print('<option value="TODOS">Todos</option>');
											
											foreach ($rowSituacao as $item){
												
												if (isset($_POST['cmbSituacao'])){
													$seleciona = $item['SituaChave'] == $_POST['cmbSituacao'] ? "selected" : "";
												} else{
													$seleciona = $item['SituaChave'] == 'AGUARDANDOLIBERACAO' ? "selected" : "";
												}
												
												print('<option value="'.$item['SituaChave'].'" '.$seleciona.'>'.$item['SituaNome'].'</option>');
											}
											
										print('	
										</select>										
									</div>');
								
							?>							

							<?php 
								if ($_SESSION['PerfiChave'] == "SUPER" or $_SESSION['PerfiChave'] == "ADMINISTRADOR") {
									print('							
										<div>
											<b>Filtrar pelo perfil</b>
											<select id="cmbPerfil" name="cmbPerfil" class="form-control form-control-select2">');
												
												$sql = "SELECT PerfiId, PerfiNome
														FROM Perfil
														JOIN Situacao on SituaId = PerfiStatus
														WHERE SituaChave = 'ATIVO'
														ORDER BY PerfiNome ASC";
												$result = $conn->query($sql);
												$rowPerfil = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowPerfil as $item){
													
													$seleciona = $item['PerfiId'] == $idPerfilLogado ? "selected" : "";
													
													print('<option value="'.$item['PerfiId'].'" '.$seleciona.'>'.$item['PerfiNome'].'</option>');
												}
												
											print('	
											</select>											
										</div>');
								}
							?>
						</div>

						<div class="table-responsive">
							
							<table class="table text-nowrap">
								<thead>
									<tr>
										<th style="width: 50px">Dias</th>
										<th style="width: 300px;">Usuário Solicitante</th>
										<th>Descrição</th>
										<th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
									</tr>
								</thead>
								<tbody>
									
									<?php 
									
										if (isset($_POST['cmbSituacao']) and $_POST['cmbSituacao'] == 'TODOS'){
											
											include('bandejaPendente.php');
											include('bandejaLiberado.php');
											include('bandejaNaoLiberado.php');
											
										} else if (isset($_POST['cmbSituacao']) and $_POST['cmbSituacao'] == 'AGUARDANDOLIBERACAO'){
											
											include('bandejaPendente.php');
											
										} else if (isset($_POST['cmbSituacao']) and $_POST['cmbSituacao'] == 'LIBERADO'){
											
											include('bandejaLiberado.php');
											
										} else if (isset($_POST['cmbSituacao']) and $_POST['cmbSituacao'] == 'NAOLIBERADO'){

											include('bandejaNaoLiberado.php');
											
										} else { // quando não tiver POST e vier do menu index.php
											
											include('bandejaPendente.php');
											//include('bandejaLiberado.php');
											//include('bandejaNaoLiberado.php');
										}
									?>
										
								</tbody>
							</table>
						</div>
					</form>
				</div>
				<!-- /support tickets -->
				
				<form name="formBandeja" method="post">
					<input type="hidden" id="inputBandejaId" name="inputBandejaId" >
					<input type="hidden" id="inputFluxoId" name="inputFluxoId" >
					<input type="hidden" id="inputFluxoStatus" name="inputFluxoStatus" >
					<input type="hidden" id="inputOrdemCompraId" name="inputOrdemCompraId" >
					<input type="hidden" id="inputOrdemCompraStatus" name="inputOrdemCompraStatus" >
					<input type="hidden" id="inputSolicitacaoId" name="inputSolicitacaoId" >					
					<input type="hidden" id="inputSolicitacaoStatus" name="inputSolicitacaoStatus" >					
					<input type="hidden" id="inputMotivo" name="inputMotivo" >
				</form>				
				
			</div>
			<!-- /content area -->

			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->
	
	<?php include_once("alerta.php"); ?>

</body>
</html>
