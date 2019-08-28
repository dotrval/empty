
	<section class="section">
            <div class="container">
                <header class="section__title">
                    <h2><?php echo $lang['track_exchange']; ?></h2>
                </header>

                <div class="row">
                    <div class="col-md-12 faq">
                        <div class="card faq__item">
                            <div class="card__body">
                  									<div id="bit_exchange_box">
					
						<table class="table table-striped">
						<tbody>
							<tr>
								<td colspan="4">
									<h2 class="text-center">
										<?php if($row['wid']>0) { echo 'Wallet '.walletinfo($row['wid'],"currency"); } else { ?><img src="<?php echo gatewayicon(gatewayinfo($row['gateway_send'],"name")); ?>" width="36px" height="36px" class="img-circle"> <b><?php echo gatewayinfo($row['gateway_send'],"name"); ?> <?php echo gatewayinfo($row['gateway_send'],"currency"); ?></b><?php } ?>
										&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;
										<img src="<?php echo gatewayicon(gatewayinfo($row['gateway_receive'],"name")); ?>" width="36px" height="36px" class="img-circle"> <b><?php echo gatewayinfo($row['gateway_receive'],"name"); ?> <?php echo gatewayinfo($row['gateway_receive'],"currency"); ?></b>
									</h2><br>
									<?php echo $lang['exchange']; ?>: <?php echo $row['exchange_id']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><?php echo $lang['amount_send']; ?>: <?php echo $row['amount_send']; ?> <?php echo gatewayinfo($row['gateway_send'],"currency"); ?></td>
								<td colspan="2"><?php echo $lang['amount_receive']; ?>: <?php echo $row['amount_receive']; ?> <?php echo gatewayinfo($row['gateway_receive'],"currency"); ?></td>
							</tr>
							<tr>
								<td colspan="2"><?php echo $lang['exchange_rate']; ?>: <?php echo $row['rate_from']." ".$bit_currency_from; ?> = <?php echo $row['rate_to']." ".$bit_currency_to; ?></td>
								<td colspan="2"><?php echo $lang['transaction_number']; ?>: <?php if($row['transaction_id']) { echo $row['transaction_id']; } else { echo '-'; } ?></td>
							</tr>
							<tr>
								<td colspan="2">
										<?php echo $lang['process_type']; ?>:
										<?php
										$process_type = gatewayinfo($row['gateway_send'],"exchange_type");
										if($process_type == "1") {
											echo '<span class="label label-info">'.$lang[process_type_automatically].'</span>';
										} elseif($process_type == "2") {
											echo '<span class="label label-info">'.$lang[process_type_semi_automatic].'</span>';
										} elseif($process_type == "3") {	
											echo '<span class="label label-info">'.$lang[process_type_manually].'</span>';
										} else {
											echo '<span class="label label-default">'.$lang[process_type_manually].'</span>';
										}
										?>
								</td>
								<td colspan="2">
										<?php echo $lang['status']; ?>:
										<?php
										echo decodeStatus($row['status'],3);
										?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?php echo $lang['created_on']; ?> 
										<?php
										if($row['created']) {
											echo '<span class="label label-default">'.date("d/m/Y H:i:s",$row[created]).'</span>';
										} else {
											echo '-';
										}
										?>
								</td>
								<?php if($row['status']>4 && $row['expired']>0) { ?>
								<td colspan="2">
									<?php echo $lang['expired_on']; ?> 
										<?php
										if($row['expired']) {
											echo '<span class="label label-default">'.date("d/m/Y H:i:s",$row[expired]).'</span>';
										} else {
											echo '-';
										}
										?>
								</td>
								<?php } ?>
								<?php if($row['status'] == "4" && $row['updated']>0) { ?>
								<td colspan="2">
									<?php echo $lang['processed_on']; ?> 
										<?php
										if($row['updated']) {
											echo '<span class="label label-default">'.date("d/m/Y H:i:s",$row[updated]).'</span>';
										} else {
											echo '-';
										}
										?>
								</td>
								<?php } ?>
							</tr>
							</tbody>
					</table>
					
			</div>

				
                            </div>
                        </div>
					</diV>
				</div>
		</div>
	</section>