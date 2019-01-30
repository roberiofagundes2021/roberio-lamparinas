
/* Função responsavel pelos alertas do sistema */
function alerta(titulo, msg, tipo, modal) {
			
	var opts = {
		title: "",
		text: "",
		type: "",
		icon: ""
	};

	opts.title = titulo;
	opts.text = msg;
	opts.type = tipo;
	//opts.desktop = {desktop: true}
	opts.addclass = 'stack-modal',
	opts.stack = {'dir1': 'down', 'dir2': 'right', 'modal': false}
	
	switch (tipo) {
	case 'success':
		opts.icon = "icon-checkmark3";
		break;
	case 'error':
		opts.icon = "icon-blocked";
		break;
	}
	//console.log(opts);
	//PNotify.desktop.permission();
	
	new PNotify(opts);
}	

/* Confirma exclusão antes de excluir um registro */
function confirmaExclusao(form, texto, acao) {
				
	new PNotify({
		 title: 'Confirmação',
		 text: texto,
		 icon: 'icon-question4',
		 hide: false,
		 confirm: {
			confirm: true,
			buttons: [{
				  text: 'Sim',
				  primary: true,
				  click: function(notice) {
					  form.action = acao;
					  form.submit();
				  },
				},
				{
				  text: 'Não',						  
				  click: function(notice) {
					 notice.remove();
				  }
				}
			]
		 },			 
		 buttons: {
			closer: false,
			sticker: false
		 },
		 history: {
			history: false
		 },
		 addclass: 'stack-modal',
		 stack: {'dir1': 'down', 'dir2': 'right', 'modal': false}
	}).get().on('pnotify.confirm', function(){
		 form.action = acao;
		 form.submit();
	}).get().on('pnotify.cancel', function(){
		 return false;
	});			
}

function moeda(z){
	v = z.value;
	v = v.replace(/\D/g,"") //permite digitar apenas números
	v = v.replace(/[0-9]{12}/,"inválido") //limita pra máximo 999.999.999,99
	v=v.replace(/(\d)(\d{8})$/,"$1.$2");//coloca o ponto dos milhões
	v=v.replace(/(\d)(\d{5})$/,"$1.$2");//coloca o ponto dos milhares
	//v = v.replace(/(\d{1})(\d{1,2})$/,"$1.$2") //coloca ponto antes dos últimos 2 digitos
	v = v.replace(/(\d{1})(\d{1,2})$/,"$1,$2") //coloca virgula antes dos últimos 2 digitos
	z.value = v;
}
