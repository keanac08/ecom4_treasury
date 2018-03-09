<?php 
	$this->load->helper('number_helper');
	$this->load->helper('date_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-10">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title">Check Warehousing</h3>
				</div>
				<div class="box-body">
					<table class="table table-striped table-bordered" id="myTable">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th>Check ID</th>
								<th>Check Number</th>
								<th>Check Bank</th>
								<th>Check Date</th>
								<th>Check Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($result as $row){
						?>
							<tr>
								<td><a class="modal_trigger btn btn-danger btn-xs"><i class="glyphicon glyphicon-chevron-right"></i></a></td>
								<td><?php echo $row->CHECK_ID; ?></td>
								<td><?php echo $row->CHECK_NUMBER; ?></td>
								<td><?php echo $row->CHECK_BANK; ?></td>
								<td><?php echo short_date($row->CHECK_DATE); ?></td>
								<td class="text-right"><?php echo amount($row->CHECK_AMOUNT); ?></td>
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

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function(){
		$('#myTable').DataTable();
	});
</script>
