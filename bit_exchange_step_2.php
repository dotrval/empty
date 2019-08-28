<?php
ob_start();
session_start();
error_reporting(0);
include("../../../configs/bootstrap.php");
include("../../../includes/bootstrap.php");
include("../../../languages/".$settings[default_language].".php");
$bit_gateway_send = protect($_POST['bit_gateway_send']);
$bit_gateway_receive = protect($_POST['bit_gateway_receive']);
$bit_amount_send = protect($_POST['bit_amount_send']);
$bit_amount_receive = protect($_POST['bit_amount_receive']);
$bit_rate_from = protect($_POST['bit_rate_from']);
$bit_rate_to = protect($_POST['bit_rate_to']);
$bit_currency_from = protect($_POST['bit_currency_from']);
$bit_currency_to = protect($_POST['bit_currency_to']);
$min_amount = gatewayinfo($bit_gateway_send,"min_amount");
$max_amount = gatewayinfo($bit_gateway_send,"max_amount");			
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
}  elseif(empty($bit_gateway_send)) {
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
} else {
	$receive = gatewayinfo($bit_gateway_receive,"name");
	if($receive == "Bank Transfer") {
		$required_fields = '<div class="form-group">
								<label>'.$lang[your_name].'</label>
								<input type="text" class="form-control" name="bit_u_field_2">
							</div>
							<div class="form-group">
								<label>'.$lang[your_location].'</label>
								<input type="text" class="form-control" name="bit_u_field_3">
							</div>
							<div class="form-group">
								<label>'.$lang[your_bank_name].'</label>
								<input type="text" class="form-control" name="bit_u_field_4">
							</div>
							<div class="form-group">
								<label>'.$lang[your_bank_account].'</label>
								<input type="text" class="form-control" name="bit_u_field_5">
							</div>
							<div class="form-group">
								<label>'.$lang[your_bank_swift].'</label>
								<input type="text" class="form-control" name="bit_u_field_6">
							</div>';
	} elseif($receive == "Moneygram" or $receive == "Western Union") {
		$required_fields = '<div class="form-group">
								<label>'.$lang[your_name].'</label>
								<input type="text" class="form-control" name="bit_u_field_2">
							</div>
							<div class="form-group">
								<label>'.$lang[your_location].'</label>
								<input type="text" class="form-control" name="bit_u_field_3">
							</div>';
	} elseif($receive == "Edinarcoin" or $receive == "Bitcoin" or $receive == "Litecoin" or $receive == "Dogecoin" or $receive == "Dash" or $receive == "Peercoin" or $receive == "Ethereum" or $receive == "TheBillioncoin") {
		$required_fields = '<div class="form-group">
								<label>'.$lang[your].' '.$receive.' '.$lang[address].'</label>
								<input type="text" class="form-control" name="bit_u_field_2">
							</div>';
	} else {
			$fields = '';
			$check = $db->query("SELECT * FROM bit_gateways WHERE name='$receive' and external_gateway='1'");
			if($check->num_rows>0) {
				$r = $check->fetch_assoc();
				$fieldsquery = $db->query("SELECT * FROM bit_gateways_fields WHERE gateway_id='$r[id]' ORDER BY id");
				if($fieldsquery->num_rows>0) {
					while($field = $fieldsquery->fetch_assoc()) {
						$field_number = $field['field_number']+1;
						$fields .= '<div class="form-group">
									<label>'.$field[field_name].'</label>
									<input type="text" class="form-control" name="bit_u_field_'.$field_number.'">
								</div>';
					}
				}
				if($sendname == "Bitcoin" or $sendname == "Litecoin" or $sendname == "Dogecoin") {
					$payee = '<div class="form-group">
						<label>Your '.$sendname.' address</label>
						<input type="text" class="form-control" name="bit_u_field_10">
					</div>';
				}
				$required_fields = $fields;
			} else {
					if($sendname == "Bitcoin" or $sendname == "Litecoin" or $sendname == "Dogecoin") {
					$payee = '<div class="form-group">
						<label>Your '.$sendname.' address</label>
						<input type="text" class="form-control" name="bit_u_field_10">
					</div>';
				}
				$required_fields = '
								<div class="form-group">
									<label>'.$lang[your].' '.$receive.' '.$lang[account].'</label>
									<input type="text" class="form-control" name="bit_u_field_2">
								</div>';
			}
	}
	$required_fields = '<div class="form-group">
														<label>'.$lang[your_email_address].'</label>
														<input type="text" class="form-control" name="bit_u_field_1">
													</div>'.$required_fields;
	$form = '<div class="col-md-2"></div>
		<div class="col-md-8">
			<h3>'.$lang[we_require_from_you].'</h3><hr/>
			<div id="bit_exchange_results"></div>
			<form id="bit_exchange_form">
			'.$required_fields.'
			<input type="hidden" name="bit_gateway_send" value="'.$bit_gateway_send.'">
			<input type="hidden" name="bit_gateway_receive" value="'.$bit_gateway_receive.'">
			<input type="hidden" name="bit_amount_send" value="'.$bit_amount_send.'">
			<input type="hidden" name="bit_amount_receive" value="'.$bit_amount_receive.'">
			<input type="hidden" name="bit_rate_from" value="'.$bit_rate_from.'">
			<input type="hidden" name="bit_rate_to" value="'.$bit_rate_to.'">
			<input type="hidden" name="bit_currency_from" value="'.$bit_currency_from.'">
			<input type="hidden" name="bit_currency_to" value="'.$bit_currency_to.'">
			<center>
				<button type="button" class="btn btn-primary btn-lg" id="bit_exchange_btn"  onclick="bit_exchange_step_3();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i id="bit_exchange_btn_i" class="fa fa-refresh"></i> '.$lang[btn_17].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
			</center>
			</form>
		</div>
		<div class="col-md-2"></div>';
	$data['status'] = 'success';
	$data['msg'] = $form;
}

echo json_encode($data);
?>