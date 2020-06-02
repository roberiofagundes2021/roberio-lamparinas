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

        $args[]  = "MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."' ";
    }

    if(!empty($_POST['inputLocalEstoque'])){
        $args[]  = "MovimDestinoLocal = ".$_POST['inputLocalEstoque']." ";
    }

    if(!empty($_POST['inputSetor'])){
        $args[]  = "MovimDestinoSetor = ".$_POST['inputSetor']." ";
    }

    if(!empty($_POST['inputCategoria'])){
        $args[]  = "ProduCategoria = ".$_POST['inputCategoria']." ";
    }

    if(!empty($_POST['inputSubCategoria'])){
        $args[]  = "ProduSubCategoria = ".$_POST['inputSubCategoria']." ";
    }

    if(!empty($_POST['inputProduto'])){
        $args[]  = "ProduNome LIKE '%".$_POST['inputProduto']."%' ";
    }

    if (count($args) >= 1) {
        try {

            $string = implode( " and ",$args );

            if ($string != ''){
                $string .= ' and ';
            }

            $sql = "SELECT PatriNumero, MvXPrId, MovimId ,MovimData, MovimNotaFiscal, MovimOrigemLocal, LcEstNome, MovimDestinoSetor, MvXPrValidade, MvXPrValorUnitario, MvXPrValidade, ProduNome, MarcaNome, FabriNome, SetorNome
                    FROM Patrimonio
                    JOIN MovimentacaoXProduto on MvXPrPatrimonio = PatriId
                    JOIN Movimentacao on MovimId = MvXPrMovimentacao
                    JOIN Produto on ProduId = MvXPrProduto
                    LEFT JOIN Marca on MarcaId = ProduMarca
                    LEFT JOIN Fabricante on FabriId = ProduFabricante 
                    LEFT JOIN LocalEstoque on LcEstId = MovimDestinoLocal
                    LEFT JOIN Setor on SetorId = MovimDestinoSetor
                    WHERE ".$string." ProduUnidade = ".$_SESSION['UnidadeId']."
                    ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if ($cont == 1) {
        $cont = 0;
        foreach ($rowData as $item) {
            $cont++;
            print("
                
                <tr>
                   <td class='even'>" . $cont . "</td>
                   <td class='odd'>" . $item['ProduNome'] . "</td>
                   <td  class='even'>".$item['PatriNumero']."</td>
                   <td class='odd'>" . $item['MovimNotaFiscal'] . "</td>
                   <td class='even'></td>
                   <td class='odd'>" . $item['MovimNotaFiscal'] . "</td>
                   <td class='even'>".mostraData($item['MvXPrValidade'])."</td>
                   <td class='odd'>" . $item['LcEstNome'] . "</td>
                   <td class='even'>" . $item['SetorNome'] . "</td>
                   <td class='even' style='display: none'>" . $item['MarcaNome'] . "</td>
                   <td class='even' style='display: none'>" . $item['FabriNome'] . "</td>
                   <td style='text-align: center'>
                         <i idinput='campo3' idrow='row3' class='icon-pencil7 btn-acoes' style='cursor: pointer'></i>
                   </td>
                </tr>
             ");
        }
        
    }
}

queryPesquisa();
