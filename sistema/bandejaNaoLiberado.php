<?php

	print('
	<tr class="table-active table-border-double">
		<td colspan="3">Ações Não Liberadas</td>
		<td class="text-right">
			<span class="badge bg-danger badge-pill">'.$totalNaoLiberado.'</span>
		</td>
	</tr>');
	
	foreach ($rowNaoLiberado as $item){ 

		print('								
		<tr>
			<td class="text-center">
				<i class="icon-cross2 text-danger-400"></i>
			</td>
			<td>
				<div class="d-flex align-items-center">
					<div class="mr-3">
						<a href="#">
							<img src="global_assets/images/placeholders/placeholder.jpg" class="rounded-circle" width="32" height="32" alt="">
						</a>
					</div>
					<div>
						<a href="#" class="text-default font-weight-semibold">'.nomeSobrenome($item['UsuarNome'], 2).'</a>
						<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> '.$item['SituaNome'].'</div>
					</div>
				</div>
			</td>
			<td>
				<a href="#" class="text-default">
					<div>[#'.$item['BandeTabelaId'].'] '.$item['BandeIdentificacao'].'</div>
					<span class="text-muted">Ação: '.$item['BandeDescricao'].'</span>
				</a>
			</td>
			<td class="text-center">
				<div class="list-icons" style="display:none;">
					<div class="list-icons-item dropdown">
						<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu7"></i></a>
						<div class="dropdown-menu dropdown-menu-right">
							<a href="#" class="dropdown-item"><i class="icon-undo"></i> Quick reply</a>
							<a href="#" class="dropdown-item"><i class="icon-history"></i> Full history</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item"><i class="icon-plus3 text-blue"></i> Unresolve issue</a>
							<a href="#" class="dropdown-item"><i class="icon-spinner11 text-grey"></i> Reopen issue</a>
						</div>
					</div>
				</div>
			</td>
		</tr>
		');
	}

?>