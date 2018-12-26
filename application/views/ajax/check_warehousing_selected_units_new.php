<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>

<?php 		
if(!empty($result)){
?>
<p class="well well-sm lead"></i><small>Search Resultssss</small> </p>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<form id="myForm" method="POST" action="save_entry_new">
				<table class="table" style="width:1700px;max-width: none;">
					<thead>
						<tr>
							<th width="30px" class="text-center">#</th>
							<th width="70px" class="text-center">CS Number</th>
							<th width="120px" class="text-left">Account Name</th>
							<th width="180px" class="text-left">Sales Model</th>
							<th width="80px" class="text-center">Due Date</th>
							<th width="100px" class="text-right">Amount Due</th>
							<th width="100px" class="text-right">WP w/ VAT</th>
							<th width="200px" class="text-center">Check Number</th>
							<th width="200px" class="text-center">Check Bank</th>
							<th width="200px" class="text-center">Check Date</th>
							<th width="200px" class="text-center">Payment Amount</th>
							<th width="180px" class="text-left">Body Color</th>
							<th width="120px" class="text-left">Order Type</th>
							<th width="120px" class="text-left">SO - Line No</th>
							<th width="100px" class="text-left">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$cnt = 0;
						$ctr = 1;
						$total_amount_due = 0;
						$nearest_due_date = '';
						$order_type = '';
						foreach($result as $row){
						?>
							<tr>
								<td class="text-center"><?php echo $ctr; ?></td>
								<td class="text-center"><?php echo $row->CS_NUMBER; ?></td>
								<td class="text-left"><?php echo $row->ACCOUNT_NAME; ?></td>
								<td class="text-left"><?php echo $row->SALES_MODEL; ?></td>
								<td class="text-center"><?php echo short_Date($row->DUE_DATE); ?></td>
								<td class="text-right"><?php echo amount($row->NET_AMOUNT + $row->VAT_AMOUNT); ?></td>
								<td class="text-right"><?php echo amount($row->AMOUNT_DUE); ?></td>
								<td>
									<input type="hidden" class="form-control" name="data[<?php echo $cnt; ?>][cs_number]" value="<?php echo $row->CS_NUMBER; ?>"/>
									<input type="number" class="form-control" name="data[<?php echo $cnt; ?>][check_number]" value=""/>
								</td>
								<td>
									<input type="text" class="form-control" name="data[<?php echo $cnt; ?>][check_bank]" value=""/>
								</td>
								<td>
									<input type="text" class="datemask form-control" name="data[<?php echo $cnt; ?>][check_date]" value=""/>
								</td>
								<td>
									<input type="text" class="form-control numberOnly" name="data[<?php echo $cnt; ?>][check_amount]" value=""/>
								</td>
								<td class="text-left"><?php echo $row->BODY_COLOR; ?></td>
								<td class="text-left"><?php echo $row->ORDER_TYPE; ?></td>
								<td class="text-left"><?php echo $row->ORDER_NUMBER . '-' . $row->LINE_NUMBER; ?></td>
								<td class="text-center"><?php echo $row->STATUS; ?></td>
							</tr>
							<?php 
							$total_amount_due += $row->AMOUNT_DUE;
							if($ctr == 1){
								$nearest_due_date = $row->DUE_DATE;
								$order_type = strtok($row->ORDER_TYPE, ' ');
							}
							$ctr++;
							$cnt++;
						}
						?>
					
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<br />
<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<div class="col-lg-12 text-right">
				<button type="button" class="btn btn-danger" name="save">Save</button>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/moment/js/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/numberOnly/numberOnly.js'); ?>"></script>

<script>
	$(document).ready(function() {
		
		$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('button[name=save]').click(function(){
			$('#myForm').submit();
		})
	});
</script>
<?php 
}
else{
	echo '<div class="alert alert-warning" role="alert">Sorry!!! No Results Found...</div>';
}
?>
