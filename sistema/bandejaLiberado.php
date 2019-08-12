<?php

	print('
	<tr class="table-active table-border-double">
		<td colspan="3">Ações Liberadas</td>
		<td class="text-right">
			<span class="badge bg-success badge-pill">'.$totalLiberado.'</span>
		</td>
	</tr>');
	
	foreach ($rowLiberado as $item){ 

		print('
		<tr>
			<td class="text-center">
				<i class="icon-checkmark3 text-success"></i>
			</td>
			<td>
				<div class="d-flex align-items-center">
					<div class="mr-3">
						<a href="#" class="btn bg-success-400 rounded-round btn-icon btn-sm">
							<span class="letter-icon"></span>
						</a>
					</div>
					<div>
						<a href="#" class="text-default font-weight-semibold letter-icon-title">'.nomeSobrenome($item['UsuarNome'], 2).'</a>
						<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> '.$item['SituaNome'].'</div>
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
				<div class="list-icons">
					<div class="list-icons-item dropdown">
						<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu7"></i></a>
						<div class="dropdown-menu dropdown-menu-right">
							<a href="#" class="dropdown-item"><i class="icon-undo"></i> Quick reply</a>
							<a href="#" class="dropdown-item"><i class="icon-history"></i> Full history</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item"><i class="icon-plus3 text-blue"></i> Unresolve issue</a>
							<a href="#" class="dropdown-item"><i class="icon-cross2 text-danger"></i> Close issue</a>
						</div>
					</div>
				</div>
			</td>
		</tr>
		');
	}

?>