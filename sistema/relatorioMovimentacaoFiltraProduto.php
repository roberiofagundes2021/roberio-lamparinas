<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];

    if (!empty($_POST['inputDataDe']) || !empty($_POST['inputDataAte'])) {
        empty($_POST['inputDataDe']) ? $inputDataDe = '1900-01-01' : $inputDataDe = $_POST['inputDataDe'];
        empty($_POST['inputDataAte']) ? $inputDataAte = '2100-01-01' : $inputDataAte = $_POST['inputDataAte'];

        $args[]  = "MovimData BETWEEN '" . $inputDataDe . "' and '" . $inputDataAte . "' ";
    }

    if (!empty($_POST['cmbTipo'])) {
        $args[]  = "MovimTipo = '" . $_POST['cmbTipo'] . "' ";
    }

    if (!empty($_POST['cmbFornecedor'])) {
        $args[]  = "MovimFornecedor = " . $_POST['cmbFornecedor'] . " ";
    }

    if (!empty($_POST['cmbCategoria']) && $_POST['cmbCategoria'] != 'Sem Categoria' && $_POST['cmbCategoria'] != "Filtrando...") {
        $args[]  = "ProduCategoria = " . $_POST['cmbCategoria'] . " ";
    }

    if (!empty($_POST['cmbSubCategoria']) && $_POST['cmbSubCategoria'] != "Sem Subcategoria" && $_POST['cmbSubCategoria'] != "Filtrando...") {
        $args[]  = "ProduSubCategoria = " . $_POST['cmbSubCategoria'] . " ";
    }

    if (!empty($_POST['cmbCodigo'])) {
        $args[]  = "ProduCodigo = " . $_POST['cmbCodigo'] . " ";
    }

    if (!empty($_POST['cmbProduto']) && $_POST['cmbProduto'] != "Sem produto" && $_POST['cmbProduto'] != "Filtrando..."  && $_POST['cmbProduto'] != "#") {
        $args[]  = "ProduId = " . $_POST['cmbProduto'] . " ";
    }

    if(!empty($_POST['cmbOrigem'])){

        $aOrigem = explode("#", $_POST['cmbOrigem']);

        if ($aOrigem[2] == 'Local'){
            $args[]  = "MovimOrigemLocal = ".$aOrigem[0]." ";
        } else {
            $args[]  = "MovimOrigemSetor = ".$aOrigem[0]." ";
        }
    }

    if(!empty($_POST['cmbDestino'])){

        $aDestino = explode("#", $_POST['cmbDestino']);

        if ($aDestino[2] == 'Local'){
            $args[]  = "MovimDestinoLocal = ".$aDestino[0]." ";
        } else if ($aDestino[2] == 'Setor') {
            $args[]  = "MovimDestinoSetor = ".$aDestino[0]." ";
        } else {
            $args[]  = "MovimDestinoManual = '".$aDestino[1]."' ";
        }
    }

    if (!empty($_POST['cmbClassificacao'])) {
        $args[]  = "MvXPrClassificacao = " . $_POST['cmbClassificacao'] . " ";
    }

    if (count($args) >= 1) {

        $string = implode(" and ", $args);

        if ($string != '') {
            $string .= ' and ';
        }

        $sql = "SELECT MovimData, MovimTipo, 
                CASE 
                    WHEN MovimOrigemLocal IS NULL THEN SetorO.SetorNome
                ELSE LocalO.LcEstNome 
                END as Origem,
                CASE 
                    WHEN MovimDestinoLocal IS NULL THEN ISNULL(SetorD.SetorNome, MovimDestinoManual)
                ELSE LocalD.LcEstNome
                END as Destino, 
                MvXPrQuantidade, ProduNome, CategNome, ForneNome
            FROM Movimentacao   
            JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
            JOIN Produto on ProduId = MvXPrProduto
            JOIN Categoria on CategId = ProduCategoria
            JOIN Situacao on SituaId = MovimSituacao
            LEFT JOIN LocalEstoque LocalO on LocalO.LcEstId = MovimOrigemLocal 
            LEFT JOIN LocalEstoque LocalD on LocalD.LcEstId = MovimDestinoLocal 
            LEFT JOIN Setor SetorO on SetorO.SetorId = MovimOrigemSetor 
            LEFT JOIN Setor SetorD on SetorD.SetorId = MovimDestinoSetor 
            LEFT JOIN Fornecedor on ForneId = MovimFornecedor
            LEFT JOIN Classificacao on ClassId = MvXPrClassificacao
            WHERE " . $string . " MovimUnidade = " . $_SESSION['UnidadeId'] . " and 
            SituaChave in ('LIBERADO', 'LIBERADOCENTRO', 'AGUARDANDOLIBERACAOCONTABILIDADE', 'LIBERADOCONTABILIDADE')
            ";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }

    if ($cont == 1) {
        $cont = 0;

        $arrayData = [];
        foreach ($rowData as $item) {
            $cont++;     

           /* print("
            
            <tr>
                <td class='even'>" . mostraData($item['MovimData']) . "</td>
                <td class='even' style='text-align: center'>" . $item['MovimTipo'] . "</td>
                <td class='odd'>" . $item['ProduNome'] . "</td>
                <td class='even'>" . $item['CategNome'] . "</td>
                <td class='odd'>" . $item['ForneNome'] . "</td>
                <td class='odd' style='text-align: center'>" . $item['MvXPrQuantidade'] . "</td>
                <td class='odd'>" . $item['Origem']  . "</td>
                <td class='even'>" . $item['Destino'] . "</td>
            </tr>
            ");*/

            $datas = mostraData($item['MovimData']);

            $tipo = $item['MovimTipo'];

            $nomeProduto = $item['ProduNome'];

            $nomeCategoria = $item['CategNome'];

            $nomeFornecedor = $item['ForneNome'];

            $quantidade = $item['MvXPrQuantidade'];

            $origem = $item['Origem'];

            $destino = $item['Destino'];

            $array = [
                'data'=>[
                    isset($datas) ? $datas : null,
                    isset($tipo) ? $tipo : null, 
                    isset($nomeProduto) ? $nomeProduto : null,
                    isset($nomeCategoria) ? $nomeCategoria : null, 
                    isset($nomeFornecedor) ? $nomeFornecedor : null, 
                    isset($quantidade) ? $quantidade : null, 
                    isset($origem) ? $origem : null, 
                    isset($destino) ? $destino : null 
                    
                ],
                'identify'=>[
                    
                ]
            ];

            array_push($arrayData,$array);

        }

         print(json_encode($arrayData));
    }
}

queryPesquisa();
