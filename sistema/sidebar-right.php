<!-- Main sidebar -->
<div class="sidebar sidebar-light sidebar-right sidebar-expand-md">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Resumo do Financeiro -->
        <div class="card">
            
            <div style="padding: 20px 10px 5px 10px; background: #ccc; text-align: center;">
                <h2 style="color:#333">Resumo do Financeiro</h2>
            </div>

            <div style="padding: 10px 10px 0px 10px; background: #eee; text-align: center; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                <?php echo "<h3>".date("d/m/Y")."</h3>"; ?>
            </div>

            <div style="padding: 10px 10px 0 10px;">
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
                    <h3 class="form-text text-muted text-right">Conta</h3>
                </div>

                <div class="form-group">
                    <input id="inputCredito" name="inputCredito" class="form-control" value="1.400,00" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-muted text-right">Crédito</h3>
                </div> 

                <div class="form-group">
                    <input id="inputDebito" name="inputDebito" class="form-control" value="1.200,00" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-muted text-right">Dédito</h3>
                </div>                

                <div class="form-group">
                    <input id="inputSaldo" name="inputSaldo" class="form-control" value="200,00" style="font-size: 30px; text-align: right;">
                    <h3 class="form-text text-muted text-right"><b>Saldo</b> (Crédito - Débito)</h3>
                </div>
            </div>
        </div>
        <!-- /main navigation -->

        <div style="background: #fff; height:10px; margin-top: 0px;"></div>

        <div class="card">

            <div style="padding: 10px 10px 5px 10px; background: #eee; text-align: center; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                <?php echo "<h3>Acesso Rápido</h3>"; ?>
            </div>        
            <div class="card-body" style="">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary legitRipple" style="width: 100%;">
                            <span>PDV</span>
                        </button>

                        <button type="button" class="btn btn-outline-primary legitRipple" style="width: 100%; margin-top: 10px;">
                            <span>Transferência</span>
                        </button>

                        <button type="button" class="btn btn-outline-primary legitRipple" style="width: 100%; margin-top: 10px;">
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
