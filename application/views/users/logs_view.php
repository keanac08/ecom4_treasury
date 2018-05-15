<?php 
	$this->load->helper('string_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
<!--
				<div class="box-header with-border">
					<h3 class="box-title">&nbsp;</h3>
				</div>
-->
				<div class="row">
					<div class="col-sm-3" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('01-M-y'):date('d-M-y', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('d-M-y'):date('d-M-y', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class=" control-label" for="unput1">Session Date</label>
								<input class="form-control" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<table class="table table-striped table-bordered table-hover" id="myTable">
						<thead>
							<tr>
								<th>Session ID</th>
								<th>Session Number</th>
								<th>Session Start</th>
								<th>Session End</th>
								<th>User Fullname</th>
								<th>User Type</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($result as $row){
						?>
							<tr>
								<td><?php echo $row->LOG_ID; ?></td>
								<td><?php echo $row->SESSION_ID; ?></td>
								<td><?php echo long_date($row->LOGIN_DATE); ?></td>
								<td><?php echo long_date($row->LOGOUT_DATE); ?></td>
								<td><?php echo CAMELCASE($row->FULL_NAME); ?></td>
								<td><?php echo $row->USER_TYPE_NAME; ?></td>
							</tr>
						<?php 
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content modal-content-1">

		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-danger fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Deposit Date Entry</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="hidden" name="check_id">
					<input type="text" class="datemask form-control"  placeholder="Deposit Date" name="deposit_date">
				</div>
				<div class="text-right">
					<button type="button" name="save" class="btn btn-danger">Save</button>
				</div>
			</div>
			
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/moment/js/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.js');?>"></script>
<script>
	$(document).ready(function(){
		
		$('input[name="date_created"]').daterangepicker();
		$('input[name="date_created"]').on('apply.daterangepicker', function(ev, picker) {
			$('input[name="from_date"]').val(picker.startDate.format('YYYY-MM-DD'));
			$('input[name="to_date"]').val(picker.endDate.format('YYYY-MM-DD'));
			form_filters.submit();
		});
		
		$('#myTable').DataTable({
			  "order": []
		});
		
	});
</script>
