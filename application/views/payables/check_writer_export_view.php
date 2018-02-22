<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/sweetalert/sweetalert.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-9">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Account Payables</h3>
				</div>
				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered" id="dr-list" class="display" cellspacing="0" width="100%" style="font-size: 90%;">
						<thead>
							<tr>
								<th>#</th>
								<th>IFS Payee Code</th>
								<th>Oracle Payee Code</th>
								<th>Payee Name</th>
								<th>Voucher Number</th>
								<th>Check Number</th>
								<th>Check Amount</th>
								<th class="text-right">Paid Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$count = 1;
						$total_paid = 0;
						$total_check = 0;
						
						$bank = '';
						$from = '';
						$to ='';
						
						$error = 0;
						$error_msg = '';
						
						foreach($result as $row){
							$row = (object)$row;
							$total_paid += $row->PAID_AMOUNT;
							$total_check += $row->CHECK_AMOUNT;
							if($count == 1){
								$bank = $row->CHECK_BANK;
								$from = $row->CHECK_FROM;
								$to = $row->CHECK_TO;
							}
							//~ requested to be removed by ms nats
							//~ if($row->IFS_PAYEE_CODE == NULL){
								//~ $error++;
								//~ $error_msg .= '<tr><td>' . $row->PAYEE_NAME . '</td><td>No IFS Supplier Code.</td></tr>';
							//~ }
							if($row->CHECK_AMOUNT != $row->PAID_AMOUNT){
								$error++;
								$error_msg .= '<tr><td>' .$row->PAYEE_NAME . '</td><td>Paid Amount != Check Amount. </td></tr>';
							}
						?>
							<tr>
								
								<td><?php echo $count; ?></td>
								<td><?php echo $row->IFS_PAYEE_CODE; ?></td>
								<td><?php echo $row->PAYEE_CODE; ?></td>
								<td><?php echo $row->PAYEE_NAME; ?></td>
								<td><?php echo $row->REFERENCE_NO; ?></td>
								<td><?php echo $row->CHECK_NO; ?></td>
								<td class="text-right"><?php echo amount($row->CHECK_AMOUNT); ?></td>
								<td class="text-right"><?php echo amount($row->PAID_AMOUNT); ?></td>
							</tr>
						<?php 
						$count++;
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">AP Summary</h3>
				</div>
				<div class="box-body">

					<input type="hidden" name="from" value="<?php echo $from; ?>">
					<input type="hidden" name="to" value="<?php echo $to; ?>">
					<input type="hidden" name="bank" value="<?php echo $bank; ?>">

					<strong>ID</strong>
					<p id="batch-id"><?php echo $this->uri->segment(4); ?></p>
					<strong>Bank</strong>
					<p><?php echo $bank; ?></p>
					<strong>Check Date</strong>
					<p><?php echo short_date($from); ?> to <?php echo short_date($to); ?></p>
					<strong>Total Paid Amount</strong>
					<p><?php echo amount($total_paid); ?></p>
					<strong>Total Check Amount</strong>
					<p><?php echo amount($total_check); ?></p>
					<?php 
					if($error > 0){
					?>
					<strong class="text-danger"><a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal"><?php echo $error; ?> error(s) found.</a></strong>
					<?php 
					}
					else{
					?>
					<strong class="text-danger"><a href="#" id="btn-create" class="btn btn-danger btn-sm" >Create excel and encrypted text file.</a></strong>
					<?php 
					}
					?>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Supplier Name</th>
							<th>Error</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $error_msg;?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content modal-content2">

		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/blockUI/blockUI.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/sweetalert/sweetalert.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
$(document).ready(function() {
	$('#dr-list').DataTable({
		//~ 'bSort' : false
	});
	
	//~ $('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
	
	$('#btn-create').on('click', function(){
		
		var id = $('p#batch-id').text();
		var from = $('input[name=from]').val();
		var to = $('input[name=to]').val();
		var bank = $('input[name=bank]').val();
		
		$.ajax({
			type: 'POST',
			url: '../ajax_export',
			data:{
						id : id,
						from : from,
						to : to,
						bank : bank
			},
			beforeSend : function() {
				  $.blockUI({ 
						message:  '<h4>Please wait . . .</h4>',
						css: { 
							border: 'none', 
							padding: 0, 
							backgroundColor: '#000', 
							'-webkit-border-radius': '7px', 
							'-moz-border-radius': '7px', 
							opacity: .7,
							color: '#fff'
						}
					}); 
				}, 
			success: function(data){
				$.unblockUI();
				//~ swal("Success!", "Creation of files successfully completed.", "success")
				//~ swal({
				  //~ title: "Success!",
				  //~ text: "Creation of files successfully completed.",
				  //~ type: "success",
				  //~ confirmButtonClass: 'btn-success',
				  //~ confirmButtonText: 'Close'
				//~ });
				$('#myModal2').modal('show');
				$('.modal-content2').html(data);
			}
		});
	});
	
} );
</script>
