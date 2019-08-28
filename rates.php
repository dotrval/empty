<?php
ob_start();
session_start();
error_reporting(0);
include("configs/bootstrap.php");
include("includes/bootstrap.php");
include(getLanguage($settings['url'],null,null));

$s = protect($_GET['s']);
if($s == "xml") {
    header('Content-Type: text/xml');
    echo '<rates>';
    $query = $db->query("SELECT * FROM bit_xmlrates ORDER BY id");
    if($query->num_rows>0) {
        while($row = $query->fetch_assoc()) {
            if($row['automatic_rate'] == "1") {
                $rates = get_rates($row['gateway_from'],$row['gateway_to']);
                $rate_from = $rates['rate_from'];
                $rate_to = $rates['rate_to'];
            } else {
                $rate_from = $row['rate_from'];
                $rate_to = $row['rate_to'];
            }
            echo '<item>
            <from>'.$row[gateway_from_prefix].'</from>
            <to>'.$row[gateway_to_prefix].'</to>
            <in>'.$rate_from.'</in>
            <out>'.$rate_to.'</out>
            <amount>'.gatewayinfo($row[gateway_to],"reserve").'</amount>
            </item>';
        }
    }
    echo '</rates>';
} else {
    echo 'Unknown export type.';
}
?>