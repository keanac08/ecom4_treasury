<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>

<?php 		
if(!empty($result)){
?>
<p class="well well-sm lead"></i><small>Search Results</small> </p>
<div class="row">
	<div class="col-md-12">
		
		<table class="table">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-left">Account Name</th>
					<th class="text-left">Sales Model</th>
					<th class="text-center">CS Number</th>
					<th class="text-center">Due Date</th>
					<th class="text-center">Status</th>
					<th class="text-right">Amount Due</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$ctr = 1;
				$total_amount_due = 0;
				$nearest_due_date = '';
				$order_type = '';
				foreach($result as $row){
				?>
					<tr>
						<td class="text-center"><?php echo $ctr; ?></td>
						
						<td class="text-left"><?php echo $row->ACCOUNT_NAME; ?></td>
						<td class="text-left"><?php echo $row->SALES_MODEL; ?></td>
						<td class="text-center"><?php echo $row->CS_NUMBER; ?></td>
						<td class="text-center"><?php echo short_Date($row->DUE_DATE); ?></td>
						<td class="text-center"><?php echo $row->STATUS; ?></td>
						<td class="text-right"><?php echo amount($row->AMOUNT_DUE); ?></td>
					</tr>
					<?php 
					$total_amount_due += $row->AMOUNT_DUE;
					if($ctr == 1){
						$nearest_due_date = $row->DUE_DATE;
						$order_type = strtok($row->ORDER_TYPE, ' ');
					}
					$ctr++;
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
	<p class="lead well well-sm"><small>PDC Details <span style="font-size: 80%;" class="text-danger;"><i>(Please complete the fields below)</i></span></small> </p>
				
	<div class="col-lg-6">
		<form class="form-horizontal">
			<input type="hidden" name="cs_numbers" value="<?php echo $cs_numbers;?>">
			<fieldset>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Check Number</label>
					<div class="col-lg-7">
						<input required autofocus="autofocus" type="text" class="form-control" name="check_number" value=""/>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Check Bank</label>
					<div class="col-lg-7">
						<input required  type="text" class="form-control" name="check_bank" value=""/>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Check Date</label>
					<div class="col-lg-7">
						<input required type="text" class="datemask form-control" name="check_date" value=""/>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Check Amount</label>
					<div class="col-lg-7">
						<input required type="text" class="form-control numberOnly" name="check_amount" value=""/>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-7 col-lg-offset-5">
						<button <?php echo $ctr == 1 ? 'disabled':''; ?> type="button" class="btn btn-danger" name="save">Save</button>
						<a href="<?php echo base_url();?>receivables/check_warehousing/entry" class="btn btn-default">Clear</a>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-lg-6">
		<form class="form-horizontal">
			<fieldset>
				<p class="lead"><small>&nbsp;</small> </p>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Order Type</label>
					<div class="col-lg-7">
						<input readonly type="text" class="form-control text-center" id="order_type" value="<?php echo $order_type; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Nearest Due Date</label>
					<div class="col-lg-7">
						<input readonly type="text" class="form-control text-center" id="nearest_due_date"  value="<?php echo short_date($nearest_due_date); ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail" class="col-lg-5 control-label">Total Amount Due</label>
					<div class="col-lg-7">
						<input readonly type="text" class="form-control text-center" id="total_amount_due" value="<?php echo amount($total_amount_due); ?>"/>
					</div>
				</div>
			</fieldset>
		</form>
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
			
			var check_id = $('input[name=check_id]').val();
			var cs_numbers = $('input[name=cs_numbers]').val();
			var check_number = $('input[name=check_number]').val();
			var check_bank = $('input[name=check_bank]').val();
			var check_amount = $('input[name=check_amount]').val();
			var check_date = $('input[name=check_date]').val();
			
			if(check_number && check_bank && check_amount && check_date){
				
				total_amount_due = parseFloat($('#total_amount_due').val().replace(/,/g, ''));
				check_amount = parseFloat(check_amount.replace(/,/g, ''));
				
				if(check_amount + 1 >=  total_amount_due){
					
					if($('#order_type').val() == 'Vehicle' || $('#order_type').val() == ''){
						
						nearest_due_date = moment($('#nearest_due_date').val(),"MM/DD/YYYY");
					}
					else{
						nearest_due_date = moment($('#nearest_due_date').val(),"MM/DD/YYYY").add(5, 'days');
					}
					check_date_m = moment(check_date,"MM/DD/YYYY");
					
					if(nearest_due_date >= check_date_m || $('#nearest_due_date').val() == '-'){
						$.ajax({
							type:'POST',
							data:{
								check_id : check_id,
								cs_numbers : cs_numbers,
								check_number : check_number,
								check_bank : check_bank,
								check_amount : check_amount,
								check_date : check_date
							},
							url: '<?php echo base_url();?>receivables/check_warehousing/save_entry',
							success:function(data){
								//~ $('#result').html(data);
								//~ alert(data);
								//~ window.location.replace("");
								 swal({
									text: "PDC details has been successfully saved!", 
									type: "success",
									confirmButtonColor: '#DD6B55'
								}).then(function() {
									window.open('<?php echo base_url();?>receivables/check_warehousing/pdc_details_pdf/'+data, '_blank');
									//~ location.reload();
									//~ location.href = '<?php echo base_url();?>receivables/check_warehousing/entry';
								});
							}
						});
					}
					else{
						swal({
							text: "Check date cannot be later than nearest due date.", 
							type: "error",
							confirmButtonColor: '#DD6B55'
						});
					}
					
				}
				else{
					//~ alert('Check amount is less than total amount due.');
					swal({
						text: "Insufficient check amount.", 
						type: "error",
						confirmButtonColor: '#DD6B55'
					});
				}
			}
			else{
				swal({
					text: "Please fill all fields.", 
					type: "error",
					confirmButtonColor: '#DD6B55'
				});
				//~ alert(parseFloat($('#total_amount_due').val().replace(/,/g, '')) + 1);
			}
		})
	});
</script>
<?php 
}
else{
	echo '<div class="alert alert-warning" role="alert">Sorry!!! No Results Found...</div>';
}
?>
