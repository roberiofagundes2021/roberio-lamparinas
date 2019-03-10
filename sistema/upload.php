<?php
   
	include_once("sessao.php");    
	//include('global_assets/php/conexao.php');
   
	//echo json_encode($_FILES['imagem']['name']);die;
    //print_r($_POST);
    //print_r($_FILES); die;
    $pasta = "global_assets/images/produtos/";
     
    // formatos de imagem permitidos
    $permitidos = array(".jpg", ".jpeg", ".gif", ".png", ".bmp");   
         
    if(isset($_FILES)){
		
		//print_r($_FILES);
		
        $nome_imagem    = $_FILES['file']['name'];
        $tamanho_imagem = $_FILES['file']['size'];
         
        // pega a extensão do arquivo
        $ext = strtolower(strrchr($nome_imagem,"."));
         
        //  verifica se a extensão está entre as extensões permitidas
        if(in_array($ext,$permitidos)){
             
            // converte o tamanho para KB
            $tamanho = round($tamanho_imagem / 1024);
             
            if($tamanho < 1024){ //se imagem for até 1MB envia
                $nome_atual = md5(uniqid(time())).$ext; //nome que dará a imagem

                $tmp = $_FILES['file']['tmp_name']; //caminho temporário da imagem
                
                $destino = $pasta.$nome_atual;

				//ATENCAO: tive que dá permissão no diretório "sudo chmod 777 -R /var/www/html/lamparinas/global_assets/images/produtos" pra funcionar.
				//Tem que testar no servidor, pra ver se teremos que fazer isso também no SERVIDOR da AZURE
                 
                // se enviar a foto, insere o nome da foto no banco de dados
                if(move_uploaded_file($tmp, $destino)){
                    
                    //verifica se já foi adicionado alguma foto, se sim, exclui ela fisicamente, já que nao será mais usada, eliminando lixo.
                    if ($_SESSION['fotoAtual'] != ''){
						unlink($pasta.$_SESSION['fotoAtual']);
					}                    
                    
                    echo "<a href=\"".$pasta.$nome_atual."\" class=\"fancybox\"><img src='".$pasta.$nome_atual."' class='ml-3' style='max-width: 260px; max-height:250px; border:2px solid #ccc;'></a>"; //imprime a foto na tela
                    
                    $_SESSION['fotoAtual'] = $nome_atual; //guarda o nome da foto pra gravar no banco
                }else{
                    echo "Falha ao enviar";
                }
            }else{
                echo "A imagem deve ser de no máximo 1MB";
            }
        }else{
            echo "Somente são aceitos arquivos do tipo Imagem";
        }
    }else{
        echo "Selecione uma imagem";
        exit;
    }
    
?>
