<?php 

?>
<section class="content">
	<div class="row">
		<div class="col-md-4">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Report Parameters</h3>
				</div>
				<div class="box-body">
					<form target="_blank" method="POST" accept-charset="utf-8" action="vehicle_by_due_date_excel">
						<div class="form-group">
							<label>Due Date From</label>
							<input type="text" class="form-control" id="date_from" name="from_date" required>
						</div>
						<div class="form-group">
							<label>Due Date To</label>
							<input type="text" class="form-control" id="date_to" name="to_date" required>
						</div>
						<button type="submit" class="btn btn-danger pull-right" >Generate Report</button>
					</form>	
				</div>
			</div>
		</div>
	</div>
</section>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$("#date_from").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
	});
</script>

