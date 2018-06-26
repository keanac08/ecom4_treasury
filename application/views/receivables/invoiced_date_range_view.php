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
					<h3 class="box-title">By Date Range</h3>
					<div style="margin-top: 5px;" class="box-tools pull-right">
						<a href="http://localhost/treasury/reports/invoices_excel/index/<?php echo ($from_date == '')? date('m01y'):date('mdy', strtotime($from_date)); ?>/<?php echo ($to_date == '')? date('m01y'):date('mdy', strtotime($to_date)); ?>/<?php echo $customer_id; ?>" title="" data-placement="top" data-toggle="tooltip" target="_blank" class="text-green" data-original-title="Excel"><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('01-M-y'):date('d-M-y', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('d-M-y'):date('d-M-y', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class=" control-label" for="unput1">Invoiced Date</label>
								<input class="form-control" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<?php
					//~ echo '<pre>';
					//~ print_r($result);
					//~ echo '</pre>';
						
					$vehicle = '';
					$parts = '';
					foreach($result as $row){
						$row = (object)$row;
						if($row->CS_NUMBER != NULL){
							$vehicle .= '<tr>
											<td>'.$row->ACCOUNT_NAME.'</td>
											<td>'.$row->CS_NUMBER.'</td>
											<td>'.$row->INVOICE_NUMBER.'</td>
											<td>'.short_date($row->INVOICE_DATE).'</td>
											<td>'.short_date($row->DELIVERY_DATE).'</td>
											<td>'.short_date($row->DUE_DATE).'</td>
											<td class="text-right">'.amount($row->INVOICE_AMOUNT).'</td>
											<td class="text-right">'.amount($row->BALANCE).'</td>
											<td>'.$row->INVOICE_STATUS.'</td>
										</tr>';
						}
						else{
							$parts .= '<tr>
											<td>'.$row->ACCOUNT_NAME.'</td>
											<td>'.$row->INVOICE_NUMBER.'</td>
											<td>'.short_date($row->INVOICE_DATE).'</td>
											<td>'.short_date($row->DELIVERY_DATE).'</td>
											<td>'.short_date($row->DUE_DATE).'</td>
											<td class="text-right">'.amount($row->INVOICE_AMOUNT).'</td>
											<td class="text-right">'.amount($row->BALANCE).'</td>
											<td>'.$row->INVOICE_STATUS.'</td>
										</tr>';
						}
					}
					?>
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#vehicle" aria-controls="vehicle" role="tab" data-toggle="tab">Vehicle</a></li>
							<li role="presentation"><a href="#parts" aria-controls="parts" role="tab" data-toggle="tab">Parts</a></li>
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="vehicle">
								<table class="table">
									<thead>
										<tr>
											<th>Account Name</th>
											<th>CS Number</th>
											<th>Invoice Number</th>
											<th>Invoice Date</th>
											<th>Pullout Date</th>
											<th>Due Date</th>
											<th>Invoice Amount</th>
											<th>Balance</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php echo $vehicle; ?>
									</tbody>
								</table>
							</div>
							<div role="tabpanel" class="tab-pane" id="parts">
								<table class="table">
									<thead>
										<tr>
											<th>Account Name</th>
											<th>Invoice Number</th>
											<th>Invoice Date</th>
											<th>Delivery Date</th>
											<th>Due Date</th>
											<th>Invoice Amount</th>
											<th>Balance</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php echo $parts; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
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
		
		$('.table').DataTable({
			"order": []	
		});
		
	});
</script>
