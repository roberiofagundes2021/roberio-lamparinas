<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Documentos';

include('global_assets/php/conexao.php'); 

$iAtendimentoId = isset($_POST['iAtendimentoId'])?$_POST['iAtendimentoId']:null;

if(!$iAtendimentoId){
	irpara("atendimento.php");
}

// essas variáveis são utilizadas para colocar o nome da classificação do atendimento no menu secundario

$ClaChave = isset($_POST['ClaChave'])?$_POST['ClaChave']:'';
$ClaNome = isset($_POST['ClaNome'])?$_POST['ClaNome']:'';

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
$sql = "SELECT AtendId, AtendCliente, AtendNumRegistro, AtModNome, ClienId, ClienCodigo, ClienNome, ClienSexo, ClienDtNascimento,
               ClienNomeMae, ClienCartaoSus, ClienCelular, ClienStatus, ClienUsuarioAtualizador, ClienUnidade, ClResNome
		FROM Atendimento
		JOIN Cliente ON ClienId = AtendCliente
		LEFT JOIN ClienteResponsavel on ClResCliente = AtendCliente
		LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
		JOIN Situacao ON SituaId = AtendSituacao
	    WHERE  AtendId = $iAtendimentoId 
		ORDER BY AtendNumRegistro ASC";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

$iAtendimentoCliente = $row['AtendCliente'] ;
$iAtendimentoId = $row['AtendId'];


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
	<title>Lamparinas | Documentos</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<!-- Não permite que o usuário retorne para o EDITAR -->
	<script src="global_assets/js/lamparinas/stop-back.js"></script>

	
	<script type="text/javascript">

		$(document).ready(function() {

			$('#summernote').summernote();
			
			getCmbs()
			checkDocumentos()

            /* Início: Tabela Personalizada */
			$('#documentoTable').DataTable( {
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
					orderable: true,   //tipo de documento
					width: "25%", //15
					targets: [2]
				},				
				{ 
					orderable: true,   //profissional
					width: "20%", //15
					targets: [3]
				},
                { 
					orderable: true,   //cbo
					width: "5%", //15
					targets: [4]
				},
                { 
					orderable: true,   //acoes
					width: "10%", //15
					targets: [5]
				},
                ],
				dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer">',
				language: {
					search: '<span>Filtro:</span> _INPUT_',
					searchPlaceholder: 'filtra qualquer coluna...',
					lengthMenu: '<span>Mostrar:</span> _MENU_',
					paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
				}
                
			});

			$('#enviar').on('click', function(e){
                
				let menssagem = ''

				switch(menssagem){			
					case $('#modelo').val(): menssagem='Selecione o tipo de Documento!';$('#modelo').focus();break;
					case $('#summernote').val(): menssagem='Infomrme a descrição do Documento!';$('#summernote').focus();break;
					default: menssagem='';break;
				}
				if(menssagem){
					alerta('Campo obrigatório!!', menssagem, 'error')
					return
				}
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'SALVARDOCUMENTO',
						'idAtendimento': '<?php echo $iAtendimentoId ?>',
						'profissional': '<?php echo $userId ?>',
						'modelo': $('#modelo').val(),
						'descricao': $('#summernote').val(),
					},
					success: function(response) {
                        if (response.status == 'success') {
                            getCmbs()
                            checkDocumentos()
                            alerta(response.titulo, response.menssagem, response.status);
                        }else{
                            alerta(response.titulo, response.menssagem, response.status);
                        }
					}
				});
			})

			$('#modelo').on('change', function(){

				// vai preencher MODELOS
				$.ajax({
					type: 'POST',
					url: 'filtraAtendimento.php',
					dataType: 'json',
					data:{
						'tipoRequest': 'MODELOCONTEUDO',
						'id': $(this).val()
					},
					success: function(response) {
						$('#summernote').val('')
						$('#summernote').summernote('code', response.conteudo)
                        contarCaracteres($('#summernote').get(0));
					}
				})
			})

			$('#summernote').on('summernote.change', function() {
				contarCaracteres($('#summernote').get(0));
			});

			$(".caracteressummernote").text((4000 - $("#summernote").val().length) + ' restantes');

		})//document.ready

		function getCmbs(){
			// limpa o campo text
			$('#summernote').val('')

			// vai preencher MODELOS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'ATENDMODELOS'
				},
				success: function(response) {
					$('#modelo').empty();
					$('#modelo').append(`<option value=''>Selecione</option>`)
					response.forEach(item => {
						let opt = `<option value="${item.id}">${item.nome}</option>`
						$('#modelo').append(opt)
					})
				}
			});
			
		}
		function checkDocumentos(){
			// vai preencher MEDICOS
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'DOCUMENTOS',
					'id': <?php echo $iAtendimentoId?>
				},
				success: function(response) {
					if(response.length){
						$('#documentoTable').show()
						$('#dataDocumento').html('')
						let HTML = ''

						response.forEach((item,index) => {  

							let exc = `<a style='color: black; cursor:pointer' onclick='excluiDocumento(\"${item.id}\")' class='list-icons-item'><i class='icon-bin' title='Excluir Encaminhamento'></i></a>`;
							let print = `<a style='color: black; cursor:pointer' onclick='imprimirDocumento(\"${item.id}\")' class='list-icons-item'><i class='icon-printer2' title='Imprimir Encaminhamento'></i></a>`;
							let acoes = `<div class='list-icons'>
                                        ${print}
										${exc}
									</div>`;
							HTML += `
							<tr class='servicoItem'>
								<td class="text-left">${index+1}</td>
								<td class="text-left">${item.dataHora}</td>
								<td class="text-left">${item.tipoDocumento}</td>
								<td class="text-left">${item.profissional}</td>
								<td class="text-left">${item.cbo}</td>
								<td class="text-center">${acoes}</td>
							</tr>`
						})
						$('#dataDocumento').html(HTML)
					}else{
                        $('#dataDocumento').html('')
                    }
				}
			});
		}

		function excluiDocumento(id){
			$.ajax({
				type: 'POST',
				url: 'filtraAtendimento.php',
				dataType: 'json',
				data:{
					'tipoRequest': 'EXCLUIRDOCUMENTO',
					'id': id
				},
				success: function(response) {
					alerta(response.titulo, response.menssagem, response.status)
					getCmbs()
					checkDocumentos()
				}
			});
		}

		function imprimirDocumento(id){
			console.log(id)
		}

        function contarCaracteres(params) {

            var limite = params.maxLength;
            var informativo = " restantes.";
            var caracteresDigitados = params.value.length;
            var caracteresRestantes = limite - caracteresDigitados;

            if (caracteresRestantes <= 0) {
                var texto = $(`textarea[name=${params.id}]`).val();
                $(`textarea[name=${params.id}]`).val(texto.substr(0, limite));
                $(".caracteres" + params.id).text("0 " + informativo);
            } else {
                $(".caracteres" + params.id).text(caracteresRestantes + " " + informativo);
            }
        }
		
	</script>
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

						<form name="formAtendimentoDocumento" id="formAtendimentoDocumento" method="post" class="form-validate-jquery">
							<?php
								echo "<input type='hidden' id='iAtendimentoId' name='iAtendimentoId' value='$iAtendimentoId' />";
							?>
							<div class="card">
								<div class="card-header header-elements-inline">
									<h3 class="card-title"><b>DOCUMENTOS</b></h3>
								</div>
							</div>

							<div> <?php include ('atendimentoDadosPaciente.php'); ?> </div>

							<div class="card">

								<div class="card-body">

									<div class="col-lg-6 row">
										<div class="col-lg-12">
											<label>Tipo de documento <span class="text-danger">*</span></label>
										</div>

										<div class="col-lg-12 input-group">
											<select id="modelo" name="modelo" class="form-control select-search">
												<option value="">Selecione</option>
											</select>
										</div>										
									</div>

									<br/>

									<div class="col-lg-12">
										<div class="form-group">
											<textarea rows="5" cols="5" maxLength="4000" id="summernote" name="txtareaConteudo" class="form-control"></textarea>
												<small class="text-muted form-text">Max. 4000 caracteres - <span class="caracteressummernote"></span></small>
										</div>
									</div>


                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group" style="padding-top:15px; margin-left: 10px;">
												<button type="button" class="btn btn-lg btn-principal" id="enviar">Incluir</button>
                                            </div>
                                        </div>
                                    </div>


									<div class="col-lg-12 mt-2">
										<div class="col-lg-12 card-header p-0 mb-2">
											<h5 class="card-title"><b>Histórico de Documentos do Paciente</b></h5>
										</div>
										<table class="table" id="documentoTable">
											<thead>
												<tr class="bg-slate">
													<th>Item</th>
													<th>Data/Hora</th>
													<th>Tipo de Documento</th>
													<th>Profissional</th>
													<th>CBO</th>
													<th class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody id="dataDocumento">
												
											</tbody>
										</table>
									</div>

									<div class="row">
										<div class="col-lg-12">
											<div class="form-group" style="padding-top:25px;">												
												<?php 
													if (isset($ClaChave) && $ClaChave == "ELETIVO") {
													echo "<a href='atendimentoEletivoListagem.php' class='btn btn-basic' role='button'>Cancelar</a>";
													} elseif (isset($ClaChave) && $ClaChave == "AMBULATORIAL") {
													echo "<a href='atendimentoAmbulatorialListagem.php' class='btn btn-basic' role='button'>Cancelar</a>";
													} elseif (isset($ClaChave) && $ClaChave == "ELETINTERNACAOIVO") {
													echo "<a href='atendimentoHospitalarListagem.php' class='btn btn-basic' role='button'>Cancelar</a>";
													}					
												?>
											</div>
										</div>
									</div>    
									
								</div>
							</div>
						</form>	

							<!-- /basic responsive configuration -->
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
