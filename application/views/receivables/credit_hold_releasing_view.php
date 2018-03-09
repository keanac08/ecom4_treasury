<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/iCheck/flat/_all.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-body">
					<form action="" method="post">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Batch ID : </label>
								<div class="input-group">
									<input type="number" class="form-control" name="batch_id" id="batch_id" value="" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-flat btn-danger">Search</button>
									</span>
								</div><!-- /input-group -->
							</div>
							<div class="form-group">
								<label class="control-label">Check ID : </label>
								<div class="input-group">
									<input type="number" class="form-control" name="check_id" id="check_id" value="" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-flat btn-danger">Search</button>
									</span>
								</div><!-- /input-group -->
							</div>
						</div>
					</form>
				</div>
			</div>
			<?php 
			if(isset($batch_id)){
			?>
			<div class="box box-danger" id="batch-data">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo ($batch_id != NULL)? 'Batch ID : ' .$batch_id : 'Check ID : ' .$check_id; ?></h3>
					<button class="btn btn-success pull-right" disabled="true" id="release_selected">Release Selected</button>
				</div>
				<div class="box-body">
					<table class="table dataTable" id="pdc_table">
						<thead>
							<tr>
								<th>#</th>
								<th><input type="checkbox" class="check_all" ></th>
								<th>Account Name</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Fleet Name</th>
								<th>Due Date</th>
								<th>Status</th>
								<th class="text-right">Amount Due</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$ctr = 0;
							$total_amount_due = 0;
							
							$check_id = '';
							$check_number = '';
							$check_bank = '';
							$check_date = '';
							$check_amount = 0;
							
							foreach($result as $row) {  
								if($check_id != '' AND $check_id != $row->CHECK_ID){
									?>
									<tr>
										<td colspan="8" class="text-right">
											<span class="label label-success"><?php echo $check_number; ?></span> 
											<span class="label label-success"><?php echo $check_bank; ?></span> 
											<span class="label label-success"><?php echo short_date($check_date); ?></span> 
											<span class="label label-success"><?php echo amount($check_amount); ?></span>
										</td>
										<td colspan="1" class="text-right"><strong><?php echo amount($total_amount_due); ?></strong></td>
									</tr>
									<tr>
										<td colspan="9">&nbsp;</td>
									</tr>
									<?php
									$total_amount_due = 0;
								}
								$check_id = $row->CHECK_ID; 
								$check_number = $row->CHECK_NUMBER; 
								$check_bank = $row->CHECK_BANK; 
								$check_date =$row->CHECK_DATE; 
								$check_amount = $row->CHECK_AMOUNT; 
								
								$amount_due = $row->AMOUNT_DUE != NULL ?  $row->AMOUNT_DUE : $row->INVOICE_AMOUNT_DUE;
								$ctr++;
								
								?>
								<tr>
									<td><?php echo $ctr; ?></td>
									<td><input type="checkbox" class="check" data-check_id="<?php echo $row->CHECK_ID; ?>" data-cs_number='<?php echo $row->CS_NUMBER; ?>'  value="<?php echo $row->SO_LINE_ID; ?>" <?php echo ($row->RELEASED_FLAG == 'N')? '' : 'checked disabled' ?> ></td>
									<td><?php echo $row->ACCOUNT_NAME; ?></td>
									<td><?php echo $row->CS_NUMBER; ?></td>
									<td><?php echo $row->SALES_MODEL; ?></td>
									<td><?php echo $row->FLEET_NAME; ?></td>
									<td><?php echo $row->DUE_DATE; ?></td>
									<td><?php echo $row->STATUS; ?></td>
									<td class="text-right"><?php echo amount($amount_due); ?></td>
								</tr>
								<?php 
								$total_amount_due += $amount_due;
							}
							if($ctr > 0){
							?>
							<tr>
								<td colspan="8" class="text-right">
									<span class="label label-success"><?php echo $check_number; ?></span> 
									<span class="label label-success"><?php echo $check_bank; ?></span> 
									<span class="label label-success"><?php echo short_date($check_date); ?></span> 
									<span class="label label-success"><?php echo amount($check_amount); ?></span>
								</td>
								<td colspan="1" class="text-right"><strong><?php echo amount($total_amount_due); ?></strong></td>
							</tr>
							<?php 
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php 
			}
			?>
		</div>
	</div>
</section>

<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/sweetalert2/sweetalert.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$('input[type=checkbox]').iCheck({
			checkboxClass: 'icheckbox_flat-green'
		});
		
		var checkAll = $('input.check_all');
		var checkboxes = $('input.check');
		
		if(checkboxes.filter(':checked').length == checkboxes.length) {
			checkAll.prop('disabled', 'disabled');
		} 

		checkAll.on('ifChecked ifUnchecked', function(event) {        
			if (event.type == 'ifChecked') {
				checkboxes.iCheck('check');
			} else {
				checkboxes.iCheck('uncheck');
			}
			$('#release_selected').removeAttr('disabled');
		});

		checkboxes.on('ifChanged', function(event){
			if(checkboxes.filter(':checked').length == checkboxes.length) {
				checkAll.prop('checked', 'checked');
			} else {
				checkAll.removeProp('checked');
			}
			checkAll.iCheck('update');
			$('#release_selected').removeAttr('disabled');
		});
		
		$('#release_selected').click(function(){
			
			var line_ids = [];
			var cs_numbers = [];
			var check_ids = [];
			$('input.check:checkbox:checked').each(function () {
				line_ids.push($(this).val());
				cs_numbers.push($(this).data('cs_number'));
				check_ids.push($(this).data('check_id'));
			});
			
			$.ajax({
				url: "<?php echo base_url();?>receivables/check_warehousing/credit_hold_releasing_ajax",
				method: 'POST',
				data: {
						line_ids: line_ids,
						cs_numbers: cs_numbers,
						check_ids: check_ids
					},
				success: function(){
					swal("Credit hold has been successfully released", {
						 icon : "success",
						  buttons: {
							cancel: "Release another",
							go_to : "Go to Approved PDC page"
							//~ catch: {
							  //~ text: "Throw Pokéball!",
							  //~ value: "catch",
							//~ },
							//~ defeat: true,
							
						  },
						})
						.then((value) => {
						  switch (value) {
						 
							
							case "go_to":
							  location.href = '<?php echo base_url();?>receivables/check_warehousing/pdc';
							  break;
						 
							default:
							  location.href = '<?php echo base_url();?>receivables/check_warehousing/credit_hold_releasing';
						  }
						});
					//~ $("#success_modal").modal('show');
					//setTimeout(window.location.href = "<?php echo base_url();?>pdc/unit_releasing",10023230);
				},
			});
		});
	});
</script>

