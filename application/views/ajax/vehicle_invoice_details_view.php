<?php 
$this->load->helper('null_helper');
$this->load->helper('number_helper');
?>
<div class="modal-header">
	<span class="pull-right">Status : <?php echo nvl($data->STATUS); ?></span>
	<h4 class="modal-title">Vehicle Invoice Details</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-sm-3">
			<strong>Invoice Number</strong>
			<p class="text-muted"><?php echo nvl($data->TRX_NUMBER); ?></p>
			<strong>Invoice Date</strong>
			<p class="text-muted"><?php echo nvl($data->TRX_DATE); ?></p>
			<strong>Order Number</strong>
			<p class="text-muted"><?php echo nvl($data->ORDER_NUMBER); ?></p>
			<strong>Order Date</strong>
			<p class="text-muted"><?php echo nvl($data->ORDERED_DATE); ?></p>
			<strong>Order Type</strong>
			<p class="text-muted"><?php echo nvl($data->ORDER_TYPE); ?></p>
			<strong>Cutomer PO Number</strong>
			<p class="text-muted"><?php echo nvl($data->PO_NUMBER); ?></p>
		</div>
		<div class="col-sm-3">
			<strong>CS Number</strong>
			<p class="text-muted"><?php echo nvl($data->CS_NUMBER); ?></p>
			<strong>Chassis Number</strong>
			<p class="text-muted"><?php echo nvl($data->CHASSIS_NUMBER); ?></p>
			<strong>Sales Model</strong>
			<p class="text-muted"><?php echo nvl($data->SALES_MODEL); ?></p>
			<strong>Body Color</strong>
			<p class="text-muted"><?php echo nvl($data->BODY_COLOR); ?></p>
			<strong>CSR Number</strong>
			<p class="text-muted"><?php echo nvl($data->CSR_NUMBER); ?></p>
			<strong>WB Number</strong>
			<p class="text-muted"><?php echo nvl($data->WB_NUMBER); ?></p>
		</div>
		<div class="col-sm-6">
			<div class="well well-sm">
				<table class="table table-condensed table-striped">
					<tr>
						<td><strong>Currency</strong></td>
						<td class="text-right"><?php echo $data->CURRENCY != 'PHP' ?  $data->CURRENCY .' ('.$data->EXCHANGE_RATE.') ':$data->CURRENCY; ?></td>
					</tr>
					<tr>
						<td><strong>Net Amount</strong></td>
						<td class="text-right"><?php echo amount($data->NET_AMOUNT); ?></td>
					</tr>
					<tr>
						<td><strong>Vat Amount</strong></td>
						<td class="text-right"><?php echo amount($data->VAT_AMOUNT); ?></td>
					</tr>
					<tr>
						<td style="font-size: 120%;"><strong>Transaction Amount</strong></td>
						<td style="font-size: 120%;" class="text-right">
							<strong>
									<?php echo amount($data->INVOICE_AMOUNT); ?>
							</strong>
						</td>
					</tr>
					<tr>
						<td><strong>WHT Amount</strong></td>
						<td class="text-right"><?php echo amount($data->NET_AMOUNT * .01); ?></td>
					</tr>
					<tr>
						<td><strong>Amount Due</strong></td>
						<td class="text-right"><?php echo amount($data->INVOICE_AMOUNT - ($data->NET_AMOUNT * .01)); ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><strong>Paid Amount</strong></td>
						<td class="text-right"><?php echo amount($data->PAID_AMOUNT + (-1 * ($data->ADJUSTED_AMOUNT + $data->CREDITED_AMOUNT))); ?></td>
					</tr>
					<tr>
						<td style="font-size: 110%;" class="text-danger"><strong>Balance</strong></td>
						<td style="font-size: 110%;" class="text-right text-danger"><strong><?php echo amount($data->BALANCE); ?></strong></td>
					</tr>
				</table>
			</div>
		</div>
	</div> 
</div>
<div class="modal-footer">
	<a target="blank" href="<?php echo base_url('reports/invoice_vehicle_pdf/index/'.$data->CUSTOMER_TRX_ID); ?>" class="btn btn-danger" >Print Invoice Copy</a>
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

