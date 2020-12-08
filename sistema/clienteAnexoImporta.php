<?php 

// Referência: https://www.youtube.com/watch?v=7-5yUgiOAo8

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Importa Anexo';
$_SESSION['RelImportacao'] = '';
$_SESSION['Importacao'] = '';

include('global_assets/php/conexao.php');

// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = 'global_assets/importacao/';

// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 10; // 10MB

// Array com as extensões permitidas
$_UP['extensoes'] = array('pdf');

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
	irpara("clienteAnexo.php");
}

// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
// Faz a verificação da extensão do arquivo
/*
$extensao = strtolower(end(explode(".", $_FILES['arquivo']['name'])));

if ($extensao != 'csv') {

	alerta("Por favor, envie arquivos com a seguinte extensão: CSV!");
	irpara("clienteAnexo.php");

} */

// Faz a verificação do tamanho do arquivo
else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
	
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	$_SESSION['RelImportacao'] = "Não foi possível enviar o arquivo. O arquivo enviado é muito grande, envie arquivos de até 10MB.";
	$_SESSION['Importacao'] = 'Erro';	
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
	//var_dump(($_FILES['arquivo']));die;
	$arquivo = new DomDocument();
	$arquivo->load($_FILES['arquivo']['tmp_name']);
	//var_dump($arquivo);die;	

	$linhas = $arquivo->getElementsByTagName("Row");
	//var_dump($linhas);
	
	$primeiraLinha = true;
	$qtd           = 0;
	$importados    = 0;
	$anexoimportados = "";
	$erro = "";
	
	foreach ($linhas as $linha){
		
		if($primeiraLinha){

			$codigo = $linha->getElementsByTagName("Data")->item(0)->nodeValue;
			
			if (is_numeric($codigo)) {
			   $erro = "O formato do arquivo de importação provavelmente não está correto. Verifique Modelo: <a href='global_assets/importacao/modelo.xml'>Modelo de Importação de Anexo</a>";
			   break; //sai do while	
			}
			
			$primeiraLinha = false;
		} else {
			$codigo = $linha->getElementsByTagName("Data")->item(0)->nodeValue;
			$nome = $linha->getElementsByTagName("Data")->item(1)->nodeValue;
			$detalhamento = $linha->getElementsByTagName("Data")->item(2)->nodeValue;
			
			
			$qtd++;
			
			$sql = "SELECT ClAneId
					FROM ClienteAnexo
					WHERE ClAneUnidade = ". $_SESSION['UnidadeId'] ." and Anexo = '".$codigo."'";
			$result = $conn->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			//$count = count($row);
			
			if ($row){

				$sql = "UPDATE ClienteAnexo SET ClAneData = :iData, ClAneNome = :sNome, ClAneArquivo = :iArquivo, ClAneUsuarioAtualizador = :iUsuarioAtualizador
						WHERE ClAneArquivo = :sAneArquivo and ClAneUnidade = :iUnidade";
				$result = $conn->prepare($sql);
						
				$result->execute(array(
								':iData' => $_POST['inputData'],
								':sNome' => $_POST['inputNome'],
					     		':iCliente' => $_SESSION['idCliente'],
								':iUsuarioAtualizador' => $_SESSION['UsuarId'],
								':iArquivo' => $codigo,
								':iUnidade' => $_SESSION['UnidadeId']
								));

				$anexoimportados .= $nome.', ';
				$importados++;
			}

	$relatorio = "<b>Relatório de Importação</b><br><br>";
		
	if ($erro != "") {
		      
	   $relatorio .= "<span style='color:#FF0000;'>Erro na importação</span><br><br>";
	   $relatorio .= $erro; //substr($erro, 0, -1);
	   $relatorio .= "<br><br>";
	   
	   //usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
	   $size = strlen($anexoimportados);
	   $anexoimportados = substr($anexoimportados,0, $size-2);
	   
	   if ($erro == ""){
		   $relatorio .= "Total de registros no arquivo: ".$qtd."<br>";
		   $relatorio .= "Total de registros importados: ".$importados."<br><br>";

		   $relatorio .= "<div style=\"width:600px\"><b>Anexo Importados:</b> ".$anexoimportados."</div><br>";
		   $relatorio .= "<br>";
	   }
		
	   $_SESSION['RelImportacao'] = $relatorio;
	   $_SESSION['Importacao'] = 'Erro';
			
	} else {
		   
	   $relatorio .= "<span style='color:#0080FF;'>Importação realizada com sucesso!</span><br><br>";	
		
		//usado para remover os 2 ultimos caracteres da string, para desaparecer com a ultima vírgula
	   $size = strlen($Anexoimportados);
	   $Anexoimportados = substr($Anexoimportados,0, $size-2);

	   $relatorio .= "Total de registros no arquivo: ".$qtd."<br>";
	   $relatorio .= "Total de registros importados: ".$importados."<br><br>";

	   $relatorio .= "<div style=\"width:600px\"><b>Anexo Importados:</b> ".$Anexoimportados."</div>";
	   
	   $_SESSION['RelImportacao'] = $relatorio; 
	   $_SESSION['Importacao'] = 'Sucesso';

	}

}

irpara("clienteAnexo.php");

?>