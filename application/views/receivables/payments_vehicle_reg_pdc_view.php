<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/iCheck/flat/_all.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">Request for Invoice</h3>
				</div>
				<div class="row">
					<div class="col-sm-10" style="padding-left: 27px;">
						<?php 
						if($this->uri->segment(4) == 'vehicle'){
							$vehicle_checked = 'checked';
							$fleet_checked = '';
						}
						else{
							$vehicle_checked = '';
							$fleet_checked = 'checked';
						}
						?>
						<label class="radio_inline"><input <?php echo $vehicle_checked; ?> type="radio" name="sales_type" value="vehicle" class="radio radio_vehicle"> &nbsp; Vehicle</label>&nbsp;&nbsp;&nbsp;
						<label class="radio_inline"><input <?php echo $fleet_checked; ?> type="radio" name="sales_type" value="fleet"   class="radio radio_fleet"> &nbsp; Fleet<label>
					</div>
					<div class="col-sm-2" style="padding-right: 27px;">
						<form id="nextForm" action="<?php echo base_url('receivables/payment/check_details'); ?>" method="post">
					
						</form>
						<button id="btn-submit" type="button" class="btn btn-success">
							Submit Selected
						</button>
					</div>
				</div>
				<div class="box-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Order Type</th>
								<th>Payment Terms</th>
								<th>Reserved Date</th>
								<th>Aging</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($results as $row){
							?>
							<tr>
								<td><input type="checkbox" name="cs_numbers[]" class="cs_checkbox" value="<?php echo $row->CS_NUMBER; ?>"></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td><?php echo $row->BODY_COLOR; ?></td>
								<td><?php echo $row->ORDER_TYPE; ?></td>
								<td><?php echo $row->PAYMENT_TERMS; ?></td>
								<td><?php echo short_date($row->TAGGED_DATE); ?></td>
								<td><?php echo $row->AGING; ?></td>
								<td class="text-right"><?php echo amount($row->AMOUNT_DUE); ?></td>
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

<!-- Modal -->
<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Choose a payment method</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button disabled id="btn-submit" type="button" class="btn btn-danger">Submit</button>
			</div>
			
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function() {

		$('input[type=checkbox], .radio').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});
		
		$('.radio.radio_vehicle').on('ifChecked', function(event){
			window.location.href = "<?php echo base_url(); ?>receivables/payment/regular_pdc/vehicle";
		});

		$('.radio.radio_fleet').on('ifChecked', function(event){
			window.location.href = "<?php echo base_url(); ?>receivables/payment/regular_pdc/fleet";
		});

		var mydataTable = $('table.table').DataTable();
		var form = $("#nextForm");
			
		$('button#btn-submit').on('click', function(){
			//~ alert('aw');
			form.html('');
			var count = 0;
			mydataTable.$('input[type=checkbox]').each(function(){
				// If checkbox is checked
				if(this.checked){ 
					// Create a hidden element 
					form.append(
						$('<input>')
							.attr('type', 'hidden')
							.attr('name', this.name)
							.val(this.value)
					);
					count++;
				} 
			});
			if(count > 0){
				form.submit();
				//~ $('#myModal').modal('show');
			}
		});
	});
</script>

