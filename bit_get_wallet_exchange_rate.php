<?php
ob_start();
session_start();
error_reporting(0);
include("../configs/bootstrap.php");
include("../includes/bootstrap.php");
include(getLanguage($settings['url'],null,2));
$wallet_id = protect($_GET['wallet_id']);
$walletcurr = walletinfo($wallet_id,"currency");
$getquery = $db->query("SELECT * FROM bit_gateways WHERE currency='$walletcurr'");
$get = $getquery->fetch_assoc();
$gateway_send = $get['id'];
	$gateway_sendname = gatewayinfo($gateway_send,"name");
$gateway_receive = protect($_GET['gateway_id']);
$gateway_receivename = gatewayinfo($gateway_receive,"name");
if(empty($gateway_send) or empty($gateway_receive)) {
	$data['status'] = 'error';
	$data['msg'] = '-';
} else {
	$data['status'] = 'success';
	$currency_from = gatewayinfo($gateway_send,"currency");
	$currency_to = gatewayinfo($gateway_receive,"currency");
	$fee = gatewayinfo($gateway_receive,"fee");
	$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
			if($query->num_rows>0) {
				$row = $query->fetch_assoc();
				$data['status'] = 'success';
				$rate_from = $row['rate_from'];
				$rate_to = $row['rate_to'];
			} else {
					if($currency_from == $currency_to) { 
						$fee = str_ireplace("-","",$fee);
						$calculate1 = (1 * $fee) / 100;
						$calculate2 = 1 - $calculate1;
						$rate_from = 1;
						$rate_to = $calculate2;
					} elseif($currency_to == "BTC") {
						if(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} else {
							$url = "https://www.changer.com/api/v2/rates/bitcoin_BTC/payeer_USD";
							$json = json_decode(file_get_contents($url), true);
								$price = $json['rate'];
								if($currency_from == "USD" ) {
									$price = $price;
								} else {
									$price = currencyConvertor($price,"USD",$currency_from);
								}
								$calculate1 = ($price * $fee) / 100;
								$calculate2 = $price - $calculate1;
								$calculate2 = number_format($calculate2, 2, '.', '');
								$rate_to = 1;
								$rate_from = $calculate2;
						}
					}  elseif($currency_from == "BTC") {
						if(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} else {
							$url = "https://www.changer.com/api/v2/rates/bitcoin_BTC/payeer_USD";
							$json = json_decode(file_get_contents($url), true);
								$price = $json['rate'];
								if($currency_to == "USD") {
									$price = $price;
								} else {
									$price = currencyConvertor($price,"USD",$currency_to);
								}
							$calculate1 = ($price * $fee) / 100;
							$calculate2 = $price - $calculate1;
							$calculate2 = number_format($calculate2, 2, '.', '');
							$rate_from = 1;
							$rate_to = $calculate2;
						}
					} elseif(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
						$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
						if($query->num_rows>0) {
							$row = $query->fetch_assoc();
							$data['status'] = 'success';
							$rate_from = $row['rate_from'];
							$rate_to = $row['rate_to'];
						} else {
							$data['status'] = 'error';
							$data['msg'] = '-';
						}
					} else {
						if(isCrypto($gateway_sendname) == "1" && isCrypto($gateway_receivename) == "0") {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} elseif(isCrypto($gateway_sendname) == "0" && isCrypto($gateway_receivename) == "1") {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} else {
							$rate_from = 1;
							$calculate = currencyConvertor($rate_from,$currency_from,$currency_to);
							$calculate1 = ($calculate * $fee) / 100;
							$calculate2 = $calculate - $calculate1;
							if($calculate2 < 1) { 
								$calculate = currencyConvertor($rate_from,$currency_to,$currency_from);
								$calculate1 = ($calculate * $fee) / 100;
								$calculate2 = $calculate - $calculate1;
								$rate_from = number_format($calculate2, 2, '.', '');
								$rate_to = 1;
							} else {
								$rate_to = number_format($calculate2, 2, '.', '');
							}
						}
					}
	}
	$data['rate_from'] = $rate_from; 
	$data['rate_to'] = $rate_to;
	$data['currency_form'] = $currency_from;
	$data['currency_to'] = gatewayinfo($gateway_receive,"currency");
}
echo json_encode($data);
?>