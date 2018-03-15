<?php 
$this->load->helper('number_helper');
$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/select2/dist/css/select2.min.css') ?>" rel="stylesheet" >

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-body">
					<form id="myForm" class="form-inline" method="POST" accept-charset="utf-8" action="<?php echo base_url('receivables/soa/admin/'.$sales_type); ?>">
						<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
						<input type="hidden" name="as_of_date"  value="<?php echo $as_of_date; ?>" />
						<input type="hidden" name="sales_type"  value="<?php echo $sales_type; ?>" />
						<div class="row">
							<div class="col-sm-9">
								<label class="text-90">Customer Name : </label>
								<select class="form-control select2">
									<?php 
									if($customer_id != 0){
									?>
									<option value="<?php echo $customer_details->CUSTOMER_ID; ?>" selected="selected"><?php echo $customer_details->CUSTOMER_NAME; ?></option>
									<?php 
									}
									else{
									?>
									<option value="0" selected="selected">Select Customer . . .</option>
									<?php 
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<div class="row">
									<div class="col-sm-12">
										<label class="text-90"s>As of : </label>
									</div>
									<div class="col-sm-12">
										<div class='input-group date' id='datetimepicker'>
											<input type="text" class="form-control" value="<?php echo $as_of_date; ?>"/>
											<span class="input-group-addon">
												<span class="fa fa-calendar"></span>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="nav-tabs-custom tab-danger">
				<ul class="nav nav-tabs">
<!--
					<li class="active"><a href="#summary" data-toggle="tab" aria-expanded="false">SOA Summary & Customer Details</a></li>
-->
					<li class="active"><a href="#summary" data-toggle="tab" aria-expanded="false">Summary</a></li>
					<li class=""><a href="#detailed" data-toggle="tab" aria-expanded="true">Detailed SOA</a></li>
					
					<?php 
					if(!empty($customer_details)){
					?>
					<li class="pull-right" style="margin-top: 10px;">
						<a target="_blank" data-toggle="tooltip" data-placement="top" title="PDF" class="text-red" style="display: inline;padding: 1px;" href="<?php echo base_url('reports/soa_pdf/index/'); ?><?php echo $sales_type; ?>/<?php echo $customer_details->PROFILE_CLASS_ID; ?>/<?php echo $customer_id * 101403; ?>/<?php echo date('mdY', strtotime($as_of_date)); ?>"><i class="fa fa-file-pdf-o"></i> </a>
						<a target="_blank" data-toggle="tooltip" data-placement="top" title="Excel" class="text-green" style="display: inline;padding: 1px;" href="<?php echo base_url('reports/soa_excel/index/'); ?><?php echo $sales_type; ?>/<?php echo $customer_details->PROFILE_CLASS_ID; ?>/<?php echo $customer_id * 101403; ?>/<?php echo date('mdY', strtotime($as_of_date)); ?>"><i class="fa fa-file-excel-o"></i> </a>
					</li>
					<?php 
					}
					?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane" id="detailed">
						<div class="row">
							<div class="col-md-9">
								<h5><span class="label label-warning">&nbsp;</span> Past Due Receivables</h5>
								<h5><span class="label label-success">&nbsp;</span> Current Receivables</h5>
								<h5><span class="label label-primary"> &nbsp;</span> Contingent Receivables</h5>
							</div>
							<div class="col-md-3">
								<div class="input-group">
									<input id="search_table" type="text" class="form-control" placeholder="Search . . .">
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
								</div>
							</div>
							<div class="col-md-12">
								&nbsp;
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<table id="table_soa" class="table table-hover">
									<thead>
										<tr>
											<th class="text-center">&nbsp;</th>
											<th class="text-center"><?php echo (in_array( $sales_type, array('vehicle','fleet'))) ? 'CS Number' : ''; ?></th>
											<th class="text-center">Invoice Number</th>
											<th class="text-center">Invoice Date</th>
											<th class="text-center"><?php echo (in_array( $sales_type, array('vehicle','fleet'))) ? 'Pullout Date' : 'Delivery Date'; ?></th>
											<th class="text-center">Payment Terms</th>
											<th class="text-right">Invoice Amount</th>
											<th class="text-right">WHT Amount</th>
											<th class="text-right">Balance</th>
											<th class="text-center">Days Overdue</th>
											<?php 
											if(in_array($sales_type, array('vehicle', 'fleet'))){ 
											?>
											<th class="text-center">PDC Number</th>
											<?php 
											}
											else if(in_array($sales_type, array('parts'))){ 
											?>
											<th class="text-center">Cust PO Number</th>
											<?php 	
											}
											if(in_array($sales_type, array('fleet'))){
											?>
											<th class="text-center">Fleet Name</th>
											<?php 
											}
											?>
										</tr>
									</thead>
									<tbody>
										
									<?php
									$past_due_01_15 = 0;
									$past_due_16_30 = 0;
									$past_due_31_60 = 0;
									$past_due_61_90 = 0;
									$past_due_91_120 = 0;
									$past_due_over_120 = 0;
									$past_due_01_15_count = 0;
									$past_due_16_30_count = 0;
									$past_due_31_60_count = 0;
									$past_due_61_90_count = 0;
									$past_due_91_120_count = 0;
									$past_due_over_120_count = 0;
									$contingent = 0;
									$current = 0;
									$past_due = 0;
									$contingent_count = 0;
									$current_count = 0;
									$past_due_count = 0;
									
									$past_due_01_15_wht = 0;
									$past_due_16_30_wht = 0;
									$past_due_31_60_wht = 0;
									$past_due_61_90_wht = 0;
									$past_due_91_120_wht = 0;
									$past_due_over_120_wht = 0;
									$contingent_wht = 0;
									$current_wht = 0;
									$past_due_wht = 0;
									
									foreach($soa_detailed as $row){
										
										if($row->INVOICE_ID != NULL){
											
											if($row->DELIVERY_DATE == NULL){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$contingent += $row->BALANCE;
												}
												else{
													$contingent_wht += $row->BALANCE;
												}
												$contingent_count++;
											}
											else if($row->DAYS_OVERDUE == 0){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$current += $row->BALANCE;
												}
												else{
													$current_wht += $row->BALANCE;
												}
												$current_count++;
											}
											else if($row->DAYS_OVERDUE > 0 AND $row->DAYS_OVERDUE <= 15){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_01_15 += $row->BALANCE;
												}
												else{
													$past_due_01_15_wht += $row->BALANCE;
												}
												$past_due_01_15_count++;
											}
											else if($row->DAYS_OVERDUE > 15 AND $row->DAYS_OVERDUE <= 30){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_16_30 += $row->BALANCE;
												}
												else{
													$past_due_16_30_wht += $row->BALANCE;
												}
												$past_due_16_30_count++;
											}
											else if($row->DAYS_OVERDUE > 30 AND $row->DAYS_OVERDUE <= 60){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_31_60 += $row->BALANCE;
												}
												else{
													$past_due_31_60_wht += $row->BALANCE;
												}
												$past_due_31_60_count++;
											}
											else if($row->DAYS_OVERDUE > 60 AND $row->DAYS_OVERDUE <= 90){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_61_90 += $row->BALANCE;
												}
												else{
													$past_due_61_90_wht += $row->BALANCE;
												}
												$past_due_61_90_count++;
											}
											else if($row->DAYS_OVERDUE > 90 AND $row->DAYS_OVERDUE <= 120){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_91_120 += $row->BALANCE;
												}
												else{
													$past_due_91_120_wht += $row->BALANCE;
												}
												$past_due_91_120_count++;
											}
											else if($row->DAYS_OVERDUE > 120){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due_over_120 += $row->BALANCE;
												}
												else{
													$past_due_over_120_wht += $row->BALANCE;
												}
												$past_due_over_120_count++;
											}
											
											if($row->DAYS_OVERDUE > 0){
												if($row->BALANCE - $row->WHT_AMOUNT > 1){
													$past_due += $row->BALANCE;
												}
												else{
													$past_due_wht += $row->BALANCE;
												}
												$past_due_count++;
											}

											
										?>
											<tr class="texts-<?php echo ($row->DELIVERY_DATE == NULL ? 'info info' : ($row->DAYS_OVERDUE > 0 ? 'yellow warning':'success success')); ?>" >
												<td>
													<a class="modal_trigger btn btn-<?php echo ($row->DELIVERY_DATE == NULL ? 'info' : ($row->DAYS_OVERDUE > 0 ? 'warning':'success')); ?> btn-xs " href="javascript:;" data-invoice_id="<?php echo $row->INVOICE_ID; ?>" ><i class="glyphicon glyphicon-chevron-right"></i></a>
												</td>
												<td class="text-center"><?php echo (in_array( $sales_type, array('vehicle','fleet'))) ? $row->CS_NUMBER : ''; ?></td>
												<td class="text-center"><?php echo $row->INVOICE_NO; ?></td>
												<td class="text-center"><?php echo short_date($row->INVOICE_DATE); ?></td>
												<td class="text-center"><?php echo short_date($row->DELIVERY_DATE); ?></td>
												<td class="text-center"><?php echo $row->PAYMENT_TERM; ?></td>
												<td class="text-right"><?php echo amount($row->TRANSACTION_AMOUNT); ?></td>
												<td class="text-right"><?php echo amount($row->WHT_AMOUNT); ?></td>
												<td class="text-right"><?php echo amount($row->BALANCE); ?></td>
												<td class="text-center"><?php echo $row->DAYS_OVERDUE; ?></td>
												<?php 
												if(in_array($sales_type, array('vehicle', 'fleet'))){ 
												?>
												<td class="text-center">-</td>
												<?php 
												}
												else if(in_array($sales_type, array('parts'))){
												?>
												<td class="text-center"><?php echo $row->CUST_PO_NUMBER; ?></td>
												<?php 	
												}
												if(in_array($sales_type, array('fleet'))){
												?>
												<td class="text-center"><?php echo $row->FLEET_NAME; ?></td>
												<?php 
												}
												?>
											</tr>
										<?php 
										}
										 else if(
												($row->INVOICE_ID == NULL AND $row->DUE_DATE != NULL) OR
												($row->INVOICE_ID == NULL AND $row->DUE_DATE == NULL AND $row->DELIVERY_DATE == NULL)
												){
										?>
											<tr class="item texts-<?php echo ($row->DELIVERY_DATE == NULL ? 'info info' : ($row->DAYS_OVERDUE > 0 ? 'yellow warning':'success success')); ?>">
												<td colspan="1">&nbsp;</td>
												<td colspan="5" class="text-center">
													<strong>
														<?php echo ($row->DELIVERY_DATE == NULL ? 'Subtotal' : ($row->DAYS_OVERDUE > 0 ? 'Subtotal Past Due':'Subtotal Due')); ?> &emsp; <?php echo short_date($row->DUE_DATE); ?>
													</strong>
												</td>
												<td class="text-right"><strong><?php echo amount($row->TRANSACTION_AMOUNT); ?></strong></td>
												<td class="text-right"><strong><?php echo amount($row->WHT_AMOUNT); ?></strong></td>
												<td class="text-right"><strong><?php echo amount($row->BALANCE); ?></strong></td>
												<td colspan="1">&nbsp;</td>
												<?php 
												if(in_array($sales_type, array('vehicle', 'fleet', 'parts'))){ 
												?>
												<td class="text-center">&nbsp;</td>
												<?php 
												}
												if(in_array($sales_type, array('fleet'))){
												?>
												<td class="text-center">&nbsp;</td>
												<?php 
												}
												?>
											</tr>
											<tr>
												<td colspan="10">&nbsp;</td>
												<?php 
												if(in_array($sales_type, array('vehicle', 'fleet', 'parts'))){ 
												?>
												<td class="text-center">&nbsp;</td>
												<?php 
												}
												if(in_array($sales_type, array('fleet'))){
												?>
												<td class="text-center">&nbsp;</td>
												<?php 
												}
												?>
												
											</tr>
										<?php 
										}
									}
									?>
									</tbody>
								</table>
								<a id="back-to-top" href="#" class="btn btn-danger btn-lg" role="button"><i class="fa fa-chevron-up"></i></a>
							</div>
						</div>
					</div>
					<div class="tab-pane active" id="summary">
						<div class="row">
							<div class="col-md-3">
								<div class="box box-danger" style="margin-top: 36px;">
									<div class="box-header with-border">
										<h3 class="box-title">Customer Details</h3>
									</div>
									<div class="box-body">
										<strong>Customer Name</strong>
										<p class="text-muted">
											<?php echo (!isset($customer_details->PARTY_NAME) OR $customer_details->PARTY_NAME == NULL) ? '-' : $customer_details->CUSTOMER_ID . ' - ' .$customer_details->PARTY_NAME; ?>
										</p>
										<strong>Account Name</strong>
										<p class="text-muted">
											<?php echo (!isset($customer_details->ACCOUNT_NAME) OR $customer_details->ACCOUNT_NAME == NULL) ? '-' : $customer_details->ACCOUNT_NUMBER . ' - ' .$customer_details->ACCOUNT_NAME; ?>										
										</p>
										<strong>Customer Address</strong>
										<p class="text-muted">
											<?php echo (!isset($customer_details->ADDRESS) OR $customer_details->ADDRESS == NULL) ? '-' : $customer_details->ADDRESS; ?>
										</p>
										<strong>Profile Class</strong>
										<p class="text-muted">
											<?php echo (!isset($customer_details->PROFILE_CLASS) OR $customer_details->PROFILE_CLASS == NULL)  ? '-' : $customer_details->PROFILE_CLASS_ID . ' - ' . $customer_details->PROFILE_CLASS; ?>
											<input type="hidden" name="profile_class" value="<?php echo @$customer_details->PROFILE_CLASS; ?>">
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-9">
								<table class="table">
									<thead>
										<th>&nbsp;</th>
										<th>&nbsp;</th>
										<th class="text-center">Invoice Count</th>
										<th class="text-right">WHT Balance</th>
										<th class="text-right">Amount Due</th>
										<th class="text-right">Invoice Balance</th>
									</thead>
									<tbody>
										<tr class="text-blue text-bold info">
											<td><span class="label label-primary">&nbsp;</span></td>
											<td>Total Contingent Receivables</td>
											<td class="text-center"><?php echo $contingent_count; ?></td>
											<td class="text-right"><?php echo amount($contingent_wht); ?></td>
											<td class="text-right"><?php echo amount($contingent); ?></td>
											<td class="text-right"><?php echo amount($contingent + $contingent_wht); ?></td>
										</tr>
										<tr class="text-success text-bold success">
											<td><span class="label label-success">&nbsp;</span></td>
											<td>Total Current Receivables</td>
											<td class="text-center"><?php echo $current_count; ?></td>
											<td class="text-right"><?php echo amount($current_wht); ?></td>
											<td class="text-right"><?php echo amount($current); ?></td>
											<td class="text-right"><?php echo amount($current_wht + $current); ?></td>
										</tr>
										<tr class="text-yellow text-bold warning">
											<td><span class="label label-warning">&nbsp;</span></td>
											<td>Total Past Due Receivables</td>
											<td class="text-center"><?php echo $past_due_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due); ?></td>
											<td class="text-right"><?php echo amount($past_due + $past_due_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30">0 - 15 Days</td>
											<td class="text-center"><?php echo $past_due_01_15_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_01_15_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_01_15); ?></td>
											<td class="text-right"><?php echo amount($past_due_01_15 + $past_due_01_15_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30">16 - 30 Days</td>
											<td class="text-center"><?php echo $past_due_16_30_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_16_30_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_16_30); ?></td>
											<td class="text-right"><?php echo amount($past_due_16_30 + $past_due_16_30_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30">31 - 60 Days</td>
											<td class="text-center"><?php echo $past_due_31_60_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_31_60_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_31_60); ?></td>
											<td class="text-right"><?php echo amount($past_due_31_60 + $past_due_31_60_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30">61 - 90 Days</td>
											<td class="text-center"><?php echo $past_due_61_90_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_61_90_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_61_90); ?></td>
											<td class="text-right"><?php echo amount($past_due_61_90 + $past_due_61_90_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30">91 - 120 Days</td>
											<td class="text-center"><?php echo $past_due_91_120_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_91_120_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_91_120); ?></td>
											<td class="text-right"><?php echo amount($past_due_91_120 + $past_due_91_120_wht); ?></td>
										</tr>
										<tr class="text-yellow">
											<td>&nbsp;</td>
											<td class="padding-l-30"> Over 120 Days</td>
											<td class="text-center"><?php echo $past_due_over_120_count; ?></td>
											<td class="text-right"><?php echo amount($past_due_over_120_wht); ?></td>
											<td class="text-right"><?php echo amount($past_due_over_120); ?></td>
											<td class="text-right"><?php echo amount($past_due_over_120 + $past_due_over_120_wht); ?></td>
										</tr>
										<tr class="text-red text-bold danger">
											<td><span class="label label-danger">&nbsp;</span></td>
											<td>Total Receivables</td>
											<td class="text-center"><?php echo $contingent_count + $current_count + $past_due_count; ?></td>
											<td class="text-right"><?php echo amount($contingent_wht + $current_wht + $past_due_wht); ?></td>
											<td class="text-right"><?php echo amount($contingent + $current + $past_due); ?></td>
											<td class="text-right"><?php echo amount($contingent + $current + $past_due + $contingent_wht + $current_wht + $past_due_wht); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
<!--
			modal content
-->
		</div>
	</div>
</div>


<script src="<?php echo base_url('resources/plugins/moment/js/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/select2/dist/js/select2.full.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/floatThead/floatThead.min.js');?>"></script>

<script>
	$(document).ready(function() {
		
		$('[data-toggle="tooltip"]').tooltip(); 
		
		var sales_type = $('input[name=sales_type]').val();
		$("select.select2").select2({
			width: '100%',
			  ajax: {
				url: "<?php echo base_url(); ?>receivables/soa/ajax_customers_per_profile",
				dataType: 'json',
				type: 'POST',
				delay: 250,
				data: function (params) {
				  return {
					q: params.term,
					sales_type: sales_type
				  };
				},
				processResults: function (data, page) {
				  return {
					results: data  
				  };
				},
				cache: true
			  },
			  minimumInputLength: 3
		});
		
		$('#datetimepicker').datetimepicker({
			//~ debug:true,
			format: 'MM/DD/YYYY'
		});
		
		$('#datetimepicker').on('dp.change', function (e) {
			
			var data = $('select.select2').select2('data');
			customer_id = data[0].id;
			customer_name = data[0].text;
			as_of_date = $(this).data('date');
			
			//~ alert(as_of_date);
			
			$('input[name=customer_id]').val(customer_id);
			$('input[name=customer_name]').val(customer_name);
			$('input[name=as_of_date]').val(as_of_date);
			
			$('#myForm').submit();
        });
        
        $('select.select2').on('select2:select', function (e) {
			
			var data = e.params.data;
			customer_id = data['id'];
			customer_name = data['text'];
			as_of_date = $('#datetimepicker input').val();
			
			$('input[name=customer_id]').val(customer_id);
			$('input[name=customer_name]').val(customer_name);
			$('input[name=as_of_date]').val(as_of_date);
			
			$('#myForm').submit();
		});
		
		$('table#table_soa').floatThead();
		
		var $rows = $('table#table_soa tbody tr');
		$('#search_table').keyup(function() {
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
			
			$rows.show().filter(function() {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
				return !~text.indexOf(val);
			}).hide();
		});
		
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-to-top').fadeIn();
			}
			else{
				$('#back-to-top').fadeOut();
			}
		});
		
		$('#back-to-top').click(function () {
			$('#back-to-top').tooltip('hide');
			$('body,html').animate({
			scrollTop: 0
			}, 800);
			return false;
		});

		$('#back-to-top').tooltip('show');
		
		$('body').on('click','a.modal_trigger',function(){
			var customer_trx_id = $(this).data('invoice_id');
			var profile_class = $('input[name=profile_class]').val();
			 //~ alert(profile_class);
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>receivables/soa/ajax_invoice_details',
				data: {
						customer_trx_id: customer_trx_id,
						profile_class: profile_class
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('#myModal').modal('show');
					$('.modal-content').html(data);
				}
			});
		});
	});
</script>

