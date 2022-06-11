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

//Consulta os planos de Contas  que pertecem ao  determinado grupo
function retornaBuscaComoArray($datasFiltro, $datasFiltro2, $datasFiltro3, $datasFiltro4, $plFiltro, $grupoPlanoConta, $tipo) {
  include('global_assets/php/conexao.php');

  if($tipo == 'E') {
    $sql = "SELECT PlConId, PlConNome,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as PrevistoSaida,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as RealizadoSaida,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro2['data_inicio_mes']."', '".$datasFiltro2['data_fim_mes']."', '".$tipo."') as PrevistoSaida2,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro2['data_inicio_mes']."', '".$datasFiltro2['data_fim_mes']."', '".$tipo."') as RealizadoSaida2,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro3['data_inicio_mes']."', '".$datasFiltro3['data_fim_mes']."', '".$tipo."') as PrevistoSaida3,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro3['data_inicio_mes']."', '".$datasFiltro3['data_fim_mes']."', '".$tipo."') as RealizadoSaida3,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro4['data_inicio_mes']."', '".$datasFiltro4['data_fim_mes']."', '".$tipo."') as PrevistoSaida4,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro4['data_inicio_mes']."', '".$datasFiltro4['data_fim_mes']."', '".$tipo."') as RealizadoSaida4
            FROM PlanoConta
            WHERE PlConId in ($plFiltro) and PlConNatureza = 'R' AND PlConGrupo = $grupoPlanoConta AND PlConTipo = 'S'
            ORDER BY PlConNome ASC";
    $result = $conn->query($sql);
    $rowPlanoContaSintetica = $result->fetchAll(PDO::FETCH_ASSOC);
  }else {
    $sql = "SELECT PlConId, PlConNome,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as PrevistoSaida,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro['data_inicio_mes']."', '".$datasFiltro['data_fim_mes']."', '".$tipo."') as RealizadoSaida,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro2['data_inicio_mes']."', '".$datasFiltro2['data_fim_mes']."', '".$tipo."') as PrevistoSaida2,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro2['data_inicio_mes']."', '".$datasFiltro2['data_fim_mes']."', '".$tipo."') as RealizadoSaida2,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro3['data_inicio_mes']."', '".$datasFiltro3['data_fim_mes']."', '".$tipo."') as PrevistoSaida3,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro3['data_inicio_mes']."', '".$datasFiltro3['data_fim_mes']."', '".$tipo."') as RealizadoSaida3,
                  dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro4['data_inicio_mes']."', '".$datasFiltro4['data_fim_mes']."', '".$tipo."') as PrevistoSaida4,
                  dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", PlConCodigo, '".$datasFiltro4['data_inicio_mes']."', '".$datasFiltro4['data_fim_mes']."', '".$tipo."') as RealizadoSaida4
            FROM PlanoConta
            WHERE PlConId in ($plFiltro) and PlConNatureza = 'D' AND PlConGrupo = $grupoPlanoConta AND PlConTipo = 'S'
            ORDER BY PlConNome ASC";
    $result = $conn->query($sql);
    $rowPlanoContaSintetica = $result->fetchAll(PDO::FETCH_ASSOC);
  }

  $regAt = '';
  $cont = 0;
  
  if(count($rowPlanoContaSintetica) > 0){
    foreach($rowPlanoContaSintetica as $rowCC) {
      if($regAt != $rowCC['PlConId']) {
        $regAt = $rowCC['PlConId'];
  
        $cont = 0;
  
        //reserva os dados do plano de contas
        $pl[$regAt]['PlConId']         = $rowCC['PlConId']; 
        $pl[$regAt]['PlConNome']       = $rowCC['PlConNome'];
        $pl[$regAt]['PL_Previsto']     = $rowCC['PrevistoSaida'];
        $pl[$regAt]['PL_Realizado']    = $rowCC['RealizadoSaida'];
        $pl[$regAt]['PL_Previsto2']    = $rowCC['PrevistoSaida2'];
        $pl[$regAt]['PL_Realizado2']   = $rowCC['RealizadoSaida2'];
        $pl[$regAt]['PL_Previsto3']    = $rowCC['PrevistoSaida3'];
        $pl[$regAt]['PL_Realizado3']   = $rowCC['RealizadoSaida3'];
        $pl[$regAt]['PL_Previsto4']    = $rowCC['PrevistoSaida4'];
        $pl[$regAt]['PL_Realizado4']   = $rowCC['RealizadoSaida4'];
        
      }
  
      $cont++;
    }
      
    $retorno = array('pl'=>$pl);
    
    unset($pl);
    unset($result);
    unset($rowPlanoContaSintetica);
    
    return $retorno;
  } else {
    return false;
  }
}

//Gerar os dados dos planos de Contas Sintéticos
function planoConta($idPlanoConta1, $nome, $valorPrevisto, $valorPrevisto2, $valorPrevisto3, $valorPrevisto4, $valorRealizado, $valorRealizado2, $valorRealizado3, $valorRealizado4, 
                           $segundaColuna, $terceiraColuna, $quartaColuna, $data, $codigoGrupo, $indice) {
  include('global_assets/php/conexao.php');

  $data2 = '';
  $data3 = '';
  $data4 = '';

  if($segundaColuna) {
    $arrayData2 = explode('-', $data);
    $dia2 = $arrayData2[2] + 1;
    $data2 = $arrayData2[0]."-".$arrayData2[1]."-".$dia2;
  }

  if($terceiraColuna) {
    $arrayData3 = explode('-', $data);
    $dia3 = $arrayData3[2] + 2;
    $data3 = $arrayData3[0]."-".$arrayData3[1]."-".$dia3;
  }

  if($quartaColuna) {
    $arrayData4 = explode('-', $data);
    $dia4 = $arrayData4[2] + 3;
    $data4 = $arrayData4[0]."-".$arrayData4[1]."-".$dia4;
  }

  //Foi inserido os inputs de datas para a primeira, segunda... coluna para ser feito a consulta através dela quando for consultar na página de Fluxo de Caixa
  //A segunda e terceira coluna são o segundo e terceiro dia que é mostrado em cada paginação do Fluxo de Caixa

  $resposta[0] = "
    <div class='row' style='background: #a3a3a3; line-height: 3rem; box-sizing:border-box;'>
      <div id='planoConta".$indice."' class='col-lg-3 planoConta' style='border-right: 1px dotted black; cursor:pointer;'>
        <input type='hidden' id='idPlanoConta".$indice."' value='".$idPlanoConta1."'>
        <input type='hidden' id='data".$indice."' value='".$data."'>
        <input type='hidden' id='dataSegundaColuna".$indice."' value='".$data2."'>
        <input type='hidden' id='dataTerceiraColuna".$indice."' value='".$data3."'>
        <input type='hidden' id='dataQuartaColuna".$indice."' value='".$data4."'>
        <span><span id='simbolo".$indice."' style='font-weight: bold; color: #607D8B;'>( + ) </span>".$nome."</span>
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
    }

    if($quartaColuna) {
      $resposta[0] .= "
      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
        <div class='row'>
          <div class='col-md-6'>
            <span>".mostraValor($valorPrevisto4)."</span>
          </div>

          <div class='col-md-6'>
            <span>".mostraValor($valorRealizado4)."</span>
          </div>
        </div>
      </div>";
    }

    $resposta[0] .= "  
    </div>

    <div id='planoContaPai".$indice."' style='padding-top: 0; padding-bottom: 0'>
    </div>";

    return $resposta;
}

//Gera o saldo Inicial e Final - previsto e realizado
function retornoSaldo($datasFiltro, $datasFiltro2, $datasFiltro3, $datasFiltro4) {
  include('global_assets/php/conexao.php');

  //Pega o saldo inicial e final
  $sql_saldo_ini_p    = "select dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialPrevisto,
                                dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro2['data_inicio_mes']."') as SaldoInicialPrevisto2,
                                dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro3['data_inicio_mes']."') as SaldoInicialPrevisto3,
                                dbo.fnFluxoCaixaSaldoInicialPrevisto(".$_SESSION['UnidadeId'].",'".$datasFiltro4['data_inicio_mes']."') as SaldoInicialPrevisto4,
                                dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoInicialRealizado,
                                dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro2['data_inicio_mes']."') as SaldoInicialRealizado2,
                                dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro3['data_inicio_mes']."') as SaldoInicialRealizado3,
                                dbo.fnFluxoCaixaSaldoInicialRealizado(".$_SESSION['UnidadeId'].",'".$datasFiltro4['data_inicio_mes']."') as SaldoInicialRealizado4,
                                
                                dbo.fnFluxoCaixaSaldoFinal(".$_SESSION['UnidadeId'].",'".$datasFiltro['data_inicio_mes']."') as SaldoFinal,
                                dbo.fnFluxoCaixaSaldoFinal(".$_SESSION['UnidadeId'].",'".$datasFiltro2['data_inicio_mes']."') as SaldoFinal2,
                                dbo.fnFluxoCaixaSaldoFinal(".$_SESSION['UnidadeId'].",'".$datasFiltro3['data_inicio_mes']."') as SaldoFinal3,
                                dbo.fnFluxoCaixaSaldoFinal(".$_SESSION['UnidadeId'].",'".$datasFiltro4['data_inicio_mes']."') as SaldoFinal4";
  $resultSaldo = $conn->query($sql_saldo_ini_p);
  $saldo = $resultSaldo->fetch(PDO::FETCH_ASSOC);

  return $saldo;
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

//Consulta os grupos dos planos de contas
$sql = "SELECT GrConId, GrConNome, GrConNomePersonalizado, GrConCodigo, SituaChave
		FROM GrupoConta
		JOIN Situacao on SituaId = GrConStatus
	  WHERE GrConUnidade = ". $_SESSION['UnidadeId'] ." AND SituaChave = 'ATIVO' AND GrConCodigo != ''
		ORDER BY GrConCodigo ASC";
$result = $conn->query($sql);
$rowGrupo = $result->fetchAll(PDO::FETCH_ASSOC);

$print = "
  <div id='carouselExampleControls' class='carousel slide' data-ride='carousel'>
    <div class='carousel-inner'> ";

//Filtra por dia
if($typeFiltro == "D"){
  $mesArray = explode('-', $dataInicio);
  $anoData = $mesArray[0];
  $mesData = (int)$mesArray[1];

  $controlador = (($diaFim - $diaInicio) < 3 ) ? true : false; 

  $data = explode('-', $dataInicio);
  $dataFormatado = $data[0].'-'.$data[1].'-'.$data[2];
  $anoMesFormatado = $data[0].'-'.$data[1];

  $indice = 1;
  $receitaTotal = 0;
  for($i = $diaInicio;$i <= $diaFim;$i++) {
    $segundaColuna = false;
    $terceiraColuna = false;
    $quartaColuna = false;

    //limpa as variaveis
    if(isset($mes)) {
      unset($mes);
      unset($saldoIni_p1);
      unset($saldoIni_r1);
      unset($saldoIni_p2);
      unset($saldoIni_r2);
      unset($saldoIni_p3);
      unset($saldoIni_r3);
    }
    
    $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.$i;   
  
    // $ultimo_dia = date("t", mktime(0,0,0,($i),'01',$anoData));
    $dataFiltroDiaInicio1 = $anoData.'-'.$mesData.'-'.$i;
    $dataFiltroDiaFim1 = $anoData.'-'.$mesData.'-'.$i;

    
    $dataFiltroDiaInicio2 = '';
    $dataFiltroDiaFim2 = '';
    
    $dataFiltroDiaInicio3 = '';
    $dataFiltroDiaFim3 = '';

    $dataFiltroDiaInicio4 = '';
    $dataFiltroDiaFim4 = '';
    
    $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio1;
    $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim1;
    $datasFiltro2['data_inicio_mes'] = '';
    $datasFiltro2['data_fim_mes'] = '';
    $datasFiltro3['data_inicio_mes'] = '';
    $datasFiltro3['data_fim_mes'] = '';
    $datasFiltro4['data_inicio_mes'] = '';
    $datasFiltro4['data_fim_mes'] = '';
    
    if(($i+1) <= $diaFim) {
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
  
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio2 = $anoData.'-'.$mesData.'-'.($i+1);
      $dataFiltroDiaFim2 = $anoData.'-'.$mesData.'-'.($i+1);
      
      $datasFiltro2['data_inicio_mes'] = $dataFiltroDiaInicio2;
      $datasFiltro2['data_fim_mes'] = $dataFiltroDiaFim2;

      $segundaColuna = true;
    }

    if(($i+2) <= $diaFim) {
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
  
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio3 = $anoData.'-'.$mesData.'-'.($i+2);
      $dataFiltroDiaFim3 = $anoData.'-'.$mesData.'-'.($i+2);
      
      $datasFiltro3['data_inicio_mes'] = $dataFiltroDiaInicio3;
      $datasFiltro3['data_fim_mes'] = $dataFiltroDiaFim3;

      $terceiraColuna = true;
    }

    if(($i+3) <= $diaFim)  {
      $dataFiltro = trim(date('Y-m',strtotime($dataInicio))).'-'.($i+1);      
  
      // $ultimo_dia = date("t", mktime(0,0,0,$i+1,'01',$anoData));
      $dataFiltroDiaInicio4 = $anoData.'-'.$mesData.'-'.($i+3);
      $dataFiltroDiaFim4 = $anoData.'-'.$mesData.'-'.($i+3);
      
      $datasFiltro4['data_inicio_mes'] = $dataFiltroDiaInicio4;
      $datasFiltro4['data_fim_mes'] = $dataFiltroDiaFim4;

      $quartaColuna = true;
    }

    $arraySaldo  = retornoSaldo($datasFiltro, $datasFiltro2, $datasFiltro3, $datasFiltro4);
    $saldoIni_p1 = $arraySaldo['SaldoInicialPrevisto'];
    $saldoIni_r1 = $arraySaldo['SaldoInicialRealizado'];
    $saldoIni_p2 = $arraySaldo['SaldoInicialPrevisto2'];
    $saldoIni_r2 = $arraySaldo['SaldoInicialRealizado2'];
    $saldoIni_p3 = $arraySaldo['SaldoInicialPrevisto3'];
    $saldoIni_r3 = $arraySaldo['SaldoInicialRealizado3']; 
    $saldoIni_p4 = $arraySaldo['SaldoInicialPrevisto4'];
    $saldoIni_r4 = $arraySaldo['SaldoInicialRealizado4']; 

    $saldoFin_p1 = $arraySaldo['SaldoFinal'];
    $saldoFin_p2 = $arraySaldo['SaldoFinal2'];
    $saldoFin_p3 = $arraySaldo['SaldoFinal3'];
    $saldoFin_p4 = $arraySaldo['SaldoFinal4'];

    //Por padrão a data estava vindo com um valor a mais, porém isso foi corrigido logo abaixo
    $dataFormatado = $data[0].'-'.$data[1].'-'.$data[2];
    $anoMesFormatado = $data[0].'-'.$data[1];
    
    /* Obs.: utf8_encode é para o servidor da AZURE. Localmente não precisaria, mas para o servidor sim. */  
    $print .= " 
      <div class='carousel-item ".($i == $diaInicio ? " active":"")."'> 
        <div class='row'>
          <div class='col-lg-12'>
            <!-- Basic responsive configuration -->
            <div class='card-body' >
              <div class='row'>
                  
                <div class='col-lg-3'>
                </div>

                <div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-right: 2px; '>
                  <span><strong>".str_pad($i, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($dataFormatado))))."</strong></span>
                </div>

                  ".($segundaColuna ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                  <span><strong>".str_pad($i+1, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoMesFormatado ."-".str_pad($i+1, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                </div>" : "")."

                ".($terceiraColuna ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                  <span><strong>".str_pad($i+2, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoMesFormatado ."-".str_pad($i+2, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                </div>" : "")." 
                
                ".($quartaColuna ? "<div class='col-lg-2' style='text-align:center; border-top: 2px solid #ccc; padding-top: 1rem; margin-left: 2px;'>
                  <span><strong>".str_pad($i+3, 2, '0', STR_PAD_LEFT)." ".utf8_encode(ucfirst(strftime("%B de %Y", strtotime($anoMesFormatado ."-".str_pad($i+3, 2, '0', STR_PAD_LEFT)))))."</strong></span>
                </div>" : "")." 
              </div>
            </div>
          </div>
        </div>";
  
    //------------------------------------------------------------------------------
    // limpa as variaveis que vao receber o plano de contas
    // <!--  usei essas variaveis para nao ter q fazer dois loops -->
    $print_corpo = '';
    $print_sai = '';
    //==============================================================================
    
    $print_corpo .= "
      <div class='card-body' style='padding-top: 0;'>
        <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
          <div class='col-lg-3' style='border-right: 1px dotted black;'>
              <strong></strong>
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
          
          ".($segundaColuna ? "
          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
            <div class='row'>
              <div class='col-md-6'>
                <span><strong>Previsto</strong></span>
              </div>
              <div class='col-md-6'>
                <span><strong>Realizado</strong></span>
              </div>
            </div>
          </div>" : "")."
          
          ".($terceiraColuna ? "
          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
            <div class='row'>
              <div class='col-md-6'>
                <span><strong>Previsto</strong></span>
              </div>
              <div class='col-md-6'>
                <span><strong>Realizado</strong></span>
              </div>
            </div>
          </div>" : "")."

          ".($quartaColuna ? "
          <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
            <div class='row'>
              <div class='col-md-6'>
                <span><strong>Previsto</strong></span>
              </div>
              <div class='col-md-6'>
                <span><strong>Realizado</strong></span>
              </div>
            </div>
          </div>" : "")."

        </div>
      </div>
      
      <div class='row''>
        <!-- SALDO INICIAL -->
        <div class='col-lg-12'>
          <!-- Basic responsive configuration -->
            <div class='card-body' style='padding-top: 0;'>
              <div class='row' style='background: #a3a3a3; line-height: 3rem; box-sizing:border-box'>
                <div class='col-lg-3' style='border-right: 1px dotted black;'>
                  <span><strong>Saldo Inicial</strong></span>
                </div>

                <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                  <div class='row'>
                    <div class='col-md-6'>
                      <span><b>".mostraValor($saldoIni_p1)."</b></span>
                    </div>

                    <div class='col-md-6'>
                      <span><b>".mostraValor($saldoIni_r1)."</b></span>
                    </div>
                  </div>
                </div>
                
                ".($segundaColuna ? "
                <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                  <div class='row'>
                    <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_p2)."</b></span>
                  </div>

                  <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_r2)."</b></span>
                  </div>
                  </div>
                </div>" : "")."

                ".($terceiraColuna ? "
                <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                  <div class='row'>
                    <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_p3)."</b></span>
                  </div>

                  <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_r3)."</b></span>
                  </div>
                  </div>
                </div>" : "")."

                ".($quartaColuna ? "
                <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                  <div class='row'>
                    <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_p4)."</b></span>
                  </div>

                  <div class='col-md-6'>
                    <span><b>".mostraValor($saldoIni_r4)."</b></span>
                  </div>
                  </div>
                </div>" : "")."

              </div>
            </div>
        </div>
      </div>
      <!-- SALDO INICIAL -->";

    //Primeiro o tipo de grupo é definido como entrada, pois sempre começa pela receita, dps dentro do próprio loop ele se torna 'S' para trazer a saída
    $tipoGrupo = 'E';

    $totalPrevistoPrimeiraColuna = 0;
    $totalPrevistoSegundaColuna = 0;
    $totalPrevistoTerceiraColuna = 0;
    $totalPrevistoQuartaColuna = 0;

    $totalRealizadoPrimeiraColuna = 0;
    $totalRealizadoSegundaColuna = 0;
    $totalRealizadoTerceiraColuna = 0;
    $totalRealizadoQuartaColuna = 0;

    $percentual = 0;
    $percentualPrevisto1 = 0;
    $percentualRealizado1 = 0;
    $percentualPrevisto2 = 0;
    $percentualRealizado2 = 0;
    $percentualPrevisto3 = 0;
    $percentualRealizado3 = 0;
    $percentualPrevisto4 = 0;
    $percentualRealizado4 = 0;

    foreach($rowGrupo as $grupo) {
      $nomeGrupo = $grupo['GrConNomePersonalizado'] != '' ? $grupo['GrConNomePersonalizado'] :  $grupo['GrConNome'];
      
      $sql = "SELECT dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio1."', '".$dataFiltroDiaFim1."', '".$tipoGrupo."') as PrevistoSaidaGrupo1,
                     dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio1."', '".$dataFiltroDiaFim1."', '".$tipoGrupo."') as RealizadoSaidaGrupo1,
                     dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio2."', '".$dataFiltroDiaFim2."', '".$tipoGrupo."') as PrevistoSaidaGrupo2,
                     dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio2."', '".$dataFiltroDiaFim2."', '".$tipoGrupo."') as RealizadoSaidaGrupo2,
                     dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio3."', '".$dataFiltroDiaFim3."', '".$tipoGrupo."') as PrevistoSaidaGrupo3,
                     dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio3."', '".$dataFiltroDiaFim3."', '".$tipoGrupo."') as RealizadoSaidaGrupo3,
                     dbo.fnPlanoContasPrevisto(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio4."', '".$dataFiltroDiaFim4."', '".$tipoGrupo."') as PrevistoSaidaGrupo4,
                     dbo.fnPlanoContasRealizado(".$_SESSION['UnidadeId'].", ".$grupo['GrConCodigo'].", '".$dataFiltroDiaInicio4."', '".$dataFiltroDiaFim4."', '".$tipoGrupo."') as RealizadoSaidaGrupo4";
      $resultGrupo = $conn->query($sql);
      $rowTotalGrupo = $resultGrupo->fetch(PDO::FETCH_ASSOC);

      $totalGrupo1 = '';
      $totalPrevisto1 = $rowTotalGrupo['PrevistoSaidaGrupo1'];
      $totalRealizado1 = $rowTotalGrupo['RealizadoSaidaGrupo1'];
      $totalPrevisto2 = $rowTotalGrupo['PrevistoSaidaGrupo2'];
      $totalRealizado2 = $rowTotalGrupo['RealizadoSaidaGrupo2'];
      $totalPrevisto3 = $rowTotalGrupo['PrevistoSaidaGrupo3'];
      $totalRealizado3 = $rowTotalGrupo['RealizadoSaidaGrupo3'];
      $totalPrevisto4 = $rowTotalGrupo['PrevistoSaidaGrupo4'];
      $totalRealizado4 = $rowTotalGrupo['RealizadoSaidaGrupo4'];

      if($tipoGrupo == 'E') {
        $totalPrevistoPrimeiraColuna = $totalPrevisto1;
        $totalPrevistoSegundaColuna = $totalPrevisto2;
        $totalPrevistoTerceiraColuna = $totalPrevisto3;
        $totalPrevistoQuartaColuna = $totalPrevisto4;

        $totalRealizadoPrimeiraColuna = $totalRealizado1;
        $totalRealizadoSegundaColuna = $totalRealizado2;
        $totalRealizadoTerceiraColuna = $totalRealizado3;
        $totalRealizadoQuartaColuna = $totalRealizado4;
      }else {
        $totalPrevistoPrimeiraColuna -= $totalPrevisto1;
        $totalPrevistoSegundaColuna -= $totalPrevisto2;
        $totalPrevistoTerceiraColuna -= $totalPrevisto3;
        $totalPrevistoQuartaColuna -= $totalPrevisto4;

        $totalRealizadoPrimeiraColuna -= $totalRealizado1;
        $totalRealizadoSegundaColuna -= $totalRealizado2;
        $totalRealizadoTerceiraColuna -= $totalRealizado3;
        $totalRealizadoQuartaColuna -= $totalRealizado4;
      }
     
      $print_corpo .= "<!-- ENTRADA -->
      <div class='row'>
        <div class='col-lg-12'>
          <!-- Basic responsive configuration -->
          
          <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
            <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
              <div class='col-lg-3' style='border-right: 1px dotted black;'>
                  <strong>".$nomeGrupo."</strong>
              </div> 
              
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span><strong>".mostraValor($totalPrevisto1)."</strong></span>
                  </div>
                  <div class='col-md-6'>
                    <span><strong>".mostraValor($totalRealizado1)."</strong></span>
                  </div>
                </div>
              </div>
              
              ".($segundaColuna ? "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span><strong>".mostraValor($totalPrevisto2)."</strong></span>
                  </div>
                  <div class='col-md-6'>
                      <span><strong>".mostraValor($totalRealizado2)."</strong></span>
                  </div>
                </div>
              </div>" : "")."
              
              ".($terceiraColuna ? "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span><strong>".mostraValor($totalPrevisto3)."</strong></span>
                  </div>
                  <div class='col-md-6'>
                        <span><strong>".mostraValor($totalRealizado3)."</strong></span>
                  </div>
                </div>
              </div>" : "")."

              ".($quartaColuna ? "
              <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                <div class='row'>
                  <div class='col-md-6'>
                    <span><strong>".mostraValor($totalPrevisto4)."</strong></span>
                  </div>
                  <div class='col-md-6'>
                        <span><strong>".mostraValor($totalRealizado4)."</strong></span>
                  </div>
                </div>
              </div>" : "")."

            </div>
          </div> 
          
          <div class='card-body' style='padding-top: 0;padding-bottom: 0'>";

        $mes = retornaBuscaComoArray($datasFiltro, $datasFiltro2, $datasFiltro3, $datasFiltro4, $plFiltro, $grupo['GrConId'], $tipoGrupo);

        $datasFiltro['data_inicio_mes'] = $dataFiltroDiaInicio1;
        $datasFiltro['data_fim_mes'] = $dataFiltroDiaFim1;
        
        $arrayPlanoConta = isset($mes['pl']) ? $mes['pl'] : null;

        if(isset($arrayPlanoConta) && (!empty($arrayPlanoConta))) {
          foreach($arrayPlanoConta as $planoConta){
            $resultadoPlanoConta = planoConta($planoConta["PlConId"], $planoConta["PlConNome"], $planoConta["PL_Previsto"], 
                                            ($segundaColuna)?$planoConta['PL_Previsto2']:"",
                                            ($terceiraColuna)?$planoConta['PL_Previsto3']:"",
                                            ($quartaColuna)?$planoConta['PL_Previsto4']:"",
                                            $planoConta["PL_Realizado"],
                                            ($segundaColuna)?$planoConta['PL_Realizado2']:"",
                                            ($terceiraColuna)?$planoConta['PL_Realizado3']:"", 
                                            ($quartaColuna)?$planoConta['PL_Realizado4']:"", 
                                            $segundaColuna, $terceiraColuna, $quartaColuna, $dataFiltroDiaInicio1, $grupo['GrConCodigo'], $indice);
            $print_corpo .= $resultadoPlanoConta[0];

            $indice++;
          }   
        }

        //Os totalizadores só aparecem em alguns grupos específicos
        $tituloTotalizador = 'sem rodape';
        if($grupo['GrConCodigo'] == 1) {
          $tituloTotalizador = 'Receita operacional líquida';

          $receitaTotal1 = isset($rowTotalGrupo['PrevistoSaidaGrupo1']) ? $rowTotalGrupo['PrevistoSaidaGrupo1'] : 0;
          $receitaTotal2 = isset($rowTotalGrupo['PrevistoSaidaGrupo2']) ? $rowTotalGrupo['PrevistoSaidaGrupo2'] : 0;
          $receitaTotal3 = isset($rowTotalGrupo['PrevistoSaidaGrupo3']) ? $rowTotalGrupo['PrevistoSaidaGrupo3'] : 0;
          $receitaTotal4 = isset($rowTotalGrupo['PrevistoSaidaGrupo4']) ? $rowTotalGrupo['PrevistoSaidaGrupo4'] : 0;
        } else if($grupo['GrConCodigo'] == 5) {
          $tituloTotalizador = 'Margem de contribuição';
        }else if($grupo['GrConCodigo'] == 6) {
          $tituloTotalizador = 'Resultado Operacional';
        }else if($grupo['GrConCodigo'] == 7) {
          $tituloTotalizador = 'Variação de caixa';
        }

        if($receitaTotal1 != 0) {
          $percentualPrevisto1 = ($totalPrevistoPrimeiraColuna * 100) / $receitaTotal1;
          $percentualRealizado1 = ($totalRealizadoPrimeiraColuna * 100) / $receitaTotal1;
  
          $percentualPrevisto1 = is_float($percentualPrevisto1) ? number_format($percentualPrevisto1, 1, '.', '') : $percentualPrevisto1;
          $percentualRealizado1 = is_float($percentualRealizado1) ? number_format($percentualRealizado1, 1, '.', '') : $percentualRealizado1;
        }
        
        $print_corpo .= "
          </div>
            <div class='row'>
              <div class='col-lg-12'>
                <!-- Basic responsive configuration -->
                  <div class='card-body' style=''>";
        
        if($tituloTotalizador != 'sem rodape') {
          $print_corpo .="
                    <div class='row' style='background: #a3a3a3; line-height: 3rem; box-sizing:border-box'>
                      <div class='col-lg-3' style='border-right: 1px dotted black;'>
                        <span><strong>(=) ".$tituloTotalizador."</strong></span>
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
                      </div>";

        if($segundaColuna) {
          if($receitaTotal2 != 0) {
            $percentualPrevisto2 = ($totalPrevistoSegundaColuna * 100) / $receitaTotal2;
            $percentualRealizado2 = ($totalRealizadoSegundaColuna * 100) / $receitaTotal2;
  
            $percentualPrevisto2 = is_float($percentualPrevisto2) ? number_format($percentualPrevisto2, 1, '.', '') : $percentualPrevisto2;
            $percentualRealizado2 = is_float($percentualRealizado2) ? number_format($percentualRealizado2, 1, '.', '') : $percentualRealizado2;
          }


          $print_corpo .="
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".mostraValor($totalPrevistoSegundaColuna)."</span>
                          </div>
  
                          <div class='col-md-6'>
                            <span>".mostraValor($totalRealizadoSegundaColuna)."</span>
                          </div>
                        </div>
                      </div>";
        }

        if($terceiraColuna) {
          if($receitaTotal3 != 0) {
            $percentualPrevisto3 = ($totalPrevistoTerceiraColuna * 100) / $receitaTotal3;
            $percentualRealizado3 = ($totalRealizadoTerceiraColuna * 100) / $receitaTotal3;
  
            $percentualPrevisto3 = is_float($percentualPrevisto3) ? number_format($percentualPrevisto3, 1, '.', '') : $percentualPrevisto3;
            $percentualRealizado3 = is_float($percentualRealizado3) ? number_format($percentualRealizado3, 1, '.', '') : $percentualRealizado3;
          }


          $print_corpo .= "
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".mostraValor($totalPrevistoTerceiraColuna)."</span>
                          </div>

                          <div class='col-md-6'>
                            <span>".mostraValor($totalRealizadoTerceiraColuna)."</span>
                          </div>
                        </div>
                      </div>";
        }

        if($quartaColuna) {
          if($receitaTotal4 != 0) {
            $percentualPrevisto4 = ($totalPrevistoQuartaColuna * 100) / $receitaTotal4;
            $percentualRealizado4 = ($totalRealizadoQuartaColuna * 100) / $receitaTotal4;
  
            $percentualPrevisto4 = is_float($percentualPrevisto4) ? number_format($percentualPrevisto4, 1, '.', '') : $percentualPrevisto4;
            $percentualRealizado4 = is_float($percentualRealizado4) ? number_format($percentualRealizado4, 1, '.', '') : $percentualRealizado4;
          }


          $print_corpo .= "
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".mostraValor($totalPrevistoQuartaColuna)."</span>
                          </div>

                          <div class='col-md-6'>
                            <span>".mostraValor($totalRealizadoQuartaColuna)."</span>
                          </div>
                        </div>
                      </div>";
        }
          
        $print_corpo .= " </div>
  
                    <div class='row' style='background: #a3a3a3; line-height: 3rem; box-sizing:border-box'>
                      <div class='col-lg-3' style='border-right: 1px dotted black;'>
                        <span style='padding-left: 20px;'><strong>".$tituloTotalizador." (%)</strong></span>
                      </div>
  
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".$percentualPrevisto1."%</span>
                          </div>
  
                          <div class='col-md-6'>
                            <span>".$percentualRealizado1."%</span>
                          </div>
                        </div>
                      </div>
  
                      ".($segundaColuna ? "
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".$percentualPrevisto2."%</span>
                          </div>
  
                          <div class='col-md-6'>
                              <span>".$percentualRealizado2."%</span>
                          </div>
                        </div>
                      </div>" : "")."
  
                      ".($terceiraColuna ? "
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".$percentualPrevisto3."%</span>
                          </div>
    
                          <div class='col-md-6'>
                              <span>".$percentualRealizado3."%</span>
                          </div>
                        </div>
                      </div>" : "")."

                      ".($quartaColuna ? "
                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>".$percentualPrevisto4."%</span>
                          </div>
    
                          <div class='col-md-6'>
                              <span>".$percentualRealizado4."%</span>
                          </div>
                        </div>
                      </div>" : "")."
                    </div>";
        }
      
        $print_corpo .= "        
                  </div>
                </div>
                </div>
                
              </div>
            </div>
            <!-- TOTAL ENTRADA -->
            <!-- ENTRADA -->";
            
      $tipoGrupo = 'S';
    }

        //----------------------------------------------------------------------
        //junta tudo no $print principal
        $print .= $print_corpo . "
            <!-- SALDO FINAL -->
            <div class='row' style='margin-top: 1rem;'>
              <div class='col-lg-12'>
                <!-- Basic responsive configuration -->
                  <div class='card-body' style='padding-top: 0;'>
                  <div class='row' style='background: #a3a3a3; line-height: 3rem; box-sizing:border-box'>
                    <div class='col-lg-3' style='border-right: 1px dotted black;'>
                      <span><strong>SALDO FINAL</strong></span>
                    </div>

                    <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                      <div class='row'>
                        <div class='col-md-12'>
                          <span>".mostraValor($saldoFin_p1)."</span>
                        </div>
                      </div>
                    </div>
                      
                    ".($segundaColuna ? "
                    <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                      <div class='row'>
                        <div class='col-md-12'>
                          <span>".mostraValor($saldoFin_p2)."</span>
                        </div>
                      </div>
                    </div>" : "")."

                    ".($terceiraColuna ? "
                    <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                    <div class='row'>
                      <div class='col-md-12'>
                        <span>".mostraValor($saldoFin_p3)."</span>
                      </div>
                    </div>
                  </div>" : "")."

                  ".($quartaColuna ? "
                    <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                    <div class='row'>
                      <div class='col-md-12'>
                        <span>".mostraValor($saldoFin_p4)."</span>
                      </div>
                    </div>
                  </div>" : "")."
                      
                </div>
              </div>
            </div>
          </div>
        </div> 
        <!-- SALDO FINAL -->";

        /*
        $print .= " <!-- SALDO FINAL -->
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
        */
    
    //Usado para ele não criar uma coluna extra
    if($controlador) {
      break;
    }

    //Usado para q o loop para depois que todos os dias sejam mostradados, pois sempre está acrescentando valores ao índice
    //evitando inclusive alguns bugs como uma coluna extra
    if(($i+1) <= $diaFim) {
        $i += 3;
    } 
  }
}

$print .= "
  </div>
    <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev' style='color:black;'>
      <span class='carousel-control-prev-icon' aria-hidden='true' ><img src='global_assets/images/lamparinas/seta-left.png' width='32' /></span>
      <span class='sr-only'>Previous</span>
    </a>

    <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next' style='color:black;'>
      <span class='carousel-control-next-icon' aria-hidden='true'><img src='global_assets/images/lamparinas/seta-right.png' width='32' /></span>
      <span class='sr-only'>Next</span>
    </a>
  </div>";

//$total1 = microtime(true) - $inicio1;
//echo '<span style="background-color:yellow; padding: 10px; font-size:24px;">Tempo de execução do script: ' . round($total1, 2).' segundos</span>'; 

print($print);