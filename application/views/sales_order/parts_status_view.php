<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Parts DBS</h3>
				</div>
				<div class="row">
					<div class="col-sm-3" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('01-M-y'):date('d-M-y', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('d-M-y'):date('d-M-y', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class=" control-label" for="unput1">SO Date</label>
								<input class="form-control" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<table id="myTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Customer Name</th>
								<th>Prepared By</th>
								<th>PO Number</th>
								<th>Sales Order Number</th>
								<th>Sales Order Date</th>
								<th>Picklist Number</th>
								<th>DR Reference</th>
								<th>DR Number</th>
								<th>Invoice Number</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($result as $row){
							?>
							<tr>
								<td><?php echo $row->CUSTOMER_NAME;?></td>
								<td><?php echo $row->PREPARED_BY;?></td>
								<td><?php echo $row->CUST_PO_NUMBER;?></td>
								<td><?php echo $row->ORDER_NUMBER;?></td>
								<td><?php echo short_Date($row->ORDERED_DATE);?></td>
								<td><?php echo $row->PICKLIST_NUMBER;?></td>
								<td><?php echo $row->DR_REFERENCE;?></td>
								<td><?php echo $row->DR_NUMBER;?></td>
								<td><?php echo $row->INVOICE_NUMBER;?></td>
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
		
		
		$('#myTable').DataTable();
		

	});
</script>
