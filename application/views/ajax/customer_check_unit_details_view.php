<?php 
$this->load->helper('null_helper');
$this->load->helper('number_helper');
?>
<div class="modal-header">
	<span class="pull-right"><?php echo $check_bank . ' ' .$check_number; ?></span>
	<h4 class="modal-title">Check Details</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Account Name</th>
						<th>CS Number</th>
						<th>Sales Model</th>
						<th>Invoice Number</th>
						<th class="text-right">Amount Due</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total_amount_due = 0;
					foreach($result as $row){
					?>
						<tr>
							<td><?php echo $row->ACCOUNT_NAME; ?></td>
							<td><?php echo $row->CS_NUMBER; ?></td>
							<td><?php echo $row->SALES_MODEL; ?></td>
							<td><?php echo $row->TRX_NUMBER; ?></td>
							<td class="text-right"><?php echo amount($row->AMOUNT_DUE); ?></td>
						</tr>
					<?php 
					$total_amount_due += $row->AMOUNT_DUE;
					}
					?>
						<tr>
							<td class="text-right" colspan="4"><strong>Total Amount Due : </strong></td>
							<td class="text-right"><strong><?php echo amount($total_amount_due); ?></strong></td>
						</tr>
				</tbody>
			</table>
		</div>
	</div> 
	<div class="row">
		<div class="col-sm-12">
			
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

