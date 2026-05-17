<?php
	ini_set("display_errors","on");
	define('PROJECT_DIR', '/resin');

	require_once($_SERVER['DOCUMENT_ROOT'].PROJECT_DIR. '/configs/common/main.conf.php');
	/* === Start send mail =====*/	
	$mail_to="engineer@gmx.ru";
				
	$mail_subject 	= "Lira Digital Sales Invoice";
		
	$mailToArr  	= explode(",",$mail_to);
	$total_mail 	= count($mailToArr);

	require_once(EXT_DIR.'/phpmailer/PHPMailerAutoload.php');
	$mail = new PHPMailer(true);
	$mail->clearAddresses();
	$sm =0; $issend=0; $mailfrom = "LIRA GROUP";
	while($sm < $total_mail){	
	if(trim($mailToArr[$sm]) !=""){ 
	$mail->AddAddress($mailToArr[$sm], $mailfrom); 
	}
	$sm++;
	}			

	$subject 	= $mail_subject." - ".$delivery_date; 	
	$voucher_no     = "V00004";	
		
	// Send mail using Gmail
	$send_time = date("d-M-Y");
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPAuth = true; // true / false enable SMTP authentication
	//$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
	$mail->Host = "relay.mailbaby.net"; // sets GMAIL as the SMTP server (smtp.gmail.com)
	$mail->Port = 25; // 465 or 587 set the SMTP port for the GMAIL server (587)
	$TotalSend=0;
	$mail->Username = "mb16244";  // GMAIL username : liragroupsales@gmail.com
	$email_from ="imran@lirabd.com";	

	$mail->Password = "3YGBRaMn9Es9ySxxCmpw"; // GMAIL password :imran@#0088
	$mail->ContentType ="text/html";	
	// Typical mail data
	$email=$sendto; $full_name ="Lira Delivery Point"; 
	
	$mail->SetFrom($email_from, $full_name);
	$mail->Subject 	= $Subject;
	$mail->Body 	= "<p>Dear Sir, Your Sales Invoice No ".$voucher_no.". Please kindly see the attached sales invoice below :</p>";
	// Attach the uploaded file			
	
	try{
		
		if($mail->Send()){
			echo "Successfully send mail";
		}else{
			echo "Failed send mail";
		}

	} catch(Exception $e){
		// Something went bad
		echo $e;
	}
?>
