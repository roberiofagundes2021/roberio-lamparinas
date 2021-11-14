<?php

use function PHPSTORM_META\type;

include_once("sessao.php"); 

$_SESSION['PaginaAtual'] = 'Produto Excel';

include('global_assets/php/conexao.php');

// $sql = "SELECT ProduCodigo, ProduCodigoBarras, ProduNome, CategNome, SbCatNome, ProduDetalhamento,
// 		ProduValorCusto, ProduOutrasDespesas, ProduCustoFinal, ProduMargemLucro, ProduValorVenda,
// 		ProduEstoqueMinimo, MarcaNome, ModelNome, FabriNome, UnMedNome, TpFisNome,
// 		NcmNome, OrFisNome, ProduCest, SituaNome
// 		FROM Produto
// 		LEFT JOIN Categoria on CategId = ProduCategoria
// 		LEFT JOIN SubCategoria on SbCatId = ProduSubCategoria
// 		LEFT JOIN Marca on MarcaId = ProduMarca
// 		LEFT JOIN Modelo on ModelId = ProduModelo
// 		LEFT JOIN Fabricante on FabriId = ProduFabricante
// 		LEFT JOIN UnidadeMedida on UnMedId = ProduUnidadeMedida
// 		LEFT JOIN TipoFiscal on TpFisId = ProduTipoFiscal
// 		LEFT JOIN Ncm on NcmId = ProduNcmFiscal
// 		LEFT JOIN OrigemFiscal on OrFisId = ProduOrigemFiscal
// 		LEFT JOIN Situacao on SituaId = ProduStatus
// 		WHERE ProduUnidade = ".$_SESSION['UnidadeId']."
// 		ORDER BY ProduNome ASC";
// $result = $conn->query($sql);
// $row = $result->fetchAll(PDO::FETCH_ASSOC);
// $count = count($row);
//	var_dump($count);die;

// $dadosXls  = "";
// $dadosXls .= "  <table>";
// // Cabeçalho data
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th bgcolor='#fff' colspan='6'></th>";
// $dadosXls .= "        <th bgcolor='#fff' colspan='4'>24 Junho de 2021</th>";
// $dadosXls .= "        <th bgcolor='#fff' colspan='4'>25 Junho de 2021</th>";
// $dadosXls .= "     </tr>";
// // Cabeçalho saldo inicial
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2'>Saldo Inicial</th>";
// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2'></th>";

// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2'>-9.136.614,30</th>";
// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2' border-right='1px solid #cccccc'>-5.356.948,09</th>";
// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2'>-9.136.614,30</th>";
// $dadosXls .= "        <th bgcolor='#cccccc' colspan='2' border-right='1px solid #cccccc'>-5.356.948,09</th>";
// $dadosXls .= "     </tr>";
// // Cabeçalho entrada
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th></th>";
// $dadosXls .= "     </tr>";
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>ENTRADA</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2'></th>";

// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>";
// $dadosXls .= "     </tr>";
// // Linhas da entrada
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th></th>";
// $dadosXls .= "     </tr>";
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>ENTRADA</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2'></th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>";
// $dadosXls .= "        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>";
// $dadosXls .= "     </tr>";




//declaramos uma variavel para monstarmos a tabela
// $dadosXls  = "";
// $dadosXls .= "  <table>";
// $dadosXls .= "     <tr>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Codigo</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>CodigoBarras</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Nome</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Categoria</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>SubCategoria</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Detalhamento</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>ValorCusto</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>OutrasDespesas</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>CustoFinal</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>MargemLucro</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>ValorVenda</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>EstoqueMinimo</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Marca</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Modelo</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Fabricante</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>UnidadeMedida</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>TipoFiscal</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>NcmFiscal</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>OrigemFiscal</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Cest</th>";
// $dadosXls .= "        <th bgcolor='#cccccc'>Situacao</th>";
// $dadosXls .= "     </tr>";

//varremos o array com o foreach para pegar os dados
// foreach($row as $item){
// 	$dadosXls .= "   <tr>";
// 	$dadosXls .= "      <td>".$item['ProduCodigo']."</td>";
// 	$dadosXls .= "      <td>&nbsp;".$item['ProduCodigoBarras']."</td>";  //Como os números podem ser grandes, o Excel reduzirá o mesmo. Daí colocando espaço em branco antes ele entenderá que é um texto e não um numero
// 	$dadosXls .= "      <td>".utf8_decode($item['ProduNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['CategNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['SbCatNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['ProduDetalhamento'])."</td>";
// 	$dadosXls .= "      <td>".mostraValor($item['ProduValorCusto'])."</td>";
// 	$dadosXls .= "      <td>".mostraValor($item['ProduOutrasDespesas'])."</td>";
// 	$dadosXls .= "      <td>".mostraValor($item['ProduCustoFinal'])."</td>";
// 	$dadosXls .= "      <td>".mostraValor($item['ProduMargemLucro'])."</td>";
// 	$dadosXls .= "      <td>".mostraValor($item['ProduValorVenda'])."</td>";
// 	$dadosXls .= "      <td>".$item['ProduEstoqueMinimo']."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['MarcaNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['ModelNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['FabriNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['UnMedNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['TpFisNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['NcmNome'])."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['OrFisNome'])."</td>";
// 	$dadosXls .= "      <td>".$item['ProduCest']."</td>";
// 	$dadosXls .= "      <td>".utf8_decode($item['SituaNome'])."</td>";
// 	$dadosXls .= "   </tr>";
// }
    // $dadosXls .= "  </table>";
 
    // // Definimos o nome do arquivo que será exportado  
    // $arquivo = "LamparinasProdutos.xls"; 
    
    
     
    // // Configurações header para forçar o download  
	// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// //header('Content-Type: application/vnd.ms-excel');
    // header('Content-Disposition: attachment;filename="'.$arquivo.'"');
    // header('Cache-Control: max-age=0');
    // // Se for o IE9, isso talvez seja necessário
    // header('Cache-Control: max-age=1');
    
    // header("Content-type: text/html; charset=utf-8");
	
    // // Envia o conteúdo do arquivo  
    // echo $dadosXls;  
    // exit;






















// Para a data ficar em português. Foi usado lá embaixo onde tem strftime (referência: https://www.linhadecomando.com/php/php-funcao-date-para-strftime-em-portugues)
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

/* 
  --------------- $_POST -------------
  ["typeDate"]=> "D"/"M"
  ["dateInitial"]=> string(10) "2021-03-05"
  ["dateEnd"]=> string(10) "2021-03-17"
  ["cmbCentroDeCustos"]=> string(1) "4"
  ["cmbPlanoContas"]=>string(2) "78"
*/

//gera os centros de custo
function centroDeCusto($CCNome, $CCPrevisto, $CCRealizado, $planoDeContas, $diaInicio, $diaFim)
{      
  $retorno = "";
  
    $porc_cc_1  = 0.00;
    
    // $cc2Previsto = ($cc2Previsto != "" ? $cc2Previsto : 0);
    // $cc2Realizado = ($cc2Realizado != "" ? $cc2Realizado : 0);
  
    // if($cc2Previsto != 0)
    // {
    //   if(($CCPrevisto+$cc2Previsto) != 0)
    //   {
    //     $porc_cc_1  =  ((($CCRealizado + $cc2Realizado) * 100) / ($CCPrevisto+$cc2Previsto));
    //   }
    // }
    // else
    // {
    //    if(($CCPrevisto) != 0)
    //   {
    //     $porc_cc_1  =  (($CCRealizado  * 100) / $CCPrevisto);
    //   } 
    // }

  $retorno .=  "<tr>
                  <td bgcolor='#cccccc' colspan='2'>".$CCNome."</td>
               ";

  for($idias = $diaInicio; $idias <= $diaFim; $idias++)
  {  
    $retorno .= "
                  <td bgcolor='#cccccc' colspan='2'>".mostraValor($CCPrevisto)."</td>
                  <td bgcolor='#cccccc' colspan='2'>".mostraValor($CCRealizado)."</td>
                ";
  }

  $retorno .= "
                <td bgcolor='#cccccc' colspan='2'>".mostraValor($porc_cc_1)."%</td>
              </tr>
              ".$planoDeContas;
  //----------------------------------------------------------------------------------
  $planoDeContas;
  //----------------------------------------------------------------------------------

  return $retorno;
}

//gera os centros de custo
function centroDeCustoMes($CCNome, $CCPrevisto, $cc2Previsto, $cc3Previsto, $CCRealizado, $cc2Realizado, $cc3Realizado, $planoDeContas, $pagina)
{      
  $porc_cc_1  = 0.00;
  
  $cc2Previsto = ($cc2Previsto != "" ? $cc2Previsto : 0);
  $cc2Realizado = ($cc2Realizado != "" ? $cc2Realizado : 0);
  ///////////////////////
  $cc3Previsto = ($cc3Previsto != "" ? $cc3Previsto : 0);
  $cc3Realizado = ($cc3Realizado != "" ? $cc3Realizado : 0);
  ///////////////////////

  if($cc2Previsto != 0 || $cc3Previsto != 0)
  {
    if(($CCPrevisto + $cc2Previsto + $cc3Previsto) != 0)
    {
      $porc_cc_1  =  ((($CCRealizado + $cc2Realizado + $cc3Previsto) * 100) / ($CCPrevisto + $cc2Previsto + $cc3Previsto));
    }
  }
  else
  {
     if(($CCPrevisto) != 0)
    {
      $porc_cc_1  =  (($CCRealizado  * 100) / $CCPrevisto);
    } 
  }

  $retorno = "      <div class='card-body' style='padding-top: 0;padding-bottom: 0'>
                      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                        <div class='col-lg-4' style='border-right: 1px dotted black;'>
                          <span>". $CCNome ."</span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>".mostraValor($CCPrevisto) ."</span>
                            </div>

                            <div class='col-md-6'>
                              <span>".mostraValor($CCRealizado)."</span>
                            </div>
                          </div>
                        </div>
                        
                        ".(($pagina['i']+1) + $pagina['pagina'] <= $pagina['mesFinal'] ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                                            <div class='row'>
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Previsto) ."</span>
                                                                                              </div>
                                                                  
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Realizado) ."</span>
                                                                                              </div>
                                                                                            </div>
                                                                                          </div>" : "")."
                        
                        ".(($pagina['i']+3) + $pagina['pagina'] <= $pagina['mesFinal'] ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                                            <div class='row'>
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc3Previsto) ."</span>
                                                                                              </div>
                                                                  
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc3Realizado) ."</span>
                                                                                              </div>
                                                                                            </div>
                                                                                          </div>" : "")."

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-12'>
                              <span>".mostraValor($porc_cc_1) ."%</span>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>".
  
  //----------------------------------------------------------------------------------
   $planoDeContas;
  //----------------------------------------------------------------------------------
             
  return $retorno;
}

//gera os planos de contas
function planoDeContas($PL, $tipo, $dataInicio, $dataFim)
{
  $Retorno = array('HTML' => '','total_previsto' => 0.00, 'total_realizado' => 0.00,'total_previsto2' => 0.00, 'total_realizado2' => 0.00  );
  $porc_cc_1  = 0;

  for($i = 0;$i < count($PL,0);$i++)
  {
    $nomePL = strlen($PL[$i]['PlConNome']) > 50 ? substr($PL[$i]['PlConNome'], 0, 50)."..." : $PL[$i]['PlConNome'];

    $Retorno['HTML'] .= "<tr>
                             <td bgcolor='#eeeeee' colspan='2'>".$nomePL."</td>
                        ";

    // for($meses = $dataInicio; $meses <= $dataFim; $meses++)
    // {  
      $Retorno['HTML'] .= "
                            <td bgcolor='#eeeeee' colspan='2'>".mostraValor($PL[$i]['PL_Previsto'.$tipo])."</td>
                            <td bgcolor='#eeeeee' colspan='2'>".mostraValor($PL[$i]['PL_Realizado'.$tipo])."</td>
                          ";
    // }

    //totaliza os valores
    $Retorno['total_previsto']  += $PL[$i]['PL_Previsto'.$tipo];
    $Retorno['total_realizado'] += $PL[$i]['PL_Realizado'.$tipo];  

    //calculo de porcentagem dos previstos e realizados
    if(isset($pl2)&&(is_array($pl2)))
        $vlrPl2 = $pl2[$i]['PL_Previsto'.$tipo];
    else
        $vlrPl2 = 0;
    
    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl2)  != 0)
    {
      $porc_cc_1  =  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl2) * 100) / 
                            ($PL[$i]['PL_Previsto'.$tipo]+$vlrPl2));
    }    

    $Retorno['HTML'] .= "  <td bgcolor='#eeeeee' colspan='2'>".mostraValor($porc_cc_1) ."%</td>
                        </tr>";
                        
  }

  return $Retorno;
}
/////////////////////////////////////////////////////////////////////
function planoDeContasMes($PL,$pl2, $pl3, $pagina, $tipo)
{
  $Retorno    = array('HTML' => '','total_previsto' => 0.00, 'total_realizado' => 0.00,'total_previsto2' => 0.00, 'total_realizado2' => 0.00,'total_previsto3' => 0.00, 'total_realizado3' => 0.00  );
  $porc_cc_1  = 0;

  for($i = 0;$i < count($PL,0);$i++)
  {
    $nomePL = strlen($PL[$i]['PlConNome']) > 50 ? substr($PL[$i]['PlConNome'], 0, 50)."..." : $PL[$i]['PlConNome'];

    $Retorno['HTML'] .= "<div class='card-body' style='padding-top: 0; padding-bottom: 0'>
      <div class='row' style='background: #eeeeee; line-height: 3rem; box-sizing:border-box'>

        <div class='col-lg-4' style='border-right: 1px dotted black; padding-left: 20px;'>
          <span title='".$PL[$i]['PlConNome']."'>". $nomePL ."</span>
        </div>

        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
          <div class='row'>
            <div class='col-md-6'>
              <span>".mostraValor($PL[$i]['PL_Previsto'.$tipo])."</span>
            </div>

            <div class='col-md-6'>
              <span>".mostraValor($PL[$i]['PL_Realizado'.$tipo])."</span>
            </div>
          </div>
        </div>
        
        ".(($pagina['i']+1) + $pagina['pagina'] <= $pagina['mesFinal'] ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                            <div class='row'>
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Previsto'.$tipo]):"")."</span>
                                                                              </div>
                                                                  
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Realizado'.$tipo]):"")."</span>
                                                                              </div>
                                                                            </div>
                                                                          </div>" : "")."

        ".(($pagina['i']+2) + $pagina['pagina'] <= $pagina['mesFinal'] ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                          <div class='row'>
                                                                            <div class='col-md-6'>
                                                                              <span>".(is_array($pl3)? mostraValor($pl3[$i]['PL_Previsto'.$tipo]):"")."</span>
                                                                            </div>
                                                                
                                                                            <div class='col-md-6'>
                                                                              <span>".(is_array($pl3)? mostraValor($pl3[$i]['PL_Realizado'.$tipo]):"")."</span>
                                                                            </div>
                                                                          </div>
                                                                        </div>" : ""."");

    //totaliza os valores
    $Retorno['total_previsto']  += $PL[$i]['PL_Previsto'.$tipo];
    $Retorno['total_realizado'] += $PL[$i]['PL_Realizado'.$tipo];  
    
    $Retorno['total_previsto2']  += (is_array($pl2))?$pl2[$i]['PL_Previsto'.$tipo]:0;
    $Retorno['total_realizado2'] += (is_array($pl2))?$pl2[$i]['PL_Realizado'.$tipo]:0; 

    $Retorno['total_previsto3']  += (is_array($pl3))?$pl3[$i]['PL_Previsto'.$tipo]:0;
    $Retorno['total_realizado3'] += (is_array($pl3))?$pl3[$i]['PL_Realizado'.$tipo]:0; 

    //calculo de porcentagem dos previstos e realizados
    if(isset($pl2)&&(is_array($pl2)))
        $vlrPl2 = $pl2[$i]['PL_Previsto'.$tipo];
    else
        $vlrPl2 = 0;
    
    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl2)  != 0)
    {
      $porc_cc_1  =  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl2) * 100) / 
                            ($PL[$i]['PL_Previsto'.$tipo]+$vlrPl2));
    } 
    
    //calculo de porcentagem dos previstos e realizados
    if(isset($pl3)&&(is_array($pl3)))
        $vlrPl3 = $pl3[$i]['PL_Previsto'.$tipo];
    else
        $vlrPl3 = 0;
    
    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl3)  != 0)
    {
      $porc_cc_1  +=  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl3) * 100) / 
                            ($PL[$i]['PL_Previsto'.$tipo]+$vlrPl3));
    }

    $Retorno['HTML'] .= "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
          <div class='row'>
            <div class='col-md-12'>
              <span>".mostraValor($porc_cc_1) ."%</span>
            </div>
          </div>
        </div>

      </div>
    </div>";
  }

  return $Retorno;
}
/////////////////////////////////////////////////////////////////////

function retornaBuscaComoArray($dataFiltroDiaInicio, $dataFiltroDiaFim,$ccFiltro,$plFiltro)
{        

    // if(checkdate(explode("-",$datasFiltro[0])[2], explode("-",$datasFiltro[0])[1], explode("-",$datasFiltro[0])[0])){
 
      include('global_assets/php/conexao.php');
      $sql = "SELECT 
              CnCusId, 
              CnCusNome, 
              dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'E') as CC_PrevistoEntrada,  
              dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'E') as CC_RealizadoEntrada,  
              dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'S') as CC_PrevistoSaida,
              dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'S') as CC_RealizadoSaida,
              PlConId, 
              PlConNome,
              dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'E') as PL_PrevistoEntrada,  
              dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'E') as PL_RealizadoEntrada,  
              dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'S') as PL_PrevistoSaida,
              dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$dataFiltroDiaInicio."', '".$dataFiltroDiaFim."', 'S') as PL_RealizadoSaida
          FROM CentroCusto CC 
          JOIN PlanoContas PL ON PlConCentroCusto = CnCusId
          JOIN Situacao S1 ON S1.SituaId = CC.CnCusStatus
          JOIN Situacao S2 ON S2.SituaId = PL.PlConStatus
          WHERE CnCusUnidade = 1 
              and S1.SituaChave = 'ATIVO' 
              and S2.SituaChave = 'ATIVO'
              and CnCusId in (".$ccFiltro.")
              and plconid in (".$plFiltro.")
          ORDER BY CnCusNome ASC";
    
    
    //echo $ccFiltro."<br>".$sql."<br>".$plFiltro."<br>".print_r($_POST)."<br><br>";
  
    $result            = $conn->query($sql);
    $rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);
  
    $regAt = '';
    $cont = 0;
  
    foreach($rowCentroDeCustos as $rowCC)
    {
      if($regAt != $rowCC['CnCusId'])
      {
        $regAt = $rowCC['CnCusId'];
  
        $cont = 0;
  
        //reserva os dados de centro de custo
        $cc[$regAt]['CnCusId']             = $rowCC['CnCusId'];            
        $cc[$regAt]['CnCusNome']           = $rowCC['CnCusNome']; 
        $cc[$regAt]['CC_PrevistoEntrada']  = $rowCC['CC_PrevistoEntrada'];
        $cc[$regAt]['CC_RealizadoEntrada'] = $rowCC['CC_RealizadoEntrada'];
        $cc[$regAt]['CC_PrevistoSaida']    = $rowCC['CC_PrevistoSaida']; 
        $cc[$regAt]['CC_RealizadoSaida']   = $rowCC['CC_RealizadoSaida']; 
      }
  
      //reserva os dados do plano de contas
      $pl[$regAt][$cont]['CnCusId']              = $rowCC['CnCusId']; 
      $pl[$regAt][$cont]['PlConId']              = $rowCC['PlConId']; 
      $pl[$regAt][$cont]['PlConNome']            = $rowCC['PlConNome'];
      $pl[$regAt][$cont]['PL_PrevistoEntrada']   = $rowCC['PL_PrevistoEntrada']; 
      $pl[$regAt][$cont]['PL_RealizadoEntrada']  = $rowCC['PL_RealizadoEntrada']; 
      $pl[$regAt][$cont]['PL_PrevistoSaida']     = $rowCC['PL_PrevistoSaida']; 
      $pl[$regAt][$cont]['PL_RealizadoSaida']    = $rowCC['PL_RealizadoSaida']; 
  
      $cont++;
    }
  
    //CV: Essas funções precisam trazer os CC e PL como foi feito com as consultas acima, pois dependerá de quais CC e PL estão sendo mostrados
  
    //pega o saldo inicial presumido
    $sql_saldo_ini_p   = "select dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$dataFiltroDiaInicio."') as SaldoInicialPrevisto";
    $result_saldo_ini_p = $conn->query($sql_saldo_ini_p);
    $rowSaldoIni_p      = $result_saldo_ini_p->fetchAll(PDO::FETCH_ASSOC);
    
    //echo $sql_saldo_ini_p."<br>";
  
    //pega o saldo inicial realizado
    $sql_saldo_ini_r = "select dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$dataFiltroDiaInicio."') as SaldoInicialRealizado";
    $result_saldo_ini_r = $conn->query($sql_saldo_ini_r);
    $rowSaldoIni_r      = $result_saldo_ini_r->fetchAll(PDO::FETCH_ASSOC);
      
    $retorno = array('cc'=>$cc,'pl'=>$pl,'saldoIni_p'=>$rowSaldoIni_p,'saldoIni_r'=>$rowSaldoIni_r);
    
    unset($cc);
    unset($pl);
    unset($result);
    unset($rowCentroDeCustos);
    
    return $retorno;
  // }
}

//-------------------------------------------------------------------------------------
// loop dos dias 
//-------------------------------------------------------------------------------------
// print_r($_POST);
$_POST["inputCentroDeCustos"] = explode(",", $_POST["inputCentroDeCustos"]);
$_POST["inputPlanoContas"] = explode(",", $_POST["inputPlanoContas"]);
$numDias    = $_POST["quantityDays"];
$diaInicio  = $_POST["dayInitial"];
$diaFim     = $_POST["dayEnd"];
$dataInicio = $_POST["inputDateInitial"];
$dataFim    = $_POST["inputDateEnd"];
$typeFiltro   = $_POST["typeDate"];
$ccFiltro   = rtrim(implode(',', $_POST["inputCentroDeCustos"]));
$plFiltro   = rtrim(implode(',', $_POST["inputPlanoContas"]));

if($typeFiltro == "D"){

  $print = "";

  $teste = false;
  
  $print_ent = '';
  $print_sai = '';
  
  //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SALDO INICIAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
  $print .= "<tbody>";

  // for($i = $diaInicio; $i <= $diaFim; $i++)
  // {  
    $teste = false;
      
    //limpa as variaveis
    if(isset($mes))
    {
      unset($mes);
      unset($cc);
      unset($pl);
      unset($saldoIni_p);
      unset($saldoIni_r);
    }
    
    //limpa as variaveis
    if(isset($mes2))  
    {
      unset($mes2);
      unset($cc2);
      unset($pl2);
      unset($saldoIni_p2);
      unset($saldoIni_r2);
    }    
      
    //****************************************************************************************************************** */
    // $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;
      
    // //Pea TODOS os dads do dia $i
    // $mes = retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro);
    
    // // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    
    // $cc           = $mes['cc'];
    // $pl           = $mes['pl'];
    // $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
    // $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];
    //****************************************************************************************************************** */
   
    // if(($i+1) <= $diaFim)
    // {        
    //   $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
      
    //  //Pea TODOS os dads do dia $i+1 se ele estiver na faixa de dias do filtro
    //   $mes2 = retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro);
      
    //   $cc2           = $mes2['cc'];
    //   $pl2           = $mes2['pl'];
    //   $saldoIni_p2   = $mes2['saldoIni_p'][0]['SaldoInicialPrevisto'];
    //   $saldoIni_r2   = $mes2['saldoIni_r'][0]['SaldoInicialRealizado'];
      
    //   $teste = true;
    // }
    
    //echo "<pre>".print_r($cc1)."</pre>";;
    // echo "<pre>".print_r($cc2)."</pre>";
    // echo "<pre>".print_r($pl1)."</pre>";
    // echo "<pre>".print_r($pl2)."</pre>";  exit();
  
    // if(isset($pl_Entrada))
    // {
    //   unset($pl_Entrada);
    // }
  
    // if(isset($pl_Saida))
    // {
    //   unset($pl_Saida);
    // }
    
    
    // if(isset($saldoIni_p2))
    //   $saldoIni_p2 = $saldoIni_p2;
    // else
    //   $saldoIni_p2 = 0;
    
    // if(isset($saldoIni_r2))
    //   $saldoIni_r2 = $saldoIni_r2;
    // else
    //   $saldoIni_r2 = 0;

    // Montado o cabeçalho da tabela com os dias
    $print .= "
              <table>
                <!-- Datas dos Dias -->
                  <thead>
                    <tr>
                      <th bgcolor='#fff' colspan='2'></th>
              ";

    for ($idias_2 = $diaInicio; $idias_2 <= $diaFim; $idias_2++) { 
        /* Obs.: utf8_encode é para o servidor da AZURE. Localmente não precisaria, mas para o servidor sim. */  
        $print .= "
                  <th bgcolor='#fff' colspan='4' float='left'>".str_pad($idias_2, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataInicio))))."</th>
                ";
    }
    
    $print .= "     
                  </tr>
                </thead>
              <!-- Datas dos Dias -->
            ";

    /////////////////////////////// Montando Saldo Inicial dos dias ////////////////////////////
    $print .= "
              <!-- SALDO INICIAL -->
               <tr>
                  <th bgcolor='#cccccc' colspan='2'>Saldo Inicial</th>
              ";

    $mesArray = explode('-', $dataInicio);
    $anoData = $mesArray[0];
    $mesData = (int)$mesArray[1];

    for ($idias_3 = $diaInicio; $idias_3 <= $diaFim; $idias_3++) { 

      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$idias_3;

      $dataFiltroDiaInicio = $anoData.'-'.$mesData.'-'.$idias_3;
      $dataFiltroDiaFim = $anoData.'-'.$mesData.'-'.$idias_3;
// print($dataFiltroDiaInicio);
// print("###");
// print($dataFiltroDiaFim);
// print("/////");
      // $datasFiltro = [];
      // $datasFiltro[0] = $dataFiltroDiaInicio;
      // $datasFiltro[1] = $dataFiltroDiaFim;
      
      //Pea TODOS os dads do dia $i
      $mes = retornaBuscaComoArray($dataFiltroDiaInicio,$dataFiltroDiaFim,$ccFiltro,$plFiltro);
      
      // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
      
      $cc           = $mes['cc'];
      $pl           = $mes['pl'];
      $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];

      /* Datas dos dias, Saldo inicial */
      $print .= "
                   <th bgcolor='#cccccc' colspan='2'>".mostraValor($saldoIni_p)."</th>
                   <th bgcolor='#cccccc' colspan='2'>".mostraValor($saldoIni_r)."</th>
                ";
    }

    $print .= "
                </tr>
                  <!-- SALDO INICIAL -->
              ";

    //------------------------------------------------------------------------------
    // limpa as variaveis que vao receber o plano de 
    // contas e centro de custo das funções    
    // <!--  usei essas variaveis para nao ter q fazer dois loops -->
    
    //==============================================================================
    // }
  
    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM SALDO INICIAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\]
  
    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ENTRADA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    // for($i = $diaInicio; $i <= $diaFim; $i++)
    // {  
    //recebe entradas apenas
    for($idias_4 = $diaInicio; $idias_4 <= $diaFim; $idias_4++)
    {  
    
      if($idias_4 == $diaInicio){
        /*Cabeçalho entrada */
        $print_ent .= " <!-- CABEÇALHO ENTRADA -->
                      <tr>
                         <th></th>
                      </tr>
                      <tr>
                         <th bgcolor='#607D8B' colspan='2' color='#fff'>ENTRADA</th>
                      ";
      }

      $print_ent .= "<th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>
                     <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>
                    ";

      if($idias_4 == $diaFim){
        $print_ent .= "
                        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>
                      </tr>
                      ";
      }       
      // }
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM ENTRADA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SAÍDA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      // for($i = $diaInicio; $i <= $diaFim; $i++)
      // {  
      //------------------------------------------------------------------------
      //recebe saidas apenas
      /*Cabeçalho entrada */
      if($idias_4 == $diaInicio){
        $print_sai .= " <!-- CABEÇALHO SAIDA -->
                              <tr>
                                 <th></th>
                              </tr>
                              <tr>
                                 <th bgcolor='#607D8B' colspan='2' color='#fff'>SAIDA</th>
                            ";
     }
      
      $print_sai .= " <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>
                      <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>
                      ";   
      if($idias_4 == $diaFim){
        $print_sai .= "<th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>  
                      </tr>";
      }
      // }
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM SAÍDA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    }
  
    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\TOTAL ENTRADA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    for($idias_5 = $diaInicio; $idias_5 <= $diaFim; $idias_5++)
    {  
      // totaliza as entradas e saidas
      $tot_previsto_entrada  = 0;
      $tot_previsto_saida    = 0;
      $tot_realizado_entrada = 0;
      $tot_realizado_saida   = 0;      
      // $tot_previsto_entrada2  = 0;
      // $tot_previsto_saida2    = 0;
      // $tot_realizado_entrada2 = 0;
      // $tot_realizado_saida2   = 0;
  
      // totaliza as entradas e saidas
      $tot_geral_previsto  = 0;
      $tot_geral_realizado = 0;
      // $tot_geral_previsto2  = 0;
      // $tot_geral_realizado2 = 0;
  
      $tot_previsto = 0;
      $tot_realizado = 0;
      // $tot_previsto2 = 0;
      // $tot_realizado2 = 0;

      ///////////////////////////////////////////////////////////////////////////////////
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$idias_5;
      
      //Pea TODOS os dads do dia $i
      $mes = retornaBuscaComoArray($dataFiltro, $dataFiltro,$ccFiltro,$plFiltro);
      
      // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
      
      $cc           = $mes['cc'];
      $pl           = $mes['pl'];
      $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];
      ///////////////////////////////////////////////////////////////////////////////////
  
      if(isset($cc) && (!empty($cc)))
      {
        //var_dump($cc1);
        foreach($cc as $cCusto)
        {
          if(isset($pl_Entrada))
            unset($pl_Entrada);
    
          if(isset($pl_Saida))
            unset($pl_Saida);
    
          //gera o html de plano de contas das entradas e soma os totais
          $pl_Entrada = planoDeContas($pl[$cCusto['CnCusId']],"Entrada", $diaInicio, $diaFim);
        
          //gera o html de plano de contas das saidas e soma os totais
          $pl_Saida = planoDeContas($pl[$cCusto['CnCusId']],"Saida", $diaInicio, $diaFim);
          
          //gera o html dos centros de custo das entradas
          //concatena com o htm gerado pelo plano de contas
          $print_ent .= centroDeCusto($cCusto['CnCusNome'],
                                        $cCusto['CC_PrevistoEntrada'],
                                        $cCusto['CC_RealizadoEntrada'],
                                        $pl_Entrada['HTML'],
                                        $diaInicio, $diaFim);                  
    
          //gera o html dos centros de custo das saidas
          //concatena com o htm gerado pelo plano de contas
          $print_sai .= centroDeCusto($cCusto['CnCusNome'],
                                        $cCusto['CC_PrevistoSaida'],
                                        $cCusto['CC_RealizadoSaida'],
                                        $pl_Saida['HTML'],
                                        $diaInicio, $diaFim);
    
          //soma os totais dos centro de custo das entradas
          $tot_entrada_prev_cc  = $cCusto['CC_PrevistoEntrada'];
          $tot_entrada_real_cc  = $cCusto['CC_RealizadoEntrada'];
          
    
          //soma os totais dos planos de contas das saidas com o centro de custo atual
          $tot_saida_prev_cc  = $cCusto['CC_PrevistoSaida'];
          $tot_saida_real_cc  = $cCusto['CC_RealizadoSaida'];
          
    
          // totaliza as entradas e saidas
          $tot_previsto_entrada  += $tot_entrada_prev_cc;
          $tot_previsto_saida    += $tot_saida_prev_cc;
          $tot_realizado_entrada += $tot_entrada_real_cc;
          $tot_realizado_saida   += $tot_saida_real_cc;
    
          // totaliza as entradas e saidas
          $tot_previsto  += ($tot_entrada_prev_cc - $tot_saida_prev_cc);
          $tot_realizado += ($tot_entrada_real_cc - $tot_saida_real_cc);
        }
    
        $tot_geral_previsto = $tot_previsto + $saldoIni_p;
        $tot_geral_realizado = $tot_realizado + $saldoIni_r;

      }
      //------------------------------------------------------------------------
      if($idias_5 == $diaInicio){
        $print_ent .= "<!-- TOTAL ENTRADA -->
                                <tr>
                                   <th bgcolor='#cccccc' colspan='2'>TOTAL</th>";
      }
      //recebe entradas apenas
      
      $print_ent .=  "   
                        <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_previsto_entrada)."</th>
                        <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_realizado_entrada)."</th>
                     ";
                     
      if($idias_5 == $diaFim){
        $print_ent .= "
                        </tr>
                        <!-- TOTAL ENTRADA -->
                        <!-- ENTRADA -->
                      ";
      }
      //}
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM TOTAL ENTRADA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
  
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\TOTAL SAÍDA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      //------------------------------------------------------------------------
      //recebe saidas apenas
      if($idias_5 == $diaInicio){
        $print_sai .= "<!-- TOTAL SAIDA -->
                        <tr>
                           <th bgcolor='#cccccc' colspan='2'>TOTAL</th>";
      }
      $print_sai .= "
                      <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_previsto_saida)."</th>
                      <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_realizado_saida)."</th>
                    ";
      if($idias_5 == $diaFim){
        $print_sai .= "
                      </tr>
                      <!-- TOTAL SAIDA -->
                      <!-- SAIDA -->
                    ";
      }
                      //  <th bgcolor='#cccccc' colspan='2'>".($teste ? mostraValor($tot_previsto_saida2) :"")."</th>
                      //  <th bgcolor='#cccccc' colspan='2'>".($teste ? mostraValor($tot_realizado_saida2) :"")."</th>
      // }
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM TOTAL SAÍDA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
  
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SALDO FINAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      //----------------------------------------------------------------------
      if($idias_5 == $diaInicio){
        $print .= $print_ent . $print_sai." <!-- SALDO FINAL -->
                                              <tr>
                                                <th bgcolor='#cccccc' colspan='2'>SALDO FINAL</th>";
      }
      //junta tudo no $print principal
      $print .= "
                   
                     <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_geral_previsto)."</th>
                     <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_geral_realizado)."</th>
                  
                  ";
      if($idias_5 == $diaFim){
        $print .= "
                    </tr>
                    <!-- SALDO FINAL -->
                    <!-- SALDO FINAL -->
                    <tr>
                       <th></th>
                    </tr>
                    <tr>
                       <th bgcolor='#cccccc' colspan='2'>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA):</th>
                    </tr>
                  ";
      }
  
      // $print .= "<tr>";
      // if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0)
      // {
      //   $print .= "<td>".mostraValor(((($tot_geral_realizado1+$tot_geral_realizado2) /($tot_geral_previsto1+$tot_geral_previsto2)) * 100)) ."%</td>";
      // }
      // else if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0)
      // {
      //   $print .= "<td>".mostraValor(((($tot_geral_realizado1) /($tot_geral_previsto1)) * 100)) ."%</td>";  
      // }
      // else
      // {
      //   $print .= " <td>0,00%</td>";
      // }
      
      if($idias_5 == $diaFim){
        $print .= "           
                            <!--td>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</td-->
                          </tr>
                      <tbody>
                    <!-- SALDO FINAL -->  
                  </table>
                  ";
      }    
    }
  // }
  //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM SALDO FINAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
} else {
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  // $data1 = new DateTime( $dataInicio );
  // $data2 = new DateTime( $dataFim );
  // $intervaloMeses = $data1->diff( $data2 )->m;

  $anoTela = explode('-', $dataInicio)[0];
  $dataInicioMes = explode('-', $dataInicio)[1];
  $dataFimMes =explode('-', $dataFim)[1];
  
  // print($dataInicio);
  // print("  /  ");
  // print($dataFim);
  // print("  /  ");
  // print($intervaloMeses);

  $print = "";

  $teste = false;
  
  $print_ent = '';
  $print_sai = '';
  
  //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SALDO INICIAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
  $print .= "<tbody>";

  // for($i = $diaInicio; $i <= $diaFim; $i++)
  // {  
    $teste = false;
      
    //limpa as variaveis
    if(isset($mes))
    {
      unset($mes);
      unset($cc);
      unset($pl);
      unset($saldoIni_p);
      unset($saldoIni_r);
    }
    
    //limpa as variaveis
    if(isset($mes2))  
    {
      unset($mes2);
      unset($cc2);
      unset($pl2);
      unset($saldoIni_p2);
      unset($saldoIni_r2);
    }    
      
    //****************************************************************************************************************** */
    // $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;
      
    // //Pea TODOS os dads do dia $i
    // $mes = retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro);
    
    // // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    
    // $cc           = $mes['cc'];
    // $pl           = $mes['pl'];
    // $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
    // $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];
    //****************************************************************************************************************** */

    // Montado o cabeçalho da tabela com os meses
    $print .= "
              <table>
                <!-- Datas dos Meses -->
                  <thead>
                    <tr>
                      <th bgcolor='#fff' colspan='2'></th>
              ";

    for ($imeses_2 = $dataInicioMes; $imeses_2 <= $dataFimMes; $imeses_2++) { 
        /* Obs.: utf8_encode é para o servidor da AZURE. Localmente não precisaria, mas para o servidor sim. */ 
    
        $print .= "
                  <th bgcolor='#fff' colspan='4' float='left'>".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoTela."-".str_pad($imeses_2, 2, '0', STR_PAD_LEFT)))))."</th>

                ";
        }
    
        $print .= "     
                      </tr>
                    </thead>
                  <!-- Datas dos Dias -->
                ";
    
    /////////////////////////////// Montando Saldo Inicial dos dias ////////////////////////////
    $print .= "
              <!-- SALDO INICIAL -->
               <tr>
                  <th bgcolor='#cccccc' colspan='2'>Saldo Inicial</th>
              ";

    $mesArray = explode('-', $dataInicio);
    $anoData = $mesArray[0];
    $mesData = (int)$mesArray[1];

    for ($imeses_3 = (int) $dataInicioMes; $imeses_3 <= (int) $dataFimMes; $imeses_3++) { 
      
      $ultimo_dia = date("t", mktime(0,0,0,$imeses_3,'01',$anoTela));
      $dataFiltroDiaInicioMes = $anoTela.'-'.$imeses_3.'-01';
      $dataFiltroDiaFimMes = $anoTela.'-'.$imeses_3.'-'.$ultimo_dia;

      $mes = retornaBuscaComoArray($dataFiltroDiaInicioMes,$dataFiltroDiaFimMes,$ccFiltro,$plFiltro);
      
      // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
      
      $cc           = $mes['cc'];
      $pl           = $mes['pl'];
      $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];

      /* Datas dos dias, Saldo inicial */
      $print .= "
                   <th bgcolor='#cccccc' colspan='2'>".mostraValor($saldoIni_p)."</th>
                   <th bgcolor='#cccccc' colspan='2'>".mostraValor($saldoIni_r)."</th>
                ";
    }
    // print($dataInicioMes);


    $print .= "
                </tr>
                  <!-- SALDO INICIAL -->
              ";

    //------------------------------------------------------------------------------
    // limpa as variaveis que vao receber o plano de 
    // contas e centro de custo das funções    
    // <!--  usei essas variaveis para nao ter q fazer dois loops -->
    
    //==============================================================================
    // }
  
    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM SALDO INICIAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\]
  
    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ENTRADA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    // for($i = $diaInicio; $i <= $diaFim; $i++)
    // {  
    //recebe entradas apenas
    for($imeses_4 = (int) $dataInicioMes; $imeses_4 <= (int) $dataFimMes; $imeses_4++)
    {  
    
      if($imeses_4 == $dataInicioMes){
        /*Cabeçalho entrada */
        $print_ent .= " <!-- CABEÇALHO ENTRADA -->
                      <tr>
                         <th></th>
                      </tr>
                      <tr>
                         <th bgcolor='#607D8B' colspan='2' color='#fff'>ENTRADA</th>
                      ";
      }

      $print_ent .= "<th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>
                     <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>
                    ";

      if($imeses_4 == $dataFimMes){
        $print_ent .= "
                        <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>
                      </tr>
                      ";
      }       
      // }
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM ENTRADA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SAÍDA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      // for($i = $diaInicio; $i <= $diaFim; $i++)
      // {  
      //------------------------------------------------------------------------
      //recebe saidas apenas
      /*Cabeçalho entrada */
      if($imeses_4 == $dataInicioMes){
        $print_sai .= " <!-- CABEÇALHO SAIDA -->
                              <tr>
                                 <th></th>
                              </tr>
                              <tr>
                                 <th bgcolor='#607D8B' colspan='2' color='#fff'>SAIDA</th>
                            ";
     }
      
      $print_sai .= " <th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto</th>
                      <th bgcolor='#607D8B' colspan='2' color='#fff'>Realizado</th>
                      ";   
      if($imeses_4 == $dataFimMes){
        $print_sai .= "<th bgcolor='#607D8B' colspan='2' color='#fff'>Previsto/Realizado%</th>  
                      </tr>";
      }
      // }
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\FIM SAÍDA CABEÇALHO\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    }
  
    $total_entrada_array = [];
    $total_saida_array = [];
    $total_geral_array = [];

    //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\TOTAL ENTRADA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    for($imeses_5 = (int) $dataInicioMes; $imeses_5 <= (int) $dataFimMes; $imeses_5++)
    {  

      $total_entrada_temp_array = [];
      $total_saida_temp_array = [];
      $total_geral_temp_array = [];

      // totaliza as entradas e saidas
      $tot_previsto_entrada  = 0;
      $tot_previsto_saida    = 0;
      $tot_realizado_entrada = 0;
      $tot_realizado_saida   = 0;      
      // $tot_previsto_entrada2  = 0;
      // $tot_previsto_saida2    = 0;
      // $tot_realizado_entrada2 = 0;
      // $tot_realizado_saida2   = 0;
  
      // totaliza as entradas e saidas
      $tot_geral_previsto  = 0;
      $tot_geral_realizado = 0;
      // $tot_geral_previsto2  = 0;
      // $tot_geral_realizado2 = 0;
  
      $tot_previsto = 0;
      $tot_realizado = 0;
      // $tot_previsto2 = 0;
      // $tot_realizado2 = 0;

      ///////////////////////////////////////////////////////////////////////////////////
      // $dataFiltro = trim(date('Y-m-d',strtotime($anoTela.'-'.$imeses_5.'-'.'01')));
      /////////////////////////////////////////////////////////////////////////////
      /////////////////////////////////////////////////////////////////////////////
      /////////////////////////////Analisar////////////////////////////////////////
      ///////////////////////////////Isso//////////////////////////////////////////
      /////////////////////////////////////////////////////////////////////////////
      // Inicialmente estava dentro de um for, com o conteúdo a baixo, mas estava ocorrendo um erro 
      // onde o conteúdo da tabela era repetido várias vezes. Então retirei o for, e agora comentei
      // este trecho a baixo.
      $ultimo_dia = date("t", mktime(0,0,0,$imeses_5,'01',$anoTela));
      $dataFiltroDiaInicioMes = $anoTela.'-'.$imeses_5.'-01';
      $dataFiltroDiaFimMes = $anoTela.'-'.$imeses_5.'-'.$ultimo_dia;

      $mes = retornaBuscaComoArray($dataFiltroDiaInicioMes,$dataFiltroDiaFimMes,$ccFiltro,$plFiltro);
      
      // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
      
      $cc           = $mes['cc'];
      $pl           = $mes['pl'];
      $saldoIni_p   = $mes['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r   = $mes['saldoIni_r'][0]['SaldoInicialRealizado'];
      ///////////////////////////////////////////////////////////////////////////////////
      ///////////////////////////////////////////////////////////////////////////////////
      ///////////////////////////////////////////////////////////////////////////////////
      ///////////////////////////////////////////////////////////////////////////////////
      ///////////////////////////////////////////////////////////////////////////////////
  
      if(isset($cc) && (!empty($cc)))
      {
        //var_dump($cc1);
        foreach($cc as $cCusto)
        {
          if(isset($pl_Entrada))
            unset($pl_Entrada);
    
          if(isset($pl_Saida))
            unset($pl_Saida);
    
          //gera o html de plano de contas das entradas e soma os totais
          $pl_Entrada = planoDeContas($pl[$cCusto['CnCusId']],"Entrada", $dataInicioMes, $dataFimMes);
        
          //gera o html de plano de contas das saidas e soma os totais
          $pl_Saida = planoDeContas($pl[$cCusto['CnCusId']],"Saida", $dataInicioMes, $dataFimMes);
          
          //gera o html dos centros de custo das entradas
          //concatena com o htm gerado pelo plano de contas
          $print_ent .= centroDeCusto($cCusto['CnCusNome'],
                                        $cCusto['CC_PrevistoEntrada'],
                                        $cCusto['CC_RealizadoEntrada'],
                                        $pl_Entrada['HTML'],
                                        $dataInicioMes, $dataFimMes);                  
    
          //gera o html dos centros de custo das saidas
          //concatena com o htm gerado pelo plano de contas
          $print_sai .= centroDeCusto($cCusto['CnCusNome'],
                                        $cCusto['CC_PrevistoSaida'],
                                        $cCusto['CC_RealizadoSaida'],
                                        $pl_Saida['HTML'],
                                        $dataInicioMes, $dataFimMes);
    
          //soma os totais dos centro de custo das entradas
          $tot_entrada_prev_cc  = $cCusto['CC_PrevistoEntrada'];
          $tot_entrada_real_cc  = $cCusto['CC_RealizadoEntrada'];
          
    
          //soma os totais dos planos de contas das saidas com o centro de custo atual
          $tot_saida_prev_cc  = $cCusto['CC_PrevistoSaida'];
          $tot_saida_real_cc  = $cCusto['CC_RealizadoSaida'];
          
    
          // totaliza as entradas e saidas
          $tot_previsto_entrada  += $tot_entrada_prev_cc;
          $tot_previsto_saida    += $tot_saida_prev_cc;
          $tot_realizado_entrada += $tot_entrada_real_cc;
          $tot_realizado_saida   += $tot_saida_real_cc;
    
          // totaliza as entradas e saidas
          $tot_previsto  += ($tot_entrada_prev_cc - $tot_saida_prev_cc);
          $tot_realizado += ($tot_entrada_real_cc - $tot_saida_real_cc);
        }
    
        $tot_geral_previsto = $tot_previsto + $saldoIni_p;
        $tot_geral_realizado = $tot_realizado + $saldoIni_r;

      }
      //------------------------------------------------------------------------
      $total_entrada_temp_array["previsto"] = $tot_previsto_entrada;
      $total_entrada_temp_array["realizado"] = $tot_realizado_entrada;
      $total_saida__temp_array["previsto"] = $tot_previsto_saida;
      $total_saida__temp_array["realizado"] = $tot_realizado_saida;
      $total_geral_temp_array["previsto"] = $tot_geral_previsto;
      $total_geral_temp_array["realizado"] = $tot_geral_realizado;

      array_push($total_entrada_array, $total_entrada_temp_array);
      array_push($total_saida_array, $total_saida__temp_array);
      array_push($total_geral_array, $total_geral_temp_array);

      // if($imeses_5 == 2){
      //   $new_print_ent = $print_ent;
      //   $new_print_sai = $print_sai;
      //   // print($print_ent);
      //   // print("</table>") ;
      // } else if($imeses_5 == 1){
      //   $new_print_ent = $print_ent;
      //   $new_print_sai = $print_sai;
      //   // print($print_ent);
      //   // print("</table>") ;
      // } else if($imeses_5 == ){
      //   $new_print_ent = $print_ent;
      //   $new_print_sai = $print_sai;
      //   // print($print_ent);
      //   // print("</table>") ;
      // }
      
      //------------------------------------------------------------------------   
    }

    $cont6 = 0;
    for($imeses_6 = (int) $dataInicioMes; $imeses_6 <= (int) $dataFimMes; $imeses_6++)
    { 
      // $tot_previsto_entrada  = $total_entrada_array[$cont6]["previsto"];
      // $tot_realizado_entrada = $total_entrada_array[$cont6]["realizado"];
      // $tot_previsto_saida    = $total_saida_array[$cont6]["previsto"];
      // $tot_realizado_saida   = $total_saida_array[$cont6]["realizado"];
      $cont6++;

      if($imeses_6 == $dataInicioMes){
        $print_ent .= "<!-- TOTAL ENTRADA -->
                                <tr>
                                   <th bgcolor='#cccccc' colspan='2'>TOTAL</th>";
      }
      
      $print_ent .=  "   
                        <th bgcolor='#cccccc' colspan='2' align='left'>".mostraValor($tot_previsto_entrada)."</th>
                        <th bgcolor='#cccccc' colspan='2' align='left'>".mostraValor($tot_realizado_entrada)."</th>
                     ";
                     
      if($imeses_6 == $dataFimMes){
        $print_ent .= "
                        </tr>
                        <!-- TOTAL ENTRADA -->
                        <!-- ENTRADA -->
                      ";
      }
      
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\TOTAL SAÍDA\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      if($imeses_6 == $dataInicioMes){
        $print_sai .= "<!-- TOTAL SAIDA -->
                        <tr>
                           <th bgcolor='#cccccc' colspan='2' margin='0px 5px'>TOTAL</th>
                             ";
      }

      $print_sai .= "
                      <th bgcolor='#cccccc' colspan='2' align='left'>".mostraValor($tot_previsto_saida)."</th>
                      <th bgcolor='#cccccc' colspan='2' align='left'>".mostraValor($tot_realizado_saida)."</th>
                    ";

      if($imeses_6 == $dataFimMes){
        $print_sai .= "
                          </tr>
                        <!-- TOTAL SAIDA -->
                      <!-- SAIDA -->
                    ";
      }
    }

    $cont7 = 0;
    for($imeses_7 = (int) $dataInicioMes; $imeses_7 <= (int) $dataFimMes; $imeses_7++)
    {
      
      // $tot_geral_previsto = $total_geral_array[$cont7]["previsto"];
      // $tot_geral_realizado = $total_geral_array[$cont7]["realizado"];
      $cont7++;
      //\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\SALDO FINAL\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
      //----------------------------------------------------------------------
      if($imeses_7 == $dataInicioMes){
      
        $print .= $print_ent . $print_sai." <!-- SALDO FINAL -->
                                              <tr>
                                                <th bgcolor='#cccccc' colspan='2'>SALDO FINAL</th>";
      }
      //junta tudo no $print principal
      $print .= "
                   
                     <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_geral_previsto)."</th>
                     <th bgcolor='#cccccc' colspan='2'>".mostraValor($tot_geral_realizado)."</th>
                  
                  ";
      if($imeses_7 == $dataFimMes){
        $print .= "
                    </tr>
                    <!-- SALDO FINAL -->
                    <!-- SALDO FINAL -->
                    <tr>
                       <th></th>
                    </tr>
                    <tr>
                       <th bgcolor='#cccccc' colspan='2'>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA):";
                       
                       if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0)
                       {
                         $print .= "<span>".mostraValor(((($tot_geral_realizado1+$tot_geral_realizado2) /($tot_geral_previsto1+$tot_geral_previsto2)) * 100)) ."%</span>";
                       }
                       else if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0)
                       {
                         $print .= "<span>".mostraValor(((($tot_geral_realizado1) /($tot_geral_previsto1)) * 100)) ."%</span>";  
                       }
                       else
                       {
                         $print .= " <span>0,00%</span>";
                       }
                       
          $print .= " </th>
                    </tr>";
      }
  
      // $print .= "<tr>";
      // if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0)
      // {
      //   $print .= "<td>".mostraValor(((($tot_geral_realizado1+$tot_geral_realizado2) /($tot_geral_previsto1+$tot_geral_previsto2)) * 100)) ."%</td>";
      // }
      // else if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0)
      // {
      //   $print .= "<td>".mostraValor(((($tot_geral_realizado1) /($tot_geral_previsto1)) * 100)) ."%</td>";  
      // }
      // else
      // {
      //   $print .= " <td>0,00%</td>";
      // }
      
      if($imeses_7 == $dataFimMes){
        $print .= "           
                            <!--td>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</td-->
                          </tr>
                      <tbody>
                    <!-- SALDO FINAL -->  
                  </table>
                  ";
      } 
    }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}


// $print .= "</div>
//             <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev' style='color:black;'>
//               <span class='carousel-control-prev-icon' aria-hidden='true' ><img src='global_assets/images/lamparinas/seta-left.png' width='32' /></span>
//               <span class='sr-only'>Previous</span>
//             </a>

//             <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next' style='color:black;'>
//               <span class='carousel-control-next-icon' aria-hidden='true'><img src='global_assets/images/lamparinas/seta-right.png' width='32' /></span>
//               <span class='sr-only'>Next</span>
//             </a>
//           </div>";

// print($print);


$print .= "  </table>";
 
// // Definimos o nome do arquivo que será exportado  
$arquivo = "LamparinasProdutos.xls"; 

// // Configurações header para forçar o download  
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// //header('Content-Type: application/vnd.ms-excel');
// header('Content-Disposition: attachment;filename="'.$arquivo.'"');
// header('Cache-Control: max-age=0');
// // Se for o IE9, isso talvez seja necessário
// header('Cache-Control: max-age=1');

// header("Content-type: text/html; charset=utf-8");

// Envia o conteúdo do arquivo  
echo $print;  
exit;

?>
