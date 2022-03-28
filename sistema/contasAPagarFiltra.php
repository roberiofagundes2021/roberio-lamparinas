<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

//function queryPesquisa()
//{
//    include('global_assets/php/conexao.php');

    $cont = 0;

    $args = [];

    //Aqui é para limpar a sessão caso o usuário filtre todos novamente
    $_SESSION['ContPagPeriodoDe'] = '';
    $_SESSION['ContPagAte'] = '';
    $_SESSION['ContPagFornecedor'] = '';
    $_SESSION['ContPagPlanoContas'] = '';
    $_SESSION['ContPagStatus'] = '';

    if (!empty($_POST['inputPeriodoDe']) || !empty($_POST['inputAte'])) {
        empty($_POST['inputPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_POST['inputPeriodoDe'];
        empty($_POST['inputAte']) ? $inputAte = '2100-01-01' : $inputAte = $_POST['inputAte'];

        if($_POST['statusTipo'] == 'APAGAR'){
            $args[]  = "CnAPaDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
        } else {
            $args[]  = "CnAPaDtPagamento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";                
        }

        if(!empty($_POST['inputPeriodoDe'])){
            $_SESSION['ContPagPeriodoDe'] = $_POST['inputPeriodoDe'];
        }

        if(!empty($_POST['inputAte'])){
            $_SESSION['ContPagAte'] = $_POST['inputAte'];
        }
    }

    if (!empty($_POST['cmbFornecedor'])) {
        $args[]  = "CnAPaFornecedor = " . $_POST['cmbFornecedor'] . " ";
        $_SESSION['ContPagFornecedor'] = $_POST['cmbFornecedor'];
    }

    if (!empty($_POST['cmbPlanoContas'])) {
        $args[]  = "CnAPaPlanoContas = " . $_POST['cmbPlanoContas'] . " ";
        $_SESSION['ContPagPlanoContas'] = $_POST['cmbPlanoContas'];
    }

    if (!empty($_POST['cmbStatus'])) {
        $args[]  = "CnApaStatus = " . $_POST['cmbStatus'] . " ";
        $_SESSION['ContPagStatus'] = $_POST['cmbStatus'];
    }

    if (count($args) >= 1) {

        $string = implode(" and ", $args);

        if ($string != '') {
            $string .= ' and ';
        }

        $sql = "SELECT * 
                FROM ContasAPagar
                LEFT JOIN Fornecedor on ForneId = CnAPaFornecedor
                JOIN Situacao on SituaId = CnApaStatus
                WHERE " . $string . " CnAPaUnidade = " . $_SESSION['UnidadeId'] . "
            ";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }

    if ($cont == 1) {
        $cont = 0;
        //print('<input type="hidden" id="elementosGrid" value="'.count($rowData).'">');

        $arrayData = [];
        foreach ($rowData as $item) {
            $cont++;     
            $status = $item['CnAPaStatus'] == 11 ? 'À Pagar' : 'Pago';
            $data = $_POST['statusTipo'] == 'APAGAR' ? mostraData($item['CnAPaDtVencimento']) : mostraData($item['CnAPaDtPagamento']);
            
            /*

            print('
            
            <tr>
                <td class="even">
                    <input type="checkbox" id="check'.$cont.'">
                    <input type="hidden" value="'.$item["CnAPaId"].'">
                </td>
                <td class="even"><p class="m-0">' . $data . '</p><input type="hidden" value="'.$item["CnAPaDtVencimento"].'"></td>
                <td class="even"><a href="#" onclick="atualizaContasAPagar('.$_POST['permissionAtualiza'].','.$item["CnAPaId"].', \'edita\')">' . $item["CnAPaDescricao"] . '</a></td>
                <td class="even">' . $item["ForneNome"] . '</td>
                <td class="even" style="text-align: center">' . $item["CnAPaNumDocumento"] . '</td>
                <td class="even" style="text-align: right; padding-right:1.5rem;">' . mostraValor($item["CnAPaValorAPagar"]) . '</td>
                <td class="even" style="text-align: center">' .$status. '</td>
                <td class="even d-flex flex-row justify-content-around align-content-center" style="text-align: center">
                <div class="list-icons">
                    <div class="list-icons list-icons-extended">
                        <a href="#" onclick="atualizaContasAPagar('.$_POST['permissionAtualiza'].','.$item["CnAPaId"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>
                        <a href="#" onclick="atualizaContasAPagar('.$_POST['permissionExclui'].','.$item["CnAPaId"].', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>
				        <div class="dropdown">													
				        	<a href="#" class="list-icons-item" data-toggle="dropdown">
				        		<i class="icon-menu9"></i>
				    
				        	<div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item btnParcelar"  data-popup="tooltip" data-placement="bottom" title="Parcelar"><i class="icon-file-text2"></i> Parcelar</a>
				        	</div>
				        </div>
				    </div>
                   
                    </div>
                </td>
            </tr>
            ');
            */

            $visibilidade = ($status == 'Pago') ? 'none' : 'block';

            $estornamento =  (!isset($item['CnAPaJustificativaEstorno'])) ? 'none' : 'block';
            $justificativaEstornamento = (isset($item['CnAPaJustificativaEstorno'])) ? $item['CnAPaJustificativaEstorno'] : '';

            $checkbox = '<input type="checkbox" id="check'.$cont.'" style="display: '.$visibilidade.';"> <input type="hidden" value="'.$item["CnAPaId"].'">';
            
            $vencimento = '<p class="m-0">' . $data . '</p><input type="hidden" value="'.$item["CnAPaDtVencimento"].'">';

            $descricao = '<a href="#" onclick="atualizaContasAPagar('.$_POST['permissionAtualiza'].','.$item["CnAPaId"].', \'edita\')">' . $item["CnAPaDescricao"] . '</a>';
            
            $favorecido = $item["ForneNome"];

            $numDoc = $item["CnAPaNumDocumento"];

            $valorTotal = mostraValor($item["CnAPaValorAPagar"]);

            $status = $status;

            $acaoConta = ($status == 'Pago') ? '<a href="#" data-toggle="modal" data-target="#modal_mini-estornar" onclick="atualizaContasAPagar('.$_POST['permissionAtualiza'].','.$item["CnAPaId"].', \'estornar\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Estornar Conta"><i class="icon-undo2"></i></a>' : '<a href="#" onclick="atualizaContasAPagar('.$_POST['permissionExclui'].','.$item["CnAPaId"].', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin"></i></a>';

            $acoes = '
                <div class="list-icons">
                    <div class="list-icons list-icons-extended">
                        <a href="#" onclick="atualizaContasAPagar('.$_POST['permissionAtualiza'].','.$item["CnAPaId"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>
                        '.$acaoConta.'
                        <a href="#" class="list-icons-item" data-toggle="modal" data-target="#modal_mini-justificativa-estorno" onclick="estornoJustificativa(\''.$justificativaEstornamento.'\');"  data-popup="tooltip" data-placement="bottom"title="Motivo do estorno" style="display: '.$estornamento.';"><i class="icon-info3"></i></a>
                        <!-- Retirado ícone de parcelar
                        <div class="dropdown" style="display: '.$visibilidade.';">													
                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                <i class="icon-menu9"></i>
                    
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item btnParcelar"  data-popup="tooltip" data-placement="bottom" title="Parcelar"><i class="icon-file-text2"></i> Parcelar</a>
                            </div>
                        </div>-->
                    </div>
                </div>';

            $array = [
                'data'=>[
                    isset($checkbox) ? $checkbox : null, 
                    isset($vencimento) ? $vencimento : null,
                    isset($descricao) ? $descricao : null, 
                    isset($favorecido) ? $favorecido : null, 
                    isset($numDoc) ? $numDoc : null, 
                    isset($valorTotal) ? $valorTotal : null, 
                    isset($status) ? $status : null, 
                    isset($acoes) ? $acoes : null
                ],
                'identify'=>[
                    
                ]
            ];

            array_push($arrayData,$array);
        }

        print(json_encode($arrayData));
    }
//}

//queryPesquisa();