<?php

$sql = "SELECT isNull(dbo.fnDebitosDia(".$_SESSION['UnidadeId'].", null, convert(date, getdate())), 0.00) as Debito";
$result = $conn->query($sql);
$rowResumo = $result->fetch(PDO::FETCH_ASSOC);

$fDebito = mostraValor($rowResumo['Debito']);

?>

<!-- Main sidebar -->
<div class="sidebar sidebar-light sidebar-right sidebar-expand-md">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Resumo do Financeiro -->
        <div class="card">
            
            <div style="padding: 30px 10px 15px 10px;background: #aaa;text-align: center;">
                <h2 style="color:#333">Resumo do Financeiro</h2>
            </div>

            <div style="padding: 10px 10px 0px 10px;background: #EEEDED;text-align: center;border-top: 1px solid #ccc;border-bottom: 1px solid #ccc;">
                <?php echo "<h3>".date("d/m/Y")."</h3>"; ?>
            </div>

            <div style="padding: 10px 10px 0 10px; background: #ccc;">
                <div class="form-group" style="font-size:16px;">
                    <select id="cmbContaBanco" name="cmbContaBanco" class="form-control form-control-select2">
                        <option value="">Todos</option>
                        <?php
                            $sql = "SELECT CnBanId, CnBanNome, dbo.fnDebitosDia(".$_SESSION['UnidadeId'].", CnBanId, convert(date, getdate())) as Debito
                                    FROM ContaBanco
                                    JOIN Situacao on SituaId = CnBanStatus
                                    WHERE CnBanUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                                    ORDER BY CnBanNome ASC";
                            $result = $conn->query($sql);
                            $rowContaBanco = $result->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($rowContaBanco as $item) {
                                print('<option value="'.$item['CnBanId'].'#'.$item['Debito'].'">' . $item['CnBanNome'] . '</option>');
                            }
                        ?>
                    </select>
                    <h3 class="form-text text-right" style="color: #666;">Conta</h3>
                </div>

                <div class="form-group">
                    <input id="inputCredito" name="inputCredito" class="form-control" value="1.400,00" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-right" style="color: #666;">Crédito</h3>
                </div> 

                <div class="form-group">
                    <input id="inputDebito" name="inputDebito" class="form-control" value="<?php echo $fDebito; ?>" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-right" style="color: #666;">Débito</h3>
                </div>                

                <div class="form-group">
                    <input id="inputSaldo" name="inputSaldo" class="form-control" value="200,00" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-right" style="color: #666;"><b>Saldo</b> (Crédito - Débito)</h3>
                </div>
            </div>
        </div>
        <!-- /main navigation -->

        <div style="background: #ccc; height:10px; margin-top: 0px;"></div>

        <div class="card">

            <div style="padding: 10px 10px 5px 10px;background: #aaa;text-align: center;border-top: 1px solid #ccc;border-bottom: 1px solid #ccc;">
                <?php echo "<h2>Acesso Rápido</h2>"; ?>
            </div>        
            <div class="card-body" style="">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%;">
                            <span>PDV</span>
                        </button>

                        <button type="button" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">
                            <span>Transferência</span>
                        </button>

                        <button type="button" class="btn btn-outline bg-slate-600 text-slate-600 border-slate legitRipple" style="width: 100%; margin-top: 10px;">
                            <span>Movimentação</span>
                        </button>                        
                    </div>                    
                </div>
            </div>
        </div>

    </div>
    <!-- /sidebar content -->
			
</div>
<!-- /main sidebar -->
