<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/iCheck/flat/_all.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Select Units to Request for Invoice</h3>
					<div style="margin-top: 5px;" class="box-tools pull-right">
						<a href="http://localhost/treasury/reports/tagged_excel" title="" data-placement="top" data-toggle="tooltip" target="_blank" class="text-green" data-original-title="Excel"><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<br />
				<div class="row">
					<div style="padding-right: 27px;" class="col-sm-12 text-right">
						<button class="btn btn-danger" type="button" id="btn-next">Submit Selected</button>
					</div>
				</div>
				<div class="box-body">
					<table id="myTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Account Name</th>
								<th>CS Number</th>
								<th>Sales Model</th>
								<th>Body Color</th>
								<th>Order Type</th>
								<th>Tagged Date</th>
								<th>For Invoice Date</th>
								<th>Aging</th>
								<th class="text-right">Amount Due</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($result as $row){
							?>
							<tr>
								<td>
									<?php 
									if($row->FOR_INVOICE_DATE == NULL){
									?>
										<input type="checkbox" class="cs_checkbox" name="cs_numbers[]" data-cs_number="<?php echo $row->CS_NUMBER; ?>" value="<?php echo $row->AMOUNT_DUE; ?>">
									<?php 
									}
									?>
								</td>
								<td><?php echo $row->ACCOUNT_NAME;?></td>
								<td><?php echo $row->CS_NUMBER;?></td>
								<td><?php echo $row->SALES_MODEL;?></td>
								<td><?php echo $row->BODY_COLOR;?></td>
								<td><?php echo $row->ORDER_TYPE;?></td>
								<td><?php echo short_date($row->TAGGED_DATE);?></td>
								<td><?php echo short_date($row->FOR_INVOICE_DATE);?></td>
								<td><?php echo $row->AGING;?></td>
								<td class="text-right"><?php echo amount($row->AMOUNT_DUE);?></td>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Request for Invoice</h4>
			</div>
			<div class="modal-body">
				<p id="v_total" style="font-size: 110%">The selected units will now be requested for invoice.</p>
				<form id="myForm" action="<?php echo base_url('sales_order/vehicle/request_for_invoice'); ?>" method="POST">
					
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="myForm-submit" class="btn-danger btn" type="button">Continue</button>
			</div>
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function(){
		
		$('input[type=checkbox]').iCheck({
			checkboxClass: 'icheckbox_flat-green'
		});
		
		var mydataTable = $('.table').DataTable({
			"order": []
		});
		var form = $("#myForm");
		
		$('button#btn-next').on('click', function(){
			//~ $('#v_total').html('');
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
			
			//~ $('#total_amount_due span').html(count + ' ');
			
			if(count > 0){
				$('#myModal').modal('show')
			}
		});
		
		$('button#myForm-submit').on('click', function(){
			form.submit();
			//~ alert('aw');
		});
	});
</script>
