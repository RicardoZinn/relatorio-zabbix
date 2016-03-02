<?php
ini_set( 'display_errors', true );
error_reporting( E_ALL );

include "fhost.php";


require("PHPMailer/class.phpmailer.php"); // path to the PHPMailer class 

$configMail = parse_ini_file('email.ini'); 

$mail = new PHPMailer();  
$mail->IsSMTP();
$mail->IsHTML($configMail['ishtml']);
$mail->Mailer = $configMail['mailer'];
$mail->CharSet = $configMail['charset'];
$mail->Host = $configMail['host']; 
$mail->Port = $configMail['port']; 
$mail->SMTPAuth = $configMail['smtpauth'];
$mail->Username = $configMail['username']; 
$mail->Password = $configMail['password']; 
$mail->FromName = $configMail['fromname'];
$mail->From     = $configMail['from']; 


$host = new stdClass;
$ArrayObjetos64 = getObjectsToSend();
debugl("Emails pendentes: " . count($ArrayObjetos64));
foreach ($ArrayObjetos64 as $Obj64){
	debugl("");
	$base64string = $Obj64["phpbase64"];
	$mailid = $Obj64["id"];
	
	debugl("Mailid : $mailid");
	debugl("base64string : $base64string");
	
	$host = unserialize(base64_decode($base64string));
	
	if(is_null($host->emailResponsavel)){
		echo ("<h1>Não encontramos o registro do e-mail do responsável! mailid=$mailid  :(");
		exit();
	}else{
		$mail->Subject = "Subject Empresa($host->nomeEmpresa) host($host->nome) mail($mailid)"; 
		$mail->AddAddress($host->emailResponsavel);
		
		$ArrayCopiasControladas = $host->CopiasControladas;
		
		if(!is_null($ArrayCopiasControladas)){
			foreach ($ArrayCopiasControladas as $Copia){
				$mail->AddCC($Copia["email"], $Copia["nome"]);
				debugl("Cc nome:" . $Copia["nome"] . " e-mail:" . $Copia["email"]);
			}
		}
		$mail->Body = processaRelatorioHost($mailid,$host);
		if(!$mail->Send()) {
			echo "Message was not sent";
			echo "Mailer error: " . $mail->ErrorInfo;
		} else {
			echo "Message has been sent (mailid=$mailid)<br>";
			if(setDataEnvio($mailid))
				echo "Marcado como enviado(mailid=$mailid)<br>";
			else
				echo "Não Marcado como enviado(mailid=$mailid) #erro";
		}
	}
}

?>