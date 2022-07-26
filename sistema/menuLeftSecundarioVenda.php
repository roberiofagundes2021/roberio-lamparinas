<?php
	// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

	$sql = "SELECT AtClaNome, AtClaChave, AtendCliente, ClienCodigo, ClienNome
	FROM Atendimento
	JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
	JOIN Cliente ON ClienId = AtendCliente
	WHERE AtendId = $iAtendimentoId";
	$result = $conn->query($sql);
	$rowClassificacao = $result->fetch(PDO::FETCH_ASSOC);

	$ClaChave = $rowClassificacao['AtClaChave'];
	$ClaNome = $rowClassificacao['AtClaNome'];
	$prontuario = $rowClassificacao['ClienCodigo'];
	$Cliente = $rowClassificacao['ClienNome'];
?>

<script language ="javascript">

	$(document).ready(function() {
		$('.itemLink').each(function() {
			$(this).click(function(e) {
				e.preventDefault()
				let tipo = $(this).data('tipo')
				let URL = ''

				switch(tipo){
					case 'atendimentoEletivo': URL = 'atendimentoEletivo.php'; break;
					case 'receituario': URL = 'atendimentoReceituario.php'; break;
					case 'atestadoMedico': URL = 'atendimentoAtestadoMedico.php'; break;
					case 'encaminhamentoMedico': URL = 'atendimentoEncaminhamentoMedico.php'; break;
					default: URL = ''; console.log(tipo); return; break;
				}
				$('#dadosPost').attr('action', URL)
				$('#dadosPost').attr('method', 'POST')
				$('#dadosPost').submit()
			})
		})

		$('#finalizarAtendimento').on('click', function(){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'MUDARSITUACAO',
					'iAtendimento': $('#iAtendimentoId').val(),
					'sSituacao': 'ATENDIDOVENDA'
				},
				success: function(response) {
					window.location.href = 'atendimento.php';
					alerta(response.titulo, response.menssagem, response.tipo);
				},
				error: function(response) {
					alerta(response.titulo, response.menssagem, response.tipo);
				}
			});
		})
	})

</script>

<!-- Secondary sidebar -->
<div class="sidebar sidebar-light sidebar-secondary sidebar-expand-md">
	<!-- Sidebar mobile toggler -->
	<div class="sidebar-mobile-toggler text-center">
		<a href="#" class="sidebar-mobile-secondary-toggle">
			<i class="icon-arrow-left8"></i>
		</a>
		<span class="font-weight-semibold">Secondary sidebar</span>
		<a href="#" class="sidebar-mobile-expand">
			<i class="icon-screen-full"></i>
			<i class="icon-screen-normal"></i>
		</a>
	</div>
	<!-- /sidebar mobile toggler -->

	<!-- Sidebar content -->
	<div class="sidebar-content">
		<!-- Sub navigation -->
		<div class="card mb-2">
			<div class="card-body p-0">
				<?php if($ClaChave == 'AMBULATORIAL'){?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><b><?php echo "".strtoupper($ClaNome); ?></b></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item-header"><?php echo strtoupper($Cliente). "<br>Prontuário: " .$prontuario ; ?></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atendimentoEletivo'><i class="icon-certificate"></i> Atendimento Eletivo</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='triagem'><i class="icon-home7"></i> Triágem</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='receituario'><i class="icon-cabinet"></i> Receituário</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atestadoMedico'><i class="icon-box"></i> Atestado Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='historicoPaciente'><i class="icon-equalizer"></i> Histórico do Paciente</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoExame'><i class="icon-office"></i> Solicitação de Exame</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='encaminhamentoMedico'><i class="icon-office"></i> Encaminhamento Médico</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='exportacaoProntuario'><i class="icon-office"></i> Exportação do Prontuário</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='prescricaoMedica'><i class="icon-office"></i> Prescrição Médica</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='tabelaGastos'><i class="icon-office"></i> Tabela de Gastos</a>
						</li>

						<li class="nav-item-divider"></li>

						<li class="nav-item pt-3">
							<div class="col-lg-12">
								<button class="btn w-100 btn-lg btn-principal" id="finalizarAtendimento">Finalizar atendimento</button>
							</div>
						</li>
					</ul>
				<?php }elseif($ClaChave == 'ELETIVO'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo "".strtoupper($ClaNome)." - ".strtoupper($Cliente); ?></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atendimentoEletivo'><i class="icon-certificate"></i> Atendimento Eletivo</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='triagem'><i class="icon-home7"></i> Triágem</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='receituario'><i class="icon-cabinet"></i> Receituário</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atestadoMedico'><i class="icon-box"></i> Atestado Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='historicoPaciente'><i class="icon-equalizer"></i> Histórico do Paciente</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoExame'><i class="icon-office"></i> Solicitação de Exame</a>
						</li>
						
						<li class="nav-item-divider"></li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='exportacaoProntuario'><i class="icon-office"></i> Exportação do Prontuário</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='prescricaoMedica'><i class="icon-office"></i> Prescrição Médica</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='tabelaGastos'><i class="icon-office"></i> Tabela de Gastos</a>
						</li>

						<li class="nav-item-divider"></li>

						<li class="nav-item pt-3">
							<div class="col-lg-12">
								<button class="btn w-100 btn-lg btn-principal" id="finalizarAtendimento">Finalizar atendimento</button>
							</div>
						</li>
					</ul>
				<?php }elseif($ClaChave == 'INTERNACAO'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo "".strtoupper($ClaNome)." - ".strtoupper($Cliente); ?></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='leitos'><i class="icon-certificate"></i> Leitos</a>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link legitRipple">Ato de Enfermagem</a>
							<ul class="nav nav-group-sub">
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='admissao'><i class="icon-certificate"></i> Admissão</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='evolucaoDiaria'><i class="icon-certificate"></i> Evolução diária</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='formularios'><i class="icon-certificate"></i> Formularios</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='relatorioAta'><i class="icon-certificate"></i> Relatórios de Ata</a>
								</li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link legitRipple">Ato de Médico</a>
							<ul class="nav nav-group-sub">
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='prescricaoHospitalar'><i class="icon-certificate"></i> Prescrição Hospitalar</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='AIH'><i class="icon-certificate"></i> AIH</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='evolucaoDiaria'><i class="icon-certificate"></i> Evolução Diária</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='interconsulta'><i class="icon-certificate"></i> Interconsulta</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='encaminhamento'><i class="icon-certificate"></i> Encaminhamento</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='solicitacaoExame'><i class="icon-certificate"></i> Solicitação de Exames</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='relatorioAtaHospitalar'><i class="icon-certificate"></i> Relatório de Ata Hospitalar</a>
								</li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link legitRipple">Ato de Multidiciplinar</a>
							<ul class="nav nav-group-sub">
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='admissao'><i class="icon-certificate"></i> Admissão</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='evolucaoDiaria'><i class="icon-certificate"></i> Evolução Diária</a>
								</li>
								<li class="nav-item">
									<a href="#" class="nav-link itemLink" data-tipo='formularios'><i class="icon-certificate"></i> Formulários</a>
								</li>
							</ul>
						</li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='tabelaGastos'><i class="icon-certificate"></i> Tabela de Gastos</a>
						</li>

						<li class="nav-item-divider"></li>

						<li class="nav-item pt-3">
							<div class="col-lg-12">
								<button class="btn w-100 btn-lg btn-principal" id="finalizarAtendimento">Finalizar atendimento</button>
							</div>
						</li>
					</ul>
				<?php }else{irpara("atendimento.php");} ?>
			</div>
		</div>
		<!-- /sub navigation -->

	</div>
	<!-- /sidebar content -->
</div>
<!-- /secondary sidebar -->
