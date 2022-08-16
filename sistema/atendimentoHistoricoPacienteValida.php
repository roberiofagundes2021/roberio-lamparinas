<?php 

include_once("sessao.php"); 

include('global_assets/php/conexao.php');

if(isset($_POST['historicoId'])){

	$sql = "SELECT AtendId, AtendNumRegistro, UnidaNome, AtModNome, AtRecReceituario, AtSExSolicitacaoExame, AtAmbData, AtAmbHoraInicio, AtAmbHoraFim, AtAmbQueixaPrincipal, 
                   AtAmbHistoriaMolestiaAtual,AtAmbExameFisico, AtAmbSuspeitaDiagnostico, AtAmbExameSolicitado, AtAmbPrescricao, AtAmbOutrasObservacoes, ProfiNome, ProfiProfissao,
				   AtEleData, AtEleHoraInicio, AtEleHoraFim, AtEleAnamnese
			FROM Atendimento
			LEFT JOIN AtendimentoEletivo ON AtEleAtendimento = AtendId
			LEFT JOIN AtendimentoAmbulatorial ON AtAmbAtendimento = AtendId
			LEFT JOIN AtendimentoModalidade ON AtModId = AtendModalidade
			LEFT JOIN AtendimentoReceituario ON AtRecAtendimento = AtendId
			LEFT JOIN  AtendimentoSolicitacaoExame ON AtSExAtendimento = AtendId
			LEFT JOIN Profissional  ON ProfiId = AtSExProfissional
			JOIN Unidade ON UnidaId = AtendUnidade
			JOIN Cliente ON ClienId = AtendCliente
			WHERE AtendId = '". $_POST['historicoId']."' and AtendUnidade = ".$_SESSION['UnidadeId']; 
	$result = $conn->query($sql);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$count = count($row);
	
	//$sql = "SELECT ProfiNome
	//FROM Profissao
	//WHERE ProfiId = " .$row['ProfiProfissao']. "
	//";
	//$result = $conn->query($sql);
	//$rowProfissao = $result->fetch(PDO::FETCH_ASSOC);		
	

	$Inicio = strtotime($row['AtAmbHoraInicio']);
	$HoraInicio = date("H:i", $Inicio);

	$Fim = strtotime($row['AtAmbHoraFim']);
	$HoraFim = date("H:i", $Fim);

	$Inicio = strtotime($row['AtEleHoraInicio']);
	$HoraInicioEletivo = date("H:i", $Inicio);

	$Fim = strtotime($row['AtEleHoraFim']);
	$HoraFimEletivo = date("H:i", $Fim);

	if($count){

		if($count){
			print('
				<p style="margin-right:10px; margin-left: 10px"><b>ENTRADA:</b> '.mostraData($row['AtAmbData']).' - '.$HoraInicio.'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>SAÍDA:</b> '.mostraData($row['AtAmbData']).' - '.$HoraFim.'</p>
				<hr style="margin-right:10px; margin-left: 10px">
				<p style="margin-right:10px; margin-left: 10px"><b>Modalidade:</b> '.$row['AtModNome'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Guia:</b> '.$row['AtendNumRegistro'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Unidade de Atendimento:</b> '.$row['UnidaNome'].'</p>
				<hr style="margin-right:10px; margin-left: 10px">
				<p style="margin-right:10px; margin-left: 10px"><b>Queixa Principal (QP):</b> '.$row['AtAmbQueixaPrincipal'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>História da Moléstia Atual (HMA):</b> '.$row['AtAmbHistoriaMolestiaAtual'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Exame Físico:</b> '.$row['AtAmbExameFisico'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Suspeita Diagnóstico:</b> '.$row['AtAmbSuspeitaDiagnostico'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Exame Solicitado:</b> '.$row['AtAmbExameSolicitado'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Prescrição:</b> '.$row['AtAmbPrescricao'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Outras Observações:</b> '.$row['AtAmbOutrasObservacoes'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Médico Solicitante:</b><b> '.$row['ProfiNome'].'</b></p>
				<p style="margin-right:10px; margin-left: 10px"><b>Receituário:</b> '.$row['AtRecReceituario'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Solicitação de Procedimento:</b> '.$row['AtSExSolicitacaoExame'].'</p>
				
			');
		} else{

			print('
				<p style="margin-right:10px; margin-left: 10px"><b>ENTRADA:</b> '.mostraData($row['AtEleData']).' - '.$HoraInicioEletivo.'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>SAÍDA:</b> '.mostraData($row['AtEleData']).' - '.$HoraFimEletivo.'</p>
				<hr style="margin-right:10px; margin-left: 10px">
				<p style="margin-right:10px; margin-left: 10px"><b>Modalidade:</b> '.$row['AtModNome'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Guia:</b> '.$row['AtendNumRegistro'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Unidade de Atendimento:</b> '.$row['UnidaNome'].'</p>
				<hr style="margin-right:10px; margin-left: 10px">
				<p style="margin-right:10px; margin-left: 10px"><b>Clínica (anamnese):</b> '.$row['AtEleAnamnese'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Médico Solicitante:</b><b> '.$row['ProfiNome'].'</b></p>
				<p style="margin-right:10px; margin-left: 10px"><b>Receituário:</b> '.$row['AtRecReceituario'].'</p>
				<p style="margin-right:10px; margin-left: 10px"><b>Solicitação de Procedimento:</b> '.$row['AtSExSolicitacaoExame'].'</p>
				
			');

		}
		
	} else{
		echo 0;
	}
} else {
	echo 0;
}

?>
