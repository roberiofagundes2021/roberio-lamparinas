<?php

include_once("sessao.php");

//Fazer alteração posteriormente
$_SESSION['PaginaAtual'] = 'Caixa PDV';

include('global_assets/php/conexao.php');

if(isset($_POST['inputAbrirCaixa'])) {
    $dataHorAtual = date('Y-m-d H:i:s');
    $saldoInicial = str_replace(',', '.', $_POST['inputSaldoInicial']);

    $sql = "INSERT INTO CaixaAbertura (CxAbeCaixa, CxAbeDataHoraAbertura, CxAbeOperador, 
                        CxAbeSaldoInicial, CxAbeStatus, CxAbeUnidade) 
            VALUES ( :iCaixa, :sDataHoraAbertura, :iOperador, :bSaldoInicial, :iStatus, :iUnidade)";
    $result = $conn->prepare($sql);

    $result->execute(array(
                    ':iCaixa' => $_POST['cmbCaixa'],
                    ':sDataHoraAbertura' => $dataHorAtual,
                    ':iOperador' => $_SESSION['UsuarId'],
                    ':bSaldoInicial' => $saldoInicial,
                    ':iStatus' => 1,
                    ':iUnidade' => $_SESSION['UnidadeId']
                    )); //Depois se informar a respeito do status

    $nomeCaixa = $_POST['inputCaixaNome'];
}

if(isset($_POST['inputAberturaCaixaId'])) {
    //alerta($_POST['inputAberturaCaixaId']);
    //alerta($_POST['inputAberturaCaixaNome']);

    $nomeCaixa = $_POST['inputAberturaCaixaNome'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lamparinas | Movimentação do Caixa</title>

    <?php include_once("head.php"); ?>

    <!-- Theme JS files -->
    <script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="global_assets/js/plugins/tables/datatables/extensions/responsive.min.js"></script>

    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/demo_pages/form_layouts.js"></script>
    <script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
    <script src="global_assets/js/demo_pages/datatables_sorting.js"></script>
   
    <!-- /theme JS files -->

    <!-- Plugin para corrigir a ordenação por data. Caso a URL dê problema algum dia, salvei esses 2 arquivos na pasta global_assets/js/lamparinas -->
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tblAtendimento').DataTable( {
                "order": [[ 0, "asc" ]],
                autoWidth: false,
                responsive: true,
                columnDefs: [
                {
                    orderable: true,   //Marca
                    width: "40%",
                    targets: [0]
                },
                { 
                    orderable: true,   //Situação
                    width: "40%",
                    targets: [1]
                },
                { 
                    orderable: true,   //Ações
                    width: "20%",
                    targets: [2]
                }],
                dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filtro:</span> _INPUT_',
                    searchPlaceholder: 'filtra qualquer coluna...',
                    lengthMenu: '<span>Mostrar:</span> _MENU_',
                    paginate: { 'first': 'Primeira', 'last': 'Última', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
                }
            });
            
            // Select2 for length menu styling
            var _componentSelect2 = function() {
                if (!$().select2) {
                    console.warn('Warning - select2.min.js is not loaded.');
                    return;
                }

                // Initialize
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity,
                    dropdownAutoWidth: true,
                    width: 'auto'
                });
            };	

            _componentSelect2();
            
            /* Fim: Tabela Personalizada */

            $('#cmbAtendimento').on("change", function() {
                let urlConsultaAberturaCaixa = "consultaCaixaServicos.php";
                let idAtendimento = ($(this).val() != '') ? $(this).val() : 0;

                let inputsValuesConsulta = {
                    inputAtendimentoId: idAtendimento
                }; 

                //Verifica se deverá ou não abrir o caixa
                $.ajax({
                    type: "POST",
                    url: urlConsultaAberturaCaixa,
                    dataType: "json",
                    data: inputsValuesConsulta,
                    success: function(resposta) {
                        //|--Aqui é criado o DataTable caso seja a primeira vez q é executado e o clear é para evitar duplicação na tabela depois da primeira pesquisa
                        let table 
                        table = $('#tblAtendimento').DataTable()
                        table = $('#tblAtendimento').DataTable().clear().draw()
                        //--|

                        table = $('#tblAtendimento').DataTable()

                        let rowNode

                        let valorTotal = null;

                        resposta.forEach(item => {
                            valorTotal += Number(item.data[2])
                            
                            rowNode = table.row.add(item.data).draw().node()

                            $(rowNode).find('td').eq(2).attr('style', 'text-align: right;')
                        })

                        valorTotal = (valorTotal != null) ? float2moeda(valorTotal) : null;
                        $("#inputDescricao").val(valorTotal);
                    }
                })
            })
        });

        //Essa função foi criada para não usar $_GET e ficar mostrando os ids via URL
        function atualizaContasAPagar(Permission, ContasAPagarId, Tipo) {

            document.getElementById('inputContasAPagarId').value = ContasAPagarId;
            document.getElementById('inputPermissionAtualiza').value = Permission;

            if (Tipo == 'novo' || Tipo == 'edita') {
                document.formContasAPagar.action = "contasAPagarNovoLancamento.php";
            } else if (Tipo == 'exclui') {
                if(Permission){
                    confirmaExclusao(document.formContasAPagar, "Tem certeza que deseja excluir essa Conta?", "contasAPagarExclui.php");
                } else{
                    alerta('Permissão Negada!','');
                    return false;
                }
            }else if (Tipo == 'estornar') {
                return false
            }else if (Tipo.slice(0,17) == 'consultaPagamento') {
                consultaGrupoPagamento(Tipo)
                return false
            }        

            document.formContasAPagar.submit();
        } 
    </script>

</head>

<body class="navbar-top sidebar-right-visible sidebar-xs">

    <?php include_once("topo.php"); ?>

    <!-- Page content -->
    <div class="page-content">

        <?php include_once("menu-left.php"); ?>

        <!-- Main content -->
        <div class="content-wrapper">

            <?php include_once("cabecalho.php"); ?>

            <!-- Content area -->
            <div class="content">

                <!-- Info blocks -->
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Basic responsive configuration -->
                        <div class="card">
                            <div class="card-header header-elements">
                                <div class="row">
                                    <div class="col-4">
                                        <h4 class="m-auto">PDV: 005 - Mudar dps</h4>
                                    </div>

                                    <div class="col-4">
                                        <h4 class="m-auto">Caixa: <?php echo $nomeCaixa; ?></h4>
                                    </div>

                                    <div class="col-4 text-right">
                                        <h4 class="m-auto">Data: <?php echo date('d/m/Y'); ?></h4>
                                    </div>
                                </div>

                                <hr>

                                <h3 class="card-title">PDV - <?php echo  $_SESSION['UnidadeNome']; ?></h3>
                            </div>

                            <div class="card-body">
                                <?php
                                if (isset($lancamento)) {
                                    echo '<input type="hidden" name="inputEditar" value="sim">';
                                    echo '<input type="hidden" name="inputContaId" value="' . $lancamento['CnAPaId'] . '">';
                                }

                                ?>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="cmbAtendimento">Atendimento</label>
                                            <select id="cmbAtendimento" name="cmbAtendimento" class="form-control form-control-select2">
                                                <option value="">Todos</option>
                                                <?php
                                                $sql = "SELECT AtendId, ClienNome
                                                        FROM Atendimento
                                                        JOIN Cliente on ClienId = AtendCliente
                                                        JOIN Situacao on SituaId = AtendSituacao
                                                        WHERE AtendUnidade = ".$_SESSION['UnidadeId']."
                                                        ORDER BY ClienNome";
                                                $result = $conn->query($sql);
                                                $rowFornecedor = $result->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($rowFornecedor as $item) {
                                                    print('<option value="' . $item['AtendId'] . '">' . $item['ClienNome'] . '</option>');
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="inputDescricao">Valor Total</label>
                                                <input type="text" id="inputDescricao" class="form-control text-right" name="inputDescricao" value="" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group text-right">
                                            <label for="inputDescricao">Desconto</label>
                                            <input type="text" id="inputDescricao" class="form-control text-right" name="inputDescricao" value="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div>
                                            <div class="form-group text-right">
                                                <label for="inputDescricao">Valor Final</label>
                                                <input type="text" id="inputDescricao" class="form-control text-right" name="inputDescricao" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                 </div>

                                <div class="row">
                                    <div class="col-12">
                                        <table id="tblAtendimento" class="table">
                                            <thead>
                                                <tr class="bg-slate">
                                                    <th>Procedimento</th>
                                                    <th>Médico</th>
                                                    <th class="text-center">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
									<div class="col-lg-12">
										<div class="text-right">
											<div>
                                                <a href="javascript:history.go(-1)" class="btn legitRipple">Fechar</a>
                                                <button id="salvar" class="btn btn-principal legitRipple">Finalizar</button>
											</div>
										</div>
									</div>	
								</div>
                            </div>

                        </div>
                        <!-- /basic responsive configuration -->

                    </div>
                </div>
                
                <form name="formContasAPagar" method="post">
					<input type="hidden" id="inputPermissionAtualiza" name="inputPermissionAtualiza" value="<?php echo $atualizar; ?>" >
                    <input type="hidden" id="inputPermissionExclui" name="inputPermissionExclui" value="<?php echo $excluir; ?>" >
					<input type="hidden" id="inputContasAPagarId" name="inputContasAPagarId" >
                    <input type="hidden" id="inputContaJustificativa" name="inputContaJustificativa" >
				</form>

            </div>
            <!-- /content area -->

            <!-- Small modal -->
            <!--Procurar uma correção com relação ao filtro do select-->
            <div id="modal_small_abertura_caixa" class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Abertura de Caixa</h5>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    Data: <?php echo date('d/m/Y'); ?>
                                </div>

                                <div class="col-lg-6">
                                    Operador: Teste
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="cmbPlanoContas">Plano de Contas <span class="text-danger">*</span></label>
                                <select id="cmbPlanoContas" name="cmbPlanoContas" class="form-control form-control-select2 select2-hidden-accessible" required="" tabindex="-1" aria-hidden="true">
                                    <option value="">Selecionar</option>
                                    <?php
                                    $sql = "SELECT CaixaId, CaixaNome, SituaNome
                                            FROM Caixa
                                            JOIN Situacao on SituaId = CaixaStatus
                                            WHERE CaixaUnidade = " . $_SESSION['UnidadeId'] . "
                                            ORDER BY CaixaNome ASC";
                                    $result = $conn->query($sql);
                                    $rowPlanoContas = $result->fetchAll(PDO::FETCH_ASSOC);


                                    foreach ($rowPlanoContas as $item) {
                                        print('<option value="' . $item['CaixaId'] . '">'. $item['CaixaNome'] . '</option>');
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mt-2">
                                <label for="inputValor">Valor</label>
                                <input type="text" id="inputValor" name="inputValor" value="saldo aleatório" class="form-control removeValidacao">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-basic legitRipple" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn bg-slate legitRipple">Abrir Caixa</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /small modal -->

            <?php include_once("footer.php"); ?>

        </div>
        <!-- /main content -->

        <?php include_once("sidebar-right-resumo-caixa.php"); ?>

    </div>
    <!-- /page content -->

    <?php include_once("alerta.php"); ?>

</body>

</html>