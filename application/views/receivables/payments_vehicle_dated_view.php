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
					<div class="col-sm-12 text-right" style="padding-right: 27px;">
						<button id="btn-submit-selected" type="button" class="btn btn-success">
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
				<div class="radio">
					<label><input type="radio" name="optradio" value="1"> RCBC Bills Payment (Over-the-Counter)</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio" value="2"> Security Bank Bills Payment <br />(Over-the-Counter)</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio" value="3"> Online Fund Transfer</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio" value="4"> Check</label>
				</div>
				<form id="nextForm" action="../customer_entry_2" method="post" target="_blank">
					
				<form>
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

		$('input[type=checkbox]').iCheck({
			checkboxClass: 'icheckbox_flat-green'
		});

		var mydataTable = $('table.table').DataTable();
		var form = $("#nextForm");
		
		$('input[name=optradio]').on('click', function(){
			var payment_method = $(this).val();
			
			$('button#btn-submit').prop('disabled',false);
			if(payment_method != 4 ){
				
				$('button#btn-submit').html('Print Payment Reference');
				
				var bank;
				if(payment_method == 1){
					bank = 'RCBC';
				}
				else if(payment_method == 2){
					bank = 'Security Bank';
				}
				else if(payment_method == 3){
					bank = 'Fund Transfer';
				}
				
				form.append(
				$('<input>')
						.attr('type', 'hidden')
						.attr('name', 'bank')
						.val(bank)
				);
			
				form.attr("action", "<?php echo base_url(); ?>reports/rcbc_payment_reference_v_pdf");
				
			}
			else{
				
				$('button#btn-submit').html('Fill Out Check Details Form');
				
				form.attr("action", "<?php echo base_url(); ?>receivables/payment/check_details");
			}
		});
		
		$('button#btn-submit').on('click', function(){
			
			form.submit();
		});
			
		$('button#btn-submit-selected').on('click', function(){
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
				//~ form.submit();
				$('#myModal').modal('show');
			}
		});
	});
</script>

