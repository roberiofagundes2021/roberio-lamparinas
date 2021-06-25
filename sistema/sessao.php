<?php

session_start();
include('global_assets/php/conexao.php');
$visualizar = true;
$atualizar = 0;
$excluir = 0;

// faz o controle de acesso às paginas de acordo à permissão

foreach($_SESSION['Permissoes'] as $key => $permissao){
	if($permissao['url'] == basename($_SERVER['REQUEST_URI'])){
		$atualizar = $permissao['atualizar'];
		$excluir = $permissao['excluir'];
		if($permissao['visualizar'] == 0){
			$visualizar = false;
		}
	}
}
if(!$visualizar){header("location:javascript://history.go(-1)");}

$arquivosEmpresa = array(
	'usuario.php', 'usuarioNovo.php', 'usuarioEdita.php', 'usuarioExclui.php', 'usuarioMudaSituacao.php', 'usuarioValida.php',
	'licenca.php', 'licencaNovo.php', 'licencaEdita.php', 'licencaExclui.php', 'licencaMudaSituacao.php',
	'unidade.php', 'unidadeNovo.php', 'unidadeEdita.php', 'unidadeExclui.php', 'unidadeMudaSituacao.php', 'unidadeValida.php',
	'setor.php', 'setorNovo.php', 'setorEdita.php', 'setorExclui.php', 'setorMudaSituacao.php', 'filtraSetor.php', 'setorValida.php',
	'menu.php', 'menuNovo.php', 'menuEdita.php', 'menuExclui.php', 'menuMudaSituacao.php', 'menuLeftSecundario.php',
	'parametro.php', 'menuLeftSecundarioAjax.php', 'filtraLocalEstoque.php', 'localEstoque.php', 'localEstoqueNovo.php', 
	'localEstoqueEdita.php', 'localEstoqueExclui.php', 'localEstoqueValida.php', 'localEstoqueMudaSituacao.php', 
	'usuarioLotacao.php', 'usuarioLotacaoNovo.php', 'usuarioLotacaoValida.php', 'usuarioLotacaoExclui.php'
);

//Se existe a sessão $_SESSION['EmpresaId'] e a página que está sendo acessada não é nenhuma das sitadas acima, limpa essa sessão.	  
if (array_key_exists('EmpresaId', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $arquivosEmpresa)) {
	unset($_SESSION['EmpresaId']);
	unset($_SESSION['EmpresaNome']);
}

$arquivosTermoReferencia = array(
	'trAprovacaoAdministrativo.php', 'trAprovacaoComissao.php', 'trAprovacaoContabilidade.php', 
	'trComissao.php', 'trComissaoExclui.php', 'trComissaoPresidente.php', 'trComissaoValida.php', 
	'trDotacao.php', 'trDotacaoExclui.php', 'trDotacaoNovo.php', 'trEdita.php', 'trExclui.php', 
	'trFiltraProduto.php', 'trFiltraServico.php', 'trGravaProduto.php', 'trGravaServico.php',
 	'trImprime.php', 'trMudaSituacao.php', 'trMudaSituacaoContabilidade', 'trNovo.php', 'trOrcamento.php',
	'trOrcamentoDuplica.php', 'trOrcamentoEdita.php', 'trOrcamentoExclui.php', 'trOrcamentoImprime.php',
	'trOrcamentoNovo.php', 'trOrcamentoProduto.php', 'trOrcamentoServico.php', 'trProduto.php', 'trServico.php',
	'trValidaProdutoServico.php', 'trValidaQuantidade.php', 'trVerificaProdutoServico.php', 'trComissaoAnexoNovo.php',
	'trComissaoAnexoExclui.php');

if ((array_key_exists('TRId', $_SESSION) || array_key_exists('TRNumero', $_SESSION)) && !in_array(basename($_SERVER['PHP_SELF']), $arquivosTermoReferencia)) {
	
	unset($_SESSION['TRId']);
	unset($_SESSION['TRNumero']);
}

$arquivosAditivo = array('fluxoAditivo.php', 'fluxoAditivoNovo.php', 'fluxoAditivoEdita.php', 'fluxoAditivoExclui.php');

//Se existe a sessão $_SESSION['FluxoId'] e a página que está sendo acessada não é nenhuma das sitadas acima, limpa essa sessão.	  
if (array_key_exists('FluxoId', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $arquivosAditivo)) {
	unset($_SESSION['FluxoId']);
}

$arquivoAditivoNovo = array('fluxoAditivoNovo.php');

if (array_key_exists('AditivoNovo', $_SESSION) and !in_array(basename($_SERVER['PHP_SELF']), $arquivoAditivoNovo)) {

	$sql = "DELETE FROM Aditivo WHERE AditiId =  :id
		";
	$result = $conn->prepare($sql);
	$result->bindParam(':id', $_SESSION['AditivoNovo']);
	$result->execute();

	unset($_SESSION['AditivoNovo']);
}

$arquivosMovimentacaoFinanceira = array(
	'movimentacaoFinanceira.php', 'movimentacaoFinanceiraFiltra.php', 'movimentacaoFinanceiraExclui.php', 'movimentacaoFinanceiraImprime.php', 'movimentacaoFinanceiraPagamento.php', 'movimentacaoFinanceiraRecebimento.php', 'movimentacaoFinanceiraTransferencia.php');

if ((array_key_exists('MovFinancPeriodoDe', $_SESSION) || array_key_exists('MovFinancAte', $_SESSION) || array_key_exists('MovFinancContaBanco', $_SESSION) || array_key_exists('MovFinancPlanoContas', $_SESSION)|| array_key_exists('MovFinancCentroDeCustos', $_SESSION) || array_key_exists('MovFinancStatus', $_SESSION) || array_key_exists('MovFinancFormaPagamento', $_SESSION)) && !in_array(basename($_SERVER['PHP_SELF']), $arquivosMovimentacaoFinanceira)) {
	
	unset($_SESSION['MovFinancPeriodoDe']);
	unset($_SESSION['MovFinancAte']);
	unset($_SESSION['MovFinancContaBanco']);
	unset($_SESSION['MovFinancPlanoContas']);
	unset($_SESSION['MovFinancCentroDeCustos']);
	unset($_SESSION['MovFinancStatus']);
	unset($_SESSION['MovFinancFormaPagamento']);
}

$arquivosMovimentacaoFinanceiraConciliacao = array(
	'movimentacaoFinanceiraConciliacao.php', 'movimentacaoFinanceiraConciliacaoFiltra.php', 'movimentacaoFinanceiraExclui.php', 'movimentacaoFinanceiraImprime.php', 'movimentacaoFinanceiraPagamento.php', 'movimentacaoFinanceiraRecebimento.php', 'movimentacaoFinanceiraTransferencia.php');

if ((array_key_exists('MovimentacaoFinanceiraConciliacaoPeriodoDe', $_SESSION) || array_key_exists('MovimentacaoFinanceiraConciliacaoAte', $_SESSION) || array_key_exists('MovimentacaoFinanceiraConciliacaoContaBanco', $_SESSION) || array_key_exists('MovimentacaoFinanceiraConciliacaoPlanoContas', $_SESSION)|| array_key_exists('MovimentacaoFinanceiraConciliacaoCentroDeCustos', $_SESSION) || array_key_exists('MovimentacaoFinanceiraConciliacaoStatus', $_SESSION) || array_key_exists('MovimentacaoFinanceiraConciliacaoFormaPagamento', $_SESSION)) && !in_array(basename($_SERVER['PHP_SELF']), $arquivosMovimentacaoFinanceiraConciliacao)) {
	
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoPeriodoDe']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoAte']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoContaBanco']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoPlanoContas']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoCentroDeCustos']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoStatus']);
	unset($_SESSION['MovimentacaoFinanceiraConciliacaoFormaPagamento']);
}


$arquivosContasAPagar = array(
	'contasAPagar.php', 'contasAPagarNovoLancamento.php',
	'contasAPagarFiltra.php', 'contasAPagarExclui.php',
	'contasAPagarPagamentoAgrupado.php',
	'contasAPagarParcelamento.php'
);

if ((array_key_exists('ContPagPeriodoDe', $_SESSION) || array_key_exists('ContPagAte', $_SESSION) || array_key_exists('ContPagFornecedor', $_SESSION) || array_key_exists('ContPagPlanoContas', $_SESSION) || array_key_exists('ContPagStatus', $_SESSION)) && !in_array(basename($_SERVER['PHP_SELF']), $arquivosContasAPagar)) {

	unset($_SESSION['ContPagPeriodoDe']);
	unset($_SESSION['ContPagAte']);
	unset($_SESSION['ContPagFornecedor']);
	unset($_SESSION['ContPagPlanoContas']);
	unset($_SESSION['ContPagStatus']);
}

$arquivosContasAReceber = array(
	'contasAReceber.php', 'contasAReceberNovoLancamento.php',
	'contasAReceberFiltra.php', 'contasAReceberExclui.php',
	'contasAReceberPagamentoAgrupado.php',
	'contasAReceberParcelamento.php'
);

if ((array_key_exists('ContRecPeriodoDe', $_SESSION)
		|| array_key_exists('ContRecAte', $_SESSION)
		|| array_key_exists('ContRecClientes', $_SESSION)
		|| array_key_exists('ContRecPlanoContas', $_SESSION)
		|| array_key_exists('ContRecStatus', $_SESSION)
		|| array_key_exists('ContRecNumDoc', $_SESSION)
		|| array_key_exists('ContRecFormaPagamento', $_SESSION))
	&& !in_array(basename($_SERVER['PHP_SELF']), $arquivosContasAReceber)
) {

	unset($_SESSION['ContRecPeriodoDe']);
	unset($_SESSION['ContRecAte']);
	unset($_SESSION['ContRecCliente']);
	unset($_SESSION['ContRecPlanoContas']);
	unset($_SESSION['ContRecStatus']);
	unset($_SESSION['ContRecNumDoc']);
	unset($_SESSION['ContRecFormaPagamento']);
}


if (!array_key_exists('UsuarId', $_SESSION)) {  // or !$_SESSION['UsuarLogado']
	header('Expires: 0');
	header('Pragma: no-cache');
	header("Location: login.php");
	return false;
}

require_once("global_assets/php/funcoesgerais.php");