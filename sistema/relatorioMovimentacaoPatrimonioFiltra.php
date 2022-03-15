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

            $sql = "SELECT PatriId ,PatriNumero, PatriNumSerie, PatriEstadoConservacao, MvXPrId, MovimId, MovimData, PrXFaId, PrXFaMarca, PrXFaModelo, PrXFaFabricante,
                    MovimNotaFiscal, MvXPrValidade, MvXPrValorUnitario, MvXPrValidade, MvXPrAnoFabricacao, ProduNome, ProduId,
                    dbo.fnEmpenhosOrdemCompra(MovimUnidade, MovimOrdemCompra) as EmpenhosOrdemCompra,                   
                    CASE 
                        WHEN MovimOrigemLocal IS NULL THEN SetorO.SetorNome
                        ELSE LocalO.LcEstNome 
                            END as Origem,
                        CASE 
                        WHEN MovimDestinoLocal IS NULL THEN ISNULL(SetorD.SetorNome, MovimDestinoManual)
                        ELSE LocalD.LcEstNome
                            END as Destino
                    FROM Patrimonio
                    JOIN MovimentacaoXProduto on MvXPrPatrimonio = PatriId
                    JOIN Movimentacao on MovimId = MvXPrMovimentacao
                    JOIN Produto on ProduId = MvXPrProduto
                    LEFT JOIN ProdutoXFabricante on PrXFaPatrimonio = PatriId
                    LEFT JOIN LocalEstoque LocalO on LocalO.LcEstId = MovimOrigemLocal 
                    LEFT JOIN LocalEstoque LocalD on LocalD.LcEstId = MovimDestinoLocal 
                    LEFT JOIN Setor SetorO on SetorO.SetorId = MovimOrigemSetor 
                    LEFT JOIN Setor SetorD on SetorD.SetorId = MovimDestinoSetor 
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

        $arrayData = [];
        foreach ($rowData as $item) {
            $cont++;
           /* print("
                
                <tr idPatrimonio=".$item['PatriId'].'#'.$item['ProduId'].'#'.$item['PrXFaId']." editado='0'>
                   <td class='even'>" . $cont . "</td>
                   <td class='odd'>" . $item['ProduNome'] . "</td>
                   <td class='even'>".$item['PatriNumero']."</td>
                   <td class='odd'>" . $item['MovimNotaFiscal'] . "</td>
                   <td class='even'>".mostraValor($item['MvXPrValorUnitario'])."</td>
                   <td class='odd'></td>
                   <td class='even'>".mostraData($item['MvXPrValidade'])."</td>
                   <td class='odd'>" . $item['Origem'] . "</td>
                   <td class='even'>" . $item['Destino'] . "</td>
                   <td class='even' style='display: none'>" . mostraData($item['MovimData']) . "</td>
                   <td class='even' style='display: none'>" . mostraData($item['MvXPrAnoFabricacao']) . "</td>
                   <td class='even' style='display: none'>" . $item['EmpenhosOrdemCompra'] . "</td>
                   <td style='text-align: center'><i idinput='campo3' idrow='row3' class='icon-pencil7 btn-acoes' style='cursor: pointer'></i></td>
                   <td style='display: none' id='inputNumeroSerie'><input type='text' value='" . $item['PatriNumSerie'] . "'></td>
                   <td style='display: none' id='inputEstadoConservacao'><input type='text' value='" . $item['PatriEstadoConservacao'] . "'></td> 
                   <td style='display: none' id='inputIdProdutoXFabricante'><input type='text' value='" . $item['PrXFaMarca'] . '#' . $item['PrXFaModelo'] . '#' . $item['PrXFaFabricante'] ."'></td>                   
                </tr>
             ");*/

            $contador = $cont;  

            $nomeProduto = $item['ProduNome'];

            $patriNumero = $item['PatriNumero'];

            $notaFiscal = $item['MovimNotaFiscal'];

            $valorUnitario = mostraValor($item['MvXPrValorUnitario']);

            $valor = ;

            $validade = mostraData($item['MvXPrValidade']);

            $origem = $item['Origem'];

            $destino = $item['Destino'];

            $datas = mostraData($item['MovimData']);

            $anoFabricacao = mostraData($item['MvXPrAnoFabricacao']);

            $empenhos = $item['EmpenhosOrdemCompra'];

            $acoes = '<i idinput='campo3' idrow='row3' class='icon-pencil7 btn-acoes' style='cursor: pointer'></i>';

            $numeroSerie = $item['PatriNumSerie'];

            $estadoConservacao = $item['PatriEstadoConservacao'];

            $fabricante = $item['PrXFaMarca'];
           
            $array = [
                'data'=>[
                    isset($contador) ? $contador : null,
                    isset($nomeProduto) ? $nomeProduto : null,
                    isset($patriNumero) ? $patriNumero : null,
                    isset($notaFiscal) ? $notaFiscal : null,
                    isset($valorUnitario) ? $valorUnitario : null,
                    isset($valor) ? $valor : null, 
                    isset($validade) ? $validade : null,  
                    isset($origem) ? $origem : null, 
                    isset($destino) ? $destino : null,
                    isset($datas) ? $datas : null,
                    isset($anoFabricacao) ? $anoFabricacao : null,
                    isset($empenhos) ? $empenhos : null,
                    isset($acoes) ? $acoes : null,
                    isset($numeroSerie) ? $numeroSerie : null, 
                    isset($estadoConservacao) ? $estadoConservacao : null,
                    isset($fabricante) ? $fabricante : null

                    
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
