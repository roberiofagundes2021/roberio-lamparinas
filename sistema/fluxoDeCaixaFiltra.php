<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');
/* 
  --------------- $_POST -------------
  ["typeDate"]=> "D"/"M"
  ["dateInitial"]=> string(10) "2021-03-05"
  ["dateEnd"]=> string(10) "2021-03-17"
  ["cmbCentroDeCustos"]=> string(1) "4"
  ["cmbPlanoContas"]=>string(2) "78"
*/



$sql = "SELECT CnCusId,
CnCusNome, PlConNome, 
dbo.fnPrevistoCentroCusto(CnCusId, ". $_SESSION['UnidadeId'].", ) as PrevistoCC, dbo.fnRealizadoCentroCusto(CnCusId, ". $_SESSION['UnidadeId'].") as RealizadoCC,
dbo.fnPrevistoPlanoContas() as PrevistoPL, dbo.fnRealizadoPlanoContas() as RealizadoPL
FROM CentroCusto CC
JOIN PlanoContas PL
ON PlConCentroCusto = CnCusId
JOIN Situacao S1
ON S1.SituaId = CC.CnCusStatus
JOIN Situacao S2
ON S2.SituaId = PL.PlConStatus
WHERE CnCusUnidade = " . $_SESSION['UnidadeId'] . " 
and S1.SituaChave = 'ATIVO' and S2.SituaChave = 'ATIVO'
ORDER BY CnCusNome ASC";
$result = $conn->query($sql);
$rowCentroDeCustos = $result->fetchAll(PDO::FETCH_ASSOC);


foreach ($rowCentroDeCustos as $item) {

}

//$dia['01-02']['Previsto'] = 2000;
//$dia['01-02']['Realizado'] = 3000;

//$dia['02-02']['Previsto'] = 2000;
//$dia['02-02']['Realizado'] = 3000;

$print = "
        <div id='carouselExampleControls' class='carousel slide' data-ride='carousel'>
          <div class='carousel-inner'>
            <div class='carousel-item active'> ";



$print .= "
              <div class='row'>
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                    <div class='card-body' >
                      <div class='row'>
                        <div class='col-lg-2'>
                        </div>
                        <div class='col-lg-2' style='text-align:center; border-top: 2px solid #1B3280; padding-top: 1rem;'>
                          <span><strong>2021</strong></span><br/>
                          <span><strong>JAN</strong></span>
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
                        <div class='col-lg-2' style='border-right: 1px dotted black;'>
                          <span><strong>Saldo Inicial</strong></span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>2000,00</span>
                            </div>

                            <div class='col-md-6'>
                              <span>3000,00</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              <!-- SALDO INICIAL -->

              <!-- ENTRADA -->
              <div class='row'>
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                  <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                    <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                      <div class='col-lg-2' style='border-right: 1px dotted black;'><strong>ENTRADA</strong></div> 

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
                    </div>
                  </div>

                  <div class='card-body' style='padding-top: 0;'>
                    <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                      <div class='col-lg-2' style='border-right: 1px dotted black;'>
                        <span>Lista com os Centros de Custo</span>
                      </div>

                      <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <span>2000,00</span>
                          </div>

                          <div class='col-md-6'>
                            <span>3000,00</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              

              <!-- TOTAL ENTRADA -->
              <div class='row'>
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                    <div class='card-body' style='padding-top: 0;'>
                      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                        <div class='col-lg-2' style='border-right: 1px dotted black;'>
                        <span><strong>TOTAL</strong></span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>2000,00</span>
                            </div>

                            <div class='col-md-6'>
                              <span>3000,00</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              <!-- TOTAL ENTRADA -->
              <!-- ENTRADA -->

              <!-- SAIDA -->
              <div class='row' style='margin-top: 1rem;'>
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                  <div class='card-body' style='padding-top: 0; padding-bottom: 0'>
                    <div class='row' style='background: #607D8B; line-height: 3rem; box-sizing:border-box; color:white;'>
                      <div class='col-lg-2' style='border-right: 1px dotted black;'><strong>SAÍDA</strong></div> 

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
                    </div>
                  </div>

                    <div class='card-body' style='padding-top: 0;'>
                      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                        <div class='col-lg-2' style='border-right: 1px dotted black;'>
                          <span>Lista com os Centros de Custo</span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>2000,00</span>
                            </div>

                            <div class='col-md-6'>
                              <span>3000,00</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              
              <!-- TOTAL SAIDA -->
              <div class='row' >
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                    <div class='card-body' style='padding-top: 0;'>
                      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                        <div class='col-lg-2' style='border-right: 1px dotted black;'>
                          <span><strong>TOTAL</strong></span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>2000,00</span>
                            </div>

                            <div class='col-md-6'>
                              <span>3000,00</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              <!-- TOTAL SAIDA -->
              <!-- SAIDA -->

              <!-- SALDO FINAL -->
              <div class='row' style='margin-top: 1rem;'>
                <div class='col-lg-12'>
                  <!-- Basic responsive configuration -->
                    <div class='card-body' style='padding-top: 0;'>
                      <div class='row' style='background: #CCCCCC; line-height: 3rem; box-sizing:border-box'>
                        <div class='col-lg-2' style='border-right: 1px dotted black;'>
                        <span><strong>SALDO FINAL</strong></span>
                        </div>

                        <div class='dataOpeningBalance col-lg-2' style='border-right: 1px dotted black; text-align:center;'>
                          <div class='row'>
                            <div class='col-md-6'>
                              <span>2000,00</span>
                            </div>

                            <div class='col-md-6'>
                              <span>3000,00</span>
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

                      <div class='row col-lg-12' style='background: #fff; line-height: 3rem; box-sizing:border-box'>
                        <span>TOTAL SAÍDAS / TOTAL ENTRADAS * 100 = 100%</span>
                      </div>
                    </div>
                </div>
              </div>
              <!-- SALDO FINAL -->
            </div>
          </div>

          <a class='carousel-control-prev' href='#carouselExampleControls' role='button' data-slide='prev' style='color:black;'>
            <span class='carousel-control-prev-icon' aria-hidden='true' >Previous</span>
            <span class='sr-only'>Previous</span>
          </a>
          <a class='carousel-control-next' href='#carouselExampleControls' role='button' data-slide='next' style='color:black;'>
            <span class='carousel-control-next-icon' aria-hidden='true'>Next</span>
            <span class='sr-only'>Next</span>
          </a>
        </div>
      ";

print($print);