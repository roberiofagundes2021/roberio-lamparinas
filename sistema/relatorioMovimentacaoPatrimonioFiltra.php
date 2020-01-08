<?php 

include_once("sessao.php");
include('global_assets/php/conexao.php');

$inputDataDe = $_POST['inputDataDe'];
$inputDataAte = $_POST['inputDataAte'];
/*$inputLocalEstoque = $_POST['inputEstoqueLocal'];
$inputSetor = $_POST['inputDataDe'];
$inputCategoria = $_POST['inputCategoria'];
$inputSubCategoria = $_POST['inputSubCategoria'];
$inputProduto = $_POST['inputProduto'];*/
if($inputDataDe || $inputDataAte /*|| $inputEstoqueLocal || $inputSetor || $inputProduto*/){
    try{
       
        $sql = "SELECT MovimId ,MovimData, MovimNotaFiscal, MovimOrigem, MovimDestinoLocal, MvXPrValidade
				FROM Movimentacao
                JOIN MovimentacaoXProduto on MvXPrMovimentacao = MovimId
				WHERE MovimData BETWEEN '".$inputDataDe."' and '".$inputDataAte."'";
		$result = $conn->query("$sql");
		$row = $result->fetchAll(PDO::FETCH_ASSOC);	

    } catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
    }
    
    if( $row && count($row) >= 1){
        foreach($row as $item){
            print("
                <tr>
                   <td>".$item['MovimId']."</td>
                   <td>".$item['MovimData']."</td>
                   <td>".$item['MovimNotaFiscal']."</td>
                   <td>".$item['MovimOrigem']."</td>
                   <td>".$item['MovimDestinoLocal']."</td>
                   <td>".$item['MvXPrValidade']."</td>
                </tr>
             ");
        }
    } else {
        print('Nada emcontrado...');
    }
}

?>