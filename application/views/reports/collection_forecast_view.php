<form target="_blank" method="POST" accept-charset="utf-8" action="collection_forecast_excel">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Forecast Collection</h4>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label>Due Date From</label>
			<input type="text" class="form-control" id="date_from" name="from_date" required>
		</div>
		<div class="form-group">
			<label>Due Date To</label>
			<input type="text" class="form-control" id="date_to" name="to_date" required>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-danger" >Generate Report</button>
	</div>
</form>	
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$("#date_from").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
		$("#date_to").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
	});
</script>
