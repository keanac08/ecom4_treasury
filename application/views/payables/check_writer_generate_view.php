<link href="<?php echo base_url('resources/plugins/iCheck/flat/red.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-6">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Export IPC Payables (Supplier)</h3>
				</div>
				<form role="form" class="form-horizontal">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">Check Bank</label>
							<div class="col-sm-9">
								<div class="radio">
									<label>
										<input type="radio" name="check_bank" value="BPI-CW" checked>
										BPI Check Writer
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="check_bank" value="RCBC-CW">
										RCBC Check Writer
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="check_bank" value="UBP-CW">
										UBP Check Writer
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="check_bank" value="UBP-ATM">
										UBP ATM
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Check Date</label>
							<div class="col-sm-6">
								<div class="input-group" style="margin-bottom: 3px;">
								   <span class="input-group-addon">From</span>
								  <input type="text" class="form-control datepicker1" name="from_date">
								 
								</div>
								<div class="input-group">
								   <span class="input-group-addon" style="width: 56px;">To</span>
								   <input type="text" class="form-control datepicker2" name="to_date">
								 
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer with-border text-right">
						<button id="btn-submit" type="button" class="btn btn-sm btn-danger">Export Data</button>
					</div>
				</form>
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
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datepicker/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/blockUI/blockUI.js');?>"></script>
<script>
	$(document).ready(function() {
		
	
			
		$('#btn-submit').on('click', function(){
			//~ alert('aw');
			
			 var check_bank = $('input[name=check_bank]:checked').val();
			 var from_date = $('input[name=from_date]').val();
			 var to_date = $('input[name=to_date]').val();
			 
			
			if(!from_date && !to_date){
				alert('Invalid date!')
			}
			else{
				$.ajax({
					type: 'POST',
					data: {
						check_bank : check_bank, 
						from_date : from_date,
						to_date : to_date
					},
					beforeSend : function() {
						  $.blockUI({ 
								message:  '<h4>Processing data, please wait . . .</h4>',
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
					url: '<?php echo base_url();?>payables/check_writer/ajax_export_payables',
					success: function(data){
						if(data != 'false'){
							document.location.href = 'export/'+data;
							//~ $.unblockUI;
							//~ alert('Success!');
						}
						else{
							
							alert('No data found!');
							 location.reload();
						}					
					}
				});
			}
		});
		
		$('input[type=radio], input[type=checkbox]').iCheck({
			checkboxClass: 'icheckbox_flat-red',
			radioClass: 'iradio_flat-red'
		});
		
		$('.datepicker1').datepicker().on('changeDate', function(e) {
				$('.datepicker2').datepicker('update',  $(this).datepicker('getFormattedDate'));
			});
		$('.datepicker2').datepicker();
		
	});
</script>

