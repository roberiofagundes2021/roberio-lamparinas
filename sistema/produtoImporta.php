<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Importa Produto';
$_SESSION['RelImportacao'] = '';
$_SESSION['Importacao'] = '';

include('global_assets/php/conexao.php');

// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = 'global_assets/importacao/';

// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 10; // 10MB

// Array com as extensões permitidas
$_UP['extensoes'] = array('csv');

// Renomeia o arquivo? (Se true, o arquivo será salvo como .doc e um nome único)
$_UP['renomeia'] = false;

// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';

$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';

$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';

$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';

$_UP['erros'][4] = 'Não foi feito o upload do arquivo'; 


// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
if ($_FILES['arquivo']['error'] != 0) {

	alerta($_UP['erros'][$_FILES['arquivo']['error']]);
	irpara("produto.php");
}

// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
// Faz a verificação da extensão do arquivo
/*
$extensao = strtolower(end(explode(".", $_FILES['arquivo']['name'])));

if ($extensao != 'csv') {

	alerta("Por favor, envie arquivos com a seguinte extensão: CSV!");
	irpara("produto.php");

} */

// Faz a verificação do tamanho do arquivo
else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {

	echo "O arquivo enviado é muito grande, envie arquivos de até 10MB.";
}

// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
else {

	// Primeiro verifica se deve trocar o nome do arquivo
	if ($_UP['renomeia'] == true) {
	
		// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .csv
		$nome_final = time().".".$extensao;
	
	} else {
	
		// Mantém o nome original do arquivo
		$nome_final = $_FILES['arquivo']['name'];
	}

	// Depois verifica se é possível mover o arquivo para a pasta escolhida
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
	
		$nome_arquivo   =  "./".$_UP['pasta'].$nome_final;
		$arquivo        = fopen($nome_arquivo, "r");
		$qtd            = 0;
		$importados     = 0;
		
		$produtosimportados = "";
		$erroFormato = "";
		$erro = "";
		$cont = 0;
	
		while ($linha = fgetcsv($arquivo, 1000, ",")) {
				
			$Codigo = $linha[0];	
			$Nome = $linha[1];
			$Detalhamento = $linha[2];
				
			if ($cont == 0) {  //só ler isso uma única vez (no cabeçalho apenas)

				if (strtoupper(substr($codigo, 0, 3)) == 'COD') {
				
				   $cont++;
				   
				} else { 
				
				   $erroFormato = "O formato do arquivo de importação provavelmente não está correto. Verifique Modelo: <a href='global_assets/importacao/modelo.csv'>Modelo de Importação de Produtos</a>";
				   break; //sai do while	
				}
							   
			} else {
						
				if (trim($codigoBarras) <> ''){
					
					$qtd++;
					
					$sql = "SELECT ProduId
							FROM Produto
							WHERE ProduEmpresa = ". $_SESSION['EmpreId'] ." and ProduCodigoBarras = '".$codigoBarras."'";
					$result = $conn->query($sql);
					$row = $result->fetch(PDO::FETCH_ASSOC);
					//$count = count($row);
					
					if ($row){
		
						$sql = "UPDATE Produto SET ProduNome = :sNome, ProduDetalhamento = :sDetalhamento, ProduUsuarioAtualizador = :iUsuarioAtualizador
								WHERE ProduCodigoBarras = :sCodigoBarras and ProduEmpresa = :iEmpresa";
						$result = $conn->prepare($sql);
								
						$result->execute(array(
										':sNome' => $nomeProduto,
										':sDetalhamento' => $detalhamentoProduto,
										':iUsuarioAtualizador' => $_SESSION['UsuarId'],
										':sCodigoBarras' => $codigoBarras,
										':iEmpresa' => $_SESSION['EmpreId']
										));
						 
						$registrou_arquivo = TRUE;  			    
						$produtosimportados.= $nomeProduto.', ';
						$importados++;
					}
					else {
						
						$sql = "SELECT COUNT(isnull(ProduCodigo,0)) as Codigo
										FROM Produto
										Where ProduEmpresa = ".$_SESSION['EmpreId']."";
						//echo $sql;die;
						$result = $conn->query("$sql");
						$rowCodigo = $result->fetch(PDO::FETCH_ASSOC);	
						
						$sCodigo = (int)$rowCodigo['Codigo'] + 1;
						$sCodigo = str_pad($sCodigo,6,"0",STR_PAD_LEFT);						
						
						$sql = "INSERT INTO Produto (ProduCodigo, ProduCodigoBarras, ProduNome, ProduDetalhamento, ProduFoto, ProduCategoria, ProduSubCategoria, ProduValorCusto, 
									 ProduOutrasDespesas, ProduCustoFinal, ProduMargemLucro, ProduValorVenda, 
									 ProduEstoqueMinimo, ProduMarca, ProduModelo, ProduNumSerie, ProduFabricante, ProduUnidadeMedida, 
									 ProduTipoFiscal, ProduNcmFiscal, ProduOrigemFiscal, ProduCest, ProduStatus, 
									 ProduUsuarioAtualizador, ProduEmpresa) 
								VALUES (:sCodigo, :sCodigoBarras, :sNome, :sDetalhamento, :sFoto, :iCategoria, :iSubCategoria, :fValorCusto, 
										:fOutrasDespesas, :fCustoFinal, :fMargemLucro, :fValorVenda, :iEstoqueMinimo, :iMarca, :iModelo, :sNumSerie, 
										:iFabricante, :iUnidadeMedida, :iTipoFiscal, :iNcmFiscal, :iOrigemFiscal, :iCest, :bStatus, 
										:iUsuarioAtualizador, :iEmpresa);";
						$result = $conn->prepare($sql);
								
						$result->execute(array(
										':sCodigo' => $sCodigo,
										':sCodigoBarras' => $codigoBarras,
										':sNome' => $nomeProduto,
										':sDetalhamento' => $detalhamentoProduto,
										':sFoto' => null,
										':iCategoria' => null,
										':iSubCategoria' => null,
										':fValorCusto' => null,						
										':fOutrasDespesas' => null,
										':fCustoFinal' => null,
										':fMargemLucro' => null,
										':fValorVenda' => null,
										':iEstoqueMinimo' => null,
										':iMarca' => null,
										':iModelo' => null,
										':sNumSerie' => null,
										':iFabricante' => null,
										':iUnidadeMedida' => null,
										':iTipoFiscal' => null,
										':iNcmFiscal' => null,
										':iOrigemFiscal' => null,
										':iCest' => null,
										':bStatus' => 1,
										':iUsuarioAtualizador' => $_SESSION['UsuarId'],
										':iEmpresa' => $_SESSION['EmpreId']
										));
						 
						$registrou_arquivo = TRUE;  			    
						$produtosimportados.= $nomeProduto.', ';
						$importados++;
					}
						
				} else {
				   
				   $qtd++;
				   $linha = $qtd + 1;
				   $erro.= " (1 registro em branco na linha: ".$linha."), ";					
				}
				
			} // fim do else	*/		
							
		} //fim do while
							
	
		fclose($arquivo);
		die;
		/*
		$relatorio = "<b>Relatório de Importação</b><br><br>";
		
		if ($erro != "" or $erroFormato != "") {
		   
		   if ($erroFormato != "") $erro .= $erroFormato;
		   
		   $relatorio .= "<span style='color:#FF0000;'>Erro na importação</span><br><br>";
		   $relatorio .= $erro; //substr($erro, 0, -1);
		   $relatorio .= "<br><br>";
		   
		   //usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
		   $size = strlen($produtosimportados);
		   $produtosimportados = substr($produtosimportados,0, $size-2);
		   
		   if ($erroFormato == ""){
			   $relatorio .= "Total de registros no arquivo: ".$qtd."<br>";
			   $relatorio .= "Total de registros importados: ".$importados."<br><br>";

			   $relatorio .= "<div style=\"width:600px\"><b>Produtos Importados:</b> ".$produtosimportados."</div><br>";
			   $relatorio .= "<br>";
		   }
			
		   $_SESSION['RelImportacao'] = $relatorio;
		   $_SESSION['Importacao'] = 'Erro';
			
		} else {
		   
		   $relatorio .= "<span style='color:#0080FF;'>Importação realizada com sucesso!</span><br><br>";	
			
			//usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
		   $size = strlen($produtosimportados);
		   $produtosimportados = substr($produtosimportados,0, $size-2);
	
		   $relatorio .= "Total de registros no arquivo: ".$qtd."<br>";
		   $relatorio .= "Total de registros importados: ".$importados."<br><br>";

		   $relatorio .= "<div style=\"width:600px\"><b>Produtos Importados:</b> ".$produtosimportados."</div>";
		   
		   $_SESSION['RelImportacao'] = $relatorio; 
		   $_SESSION['Importacao'] = 'Sucesso';	
		}
	
		*/
	} else {

		// Não foi possível fazer o upload, provavelmente a pasta está incorreta
		$_SESSION['RelImportacao'] = "Não foi possível enviar o arquivo, tente novamente";
		$_SESSION['Importacao'] = 'Erro';
	}
	
	irpara("produto.php");
}



?>

