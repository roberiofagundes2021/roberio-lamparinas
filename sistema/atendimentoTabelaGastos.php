<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Tabela de Gastos';

include('global_assets/php/conexao.php');

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if (isset($_SESSION['iAtendimentoId']) && $iAtendimentoId == null) {
	$iAtendimentoId = $_SESSION['iAtendimentoId'];
}
$_SESSION['iAtendimentoId'] = null;

if(!$iAtendimentoId){
	$uTipoAtendimento = $_SESSION['UltimaPagina'];

	if ($uTipoAtendimento == "ELETIVO") {
		irpara("atendimentoEletivoListagem.php");
	} elseif ($uTipoAtendimento == "AMBULATORIAL") {
		irpara("atendimentoAmbulatorialListagem.php");
	} elseif ($uTipoAtendimento == "INTERNACAO") {
		irpara("atendimentoHospitalarListagem.php");
	}	
}

// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';

$_SESSION['atendimentoTabelaServicos'] = [];
$_SESSION['atendimentoTabelaProdutos'] = [];

//Essa consulta é para verificar  o profissional
$sql = "SELECT UsuarId, A.ProfiUsuario, A.ProfiId as ProfissionalId, A.ProfiNome as ProfissionalNome, PrConNome, B.ProfiCbo as ProfissaoCbo
		FROM Usuario
		JOIN Profissional A ON A.ProfiUsuario = UsuarId
		LEFT JOIN Profissao B ON B.ProfiId = A.ProfiProfissao
		LEFT JOIN ProfissionalConselho ON PrConId = ProfiConselho
		WHERE UsuarId =  ". $_SESSION['UsuarId'] . " ";
$result = $conn->query($sql);
$rowUser = $result->fetch(PDO::FETCH_ASSOC);
$userId = $rowUser['ProfissionalId'];

//Essa consulta é para verificar qual é o atendimento e cliente 
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtClaNome, AtendDataRegistro, AtModNome, ClienId,
		ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,ClienNomeMae, ClienCartaoSus, ClienCelular,
		ClResNome, AtClaChave
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		LEFT JOIN AtendimentoClassificacao ON AtClaId = AtendClassificacao
		LEFT JOIN Situacao ON SituaId = AtendSituacao
	    WHERE AtendId = $iAtendimentoId and AtendUnidade = ".$_SESSION['UnidadeId']."
		ORDER BY AtendDataRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoId = $row['AtendId'];
$iClienteId = $row['ClienId'];

$sql = "SELECT AtTGaId, AtTGaAtendimento, AtTGaDataRegistro, AtTGaServico, AtTGaProfissional, AtTGaHorario, 
               AtTGaValor, AtTGaDesconto, AtTGaDesconto, AtendCliente, AtendDataRegistro, SrVenNome, ProfiNome
		FROM AtendimentoTabelaGasto
		JOIN Atendimento ON AtendId = AtTGaAtendimento
		JOIN Cliente ON ClienId = AtendCliente
		JOIN ServicoVenda ON SrVenId = AtTGaServico
		JOIN Profissional ON ProfiId = AtTGaProfissional
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE AtendCliente = $iClienteId and AtTGaUnidade = ".$_SESSION['UnidadeId']."
		ORDER BY AtTGaDataRegistro ASC";
$result = $conn->query($sql);
$rowTGasto = $result->fetchAll(PDO::FETCH_ASSOC);


$iAtendimentoHistoricoId = $row['AtendId'];

//Essa consulta é para preencher o sexo
if ($row['ClienSexo'] == 'F'){
    $sexo = 'Feminino';
} else{
    $sexo = 'Masculino';
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Tabela de Gastos</title>

	<?php include_once("head.php"); ?>
	
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/lamparinas/components.min.css" rel="stylesheet" type="text/css">

	<script src="global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/plugins/ui/ripple.min.js"></script>

	<script src="global_assets/js/plugins/forms/wizards/steps.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/inputmask.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>

	<!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>	

	<!-- Modal -->
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
    
    <!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<?php
		// essa parte do código transforma uma variáve php em Js para ser utilizado 
		echo '<script>
				var atendimento = '.json_encode($row).';
			</script>';
	?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			getCmbs()
			checkServicos()
			checkProdutos()			

            /* Início: Tabela Personalizada */
			$('#tblTabelaGastos').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
				searching: false,
				ordering: false, 
				paging: false,
			    columnDefs: [
				{ 
					orderable: true,   //item
					width: "5%", //15
					targets: [0]
				},
				{ 
					orderable: true,   //data-hora
					width: "15%", //20
					targets: [1]
				},
				{ 
					orderable: true,   //grupo
					width: "10%", //15
					targets: [2]
				},				
				{ 
					orderable: true,   //subgrupo
					width: "10%", //15
					targets: [3]
				},
				{ 
					orderable: true,   //procedimento
					width: "30%", //15
					targets: [4]
				},
				{ 
					orderable: true,   //Valor
					width: "10%",
					targets: [5]
				},
				{ 
					orderable: false,   //Ações
					width: "10%",
					targets: [6]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer">',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
                
			});

			/* Início: Tabela Personalizada */
			$('#tblTabelaGastosProduto').DataTable( {
				"order": [[ 0, "asc" ]],
			    autoWidth: false,
				responsive: true,
				searching: false,
				ordering: false, 
				paging: false,
			    columnDefs: [
				{ 
					orderable: true,   //item
					width: "5%", //15
					targets: [0]
				},
				{ 
					orderable: true,   //data-hora
					width: "10%", //20
					targets: [1]
				},
				{ 
					orderable: true,   //codigo
					width: "10%", //15
					targets: [2]
				},				
				{ 
					orderable: true,   //produto
					width: "35%", //15
					targets: [3]
				},
				{ 
					orderable: true,   //valor
					width: "10%", //15
					targets: [4]
				},
				{ 
					orderable: true,   //acoes
					width: "10%",
					targets: [5]
				}],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer">',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
                
			});

			divTotal = `<div class="row " style="padding-right: 13%;">
                            <div class="col-lg-9">
								<button class="btn btn-lg btn-principal fecharConta" id="">Fechar Conta</button>
								<a href="atendimento.php" class="btn btn-basic" role="button">Voltar</a>
							</div>
							<div id="tabelaValores" class="col-lg-3 text-right " >	
								<div style='font-weight: bold;'>Desconto: </div> <br> 
								<div style='font-weight: bold;'>TOTAL A PAGAR: </div>
							</div>
						</div> <br>`
			
			divTotalP = `<div class="row" style="padding-right: 14%;">
                            <div class="col-lg-9">
								<button class="btn btn-lg btn-principal fecharConta" id="">Fechar Conta</button>
								<a href="atendimento.php" class="btn btn-basic" role="button">Voltar</a>
							</div>
							<div id="tabelaValoresProdutos" class="col-lg-3 text-right" >	
								<div style='font-weight: bold;'>Desconto: </div> <br> 
								<div style='font-weight: bold;'>TOTAL A PAGAR: </div>
							</div>
						</div> <br>`

			$('.footerProcedimento').append(divTotal);
        	$('.footerProduto').append(divTotalP);
			

			$('#inserirServico').on('click',function(e){
				$('#formTabelaGastos').submit()
			})

			$('#inserirProduto').on('click',function(e){
				$('#formTabelaGastosProduto').submit()
			})

			$('#modal-close-x').on('click', function(e){
				e.preventDefault()
				$('#pageModalDescontos').fadeOut(200)
			})
			$('#modal-close-x-p').on('click', function(e){
				e.preventDefault()
				$('#pageModalDescontosProduto').fadeOut(200)
			})

			$('#formTabelaGastos').submit(function(e){
				e.preventDefault()
				let menssageError = ''

				let grupo = $('#grupo').val()
				let subgrupo = $('#subgrupo').val()
				let procedimentos  = $('#procedimentos').val()

				let profissional = <?php echo $userId; ?>

				switch(menssageError){
					case grupo: menssageError = 'informe o grupo'; $('#grupo').focus();break;
					case subgrupo: menssageError = 'informe o subgrupo'; $('#subgrupo').focus();break;
					case procedimentos: menssageError = 'informe o procedimento'; $('#procedimentos').focus();break;
					
					default: menssageError = ''; break;
				}

				if(menssageError){
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADICIONARSERVICO',

						'grupo' : grupo,
						'subgrupo' : subgrupo,
						'servico': procedimentos,
						'medicos': profissional
					},
					success: function(response) {
						if(response.status == 'success'){
							getCmbs()
							checkServicos()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status);
						}
					},
					error: function(response) {
						alerta(response.titulo, response.menssagem, response.status);
					}
				});
			})

			$('#formTabelaGastosProduto').submit(function(e){
				e.preventDefault()
				let menssageError = ''

				let produtos  = $('#produtos').val()
				let profissional = <?php echo $userId; ?>

				switch(menssageError){
					case produtos: menssageError = 'informe o Produto'; $('#procedimentos').focus();break;					
					default: menssageError = ''; break;
				}

				if(menssageError){
					alerta('Campo Obrigatório!', menssageError, 'error')
					return
				}

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'ADICIONARPRODUTO',
						'servico': produtos,
						'medicos': profissional
					},
					success: function(response) {
						if(response.status == 'success'){
							getCmbs()
							checkProdutos()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status);
						}
					},
					error: function(response) {
						alerta(response.titulo, response.menssagem, response.status);
					}
				});
			})



			$('#grupo').on('change', function() {

				let idGrupo = $(this).val();				

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SUBGRUPOS',
						'idGrupo' : idGrupo						 
					},
					success: function(response) {
						$('#subgrupo').empty();
						$('#subgrupo').append(`<option value=''>Selecione</option>`)

						response.forEach(item => {
							let opt = `<option value="${item.id}">${item.nome}</option>`
							$('#subgrupo').append(opt)
						})
						$('#subgrupo').focus();
						
					}
					
				})
			})

			$('#subgrupo').on('change', function() {

				let idSubGrupo = $(this).val();				

				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'PROCEDIMENTOS',
						'idSubGrupo' : idSubGrupo						 
					},
					success: function(response) {
						$('#procedimentos').empty();
						$('#procedimentos').append(`<option value=''>Selecione</option>`)

						response.forEach(item => {
							let opt = `<option value="${item.id}">${item.nome}</option>`
							$('#procedimentos').append(opt)
						})
						$('#procedimentos').focus();
						
					}
					
				})
			})

			$('.fecharConta').on('click', function(e){
				e.preventDefault();
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'FECHARCONTA',
						'atendimento': atendimento.AtendId,
						'cliente': atendimento.ClienId,
					},
					success: function(response) {
						if(response.status == 'success'){
							getCmbs()
							checkServicos()
							checkProdutos()
							window.location.reload()
							alerta(response.titulo, response.menssagem, response.status)
						} else {
							alerta(response.titulo, response.menssagem, response.status)
						}
					}
				});
			})

			$('#setDesconto').on('click', function(e){
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SETDESCONTO',
						'id':$('#itemId').val(),
						'desconto':$('#inputDesconto').val(),
					},
					success: async function(response) {
						checkServicos()
						$('#pageModalDescontos').fadeOut(200)
						alerta(response.titulo, response.menssagem, response.status)
					}
				});
			})

			$('#setDescontoProduto').on('click', function(e){
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimentoTabelaGastos.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SETDESCONTOPRODUTO',
						'id':$('#itemIdp').val(),
						'desconto':$('#inputDescontop').val(),
					},
					success: async function(response) {
						checkProdutos()
						$('#pageModalDescontosProduto').fadeOut(200)
						alerta(response.titulo, response.menssagem, response.status)
					}
				});
			})

			$('#inputDesconto').on('keyup', function(e){
				let novoValor = parseFloat($('#itemModalValue').val()) - ($('#inputDesconto').val().replaceAll('.', '').replace(',', '.')?parseFloat($('#inputDesconto').val().replaceAll('.', '').replace(',', '.')):0)
				$('#inputModalValorF').val(`R$ ${float2moeda(novoValor)}`)
			})

			$('#inputDescontop').on('keyup', function(e){
				let novoValor = parseFloat($('#itemModalValuep').val()) - ($('#inputDescontop').val().replaceAll('.', '').replace(',', '.')?parseFloat($('#inputDescontop').val().replaceAll('.', '').replace(',', '.')):0)
				$('#inputModalValorFp').val(`R$ ${float2moeda(novoValor)}`)
			})

			
		}); //document.ready

		function getCmbs(){
			// vai preencher PRODUTOS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'PRODUTOS'
				},
				success: function(response) {
					$('#produtos').empty();
					$('#produtos').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = ''
						// caso exista algo na variável atendimento significa que o usuário esta alterando um valor
						// logo esses valores deveram vir preenchido com os dados desse atendimento
						if(atendimento){
							 opt = atendimento.AtendModalidade == item.id?`<option selected value="${item.id}">${item.nome}</option>`:`<option value="${item.id}">${item.nome}</option>`
						} else {
							opt = `<option value="${item.id}">${item.nome}</option>`
						}
						$('#produtos').append(opt)
					})
				}
			});
			// vai preencher GRUPOS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'GRUPOS'
				},
				success: function(response) {
					$('#grupo').empty();
					$('#grupo').append(`<option value=''>Selecione</option>`)
				
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#grupo').append(opt)
					})
					
				}
			});
		}

		function checkServicos(){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'CHECKSERVICO',
					'iAtendimento': atendimento?atendimento['AtendId']:''
				},
				success: async function(response) {
					statusServicos = response.array.length?true:false;
					if(statusServicos){
						$('#dataServico').html('');

						let HTML = ''
						let i = 1;
						response.array.forEach(item => {
							if(item.status != 'rem'){
								let exc = `<a class='list-icons-item removeItem' style='color: black; cursor:pointer' data-id="${item.id}" data-valor="${item.valor}"><i class='icon-bin' title='Excluir Atendimento'></i></a>`;
								let popup = `<a class='list-icons-item openPopUp' style="color:${(item.desconto?'#50b900':'#000')}; cursor:pointer" data-value="${float2moeda(item.valor)}" data-titulo="${item.servico}" data-id="${item.id}"><i class='icon-cash' title='Desconto'></i></a>`;
								
								let acoes = `<div class='list-icons'>
											${popup}
											${exc}
										</div>`;
								HTML += `
								<tr class='servicoItem'>
									<td class="text-left">${i}</td>
									<td class="text-left">${item.sData} - ${item.hora}</td>
									<td class="text-left">${item.grupo}</td>
									<td class="text-left">${item.subgrupo}</td>
									<td class="text-left">${item.servico}</td>
									<td class="text-right">R$ ${float2moeda(item.valor)}</td>
									<td class="text-center">${acoes}</td>
								</tr>`
							}
							i++;
						})
						$('#tabelaValores').html(`
							<div style='font-weight: bold;'>Desconto: R$${float2moeda(response.desconto)}</div> <br> 
							<div style='font-weight: bold;'>TOTAL A PAGAR: R$${float2moeda(response.valorTotal)}</div>
						`)
						$('#dataServico').html(HTML).show();
						$('#servicoTable').show();

						$('.openPopUp').each(function(index,element){
							$(element).on('click', function(e){
								e.preventDefault()
								$('#itemId').val($(this).data('id'))
								$('#inputModalValorB').val(`R$ ${$(this).data('value')}`)
								$('#itemModalValue').val($(this).data('value'))
								let Val1 = parseFloat($(this).data('value'))

								$('#tituloModal').html(`Descontos do serviço <strong>${$(this).data('titulo')}</strong>`)
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'GETDESCONTO',
										'id': $(this).data('id')
									},
									success: async function(response) {
										if(response.status == 'success'){
											if (response.desconto != 0) {
												$('#inputDesconto').val(response.desconto)
											}
											let Val2 = parseFloat(response.desconto)
											console.log(Val1)
											console.log(Val2)
											$('#inputModalValorF').val(`R$ ${float2moeda(Val1 - Val2)}`)
											$('#pageModalDescontos').fadeIn(200)
										}else{
											alerta(response.titulo, response.menssagem, response.status)
										}
									}
								});
							})
						})

						$('.removeItem').each(function(index,element){
							$(element).on('click', function(e){
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'EXCLUISERVICO',
										'id': $(this).data('id'),
										'valor': $(this).data('valor')
									},
									success: function(response) {
										alerta(response.titulo, response.menssagem, response.status)
										checkServicos()
									}
								});
							})
						})
					}else{
						$('#servicoTable').hide();
					}
				}
			});
		}

		//check produtos
		function checkProdutos(){			
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimentoTabelaGastos.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'CHECKPRODUTO',
					'iAtendimento': atendimento?atendimento['AtendId']:''
				},
				success: async function(response) {
					statusProdutos = response.array.length?true:false;
					if(statusProdutos){
						$('#dataProduto').html('');

						let HTML = ''
						let i = 1;
						response.array.forEach(item => {
							if(item.status != 'rem'){
								let exc = `<a class='list-icons-item removeItemP' style='color: black; cursor:pointer' data-id="${item.id}" data-valor="${item.valor}"><i class='icon-bin' title='Excluir Produto'></i></a>`;
								let popup = `<a class='list-icons-item openPopUpProduto' style="color:${(item.desconto?'#50b900':'#000')}; cursor:pointer" data-value="${float2moeda(item.valor)}" data-titulo="${item.servico}" data-id="${item.id}"><i class='icon-cash' title='Desconto'></i></a>`;
								
								let acoes = `<div class='list-icons'>
											${popup}
											${exc}
										</div>`;
								HTML += `
								<tr class='servicoItem'>
									<td class="text-left">${i}</td>
									<td class="text-left">${item.sData} - ${item.hora}</td>
									<td class="text-left">${item.codigo}</td>
									<td class="text-left">${item.servico}</td>
									<td class="text-right">R$ ${float2moeda(item.valor)}</td>
									<td class="text-center">${acoes}</td>
								</tr>`
							}
							i++;
						})
				
						$('#tabelaValoresProdutos').html(`
							<div style='font-weight: bold;'>Desconto: R$${float2moeda(response.desconto)}</div> <br> 
							<div style='font-weight: bold;'>TOTAL A PAGAR: R$${float2moeda(response.valorTotal)}</div>
						`)
						$('#dataProduto').html(HTML).show();
						$('#produtoTable').show();

						$('.openPopUpProduto').each(function(index,element){
							$(element).on('click', function(e){
								e.preventDefault()
								$('#itemIdp').val($(this).data('id'))
								$('#inputModalValorBp').val(`R$ ${$(this).data('value')}`)
								$('#itemModalValuep').val($(this).data('value'))
								let Val1 = parseFloat($(this).data('value'))

								$('#tituloModalp').html(`Descontos do produto <strong>${$(this).data('titulo')}</strong>`)
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'GETDESCONTOPRODUTO',
										'id': $(this).data('id')
									},
									success: async function(response) {
										if(response.status == 'success'){
											if (response.desconto != 0) {
												$('#inputDescontop').val(response.desconto)
											}
											let Val2 = parseFloat(response.desconto)
											console.log(Val1)
											console.log(Val2)
											$('#inputModalValorFp').val(`R$ ${float2moeda(Val1 - Val2)}`)
											$('#pageModalDescontosProduto').fadeIn(200)
										}else{
											alerta(response.titulo, response.menssagem, response.status)
										}
									}
								});
							})
						})

						$('.removeItemP').each(function(index,element){
							$(element).on('click', function(e){
								$.ajax({
									type: 'POST',
									url: 'filtraAtendimentoTabelaGastos.php',
									dataType: 'json',
									data:{
										'tipoRequest': 'EXCLUIPRODUTO',
										'id': $(this).data('id'),
									},
									success: function(response) {
										alerta(response.titulo, response.menssagem, response.status)
										checkProdutos()
									}
								});
							})
						})
					}else{
						$('#produtoTable').hide();
					}
				}
			});
		}

		$(function() {
			$('.btn-grid').click(function(){
				$('.btn-grid').removeClass('active');
				$(this).addClass('active');     
			});
		});

		function mudarGrid(grid){
			if (grid == 'procedimentos') {				
				document.getElementById("box-procedimentos").style.display = 'block';
				document.getElementById("box-produtos").style.display = 'none';
			} else if (grid == 'produtos') {
				document.getElementById("box-produtos").style.display = 'block';
				document.getElementById("box-procedimentos").style.display = 'none';
			}
		}

	</script>

	<style>
		.valorTotalEDesconto {
            font-size: 1.5rem;
            border: 1px solid #ccc;
            float: left;
            min-width: 100px;
        }
	</style>

</head>

<body class="navbar-top sidebar-xs">

	<?php include_once("topo.php"); ?>	

	<!-- Page content -->
	<div class="page-content">
		
		<?php
			include_once("menu-left.php");
			include_once("menuLeftSecundarioVenda.php");
		?>

		<!-- Main content -->
		<div class="content-wrapper">

			<?php include_once("cabecalho.php"); ?>	

			<!-- Content area -->
			<div class="content">

				<!-- Info blocks -->		
				<div class="row">
					
					<div class="col-lg-12">
						<form id='dadosPost'>
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
						</form>
						<!-- Basic responsive configuration -->
						
						<?php
							echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
						?>
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title"><b>TABELA DE GASTOS</b></h3>
							</div>
						</div>

						<div> <?php include ('atendimentoDadosPaciente.php'); ?> </div>

						<div class="card">

							<div class="card-header header-elements-inline">
								<div class="col-lg-11">	
									<button type="button" id="procedimentos-btn" class="btn-grid btn btn-outline-secondary btn-lg active" onclick="mudarGrid('procedimentos')" style="margin-left: -10px; margin-right: 12px;" >Procedimentos</button>
									<button type="button" id="produtos-btn" class="btn-grid btn btn-outline-secondary btn-lg " onclick="mudarGrid('produtos')" >Produtos</button>
								</div>
							</div>

							<div id="box-procedimentos" style="display: block;">

								<div class="card-body">
									<form id="formTabelaGastos" name="formTabelaGastos" method="post" class="form-validate-jquery">
										<div class="col-lg-10 mb-2 row">
											<!-- titulos -->
											<div class="col-lg-3">
												<label>Grupo <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-3">
												<label>Subgrupo <span class="text-danger">*</span></label>
											</div>
											<div class="col-lg-4">
												<label>Procedimentos <span class="text-danger">*</span></label>
											</div>
											
											<!-- campos -->										
											<div class="col-lg-3">
												<select id="grupo" name="grupo" class="select-search" >
													<option value=''>Selecione</option>
												</select>
											</div>
											<div class="col-lg-3">
												<select id="subgrupo" name="subgrupo" class="select-search" >
													<option value=''>Selecione</option>
												</select>											
											</div>
											<div class="col-lg-4">
												<select id="procedimentos" name="procedimentos" class="select-search" >
													<option value=''>Selecione</option>
												</select>
											</div>
											
											<div class="col-lg-1" style="margin-top: -5px;">
												<a id="inserirServico" class="btn btn-lg btn-principal">Incluir</a>
											</div>
										</div>
									</form>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<table class="table" id="tblTabelaGastos">
											<thead>
												<tr class="bg-slate">
													<th class="text-left">Item</th>
													<th class="text-left">Data Registro</th>
													<th class="text-left">Grupo</th>
													<th class="text-left">SubGrupo</th>
													<th class="text-left">Procedimento</th>
													<th class="text-right">Valor</th>
													<th class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody id="dataServico">
											</tbody>
										</table>
										<div class="footerProcedimento" style="margin-left: 25px"></div>
									</div>		
								</div>							
						
							</div>


							<div id="box-produtos" style="display: none;">

								<div class="card-body">
									<form id="formTabelaGastosProduto" name="formTabelaGastosProduto" method="post" class="form-validate-jquery">
										<div class="col-lg-10 mb-2" style="margin-top: -20px">
											<!-- titulos -->
											<div class="row">
												<div class="col-lg-10">
													<label>Produtos em Estoque <span class="text-danger">*</span></label>
												</div>
											</div>
											
											<!-- campos -->										
											<br>
											<div class="row" style="margin-top: -20px">												
												<div class="col-lg-9">
													<select id="produtos" name="produtos" class="select-search" >
														<!--  -->
													</select>
												</div>
												<div class="col-lg-1" style="margin-top: -5px;">
													<a id="inserirProduto" class="btn btn-lg btn-principal">Incluir</a>
												</div>
											</div>
											
										</div>
									</form>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<table class="table" id="tblTabelaGastosProduto">
											<thead>
												<tr class="bg-slate">
													<th class="text-left">Item</th>
													<th class="text-left">Data Registro</th>
													<th class="text-left">Código</th>
													<th class="text-left">Produto</th>
													<th class="text-right">Valor</th>
													<th class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody id="dataProduto">
											</tbody>
										</table>
										<div class="footerProduto" style="margin-left: 25px"></div>
									</div>		
								</div>

							
							</div>


							
						</div>
							<!-- /basic responsive configuration -->

							<!--Modal Desconto Procedimento-->
							<div id="pageModalDescontos" class="custon-modal">
								<div class="custon-modal-container" style="max-width: 500px;">
									<div class="card custon-modal-content">
										<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
											<p id='tituloModal' class="h5">Desconto</p>
											<i id="modal-close-x" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
										</div>
										<div class="px-0">
											<div class="d-flex flex-row">
												<div class="col-lg-12">
													<form id="editaSituacao" name="alterarSituacao" method="POST" class="form-validate-jquery">
														<div class="form-group">
														
															<div class="p-3">
																<div class="d-flex flex-row justify-content-between">
																	<div class="col-lg-12 d-flex justify-content-center" >
																		<div class=" col-lg-8 form-group text-center ">												

																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Desconto:</h2>
																					<div class="text-right pl-10" >
																						<input id="inputDesconto" maxLength="12" onKeyUp="moeda(this)" class="form-control valorTotalEDesconto text-center" style="color: green" type="text" name="inputDesconto">
																					</div>                                                
																				</div>
																			</div>   
																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Valor:</h2>
																					<div class="text-right pl-10">
																						<input id="inputModalValorB" maxLength="12" class="form-control valorTotalEDesconto text-center" type="text" readonly>
																					</div>                                                
																				</div>
																			</div>  
																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Valor Final:</h2>
																					<div class="text-right pl-10">
																						<input id="inputModalValorF" maxLength="12" class="form-control valorTotalEDesconto text-center" type="text" readonly>
																					</div>                                                
																				</div>
																			</div>    

																			<input id="itemId" name="itemId" type="hidden" value=''>
																			<input id="itemModalValue" name="itemId" type="hidden" value=''>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</form>
												</div>
											</div>
											<div class="text-right m-2">
												<button id="setDesconto" class="btn btn-principal" role="button">Confirmar</button>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!--Modal Desconto Produto-->
							<div id="pageModalDescontosProduto" class="custon-modal">
								<div class="custon-modal-container" style="max-width: 500px;">
									<div class="card custon-modal-content">
										<div class="custon-modal-title mb-2" style="background-color: #466d96; color: #ffffff">
											<p id='tituloModalp' class="h5">Desconto</p>
											<i id="modal-close-x-p" class="fab-icon-open icon-cross2 p-3" style="cursor: pointer"></i>
										</div>
										<div class="px-0">
											<div class="d-flex flex-row">
												<div class="col-lg-12">
													<form id="editaSituacao" name="alterarSituacao" method="POST" class="form-validate-jquery">
														<div class="form-group">
																														
															<div class="p-3">
																<div class="row d-flex flex-row justify-content-between">
																	<div class=" col-lg-12 d-flex justify-content-center">
																		<div class=" col-lg-8 form-group text-center ">												

																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Desconto:</h2>
																					<div class="text-right pl-10" >
																						<input id="inputDescontop" maxLength="12" onKeyUp="moeda(this)" class="form-control valorTotalEDesconto text-center" style="color: green" type="text" name="inputDescontoP">
																					</div>                                                
																				</div>
																			</div>   
																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Valor:</h2>
																					<div class="text-right pl-10">
																						<input id="inputModalValorBp" maxLength="12" class="form-control valorTotalEDesconto text-center" type="text" readonly>
																					</div>                                                
																				</div>
																			</div>  
																			<div class="row">
																				<div class="col-12">
																					<h2 class="text-left pr-3">Valor Final:</h2>
																					<div class="text-right pl-10">
																						<input id="inputModalValorFp" maxLength="12" class="form-control valorTotalEDesconto text-center" type="text" readonly>
																					</div>                                                
																				</div>
																			</div>    

																			<input id="itemIdp" name="itemId" type="hidden" value=''>
																			<input id="itemModalValuep" name="itemId" type="hidden" value=''>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</form>
												</div>
											</div>
											<div class="text-right m-2">
												<button id="setDescontoProduto" class="btn btn-principal" role="button">Confirmar</button>
											</div>
										</div>
									</div>
								</div>
							</div>
					</div>
				</div>
				<!-- /info blocks -->
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
