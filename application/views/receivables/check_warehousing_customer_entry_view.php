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
					<form id="nextForm" action="../customer_entry_2" method="post">
						
					<form>
				</div>
				<div class="row">
					<div class="col-sm-12 text-right" style="padding-right: 27px;">
						<button id="btn-next" type="button" class="btn btn-success">
							Next
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
								$include = 0;
								if($type == 'vehicle'){
									if($row->ORDER_TYPE == 'Vehicle ' AND $row->PAYMENT_TERMS == '0 Day'){
										$include = 1;
									}
								}
								else if($type == 'vehicle_terms'){
									if($row->ORDER_TYPE == 'Vehicle ' AND $row->PAYMENT_TERMS != '0 Day'){
										$include = 1;
									}
								}
								else if($type == 'fleet'){
									if($row->ORDER_TYPE == 'Fleet '){
										$include = 1;
									}
								}
								
								if($row->CS_NUMBER != NULL){
									if($include == 1){
										?>
										<tr>
											<td><input type="checkbox" name="cs_numbers[]" class="cs_checkbox" value="<?php echo $row->CS_NUMBER; ?>"></td>
											<td><?php echo $row->CS_NUMBER; ?></td>
											<td><?php echo $row->SALES_MODEL; ?></td>
											<td><?php echo $row->BODY_COLOR; ?></td>
											<td><?php echo $row->ORDER_TYPE; ?></td>
											<td><?php echo $row->PAYMENT_TERMS; ?></td>
											<td><?php echo short_date($row->RESERVED_DATE); ?></td>
											<td><?php echo $row->AGING; ?></td>
											<td class="text-right"><?php echo amount($row->AMOUNT_DUE); ?></td>
										</tr>
										<?php 
									}
								}
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function() {

		$('input[type=checkbox]').iCheck({
			checkboxClass: 'icheckbox_flat-green'
		});

		var mydataTable = $('table.table').DataTable();
		var form = $("#nextForm");
		
		$('button#btn-next').on('click', function(){
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
			}
		});
	});
</script>

