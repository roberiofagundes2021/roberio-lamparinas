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
					case 'atendimentoEletivo': URL = 'atendimentoEletivo.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'atestadoMedico': URL = 'atendimentoAtestadoMedico.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'historicoPaciente': URL = 'atendimentoHistoricoPaciente.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'encaminhamentoMedico': URL = 'atendimentoEncaminhamentoMedico.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'observacaoHospitalar': URL = 'atendimentoObservacaoHospitalar.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'solicitacaoExame': URL = 'atendimentoSolicitacaoExame.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'solicitacaoProcedimento': URL = 'atendimentoSolicitacaoProcedimento.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'triagem': URL = 'atendimentoTriagem.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'atendimentoAmbulatorial': URL = 'atendimentoAmbulatorial.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'exportacaoProntuario': URL = 'atendimentoProntuarioExportacao.php'; $('#dadosPost').attr('target', '_blank'); break;
					case 'tabelaGastos': URL = 'atendimentoTabelaGastos.php'; $('#dadosPost').attr('target', '_self'); break;
					case 'documento': URL = 'atendimentoDocumentos.php'; $('#dadosPost').attr('target', '_self'); break;
					default: URL = ''; console.log(tipo); return; break;
				}
				$('#dadosPost').attr('action', URL)
				$('#dadosPost').attr('method', 'POST')
				$('#dadosPost').submit()
			})
		})

		$('#finalizarAtendimento').on('click', function(e){
			e.preventDefault()
			$('#dadosPost').attr('action', 'atendimentoFinalizar.php')
			$('#dadosPost').attr('method', 'POST')
			$('#dadosPost').submit()
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
						<li style="padding: 20px 0px 0px 20px;"><h2 style="font-weight: 500"><?php echo "".strtoupper($ClaNome); ?></b></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item-header"><?php echo strtoupper($Cliente). "<br>Prontuário: " .$prontuario ; ?></li>

						<li class="nav-item-divider"></li>

						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atendimentoAmbulatorial'><i class="icon-certificate"></i> Atendimento Ambulatorial</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='triagem'><i class="icon-home7"></i> Triagem</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='documento'><i class="icon-file-text"></i> Documentos</a>
						</li>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='observacaoHospitalar'><i class="icon-file-eye"></i> Observação Hospitalar</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atestadoMedico'><i class="icon-box"></i> Atestado Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='historicoPaciente'><i class="icon-equalizer"></i> Histórico do Paciente</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoExame'><i class="icon-copy"></i> Solicitação de Exames</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoProcedimento'><i class="icon-copy"></i> Solicitação de Procedimentos</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='encaminhamentoMedico'><i class="icon-folder-plus4"></i> Encaminhamento Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='exportacaoProntuario'><i class="icon-drawer-out"></i> Exportação do Prontuário</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='prescricaoMedica'><i class="icon-file-text2"></i> Prescrição Médica</a>
						</li>	
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='tabelaGastos'><i class="icon-table2"></i> Tabela de Gastos</a>
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
					<li style="padding: 20px 0px 0px 20px;"><h2 style="font-weight: 500"><?php echo "".strtoupper($ClaNome); ?></b></li>

					<li class="nav-item-divider"></li>

					<li class="nav-item-header"><?php echo strtoupper($Cliente). "<br>Prontuário: " .$prontuario ; ?></li>

					<li class="nav-item-divider"></li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atendimentoEletivo'><i class="icon-certificate"></i> Atendimento Eletivo</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='triagem'><i class="icon-home7"></i> Triagem</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='documento'><i class="icon-file-text"></i> Documentos</a>
						</li>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='atestadoMedico'><i class="icon-box"></i> Atestado Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='encaminhamentoMedico'><i class="icon-folder-plus4"></i> Encaminhamento Médico</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='historicoPaciente'><i class="icon-equalizer"></i> Histórico do Paciente</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoExame'><i class="icon-office"></i> Solicitação de Exames</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link itemLink" data-tipo='solicitacaoProcedimento'><i class="icon-office"></i> Solicitação de Procedimentos</a>
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
					<li style="padding: 20px 0px 0px 20px;"><h2 style="font-weight: 500"><?php echo "".strtoupper($ClaNome); ?></b></li>

					<li class="nav-item-divider"></li>

					<li class="nav-item-header"><?php echo strtoupper($Cliente). "<br>Prontuário: " .$prontuario ; ?></li>

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
									<a href="#" class="nav-link itemLink" data-tipo='observacaoHospitalar'><i class="icon-file-eye"></i> Observação Hospitalar</a>
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
									<a href="#" class="nav-link itemLink" data-tipo='solicitacaoProcedimento'><i class="icon-certificate"></i> Solicitação de Procedimentos</a>
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
