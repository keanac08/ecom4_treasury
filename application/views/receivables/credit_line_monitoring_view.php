<?php 
	$this->load->helper('number_helper');
?>
<link href="<?php echo base_url('resources/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" >
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-danger">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo UCWORDS($sales_type); ?> Credit Line Status</h3>
					<div class="box-tools pull-right" style="margin-top: 5px;">
						<a class="text-green" target="_blank" data-toggle="tooltip" data-placement="top" title="Excel" href="<?php echo base_url("reports/credit_line_status_excel/".$sales_type); ?>" class=""><i class="fa fa-file-excel-o"></i></a>
					</div>
				</div>
				<div class="col-md-9">
					<h5><span class="label label-success">&nbsp;</span> Under Credit Limit</h5>
					<h5><span class="label label-warning">&nbsp;</span> Over Credit Limit</h5>
				</div>
				<br />
				<div class="box-body">
					<table class="table table-bordered" id="reports_tab" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Customer Name</th>
								<th class="text-right">Credit<br />Limit</th>
								<th class="text-right">Exposure<br />AR Balance</th>
								<th class="text-right">Exposure<br />Open SO</th>
								<th class="text-right">Total<br />Exposure</th>
								<th class="text-right">Available<br />Credit Limit</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach($results as $row){
						?>
							<tr class="<?php echo $row->AVAILABLE_CREDIT_LIMIT < 0 ? 'warning text-yellow':'success text-success';?>" >
								<td><?php echo $row->CUSTOMER_NAME; ?></td>
								<td class="text-right"><?php echo amount($row->CREDIT_LIMIT); ?></td>
								<td class="text-right"><?php echo amount($row->EXPOSURE_AR_BALANCE_TOTAL); ?></td>
								<td class="text-right"><?php echo amount($row->EXPOSURE_OPEN_SO); ?></td>
								<td class="text-right"><?php echo amount($row->TOTAL_EXPOSURE); ?></td>
								<td class="text-right"><?php echo $row->AVAILABLE_CREDIT_LIMIT < 0 ? '('.amount($row->AVAILABLE_CREDIT_LIMIT * -1).')':amount($row->AVAILABLE_CREDIT_LIMIT);?></td>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog modal-danger">
		<div class="modal-content">
		
		</div>
	</div>
</div>

<script src="<?php echo base_url('resources/plugins/datatables/datatables.min.js');?>"></script>
<script>
	$(document).ready(function() {
		
		$('#reports_tab').DataTable({
			//~ 'bSort' : false
			 "order": []
		});
		
		$('body').on('click','a.btn_dr_modal',function(){
			v_link = $(this).data('link');
			$.ajax({
				url: '<?php echo base_url(); ?>' + v_link,
				success: function(data)
				{					
					$('.modal-content').html(data);
					$('#myModal').modal({
						backdrop: 'static'
						//~ keyboard: false  // to prevent closing with Esc button (if you want this too)
					});
				}
			});
		});
	});
</script>

