<?php
ob_start();
session_start();
error_reporting(0);
include("../../../configs/bootstrap.php");
include("../../../includes/bootstrap.php");
include("../../../languages/".$settings[default_language].".php");
$bit_gateway_send = protect($_POST['bit_gateway_send']);
$bit_gateway_receive = protect($_POST['bit_gateway_receive']);
$from = $bit_gateway_send;
$to = $bit_gateway_receive;
$bit_amount_send = protect($_POST['bit_amount_send']);
$bit_amount_receive = protect($_POST['bit_amount_receive']);
$bit_rate_from = protect($_POST['bit_rate_from']);
$bit_rate_to = protect($_POST['bit_rate_to']);
$bit_currency_from = protect($_POST['bit_currency_from']);
$bit_currency_to = protect($_POST['bit_currency_to']);
$min_amount = gatewayinfo($bit_gateway_send,"min_amount");
$max_amount = gatewayinfo($bit_gateway_send,"max_amount");		
$bit_u_field_1 = protect($_POST['bit_u_field_1']);
$bit_u_field_2 = protect($_POST['bit_u_field_2']);
$bit_u_field_3 = protect($_POST['bit_u_field_3']);
$bit_u_field_4 = protect($_POST['bit_u_field_4']);
$bit_u_field_5 = protect($_POST['bit_u_field_5']);
$bit_u_field_6 = protect($_POST['bit_u_field_6']);
$bit_u_field_7 = protect($_POST['bit_u_field_7']);
$bit_u_field_8 = protect($_POST['bit_u_field_8']);
$bit_u_field_9 = protect($_POST['bit_u_field_9']);
$bit_u_field_10 = protect($_POST['bit_u_field_10']);	
$account = gatewayinfo($bit_gateway_send,"a_field_1");
$gateway = gatewayinfo($bit_gateway_send,"name").' '.gatewayinfo($bit_gateway_send,"currency");	
if(empty($bit_rate_from) or empty($bit_rate_to)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_15']);
} elseif($settings['login_to_exchange'] == "1" && !checkSession()) {
		$data['status'] = 'error';
		$data['msg'] = info($lang['error_16']);
} elseif($settings['login_to_exchange'] == "1" && get_verify_type() !== "9" && idinfo($_SESSION['bit_uid'],"status") == "1") {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_17']);
}  elseif(gatewayinfo($bit_gateway_send,"require_login") == "1" && !checkSession()) {
		$results = $lang[error_51].' '.gatewayinfo($bit_gateway_send,"name").' '.gatewayinfo($bit_gateway_send,"currency").' '.$lang[error_51_1];
		$data['status'] = 'error';
		$data['msg'] = error($results);
} elseif(gatewayinfo($bit_gateway_send,"require_email_verify") == "1" && idinfo($_SESSION['bit_uid'],"email_verified") == "0") {
		$results = $lang[error_51].' '.gatewayinfo($bit_gateway_send,"name").' '.gatewayinfo($bit_gateway_send,"currency").' '.$lang[error_51_2];
		$data['status'] = 'error';
		$data['msg'] = error($results);
}  elseif(gatewayinfo($bit_gateway_send,"require_document_verify") == "1" && idinfo($_SESSION['bit_uid'],"document_verified") == "0") {
		$results = $lang[error_51].' '.gatewayinfo($bit_gateway_send,"name").' '.gatewayinfo($bit_gateway_send,"currency").' '.$lang[error_51_3];
		$data['status'] = 'error';
		$data['msg'] = error($results);
}  elseif(gatewayinfo($bit_gateway_send,"require_mobile_verify") == "1" && idinfo($_SESSION['bit_uid'],"mobile_verified") == "0") {
		$results = $lang[error_51].' '.gatewayinfo($bit_gateway_send,"name").' '.gatewayinfo($bit_gateway_send,"currency").' '.$lang[error_51_4];
		$data['status'] = 'error';
		$data['msg'] = error($results);
} elseif(empty($bit_gateway_send)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_18']);
} elseif(empty($bit_gateway_receive)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_19']);
} elseif(empty($account)) {
		$data['status'] = 'error';
		$data['msg'] = error("$lang[error_20] $gateway.");
} elseif(empty($bit_amount_send)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_21']);
} elseif(!is_numeric($bit_amount_send)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_22']);
} elseif($bit_amount_receive > gatewayinfo($bit_gateway_receive,"reserve")) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_23']);
} elseif($min_amount > $bit_amount_send) {
		$data['status'] = 'error';
		$data['msg'] = error("$lang[error_24] $min_amount $bit_currency_from.");
} elseif($bit_amount_send > $max_amount) {
		$data['status'] = 'error';
		$data['msg'] = error("$lang[error_25] $max_amount $bit_currency_from.");
} elseif(empty($bit_u_field_1) or empty($bit_u_field_2)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_26']);
} elseif(!isValidEmail($bit_u_field_1)) {
		$data['status'] = 'error';
		$data['msg'] = error($lang['error_27']);
} else {
		if(checkSession()) { $uid = $_SESSION['bit_uid']; } else { $uid = 0; }
		if($_SESSION['refid']) { $referral_id = $_SESSION['refid']; } else { $referral_id = 0; }
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$exchange_id = randomHash(20);
		$exchange_id = strtoupper($exchange_id);
		$insert = $db->query("INSERT bit_exchanges (uid,gateway_send,gateway_receive,amount_send,amount_receive,rate_from,rate_to,status,created,updated,expired,u_field_1,u_field_2,u_field_3,u_field_4,u_field_5,u_field_6,u_field_7,u_field_8,u_field_9,u_field_10,ip,exchange_id,referral_id,referral_status) VALUES ('$uid','$bit_gateway_send','$bit_gateway_receive','$bit_amount_send','$bit_amount_receive','$bit_rate_from','$bit_rate_to','1','$time','0','0','$bit_u_field_1','$bit_u_field_2','$bit_u_field_3','$bit_u_field_4','$bit_u_field_5','$bit_u_field_6','$bit_u_field_7','$bit_u_field_8','$bit_u_field_9','$bit_u_field_10','$ip','$exchange_id','$referral_id','0')");
		$query = $db->query("SELECT * FROM bit_exchanges WHERE exchange_id='$exchange_id'");
		$row = $query->fetch_assoc();
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Set the hostname of the mail server
		$mail->Host = $smtpconf["host"];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $smtpconf["port"];
		//Whether to use SMTP authentication
		$mail->SMTPAuth = $smtpconf['SMTPAuth'];
		//Username to use for SMTP authentication
		$mail->Username = $smtpconf["user"];
		//Password to use for SMTP authentication
		$mail->Password = $smtpconf["pass"];
		//Set who the message is to be sent from
		$mail->setFrom($settings['infoemail'], $settings['name']);
		//Set who the message is to be sent to
		$mail->addAddress($bit_u_field_1, $bit_u_field_1);
		//Set the subject line
		$mail->Subject = '['.$settings[name].'] New exchange order '.$exchange_id;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$email_template = file_get_contents("../../email_templates/new_exchange.html",__DIR__);
		$email_template = str_ireplace("{@site_title}",$settings['name'],$email_template);
		$email_template = str_ireplace("{@url}",$settings['url'],$email_template);
		$email_template = str_ireplace("{@exchange_id}",$exchange_id,$email_template);
		$email_template = str_ireplace("{@email}",$bit_u_field_1,$email_template);
		$email_template = str_ireplace("{@from}",gatewayinfo($from,"name"),$email_template);
		$email_template = str_ireplace("{@from_c}",gatewayinfo($from,"currency"),$email_template);
		$email_template = str_ireplace("{@to}",gatewayinfo($to,"name"),$email_template);
		$email_template = str_ireplace("{@to_c}",gatewayinfo($to,"currency"),$email_template);
		$email_template = str_ireplace("{@amount}",$bit_amount_send,$email_template);
		$mail->msgHTML($email_template);
		//Replace the plain text body with one created manually
		$mail->AltBody = $settings[name].' New exchange order '.$exchange_id;
		//Attach an image file
		//send the message, check for errors
		$mail->send();
		$_SESSION['bit_requested_exchange_id'] = $row['exchange_id'];
		$redirect = $settings['url']."order/".$exchange_id;
		$redirect = '<meta http-equiv="refresh" content="0;URL='.$redirect.'" /><script type="text/javascript">window.location.href="'.$redirect.'";</script>';
		$redirect .= '<center><i class="fa fa-spin fa-spinner fa-3x"></i></center>';
		$data['status'] = 'success';
		$data['msg'] = $redirect;
	}

echo json_encode($data);
?>