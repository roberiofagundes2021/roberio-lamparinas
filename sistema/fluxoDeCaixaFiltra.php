<?php

include_once("sessao.php");

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
function centroDeCusto($CCNome,$CCPrevisto,$cc2Previsto,$cc3Previsto,$CCRealizado,$cc2Realizado,$cc3Realizado,$planoDeContas,$teste1,$teste2)
{      
  $porc_cc_1  = 0.00;
  
  $cc2Previsto = ($cc2Previsto != "" ? $cc2Previsto : 0);
  $cc2Realizado = ($cc2Realizado != "" ? $cc2Realizado : 0);

  $cc3Previsto = ($cc3Previsto != "" ? $cc3Previsto : 0);
  $cc3Realizado = ($cc3Realizado != "" ? $cc3Realizado : 0);

  if($cc2Previsto != 0 || $cc3Previsto != 0)
  {
    if(($CCPrevisto+$cc2Previsto) != 0)
    {
      $porc_cc_1  =  ((($CCRealizado + $cc2Realizado) * 100) / ($CCPrevisto+$cc2Previsto));
    }

    if(($CCPrevisto+$cc2Previsto+$cc3Previsto) != 0)
    {
      $porc_cc_1  =  ((($CCRealizado + $cc2Realizado + $cc3Realizado) * 100) / ($CCPrevisto+$cc2Previsto+$cc3Previsto));
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
                        
                       
                        ".($teste1 ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                                            <div class='row'>
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Previsto) ."</span>
                                                                                              </div>
                                                                  
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Realizado) ."</span>
                                                                                              </div>
                                                                                            </div>
                                                                                          </div>" : "")."
                        
                        ".($teste2 ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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

//gera os centros de custo
function centroDeCustoMes($CCNome, $CCPrevisto, $cc2Previsto, $cc3Previsto, $CCRealizado, $cc2Realizado, $cc3Realizado, $planoDeContas, $teste1, $teste2)
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
                        
                        ".($teste1 ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                                            <div class='row'>
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Previsto) ."</span>
                                                                                              </div>
                                                                  
                                                                                              <div class='col-md-6'>
                                                                                                <span>". mostraValor($cc2Realizado) ."</span>
                                                                                              </div>
                                                                                            </div>
                                                                                          </div>" : "")."
                        
                        ".($teste2 ?  "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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
function planoDeContas($PL,$pl2, $pl3, $tipo, $teste1, $teste2)
{
  $Retorno    = array('HTML' => '','total_previsto' => 0.00, 'total_realizado' => 0.00,'total_previsto2' => 0.00, 'total_realizado2' => 0.00, 'total_previsto3' => 0.00, 'total_realizado3' => 0.00  );
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
        
        ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                            <div class='row'>
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Previsto'.$tipo]):"")."</span>
                                                                              </div>
                                                                  
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Realizado'.$tipo]):"")."</span>
                                                                              </div>
                                                                            </div>
                                                                          </div>" : "")."

        ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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

    if(isset($pl3)&&(is_array($pl3)))
        $vlrPl3 = $pl3[$i]['PL_Previsto'.$tipo];
    else
        $vlrPl3 = 0;
    
    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl2)  != 0)
    {
      $porc_cc_1  =  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl2) * 100) / 
                            ($PL[$i]['PL_Previsto'.$tipo]+$vlrPl2));
    }

    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl3)  != 0)
    {
      $porc_cc_1  =  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl3) * 100) / 
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
function planoDeContasMes($PL,$pl2, $pl3, $tipo, $teste1, $teste2)
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
        
        ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                            <div class='row'>
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Previsto'.$tipo]):"")."</span>
                                                                              </div>
                                                                  
                                                                              <div class='col-md-6'>
                                                                                <span>".(is_array($pl2)? mostraValor($pl2[$i]['PL_Realizado'.$tipo]):"")."</span>
                                                                              </div>
                                                                            </div>
                                                                          </div>" : "")."

        ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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

function retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro)
{        
  // echo $dataFiltro;
  // echo "/";

  // $dataFiltroInicioMes = '2021-10-01';
  // $dataFiltroFimMes = '2021-10-31';
  // $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes1;
  // $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes1;
    include('global_assets/php/conexao.php');
    $sql = "SELECT 
            CnCusId, 
            CnCusNome, 
            dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'E') as CC_PrevistoEntrada,  
            dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'E') as CC_RealizadoEntrada,  
            dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'S') as CC_PrevistoSaida,
            dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'S') as CC_RealizadoSaida,
            PlConId, 
            PlConNome,
            dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'E') as PL_PrevistoEntrada,  
            dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'E') as PL_RealizadoEntrada,  
            dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'S') as PL_PrevistoSaida,
            dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', 'S') as PL_RealizadoSaida
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
  
  
  // echo $ccFiltro."<br>".$sql."<br>".$plFiltro."<br>".print_r($_POST)."<br><br>";

  $result            = $conn->query($sql);
  $rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);

  $regAt = '';
  $cont = 0;

  if(count($rowCentroDeCustos) > 0){
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
    $sql_saldo_ini_p   = "select dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialPrevisto";
    $result_saldo_ini_p = $conn->query($sql_saldo_ini_p);
    $rowSaldoIni_p      = $result_saldo_ini_p->fetchAll(PDO::FETCH_ASSOC);
    
    //echo $sql_saldo_ini_p."<br>";
  
    //pega o saldo inicial realizado
    $sql_saldo_ini_r = "select dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialRealizado";
    $result_saldo_ini_r = $conn->query($sql_saldo_ini_r);
    $rowSaldoIni_r      = $result_saldo_ini_r->fetchAll(PDO::FETCH_ASSOC);
      
    $retorno = array('cc'=>$cc,'pl'=>$pl,'saldoIni_p'=>$rowSaldoIni_p,'saldoIni_r'=>$rowSaldoIni_r);
    
    unset($cc);
    unset($pl);
    unset($result);
    unset($rowCentroDeCustos);
    
    return $retorno;
  } else {
    return false;
  }
}

//-------------------------------------------------------------------------------------
// loop dos dias 
//-------------------------------------------------------------------------------------

if(!isset($_POST["cmbCentroDeCustos"]) || !isset($_POST["cmbPlanoContas"])){
   print("
            <div class='d-flex flex-column justify-content-center' style='height:300px'>
              <div class='flex-row justify-content-center'>
                <p style='text-align:center'>Relatório não pode ser gerado. Ainda não foi cadastrado nenhum Centro de Custos e Plano de contas.</p>
              </div>
            </div>
          ");
   return;
}
$numDias    = $_POST["quantityDays"];
$diaInicio  = $_POST["dayInitial"];
$diaFim     = $_POST["dayEnd"];
$dataInicio = $_POST["inputDateInitial"];
$dataFim    = $_POST["inputDateEnd"];
$typeFiltro   = $_POST["typeDate"];
$ccFiltro   = rtrim(implode(',', $_POST["cmbCentroDeCustos"]));
$plFiltro   = rtrim(implode(',', $_POST["cmbPlanoContas"]));

$print = "<div id='carouselExampleControls' class='carousel slide' data-ride='carousel'>
            <div class='carousel-inner'> ";

$teste = false;

if($typeFiltro == "D"){
  $mesArray = explode('-', $dataInicio);
  $anoData = $mesArray[0];
  $mesData = (int)$mesArray[1];

  if((($diaFim - $diaInicio) != 0) && (($diaFim - $diaInicio) != 1) && ($diaFim - $diaInicio) != 2){
    $diaFim = $diaFim - 1;
  }

  // print($diaFim - $diaInicio);

  for($i = $diaInicio;$i <= $diaFim;$i++)
  {  
    // if(($diaFim - $diaInicio) == 2){
    //   break;
    // }
    $teste1 = false;
    $teste2 = false;
      
    //limpa as variaveis
    if(isset($mes1))
    {
      unset($mes1);
      unset($cc1);
      unset($pl1);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
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

    if(isset($mes3))  
    {
      unset($mes3);
      unset($cc3);
      unset($pl3);
      unset($saldoIni_p3);
      unset($saldoIni_r3);
    }    
      
    $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;
    
    // $ultimo_dia = date("t", mktime(0,0,0,($i),'01',$anoData));
    $dataFiltroDiaInicio1 = $anoData.'-'.$mesData.'-'.$i;
    $dataFiltroDiaFim1 = $anoData.'-'.$mesData.'-'.$i;
// print($dataFiltroDiaInicio1);
// print("###");
// print($dataFiltroDiaFim1);
// print("/////");
    $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio1;
    $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim1;

    //Pea TODOS os dads do dia $i
    $mes1 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
    
   // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    
    $cc1           = $mes1['cc'];
    $pl1           = $mes1['pl'];
    $saldoIni_p1   = $mes1['saldoIni_p'][0]['SaldoInicialPrevisto'];
    $saldoIni_r1   = $mes1['saldoIni_r'][0]['SaldoInicialRealizado'];
    
    if(($i+1) <= $diaFim)
    {        
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
      
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio2 = $anoData.'-'.$mesData.'-'.($i+1);
      $dataFiltroDiaFim2 = $anoData.'-'.$mesData.'-'.($i+1);
// print($dataFiltroDiaInicio2);
// print("###");
// print($dataFiltroDiaFim2);
// print("/////");
      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio2;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim2;

     //Pea TODOS os dads do dia $i+1 se ele estiver na faixa de dias do filtro
      $mes2 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      
      $cc2           = $mes2['cc'];
      $pl2           = $mes2['pl'];
      $saldoIni_p2   = $mes2['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r2   = $mes2['saldoIni_r'][0]['SaldoInicialRealizado'];
      
      $teste1 = true;
    }

    if(($i+2) <= $diaFim)
    {
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
      
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio2 = $anoData.'-'.$mesData.'-'.($i+1);
      $dataFiltroDiaFim2 = $anoData.'-'.$mesData.'-'.($i+1);
// print($dataFiltroDiaInicio2);
// print("###");
// print($dataFiltroDiaFim2);
// print("/////");
      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio2;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim2;

     //Pea TODOS os dads do dia $i+1 se ele estiver na faixa de dias do filtro
      $mes3 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      
      $cc3           = $mes3['cc'];
      $pl3           = $mes3['pl'];
      $saldoIni_p3   = $mes3['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r3   = $mes3['saldoIni_r'][0]['SaldoInicialRealizado'];
      
      $teste2 = true;
    }
    
    //echo "<pre>".print_r($cc1)."</pre>";;
   // echo "<pre>".print_r($cc2)."</pre>";
   // echo "<pre>".print_r($pl1)."</pre>";
   // echo "<pre>".print_r($pl2)."</pre>";  exit();
  
    if(isset($pl_Entrada))
    {
      unset($pl_Entrada);
    }
  
    if(isset($pl_Saida))
    {
      unset($pl_Saida);
    }
    
    
    if(isset($saldoIni_p2))
      $saldoIni_p2 = $saldoIni_p2;
    else
      $saldoIni_p2 = 0;
    
    if(isset($saldoIni_r2))
      $saldoIni_r2 = $saldoIni_r2;
    else
      $saldoIni_r2 = 0;

    if(isset($saldoIni_r3))
      $saldoIni_r3 = $saldoIni_r3;
    else
      $saldoIni_r3 = 0;

    if($i != $diaInicio)
      $i += 1;
  
    /* Obs.: utf8_encode é para o servidor da AZURE. Localmente não precisaria, mas para o servidor sim. */  
    $print .= " <div class='carousel-item ".($i == $diaInicio ? " active":"")."'> 
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      <div class='card-body' >
                        <div class='row'>
                            
                          <div class='col-lg-4'>
                          </div>
  
                          <div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-right: 2px; '>
                            <span><strong>".str_pad($i, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataInicio))))."</strong></span>
                          </div>
  
                           ".($teste1 ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                            <span><strong>".str_pad($i+1, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataInicio ."-".str_pad($i+1, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>" : "")."

                          ".($teste2 ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                            <span><strong>".str_pad($i+2, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataInicio ."-".str_pad($i+2, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>" : "")."                        
                        </div>
                      </div>
                    </div>
                  </div>
  
                  <div class='row' style='margin-bottom: 1rem;'>
  
                    <!-- SALDO INICIAL -->
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <span><strong>Saldo Inicial</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($saldoIni_p1)."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($saldoIni_r1)."</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_p2)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_r2)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_p3)."</span>
                                                                    </div>
                                     
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_r3)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                          </div>
                        </div>
                    </div>
                  </div>
                  <!-- SALDO INICIAL -->";
  
    //------------------------------------------------------------------------------
    // limpa as variaveis que vao receber o plano de 
    // contas e centro de custo das funções    
    // <!--  usei essas variaveis para nao ter q fazer dois loops -->
    $print_ent = '';
    $print_sai = '';
    //==============================================================================
  
    //recebe entradas apenas
    $print_ent .= "<!-- ENTRADA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      
                      <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                        <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <strong>ENTRADA</strong>
                          </div> 
                          
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span><strong>Previsto</strong></span>
                              </div>
                              <div class='col-md-6'>
                                <span><strong>Realizado</strong></span>
                              </div>
                            </div>
                          </div>
                          
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                               </div>" : "")."
                          
                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."
  
                          <div class='dataOpeningBalance col-lg-2' style='text-align:center;'>
                            <div class='row'>
                              <div class='col-md-12'>
                                <span style='padding:0 px;margin:0px;'><strong style='padding:0px;margin:0px;'>Previsto/Realizado%</strong></span>
                              </div>
                            </div>
                          </div>
  
                        </div>
                      </div> ";
  
    //------------------------------------------------------------------------
    //recebe saidas apenas
    $print_sai .= "<!-- SAIDA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      
                      <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                        <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <strong>SAIDA</strong>
                          </div> 
                          
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span><strong>Previsto</strong></span>
                              </div>
                              <div class='col-md-6'>
                                <span><strong>Realizado</strong></span>
                              </div>
                            </div>
                          </div>
                          
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."
                          
                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span><strong>Previsto</strong></span>
                                                                </div>
                                                                <div class='col-md-6'>
                                                                  <span><strong>Realizado</strong></span>
                                                                </div>
                                                              </div>
                                                            </div>" : "")."
  
                          <div class='dataOpeningBalance col-lg-2' style='text-align:center;'>
                            <div class='row'>
                              <div class='col-md-12'>
                                <span><strong>Previsto/Realizado %</strong></span>
                              </div>
                            </div>
                          </div>
  
                        </div>
                      </div>";
  
      // totaliza as entradas e saidas
      $tot_previsto_entrada1  = 0;
      $tot_previsto_saida1    = 0;
      $tot_realizado_entrada1 = 0;
      $tot_realizado_saida1   = 0;      
      $tot_previsto_entrada2  = 0;
      $tot_previsto_saida2    = 0;
      $tot_realizado_entrada2 = 0;
      $tot_realizado_saida2   = 0;
      $tot_previsto_entrada3  = 0;
      $tot_previsto_saida3    = 0;
      $tot_realizado_entrada3 = 0;
      $tot_realizado_saida3   = 0;
  
      // totaliza as entradas e saidas
      $tot_geral_previsto1  = 0;
      $tot_geral_realizado1 = 0;
      $tot_geral_previsto2  = 0;
      $tot_geral_realizado2 = 0;
      $tot_geral_previsto3  = 0;
      $tot_geral_realizado3 = 0;
  
      $tot_previsto1 = 0;
      $tot_realizado1 = 0;
      $tot_previsto2 = 0;
      $tot_realizado2 = 0;
      $tot_previsto3 = 0;
      $tot_realizado3 = 0;
  
    if(isset($cc1) && (!empty($cc1)))
    {
      //var_dump($cc1);
      foreach($cc1 as $cCusto)
      {
        if(isset($pl_Entrada))
          unset($pl_Entrada);
  
        if(isset($pl_Saida))
          unset($pl_Saida);
  
        //gera o html de plano de contas das entradas e soma os totais
        $pl_Entrada = planoDeContas($pl1[$cCusto['CnCusId']],($teste1)?$pl2[$cCusto['CnCusId']]:"", ($teste2)?$pl3[$cCusto['CnCusId']]:"","Entrada", $teste1, $teste2);
        //gera o html de plano de contas das saidas e soma os totais
        $pl_Saida = planoDeContas($pl1[$cCusto['CnCusId']],($teste1)?$pl2[$cCusto['CnCusId']]:"", ($teste2)?$pl3[$cCusto['CnCusId']]:"","Saida", $teste1, $teste2);
        
        //gera o html dos centros de custo das entradas
        //concatena com o htm gerado pelo plano de contas
        $print_ent .= centroDeCusto($cCusto['CnCusNome'],
                                      $cCusto['CC_PrevistoEntrada'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoEntrada']:"",
                                      $cCusto['CC_RealizadoEntrada'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoEntrada']:"",
                                      $pl_Entrada['HTML'], $teste1, $teste2);                  
  
        //gera o html dos centros de custo das saidas
        //concatena com o htm gerado pelo plano de contas
        $print_sai .= centroDeCusto($cCusto['CnCusNome'],
                                      $cCusto['CC_PrevistoSaida'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoSaida']:"",
                                      $cCusto['CC_RealizadoSaida'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoSaida']:"",
                                      $pl_Saida['HTML'], $teste1, $teste2);
  
        //soma os totais dos centro de custo das entradas
        $tot_entrada_prev_cc  = $cCusto['CC_PrevistoEntrada'];
        $tot_entrada_real_cc  = $cCusto['CC_RealizadoEntrada'];
        $tot_entrada_prev_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:0;
        $tot_entrada_real_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:0;
        $tot_entrada_prev_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoEntrada']:0;
        $tot_entrada_real_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoEntrada']:0;
  
        //soma os totais dos planos de contas das saidas com o centro de custo atual
        $tot_saida_prev_cc  = $cCusto['CC_PrevistoSaida'];
        $tot_saida_real_cc  = $cCusto['CC_RealizadoSaida'];
        $tot_saida_prev_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:0;
        $tot_saida_real_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:0;
        $tot_saida_prev_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoSaida']:0;
        $tot_saida_real_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoSaida']:0;
  
        // totaliza as entradas e saidas
        $tot_previsto_entrada1  += $tot_entrada_prev_cc;
        $tot_previsto_saida1    += $tot_saida_prev_cc;
        $tot_realizado_entrada1 += $tot_entrada_real_cc;
        $tot_realizado_saida1   += $tot_saida_real_cc;
        $tot_previsto_entrada2  += $tot_entrada_prev_cc2;
        $tot_previsto_saida2    += $tot_saida_prev_cc2;
        $tot_realizado_entrada2 += $tot_entrada_real_cc2;
        $tot_realizado_saida2   += $tot_saida_real_cc2;
        $tot_previsto_entrada3  += $tot_entrada_prev_cc2;
        $tot_previsto_saida3    += $tot_saida_prev_cc2;
        $tot_realizado_entrada3 += $tot_entrada_real_cc2;
        $tot_realizado_saida3   += $tot_saida_real_cc2;
  
        // totaliza as entradas e saidas
        $tot_previsto1  += ($tot_entrada_prev_cc - $tot_saida_prev_cc);
        $tot_realizado1 += ($tot_entrada_real_cc - $tot_saida_real_cc);
        
        if ($teste1)
        {
          $tot_previsto2  += ($tot_entrada_prev_cc2 - $tot_saida_prev_cc2);
          $tot_realizado2 += ($tot_entrada_real_cc2 - $tot_saida_real_cc2);
        }
        else 
        {        
          $tot_previsto2  += 0;
          $tot_realizado2 += 0;  
        }

        if ($teste2)
        {
          $tot_previsto3  += ($tot_entrada_prev_cc3 - $tot_saida_prev_cc3);
          $tot_realizado3 += ($tot_entrada_real_cc3 - $tot_saida_real_cc3);
        }
        else 
        {        
          $tot_previsto3  += 0;
          $tot_realizado3 += 0;  
        }
      }
  
      $tot_geral_previsto1 = $tot_previsto1 + $saldoIni_p1;
      $tot_geral_realizado1 = $tot_realizado1 + $saldoIni_r1;
  
      $tot_geral_previsto2 = $tot_previsto2 + $saldoIni_p2;
      $tot_geral_realizado2 = $tot_realizado2 + $saldoIni_r2;

     if($teste2){
      $tot_geral_previsto3 = $tot_previsto3 + $saldoIni_p3;
      $tot_geral_realizado3 = $tot_realizado3 + $saldoIni_r3;
     }
    }
    //------------------------------------------------------------------------
    //recebe entradas apenas
    $print_ent .= "<!-- TOTAL ENTRADA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style=''>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>TOTAL</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_previsto_entrada1) ."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_realizado_entrada1) ."</span>
                                </div>
                              </div>
                            </div>
  
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_previsto_entrada2) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_realizado_entrada2) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_previsto_entrada3) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_realizado_entrada3) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                      </div>
                      
                      </div>
                      </div>
                  <!-- TOTAL ENTRADA -->
                  <!-- ENTRADA -->";
  
    //------------------------------------------------------------------------
    //recebe saidas apenas
    $print_sai .= "<!-- TOTAL SAIDA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style=''>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>TOTAL</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_previsto_saida1) ."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_realizado_saida1) ."</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_previsto_saida2) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_realizado_saida2) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
                            
                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_previsto_saida3) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_realizado_saida3) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."  
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    
                        </div>
                    </div>
                  <!-- TOTAL SAIDA -->
                  <!-- SAIDA -->";
  
    //----------------------------------------------------------------------
    //junta tudo no $print principal
    $print .= $print_ent . $print_sai. "
        
                  <!-- SALDO FINAL -->
                  <div class='row' style='margin-top: 1rem;'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                         <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>SALDO FINAL</strong></span>
                          </div>
  
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>". mostraValor($tot_geral_previsto1)  ."</span>
                              </div>
  
                              <div class='col-md-6'>
                                <span>". mostraValor($tot_geral_realizado1)  ."</span>
                              </div>
                            </div>
                          </div>
                            
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span>". mostraValor($tot_geral_previsto2) ."</span>
                                                                  </div>
                                    
                                                                  <div class='col-md-6'>
                                                                    <span>". mostraValor($tot_geral_realizado2)  ."</span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."

                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span>". mostraValor($tot_geral_previsto3) ."</span>
                                                                </div>
                                  
                                                                <div class='col-md-6'>
                                                                  <span>". mostraValor($tot_geral_realizado3)  ."</span>
                                                                </div>
                                                              </div>
                                                            </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span> </span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span> </span>
                                </div>
                              </div>
                            </div>
                            
                          </div>
                        </div>
                    </div>
                    </div>
                    
                  
                  <!-- SALDO FINAL -->
  
                  <!-- SALDO FINAL -->
                  <div class='row' style='margin-top: 2rem;'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                          <div class='row'>
                            <div class='col-lg-12' style='background: #607D8B; color:white; line-height: 3rem; box-sizing:border-box'>
                              <span><strong>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA): </strong></span>
                            </div>
                          </div>
  
                          <div class='row col-lg-12' style='background: #fff; line-height: 3rem; box-sizing:border-box'>";
    if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0 && isset($tot_geral_previsto3) && $tot_geral_previsto3 != 0)
    {
      $print .= "<span>".mostraValor(((($tot_geral_realizado1+$tot_geral_realizado2+$tot_geral_realizado3) /($tot_geral_previsto1+$tot_geral_previsto2+$tot_geral_previsto3)) * 100)) ."%</span>";
    }
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
  
    $print .= "           <!--span>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</span-->
                          </div>
                        </div>
                    </div>
                  </div>
                  <!-- SALDO FINAL --> 
                </div>  
                ";
    
    if(($i+1) <= $diaFim)
    {
        $i++;
    }  
  }
} else {
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $data1 = new DateTime( $dataInicio );
  $data2 = new DateTime( $dataFim );

  $intervaloMeses = $data1->diff( $data2 )->m;
  if($intervaloMeses == 0){
    $intervaloMeses = 1;
  }

  $mesInicioArray = explode('-', $dataInicio);
  $anoTela = $mesInicioArray[0];
  $mesInicio = (int)$mesInicioArray[1];

  $mesFimArray = explode('-', $dataFim);
  $mesFinal = (int)$mesFimArray[1];
  

  // $intervaloMeses = $mesFinal - $mesInicio;
  // $intervaloMeses = (($intervaloMeses + 2) / 3);

  $pagina = 0;
  $numeroPaginasCont = 0;
  // for($i = $mesInicio; $i <= $mesFinal; $i++)
  // {  

  if((($mesFinal - $mesInicio) != 0) && (($mesFinal - $mesInicio) != 1) && ($mesFinal - $mesInicio) != 2){
    // print($diaFim - $diaInicio);
    $mesFinal = $mesFinal - 1;
  }
  
  // print($diaFim - $diaInicio);
  
  for($i = $mesInicio; $i <= $mesFinal; $i++)
  {

    $teste1 = false;
    $teste2 = false;
    
    //limpa as variaveis
    if(isset($mes1))
    {
      unset($mes1);
      unset($cc1);
      unset($pl1);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
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

    if(isset($mes3))  
    {
      unset($mes3);
      unset($cc3);
      unset($pl3);
      unset($saldoIni_p3);
      unset($saldoIni_r3);
    }   
    
    // if($i+$pagina == 13){
    //   break;
    // }
    $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;

    // $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$anoTela));

    $ultimo_dia = date("t", mktime(0,0,0,($i),'01',$anoTela));
    $dataFiltroDiaInicioMes1 = $anoTela.'-'.($i).'-01';
    $dataFiltroDiaFimMes1 = $anoTela.'-'.($i).'-'.$ultimo_dia;

    // print($dataFiltroDiaInicioMes1);
    // print("///////////////////////");
    // print($dataFiltroDiaFimMes1);
    // print("///////////////////////");
    $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes1;
    $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes1;
// print($dataFiltro);
// print(" | ");
    // if($i == 1){
    //   $dataFiltro = $dataInicio;
    // } else if($i == 2){
    //   $dataFiltro = $dataFim;
    // }


    ///////////////////////////////////////Dados mês 1
    //Pega TODOS os dados do mês $i
    $mes1 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
    // if($mes1 == false){
    //   break;
    // }
   // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    
    $cc1           = $mes1['cc'];
    $pl1           = $mes1['pl'];
    $saldoIni_p1   = $mes1['saldoIni_p'][0]['SaldoInicialPrevisto'];
    $saldoIni_r1   = $mes1['saldoIni_r'][0]['SaldoInicialRealizado'];
    
    ////////////////////////////////////////Dados mês 2
    if(($i+1) <= $mesFinal)
    {        
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.(($i+1));
      
      $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoTela));
      $dataFiltroDiaInicioMes2 = $anoTela.'-'.($i+1).'-01';
      $dataFiltroDiaFimMes2 = $anoTela.'-'.($i+1).'-'.$ultimo_dia;

      // print($dataFiltroDiaInicioMes2);
      // print("##############");
      // print($dataFiltroDiaFimMes2);
      // print("##############");
      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes2;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes2;
      
     //Pea TODOS os dads do mês (($i+1) + $pagina) se ele estiver na faixa de meses do filtro
      $mes2 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      
      $cc2           = $mes2['cc'];
      $pl2           = $mes2['pl'];
      $saldoIni_p2   = $mes2['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r2   = $mes2['saldoIni_r'][0]['SaldoInicialRealizado'];
      
      $teste1 = true;
    }

    if(($i+2) <= $mesFinal)
    {        
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.(($i+2));
      
      $ultimo_dia = date("t", mktime(0,0,0,$i+2,'01',$anoTela));
      $dataFiltroDiaInicioMes3 = $anoTela.'-'.($i+2).'-01';
      $dataFiltroDiaFimMes3 = $anoTela.'-'.($i+2).'-'.$ultimo_dia;

      // print($dataFiltroDiaInicioMes3);
      // print("$$$$$$$$$$$$$");
      // print($dataFiltroDiaFimMes3);
      // print("$$$$$$$$$$$$$");
      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes3;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes3;
      
     //Pea TODOS os dads do mês (($i+1) + $pagina) se ele estiver na faixa de meses do filtro
      $mes3 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      
      $cc3           = $mes3['cc'];
      $pl3           = $mes3['pl'];
      $saldoIni_p3   = $mes3['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r3   = $mes3['saldoIni_r'][0]['SaldoInicialRealizado'];
      
      $teste2 = true;
    }
    
    //echo "<pre>".print_r($cc1)."</pre>";;
   // echo "<pre>".print_r($cc2)."</pre>";
   // echo "<pre>".print_r($pl1)."</pre>";
   // echo "<pre>".print_r($pl2)."</pre>";  exit();
  
    if(isset($pl_Entrada))
    {
      unset($pl_Entrada);
    }
  
    if(isset($pl_Saida))
    {
      unset($pl_Saida);
    }
    
    
    if(isset($saldoIni_p2))
      $saldoIni_p2 = $saldoIni_p2;
    else
      $saldoIni_p2 = 0;
    
    if(isset($saldoIni_r2))
      $saldoIni_r2 = $saldoIni_r2;
    else
      $saldoIni_r2 = 0;

    // if(isset($saldoIni_r3))
    //   $saldoIni_r3 = $saldoIni_r3;
    // else
    //   $saldoIni_r3 = 0;

    // if($i != $diaInicio)
    //   $i += 1;
    if($i != $mesInicio)
      $i += 1;
  
    /* Obs.: utf8_encode é para o servidor da AZURE. Localmente não precisaria, mas para o servidor sim. */  
    $print .= " <div class='carousel-item ".($i == $mesInicio ? " active":"")."'> 
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      <div class='card-body' >
                        <div class='row'>
                            
                          <div class='col-lg-4'>
                          </div>
  
                          <div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-right: 2px; '>
                            <span><strong>".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoTela."-".str_pad($i, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>
                          
  
                          ".($teste1 ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                            <span><strong>". utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoTela."-".str_pad($i+1, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>" : "")."

                          ".($teste2 ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                            <span><strong>". utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoTela."-".str_pad($i+2, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>" : "")."

                        </div>
                      </div>
                    </div>
                  </div>
  
                  <div class='row' style='margin-bottom: 1rem;'>
  
                    <!-- SALDO INICIAL -->
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <span><strong>Saldo Inicial</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($saldoIni_p1)."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($saldoIni_r1)."</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_p2)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_r2)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_p3)."</span>
                                                                    </div>
                                     
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($saldoIni_r3)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                          </div>
                        </div>
                    </div>
                  </div>
                  <!-- SALDO INICIAL -->";
    
    //------------------------------------------------------------------------------
    // limpa as variaveis que vao receber o plano de 
    // contas e centro de custo das funções    
    // <!--  usei essas variaveis para nao ter q fazer dois loops -->
    $print_ent = '';
    $print_sai = '';
    //==============================================================================
  
    //recebe entradas apenas
    $print_ent .= "<!-- ENTRADA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      
                      <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                        <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <strong>ENTRADA</strong>
                          </div> 
                          
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span><strong>Previsto</strong></span>
                              </div>
                              <div class='col-md-6'>
                                <span><strong>Realizado</strong></span>
                              </div>
                            </div>
                          </div>
                          
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                               </div>" : "")."
                          
                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."
  
                          <div class='dataOpeningBalance col-lg-2' style='text-align:center;'>
                            <div class='row'>
                              <div class='col-md-12'>
                                <span style='padding:0 px;margin:0px;'><strong style='padding:0px;margin:0px;'>Previsto/Realizado%</strong></span>
                              </div>
                            </div>
                          </div>
  
                        </div>
                      </div> ";
    
    //------------------------------------------------------------------------
    //recebe saidas apenas
    $print_sai .= "<!-- SAIDA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                      
                      <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                        <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                              <strong>SAIDA</strong>
                          </div> 
                          
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span><strong>Previsto</strong></span>
                              </div>
                              <div class='col-md-6'>
                                <span><strong>Realizado</strong></span>
                              </div>
                            </div>
                          </div>
                          
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."
                          
                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span><strong>Previsto</strong></span>
                                                                </div>
                                                                <div class='col-md-6'>
                                                                  <span><strong>Realizado</strong></span>
                                                                </div>
                                                              </div>
                                                            </div>" : "")."
  
                          <div class='dataOpeningBalance col-lg-2' style='text-align:center;'>
                            <div class='row'>
                              <div class='col-md-12'>
                                <span><strong>Previsto/Realizado %</strong></span>
                              </div>
                            </div>
                          </div>
  
                        </div>
                      </div>";

    //Dados do numero de meses por pagina
    $localizacaoPagina = [
      "i"      => $i,
      "pagina" => $pagina,
      "mesFinal"    => $mesFinal
    ];

    // totaliza as entradas e saidas
    $tot_previsto_entrada1  = 0;
    $tot_previsto_saida1    = 0;
    $tot_realizado_entrada1 = 0;
    $tot_realizado_saida1   = 0;      
    $tot_previsto_entrada2  = 0;
    $tot_previsto_saida2    = 0;
    $tot_realizado_entrada2 = 0;
    $tot_realizado_saida2   = 0;
    /////////////////////////////////
    $tot_previsto_entrada3  = 0;
    $tot_previsto_saida3    = 0;
    $tot_realizado_entrada3 = 0;
    $tot_realizado_saida3   = 0;
    /////////////////////////////////

    // totaliza as entradas e saidas
    $tot_geral_previsto1  = 0;
    $tot_geral_realizado1 = 0;
    $tot_geral_previsto2  = 0;
    $tot_geral_realizado2 = 0;
    /////////////////////////////////
    $tot_geral_previsto3  = 0;
    $tot_geral_realizado3 = 0;
    /////////////////////////////////

    $tot_previsto1 = 0;
    $tot_realizado1 = 0;
    $tot_previsto2 = 0;
    $tot_realizado2 = 0;
    /////////////////////////////////
    $tot_previsto3 = 0;
    $tot_realizado3 = 0;
    /////////////////////////////////
  
    if(isset($cc1) && (!empty($cc1)))
    {
      //var_dump($cc1);
      foreach($cc1 as $cCusto)
      {
        if(isset($pl_Entrada))
          unset($pl_Entrada);
  
        if(isset($pl_Saida))
          unset($pl_Saida);
  
        //gera o html de plano de contas das entradas e soma os totais
        $pl_Entrada = planoDeContasMes($pl1[$cCusto['CnCusId']],($teste1)?$pl2[$cCusto['CnCusId']]:"", ($teste2)?$pl3[$cCusto['CnCusId']]:"", "Entrada", $teste1, $teste2);
        
        //gera o html de plano de contas das saidas e soma os totais
        $pl_Saida = planoDeContasMes($pl1[$cCusto['CnCusId']],($teste1)?$pl2[$cCusto['CnCusId']]:"", ($teste2)?$pl3[$cCusto['CnCusId']]:"", "Saida", $teste1, $teste2);
        
        //gera o html dos centros de custo das entradas
        //concatena com o htm gerado pelo plano de contas
        $print_ent .= centroDeCustoMes($cCusto['CnCusNome'],
                                      $cCusto['CC_PrevistoEntrada'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoEntrada']:"",
                                      $cCusto['CC_RealizadoEntrada'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoEntrada']:"",
                                      $pl_Entrada['HTML'], $teste1, $teste2);                  
  
        //gera o html dos centros de custo das saidas
        //concatena com o htm gerado pelo plano de contas
        $print_sai .= centroDeCustoMes($cCusto['CnCusNome'],
                                      $cCusto['CC_PrevistoSaida'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoSaida']:"",
                                      $cCusto['CC_RealizadoSaida'],
                                      ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:"",
                                      ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoSaida']:"",
                                      $pl_Saida['HTML'], $teste1, $teste2);
  
        //soma os totais dos centro de custo das entradas
        $tot_entrada_prev_cc  = $cCusto['CC_PrevistoEntrada'];
        $tot_entrada_real_cc  = $cCusto['CC_RealizadoEntrada'];
        $tot_entrada_prev_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:0;
        $tot_entrada_real_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:0;
        //////////////////////////////////
        $tot_entrada_prev_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoEntrada']:0;
        $tot_entrada_real_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoEntrada']:0;
        //////////////////////////////////
  
        //soma os totais dos planos de contas das saidas com o centro de custo atual
        $tot_saida_prev_cc  = $cCusto['CC_PrevistoSaida'];
        $tot_saida_real_cc  = $cCusto['CC_RealizadoSaida'];
        $tot_saida_prev_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:0;
        $tot_saida_real_cc2 = ($teste1)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:0;
        //////////////////////////////////
        $tot_saida_prev_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_PrevistoSaida']:0;
        $tot_saida_real_cc3 = ($teste2)?$cc3[$cCusto['CnCusId']]['CC_RealizadoSaida']:0;
        //////////////////////////////////
  
        // totaliza as entradas e saidas
        $tot_previsto_entrada1  += $tot_entrada_prev_cc;
        $tot_previsto_saida1    += $tot_saida_prev_cc;
        $tot_realizado_entrada1 += $tot_entrada_real_cc;
        $tot_realizado_saida1   += $tot_saida_real_cc;
        $tot_previsto_entrada2  += $tot_entrada_prev_cc2;
        $tot_previsto_saida2    += $tot_saida_prev_cc2;
        $tot_realizado_entrada2 += $tot_entrada_real_cc2;
        $tot_realizado_saida2   += $tot_saida_real_cc2;
        ///////////////////////////////////
        $tot_previsto_entrada3  += $tot_entrada_prev_cc3;
        $tot_previsto_saida3    += $tot_saida_prev_cc3;
        $tot_realizado_entrada3 += $tot_entrada_real_cc3;
        $tot_realizado_saida3   += $tot_saida_real_cc3;
        ///////////////////////////////////

        // totaliza as entradas e saidas
        $tot_previsto1  += ($tot_entrada_prev_cc - $tot_saida_prev_cc);
        $tot_realizado1 += ($tot_entrada_real_cc - $tot_saida_real_cc);
        
        if ($teste1)
        {
          $tot_previsto2  += ($tot_entrada_prev_cc2 - $tot_saida_prev_cc2);
          $tot_realizado2 += ($tot_entrada_real_cc2 - $tot_saida_real_cc2);
        }
        else 
        {        
          $tot_previsto2  += 0;
          $tot_realizado2 += 0;  
        }
        //////////////////////////////////
        if ($teste2)
        {
          $tot_previsto3  += ($tot_entrada_prev_cc3 - $tot_saida_prev_cc3);
          $tot_realizado3 += ($tot_entrada_real_cc3 - $tot_saida_real_cc3);
        }
        else 
        {        
          $tot_previsto3  += 0;
          $tot_realizado3 += 0;  
        }
        //////////////////////////////////
      }
  
      $tot_geral_previsto1 = $tot_previsto1 + $saldoIni_p1;
      $tot_geral_realizado1 = $tot_realizado1 + $saldoIni_r1;
  
      $tot_geral_previsto2 = $tot_previsto2 + $saldoIni_p2;
      $tot_geral_realizado2 = $tot_realizado2 + $saldoIni_r2;

      if(($i+2) <= $mesFinal)
      {  
        ///////////////////////////////////
        $tot_geral_previsto3 = $tot_previsto3 + $saldoIni_p3;
        $tot_geral_realizado3 = $tot_realizado3 + $saldoIni_r3;
        ///////////////////////////////////
      }
    }
    //------------------------------------------------------------------------
    //recebe entradas apenas
    $print_ent .= "<!-- TOTAL ENTRADA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style=''>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>TOTAL</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_previsto_entrada1) ."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_realizado_entrada1) ."</span>
                                </div>
                              </div>
                            </div>
  
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_previsto_entrada2) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_realizado_entrada2) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_previsto_entrada3) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_realizado_entrada3) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
                      </div>
                      
                      </div>
                      </div>
                  <!-- TOTAL ENTRADA -->
                  <!-- ENTRADA -->";
  
    //------------------------------------------------------------------------
    //recebe saidas apenas
    $print_sai .= "<!-- TOTAL SAIDA -->
                  <div class='row'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style=''>
                          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                            <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>TOTAL</strong></span>
                            </div>
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_previsto_saida1) ."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($tot_realizado_saida1) ."</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_previsto_saida2) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste1 ? mostraValor($tot_realizado_saida2) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
                            
                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_previsto_saida3) :"") ."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".($teste2 ? mostraValor($tot_realizado_saida3) :"") ."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    
                        </div>
                    </div>
                  <!-- TOTAL SAIDA -->
                  <!-- SAIDA -->";
  
    //----------------------------------------------------------------------
    //junta tudo no $print principal
    $print .= $print_ent . $print_sai. "
        
                  <!-- SALDO FINAL -->
                  <div class='row' style='margin-top: 1rem;'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                         <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                          <div class='col-lg-4' style='border-right: 1px dotted black;'>
                            <span><strong>SALDO FINAL</strong></span>
                          </div>
  
                          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>". mostraValor($tot_geral_previsto1)  ."</span>
                              </div>
  
                              <div class='col-md-6'>
                                <span>". mostraValor($tot_geral_realizado1)  ."</span>
                              </div>
                            </div>
                          </div>
                            
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span>". mostraValor($tot_geral_previsto2) ."</span>
                                                                  </div>
                                    
                                                                  <div class='col-md-6'>
                                                                    <span>". mostraValor($tot_geral_realizado2)  ."</span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."

                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span>". mostraValor($tot_geral_previsto3) ."</span>
                                                                </div>
                                  
                                                                <div class='col-md-6'>
                                                                  <span>". mostraValor($tot_geral_realizado3)  ."</span>
                                                                </div>
                                                              </div>
                                                            </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                              <div class='row'>
                                <div class='col-md-6'>
                                  <span> </span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span> </span>
                                </div>
                              </div>
                            </div>
                            
                          </div>
                        </div>
                    </div>
                    </div>
                    
                  
                  <!-- SALDO FINAL -->
  
                  <!-- SALDO FINAL -->
                  <div class='row' style='margin-top: 2rem;'>
                    <div class='col-lg-12'>
                      <!-- Basic responsive configuration -->
                        <div class='card-body' style='padding-top: 0;'>
                          <div class='row'>
                            <div class='col-lg-12' style='background: #607D8B; color:white; line-height: 3rem; box-sizing:border-box'>
                              <span><strong>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA): </strong></span>
                            </div>
                          </div>
  
                          <div class='row col-lg-12' style='background: #fff; line-height: 3rem; box-sizing:border-box'>";
    if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0 && isset($tot_geral_previsto3) && $tot_geral_previsto3 != 0)
    {
      $print .= "<span>".mostraValor(((($tot_geral_realizado1+$tot_geral_realizado2+$tot_geral_realizado3) /($tot_geral_previsto1+$tot_geral_previsto2+$tot_geral_previsto3)) * 100)) ."%</span>";
    }
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
  
    $print .= "           <!--span>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</span-->
                          </div>
                        </div>
                    </div>
                  </div>
                  <!-- SALDO FINAL --> 
                </div>  
                ";

    // $pagina += 2;
    // $numeroPaginasCont++;
    
    // print($intervaloMeses);
    // $intervaloMeses = $intervaloMeses - 0.3333333333333;
    // print($intervaloMeses); // Falta resolver o problema de ao selecionar 3 meses aparece 1 a mais / Numero inteiro vindo com float
    //   print("-");
    //   print($numeroPaginasCont);
    // if("".ceil($intervaloMeses)."" == "".(int)$numeroPaginasCont.""){
    //   // print("final - ".$numeroPaginasCont."---".$intervaloMeses);
    //   break;
    // }
    if(($i+1) <= $mesFinal)
    {
        $i++;
    }  
  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}


$print .= "</div>
            <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev' style='color:black;'>
              <span class='carousel-control-prev-icon' aria-hidden='true' ><img src='global_assets/images/lamparinas/seta-left.png' width='32' /></span>
              <span class='sr-only'>Previous</span>
            </a>

            <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next' style='color:black;'>
              <span class='carousel-control-next-icon' aria-hidden='true'><img src='global_assets/images/lamparinas/seta-right.png' width='32' /></span>
              <span class='sr-only'>Next</span>
            </a>
          </div>";



print($print);