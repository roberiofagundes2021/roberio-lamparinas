<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Nova Movimentação';

include('global_assets/php/conexao.php');

if(isset($_POST['inputData'])){

	try{
		
		if($_POST['cmbMotivo'] != '#'){
			$aMotivo = explode("#",$_POST['cmbMotivo']);
			$iMotivo = $aMotivo[0];
		} else{
			$iMotivo = null;
		}
		
		$sql = "INSERT INTO Movimentacao (MovimTipo, MovimMotivo, MovimData, MovimFinalidade, MovimOrigem, MovimDestinoLocal, MovimDestinoSetor, MovimDestinoManual, 
										  MovimObservacao, MovimFornecedor, MovimOrdemCompra, MovimNotaFiscal, MovimDataEmissao, MovimNumSerie, MovimValorTotal, 
										  MovimChaveAcesso, MovimSituacao, MovimUsuarioAtualizador, MovimEmpresa)
				VALUES (:sTipo, :iMotivo, :dData, :iFinalidade, :iOrigem, :iDestinoLocal, :iDestinoSetor, :sDestinoManual, 
						:sObservacao, :iFornecedor, :iOrdemCompra, :sNotaFiscal, :dDataEmissao, :sNumSerie, :fValorTotal, 
						:sChaveAcesso, :iSituacao, :iUsuarioAtualizador, :iEmpresa)";
		$result = $conn->prepare($sql);	

		/*echo $sql;
		echo "<br>";
		var_dump($_POST['inputTipo'], $_POST['cmbClassificacao'], $iMotivo, gravaData($_POST['inputData']), $_POST['cmbFinalidade'], $_POST['cmbOrigem'], $_POST['cmbDestinoLocal'],
		 $_POST['cmbDestinoSetor'], $_POST['inputDestinoManual'], $_POST['txtareaObservacao'], $_POST['cmbFornecedor'], $_POST['cmbOrdemCompra'], $_POST['inputNotaFiscal'],
		 gravaData($_POST['inputDataEmissao']), $_POST['inputNumSerie'], gravaValor($_POST['inputValorTotal']), $_POST['inputChaveAcesso'],
		 $_POST['cmbSituacao'], $_SESSION['UsuarId'], $_SESSION['EmpreId']);
		die;*/
		$conn->beginTransaction();				
				
		$result->execute(array(
						':sTipo' => $_POST['inputTipo'],
						':iMotivo' => $iMotivo,
						':dData' => gravaData($_POST['inputData']),
						':iFinalidade' => $_POST['cmbFinalidade'] == '#' ? null : $_POST['cmbFinalidade'],
						':iOrigem' => $_POST['cmbEstoqueOrigem'] == '#' ? null : $_POST['cmbEstoqueOrigem'],
						':iDestinoLocal' => $_POST['cmbDestinoLocal'] == '#' ? null : $_POST['cmbDestinoLocal'],
						':iDestinoSetor' => $_POST['cmbDestinoSetor'] == '#' ? null : $_POST['cmbDestinoSetor'],
						':sDestinoManual' => $_POST['inputDestinoManual'] == '' ? null : $_POST['inputDestinoManual'],
						':sObservacao' => $_POST['txtareaObservacao'],
						':iFornecedor' => $_POST['cmbFornecedor'] == '-1' ? null : $_POST['cmbFornecedor'],
						':iOrdemCompra' => $_POST['cmbOrdemCompra'] == '#' ? null : $_POST['cmbOrdemCompra'],
						':sNotaFiscal' => $_POST['inputNotaFiscal'] == '' ? null : $_POST['inputNotaFiscal'],
						':dDataEmissao' => $_POST['inputDataEmissao'] == '' ? null : gravaData($_POST['inputDataEmissao']),
						':sNumSerie' => $_POST['inputNumSerie'] == '' ? null : $_POST['inputNumSerie'],
						':fValorTotal' => $_POST['inputValorTotal'] == '' ? null : gravaValor($_POST['inputValorTotal']),
						':sChaveAcesso' => $_POST['inputChaveAcesso'] == '' ? null : $_POST['inputChaveAcesso'],
						':iSituacao' => $_POST['cmbSituacao'] == '#' ? null : $_POST['cmbSituacao'],
						':iUsuarioAtualizador' => $_SESSION['UsuarId'],
						':iEmpresa' => $_SESSION['EmpreId']
						));
						
		$insertId = $conn->lastInsertId();
					
		try{
			$sql = "INSERT INTO MovimentacaoXProduto
						(MvXPrMovimentacao, MvXPrProduto, MvXPrQuantidade, MvXPrValorUnitario, MvXPrLote, MvXPrValidade, MvXPrClassificacao, MvXPrUsuarioAtualizador, MvXPrEmpresa)
					VALUES 
						(:iMovimentacao, :iProduto, :iQuantidade, :fValorUnitario, :sLote, :dValidade, :iClassificacao, :iUsuarioAtualizador, :iEmpresa)";
			$result = $conn->prepare($sql);
		
			for ($i=1; $i <= $_POST['inputNumItens']; $i++) {
		
				$campo = 'campo'.$i;
				
				//Aqui tenho que fazer esse IF, por causa das exclusões da Grid
				if (isset($_POST[$campo])){
					$registro = explode('#', $_POST[$campo]);	
					
					$result->execute(array(
									':iMovimentacao' => $insertId,
									':iProduto' => $registro[0],
									':iQuantidade' => $registro[1],
									':fValorUnitario' => gravaValor($registro[2]),
									':sLote' => $registro[3],
									':dValidade' => gravaData($registro[4]),
									':iClassificacao' => $registro[5],
									':iUsuarioAtualizador' => $_SESSION['UsuarId'],
									':iEmpresa' => $_SESSION['EmpreId']
									));
				}	
			}
			
		} catch(PDOException $e) {
			$conn->rollback();
			echo 'Error: ' . $e->getMessage();exit;
		}
		
		$conn->commit();		
		
		$_SESSION['msg']['titulo'] = "Sucesso";
		$_SESSION['msg']['mensagem'] = "Movimentação realizada!!!";
		$_SESSION['msg']['tipo'] = "success";
		
	} catch(PDOException $e) {

		$conn->rollback();
		
		$_SESSION['msg']['titulo'] = "Erro";
		$_SESSION['msg']['mensagem'] = "Erro ao realizar movimentação!!!";
		$_SESSION['msg']['tipo'] = "error";	
		
		echo 'Error: ' . $e->getMessage(); exit;
	}
	
	irpara("movimentacao.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Lamparinas | Movimentação</title>

	<?php include_once("head.php"); ?>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.	min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/datatables_sorting.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>

	<script src="global_assets/js/demo_pages/form_layouts.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/lamparinas/jquery.maskMoney.js"></script>  <!-- http://www.fabiobmed.com.br/criando-mascaras-para-moedas-com-jquery/ -->
	<!-- /theme JS files -->
	
	<!-- Adicionando Javascript -->
    <script type="text/javascript" >

        $(document).ready(function() {	
	
			//Ao mudar o fornecedor, filtra a categoria, subcategoria e produto via ajax (retorno via JSON)
			$('#cmbFornecedor').on('change', function(e){
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				var inputFornecedor = $('#inputFornecedor').val();
				var cmbFornecedor = $('#cmbFornecedor').val();
				
				if(inputNumItens > 0){
					alerta('Atenção','O fornecedor não pode ser alterado quando se tem produto(s) na lista! Exclua-o(s) primeiro ou cancele e recomece o cadastro da movimentação.','error');
					$('#cmbFornecedor').val(inputFornecedor);
					return false;
				}
/*				
				var element = document.getElementById("cmbFornecedor");
				element.className = element.classList.remove("form-control-select2");
				
				$("#cmbFornecedor").removeClass("form-control-select2"); */
				
				$('#inputFornecedor').val(cmbFornecedor);
				
				FiltraCategoria();
				Filtrando();

				$.getJSON('filtraCategoria.php?idFornecedor='+cmbFornecedor, function (dados){

					var option = '<option value="#">Selecione a Categoria</option>';

					if (dados.length){
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.CategId+'">'+obj.CategNome+'</option>';
						});						
						
						$('#cmbCategoria').html(option).show();
					} else {
						ResetCategoria();
					}					
				});				
				
				$.getJSON('filtraSubCategoria.php?idFornecedor='+cmbFornecedor, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});
				
				$.getJSON('filtraProduto.php?idFornecedor='+cmbFornecedor, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								option += '<option value="'+obj.ProduId+'#'+obj.ProduValorCusto+'">'+obj.ProduNome+'</option>';
							} else {
								option += '<option value="'+obj.ProduId+'#'+obj.ProduCustoFinal+'">'+obj.ProduNome+'</option>';
							}
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});		


				$.get('filtraOrdemCompra.php?idFornecedor='+cmbFornecedor, function (dados){

					var option = '<option value="#">Selecione</option>';
					console.log(dados)
					if (dados){
						$('#cmbOrdemCompra').html(option).show();
						$('#cmbOrdemCompra').append(dados).show();
					} else {
						$('#cmbOrdemCompra').html(option).show();
					}
				});				
				
			});
		
			//Ao mudar a categoria, filtra a subcategoria e produto via ajax (retorno via JSON)
			$('#cmbCategoria').on('change', function(e){
				
				Filtrando();
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbCategoria = $('#cmbCategoria').val();

				$.getJSON('filtraSubCategoria.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#">Selecione a SubCategoria</option>';
					
					if (dados.length){						
						
						$.each(dados, function(i, obj){
							option += '<option value="'+obj.SbCatId+'">'+obj.SbCatNome+'</option>';
						});						
						
						$('#cmbSubCategoria').html(option).show();
					} else {
						ResetSubCategoria();
					}					
				});
				
				$.getJSON('filtraProduto.php?idCategoria='+cmbCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								option += '<option value="'+obj.ProduId+'#'+obj.ProduValorCusto+'">'+obj.ProduNome+'</option>';
							} else {
								option += '<option value="'+obj.ProduId+'#'+obj.ProduCustoFinal+'">'+obj.ProduNome+'</option>';
							}
						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	
					
			//Ao mudar a SubCategoria, filtra o produto via ajax (retorno via JSON)
			$('#cmbSubCategoria').on('change', function(e){
				
				FiltraProduto();
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbFornecedor = $('#cmbFornecedor').val();				
				var cmbCategoria = $('#cmbCategoria').val();
				var cmbSubCategoria = $('#cmbSubCategoria').val();
				
				$.getJSON('filtraProduto.php?idFornecedor='+cmbFornecedor+'&idCategoria='+cmbCategoria+'&idSubCategoria='+cmbSubCategoria, function (dados){
					
					var option = '<option value="#" "selected">Selecione o Produto</option>';

					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								option += '<option value="'+obj.ProduId+'#'+obj.ProduValorCusto+'">'+obj.ProduNome+'</option>';
							} else {
								option += '<option value="'+obj.ProduId+'#'+obj.ProduCustoFinal+'">'+obj.ProduNome+'</option>';
							}

						});						
						
						$('#cmbProduto').html(option).show();
					} else {
						ResetProduto();
					}					
				});				
				
			});	

			//Ao mudar o Produto, trazer o Valor Unitário do cadastro (retorno via JSON)
			$('#cmbProduto').on('change', function(e){
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var cmbProduto = $('#cmbProduto').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				
				var Produto = cmbProduto.split("#");				
				var valor = Produto[1].replace(".",",");
				
				$('#inputValorUnitario').val(valor);
				$('#inputQuantidade').focus();
			});	
			
			$("input[type=radio][name=inputTipo]").click(function(){
				
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				
				if(inputNumItens > 0){
					alerta('Atenção','O tipo não pode ser alterado quando se tem produto(s) na lista! Exclua-o(s) primeiro ou cancele e recomece o cadastro da movimentação.','error');
					return false;
				}
								
				$('#cmbCategoria').val("#");
				$('#inputQuantidade').val("");
				$('#inputValorUnitario').val("");
				$('#inputLote').val("");
				$('#inputValidade').val("");
				
				//Quando mudar o tipo para Saída ou Transferência a combo Fornecedor precisa voltar a estaca zero, já que para esses tipos não tem que informar Fornecedor
				if (inputTipo != 'E'){
					$('#cmbFornecedor').val(-1); //Selecione
					$("select#cmbFornecedor").trigger("change"); //Simula o change do select
				}				
				
				$.getJSON('movimentacaoSituacao.php', function (dados){
					
					var option = '<option value="#">Selecione</option>';
					
					if (dados.length){
						
						$.each(dados, function(i, obj){
							if (inputTipo == 'E'){
								if (obj.SituaChave == 'BLOQUEADO'){
									option += '<option value="'+obj.SituaId+'" selected>'+obj.SituaNome+'</option>';
								} else {
									option += '<option value="'+obj.SituaId+'">'+obj.SituaNome+'</option>';
								}
							} else {
								if (obj.SituaChave == 'FINALIZADO'){
									option += '<option value="'+obj.SituaId+'" selected>'+obj.SituaNome+'</option>';
								} else {
									option += '<option value="'+obj.SituaId+'">'+obj.SituaNome+'</option>';
								}
							}
						});						
						
						$('#cmbSituacao').html(option).show();
					}					
				});					
			});
			
			$('#btnAdicionar').click(function(){	
			
				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputNumItens = $('#inputNumItens').val();
				var cmbProduto = $('#cmbProduto').val();
				var cmbFornecedor = $('#cmbFornecedor').val();
				
				var Produto = cmbProduto.split("#");
				
				var inputQuantidade = $('#inputQuantidade').val();
				var inputValorUnitario = $('#inputValorUnitario').val();
				var inputTotal = $('#inputTotal').val();
				var inputLote = $('#inputLote').val();
				var inputValidade = $('#inputValidade').val();
				var cmbClassificacao = $('#cmbClassificacao').val();
				var inputIdProdutos = $('#inputIdProdutos').val(); //esse aqui guarda todos os IDs de produtos que estão na grid para serem movimentados

				//remove os espaços desnecessários antes e depois
				inputQuantidade = inputQuantidade.trim();
				
				//Verifica se o campo só possui espaços em branco
				if (inputTipo == 'E' && cmbFornecedor == '-1' && inputNumItens == 0){
					alerta('Atenção','Para entrada de mercadoria deve-se informar o Fornecedor antes de adicionar!','error');
					$('#inputQuantidade').focus();
					return false;
				}					
				
				//Verifica se o campo só possui espaços em branco
				if (inputQuantidade == ''){
					alerta('Atenção','Informe a quantidade antes de adicionar!','error');
					$('#inputQuantidade').focus();
					return false;
				}				
				
				//Verifica se o campo só possui espaços em branco
				if (inputValorUnitario == ''){
					alerta('Atenção','Nenhum produto foi selecionado!','error');
					$('#cmbProduto').focus();
					return false;
				}

				//Verifica se a combo Classificação foi informada
				if (inputTipo == 'S' && cmbClassificacao == '#'){
					alerta('Atenção','Informe a Classificação/Bens!','error');
					$('#cmbClassificacao').focus();
					return false;
				}				
							
				//Verifica se o campo já está no array
				if ( inputIdProdutos.includes(Produto[0]) ){
					alerta('Atenção','Esse produto já foi adicionado!','error');
					$('#cmbProduto').focus();
					return false;
				}				
				
				var resNumItens = parseInt(inputNumItens) + 1;
				var total = parseInt(inputQuantidade) * parseFloat(inputValorUnitario.replace(',', '.'));

				total = total + parseFloat(inputTotal);
				var totalFormatado = "R$ " + float2moeda(total).toString();
				
				//Esse ajax está sendo usado para verificar no banco se o registro já existe
				$.ajax({
					type: "POST",
					url: "movimentacaoAddProduto.php",
					data: {tipo: inputTipo, numItens: resNumItens, idProduto: Produto[0], quantidade: inputQuantidade},
					success: function(resposta){
																	
						//var newRow = $("<tr>");
						
						//newRow.append(resposta);	    
						$("#tabelaProdutos").append(resposta);
												
						//Adiciona mais um item nessa contagem
						$('#inputNumItens').val(resNumItens);
						$('#cmbProduto').val("#").change();						
						$('#inputQuantidade').val('');
						$('#inputValorUnitario').val('');
						$('#inputTotal').val(total);
						$('#total').text(totalFormatado);
						$('#inputLote').val('');
						$('#inputValidade').val('');
						
						$('#inputProdutos').append('<input type="hidden" id="campo'+resNumItens+'" name="campo'+resNumItens+'" value="'+Produto[0]+'#'+inputQuantidade+'#'+inputValorUnitario+'#'+inputLote+'#'+inputValidade+'#'+cmbClassificacao+'">');												
						
						inputIdProdutos = inputIdProdutos + ', ' + parseInt(Produto[0]);

						$('#inputIdProdutos').val(inputIdProdutos);					
						
						$('#cmbFornecedor').prop('disabled', true);
						
						return false;
						
					}
				})	
			}); //click
			
			$(document).on('click', '.btn_remove', function(){
						
				var inputTotal = $('#inputTotal').val();
				var button_id = $(this).attr("id");				
				var Produto = button_id.split("#");
				var inputIdProdutos = $('#inputIdProdutos').val(); //array com o Id dos produtos adicionados
				var inputNumItens = $('#inputNumItens').val();
								
				//alert("Antes: " + inputIdProdutos);
				
				var item = inputIdProdutos.split(",");
				
				var i;
				var arr = [];
				
				for (i = 0; i < item.length; i++) {
					arr.push(item[i]);
				}				
				
				var index = arr.indexOf(Produto[0]);			
				
				arr.splice(index, 1);

				$('#inputIdProdutos').val(arr);
				
				//var teste = $('#inputIdProdutos').val();
				//alert(teste);
				
				$("#row"+Produto[0]+"").remove(); //remove a linha da tabela
				$("#campo"+Produto[0]+"").remove(); //remove o campo hidden
				
				//Agora falta calcular o valor total novamente
				inputTotal = parseFloat(inputTotal) - parseFloat(Produto[1]);
				var totalFormatado = "R$ " + float2moeda(inputTotal).toString();

				$('#inputTotal').val(inputTotal);
				$('#total').text(totalFormatado);
				
				
				var resNumItens = parseInt(inputNumItens) - 1;
				$('#inputNumItens').val(resNumItens);
				
				if (resNumItens == 0){
					$('#cmbFornecedor').prop('disabled', false);
				}
			})

			//Valida Registro Duplicado
			$('#enviar').on('click', function(e){
				
				e.preventDefault();

				var inputTipo = $('input[name="inputTipo"]:checked').val();
				var inputTotal = $('#inputTotal').val();
				var cmbFinalidade = $('#cmbFinalidade').val();
				var cmbMotivo = $('#cmbMotivo').val();
				var cmbEstoqueOrigem = $('#cmbEstoqueOrigem').val();
				var cmbDestinoLocal = $('#cmbDestinoLocal').val();
				var cmbDestinoSetor = $('#cmbDestinoSetor').val();
				var inputDestinoManual = $('#inputDestinoManual').val();
				
				var Motivo = cmbMotivo.split("#");
				var chave = Motivo[1];
				
				//remove os espaços desnecessários antes e depois
				inputDestinoManual = inputDestinoManual.trim();
				
				if (inputTipo == 'E'){
					
					//Verifica se a combo Finalidade foi informada
					if (cmbFinalidade == '#'){
						alerta('Atenção','Informe a Finalidade!','error');
						$('#cmbFinalidade').focus();
						return false;
					}
					
					//Verifica se a combo Estoque de Destino foi informada
					if (cmbDestinoLocal == '#'){
						alerta('Atenção','Informe o Estoque de Destino!','error');
						$('#cmbDestinoLocal').focus();
						return false;
					}
				} else if (inputTipo == 'S'){
					
					//Verifica se a combo Finalidade foi informada
					if (cmbFinalidade == '#'){
						alerta('Atenção','Informe a Finalidade!','error');
						$('#cmbFinalidade').focus();
						return false;
					}

					//Verifica se a combo Estoque de Origem foi informada
					if (cmbEstoqueOrigem == '#'){
						alerta('Atenção','Informe o Estoque de Origem!','error');
						$('#cmbEstoqueOrigem').focus();
						return false;
					}
					
					//Verifica se a combo Estoque de Destino foi informada
					if (cmbDestinoSetor == '#'){
						alerta('Atenção','Informe o Estoque de Destino!','error');
						$('#cmbDestinoSetor').focus();
						return false;
					}
					
				} else if (inputTipo == 'T'){
					
					//Verifica se a combo Motivo foi informada
					if (cmbMotivo == '#'){
						alerta('Atenção','Informe o Motivo!','error');
						$('#cmbMotivo').focus();
						return false;
					}
									
					//Verifica se a combo Finalidade foi informada
					if (cmbFinalidade == '#'){
						alerta('Atenção','Informe a Finalidade!','error');
						$('#cmbFinalidade').focus();
						return false;
					}
					
					//Verifica se a combo Estoque de Origem foi informada
					if (cmbEstoqueOrigem == '#'){
						alerta('Atenção','Informe o Estoque de Origem!','error');
						$('#cmbEstoqueOrigem').focus();
						return false;
					}					
					
					if (chave == 'DOACAO' || chave == 'DESCARTE' || chave == 'DEVOLUCAO' || chave == 'CONSIGNACAO'){
						
						//Verifica se o input Destino foi informado
						if (inputDestinoManual == ''){
							alerta('Atenção','Informe o Destino!','error');
							$('#inputDestinoManual').focus();
							return false;
						}
					} else {

						//Verifica se a combo Estoque de Destino foi informada
						if (cmbDestinoLocal == '#'){
							alerta('Atenção','Informe o Estoque de Destino!','error');
							$('#cmbDestinoLocal').focus();
							return false;
						}						
					}					
				}
				
				//Verifica se tem algum produto na Grid
				if (inputTotal == '' || inputTotal == 0){
					alerta('Atenção','Informe algum produto!','error');
					$('#cmbCategoria').focus();
					return false;
				}				
				
				//desabilita as combos "Fornecedor" e "Situacao" na hora de gravar, senão o POST não o encontra
				$('#cmbFornecedor').prop('disabled', false);
				$('#cmbSituacao').prop('disabled', false);
				
				$("#formMovimentacao").submit();
			});
			
			//Mostra o "Filtrando..." na combo SubCategoria e Produto ao mesmo tempo
			function Filtrando(){
				$('#cmbSubCategoria').empty().append('<option>Filtrando...</option>');
				FiltraProduto();
			}		

			//Mostra o "Filtrando..." na combo Produto
			function FiltraCategoria(){
				$('#cmbCategoria').empty().append('<option>Filtrando...</option>');
			}
			
			//Mostra o "Filtrando..." na combo Produto
			function FiltraProduto(){
				$('#cmbProduto').empty().append('<option>Filtrando...</option>');
			}		
			
			function ResetCategoria(){
				$('#cmbCategoria').empty().append('<option>Sem Categoria</option>');
			}			
			
			function ResetSubCategoria(){
				$('#cmbSubCategoria').empty().append('<option>Sem Subcategoria</option>');
			}
			
			function ResetProduto(){
				$('#cmbProduto').empty().append('<option>Sem produto</option>');
			}				
			
		}); //document.ready	
		
		Array.prototype.remove = function(start, end) {
		  this.splice(start, end);
		  return this;
		}

		Array.prototype.insert = function(pos, item) {
		  this.splice(pos, 0, item);
		  return this;
		}
		
		function selecionaTipo(tipo) {
			
			if (tipo == 'E'){				
				document.getElementById('EstoqueOrigem').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "none";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "block";
			} else if (tipo == 'S') {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
				document.getElementById('DestinoSetor').style.display = "block";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "none";
				document.getElementById('dadosNF').style.display = "none";
			} else {
				document.getElementById('EstoqueOrigem').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoSetor').style.display = "none";
				document.getElementById('classificacao').style.display = "block";
				document.getElementById('motivo').style.display = "block";
				document.getElementById('dadosNF').style.display = "none";
			}
		}	

		function selecionaMotivo(motivo) {
			var Motivo = motivo.split("#");
			var chave = Motivo[1];
			
			if (chave == 'DOACAO' || chave == 'DESCARTE' || chave == 'DEVOLUCAO' || chave == 'CONSIGNACAO'){
				document.getElementById('DestinoManual').style.display = "block";
				document.getElementById('DestinoLocal').style.display = "none";
			} else {
				document.getElementById('DestinoManual').style.display = "none";
				document.getElementById('DestinoLocal').style.display = "block";
				document.getElementById('DestinoManual').value = '';
			}
		}	
		
		function float2moeda(num) {

		   x = 0;

		   if(num<0) {
			  num = Math.abs(num);
			  x = 1;
		   }
		   if(isNaN(num)) num = "0";
			  cents = Math.floor((num*100+0.5)%100);

		   num = Math.floor((num*100+0.5)/100).toString();

		   if(cents < 10) cents = "0" + cents;
			  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
				 num = num.substring(0,num.length-(4*i+3))+'.'
					   +num.substring(num.length-(4*i+3));
		   ret = num + ',' + cents;
		   if (x == 1) ret = ' - ' + ret;
		   
		   return ret;

		}		
								
	</script>
	<!-- Validação -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/localization/messages_pt_BR.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

</head>

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
				
				<!-- Info blocks -->
				<div class="card">
					
					<form name="formMovimentacao" id="formMovimentacao" method="post" class="form-validate-jquery" action="movimentacaoNovo.php">
						<div class="card-header header-elements-inline">
							<h5 class="text-uppercase font-weight-bold">Cadastrar Nova Movimentação</h5>
						</div>
						
						<div class="card-body">								
							<div class="row">
								<div class="col-lg-4">
									<div class="form-group">							
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="E" class="form-input-styled" onclick="selecionaTipo('E')" checked data-fouc>
												Entrada
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="S" class="form-input-styled" onclick="selecionaTipo('S')" data-fouc>
												Saída
											</label>
										</div>
										<div class="form-check form-check-inline">
											<label class="form-check-label">
												<input type="radio" id="inputTipo" name="inputTipo" value="T" class="form-input-styled" onclick="selecionaTipo('T')" data-fouc>
												Transferência
											</label>
										</div>										
									</div>
								</div>
								
								<div class="col-lg-4" id="motivo" style="display:none;">
									<div class="form-group">
										<label for="cmbMotivo">Motivo</label>
										<select id="cmbMotivo" name="cmbMotivo" class="form-control form-control-select2" onChange="selecionaMotivo(this.value)">
											<option value="#">Selecione</option>
											<?php 
												$sql = ("SELECT MotivId, MotivNome, MotivChave
														 FROM Motivo
														 WHERE MotivStatus = 1 and MotivEmpresa = ". $_SESSION['EmpreId'] ."
														 ORDER BY MotivNome ASC");
												$result = $conn->query("$sql");
												$rowMotivo = $result->fetchAll(PDO::FETCH_ASSOC);
												
												foreach ($rowMotivo as $item){
													print('<option value="'.$item['MotivId'].'#'.$item['MotivChave'].'">'.$item['MotivNome'].'</option>');
												}
											
											?>
										</select>
									</div>
								</div>
								
							</div>
								
							<div class="row">				
								<div class="col-lg-12">
									<div class="row">
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputData">Data</label>
												<input type="text" id="inputData" name="inputData" class="form-control" value="<?php echo date('d/m/Y'); ?>" readOnly>
											</div>
										</div>
																				
										<div class="col-lg-2">
											<div class="form-group">
												<label for="cmbFinalidade">Finalidade</label>
												<select id="cmbFinalidade" name="cmbFinalidade" class="form-control select">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT FinalId, FinalNome
																 FROM Finalidade
																 WHERE FinalStatus = 1
															     ORDER BY FinalNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['FinalId'].'">'.$item['FinalNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4" id="EstoqueOrigem" style="display:none;">
											<div class="form-group">
												<label for="cmbEstoqueOrigem">Origem</label>
												<select id="cmbEstoqueOrigem" name="cmbEstoqueOrigem" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT LcEstId, LcEstNome
																 FROM LocalEstoque
																 WHERE LcEstStatus = 1 and LcEstEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY LcEstNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4" id="DestinoLocal">
											<div class="form-group">
												<label for="cmbDestinoLocal">Destino</label>
												<select id="cmbDestinoLocal" name="cmbDestinoLocal" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT LcEstId, LcEstNome
																 FROM LocalEstoque
																 WHERE LcEstStatus = 1 and LcEstEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY LcEstNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['LcEstId'].'">'.$item['LcEstNome'].'</option>');
														}
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4" id="DestinoSetor" style="display:none">
											<div class="form-group">
												<label for="cmbDestinoSetor">Destino</label>
												<select id="cmbDestinoSetor" name="cmbDestinoSetor" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT SetorId, SetorNome
																 FROM Setor
																 WHERE SetorStatus = 1 and SetorEmpresa = ". $_SESSION['EmpreId'] ."
															     ORDER BY SetorNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['SetorId'].'">'.$item['SetorNome'].'</option>');
														}
													?>
												</select>
											</div>
										</div>	
										
										<div class="col-lg-4" id="DestinoManual" style="display:none">
											<div class="form-group">
												<label for="inputDestinoManual">Destino</label>
												<input type="text" id="inputDestinoManual" name="inputDestinoManual" class="form-control">
											</div>											
										</div>
									</div>
								</div>
							</div>
								
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="txtareaObservacao">Observação</label>
										<textarea rows="5" cols="5" class="form-control" id="txtareaObservacao" name="txtareaObservacao" placeholder="Observação"></textarea>
									</div>
								</div>
							</div>
							<br>
							
							<div class="row" id="dadosNF">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados da Nota Fiscal</h5>
									<br>
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<label for="cmbFornecedor">Fornecedor</label>
												<select id="cmbFornecedor" name="cmbFornecedor" class="form-control form-control-select2">
													<option value="-1">Selecione</option>
													<?php 
														$sql = ("SELECT ForneId, ForneNome
																 FROM Fornecedor														     
																 WHERE ForneEmpresa = ". $_SESSION['EmpreId'] ." and ForneStatus = 1
															     ORDER BY ForneNome ASC");
														$result = $conn->query("$sql");
														$rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowFornecedor as $item){															
															print('<option value="'.$item['ForneId'].'">'.$item['ForneNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<input type="hidden" id="inputFornecedor" name="inputFornecedor" value="#">
										
										<div class="col-lg-3">											
											<div class="form-group">
												<label for="cmbOrdemCompra">*Nº Ordem Compra / Carta Contrato</label>
												<select id="cmbOrdemCompra" name="cmbOrdemCompra" class="form-control form-control-select2" required>
													<option value="#">Selecione</option>
												</select>
											</div>											
										</div>	
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNotaFiscal">Nº Nota Fiscal</label>
												<input type="text" id="inputNotaFiscal" name="inputNotaFiscal" class="form-control">
											</div>
										</div>										
									</div> <!-- row -->
									
									<div class="row">

										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputDataEmissao">Data Emissão</label>
												<input type="text" id="inputDataEmissao" name="inputDataEmissao" class="form-control">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputNumSerie">Nº Série</label>
												<input type="text" id="inputNumSerie" name="inputNumSerie" class="form-control" maxLength="30">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputValorTotal">Valor Total</label>
												<input type="text" id="inputValorTotal" name="inputValorTotal" class="form-control" onKeyUp="moeda(this)" maxLength="11">
											</div>
										</div>
										
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputChaveAcesso">Chave de Acesso</label>
												<input type="text" id="inputChaveAcesso" name="inputChaveAcesso" class="form-control">
											</div>
										</div>
										
									</div> <!-- row -->
								</div> <!-- col-lg-12 -->
							</div> <!-- row -->
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<h5 class="mb-0 font-weight-semibold">Dados dos Produtos</h5>
									<br>
									
									<div class="row">
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbCategoria">Categoria</label>
												<select id="cmbCategoria" name="cmbCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT CategId, CategNome
																 FROM Categoria															     
																 WHERE CategStatus = 1 and CategEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY CategNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){															
															print('<option value="'.$item['CategId'].'">'.$item['CategNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbSubCategoria">SubCategoria</label>
												<select id="cmbSubCategoria" name="cmbSubCategoria" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														/*$sql = ("SELECT SbCatId, SbCatNome
																 FROM SubCategoria															     
																 WHERE SbCatStatus = 1 and SbCatEmpresa = ". $_SESSION['EmpreId'] ."
																 ORDER BY SbCatNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															print('<option value="'.$item['SbCatId'].'">'.$item['SbCatNome'].'</option>');
														}
													  */
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-4">
											<div class="form-group">
												<label for="cmbProduto">Produto</label>
												<select id="cmbProduto" name="cmbProduto" class="form-control form-control-select2">
													<option value="#">Selecione</option>
												</select>
											</div>
										</div>										
									</div>							
													
									<div class="row">
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputQuantidade">Quantidade</label>
												<input type="text" id="inputQuantidade" name="inputQuantidade" class="form-control">												
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValorUnitario">Valor Unitário</label>
												<input type="text" id="inputValorUnitario" name="inputValorUnitario" class="form-control" readOnly>
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputLote">Lote</label>
												<input type="text" id="inputLote" name="inputLote" class="form-control">
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">
												<label for="inputValidade">Validade</label>
												<input type="text" id="inputValidade" name="inputValidade" class="form-control">
											</div>
										</div>

										<div class="col-lg-2" id="classificacao" style="display:none;">
											<div class="form-group">
												<label for="cmbClassificacao">Classificação/Bens</label>
												<select id="cmbClassificacao" name="cmbClassificacao" class="form-control form-control-select2">
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT ClassId, ClassNome
																 FROM Classificacao
																 WHERE ClassStatus = 1
																 ORDER BY ClassNome ASC");
														$result = $conn->query("$sql");
														$rowClassificacao = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($rowClassificacao as $item){
															print('<option value="'.$item['ClassId'].'">'.$item['ClassNome'].'</option>');
														}
													
													?>
												</select>
											</div>
										</div>
										
										<div class="col-lg-2">
											<div class="form-group">												
												<button type="button" id="btnAdicionar" class="btn btn-lg btn-success" style="margin-top:20px;">Adicionar</button>
												<!--<button id="adicionar" type="button">Teste</button>-->
											</div>
										</div>										
									</div>
								</div>
							</div>						
							
							<div id="inputProdutos">
								<input type="hidden" id="inputNumItens" name="inputNumItens" value="0">
								<input type="hidden" id="inputIdProdutos" name="inputIdProdutos" value="0">
								<input type="hidden" id="inputProdutosRemovidos" name="inputProdutosRemovidos" value="0">
								<input type="hidden" id="inputTotal" name="inputTotal" value="0">
							</div>
							
							<div class="row">
								<div class="col-lg-12">	
										<table class="table" id="tabelaProdutos">
											<thead>
												<tr class="bg-slate">
													<th width="5%">Item</th>
													<th width="40%">Produto</th>
													<th width="14%">Unidade Medida</th>
													<th width="8%">Quantidade</th>
													<th width="14%">Valor Unitário</th>
													<th width="14%">Valor Total</th>
													<th width="5%" class="text-center">Ações</th>
												</tr>
											</thead>
											<tbody>
												<tr style="display:none;">
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												</tr>
											</tbody>
									        <tfoot>
												<tr>
													<th colspan="5" style="text-align:right; font-size: 16px; font-weight:bold;">Total:</th>
													<th colspan="2"><div id="total" style="text-align:left; font-size: 15px; font-weight:bold;">R$ 0,00</div></th>
												</tr>
											</tfoot>
										</table>
								</div>
							</div>							
							<br>
							<br>
							
							<div class="row">
								<div class="col-lg-12">									
									<div class="row">
										<div class="col-lg-3">
											<div class="form-group">
												<label for="inputSituacao">Situação</label>
												<select id="cmbSituacao" name="cmbSituacao" class="form-control form-control-select2" disabled>
													<option value="#">Selecione</option>
													<?php 
														$sql = ("SELECT SituaId, SituaNome, SituaChave
																 FROM Situacao
																 WHERE SituaStatus = 1
															     ORDER BY SituaNome ASC");
														$result = $conn->query("$sql");
														$row = $result->fetchAll(PDO::FETCH_ASSOC);
														
														foreach ($row as $item){
															if($item['SituaChave'] == 'BLOQUEADO'){
																print('<option value="'.$item['SituaId'].'" selected>'.$item['SituaNome'].'</option>');
															} else {
																print('<option value="'.$item['SituaId'].'">'.$item['SituaNome'].'</option>');
															}
														}													
													?>
												</select>
												
											</div>
										</div>
									</div>
								</div>
							</div>							

							<div class="row" style="margin-top: 10px;">
								<div class="col-lg-12">								
									<div class="form-group">
										<button class="btn btn-lg btn-success" id="enviar">Incluir</button>
										<a href="movimentacao.php" class="btn btn-basic" role="button">Cancelar</a>
									</div>
								</div>
							</div>
						</div>
						<!-- /card-body -->
					</form>
					
				</div>
				<!-- /info blocks -->

			</div>
			<!-- /content area -->			
			
			<?php include_once("footer.php"); ?>

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
