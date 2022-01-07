<?php

    // Caso seja uma movimentação de saída
    if ($row['MovimTipo'] == 'S') {

        $numPaginas = count($rowMvPrPatrimoniado) / 4;
        $cont = 0;
        $produtos = array_chunk($rowMvPrPatrimoniado, 4);

        foreach ($produtos as $produtos3) {            
            
            $cont += 1;
            
            //Isso aqui para os casos de uma retirada de mais de 4 bens permanentes de uma só vez (isso é para quebrar a página)
            if ($cont > 1){
                $html .= '<br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
            }

            $html .= '  
           
            <table style="width:100%; border: none;">
                <tr>
                    <td  style="background-color: #d8d8d8; text-align: center; font-weight: bold; width:100%; ">BENS PATRIMONIADOS</td>
                </tr>
            </table> <br> ';

            foreach ($produtos3 as $value) {

                $html .= '   
                
                <table style="width:100%;">
                    <tr>   

                        <td style="text-align: center; background-color: #eee;">Patrimônio: '.$value['PatriNumero'].'</td>
                                          
                        <td>   
                            <table style="width:100%; border: none;"> 
                                <tr>
                                    <td style="text-align: left; width:50%">Produto:<br>'.$value['ProduNome'].'</td>
                                    <td style="text-align: left; width:40%">Código:<br>'.$value['ProduCodigo'].'</td>
                                </tr>
                            </table>
                            <table style="width:100%; border: none;">
                                <tr>
                                    <td style="text-align: left; width:60%">Marca:<br>'. $value['MarcaNome'] .'</td>
                                    <td style="text-align: left; width:20%">Modelo:<br>'.$value['ModelNome'].'</td>
                                    <td style="text-align: left; width:20%">Unidade:<br>'.$value['UnMedSigla'].'</td>                                    
                                </tr>
                            </table>

                            <table style="width:100%;border: none;">
                                <tr>
                                    ';

                                    $html .= '  <td style="text-align: left; width:30%">Categoria:<br>'.$value['CategNome'].'</td>';

                                    if($value['Validade'] == ''){
                                        $html .= '  <td style="text-align: left; width:20%">Lote:<br>'.$value['MvXPrLote'].'</td>';                    
                                    } else{
                                        $html .= '  <td style="text-align: left; width:20%">Lote:<br>'.$value['MvXPrLote'].'</td>';
                                        $html .= '  <td style="text-align: left; width:30%">Validade:<br>'.mostraData($value['Validade']).'</td>';    
                                    }

                                    $html .= '  <td style="text-align: left; width:20%">Quantidade:<br>1</td>';

                                $html .= '</tr>
                            </table> 
                        </td> 
                    </tr>   
                </table> <br> ';
            }

        }

        //*************************************** Caso seja uma movimentação de Transferência ***********************************\\
    } else if ($row['MovimTipo'] == 'T') {

        $numPaginas = count($rowMvPrPatrimoniado) / 3;
        $cont = 0;
        $produtos = array_chunk($rowMvPrPatrimoniado, 3);

        foreach ($produtos as $produtos3) {
            $cont += 1;

            $html = "";

            $html .= "<div style='height: 950px; border: 1px solid rgb(149, 150, 148); box-sizing: border-box; padding: 20px'>";

            $html .= "
	            <div style='position: relative; width:100%; border-bottom: 1px solid #000;'>
		           <div style='float:left; width: 400px; display: inline-block; margin-bottom: 10px;'>
			           <img src='global_assets/images/empresas/".$_SESSION['EmpreFoto']."' style='width:60px; height:60px; float:left; margin-right: 10px; margin-top:-10px;' />		
			           <span style='font-weight:bold;line-height:200px;'>" . $_SESSION['EmpreNomeFantasia'] . "</span><br>
			           <div style='position: absolute; font-size:9px; margin-top: 8px; margin-left:4px;'>Unidade: ".$_SESSION['UnidadeNome']."</div>
		               </div>
		            <div style='margin-top: -44px;width:300px; float:right; display: inline-block; text-align:right; font-size: 0.8rem; margin-bottom: 10px;'>
			            <div style='margin-top:8px; font-weight:bold;'>Recibo de Retirada - Requisição de Material</div>
		            </div> 
	            </div>
			";

            $html .= '<br>
            <div style="display: flex; flex-direction: column ;width: 100%; height 20px">
               <div style="">
                    <div style="margin-right: 12px ;float: left ;width: 18.1%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Data: ' . mostraData($row['MovimData']) . '</p>
                    </div>
                    <div style="margin: 0px 2px 0px 2px ;float: left; width: 13.5%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                        <p style="font-size: 0.8rem; text-align: center ;margin: 0px; padding: 8px">Nº 0001/19</p>
                    </div>
               </div>
               <div style="margin-top: 3px ;">
                    <div style="margin-right: 12px ;float: left ;width: 49.5%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px">Origem: ' . $Origem . '</p>
                    </div>
                    <div style="margin: 0px 2px 0px 2px ;float: left; width: 49.5%; border: 1px solid #c9d0d4">
                        <p style="font-size: 0.8rem ;margin: 0px; padding: 8px">Destino: ' . $Destino . '</p>
                    </div>
               </div>
               <div style="margin-top: 7px ;">
                    <div style="margin-right: 12px ;float: left ;width: 100%; border: 1px solid #c9d0d4; background-color: #d8d8d8">
                        <p style="font-size: 0.8rem; margin: 0px; padding: 8px; text-align: center">Identificação dos Bens</p>
                    </div>
               </div>
            </div>
            <br>';

            foreach ($produtos3 as $value) {


                if ($value['ClassNome'] == 'Bem Permanente') {

                    $html .= "
                            <div style='margin-top: -9px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 23.42%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: " . $value['ProduCodigo'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 50%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: " . $value['ProduNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 25%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Categoria: " . $value['CategNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-top: 2px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Marca: " . $value['MarcaNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Modelo: " . $value['ModelNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-bottom: 8px ;margin-top: 2px; margin-bottom 4px; max-height: 200px !important'>
                                <div style='margin-right: 12px ;float: left ;width: 20%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem; margin: 0px; padding: 8px'>Classificação: " . $value['ClassNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 16.4%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Patrimônio: 154224</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Lote: " . $value['MvXPrLote'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Validade: " . mostraData($value['MvXPrValidade']) . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Unidade: " . $value['UnMedNome'] . "</p>
                                </div>
                                <div style='heigth: 5vmin;margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='heigth: 100% ;font-size: 0.6rem ;margin: 0px; padding: 8px'>Quantidade1: " . $value['MvXPrQuantidade'] . "</p>
                                </div>
                            </div>
                                 ";
                } else {
                    $html .= "
                            <div style='margin-top: -9px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 23.42%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Código: " . $value['ProduCodigo'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 50%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Produto: " . $value['ProduNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 25%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Categoria: " . $value['CategNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-top: 2px ;'>
                                <div style='margin-right: 12px ;float: left ;width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem; margin: 0px; padding: 8px'>Marca: " . $value['MarcaNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 39.2%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.8rem ;margin: 0px; padding: 8px'>Modelo: " . $value['ModelNome'] . "</p>
                                </div>
                            </div>
                            <div style='margin-bottom: 8px ;margin-top: 2px; margin-bottom 4px; background-color: #e9e9e9!important'>
                                <div style='margin-right: 12px ;float: left ;width: 20%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem; margin: 0px; padding: 8px'>Classificação: " . $value['ClassNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 16.4%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Patrimônio: </p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Lote: " . $value['MvXPrLote'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Validade: " . mostraData($value['MvXPrValidade']) . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Unidade: " . $value['UnMedNome'] . "</p>
                                </div>
                                <div style='margin: 0px 2px 0px 2px ;float: left; width: 15%; border: 1px solid #c9d0d4; background-color: #e9e9e9'>
                                    <p style='font-size: 0.6rem ;margin: 0px; padding: 8px'>Quantidade: " . $value['MvXPrQuantidade'] . "</p>
                                </div>
                            </div>
                                 ";
                }
            }
        }
    }

    ?>