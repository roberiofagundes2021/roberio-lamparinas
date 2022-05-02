<?php

include_once("sessao.php");

$inicio1 = microtime(true);

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

function retornaBuscaComoArray($datasFiltro ,$plFiltro, $tipo) {
  include('global_assets/php/conexao.php');

  if($tipo == 'E') {
    $sql = "SELECT PlConId, PlConNome,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as PrevistoSaida,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as RealizadoSaida
            FROM PLanoConta
            WHERE PlConId in ($plFiltro) and PlConNatureza = 'R'
            ORDER BY PlConNome ASC";
    $result = $conn->query($sql);
    $rowPLanoContaPaga = $result->fetchAll(PDO::FETCH_ASSOC);
  }else {
    $sql = "SELECT PlConId, PlConNome,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as PrevistoSaida,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConId, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as RealizadoSaida
            FROM PLanoConta
            WHERE PlConId in ($plFiltro) and PlConNatureza = 'D'
            ORDER BY PlConNome ASC";
    $result = $conn->query($sql);
    $rowPLanoContaPaga = $result->fetchAll(PDO::FETCH_ASSOC);
  }

  $regAt = '';
  $cont = 0;
  
  if(count($rowPLanoContaPaga) > 0){
    foreach($rowPLanoContaPaga as $rowCC) {
      if($regAt != $rowCC['PlConId']) {
        $regAt = $rowCC['PlConId'];
  
        $cont = 0;
  
        //reserva os dados do plano de contas
        $pl[$regAt]['PlConId']              = $rowCC['PlConId']; 
        $pl[$regAt]['PlConNome']            = $rowCC['PlConNome'];
        $pl[$regAt]['PL_Previsto']     = $rowCC['PrevistoSaida'];
        $pl[$regAt]['PL_Realizado']    = $rowCC['RealizadoSaida'];
      }
  
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
      
    $retorno = array('pl'=>$pl,'saldoIni_p'=>$rowSaldoIni_p,'saldoIni_r'=>$rowSaldoIni_r);
    
    unset($pl);
    unset($result);
    unset($rowPLanoContaPaga);
    
    return $retorno;
  } else {
    return false;
  }
}

function planoContaEntrada($nome, $valorPrevisto, $valorPrevisto2, $valorPrevisto3, $valorRealizado, $valorRealizado2, $valorRealizado3, $segundaColuna, $terceiraColuna) {
  include('global_assets/php/conexao.php');
  /*
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
  }*/

  $ValorPrimeiraColunaPrevisto = $valorPrevisto;
  $valorSegundaColunaPrevisto = 0;
  $valorTerceiraColunaPrevisto = 0;

  $ValorPrimeiraColunaRealizado = $valorRealizado;//planoContaRealizadoEntrada1['RealizadoEntrada'];
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
                  <span>".mostraValor($valorPrevisto)."</span>
                </div>

                <div class='col-md-6'>
                  <span>".mostraValor($valorRealizado)."</span>
                </div>
              </div>
            </div>";

            if($segundaColuna) {
              $resposta[0] .= "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($valorPrevisto2)."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($valorRealizado2)."</span>
                  </div>
                </div>
              </div>";

              $valorSegundaColunaPrevisto = $valorPrevisto2;
              $valorSegundaColunaRealizado = $valorRealizado2;
            }

            if($terceiraColuna) {
              $resposta[0] .= "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($valorPrevisto3)."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($valorRealizado3)."</span>
                  </div>
                </div>
              </div>";

              $valorTerceiraColunaPrevisto = $valorPrevisto3;
              $valorTerceiraColunaRealizado = $valorRealizado3;
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

function planoContaSaida($nome, $valorPrevisto, $valorPrevisto2, $valorPrevisto3, $valorRealizado, $valorRealizado2, $valorRealizado3, $consultaCentroDeCusto, $segundaColuna, $terceiraColuna) {
  include('global_assets/php/conexao.php');
  /*
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
  }*/

  $ValorPrimeiraColunaPrevisto = $valorPrevisto;
  $valorSegundaColunaPrevisto = 0;
  $valorTerceiraColunaPrevisto = 0;

  $ValorPrimeiraColunaRealizado = $valorRealizado;
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
                  <span>".mostraValor($valorPrevisto)."</span>
                </div>

                <div class='col-md-6'>
                  <span>".mostraValor($valorRealizado)."</span>
                </div>
              </div>
            </div>";

      if($segundaColuna) {
        $resposta[0] .="
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($valorPrevisto2)."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($valorRealizado2)."</span>
                  </div>
                </div>
              </div>";

        $valorSegundaColunaPrevisto = $valorPrevisto2;
        $valorSegundaColunaRealizado = $valorRealizado2;
      }

      if($terceiraColuna) {
        $resposta[0] .="
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span>".mostraValor($valorPrevisto3)."</span>
                  </div>
  
                  <div class='col-md-6'>
                    <span>".mostraValor($valorRealizado3)."</span>
                  </div>
                </div>
              </div>";

        $valorTerceiraColunaPrevisto = $valorPrevisto3;
        $valorTerceiraColunaRealizado = $valorRealizado3;
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

  $controlador = (($diaFim - $diaInicio) < 3 ) ? true : false; 
  $diaFimControle = $diaFim;

  // print($diaFim - $diaInicio);

  for($i = $diaInicio;$i <= $diaFim;$i++) {
    // if(($diaFim - $diaInicio) == 2){
    //   break;
    // }
    $segundaColuna = false;
    $terceiraColuna = false;

    //limpa as variaveis
    if(isset($mes1Entrada)) {
      unset($mes1Entrada);
      //unset($cc1);
      unset($pl1Entrada);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
    }

    if(isset($mes1Saida)) {
      unset($mes1Saida);
      //unset($cc1);
      unset($pl1Saida);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
    }

    if(isset($mes2Entrada)) {
      unset($mes2Entrada);
      //unset($cc1);
      unset($pl2Entrada);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
    }
    
    if(isset($mes2Saida)) {
      unset($mes2Saida);
      //unset($cc2);
      unset($pl2Saida);
      unset($saldoIni_p2);
      unset($saldoIni_r2);
    }    

    if(isset($mes3Entrada)) {
      unset($mes3Entrada);
      //unset($cc1);
      unset($pl3Entrada);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
    }

    if(isset($mes3Saida)) {
      unset($mes3Saida);
      //unset($cc3);
      unset($pl3Saida);
      unset($saldoIni_p3);
      //unset($saldoIni_r3);
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
    $mes1Entrada = retornaBuscaComoArray($datasFiltro,$plFiltro, 'E');
    $mes1Saida = retornaBuscaComoArray($datasFiltro,$plFiltro, 'S');

    $pl1Entrada    = $mes1Entrada['pl'];
    
    $pl1Saida      = $mes1Saida['pl'];
    $saldoIni_p1   = $mes1Saida['saldoIni_p'][0]['SaldoInicialPrevisto'];
    $saldoIni_r1   = $mes1Saida['saldoIni_r'][0]['SaldoInicialRealizado'];
    
   // echo "".$dataFiltro."---".$ccFiltro."---".$plFiltro."<br>";
    if(($i+1) <= $diaFim) {        
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
      $mes2Entrada = retornaBuscaComoArray($datasFiltro,$plFiltro, 'E');
      $mes2Saida = retornaBuscaComoArray($datasFiltro,$plFiltro, 'S');
      
      $pl2Entrada = $mes2Entrada['pl']; 
      
      $pl2Saida      = $mes2Saida['pl'];
      $saldoIni_p2   = $mes2Saida['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r2   = $mes2Saida['saldoIni_r'][0]['SaldoInicialRealizado'];

      $segundaColuna = true;
    }

    if(($i+2) <= $diaFim) {
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
      
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio3 = $anoData.'-'.$mesData.'-'.($i+2);
      $dataFiltroDiaFim3 = $anoData.'-'.$mesData.'-'.($i+2);
      // print($dataFiltroDiaInicio2);
      // print("###");
      // print($dataFiltroDiaFim2);
      // print("/////");
      $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio3;
      $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim3;

     //Pea TODOS os dads do dia $i+1 se ele estiver na faixa de dias do filtro
      $mes3Entrada = retornaBuscaComoArray($datasFiltro,$plFiltro, 'E');
      $mes3Saida = retornaBuscaComoArray($datasFiltro,$plFiltro, 'S');

      $pl3Entrada = $mes3Entrada['pl']; 

      $pl3Saida      = $mes3Saida['pl'];
      //$cc3         = $mes3Saida['cc'];
      $saldoIni_p3   = $mes3Saida['saldoIni_p'][0]['SaldoInicialPrevisto'];
      $saldoIni_r3   = $mes3Saida['saldoIni_r'][0]['SaldoInicialRealizado'];

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
                                <div class='col-md-12'>
                                  <span>".mostraValor($saldoIni_p1)."</span>
                                </div>
                              </div>
                            </div>
                            
                            ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-12'>
                                                                      <span>".mostraValor($saldoIni_p2)."</span>
                                                                    </div>
                                                                  </div>
                                                                </div>" : "")."

                            ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                  <div class='row'>
                                                                    <div class='col-md-12'>
                                                                      <span>".mostraValor($saldoIni_p3)."</span>
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
    /*
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
    }*/

    if(isset($pl1Entrada) && (!empty($pl1Entrada))) {
      foreach($pl1Entrada as $planoConta){
        $planoContaEntrada = planoContaEntrada($planoConta["PlConNome"], $planoConta["PL_Previsto"], 
                                              ($segundaColuna)?$pl2Entrada[$planoConta["PlConId"]]['PL_Previsto']:"",
                                              ($terceiraColuna)?$pl3Entrada[$planoConta["PlConId"]]['PL_Previsto']:"",
                                              $planoConta["PL_Realizado"],
                                              ($segundaColuna)?$pl2Entrada[$planoConta["PlConId"]]['PL_Realizado']:"",
                                              ($terceiraColuna)?$pl3Entrada[$planoConta["PlConId"]]['PL_Realizado']:"", 
                                              $segundaColuna, $terceiraColuna);
        $print_ent .= $planoContaEntrada[0];

        $ValorPrimeiraColuna = $planoContaEntrada[1];
        $valorSegundaColuna = $planoContaEntrada[2];
        $valorTerceiraColuna = $planoContaEntrada[3];

        $ValorRealizadoPrimeiraColuna = $planoContaEntrada[4];
        $valorRealizadoSegundaColuna = $planoContaEntrada[5];
        $valorRealizadoTerceiraColuna = $planoContaEntrada[6];

        $totalPrevistoPrimeiraColuna += $ValorPrimeiraColuna;
        $totalPrevistoSegundaColuna += $valorSegundaColuna;
        $totalPrevistoTerceiraColuna += $valorTerceiraColuna;

        $totalRealizadoPrimeiraColuna += $ValorRealizadoPrimeiraColuna;
        $totalRealizadoSegundaColuna += $valorRealizadoSegundaColuna;
        $totalRealizadoTerceiraColuna += $valorRealizadoTerceiraColuna;
      }    
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
    /*  
    foreach($rowPLanoContaPaga as $planoContaPaga) {
      $planoContaSaida = planoContaSaida($planoContaPaga['PlConId'], $planoContaPaga['PlConNome'], $rowCentroDeCusto, $segundaColuna, $terceiraColuna, $data1, $data2, $data3, $planoContaPaga['PrevistoSaida']);
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
    }*/

    if(isset($pl1Saida) && (!empty($pl1Saida))) {
      //var_dump($pl1Saida);
      foreach($pl1Saida as $planoConta){
        $planoContaSaida = planoContaSaida($planoConta["PlConNome"], $planoConta["PL_Previsto"], 
                                           ($segundaColuna)?$pl2Saida[$planoConta["PlConId"]]['PL_Previsto']:"",
                                           ($terceiraColuna)?$pl3Saida[$planoConta["PlConId"]]['PL_Previsto']:"",
                                           $planoConta["PL_Realizado"], 
                                           ($segundaColuna)?$pl2Saida[$planoConta["PlConId"]]['PL_Realizado']:"",
                                           ($terceiraColuna)?$pl3Saida[$planoConta["PlConId"]]['PL_Realizado']:"",
                                           $rowCentroDeCusto, $segundaColuna, $terceiraColuna);
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
                              <div class='col-md-12'>
                                <span>".mostraValor($saldoIni_r1)."</span>
                              </div>
                            </div>
                          </div>
                            
                          ".($segundaColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                                <div class='row'>
                                                                  <div class='col-md-12'>
                                                                    <span>". mostraValor($saldoIni_r2)."</span>
                                                                  </div>
                                                                </div>
                                                              </div>" : "")."

                          ".($terceiraColuna ? "<div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                                                              <div class='row'>
                                                                <div class='col-md-12'>
                                                                  <span>". mostraValor($saldoIni_r3)."</span>
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
    if($controlador) {
      break;
    }

    if(($i+1) <= $diaFim) {
        $i += 2;
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
$total1 = microtime(true) - $inicio1;
		 echo '<span style="background-color:yellow; padding: 10px; font-size:24px;">Tempo de execução do script: ' . round($total1, 2).' segundos</span>'; 

print($print);