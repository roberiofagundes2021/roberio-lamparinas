<?php 
include_once("sessao.php");
include('global_assets/php/conexao.php');

$data = $_POST['date'];

if($_POST['conta'] != '') {
    $conta = explode("#", $_POST['conta']);
    //alerta($conta[0].' '.$conta[1]); Índice 0 Id da conta banco e índice 1 é Débito
}

$sql = "SELECT isNull(dbo.fnDebitosDia(".$_SESSION['UnidadeId'].", null, '".$data."'), 0.00) as Debito,
               isNull(dbo.fnCreditosDia(".$_SESSION['UnidadeId'].", null, '".$data."'), 0.00) as Credito";
$result = $conn->query($sql);
$rowResumo = $result->fetch(PDO::FETCH_ASSOC);

$fCredito = mostraValor($rowResumo['Credito']);
$fDebito = mostraValor($rowResumo['Debito']);

$fSaldo = mostraValor($rowResumo['Credito'] - $rowResumo['Debito']);
?>

<div class="form-group">
    <input id="inputCredito" name="inputCredito" class="form-control" value="<?php echo $fCredito; ?>" style="font-size: 30px; text-align: right;" readonly>
    <h3 class="form-text text-right" style="color: #666;">Crédito</h3>
</div> 

<div class="form-group">
    <input id="inputDebito" name="inputDebito" class="form-control" value="<?php echo $fDebito; ?>" style="font-size: 30px; text-align: right;" readonly>
    <h3 class="form-text text-right" style="color: #666;">Débito</h3>
</div>                

<div class="form-group">
    <input id="inputSaldo" name="inputSaldo" class="form-control" value="<?php echo $fSaldo; ?>" style="font-size: 30px; text-align: right;" readonly>
    <h3 class="form-text text-right" style="color: #666;"><b>Saldo</b> (Crédito - Débito)</h3>
</div>