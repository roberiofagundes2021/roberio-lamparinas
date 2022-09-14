<?php
//Variável cetada na página caixaPDV
//alerta($teste); 

?>

<script type="text/javascript">
    $(document).ready(function () {
        function consultaSaldoCaixaAtual() {
            let urlConsultaAberturaCaixa = "consultaCaixaSaldoAtual.php";
            
            //Verifica se deverá ou não abrir o caixa
            $.ajax({
                type: "POST",
                url: urlConsultaAberturaCaixa,
                dataType: "json",
                success: function(resposta) {
                    if(resposta != 'consultaVazia') {
                        let saldoInicial = parseFloat(resposta[0].CxAbeSaldoInicial);
                        let valorRecebido = parseFloat(resposta[1].SaldoRecebido);
                        let valorPago = parseFloat(resposta[2].SaldoPago);
                        let saldo = saldoInicial + valorRecebido - valorPago;
                        
                        $("#inputResumoCaixaSaldoInicial").val(float2moeda(saldoInicial));
                        $("#inputResumoCaixaRecebido").val(float2moeda(valorRecebido));
                        $("#inputResumoCaixaPago").val(float2moeda(valorPago * -1));        
                        $("#inputResumoCaixaSaldo").val(float2moeda(saldo));
                    }else {
                        $("#inputResumoCaixaSaldoInicial").val('');
                        $("#inputResumoCaixaRecebido").val('');
                        $("#inputResumoCaixaPago").val('');
                        $("#inputResumoCaixaSaldo").val('');
                    }
                }
            })
        }

        consultaSaldoCaixaAtual();
    });
    
</script>

<!-- Main sidebar -->
<div class="sidebar sidebar-light sidebar-right sidebar-expand-md">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Resumo do Financeiro -->
        <div class="card">
            
            <div style="padding: 30px 10px 15px 10px;background: #aaa;text-align: center;">
                <h2 style="color:#333">Resumo do Caixa</h2>
            </div>

            <div style="padding: 10px 10px 0px 10px;background: #EEEDED;text-align: center;border-top: 1px solid #ccc;border-bottom: 1px solid #ccc;">
                <!--
                <input type="date" id="inputDtVencResumoFinanceiro" onchange="selecionaData();" name="inputDtVencResumoFinanceiro" class="form-control" value="<?php //echo date('Y-m-d'); ?>" style="font-size: 21px; text-align: center;">
                -->
                <h2><?php echo date('d/m/Y'); ?></h2>
            </div>

            <div style="padding: 10px 10px 0 10px; background: #ccc;">
                <div id="dados">
                    <div class="form-group">
                        <h3 class="form-text text-right" style="color: #666;">Saldo Inicial</h3>
                        <input id="inputResumoCaixaSaldoInicial" name="inputResumoCaixaSaldoInicial" class="form-control" value="" style="font-size: 30px; text-align: right;" readonly>
                    </div>
                
                    <div class="form-group">
                        <h3 class="form-text text-right" style="color: #666;">Recebido</h3>
                        <input id="inputResumoCaixaRecebido" name="inputResumoCaixaRecebido" class="form-control" value="" style="font-size: 30px; text-align: right;" readonly>
                    </div>
                
                    <div class="form-group">
                        <h3 class="form-text text-right" style="color: #666;">Pago</h3>
                        <input id="inputResumoCaixaPago" name="inputResumoCaixaPago" class="form-control" value="" style="font-size: 30px; text-align: right;" readonly>
                    </div>             
    
                    <div class="form-group">
                        <h3 class="form-text text-right" style="color: #666;"><b>Saldo</b></h3>
                        <input id="inputResumoCaixaSaldo" name="inputResumoCaixaSaldo" class="form-control" value="" style="font-size: 30px; text-align: right;" readonly>
                    </div>
                </div>
            </div>
        </div>
        <!-- /main navigation -->

        <div style="background: #ccc; height:10px; margin-top: 0px;"></div>

        <div class="card">      
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <button id="btnPdv" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">PDV</button>
                    </div>

                    <div class="col-6">
                        <a href="caixaMovimentacao.php" type="button" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">
                            <span style="font-size: 11px;">Movimentação</span>
                        </a>   
                    </div>
                </div>

                <div id="caixaEmOperacao" class="row">
                    <div class="col-6">
                        <button id="btnRetirada" type="button" data-toggle="modal" data-target="#modal_small_Retirada_Caixa" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">
                            <span style="font-size: 11px;">Retirada</span>
                        </button>
                    </div>
                     
                    <div class="col-6">
                        <button id="btnFechamento" type="button" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">
                            <span style="font-size: 11px;">Fechamento</span>
                        </button>   
                    </div>                    
                </div>

                <h4 class="text-center p-1"><?php echo "Operador: " . nomeSobrenome($_SESSION['UsuarNome'], 1); ?></h4>
            </div>
        </div>

    </div>
    <!-- /sidebar content -->
			
</div>
<!-- /main sidebar -->
