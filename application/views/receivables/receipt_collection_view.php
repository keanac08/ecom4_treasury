<?php 
$this->load->helper('number_helper');
$this->load->helper('null_helper');

$data = '';
$total_amount_applied = 0;
$total_amount_applied_2 = 0;
$total_balance_payable = 0;
$total_invoice_amount = 0;
$total_wht_amount = 0;
$negative_amount_applied = 0;
foreach($lines as $line){
	if($line->AMOUNT_APPLIED > 0){
		$data .= '<tr>
					<td class="text-center">'.$line->TRX_NUMBER.'</td>
					<td class="text-center">'.$line->CS_NUMBER.'</td>
					<td class="text-right">'.amount($line->INVOICE_AMOUNT).'</td>
					<td class="text-right">'.amount($line->AMOUNT_APPLIED).'</td>
					<td class="text-right">'.amount($line->WHT_AMOUNT).'</td>
					<td class="text-right">'.amount($line->BALANCE_PAYABLE).'</td>
				</tr>';
		$total_amount_applied += $line->AMOUNT_APPLIED;
		$total_balance_payable += $line->BALANCE_PAYABLE;
		$total_invoice_amount += $line->INVOICE_AMOUNT;
		$total_wht_amount += $line->WHT_AMOUNT;
	}
	else{
		$negative_amount_applied += $line->AMOUNT_APPLIED;
	}
	$total_amount_applied_2 += $line->AMOUNT_APPLIED;
}
$data .= '<tr class="primary">
			<td colspan="2" class="text-center"><strong>Total</strong></td>
			<td class="text-right"><strong>'.amount($total_invoice_amount).'</strong></td>
			<td class="text-right"><strong>'.amount($total_amount_applied).'</strong></td>
			<td class="text-right"><strong>'.amount($total_wht_amount).'</strong></td>
			<td class="text-right"><strong>'.amount($total_balance_payable).'</strong></td>
		</tr>';

$wht_amount = $negative_amount_applied * -1 > 0 ? $negative_amount_applied : 0;

?>
<section class="content">
	<div class="row">
		<div class="col-md-3">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Search : </h6>
				</div>
				<div class="box-body">
					<div class="input-group">
						<input type="text" class="form-control" name="search_receipt" placeholder="Search for Receipt, Invoice or CS Number">
						<span class="input-group-btn">
							<button id="btn-search" class="btn btn-danger" type="button">Go!</button>
						</span>
					</div><!-- /input-group -->
					<p class="help-block"></p>
				</div>
			</div>
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Receipt Details</h6>
				</div>
				<div class="box-body">
					<strong>Receipt Number</strong>
					<p class="text-muted"><?php echo $header->RECEIPT_NUMBER;?></p>
					
					<strong>Customer Name</strong>
					<p class="text-muted"><?php echo $header->PARTY_NAME;?></p>
					
					<strong>Receipt Amount</strong>
					<p class="text-muted"><?php echo amount($header->RECEIPT_AMOUNT);?></p>
					
					<strong>WHT Amount</strong>
					<p class="text-muted"><?php echo $negative_amount_applied * -1 > 0 ?  amount($negative_amount_applied * -1):'0.00'; ?></p>
					
					<strong>Applied Amount</strong>
					<p class="text-muted"><?php echo amount($total_amount_applied); ?></p>
					
					<strong>Unapplied Amount</strong>
					<p class="text-muted"><?php echo amount($header->RECEIPT_AMOUNT - $total_amount_applied_2); ?></p>
				</div>
				<?php 
				if($header->RECEIPT_AMOUNT - $total_amount_applied_2 < 1 AND $header->RECEIPT_AMOUNT - $total_amount_applied_2 > -1){
				?>
				<div class="box-footer text-right">
					<a target="_blank" class="btn btn-danger" href="<?php echo base_url('reports/collection_receipt_pdf/index/'.$header->CASH_RECEIPT_ID)?>">Print Receipt</a>
				</div>
				<?php 
				}
				?>
			</div>
		</div>
		<div class="col-md-9" style="min-height:400px;">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h6 class="box-title">Receipt Application Details</h6>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th class="text-center">Invoice Number</th>
								<th class="text-center">CS Number</th>
								<th class="text-right">Invoice Amount</th>
								<th class="text-right">Amount Applied</th>
								<th class="text-right">WHT Amount</th>
								<th class="text-right">Amount Due Remaining</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $data; ?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- test
			test
			test
			test -->
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('body').on('click','button#btn-search',function(){
			//~ alert('aw');
			var search_receipt = $('input[name=search_receipt]').val();
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>receivables/receipt/ajax_find_receipt_id',
				data: {
						search_receipt: search_receipt
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
	});
</script>

