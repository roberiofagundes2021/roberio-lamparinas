<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];
	
    if (!empty($_POST['cmbUnidade'])) {
		
		if (!empty($_POST['cmbSetor'])) {
			$args[]  = "MovimDestinoSetor = " . $_POST['cmbSetor'] . " ";
			$iSetor = $_POST['cmbSetor'];
		} else{
			$iSetor = 'NULL';
		}		
		
    } else{
		$iUnidade = 'NULL';
		$iSetor = 'NULL';
	}
	
    if (!empty($_POST['cmbCategoria'])) {
        $args[]  = "ProduCategoria = " . $_POST['cmbCategoria'] . " ";
		$iCategoria = $_POST['cmbCategoria'];
    } else{
		$iCategoria = 'NULL';
	}

    if (!empty($_POST['cmbSubCategoria'])) {
        $args[]  = "ProduSubCategoria = " . $_POST['cmbSubCategoria'] . " ";
		$iSubCategoria = $_POST['cmbSubCategoria'];
    } else{
		$iSubCategoria = 'NULL';
	}

    if (!empty($_POST['cmbClassificacao'])) {
        $args[]  = "MvXPrClassificacao = " . $_POST['cmbClassificacao'] . " ";
		$iClassificacao = $_POST['cmbClassificacao'];
    } else{
		$iClassificacao = 'NULL';
	}

	$dataInicio = $_POST['inputDataInicio'];
	$dataFim = $_POST['inputDataFim'];

    if (count($args) >= 1) {
        try {

            $string = implode(" and ", $args);

            if ($string != '') {
                $string .= ' and ';
            }
			
			$sql = "SELECT ProduId, ProduNome, MvXPrValorUnitario, dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, $iSetor, $iCategoria, $iSubCategoria, $iClassificacao, '$dataInicio', '$dataFim') as Saidas,
				   (MvXPrValorUnitario * dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, $iSetor, $iCategoria, $iSubCategoria, $iClassificacao, '$dataInicio', '$dataFim')) as ValorTotal
			FROM Produto
			JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
			JOIN Movimentacao on MovimId = MvXPrMovimentacao
			JOIN Situacao on SituaId = MovimSituacao
			WHERE " . $string . " ProduEmpresa = ".$_SESSION['EmpreId']." and MovimTipo = 'S' and SituaChave = 'FINALIZADO' and MovimData between '".$dataInicio."' and '".$dataFim."' ";			
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
			
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        try {

            $sql = "SELECT ProduId, ProduNome, MvXPrValorUnitario, dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, $iSetor, $iCategoria, $iSubCategoria, $iClassificacao, '$dataInicio', '$dataFim') as Saidas,
				   (MvXPrValorUnitario * dbo.fnTotalSaidas(ProduEmpresa, ProduId, NULL, $iSetor, $iCategoria, $iSubCategoria, $iClassificacao, '$dataInicio', '$dataFim')) as ValorTotal
			FROM Produto
			JOIN MovimentacaoXProduto on MvXPrProduto = ProduId
			JOIN Movimentacao on MovimId = MvXPrMovimentacao
			JOIN Situacao on SituaId = MovimSituacao
			WHERE ProduEmpresa = ".$_SESSION['EmpreId']." and MovimTipo = 'S' and SituaChave = 'FINALIZADO' and MovimData between '".$dataInicio."' and '".$dataFim."' ";
            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
			
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if ($cont == 1) {
        
		$cont = 0;

		$resultado = '<div class="col-lg-12">
					
						<!-- Basic responsive configuration -->
						<div class="card">
							<div class="card-header header-elements-inline">
								<h3 class="card-title">Resultado da Pesquisa</h3>
								<div class="header-elements">
									<div class="list-icons">
										<a class="list-icons-item" data-action="collapse"></a>
										<a href="relatorioCurvaABC.php" class="list-icons-item" data-action="reload"></a>
										<!--<a class="list-icons-item" data-action="remove"></a>-->
									</div>
								</div>
							</div>

							<div class="card-body">
								
								<table class="table" id="tblCurvaABC">
									<thead>
										<tr class="bg-slate">											
											<th width="40%">Produto</th>
											<th width="10%">Valor Unit.</th>
											<th width="10%">Saída</th>
											<th width="10%">Valor Total</th>									
											<th width="10%">Porcentagem</th>										
											<th width="10%">% Acumulada</th>
											<th width="10%" style="background-color: #ccc; color:#333;">Classificação</th>
										</tr>
									</thead>
									<tbody>
						';
     
		foreach ($rowData as $item) {

			$resultado .= '
			<tr>
				<td>'.$item['ProduNome'].'</td>
				<td>'.mostraValor($item['MvXPrValorUnitario']).'</td>
				<td>'.$item['Saidas'].'</td>
				<td>'.mostraValor($item['ValorTotal']).'</td>
				<td></td>											
				<td></td>
				<td style="background-color: #eee; color:#333;"></td>
			</tr>';

			$cont++;
		}

		$resultado .= '				</tbody>
								</table>
							</div>
						</div>
					</div>
		';
		
		echo $resultado;
    }
}

queryPesquisa();
