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

function planoContaEntrada($idPlanoConta, $nome, $segundaColuna, $terceiraColuna, $data1, $data2, $data3) {
  include('global_assets/php/conexao.php');

  //Plano Conta Previsto
  $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data1."',  '".$data1."', 'E') as PrevistoEntrada";
  $result = $conn->query($sql);
  $planoContaPrevisaoEntrada1 = $result->fetch(PDO::FETCH_ASSOC);

  if($segundaColuna) {
    $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data2."',  '".$data2."', 'E') as PrevistoEntrada";
    $result = $conn->query($sql);
    $planoContaPrevisaoEntrada2 = $result->fetch(PDO::FETCH_ASSOC);
  }

  if($terceiraColuna) {
    $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data3."',  '".$data3."', 'E') as PrevistoEntrada";
    $result = $conn->query($sql);
    $planoContaPrevisaoEntrada3 = $result->fetch(PDO::FETCH_ASSOC);
  }

  //Plano Conta realizado
  $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data1."',  '".$data1."', 'E') as RealizadoEntrada";
  $result = $conn->query($sql);
  $planoContaRealizadoEntrada1 = $result->fetch(PDO::FETCH_ASSOC);

  if($segundaColuna) {
    $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data2."',  '".$data2."', 'E') as RealizadoEntrada";
    $result = $conn->query($sql);
    $planoContaRealizadoEntrada2 = $result->fetch(PDO::FETCH_ASSOC);
  }

  if($terceiraColuna) {
    $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data3."',  '".$data3."', 'E') as RealizadoEntrada";
    $result = $conn->query($sql);
    $planoContaRealizadoEntrada3 = $result->fetch(PDO::FETCH_ASSOC);
  }

  $ValorPrimeiraColunaPrevisto = $planoContaPrevisaoEntrada1['PrevistoEntrada'];
  $valorSegundaColunaPrevisto = 0;
  $valorTerceiraColunaPrevisto = 0;

  $ValorPrimeiraColunaRealizado = $planoContaRealizadoEntrada1['RealizadoEntrada'];
  $valorSegundaColunaRealizado = 0;
  $valorTerceiraColunaRealizado = 0;

  $resposta[0] = "
        <div class='card-body' style='padding-top: 0;padding-bottom: 0'>
          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
            <div class='col-lg-4' style='border-right: 1px dotted black;'>
              <span>".$nome."</span>
            </div>

            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-6'>
                  <span>".mostraValor($planoContaPrevisaoEntrada1['PrevistoEntrada'])."</span>
                </div>

                <div class='col-md-6'>
                  <span>".mostraValor($planoContaRealizadoEntrada1['RealizadoEntrada'])."</span>
                </div>
              </div>
            </div>";

            if($segundaColuna) {
              $resposta[0] .= "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaPrevisaoEntrada2['PrevistoEntrada'])."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaRealizadoEntrada2['RealizadoEntrada'])."</span>
                  </div>
                </div>
              </div>";

              $valorSegundaColunaPrevisto = $planoContaPrevisaoEntrada2['PrevistoEntrada'];
              $valorSegundaColunaRealizado = $planoContaRealizadoEntrada2['RealizadoEntrada'];
            }

            if($terceiraColuna) {
              $resposta[0] .= "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaPrevisaoEntrada3['PrevistoEntrada'])."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaRealizadoEntrada3['RealizadoEntrada'])."</span>
                  </div>
                </div>
              </div>";

              $valorTerceiraColunaPrevisto = $planoContaPrevisaoEntrada3['PrevistoEntrada'];
              $valorTerceiraColunaRealizado = $planoContaRealizadoEntrada3['RealizadoEntrada'];
            }
        $totalValorPrevisto = $ValorPrimeiraColunaPrevisto + $valorSegundaColunaPrevisto + $valorTerceiraColunaPrevisto;
        $totalValorRealizado = $ValorPrimeiraColunaRealizado + $valorSegundaColunaRealizado + $valorTerceiraColunaRealizado;

        $percentual = 0;
        if($totalValorPrevisto > 0)
          $percentual = ($totalValorRealizado * 100) / $totalValorPrevisto;

            $resposta[0] .= "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-12'>
                  <span>".($percentual > 0 ? number_format($percentual,2) : 0)."%</span>
                </div>
              </div>
            </div>
        </div>
      </div>";

    $resposta[1] = $ValorPrimeiraColunaPrevisto;
    $resposta[2] = $valorSegundaColunaPrevisto;
    $resposta[3] = $valorTerceiraColunaPrevisto;

    $resposta[4] = $ValorPrimeiraColunaRealizado;
    $resposta[5] = $valorSegundaColunaRealizado;
    $resposta[6] = $valorTerceiraColunaRealizado;

    return $resposta;
}

function planoContaSaida($idPlanoConta, $nome, $consultaCentroDeCusto, $segundaColuna, $terceiraColuna, $data1, $data2, $data3) {
  include('global_assets/php/conexao.php');
  
  //Plano Conta Previsto
  $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data1."',  '".$data1."', 'S') as PrevistoSaida";
  $result = $conn->query($sql);
  $planoContaPrevistoSaida = $result->fetch(PDO::FETCH_ASSOC);

  if($segundaColuna) {
    $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data2."',  '".$data2."', 'S') as PrevistoSaida";
    $result = $conn->query($sql);
    $planoContaPrevistoSaida2 = $result->fetch(PDO::FETCH_ASSOC);
  }

  if($terceiraColuna) {
    $sql = " SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data3."',  '".$data3."', 'S') as PrevistoSaida";
    $result = $conn->query($sql);
    $planoContaPrevistoSaida3 = $result->fetch(PDO::FETCH_ASSOC);
  }

  //Plano Conta realizado
  $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data1."',  '".$data1."', 'S') as RealizadoSaida";
  $result = $conn->query($sql);
  $planoContaRealizadoSaida1 = $result->fetch(PDO::FETCH_ASSOC);

  if($segundaColuna) {
    $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data2."',  '".$data2."', 'S') as RealizadoSaida";
    $result = $conn->query($sql);
    $planoContaRealizadoSaida2 = $result->fetch(PDO::FETCH_ASSOC);
  }

  if($terceiraColuna) {
    $sql = " SELECT dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", $idPlanoConta, '".$data3."',  '".$data3."', 'S') as RealizadoSaida";
    $result = $conn->query($sql);
    $planoContaRealizadoSaida3 = $result->fetch(PDO::FETCH_ASSOC);
  }

  $ValorPrimeiraColunaPrevisto = $planoContaPrevistoSaida['PrevistoSaida'];
  $valorSegundaColunaPrevisto = 0;
  $valorTerceiraColunaPrevisto = 0;

  $ValorPrimeiraColunaRealizado = $planoContaRealizadoSaida1['RealizadoSaida'];
  $valorSegundaColunaRealizado = 0;
  $valorTerceiraColunaRealizado = 0;

    $resposta[0] = "
        <div class='card-body' style='padding-top: 0;padding-bottom: 0'>
          <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
            <div class='col-lg-4' style='border-right: 1px dotted black;'>
              <span>".$nome."</span>
            </div>

            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-6'>
                  <span>".mostraValor($planoContaPrevistoSaida['PrevistoSaida']).//mostraValor($CCPrevisto) .
                  "</span>
                </div>

                <div class='col-md-6'>
                  <span>".mostraValor($planoContaRealizadoSaida1['RealizadoSaida'])."</span>
                </div>
              </div>
            </div>";

      if($segundaColuna) {
        $resposta[0] .="
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaPrevistoSaida2['PrevistoSaida'])."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaRealizadoSaida2['RealizadoSaida'])."</span>
                  </div>
                </div>
              </div>";

        $valorSegundaColunaPrevisto = $planoContaPrevistoSaida2['PrevistoSaida'];
        $valorSegundaColunaRealizado = $planoContaRealizadoSaida2['RealizadoSaida'];
      }

      if($terceiraColuna) {
        $resposta[0] .="
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaPrevistoSaida3['PrevistoSaida'])."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($planoContaRealizadoSaida3['RealizadoSaida'])."</span>
                  </div>
                </div>
              </div>";

        $valorTerceiraColunaPrevisto = $planoContaPrevistoSaida3['PrevistoSaida'];
        $valorTerceiraColunaRealizado = $planoContaRealizadoSaida3['RealizadoSaida'];
      }

      $totalValorPrevisto = $ValorPrimeiraColunaPrevisto + $valorSegundaColunaPrevisto + $valorTerceiraColunaPrevisto;
      $totalValorRealizado = $ValorPrimeiraColunaRealizado + $valorSegundaColunaRealizado + $valorTerceiraColunaRealizado;

      $percentual = 0;
      if($totalValorPrevisto > 0)
        $percentual = ($totalValorRealizado * 100) / $totalValorPrevisto;

      $resposta[0] .="<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-12'>
                  <span>".($percentual > 0 ? number_format($percentual,2) : 0)."%</span>
                </div>
              </div>
            </div>
        </div>
      </div>";

    foreach($consultaCentroDeCusto as $centroCusto) {
      $resposta[0]  .= "
        <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
          <div class='row' style='background: #eeeeee; line-height: 3rem; box-sizing:border-box'>
    
            <div class='col-lg-4' style='border-right: 1px dotted black; padding-left: 20px;'>
              <span title='".//$PL[$i]['PlConNome'].
              "'>".$centroCusto['CnCusNome']."</span>
            </div>
    
            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-6'>
                  <span>".//mostraValor($PL[$i]['PL_Previsto'.$tipo]).
                  "</span>
                </div>
    
                <div class='col-md-6'>
                  <span>".//mostraValor($PL[$i]['PL_Realizado'.$tipo]).
                  "</span>
                </div>
              </div>
            </div>
            
            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
              <div class='row'>
                <div class='col-md-6'>
                  <span>".//(is_array($pl3)? mostraValor($pl3[$i]['PL_Previsto'.$tipo]):"").
                  "</span>
                </div>
    
                <div class='col-md-6'>
                  <span>".//(is_array($pl3)? mostraValor($pl3[$i]['PL_Realizado'.$tipo]):"").
                  "</span>
                </div>
              </div>
            </div>
    
          </div>
        </div>";
      }

    $resposta[1] = $ValorPrimeiraColunaPrevisto;
    $resposta[2] = $valorSegundaColunaPrevisto;
    $resposta[3] = $valorTerceiraColunaPrevisto;

    $resposta[4] = $ValorPrimeiraColunaRealizado;
    $resposta[5] = $valorSegundaColunaRealizado;
    $resposta[6] = $valorTerceiraColunaRealizado;

    return $resposta;
}

function teste() {
  return 'a';
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

$sql = "SELECT PlConId, PlConNome
        FROM PlanoConta
        WHERE PlConId in ($plFiltro) and PlConNatureza = 'R'
        ORDER BY PlConNome";
$resultPlanoConta = $conn->query($sql);
$rowPLanoContaRecebe = $resultPlanoConta->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT PlConId, PlConNome
        FROM PlanoConta
        WHERE PlConId in ($plFiltro) and PlConNatureza = 'D'
        ORDER BY PlConNome";
$resultPlanoConta = $conn->query($sql);
$rowPLanoContaPaga = $resultPlanoConta->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT CnCusId, CnCusNome
        FROM CentroCusto
        WHERE CnCusId in ($ccFiltro)
        ORDER BY CnCusNome";
$resultCentroDeCusto = $conn->query($sql);
$rowCentroDeCusto = $resultCentroDeCusto->fetchAll(PDO::FETCH_ASSOC);

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

  for($i = $diaInicio;$i <= $diaFim;$i++) {  
    // if(($diaFim - $diaInicio) == 2){
    //   break;
    // }
    $segundaColuna = false;
    $terceiraColuna = false;
    /*   
    //limpa as variaveis
    if(isset($mes1))
    {
      unset($mes1);
      //unset($cc1);
      //unset($pl1);
      //unset($saldoIni_p1);
      //unset($saldoIni_r1);
    }
    
    //limpa as variaveis
    if(isset($mes2))  
    {
      unset($mes2);
      //unset($cc2);
      //unset($pl2);
      //unset($saldoIni_p2);
      //unset($saldoIni_r2);
    }    

    if(isset($mes3))  
    {
      unset($mes3);
      //unset($cc3);
      //unset($pl3);
      //unset($saldoIni_p3);
      //unset($saldoIni_r3);
    }    */
      
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

     //pega o saldo inicial presumido
    $sql_saldo_ini_p   = "select dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialPrevisto";
    $result_saldo_ini_p = $conn->query($sql_saldo_ini_p);
    $rowSaldoIni_p      = $result_saldo_ini_p->fetch(PDO::FETCH_ASSOC);
    $saldoInicialPrevisto = $rowSaldoIni_p["SaldoInicialPrevisto"];
    
    //echo $sql_saldo_ini_p."<br>";

    //pega o saldo inicial realizado
    $sql_saldo_ini_r = "select dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialRealizado";
    $result_saldo_ini_r = $conn->query($sql_saldo_ini_r);
    $rowSaldoIni_r      = $result_saldo_ini_r->fetch(PDO::FETCH_ASSOC);
    $saldoInicialRealizado = $rowSaldoIni_r["SaldoInicialRealizado"];

    //Pea TODOS os dads do dia $i
    //$mes1 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
    
   // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    
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
      //$mes2 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      
      $segundaColuna = true;
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
    //$mes3 = retornaBuscaComoArray($datasFiltro,$ccFiltro,$plFiltro);
      /*
      $cc3           = $mes3['cc'];
      $pl3           = $mes3['pl'];
      $saldoIni_p3   = $mes3['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r3   = $mes3['saldoIni_r'][0]['SaldoInicialRealizado'];*/
      
      $terceiraColuna = true;
    }
    
    //echo "<pre>".print_r($cc1)."</pre>";;
   // echo "<pre>".print_r($cc2)."</pre>";
   // echo "<pre>".print_r($pl1)."</pre>";
   // echo "<pre>".print_r($pl2)."</pre>";  exit();
  
    /*
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
      $saldoIni_r3 = 0;*/

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
  
                           ".($segundaColuna ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                            <span><strong>".str_pad($i+1, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataInicio ."-".str_pad($i+1, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                          </div>" : "")."

                          ".($terceiraColuna ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
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
                                  <span>".mostraValor($saldoInicialPrevisto)."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".//mostraValor($saldoInicialRealizado).
                                  "</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_p2).
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_r2).
                                                                      "</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_p3).
                                                                      "</span>
                                                                    </div>
                                     
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_r3).
                                                                      "</span>
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
                          
                          ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                               </div>" : "")."
                          
                          ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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
                                <span style='padding:0 px;margin:0px;'><strong style='padding:0px;margin:0px;'>Previsto/Realizado %</strong></span>
                              </div>
                            </div>
                          </div>
  
                        </div>
                      </div> ";

    $dia1 = str_pad($i, 2, '0', STR_PAD_LEFT);
    $dia2 = str_pad($i+1, 2, '0', STR_PAD_LEFT);
    $dia3 = str_pad($i+2, 2, '0', STR_PAD_LEFT);
    
    $dataAnoMes = explode('-',$dataInicio);

    $data1 = $dataAnoMes[0].'-'.$dataAnoMes[1].'-'.$dia1;
    $data2 = $dataAnoMes[0].'-'.$dataAnoMes[1].'-'.$dia2;
    $data3 = $dataAnoMes[0].'-'.$dataAnoMes[1].'-'.$dia3;

    $totalPrevistoPrimeiraColuna = 0;
    $totalPrevistoSegundaColuna = 0;
    $totalPrevistoTerceiraColuna = 0;

    $totalRealizadoPrimeiraColuna = 0;
    $totalRealizadoSegundaColuna = 0;
    $totalRealizadoTerceiraColuna = 0;
    foreach($rowPLanoContaRecebe as $planoContaRecebe) {
      $planoContaEntrada = planoContaEntrada($planoContaRecebe['PlConId'], $planoContaRecebe['PlConNome'], $segundaColuna, $terceiraColuna, $data1, $data2, $data3);
      $print_ent .= $planoContaEntrada[0];

      $ValorPrevistoPrimeiraColuna = $planoContaEntrada[1];
      $valorPrevistoSegundaColuna = $planoContaEntrada[2];
      $valorPrevistoTerceiraColuna = $planoContaEntrada[3];

      $ValorRealizadoPrimeiraColuna = $planoContaEntrada[4];
      $valorRealizadoSegundaColuna = $planoContaEntrada[5];
      $valorRealizadoTerceiraColuna = $planoContaEntrada[6];

      $totalPrevistoPrimeiraColuna += $ValorPrevistoPrimeiraColuna;
      $totalPrevistoSegundaColuna += $valorPrevistoSegundaColuna;
      $totalPrevistoTerceiraColuna += $valorPrevistoTerceiraColuna;

      $totalRealizadoPrimeiraColuna += $ValorRealizadoPrimeiraColuna;
      $totalRealizadoSegundaColuna += $valorRealizadoSegundaColuna;
      $totalRealizadoTerceiraColuna += $valorRealizadoTerceiraColuna;
    }

    $totalValorPrevisto = $totalPrevistoPrimeiraColuna + $totalPrevistoSegundaColuna + $totalPrevistoTerceiraColuna;
    $totalValorRealizado = $totalRealizadoPrimeiraColuna + $totalRealizadoSegundaColuna + $totalRealizadoTerceiraColuna;

    $percentual = 0;
    if($totalValorPrevisto > 0)
      $percentual = ($totalValorRealizado * 100) / $totalValorPrevisto;

    $saldoInicialPrevisto += $totalValorPrevisto;
    $saldoInicialRealizado += $totalValorRealizado;

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
                                  <span>".mostraValor($totalPrevistoPrimeiraColuna)."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($totalRealizadoPrimeiraColuna)."</span>
                                </div>
                              </div>
                            </div>
  
                            ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalPrevistoSegundaColuna)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalRealizadoSegundaColuna)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalPrevistoTerceiraColuna)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalRealizadoTerceiraColuna)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                    <span>".($percentual > 0 ? number_format($percentual,2) : 0)."%</span>
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
                          
                          ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Previsto</strong></span>
                                                                  </div>
                                                                  <div class='col-md-6'>
                                                                    <span><strong>Realizado</strong></span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."
                          
                          ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
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
                            <span style='padding:0 px;margin:0px;'><strong style='padding:0px;margin:0px;'>Previsto/Realizado %</strong></span>
                          </div>
  
                        </div>
                      </div>";
    $totalPrevistoPrimeiraColuna = 0;
    $totalPrevistoSegundaColuna = 0;
    $totalPrevistoTerceiraColuna = 0;

    $totalRealizadoPrimeiraColuna = 0;
    $totalRealizadoSegundaColuna = 0;
    $totalRealizadoTerceiraColuna = 0;  
    foreach($rowPLanoContaPaga as $planoContaPaga) {
      $planoContaSaida = planoContaSaida($planoContaPaga['PlConId'], $planoContaPaga['PlConNome'], $rowCentroDeCusto, $segundaColuna, $terceiraColuna, $data1, $data2, $data3);
      $print_sai .= $planoContaSaida[0];

      $ValorPrimeiraColuna = $planoContaSaida[1];
      $valorSegundaColuna = $planoContaSaida[2];
      $valorTerceiraColuna = $planoContaSaida[3];

      $ValorRealizadoPrimeiraColuna = $planoContaSaida[4];
      $valorRealizadoSegundaColuna = $planoContaSaida[5];
      $valorRealizadoTerceiraColuna = $planoContaSaida[6];

      $totalPrevistoPrimeiraColuna += $ValorPrimeiraColuna;
      $totalPrevistoSegundaColuna += $valorSegundaColuna;
      $totalPrevistoTerceiraColuna += $valorTerceiraColuna;

      $totalRealizadoPrimeiraColuna += $ValorRealizadoPrimeiraColuna;
      $totalRealizadoSegundaColuna += $valorRealizadoSegundaColuna;
      $totalRealizadoTerceiraColuna += $valorRealizadoTerceiraColuna;
    }

    $totalValorPrevisto = $totalPrevistoPrimeiraColuna + $totalPrevistoSegundaColuna + $totalPrevistoTerceiraColuna;
    $totalValorRealizado = $totalRealizadoPrimeiraColuna + $totalRealizadoSegundaColuna + $totalRealizadoTerceiraColuna;

    $percentual = 0;
    if($totalValorPrevisto > 0)
      $percentual = ($totalValorRealizado * 100) / $totalValorPrevisto;

    $saldoInicialPrevisto -= $totalValorPrevisto;
    $saldoInicialRealizado -= $totalValorRealizado;
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
                                  <span>".mostraValor($totalPrevistoPrimeiraColuna)."</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".mostraValor($totalRealizadoPrimeiraColuna)."</span>
                                </div>
                              </div>
                            </div>
  
                            ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalPrevistoSegundaColuna)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalRealizadoSegundaColuna)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalPrevistoTerceiraColuna)."</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".mostraValor($totalRealizadoTerceiraColuna)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
  
                            <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                <div class='row'>
                                  <div class='col-md-12'>
                                    <div class='col-md-12'>
                                      <span>".($percentual > 0 ? number_format($percentual,2) : 0)."%</span>
                                    </div>
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
                                <span>".// mostraValor($tot_geral_previsto1)  .
                                "</span>
                              </div>
  
                              <div class='col-md-6'>
                                <span>".// mostraValor($tot_geral_realizado1)  .
                                "</span>
                              </div>
                            </div>
                          </div>
                            
                          ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span>". //mostraValor($tot_geral_previsto2) .
                                                                    "</span>
                                                                  </div>
                                    
                                                                  <div class='col-md-6'>
                                                                    <span>". //mostraValor($tot_geral_realizado2)  .
                                                                    "</span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."

                          ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span>". //mostraValor($tot_geral_previsto3) .
                                                                  "</span>
                                                                </div>
                                  
                                                                <div class='col-md-6'>
                                                                  <span>". //mostraValor($tot_geral_realizado3)  .
                                                                  "</span>
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
    
    $print .= " <span>0,00%</span>";
  
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
    
    // if($i+$pagina == 13){
    //   break;
    // }
    $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;

    $ultimo_dia = date("t", mktime(0,0,0,($i),'01',$anoTela));
    $dataFiltroDiaInicioMes1 = $anoTela.'-'.($i).'-01';
    $dataFiltroDiaFimMes1 = $anoTela.'-'.($i).'-'.$ultimo_dia;

    $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes1;
    $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes1;
    
    ////////////////////////////////////////Dados mês 2
    if(($i+1) <= $mesFinal)
    {        
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.(($i+1));
      
      $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoTela));
      $dataFiltroDiaInicioMes2 = $anoTela.'-'.($i+1).'-01';
      $dataFiltroDiaFimMes2 = $anoTela.'-'.($i+1).'-'.$ultimo_dia;

      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicioMes2;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFimMes2;
      
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
      
      $teste2 = true;
    }

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
                                  <span>".//mostraValor($saldoIni_p1).
                                  "</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".//mostraValor($saldoIni_r1).
                                  "</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_p2).
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_r2).
                                                                      "</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_p3).
                                                                      "</span>
                                                                    </div>
                                     
                                                                    <div class='col-md-6'>
                                                                      <span>".//mostraValor($saldoIni_r3).
                                                                      "</span>
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
                                  <span>".//mostraValor($tot_previsto_entrada1) .
                                  "</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".//mostraValor($tot_realizado_entrada1) .
                                  "</span>
                                </div>
                              </div>
                            </div>
  
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste1 ? mostraValor($tot_previsto_entrada2) :"") .
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste1 ? mostraValor($tot_realizado_entrada2) :"") .
                                                                      "</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste2 ? mostraValor($tot_previsto_entrada3) :"") .
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste2 ? mostraValor($tot_realizado_entrada3) :"") .
                                                                      "</span>
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
                                  <span>".//mostraValor($tot_previsto_saida1) .
                                  "</span>
                                </div>
  
                                <div class='col-md-6'>
                                  <span>".//mostraValor($tot_realizado_saida1) .
                                  "</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste1 ? mostraValor($tot_previsto_saida2) :"") .
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste1 ? mostraValor($tot_realizado_saida2) :"") .
                                                                      "</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."
                            
                            ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste2 ? mostraValor($tot_previsto_saida3) :"") .
                                                                      "</span>
                                                                    </div>
                                      
                                                                    <div class='col-md-6'>
                                                                      <span>".//($teste2 ? mostraValor($tot_realizado_saida3) :"") .
                                                                      "</span>
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
                                <span>".// mostraValor($tot_geral_previsto1)  .
                                "</span>
                              </div>
  
                              <div class='col-md-6'>
                                <span>".// mostraValor($tot_geral_realizado1)  .
                                "</span>
                              </div>
                            </div>
                          </div>
                            
                          ".($teste1 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-6'>
                                                                    <span>". //mostraValor($tot_geral_previsto2) .
                                                                    "</span>
                                                                  </div>
                                    
                                                                  <div class='col-md-6'>
                                                                    <span>". //mostraValor($tot_geral_realizado2)  .
                                                                    "</span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."

                          ".($teste2 ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-6'>
                                                                  <span>". //mostraValor($tot_geral_previsto3) .
                                                                  "</span>
                                                                </div>
                                  
                                                                <div class='col-md-6'>
                                                                  <span>". //mostraValor($tot_geral_realizado3)  .
                                                                  "</span>
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
    
    $print .= " <span>0,00%</span>";
  
    $print .= "           <!--span>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</span-->
                          </div>
                        </div>
                    </div>
                  </div>
                  <!-- SALDO FINAL --> 
                </div>  
                ";

  if(($i+1) <= $mesFinal)
    {
        $i++;
    }  
  }
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