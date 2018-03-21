<?php 
	$this->load->helper('date_helper');
	$this->load->helper('number_helper');
	$this->load->helper('null_helper');
?>
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#header" aria-expanded="true">Header Details</a></li>
					<li class=""><a data-toggle="tab" href="#line" aria-expanded="true">Line Details</a></li>
					<li class=""><a data-toggle="tab" href="#payment" aria-expanded="true">Payment Details</a></li>
				</ul>
				<div class="tab-content">
					<div id="header" class="tab-pane active">
						<div class="row">
							<div class="col-sm-10">
								<div class="col-sm-3">
									<p class="lead">Invoice Details</p>
									<strong>Invoice Number</strong>
									<p class="text-muted">
										<?php echo $header->INVOICE_NUMBER; ?>
									</p>
									<strong>Invoice Date</strong>
									<p class="text-muted">
										<?php echo short_date($header->INVOICE_DATE); ?>
									</p>
									<strong>Invoice Status</strong>
									<p class="text-muted">
										<?php echo $header->STATUS; ?>
									</p>
									<strong>Invoice Type</strong>
									<p class="text-muted">
										<?php echo $header->INVOICE_TYPE; ?>
									</p>
									
									<strong><?php echo (in_array($header->CUST_TRX_TYPE_ID, array(2082,4081,3080)) ? 'Delivery': 'Pullout')?> Date</strong>
									<p class="text-muted">
										<?php echo short_date($header->DELIVERY_DATE); ?>
									</p>
									<strong>Due Date</strong>
									<p class="text-muted">
										<?php echo short_date($header->DUE_DATE); ?>
									</p>
								</div>
								<div class="col-sm-3">
									<p class="lead">Order Details</p>
									<strong>Order Number</strong>
									<p class="text-muted">
										<?php echo NVL($header->ORDER_NUMBER); ?>
									</p>
									<strong>Order Date</strong>
									<p class="text-muted">
										<?php echo short_date($header->ORDERED_DATE); ?>
									</p>
									<strong>Order Type</strong>
									<p class="text-muted">
										<?php echo NVL($header->ORDER_TYPE); ?>
									</p>
									<strong>DR Number</strong>
									<p class="text-muted">
										<?php echo NVL($header->DR_NUMBER); ?>
									</p>
									<strong>Payment Terms</strong>
									<p class="text-muted">
										<?php echo NVL($header->PAYMENT_TERMS); ?>
									</p>
									<strong>Customer PO Number</strong>
									<p class="text-muted">
										<?php echo NVL($header->CUST_PO_NUMBER); ?>
									</p>
								</div>
								<div class="col-sm-6">
									<p class="lead">Customer Details</p>
									<strong>Customer Name</strong>
									<p class="text-muted">
										<?php echo $header->PARTY_NAME; ?>
									</p>
									<strong>Account Name</strong>
									<p class="text-muted">
										<?php echo NVL($header->ACCOUNT_NAME); ?>
									</p>
									<strong>Profile Class</strong>
									<p class="text-muted">
										<?php echo $header->PROFILE_CLASS; ?>
									</p>
									<?php
									if(!in_array($header->CUST_TRX_TYPE_ID, array(2082,4081,3080))){
									?>
									<strong>Fleet Name</strong>
									<p class="text-muted">
										<?php echo NVL($header->FLEET_NAME); ?>
									</p>
									<?php 
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div id="line" class="tab-pane">
						<div class="row">
							<?php 
							if($sales_type == 'vehicle'){
							?>
							<div class="col-sm-10">
								<div class="col-sm-3">
									<p class="lead">Unit Details</p>
									<strong>CS Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->CS_NUMBER); ?>
									</p>
									<strong>Sales Model</strong>
									<p class="text-muted">
										<?php echo NVL($line->SALES_MODEL); ?>
									</p>
									<strong>Body Color</strong>
									<p class="text-muted">
										<?php echo NVL($line->BODY_COLOR); ?>
									</p>
									<strong>CSR Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->CSR_NUMBER); ?>
									</p>
									<strong>WB Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->WB_NUMBER); ?>
									</p>
									<strong>Buyoff Date</strong>
									<p class="text-muted">
										<?php echo short_date($line->BUYOFF_DATE); ?>
									</p>
								</div>
								<div class="col-sm-3">
									<p class="lead">&nbsp;</p>
									<strong>Chassis Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->CHASSIS_NUMBER); ?>
									</p>
									<strong>Engine Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->ENGINE_NUMBER); ?>
									</p>
									<strong>Lot Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->LOT_NUMBER); ?>
									</p>
									<strong>Key Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->KEY_NUMBER); ?>
									</p>
									<strong>Aircon Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->AIRCON_NUMBER); ?>
									</p>
									<strong>Stereo Number</strong>
									<p class="text-muted">
										<?php echo NVL($line->STEREO_NUMBER); ?>
									</p>
								</div>
								<div class="col-sm-6">
									<p class="lead">Amount Details</p>
									<table class="table table-condensed table-striped">
										<tr>
											<td><strong>Currency</strong></td>
											<td class="text-right"><?php echo $line->CURRENCY != 'PHP' ?  $line->CURRENCY .' ('.$line->EXCHANGE_RATE.') ':$line->CURRENCY; ?></td>
										</tr>
										<tr>
											<td><strong>Net Amount</strong></td>
											<td class="text-right"><?php echo amount($line->NET_AMOUNT); ?></td>
										</tr>
										<tr>
											<td><strong>Vat Amount</strong></td>
											<td class="text-right"><?php echo amount($line->VAT_AMOUNT); ?></td>
										</tr>
										<tr>
											<td style="font-size: 120%;"><strong>Transaction Amount</strong></td>
											<td style="font-size: 120%;" class="text-right">
												<strong>
														<?php echo amount($line->INVOICE_AMOUNT); ?>
												</strong>
											</td>
										</tr>
										<tr>
											<td><strong>WHT Amount</strong></td>
											<td class="text-right"><?php echo amount($line->NET_AMOUNT * .01); ?></td>
										</tr>
										<tr>
											<td><strong>Amount Due</strong></td>
											<td class="text-right"><?php echo amount($line->INVOICE_AMOUNT - ($line->NET_AMOUNT * .01)); ?></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><strong>Paid Amount</strong></td>
											<td class="text-right"><?php echo amount($line->PAID_AMOUNT + ( -1 * ($line->ADJUSTED_AMOUNT + $line->CREDITED_AMOUNT))); ?></td>
										</tr>
										<tr>
											<td style="font-size: 110%;" class="text-danger"><strong>Balance</strong></td>
											<td style="font-size: 110%;" class="text-right text-danger"><strong><?php echo amount($line->BALANCE); ?></strong></td>
										</tr>
									</table>
								</div>
							</div>
							<?php 
							}
							else if($sales_type == 'parts'){
							?>
							<div class="col-sm-8">
								<table class="table">
									<thead>
										<tr>
											
											<th>#</th>
											<th>Part Number</th>
											<th class="text-center">Line Number</th>
											<th>Part Desciption</th>
											<th class="text-center">Qty</th>
											<th class="text-right">Unit Selling Price</th>
											<th class="text-right">Line Total</th>
											
										</tr>
									</thead>
									<tbody>
										<?php 
										$x = 0;
										$net_amount = 0;
										$vat_amount = 0;
										$total_qty = 0;
										$total_net_amount = 0;
										$ctr = 1;
										foreach($lines as $line){
											$line = (object)$line;
										?>
											<tr>
											
												<td><?php echo $ctr++;; ?></td>
												<td><?php echo $line->PART_NO; ?></td>
												<td class="text-center"><?php echo $line->LINE_NUMBER; ?></td>
												<td><?php echo $line->PART_DESCRIPTION; ?></td>
												<td class="text-center"><?php echo $line->QTY; ?></td>
												<td class="text-right"><?php echo amount($line->UNIT_SELLING_PRICE); ?></td>
												<td class="text-right"><?php echo amount($line->NET_AMOUNT); ?></td>
											</tr>
										<?php 
										$total_qty += $line->QTY;
										$total_net_amount += $line->NET_AMOUNT;
										}
										?>
										<tr style="background-color: #ccc;font-weight: bold;">
											<td class="text-right" colspan="4"></td>
											<td class="text-center" ><?php echo $total_qty;?></td>
											<td class="text-right" ></td>
											<td class="text-right"><?php echo amount($total_net_amount); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-sm-4">
								<div class="well well-sm">
									<table class="table table-striped">
										<tr>
											<td><strong>Currency</strong></td>
											<td class="text-right"><?php echo $lines_header->CURRENCY != 'PHP' ?  $lines_header->CURRENCY .' ('.$lines_header->EXCHANGE_RATE.') ':$lines_header->CURRENCY; ?></td>
										</tr>
										<tr>
											<td><strong>Net Amount</strong></td>
											<td class="text-right"><?php echo amount($lines_header->TOTAL_NET_AMOUNT); ?></td>
										</tr>
										<tr>
											<td><strong>Vat Amount</strong></td>
											<td class="text-right"><?php echo amount($lines_header->TOTAL_VAT_AMOUNT); ?></td>
										</tr>
										<tr>
											<td style="font-size: 120%;"><strong>Transaction Amount</strong></td>
											<td style="font-size: 120%;" class="text-right">
												<strong>
														<?php echo amount($lines_header->INVOICE_AMOUNT); ?>
												</strong>
											</td>
										</tr>
										<tr>
											<td><strong>WHT Amount</strong></td>
											<td class="text-right"><?php echo amount($lines_header->TOTAL_NET_AMOUNT * .01); ?></td>
										</tr>
										<tr>
											<td><strong>Amount Due</strong></td>
											<td class="text-right"><?php echo amount($lines_header->INVOICE_AMOUNT - ($lines_header->TOTAL_NET_AMOUNT * .01)); ?></td>
										</tr>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><strong>Paid Amount</strong></td>
											<td class="text-right"><?php echo amount($lines_header->PAID_AMOUNT + ( -1 * ($lines_header->ADJUSTED_AMOUNT + $lines_header->CREDITED_AMOUNT))); ?></td>
										</tr>
										<tr>
											<td style="font-size: 110%;" class="text-danger"><strong>Balance</strong></td>
											<td style="font-size: 110%;" class="text-right text-danger"><strong><?php echo amount($lines_header->BALANCE); ?></strong></td>
										</tr>
									</table>
								</div>
							</div>
							<?php 
							}
							?>
						</div>
					</div>
					<div id="payment" class="tab-pane">
						<div class="row">
							<div class="col-sm-12">
								<table class="table">
									<thead>
										<tr>
											<th>Receipt Number</th>
											<th>Receipt Date</th>
											<th>Currency Code</th>
											<th>Payment Type</th>
											<th>Paid Date</th>
											<th class="text-right">Paid Amount</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$total_amount = 0;
										foreach($payments as $row){
										?>
										<tr>
											<td><?php echo NVL($row->RECEIPT_NUMBER); ?></td>
											<td><?php echo short_date($row->RECEIPT_DATE); ?></td>
											<td><?php echo  NVL($row->CURRENCY_CODE); ?></td>
											<td><?php echo $row->APPLICATION_TYPE; ?></td>
											<td><?php echo short_date($row->APPLY_DATE); ?></td>
											<td class="text-right"><?php echo amount($row->AMOUNT_APPLIED); ?></td>
										</tr>
										<?php 
										$total_amount += $row->AMOUNT_APPLIED;
										}
										?>
										<tr>
											<td colspan="6">&nbsp;</td>
										</tr>
										<tr>
											<td class="text-right" colspan="5"><strong>Total Amount Applied : </strong></td>
											<td class="text-right"><strong><?php echo amount($total_amount); ?></strong></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.nav-tabs li a').click(function(e){
		  e.preventDefault();
		  e.stopImmediatePropagation();
		  $(this).tab('show');
		});
	});
</script>
