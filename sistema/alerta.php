<?php

	if (isset($_SESSION['msg'])){
		
		print("
			<script>
				
				var titulo = '".$_SESSION['msg']['titulo']."';
				var msg = '".$_SESSION['msg']['mensagem']."';
				var tipo = '".$_SESSION['msg']['tipo']."';							
				
				if (msg) {
												
					$(function(){
						alerta(titulo, msg, tipo);
					});
				}
		
			 </script>  					
		");	
		
		$_SESSION['msg'] = array();
	}
	
?>
