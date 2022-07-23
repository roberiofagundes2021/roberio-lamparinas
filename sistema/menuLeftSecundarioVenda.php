<?php
	// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

	$sql = "SELECT AtClaNome, AtClaChave
	FROM Atendimento
	JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
	WHERE AtendId = $iAtendimentoId";
	$result = $conn->query($sql);
	$rowClassificacao = $result->fetch(PDO::FETCH_ASSOC);

	$ClaChave = $rowClassificacao['AtClaChave'];
	$ClaNome = $rowClassificacao['AtClaNome'];
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

					case 'triagem': URL = 'atendimentoEletivo.php'; break;
					case 'atestadoMedico': URL = 'atendimentoEletivo.php'; break;
					case 'historicoPaciente': URL = 'atendimentoEletivo.php'; break;
					case 'solicitacaoExame': URL = 'atendimentoEletivo.php'; break;
					case 'encaminhamentoMedico': URL = 'atendimentoEletivo.php'; break;
					case 'exportacaoProntuario': URL = 'atendimentoEletivo.php'; break;
					case 'prescricaoMedica': URL = 'atendimentoEletivo.php'; break;
					case 'tabelaGastos': URL = 'atendimentoEletivo.php'; break;
				}

				$('#dadosPost').attr('action', URL)
				$('#dadosPost').attr('method', 'POST')
				$('#dadosPost').submit()
			})
		})
	})

	function getAttributs() {
		
	}

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
						<li class="nav-item-header"><?php echo strtoupper($ClaNome); ?></li>

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
					</ul>
				<?php }elseif($ClaChave == 'ELETIVO'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo strtoupper($ClaNome); ?></li>
					</ul>
				<?php }elseif($ClaChave == 'ELETIVO'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo strtoupper($ClaNome); ?></li>
					</ul>
				<?php }elseif($ClaChave == 'INTERNACAO'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo strtoupper($ClaNome); ?></li>
					</ul>
				<?php }elseif($ClaChave == 'AMBULATORIAL'){ ?>
					<ul class="nav nav-sidebar" data-nav-type="accordion">
						<li class="nav-item-header"><?php echo strtoupper($ClaNome); ?></li>
					</ul>
				<?php } ?>

			</div>
		</div>
		<!-- /sub navigation -->

	</div>
	<!-- /sidebar content -->
</div>
<!-- /secondary sidebar -->
