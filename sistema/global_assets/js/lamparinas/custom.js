/* Função responsavel pelos alertas do sistema */
function alerta(titulo, msg, tipo, modal) {
	var opts = {
		title: '',
		text: '',
		type: '',
		icon: '',
	};

	opts.title = titulo;
	opts.text = msg;
	opts.type = tipo;
	//opts.desktop = {desktop: true}
	opts.addclass = 'stack-modal';
	opts.stack = { dir1: 'down', dir2: 'right', modal: false };

	switch (tipo) {
		case 'success':
			opts.icon = 'icon-checkmark3';
			break;
		case 'error':
			opts.icon = 'icon-blocked';
			break;
		case 'info':
			opts.icon = 'icon-info22';
			break;
	}

	//console.log(opts);
	//PNotify.desktop.permission();

	new PNotify(opts);
}

/* Confirma exclusão antes de excluir um registro */
function confirmaExclusao(form, texto, acao, acaoComplete) {
	new PNotify({
		title: 'Confirmação',
		text: texto,
		icon: 'icon-question4',
		hide: false,
		confirm: {
			confirm: true,
			buttons: [
				{
					text: 'Sim',
					primary: true,
					click: function (notice) {
						form.action = acao;
						form.submit();
					},
				},
				{
					text: 'Não',
					click: function (notice) {
						notice.remove();
					},
				},
			],
		},
		buttons: {
			closer: false,
			sticker: false,
		},
		history: {
			history: false,
		},
		addclass: 'stack-modal',
		stack: { dir1: 'down', dir2: 'right', modal: false },
	})
		.get()
		.on('pnotify.confirm', function () {
			form.action = acao;
			form.submit();
		})
		.get()
		.on('pnotify.cancel', function () {
			return false;
		});
}

function confirmaExclusaoAjax(url, texto, tipoRequest, id, acaoSucces) {
	new PNotify({
		title: 'Confirmação',
		text: texto,
		icon: 'icon-question4',
		hide: false,
		confirm: {
			confirm: true,
			buttons: [
				{
					text: 'Sim',
					primary: true,
					click: function (notice) {
						$.ajax({
							type: 'POST',
							url: url,
							dataType: 'json',
							data: {
								'tipoRequest': tipoRequest,
								'id': id
							},
							success: function(response) {
								if(response.status  == 'success'){
									alerta(response.titulo, response.menssagem, response.status)
									acaoSucces()
								} else {
									alerta(response.titulo, response.menssagem, response.status)
								}
							}
						});
						notice.remove();
					},
				},
				{
					text: 'Não',
					click: function (notice) {
						notice.remove();
					},
				},
			],
		},
		buttons: {
			closer: false,
			sticker: false,
		},
		history: {
			history: false,
		},
		addclass: 'stack-modal',
		stack: { dir1: 'down', dir2: 'right', modal: false },
	})
}

function confirmaReset(form, texto, acao, id) {
	new PNotify({
		title: 'Confirmação',
		text: texto,
		icon: 'icon-question4',
		hide: false,
		confirm: {
			confirm: true,
			buttons: [
				{
					text: 'Sim',
					primary: true,
					click: function (notice) {
						form.action = acao;
						$("#inputTypeRequest").val("reset");
						if(Array.isArray(id)){
							id.forEach(element => {
								$("#"+element).val(null);
							})
						}else{
							$("#"+id).val(null);
						}
						form.submit();
					},
				},
				{
					text: 'Não',
					click: function (notice) {
						notice.remove();
					},
				},
			],
		},
		buttons: {
			closer: false,
			sticker: false,
		},
		history: {
			history: false,
		},
		addclass: 'stack-modal',
		stack: { dir1: 'down', dir2: 'right', modal: false },
	})
		.get()
		.on('pnotify.confirm', function () {
			form.action = acao;
			form.submit();
		})
		.get()
		.on('pnotify.cancel', function () {
			return false;
		});
}

function moeda(z) {
	v = z.value;
	v = v.replace(/\D/g, ''); //Permite digitar apenas números
	v = v.replace(/[0-9]{12}/, 'inválido'); //limita pra máximo 999.999.999,99
	v = v.replace(/(\d)(\d{8})$/, '$1.$2'); //coloca o ponto dos milhões
	v = v.replace(/(\d)(\d{5})$/, '$1.$2'); //coloca o ponto dos milhares
	//v = v.replace(/(\d{1})(\d{1,2})$/,"$1.$2") //coloca ponto antes dos últimos 2 digitos
	v = v.replace(/(\d{1})(\d{1,2})$/, '$1,$2'); //coloca virgula antes dos últimos 2 digitos
	z.value = v;
}

function moedatofloat(num) {
	if (num === '') {
		num = 0;
	} else {
		num = num.replace('.', '');
		num = num.replace(',', '.');
		num = parseFloat(num);
	}
	return num;
}

function float2moeda(num) {
	x = 0;

	if (num < 0) {
		num = Math.abs(num);
		x = 1;
	}
	if (isNaN(num)) num = '0';
	cents = Math.floor((num * 100 + 0.5) % 100);

	num = Math.floor((num * 100 + 0.5) / 100).toString();

	if (cents < 10) cents = '0' + cents;
	for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
		num =
			num.substring(0, num.length - (4 * i + 3)) +
			'.' +
			num.substring(num.length - (4 * i + 3));
	ret = num + ',' + cents;
	if (x == 1) ret = ' - ' + ret;

	return ret;
}

/* Só aceita numeros */
function onlynumber(evt) {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode(key);
	//var regex = /^[0-9.,]+$/;
	var regex = /^[0-9]+$/;
	if (!regex.test(key)) {
		theEvent.returnValue = false;
		if (theEvent.preventDefault) theEvent.preventDefault();
	}
}

function validaCPF(strCPF) {
    var Soma;
    var Resto;
    Soma = 0;
    let numerosUnicos = [...new Set(strCPF)];
    if(numerosUnicos.length <=1){
        return false;
    }

    for (i = 1; i <= 9; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10))) return false;

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11))) return false;
    return true;
}

function validarCNPJ(cnpj) {
 
	cnpj = cnpj.replace(/[^\d]+/g,'');

	if(cnpj == '') return false;
	
	if (cnpj.length != 14)
		return false;

	let numerosUnicosCnpj = [...new Set(cnpj)];
    if(numerosUnicosCnpj.length <=1){
    return false;
    }
	
	// Valida DVs
	tamanho = cnpj.length - 2
	numeros = cnpj.substring(0,tamanho);
	digitos = cnpj.substring(tamanho);
	soma = 0;
	pos = tamanho - 7;
	for (i = tamanho; i >= 1; i--) {
	soma += numeros.charAt(tamanho - i) * pos--;
	if (pos < 2)
			pos = 9;
	}
	resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	if (resultado != digitos.charAt(0))
		return false;
		
	tamanho = tamanho + 1;
	numeros = cnpj.substring(0,tamanho);
	soma = 0;
	pos = tamanho - 7;
	for (i = tamanho; i >= 1; i--) {
	soma += numeros.charAt(tamanho - i) * pos--;
	if (pos < 2)
			pos = 9;
	}
	resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	if (resultado != digitos.charAt(1))
		return false;
			
	return true;
	
}	