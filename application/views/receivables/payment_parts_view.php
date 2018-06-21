<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/iCheck/flat/_all.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Create new payment reference</h3>
				</div>
				<br />
				<div class="row">
					<div style="padding-right: 27px;" class="col-sm-12 text-right">
						<button class="btn btn-danger" type="button" id="btn-next">Submit Selected</button>
					</div>
				</div>
				<div class="box-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>Account Name</th>
								<th>Invoice Number</th>
								<th>Payment Terms</th>
								<th>Due Date</th>
								<th>Invoice Amount</th>
								<th>Balance</th>
								<th>Amount Due</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							$due_date = '';
							foreach($results as $row){
							?>
								<?php 
								if($due_date != $row->DUE_DATE){
									?>
									<tr>
										<td><input type="checkbox" class="duedate_checkbox" name="due_date"  value="<?php echo $row->DUE_DATE; ?>"></td>
										<td colspan="2">Due On <?php echo short_date($row->DUE_DATE); ?></td>
										<td style="display: none;">&nbsp;</td>
										<td colspan="6">&nbsp;</td>
										<td style="display: none;">&nbsp;</td>
										<td style="display: none;">&nbsp;</td>
										<td style="display: none;">&nbsp;</td>
										<td style="display: none;">&nbsp;</td>
										<td style="display: none;">&nbsp;</td>
									</tr>
									<?php 
									$due_date = $row->DUE_DATE;
								}
								?>
								<tr>
									<td>&nbsp;</td>
									<td><input type="checkbox" class="cs_checkbox" name="invoice_numbers[]" data-due_date="<?php echo $row->DUE_DATE; ?>" data-invoice_number="<?php echo $row->INVOICE_NUMBER; ?>" value="<?php echo $row->AMOUNT_DUE; ?>"></td>
									<td><?php echo $row->ACCOUNT_NAME; ?></td>
									<td><?php echo $row->INVOICE_NUMBER; ?></td>
									<td><?php echo $row->PAYMENT_TERM; ?></td>
									<td><?php echo short_date($row->DUE_DATE); ?></td>
									<td align="right"><?php echo amount($row->INVOICE_AMOUNT); ?></td>
									<td align="right"><?php echo amount($row->BALANCE); ?></td>
									<td align="right"><?php echo amount($row->AMOUNT_DUE); ?></td>
								</tr>
							<?php 
							}
						?>
						</tbody>
					</table>
				</div>
				<div class="box-footer text-right">
					&nbsp;
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Payments</h4>
			</div>
			<div class="modal-body">
				<p id="total_amount_due" style="font-size: 110%">Total Amount Due : <span class="pull-right"></span></p>
				<form id="myForm" action="<?php echo base_url('reports/rcbc_payment_reference_p_pdf'); ?>" method="POST" target="_blank">
					
				</form>
			</div>
			<div class="modal-footer">
				<button id="myForm-submit" class="btn-danger btn" type="button">Print Payment Reference</button>
			</div>
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
$(document).ready(function() {
	
	$('input[type=checkbox]').iCheck({
		checkboxClass: 'icheckbox_flat-green'
	});
	
	var mydataTable = $('table.table').DataTable({
		"order": [],
		"drawCallback": function( settings ) {
			$('input[type=checkbox].duedate_checkbox').on('ifChecked', function(){
				var due_date = this.value;
				
				mydataTable.$('input[type=checkbox].cs_checkbox').each(function(){
					if(due_date == $(this).data('due_date')){
						$(this).iCheck('check');
					}
				});
			});
			$('input[type=checkbox].duedate_checkbox').on('ifUnchecked', function(){
				var due_date = this.value;
				
				mydataTable.$('input[type=checkbox].cs_checkbox').each(function(){
					if(due_date == $(this).data('due_date')){
						$(this).iCheck('uncheck');
					}
				});
			});
		}
    });
	var form = $("#myForm");
	
	$('button#myForm-submit').on('click', function(){
		form.submit();
		//~ alert('aw');
	});
	
	$('button#btn-next').on('click', function(){
		$('#total_amount_due span').html('');
		var count = 0;
		var total = 0;
		mydataTable.$('input[type=checkbox].cs_checkbox').each(function(){
			// If checkbox is checked
			if(this.checked){ 
				//~ total += (this.value);
				form.append(
					$('<input>')
						.attr('type', 'hidden')
						.attr('name', this.name)
						.val($(this).data('invoice_number'))
				);
				total += parseFloat(this.value.replace(/,/g, ''));
				count++;
			} 
		});
		
		$('#total_amount_due span').html(total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
		
		if(count > 0){
			$('#myModal').modal('show')
		}
	});
	
});
</script>
