<?php
function protect($string) {
	$protection = htmlspecialchars(trim($string), ENT_QUOTES);
	return $protection;
}

function randomHash($lenght = 7) {
	$random = substr(md5(rand()),0,$lenght);
	return $random;
}

function isValidURL($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function cropexchangeid($text,$chars) {
	$string = $text;
	if(strlen($string) > $chars) $string = substr($string, 0, $chars).'***************';
	echo $string;
}

function checkAdminSession() {
	if(isset($_SESSION['bit_admin_uid'])) {
		return true;
	} else {
		return false;
	}
}


function currencyConvertor($amount,$from_Currency,$to_Currency) {
	$am = urlencode($amount);
	
	$prefix = $from_Currency.'_'.$to_Currency;
	$ch = curl_init();
	$url = "https://free.currencyconverterapi.com/api/v6/convert?apiKey=sample-api-key&q=$prefix&compact=y";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	//echo $json[$prefix]['val'];
	$converted_amount = $json[$prefix]['val'];
	if($amount>1 && $from_Currency != "USD") {
		$converted_amount = $amount * $converted_amount;
		return number_format($converted_amount, 2, '.', '');
	} elseif($amount>1 && $to_Currency != "USD") {
		$converted_amount = $amount * $converted_amount;
		return number_format($converted_amount, 2, '.', '');
	} else {
		return number_format($converted_amount, 2, '.', '');
	}
}

function checkOperatorSession() {
	if(isset($_SESSION['bit_operator_uid'])) {
		return true;
	} else {
		return false;
	}
}

function BitDecodeTitle($prefix) {
	global $db, $settings;
	if($prefix == "exchange") {
		$from = protect($_GET['from']);
		$to = protect($_GET['to']);
		$title = 'Exchange '.gatewayinfo($from,"name").' '.gatewayinfo($from,"currency").' to '.gatewayinfo($to,"name").' '.gatewayinfo($to,"currency").' - '.$settings[name];
		return $title;
	} else {
		return $settings['title'];
	}
}

function isValidUsername($str) {
    return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
}

function isValidEmail($str) {
	return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function checkSession() {
	if(isset($_SESSION['bit_uid'])) {
		return true;
	} else {
		return false;
	}
}

function success($text) {
	return '<div class="alert alert-success" style="color:#010101;text-align:left;"><i class="fa fa-check"></i> <span>'.$text.'</span></div>';
}

function error($text) {
	return '<div class="alert alert-danger" style="color:#ffffff;text-align:left;"><i class="fa fa-times"></i> <span>'.$text.'</span></div>';
}

function info($text) {
	return '<div class="alert alert-info" style="color:#010101;text-align:left;"><i class="fa fa-info-circle"></i> <span>'.$text.'</span></div>';
}

function admin_pagination($query,$ver,$per_page = 10,$page = 1, $url = '?') { 
    	global $db;
		$query = $db->query("SELECT * FROM $query");
    	$total = $query->num_rows;
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination'>";
                
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    				else
    					$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled page-item'>...</li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lpm1' class='page-link'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>$lastpage</a></li>";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li class='page-item'><a href='$ver&page=1' class='page-link'>1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=2' class='page-link'>2</a></li>";
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>...</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>..</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lpm1' class='page-link'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>$lastpage</a></li>";		
    			}
    			else
    			{
    				$pagination.= "<li class='page-item'><a href='$ver&page=1' class='page-link'>1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=2' class='page-link'>2</a></li>";
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li class='page-item'><a href='$ver&page=$next' class='page-link'>Next</a></li>";
                $pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>Last</a></li>";
    		}else{
    			$pagination.= "<li class='page-item'><a class='disabled page-link'>Next</a></li>";
                $pagination.= "<li class='page-item'><a class='disabled page-link'>Last</a></li>";
            }
    		$pagination.= "</ul>\n";		
    	}
    
    
        return $pagination;
} 

function web_pagination($query,$ver,$per_page = 10,$page = 1, $url = '?') { 
    	global $db;
		$query = $db->query("SELECT * FROM $query");
    	$total = $query->num_rows;
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination'>";
                
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li><a class='active'>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='$ver/$counter'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='active'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='$ver/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled'>...</li>";
    				$pagination.= "<li><a href='$ver/$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li><a href='$ver/$lastpage'>$lastpage</a></li>";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='$ver/1'>1</a></li>";
    				$pagination.= "<li><a href='$ver/2'>2</a></li>";
    				$pagination.= "<li class='disabled'><a>...</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='active'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='$ver/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled'><a>..</a></li>";
    				$pagination.= "<li><a href='$ver/$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li><a href='$ver/$lastpage'>$lastpage</a></li>";		
    			}
    			else
    			{
    				$pagination.= "<li><a href='$ver/1'>1</a></li>";
    				$pagination.= "<li><a href='$ver/2'>2</a></li>";
    				$pagination.= "<li class='disabled'><a>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='active'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='$ver/$counter'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li><a href='$ver/$next'>Next</a></li>";
                $pagination.= "<li><a href='$ver/$lastpage'>Last</a></li>";
    		}else{
    			$pagination.= "<li><a class='disabled'>Next</a></li>";
                $pagination.= "<li><a class='disabled'>Last</a></li>";
            }
    		$pagination.= "</ul>\n";		
    	}
    
    
        return $pagination;
} 

function idinfo($uid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM bit_users WHERE id='$uid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function gatewayinfo($gid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM bit_gateways WHERE id='$gid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function exchangeinfo($eid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM bit_exchanges WHERE id='$eid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}

function einfo($eid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM bit_exchanges WHERE exchange_id='$eid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	


function walletinfo($eid,$value) {
	global $db;
	$query = $db->query("SELECT * FROM bit_users_earnings WHERE id='$eid'");
	$row = $query->fetch_assoc();
	return $row[$value];
}	

function siteURL() {
  global $db, $settings;
  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 
    $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $domainName = $_SERVER['HTTP_HOST'];
  $path = Dirname($_SERVER[PHP_SELF]);
  if(empty($path)) { $sub_dir = '/'; } else { $sub_dir = $path.'/'; }
  if(empty($domainName)) {
	return $settings['url'];
  } else {
	return $protocol.$domainName.$sub_dir;
  }
}

function getIcon($name,$width,$height) {
	global $db, $settings;
	$path = "assets/icons/";
	$external_icon = 0;
	if($name == "PayPal") { $icon = 'PayPal.png'; }
	elseif($name == "Skrill") { $icon = 'Skrill.png'; }
	elseif($name == "WebMoney") { $icon = 'WebMoney.png'; }
	elseif($name == "Payeer") { $icon = 'Payeer.png'; }
	elseif($name == "Perfect Money") { $icon = 'PerfectMoney.png'; }
	elseif($name == "AdvCash") { $icon = 'AdvCash.png'; }
	elseif($name == "OKPay") { $icon = 'OKPay.png'; }
	elseif($name == "Entromoney") { $icon = 'Entromoney.png'; }
	elseif($name == "SolidTrust Pay") { $icon = 'SolidTrustPay.png'; }
	elseif($name == "2checkout") { $icon = '2checkout.png'; }
	elseif($name == "Litecoin") { $icon = 'Litecoin.png'; }
	elseif($name == "Neteller") { $icon = 'Neteller.png'; }
	elseif($name == "UQUID") { $icon = 'UQUID.png'; }
	elseif($name == "Dash") { $icon = 'Dash.png'; }
	elseif($name == "Dogecoin") { $icon = 'Dogecoin.png'; }
	elseif($name == "BTC-e") { $icon = 'BTCe.png'; }
	elseif($name == "Ethereum") { $icon = 'Ethereum.png'; }
	elseif($name == "Peercoin") { $icon = 'Peercoin.png'; }
	elseif($name == "Yandex Money") { $icon = 'YandexMoney.png'; }
	elseif($name == "QIWI") { $icon = 'QIWI.png'; }
	elseif($name == "Payza") { $icon = 'Payza.png'; }
	elseif($name == "Bitcoin") { $icon = 'Bitcoin.png'; }
	elseif($name == "Bank Transfer") { $icon = 'BankTransfer.png'; }
	elseif($name == "Western Union") { $icon = 'Westernunion.png'; }
	elseif($name == "Moneygram") { $icon = 'Moneygram.png'; }
	elseif($name == "TheBillioncoin") { $icon = 'TheBillioncoin.png'; }
	elseif($name == "Edinarcoin") { $icon = 'Edinarcoin.png'; }
	elseif($name == "Mollie") { $icon = 'Mollie.png'; }
	else { 
		$check = $db->query("SELECT * FROM bit_gateways WHERE name='$name' and external_gateway='1'");
		if($check->num_rows>0) {
			$r = $check->fetch_assoc();
			$icon = $r['external_icon'];
			$external_icon = 1;
		} else {
			$cicon = GetCryptoCurrency($name);
			$cicon = 'assets/icons/crypto/'.$cicon.'.png';
			if(file_exists($cicon)) {
				$icon = $cicon;
				$external_icon = '1';
			} elseif(file_exists("../".$cicon)) {
				$icon = $cicon;
				$external_icon = '1';
			} else {
				$icon = "Missing.png";
			}
		}
	}
	if($external_icon == "1") {
		return '<img src="'.$settings[url].$icon.'" width="'.$width.'" height="'.$height.'">';
	} else {
		return '<img src="'.$settings[url].$path.$icon.'" width="'.$width.'" height="'.$height.'">';
	}
}

function gatewayicon($name) {
	global $db, $settings;
	$path = "assets/icons/";
	$external_icon = 0;
	if($name == "PayPal") { $icon = 'PayPal.png'; }
	elseif($name == "Skrill") { $icon = 'Skrill.png'; }
	elseif($name == "WebMoney") { $icon = 'WebMoney.png'; }
	elseif($name == "Payeer") { $icon = 'Payeer.png'; }
	elseif($name == "Perfect Money") { $icon = 'PerfectMoney.png'; }
	elseif($name == "AdvCash") { $icon = 'AdvCash.png'; }
	elseif($name == "OKPay") { $icon = 'OKPay.png'; }
	elseif($name == "Entromoney") { $icon = 'Entromoney.png'; }
	elseif($name == "SolidTrust Pay") { $icon = 'SolidTrustPay.png'; }
	elseif($name == "2checkout") { $icon = '2checkout.png'; }
	elseif($name == "Litecoin") { $icon = 'Litecoin.png'; }
	elseif($name == "Neteller") { $icon = 'Neteller.png'; }
	elseif($name == "UQUID") { $icon = 'UQUID.png'; }
	elseif($name == "Dash") { $icon = 'Dash.png'; }
	elseif($name == "Dogecoin") { $icon = 'Dogecoin.png'; }
	elseif($name == "BTC-e") { $icon = 'BTCe.png'; }
	elseif($name == "Ethereum") { $icon = 'Ethereum.png'; }
	elseif($name == "Peercoin") { $icon = 'Peercoin.png'; }
	elseif($name == "Yandex Money") { $icon = 'YandexMoney.png'; }
	elseif($name == "QIWI") { $icon = 'QIWI.png'; }
	elseif($name == "Payza") { $icon = 'Payza.png'; }
	elseif($name == "Bitcoin") { $icon = 'Bitcoin.png'; }
	elseif($name == "Bank Transfer") { $icon = 'BankTransfer.png'; }
	elseif($name == "Western Union") { $icon = 'Westernunion.png'; }
	elseif($name == "Moneygram") { $icon = 'Moneygram.png'; }
	elseif($name == "TheBillioncoin") { $icon = 'TheBillioncoin.png'; }
	elseif($name == "Edinarcoin") { $icon = 'Edinarcoin.png'; }
	elseif($name == "Mollie") { $icon = 'Mollie.png'; }
	else { 
		$check = $db->query("SELECT * FROM bit_gateways WHERE name='$name' and external_gateway='1'");
		if($check->num_rows>0) {
			$r = $check->fetch_assoc();
			$icon = $r['external_icon'];
			$external_icon = 1;
		} else {
			$cicon = GetCryptoCurrency($name);
			$cicon = 'assets/icons/crypto/'.$cicon.'.png';
			if(file_exists($cicon)) {
				$icon = $cicon;
				$external_icon = '1';
			} elseif(file_exists("../".$cicon)) {
				$icon = $cicon;
				$external_icon = '1';
			} else {
				$icon = "Missing.png";
			}
		}
	}
	if($external_icon == "1") {
		return $settings[url].$icon;
	} else {
		return $settings[url].$path.$icon;
	}
}

function exchangetype($name) {
	global $db, $settings;
	if($name == "PayPal") { $type = '2'; }
	elseif($name == "Skrill") { $type = '2'; }
	elseif($name == "WebMoney") { $type = '2'; }
	elseif($name == "Payeer") { $type = '2'; }
	elseif($name == "Perfect Money") { $type = '2'; }
	elseif($name == "AdvCash") { $type = '2'; }
	elseif($name == "OKPay") { $type = '2'; }
	elseif($name == "Entromoney") { $type = '2'; }
	elseif($name == "SolidTrust Pay") { $type = '2'; }
	elseif($name == "2checkout") { $type = '3'; }
	elseif($name == "Litecoin") { $type = '3'; }
	elseif($name == "Neteller") { $type = '3'; }
	elseif($name == "UQUID") { $type = '3'; }
	elseif($name == "Dash") { $type = '3'; }
	elseif($name == "Dogecoin") { $type = '3'; }
	elseif($name == "BTC-e") { $type = '3'; }
	elseif($name == "Ethereum") { $type = '3'; }
	elseif($name == "Peercoin") { $type = '3'; }
	elseif($name == "Yandex Money") { $type = '3'; }
	elseif($name == "QIWI") { $type = '3'; }
	elseif($name == "Payza") { $type = '2'; }
	elseif($name == "Bitcoin") { $type = '3'; }
	elseif($name == "Bank Transfer") { $type = '3'; }
	elseif($name == "Western Union") { $type = '3'; }
	elseif($name == "Moneygram") { $type = '3'; }
	elseif($name == "TheBillioncoin") { $type = '3'; }
	elseif($name == "Edinarcoin") { $type = '3'; }
	elseif($name == "Mollie") { $type = '2'; }
	else { $type = '3'; }
	return $type;
}

function decodeStatus($code,$type) {
	global $lang;
	if($type == "1") {
		if($code == "1") { 
			$status = '<span class="label label-warning" style="padding:10px;"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_1].'</span>';
		} elseif($code == "2") {
			$status = '<span class="label label-warning" style="padding:10px;"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_2].'</span>';
		} elseif($code == "3") {
			$status = '<span class="label label-info" style="padding:10px;"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_3].'</span>';
		} elseif($code == "4") { 
			$status = '<span class="label label-success" style="padding:10px;"><i class="fa fa-check" style="font-size:12px;"></i> '.$lang[status_4].'</span>';
		} elseif($code == "5") {
			$status = '<span class="label label-danger" style="padding:10px;"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_5].'</span>';
		} elseif($code == "6") {
			$status = '<span class="label label-danger" style="padding:10px;"><i class="fa fa-times" style="font-size:12px;"></i> '.$lang[status_6].'</span>';
		} elseif($code == "7") {
			$status = '<span class="label label-danger" style="padding:10px;"><i class="fa fa-times" style="font-size:12px;"></i> '.$lang[status_7].'</span>';
		} else {
			$status = '<span class="label label-default" style="padding:10px;"><i class="fa fa-bug" style="font-size:12px;"></i> Unknown</span>';
		}
	} elseif($type == "2") {
		if($code == "1") {
			$status = '<span class="label label-warning" data-toggle="tooltip" data-placement="top" title="'.$lang[status_1].'"><i class="fa fa-clock-o"></i> </span>';
		} elseif($code == "2") {
			$status = '<span class="label label-warning" data-toggle="tooltip" data-placement="top" title="'.$lang[status_2].'"><i class="fa fa-clock-o"></i> </span>';
		} elseif($code == "3") {
			$status = '<span class="label label-info" data-toggle="tooltip" data-placement="top" title="'.$lang[status_3].'"><i class="fa fa-clock-o"></i> </span>';
		} elseif($code == "4") {
			$status = '<span class="label label-success" data-toggle="tooltip" data-placement="top" title="'.$lang[status_4].'"><i class="fa fa-check"></i> </span>';
		} elseif($code == "5") {
			$status = '<span class="label label-danger" data-toggle="tooltip" data-placement="top" title="'.$lang[status_5].'"><i class="fa fa-clock-o"></i> </span>';
		} elseif($code == "6") {
			$status = '<span class="label label-danger" data-toggle="tooltip" data-placement="top" title="'.$lang[status_6].'"><i class="fa fa-times"></i> </span>';
		} elseif($code == "7") {
			$status = '<span class="label label-danger" data-toggle="tooltip" data-placement="top" title="'.$lang[status_7].'"><i class="fa fa-times"></i> </span>';
		} else {
			$status = '<span class="label label-default" data-toggle="tooltip" data-placement="top" title="Unknown"><i class="fa fa-bug"></i> </span>';
		}
	} elseif($type == "3") {
		if($code == "1") { 
			$status = '<span class="label label-warning"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_1].'</span>';
		} elseif($code == "2") {
			$status = '<span class="label label-warning"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_2].'</span>';
		} elseif($code == "3") {
			$status = '<span class="label label-info"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_3].'</span>';
		} elseif($code == "4") { 
			$status = '<span class="label label-success"><i class="fa fa-check" style="font-size:12px;"></i> '.$lang[status_4].'</span>';
		} elseif($code == "5") {
			$status = '<span class="label label-danger"><i class="fa fa-clock-o" style="font-size:12px;"></i> '.$lang[status_5].'</span>';
		} elseif($code == "6") {
			$status = '<span class="label label-danger"><i class="fa fa-times" style="font-size:12px;"></i> '.$lang[status_6].'</span>';
		} elseif($code == "7") {
			$status = '<span class="label label-danger"><i class="fa fa-times" style="font-size:12px;"></i> '.$lang[status_7].'</span>';
		} else {
			$status = '<span class="label label-default"><i class="fa fa-bug" style="font-size:12px;"></i> Unknown</span>';
		}
	} else { }
	return $status;
}

function get_verify_type() {
	global $settings;
	if($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "1") {
		$status = '1';
	} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
		$status = '2';
	} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "1") {
		$status = '3'; 
	} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "1") {
		$status = '4';
	} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
		$status = '5';
	} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "0") {
		$status = '6';
	} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
		$status = '7';
	} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "1") {
		$status = '8';
	} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "0") {
		$status = '9';
	} else {
		$status = '0';
	}
	return $status;
}

function check_user_verify_status() {
	global $db,$settings;
	$email_verified = idinfo($_SESSION['bit_uid'],"email_verified");
	$mobile_verified = idinfo($_SESSION['bit_uid'],"mobile_verified");
	$document_verified = idinfo($_SESSION['bit_uid'],"document_verified");
	$ustatus = idinfo($_SESSION['bit_uid'],"status");
	if($ustatus !== "666" && $ustatus !== "777") { 
		if($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "1") {
			if($document_verified == "1" && $email_verified == "1" && $mobile_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
			if($document_verified == "1" && $email_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "1") {
			if($document_verified == "1" && $mobile_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "1") {
			if($email_verified == "1" && $mobile_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
			if($document_verified == "1" && $email_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "1" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "0") {
			if($document_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "1" && $settings['phone_verification'] == "0") {
			if($email_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "1") {
			if($mobile_verified == "1") {
				$update = $db->query("UPDATE bit_users SET status='3' WHERE id='$_SESSION[bit_uid]'");
			}
		} elseif($settings['document_verification'] == "0" && $settings['email_verification'] == "0" && $settings['phone_verification'] == "0") {
			$status = '9';
		} else {
			$status = '0';
		}
	}
}

function getLanguage($url, $ln = null, $type = null) {
	global $settings;
	// Type 1: Output the available languages
	// Type 2: Change the path for the /requests/ folder location
	// Set the directory location
	if($type == 2) {
		$languagesDir = '../languages/';
	} else {
		$languagesDir = './languages/';
	}
	// Search for pathnames matching the .png pattern
	$language = glob($languagesDir . '*.php', GLOB_BRACE);

	if($type == 1) {
		// Add to array the available images
		foreach($language as $lang) {
			// The path to be parsed
			$path = pathinfo($lang);
			
			// Add the filename into $available array
			if($path['filename'] == "index") {
			
			} else {
			$available .= '<a href="'.$url.'index.php?lang='.$path['filename'].'">'.ucfirst(strtolower($path['filename'])).'</a>   ';
			}
		}
		return substr($available, 0, -3);
	} else {
		// If get is set, set the cookie and stuff
		$lang = $settings['default_language']; // DEFAULT LANGUAGE
		if($type == 2) {
			$path = '../languages/';
		} else {
			$path = './languages/';
		}
		if(isset($_GET['lang'])) {
			if(in_array($path.$_GET['lang'].'.php', $language)) {
				$lang = $_GET['lang'];
				setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
			} else {
				setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
			}
			header("Location: $settings[url]");
		} elseif(isset($_COOKIE['lang'])) {
			if(in_array($path.$_COOKIE['lang'].'.php', $language)) {
				$lang = $_COOKIE['lang'];
			}
		} else {
			setcookie('lang', $lang, time() +  (10 * 365 * 24 * 60 * 60)); // Expire in one month
		}

		if(in_array($path.$lang.'.php', $language)) {
			return $path.$lang.'.php';
		}
	}
}

function formatBytes($bytes, $precision = 2) { 
    if ($bytes > pow(1024,3)) return round($bytes / pow(1024,3), $precision)."GB";
    else if ($bytes > pow(1024,2)) return round($bytes / pow(1024,2), $precision)."MB";
    else if ($bytes > 1024) return round($bytes / 1024, $precision)."KB";
    else return ($bytes)."B";
} 

function check_unpayed() {
	global $db;
	$query = $db->query("SELECT * FROM bit_exchanges WHERE status='1' or status='2' ORDER BY id");
	if($query->num_rows>0) {
		while($row = $query->fetch_assoc()) {
			$time = $row['created']+86400;
			if(time() > $time) {
				$time = time();
				$update = $db->query("UPDATE bit_exchanges SET status='5',expired='$time' WHERE id='$row[id]'");
			}
		}
	} 
}

function checklicense() {
	global $db, $settings;
	$license_key = $settings['license_key'];
	$domain = $_SERVER['SERVER_NAME'];
	$checkurl = '/?check=1&license_key='.$license_key.'&domain='.$domain;
	$contents = file_get_contents($checkurl);
	$json_a=json_decode($contents,true);
	
	foreach ($json_a as $key => $value){
		$string[$key] = $value;
	}
								
	if($string['status'] == "error") {
		die($string['message']);
	}
} 

function getCrypto2CryptoPrice($from,$to) {
	$ch = curl_init();
	$url = "https://min-api.cryptocompare.com/data/price?fsym=$from&tsyms=$to";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	if($json[$to]) {
		return $json[$to];
	} else {
		return '0';
	}
}

function get_rates($gateway_send,$gateway_receive) {
	global $db, $settings;
	$gateway_sendname = gatewayinfo($gateway_send,"name");
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
						} elseif(gatewayinfo($gateway_receive,"is_crypto") == "1") {
							if(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
								$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
								if($query->num_rows>0) {
									$row = $query->fetch_assoc();
									$data['status'] = 'success';
									$rate_from = $row['rate_from'];
									$rate_to = $row['rate_to'];
								} else {
									$price = getCrypto2CryptoPrice($currency_from,$currency_to);
									$fee = str_ireplace("-","",$fee);
									$calculate1 = ($price * $fee) / 100;
									$calculate2 = $price - $calculate1;
									$calculate2 = number_format($calculate2, 6, '.', '');
									$data['status'] = 'success';
									$rate_from = 1;
									$rate_to = $calculate2;
								}
							} else {
									$price = getCryptoPrice($currency_to);
									if($currency_from == "USD" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
										$price = $price;
									} else {
										$price = currencyConvertor($price,"USD",$currency_from);
									}
									$calculate1 = ($price * $fee) / 100;
									$calculate2 = $price - $calculate1;
									$calculate2 = number_format($calculate2, 6, '.', '');
									$rate_to = 1;
									$rate_from = $calculate2;
							}
						}  elseif(gatewayinfo($gateway_send,"is_crypto") == "1") {
							if(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
								$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
								if($query->num_rows>0) {
									$row = $query->fetch_assoc();
									$data['status'] = 'success';
									$rate_from = $row['rate_from'];
									$rate_to = $row['rate_to'];
								} else {
									$price = getCrypto2CryptoPrice($currency_from,$currency_to);
									$fee = str_ireplace("-","",$fee);
									$calculate1 = ($price * $fee) / 100;
									$calculate2 = $price - $calculate1;
									$calculate2 = number_format($calculate2, 6, '.', '');
									$data['status'] = 'success';
									$rate_from = 1;
									$rate_to = $calculate2;
								}
							} else {
									$price = getCryptoPrice($currency_from);
									if(gatewayinfo($gateway_send,"is_crypto") == "1" && $currency_to == "USD") {
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
						} elseif(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$price = getCrypto2CryptoPrice($currency_from,$currency_to);
									$fee = str_ireplace("-","",$fee);
									$calculate1 = ($price * $fee) / 100;
									$calculate2 = $price - $calculate1;
									$calculate2 = number_format($calculate2, 6, '.', '');
									$data['status'] = 'success';
									$rate_from = 1;
									$rate_to = $calculate2;
							}
						} else {
							if(gatewayinfo($gateway_send,"is_crypto") == "1" && gatewayinfo($gateway_receive,"is_crypto") == "0") {
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
							} elseif(gatewayinfo($gateway_send,"is_crypto") == "0" && gatewayinfo($gateway_receive,"is_crypto") == "1") {
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
		$data['currency_from'] = $currency_from;
		$data['currency_to'] = $currency_to;
	}
	return $data;
}

function getCryptoPrice($coin) {
	$ch = curl_init();
	$url = "https://min-api.cryptocompare.com/data/price?fsym=$coin&tsyms=USD";
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);
	$json = json_decode($result, true);
	if($json['USD']) {
		return $json['USD'];
	} else {
		return '0';
	}
}

function checkCryptoExchange($gateway_send,$gateway_receive) {
	$isCrypto_1 = isCrypto($gateway_send);
	$isCrypto_2 = isCrypto($gateway_receive);
	if($isCrypto_1 == "1" && $isCrypto_2 == "1") {
		return true;
	} else {
		return false;
	}
}

function cryptoPrefix($gateway) {
	if($gateway == "Bitcoin") { $prefix = 'bitcoin_BTC'; }
	elseif($gateway == "Litecoin") { $prefix = 'litecoin_LTC'; }
	elseif($gateway == "Dogecoin") { $prefix = 'dogecoin_DOGE'; }
	elseif($gateway == "Dash") { $prefix = 'dash_DASH'; }
	elseif($gateway == "Ethereum") { $prefix = 'ethereum_ETH'; }
	elseif($gateway == "Peercoin") { $prefix = 'peercoin_PPC'; }
	else {
		$prefix = 'Unknown';
	}	
	return $prefix;
}

function isCrypto($gateway) {
	if($gateway == "Bitcoin") { $prefix = '1'; }
	elseif($gateway == "Litecoin") { $prefix = '1'; }
	elseif($gateway == "Dogecoin") { $prefix = '1'; }
	elseif($gateway == "Dash") { $prefix = '1'; }
	elseif($gateway == "Ethereum") { $prefix = '1'; }
	elseif($gateway == "Peercoin") { $prefix = '1'; }
	else {
		$prefix = '0';
	}	
	return $prefix;
}

function CryptoSupport($merchant) {
	if($merchant == "block.io") {
		$supported_coins = array("Bitcoin","Litecoin","Dogecoin");
		return $supported_coins;
	} elseif($merchant == "coinpayments.net") {
		$supported_coins = array("Bitcoin","Litecoin","Dogecoin","CPS Coin","Bitcoin Cash","Bytecoin","BitBean","BlackCoin","Breakout","CloakCoin","ClubCoin","Crown","CureCoin","Dash","Decred","DigiByte","eBoost","Ether Classic","Ether","Goldcoin","Groestlcoin","Komodo","LISK","MonetaryUnit","NAV Coin","NEO","Namecoin","NXT","Pinkcoin","PoSW Coin","Potcoin","Peercoin","ProCurrency","Pura","Qtum","SmartCash","Stratis","Syscoin","TokenPay","Triggers","Ubiq","Vertcoin","Waves","Counterparty","NEM","Monero","VERGE","ZCoin","ZCash","ZenCash");
		return $supported_coins;
	} else {
		return false;
	}
}

function GetCryptoCurrency($gateway) {
	if($gateway == "Bitcoin") { $currency = 'BTC'; }
	elseif($gateway == "Litecoin") { $currency = 'LTC'; }
	elseif($gateway == "Dogecoin") { $currency = 'DOGE'; }
	elseif($gateway == "CPS Coin") { $currency = 'CPS'; }
	elseif($gateway == "Bitcoin Cash") { $currency = 'BCH'; }
	elseif($gateway == "Bytecoin") { $currency = 'BCN'; } 
	elseif($gateway == "BitBean") { $currency = 'BITB'; }
	elseif($gateway == "BlackCoin") { $currency = 'BLK'; }
	elseif($gateway == "Breakout") { $currency = 'BRK'; }
	elseif($gateway == "CloakCoin") { $currency = 'CLOAK'; } 
	elseif($gateway == "ClubCoin") { $currency = 'CLUB'; }
	elseif($gateway == "Crown") { $currency = 'CRW'; } 
	elseif($gateway == "CureCoin") { $currency = 'CURE'; }
	elseif($gateway == "Dash") { $currency = 'DASH'; } 
	elseif($gateway == "Decred") { $currency = 'DCR'; }
	elseif($gateway == "DigiByte") { $currency = 'DGB'; }
	elseif($gateway == "eBoost") { $currency = 'EBST'; }
	elseif($gateway == "Ether Classic") { $currency = 'ETC'; }
	elseif($gateway == "Ether") { $currency = 'ETH'; }
	elseif($gateway == "Goldcoin") { $currency = 'GLD'; }
	elseif($gateway == "Groestlcoin") { $currency = 'GRS'; } 
	elseif($gateway == "Komodo") { $currency = 'KMD'; }
	elseif($gateway == "LISK") { $currency = 'LSK'; }
	elseif($gateway == "MonetaryUnit") { $currency = 'MUE'; }
	elseif($gateway == "NAV Coin") { $currency = 'NAV'; } 
	elseif($gateway == "NEO") { $currency = 'NEO'; }
	elseif($gateway == "Namecoin") { $currency = 'NMC'; }
	elseif($gateway == "NXT") { $currency = 'NXT'; }
	elseif($gateway == "PinkCoin") { $currency = 'PINK'; }
	elseif($gateway == "Potcoin") { $currency = 'POT'; } 
	elseif($gateway == "Peercoin") { $currency = 'PPC'; }
	elseif($gateway == "ProCurrency") { $currency = 'PROC'; }
	elseif($gateway == "Pura") { $currency = 'PURA'; }
	elseif($gateway == "Qtum") { $currency = 'QTUM'; }
	elseif($gateway == "Smart Dollars") { $currency = 'SBD'; } 
	elseif($gateway == "SmartCash") { $currency = 'SMART'; }
	elseif($gateway == "SOXAX") { $currency = 'SOXAX'; }
	elseif($gateway == "STEEM") { $currency = 'STEEM'; } 
	elseif($gateway == "Stratis") { $currency = 'STRAT'; }
	elseif($gateway == "Syscoin") { $currency = 'SYS'; }
	elseif($gateway == "TokenPay") { $currency = 'TPAY'; }
	elseif($gateway == "Triggers") { $currency = 'TRIG'; }
	elseif($gateway == "Ubiq") { $currency = 'UBQ'; }
	elseif($gateway == "UniversalCurrency") { $currency = 'UNIT'; }
	elseif($gateway == "Vertcoin") { $currency = 'VTC'; }
	elseif($gateway == "Waves") { $currency = 'WAVES'; }
	elseif($gateway == "Counterparty") { $currency = 'XCP'; }
	elseif($gateway == "NEM") { $currency = 'XEM'; }
	elseif($gateway == "Monero") { $currency = 'XMR'; }
	elseif($gateway == "Stakenet") { $currency = 'XSN'; }
	elseif($gateway == "VERGE") { $currency = 'XVG'; }
	elseif($gateway == "ZCoin") { $currency = 'XZC'; } 
	elseif($gateway == "ZCash") { $currency = 'ZEC'; }
	elseif($gateway == "ZenCash") { $currency = 'ZEN'; }
	else { $currency = 'Unknown'; }
	return $currency;
}

?>