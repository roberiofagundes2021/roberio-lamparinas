<?php

	print('
	<tr class="table-active table-border-double">
		<td colspan="3">Ações Aguardando Liberação</td>
		<td class="text-right">
			<span class="badge bg-blue badge-pill">'.$totalPendente.'</span>
		</td>
	</tr>');
								
	foreach ($rowPendente as $item){ 
		
		if ($item['Intervalo'] > 1){
			$dias = 'dias';
		} else{
			$dias = 'dia';
		}

		print('
		<tr>
			<td class="text-center">
				<h6 class="mb-0" data-popup="tooltip" data-placement="bottom" data-container="body" title="'.mostradata($item['BandeData']).'">'.$item['Intervalo'].'</h6>
				<div class="font-size-sm text-muted line-height-1">'.$dias.'</div>
			</td>
			<td>
				<div class="d-flex align-items-center">
					<div class="mr-3">
						<a href="#" class="btn bg-teal-400 rounded-round btn-icon btn-sm">
							<span class="letter-icon"></span>
						</a>
					</div>
					<div>
						<a href="#" class="text-default font-weight-semibold letter-icon-title">'.nomeSobrenome($item['UsuarNome'], 2).'</a>
						<div class="text-muted font-size-sm"><span class="badge badge-mark border-blue mr-1"></span> '.$item['SituaNome'].'</div>
					</div>
				</div>
			</td>
			<td>
				<a href="#" class="text-default">
					<div class="font-weight-semibold">[#'.$item['BandeTabelaId'].'] '.$item['BandeIdentificacao'].'</div>
					<span class="text-muted">Ação: '.$item['BandeDescricao'].'</span>
				</a>
			</td>
			<td class="text-center">
				<div class="list-icons">
					<div class="list-icons-item dropdown">
						<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu7"></i></a>
						<div class="dropdown-menu dropdown-menu-right">
							<a href="#" onclick="atualizaBandeja('.$item['BandeId'].',\''.$item['BandeTabela'].'\','.$item['BandeTabelaId'].', \''.$item['MovimTipo'].'\', \'imprimir\');" class="dropdown-item"><i class="icon-printer2"></i> Visualizar</a>
							
							<div class="dropdown-divider"></div>							
							<a href="#" onclick="atualizaBandeja('.$item['BandeId'].',\''.$item['BandeTabela'].'\','.$item['BandeTabelaId'].', \''.$item['MovimTipo'].'\', \'liberar\');" class="dropdown-item"><i class="icon-checkmark3 text-success"></i> Liberar</a>
							<a href="#" onclick="atualizaBandeja('.$item['BandeId'].',\''.$item['BandeTabela'].'\','.$item['BandeTabelaId'].', \''.$item['MOvimTipo'].'\', \'naoliberar\');" class="dropdown-item" id="motivo"><i class="icon-cross2 text-danger"></i> Não Liberar</a>
						</div>
					</div>
				</div>
			</td>
		</tr>
		'); 
   }

?>