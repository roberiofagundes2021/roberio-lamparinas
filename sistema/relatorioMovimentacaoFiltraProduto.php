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
            WHERE " . $string . " MovimUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'LIBERADO'
            ";
        $result = $conn->query($sql);
        $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

        count($rowData) >= 1 ? $cont = 1 : $cont = 0;
    }

    if ($cont == 1) {
        $cont = 0;
        foreach ($rowData as $item) {
            $cont++;     

            print("
            
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
            ");
        }
    }
}

queryPesquisa();
