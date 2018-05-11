<?php 
$this->load->helper('number_helper');
$this->load->helper('profile_class_helper');
?>
<link href="<?php echo base_url('resources/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/select2/dist/css/select2.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-body">
					<form id="myForm" class="form-inline" method="POST" accept-charset="utf-8">
						<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
						<input type="hidden" name="customer_name" value="<?php echo $customer_name; ?>" />
						<input type="hidden" name="as_of_date"  value="<?php echo $as_of_date; ?>" />
						<div class="row">
							<div class="col-sm-9">
								<label class="text-90">Customer Name : </label>
								<select class="form-control select2">
									<?php 
									if($customer_id == NULL){
									?>
									<option value="" selected="selected">Select Customer</option>
									<?php
									}
									else{
									?>
									<option value="<?php echo $customer_id; ?>" selected="selected"><?php echo $customer_name; ?></option>
									<?php 
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="text-90"s>As of : </label>
								<div class='input-group date'  id='datetimepicker'>
									<input type="text" class="form-control" value="<?php echo $as_of_date; ?>"/>
									<span class="input-group-addon">
										<span class="fa fa-calendar"></span>
									</span>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="box box-danger">
				<?php 
				if($customer_id != NULL){
				?>
				<div class="box-header with-border">
					<h6 class="box-title">&nbsp;</h6>
					<div class="box-tools pull-right" style="margin-top: 5px;">
						<a class="text-red" target="_blank" data-toggle="tooltip" data-placement="top" title="PDF" href="<?php echo base_url("reports/transaction_summary_pdf/index/".str_replace('/', '', $as_of_date)."/".($customer_id * 101403)); ?>" class=""><i class="fa fa-file-pdf-o"></i></a>
						<a class="text-green" target="_blank" data-toggle="tooltip" data-placement="top" title="Excel" href="<?php echo base_url("reports/receivables_excel/index/".str_replace('/', '', $as_of_date)."/NULL/".$customer_id); ?>" class=""><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<?php 
				}
				?>
				<div class="box-body">
					<table class="table table-hover">
						<thead >
							<tr>
								<th class="text-left">&nbsp;&nbsp;</th>
								<th class="text-left">Profile Class</th>
								<th class="text-right info text-info">Unpulledout Receivables</th>
								<th class="text-right success text-success">Current Receivables</th>
								<th class="text-right warning text-yellow">Past Due Receivables</th>
								<th class="text-right danger text-red">Total Receivables</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(!empty($results)){
								foreach($results as $row){
								?>
									<tr class="<?php echo ($row->PROFILE_CLASS_ID == NULL) ? 'text-bold':''; ?>">
										<td><a class="btn btn-danger btn-xs <?php echo ($row->PROFILE_CLASS_ID == NULL) ? 'hidden':''; ?>" 
												href="../soa/admin/<?php echo get_sales_type($row->PROFILE_CLASS_ID); ?>/<?php echo $row->PROFILE_CLASS_ID; ?>/<?php echo $customer_id * 101403; ?>/<?php echo date('mdY', strtotime($as_of_date)); ?>">
												<i class="glyphicon glyphicon-chevron-right"></i>
											</a>
										</td>
										<td class="text-left"><?php echo $row->PROFILE_CLASS;?></td>
										<td class="text-right info text-info"><?php echo amount($row->CONTINGENT_RECEIVABLES);?></td>
										<td class="text-right success text-success"><?php echo amount($row->CURRENT_RECEIVABLES);?></td>
										<td class="text-right warning text-yellow"><?php echo amount($row->PAST_DUE);?></td>
										<td class="text-right danger text-red"><?php echo amount($row->TOTAL);?></td>
									</tr>
								<?php 
								}
							}
							else{
								$ctr = 0;
								while(5 > $ctr){
								?>
									<tr>
										<td class="text-center">-</td>
										<td class="text-center">-</td>
										<td class="text-center">-</td>
										<td class="text-center">-</td>
										<td class="text-center">-</td>
									</tr>
								<?php
									$ctr++;
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body" style="font-size: 16px;">
				<p align="center"><strong><h4  align="center">ANNOUNCEMENT/REMINDER</h4></strong></p>
				<hr />
				<p class="text-justify">To ensure that your Statement of Account is updated, always send the corresponding Creditable Withholding Tax Certificate (CWT) copy via email right after making the payment online or via bank deposit. Send this always to IPC Treasury together with the validated deposit slip or proof of online transfer/payment. In case the dealer decides to send the check payment over to IPC Treasury, the same documents should be attached( payment details of the check and Original CWT for the payment). The same is true when sending Post Dated Checks to IPC Treasury for Vehicle Fleet Transactions or Retail Vehicle Transactions with payment terms.</p>
				<p class="text-justify">These conditions apply to all transactions to IPC may it be vehicle, parts, Isuzu Merchandise, payment for licenses, special tools grant from service or any other payments. </p>
				<p class="text-justify">Thank You.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">WE UNDERSTAND</button>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/moment/js/moment.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/select2/dist/js/select2.full.min.js');?>"></script>
<script>
	$(document).ready(function() {
		
		$('[data-toggle="tooltip"]').tooltip(); 
		
		$('#datetimepicker').datetimepicker({
			//~ debug:true,
			format: 'MM/DD/YYYY'
		});
		
		$('#datetimepicker').on('dp.change', function (e) {
			
			var data = $('select.select2').select2('data');
			customer_id = data[0].id;
			customer_name = data[0].text;
			as_of_date = $(this).data('date');
			
			$('input[name=customer_id]').val(customer_id);
			$('input[name=customer_name]').val(customer_name);
			$('input[name=as_of_date]').val(as_of_date);
			
			$('#myForm').submit();
        });
    });
</script>
<?php
if($this->session->flashdata('banner') !== NULL){
	?>
		<script>
			$(document).ready(function() {
				
				 $('#myModal').modal({
					backdrop: 'static',
					keyboard: false  // to prevent closing with Esc button (if you want this too)
				})
			});
		</script>
	<?php 
	}
	?>
<?php
if(in_array($this->session->tre_portal_user_type, array('Administrator','IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){ 
?>
	<script>
		$(document).ready(function() {
		  
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

			$("select.select2").select2({
				width: '100%',
				  ajax: {
					url: "ajax_customer_list",
					dataType: 'json',
					type: 'GET',
					delay: 250,
					data: function (params) {
					  return {
						q: params.term // search term
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
		});
	</script>
<?php 
}
else if($this->session->tre_portal_user_type == 'Dealer Admin'){ 
?>	
	<script>
		$(document).ready(function() {
			
			$("select.select2").select2({
				width: '100%'
			});
			$('select.select2').select2("enable",false);
		});
	</script>
<?php 
}
?>

