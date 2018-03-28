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
					<h3 class="box-title">Approved Checks</h3>
				</div>
				<div class="row">
					<div class="col-sm-3" style="padding: 10px;margin-left: 20px;">
						<form id="form_filters" method="POST" accept-charset="utf-8">
							<input type="hidden" name="from_date" value="<?php echo ($from_date == '')? date('01-M-y'):date('d-M-y', strtotime($from_date)); ?>"/>
							<input type="hidden" name="to_date" value="<?php echo ($to_date == '')? date('d-M-y'):date('d-M-y', strtotime($to_date)); ?>"/>
							<div class="form-group">
								<label class=" control-label" for="unput1">Check Date</label>
								<input class="form-control" type="text" name="date_created" value="<?php echo ($from_date == '')? date('m/01/Y'):date('m/d/Y', strtotime($from_date)); ?> - <?php echo ($to_date == '')? date('m/d/Y'):date('m/d/Y', strtotime($to_date)); ?>" />
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<table class="table table-striped table-bordered table-hover" id="myTable">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Check ID</th>
								<th>Check Number</th>
								<th>Check Bank</th>
								<th>Check Date</th>
								<th>Date Created</th>
								<th>Check Amount</th>
								<th>Print</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($result as $row){
						?>
							<tr>
								<td><a class="modal_trigger btn btn-danger btn-xs" data-check_id="<?php echo $row->CHECK_ID; ?>" data-check_number="<?php echo $row->CHECK_NUMBER; ?>" data-check_bank="<?php echo $row->CHECK_BANK; ?>"><i class="glyphicon glyphicon-chevron-right"></i></a></td>
								<td><?php echo $row->CHECK_ID; ?></td>
								<td><?php echo $row->CHECK_NUMBER; ?></td>
								<td><?php echo $row->CHECK_BANK; ?></td>
								<td><?php echo short_date($row->CHECK_DATE); ?></td>
								<td><?php echo short_date($row->DATE_CREATED); ?></td>
								<td class="text-right"><?php echo amount($row->CHECK_AMOUNT); ?></td>
								<td class="text-center"><a target="_blank" href="pdc_details_pdf/<?php echo $row->CHECK_ID; ?>"><i class="fa fa-file-pdf-o"></i></td>
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

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content modal-content-1">

		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-danger fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Deposit Date Entry</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="hidden" name="check_id">
					<input type="text" class="datemask form-control"  placeholder="Deposit Date" name="deposit_date">
				</div>
				<div class="text-right">
					<button type="button" name="save" class="btn btn-danger">Save</button>
				</div>
			</div>
			
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('resources/plugins/input-mask/jquery.inputmask.date.extensions.js'); ?>"></script>
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
		
		var my_input;
		
		$('table tbody').on('click','tr td a.modal-edit',function(){
			$('input[name=check_id]').val($(this).data('check_id'));
			my_input = $(this).parent();
		});
		
		$('.datemask').inputmask('mm/dd/yyyy', {'placeholder' : 'mm/dd/yyyy'});
		
		$('#myTable').DataTable();
		
		$('table tbody').on('click','tr td a.modal_trigger',function(){
		//~ $('table tbody tr td a.modal_trigger').on('click' ,function(){
			var check_id = $(this).data('check_id');
			var check_number = $(this).data('check_number');
			var check_bank= $(this).data('check_bank');
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url();?>receivables/check_warehousing/customer_check_unit_details_ajax',
				data: {
						check_id: check_id,
						check_number: check_number,
						check_bank: check_bank
					},
				success: function(data) 
				{
					//alert(dr_number);
					$('#myModal').modal('show');
					$('.modal-content-1').html(data);
				}
			});
		});
	});
</script>
