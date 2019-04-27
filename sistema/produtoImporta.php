<?php 

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Importa Produto';

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

$extensao = strtolower(end(explode(".", $_FILES['arquivo']['name'])));

if ($extensao != 'csv') {

	alerta("Por favor, envie arquivos com a seguinte extensão: CSV!");
	irpara("produto.php");

}

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
		$identificador = date('now').time('now');
	
		$erro  = "";
		$cont = 0;
	
		while ($linha_arquivo = fgets($arquivo)) {
	
			$coluna  = explode(",",$linha_arquivo);
			$codigoBarras = $coluna[0];
			$nomeProduto = $coluna[1];
			$detalhamentoProduto = $coluna[2];
			
			if ($cont == 0) {  //só ler isso uma única vez (no cabeçalho apenas)

				if ($codigoBarras == 'CodigoBarras') {
				
				   $cont++;
				   
				} else { 
				
				   $erro = "O formato do arquivo de importação provavelmente não está correto. Verifique Modelo: <a href=\"images/modelo_importacao_emp.jpg\">Modelo de Importação de Produtos</a>";
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
					$count = count($row);
					
					if ($count){
		
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
					else 
						$erro.= $nomeProduto.", ";
						
				} else {
				   
				   $qtd++;
				   $linha = $qtd + 1;
				   $erro.= " (1 registro em branco na linha: ".$linha."), ";					
				}
				
			} // fim do else			
							
		} //fim do while
							
	
		fclose($arquivo);
		
		echo "<b>Relatório de Importação</b><br><br>";
		
		if ($erro != "") {
		   
		   echo "<b>Erro na importação</b> - Produtos que não foram importados:<br><br>";
		   echo substr($erro, 0, -2);
		   echo "<br><br>";
		   
		   //usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
		   $size = strlen($produtosimportados);
		   $produtosimportados = substr($produtosimportados,0, $size-2);
	
		   echo "Total de registros no arquivo: ".$qtd."<br>";
		   echo "Total de registros importados: ".$importados."<br><br>";

		   echo "<div style=\"width:600px\"><b>Produtos Importados:</b> ".$produtosimportados."</div><br>";
		   echo "<br>";
		   //echo "<a href=\"importacaogerar.php?identificador=$identificador\">Gerar empenhos>></a>";
			  
		   alerta("Upload efetuado com algumas ressalvas!");
			
		} else {
			//usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
		   $size = strlen($produtosimportados);
		   $produtosimportados = substr($produtosimportados,0, $size-2);
	
		   echo "Total de registros no arquivo: ".$qtd."<br>";
		   echo "Total de registros importados: ".$importados."<br><br>";

		   echo "<div style=\"width:600px\"><b>Produtos Importados:</b> ".$produtosimportados."</div><br>";
		   echo "<br>";
		   //echo "<a href=\"importacaogerar.php?identificador=$identificador\">Gerar empenhos>></a>";
			  
		   alerta("Upload efetuado com sucesso!");		
		}
	
	} else {

		// Não foi possível fazer o upload, provavelmente a pasta está incorreta
		alerta("Não foi possível enviar o arquivo, tente novamente");
		irpara("produto.php");
	}
}



?>

