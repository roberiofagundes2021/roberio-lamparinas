<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

//function queryPesquisa()
//{
//   include('global_assets/php/conexao.php');

    if ($_POST['tipoFiltro'] == 'FiltroNormal') {

        $cont = 0;

        $args = [];

        if (!empty($_POST['inputPeriodoDe']) || !empty($_POST['inputAte'])) {
            empty($_POST['inputPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_POST['inputPeriodoDe'];
            empty($_POST['inputAte']) ? $inputAte = '2100-01-01' : $inputAte = $_POST['inputAte'];

            if ($_POST['statusTipo'] == 'ARECEBER') {
                $args[]  = "CnAReDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
            } else {
                $args[]  = "CnAReDtRecebimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
            }

            if (!empty($_POST['inputPeriodoDe'])) {
                $_SESSION['ContRecPeriodoDe'] = $_POST['inputPeriodoDe'];
            }

            if (!empty($_POST['inputAte'])) {
                $_SESSION['ContRecAte'] = $_POST['inputAte'];
            }
        }

        if (!empty($_POST['cmbClientes'])) {
            $args[]  = "CnAReCliente = " . $_POST['cmbClientes'] . " ";
            $_SESSION['ContRecCliente'] = $_POST['cmbClientes'];
        }

        if (!empty($_POST['cmbPlanoContas'])) {
            $args[]  = "CnARePlanoContas = " . $_POST['cmbPlanoContas'] . " ";
            $_SESSION['ContRecPlanoContas'] = $_POST['cmbPlanoContas'];
        }

        if (!empty($_POST['cmbStatus'])) {
            $args[]  = "CnAReStatus = " . $_POST['cmbStatus'] . " ";
            $_SESSION['ContRecStatus'] = $_POST['cmbStatus'];
        }

        if (!empty($_POST['cmbNumDoc'])) {
            $args[]  = "CnAReNumDocumento = " . $_POST['cmbNumDoc'] . " ";
            $_SESSION['ContRecNumDoc'] = $_POST['cmbNumDoc'];
        }

        if (!empty($_POST['cmbFormaDeRecebimento'])) {
            $args[]  = "CnAReFormaPagamento = " . $_POST['cmbFormaDeRecebimento'] . " ";
            $_SESSION['ContRecFormaPagamento'] = $_POST['cmbFormaDeRecebimento'];
        }

        if (count($args) >= 1) {

            $string = implode(" and ", $args);

            if ($string != '') {
                $string .= ' and ';
            }

            $sql = "SELECT * 
                    FROM ContasAReceber
                    LEFT JOIN Cliente on ClienId = CnAReCliente
                    JOIN Situacao on SituaId = CnAReStatus
                    WHERE " . $string . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
    } else if (isset($_SESSION['ContRecPeriodoDe']) ||  isset($_SESSION['ContRecAte']) || isset($_SESSION['ContRecCliente']) || isset($_SESSION['ContRecPlanoContas']) || isset($_SESSION['ContRecStatus'])) {

        $cont = 0;
        $args = [];

        if (!empty($_SESSION['ContRecPeriodoDe']) || !empty($_SESSION['ContRecAte'])) {
            empty($_SESSION['ContRecPeriodoDe']) ? $inputPeriodoDe = '1900-01-01' : $inputPeriodoDe = $_SESSION['ContRecPeriodoDe'];
            empty($_SESSION['ContRecAte']) ? $inputAte = '2100-01-01' : $inputAte = $_SESSION['ContRecAte'];

            $args[]  = "CnAReDtVencimento BETWEEN '" . $inputPeriodoDe . "' and '" . $inputAte . "' ";
        }

        if (!empty($_SESSION['ContRecCliente'])) {
            $args[]  = "CnAReCliente = " . $_SESSION['ContRecCliente'] . " ";
        }

        if (!empty($_SESSION['ContRecPlanoContas'])) {
            $args[]  = "CnARePlanoContas = " . $_SESSION['ContRecPlanoContas'] . " ";
        }

        if (!empty($_SESSION['ContRecStatus'])) {
            $args[]  = "CnAReStatus = " . $_SESSION['ContRecStatus'] . " ";
        }

        if (count($args) >= 1) {

            $string = implode(" and ", $args);

            if ($string != '') {
                $string .= ' and ';
            }

            $sql = "SELECT * 
                    FROM ContasAReceber
                    LEFT JOIN Cliente on ClienId = CnAReCliente
                    JOIN Situacao on SituaId = CnAReStatus
                    WHERE " . $string . " CnAReUnidade = " . $_SESSION['UnidadeId'] . "
                ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        }
    } else {
        $dataInicio = date("Y-m-d");
        $dataFim = date("Y-m-d");

        $sql = "SELECT * 
                FROM ContasAReceber
                LEFT JOIN Cliente on ClienId = CnAReCliente
                JOIN Situacao on SituaId = CnAReStatus
                WHERE CnAReUnidade = " . $_SESSION['UnidadeId'] . " and CnAReDtVencimento BETWEEN '" . $dataInicio . "' and '" . $dataFim . "' and SituaChave = 'ARECEBER'
        ";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);
        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }

    if ($cont == 1) {
        $cont = 0;
        //print('<input type="hidden" id="elementosGrid" value="' . count($rowData) . '">');

        $arrayData = [];
        foreach ($rowData as $item) {
            $cont++;

            $status = $item['CnAReStatus'] == 13 ? 'À Receber' : 'Recebida';
            $data = $_POST['statusTipo'] == 'ARECEBER' ? mostraData($item['CnAReDtVencimento']) : mostraData($item['CnAReDtRecebimento']);
            /*
            print('
            
            <tr>
                <td class="even">
                    <input type="checkbox" id="check' . $cont . '">
                    <input type="hidden" value="' . $item["CnAReId"] . '">
                </td>
                <td class="even"><p class="m-0">' . $data . '</p><input type="hidden" value="' . $item["CnAReDtVencimento"] . '"></td>
                <td class="even"><a href=#" onclick="atualizaContasAReceber('.$_POST['permissionAtualiza'].','.$item["CnAReId"].', \'edita\')">' . $item["CnAReDescricao"] . '</a></td>
                <td class="even">' . $item["ClienNome"] . '</td>
                <td class="even" style="text-align: center">' . $item["CnAReNumDocumento"] . '</td>
                <td class="even" style="text-align: right; padding-right:1.5rem;">' . mostraValor($item["CnAReValorAReceber"]) . '</td>
                <td class="even" style="text-align: center">' . $status . '</td>
                <td class="even d-flex flex-row justify-content-around align-content-center" style="text-align: center">
                <div class="list-icons">
                    <div class="list-icons list-icons-extended">
                    <a href="#" onclick="atualizaContasAReceber('.$_POST['permissionAtualiza'].','.$item["CnAReId"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>
                    <a href="#" onclick="atualizaContasAReceber('.$_POST['permissionExclui'].','.$item["CnAReId"].', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>
				        <div class="dropdown">													
				        	<a href="#" class="list-icons-item" data-toggle="dropdown">
				        		<i class="icon-menu9"></i>
				    
				        	<div class="dropdown-menu dropdown-menu-right">
                                <a href="#" class="dropdown-item btnParcelar"  data-popup="tooltip" data-placement="bottom" title="Parcelar"><i class="icon-file-text2"></i> Parcelar</a>
                                <a href="#" class="dropdown-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Produto"><i class="icon-file-empty"></i></a>
				        	</div>
				        </div>
				    </div>
                   
                    </div>
                </td>
            </tr>
            ');
            */

            $checkbox = '<input type="checkbox" id="check'.$cont.'"> <input type="hidden" value="'.$item["CnAReId"].'">';
            
            $vencimento = '<p class="m-0">' . $data . '</p><input type="hidden" value="'.$item["CnAReDtVencimento"].'">';

            $descricao = '<a href=#" onclick="atualizaContasAReceber('.$_POST['permissionAtualiza'].','.$item["CnAReId"].', \'edita\')">' . $item["CnAReDescricao"] . '</a>';
            
            $cliente = $item["ClienNome"];

            $numDoc = $item["CnAReNumDocumento"];

            $valorTotal = mostraValor($item["CnAReValorAReceber"]);

            $status = $status;

            $acoes = '
                    <div class="list-icons list-icons-extended">
                        <a href="#" onclick="atualizaContasAReceber('.$_POST['permissionAtualiza'].','.$item["CnAReId"].', \'edita\');" class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Editar Conta"><i class="icon-pencil7"></i></a>
                        <a href="#" onclick="atualizaContasAReceber('.$_POST['permissionExclui'].','.$item["CnAReId"].', \'exclui\');"  class="list-icons-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Conta"><i class="icon-bin" title="'.$_POST['permissionExclui'].'"></i></a>
                            <div class="dropdown">													
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                        
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item btnParcelar"  data-popup="tooltip" data-placement="bottom" title="Parcelar"><i class="icon-file-text2"></i> Parcelar</a>
                                    <a href="#" class="dropdown-item"  data-popup="tooltip" data-placement="bottom" title="Excluir Produto"><i class="icon-file-empty"></i></a>
                                </div>
                            </div>
                        </div>
                
                    </div>';

            $array = [
                'data'=>[
                    isset($checkbox) ? $checkbox : null, 
                    isset($vencimento) ? $vencimento : null,
                    isset($descricao) ? $descricao : null, 
                    isset($cliente) ? $cliente : null, 
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