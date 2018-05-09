<link href="<?php echo base_url('resources/plugins/select2/dist/css/select2.min.css') ?>" rel="stylesheet" >
<form target="_blank" method="POST" accept-charset="utf-8" action="invoices_excel">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Invoices</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label>Invoice Date From</label>
			<input type="text" class="form-control" id="date_from" name="from_date" required>
		</div>
		<div class="form-group">
			<label>Invoice Date To</label>
			<input type="text" class="form-control" id="date_to" name="to_date" required>
		</div>
		<div class="form-group">
			<label>Sales Type</label>
			<select class="form-control select2" name="sales_type">
				<option value="all" selected>All</option>
				<option value="vehicle">Vehicle</option>
				<option value="fleet">Fleet</option>
				<option value="parts">Parts</option>
				<option value="others">Others</option>
				<option value="powertrain">Powertrain</option>
				<option value="employee">Employee</option>
			</select>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-danger" >Generate Report</button>
	</div>
</form>	
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/select2/dist/js/select2.full.min.js');?>"></script>
<script>
	$(document).ready(function() {
		$("select.select2").select2({
			width: '100%'
		});
		$("#date_from").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
	});
</script>
