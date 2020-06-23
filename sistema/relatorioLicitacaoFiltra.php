<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa(){

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = []; 

    if (!empty($_POST['inputDataDe']) || !empty($_POST['inputDataAte'])) {
        empty($_POST['inputDataDe']) ? $inputDataDe = '1900-01-01' : $inputDataDe = $_POST['inputDataDe'];
        empty($_POST['inputDataAte']) ? $inputDataAte = '2100-01-01' : $inputDataAte = $_POST['inputDataAte'];

        $args[]  = "FlOpeDataInicio > '".$inputDataDe."' and FlOpeDataFim < '".$inputDataAte."' ";
    }

    if(!empty($_POST['cmbUnidade']) && $_POST['cmbUnidade'] != ""){
        $args[]  = "FlOpeUnidade = ".$_POST['cmbUnidade']." ";
    }

    if(!empty($_POST['cmbEmpresaContratada']) && $_POST['cmbEmpresaContratada'] != ""){
        $args[]  = "FlOpeFornecedor = ".$_POST['cmbEmpresaContratada']." ";
    }

    if(!empty($_POST['cmbCategoria']) && $_POST['cmbCategoria'] != ""){
        $args[]  = "FlOpeCategoria = ".$_POST['cmbCategoria']." ";
    }

    // if(!empty($_POST['cmbClassificacao'])){
    //     $args[]  = "FlOpe = ".$_POST['inputSubCategoria']." ";
    // }

    if(!empty($_POST['cmbModalidade']) && $_POST['cmbModalidade'] != ""){
        $args[]  = "FlOpeModalidadeLicitacao = ".$_POST['cmbModalidade']." ";
    }

    if(!empty($_POST['cmbPrioridade']) && $_POST['cmbPrioridade'] != ""){
        $args[]  = "FlOpePrioridade = ".$_POST['cmbPrioridade']." ";
    }

    if(!empty($_POST['cmbStatus']) && $_POST['cmbStatus'] != ""){
        $args[]  = "FlOpeStatus = ".$_POST['cmbStatus']." ";
    }

    if (count($args) >= 1) {
        try {

            $string = implode( " and ",$args );

            // if ($string != ''){
            //     $string .= ' and ';
            // }
      
            $sql = "SELECT FlOpeId,  FlOpeDataInicio,  FlOpeDataFim,  FlOpeObservacao, FlOpePrioridade, UnidaNome, ForneNome, CategNome, MdLicNome, PriorNome, SituaNome
                    FROM FluxoOperacional
                    LEFT JOIN Unidade on UnidaId = FlOpeUnidade
                    LEFT JOIN Fornecedor on ForneId = FlOpeId
                    LEFT JOIN Categoria  on CategId = FlOpeCategoria
                    LEFT JOIN ModalidadeLicitacao on MdLicId = FlOpeModalidadeLicitacao
                    LEFT JOIN Prioridade on PriorId = FlOpePrioridade 
                    JOIN Situacao on SituaId = FlOpeStatus
                    WHERE ".$string."
                    ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;

            print($sql);

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    if ($cont == 1) {
        $cont = 0;
        foreach ($rowData as $item) {
            $cont++;
            print("
                
                <tr idPatrimonio=".$item['FlOpeId']." editado='0'>
                   <td class='even'>" . $cont . "</td>
                   <td class='odd'>" . $item['CategNome'] . "</td>
                   <td  class='even'></td>
                   <td class='odd'>" . $item['ForneNome'] . "</td>
                   <td class='even'>".$item['UnidaNome']."</td>
                   <td class='odd'>".$item['SituaNome']."</td>
                   <td class='even'>".$item['MdLicNome']."</td>
                   <td class='odd'>".$item['FlOpeDataInicio']."</td>
                   <td class='even'>".$item['FlOpeDataFim']."</td>
                   <td class='odd'>".$item['FlOpePrioridade']."</td>
                   <td class='even'>".$item['FlOpeObservacao']."</td>
                   <td  class='odd' style='text-align: center'>
                         <i idinput='campo3' idrow='row3' class='icon-pencil7 btn-acoes' style='cursor: pointer'></i>
                   </td>
                   
                </tr>
             ");
        }
        
    }
}

queryPesquisa();
