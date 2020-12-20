<?php

/*--------------------------------------------------------------------------
	  |	    Passando data do text box "AAAA-MM-DD" para "DD/MM/AAAA"		   |
	  -------------------------------------------------------------------------*/
function mostraData($data)
{

	if ($data <> '0000-00-00' and $data <> '') {
		$data = explode(" ", $data);
		$data = explode("-", $data[0]);
		$dataformatada = array();
		@$dataformatada = date("d/m/Y", mktime(0, 0, 0, $data[1], $data[2], $data[0]));
	} else {
		$dataformatada = '';
	}
	return ($dataformatada);
}

/*--------------------------------------------------------------------------
	  |					Exibe mensagem na tela								   |
	  -------------------------------------------------------------------------*/
function alerta($mensagem)
{
	echo "
			<script language=\"javascript\">
			alert(\"$mensagem\");
			</script>
			";
}

/*--------------------------------------------------------------------------
	  |					Direciona para a url detectada			   			   |
	  -------------------------------------------------------------------------*/
function irpara($link)
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=$link\">";
	exit();
}


/*--------------------------------------------------------------------------*/
function voltar()
{
	echo "<meta http-equiv=\"refresh\" content=\"0;url=javascript:history.go(-1)\">";
	exit();
}

/*--------------------------------------------------------------------------*/
function fechar()
{
	echo '
		<script language="javascript">
		  window.close();
		</script>
		';
	exit();
}

/*-------------------------------------------------------------------------*/
function abrir($link, $texto, $largura, $altura, $barra, $ferramenta)
{
	return  '<a href="javascript:;" onClick="window.open(\'' . $link . '\',\'\',\'width=' . $largura . ',height=' . $altura . ',scrollbars=' . $barra . ',toolbar=' . $ferramenta . '\')">' . $texto . '</a>';
}


//-------------------------------------------------------------------------
//###############################################################

function anti_injection($sql) //evita que aconte�a ataques SQL INJECTION
{
	// remove palavras que contenham sintaxe sql
	$sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
	// $sql = trim($sql);//limpa espa�os vazio
	$sql = strip_tags($sql); //tira tags html e php
	$sql = addslashes($sql); //Adiciona barras invertidas a uma string
	return $sql;

	# //modo de usar pegando dados vindos do formulario
	# $nome = anti_injection($_POST["nome"]);
	# $senha = anti_injection($_POST["senha"]);


}

// Passando data do text box "DD/MM/AAAA" para "AAAA-MM-DD"
function gravaData($data)
{
	if ($data != '' && $data != null) {
		$parte = explode("/", $data);
		return ($parte[2] . '-' . $parte[1] . '-' . $parte[0]);
		/* return (substr($data,6,4).'/'.substr($data,3,2).'/'.substr($data,0,2));  */
	} else {
		return '';
	}
}

function gravaValor($campo)
{
	//Varre o conteudo da variavel $total e troca todos os pontos por em branco. Ex.: 5.423,36 ficaria 5423,36
	$frase = str_replace(".", "", $campo);
	//Varre o conteudo da variavel $frase e troca todas as virgulas por ponto. Ex.: 5423,36 ficaria 5423.36
	$campo = str_replace(",", ".", $frase);

	return $campo;
}

function mostraValor($campo)
{
	//Varre o conteudo da variavel $total e troca todos os pontos por em branco. Ex.: 5.423,36 ficaria 5423,36
	$frase = number_format($campo, 2, '.', '');
	//Varre o conteudo da variavel $frase e troca todas as virgulas por ponto. Ex.: 5423,36 ficaria 5423.36
	$campo = number_format($frase, 2, ',', '.');

	return $campo;
}

function formataMoeda($campo)
{
	//Varre o conteudo da variavel $total e troca todos os pontos por em branco. Ex.: 5.423,36 ficaria 5423,36
	$frase = number_format($campo, 2, '.', '');
	//Varre o conteudo da variavel $frase e troca todas as virgulas por ponto. Ex.: 5423,36 ficaria 5423.36
	$campo = "R$ " . number_format($frase, 2, ',', '.');

	return $campo;
}

//-------------------------------- Retira os acentos
function tiraAcento($string)
{
	$string	= utf8_decode($string);

	// matriz de entrada
	$de = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', ' ', '-', '(', ')', ',', ';', ':', '|', '!', '"', '#', '$', '%', '&', '/', '=', '?', '~', '^', '>', '<', '�', '�');

	// matriz de sa�da
	$para   = array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'A', 'A', 'E', 'I', 'O', 'U', 'n', 'n', 'c', 'C', '', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_');

	// devolver a string
	return str_replace($de, $para, $string);
}

function nomeSobrenome($fullName, $num = 1)
{

	$arr = explode(' ', $fullName);

	/* Junta os dois primeiros nomes em uma nova string */
	if (isset($arr[1])) {
		$doisNomes = $arr[0] . ' ' . $arr[1];
	} else {
		$doisNomes = $arr[0];
	}

	if ($num == 1) {
		return $arr[0];
	} else {
		return $doisNomes;
	}
}

function saudacoes()
{

	date_default_timezone_set('America/Sao_Paulo');

	$hr = date(" H ");

	if ($hr >= 12 && $hr < 18) {
		$resp = "Boa tarde";
	} else if ($hr >= 0 && $hr < 12) {
		$resp = "Bom dia";
	} else {
		$resp = "Boa noite";
	}

	return "$resp";
}

function formatarCPF_Cnpj($cnpj_cpf)
{

	if (strlen(preg_replace("/\D/", '', $cnpj_cpf)) === 11) {
		$response = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
	} else {
		$response = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
	}

	return $response;
}

function formatarChave($texto)
{

	$TextoSemAcentos = tiraacento($texto);

	$TextoSemEspacoBranco = str_replace(' ', '', $TextoSemAcentos);

	$TextoFormatado = strtoupper(trim(utf8_decode($TextoSemEspacoBranco)));

	return $TextoFormatado;
}

function limpaCPF_CNPJ($valor)
{
	$valor = trim($valor);
	$valor = str_replace(".", "", $valor);
	$valor = str_replace(",", "", $valor);
	$valor = str_replace("-", "", $valor);
	$valor = str_replace("/", "", $valor);
	return $valor;
}

function validaCPF($cpf = null)
{

	// Verifica se um n�mero foi informado
	if (empty($cpf)) {
		return false;
	}

	// Elimina possivel mascara
	$cpf = preg_replace("/[^0-9]/", "", $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	// Verifica se o numero de digitos informados � igual a 11 
	if (strlen($cpf) != 11) {
		return false;
	}
	// Verifica se nenhuma das sequ�ncias invalidas abaixo 
	// foi digitada. Caso afirmativo, retorna falso
	else if (
		$cpf == '00000000000' ||
		$cpf == '11111111111' ||
		$cpf == '22222222222' ||
		$cpf == '33333333333' ||
		$cpf == '44444444444' ||
		$cpf == '55555555555' ||
		$cpf == '66666666666' ||
		$cpf == '77777777777' ||
		$cpf == '88888888888' ||
		$cpf == '99999999999'
	) {
		return false;
		// Calcula os digitos verificadores para verificar se o
		// CPF � v�lido
	} else {

		for ($t = 9; $t < 11; $t++) {

			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{
				$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{
			$c} != $d) {
				return false;
			}
		}

		return true;
	}
}

function limpaCEP($valor)
{
	$valor = trim($valor);
	$valor = str_replace("-", "", $valor);
	return $valor;
}

function formatarNumero($numero)
{
	$numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
	return $numero;
}
