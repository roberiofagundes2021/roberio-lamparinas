<?php

	/*--------------------------------------------------------------------------
	  |	    Passando data do text box "AAAA-MM-DD" para "DD/MM/AAAA"		   |
	  -------------------------------------------------------------------------*/
	function mostradata($data) {
	
		if ($data <> '0000-00-00'){
			$data = explode(" ", $data);
			$data = explode("-", $data[0]);
			$dataformatada = array();
			@$dataformatada = date("d/m/Y", mktime(0,0,0, $data[1] , $data[2] , $data[0] ));
		} 
		else{
			$dataformatada = '';
		}
		return($dataformatada);
	}
	
	/*--------------------------------------------------------------------------
	  |					Exibe mensagem na tela								   |
	  -------------------------------------------------------------------------*/
	function alerta($mensagem) {
		echo "
			<script language=\"javascript\">
			alert(\"$mensagem\");
			</script>
			";
	}
	
	/*--------------------------------------------------------------------------
	  |					Direciona para a url detectada			   			   |
	  -------------------------------------------------------------------------*/
	function irpara($link) {
		echo "<meta http-equiv=\"refresh\" content=\"0;url=$link\">";
		exit();
	}
	

	/*--------------------------------------------------------------------------*/
	function voltar() {
		echo "<meta http-equiv=\"refresh\" content=\"0;url=javascript:history.go(-1)\">";
		exit();
	}
	
	/*--------------------------------------------------------------------------*/
	function fechar() {
		echo '
		<script language="javascript">
		  window.close();
		</script>
		';
		exit();
	}
	
	/*-------------------------------------------------------------------------*/
	function abrir($link, $texto, $largura, $altura, $barra, $ferramenta) {
		return  '<a href="javascript:;" onClick="window.open(\''.$link.'\',\'\',\'width='.$largura.',height='.$altura.',scrollbars='.$barra.',toolbar='.$ferramenta.'\')">'.$texto.'</a>';
	}
	
	
	//-------------------------------------------------------------------------
	//###############################################################
	
	 function anti_injection($sql) //evita que aconteça ataques SQL INJECTION
	 {
	 // remove palavras que contenham sintaxe sql
	 $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
	// $sql = trim($sql);//limpa espaços vazio
	 $sql = strip_tags($sql);//tira tags html e php
	 $sql = addslashes($sql);//Adiciona barras invertidas a uma string
	 return $sql;
	 
	# //modo de usar pegando dados vindos do formulario
	# $nome = anti_injection($_POST["nome"]);
	# $senha = anti_injection($_POST["senha"]);
	
	 
	 }
	
	// Passando data do text box "DD/MM/AAAA" para "AAAA-MM-DD"
	function gravadata ($data) {
	if ($data != '') {
	   $parte = explode("/", $data);
		return ($parte[2].'/'.$parte[1].'/'.$parte[0]); 
	   /* return (substr($data,6,4).'/'.substr($data,3,2).'/'.substr($data,0,2));  */
	}
	else { 
		return ''; }
	}
	
	function gravavalor($campo){
		//Varre o conteudo da variavel $total e troca todos os pontos por em branco. Ex.: 5.423,36 ficaria 5423,36
		$frase = str_replace(".","",$campo);
		//Varre o conteudo da variavel $frase e troca todas as virgulas por ponto. Ex.: 5423,36 ficaria 5423.36
		$campo = str_replace(",",".",$frase);
		
		return $campo;		
	}
	
	function formatamoeda($campo){
		//Varre o conteudo da variavel $total e troca todos os pontos por em branco. Ex.: 5.423,36 ficaria 5423,36
		$frase = number_format($campo, 2, '.', '');
		//Varre o conteudo da variavel $frase e troca todas as virgulas por ponto. Ex.: 5423,36 ficaria 5423.36
		$campo = "R$ " . number_format($frase, 2, ',', '.');
		
		return $campo;		
	}	
	
	//-------------------------------- Retira os acentos
	function tiraacento($s) {
		$s = ereg_replace("[áàâãª]","a",$s);
		$s = ereg_replace("[ÁÀÂÃ]","A",$s);
		$s = ereg_replace("[éèê]","e",$s);
		$s = ereg_replace("[ÉÈÊ]","E",$s);
		$s = ereg_replace("[óòôõº]","o",$s);
		$s = ereg_replace("[ÓÒÔÕ]","O",$s);
		$s = ereg_replace("[úùû]","u",$s);
		$s = ereg_replace("[ÚÙÛ]","U",$s);
		$s = str_replace("ç","c",$s);
		$s = str_replace("Ç","C",$s);
		$s = ereg_replace("  ","",$s);
		return ($s);
	}
	
	function nomeSobrenome($fullName) {
		$arr = explode(' ', $fullName);
		/* Junta os dois primeiros nomes em uma nova string */
		if(isset($arr[1])){
			$doisNomes = $arr[0] . ' ' . $arr[1];
		} else {
			$doisNomes = $arr[0];
		}
		return $doisNomes;
	}
	

?>
