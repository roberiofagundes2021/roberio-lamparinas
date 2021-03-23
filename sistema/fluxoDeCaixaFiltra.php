<?php

include_once("sessao.php");
/* 
  --------------- $_POST -------------
  ["typeDate"]=> "D"/"M"
  ["dateInitial"]=> string(10) "2021-03-05"
  ["dateEnd"]=> string(10) "2021-03-17"
  ["cmbCentroDeCustos"]=> string(1) "4"
  ["cmbPlanoContas"]=>string(2) "78"
*/

//gera os centros de custo
function centroDeCusto($CCNome,$CCPrevisto,$CC2Previsto,$CCRealizado,$CC2Realizado,$planoDeContas)
{      
  $porc_cc_1  = 0.00;
  
  $CC2Previsto = ($CC2Previsto != "" ? number_format($CC2Previsto, 2, '.', ''):"");
  $CC2Realizado = ($CC2Realizado != "" ? number_format($CC2Realizado, 2, '.', ''):"");

  if($CC2Previsto != "")
  {
    if(($CCPrevisto+$CC2Previsto) != 0)
    {
      $porc_cc_1  =  ((($CCRealizado + $CC2Realizado) * 100) / ($CCPrevisto+$CC2Previsto));
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

                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>".number_format($CCPrevisto, 2, '.', '') ."</span>
                            </div>

                            <div class='col-md-6'>
                              <span>".number_format($CCRealizado, 2, '.', '')."</span>
                            </div>
                          </div>
                        </div>
                        
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>". $CC2Previsto ."</span>
                            </div>

                            <div class='col-md-6'>
                              <span>". $CC2Realizado ."</span>
                            </div>
                          </div>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-12'>
                              <span>".number_format($porc_cc_1, 2, '.', '') ."%</span>
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
function planoDeContas($PL,$PL2,$tipo)
{
  $Retorno    = array('HTML' => '','total_previsto' => 0.00, 'total_realizado' => 0.00,'total_previsto2' => 0.00, 'total_realizado2' => 0.00  );
  $porc_cc_1  = 0;

  for($i = 0;$i < count($PL,0);$i++)
  {
    $Retorno['HTML'] .= "<div class='card-body' style='padding-top: 0; padding-bottom: 0'>
      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>

        <div class='col-lg-1' style='border-right: 0'>          
        </div>

        <div class='col-lg-3' style='border-right: 1px dotted black;'>
          <span>". $PL[$i]['PlConNome'] ."</span>
        </div>

        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
          <div class='row'>
            <div class='col-md-6'>
              <span>".number_format($PL[$i]['PL_Previsto'.$tipo], 2, '.', '')."</span>
            </div>

            <div class='col-md-6'>
              <span>".number_format($PL[$i]['PL_Realizado'.$tipo], 2, '.', '')."</span>
            </div>
          </div>
        </div>
        
        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
          <div class='row'>
            <div class='col-md-6'>
              <span>".(is_array($PL2)? number_format($PL2[$i]['PL_Previsto'.$tipo], 2, '.', ''):"")."</span>
            </div>

            <div class='col-md-6'>
              <span>".(is_array($PL2)?number_format($PL2[$i]['PL_Realizado'.$tipo], 2, '.', ''):"")."</span>
            </div>
          </div>
        </div>";    

    //totaliza os valores
    $Retorno['total_previsto']  += $PL[$i]['PL_Previsto'.$tipo];
    $Retorno['total_realizado'] += $PL[$i]['PL_Realizado'.$tipo];  
    
    $Retorno['total_previsto2']  += (is_array($PL2))?$PL2[$i]['PL_Previsto'.$tipo]:0;
    $Retorno['total_realizado2'] += (is_array($PL2))?$PL2[$i]['PL_Realizado'.$tipo]:0; 

    //calculo de porcentagem dos previstos e realizados
    if(isset($PL2)&&(is_array($PL2)))
        $vlrPl2 = $PL2[$i]['PL_Previsto'.$tipo];
    else
        $vlrPl2 = 0;
    
    if(($PL[$i]['PL_Previsto'.$tipo] + $vlrPl2)  != 0)
    {
      $porc_cc_1  =  ((($PL[$i]['PL_Realizado'.$tipo] + $vlrPl2) * 100) / 
                            ($PL[$i]['PL_Previsto'.$tipo]+$vlrPl2));
    }    

    $Retorno['HTML'] .= "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
          <div class='row'>
            <div class='col-md-12'>
              <span>".number_format($porc_cc_1, 2, '.', '') ."%</span>
            </div>
          </div>
        </div>

      </div>
    </div>";
  }

  return $Retorno;
}

function retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro)
{        
    include('global_assets/php/conexao.php');
    $sql = "SELECT 
            CnCusId, 
            CnCusNome, 
            dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$dataFiltro."', 'E') as CC_PrevistoEntrada,  
            dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$dataFiltro."', 'E') as CC_RealizadoEntrada,  
            dbo.fnCentroCustoPrevisto(CnCusUnidade, CnCusId, '".$dataFiltro."', 'S') as CC_PrevistoSaida,
            dbo.fnCentroCustoRealizado(CnCusUnidade, CnCusId, '".$dataFiltro."', 'S') as CC_RealizadoSaida,
            PlConId, 
            PlConNome,
            dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$dataFiltro."', 'E') as PL_PrevistoEntrada,  
            dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$dataFiltro."', 'E') as PL_RealizadoEntrada,  
            dbo.fnPlanoContasPrevisto(CnCusUnidade, PlConId, '".$dataFiltro."', 'S') as PL_PrevistoSaida,
            dbo.fnPlanoContasRealizado(CnCusUnidade, PlConId, '".$dataFiltro."', 'S') as PL_RealizadoSaida
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
  
  
  //echo $ccFiltro."<br>".$sql."<br>".$plFiltro."<br>".print_r($_POST);exit;

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

  //pega o saldo inicial presumido
  $sql_saldo_ini_p   = "select dbo.fnFluxoCaixaSaldoInicialPrevisto(1,'".$dataFiltro."') as SaldoInicialPrevisto";
  $result_saldo_ini_p = $conn->query($sql_saldo_ini_p);
  $rowSaldoIni_p      = $result_saldo_ini_p->fetchAll(PDO::FETCH_ASSOC);

  //pega o saldo inicial realizado
  $sql_saldo_ini_r = "select dbo.fnFluxoCaixaSaldoInicialRealizado(1,'".$dataFiltro."') as SaldoInicialRealizado";
  $result_saldo_ini_r = $conn->query($sql_saldo_ini_r);
  $rowSaldoIni_r      = $result_saldo_ini_r->fetchAll(PDO::FETCH_ASSOC);
    
  $retorno = array('cc'=>$cc,'pl'=>$pl,'saldoIni_p'=>$rowSaldoIni_p,'saldoIni_r'=>$rowSaldoIni_r);
  
  unset($cc);
  unset($pl);
  unset($result);
  unset($rowCentroDeCustos);
  
  return $retorno;
}

//-------------------------------------------------------------------------------------
// loop dos dias 
//-------------------------------------------------------------------------------------

$numDias    = $_POST["quantityDays"];
$diaInicio  = $_POST["dayInitial"];
$diaFim     = $_POST["dayEnd"];
$dataInicio = $_POST["inputDateInitial"];
$dataFim    = $_POST["inputDateEnd"];
$ccFiltro   = rtrim(implode($_POST["cmbCentroDeCustos"],','));
$plFiltro   = rtrim(implode($_POST["cmbPlanoContas"],','));

$print = "<div id='carouselExampleControls' class='carousel slide' data-ride='carousel'>
            <div class='carousel-inner'> ";

$teste = false;

for($i = $diaInicio;$i <= $diaFim;$i++)
{  
  $teste = false;
    
  //limpa as variaveis
  if(isset($dia1))
  {
    unset($dia1);
    unset($cc1);
    unset($pl1);
    unset($saldoIni_p1);
    unset($saldoIni_r1);
  }
  
  //limpa as variaveis
  if(isset($dia2))  
  {
    unset($dia2);
    unset($cc2);
    unset($pl2);
    unset($saldoIni_p2);
    unset($saldoIni_r2);
  }    
    
  $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;
  
  //Pea TODOS os dads do dia $i
  $dia1 = retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro);
  
 // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
  
  $cc1           = $dia1['cc'];
  $pl1           = $dia1['pl'];
  $saldoIni_p1   = $dia1['saldoIni_p'][0]['SaldoInicialPrevisto'];
  $saldoIni_r1   = $dia1['saldoIni_r'][0]['SaldoInicialRealizado'];
  
  if(($i+1) <= $diaFim)
  {        
    $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
    
   //Pea TODOS os dads do dia $i+1 se ele estiver na faixa de dias do filtro
    $dia2 = retornaBuscaComoArray($dataFiltro,$ccFiltro,$plFiltro);
    
    $cc2           = $dia2['cc'];
    $pl2           = $dia2['pl'];
    $saldoIni_p2   = $dia2['saldoIni_p'][0]['SaldoInicialPrevisto'];
    $saldoIni_r2   = $dia2['saldoIni_r'][0]['SaldoInicialRealizado'];
    
    $teste = true;
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
    $saldoIni_p2 = number_format( $saldoIni_p2, 2, '.', '');
  else
    $saldoIni_p2 = "";
  
  if(isset($saldoIni_r2))
    $saldoIni_r2 = number_format($saldoIni_r2, 2, '.', '');
  else
    $saldoIni_r2 = "";

  $print .= " <div class='carousel-item ".($i == $diaInicio ? " active":"")."'> 
                <div class='row'>
                  <div class='col-lg-12'>
                    <!-- Basic responsive configuration -->
                    <div class='card-body' >
                      <div class='row'>
                          
                        <div class='col-lg-3'>
                        </div>

                        <div class='col-lg-3' style='text-align:center; border-top: 2px solid #1B3280; padding-top: 1rem;'>
                          <span><strong>".date('Y',strtotime($dataInicio))."-".date('F',strtotime($dataInicio))."-". $i.($teste ?" / ".($i+1):"")."</strong></span>
                        </div>
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

                          <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".number_format($saldoIni_p1, 2, '.', '') ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".number_format($saldoIni_r1, 2, '.', '') ."</span>
                              </div>
                            </div>
                          </div>
                          
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".(string)$saldoIni_p2."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".(string)$saldoIni_r2."</span>
                              </div>
                            </div>
                          </div>

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
                        
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span><strong>Previsto</strong></span>
                            </div>
                            <div class='col-md-6'>
                              <span><strong>Realizado</strong></span>
                            </div>
                          </div>
                        </div>
                        
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span><strong>Previsto</strong></span>
                            </div>
                            <div class='col-md-6'>
                              <span><strong>Realizado</strong></span>
                            </div>
                          </div>
                        </div>

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
                        
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span><strong>Previsto</strong></span>
                            </div>
                            <div class='col-md-6'>
                              <span><strong>Realizado</strong></span>
                            </div>
                          </div>
                        </div>
                        
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span><strong>Previsto</strong></span>
                            </div>
                            <div class='col-md-6'>
                              <span><strong>Realizado</strong></span>
                            </div>
                          </div>
                        </div>

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

    // totaliza as entradas e saidas
    $tot_geral_previsto1  = 0;
    $tot_geral_realizado1 = 0;
    $tot_geral_previsto2  = 0;
    $tot_geral_realizado2 = 0;  

  if(isset($cc1) && (!empty($cc1)))
  {
    foreach($cc1 as $cCusto)
    {
      if(isset($pl_Entrada))
        unset($pl_Entrada);

      if(isset($pl_Saida))
        unset($pl_Saida);

      //gera o html de plano de contas das entradas e soma os totais
      $pl_Entrada = planoDeContas($pl1[$cCusto['CnCusId']],($teste)?$pl2[$cCusto['CnCusId']]:"","Entrada");
      //gera o html de plano de contas das saidas e soma os totais
      $pl_Saida = planoDeContas($pl1[$cCusto['CnCusId']],($teste)?$pl2[$cCusto['CnCusId']]:"","Saida");
      
      //gera o html dos centros de custo das entradas
      //concatena com o htm gerado pelo plano de contas
      $print_ent .= centroDeCusto($cCusto['CnCusNome'],
                                    $cCusto['CC_PrevistoEntrada'],
                                    ($teste)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:"",
                                    $cCusto['CC_RealizadoEntrada'],
                                    ($teste)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:"",
                                    $pl_Entrada['HTML']);                  

      //gera o html dos centros de custo das saidas
      //concatena com o htm gerado pelo plano de contas
      $print_sai .= centroDeCusto($cCusto['CnCusNome'],
                                    $cCusto['CC_PrevistoSaida'],
                                    ($teste)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:"",
                                    $cCusto['CC_RealizadoSaida'],
                                    ($teste)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:"",
                                    $pl_Saida['HTML']);

      //soma os totais dos planos de contas das entradas com o centro de custo atual
      $pl_Entrada['total_previsto']  += $cCusto['CC_PrevistoEntrada'];
      $pl_Entrada['total_realizado'] += $cCusto['CC_RealizadoEntrada'];
      $pl_Entrada['total_previsto2']  += ($teste)?$cc2[$cCusto['CnCusId']]['CC_PrevistoEntrada']:0;
      $pl_Entrada['total_realizado2'] += ($teste)?$cc2[$cCusto['CnCusId']]['CC_RealizadoEntrada']:0;

      //soma os totais dos planos de contas das saidas com o centro de custo atual
      $pl_Saida['total_previsto']  += $cCusto['CC_PrevistoSaida'];
      $pl_Saida['total_realizado'] += $cCusto['CC_RealizadoSaida'];
      $pl_Saida['total_previsto2']  += ($teste)?$cc2[$cCusto['CnCusId']]['CC_PrevistoSaida']:0;
      $pl_Saida['total_realizado2'] += ($teste)?$cc2[$cCusto['CnCusId']]['CC_RealizadoSaida']:0;

      // totaliza as entradas e saidas
      $tot_previsto_entrada1  += $pl_Entrada['total_previsto'];
      $tot_previsto_saida1    += $pl_Saida['total_previsto'];
      $tot_realizado_entrada1 += $pl_Entrada['total_realizado'];
      $tot_realizado_saida1   += $pl_Saida['total_realizado'];      
      $tot_previsto_entrada2  += $pl_Entrada['total_previsto2'];
      $tot_previsto_saida2    += $pl_Saida['total_previsto2'];
      $tot_realizado_entrada2 += $pl_Entrada['total_realizado2'];
      $tot_realizado_saida2   += $pl_Saida['total_realizado2'];

      // totaliza as entradas e saidas
      $tot_geral_previsto1  += ($tot_previsto_entrada1 - $tot_previsto_saida1 )+($saldoIni_p1);
      $tot_geral_realizado1 += ($tot_realizado_entrada1 - $tot_realizado_saida1)+($saldoIni_r1);
      
      if ($teste)
      {
        $tot_geral_previsto2  += ($tot_previsto_entrada2 - $tot_previsto_saida2 )+($saldoIni_p2);
        $tot_geral_realizado2 += ($tot_realizado_entrada2 - $tot_realizado_saida2)+($saldoIni_r2);
      }
      else 
      {        
        $tot_geral_previsto2  += 0;
        $tot_geral_realizado2 += 0;  
      }
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

                          <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".number_format($tot_previsto_entrada1, 2, '.', '') ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".number_format($tot_realizado_entrada1, 2, '.', '') ."</span>
                              </div>
                            </div>
                          </div>

                          <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".($teste ? number_format($tot_previsto_entrada2, 2, '.', '') :"") ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".($teste ? number_format($tot_realizado_entrada2, 2, '.', '') :"") ."</span>
                              </div>
                            </div>
                          </div>

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

                          <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".number_format($tot_previsto_saida1, 2, '.', '') ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".number_format($tot_realizado_saida1, 2, '.', '') ."</span>
                              </div>
                            </div>
                          </div>
                          
                          <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>".($teste ? number_format($tot_previsto_saida2, 2, '.', '') :"") ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>".($teste ? number_format($tot_realizado_saida2, 2, '.', '') :"") ."</span>
                              </div>
                            </div>
                          </div>  

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

                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>". number_format($tot_geral_previsto1+$tot_geral_previsto2, 2, '.', '')  ."</span>
                            </div>

                            <div class='col-md-6'>
                              <span>". number_format($tot_geral_realizado1+$tot_geral_realizado2, 2, '.', '')  ."</span>
                            </div>
                          </div>
                        </div>
                          
                        <div class='dataOpeningBalance col-lg-3' style='border-right: 1px dotted black; text-align:center;'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <span>". /*number_format($tot_geral_previsto2, 2, '.', '')*/ "" ."</span>
                              </div>

                              <div class='col-md-6'>
                                <span>". /*number_format($tot_geral_realizado2, 2, '.', '')*/""  ."</span>
                              </div>
                            </div>
                          </div>

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
                        <div class='row col-lg-12' style='background: #607D8B; color:white; line-height: 3rem; box-sizing:border-box'>
                          <span><strong>COMPARATIVO DO PERÍODO (ENTRADA E SAÍDA): </strong></span>
                        </div>

                        <div class='row col-lg-12' style='background: #fff; line-height: 3rem; box-sizing:border-box'>";
  if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0 && isset($tot_geral_previsto2) && $tot_geral_previsto2 != 0)
  {
    $print .= "<span>".number_format(((($tot_geral_realizado1+$tot_geral_realizado2) /($tot_geral_previsto1+$tot_geral_previsto2)) * 100), 2, '.', '') ."%</span>";
  }
  else if(isset($tot_geral_previsto1) && $tot_geral_previsto1 != 0)
  {
    $print .= "<span>".number_format(((($tot_geral_realizado1) /($tot_geral_previsto1)) * 100), 2, '.', '') ."%</span>";  
  }
  else
  {
    $print .= " <span>0.00%</span>";
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

$print .= "</div>
            <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev' style='color:black;'>
              <span class='carousel-control-prev-icon' aria-hidden='true' >Previous</span>
              <span class='sr-only'>Previous</span>
            </a>

            <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next' style='color:black;'>
              <span class='carousel-control-next-icon' aria-hidden='true'>Next</span>
              <span class='sr-only'>Next</span>
            </a>
          </div>";



print($print);