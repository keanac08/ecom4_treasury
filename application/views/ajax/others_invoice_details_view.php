<?php 
$this->load->helper('null_helper');
$this->load->helper('number_helper');
?>
<div class="modal-header">
	<span class="pull-right">Status : <?php echo nvl($header->STATUS); ?></span>
	<h4 class="modal-title">Others Invoice Details</h4>
</div>
<div class="modal-body">
	<div class="row">
		
	</div> 
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						
						<th>#</th>
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
						<td class="text-right" colspan="3"></td>
						<td class="text-center" ><?php echo $total_qty;?></td>
						<td class="text-right" ></td>
						<td class="text-right"><?php echo amount($total_net_amount); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="col-sm-6">
				<strong>Invoice Number</strong>
				<p class="text-muted"><?php echo nvl($header->TRX_NUMBER); ?></p>
				<strong>Invoice Date</strong>
				<p class="text-muted"><?php echo nvl($header->TRX_DATE); ?></p>
				<strong>Cutomer PO Number</strong>
				<p class="text-muted">-</p>
			</div>
			<div class="col-sm-6">
				<strong>Order Number</strong>
				<p class="text-muted">-</p>
				<strong>Order Date</strong>
				<p class="text-muted">-</p>
				<strong>Order Type</strong>
				<p class="text-muted">-</p>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="well well-sm">
				<table class="table table-condensed table-striped">
					<tr>
						<td><strong>Currency</strong></td>
						<td class="text-right"><?php echo $header->CURRENCY != 'PHP' ?  $header->CURRENCY .' ('.$header->EXCHANGE_RATE.') ':$header->CURRENCY; ?></td>
					</tr>
					<tr>
						<td><strong>Net Amount</strong></td>
						<td class="text-right"><?php echo amount($header->TOTAL_NET_AMOUNT); ?></td>
					</tr>
					<tr>
						<td><strong>Vat Amount</strong></td>
						<td class="text-right"><?php echo amount($header->TOTAL_VAT_AMOUNT); ?></td>
					</tr>
					<tr>
						<td style="font-size: 120%;"><strong>Transaction Amount</strong></td>
						<td style="font-size: 120%;" class="text-right">
							<strong>
									<?php echo amount($header->INVOICE_AMOUNT); ?>
							</strong>
						</td>
					</tr>
					<tr>
						<td><strong>WHT Amount</strong></td>
						<td class="text-right"><?php echo amount($header->TOTAL_NET_AMOUNT * .01); ?></td>
					</tr>
					<tr>
						<td><strong>Amount Due</strong></td>
						<td class="text-right"><?php echo amount($header->INVOICE_AMOUNT - ($header->TOTAL_NET_AMOUNT * .01)); ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Paid Amount</strong></td>
						<td class="text-right"><?php echo amount($header->PAID_AMOUNT + ( -1 * ($header->ADJUSTED_AMOUNT + $header->CREDITED_AMOUNT))); ?></td>
					</tr>
					<tr>
						<td style="font-size: 110%;" class="text-danger"><strong>Balance</strong></td>
						<td style="font-size: 110%;" class="text-right text-danger"><strong><?php echo amount($header->BALANCE); ?></strong></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

