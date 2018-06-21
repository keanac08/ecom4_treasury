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
								<th>Account Name</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Tagged Date</th>
								<th>Aging</th>
								<th>Amount Due</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($results as $row){
						?>
							<tr>
								<td><input type="checkbox" class="cs_checkbox" name="cs_numbers[]" data-cs_number="<?php echo $row->CS_NUMBER; ?>" value="<?php echo $row->AMOUNT_DUE; ?>"></td>
								<td><?php echo $row->ACCOUNT_NAME; ?></td>
								<td><?php echo $row->CS_NUMBER; ?></td>
								<td><?php echo $row->SALES_MODEL; ?></td>
								<td><?php echo $row->BODY_COLOR; ?></td>
								<td><?php echo short_date($row->TAGGED_DATE); ?></td>
								<td><?php echo $row->AGING; ?></td>
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
				<form id="myForm" action="<?php echo base_url('reports/rcbc_payment_reference_v_pdf'); ?>" method="POST" target="_blank">
					
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
	
	var mydataTable = $('.table').DataTable();
	var form = $("#myForm");
	
	$('button#myForm-submit').on('click', function(){
		form.submit();
		//~ alert('aw');
	});
	
	$('button#btn-next').on('click', function(){
		$('#total_amount_due span').html('');
		var count = 0;
		var total = 0;
		mydataTable.$('input[type=checkbox]').each(function(){
			// If checkbox is checked
			if(this.checked){ 
				//~ total += (this.value);
				form.append(
					$('<input>')
						.attr('type', 'hidden')
						.attr('name', this.name)
						.val($(this).data('cs_number'))
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
